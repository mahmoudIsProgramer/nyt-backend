<?php

namespace App\Http\Requests;

use App\Http\FormRequest;
use App\Utils\Helper;

class ToggleFavoriteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'article_id' => ['required'],
            // 'user_id' => ['required','exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'article_id.required' => 'Article ID is required',
            'user_id.exists' => 'User ID does not exist'
        ];
    }

    public function all(): array
    {
        $data = parent::all();
        // $data['user_id'] = $_REQUEST['user_id'] ?? null;
        // Helper::dd($data);
        
        // if (!$data['user_id']) {
        //     throw new \InvalidArgumentException('Unauthorized');
        // }
        
        return $data;
    }


    public function authorize(): bool
    {
        return true;
    }
}
