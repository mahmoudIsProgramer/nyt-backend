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

// Initialize and run the application
use App\Core\App;

try {
    $app = App::getInstance();
    $app->bootstrap();
    $app->run();
} catch (\Throwable $e) {
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, $e->getMessage() . PHP_EOL);
        exit(1);
    }
    
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Critical error occurred',
        'code' => 500
    ]);
    exit(1);
}
