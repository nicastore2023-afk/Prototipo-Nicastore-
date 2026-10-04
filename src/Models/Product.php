<?php

declare(strict_types=1);

namespace Nicastore\Models;

use Nicastore\Core\Model;

class Product extends Model
{
    protected string $table = 'products';
    protected array $fillable = [
        'category_id', 'name', 'slug', 'description', 'short_description',
        'price', 'sale_price', 'sku', 'stock', 'image', 'images',
        'is_active', 'is_featured'
    ];
    
    public function getActive(int $page = 1, int $perPage = 12): array
    {
        return $this->paginate($page, $perPage, ['*']);
    }
    
    public function getFeatured(int $limit = 8): array
    {
        return $this->query(
            "SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 ORDER BY id DESC LIMIT ?",
            [$limit]
        )->fetchAll();
    }
    
    public function findBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }
    
    public function getRelated(int $productId, int $categoryId, int $limit = 4): array
    {
        return $this->query(
            "SELECT * FROM products WHERE category_id = ? AND id != ? AND is_active = 1 ORDER BY RANDOM() LIMIT ?",
            [$categoryId, $productId, $limit]
        )->fetchAll();
    }
    
    public function search(string $query, int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        $searchTerm = "%{$query}%";
        
        $total = (int)$this->query(
            "SELECT COUNT(*) as count FROM products WHERE is_active = 1 AND (name LIKE ? OR description LIKE ? OR short_description LIKE ?)",
            [$searchTerm, $searchTerm, $searchTerm]
        )->fetch()['count'];
        
        $data = $this->query(
            "SELECT * FROM products WHERE is_active = 1 AND (name LIKE ? OR description LIKE ? OR short_description LIKE ?) ORDER BY id DESC LIMIT ? OFFSET ?",
            [$searchTerm, $searchTerm, $searchTerm, $perPage, $offset]
        )->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($total / $perPage)
        ];
    }
    
    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        return $this->whereIn('id', $ids);
    }
    
    public function decreaseStock(int $productId, int $quantity): int
    {
        return $this->query(
            "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?",
            [$quantity, $productId, $quantity]
        )->rowCount();
    }
    
    public function increaseStock(int $productId, int $quantity): int
    {
        return $this->query(
            "UPDATE products SET stock = stock + ? WHERE id = ?",
            [$quantity, $productId]
        )->rowCount();
    }
    
    public function getPrice(float $price, ?float $salePrice = null): float
    {
        return $salePrice !== null && $salePrice < $price ? $salePrice : $price;
    }
    
    public function formatPrice(float $price): string
    {
        return number_format($price, 2, ',', '.') . ' €';
    }
}