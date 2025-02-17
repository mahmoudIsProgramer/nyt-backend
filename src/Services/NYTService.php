<?php

namespace App\Services;

use App\Config\Config;
use App\Services\ValueObjects\{ApiResponse, Article, Pagination};
use GuzzleHttp\Client;
use GuzzleHttp\Exception\{GuzzleException, RequestException};

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

    private function makeRequest(string $endpoint, array $params = []): array {
        try {
            $response = $this->client->request('GET', $endpoint, [
                'query' => array_merge($params, ['api-key' => $this->apiKey])
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $error = json_decode($e->getResponse()->getBody(), true);
                throw new \RuntimeException(
                    $error['fault']['faultstring'] ?? 'API request failed',
                    $e->getCode() ?: 500
                );
            }
            throw new \RuntimeException('Failed to connect to NYT API', 503);
        }
    }

    public function searchArticles(string $query, int $page = 1): array {
        try {
            $data = $this->makeRequest('articlesearch.json', [
                'q' => $query,
                'page' => max(0, $page - 1)
            ]);

            $articles = array_map(
                fn($article) => Article::fromArray($article),
                $data['response']['docs'] ?? []
            );

            $pagination = Pagination::create(
                currentPage: $page,
                totalItems: $data['response']['meta']['hits'] ?? 0,
                itemsPerPage: self::ITEMS_PER_PAGE
            );

            return ApiResponse::success([
                'articles' => array_map(fn($article) => $article->toArray(), $articles),
                'pagination' => $pagination->toArray()
            ])->toArray();

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode())->toArray();
        }
    }

    public function getArticle(string $articleUrl): array {
        try {
            $data = $this->makeRequest('articlesearch.json', [
                'fq' => sprintf('web_url:"%s"', $articleUrl)
            ]);

            $articleData = $data['response']['docs'][0] ?? null;

            if (!$articleData) {
                return ApiResponse::error('Article not found', 404)->toArray();
            }

            $article = Article::fromArray($articleData);

            return ApiResponse::success([
                'article' => $article->toArray()
            ])->toArray();

        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), $e->getCode())->toArray();
        }
    }
}
