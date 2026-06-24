<?php
/**
 * File Location: app/Services/RoomService.php
 * File Name: RoomService.php
 * Description: Business logic service layer validating room registration rules.
 */

namespace App\Services;

use App\Models\RoomModel;
use Exception;

class RoomService {
    private RoomModel $roomModel;

    public function __construct() {
        $this->roomModel = new RoomModel();
    }

    /**
     * Pre-validates form variables and writes room attributes to the database.
     */
    public function addRoom(int $houseId, int $ownerId, array $inputs, ?array $file): bool {
        // Enforce ownership and check if boarding house is approved
        $house = $this->roomModel->findHouseByOwner($houseId, $ownerId);
        if (!$house) {
            throw new Exception("You are not authorized to add rooms to this property or it has not been approved yet.");
        }

        $this->validateRoomInputs($inputs);

        $imagePath = null;
        if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $imagePath = $this->processRoomImageUpload($file);
        }

        $insertData = [
            'boarding_house_id' => $houseId,
            'room_name'         => trim($inputs['room_name']),
            'price'             => (float)$inputs['price'],
            'capacity'          => (int)$inputs['capacity'],
            'available_beds'    => (int)$inputs['available_beds'],
            'amenities'         => trim($inputs['amenities'] ?? ''),
            'status'            => $inputs['status'] ?? 'Available',
            'image_path'        => $imagePath
        ];

        if (!$this->roomModel->create($insertData)) {
            throw new Exception("Failed to record room specification in database storage.");
        }

        return true;
    }

    /**
     * Verifies room validity and applies dynamic modifications.
     */
    public function updateRoom(int $roomId, int $houseId, int $ownerId, array $inputs, ?array $file): bool {
        // Enforce ownership
        $house = $this->roomModel->findHouseByOwner($houseId, $ownerId);
        if (!$house) {
            throw new Exception("You are not authorized to update rooms on this boarding house.");
        }

        $existingRoom = $this->roomModel->findRoomById($roomId);
        if (!$existingRoom || (int)$existingRoom['boarding_house_id'] !== $houseId) {
            throw new Exception("The specified room details could not be found.");
        }

        $this->validateRoomInputs($inputs);

        $imagePath = $existingRoom['image_path'];
        // If a new image is provided, upload it and unlink the old one
        if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $imagePath = $this->processRoomImageUpload($file);
            $this->unlinkPhysicalFile($existingRoom['image_path']);
        }

        // Automatically synchronize status with physical bed vacancy
        $status = $inputs['status'];
        if ($status !== 'Maintenance') {
            $status = ((int)$inputs['available_beds'] === 0) ? 'Fully Booked' : 'Available';
        }

        $updateData = [
            'room_name'      => trim($inputs['room_name']),
            'price'          => (float)$inputs['price'],
            'capacity'       => (int)$inputs['capacity'],
            'available_beds' => (int)$inputs['available_beds'],
            'amenities'      => trim($inputs['amenities'] ?? ''),
            'status'         => $status,
            'image_path'     => $imagePath
        ];

        if (!$this->roomModel->update($roomId, $updateData)) {
            throw new Exception("Failed to update room specifications inside storage.");
        }

        return true;
    }

    /**
     * Safely removes a room profile from database, unlinking media files.
     */
    public function deleteRoom(int $roomId, int $houseId, int $ownerId): bool {
        $house = $this->roomModel->findHouseByOwner($houseId, $ownerId);
        if (!$house) {
            throw new Exception("You are not authorized to delete rooms on this boarding house.");
        }

        $existingRoom = $this->roomModel->findRoomById($roomId);
        if (!$existingRoom || (int)$existingRoom['boarding_house_id'] !== $houseId) {
            throw new Exception("The specified room details could not be found.");
        }

        $this->unlinkPhysicalFile($existingRoom['image_path']);

        if (!$this->roomModel->delete($roomId)) {
            throw new Exception("An internal database execution error occurred while clearing room data.");
        }

        return true;
    }

    /**
     * Form data validation rules for room configurations.
     */
    private function validateRoomInputs(array $inputs): void {
        if (empty(trim($inputs['room_name']))) {
            throw new Exception("Room identifier or Room Name cannot be left blank.");
        }

        if (!isset($inputs['price']) || (float)$inputs['price'] <= 0) {
            throw new Exception("Please specify a valid monthly rent price amount greater than zero.");
        }

        $capacity = isset($inputs['capacity']) ? (int)$inputs['capacity'] : 0;
        $availableBeds = isset($inputs['available_beds']) ? (int)$inputs['available_beds'] : 0;

        if ($capacity <= 0) {
            throw new Exception("Room accommodation capacity must be 1 or higher.");
        }

        if ($availableBeds < 0) {
            throw new Exception("Available beds count cannot be a negative value.");
        }

        if ($availableBeds > $capacity) {
            throw new Exception("Available free beds cannot exceed the maximum room capacity limit.");
        }
    }

    /**
     * Handles binary upload of room-specific media files.
     */
    private function processRoomImageUpload(array $file): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Room photo upload failed with system code: " . $file['error']);
        }

        if ($file['size'] > 3 * 1024 * 1024) {
            throw new Exception("Room thumbnail image must not exceed the maximum 3MB size limit.");
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and WEBP formats are allowed.");
        }

        $targetDir = dirname(dirname(__DIR__)) . '/public/uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $safeName = 'room_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $destination = $targetDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Unable to save room photo to server directory.");
        }

        return 'uploads/' . $safeName;
    }

    /**
     * Safely unlinks a system file from the disk to preserve space.
     */
    private function unlinkPhysicalFile(?string $relativePath): void {
        if (empty($relativePath)) {
            return;
        }

        $absolutePath = dirname(dirname(__DIR__)) . '/public/' . $relativePath;
        if (file_exists($absolutePath) && is_file($absolutePath)) {
            unlink($absolutePath);
        }
    }
}