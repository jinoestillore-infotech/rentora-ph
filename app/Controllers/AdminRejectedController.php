<?php
/**
 * File Location: app/Controllers/AdminRejectedController.php
 * File Name: AdminRejectedController.php
 * Description: Controller managing routing endpoints for rejected properties and justification logs.
 */

namespace App\Controllers;

use App\Core\Security;
use App\Models\AdminRejectedHouse;
use App\Services\AdminRejectedService;
use Exception;

class AdminRejectedController {
    private AdminRejectedHouse $rejectedModel;
    private AdminRejectedService $rejectedService;

    public function __construct() {
        $this->rejectedModel = new AdminRejectedHouse();
        $this->rejectedService = new AdminRejectedService();
    }

    /**
     * Gate entrance strictly to administrators.
     */
    private function checkAdminAccess(): void {
        Security::startSecureSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            $_SESSION['error'] = "Access denied. Administrator session is required.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Displays a searchable, paginated index of rejected properties.
     * Maps to GET /admin/rejected-houses
     */
    public function rejectedList(): void {
        $this->checkAdminAccess();

        $properties = $this->rejectedModel->getRejectedProperties();

        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/admin/rejected_houses.php';
    }

    /**
     * Detail inspector for specific rejected boarding house file.
     * Maps to GET /admin/rejected-house/view/{houseId}
     */
    public function showRejectedDetail(mixed $houseId = 0): void {
        $this->checkAdminAccess();

        $houseId = (int)$houseId;
        $house = $this->rejectedModel->findDetailsById($houseId);

        if (!$house) {
            $_SESSION['error'] = "The requested rejected listing profile could not be found.";
            header("Location: " . BASE_URL . "/admin/rejected-houses");
            exit();
        }

        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);

        require_once dirname(__DIR__, 2) . '/views/admin/view_rejected.php';
    }

    /**
     * Handles administrative saving/editing of justification reason text.
     * Maps to POST /admin/rejected-house/reason
     */
    public function updateReason(): void {
        $this->checkAdminAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Anti-forgery token expired. Please reload and try again.";
            header("Location: " . BASE_URL . "/admin/rejected-houses");
            exit();
        }

        $houseId = isset($_POST['house_id']) ? (int)$_POST['house_id'] : 0;
        $reason = $_POST['reason'] ?? '';

        try {
            $this->rejectedService->updateRejectionReason($houseId, $reason);
            $_SESSION['success'] = "The justification reason has been logged and updated successfully.";
            header("Location: " . BASE_URL . "/admin/rejected-house/view/" . $houseId);
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/admin/rejected-house/view/" . $houseId);
            exit();
        }
    }

    /**
     * Permanent delete operation for rejected file.
     * Maps to POST /admin/rejected-house/delete
     */
    public function handleDelete(): void {
        $this->checkAdminAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Anti-forgery token expired. Please try again.";
            header("Location: " . BASE_URL . "/admin/rejected-houses");
            exit();
        }

        $houseId = isset($_POST['house_id']) ? (int)$_POST['house_id'] : 0;

        try {
            $this->rejectedService->deleteProperty($houseId);
            $_SESSION['success'] = "The rejected listing profile has been deleted permanently from disk and database.";
            header("Location: " . BASE_URL . "/admin/rejected-houses");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/admin/rejected-houses");
            exit();
        }
    }
}