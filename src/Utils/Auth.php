<?php

namespace App\Utils;

use App\Config\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private static function getSecret(): string {
        return Config::getInstance()->get('jwt_secret');
    }

    public static function generateToken(int $userId): string {
        $payload = [
            'user_id' => $userId,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // 24 hours
        ];

        return JWT::encode($payload, self::getSecret(), 'HS256');
    }

    public static function validateToken(?string $token): ?int {
        if (!$token) {
            return null;
        }

        try {
            $decoded = JWT::decode($token, new Key(self::getSecret(), 'HS256'));
            return $decoded->user_id;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getAuthorizationHeader(): ?string {
        $headers = null;
        
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        
        return $headers;
    }

    public static function getBearerToken(): ?string {
        $headers = self::getAuthorizationHeader();
        
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    public static function authenticate(): ?int {
        $token = self::getBearerToken();
        return self::validateToken($token);
    }
}
