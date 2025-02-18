<?php

namespace App\Http\Validation;

use App\Models\User;

class ValidationRules
{
    private static ?User $userModel = null;

    private static function getUserModel(): User
    {
        if (self::$userModel === null) {
            self::$userModel = new User();
        }
        return self::$userModel;
    }

    public static function unique(string $value, string $field, string $table): bool
    {
        if ($table === 'users' && $field === 'email') {
            return !self::getUserModel()->findByEmail($value);
        }
        
        // Add more table checks as needed
        return true;
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
