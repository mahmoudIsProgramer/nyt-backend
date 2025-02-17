<?php

namespace App\Services\DTOs;

class ApiResponseDTO {
    public function __construct(
        private string $status,
        private mixed $data = null,
        private ?string $message = null,
        private ?int $code = null
    ) {}

    public static function success(mixed $data): self {
        return new self('success', $data);
    }

    public static function error(string $message, int $code = 500): self {
        return new self('error', null, $message, $code);
    }

    public function toArray(): array {
        $response = ['status' => $this->status];
        
        if ($this->data !== null) {
            $response['data'] = $this->data;
        }
        
        if ($this->message !== null) {
            $response['message'] = $this->message;
        }
        
        if ($this->code !== null) {
            $response['code'] = $this->code;
        }
        
        return $response;
    }
}
