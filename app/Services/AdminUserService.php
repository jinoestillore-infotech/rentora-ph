<?php
/**
 * File Location: app/Services/AdminUserService.php
 * File Name: AdminUserService.php
 * Description: Business logic service managing checks on user status transitions.
 */

namespace App\Services;

use App\Models\AdminUserModel;
use Exception;

class AdminUserService {
    private AdminUserModel $userModel;

    public function __construct() {
        $this->userModel = new AdminUserModel();
    }

    /**
     * Toggles a user's status between Active and Suspended.
     */
    public function toggleStatus(int $userId, int $currentAdminId): string {
        if (empty($userId)) {
            throw new Exception("Invalid user identification reference.");
        }

        if ($userId === $currentAdminId) {
            throw new Exception("Security violation: You cannot suspend your own Administrator account.");
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            throw new Exception("The specified user account could not be found.");
        }

        // Only toggle between Active and Suspended. Keep Inactive as is if custom-set.
        $newStatus = ($user['status'] === 'Suspended') ? 'Active' : 'Suspended';

        $result = $this->userModel->updateStatus($userId, $newStatus);
        if (!$result) {
            throw new Exception("An internal transaction failure occurred while updating the status.");
        }

        return $newStatus;
    }
}