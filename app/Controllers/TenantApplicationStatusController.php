<?php
/**
 * File Location: app/Controllers/TenantApplicationStatusController.php
 * File Name: TenantApplicationStatusController.php
 * Description: Controller managing list visualizations of a tenant's submitted accommodation applications.
 */

namespace App\Controllers;

use App\Core\Security;
use App\Models\TenantApplicationModel;

class TenantApplicationStatusController {
    private TenantApplicationModel $applicationModel;

    public function __construct() {
        $this->applicationModel = new TenantApplicationModel();
    }

    /**
     * Ensure only logged-in Tenants can view their history tracking cards.
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
     * Display tracking dashboard lists of applications.
     * Maps to GET /tenant/applications
     */
    public function index(): void {
        $this->checkTenantAccess();
        $tenantId = (int)$_SESSION['user_id'];

        $applications = $this->applicationModel->getApplicationsByTenantId($tenantId);

        // Flash message handling
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/tenant/applications.php';
    }
}