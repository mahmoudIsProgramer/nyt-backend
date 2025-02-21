<?php

namespace App\Utils;

use Firebase\JWT\JWT;

class JWTHelper
{
    /**
     * Generate a JWT token for a user
     *
     * @param int $userId User ID to encode in token
     * @param string $secret Secret key for signing
     * @param int $expiry Expiration time in seconds (default 24 hours)
     * @return string
     */
    public static function generateToken(int $userId, string $secret, int $expiry = 86400): string
    {
        $payload = [
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + $expiry,
            'jti' => bin2hex(random_bytes(16)) // Unique token ID
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }

    /**
     * Force expire a token by setting its expiration to now
     */
    public static function expireToken(string $token, string $secret): string
    {
        $decoded = JWT::decode($token, new \Firebase\JWT\Key($secret, 'HS256'));
        
        $payload = [
            'sub' => $decoded->sub,
            'iat' => $decoded->iat,
            'exp' => time(), // Set expiration to current time
            'jti' => $decoded->jti
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }
}