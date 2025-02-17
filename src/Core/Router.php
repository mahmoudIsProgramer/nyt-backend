<?php

namespace App\Core;

use Exception;

class Router {
    private array $routes = [];
    private array $uri;
    private string $requestMethod;

    public function __construct() {
        $this->uri = $this->parseUri();
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    private function parseUri(): array {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return array_values(array_filter(explode('/', $path)));
    }

    public function get(string $path, callable $handler): void {
        $this->routes['GET'][$path] = $handler;
    }

    private function getPathPattern(array $segments): string {
        return implode('/', $segments);
    }

    public function dispatch(): void {
        // Handle CORS preflight request
        if ($this->requestMethod === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');
            http_response_code(200);
            exit;
        }

        try {
            if (empty($this->uri) || $this->uri[0] !== 'api') {
                throw new Exception('Not Found', 404);
            }

            $pathPattern = $this->getPathPattern($this->uri);

            // Check if route exists
            if (!isset($this->routes[$this->requestMethod][$pathPattern])) {
                throw new Exception('Not Found', 404);
            }

            // Execute route handler
            $handler = $this->routes[$this->requestMethod][$pathPattern];
            $handler();

        } catch (Exception $e) {
            $this->handleError($e);
        }
    }
 

    private function handleError(Exception $e): void {
        $statusCode = $e->getCode() ?: 500;
        $statusCode = is_numeric($statusCode) ? $statusCode : 500;

        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $statusCode
        ], JSON_PRETTY_PRINT);
    }
}
