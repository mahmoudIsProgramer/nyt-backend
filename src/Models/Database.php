<?php

namespace App\Models;

use App\Config\Config;
use SQLite3;
use SQLite3Result;

class Database {
    private static ?Database $instance = null;
    private SQLite3 $db;

    private function __construct() {
        $config = Config::getInstance();
        $this->db = new SQLite3($config->get('db_path'));
        $this->db->exec('PRAGMA foreign_keys = ON;');
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query(string $sql, array $params = []): SQLite3Result {
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        return $stmt->execute();
    }

    public function lastInsertRowID(): int {
        return $this->db->lastInsertRowID();
    }

    public function fetchAll(SQLite3Result $result): array {
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function fetchOne(SQLite3Result $result): ?array {
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ?: null;
    }
}
