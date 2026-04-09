<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use CodeIgniter\Controller;

class Inventory extends Controller
{
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        helper(['form', 'url']);
    }

    /**
     * Display all products (Dashboard)
     */
    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'products' => $this->productModel->getAllProducts(),
            'totalProducts' => $this->productModel->countAll(),
            'lowStock' => $this->productModel->getLowStock(10)
        ];

        return view('inventory/index', $data);
    }

    /**
     * Show add product form
     */
    public function add()
    {
        $data = [
            'title' => 'Add New Product',
            'categories' => $this->categoryModel->getAllCategories()
        ];

        if ($this->request->getMethod() === 'POST') {
            $postData = $this->request->getPost();
            
            if ($this->productModel->insert($postData)) {
                return redirect()->to('/inventory')->with('success', 'Product added successfully!');
            } else {
                $data['errors'] = $this->productModel->errors();
            }
        }

        return view('inventory/add', $data);
    }

    /**
     * Show edit product form
     */
    public function edit($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/inventory')->with('error', 'Product not found!');
        }

        $data = [
            'title' => 'Edit Product',
            'product' => $product,
            'categories' => $this->categoryModel->getAllCategories()
        ];

        if ($this->request->getMethod() === 'POST') {
            $postData = $this->request->getPost();
            
            if ($this->productModel->update($id, $postData)) {
                return redirect()->to('/inventory')->with('success', 'Product updated successfully!');
            } else {
                $data['errors'] = $this->productModel->errors();
            }
        }

        return view('inventory/edit', $data);
    }

    /**
     * View product details
     */
    public function view($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/inventory')->with('error', 'Product not found!');
        }

        // Calculate profit margin
        $profit = $product->selling_price - $product->buying_price;
        $margin = $product->buying_price > 0 ? ($profit / $product->buying_price) * 100 : 0;

        $data = [
            'title' => 'Product Details',
            'product' => $product,
            'profit' => $profit,
            'margin' => $margin
        ];

        return view('inventory/view', $data);
    }

    /**
     * Delete product
     */
    public function delete($id = null)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/inventory')->with('error', 'Product not found!');
        }

        $this->productModel->delete($id);
        return redirect()->to('/inventory')->with('success', 'Product deleted successfully!');
    }

    /**
     * Search products
     */
    public function search()
    {
        $keyword = $this->request->getGet('keyword');

        $data = [
            'title' => 'Search Results',
            'products' => $this->productModel->searchProducts($keyword),
            'totalProducts' => $this->productModel->countAll(),
            'lowStock' => [],
            'keyword' => $keyword
        ];

        return view('inventory/index', $data);
    }
}