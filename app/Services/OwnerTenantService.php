<?php
/**
 * File Location: app/Services/OwnerTenantService.php
 * File Name: OwnerTenantService.php
 * Description: Service layer handling checked-out business logic inside secure transactions.
 */

namespace App\Services;

use App\Models\OwnerTenantModel;
use App\Core\Database;
use Exception;
use PDO;

class OwnerTenantService {
    private OwnerTenantModel $tenantModel;
    private PDO $db;

    public function __construct() {
        $this->tenantModel = new OwnerTenantModel();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Safely checks out an occupant, updating status and restoring available bed space.
     */
    public function checkoutTenant(int $applicationId, int $ownerId): bool {
        $tenant = $this->tenantModel->getActiveTenantDetails($applicationId, $ownerId);
        
        if (!$tenant) {
            throw new Exception("The active tenant profile was not found or is unauthorized.");
        }

        try {
            $this->db->beginTransaction();

            // 1. Update status parameters to 'Checked Out'
            $statusUpdated = $this->tenantModel->updateStatus($applicationId, 'Checked Out');
            if (!$statusUpdated) {
                throw new Exception("Failed to update tenancy status parameters.");
            }

            // 2. Increment available beds inside the target room
            $bedsRestored = $this->tenantModel->incrementRoomBeds((int)$tenant['room_id']);
            if (!$bedsRestored) {
                throw new Exception("Failed to restore room vacancy slots. Available beds cannot exceed maximum capacity.");
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