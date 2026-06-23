<?php
/**
 * File Location: app/Models/AdminUserModel.php
 * File Name: AdminUserModel.php
 * Description: Model managing administration commands for system user accounts.
 */

namespace App\Models;

use App\Core\Database;
use PDO;

class AdminUserModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Fetch all users along with their total properties registered (especially for Owners).
     */
    public function getAllUsersWithStats(): array {
        $sql = "SELECT u.id, u.firstname, u.lastname, u.email, u.contact, u.role, u.status, u.created_at,
                       COUNT(bh.id) as property_count
                FROM users u
                LEFT JOIN boarding_houses bh ON u.id = bh.owner_id
                GROUP BY u.id
                ORDER BY u.created_at DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve details of a specific user.
     */
    public function findById(int $userId): array|bool {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update user account status (e.g. Active, Suspended).
     */
    public function updateStatus(int $userId, string $status): bool {
        $sql = "UPDATE users SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $userId
        ]);
    }
}