<?php
/**
 * File Location: app/Controllers/OwnerTenantController.php
 * File Name: OwnerTenantController.php
 * Description: Controller managing routing requests for active occupants, filtered query routing, and checkouts.
 */

namespace App\Controllers;

use App\Core\Security;
use App\Models\OwnerTenantModel;
use App\Services\OwnerTenantService;
use App\Models\RoomModel;

use Exception;

class OwnerTenantController {
    private OwnerTenantModel $tenantModel;
    private OwnerTenantService $tenantService;
    private RoomModel $roomModel;

    public function __construct() {
        $this->tenantModel = new OwnerTenantModel();
        $this->tenantService = new OwnerTenantService();
        $this->roomModel = new RoomModel();
    }

    /**
     * Enforce security guidelines to validated Owner accounts only.
     */
    private function checkOwnerAccess(): void {
        Security::startSecureSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Owner') {
            $_SESSION['error'] = "Access denied. Landlord credentials are required.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Displays active occupants registry list for the landlord.
     * Maps to GET /owner/tenants
     */
    public function index(mixed $roomId = null): void {
        $this->checkOwnerAccess();
        $ownerId = (int)$_SESSION['user_id'];

        // Convert the dynamic route parameter cleanly
        $filteredRoomId = $roomId !== null ? (int)$roomId : null;
        
        $selectedRoom = null;
        if ($filteredRoomId) {
            $selectedRoom = $this->roomModel->findRoomById($filteredRoomId);
            
            // Security verification: ensure targeted room belongs to a boarding house owned by this landlord
            if ($selectedRoom) {
                $house = $this->roomModel->findHouseByOwner((int)$selectedRoom['boarding_house_id'], $ownerId);
                if (!$house) {
                    $_SESSION['error'] = "Unauthorized access to room directories.";
                    header("Location: " . BASE_URL . "/owner/tenants");
                    exit();
                }
            } else {
                $_SESSION['error'] = "The specified room directory could not be found.";
                header("Location: " . BASE_URL . "/owner/tenants");
                exit();
            }
        }

        $tenants = $this->tenantModel->getActiveTenantsByOwnerId($ownerId, $filteredRoomId);

        // Fetch notifications
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/owner/tenants.php';
    }

    /**
     * Handles checkout form submissions.
     * Maps to POST /owner/tenant/checkout
     */
    public function checkout(): void {
        $this->checkOwnerAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Anti-forgery token verification expired. Please try again.";
            header("Location: " . BASE_URL . "/owner/tenants");
            exit();
        }

        $applicationId = isset($_POST['application_id']) ? (int)$_POST['application_id'] : 0;
        $ownerId = (int)$_SESSION['user_id'];

        try {
            $this->tenantService->checkoutTenant($applicationId, $ownerId);
            $_SESSION['success'] = "Renter checked out successfully! Room vacancy slot has been restored.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: " . BASE_URL . "/owner/tenants");
        exit();
    }
}