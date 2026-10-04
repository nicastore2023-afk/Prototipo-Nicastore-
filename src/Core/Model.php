<?php

declare(strict_types=1);

namespace Nicastore\Core;

use Nicastore\Core\Database;

abstract class Model
{
    protected string $table;
    protected array $fillable = [];
    protected array $hidden = ['password', 'remember_token'];
    protected array $dates = ['created_at', 'updated_at'];
    
    public function __construct()
    {
        if (empty($this->table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower($className) . 's';
        }
    }
    
    public function find(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }
    
    public function findBy(string $column, mixed $value): ?array
    {
        return Database::fetchOne("SELECT * FROM {$this->table} WHERE {$column} = ?", [$value]);
    }
    
    public function all(array $columns = ['*'], string $orderBy = 'id DESC'): array
    {
        $cols = implode(', ', $columns);
        return Database::fetchAll("SELECT {$cols} FROM {$this->table} ORDER BY {$orderBy}");
    }
    
    public function paginate(int $page = 1, int $perPage = 12, array $columns = ['*']): array
    {
        $offset = ($page - 1) * $perPage;
        $cols = implode(', ', $columns);
        
        $total = (int)Database::fetchOne("SELECT COUNT(*) as count FROM {$this->table}")['count'];
        $data = Database::fetchAll(
            "SELECT {$cols} FROM {$this->table} ORDER BY id DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    public function create(array $data): int
    {
        $data = $this->filterFillable($data);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return Database::insert($this->table, $data);
    }
    
    public function update(int $id, array $data): int
    {
        $data = $this->filterFillable($data);
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return Database::update($this->table, $data, 'id = ?', [$id]);
    }
    
    public function delete(int $id): int
    {
        return Database::delete($this->table, 'id = ?', [$id]);
    }
    
    public function where(string $column, mixed $value, string $operator = '='): array
    {
        return Database::fetchAll(
            "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?",
            [$value]
        );
    }
    
    public function whereIn(string $column, array $values): array
    {
        if (empty($values)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        return Database::fetchAll(
            "SELECT * FROM {$this->table} WHERE {$column} IN ({$placeholders})",
            $values
        );
    }
    
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    protected function hide(array $data): array
    {
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        return $data;
    }
    
    public function query(string $sql, array $params = []): \PDOStatement
    {
        return Database::query($sql, $params);
    }
}