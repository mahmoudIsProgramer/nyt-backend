<?php

namespace App\Controllers;

use App\Core\Response;

class FavoriteController extends BaseController {
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

    /**
     * Toggle article favorite status (Protected endpoint)
     */
    public function toggleFavorites(): void {
        $userId = $_REQUEST['user_id']; // Set by AuthMiddleware
        $articleId = $_POST['article_id'] ?? null;
        
        if (!$articleId) {
            $this->errorResponse('Article ID is required', 400);
        }

        try {
            // Check if article is already favorited
            $isFavorited = $this->checkIfFavorited($userId, $articleId);
            
            if ($isFavorited) {
                // Remove from favorites
                $this->removeFavorite($userId, $articleId);
                $message = 'Article removed from favorites';
                $status = false;
            } else {
                // Add to favorites
                $this->addFavorite($userId, $articleId);
                $message = 'Article added to favorites';
                $status = true;
            }

            $this->jsonResponse([
                'message' => $message,
                'article_id' => $articleId,
                'user_id' => $userId,
                'is_favorited' => $status
            ]);

        } catch (\Exception $e) {
            $this->errorResponse('Error updating favorites: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Check if an article is favorited by user
     */
    private function checkIfFavorited(int $userId, string $articleId): bool {
        // TODO: Implement database check
        return false;
    }

    /**
     * Add article to favorites
     */
    private function addFavorite(int $userId, string $articleId): void {
        // TODO: Implement database insert
    }

    /**
     * Remove article from favorites
     */
    private function removeFavorite(int $userId, string $articleId): void {
        // TODO: Implement database delete
    }
}
