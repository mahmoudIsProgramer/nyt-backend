<?php

namespace App\Services\Http\Config;

class HttpConfig {
    public function __construct(
        private readonly string $baseUrl,
        private readonly array $headers = [],
        private readonly array $query = [],
        private readonly float $timeout = 5.0
    ) {}

    public function getBaseUrl(): string {
        return $this->baseUrl;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getQuery(): array {
        return $this->query;
    }

    public function getTimeout(): float {
        return $this->timeout;
    }

    public function withHeaders(array $headers): self {
        return new self(
            $this->baseUrl,
            array_merge($this->headers, $headers),
            $this->query,
            $this->timeout
        );
    }

    public function withQuery(array $query): self {
        return new self(
            $this->baseUrl,
            $this->headers,
            array_merge($this->query, $query),
            $this->timeout
        );
    }
}
