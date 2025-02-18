<?php

namespace App\Controllers;

class PageController extends BaseController
{
    public function home(): void
    {
        $this->render('home');
    }

    public function dashboard(): void
    {
        // Check authentication
        $token = $_COOKIE['token'] ?? null;
        if (!$token) {
            header('Location: /');
            exit;
        }
        
        $this->render('dashboard');
    }

    protected function render(string $view, array $data = []): void
    {
        // Clean any previous output
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Extract data to make it available in the view
        extract($data);

        // Include the view file
        require dirname(__DIR__, 2) . "/views/{$view}.php";
    }
}
