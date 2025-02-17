<?php

namespace App\Services\Http;

use App\Services\Http\Config\HttpConfig;
use App\Services\Http\Contracts\HttpClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GuzzleHttpClient implements HttpClientInterface {
    private Client $client;
    private HttpConfig $config;

    public function __construct(HttpConfig $config) {
        $this->config = $config;
        $this->initializeClient();
    }

    private function initializeClient(): void {
        $this->client = new Client([
            'base_uri' => $this->config->getBaseUrl(),
            'timeout' => $this->config->getTimeout(),
            'headers' => $this->config->getHeaders()
        ]);
    }

    private function request(string $method, string $endpoint, array $options = []): array {
        try {
            $response = $this->client->request($method, $endpoint, $options);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $error = json_decode($e->getResponse()->getBody(), true);
                throw new \RuntimeException(
                    $error['fault']['faultstring'] ?? 'API request failed',
                    $e->getCode() ?: 500
                );
            }
            throw new \RuntimeException('Failed to connect to API', 503);
        }
    }

    private function mergeQueryParams(array $params = []): array {
        return array_merge($this->config->getQuery(), $params);
    }

    public function get(string $endpoint, array $params = []): array {
        return $this->request('GET', $endpoint, [
            'query' => $this->mergeQueryParams($params)
        ]);
    }

    public function post(string $endpoint, array $data = [], array $params = []): array {
        return $this->request('POST', $endpoint, [
            'json' => $data,
            'query' => $this->mergeQueryParams($params)
        ]);
    }

    public function put(string $endpoint, array $data = [], array $params = []): array {
        return $this->request('PUT', $endpoint, [
            'json' => $data,
            'query' => $this->mergeQueryParams($params)
        ]);
    }

    public function delete(string $endpoint, array $params = []): array {
        return $this->request('DELETE', $endpoint, [
            'query' => $this->mergeQueryParams($params)
        ]);
    }
}
