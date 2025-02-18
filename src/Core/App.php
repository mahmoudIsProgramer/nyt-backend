<?php

namespace App\Core;

use Dotenv\Dotenv;

class App
{
    private static ?App $instance = null;
    private Router $router;

    private function __construct()
    {
        $this->router = new Router();
    }

    public static function getInstance(): App
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function bootstrap(): void
    {
        $this->loadEnvironmentVariables();
        $this->configureErrorHandling();
        $this->registerRoutes();
    }

    private function loadEnvironmentVariables(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
    }

    private function configureErrorHandling(): void
    {
        $isProduction = getenv('APP_ENV') === 'production';
        
        error_reporting($isProduction ? 0 : E_ALL);
        ini_set('display_errors', $isProduction ? '0' : '1');

        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    public function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $this->sendJsonError('Internal Server Error');
        return true;
    }

    public function handleException(\Throwable $e): void
    {
        $isProduction = getenv('APP_ENV') === 'production';
        $message = $isProduction ? 'Internal Server Error' : $e->getMessage();
        
        $this->sendJsonError($message);
    }

    private function sendJsonError(string $message, int $code = 500): void
    {
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json');
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'code' => $code
        ]);
        exit;
    }

    private function registerRoutes(): void
    {
        $routeProvider = new RouteServiceProvider($this->router, dirname(__DIR__, 2));
        $routeProvider->boot();
    }

    public function run(): void
    {
        $this->router->dispatch();
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialize
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
