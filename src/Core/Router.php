<?php

namespace App\Core;

use Exception;

class Router {
    private const ALLOWED_METHODS = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
    
    private array $routes = [];
    private array $uri;
    private string $requestMethod;
    private array $apiHeaders = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
        'Access-Control-Max-Age' => '3600',
        'Content-Type' => 'application/json; charset=UTF-8'
    ];
    private array $routeMiddleware = [];
    private ?string $currentRoute = null;
    private string $currentPrefix = '';

    public function __construct() {
        $this->uri = $this->parseUri();
        $this->requestMethod = $this->getRequestMethod();
        $this->validateRequestMethod();
    }

    /**
     * Register a GET route
     */
    public function get(string $path, callable $handler, array $middleware = []): self {
        return $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * Register a POST route
     */
    public function post(string $path, callable $handler, array $middleware = []): self {
        return $this->addRoute('POST', $path, $handler, $middleware);
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
     * Group routes with shared attributes
     */
    public function group(array $attributes, callable $callback): void {
        // Store the previous prefix
        $previousPrefix = $this->currentPrefix;
        
        // Set the new prefix by combining the previous prefix with the new one
        if (isset($attributes['prefix'])) {
            $this->currentPrefix = $previousPrefix . '/' . trim($attributes['prefix'], '/');
        }
        
        // Execute the callback that contains the routes
        $callback();
        
        // Restore the previous prefix
        $this->currentPrefix = $previousPrefix;
    }

    /**
     * Add a route to the router
     */
    private function addRoute(string $method, string $path, callable $handler, array $middleware = []): self {
        // Normalize the path with prefix
        $path = trim($this->currentPrefix . '/' . trim($path, '/'), '/');
        $this->currentRoute = "{$method}:{$path}";
        $this->routes[$method][$path] = [
            'callback' => $handler,
            'middleware' => $middleware
        ];
        return $this;
    }

    /**
     * Add middleware to a route
     */
    public function middleware($middleware): self {
        if ($this->currentRoute) {
            if (!isset($this->routeMiddleware[$this->currentRoute])) {
                $this->routeMiddleware[$this->currentRoute] = [];
            }
            $this->routeMiddleware[$this->currentRoute][] = $middleware;
        }
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
    private function setHeaders(bool $isApi = false): void {
        if ($isApi) {
            foreach ($this->apiHeaders as $name => $value) {
                header("$name: $value");
            }
        } else {
            header('Content-Type: text/html; charset=UTF-8');
        }
    }

    /**
     * Execute the middleware chain
     */
    private function executeMiddleware(callable $handler, string $route): callable {
        $next = $handler;
        
        if (isset($this->routeMiddleware[$route])) {
            foreach (array_reverse($this->routeMiddleware[$route]) as $middleware) {
                if (is_array($middleware)) {
                    $next = call_user_func($middleware, $next);
                } else {
                    $next = $middleware($next);
                }
            }
        }
        
        return $next;
    }

    /**
     * Handle static files
     */
    private function handleStaticFile(string $path): bool {
        $publicPath = dirname(__DIR__, 2) . '/public';
        $filePath = $publicPath . '/' . $path;

        if (file_exists($filePath) && is_file($filePath)) {
            $ext = pathinfo($filePath, PATHINFO_EXTENSION);
            $contentType = match($ext) {
                'css' => 'text/css',
                'js' => 'application/javascript',
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                default => 'application/octet-stream'
            };

            header("Content-Type: $contentType");
            readfile($filePath);
            return true;
        }

        return false;
    }

    /**
     * Handle the request
     */
    public function dispatch(): void {
        try {
            // Handle CORS preflight request
            if ($this->requestMethod === 'OPTIONS') {
                $this->setHeaders(true);
                http_response_code(200);
                exit;
            }

            $pathPattern = $this->getPathPattern($this->uri);

            // Try to serve static file first
            if ($this->requestMethod === 'GET' && $this->handleStaticFile($pathPattern)) {
                return;
            }

            $route = "{$this->requestMethod}:{$pathPattern}";
            $isApi = strpos($pathPattern, 'api/') === 0;

            // Set appropriate headers based on route type
            $this->setHeaders($isApi);

            // Check if route exists
            if (!isset($this->routes[$this->requestMethod][$pathPattern])) {
                throw new Exception('Not Found', 404);
            }

            $handler = $this->routes[$this->requestMethod][$pathPattern]['callback'];
            $middleware = $this->routes[$this->requestMethod][$pathPattern]['middleware'];

            // Execute middleware
            foreach ($middleware as $middlewareClass) {
                $middlewareInstance = new $middlewareClass();
                if (!$middlewareInstance->handle()) {
                    return;
                }
            }

            $handler = $this->executeMiddleware($handler, $route);
            
            ob_start();
            $handler();
            $content = ob_get_clean();

            if ($isApi) {
                echo $content;
            } else {
                // For HTML content, ensure we don't have any previous output
                if (!headers_sent()) {
                    echo $content;
                }
            }

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
    public function addHeader(string $name, string $value): void {
        $this->apiHeaders[$name] = $value;
    }
}
