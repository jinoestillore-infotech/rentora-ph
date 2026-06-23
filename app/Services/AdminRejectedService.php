<?php
/**
 * File Location: app/Services/AdminRejectedService.php
 * File Name: AdminRejectedService.php
 * Description: Business logic validating inputs and unlinking documents for rejected listings.
 */

namespace App\Services;

use App\Models\AdminRejectedHouse;
use Exception;

class AdminRejectedService {
    private AdminRejectedHouse $rejectedModel;

    public function __construct() {
        $this->rejectedModel = new AdminRejectedHouse();
    }

    /**
     * Validate and store operational rejection reason parameters.
     */
    public function updateRejectionReason(int $houseId, string $reason): bool {
        if (empty($houseId)) {
            throw new Exception("The boarding house identification code is missing.");
        }

        $trimmedReason = trim($reason);
        if (empty($trimmedReason)) {
            throw new Exception("Please provide a valid explanation or reason for the rejection.");
        }

        if (strlen($trimmedReason) > 1000) {
            throw new Exception("The rejection reason exceeds the maximum 1,000 characters limit.");
        }

        $house = $this->rejectedModel->findDetailsById($houseId);
        if (!$house) {
            throw new Exception("The requested rejected property record was not found.");
        }

        $result = $this->rejectedModel->saveRejectionReason($houseId, $trimmedReason);
        if (!$result) {
            throw new Exception("Internal storage process failed to update the record.");
        }

        return true;
    }

    /**
     * Safely executes complete system deletion of a rejected property along with file unlinks.
     */
    public function deleteProperty(int $houseId): bool {
        if (empty($houseId)) {
            throw new Exception("The boarding house identification code is missing.");
        }

        $house = $this->rejectedModel->findDetailsById($houseId);
        if (!$house) {
            throw new Exception("The requested rejected property record was not found.");
        }

        $basePublicPath = dirname(dirname(__DIR__)) . '/public/';
        
        // Unlink physical facade thumbnail if it exists
        if (!empty($house['image_path'])) {
            $imageFile = $basePublicPath . $house['image_path'];
            if (file_exists($imageFile) && is_file($imageFile)) {
                unlink($imageFile);
            }
        }

        // Unlink legal proof document if it exists
        if (!empty($house['legality_proof'])) {
            $legalFile = $basePublicPath . $house['legality_proof'];
            if (file_exists($legalFile) && is_file($legalFile)) {
                unlink($legalFile);
            }
        }

        $result = $this->rejectedModel->delete($houseId);
        if (!$result) {
            throw new Exception("Database failed to clear the listing record.");
        }

        return true;
    }
}