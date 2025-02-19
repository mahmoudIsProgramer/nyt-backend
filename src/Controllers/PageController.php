<?php

namespace App\Controllers;

use App\Core\View;

class PageController
{
    public function home()
    {
        echo View::render('home', [
            'title' => 'Authentication - NYT',
            'styles' => ['/css/app.css'],
            'scripts' => ['/js/auth.js']
        ]);
    }

    public function dashboard()
    {
        // Check authentication
        $token = $_COOKIE['token'] ?? null;
        if (!$token) {
            header('Location: /');
            exit;
        }
        
        echo View::render('dashboard', [
            'title' => 'Dashboard - NYT'
        ]);
    }
}
