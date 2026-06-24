<?php
/**
 * File Location: app/Controllers/OwnerApplicationController.php
 * File Name: OwnerApplicationController.php
 * Description: Controller managing routing actions for landlords reviewing tenant applications.
 */

namespace App\Controllers;

use App\Core\Security;
use App\Models\OwnerApplicationModel;
use App\Services\OwnerApplicationService;
use Exception;

class OwnerApplicationController {
    private OwnerApplicationModel $applicationModel;
    private OwnerApplicationService $applicationService;

    public function __construct() {
        $this->applicationModel = new OwnerApplicationModel();
        $this->applicationService = new OwnerApplicationService();
    }

    /**
     * Enforce security access to validated Owners only.
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
     * Renders incoming tenant applications list for Owner.
     * Maps to GET /owner/applications
     */
    public function index(): void {
        $this->checkOwnerAccess();
        $ownerId = (int)$_SESSION['user_id'];

        $applications = $this->applicationModel->getApplicationsByOwnerId($ownerId);

        // Fetch notifications
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/owner/applications.php';
    }

    /**
     * Detailed preview inspector of single application.
     * Maps to GET /owner/application/view/{id}
     */
    public function view(mixed $id = 0): void {
        $this->checkOwnerAccess();
        $applicationId = (int)$id;
        $ownerId = (int)$_SESSION['user_id'];

        $application = $this->applicationModel->getApplicationDetails($applicationId, $ownerId);

        if (!$application) {
            $_SESSION['error'] = "The requested tenant application was not found or is unauthorized.";
            header("Location: " . BASE_URL . "/owner/applications");
            exit();
        }

        $error = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['error'], $_SESSION['success']);

        require_once dirname(__DIR__, 2) . '/views/owner/view_application.php';
    }

    /**
     * Handles POST request to approve a pending application.
     * Maps to POST /owner/application/approve
     */
    public function approve(): void {
        $this->checkOwnerAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Anti-forgery token expired. Please reload and try again.";
            header("Location: " . BASE_URL . "/owner/applications");
            exit();
        }

        $applicationId = isset($_POST['application_id']) ? (int)$_POST['application_id'] : 0;
        $ownerId = (int)$_SESSION['user_id'];

        try {
            $this->applicationService->approveApplication($applicationId, $ownerId);
            $_SESSION['success'] = "Tenancy booking request approved successfully! Room slots have been updated.";
            header("Location: " . BASE_URL . "/owner/applications");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/owner/application/view/" . $applicationId);
            exit();
        }
    }

    /**
     * Handles POST request to reject a pending application.
     * Maps to POST /owner/application/reject
     */
    public function reject(): void {
        $this->checkOwnerAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Anti-forgery token expired. Please reload and try again.";
            header("Location: " . BASE_URL . "/owner/applications");
            exit();
        }

        $applicationId = isset($_POST['application_id']) ? (int)$_POST['application_id'] : 0;
        $reason = $_POST['reason'] ?? '';
        $ownerId = (int)$_SESSION['user_id'];

        try {
            // Service receives the application ID and the typed reason for validation and logging
            $this->applicationService->rejectApplication($applicationId, $ownerId, $reason);
            $_SESSION['success'] = "The tenancy application has been marked as Rejected and the reason was logged.";
            header("Location: " . BASE_URL . "/owner/applications");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . BASE_URL . "/owner/application/view/" . $applicationId);
            exit();
        }
    }

    /**
     * Handles POST request to delete a rejected application.
     * Maps to POST /owner/application/delete
     */
    public function delete(): void {
        $this->checkOwnerAccess();

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Anti-forgery token expired. Please reload and try again.";
            header("Location: " . BASE_URL . "/owner/applications");
            exit();
        }

        $applicationId = isset($_POST['application_id']) ? (int)$_POST['application_id'] : 0;
        $ownerId = (int)$_SESSION['user_id'];

        try {
            // Fetch application details to assert owner legitimacy and status
            $app = $this->applicationModel->getApplicationDetails($applicationId, $ownerId);
            if (!$app || $app['status'] !== 'Rejected') {
                throw new Exception("Unauthorized or only rejected applications can be deleted.");
            }

            $basePublicPath = dirname(dirname(__DIR__)) . '/public/';
            
            // Delete uploaded Verification ID File
            if (!empty($app['verification_id_path'])) {
                $file = $basePublicPath . $app['verification_id_path'];
                if (file_exists($file) && is_file($file)) {
                    unlink($file);
                }
            }

            // Delete uploaded Emergency ID File
            if (!empty($app['emergency_verification_id_path'])) {
                $file = $basePublicPath . $app['emergency_verification_id_path'];
                if (file_exists($file) && is_file($file)) {
                    unlink($file);
                }
            }

            $this->applicationModel->delete($applicationId);
            $_SESSION['success'] = "Rejected application deleted successfully.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: " . BASE_URL . "/owner/applications");
        exit();
    }
}