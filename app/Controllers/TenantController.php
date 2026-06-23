<?php
/**
 * File Location: app/Controllers/TenantController.php
 * File Name: TenantController.php
 * Description: Controller managing routing actions for Tenant exploration and property details.
 */

namespace App\Controllers;

use App\Core\Security;
use App\Services\TenantService;
use Exception;

class TenantController {
    private TenantService $tenantService;

    public function __construct() {
        $this->tenantService = new TenantService();
    }

    /**
     * Restrict workspace strictly to validated Tenant roles.
     */
    private function checkTenantAccess(): void {
        Security::startSecureSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Tenant') {
            $_SESSION['error'] = "Unauthorized access. Renter credentials are required.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Display landing dashboard console for Tenants.
     * Maps to GET /tenant/dashboard
     */
    public function dashboard(): void {
        $this->checkTenantAccess();

        $stats = [];
        $recent = [];
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        try {
            $dashboardData = $this->tenantService->getDashboardData();
            $stats = $dashboardData['stats'];
            $recent = $dashboardData['recent'];
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        require_once dirname(__DIR__, 2) . '/views/tenant/dashboard.php';
    }

    /**
     * Display searchable exploration board for Tenants.
     * Maps to GET /tenant/browse
     */
    public function index(): void {
        $this->checkTenantAccess();

        $properties = [];
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        try {
            $properties = $this->tenantService->searchProperties($_GET);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        require_once dirname(__DIR__, 2) . '/views/tenant/browse.php';
    }

    /**
     * Detailed preview of single property with landlord details.
     * Maps to GET /tenant/house/view/{houseId}
     */
    public function viewHouse(mixed $houseId = 0): void {
        $this->checkTenantAccess();
        $houseId = (int)$houseId;

        try {
            $profile = $this->tenantService->getPropertyProfile($houseId);
            $house = $profile['house'];
            $rooms = $profile['rooms'];
            
            require_once dirname(__DIR__, 2) . '/views/tenant/view_house.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/tenant/dashboard");
            exit();
        }
    }
}