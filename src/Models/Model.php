<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Interfaces\DatabaseDriverInterface;
use PDO;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected DatabaseDriverInterface $db;

    public function __construct(DatabaseDriverInterface $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getDriver();
    }

    /**
     * Find a record by any field
     */
    public function findBy(string $field, mixed $value): ?array
    {
        return $this->executeQuery(
            "SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1",
            [':value' => $value],
            PDO::PARAM_STR
        );
    }

    /**
     * Find a record by primary key
     */
    public function findById(int $id): ?array
    {
        return $this->executeQuery(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1",
            [':id' => $id],
            PDO::PARAM_INT
        );
    }

    /**
     * Check if a record exists
     */
    public function exists(string $field, mixed $value): bool
    {
        return $this->findBy($field, $value) !== null;
    }

    /**
     * Fetch all records from the table
     */
    public function fetchAll(): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
            $result = $this->db->execute($stmt);
            
            return $this->db->fetchAll($result);
        } catch (\Exception $e) {
            $this->logError('fetching all records', $e);
            return [];
        }
    }

    /**
     * Get the table name
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Execute a parameterized query
     */
    protected function executeQuery(string $sql, array $params, int $paramType): ?array
    {
        try {
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $this->db->bindValue($stmt, $key, $value, $paramType);
            }
            
            $result = $this->db->execute($stmt);
            return $this->db->fetchArray($result);
        } catch (\Exception $e) {
            $this->logError('executing query', $e);
            return null;
        }
    }

    /**
     * Log database errors
     */
    protected function logError(string $action, \Exception $e): void
    {
        error_log(sprintf(
            "Error %s in %s: %s",
            $action,
            $this->table,
            $e->getMessage()
        ));
    }
}
