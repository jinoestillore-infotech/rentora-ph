<?php
/**
 * File Location: app/Models/AdminBoardingHouse.php
 * File Name: AdminBoardingHouse.php
 * Description: Dedicated database interaction model for system Administrators.
 */

namespace App\Models;

use App\Core\Database;
use PDO;

class AdminBoardingHouse {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Fetch global aggregate metrics for the Admin Dashboard.
     */
    public function getGlobalStats(): array {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
                FROM boarding_houses";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0
        ];
    }

    /**
     * Fetch all registered boarding houses, joining with user data.
     */
    public function getAllWithOwners(): array {
        $sql = "SELECT bh.*, u.firstname, u.lastname, u.email as owner_email 
                FROM boarding_houses bh
                JOIN users u ON bh.owner_id = u.id
                ORDER BY bh.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single boarding house details combined with owner profiles.
     */
    public function findDetailsById(int $id): array|bool {
        $sql = "SELECT bh.*, u.firstname, u.lastname, u.email as owner_email, u.contact as owner_contact
                FROM boarding_houses bh
                JOIN users u ON bh.owner_id = u.id
                WHERE bh.id = :id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update the approval status of a boarding house.
     */
    public function updateStatus(int $id, string $status): bool {
        $sql = "UPDATE boarding_houses 
                SET status = :status, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }
}