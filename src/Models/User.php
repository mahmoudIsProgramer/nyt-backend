<?php

namespace App\Models;

use App\Core\Database;
use SQLite3;

class User extends Model
{
    protected string $table = 'users';
    
    protected array $fillable = [
        'name',
        'email',
        'password',
        'created_at'
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    // Remove any create() method override if it exists
    // The parent static create() method will be used
}
