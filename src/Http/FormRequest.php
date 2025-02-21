<?php

namespace App\Http;

use App\Http\Validation\ValidationRules;

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
        $messages = method_exists($this, 'messages') ? $this->messages() : [];

        foreach ($rules as $field => $ruleSet) {
            $ruleArray = is_string($ruleSet) ? explode('|', $ruleSet) : $ruleSet;
            
            foreach ($ruleArray as $rule) {
                if (!$this->validateField($field, $rule)) {
                    $messageKey = "{$field}.{$rule}";
                    $this->errors[] = $messages[$messageKey] ?? $this->getDefaultMessage($field, $rule);
                }
            }
        }

        return empty($this->errors);
    }

    protected function validateField(string $field, string $rule): bool
    {
        $value = $this->get($field);
        
        // If the rule is 'required', check it first
        if ($rule === 'required') {
            return ValidationRules::required($value);
        }
        
        // Skip other validations if value is null/empty and field is not required
        if ($value === null || $value === '') {
            return !in_array('required', explode('|', $this->rules()[$field]));
        }
        
        // Handle rules with parameters
        if (strpos($rule, ':') !== false) {
            [$ruleName, $parameter] = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $parameter = null;
        }

        switch ($ruleName) {
            case 'required':
                return ValidationRules::required($value);

            case 'email':
                return ValidationRules::email((string)$value);

            case 'min':
                return ValidationRules::min($value, (int)$parameter);

            case 'max':
                return ValidationRules::max($value, (int)$parameter);

            case 'unique':
                [$table, $field] = explode(',', $parameter);
                return ValidationRules::unique($value, $field, $table);
        }

        return true;
    }

    protected function getDefaultMessage(string $field, string $rule): string
    {
        $field = ucfirst(str_replace('_', ' ', $field));
        
        if (strpos($rule, ':') !== false) {
            [$rule, $parameter] = explode(':', $rule, 2);
        }

        switch ($rule) {
            case 'required':
                return "$field is required";
            case 'email':
                return "$field must be a valid email address";
            case 'min':
                return "$field must be at least $parameter characters";
            case 'max':
                return "$field must not exceed $parameter characters";
            case 'unique':
                return "$field already exists";
            default:
                return "$field is invalid";
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
