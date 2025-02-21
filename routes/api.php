<?php

use App\Controllers\ArticlesController;
use App\Controllers\FavoriteController;
use App\Controllers\AuthController;
use App\Core\App;
use App\Middleware\JWTAuthMiddleware;

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
}, [JWTAuthMiddleware::class]);

$router->get('/articles/{url}', function(string $articleUrl) use ($articlesController) {
    $articlesController->getArticle($articleUrl);
}, [JWTAuthMiddleware::class]);

// Protected Routes (require authentication)
$router->get('/get-user-profile', function() use ($authController) { 
    $authController->getUser();
}, [JWTAuthMiddleware::class]);

$router->post('/articles/favorites/toggle', function() use ($favoriteController) {
    $favoriteController->toggleFavorite();
}, [JWTAuthMiddleware::class]);

$router->get('/articles/favorites', function() use ($favoriteController) {
    $favoriteController->getFavorites();
}, [JWTAuthMiddleware::class]);
