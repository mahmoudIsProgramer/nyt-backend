<?php

namespace App\Services;

use App\Models\Favorite;
use App\Core\Database;

class FavoriteService
{
    private Favorite $favorite;

    public function __construct(Database $db)
    {
        $this->favorite = new Favorite($db);
    }

    /**
     * Toggle favorite status for an article
     *
     * @param int $userId
     * @param string $articleId
     * @return array
     * @throws \Exception
     */
    public function toggleFavorite(int $userId, string $articleId): array
    {
        $isFavorited = $this->checkIfFavorited($userId, $articleId);
        
        if ($isFavorited) {
            $this->removeFavorite($userId, $articleId);
            return [
                'status' => false,
                'message' => 'Article removed from favorites'
            ];
        }

        $this->addFavorite($userId, $articleId);
        return [
            'status' => true,
            'message' => 'Article added to favorites'
        ];
    }

    /**
     * Get user's favorite articles
     *
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public function getUserFavorites(int $userId): array
    {
        $favorites = $this->favorite->getAllByUserId($userId);
        
        return [
            'favorites' => $favorites,
            'count' => count($favorites)
        ];
    }

    /**
     * Check if article is favorited by user
     *
     * @param int $userId
     * @param string $articleId
     * @return bool
     */
    private function checkIfFavorited(int $userId, string $articleId): bool
    {
        return $this->favorite->isFavorited($userId, $articleId);
    }

    /**
     * Add article to favorites
     *
     * @param int $userId
     * @param string $articleId
     * @throws \Exception
     */
    private function addFavorite(int $userId, string $articleId): void
    {
        $result = $this->favorite->create([
            'user_id' => $userId,
            'article_id' => $articleId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        if (!$result) {
            throw new \Exception('Failed to add article to favorites');
        }
    }

    /**
     * Remove article from favorites
     *
     * @param int $userId
     * @param string $articleId
     * @throws \Exception
     */
    private function removeFavorite(int $userId, string $articleId): void
    {
        $result = $this->favorite->unfavorite($userId, $articleId);
        
        if (!$result) {
            throw new \Exception('Failed to remove article from favorites');
        }
    }
}