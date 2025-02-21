<?php

namespace App\Models;

class Favorite extends Model
{
    protected string $table = 'favorites';
    
    protected array $fillable = [
        'user_id',
        'article_id',
        'created_at'
    ];

    public static function getAllByUserId(int $userId): array
    {
        try {
            $result = static::query()
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->get();
            
            // Convert Model objects to arrays
            $favorites = array_map(function($favorite) {
                return $favorite->toArray();
            }, $result);
            
            error_log("Favorites query result: " . json_encode($favorites));
            return $favorites;
        } catch (\Exception $e) {
            error_log("Error fetching favorites: " . $e->getMessage());
            return [];
        }
    }

    public static function isFavored(int $userId, string $articleId): bool
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('article_id', $articleId)
            ->first() !== null;
    }

    public static function unfavorite(int $userId, string $articleId): bool
    {
        try {
            $model = new static();
            return $model->deleteWhere([
                'user_id' => $userId,
                'article_id' => $articleId
            ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
 
}