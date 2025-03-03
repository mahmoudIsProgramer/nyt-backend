<?php

namespace App\Controllers;

use App\Services\NYTService;
use App\Services\DTOs\SearchRequestDTO;
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
            $request = SearchRequestDTO::fromRequest($_GET);
            
            [$articles, $pagination] = $this->nytService->searchArticles($request);
            
            $resource = new ArticleCollectionResource($articles, $pagination);
            $this->success($resource->toArray());

        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage(), 400);
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
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
                $this->notFound('Article not found');
                return;
            }

            $resource = new ArticleResource($article);
            $this->success($resource->toArray());

        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
