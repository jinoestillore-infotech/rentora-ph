<?php

namespace App\Controllers;

use App\Core\Security;
use App\Models\BoardingHouse;
use Exception;

class OwnerController {
    private BoardingHouse $houseModel;

    public function __construct() {
        $this->houseModel = new BoardingHouse();
    }

    /**
     * Enforce strict Role-Based Access Control (RBAC) safeguards.
     */
    private function checkOwnerAccess(): void {
        Security::startSecureSession();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Owner') {
            $_SESSION['error'] = "Unauthorized access. Please log in with an Owner account.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Renders the primary Owner Dashboard.
     * Maps to GET /owner/dashboard
     */
    public function dashboard(): void {
        $this->checkOwnerAccess();

        $ownerId = (int)$_SESSION['user_id'];
        
        // Fetch properties and analytics
        $stats = $this->houseModel->getStatsByOwnerId($ownerId);
        $properties = $this->houseModel->getByOwnerId($ownerId);

        // Flash message handling
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        require_once dirname(__DIR__, 2) . '/views/owner/dashboard.php';
    }

    /**
     * Renders the dedicated Boarding House Registration Page.
     * Maps to GET /owner/add-house
     */
    public function showAddHouseForm(): void {
        $this->checkOwnerAccess();
        require_once dirname(__DIR__, 2) . '/views/owner/add_house.php';
    }

    /**
     * Register/Create a new Boarding House.
     * Maps to POST /owner/add-house
     */
    public function addHouse(): void {
        $this->checkOwnerAccess();

        // Validate Anti-Forgery Token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Invalid CSRF verification token. Please try again.";
            header("Location: " . BASE_URL . "/owner/add-house");
            exit();
        }

        // Sanitize overall inputs
        $cleanData = Security::sanitize($_POST);

        $name = $cleanData['name'] ?? '';
        $town = $cleanData['town'] ?? '';
        $address = $cleanData['address'] ?? '';
        $contact = $cleanData['contact_number'] ?? '';

        // Validation Rules
        if (empty($name) || empty($town) || empty($address) || empty($contact)) {
            $_SESSION['error'] = "Property Name, Town, Complete Address, and Contact Number are required.";
            $_SESSION['old_input'] = $cleanData;
            header("Location: " . BASE_URL . "/owner/add-house");
            exit();
        }

        try {
            $insertData = [
                'owner_id'       => (int)$_SESSION['user_id'],
                'name'           => $name,
                'description'    => $cleanData['description'] ?? '',
                'address'        => $address,
                'town'           => $town,
                'contact_number' => $contact,
                'amenities'      => $cleanData['amenities'] ?? '',
                'house_rules'    => $cleanData['house_rules'] ?? ''
            ];

            if ($this->houseModel->create($insertData)) {
                $_SESSION['success'] = "Boarding House listing created successfully! Please wait for admin approval before adding rooms.";
                header("Location: " . BASE_URL . "/owner/dashboard");
                exit();
            } else {
                throw new Exception("Unable to save property record. Please check database connectivity.");
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['old_input'] = $cleanData;
            header("Location: " . BASE_URL . "/owner/add-house");
            exit();
        }
    }
}