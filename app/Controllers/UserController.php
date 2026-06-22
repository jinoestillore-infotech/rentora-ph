<?php

namespace App\Controllers;

use App\Core\Security;
use App\Services\UserService;
use Exception;

class UserController {
    private UserService $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    /**
     * Display the registration page view.
     * Maps to GET /register
     */
    public function showRegisterForm(): void {
        Security::startSecureSession();
        // The view would render here. HTML structure will draw its CSRF using Security::csrfField()
        require_once dirname(__DIR__, 2) . '/views/register.php';
    }

    /**
     * Processes registration submissions.
     * Maps to POST /register
     */
    public function handleRegister(): void {
        Security::startSecureSession();

        // 1. Enforce strict CSRF protection
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Invalid CSRF verification token. Please try again.";
            header("Location: /register");
            exit();
        }

        // 2. Sanitize overall input array to eliminate XSS scripting payloads
        $cleanData = Security::sanitize($_POST);

        try {
            // 3. Handoff processing to dedicated Service Layer
            $newUserId = $this->userService->registerUser($cleanData);

            // 4. Set Success Feedback and Redirect to Login Screen
            $_SESSION['success'] = "Registration successful! You can now log in.";
            header("Location: /login");
            exit();

        } catch (Exception $e) {
            // Log exceptions and pass readable error feedback
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['old_input'] = [
                'firstname' => $cleanData['firstname'] ?? '',
                'lastname'  => $cleanData['lastname'] ?? '',
                'email'     => $cleanData['email'] ?? '',
                'contact'   => $cleanData['contact'] ?? '',
                'role'      => $cleanData['role'] ?? ''
            ];

            header("Location: /register");
            exit();
        }
    }
}