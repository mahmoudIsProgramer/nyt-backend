<?php

namespace App\DTOs;

class FavoriteDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $articleId,
        public readonly string $webUrl,
        public readonly string $headline,
        public readonly ?string $snippet,
        public readonly ?string $pubDate,
        public readonly ?string $source,
        public readonly ?string $imageUrl,
        public readonly ?string $author,
        public readonly string $createdAt
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: (int) $data['user_id'],
            articleId: $data['article_id'],
            webUrl: $data['web_url'],
            headline: $data['headline'],
            snippet: $data['snippet'] ?? null,
            pubDate: $data['pub_date'] ?? null,
            source: $data['source'] ?? null,
            imageUrl: $data['image_url'] ?? null,
            author: $data['author'] ?? null,
            createdAt: $data['created_at'] ?? date('Y-m-d H:i:s')
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'article_id' => $this->articleId,
            'web_url' => $this->webUrl,
            'headline' => $this->headline,
            'snippet' => $this->snippet,
            'pub_date' => $this->pubDate,
            'source' => $this->source,
            'image_url' => $this->imageUrl,
            'author' => $this->author,
            'created_at' => $this->createdAt
        ];
    }
}