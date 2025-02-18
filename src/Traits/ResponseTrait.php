<?php

namespace App\Traits;

trait ResponseTrait {
    /**
     * Send a JSON response
     */
    public function jsonResponse(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
        exit;
    }

    /**
     * Send an error response
     */
    public function errorResponse(string $message, int $statusCode = 400): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'code' => $statusCode
        ]);
        exit;
    }
}
