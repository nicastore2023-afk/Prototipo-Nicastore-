<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\User;
use Nicastore\Models\Product;
use Nicastore\Models\Category;
use Nicastore\Models\Order;
use Nicastore\Models\Setting;
use Nicastore\Core\Session;
use Nicastore\Config\AppConfig;

class AdminController extends Controller
{
    private User $userModel;
    private Product $productModel;
    private Category $categoryModel;
    private Order $orderModel;
    private Setting $settingModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->orderModel = new Order();
        $this->settingModel = new Setting();
        
        if (!Session::isLoggedIn() || Session::getUser()['role'] !== 'admin') {
            $this->redirect(AppConfig::url('/'));
        }
    }
    
    public function dashboard(): void
    {
        $stats = [
            'users' => (int)$this->userModel->query("SELECT COUNT(*) as count FROM users")->fetch()['count'],
            'products' => (int)$this->productModel->query("SELECT COUNT(*) as count FROM products")->fetch()['count'],
            'orders' => (int)$this->orderModel->query("SELECT COUNT(*) as count FROM orders")->fetch()['count'],
            'revenue' => (float)$this->orderModel->query("SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE status != 'cancelled'")->fetch()['total'],
            'pending_orders' => (int)$this->orderModel->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch()['count'],
        ];
        
        $recentOrders = $this->orderModel->query("
            SELECT o.*, u.name as user_name
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT 5
        ")->fetchAll();
        
        $this->view('admin/dashboard', [
            'title' => 'Panel de administración',
            'stats' => $stats,
            'recentOrders' => $recentOrders
        ]);
    }
    
    // Products
    public function products(int $page = 1): void
    {
        $perPage = 20;
        $result = $this->productModel->paginate($page, $perPage);
        
        $this->view('admin/products/index', [
            'title' => 'Gestión de productos',
            'products' => $result['data'],
            'pagination' => $result
        ]);
    }
    
    public function createProduct(): void
    {
        $categories = $this->categoryModel->getActive();
        
        $this->view('admin/products/create', [
            'title' => 'Nuevo producto',
            'categories' => $categories
        ]);
    }
    
    public function storeProduct(): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Token CSRF inválido']);
            $this->back();
        }
        
        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'short_description' => trim($_POST['short_description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
            'sku' => trim($_POST['sku'] ?? ''),
            'stock' => (int)($_POST['stock'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/images/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $filepath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                $data['image'] = '/images/products/' . $filename;
            }
        }
        
        $errors = [];
        if (empty($data['name'])) $errors[] = 'El nombre es requerido';
        if (empty($data['slug'])) $errors[] = 'El slug es requerido';
        if ($data['price'] <= 0) $errors[] = 'El precio debe ser mayor a 0';
        if ($data['category_id'] <= 0) $errors[] = 'Debe seleccionar una categoría';
        
        if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->back();
        }
        
        $this->productModel->create($data);
        Session::flash('flash', ['type' => 'success', 'message' => 'Producto creado correctamente']);
        $this->redirect(AppConfig::url('/admin/productos'));
    }
    
    public function editProduct(int $id): void
    {
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->redirect(AppConfig::url('/admin/productos'));
        }
        
        $categories = $this->categoryModel->getActive();
        
        $this->view('admin/products/edit', [
            'title' => 'Editar producto: ' . $product['name'],
            'product' => $product,
            'categories' => $categories
        ]);
    }
    
    public function updateProduct(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Token CSRF inválido']);
            $this->back();
        }
        
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->redirect(AppConfig::url('/admin/productos'));
        }
        
        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'short_description' => trim($_POST['short_description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
            'sku' => trim($_POST['sku'] ?? ''),
            'stock' => (int)($_POST['stock'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/images/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $filepath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                $data['image'] = '/images/products/' . $filename;
            }
        }
        
        $errors = [];
        if (empty($data['name'])) $errors[] = 'El nombre es requerido';
        if (empty($data['slug'])) $errors[] = 'El slug es requerido';
        if ($data['price'] <= 0) $errors[] = 'El precio debe ser mayor a 0';
        if ($data['category_id'] <= 0) $errors[] = 'Debe seleccionar una categoría';
        
        if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->back();
        }
        
        $this->productModel->update($id, $data);
        Session::flash('flash', ['type' => 'success', 'message' => 'Producto actualizado correctamente']);
        $this->redirect(AppConfig::url('/admin/productos'));
    }
    
    public function deleteProduct(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Token CSRF inválido']);
            $this->back();
        }
        
        $this->productModel->delete($id);
        Session::flash('flash', ['type' => 'success', 'message' => 'Producto eliminado correctamente']);
        $this->redirect(AppConfig::url('/admin/productos'));
    }
    
    // Categories
    public function categories(): void
    {
        $categories = $this->categoryModel->all();
        
        $this->view('admin/categories/index', [
            'title' => 'Gestión de categorías',
            'categories' => $categories
        ]);
    }
    
    public function storeCategory(): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Token CSRF inválido']);
            $this->back();
        }
        
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        
        if (empty($data['name']) || empty($data['slug'])) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Nombre y slug son requeridos']);
            $this->back();
        }
        
        $this->categoryModel->create($data);
        Session::flash('flash', ['type' => 'success', 'message' => 'Categoría creada correctamente']);
        $this->redirect(AppConfig::url('/admin/categorias'));
    }
    
    public function updateCategory(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Token CSRF inválido']);
            $this->back();
        }
        
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
        
        if (empty($data['name']) || empty($data['slug'])) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Nombre y slug son requeridos']);
            $this->back();
        }
        
        $this->categoryModel->update($id, $data);
        Session::flash('flash', ['type' => 'success', 'message' => 'Categoría actualizada correctamente']);
        $this->redirect(AppConfig::url('/admin/categorias'));
    }
    
    public function deleteCategory(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Token CSRF inválido']);
            $this->back();
        }
        
        $this->categoryModel->delete($id);
        Session::flash('flash', ['type' => 'success', 'message' => 'Categoría eliminada correctamente']);
        $this->redirect(AppConfig::url('/admin/categorias'));
    }
    
    // Orders
    public function orders(int $page = 1, string $status = ''): void
    {
        $result = $this->orderModel->getAll($page, 20, $status);
        
        $this->view('admin/orders/index', [
            'title' => 'Gestión de pedidos',
            'orders' => $result['data'],
            'pagination' => $result,
            'currentStatus' => $status
        ]);
    }
    
    public function orderDetail(int $id): void
    {
        $order = $this->orderModel->findById($id);
        
        if (!$order) {
            $this->redirect(AppConfig::url('/admin/pedidos'));
        }
        
        $items = $this->orderModel->getItems($id);
        
        $this->view('admin/orders/detail', [
            'title' => 'Pedido #' . $id,
            'order' => $order,
            'items' => $items
        ]);
    }
    
    public function updateOrderStatus(int $id): void
    {
        if (!$this->validateCsrf()) {
            $this->json(['success' => false, 'message' => 'Token CSRF inválido'], 400);
        }
        
        $status = $_POST['status'] ?? '';
        
        try {
            $this->orderModel->updateStatus($id, $status);
            $this->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    // Settings
    public function settings(): void
    {
        $settings = $this->settingModel->getAll();
        
        $this->view('admin/settings/index', [
            'title' => 'Configuración',
            'settings' => $settings
        ]);
    }
    
    public function updateSettings(): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Token CSRF inválido']);
            $this->back();
        }
        
        $keys = ['store_name', 'store_email', 'store_phone', 'store_address', 'currency', 'tax_rate', 'shipping_cost', 'free_shipping_threshold'];
        
        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                $this->settingModel->set($key, trim($_POST[$key]));
            }
        }
        
        Session::flash('flash', ['type' => 'success', 'message' => 'Configuración guardada correctamente']);
        $this->redirect(AppConfig::url('/admin/configuracion'));
    }
}