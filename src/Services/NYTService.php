<?php

namespace App\Services;

use App\Config\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class NYTService {
    private Client $client;
    private string $apiKey;

    public function __construct() {
        $this->client = new Client([
            'base_uri' => 'https://api.nytimes.com/svc/search/v2/',
            'timeout'  => 5.0,
        ]);
        $this->apiKey = Config::getInstance()->get('nyt_api_key');
    }

    /**
     * Search for articles in the NYT API
     * 
     * @param string $query Search query
     * @param int $page Page number (1-based)
     * @return array Search results with pagination info
     */
    public function searchArticles(string $query, int $page = 1): array {
        try {
            $response = $this->client->request('GET', 'articlesearch.json', [
                'query' => [
                    'q' => $query,
                    'page' => $page - 1, // NYT API uses 0-based pagination
                    'api-key' => $this->apiKey
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            
            return [
                'status' => 'success',
                'data' => [
                    'articles' => $data['response']['docs'] ?? [],
                    'pagination' => [
                        'current_page' => $page,
                        'total_hits' => $data['response']['meta']['hits'] ?? 0,
                        'has_more' => ($page * 10) < ($data['response']['meta']['hits'] ?? 0)
                    ]
                ]
            ];
        } catch (GuzzleException $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch articles',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get details of a specific article
     * 
     * @param string $articleUrl The article's URL
     * @return array|null Article details or null if not found
     */
    public function getArticle(string $articleUrl): ?array {
        try {
            $response = $this->client->request('GET', 'articlesearch.json', [
                'query' => [
                    'fq' => "web_url:\"$articleUrl\"",
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
                    'article' => $article
                ]
            ];
        } catch (GuzzleException $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to fetch article details',
                'error' => $e->getMessage()
            ];
        }
    }
}
