<?php

namespace App\Core;

use Exception;

class Router {
    private const ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
    
    private array $routes = [];
    private array $uri;
    private string $requestMethod;
    private array $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
        'Access-Control-Max-Age' => '3600',
        'Content-Type' => 'application/json; charset=UTF-8'
    ];
    private array $middleware = [];

    public function __construct() {
        $this->uri = $this->parseUri();
        $this->requestMethod = $this->getRequestMethod();
        $this->validateRequestMethod();
    }

    /**
     * Register a GET route
     */
    public function get(string $path, callable $handler): self {
        return $this->addRoute('GET', $path, $handler);
    }

    /**
     * Register a POST route
     */
    public function post(string $path, callable $handler): self {
        return $this->addRoute('POST', $path, $handler);
    }

    /**
     * Register a PUT route
     */
    public function put(string $path, callable $handler): self {
        return $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Register a DELETE route
     */
    public function delete(string $path, callable $handler): self {
        return $this->addRoute('DELETE', $path, $handler);
    }

    /**
     * Add a route to the router
     */
    private function addRoute(string $method, string $path, callable $handler): self {
        $this->routes[$method][$path] = $handler;
        return $this;
    }

    /**
     * Add middleware to a route
     */
    public function middleware(callable $middleware): self {
        $this->middleware[] = $middleware;
        return $this;
    }

    /**
     * Parse the URI into segments
     */
    private function parseUri(): array {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        $_GET = [];
        
        if ($query) {
            parse_str($query, $_GET);
        }
        
        return array_values(array_filter(explode('/', $path)));
    }

    /**
     * Get the request method
     */
    private function getRequestMethod(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Validate the request method
     */
    private function validateRequestMethod(): void {
        if (!in_array($this->requestMethod, self::ALLOWED_METHODS)) {
            throw new Exception('Method not allowed', 405);
        }
    }

    /**
     * Get the current path pattern
     */
    private function getPathPattern(array $segments): string {
        return implode('/', $segments);
    }

    /**
     * Set response headers
     */
    private function setHeaders(): void {
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
    }

    /**
     * Execute the middleware chain
     */
    private function executeMiddleware(callable $handler): callable {
        $next = $handler;
        
        // Execute middleware in reverse order
        foreach (array_reverse($this->middleware) as $middleware) {
            $next = $middleware($next);
        }
        
        return $next;
    }

    /**
     * Handle the request
     */
    public function dispatch(): void {
        try {
            $this->setHeaders();

            // Handle CORS preflight request
            if ($this->requestMethod === 'OPTIONS') {
                http_response_code(200);
                exit;
            }

            // Validate API request
            if (empty($this->uri) || $this->uri[0] !== 'api') {
                throw new Exception('Not Found', 404);
            }

            $pathPattern = $this->getPathPattern($this->uri);

            // Check if route exists
            if (!isset($this->routes[$this->requestMethod][$pathPattern])) {
                throw new Exception('Not Found', 404);
            }

            $handler = $this->routes[$this->requestMethod][$pathPattern];
            $handler = $this->executeMiddleware($handler);
            
            // Clear middleware after handling request
            $this->middleware = [];
            
            $handler();

        } catch (Exception $e) {
            $this->handleError($e);
        }
    }

    /**
     * Handle errors and return JSON response
     */
    private function handleError(Exception $e): void {
        $statusCode = $this->normalizeStatusCode($e->getCode());
        http_response_code($statusCode);

        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'code' => $statusCode,
            'timestamp' => date('c')
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Normalize HTTP status code
     */
    private function normalizeStatusCode(int $code): int {
        return in_array($code, [400, 401, 403, 404, 405, 500]) ? $code : 500;
    }

    /**
     * Add a custom header
     */
    public function addHeader(string $name, string $value): self {
        $this->headers[$name] = $value;
        return $this;
    }
}
