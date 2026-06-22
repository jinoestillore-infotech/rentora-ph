<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Checks if a user already exists with the given email.
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Finds a user record by email.
     * @param string $email
     * @return array|bool
     */
    public function findByEmail(string $email): array|bool {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Inserts a new user record into the database.
     * @param array $data User details: firstname, lastname, email, password (hashed), contact, role
     * @return int|bool Returns the last inserted ID on success, or false on failure
     */
    public function create(array $data): int|bool {
        $sql = "INSERT INTO users (firstname, lastname, email, password, contact, role, status) 
                VALUES (:firstname, :lastname, :email, :password, :contact, :role, 'Active')";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':firstname' => $data['firstname'],
            ':lastname'  => $data['lastname'],
            ':email'     => $data['email'],
            ':password'  => $data['password'],
            ':contact'   => $data['contact'] ?? null,
            ':role'      => $data['role']
        ]);

        return $result ? (int)$this->db->lastInsertId() : false;
    }
}