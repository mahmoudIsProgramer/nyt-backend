<?php

namespace App\DTOs;

class FavoriteDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly string $articleId,
        public readonly string $createdAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            userId: $data['user_id'],
            articleId: $data['article_id'],
            createdAt: $data['created_at']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'article_id' => $this->articleId,
            'created_at' => $this->createdAt
        ];
    }
}