<?php

namespace App\Controllers;

use App\Core\Security;
use App\Models\BoardingHouse;
use App\Models\OwnerApplicationModel;

use Exception;

class OwnerController {
    private BoardingHouse $houseModel;

    public function __construct() {
        $this->houseModel = new BoardingHouse();
    }

    /**
     * Enforce strict Role-Based Access Control (RBAC) safeguards.
     */
    private function checkOwnerAccess(): void {
        Security::startSecureSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Owner') {
            $_SESSION['error'] = "Unauthorized access. Please log in with an Owner account.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Renders the primary Owner Dashboard.
     * Maps to GET /owner/dashboard
     */
    public function dashboard(): void {
        $this->checkOwnerAccess();

        $ownerId = (int)$_SESSION['user_id'];
        
        // Fetch properties and analytics
        $stats = $this->houseModel->getStatsByOwnerId($ownerId);
        $allProperties = $this->houseModel->getByOwnerId($ownerId);

        // Filter properties array to show ONLY Approved and Pending properties in the dashboard cards
        $properties = array_filter($allProperties, function($house) {
            return in_array($house['status'], ['Approved', 'Pending']);
        });

        // Calculate rejected count separately from the full list to trigger the warning banner
        $rejectedCount = count(array_filter($allProperties, function($house) {
            return $house['status'] === 'Rejected';
        }));
        
        // Fetch pending tenant applications count
        $appModel = new OwnerApplicationModel();
        $allApplications = $appModel->getApplicationsByOwnerId($ownerId);
        $pendingAppsCount = count(array_filter($allApplications, function($app) {
            return $app['status'] === 'Pending';
        }));

        // Flash message handling
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/owner/dashboard.php';
    }

    /**
     * Renders the dedicated Boarding House Registration Page.
     * Maps to GET /owner/add-house
     */
    public function showAddHouseForm(): void {
        $this->checkOwnerAccess();
        require_once dirname(__DIR__, 2) . '/views/owner/add_house.php';
    }

    /**
     * Register/Create a new Boarding House with binary file streams.
     * Maps to POST /owner/add-house
     */
    public function addHouse(): void {
        $this->checkOwnerAccess();

        // Validate Anti-Forgery Token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Invalid CSRF verification token. Please try again.";
            $_SESSION['old_input'] = $_POST;
            header("Location: " . BASE_URL . "/owner/add-house");
            exit();
        }

        // Sanitize overall inputs (except file pointers)
        $cleanData = Security::sanitize($_POST);

        $name = $cleanData['name'] ?? '';
        $town = $cleanData['town'] ?? '';
        $address = $cleanData['address'] ?? '';
        $contact = $cleanData['contact_number'] ?? '';

        // Validation Rules
        if (empty($name) || empty($town) || empty($address) || empty($contact)) {
            $_SESSION['error'] = "Property Name, Town, Complete Address, and Contact Number are required.";
            $_SESSION['old_input'] = $cleanData;
            header("Location: " . BASE_URL . "/owner/add-house");
            exit();
        }

        try {
            // 1. Process Property Image Upload
            if (!isset($_FILES['image_path']) || $_FILES['image_path']['error'] === UPLOAD_ERR_NO_FILE) {
                throw new Exception("A featured property facade image is required.");
            }
            $uploadedImagePath = $this->processUpload($_FILES['image_path'], ['jpg', 'jpeg', 'png', 'webp'], 'img_');

            // 2. Process Legality Verification Document Upload
            if (!isset($_FILES['legality_proof']) || $_FILES['legality_proof']['error'] === UPLOAD_ERR_NO_FILE) {
                throw new Exception("An ownership permit or document is required for verification.");
            }
            $uploadedLegalityPath = $this->processUpload($_FILES['legality_proof'], ['jpg', 'jpeg', 'png', 'webp', 'pdf'], 'legal_');

            $insertData = [
                'owner_id'       => (int)$_SESSION['user_id'],
                'name'           => $name,
                'description'    => $cleanData['description'] ?? '',
                'address'        => $address,
                'town'           => $town,
                'contact_number' => $contact,
                'amenities'      => $cleanData['amenities'] ?? '',
                'house_rules'    => $cleanData['house_rules'] ?? '',
                'image_path'     => $uploadedImagePath,
                'legality_proof' => $uploadedLegalityPath
            ];

            if ($this->houseModel->create($insertData)) {
                $_SESSION['success'] = "Boarding House listing created successfully! Please wait for admin approval before adding rooms.";
                header("Location: " . BASE_URL . "/owner/dashboard");
                exit();
            } else {
                throw new Exception("Unable to save property record. Please check database connectivity.");
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['old_input'] = $cleanData;
            header("Location: " . BASE_URL . "/owner/add-house");
            exit();
        }
    }

    /**
     * Helper routine to securely manage incoming binary file uploads.
     * Returns the web-accessible relative path to store in database.
     */
    private function processUpload(array $file, array $allowedExtensions, string $prefix): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload failed with error code: " . $file['error']);
        }

        // Limit individual file size to 5MB max
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("The uploaded file exceeds the maximum 5MB size limit.");
        }

        $origName = $file['name'];
        $extension = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Invalid file format. Allowed extensions: " . implode(', ', $allowedExtensions));
        }

        // Establish destination directory safely
        $targetDir = dirname(__DIR__, 2) . '/public/uploads/';
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception("Server folder creation failed. Please check file write permissions.");
            }
        }

        // Generate a completely unique, safe hashed filename
        $safeFileName = $prefix . bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $destinationPath = $targetDir . $safeFileName;

        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            throw new Exception("Failed to save the uploaded file on the server.");
        }

        // Return relative path for web rendering: "uploads/safe_filename.ext"
        return 'uploads/' . $safeFileName;
    }
}