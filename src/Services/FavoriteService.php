<?php

namespace App\Services;

use App\Models\Favorite;
use App\Core\Database;
use App\DTOs\FavoriteDTO;

class FavoriteService
{
    private Favorite $favorite;

    public function __construct()
    {
        $this->favorite = new Favorite();
    }

    /**
     * Toggle favorite status for an article
     *
     * @param int $userId
     * @param string $articleId
     * @return array{status: bool, message: string}
     * @throws \Exception If toggling favorite fails
     */
    public function toggleFavorite(int $userId, string $articleId): array
    {
        try {
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
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to toggle favorite: ' . $e->getMessage());
        }
    }

    /**
     * Get user's favorite articles
     *
     * @param int $userId
     * @return array{favorites: array, count: int}
     * @throws \RuntimeException If fetching favorites fails
     */
    public function getUserFavorites(int $userId): array
    {
        try {
            $favorites = $this->favorite->getAllByUserId($userId);
            
            return [
                'favorites' => array_map(
                    fn($favorite) => FavoriteDTO::fromArray($favorite),
                    $favorites
                ),
                'count' => count($favorites)
            ];
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to get user favorites: ' . $e->getMessage());
        }
    }

    /**
     * Check if article is favorited by user
     */
    private function checkIfFavorited(int $userId, string $articleId): bool
    {
        return $this->favorite->isFavorited($userId, $articleId);
    }

    /**
     * Add article to favorites
     *
     * @throws \RuntimeException If adding favorite fails
     */
    private function addFavorite(int $userId, string $articleId): void
    {
        $result = $this->favorite->create([
            'user_id' => $userId,
            'article_id' => $articleId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        if (!$result) {
            throw new \RuntimeException('Failed to add article to favorites');
        }
    }

    /**
     * Remove article from favorites
     *
     * @throws \RuntimeException If removing favorite fails
     */
    private function removeFavorite(int $userId, string $articleId): void
    {
        $result = $this->favorite->unfavorite($userId, $articleId);
        
        if (!$result) {
            throw new \RuntimeException('Failed to remove article from favorites');
        }
    }
}