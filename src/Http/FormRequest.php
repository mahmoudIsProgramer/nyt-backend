<?php

namespace App\Http;

abstract class FormRequest
{
    protected array $data = [];
    protected array $errors = [];
    protected array $validationRules = [];

    public function __construct()
    {
        $this->parseInput();
        if (!$this->authorize()) {
            $this->errorResponse('Unauthorized', 403);
        }
        if (!$this->validate()) {
            $this->errorResponse($this->errors[0] ?? 'Validation failed', 422);
        }
    }

    protected function parseInput(): void
    {
        if ($this->method() === 'GET') {
            $this->data = $_GET;
            return;
        }

        $rawInput = file_get_contents('php://input');
        if (empty($rawInput)) {
            $this->data = [];
            return;
        }

        $data = json_decode($rawInput, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
        }

        $this->data = $data;
    }

    public function validate(): bool
    {
        $this->errors = [];
        $rules = $this->rules();

        foreach ($rules as $field => $ruleSet) {
            $ruleArray = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;
            
            foreach ($ruleArray as $rule) {
                $this->validateField($field, $rule);
            }
        }

        return empty($this->errors);
    }

    protected function validateField(string $field, string $rule): void
    {
        $value = $this->get($field);
        
        if (strpos($rule, ':') !== false) {
            [$rule, $parameter] = explode(':', $rule, 2);
        }

        switch ($rule) {
            case 'required':
                if (!isset($this->data[$field]) || $this->data[$field] === '') {
                    $this->errors[] = ucfirst($field) . ' is required';
                }
                break;

            case 'email':
                if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[] = ucfirst($field) . ' must be a valid email address';
                }
                break;

            case 'min':
                if ($value !== null && strlen($value) < (int)$parameter) {
                    $this->errors[] = ucfirst($field) . " must be at least $parameter characters";
                }
                break;

            case 'max':
                if ($value !== null && strlen($value) > (int)$parameter) {
                    $this->errors[] = ucfirst($field) . " must not exceed $parameter characters";
                }
                break;
                
            // Add more validation rules as needed
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    abstract public function rules(): array;

    public function all(): array
    {
        return $this->data;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->data, array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->data, array_flip($keys));
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    protected function errorResponse(string $message, int $code): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'code' => $code
        ]);
        exit;
    }
}
