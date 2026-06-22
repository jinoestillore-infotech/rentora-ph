<?php

namespace App\Controllers;

use App\Core\Security;
use App\Core\Database;
use App\Services\UserService;
use Exception;

class AuthController {
    private UserService $userService;
    private \PDO $db;

    public function __construct() {
        $this->userService = new UserService();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Display the login page.
     * Maps to GET /login
     */
    public function showLoginForm(): void {
        Security::startSecureSession();
        require_once dirname(__DIR__, 2) . '/views/auth/login.php';
    }

    /**
     * Process user login request.
     * Maps to POST /login
     */
    public function handleLogin(): void {
        Security::startSecureSession();

        // 1. Verify CSRF Anti-Forgery Token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Security::validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = "Invalid CSRF verification token. Please try again.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        // 2. Sanitize overall input entries
        $cleanData = Security::sanitize($_POST);
        $email = $cleanData['email'] ?? '';
        $password = $_POST['password'] ?? ''; // Avoid sanitizing password string to maintain special characters

        // 3. Enforce Rate Limiting (Login Lockout)
        if (Security::isRateLimited($this->db, $email, 5, 15)) {
            $_SESSION['error'] = "Too many failed attempts. You have been locked out for 15 minutes.";
            $_SESSION['old_input'] = ['email' => $email];
            header("Location: " . BASE_URL . "/login");
            exit();
        }

        try {
            // 4. Authenticate user credentials in Service Layer
            $user = $this->userService->authenticateUser($email, $password);

            // 5. Clear prior rate limits logs upon successful authentication
            Security::clearFailedAttempts($this->db, $email);

            // 6. Regenerate Session ID to prevent fixation attacks
            session_regenerate_id(true);

            // 7. Store user authentication variables inside secure Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // 8. Redirect user based on Role-Based Access Control (RBAC) constraints
            switch ($user['role']) {
                case 'Admin':
                    header("Location: " . BASE_URL . "/admin/dashboard");
                    break;
                case 'Owner':
                    header("Location: " . BASE_URL . "/owner/dashboard");
                    break;
                case 'Tenant':
                default:
                    header("Location: " . BASE_URL . "/tenant/dashboard");
                    break;
            }
            exit();

        } catch (Exception $e) {
            // Log the failure to Rate Limiting table
            Security::logFailedAttempt($this->db, $email);

            // Send feedback error message to login view
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['old_input'] = ['email' => $email];
            
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    /**
     * Log user out of system.
     * Maps to GET /logout
     */
    public function handleLogout(): void {
        Security::destroySession();
        header("Location: " . BASE_URL . "/login");
        exit();
    }
}