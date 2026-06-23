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