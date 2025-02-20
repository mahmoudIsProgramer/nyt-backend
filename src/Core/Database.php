<?php

namespace App\Core;

use SQLite3;
use Exception;

class Database {
    private static ?Database $instance = null;
    private SQLite3 $connection;

    private function __construct() {
        try {
            $dbPath = dirname(__DIR__, 2) . '/database/database.sqlite';
            $this->connection = new SQLite3($dbPath);
            
            // Enable foreign key support
            $this->connection->exec('PRAGMA foreign_keys = ON');
            
        } catch (Exception $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): SQLite3 {
        return $this->connection;
    }

    // Helper method to convert SQLite3Result to array
    public function fetchArray($result): ?array {
        if (!$result) {
            return null;
        }
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row !== false ? $row : null;
    }

    public function prepare(string $sql): \SQLite3Stmt {
        return $this->connection->prepare($sql);
    }

    public function exec(string $sql): bool {
        return $this->connection->exec($sql);
    }

    public function lastInsertRowID(): int {
        return $this->connection->lastInsertRowID();
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
