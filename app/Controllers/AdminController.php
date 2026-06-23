<?php
/**
 * File Location: app/Controllers/AdminController.php
 * File Name: AdminController.php
 * Description: Controller managing routing requests for verification logs and dashboard indexes.
 */

namespace App\Controllers;

use App\Core\Security;
use App\Models\AdminBoardingHouse;
use App\Services\AdminService;
use Exception;

class AdminController {
    private AdminBoardingHouse $adminModel;
    private AdminService $adminService;

    public function __construct() {
        $this->adminModel = new AdminBoardingHouse();
        $this->adminService = new AdminService();
    }

    /**
     * Restrict entry to authorized Admin accounts only.
     */
    private function checkAdminAccess(): void {
        Security::startSecureSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            $_SESSION['error'] = "Unauthorized entry. Administrator privileges are required.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Render general Administrator dashboard grid.
     * Maps to GET /admin/dashboard
     */
    public function dashboard(): void {
        $this->checkAdminAccess();

        $stats = $this->adminModel->getGlobalStats();
        $allProperties = $this->adminModel->getAllWithOwners();
        $properties = $this->adminModel->getAllWithOwners();

        $pendingProperties = array_filter($allProperties, function($house) {
            return $house['status'] === 'Pending';
        });

        // Flush active flash alerts
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/admin/dashboard.php';
    }

    /**
     * Render dedicated split comparison verification display page.
     * Maps to GET /admin/verify-house
     */
    public function showVerifyForm(mixed $houseId = 0): void {
        $this->checkAdminAccess();

        $houseId = (int)$houseId;
        $house = $this->adminModel->findDetailsById($houseId);

        if (!$house) {
            $_SESSION['error'] = "Boarding House record not found.";
            header("Location: " . BASE_URL . "/admin/dashboard");
            exit();
        }

        // Flush active errors inside detail page
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/admin/verify_house.php';
    }

    /**
     * Render the list of approved boarding houses.
     * Maps to GET /admin/approved-houses
     */
    public function approvedList(): void {
        $this->checkAdminAccess();

        $properties = $this->adminModel->getApprovedProperties();
        $stats = $this->adminModel->getGlobalStats();

        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/admin/approved_houses.php';
    }

    /**
     * Render detail inspector for a specific approved asset.
     * Maps to GET /admin/approved-house/view
     */
    public function showApprovedDetail(mixed $houseId = 0): void {
        $this->checkAdminAccess();

        $houseId = (int)$houseId;
        $house = $this->adminModel->findDetailsById($houseId);

        if (!$house || $house['status'] !== 'Approved') {
            $_SESSION['error'] = "Boarding House record not found or is not approved.";
            header("Location: " . BASE_URL . "/admin/approved-houses");
            exit();
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/admin/view_approved.php';
    }

    /**
     * Handles administrative cascading delete action.
     * Maps to POST /admin/approved-house/delete
     */
    public function handleDelete(): void {
        $this->checkAdminAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Invalid CSRF verification token. Please try again.";
            header("Location: " . BASE_URL . "/admin/approved-houses");
            exit();
        }

        $houseId = isset($_POST['house_id']) ? (int)$_POST['house_id'] : 0;

        try {
            $this->adminService->deleteProperty($houseId);
            $_SESSION['success'] = "Approved property was permanently deleted from the system.";
            header("Location: " . BASE_URL . "/admin/approved-houses");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/admin/approved-house/view?house_id=" . $houseId);
            exit();
        }
    }

    /**
     * Action post back to process Approve or Reject.
     * Maps to POST /admin/verify-house
     */
    public function handleVerify(): void {
        $this->checkAdminAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Invalid CSRF verification token. Please try again.";
            header("Location: " . BASE_URL . "/admin/dashboard");
            exit();
        }

        $houseId = isset($_POST['house_id']) ? (int)$_POST['house_id'] : 0;
        $action = $_POST['status_action'] ?? ''; // Expects 'Approved' or 'Rejected'

        try {
            $this->adminService->verifyProperty($houseId, $action);
            
            $_SESSION['success'] = "Property " . htmlspecialchars($action) . " successfully!";
            header("Location: " . BASE_URL . "/admin/dashboard");
            exit();

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/admin/verify-house?house_id=" . $houseId);
            exit();
        }
    }
}