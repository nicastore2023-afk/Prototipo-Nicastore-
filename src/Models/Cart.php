<?php

declare(strict_types=1);

namespace Nicastore\Models;

use Nicastore\Core\Model;
use Nicastore\Core\Session;

class Cart extends Model
{
    protected string $table = 'cart_items';
    protected array $fillable = ['user_id', 'session_id', 'product_id', 'quantity'];
    
    public function getSessionId(): string
    {
        return Session::getId();
    }
    
    public function getUserId(): ?int
    {
        return Session::getUserId();
    }
    
    public function getItems(): array
    {
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        
        if ($userId) {
            return $this->query("
                SELECT ci.*, p.name, p.slug, p.price, p.sale_price, p.image, p.stock,
                       (p.sale_price IS NOT NULL AND p.sale_price < p.price) as on_sale
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.user_id = ? AND p.is_active = 1
                ORDER BY ci.created_at DESC
            ", [$userId])->fetchAll();
        }
        
        return $this->query("
            SELECT ci.*, p.name, p.slug, p.price, p.sale_price, p.image, p.stock,
                   (p.sale_price IS NOT NULL AND p.sale_price < p.price) as on_sale
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
            WHERE ci.session_id = ? AND p.is_active = 1
            ORDER BY ci.created_at DESC
        ", [$sessionId])->fetchAll();
    }
    
    public function getCount(): int
    {
        $items = $this->getItems();
        return array_sum(array_column($items, 'quantity'));
    }
    
    public function getSubtotal(): float
    {
        $items = $this->getItems();
        $subtotal = 0;
        foreach ($items as $item) {
            $price = $item['on_sale'] ? $item['sale_price'] : $item['price'];
            $subtotal += $price * $item['quantity'];
        }
        return $subtotal;
    }
    
    public function add(int $productId, int $quantity = 1): bool
    {
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        
        $existing = $this->query(
            "SELECT * FROM cart_items WHERE product_id = ? AND " . ($userId ? "user_id = ?" : "session_id = ?"),
            [$productId, $userId ?? $sessionId]
        )->fetch();
        
        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            return $this->update($existing['id'], ['quantity' => $newQuantity]) > 0;
        }
        
        $data = [
            'product_id' => $productId,
            'quantity' => $quantity,
        ];
        
        if ($userId) {
            $data['user_id'] = $userId;
        } else {
            $data['session_id'] = $sessionId;
        }
        
        return $this->create($data) > 0;
    }
    
    public function updateQuantity(int $itemId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->remove($itemId);
        }
        
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        
        $where = $userId ? "id = ? AND user_id = ?" : "id = ? AND session_id = ?";
        $params = [$itemId, $userId ?? $sessionId];
        
        return $this->query("UPDATE cart_items SET quantity = ? WHERE {$where}", array_merge([$quantity], $params))->rowCount() > 0;
    }
    
    public function remove(int $itemId): bool
    {
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        
        $where = $userId ? "id = ? AND user_id = ?" : "id = ? AND session_id = ?";
        $params = [$itemId, $userId ?? $sessionId];
        
        return $this->query("DELETE FROM cart_items WHERE {$where}", $params)->rowCount() > 0;
    }
    
    public function clear(): bool
    {
        $userId = $this->getUserId();
        $sessionId = $this->getSessionId();
        
        $where = $userId ? "user_id = ?" : "session_id = ?";
        $params = [$userId ?? $sessionId];
        
        return $this->query("DELETE FROM cart_items WHERE {$where}", $params)->rowCount() > 0;
    }
    
    public function mergeGuestCart(int $userId): void
    {
        $sessionId = $this->getSessionId();
        
        $guestItems = $this->query(
            "SELECT * FROM cart_items WHERE session_id = ?",
            [$sessionId]
        )->fetchAll();
        
        foreach ($guestItems as $item) {
            $existing = $this->query(
                "SELECT * FROM cart_items WHERE product_id = ? AND user_id = ?",
                [$item['product_id'], $userId]
            )->fetch();
            
            if ($existing) {
                $this->update($existing['id'], ['quantity' => $existing['quantity'] + $item['quantity']]);
            } else {
                $this->create([
                    'user_id' => $userId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ]);
            }
        }
        
        $this->query("DELETE FROM cart_items WHERE session_id = ?", [$sessionId]);
    }
    
    public function validateStock(): array
    {
        $items = $this->getItems();
        $errors = [];
        
        foreach ($items as $item) {
            if ($item['quantity'] > $item['stock']) {
                $errors[] = "Stock insuficiente para {$item['name']}. Disponible: {$item['stock']}";
            }
        }
        
        return $errors;
    }
}