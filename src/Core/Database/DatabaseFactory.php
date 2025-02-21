<?php

namespace App\Core\Database;

use App\Core\Interfaces\DatabaseDriverInterface;

class DatabaseFactory
{
    public static function create(string $driver): DatabaseDriverInterface
    {
        return match($driver) {
            'mysql' => new MySQLDriver(),
            'sqlite' => new SQLiteDriver(),
            default => throw new \InvalidArgumentException("Unsupported database driver: $driver")
        };
    }
}