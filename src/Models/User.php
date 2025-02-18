<?php

namespace App\Models;

use App\Core\Database;
use App\Utils\Helper;
use SQLite3;

class User {
    private SQLite3 $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(array $data): ?int {
        try {
            $sql = "INSERT INTO users (name, email, password, created_at) VALUES (:name, :email, :password, :created_at)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':name', $data['name'], SQLITE3_TEXT);
            $stmt->bindValue(':email', $data['email'], SQLITE3_TEXT);
            $stmt->bindValue(':password', $data['password'], SQLITE3_TEXT);
            $stmt->bindValue(':created_at', date('Y-m-d H:i:s'), SQLITE3_TEXT);
            
            $result = $stmt->execute();
            
            if ($result === false) {
                throw new \Exception("Failed to create user: " . $this->db->lastErrorMsg());
            }
            
            return $this->db->lastInsertRowID();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            
            $result = $stmt->execute();
            return Database::getInstance()->fetchArray($result);
        } catch (\Exception $e) {
            error_log("Error finding user by email: " . $e->getMessage());
            throw $e;
        }
    }

    public function findById(int $id): ?array {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            return Database::getInstance()->fetchArray($result);
        } catch (\Exception $e) {
            error_log("Error finding user by id: " . $e->getMessage());
            throw $e;
        }
    }
}
