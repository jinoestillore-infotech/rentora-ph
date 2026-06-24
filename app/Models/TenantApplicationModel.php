<?php
/**
 * File Location: app/Models/TenantApplicationModel.php
 * File Name: TenantApplicationModel.php
 * Description: Dedicated database model layer handling room selectors and tenant applications registration.
 */

namespace App\Models;

use App\Core\Database;
use PDO;

class TenantApplicationModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Fetch standard boarding house context coordinates.
     */
    public function getHouseDetails(int $houseId): array|bool {
        $sql = "SELECT id, name, town, address 
                FROM boarding_houses 
                WHERE id = :id AND status = 'Approved' 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $houseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all available rooms under a boarding house that are vacant.
     */
    public function getAvailableRooms(int $houseId): array {
        $sql = "SELECT id, room_name, price, capacity, available_beds 
                FROM rooms 
                WHERE boarding_house_id = :house_id 
                  AND status = 'Available' 
                  AND available_beds > 0 
                ORDER BY price ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':house_id' => $houseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve all applications submitted by a specific tenant ID with details.
     */
    public function getApplicationsByTenantId(int $tenantId): array {
        $sql = "SELECT ta.*, 
                       bh.name as house_name, 
                       bh.town as house_town, 
                       bh.address as house_address,
                       r.room_name, 
                       r.price as room_price
                FROM tenant_applications ta
                JOIN boarding_houses bh ON ta.boarding_house_id = bh.id
                JOIN rooms r ON ta.room_id = r.id
                WHERE ta.tenant_id = :tenant_id
                ORDER BY ta.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tenant_id' => $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Submit application registration fields to the database.
     */
    public function createApplication(array $data): bool {
        $sql = "INSERT INTO tenant_applications (
                    tenant_id, boarding_house_id, room_id, 
                    firstname, lastname, middlename, permanent_address, 
                    age, contact_number, email, verification_id_path, 
                    emergency_fullname, emergency_contact_number, emergency_verification_id_path, 
                    status
                ) VALUES (
                    :tenant_id, :boarding_house_id, :room_id, 
                    :firstname, :lastname, :middlename, :permanent_address, 
                    :age, :contact_number, :email, :verification_id_path, 
                    :emergency_fullname, :emergency_contact_number, :emergency_verification_id_path, 
                    'Pending'
                )";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tenant_id'                       => (int)$data['tenant_id'],
            ':boarding_house_id'               => (int)$data['boarding_house_id'],
            ':room_id'                         => (int)$data['room_id'],
            ':firstname'                       => $data['firstname'],
            ':lastname'                        => $data['lastname'],
            ':middlename'                      => $data['middlename'] ?? null,
            ':permanent_address'               => $data['permanent_address'],
            ':age'                             => (int)$data['age'],
            ':contact_number'                  => $data['contact_number'],
            ':email'                           => $data['email'],
            ':verification_id_path'            => $data['verification_id_path'],
            ':emergency_fullname'              => $data['emergency_fullname'],
            ':emergency_contact_number'        => $data['emergency_contact_number'],
            ':emergency_verification_id_path'  => $data['emergency_verification_id_path']
        ]);
    }
}