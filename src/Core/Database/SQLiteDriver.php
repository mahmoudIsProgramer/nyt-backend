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
}