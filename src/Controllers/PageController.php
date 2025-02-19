<?php

namespace App\Controllers;

use App\Core\View;

class PageController extends BaseController
{
    public function home(): void
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

    public function articles(): void
    {
        $query = $_GET['q'] ?? '';
        
        echo View::render('articles', [
            'title' => 'Search Articles - NYT',
            'styles' => ['/css/app.css', '/css/articles.css'],
            'scripts' => ['/js/articles.js'],
            'query' => $query
        ]);
    }
}
