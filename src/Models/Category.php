<?php

declare(strict_types=1);

namespace Nicastore\Models;

use Nicastore\Core\Model;

class Category extends Model
{
    protected string $table = 'categories';
    protected array $fillable = ['name', 'slug', 'description', 'image', 'is_active'];
    
    public function getActive(): array
    {
        return $this->where('is_active', 1);
    }
    
    public function findBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }
    
    public function getWithProductCount(): array
    {
        return $this->query("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
            WHERE c.is_active = 1
            GROUP BY c.id
            ORDER BY c.name
        ")->fetchAll();
    }
    
    public function getProducts(int $categoryId, int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        
        $total = (int)$this->query(
            "SELECT COUNT(*) as count FROM products WHERE category_id = ? AND is_active = 1",
            [$categoryId]
        )->fetch()['count'];
        
        $data = $this->query(
            "SELECT * FROM products WHERE category_id = ? AND is_active = 1 ORDER BY id DESC LIMIT ? OFFSET ?",
            [$categoryId, $perPage, $offset]
        )->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($total / $perPage)
        ];
    }
}