<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\Cart;
use Nicastore\Models\Product;
use Nicastore\Config\AppConfig;

class CartController extends Controller
{
    private Cart $cartModel;
    private Product $productModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }
    
    public function index(): void
    {
        $items = $this->cartModel->getItems();
        $subtotal = $this->cartModel->getSubtotal();
        $count = $this->cartModel->getCount();
        
        $settings = new \Nicastore\Models\Setting();
        $shippingCost = (float)$settings->get('shipping_cost', 5.99);
        $freeShippingThreshold = (float)$settings->get('free_shipping_threshold', 50);
        $taxRate = (float)$settings->get('tax_rate', 21);
        
        $shipping = $subtotal >= $freeShippingThreshold ? 0 : $shippingCost;
        $tax = $subtotal * $taxRate / 100;
        $total = $subtotal + $shipping + $tax;
        
        $this->view('cart/index', [
            'title' => 'Carrito de compras',
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'count' => $count,
            'freeShippingThreshold' => $freeShippingThreshold
        ]);
    }
    
    public function add(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 400);
        }
        
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        
        $product = $this->productModel->find($productId);
        
        if (!$product || !$product['is_active']) {
            $this->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
        }
        
        if ($product['stock'] < $quantity) {
            $this->json(['success' => false, 'message' => 'Stock insuficiente'], 400);
        }
        
        $this->cartModel->add($productId, $quantity);
        
        $count = $this->cartModel->getCount();
        
        $this->json([
            'success' => true,
            'message' => 'Producto añadido al carrito',
            'count' => $count
        ]);
    }
    
    public function update(int $itemId): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 400);
        }
        
        $quantity = max(0, (int)($_POST['quantity'] ?? 0));
        
        $items = $this->cartModel->getItems();
        $item = null;
        
        foreach ($items as $i) {
            if ($i['id'] == $itemId) {
                $item = $i;
                break;
            }
        }
        
        if (!$item) {
            $this->json(['success' => false, 'message' => 'Artículo no encontrado'], 404);
        }
        
        if ($quantity > 0 && $quantity > $item['stock']) {
            $this->json(['success' => false, 'message' => 'Stock insuficiente'], 400);
        }
        
        $this->cartModel->updateQuantity($itemId, $quantity);
        
        $items = $this->cartModel->getItems();
        $subtotal = $this->cartModel->getSubtotal();
        $count = $this->cartModel->getCount();
        
        $settings = new \Nicastore\Models\Setting();
        $shippingCost = (float)$settings->get('shipping_cost', 5.99);
        $freeShippingThreshold = (float)$settings->get('free_shipping_threshold', 50);
        $taxRate = (float)$settings->get('tax_rate', 21);
        
        $shipping = $subtotal >= $freeShippingThreshold ? 0 : $shippingCost;
        $tax = $subtotal * $taxRate / 100;
        $total = $subtotal + $shipping + $tax;
        
        $this->json([
            'success' => true,
            'count' => $count,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total
        ]);
    }
    
    public function remove(int $itemId): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 400);
        }
        
        $this->cartModel->remove($itemId);
        
        $items = $this->cartModel->getItems();
        $subtotal = $this->cartModel->getSubtotal();
        $count = $this->cartModel->getCount();
        
        $settings = new \Nicastore\Models\Setting();
        $shippingCost = (float)$settings->get('shipping_cost', 5.99);
        $freeShippingThreshold = (float)$settings->get('free_shipping_threshold', 50);
        $taxRate = (float)$settings->get('tax_rate', 21);
        
        $shipping = $subtotal >= $freeShippingThreshold ? 0 : $shippingCost;
        $tax = $subtotal * $taxRate / 100;
        $total = $subtotal + $shipping + $tax;
        
        $this->json([
            'success' => true,
            'count' => $count,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total
        ]);
    }
    
    public function count(): void
    {
        $this->json(['count' => $this->cartModel->getCount()]);
    }
    
    public function applyCoupon(): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 400);
        }
        
        $code = trim($_POST['coupon_code'] ?? '');
        
        if (empty($code)) {
            $this->json(['success' => false, 'message' => 'Código de cupón requerido'], 400);
        }
        
        $subtotal = $this->cartModel->getSubtotal();
        $couponModel = new \Nicastore\Models\Coupon();
        $coupon = $couponModel->validate($code, $subtotal);
        
        if (!$coupon) {
            $this->json(['success' => false, 'message' => 'Cupón inválido o no aplicable'], 400);
        }
        
        $discount = 0;
        if ($coupon['type'] === 'percentage') {
            $discount = $subtotal * $coupon['value'] / 100;
            if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
                $discount = $coupon['max_discount'];
            }
        } else {
            $discount = min($coupon['value'], $subtotal);
        }
        
        Session::set('applied_coupon', [
            'code' => $coupon['code'],
            'discount' => $discount,
            'type' => $coupon['type'],
            'value' => $coupon['value']
        ]);
        
        $settings = new \Nicastore\Models\Setting();
        $shippingCost = (float)$settings->get('shipping_cost', 5.99);
        $freeShippingThreshold = (float)$settings->get('free_shipping_threshold', 50);
        $taxRate = (float)$settings->get('tax_rate', 21);
        
        $shipping = $subtotal >= $freeShippingThreshold ? 0 : $shippingCost;
        $tax = $subtotal * $taxRate / 100;
        $total = $subtotal + $shipping + $tax - $discount;
        
        $this->json([
            'success' => true,
            'message' => 'Cupón aplicado correctamente',
            'discount' => $discount,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total
        ]);
    }
}