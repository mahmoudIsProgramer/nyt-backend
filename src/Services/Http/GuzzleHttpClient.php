<?php

namespace App\Services\Http;

use App\Services\Http\Config\HttpConfig;
use App\Services\Http\Contracts\HttpClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
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
            // Log error with context
            $this->logger->log(
                message: "API Error: " . $e->getMessage(),
                level: 'ERROR',
                context: [
                    'method' => $method,
                    'url' => $endpoint,
                    'options' => $options,
                    'response' => $e->hasResponse() ? json_decode($e->getResponse()->getBody(), true) : null
                ]
            );

            // Return error response if available, otherwise return error array
            if ($e->hasResponse()) {
                return [
                    'error' => true,
                    'status' => $e->getResponse()->getStatusCode(),
                    'message' => json_decode($e->getResponse()->getBody(), true)['message'] ?? $e->getMessage(),
                    'data' => json_decode($e->getResponse()->getBody(), true)
                ];
            }

            return [
                'error' => true,
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => null
            ];

        } catch (GuzzleException $e) {
            // Log other Guzzle errors
            $this->logger->log(
                message: "Guzzle Error: " . $e->getMessage(),
                level: 'ERROR',
                context: [
                    'method' => $method,
                    'url' => $endpoint,
                    'options' => $options
                ]
            );

            return [
                'error' => true,
                'status' => 500,
                'message' => 'Network or configuration error',
                'data' => null
            ];

        } catch (\Exception $e) {
            // Log unexpected errors
            $this->logger->log(
                message: "Unexpected Error: " . $e->getMessage(),
                level: 'ERROR',
                context: [
                    'method' => $method,
                    'url' => $endpoint,
                    'options' => $options
                ]
            );

            return [
                'error' => true,
                'status' => 500,
                'message' => 'An unexpected error occurred',
                'data' => null
            ];
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
