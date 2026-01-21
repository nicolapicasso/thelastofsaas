<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base Model Class
 * Omniwallet CMS
 */
abstract class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find by primary key
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1";

        $result = $this->db->fetch($sql, [$id]);

        return $result ? $this->hideFields($result) : null;
    }

    /**
     * Find by field
     */
    public function findBy(string $field, mixed $value): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$field}` = ? LIMIT 1";

        $result = $this->db->fetch($sql, [$value]);

        return $result ? $this->hideFields($result) : null;
    }

    /**
     * Get all records
     */
    public function all(array $orderBy = ['id' => 'ASC']): array
    {
        $order = $this->buildOrderBy($orderBy);
        $sql = "SELECT * FROM `{$this->table}` {$order}";

        $results = $this->db->fetchAll($sql);

        return array_map(fn($row) => $this->hideFields($row), $results);
    }

    /**
     * Get records with conditions
     */
    public function where(array $conditions, array $orderBy = ['id' => 'ASC'], ?int $limit = null): array
    {
        [$where, $params] = $this->buildWhere($conditions);
        $order = $this->buildOrderBy($orderBy);
        $limitSql = $limit ? "LIMIT {$limit}" : '';

        $sql = "SELECT * FROM `{$this->table}` WHERE {$where} {$order} {$limitSql}";

        $results = $this->db->fetchAll($sql, $params);

        return array_map(fn($row) => $this->hideFields($row), $results);
    }

    /**
     * Get first record with conditions
     */
    public function first(array $conditions): ?array
    {
        $results = $this->where($conditions, ['id' => 'ASC'], 1);

        return $results[0] ?? null;
    }

    /**
     * Create new record
     */
    public function create(array $data): int
    {
        $filteredData = $this->filterFillable($data);

        return $this->db->insert($this->table, $filteredData);
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): bool
    {
        $filteredData = $this->filterFillable($data);

        // Always update the updated_at timestamp
        $filteredData['updated_at'] = date('Y-m-d H:i:s');

        $affected = $this->db->update(
            $this->table,
            $filteredData,
            "`{$this->primaryKey}` = ?",
            [$id]
        );

        return $affected > 0;
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        $affected = $this->db->delete(
            $this->table,
            "`{$this->primaryKey}` = ?",
            [$id]
        );

        return $affected > 0;
    }

    /**
     * Count records
     */
    public function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            $sql = "SELECT COUNT(*) FROM `{$this->table}`";
            return (int) $this->db->fetchColumn($sql);
        }

        [$where, $params] = $this->buildWhere($conditions);
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE {$where}";

        return (int) $this->db->fetchColumn($sql, $params);
    }

    /**
     * Check if record exists
     */
    public function exists(array $conditions): bool
    {
        return $this->count($conditions) > 0;
    }

    /**
     * Paginate results
     */
    public function paginate(int $page = 1, int $perPage = 15, array $conditions = [], array $orderBy = ['id' => 'DESC']): array
    {
        $offset = ($page - 1) * $perPage;
        $total = $this->count($conditions);
        $totalPages = (int) ceil($total / $perPage);

        if (empty($conditions)) {
            $order = $this->buildOrderBy($orderBy);
            $sql = "SELECT * FROM `{$this->table}` {$order} LIMIT {$perPage} OFFSET {$offset}";
            $data = $this->db->fetchAll($sql);
        } else {
            [$where, $params] = $this->buildWhere($conditions);
            $order = $this->buildOrderBy($orderBy);
            $sql = "SELECT * FROM `{$this->table}` WHERE {$where} {$order} LIMIT {$perPage} OFFSET {$offset}";
            $data = $this->db->fetchAll($sql, $params);
        }

        return [
            'data' => array_map(fn($row) => $this->hideFields($row), $data),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
                'has_prev' => $page > 1,
                'has_next' => $page < $totalPages,
            ],
        ];
    }

    /**
     * Build WHERE clause from conditions
     */
    protected function buildWhere(array $conditions): array
    {
        $clauses = [];
        $params = [];

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                // Handle operators: ['field' => ['>=', 10]]
                $operator = $value[0];
                $clauses[] = "`{$field}` {$operator} ?";
                $params[] = $value[1];
            } elseif ($value === null) {
                $clauses[] = "`{$field}` IS NULL";
            } else {
                $clauses[] = "`{$field}` = ?";
                $params[] = $value;
            }
        }

        return [implode(' AND ', $clauses), $params];
    }

    /**
     * Build ORDER BY clause
     */
    protected function buildOrderBy(array $orderBy): string
    {
        $clauses = [];

        foreach ($orderBy as $field => $direction) {
            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
            $clauses[] = "`{$field}` {$direction}";
        }

        return 'ORDER BY ' . implode(', ', $clauses);
    }

    /**
     * Filter data by fillable fields
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_filter(
            $data,
            fn($key) => in_array($key, $this->fillable),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Hide specified fields from result
     */
    protected function hideFields(array $data): array
    {
        if (empty($this->hidden)) {
            return $data;
        }

        return array_filter(
            $data,
            fn($key) => !in_array($key, $this->hidden),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Get table name
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Raw query
     */
    public function raw(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }
}
