<?php

namespace App\Core\Interfaces;

interface DatabaseDriverInterface
{
    public function connect(array $config): mixed;
    public function prepare(string $sql): mixed;
    public function execute(mixed $statement): mixed;
    public function bindValue(mixed $statement, string $param, mixed $value, int $type): void;
    public function fetchArray(mixed $result): ?array;
    public function fetchAll(mixed $result): array;
    public function getConnection(): mixed;
    public function lastInsertRowID(): int;  // Changed to match SQLite3's method name
    public function executeQuery(string $sql, array $params, int $paramType): ?array;
    public function executeDelete(string $table, string $primaryKey, mixed $id): bool;
    public function getFieldType(string $field): int;
    public function executeFindBy(string $table, string $field, mixed $value, int $type): ?array;
    public function executeFindById(string $table, string $primaryKey, mixed $id): ?array;
    public function executeFetchAll(string $table, string $primaryKey, mixed $id): array;
    
}