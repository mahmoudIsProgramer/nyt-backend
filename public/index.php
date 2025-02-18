<?php

declare(strict_types=1);

/**
 * NYT Application Entry Point
 * 
 * This is the front controller for the NYT application.
 * All requests are routed through this file.
 */

// Load composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

use App\Core\App;
use Dotenv\Dotenv;

// Set error reporting based on environment
$isProduction = getenv('APP_ENV') === 'production';
error_reporting($isProduction ? 0 : E_ALL);
ini_set('display_errors', $isProduction ? '0' : '1');

try {
    // Initialize the application
    $app = App::getInstance();
    
    // Load routes
    require dirname(__DIR__) . '/routes/web.php';
    require dirname(__DIR__) . '/routes/api.php';
    
    // Run the application
    $app->run();
} catch (\Throwable $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    
    $response = [
        'status' => 'error',
        'message' => $isProduction ? 'Internal Server Error' : $e->getMessage(),
        'code' => 500
    ];
    
    echo json_encode($response);
}
