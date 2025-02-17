<?php

namespace App\Controllers;

use App\Services\NYTService;

class ArticlesController extends BaseController {
    private NYTService $nytService;

    public function __construct() {
        $this->nytService = new NYTService();
    }

    /**
     * Search articles endpoint
     * GET /api/articles/search?q=query&page=1
     */
    public function search(): void {
        $query = $_GET['q'] ?? '';
        $page = (int)($_GET['page'] ?? 1);

        if (empty($query)) {
            $this->errorResponse('Search query is required');
            return;
        }

        if ($page < 1) {
            $this->errorResponse('Page number must be greater than 0');
            return;
        }

        $result = $this->nytService->searchArticles($query, $page);
        $this->jsonResponse($result);
    }

    /**
     * Get article details endpoint
     * GET /api/articles/{url}
     */
    public function getArticle(string $articleUrl): void {
        if (empty($articleUrl)) {
            $this->errorResponse('Article URL is required');
            return;
        }

        $result = $this->nytService->getArticle($articleUrl);
        
        if ($result === null) {
            $this->errorResponse('Article not found', 404);
            return;
        }

        $this->jsonResponse($result);
    }
}
