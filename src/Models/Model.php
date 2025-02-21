<?php

namespace App\Models;

use App\Core\Database;
use PDO;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getDriver();
    }

    public function findBy(string $field, mixed $value): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $this->db->bindValue($stmt, ':value', $value, PDO::PARAM_STR);
            
            $result = $this->db->execute($stmt);
            return $this->db->fetchArray($result);
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
            $this->db->bindValue($stmt, ':id', $id, PDO::PARAM_INT);
            
            $result = $this->db->execute($stmt);
            return $this->db->fetchArray($result); // Use driver's fetchArray
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

    public function fetchAll(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        
        try {
            $stmt = $this->db->prepare($sql);
            $result = $this->db->execute($stmt);
            
            return $this->db->fetchAll($result); // Use driver's fetchAll
        } catch (\Exception $e) {
            error_log("Error fetching all records from {$this->table}: " . $e->getMessage());
            return [];
        }
    }
}
