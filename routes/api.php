<?php

use App\Controllers\ArticlesController;
use App\Controllers\ArticleController;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use App\Core\App;

/**
 * @var \App\Core\Router $router
 */

$router = App::getInstance()->router;

$articlesController = new ArticlesController();
$articleController = new ArticleController();
$authController = new AuthController();

// Public Auth Routes (no authentication required)
$router->post('api/auth/register', function() use ($authController) {
    $authController->register();
});

$router->post('api/auth/login', function() use ($authController) {
    $authController->login();
});

// Public Article Routes
$router->get('api/articles/search', function() use ($articlesController) {
    $articlesController->search();
});

$router->get('api/articles/{url}', function(string $articleUrl) use ($articlesController) {
    $articlesController->getArticle($articleUrl);
});

// Protected Routes (require authentication)
$router->get('api/user', function() use ($authController) {
    $authController->getUser();
})->middleware([AuthMiddleware::class, 'authenticate']);

$router->post('api/articles/favorites', function() use ($articleController) {
    $articleController->addToFavorites();
})->middleware([AuthMiddleware::class, 'authenticate']);

$router->get('api/articles/favorites', function() use ($articleController) {
    $articleController->getFavorites();
})->middleware([AuthMiddleware::class, 'authenticate']);
