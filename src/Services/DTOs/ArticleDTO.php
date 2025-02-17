<?php

namespace App\Services\DTOs;

class ArticleDTO {
    public function __construct(
        public readonly string $id,
        public readonly string $url,
        public readonly string $title,
        public readonly string $abstract,
        public readonly ?string $leadParagraph,
        public readonly string $source,
        public readonly string $publishedDate,
        public readonly ?string $section,
        public readonly string $type,
        public readonly int $wordCount,
        public readonly array $authors,
        public readonly array $multimedia,
        public readonly array $keywords
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            id: $data['_id'] ?? '',
            url: $data['web_url'] ?? '',
            title: $data['headline']['main'] ?? '',
            abstract: $data['abstract'] ?? '',
            leadParagraph: $data['lead_paragraph'] ?? null,
            source: $data['source'] ?? '',
            publishedDate: $data['pub_date'] ?? '',
            section: $data['section_name'] ?? null,
            type: $data['document_type'] ?? '',
            wordCount: (int)($data['word_count'] ?? 0),
            authors: self::extractAuthors($data['byline'] ?? []),
            multimedia: self::extractMultimedia($data['multimedia'] ?? []),
            keywords: self::extractKeywords($data['keywords'] ?? [])
        );
    }

    private static function extractAuthors(array $byline): array {
        if (empty($byline['person'])) {
            return [];
        }

        return array_map(function($person) {
            return [
                'name' => trim(sprintf('%s %s', $person['firstname'] ?? '', $person['lastname'] ?? '')),
                'role' => $person['role'] ?? null
            ];
        }, $byline['person']);
    }

    private static function extractMultimedia(array $multimedia): array {
        return array_map(function($item) {
            return [
                'url' => $item['url'] ?? '',
                'type' => $item['type'] ?? '',
                'height' => (int)($item['height'] ?? 0),
                'width' => (int)($item['width'] ?? 0),
                'caption' => $item['caption'] ?? null
            ];
        }, array_filter($multimedia, fn($item) => !empty($item['url'])));
    }

    private static function extractKeywords(array $keywords): array {
        return array_map(fn($keyword) => $keyword['value'], $keywords);
    }

    public function toArray(): array {
        return get_object_vars($this);
    }
}
