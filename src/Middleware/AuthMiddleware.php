<?php

namespace App\Middleware;

use App\Utils\Auth;
use App\Core\Response;

class AuthMiddleware {
    public static function authenticate(callable $next): callable {
        return function () use ($next) {
            $userId = Auth::authenticate();
            
            if ($userId === null) {
                Response::json([
                    'error' => 'Unauthorized access',
                    'message' => 'Please provide a valid authentication token'
                ], 401);
                exit;
            }
            
            // Add user ID to request for downstream use
            $_REQUEST['user_id'] = $userId;
            
            return $next();
        };
    }
}
