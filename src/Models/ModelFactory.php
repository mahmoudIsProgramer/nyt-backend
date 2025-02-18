<?php

namespace App\Models;

class ModelFactory
{
    private static array $instances = [];
    private static array $modelMap = [
        'users' => User::class,
        // Add more model mappings here
    ];

    public static function make(string $table): Model
    {
        if (!isset(self::$modelMap[$table])) {
            throw new \InvalidArgumentException("No model found for table: {$table}");
        }

        if (!isset(self::$instances[$table])) {
            $modelClass = self::$modelMap[$table];
            self::$instances[$table] = new $modelClass();
        }

        return self::$instances[$table];
    }
}
