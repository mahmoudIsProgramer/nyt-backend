<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\RouteServiceProvider;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Initialize Router
$router = new Router();

// Load routes through service provider
$routeProvider = new RouteServiceProvider($router, __DIR__ . '/..');
$routeProvider->boot();

// Handle the request
$router->dispatch();
