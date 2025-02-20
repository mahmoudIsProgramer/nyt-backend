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

use App\Core\App;
use Dotenv\Dotenv;

try {
    // Initialize the application
    $app = App::getInstance();
    
    // Bootstrap the application
    $app->bootstrap();
    
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
