<?php

namespace App\Config;

use Dotenv\Dotenv;

class Config {
    private static ?Config $instance = null;
    private array $config;

    private function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->config = [
            'nyt_api_key' => $_ENV['NYT_API_KEY'],
            'jwt_secret' => $_ENV['JWT_SECRET'],
            // 'db_path' => __DIR__ . '/../../' . $_ENV['DB_PATH'],
        ];
    }

    public static function getInstance(): Config {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get(string $key): ?string {
        return $this->config[$key] ?? null;
    }
}
