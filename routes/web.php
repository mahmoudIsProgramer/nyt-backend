<?php

use App\Core\App;
use App\Controllers\PageController;

/** @var \App\Core\Router $router */
$router = App::getInstance()->router;

$pageController = new PageController();

// Frontend Routes
$router->get('', function() use ($pageController) { // Root path
    $pageController->home();
});

$router->get('dashboard', function() use ($pageController) {
    $pageController->dashboard();
});
