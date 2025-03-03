<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Interfaces\DatabaseDriverInterface;
use App\Utils\Helper;
use PDO;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $attributes = [];
    protected DatabaseDriverInterface $db;
    protected $query = [];

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
        return array_merge(
            [$this->primaryKey => $this->attributes[$this->primaryKey] ?? null], 
            $this->attributes
        );
    }

    /**
     * Static create method that all models will inherit
     */
    public static function create(array $attributes): ?static
    {
        try {
            $instance = new static();
            $instance->fill($attributes);
            
            $data = $instance->db->executeCreate($instance->table, $instance->primaryKey, $instance->attributes);
            
            if (!$data) {
                return null;
            }
            
            $newInstance = new static();
            $newInstance->attributes = $data;
            
            return $newInstance;
        } catch (\Exception $e) {
            error_log("Create error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Instance method for saving the model
     */
    protected function save(): bool
    {
        if (empty($this->attributes)) {
            error_log("No attributes to save");
            return false;
        }

        $data = $this->db->executeCreate($this->table, $this->primaryKey, $this->attributes);
        
        if (!$data) {
            return false;
        }
        
        $this->attributes = $data;
        return true;
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
            'id', 'user_id' => PDO::PARAM_INT,
            default => PDO::PARAM_STR
        };
    }

    /**
     * Find a record by any field
     */
    public function findBy(string $field, mixed $value): ?array
    {
        return $this->db->executeFindBy($this->table, $field, $value, $this->getFieldType($field));
    }

    /**
     * Find a record by primary key
     */
    public function findById(int $id): ?array
    {
        return $this->db->executeFindById($this->table, $this->primaryKey, $id);
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
        return $this->db->executeFetchAll($this->table, $this->primaryKey, null);
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
        return $this->db->executeQuery($sql, $params, $paramType);
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
        return $this->db->executeDelete(
            $this->table,
            $this->primaryKey,
            $this->attributes[$this->primaryKey]
        );
    }

    /**
     * Delete the model from database using conditions
     */
    protected function deleteWhere(array $conditions): bool
    {
        return $this->db->executeDeleteWhere($this->table, $conditions);
    }

    public function where(string $column, string $operator, $value): self
    {
        $this->query['where'][] = [$column, $operator, $value];
        return $this;
    }

    public function first(): ?array
    {
        $result = $this->db->executeWhereFirst($this->table, $this->query['where'] ?? []);
        $this->query = []; // Reset query builder
        return $result;
    }
}
