<?php

namespace App\Models;


class User extends Model
{
    protected string $table = 'users';
    
    protected array $fillable = [
        'name',
        'email',
        'password',
        'created_at'
    ];

    protected string $primaryKey = 'id';

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', '=', $email)->first();
    }
}
