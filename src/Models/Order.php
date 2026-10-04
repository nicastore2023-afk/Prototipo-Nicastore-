<?php

declare(strict_types=1);

namespace Nicastore\Models;

use Nicastore\Core\Model;
use Nicastore\Core\Database;
use Nicastore\Core\Session;

class Order extends Model
{
    protected string $table = 'orders';
    protected array $fillable = [
        'user_id', 'status', 'total', 'subtotal', 'tax', 'shipping', 'discount',
        'coupon_code', 'shipping_name', 'shipping_email', 'shipping_phone',
        'shipping_address', 'shipping_city', 'shipping_state', 'shipping_zip',
        'shipping_country', 'notes'
    ];
    
    public function createFromCart(array $data): int
    {
        $userId = Session::getUserId();
        $cart = new Cart();
        $items = $cart->getItems();
        
        if (empty($items)) {
            throw new \Exception('El carrito está vacío');
        }
        
        $errors = $cart->validateStock();
        if (!empty($errors)) {
            throw new \Exception(implode(', ', $errors));
        }
        
        $subtotal = $cart->getSubtotal();
        $taxRate = (float)(new Setting())->get('tax_rate', 21);
        $tax = $subtotal * $taxRate / 100;
        $shipping = (float)(new Setting())->get('shipping_cost', 5.99);
        $freeShippingThreshold = (float)(new Setting())->get('free_shipping_threshold', 50);
        
        if ($subtotal >= $freeShippingThreshold) {
            $shipping = 0;
        }
        
        $discount = 0;
        $couponCode = null;
        
        if (!empty($data['coupon_code'])) {
            $coupon = (new Coupon())->validate($data['coupon_code'], $subtotal);
            if ($coupon) {
                $discount = $this->calculateDiscount($coupon, $subtotal);
                $couponCode = $coupon['code'];
                (new Coupon())->incrementUsage($coupon['id']);
            }
        }
        
        $total = $subtotal + $tax + $shipping - $discount;
        
        Database::beginTransaction();
        
        try {
            $orderId = $this->create([
                'user_id' => $userId,
                'status' => 'pending',
                'total' => $total,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'discount' => $discount,
                'coupon_code' => $couponCode,
                'shipping_name' => $data['shipping_name'],
                'shipping_email' => $data['shipping_email'],
                'shipping_phone' => $data['shipping_phone'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'] ?? null,
                'shipping_state' => $data['shipping_state'] ?? null,
                'shipping_zip' => $data['shipping_zip'] ?? null,
                'shipping_country' => $data['shipping_country'] ?? 'España',
                'notes' => $data['notes'] ?? null,
            ]);
            
            $orderItemModel = new OrderItem();
            foreach ($items as $item) {
                $price = $item['on_sale'] ? $item['sale_price'] : $item['price'];
                $orderItemModel->create([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'total' => $price * $item['quantity'],
                ]);
                
                (new Product())->decreaseStock($item['product_id'], $item['quantity']);
            }
            
            $cart->clear();
            
            Database::commit();
            
            return $orderId;
        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }
    }
    
    private function calculateDiscount(array $coupon, float $subtotal): float
    {
        if ($coupon['type'] === 'percentage') {
            $discount = $subtotal * $coupon['value'] / 100;
            if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
                return $coupon['max_discount'];
            }
            return $discount;
        }
        
        return min($coupon['value'], $subtotal);
    }
    
    public function findById(int $id): ?array
    {
        return $this->query("
            SELECT o.*, u.name as user_name, u.email as user_email
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ?
        ", [$id])->fetch();
    }
    
    public function getUserOrders(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        $total = (int)$this->query(
            "SELECT COUNT(*) as count FROM orders WHERE user_id = ?",
            [$userId]
        )->fetch()['count'];
        
        $data = $this->query(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        )->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($total / $perPage)
        ];
    }
    
    public function getAll(int $page = 1, int $perPage = 20, string $status = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $where = $status ? "WHERE status = ?" : "";
        $params = $status ? [$status, $perPage, $offset] : [$perPage, $offset];
        
        $total = (int)$this->query(
            "SELECT COUNT(*) as count FROM orders {$where}",
            $status ? [$status] : []
        )->fetch()['count'];
        
        $data = $this->query(
            "SELECT o.*, u.name as user_name, u.email as user_email FROM orders o JOIN users u ON o.user_id = u.id {$where} ORDER BY o.created_at DESC LIMIT ? OFFSET ?",
            $params
        )->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($total / $perPage)
        ];
    }
    
    public function updateStatus(int $id, string $status): int
    {
        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException('Estado no válido');
        }
        
        $data = ['status' => $status];
        if ($status === 'shipped') {
            $data['shipped_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'delivered') {
            $data['delivered_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'paid') {
            $data['paid_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->update($id, $data);
    }
    
    public function getItems(int $orderId): array
    {
        return $this->query("
            SELECT oi.*, p.name, p.slug, p.image
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ", [$orderId])->fetchAll();
    }
}