<?php

namespace App\Services\Http;

use App\Services\Http\Config\HttpConfig;
use App\Services\Http\Contracts\HttpClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Utils\Logger;

class GuzzleHttpClient implements HttpClientInterface {
    private Client $client;
    private HttpConfig $config;
    private Logger $logger;

    public function __construct(HttpConfig $config) {
        $this->config = $config;
        $this->logger = new Logger();
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
            // Log request
            $this->logger->logRequest([
                'method' => $method,
                'url' => $this->config->getBaseUrl() . $endpoint,
                'headers' => $options['headers'] ?? [],
                'body' => $options['json'] ?? [],
                'query' => $options['query'] ?? []
            ]);

            $response = $this->client->request($method, $endpoint, $options);
            $responseData = json_decode($response->getBody(), true);

            // Log response
            $this->logger->logResponse([
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $responseData
            ]);

            return $responseData;
        } catch (RequestException $e) {
            // Log error
            $this->logger->log(
                "API Error: " . $e->getMessage(),
                'ERROR',
                'api_errors.log'
            );
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
