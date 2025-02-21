<?php

namespace App\Http\Requests;

use App\Http\FormRequest;

class ToggleFavoriteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'article_id' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'article_id.required' => 'Article ID is required',
        ];
    }
}
