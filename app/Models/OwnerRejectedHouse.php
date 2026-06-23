<?php
/**
 * File Location: app/Models/OwnerRejectedHouse.php
 * File Name: OwnerRejectedHouse.php
 * Description: Data layer specifically querying rejected properties and explanation logs for Owners.
 */

namespace App\Models;

use App\Core\Database;
use PDO;

class OwnerRejectedHouse {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Retrieve all rejected boarding houses for a specific owner, mapping reasons.
     */
    public function getRejectedByOwnerId(int $ownerId): array {
        $sql = "SELECT bh.*, 
                bhr.reason as rejection_reason,
                COALESCE(bhr.updated_at, bh.updated_at) as rejected_at
                FROM boarding_houses bh
                LEFT JOIN boarding_house_rejections bhr ON bh.id = bhr.boarding_house_id
                WHERE bh.owner_id = :owner_id AND bh.status = 'Rejected'
                ORDER BY bh.updated_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':owner_id' => $ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}