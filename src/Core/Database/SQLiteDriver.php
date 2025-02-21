<?php

namespace App\Core\Database;

use App\Core\Interfaces\DatabaseDriverInterface;
use SQLite3;

class SQLiteDriver implements DatabaseDriverInterface
{
    private SQLite3 $connection;

    public function connect(array $config): mixed
    {
        $this->connection = new SQLite3($config['database']);
        return $this->connection;
    }

    public function prepare(string $sql): mixed
    {
        return $this->connection->prepare($sql);
    }

    public function execute(mixed $statement): mixed
    {
        return $statement->execute();
    }

    public function bindValue(mixed $statement, string $param, mixed $value, int $type): void
    {
        $sqliteType = $type === \PDO::PARAM_INT ? SQLITE3_INTEGER : SQLITE3_TEXT;
        $statement->bindValue($param, $value, $sqliteType);
    }

    public function fetchArray(mixed $result): ?array
    {
        return $result->fetchArray(SQLITE3_ASSOC) ?: null;
    }

    public function fetchAll(mixed $result): array
    {
        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getConnection(): mixed
    {
        return $this->connection;
    }
 

    public function lastInsertRowID(): int
    {
        return $this->connection->lastInsertRowID();
    }

    public function executeQuery(string $sql, array $params, int $paramType): ?array
    {
        try {
            $stmt = $this->prepare($sql);
            
            foreach ($params as $key => $value) {
                $this->bindValue($stmt, $key, $value, $paramType);
            }
            
            $result = $this->execute($stmt);
            return $this->fetchArray($result);
        } catch (\Exception $e) {
            error_log("Query execution error: " . $e->getMessage());
            return null;
        }
    }

    public function executeDelete(string $table, string $primaryKey, mixed $id): bool
    {
        try {
            $sql = "DELETE FROM {$table} WHERE {$primaryKey} = :id";
            $stmt = $this->prepare($sql);
            $this->bindValue($stmt, ':id', $id, \PDO::PARAM_INT);
            
            return (bool) $this->execute($stmt);
        } catch (\Exception $e) {
            error_log("Delete error: " . $e->getMessage());
            return false;
        }
    }

    public function getFieldType(string $field): int
    {
        return match($field) {
            'user_id', 'id' => \PDO::PARAM_INT,
            default => \PDO::PARAM_STR
        };
    }

    public function executeInsert(string $table, array $fields, array $values, array $bindings): bool
    {
        try {
            $placeholders = array_map(fn($field) => ":$field", $fields);
            
            $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . 
                   ") VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->prepare($sql);
            
            foreach ($bindings as $field => $binding) {
                $this->bindValue($stmt, ":$field", $binding['value'], $binding['type']);
            }
            
            return (bool) $this->execute($stmt);
        } catch (\Exception $e) {
            error_log("Insert error: " . $e->getMessage());
            return false;
        }
    }

    public function executeFindById(string $table, string $primaryKey, mixed $id): ?array
    {
        return $this->executeQuery(
            "SELECT * FROM {$table} WHERE {$primaryKey} = :id LIMIT 1",
            [':id' => $id],
            \PDO::PARAM_INT
        );
    }

    public function executeFindBy(string $table, string $field, mixed $value, int $paramType): ?array
    {
        return $this->executeQuery(
            "SELECT * FROM {$table} WHERE {$field} = :value LIMIT 1",
            [':value' => $value],
            $paramType
        );
    }

    public function executeFetchAll(string $table, string $primaryKey, mixed $id): array
    {
        try {
            $stmt = $this->prepare("SELECT * FROM {$table} WHERE {$primaryKey} = :id");
            $this->bindValue($stmt, ':id', $id, \PDO::PARAM_INT);
            $result = $this->execute($stmt);
            return $this->fetchAll($result);
        } catch (\Exception $e) {
            error_log("Error fetching all records: " . $e->getMessage());
            return [];
        }
    }

    public function executeWhereFirst(string $table, array $where): ?array
    {
        try {
            $sql = "SELECT * FROM {$table}";
            $params = [];
            
            if (!empty($where)) {
                $condition = $where[0];
                $sql .= " WHERE {$condition[0]} {$condition[1]} :value LIMIT 1";
                $params[':value'] = $condition[2];
            }
            
            return $this->executeQuery(
                $sql,
                $params,
                $this->getFieldType($where[0][0] ?? 'default')
            );
        } catch (\Exception $e) {
            error_log("Error in whereFirst: " . $e->getMessage());
            return null;
        }
    }

    public function executeCreate(string $table, string $primaryKey, array $attributes): ?array 
    {
        try {
            $fields = array_keys($attributes);
            $bindings = [];
            
            foreach ($attributes as $field => $value) {
                $bindings[$field] = [
                    'value' => $value,
                    'type' => $this->getFieldType($field)
                ];
            }
            
            if (!$this->executeInsert($table, $fields, $attributes, $bindings)) {
                error_log("Failed to save model");
                return null;
            }

            $insertedId = $this->lastInsertRowID();
            
            if (!$insertedId) {
                error_log("No ID returned after save");
                return null;
            }
            
            return $this->executeFindById($table, $primaryKey, $insertedId);
        } catch (\Exception $e) {
            error_log("Create error: " . $e->getMessage());
            return null;
        }
    }

    public function executeDeleteWhere(string $table, array $conditions): bool 
    {
        try {
            $where = [];
            $bindings = [];
            
            foreach ($conditions as $field => $value) {
                $where[] = "{$field} = :{$field}";
                $bindings[":{$field}"] = $value;
            }

            $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $where);
            $stmt = $this->prepare($sql);
            
            foreach ($bindings as $param => $value) {
                $this->bindValue(
                    $stmt,
                    $param,
                    $value,
                    $this->getFieldType(ltrim($param, ':'))
                );
            }
            
            return (bool) $this->execute($stmt);
        } catch (\Exception $e) {
            error_log("Error deleting from {$table}: " . $e->getMessage());
            return false;
        }
    }
}