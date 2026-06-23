<?php
/**
 * File Location: app/Controllers/OwnerRejectedController.php
 * File Name: OwnerRejectedController.php
 * Description: Controller managing routing requests for Owners' rejected properties console.
 */

namespace App\Controllers;

use App\Core\Security;
use App\Services\OwnerRejectedService;
use Exception;

class OwnerRejectedController {
    private OwnerRejectedService $rejectedService;

    public function __construct() {
        $this->rejectedService = new OwnerRejectedService();
    }

    /**
     * Enforce security access to authorized Owner accounts only.
     */
    private function checkOwnerAccess(): void {
        Security::startSecureSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Owner') {
            $_SESSION['error'] = "Access denied. Owner permissions are required.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Renders the page view of rejected boarding houses.
     * Maps to GET /owner/rejected-houses
     */
    public function rejectedList(): void {
        $this->checkOwnerAccess();

        $ownerId = (int)$_SESSION['user_id'];
        $properties = [];
        $error = null;

        try {
            $properties = $this->rejectedService->getRejectedProperties($ownerId);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        require_once dirname(__DIR__, 2) . '/views/owner/rejected_houses.php';
    }
}