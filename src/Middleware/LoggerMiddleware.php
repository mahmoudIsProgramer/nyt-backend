<?php

namespace App\Middleware;

use App\Utils\Logger;
use App\Traits\ResponseTrait;

class LoggerMiddleware implements MiddlewareInterface
{
    use ResponseTrait;
    
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function handle(): bool
    {
        // Log the request
        $this->logRequest();

        // Register a shutdown function to capture the response
        register_shutdown_function(function() {
            $response = ob_get_contents();
            if (!empty($response)) {
                $this->logResponse($response);
            }
        });

        // Return true to continue the request
        return true;
    }

    private function logRequest(): void
    {
        $rawBody = file_get_contents('php://input');
        
        $request = [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            'url' => $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
            'headers' => getallheaders(),
            'body' => !empty($rawBody) ? json_decode($rawBody, true) : [],
            'query' => $_GET
        ];

        $this->logger->logRequest($request);
    }

    private function logResponse(string $response): void
    {
        $responseData = [
            'status' => http_response_code(),
            'headers' => headers_list(),
            'body' => json_decode($response, true) ?? $response
        ];

        $this->logger->logResponse($responseData);
    }
}