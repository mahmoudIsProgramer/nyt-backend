<?php

namespace App\Services;

use App\Models\User;
use App\DTOs\UserDTO;
use App\Utils\Helper;
use Firebase\JWT\JWT;

class AuthService
{
    private User $userModel;
    private string $jwtSecret;
    private int $jwtExpiry;

    public function __construct()
    {
        $this->userModel = new User();
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'your-secret-key';
        $this->jwtExpiry = (int)($_ENV['JWT_EXPIRY'] ?? 3600);
    }

    /**
     * Register a new user
     *
     * @param UserDTO $userDTO
     * @throws \RuntimeException If user creation fails
     * @return array
     */
    public function register(UserDTO $userDTO): array
    {

        $user = $this->userModel->create([
            'name' => $userDTO->name,
            'email' => $userDTO->email,
            'password' => $userDTO->password,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        // Helper::dd($user->toArray());
        if (!$user) {
            throw new \RuntimeException('Failed to create user');
        }

        // Convert user model to array and remove sensitive data
        $userData = $user->toArray();
        // Helper::dd($userData);

        unset($userData['password']);

        return [
            'user' => $userData,
            'token' => $this->generateToken($user->id)
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
        // Helper::dd($user); 
        if (!$user || !password_verify($password, $user['password'])) {
            throw new \InvalidArgumentException('Invalid credentials');
        }

        unset($user['password']);
        return [
            'user' => $user,
            'token' => $this->generateToken($user['id'])
        ];
    }

    private function generateToken(int $userId): string
    {
        $payload = [
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // 24 hours
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }
}
