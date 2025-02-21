<?php

namespace App\Controllers;

use App\DTOs\UserDTO;
use App\Utils\Helper;
use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register(): void
    {
        try {
            $request = new RegisterRequest();
            $userDTO = UserDTO::fromRequest($request->all());
            // Helper::dd($userDTO);
            
            $result = $this->authService->register($userDTO);
            
            $this->authResponse(
                token: $result['token'],
                message: 'Registration successful'
            );
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    public function login(): void
    {
        try {
            $request = new LoginRequest();
            
            $result = $this->authService->authenticate(
                email: $request->get('email'),
                password: $request->get('password')
            );
            
            $this->authResponse(
                token: $result['token'],
                userData: $result['user'],
                message: 'Login successful'
            );
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage(), 401);
        } catch (\Exception $e) {
            $this->error('Server error', 500);
        }
    }

    public function getUser(): void
    {
        try {
            $userId = $_REQUEST['user_id'] ?? null;
            
            if (!$userId) {
                $this->unauthorized('User not authenticated');
                return;
            }

            $user = $this->authService->getUserById($userId);
            
            if (!$user) {
                $this->notFound('User not found');
                return;
            }

            $this->success([
                'user' => $user
            ], 'User retrieved successfully');
            
        } catch (\Exception $e) {
            $this->error('Error fetching user details', 500);
        }
    }
}
