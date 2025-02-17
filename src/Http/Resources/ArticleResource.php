<?php

namespace App\Http\Resources;

use App\Services\DTOs\ArticleDTO;

class ArticleResource extends Resource {
    public function __construct(ArticleDTO $article) {
        parent::__construct($article);
    }

    public function toArray(): array {
        return [
            'id' => $this->resource->id,
            'url' => $this->resource->url,
            'title' => $this->resource->title,
            'abstract' => $this->resource->abstract,
            'content' => $this->resource->leadParagraph,
            'metadata' => [
                'source' => $this->resource->source,
                'published_at' => $this->resource->publishedDate,
                'section' => $this->resource->section,
                'type' => $this->resource->type,
                'word_count' => $this->resource->wordCount
            ],
            'authors' => array_map(function($author) {
                return [
                    'name' => $author['name'],
                    'role' => $author['role']
                ];
            }, $this->resource->authors),
            'media' => array_map(function($item) {
                return [
                    'url' => $item['url'],
                    'type' => $item['type'],
                    'dimensions' => [
                        'width' => $item['width'],
                        'height' => $item['height']
                    ],
                    'caption' => $item['caption']
                ];
            }, $this->resource->multimedia),
            'keywords' => $this->resource->keywords
        ];
    }
}
