<?php

namespace App\Core\Database;

use App\Core\Interfaces\DatabaseDriverInterface;
use PDO;

class MySQLDriver implements DatabaseDriverInterface
{
    private PDO $connection;

    public function connect(array $config): mixed
    {
        $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
        $this->connection = new PDO($dsn, $config['username'], $config['password']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->connection;
    }

    public function prepare(string $sql): mixed
    {
        return $this->connection->prepare($sql);
    }

    public function execute(mixed $statement): mixed
    {
        $statement->execute();
        return $statement;
    }

    public function bindValue(mixed $statement, string $param, mixed $value, int $type): void
    {
        $statement->bindValue($param, $value, $type);
    }

    public function fetchArray(mixed $result): ?array
    {
        return $result->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function fetchAll(mixed $result): array
    {
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConnection(): mixed
    {
        return $this->connection;
    }
 

    public function lastInsertRowID(): int
    {
        return (int) $this->connection->lastInsertId();
    }
}