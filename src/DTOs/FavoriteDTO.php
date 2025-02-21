<?php

namespace App\DTOs;

class FavoriteDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $articleId,
        public readonly string $createdAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: (int) $data['user_id'],
            articleId: $data['article_id'],
            createdAt: $data['created_at']
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'article_id' => $this->articleId,
            'created_at' => $this->createdAt
        ];
    }
}