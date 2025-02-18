<?php

namespace App\Middleware;

use App\Utils\Auth;
use App\Traits\ResponseTrait;

class AuthMiddleware {
    use ResponseTrait;

    public static function authenticate(callable $next): callable {
        return function () use ($next) {
            $userId = Auth::authenticate();
            
            if ($userId === null) {
                (new self())->errorResponse('Unauthorized access. Please provide a valid authentication token', 401);
            }
            
            // Add user ID to request for downstream use
            $_REQUEST['user_id'] = $userId;
            
            return $next();
        };
    }
}
