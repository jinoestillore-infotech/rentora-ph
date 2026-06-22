<?php

namespace App\Core;

/**
 * Class Security
 * * Provides unified, comprehensive security services for RENTORA PH:
 * 1. Secure Session Management (Hijacking & Fixation prevention)
 * 2. Cross-Site Request Forgery (CSRF) protection
 * 3. Cross-Site Scripting (XSS) input sanitization
 * 4. Database-backed Rate Limiting (Brute-force login protection)
 */
class Security {
    
    /**
     * Initializes a highly secure PHP session.
     * Prevents Session Fixation and Session Hijacking.
     */
    public static function startSecureSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            // Force cookies only, prevent passing SID via URL
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_trans_sid', '0');

            // Apply strict cookie attributes (HttpOnly, Secure, SameSite)
            session_set_cookie_params([
                'lifetime' => 0, // Session-only cookie
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'] ?? '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true, // Prevents Javascript access to session ID
                'samesite' => 'Strict' // Mitigates CSRF
            ]);

            session_start();
        }

        // Mitigate Session Hijacking: Bind session to IP address and User Agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $userIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $fingerprint = md5($userAgent . $userIp);

        if (!isset($_SESSION['fingerprint'])) {
            $_SESSION['fingerprint'] = $fingerprint;
            $_SESSION['last_activity'] = time();
        } else {
            // If fingerprint mismatches, destroy session immediately
            if ($_SESSION['fingerprint'] !== $fingerprint) {
                self::destroySession();
                header("Location: /login?error=session_invalid");
                exit();
            }
        }

        // Regenerate session ID periodically (every 30 minutes) to prevent fixation
        if (time() - ($_SESSION['last_activity'] ?? 0) > 1800) {
            session_regenerate_id(true);
            $_SESSION['last_activity'] = time();
        }
    }

    /**
     * Completely destroys the current session securely.
     */
    public static function destroySession(): void {
        if (session_status() !== PHP_SESSION_NONE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            session_destroy();
        }
    }

    /**
     * Generates a secure CSRF token and registers it in the session.
     * * @return string
     */
    public static function generateCsrfToken(): string {
        self::startSecureSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validates a submitted CSRF token.
     * * @param string|null $token The token sent via POST, headers, or parameters.
     * @return bool
     */
    public static function validateCsrfToken(?string $token): bool {
        self::startSecureSession();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        // Timing attack safe comparison
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Renders a hidden HTML input field containing the active CSRF token.
     * * @return string
     */
    public static function csrfField(): string {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Recursively sanitizes user input to prevent XSS (Cross-Site Scripting).
     *
     * @param mixed $data Raw input array or string
     * @return mixed Cleaned input
     */
    public static function sanitize(mixed $data): mixed {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::sanitize($value);
            }
            return $data;
        }

        if (is_string($data)) {
            // Strip tags and encode special entities
            return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
        }

        return $data;
    }

    /**
     * Checks if an IP or Email is currently rate-limited on login attempts.
     * * @param \PDO $db Database connection instance
     * @param string $email The email attempting login
     * @param int $maxAttempts Max failed attempts allowed before lockout
     * @param int $lockoutMinutes Time window in minutes to look back
     * @return bool True if rate-limited (locked), false otherwise
     */
    public static function isRateLimited(\PDO $db, string $email, int $maxAttempts = 5, int $lockoutMinutes = 15): bool {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM login_attempts 
            WHERE (ip_address = :ip OR email = :email) 
              AND attempted_at > NOW() - INTERVAL :minutes MINUTE
        ");
        
        // PDO binding parameters with types
        $stmt->bindValue(':ip', $ip, \PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->bindValue(':minutes', $lockoutMinutes, \PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn() >= $maxAttempts;
    }

    /**
     * Registers a failed login attempt in the database.
     * * @param \PDO $db Database connection instance
     * @param string $email The email that failed to log in
     * @return bool
     */
    public static function logFailedAttempt(\PDO $db, string $email): bool {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $stmt = $db->prepare("
            INSERT INTO login_attempts (ip_address, email) 
            VALUES (:ip, :email)
        ");
        
        return $stmt->execute([
            ':ip' => $ip,
            ':email' => $email
        ]);
    }

    /**
     * Clears failed login attempts for an IP/email after successful authentication.
     * * @param \PDO $db Database connection instance
     * @param string $email The email to clear
     * @return bool
     */
    public static function clearFailedAttempts(\PDO $db, string $email): bool {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        $stmt = $db->prepare("
            DELETE FROM login_attempts 
            WHERE ip_address = :ip OR email = :email
        ");
        
        return $stmt->execute([
            ':ip' => $ip,
            ':email' => $email
        ]);
    }
}