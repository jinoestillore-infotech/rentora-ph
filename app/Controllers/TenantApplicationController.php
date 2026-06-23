<?php
/**
 * File Location: app/Controllers/TenantApplicationController.php
 * File Name: TenantApplicationController.php
 * Description: Controller managing session validation, validation states, and submissions for the Tenant Apply form.
 */

namespace App\Controllers;

use App\Core\Security;
use App\Services\TenantApplicationService;
use Exception;

class TenantApplicationController {
    private TenantApplicationService $applicationService;

    public function __construct() {
        $this->applicationService = new TenantApplicationService();
    }

    /**
     * Restrict entry strictly to Tenant roles.
     */
    private function checkTenantAccess(): void {
        Security::startSecureSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Tenant') {
            $_SESSION['error'] = "Access restricted. Tenant authentication is required.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Render standalone page form for rental applications.
     * Maps to GET /tenant/house/apply/{houseId}
     */
    public function showApplyForm(mixed $houseId = 0): void {
        $this->checkTenantAccess();
        $houseId = (int)$houseId;

        try {
            $context = $this->applicationService->getHouseContext($houseId);
            $house = $context['house'];
            $rooms = $context['rooms'];

            $error = $_SESSION['error'] ?? null;
            $old = $_SESSION['old_input'] ?? [];
            unset($_SESSION['error'], $_SESSION['old_input']);

            require_once dirname(__DIR__, 2) . '/views/tenant/apply.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/tenant/browse");
            exit();
        }
    }

    /**
     * Manage Tenant apply request POST streams.
     * Maps to POST /tenant/house/apply
     */
    public function handleApply(): void {
        $this->checkTenantAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Anti-forgery validation token has expired. Please try again.";
            header("Location: " . BASE_URL . "/tenant/dashboard");
            exit();
        }

        $houseId = isset($_POST['boarding_house_id']) ? (int)$_POST['boarding_house_id'] : 0;
        $tenantId = (int)$_SESSION['user_id'];

        try {
            // Service processes form elements and dual physical file uploads
            $this->applicationService->submitApplication($tenantId, $houseId, $_POST, $_FILES);
            
            $_SESSION['success'] = "Rental application submitted successfully! Please wait for the landlord's review.";
            header("Location: " . BASE_URL . "/tenant/dashboard");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            header("Location: " . BASE_URL . "/tenant/house/apply/" . $houseId);
            exit();
        }
    }
}