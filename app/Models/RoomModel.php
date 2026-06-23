<?php
/**
 * File Location: app/Models/RoomModel.php
 * File Name: RoomModel.php
 * Description: Model representing the rooms database table for RENTORA PH.
 */

namespace App\Models;

use App\Core\Database;
use PDO;

class RoomModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Fetch a boarding house record but only if it belongs to the given owner.
     * Prevents cross-owner visual manipulation.
     */
    public function findHouseByOwner(int $houseId, int $ownerId): array|bool {
        $sql = "SELECT * FROM boarding_houses 
                WHERE id = :house_id AND owner_id = :owner_id AND status = 'Approved'
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':house_id' => $houseId,
            ':owner_id' => $ownerId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all rooms for an approved boarding house.
     */
    public function getRoomsByHouseId(int $houseId): array {
        $sql = "SELECT * FROM rooms 
                WHERE boarding_house_id = :house_id 
                ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':house_id' => $houseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find details of a specific room.
     */
    public function findRoomById(int $roomId): array|bool {
        $sql = "SELECT * FROM rooms WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $roomId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a room under an approved boarding house.
     */
    public function create(array $data): bool {
        $sql = "INSERT INTO rooms (boarding_house_id, room_name, price, capacity, available_beds, amenities, status, image_path) 
                VALUES (:house_id, :room_name, :price, :capacity, :available_beds, :amenities, :status, :image_path)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':house_id'       => $data['boarding_house_id'],
            ':room_name'     => $data['room_name'],
            ':price'          => $data['price'],
            ':capacity'       => $data['capacity'],
            ':available_beds' => $data['available_beds'],
            ':amenities'      => $data['amenities'] ?? null,
            ':status'         => $data['status'] ?? 'Available',
            ':image_path'     => $data['image_path'] ?? null
        ]);
    }

    /**
     * Update room details in the database.
     */
    public function update(int $roomId, array $data): bool {
        $sql = "UPDATE rooms SET 
                room_name = :room_name, 
                price = :price, 
                capacity = :capacity, 
                available_beds = :available_beds, 
                amenities = :amenities, 
                status = :status, 
                image_path = :image_path,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':room_name'     => $data['room_name'],
            ':price'          => $data['price'],
            ':capacity'       => $data['capacity'],
            ':available_beds' => $data['available_beds'],
            ':amenities'      => $data['amenities'] ?? null,
            ':status'         => $data['status'],
            ':image_path'     => $data['image_path'] ?? null,
            ':id'             => $roomId
        ]);
    }

    /**
     * Delete room profile from database.
     */
    public function delete(int $roomId): bool {
        $sql = "DELETE FROM rooms WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $roomId]);
    }
}