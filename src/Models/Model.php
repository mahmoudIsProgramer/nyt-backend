<?php

namespace App\Models;

use App\Core\Database;
use SQLite3;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected SQLite3 $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findBy(string $field, mixed $value): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':value', $value, SQLITE3_TEXT);
            
            $result = $stmt->execute();
            return Database::getInstance()->fetchArray($result);
        } catch (\Exception $e) {
            error_log("Error finding record by $field in {$this->table}: " . $e->getMessage());
            return null;
        }
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            return Database::getInstance()->fetchArray($result);
        } catch (\Exception $e) {
            error_log("Error finding record by id in {$this->table}: " . $e->getMessage());
            return null;
        }
    }

    public function exists(string $field, mixed $value): bool
    {
        return $this->findBy($field, $value) !== null;
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
