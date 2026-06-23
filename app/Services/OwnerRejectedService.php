<?php
/**
 * File Location: app/Services/OwnerRejectedService.php
 * File Name: OwnerRejectedService.php
 * Description: Business logic layer managing dynamic 3-day count downs for rejected asset cleanup.
 */

namespace App\Services;

use App\Models\OwnerRejectedHouse;
use Exception;

class OwnerRejectedService {
    private OwnerRejectedHouse $rejectedModel;

    public function __construct() {
        $this->rejectedModel = new OwnerRejectedHouse();
    }

    /**
     * Fetch owner's rejected properties and calculate the exact remaining countdown time.
     */
    public function getRejectedProperties(int $ownerId): array {
        if (empty($ownerId)) {
            throw new Exception("Invalid owner credential reference.");
        }

        $properties = $this->rejectedModel->getRejectedByOwnerId($ownerId);
        $currentTime = time();

        foreach ($properties as &$house) {
            $rejectedTimestamp = strtotime($house['rejected_at']);
            // 3-day deletion window constraint (3 days * 24 hours * 60 minutes * 60 seconds)
            $deletionTimestamp = $rejectedTimestamp + (3 * 24 * 60 * 60);
            $timeLeftSeconds = $deletionTimestamp - $currentTime;

            if ($timeLeftSeconds <= 0) {
                $house['time_remaining_label'] = "Processing Cleanup";
                $house['is_expired'] = true;
                $house['hours_left'] = 0;
            } else {
                $days = floor($timeLeftSeconds / 86400);
                $hours = floor(($timeLeftSeconds % 86400) / 3600);
                $minutes = floor(($timeLeftSeconds % 3600) / 60);

                $house['is_expired'] = false;
                $house['hours_left'] = floor($timeLeftSeconds / 3600);
                
                if ($days > 0) {
                    $house['time_remaining_label'] = "{$days}d {$hours}h remaining";
                } else {
                    $house['time_remaining_label'] = "{$hours}h {$minutes}m remaining";
                }
            }

            // Provide calculated timestamp for formatted preview text
            $house['scheduled_deletion_date'] = date('M d, Y h:i A', $deletionTimestamp);
        }

        return $properties;
    }
}