<?php
/**
 * File Location: app/Controllers/AdminUserController.php
 * File Name: AdminUserController.php
 * Description: Controller managing routing endpoints for user and owner account administrative review.
 */

namespace App\Controllers;

use App\Core\Security;
use App\Models\AdminUserModel;
use App\Services\AdminUserService;
use Exception;

class AdminUserController {
    private AdminUserModel $userModel;
    private AdminUserService $userService;

    public function __construct() {
        $this->userModel = new AdminUserModel();
        $this->userService = new AdminUserService();
    }

    /**
     * Enforce security access to authorized Administrators only.
     */
    private function checkAdminAccess(): void {
        Security::startSecureSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            $_SESSION['error'] = "Unauthorized access. Administrator session is required.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Display searchable user account registry index.
     * Maps to GET /admin/users
     */
    public function index(): void {
        $this->checkAdminAccess();

        $users = $this->userModel->getAllUsersWithStats();

        // Flush active flash alerts
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/admin/users.php';
    }

    /**
     * Process user suspension or reactivation triggers.
     * Maps to POST /admin/user/toggle-status
     */
    public function toggleStatus(): void {
        $this->checkAdminAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Invalid verification token. Please try again.";
            header("Location: " . BASE_URL . "/admin/users");
            exit();
        }

        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
        $currentAdminId = (int)$_SESSION['user_id'];

        try {
            $newStatus = $this->userService->toggleStatus($userId, $currentAdminId);
            $_SESSION['success'] = "User account status has been updated successfully to '{$newStatus}'.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: " . BASE_URL . "/admin/users");
        exit();
    }
}