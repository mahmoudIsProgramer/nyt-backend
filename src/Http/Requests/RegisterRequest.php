<?php

namespace App\Http\Requests;

use App\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'name' => ['required', 'min:2', 'max:50']
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'This email address is already registered',
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'name.required' => 'Name is required',
            'name.min' => 'Name must be at least 2 characters',
            'name.max' => 'Name cannot exceed 50 characters'
        ];
    }
}
