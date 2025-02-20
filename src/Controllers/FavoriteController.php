<?php

namespace App\Controllers;

use App\Core\Response;
use App\Http\Requests\ToggleFavoriteRequest;
use App\Models\Favorite;
use App\Models\ModelFactory;

class FavoriteController extends BaseController 
{
    private $favorite;

    public function __construct() 
    {
        // parent::__construct();
        $this->favorite = ModelFactory::make('favorites');
    }

    /**
     * Toggle article favorite status (Protected endpoint)
     */
    public function toggleFavorites(): void 
    {
        try {
            $request = new ToggleFavoriteRequest();
            $validated = $request->all();
            
            $userId = $validated['user_id'];
            $articleId = $validated['article_id'];
            
            $isFavorited = $this->checkIfFavorited($userId, $articleId);
            
            if ($isFavorited) {
                $this->removeFavorite($userId, $articleId);
                $message = 'Article removed from favorites';
                $status = false;
            } else {
                $this->addFavorite($userId, $articleId);
                $message = 'Article added to favorites';
                $status = true;
            }

            $this->success([
                'article_id' => $articleId,
                'user_id' => $userId,
                'is_favorited' => $status
            ], $message);

        } catch (\InvalidArgumentException $e) {
            $this->validationError($e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error updating favorites', 500);
        }
    }

    /**
     * Check if an article is favorited by user
     */
    private function checkIfFavorited(int $userId, string $articleId): bool 
    {
        return $this->favorite->isFavorited($userId, $articleId);
    }

    /**
     * Add article to favorites
     */
    private function addFavorite(int $userId, string $articleId): void 
    {
        $favoriteData = [
            'user_id' => $userId,
            'article_id' => $articleId
        ];

        $result = $this->favorite->create($favoriteData);
        
        if (!$result) {
            throw new \Exception('Failed to add article to favorites');
        }
    }

    /**
     * Remove article from favorites
     */
    private function removeFavorite(int $userId, string $articleId): void 
    {
        $result = $this->favorite->unfavorite($userId, $articleId);
        
        if (!$result) {
            throw new \Exception('Failed to remove article from favorites');
        }
    }

    /**
     * Get user's favorite articles
     */
    public function getFavorites(): void 
    {
        try {
            $userId = $_REQUEST['user_id']; // Set by AuthMiddleware
            $favorites = $this->favorite->getAllByUserId($userId);
            
            $this->jsonResponse([
                'favorites' => $favorites,
                'count' => count($favorites)
            ]);
        } catch (\Exception $e) {
            $this->errorResponse('Error fetching favorites: ' . $e->getMessage(), 500);
        }
    }
}
