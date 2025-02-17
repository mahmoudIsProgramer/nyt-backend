<?php

namespace App\Http\Resources;

use App\Services\DTOs\ArticleDTO;

class ArticleResource extends Resource {
    public function __construct(ArticleDTO $article) {
        parent::__construct($article);
    }

    public function toArray(): array {
        /** @var ArticleDTO $article */
        $article = $this->resource;
        
        return [
            'id' => $article->id,
            'web_url' => $article->webUrl,
            'snippet' => $article->snippet,
            'print_info' => [
                'page' => $article->printPage,
                'section' => $article->printSection
            ],
            'source' => $article->source,
            'multimedia' => array_map(function($item) {
                return [
                    'url' => $item['url'],
                    'type' => $item['type'],
                    'dimensions' => [
                        'width' => $item['width'],
                        'height' => $item['height']
                    ],
                    'caption' => $item['caption'] ?? null,
                    'subtype' => $item['subtype'] ?? null
                ];
            }, $article->multimedia),
            'headline' => $article->headline,
            'keywords' => array_map(function($keyword) {
                return [
                    'name' => $keyword['name'] ?? '',
                    'value' => $keyword['value'] ?? '',
                    'rank' => $keyword['rank'] ?? null,
                    'major' => $keyword['major'] ?? null
                ];
            }, $article->keywords),
            'pub_date' => $article->pubDate,
            'document_type' => $article->documentType,
            'news_desk' => $article->newsDesk,
            'section_name' => $article->sectionName,
            'byline' => $article->byline,
            'type_of_material' => $article->typeOfMaterial,
            'word_count' => $article->wordCount,
            'uri' => $article->uri
        ];
    }
}
