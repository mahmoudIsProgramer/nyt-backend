<?php

namespace App\Core;

class RouteServiceProvider {
    private Router $router;
    private string $routesPath;
    private string $apiPrefix = 'api';

    public function __construct(Router $router, string $basePath) {
        $this->router = $router;
        $this->routesPath = $basePath . '/routes';
    }

    public function boot(): void {
        $this->loadWebRoutes();
        $this->loadApiRoutes();
    }

    private function loadWebRoutes(): void {
        $router = $this->router;
        require $this->routesPath . '/web.php';
    }

    private function loadApiRoutes(): void {
        $router = $this->router;
        $router->group(['prefix' => $this->apiPrefix], function() {
            require $this->routesPath . '/api.php';
        });
    }
}
