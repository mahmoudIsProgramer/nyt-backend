<?php

namespace App\Services;

use App\Models\User;
use App\DTOs\UserDTO;
use App\Utils\Helper;
use App\Utils\JWTHelper;
use Firebase\JWT\JWT;
use App\Http\Resources\UserResource;

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
            'created_at' => $userDTO->created_at
        ]);
        // Helper::dd($user->toArray());
        if (!$user) {
            throw new \RuntimeException('Failed to create user');
        }

        // Convert user model to array and remove sensitive data
        $userData = $user->toArray();

        unset($userData['password']);
        // Helper::dd($userData);

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

    /**
     * Get user by ID
     *
     * @param int $userId
     * @return array|null
     */
    public function getUserById(int $userId): ?array
    {
        try {
            $user = $this->userModel->find($userId);

            if (!$user) {
                return null;
            }

            $userData = $user->toArray();
            unset($userData['password']); // Remove sensitive data
            // Helper::dd($userData);
            // Helper::dd($userData['password']);

            $userDTO = UserDTO::fromArray($userData);
            return (new UserResource($userDTO))->toArray();
        } catch (\Exception $e) {
            error_log("Error fetching user: " . $e->getMessage());
            return null;
        }
    }

    private function generateToken(int $userId): string
    {
        return JWTHelper::generateToken(
            userId: $userId,
            secret: $this->jwtSecret,
            expiry: $this->jwtExpiry
        );
    }

    /**
     * Logout user
     *
     * @param int $userId
     * @return bool
     */
    public function logout(int $userId): bool
    {
        try {
            $token = $_REQUEST['token'] ?? null;
            
            if (!$token) {
                return false;
            }
    
            // Force token expiration
            JWTHelper::expireToken($token, $this->jwtSecret);
            return true;
            
        } catch (\Exception $e) {
            error_log("Error during logout: " . $e->getMessage());
            return false;
        }
    }
}
