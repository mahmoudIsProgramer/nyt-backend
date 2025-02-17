<?php

namespace App\Services\ValueObjects;

class Pagination {
    public function __construct(
        public readonly int $currentPage,
        public readonly int $totalItems,
        public readonly int $itemsPerPage,
        public readonly int $totalPages,
        public readonly bool $hasMore
    ) {}

    public static function create(int $currentPage, int $totalItems, int $itemsPerPage): self {
        $totalPages = ceil($totalItems / $itemsPerPage);
        
        return new self(
            currentPage: $currentPage,
            totalItems: $totalItems,
            itemsPerPage: $itemsPerPage,
            totalPages: $totalPages,
            hasMore: ($currentPage * $itemsPerPage) < $totalItems
        );
    }

    public function toArray(): array {
        return get_object_vars($this);
    }
}
