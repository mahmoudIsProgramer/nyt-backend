<?php

namespace App\Controllers;

use App\Utils\Logger;
use App\Traits\ResponseTrait;

class LogController extends BaseController
{
    use ResponseTrait;
    
    private Logger $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function store(): void
    {
        try {
            $logData = json_decode(file_get_contents('php://input'), true);
            
            if (!$logData) {
                $this->error('Invalid log data', 400);
                return;
            }

            $this->logger->log(
                message: $logData['message'] ?? 'Web Event',
                level: $logData['level'] ?? 'INFO',
                context: [
                    'event' => $logData['event'] ?? [],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
                ],
                filename: 'web.log'
            );

            $this->success([], 'Log stored successfully');
        } catch (\Exception $e) {
            $this->error('Failed to store log', 500);
        }
    }
}