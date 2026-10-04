<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\Product;
use Nicastore\Models\Category;
use Nicastore\Config\AppConfig;

class ProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    public function index(int $page = 1): void
    {
        $perPage = AppConfig::productsPerPage();
        $result = $this->productModel->paginate($page, $perPage);
        $categories = $this->categoryModel->getActive();
        
        $this->view('products/index', [
            'title' => 'Todos los productos',
            'products' => $result['data'],
            'pagination' => $result,
            'categories' => $categories
        ]);
    }
    
    public function show(string $slug): void
    {
        $product = $this->productModel->findBySlug($slug);
        
        if (!$product) {
            $this->redirect(AppConfig::url('/productos'));
        }
        
        $relatedProducts = $this->productModel->getRelated($product['id'], $product['category_id'], 4);
        $categories = $this->categoryModel->getActive();
        
        $this->view('products/show', [
            'title' => $product['name'],
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'categories' => $categories
        ]);
    }
    
    public function category(string $slug, int $page = 1): void
    {
        $category = $this->categoryModel->findBySlug($slug);
        
        if (!$category) {
            $this->redirect(AppConfig::url('/productos'));
        }
        
        $perPage = AppConfig::productsPerPage();
        $result = $this->categoryModel->getProducts($category['id'], $page, $perPage);
        $categories = $this->categoryModel->getActive();
        
        $this->view('products/category', [
            'title' => $category['name'],
            'category' => $category,
            'products' => $result['data'],
            'pagination' => $result,
            'categories' => $categories
        ]);
    }
}