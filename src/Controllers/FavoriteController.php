<?php

namespace App\Controllers;

use App\Http\Requests\ToggleFavoriteRequest;
use App\Services\FavoriteService;
use App\Core\Database;
use App\Traits\ResponseTrait;
use App\Utils\Helper;

class FavoriteController extends BaseController 
{
    use ResponseTrait;

    private FavoriteService $favoriteService;

    public function __construct() 
    {
        $this->favoriteService = new FavoriteService();
    }

    /**
     * Toggle article favorite status
     */
    public function toggleFavorite(): void 
    {
        // Helper::dd($_REQUEST);
        try {
            $request = new ToggleFavoriteRequest();
            $validated = $request->all();
            
            $result = $this->favoriteService->toggleFavorite(
                $validated['user_id'],
                $validated['article_id']
            );
            
            $this->success([
                'article_id' => $validated['article_id'],
                'user_id' => $validated['user_id'],
                'is_favorited' => $result['status']
            ], $result['message']);

        } catch (\InvalidArgumentException $e) {
            $this->validationError($e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error updating favorites', 500);
        }
    }

    /**
     * Get user's favorite articles
     */
    public function getFavorites(): void 
    {
        try {
            $userId = $_REQUEST['user_id'] ?? null;
            
            if (!$userId) {
                $this->unauthorized('User ID is required');
                return;
            }

            $result = $this->favoriteService->getUserFavorites($userId);
            $this->success($result, 'Favorites retrieved successfully');

        } catch (\Exception $e) {
            $this->error('Error fetching favorites', 500);
        }
    }
}