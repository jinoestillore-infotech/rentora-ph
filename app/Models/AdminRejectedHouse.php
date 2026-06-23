<?php
/**
 * File Location: app/Models/AdminRejectedHouse.php
 * File Name: AdminRejectedHouse.php
 * Description: Specialized database interactions for managing rejected properties and reasons.
 */

namespace App\Models;

use App\Core\Database;
use PDO;

class AdminRejectedHouse {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Fetch all rejected boarding houses matching their linked rejection logs if present.
     */
    public function getRejectedProperties(): array {
        $sql = "SELECT bh.*, u.firstname, u.lastname, u.email as owner_email,
                bhr.reason as rejection_reason
                FROM boarding_houses bh
                JOIN users u ON bh.owner_id = u.id
                LEFT JOIN boarding_house_rejections bhr ON bh.id = bhr.boarding_house_id
                WHERE bh.status = 'Rejected'
                ORDER BY bh.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find specific rejected boarding house profile with comprehensive credentials.
     */
    public function findDetailsById(int $id): array|bool {
        $sql = "SELECT bh.*, u.firstname, u.lastname, u.email as owner_email, u.contact as owner_contact,
                bhr.reason as rejection_reason
                FROM boarding_houses bh
                JOIN users u ON bh.owner_id = u.id
                LEFT JOIN boarding_house_rejections bhr ON bh.id = bhr.boarding_house_id
                WHERE bh.id = :id AND bh.status = 'Rejected'
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Upserts (inserts or updates) a rejection reason record.
     */
    public function saveRejectionReason(int $houseId, string $reason): bool {
        $sql = "INSERT INTO boarding_house_rejections (boarding_house_id, reason) 
                VALUES (:bh_id, :reason)
                ON DUPLICATE KEY UPDATE reason = :reason_update, updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':bh_id'         => $houseId,
            ':reason'        => $reason,
            ':reason_update' => $reason
        ]);
    }

    /**
     * Delete rejected asset permanently from db. Cascading rules drop reason log.
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM boarding_houses WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}