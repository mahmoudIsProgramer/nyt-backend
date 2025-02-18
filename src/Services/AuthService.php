<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\Models\User;
use App\Utils\Auth;

class AuthService
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Register a new user
     *
     * @param UserDTO $userDTO
     * @throws \InvalidArgumentException If email already exists
     * @throws \RuntimeException If user creation fails
     * @return array
     */
    public function register(UserDTO $userDTO): array
    {
        // Check if email exists
        if ($this->userModel->findByEmail($userDTO->email)) {
            throw new \InvalidArgumentException('Email already exists');
        }

        // Create user
        $userId = $this->userModel->create($userDTO->toArray());
        if (!$userId) {
            throw new \RuntimeException('Failed to create user');
        }

        // Generate token
        $token = Auth::generateToken($userId);

        return [
            'user_id' => $userId,
            'token' => $token
        ];
    }

    /**
     * Authenticate user and generate token
     *
     * @param string $email
     * @param string $password
     * @throws \InvalidArgumentException If credentials are invalid
     * @return array
     */
    public function authenticate(string $email, string $password): array
    {
        $user = $this->userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            throw new \InvalidArgumentException('Invalid credentials');
        }

        $token = Auth::generateToken($user['id']);

        return [
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ],
            'token' => $token
        ];
    }
}
