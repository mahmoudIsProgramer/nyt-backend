<?php

namespace App\Controllers;

use App\Utils\Auth;
use App\Models\User;
use App\Utils\Helper;

class AuthController extends BaseController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register(): void {
        try {
            // Check request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->errorResponse('Method not allowed', 405);
            }

            // Get and validate raw input
            $rawInput = file_get_contents('php://input');
            if (empty($rawInput)) {
                $this->errorResponse('No input data provided', 400);
            }

            // Decode JSON
            $data = json_decode($rawInput, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->errorResponse('Invalid JSON: ' . json_last_error_msg(), 400);
            }

            // Debug the received data

            // Validate input
            if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->errorResponse('Invalid email address', 400);
            }

            if (!isset($data['password']) || strlen($data['password']) < 6) {
                $this->errorResponse('Password must be at least 6 characters', 400);
            }

            if (!isset($data['name']) || empty($data['name'])) {
                $this->errorResponse('Name is required', 400);
            }

            // Check if email already exists
            if ($this->userModel->findByEmail($data['email'])) {
                $this->errorResponse('Email already exists', 400);
            }

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Create user
            $userId = $this->userModel->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $hashedPassword
            ]);

            if (!$userId) {
                $this->errorResponse('Failed to create user', 500);
            }

            // Generate token
            $token = Auth::generateToken($userId);

            $this->jsonResponse([
                'message' => 'Registration successful',
                'access_token' => $token,
                'token_type' => 'Bearer'
            ]);
        } catch (\Exception $e) {
            $this->errorResponse('Server error: ' . $e->getMessage(), 500);
        }
    }

    public function login(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (!isset($data['email']) || !isset($data['password'])) {
            $this->errorResponse('Email and password are required', 400);
        }

        // Find user by email
        $user = $this->userModel->findByEmail($data['email']);
        if (!$user) {
            $this->errorResponse('Invalid credentials', 401);
        }

        // Verify password
        if (!password_verify($data['password'], $user['password'])) {
            $this->errorResponse('Invalid credentials', 401);
        }

        // Generate token
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
    }
}
