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

    public function log(
        string $message, 
        string $level = 'INFO',
        array $context = [],
        ?string $filename = null
    ): void {
        $logFile = $filename ?? $this->defaultLogFile;
        $timestamp = date('Y-m-d H:i:s');
        
        $formattedMessage = sprintf(
            "[%s] %s: %s\nContext: %s\n%s",
            $timestamp,
            strtoupper($level),
            $message,
            json_encode($context, JSON_PRETTY_PRINT),
            str_repeat('-', 80) . "\n"
        );

        file_put_contents(
            "{$this->logPath}/{$logFile}",
            $formattedMessage,
            FILE_APPEND
        );
    }

    public function logRequest(array $request): void
    {
        $this->log(
            message: 'API Request',
            level: 'INFO',
            context: $request
        );
    }

    public function logResponse(array $response): void
    {
        $this->log(
            message: 'API Response',
            level: 'INFO',
            context: $response
        );
    }
}