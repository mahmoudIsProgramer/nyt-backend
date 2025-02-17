<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\ArticlesController;

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize router and controller
$router = new Router();
$controller = new ArticlesController();

// Define routes
$router->get('api/articles/search', function() use ($controller) {
    $controller->search();
});

$router->get('api/articles/{url}', function(string $articleUrl) use ($controller) {
    $controller->getArticle($articleUrl);
});

// Dispatch the request
$router->dispatch();
