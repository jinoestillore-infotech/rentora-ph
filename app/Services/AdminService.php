<?php
/**
 * File Location: app/Services/AdminService.php
 * File Name: AdminService.php
 * Description: Business service layer handling validation rules for boarding house approval workflows.
 */

namespace App\Services;

use App\Models\AdminBoardingHouse;
use Exception;

class AdminService {
    private AdminBoardingHouse $adminModel;

    public function __construct() {
        $this->adminModel = new AdminBoardingHouse();
    }

    /**
     * Validates and executes complete deletion of a boarding house, clearing server storage.
     */
    public function deleteProperty(int $houseId): bool {
        if (empty($houseId)) {
            throw new Exception("Invalid boarding house identification reference.");
        }

        $house = $this->adminModel->findDetailsById($houseId);
        if (!$house) {
            throw new Exception("The specified boarding house record could not be found.");
        }

        // Clean up physical files from server storage if present
        $basePublicPath = dirname(dirname(__DIR__)) . '/public/';
        
        if (!empty($house['image_path'])) {
            $imageFile = $basePublicPath . $house['image_path'];
            if (file_exists($imageFile) && is_file($imageFile)) {
                unlink($imageFile);
            }
        }

        if (!empty($house['legality_proof'])) {
            $legalFile = $basePublicPath . $house['legality_proof'];
            if (file_exists($legalFile) && is_file($legalFile)) {
                unlink($legalFile);
            }
        }

        $result = $this->adminModel->delete($houseId);
        if (!$result) {
            throw new Exception("An internal database transaction failure occurred.");
        }

        return true;
    }
    
    /**
     * Process verification update safely.
     */
    public function verifyProperty(int $houseId, string $action): bool {
        if (empty($houseId)) {
            throw new Exception("Invalid boarding house identification reference.");
        }

        $allowedStatuses = ['Approved', 'Rejected'];
        if (!in_array($action, $allowedStatuses)) {
            throw new Exception("Unsupported status transaction specified.");
        }

        // Verify boarding house existence
        $house = $this->adminModel->findDetailsById($houseId);
        if (!$house) {
            throw new Exception("The specified boarding house record could not be found.");
        }

        $result = $this->adminModel->updateStatus($houseId, $action);
        if (!$result) {
            throw new Exception("An internal database transaction failure occurred.");
        }

        return true;
    }
}