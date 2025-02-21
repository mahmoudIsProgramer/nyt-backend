<?php

namespace App\Http\Resources;

use App\DTOs\UserDTO;

class UserResource extends Resource 
{
    public function __construct(UserDTO $user) 
    {
        parent::__construct($user);
    }

    public function toArray(): array 
    {
        /** @var UserDTO $user */
        $user = $this->resource;
        
        return [
            // 'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            // 'links' => [
            //     'profile' => "/users/{$user->id}",
            //     'favorites' => "/users/{$user->id}/favorites"
            // ]
        ];
    }
}