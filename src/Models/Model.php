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
    protected array $attributes = [];
    protected DatabaseDriverInterface $db;

    public function __construct(array $attributes = [], DatabaseDriverInterface $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getDriver();
        $this->fill($attributes);
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }
        return $this;
    }

    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set(string $key, $value): void
    {
        if (in_array($key, $this->fillable)) {
            $this->attributes[$key] = $value;
        }
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Static create method that all models will inherit
     */
    public static function create(array $attributes): ?static
    {
        $instance = new static();
        $instance->fill($attributes);
        return $instance->save() ? $instance : null;
    }

    /**
     * Instance method for saving the model
     */
    protected function save(): bool
    {
        if (empty($this->attributes)) {
            return false;
        }

        try {
            $fields = array_keys($this->attributes);
            $placeholders = array_map(fn($field) => ":$field", $fields);
            
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . 
                   ") VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($this->attributes as $field => $value) {
                $this->db->bindValue(
                    $stmt,
                    ":$field",
                    $value,
                    $this->getFieldType($field)
                );
            }
            
            $result = $this->db->execute($stmt);
            if ($result) {
                $this->attributes[$this->primaryKey] = $this->db->lastInsertRowID();
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public static function find(int|string $id): ?static
    {
        return static::query()->where(static::make()->primaryKey, $id)->first();
    }

    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::class);
    }

    protected static function make(): static
    {
        return new static();
    }

    protected function getFieldType(string $field): int
    {
        return match($field) {
            'user_id', 'id' => PDO::PARAM_INT,
            default => PDO::PARAM_STR
        };
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

    /**
     * Static create helper
     */
    public static function createNew(array $attributes): ?static
    {
        $model = new static();
        return $model->fill($attributes)->save() ? $model : null;
    }

    /**
     * Delete the model from database
     */
    public function delete(): bool
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            
            $this->db->bindValue(
                $stmt, 
                ':id', 
                $this->attributes[$this->primaryKey], 
                PDO::PARAM_INT
            );
            
            return (bool) $this->db->execute($stmt);
        } catch (\Exception $e) {
            error_log("Error deleting from {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete the model from database using conditions
     */
    protected function deleteWhere(array $conditions): bool
    {
        try {
            $where = [];
            $bindings = [];
            
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = :{$field}";
                $bindings[":{$field}"] = $value;
            }

            $sql = "DELETE FROM {$this->table} WHERE " . implode(' AND ', $where);
            $stmt = $this->db->prepare($sql);
            
            foreach ($bindings as $param => $value) {
                $this->db->bindValue(
                    $stmt,
                    $param,
                    $value,
                    $this->getFieldType(ltrim($param, ':'))
                );
            }
            
            return (bool) $this->db->execute($stmt);
        } catch (\Exception $e) {
            error_log("Error deleting from {$this->table}: " . $e->getMessage());
            return false;
        }
    }
}
