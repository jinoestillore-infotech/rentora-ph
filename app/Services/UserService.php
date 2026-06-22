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
     * Authenticates a user based on email and password.
     * @param string $email
     * @param string $password
     * @return array
     * @throws Exception For invalid inputs, missing accounts, suspended status, or bad passwords
     */
    public function authenticateUser(string $email, string $password): array {
        if (empty($email) || empty($password)) {
            throw new Exception("Please fill in both Email and Password fields.");
        }

        // Fetch user from DB
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            throw new Exception("Invalid login credentials.");
        }

        // Check if account status allows access
        if ($user['status'] !== 'Active') {
            throw new Exception("Your account has been {$user['status']}. Please contact system support.");
        }

        // Verify hash security
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid login credentials.");
        }

        return $user;
    }

    /**
     * Business logic and validation process for registering a new user.
     * @param array $rawInputs Raw data from POST
     * @return int The newly created User ID
     * @throws Exception For invalid parameters, password mismatch, or duplicate user
     */
    public function registerUser(array $rawInputs): int {
        $required = ['firstname', 'lastname', 'email', 'password', 'confirm_password', 'role'];
        foreach ($required as $field) {
            if (empty($rawInputs[$field])) {
                throw new Exception("All fields are required.");
            }
        }

        $email = filter_var($rawInputs['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new Exception("Please enter a valid email address.");
        }

        $allowedRoles = ['Admin', 'Owner', 'Tenant'];
        $role = $rawInputs['role'];
        if (!in_array($role, $allowedRoles)) {
            throw new Exception("Invalid registration role selected.");
        }

        if ($role === 'Admin') {
            throw new Exception("Administrators cannot register online.");
        }

        if ($rawInputs['password'] !== $rawInputs['confirm_password']) {
            throw new Exception("Passwords do not match.");
        }

        if (strlen($rawInputs['password']) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }

        if ($this->userModel->emailExists($email)) {
            throw new Exception("This email address is already registered.");
        }

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