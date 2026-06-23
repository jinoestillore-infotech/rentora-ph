<?php
namespace App\Services;

use App\Models\BoardingHouse;
use Exception;

class PropertyService {
    private $houseModel;

    public function __construct() {
        $this->houseModel = new BoardingHouse();
    }

    /**
     * Validates and registers a new boarding house with file streams
     */
    public function registerHouse(array $data, array $files): bool {
        // Basic required checks
        if (empty($data['name']) || empty($data['town']) || empty($data['contact_number']) || empty($data['address'])) {
            throw new Exception("All mandatory fields marked with an asterisk (*) must be completed.");
        }

        // 1. Process Property Image Upload
        if (empty($files['image_path']['tmp_name'])) {
            throw new Exception("A featured property facade image is required.");
        }
        $imagePath = $this->uploadFile($files['image_path'], ['jpg', 'jpeg', 'png', 'webp'], 'property_img_');

        // 2. Process Legality Proof Document Upload
        if (empty($files['legality_proof']['tmp_name'])) {
            throw new Exception("An ownership permit or document is required for verification.");
        }
        $legalityPath = $this->uploadFile($files['legality_proof'], ['jpg', 'jpeg', 'png', 'webp', 'pdf'], 'legal_doc_');

        // Append saved system file references to data object
        $data['image_path'] = $imagePath;
        $data['legality_proof'] = $legalityPath;

        // Save to Database
        if (!$this->houseModel->create($data)) {
            throw new Exception("Something went wrong saving the application. Please try again.");
        }

        return true;
    }

    /**
     * Helper tool to handle system uploads safely
     */
    private function uploadFile(array $file, array $allowedExtensions, string $prefix): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload failed with error code: " . $file['error']);
        }

        $filename = $file['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            throw new Exception("Invalid file type format. Allowed types: " . implode(', ', $allowedExtensions));
        }

        // Limit file size to 5MB max
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("The file size exceeds the maximum limit of 5MB.");
        }

        // Create directory structure safely if it doesn't exist
        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate a completely unique encrypted file hash string
        $newFileName = $prefix . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
        $targetPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("Failed to move uploaded file to safe server directory.");
        }

        // Return relative target folder path reference for base rendering engine later
        return 'uploads/' . $newFileName;
    }
}