<?php

namespace App\Controllers;

use App\DTOs\UserDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;

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
            
            $result = $this->authService->register($userDTO);
            
            $this->authResponse(
                token: $result['token'],
                message: 'Registration successful'
            );
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->error('Server error', 500);
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
}
