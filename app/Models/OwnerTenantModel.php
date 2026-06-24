<?php
/**
 * File Location: app/Models/OwnerTenantModel.php
 * File Name: OwnerTenantModel.php
 * Description: Premium monochrome-styled model to manage and query active boarding house occupants.
 * Fully optimized with secure PDO prepared statements, transaction integrity hooks, and optional room-level filters.
 */

namespace App\Models;

use App\Core\Database;
use PDO;

class OwnerTenantModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Retrieve active approved tenants belonging to the logged-in owner, optionally filtered by a specific room ID.
     */
    public function getActiveTenantsByOwnerId(int $ownerId, ?int $roomId = null): array {
        $sql = "SELECT ta.id as application_id, ta.firstname, ta.lastname, ta.contact_number, ta.created_at as checkin_date,
                       bh.name as house_name, r.room_name, r.price as room_price, r.id as room_id
                FROM tenant_applications ta
                JOIN boarding_houses bh ON ta.boarding_house_id = bh.id
                JOIN rooms r ON ta.room_id = r.id
                WHERE bh.owner_id = :owner_id 
                  AND ta.status = 'Approved'";
        
        // Append strict room filtering query conditions when room_id is active
        if ($roomId !== null) {
            $sql .= " AND ta.room_id = :room_id";
        }
        
        $sql .= " ORDER BY ta.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $params = [':owner_id' => $ownerId];
        
        if ($roomId !== null) {
            $params[':room_id'] = $roomId;
        }
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve detailed profile parameters of a single active tenant application, ensuring owner legitimacy.
     */
    public function getActiveTenantDetails(int $applicationId, int $ownerId): array|bool {
        $sql = "SELECT ta.*, r.id as room_id 
                FROM tenant_applications ta
                JOIN boarding_houses bh ON ta.boarding_house_id = bh.id
                JOIN rooms r ON ta.room_id = r.id
                WHERE ta.id = :application_id 
                  AND bh.owner_id = :owner_id 
                  AND ta.status = 'Approved'
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':application_id' => $applicationId,
            ':owner_id'       => $ownerId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update status parameters for a target application record.
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
     * Retrieve detailed room specifications combined with boarding house names.
     */
    public function getRoomDetailsForOwner(int $roomId, int $ownerId): array|bool {
        $sql = "SELECT r.id, r.room_name, bh.name as house_name 
                FROM rooms r
                JOIN boarding_houses bh ON r.boarding_house_id = bh.id
                WHERE r.id = :room_id AND bh.owner_id = :owner_id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':room_id' => $roomId,
            ':owner_id' => $ownerId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Increment available beds inside the target room on checkout.
     */
    public function incrementRoomBeds(int $roomId): bool {
        $sql = "UPDATE rooms 
                SET available_beds = available_beds + 1,
                    status = 'Available',
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :room_id AND available_beds < capacity";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':room_id' => $roomId]);
    }
}