<?php

namespace App\Utils;

class Logger 
{
    private string $logPath;
    private string $defaultLogFile;

    public function __construct(string $logPath = null) 
    {
        $this->logPath = $logPath ?? __DIR__ . '/../../logs';
        $this->defaultLogFile = 'api.log';
        $this->ensureLogDirectoryExists();
    }

    private function ensureLogDirectoryExists(): void
    {
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
    }

    public function logRequest(array $request): void
    {
        $message = sprintf(
            "\n=== REQUEST ===\nTimestamp: %s\nMethod: %s\nURL: %s\nHeaders: %s\nBody: %s\n",
            date('Y-m-d H:i:s'),
            $request['method'] ?? 'UNKNOWN',
            $request['url'] ?? 'UNKNOWN',
            json_encode($request['headers'] ?? [], JSON_PRETTY_PRINT),
            json_encode($request['body'] ?? [], JSON_PRETTY_PRINT)
        );

        $this->log($message, 'api.log');
    }

    public function logResponse(array $response): void
    {
        $responseBody = $response['body'] ?? [];
        
        // Handle both string and array responses
        if (is_string($responseBody)) {
            $decodedBody = json_decode($responseBody, true) ?? $responseBody;
        } else {
            $decodedBody = $responseBody;
        }

        $message = sprintf(
            "=== RESPONSE ===\nTimestamp: %s\nStatus: %d\nHeaders: %s\nBody: %s\n=== END ===\n",
            date('Y-m-d H:i:s'),
            $response['status'] ?? 0,
            json_encode($response['headers'] ?? [], JSON_PRETTY_PRINT),
            json_encode($decodedBody, JSON_PRETTY_PRINT)
        );

        $this->log($message, 'api.log');
    }

    private function log(string $message, string $filename): void
    {
        $logPath = "{$this->logPath}/{$filename}";
        
        // Ensure message ends with newline
        if (substr($message, -1) !== "\n") {
            $message .= "\n";
        }

        file_put_contents($logPath, $message, FILE_APPEND);
    }
}