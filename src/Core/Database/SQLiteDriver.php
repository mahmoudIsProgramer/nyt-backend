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
}