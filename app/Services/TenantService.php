<?php
/**
 * File Location: app/Services/TenantService.php
 * File Name: TenantService.php
 * Description: Business service layer handling sanitization and dashboard data mapping for Tenants.
 */

namespace App\Services;

use App\Models\TenantBoardingHouse;
use Exception;

class TenantService {
    private TenantBoardingHouse $tenantModel;

    public function __construct() {
        $this->tenantModel = new TenantBoardingHouse();
    }

    /**
     * Process list filter search inputs and request catalog dataset.
     */
    public function searchProperties(array $inputs): array {
        $filters = [
            'search'    => isset($inputs['search']) ? trim($inputs['search']) : '',
            'town'      => isset($inputs['town']) ? trim($inputs['town']) : 'ALL',
            'min_beds'  => isset($inputs['min_beds']) ? (int)$inputs['min_beds'] : 0,
            'max_price' => !empty($inputs['max_price']) ? (float)$inputs['max_price'] : null
        ];

        return $this->tenantModel->getCatalog($filters);
    }

    /**
     * Fetch complete contextual dashboard data including analytics metrics and recently registered listings.
     */
    public function getDashboardData(): array {
        return [
            'stats'  => $this->tenantModel->getGlobalStats(),
            'recent' => $this->tenantModel->getRecentProperties(3)
        ];
    }

    /**
     * Retrieve complete contextual information for a target property.
     */
    public function getPropertyProfile(int $houseId): array {
        if (empty($houseId)) {
            throw new Exception("Invalid boarding house ID specified.");
        }

        $house = $this->tenantModel->findDetails($houseId);
        if (!$house) {
            throw new Exception("Boarding house not found, pending activation, or restricted.");
        }

        $rooms = $this->tenantModel->getHouseRooms($houseId);

        return [
            'house' => $house,
            'rooms' => $rooms
        ];
    }
}