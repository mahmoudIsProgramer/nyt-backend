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
            'exp' => time() + $expiry
        ];

        return JWT::encode($payload, $secret, 'HS256');
    }
}