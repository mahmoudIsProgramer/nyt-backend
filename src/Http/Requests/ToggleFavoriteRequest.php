<?php

namespace App\Http\Requests;

use App\Http\FormRequest;

class ToggleFavoriteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'article_id' => 'required',
            'web_url' => 'required',
            'headline' => 'required',
            'snippet' => 'nullable',
            'pub_date' => 'nullable',
            'source' => 'nullable',
            'image_url' => 'nullable',
            'author' => 'nullable'
        ];
    }

    public function messages(): array
    {
        return [
            'article_id.required' => 'Article ID is required',
            'web_url.required' => 'Web URL is required',
            'headline.required' => 'Headline is required'
        ];
    }
}
