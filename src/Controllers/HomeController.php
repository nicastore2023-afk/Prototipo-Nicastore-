<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\Product;
use Nicastore\Models\Category;
use Nicastore\Config\AppConfig;

class HomeController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    public function index(): void
    {
        $featuredProducts = $this->productModel->getFeatured(8);
        $categories = $this->categoryModel->getWithProductCount();
        $newProducts = $this->productModel->query(
            "SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC LIMIT 8"
        )->fetchAll();
        
        $this->view('home', [
            'title' => 'Nicastore - Tu tienda online',
            'featuredProducts' => $featuredProducts,
            'newProducts' => $newProducts,
            'categories' => $categories
        ]);
    }
    
    public function search(): void
    {
        $query = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = AppConfig::productsPerPage();
        
        $results = [];
        $pagination = null;
        
        if ($query) {
            $searchResult = $this->productModel->search($query, $page, $perPage);
            $results = $searchResult['data'];
            $pagination = $searchResult;
        }
        
        $this->view('products/search', [
            'title' => $query ? "Resultados para: {$query}" : 'Buscar productos',
            'query' => $query,
            'products' => $results,
            'pagination' => $pagination
        ]);
    }
}