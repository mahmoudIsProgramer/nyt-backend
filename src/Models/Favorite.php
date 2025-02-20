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

    /**
     * Get all favorites for a user
     */
    public function getAllByUserId(int $userId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        
        return $this->fetchAll($stmt->execute());
    }

    /**
     * Check if an article is already favorited by user
     */
    public function isFavorited(int $userId, string $articleId): bool
    {
        return $this->exists('user_id', $userId, [
            'article_id' => $articleId
        ]);
    }

    /**
     * Remove favorite article
     */
    public function unfavorite(int $userId, string $articleId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id AND article_id = :article_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
        $stmt->bindValue(':article_id', $articleId, SQLITE3_TEXT);
        
        return $stmt->execute() !== false;
    }

    /**
     * Create new favorite
     */
    public function create(array $data): ?int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        
        try {
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $this->fillable) . 
                   ") VALUES (" . implode(', ', array_map(fn($field) => ":$field", $this->fillable)) . ")";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($this->fillable as $field) {
                $type = $field === 'user_id' ? SQLITE3_INTEGER : SQLITE3_TEXT;
                $stmt->bindValue(":$field", $data[$field], $type);
            }
            
            $result = $stmt->execute();
            if ($result === false) {
                throw new \Exception("Failed to create favorite: " . $this->db->lastErrorMsg());
            }
            
            return $this->db->lastInsertRowID();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}