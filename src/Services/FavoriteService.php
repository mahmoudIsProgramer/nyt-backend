<?php

namespace App\Services;

use Exception;
use App\Models\Favorite;
use App\DTOs\FavoriteDTO;
use App\Utils\Helper;

class FavoriteService
{
    private Favorite $favorite;

    public function __construct(Favorite $favorite = null)
    {
        $this->favorite = $favorite ?? new Favorite();
    }

    /**
     * Toggle favorite status for an article
     *
     * @throws Exception
     */
    public function toggleFavorite(int $userId, string $articleId, array $articleData): array
    {
        try {
            return $this->checkIfFavored($userId, $articleId)
                ? $this->handleUnfavorite($userId, $articleId)
                : $this->handleFavorite($userId, $articleId, $articleData);
        } catch (\Exception $e) {
            throw new Exception('Failed to toggle favorite: ' . $e->getMessage());
        }
    }

    /**
     * Get user's favorite articles
     *
     * @throws Exception
     */
    public function getUserFavorites(int $userId): array
    {
        try {
            $favorites = $this->favorite->getAllByUserId($userId);

            // Helper::dd($favorites);
            
            return [
                'favorites' => $this->mapFavoritesToDTOs($favorites),
                'count' => count($favorites)
            ];
        } catch (\Exception $e) {
            throw new Exception('Failed to get user favorites: ' . $e->getMessage());
        }
    }

    /**
     * Map favorites to DTOs
     */
    private function mapFavoritesToDTOs(array $favorites): array
    {
        return array_map(
            fn($favorite) => FavoriteDTO::fromArray($favorite),
            $favorites
        );
    }

    /**
     * Handle favoriting an article
     */
    private function handleFavorite(int $userId, string $articleId, array $articleData): array
    {
        $this->addFavorite($userId, $articleId, $articleData);
        return [
            'status' => true,
            'article_id' => $articleId,
            'user_id' => $userId,
            'message' => 'Article added to favorites'
        ];
    }

    /**
     * Handle unfavoriting an article
     */
    private function handleUnfavorite(int $userId, string $articleId): array
    {
        $this->removeFavorite($userId, $articleId);
        return [
            'status' => false,
            'article_id' => $articleId,
            'user_id' => $userId,
            'message' => 'Article removed from favorites'
        ];
    }

    /**
     * Check if article is favorited by user
     */
    private function checkIfFavored(int $userId, string $articleId): bool
    {
        return $this->favorite->isFavored($userId, $articleId);
    }

    private function addFavorite(int $userId, string $articleId, array $articleData): void
    {
        $result = Favorite::create([
            'user_id' => $userId,
            'article_id' => $articleId,
            'web_url' => $articleData['web_url'],
            'headline' => $articleData['headline'],
            'snippet' => $articleData['snippet'] ?? null,
            'pub_date' => $articleData['pub_date'] ?? null,
            'source' => $articleData['source'] ?? null,
            'image_url' => $articleData['image_url'] ?? null,
            'author' => $articleData['author'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        if (!$result) {
            throw new \RuntimeException('Failed to add favorite');
        }
    }

    private function removeFavorite(int $userId, string $articleId): void
    {
        $result = $this->favorite->unfavorite($userId, $articleId);
        
    }
}