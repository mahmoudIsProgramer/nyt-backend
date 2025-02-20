<?php

use App\Controllers\ArticlesController;
use App\Controllers\FavoriteController;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use App\Core\App;

/**
 * @var \App\Core\Router $router
 */

$router = App::getInstance()->router;

$articlesController = new ArticlesController();
$favoriteController = new FavoriteController();
$authController = new AuthController();

// Public Auth Routes (no authentication required)
$router->post('/auth/register', function() use ($authController) {
    $authController->register();
});

$router->post('/auth/login', function() use ($authController) {
    $authController->login();
});

// Public Article Routes
$router->get('/articles/search', function() use ($articlesController) {
    $articlesController->search();
});

$router->get('/articles/{url}', function(string $articleUrl) use ($articlesController) {
    $articlesController->getArticle($articleUrl);
});

// Protected Routes (require authentication)
$router->get('/user', function() use ($authController) { 
    $authController->getUser();
})->middleware([AuthMiddleware::class, 'authenticate']);

$router->post('/articles/favorites/toggle', function() use ($favoriteController) {
    $favoriteController->toggleFavorite();
})->middleware([AuthMiddleware::class, 'authenticate']);

$router->get('/articles/favorites', function() use ($favoriteController) {
    $favoriteController->getFavorites();
})->middleware([AuthMiddleware::class, 'authenticate']);
