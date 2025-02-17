<?php

namespace App\Services;

use App\Config\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class NYTService {
    private const BASE_URL = 'https://api.nytimes.com/svc/search/v2/';
    private const ITEMS_PER_PAGE = 10;
    
    private Client $client;
    private string $apiKey;

    public function __construct() {
        $this->initializeClient();
        $this->apiKey = $this->getApiKey();
    }

    private function initializeClient(): void {
        $this->client = new Client([
            'base_uri' => self::BASE_URL,
            'timeout'  => 5.0,
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'NYT Article Explorer/1.0'
            ]
        ]);
    }

    private function getApiKey(): string {
        $apiKey = Config::getInstance()->get('nyt_api_key');
        if (!$apiKey) {
            throw new \RuntimeException('NYT API key not configured');
        }
        return $apiKey;
    }

    private function extractArticleData(array $article): array {
        return [
            'id' => $article['_id'] ?? '',
            'url' => $article['web_url'] ?? '',
            'title' => $article['headline']['main'] ?? '',
            'abstract' => $article['abstract'] ?? '',
            'lead_paragraph' => $article['lead_paragraph'] ?? '',
            'source' => $article['source'] ?? '',
            'published_date' => $article['pub_date'] ?? '',
            'section' => $article['section_name'] ?? '',
            'type' => $article['document_type'] ?? '',
            'word_count' => (int)($article['word_count'] ?? 0),
            'authors' => $this->extractAuthors($article['byline'] ?? []),
            'multimedia' => $this->extractMultimedia($article['multimedia'] ?? []),
            'keywords' => $this->extractKeywords($article['keywords'] ?? [])
        ];
    }

    private function extractAuthors(array $byline): array {
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

    private function extractMultimedia(array $multimedia): array {
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

    private function extractKeywords(array $keywords): array {
        return array_map(fn($keyword) => $keyword['value'], $keywords);
    }

    public function searchArticles(string $query, int $page = 1): array {
        try {
            $response = $this->client->request('GET', 'articlesearch.json', [
                'query' => [
                    'q' => $query,
                    'page' => max(0, $page - 1),
                    'api-key' => $this->apiKey
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $articles = $data['response']['docs'] ?? [];
            $totalHits = $data['response']['meta']['hits'] ?? 0;

            return [
                'status' => 'success',
                'data' => [
                    'articles' => array_map([$this, 'extractArticleData'], $articles),
                    'pagination' => [
                        'current_page' => $page,
                        'total_items' => $totalHits,
                        'items_per_page' => self::ITEMS_PER_PAGE,
                        'total_pages' => ceil($totalHits / self::ITEMS_PER_PAGE),
                        'has_more' => ($page * self::ITEMS_PER_PAGE) < $totalHits
                    ]
                ]
            ];

        } catch (RequestException $e) {
            return [
                'status' => 'error',
                'message' => $e->hasResponse() 
                    ? json_decode($e->getResponse()->getBody(), true)['fault']['faultstring'] ?? 'API request failed'
                    : 'Failed to connect to NYT API',
                'code' => $e->getCode()
            ];
        } catch (GuzzleException $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch articles: ' . $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }

    public function getArticle(string $articleUrl): ?array {
        try {
            $response = $this->client->request('GET', 'articlesearch.json', [
                'query' => [
                    'fq' => sprintf('web_url:"%s"', $articleUrl),
                    'api-key' => $this->apiKey
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            $article = $data['response']['docs'][0] ?? null;

            if (!$article) {
                return null;
            }

            return [
                'status' => 'success',
                'data' => [
                    'article' => $this->extractArticleData($article)
                ]
            ];

        } catch (GuzzleException $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch article: ' . $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }
}
