<?php

namespace App\Controllers;

use App\Services\NYTService;
use App\Http\Resources\{ArticleResource, ArticleCollectionResource};

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
        try {
            $query = $_GET['q'] ?? '';
            $page = (int)($_GET['page'] ?? 1);

            if (empty($query)) {
                $this->errorResponse('Search query is required', 400);
                return;
            }

            [$articles, $pagination] = $this->nytService->searchArticles($query, $page);
            
            $resource = new ArticleCollectionResource($articles, $pagination);
            $this->jsonResponse($resource->toArray());

        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Get article details endpoint
     * GET /api/articles/{url}
     */
    public function getArticle(string $articleUrl): void {
        try {
            $article = $this->nytService->getArticle($articleUrl);
            
            if (!$article) {
                $this->errorResponse('Article not found', 404);
                return;
            }

            $resource = new ArticleResource($article);
            $this->jsonResponse($resource->toArray());

        } catch (\Exception $e) {
            $this->errorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
