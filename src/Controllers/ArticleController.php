<?php

namespace App\Controllers;

use App\Core\Response;

class ArticleController extends BaseController {
    /**
     * Add an article to favorites (Protected endpoint example)
     */
    public function addToFavorites(): void {
        $userId = $_REQUEST['user_id']; // Set by AuthMiddleware
        $articleId = $_POST['article_id'] ?? null;
        
        if (!$articleId) {
            $this->errorResponse('Article ID is required', 400);
        }
        
        // TODO: Add your logic to save the article to favorites
        
        $this->jsonResponse([
            'message' => 'Article added to favorites successfully',
            'article_id' => $articleId,
            'user_id' => $userId
        ]);
    }
}
