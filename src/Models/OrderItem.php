<?php

declare(strict_types=1);

namespace Nicastore\Models;

use Nicastore\Core\Model;

class OrderItem extends Model
{
    protected string $table = 'order_items';
    protected array $fillable = ['order_id', 'product_id', 'quantity', 'price', 'total'];
}