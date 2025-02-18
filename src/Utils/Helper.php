<?php

namespace App\Utils;

class Helper {
    /**
     * Debug and die function
     * 
     * @param mixed $data Data to dump
     * @return void
     */
    public static function dd($data): void {
        header('Content-Type: application/json');
        echo json_encode([
            'debug_data' => $data,
            'debug_type' => gettype($data),
            'debug_backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
        ], JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Debug without dying
     * 
     * @param mixed $data Data to dump
     * @return void
     */
    public static function dump($data): void {
        if (getenv('APP_ENV') !== 'development') {
            return;
        }
        
        error_log(json_encode([
            'debug_data' => $data,
            'debug_type' => gettype($data)
        ], JSON_PRETTY_PRINT));
    }
}
