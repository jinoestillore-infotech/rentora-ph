<?php
/**
 * File Location: app/Services/OwnerApplicationService.php
 * File Name: OwnerApplicationService.php
 * Description: Business transaction service managing tenancy request updates.
 */

namespace App\Services;

use App\Models\OwnerApplicationModel;
use App\Core\Database;
use Exception;
use PDO;

class OwnerApplicationService {
    private OwnerApplicationModel $applicationModel;
    private PDO $db;

    public function __construct() {
        $this->applicationModel = new OwnerApplicationModel();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Safely approves a tenant application inside a database transaction,
     * verifying ownership, vacancy limits, and decrementing available beds.
     */
    public function approveApplication(int $applicationId, int $ownerId): bool {
        $application = $this->applicationModel->getApplicationDetails($applicationId, $ownerId);
        
        if (!$application) {
            throw new Exception("The requested tenancy application was not found or is unauthorized.");
        }

        if ($application['status'] !== 'Pending') {
            throw new Exception("This application has already been processed and is currently marked as " . $application['status'] . ".");
        }

        $availableBeds = (int)$application['available_beds'];
        if ($availableBeds <= 0) {
            throw new Exception("The selected room does not have any vacant beds remaining to complete approval.");
        }

        try {
            $this->db->beginTransaction();

            // 1. Update application status to Approved
            $statusUpdated = $this->applicationModel->updateStatus($applicationId, 'Approved');
            if (!$statusUpdated) {
                throw new Exception("Failed to update tenancy application status.");
            }

            // 2. Decrement the available beds in the target room
            $bedsDecremented = $this->applicationModel->decrementRoomBeds((int)$application['room_id']);
            if (!$bedsDecremented) {
                throw new Exception("Failed to lock room beds reservation. It might have been filled up.");
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Rejects a tenant application, saving a mandatory justification reason.
     */
    public function rejectApplication(int $applicationId, int $ownerId, string $reason): bool {
        $application = $this->applicationModel->getApplicationDetails($applicationId, $ownerId);
        
        if (!$application) {
            throw new Exception("The requested tenancy application was not found or is unauthorized.");
        }

        if ($application['status'] !== 'Pending') {
            throw new Exception("This application has already been processed and is currently marked as " . $application['status'] . ".");
        }

        $trimmedReason = trim($reason);
        if (empty($trimmedReason)) {
            throw new Exception("Please specify a valid reason for rejecting this tenancy application.");
        }

        if (strlen($trimmedReason) > 1000) {
            throw new Exception("Rejection reasons must not exceed 1000 characters.");
        }

        try {
            $this->db->beginTransaction();

            // 1. Update application status flag to Rejected
            $statusUpdated = $this->applicationModel->updateStatus($applicationId, 'Rejected');
            if (!$statusUpdated) {
                throw new Exception("Failed to update status parameters on application rejection.");
            }

            // 2. Insert or update the rejection justification text
            $reasonSaved = $this->applicationModel->saveRejectionReason($applicationId, $trimmedReason);
            if (!$reasonSaved) {
                throw new Exception("An internal error occurred while saving the rejection reason.");
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}