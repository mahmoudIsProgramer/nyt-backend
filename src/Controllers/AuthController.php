<?php

namespace App\Controllers;

use App\DTOs\UserDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Utils\Auth;

class AuthController extends BaseController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register(): void
    {
        try {
            $request = new RegisterRequest();

            // Check if email exists
            if ($this->userModel->findByEmail($request->get('email'))) {
                $this->errorResponse('Email already exists', 400);
            }

            // Create user
            $userDTO = UserDTO::fromRequest($request->all());
            $userId = $this->userModel->create($userDTO->toArray());

            if (!$userId) {
                $this->errorResponse('Failed to create user', 500);
            }

            // Generate token and respond
            $token = Auth::generateToken($userId);
            $this->jsonResponse([
                'message' => 'Registration successful',
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->errorResponse('Server error', 500);
        }
    }

    public function login(): void
    {
        try {
            $request = new LoginRequest();

            // Find and verify user
            $user = $this->userModel->findByEmail($request->get('email'));
            if (!$user || !password_verify($request->get('password'), $user['password'])) {
                $this->errorResponse('Invalid credentials', 401);
            }

            // Generate token and respond
            $token = Auth::generateToken($user['id']);
            $this->jsonResponse([
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ]);
        } catch (\Exception $e) {
            $this->errorResponse('Server error', 500);
        }
    }
}
