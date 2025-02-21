<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Utils\Helper;

class JWTAuthMiddleware implements MiddlewareInterface
{
    private string $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'your-secret-key';
    }

    public function handle(): bool
    {
        $headers = getallheaders();
        $token = $this->extractToken($headers);

        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'No token provided']);
            return false;
        }

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            $_REQUEST['user_id'] = $decoded->sub;
            return true;
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            return false;
        }
    }

    private function extractToken(array $headers): ?string
    {
        $authHeader = $headers['Authorization'] ?? '';
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }
}