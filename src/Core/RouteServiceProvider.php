<?php

namespace App\Core;

class RouteServiceProvider {
    private Router $router;
    private string $routesPath;

    public function __construct(Router $router, string $basePath) {
        $this->router = $router;
        $this->routesPath = $basePath . '/routes';
    }

    public function boot(): void {
        $this->loadApiRoutes();
    }

    private function loadApiRoutes(): void {
        $router = $this->router;
        require $this->routesPath . '/api.php';
    }
}
