<?php

namespace App\Models;

use App\Core\Database;

class QueryBuilder
{
    protected string $modelClass;
    protected array $where = [];
    protected array $orderBy = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    public function where(string $column, mixed $value, string $operator = '='): self
    {
        $this->where[] = [$column, $operator, $value];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = [$column, strtoupper($direction)];
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        $query = $this->buildQuery();
        $stmt = Database::getInstance()->getDriver()->prepare($query['sql']);
        
        foreach ($query['bindings'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $result = Database::getInstance()->getDriver()->execute($stmt);
        $rows = Database::getInstance()->getDriver()->fetchAll($result);
        
        return array_map(fn($row) => new $this->modelClass($row), $rows);
    }

    public function first(): ?object
    {
        return $this->limit(1)->get()[0] ?? null;
    }

    protected function buildQuery(): array
    {
        $model = new $this->modelClass();
        $sql = "SELECT * FROM {$model->getTable()}";
        $bindings = [];

        if (!empty($this->where)) {
            $conditions = [];
            foreach ($this->where as $i => $where) {
                $key = ":where_{$i}";
                $conditions[] = "{$where[0]} {$where[1]} {$key}";
                $bindings[$key] = $where[2];
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', array_map(
                fn($order) => "{$order[0]} {$order[1]}", 
                $this->orderBy
            ));
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return ['sql' => $sql, 'bindings' => $bindings];
    }
}