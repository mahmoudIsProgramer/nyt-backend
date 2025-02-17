<?php

namespace App\Services\Interfaces;

use App\Services\ValueObjects\ApiResponse;

interface ArticleServiceInterface {
    /**
     * Search for articles
     */
    public function searchArticles(string $query, int $page = 1): ApiResponse;

    /**
     * Get article by URL
     */
    public function getArticle(string $articleUrl): ApiResponse;
}
