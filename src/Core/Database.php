<?php

namespace App\Core;

use Exception;
use App\Core\Database\DatabaseFactory;
use App\Core\Interfaces\DatabaseDriverInterface;

class Database
{
    private static ?Database $instance = null;
    private DatabaseDriverInterface $driver;

    private function __construct()
    {
        $config = $this->loadConfig();
        $this->driver = DatabaseFactory::create($config['driver']);
        $this->driver->connect($config);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig(): array
    {
        return [
            'driver' => $_ENV['DB_DRIVER'] ?? 'sqlite',
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'database' => $_ENV['DB_DATABASE'] ?? 'database.sqlite',
            'username' => $_ENV['DB_USERNAME'] ?? '',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
        ];
    }

    public function getDriver(): DatabaseDriverInterface
    {
        return $this->driver;
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
