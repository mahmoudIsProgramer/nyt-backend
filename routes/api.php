<?php

use App\Controllers\ArticlesController;
use App\Controllers\ArticleController;
use App\Middleware\AuthMiddleware;

/**
 * @var \App\Core\Router $router
 */

$articlesController = new ArticlesController();
$articleController = new ArticleController();

// Public Routes
$router->get('api/articles/search', function() use ($articlesController) {
    $articlesController->search();
});

$router->get('api/articles/{url}', function(string $articleUrl) use ($articlesController) {
    $articlesController->getArticle($articleUrl);
});

// Protected Routes (require authentication)
$router->post('api/articles/favorites', function() use ($articleController) {
    $articleController->addToFavorites();
})->middleware([AuthMiddleware::class, 'authenticate']);

$router->get('api/articles/favorites', function() use ($articleController) {
    $articleController->getFavorites();
})->middleware([AuthMiddleware::class, 'authenticate']);
