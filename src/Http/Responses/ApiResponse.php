<?php

namespace App\Http\Responses;

class ApiResponse
{
    public static function success(array $data, string $message = '', int $code = 200): array
    {
        return [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'code' => $code
        ];
    }

    public static function error(string $message, int $code = 400): array
    {
        return [
            'status' => 'error',
            'message' => $message,
            'code' => $code
        ];
    }

    public static function auth(string $token, array $userData = [], string $message = ''): array
    {
        return self::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $userData
        ], $message);
    }
}
