<?php

namespace App\Http\Requests;

use App\Http\FormRequest;

class ToggleFavoriteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'article_id' => ['required'],
            'user_id' => ['required', 'exists:users,id']
        ];
    }

    public function messages(): array
    {
        return [
            'article_id.required' => 'Article ID is required',
            'user_id.required' => 'User ID is required',
            'user_id.exists' => 'User not found'
        ];
    }
}
