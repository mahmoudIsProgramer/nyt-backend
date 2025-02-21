<?php

namespace App\Http\Resources;

abstract class Resource {
    protected $resource;
    protected $additional;

    public function __construct($resource) {
        $this->resource = $resource;
    }

    abstract public function toArray(): array;

    public function additional(array $data): self {
        $this->additional = $data;
        return $this;
    }

    public function toResponse(): array {
        return array_merge(
            ['data' => $this->toArray()],
            $this->additional ?? []
        );
    }
}
