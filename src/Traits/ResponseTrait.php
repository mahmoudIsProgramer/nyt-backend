<?php

namespace App\Traits;

use App\Http\Responses\ApiResponse;

trait ResponseTrait
{
    protected function respond(array $response, int $statusCode = 200): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
        }
        
        echo json_encode($response);
        exit;
    }

    protected function success(array $data = [], string $message = '', int $code = 200): void
    {
        $this->respond(ApiResponse::success($data, $message, $code), $code);
    }

    protected function error(string $message, int $code = 400): void
    {
        $this->respond(ApiResponse::error($message, $code), $code);
    }

    protected function authResponse(string $token, array $userData = [], string $message = ''): void
    {
        $this->respond(ApiResponse::auth($token, $userData, $message));
    }
    
    protected function noContent(): void
    {
        $this->respond([], 204);
    }
    
    protected function created(array $data = [], string $message = ''): void
    {
        $this->respond(ApiResponse::success($data, $message), 201);
    }
    
    protected function forbidden(string $message = 'Forbidden'): void
    {
        $this->respond(ApiResponse::error($message, 403), 403);
    }
    
    protected function unauthorized(string $message = 'Unauthorized'): void
    {
        $this->respond(ApiResponse::error($message, 401), 401);
    }
    
    protected function notFound(string $message = 'Not Found'): void
    {
        $this->respond(ApiResponse::error($message, 404), 404);
    }
    
    protected function validationError(array|string $errors): void
    {
        $message = is_array($errors) ? reset($errors) : $errors;
        $this->respond(ApiResponse::error($message, 422), 422);
    }
}
