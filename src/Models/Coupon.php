<?php

declare(strict_types=1);

namespace Nicastore\Models;

use Nicastore\Core\Model;
use Nicastore\Core\Database;

class Coupon extends Model
{
    protected string $table = 'coupons';
    protected array $fillable = [
        'code', 'type', 'value', 'min_order_amount', 'max_discount',
        'usage_limit', 'used_count', 'starts_at', 'expires_at', 'is_active'
    ];
    
    public function create(array $data): int
    {
        $data = $this->filterFillable($data);
        $data['code'] = strtoupper($data['code']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return Database::insert($this->table, $data);
    }
    
    public function findByCode(string $code): ?array
    {
        return $this->findBy('code', strtoupper($code));
    }
    
    public function validate(string $code, float $subtotal): ?array
    {
        $coupon = $this->findByCode($code);
        
        if (!$coupon) {
            return null;
        }
        
        if (!$coupon['is_active']) {
            return null;
        }
        
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return null;
        }
        
        $now = new \DateTime();
        
        if ($coupon['starts_at'] && $now < new \DateTime($coupon['starts_at'])) {
            return null;
        }
        
        if ($coupon['expires_at'] && $now > new \DateTime($coupon['expires_at'])) {
            return null;
        }
        
        if ($subtotal < $coupon['min_order_amount']) {
            return null;
        }
        
        return $coupon;
    }
    
    public function incrementUsage(int $id): int
    {
        return $this->query(
            "UPDATE coupons SET used_count = used_count + 1 WHERE id = ?",
            [$id]
        )->rowCount();
    }
    
    public function getActive(): array
    {
        return $this->where('is_active', 1);
    }
}