<?php
/**
 * File Location: app/Controllers/RoomController.php
 * File Name: RoomController.php
 * Description: Controller managing routing actions for Room configurations.
 */

namespace App\Controllers;

use App\Core\Security;
use App\Models\RoomModel;
use App\Services\RoomService;
use Exception;

class RoomController {
    private RoomModel $roomModel;
    private RoomService $roomService;

    public function __construct() {
        $this->roomModel = new RoomModel();
        $this->roomService = new RoomService();
    }

    /**
     * Restrict entry strictly to authorized Owners.
     */
    private function checkOwnerAccess(): void {
        Security::startSecureSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Owner') {
            $_SESSION['error'] = "Unauthorized dashboard access. Owner authorization is required.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Retrieve and list all configured rooms under a verified boarding house.
     * Maps to GET /owner/rooms/{houseId}
     */
    public function index(mixed $houseId = 0): void {
        $this->checkOwnerAccess();
        $houseId = (int)$houseId;
        $ownerId = (int)$_SESSION['user_id'];

        // Enforce ownership block to prevent unauthorized inspection
        $house = $this->roomModel->findHouseByOwner($houseId, $ownerId);
        if (!$house) {
            $_SESSION['error'] = "Boarding house listing not found, pending verification, or unauthorized.";
            header("Location: " . BASE_URL . "/owner/dashboard");
            exit();
        }

        $rooms = $this->roomModel->getRoomsByHouseId($houseId);

        // Fetch active notification flashes
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/owner/rooms.php';
    }

    /**
     * Renders standalone page form for adding a room.
     * Maps to GET /owner/room/add/{houseId}
     */
    public function showAddForm(mixed $houseId = 0): void {
        $this->checkOwnerAccess();
        $houseId = (int)$houseId;
        $ownerId = (int)$_SESSION['user_id'];

        $house = $this->roomModel->findHouseByOwner($houseId, $ownerId);
        if (!$house) {
            $_SESSION['error'] = "Unauthorized boarding house access or pending verification status.";
            header("Location: " . BASE_URL . "/owner/dashboard");
            exit();
        }

        require_once dirname(__DIR__, 2) . '/views/owner/add_room.php';
    }

    /**
     * Creates a new room.
     * Maps to POST /owner/room/add
     */
    public function addRoom(): void {
        $this->checkOwnerAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Anti-forgery token verification failed. Please try again.";
            header("Location: " . BASE_URL . "/owner/dashboard");
            exit();
        }

        $houseId = isset($_POST['boarding_house_id']) ? (int)$_POST['boarding_house_id'] : 0;
        $ownerId = (int)$_SESSION['user_id'];

        try {
            $this->roomService->addRoom($houseId, $ownerId, $_POST, $_FILES['image_path'] ?? null);
            $_SESSION['success'] = "New room configuration created successfully.";
            header("Location: " . BASE_URL . "/owner/rooms/" . $houseId);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            header("Location: " . BASE_URL . "/owner/room/add/" . $houseId);
            exit();
        }
    }

    /**
     * Renders standalone page form for editing a room.
     * Maps to GET /owner/room/edit/{houseId}/{roomId}
     */
    public function showEditForm(mixed $houseId = 0, mixed $roomId = 0): void {
        $this->checkOwnerAccess();
        $houseId = (int)$houseId;
        $roomId = (int)$roomId;
        $ownerId = (int)$_SESSION['user_id'];

        $house = $this->roomModel->findHouseByOwner($houseId, $ownerId);
        if (!$house) {
            $_SESSION['error'] = "Unauthorized boarding house access or pending verification status.";
            header("Location: " . BASE_URL . "/owner/dashboard");
            exit();
        }

        $room = $this->roomModel->findRoomById($roomId);
        if (!$room || (int)$room['boarding_house_id'] !== $houseId) {
            $_SESSION['error'] = "The specified room configurations could not be found.";
            header("Location: " . BASE_URL . "/owner/rooms/" . $houseId);
            exit();
        }

        require_once dirname(__DIR__, 2) . '/views/owner/edit_room.php';
    }

    /**
     * Edit structural details of a room.
     * Maps to POST /owner/room/edit
     */
    public function editRoom(): void {
        $this->checkOwnerAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Anti-forgery token verification failed. Please try again.";
            header("Location: " . BASE_URL . "/owner/dashboard");
            exit();
        }

        $roomId = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
        $houseId = isset($_POST['boarding_house_id']) ? (int)$_POST['boarding_house_id'] : 0;
        $ownerId = (int)$_SESSION['user_id'];

        try {
            $this->roomService->updateRoom($roomId, $houseId, $ownerId, $_POST, $_FILES['image_path'] ?? null);
            $_SESSION['success'] = "Room configurations adjusted successfully.";
            header("Location: " . BASE_URL . "/owner/rooms/" . $houseId);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            header("Location: " . BASE_URL . "/owner/room/edit/" . $houseId . "/" . $roomId);
            exit();
        }
    }

    /**
     * Deletes a room specification.
     * Maps to POST /owner/room/delete
     */
    public function deleteRoom(): void {
        $this->checkOwnerAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Anti-forgery token verification failed. Please try again.";
            header("Location: " . BASE_URL . "/owner/dashboard");
            exit();
        }

        $roomId = isset($_POST['room_id']) ? (int)$_POST['room_id'] : 0;
        $houseId = isset($_POST['boarding_house_id']) ? (int)$_POST['boarding_house_id'] : 0;
        $ownerId = (int)$_SESSION['user_id'];

        try {
            $this->roomService->deleteRoom($roomId, $houseId, $ownerId);
            $_SESSION['success'] = "The selected room has been deleted from the database registry.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: " . BASE_URL . "/owner/rooms/" . $houseId);
        exit();
    }
}