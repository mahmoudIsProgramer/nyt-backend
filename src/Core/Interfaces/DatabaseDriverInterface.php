<?php

namespace App\Core\Interfaces;

interface DatabaseDriverInterface
{
    public function connect(array $config): mixed;
    public function prepare(string $sql);
    public function execute($stmt);
    public function bindValue($stmt, string $param, $value, int $type);
    public function fetchArray($result): ?array;
    public function fetchAll(mixed $result): array;
    public function getConnection(): mixed;
    public function lastInsertRowID(): int;
    public function executeQuery(string $sql, array $params, int $paramType): ?array;
    public function executeDelete(string $table, string $primaryKey, mixed $id): bool;
    public function getFieldType(string $field): int;
    public function executeFindBy(string $table, string $field, mixed $value, int $type): ?array;
    public function executeFindById(string $table, string $primaryKey, mixed $id): ?array;
    public function executeFetchAll(string $table, string $primaryKey, mixed $id): array;
    public function executeInsert(string $table, array $fields, array $attributes, array $bindings): bool;
    public function executeWhereFirst(string $table, array $conditions): ?array;
    public function executeCreate(string $table, string $primaryKey, array $attributes): ?array;
    public function executeDeleteWhere(string $table, array $conditions): bool;

}