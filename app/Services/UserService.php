<?php

namespace App\Services;

use App\Models\User;
use Exception;

class UserService {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Business logic and validation process for registering a new user.
     * * @param array $rawInputs Raw data from POST
     * @return int The newly created User ID
     * @throws Exception For invalid parameters, password mismatch, or duplicate user
     */
    public function registerUser(array $rawInputs): int {
        // Essential field checks
        $required = ['firstname', 'lastname', 'email', 'password', 'confirm_password', 'role'];
        foreach ($required as $field) {
            if (empty($rawInputs[$field])) {
                throw new Exception("All fields are required.");
            }
        }

        // Validate emails
        $email = filter_var($rawInputs['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new Exception("Please enter a valid email address.");
        }

        // Validate Role Constraints to enforce RBAC safety
        $allowedRoles = ['Admin', 'Owner', 'Tenant'];
        $role = $rawInputs['role'];
        if (!in_array($role, $allowedRoles)) {
            throw new Exception("Invalid registration role selected.");
        }

        // Block unauthorized direct registration of Admins
        if ($role === 'Admin') {
            throw new Exception("Administrators cannot register online.");
        }

        // Validate password equality and strength
        if ($rawInputs['password'] !== $rawInputs['confirm_password']) {
            throw new Exception("Passwords do not match.");
        }

        if (strlen($rawInputs['password']) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }

        // Check for duplicate accounts
        if ($this->userModel->emailExists($email)) {
            throw new Exception("This email address is already registered.");
        }

        // Prepare sanitized inputs and secure password hash
        $hashedPassword = password_hash($rawInputs['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $userData = [
            'firstname' => $rawInputs['firstname'],
            'lastname'  => $rawInputs['lastname'],
            'email'     => $email,
            'password'  => $hashedPassword,
            'contact'   => $rawInputs['contact'] ?? null,
            'role'      => $role
        ];

        $userId = $this->userModel->create($userData);
        if (!$userId) {
            throw new Exception("An internal error occurred. Please try again.");
        }

        return $userId;
    }
}