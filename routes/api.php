<?php

use App\Controllers\ArticlesController;
use App\Controllers\FavoriteController;
use App\Controllers\AuthController;
use App\Controllers\LogController;
use App\Core\App;
use App\Middleware\JWTAuthMiddleware;
use App\Middleware\LoggerMiddleware;

/**
 * @var \App\Core\Router $router
 */

$router = App::getInstance()->router;

$articlesController = new ArticlesController();
$favoriteController = new FavoriteController();
$authController = new AuthController();
$logController = new LogController();

// Public Auth Routes (no authentication required)
$router->post('/auth/register', function() use ($authController) {
    $authController->register();
}, [LoggerMiddleware::class]);

$router->post('/auth/login', function() use ($authController) {
    $authController->login();
}, [LoggerMiddleware::class]);

$router->post('/auth/logout', function() use ($authController) {
    $authController->logout();
}, [JWTAuthMiddleware::class]);


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

$router->post('/logs/web', function() use ($logController) {
    $logController->store();
});
