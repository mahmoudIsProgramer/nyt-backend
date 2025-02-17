<?php

namespace App\Services\DTOs;

class ArticleDTO {
    public function __construct(
        public readonly string $id,
        public readonly string $webUrl,
        public readonly string $snippet,
        public readonly ?int $printPage,
        public readonly ?string $printSection,
        public readonly string $source,
        public readonly array $multimedia,
        public readonly array $headline,
        public readonly array $keywords,
        public readonly string $pubDate,
        public readonly string $documentType,
        public readonly ?string $newsDesk,
        public readonly ?string $sectionName,
        public readonly array $byline,
        public readonly string $typeOfMaterial,
        public readonly int $wordCount,
        public readonly string $uri
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            id: $data['_id'] ?? '',
            webUrl: $data['web_url'] ?? '',
            snippet: $data['snippet'] ?? '',
            printPage: isset($data['print_page']) ? (int)$data['print_page'] : null,
            printSection: $data['print_section'] ?? null,
            source: $data['source'] ?? '',
            multimedia: array_map(function($item) {
                return [
                    'url' => $item['url'] ?? '',
                    'type' => $item['type'] ?? '',
                    'height' => (int)($item['height'] ?? 0),
                    'width' => (int)($item['width'] ?? 0),
                    'caption' => $item['caption'] ?? null,
                    'subtype' => $item['subtype'] ?? null
                ];
            }, $data['multimedia'] ?? []),
            headline: $data['headline'] ?? [],
            keywords: array_map(function($keyword) {
                return [
                    'name' => $keyword['name'] ?? '',
                    'value' => $keyword['value'] ?? '',
                    'rank' => $keyword['rank'] ?? null,
                    'major' => $keyword['major'] ?? null
                ];
            }, $data['keywords'] ?? []),
            pubDate: $data['pub_date'] ?? '',
            documentType: $data['document_type'] ?? '',
            newsDesk: $data['news_desk'] ?? null,
            sectionName: $data['section_name'] ?? null,
            byline: $data['byline'] ?? [],
            typeOfMaterial: $data['type_of_material'] ?? '',
            wordCount: (int)($data['word_count'] ?? 0),
            uri: $data['uri'] ?? ''
        );
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'web_url' => $this->webUrl,
            'snippet' => $this->snippet,
            'print_page' => $this->printPage,
            'print_section' => $this->printSection,
            'source' => $this->source,
            'multimedia' => $this->multimedia,
            'headline' => $this->headline,
            'keywords' => $this->keywords,
            'pub_date' => $this->pubDate,
            'document_type' => $this->documentType,
            'news_desk' => $this->newsDesk,
            'section_name' => $this->sectionName,
            'byline' => $this->byline,
            'type_of_material' => $this->typeOfMaterial,
            'word_count' => $this->wordCount,
            'uri' => $this->uri
        ];
    }
}
