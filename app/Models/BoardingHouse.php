<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class BoardingHouse {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Fetch all boarding houses owned by a specific owner ID.
     */
    public function getByOwnerId(int $ownerId): array {
        $sql = "SELECT bh.*, 
                (SELECT COUNT(*) FROM rooms r WHERE r.boarding_house_id = bh.id) as total_rooms,
                (SELECT SUM(r.available_beds) FROM rooms r WHERE r.boarding_house_id = bh.id) as total_available_beds
                FROM boarding_houses bh 
                WHERE bh.owner_id = :owner_id 
                ORDER BY bh.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':owner_id' => $ownerId]);
        return $stmt->fetchAll();
    }

    /**
     * Get aggregate stats for an owner's dashboard dashboard overview.
     */
    public function getStatsByOwnerId(int $ownerId): array {
        $sql = "SELECT 
                COUNT(*) as total_properties,
                SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved_properties,
                SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_properties,
                (SELECT COUNT(*) FROM rooms r 
                 JOIN boarding_houses b ON r.boarding_house_id = b.id 
                 WHERE b.owner_id = :owner_id_sub) as total_rooms
                FROM boarding_houses 
                WHERE owner_id = :owner_id_main";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':owner_id_sub'  => $ownerId,
            ':owner_id_main' => $ownerId
        ]);
        return $stmt->fetch() ?: [
            'total_properties' => 0,
            'approved_properties' => 0,
            'pending_properties' => 0,
            'total_rooms' => 0
        ];
    }

    /**
     * Inserts a new boarding house listing into the database.
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO boarding_houses (owner_id, name, description, address, town, contact_number, amenities, house_rules, status) 
                VALUES (:owner_id, :name, :description, :address, :town, :contact_number, :amenities, :house_rules, 'Pending')";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':owner_id'       => $data['owner_id'],
            ':name'           => $data['name'],
            ':description'    => $data['description'] ?? null,
            ':address'        => $data['address'],
            ':town'           => $data['town'],
            ':contact_number' => $data['contact_number'],
            ':amenities'      => $data['amenities'] ?? null,
            ':house_rules'    => $data['house_rules'] ?? null
        ]);
    }
}