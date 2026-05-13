<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\FixedPriceModel;

class Inventory extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $fixedPriceModel;
    protected $allowedUnits = ['pcs', 'box', 'ream', 'kg', 'ltr'];

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->fixedPriceModel = new FixedPriceModel();
        helper(['form', 'url', 'currency']);
    }

    private function ensureFixedPriceForProduct(object $product): void
    {
        $existing = $this->fixedPriceModel->getByProductId((int) $product->id);
        if ($existing) {
            return;
        }

        $this->fixedPriceModel->insert([
            'product_id' => (int) $product->id,
            'buying_price' => (float) ($product->buying_price ?? 0),
            'selling_price' => (float) ($product->selling_price ?? 0),
        ]);
    }

    private function getFixedPriceMap(): array
    {
        $map = [];
        foreach ($this->fixedPriceModel->findAll() as $row) {
            $map[(int) $row->product_id] = $row;
        }
        return $map;
    }

    private function requireLogin()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }
        if (!session()->get('storeType')) {
            return redirect()->to('/store/select')->with('error', 'Please select a store type first.');
        }

        return null;
    }

    private function validateCategory(?string $categoryName): bool
    {
        if ($categoryName === null || $categoryName === '') {
            return true;
        }

        $categories = $this->categoryModel->getAllCategories();
        foreach ($categories as $category) {
            if ($category->category_name === $categoryName) {
                return true;
            }
        }

        return false;
    }

    private function validateBusinessRules(array $postData): array
    {
        $errors = [];

        if (
            isset($postData['buying_price'], $postData['selling_price'])
            && (float) $postData['selling_price'] < (float) $postData['buying_price']
        ) {
            $errors['selling_price'] = 'Selling price must be greater than or equal to buying price.';
        }

        if (!$this->validateCategory($postData['category'] ?? null)) {
            $errors['category'] = 'Selected category is invalid.';
        }

        if (
            !empty($postData['unit'])
            && !in_array($postData['unit'], $this->allowedUnits, true)
        ) {
            $errors['unit'] = 'Selected unit is invalid.';
        }

        if (
            isset($postData['category'], $postData['unit'])
            && strtolower((string) $postData['category']) === 'lpg'
            && strtolower((string) $postData['unit']) !== 'kg'
        ) {
            $errors['unit'] = 'For LPG category, unit must be kg.';
        }

        if (session()->get('storeType') === 'lpg' && empty($postData['cylinder_weight'])) {
            $errors['cylinder_weight'] = 'Cylinder weight is required for LPG store items.';
        }

        return $errors;
    }

    /**
     * Display all products (Dashboard)
     */
    public function index()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $products = $this->productModel->getAllProducts();
        foreach ($products as $product) {
            $this->ensureFixedPriceForProduct($product);
        }

        $data = [
            'title' => 'Dashboard',
            'products' => $products,
            'totalProducts' => $this->productModel->countAll(),
            'lowStock' => $this->productModel->getLowStock(10),
            'fixedPriceMap' => $this->getFixedPriceMap(),
        ];

        return view('inventory/index', $data);
    }

    /**
     * Show add product form
     */
    public function add()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $data = [
            'title' => 'Inventory Stock',
            'categories' => $this->categoryModel->getAllCategories()
        ];

        if (strtolower($this->request->getMethod()) === 'post') {
            $postData = $this->request->getPost();
            $fixedBuying = (float) ($this->request->getPost('fixed_buying_price') ?? 0);
            $fixedSelling = (float) ($this->request->getPost('fixed_selling_price') ?? 0);
            unset($postData['fixed_buying_price'], $postData['fixed_selling_price']);
            $postData['buying_price'] = $fixedBuying;
            $postData['selling_price'] = $fixedSelling;
            $businessErrors = $this->validateBusinessRules($postData);

            if ($fixedBuying < 0 || $fixedSelling < 0) {
                $businessErrors['fixed_price'] = 'Fixed prices cannot be negative.';
            } elseif ($fixedSelling < $fixedBuying) {
                $businessErrors['fixed_price'] = 'Fixed selling price must be greater than or equal to fixed buying price.';
            }

            if (!empty($businessErrors)) {
                $data['errors'] = $businessErrors;
            } elseif ($this->productModel->insert($postData)) {
                $newId = (int) $this->productModel->getInsertID();
                $this->fixedPriceModel->insert([
                    'product_id' => $newId,
                    'buying_price' => $fixedBuying,
                    'selling_price' => $fixedSelling,
                ]);
                return redirect()->to('/stock/inventory')->with('success', 'Product added successfully!');
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
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/stock/inventory')->with('error', 'Product not found!');
        }

        $data = [
            'title' => 'Edit Product',
            'product' => $product,
            'categories' => $this->categoryModel->getAllCategories(),
            'fixedPrice' => $this->fixedPriceModel->getByProductId((int) $product->id),
        ];

        if (strtolower($this->request->getMethod()) === 'post') {
            $postData = $this->request->getPost();
            $postData['id'] = $id;
            unset($postData['buying_price'], $postData['selling_price']);
            $postData['buying_price'] = (float) ($product->buying_price ?? 0);
            $postData['selling_price'] = (float) ($product->selling_price ?? 0);
            $businessErrors = $this->validateBusinessRules($postData);

            if (!empty($businessErrors)) {
                $data['errors'] = $businessErrors;
            } elseif ($this->productModel->update($id, $postData)) {
                return redirect()->to('/stock/inventory')->with('success', 'Product updated successfully!');
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
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/stock/inventory')->with('error', 'Product not found!');
        }

        // Calculate profit margin
        $sellingPrice = (float) ($product->selling_price ?? 0);
        $buyingPrice = (float) ($product->buying_price ?? 0);
        $profit = $sellingPrice - $buyingPrice;
        $margin = $buyingPrice > 0 ? ($profit / $buyingPrice) * 100 : 0;

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
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/stock/inventory')->with('error', 'Invalid request method.');
        }

        $product = $this->productModel->find($id);

        if (!$product) {
            return redirect()->to('/stock/inventory')->with('error', 'Product not found!');
        }

        $this->productModel->delete($id);
        return redirect()->to('/stock/inventory')->with('success', 'Product deleted successfully!');
    }

    /**
     * Search products
     */
    public function search()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $keyword = trim((string) $this->request->getGet('keyword'));
        $products = $keyword === '' ? $this->productModel->getAllProducts() : $this->productModel->searchProducts($keyword);
        foreach ($products as $product) {
            $this->ensureFixedPriceForProduct($product);
        }

        $data = [
            'title' => 'Search Results',
            'products' => $products,
            'totalProducts' => count($products),
            'lowStock' => [],
            'keyword' => $keyword,
            'fixedPriceMap' => $this->getFixedPriceMap(),
        ];

        return view('inventory/index', $data);
    }

    public function fixedPrices()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $products = $this->productModel->orderBy('product_name', 'ASC')->findAll();
        foreach ($products as $product) {
            $this->ensureFixedPriceForProduct($product);
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $productIds = $this->request->getPost('product_id');
            $buyingPrices = $this->request->getPost('buying_price');
            $sellingPrices = $this->request->getPost('selling_price');

            if (!is_array($productIds) || !is_array($buyingPrices) || !is_array($sellingPrices)) {
                return redirect()->back()->with('error', 'Invalid fixed price submission.');
            }

            $db = \Config\Database::connect();
            $db->transStart();

            foreach ($productIds as $idx => $productIdRaw) {
                $productId = (int) $productIdRaw;
                $buying = (float) ($buyingPrices[$idx] ?? 0);
                $selling = (float) ($sellingPrices[$idx] ?? 0);

                if ($productId <= 0 || $buying < 0 || $selling < $buying) {
                    continue;
                }

                $existing = $this->fixedPriceModel->getByProductId($productId);
                if ($existing) {
                    $this->fixedPriceModel->update($existing->id, [
                        'buying_price' => $buying,
                        'selling_price' => $selling,
                    ]);
                } else {
                    $this->fixedPriceModel->insert([
                        'product_id' => $productId,
                        'buying_price' => $buying,
                        'selling_price' => $selling,
                    ]);
                }

                $this->productModel->update($productId, [
                    'buying_price' => $buying,
                    'selling_price' => $selling,
                ]);
            }

            $db->transComplete();
            if (!$db->transStatus()) {
                return redirect()->back()->with('error', 'Failed to update fixed prices.');
            }

            return redirect()->to('/stock/fixed-prices')->with('success', 'Fixed prices updated successfully.');
        }

        $data = [
            'title' => 'Fixed Prices',
            'fixedPrices' => $this->fixedPriceModel->getAllWithProduct(),
        ];

        return view('inventory/fixed_prices', $data);
    }
}