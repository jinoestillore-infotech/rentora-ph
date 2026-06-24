<?php
/**
 * File Location: app/Models/OwnerApplicationModel.php
 * File Name: OwnerApplicationModel.php
 * Description: Database model layer for landlords managing incoming tenancy applications.
 */

namespace App\Models;

use App\Core\Database;
use PDO;

class OwnerApplicationModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Fetch all applications submitted to boarding houses owned by the active landlord.
     */
    public function getApplicationsByOwnerId(int $ownerId): array {
        $sql = "SELECT ta.id, ta.firstname, ta.lastname, ta.status, ta.created_at,
                       bh.name as house_name,
                       r.room_name, r.price as room_price
                FROM tenant_applications ta
                JOIN boarding_houses bh ON ta.boarding_house_id = bh.id
                JOIN rooms r ON ta.room_id = r.id
                WHERE bh.owner_id = :owner_id
                ORDER BY ta.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':owner_id' => $ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve detailed profile parameters of a single application, ensuring owner legitimacy.
     */
    public function getApplicationDetails(int $applicationId, int $ownerId): array|bool {
        $sql = "SELECT ta.*, 
                       bh.name as house_name, bh.town as house_town, bh.address as house_address,
                       r.room_name, r.price as room_price, r.available_beds, r.id as room_id
                FROM tenant_applications ta
                JOIN boarding_houses bh ON ta.boarding_house_id = bh.id
                JOIN rooms r ON ta.room_id = r.id
                WHERE ta.id = :application_id AND bh.owner_id = :owner_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':application_id' => $applicationId,
            ':owner_id'       => $ownerId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update the approval status of a specific tenant application.
     */
    public function updateStatus(int $applicationId, string $status): bool {
        $sql = "UPDATE tenant_applications 
                SET status = :status, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $applicationId
        ]);
    }

    /**
     * Writes/logs the reason why a tenant application was rejected by the owner.
     */
    public function saveRejectionReason(int $applicationId, string $reason): bool {
        $sql = "INSERT INTO tenant_application_rejections (application_id, reason) 
                VALUES (:application_id, :reason)
                ON DUPLICATE KEY UPDATE reason = :reason_update, updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':application_id' => $applicationId,
            ':reason'         => $reason,
            ':reason_update'  => $reason
        ]);
    }

    /**
     * Decrement available bed count inside a room when an application gets approved.
     */
    public function decrementRoomBeds(int $roomId): bool {
        $sql = "UPDATE rooms 
                SET available_beds = available_beds - 1,
                    status = CASE WHEN available_beds - 1 = 0 THEN 'Fully Booked' ELSE status END,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :room_id AND available_beds > 0";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':room_id' => $roomId]);
    }

    /**
     * Securely deletes a tenant application from the database records.
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM tenant_applications WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

}