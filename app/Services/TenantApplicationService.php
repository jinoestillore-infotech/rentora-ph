<?php
/**
 * File Location: app/Services/TenantApplicationService.php
 * File Name: TenantApplicationService.php
 * Description: Business service layer handling input validation and dual upload logic for Tenant applications.
 */

namespace App\Services;

use App\Models\TenantApplicationModel;
use Exception;

class TenantApplicationService {
    private TenantApplicationModel $applicationModel;

    public function __construct() {
        $this->applicationModel = new TenantApplicationModel();
    }

    /**
     * Handle multi-step validation checks and document binary upload integrations.
     */
    public function submitApplication(int $tenantId, int $houseId, array $inputs, array $files): bool {
        $this->validateInputs($inputs);

        $house = $this->applicationModel->getHouseDetails($houseId);
        if (!$house) {
            throw new Exception("The target boarding house is invalid or not approved for bookings.");
        }

        // Validate selected room
        $roomId = (int)($inputs['room_id'] ?? 0);
        $rooms = $this->applicationModel->getAvailableRooms($houseId);
        $roomIds = array_column($rooms, 'id');

        if (!in_array($roomId, $roomIds)) {
            throw new Exception("Please select a valid, vacant room selection from the list.");
        }

        if (!isset($files['verification_id']) || $files['verification_id']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception("Your primary identity verification ID document is required.");
        }

        if (!isset($files['emergency_verification_id']) || $files['emergency_verification_id']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception("An identity verification ID for your emergency contact person is required.");
        }

        // Secure file system uploads
        $tenantIdPath = $this->uploadDocument($files['verification_id'], 'tenant_id_');
        $emergencyIdPath = $this->uploadDocument($files['emergency_verification_id'], 'emergency_id_');

        $applicationData = [
            'tenant_id'                       => $tenantId,
            'boarding_house_id'               => $houseId,
            'room_id'                         => $roomId,
            'firstname'                       => trim($inputs['firstname']),
            'lastname'                        => trim($inputs['lastname']),
            'middlename'                      => !empty($inputs['middlename']) ? trim($inputs['middlename']) : null,
            'permanent_address'               => trim($inputs['permanent_address']),
            'age'                             => (int)$inputs['age'],
            'contact_number'                  => trim($inputs['contact_number']),
            'email'                           => trim($inputs['email']),
            'verification_id_path'            => $tenantIdPath,
            'emergency_fullname'              => trim($inputs['emergency_fullname']),
            'emergency_contact_number'        => trim($inputs['emergency_contact_number']),
            'emergency_verification_id_path'  => $emergencyIdPath
        ];

        if (!$this->applicationModel->createApplication($applicationData)) {
            $this->cleanupFile($tenantIdPath);
            $this->cleanupFile($emergencyIdPath);
            throw new Exception("Database registration failed. Please contact support.");
        }

        return true;
    }

    /**
     * Fetch house details context payload.
     */
    public function getHouseContext(int $houseId): array {
        $house = $this->applicationModel->getHouseDetails($houseId);
        if (!$house) {
            throw new Exception("Target property profile not found or pending.");
        }

        $rooms = $this->applicationModel->getAvailableRooms($houseId);
        return [
            'house' => $house,
            'rooms' => $rooms
        ];
    }

    /**
     * Rigorous input checks for application fields.
     */
    private function validateInputs(array $inputs): void {
        $requiredFields = [
            'firstname'                => "First name",
            'lastname'                 => "Last name",
            'permanent_address'        => "Complete permanent address",
            'age'                      => "Age",
            'contact_number'           => "Contact number",
            'email'                    => "Email address",
            'emergency_fullname'       => "Emergency contact's full name",
            'emergency_contact_number' => "Emergency contact number"
        ];

        foreach ($requiredFields as $key => $label) {
            if (empty(trim($inputs[$key] ?? ''))) {
                throw new Exception("{$label} field is mandatory and cannot be left blank.");
            }
        }

        if (!filter_var($inputs['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please specify a valid, deliverable email address.");
        }

        $age = (int)($inputs['age'] ?? 0);
        if ($age < 15 || $age > 100) {
            throw new Exception("Please specify a valid age between 15 and 100.");
        }
    }

    /**
     * Upload and secure validation binary.
     */
    private function uploadDocument(array $file, string $prefix): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Identity upload failed with error code: " . $file['error']);
        }

        if ($file['size'] > 3 * 1024 * 1024) {
            throw new Exception("Verification document sizes must not exceed the maximum 3MB limit.");
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Invalid file type. Please upload a clear photo or PDF (JPG, PNG, PDF).");
        }

        $targetDir = dirname(dirname(__DIR__)) . '/public/uploads/applications/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $safeName = $prefix . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $destination = $targetDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Failed to secure local storage directory for document uploads.");
        }

        return 'uploads/applications/' . $safeName;
    }

    /**
     * Clean up written files on system transaction rollback.
     */
    private function cleanupFile(string $relativePath): void {
        $absolutePath = dirname(dirname(__DIR__)) . '/public/' . $relativePath;
        if (file_exists($absolutePath) && is_file($absolutePath)) {
            unlink($absolutePath);
        }
    }
}