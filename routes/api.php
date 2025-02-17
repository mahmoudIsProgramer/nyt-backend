<?php

use App\Controllers\ArticlesController;

/**
 * @var \App\Core\Router $router
 */

$controller = new ArticlesController();

// Articles Routes
$router->get('api/articles/search', function() use ($controller) {
    $controller->search();
});

$router->get('api/articles/{url}', function(string $articleUrl) use ($controller) {
    $controller->getArticle($articleUrl);
});
