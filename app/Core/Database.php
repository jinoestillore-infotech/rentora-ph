<?php

namespace App\Core;

use PDO;
use PDOException;
use Exception;

class Database {
    /**
     * Singleton instance of the Database class.
     * @var Database|null
     */
    private static ?Database $instance = null;

    /**
     * The PDO database connection instance.
     * @var PDO|null
     */
    private ?PDO $connection = null;

    /**
     * Private constructor to prevent direct instantiation (Singleton pattern).
     * Loads the environment variables and establishes the PDO connection.
     */
    private function __construct() {
        $this->loadEnv();

        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $dbName = getenv('DB_NAME') ?: 'bh_db';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset={$charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            // Log error or display structured message
            throw new Exception("Database Connection Failure: " . $e->getMessage());
        }
    }

    /**
     * Parses the .env file and populates environment variables.
     * Safe fallback helper for environments without native env loaders.
     */
    private function loadEnv(): void {
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                // Split key and value by first "="
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);

                    // Remove surrounding quotes if present
                    $value = trim($value, "\"'");

                    putenv("{$key}={$value}");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }

    /**
     * Gets the single Instance of the Database class.
     * @return Database
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Exposes the active PDO connection.
     * @return PDO
     */
    public function getConnection(): PDO {
        return $this->connection;
    }

    /**
     * Prevent cloning of the instance.
     */
    private function __clone() {}

    /**
     * Prevent unserialization of the instance.
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize a singleton.");
    }
}