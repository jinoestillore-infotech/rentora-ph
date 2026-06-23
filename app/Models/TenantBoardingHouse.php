<?php
/**
 * File Location: app/Models/TenantBoardingHouse.php
 * File Name: TenantBoardingHouse.php
 * Description: Dedicated data layer for Tenants browsing approved properties.
 */

namespace App\Models;

use App\Core\Database;
use PDO;

class TenantBoardingHouse {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Fetch all approved properties with calculated pricing ranges and overall vacancies, matching search criteria.
     */
    public function getCatalog(array $filters): array {
        $sql = "SELECT bh.*, 
                       u.firstname as owner_firstname, 
                       u.lastname as owner_lastname,
                       MIN(r.price) as min_price, 
                       MAX(r.price) as max_price,
                       COUNT(r.id) as room_count,
                       COALESCE(SUM(r.available_beds), 0) as total_available_beds
                FROM boarding_houses bh
                JOIN users u ON bh.owner_id = u.id
                LEFT JOIN rooms r ON bh.id = r.boarding_house_id
                WHERE bh.status = 'Approved'";

        $params = [];

        // Apply real-time search term matching
        if (!empty($filters['search'])) {
            $sql .= " AND (bh.name LIKE :search 
                        OR bh.description LIKE :search_desc 
                        OR bh.amenities LIKE :search_amenities 
                        OR bh.address LIKE :search_address)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[':search'] = $searchTerm;
            $params[':search_desc'] = $searchTerm;
            $params[':search_amenities'] = $searchTerm;
            $params[':search_address'] = $searchTerm;
        }

        // Apply Town location selector
        if (!empty($filters['town']) && $filters['town'] !== 'ALL') {
            $sql .= " AND bh.town = :town";
            $params[':town'] = $filters['town'];
        }

        $sql .= " GROUP BY bh.id";

        // Having filters for aggregated fields (Beds, Price range)
        $havingClauses = [];

        // Filter by minimum free beds
        if (isset($filters['min_beds']) && (int)$filters['min_beds'] > 0) {
            $havingClauses[] = "total_available_beds >= :min_beds";
            $params[':min_beds'] = (int)$filters['min_beds'];
        }

        // Filter by maximum room pricing cap
        if (!empty($filters['max_price'])) {
            $havingClauses[] = "min_price <= :max_price";
            $params[':max_price'] = (float)$filters['max_price'];
        }

        if (!empty($havingClauses)) {
            $sql .= " HAVING " . implode(" AND ", $havingClauses);
        }

        $sql .= " ORDER BY bh.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single approved boarding house details along with landlord credentials.
     */
    public function findDetails(int $houseId): array|bool {
        $sql = "SELECT bh.*, 
                       u.firstname as owner_firstname, 
                       u.lastname as owner_lastname, 
                       u.email as owner_email,
                       u.contact as contact_number
                FROM boarding_houses bh
                JOIN users u ON bh.owner_id = u.id
                WHERE bh.id = :id AND bh.status = 'Approved'
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $houseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all active rooms mapped to a specific approved property ID.
     */
    public function getHouseRooms(int $houseId): array {
        $sql = "SELECT * FROM rooms 
                WHERE boarding_house_id = :house_id 
                ORDER BY price ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':house_id' => $houseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch global stats for the tenant dashboard display.
     */
    public function getGlobalStats(): array {
        $sql = "SELECT 
                    COUNT(DISTINCT bh.id) as total_houses,
                    COUNT(DISTINCT bh.town) as total_towns,
                    COALESCE(SUM(r.available_beds), 0) as total_vacant_beds
                FROM boarding_houses bh
                LEFT JOIN rooms r ON bh.id = r.boarding_house_id
                WHERE bh.status = 'Approved'";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_houses' => 0,
            'total_towns' => 0,
            'total_vacant_beds' => 0
        ];
    }

    /**
     * Retrieve the most recently added approved boarding houses with price ranges.
     */
    public function getRecentProperties(int $limit = 3): array {
        $sql = "SELECT bh.*, 
                       MIN(r.price) as min_price, 
                       MAX(r.price) as max_price,
                       COUNT(r.id) as room_count,
                       COALESCE(SUM(r.available_beds), 0) as total_available_beds
                FROM boarding_houses bh
                LEFT JOIN rooms r ON bh.id = r.boarding_house_id
                WHERE bh.status = 'Approved'
                GROUP BY bh.id
                ORDER BY bh.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}