<?php

namespace App\Controllers;

class BaseController {
    /**
     * Send a JSON response
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        
        echo json_encode($data);
        exit;
    }

    /**
     * Send an error response
     */
    protected function errorResponse(string $message, int $statusCode = 400): void {
        $this->jsonResponse([
            'status' => 'error',
            'message' => $message
        ], $statusCode);
    }
}
