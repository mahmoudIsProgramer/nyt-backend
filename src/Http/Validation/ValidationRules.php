<?php

namespace App\Http\Validation;

use App\Models\ModelFactory;

class ValidationRules
{
    public static function unique(string $value, string $field, string $table): bool
    {
        try {
            $model = ModelFactory::make($table);
            return !$model->exists($field, $value);
        } catch (\InvalidArgumentException $e) {
            // If no model exists for the table, assume the value is unique
            return true;
        }
    }

    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required($value): bool
    {
        return !empty($value);
    }

    public static function min(string $value, int $min): bool
    {
        return strlen($value) >= $min;
    }

    public static function max(string $value, int $max): bool
    {
        return strlen($value) <= $max;
    }
}
