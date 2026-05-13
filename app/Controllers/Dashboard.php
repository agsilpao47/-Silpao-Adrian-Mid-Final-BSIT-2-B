<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SalesModel;
use App\Models\SaleItemModel;

class Dashboard extends BaseController
{
    protected $productModel;
    protected $salesModel;
    protected $saleItemModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->salesModel = new SalesModel();
        $this->saleItemModel = new SaleItemModel();
        helper(['form', 'url', 'currency']);
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

    private function applyPresetDates(string $preset, string &$dateFrom, string &$dateTo): void
    {
        if ($preset === '') {
            return;
        }

        $today = date('Y-m-d');
        if ($preset === 'today') {
            $dateFrom = $today;
            $dateTo = $today;
            return;
        }
        if ($preset === 'week') {
            $dateFrom = date('Y-m-d', strtotime('monday this week'));
            $dateTo = date('Y-m-d', strtotime('sunday this week'));
            return;
        }
        if ($preset === 'month') {
            $dateFrom = date('Y-m-01');
            $dateTo = date('Y-m-t');
            return;
        }
        if ($preset === 'year') {
            $dateFrom = date('Y-01-01');
            $dateTo = date('Y-12-31');
        }
    }

    public function index()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        // Get date range from request
        $preset = trim((string) $this->request->getGet('preset'));
        $dateFrom = trim((string) $this->request->getGet('date_from'));
        $dateTo = trim((string) $this->request->getGet('date_to'));
        
        // Initialize variables to prevent undefined errors
        $preset = $preset ?: '';
        $dateFrom = $dateFrom ?: '';
        $dateTo = $dateTo ?: '';
        
        // Apply preset dates if selected
        $this->applyPresetDates($preset, $dateFrom, $dateTo);
        
        // Build query for income calculation
        $incomeQuery = $this->salesModel;
        if ($dateFrom !== '') {
            $incomeQuery->where('DATE(sale_date) >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $incomeQuery->where('DATE(sale_date) <=', $dateTo);
        }
        
        $incomeData = $incomeQuery
            ->selectSum('total_amount')
            ->get()
            ->getRow();

        $totalIncome = (float) ($incomeData->total_amount ?? 0);

        // Get inventory summary by category
        $inventorySummary = $this->productModel
            ->select('category, COUNT(*) as count, SUM(quantity) as total_qty')
            ->groupBy('category')
            ->get()
            ->getResult();

        $productPricing = $this->productModel
            ->select('id, product_name, product_code, buying_price, selling_price')
            ->orderBy('product_name', 'ASC')
            ->findAll();

        $categoryPricing = $this->productModel
            ->select('category, COUNT(*) as products_count, AVG(buying_price) as avg_buying_price, AVG(selling_price) as avg_selling_price')
            ->groupBy('category')
            ->get()
            ->getResult();

        $data = [
            'title' => 'Dashboard',
            'totalIncome' => $totalIncome,
            'inventorySummary' => $inventorySummary,
            'productPricing' => $productPricing,
            'categoryPricing' => $categoryPricing,
            'preset' => $preset,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];

        return view('dashboard/index', $data);
    }
}
