<?php

namespace App\Services\DTOs;

class SearchRequestDTO {
    public function __construct(
        public readonly ?string $query = null,
        public readonly ?string $beginDate = null,
        public readonly ?string $endDate = null,
        public readonly ?bool $facet = null,
        public readonly ?array $facetFields = null,
        public readonly ?bool $facetFilter = null,
        public readonly ?array $fl = null,
        public readonly ?string $fq = null,
        public readonly ?int $page = 0,
        public readonly string $sort = 'relevance'
    ) {
        $this->validate();
    }

    private function validate(): void {
        if ($this->beginDate && !preg_match('/^\d{8}$/', $this->beginDate)) {
            throw new \InvalidArgumentException('Begin date must be in YYYYMMDD format');
        }

        if ($this->endDate && !preg_match('/^\d{8}$/', $this->endDate)) {
            throw new \InvalidArgumentException('End date must be in YYYYMMDD format');
        }

        if ($this->page !== null && ($this->page < 0 || $this->page > 100)) {
            throw new \InvalidArgumentException('Page must be between 0 and 100');
        }

        if ($this->sort && !in_array($this->sort, ['newest', 'oldest', 'relevance'])) {
            throw new \InvalidArgumentException('Sort must be newest, oldest, or relevance');
        }

        if ($this->facetFields) {
            $validFacets = [
                'day_of_week', 'document_type', 'ingredients', 'news_desk',
                'pub_month', 'pub_year', 'section_name', 'source',
                'subsection_name', 'type_of_material'
            ];
            
            $invalidFacets = array_diff($this->facetFields, $validFacets);
            if (!empty($invalidFacets)) {
                throw new \InvalidArgumentException('Invalid facet fields: ' . implode(', ', $invalidFacets));
            }
        }
    }

    public function toQueryParams(): array {
        $params = [];

        if ($this->query) {
            $params['q'] = $this->query;
        }

        if ($this->beginDate) {
            $params['begin_date'] = $this->beginDate;
        }

        if ($this->endDate) {
            $params['end_date'] = $this->endDate;
        }

        if ($this->facet !== null) {
            $params['facet'] = $this->facet ? 'true' : 'false';
        }

        if ($this->facetFields) {
            $params['facet_fields'] = implode(',', $this->facetFields);
        }

        if ($this->facetFilter !== null) {
            $params['facet_filter'] = $this->facetFilter ? 'true' : 'false';
        }

        if ($this->fl) {
            $params['fl'] = implode(',', $this->fl);
        }

        if ($this->fq) {
            $params['fq'] = $this->fq;
        }

        if ($this->page !== null) {
            $params['page'] = $this->page;
        }

        if ($this->sort !== 'relevance') {
            $params['sort'] = $this->sort;
        }

        return $params;
    }

    public static function fromRequest(array $request): self {
        return new self(
            query: $request['q'] ?? null,
            beginDate: $request['begin_date'] ?? null,
            endDate: $request['end_date'] ?? null,
            facet: isset($request['facet']) ? filter_var($request['facet'], FILTER_VALIDATE_BOOLEAN) : null,
            facetFields: isset($request['facet_fields']) ? explode(',', $request['facet_fields']) : null,
            facetFilter: isset($request['facet_filter']) ? filter_var($request['facet_filter'], FILTER_VALIDATE_BOOLEAN) : null,
            fl: isset($request['fl']) ? explode(',', $request['fl']) : null,
            fq: $request['fq'] ?? null,
            page: isset($request['page']) ? ((int)$request['page'] - 1) : 0,
            sort: $request['sort'] ?? 'relevance'
        );
    }
}
