<?php

namespace App\DTOs;

class UserDTO
{
    public function __construct(
        // public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $created_at = null

    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            // id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            password: password_hash($data['password'], PASSWORD_DEFAULT),
            created_at: $data['created_at'] ?? date('Y-m-d H:i:s')
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            // id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            password: $data['password']??'',
            created_at: $data['created_at'] ?? date('Y-m-d H:i:s')
        );
    }
    

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,  
            'email' => $this->email,
            'password' => $this->password,
            'created_at' => $this->created_at
        ];
    }
}
