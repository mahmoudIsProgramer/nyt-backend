<?php

namespace App\Http\Resources;

use App\Services\DTOs\PaginationDTO;

class ArticleCollectionResource extends Resource {
    private PaginationDTO $pagination;

    public function __construct(array $articles, PaginationDTO $pagination) {
        parent::__construct($articles);
        $this->pagination = $pagination;
    }

    public function toArray(): array {
        return [
            'articles' => array_map(
                fn($article) => (new ArticleResource($article))->toArray(),
                $this->resource
            ),
            'pagination' => [
                'current_page' => $this->pagination->currentPage,
                'total_items' => $this->pagination->totalItems,
                'items_per_page' => $this->pagination->itemsPerPage,
                'total_pages' => $this->pagination->totalPages,
                'has_more' => $this->pagination->hasMore
            ]
        ];
    }
}
