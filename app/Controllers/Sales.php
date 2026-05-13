<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SalesModel;
use App\Models\SaleItemModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Sales extends BaseController
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

    private function generateInvoiceNo(): string
    {
        return 'INV-' . date('Ymd-His') . '-' . random_int(100, 999);
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

        $dateFrom = trim((string) $this->request->getGet('date_from'));
        $dateTo = trim((string) $this->request->getGet('date_to'));
        $preset = trim((string) $this->request->getGet('preset'));
        $this->applyPresetDates($preset, $dateFrom, $dateTo);

        $builder = $this->salesModel->orderBy('id', 'DESC');
        if ($dateFrom !== '') {
            $builder->where('DATE(sale_date) >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $builder->where('DATE(sale_date) <=', $dateTo);
        }

        $sumBuilder = \Config\Database::connect()->table('sales');
        if ($dateFrom !== '') {
            $sumBuilder->where('DATE(sale_date) >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $sumBuilder->where('DATE(sale_date) <=', $dateTo);
        }
        $sumRow = $sumBuilder->selectSum('total_amount')->get()->getRow();
        $totalSalesAmount = (float) ($sumRow->total_amount ?? 0);

        $customerSummaryBuilder = \Config\Database::connect()->table('sales')
            ->select("COALESCE(NULLIF(customer_name, ''), 'Walk-in Customer') as customer_name, COUNT(*) as total_transactions, SUM(total_amount) as total_amount")
            ->groupBy("COALESCE(NULLIF(customer_name, ''), 'Walk-in Customer')")
            ->orderBy('total_amount', 'DESC');
        if ($dateFrom !== '') {
            $customerSummaryBuilder->where('DATE(sale_date) >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $customerSummaryBuilder->where('DATE(sale_date) <=', $dateTo);
        }
        $customerSummary = $customerSummaryBuilder->get()->getResult();

        $data = [
            'title' => 'Sales Records',
            'sales' => $builder->findAll(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'preset' => $preset,
            'totalSalesAmount' => $totalSalesAmount,
            'customerSummary' => $customerSummary,
        ];

        return view('sales/index', $data);
    }

    public function create()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $data = [
            'title' => 'Create Sales Record',
            'products' => $this->productModel->orderBy('product_name', 'ASC')->findAll(),
            'invoiceNo' => $this->generateInvoiceNo(),
            'saleDate' => date('Y-m-d\TH:i'),
        ];

        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('sales/create', $data);
        }

        $productIds = $this->request->getPost('product_id');
        $quantities = $this->request->getPost('quantity');
        $customerName = trim((string) $this->request->getPost('customer_name'));
        $notes = trim((string) $this->request->getPost('notes'));
        $saleDateInput = (string) $this->request->getPost('sale_date');
        $saleDate = $saleDateInput !== '' ? $saleDateInput : date('Y-m-d H:i:s');

        if (!is_array($productIds) || !is_array($quantities) || count($productIds) === 0) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one item.');
        }

        $lineItems = [];
        $totalAmount = 0.0;

        foreach ($productIds as $index => $productIdRaw) {
            $productId = (int) $productIdRaw;
            $quantity = isset($quantities[$index]) ? (int) $quantities[$index] : 0;

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $product = $this->productModel->find($productId);
            if (!$product) {
                return redirect()->back()->withInput()->with('error', 'One of the selected products is invalid.');
            }

            if ((int) $product->quantity < $quantity) {
                return redirect()->back()->withInput()->with('error', 'Insufficient stock for ' . $product->product_name . '.');
            }

            $unitPrice = (float) $product->selling_price;
            $subtotal = $unitPrice * $quantity;
            $totalAmount += $subtotal;

            $lineItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];
        }

        if (empty($lineItems)) {
            return redirect()->back()->withInput()->with('error', 'Please add valid sale items.');
        }

        $invoiceNo = $this->generateInvoiceNo();
        $db = \Config\Database::connect();
        $db->transStart();

        $salePayload = [
            'invoice_no' => $invoiceNo,
            'sale_date' => $saleDate,
            'customer_name' => $customerName,
            'notes' => $notes,
            'total_amount' => $totalAmount,
            'created_by' => (int) session()->get('userId'),
        ];

        $this->salesModel->insert($salePayload);
        $saleId = $this->salesModel->getInsertID();

        foreach ($lineItems as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];

            $this->saleItemModel->insert([
                'sale_id' => $saleId,
                'product_id' => (int) $product->id,
                'product_name' => $product->product_name,
                'cylinder_weight' => $product->cylinder_weight,
                'quantity' => $quantity,
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
            ]);

            $this->productModel->update($product->id, [
                'quantity' => ((int) $product->quantity) - $quantity,
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Failed to save sales record. Try again.');
        }

        return redirect()->to('/sales')->with('success', 'Sales record created successfully.');
    }

    public function view($id = null)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $sale = $this->salesModel->find($id);
        if (!$sale) {
            return redirect()->to('/sales')->with('error', 'Sale record not found.');
        }

        $items = $this->saleItemModel->where('sale_id', $id)->findAll();

        return view('sales/view', [
            'title' => 'View Sale Details',
            'sale' => $sale,
            'items' => $items,
        ]);
    }

    public function print($id = null)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $sale = $this->salesModel->find($id);
        if (!$sale) {
            return redirect()->to('/sales')->with('error', 'Sale record not found.');
        }

        $items = $this->saleItemModel->where('sale_id', $id)->findAll();

        return view('sales/print', [
            'title' => 'Print Invoice',
            'sale' => $sale,
            'items' => $items,
            'printedBy' => (string) session()->get('username'),
        ]);
    }

    public function pdf($id = null)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $sale = $this->salesModel->find($id);
        if (!$sale) {
            return redirect()->to('/sales')->with('error', 'Sale record not found.');
        }

        $items = $this->saleItemModel->where('sale_id', $id)->findAll();

        $html = view('sales/pdf', [
            'sale' => $sale,
            'items' => $items,
            'printedBy' => (string) session()->get('username'),
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'invoice-' . ($sale->invoice_no ?? 'unknown') . '.pdf';
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody((string) $dompdf->output());
    }

    public function customer()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $customer = trim((string) $this->request->getGet('customer'));
        $dateFrom = trim((string) $this->request->getGet('date_from'));
        $dateTo = trim((string) $this->request->getGet('date_to'));
        $preset = trim((string) $this->request->getGet('preset'));
        $this->applyPresetDates($preset, $dateFrom, $dateTo);

        $isWalkIn = $customer === '' || $customer === '__walkin__' || strtolower($customer) === 'walk-in customer';
        $builder = $this->salesModel->orderBy('sale_date', 'DESC');
        if ($isWalkIn) {
            $builder->groupStart()
                ->where('customer_name', '')
                ->orWhere('customer_name', null)
                ->groupEnd();
        } else {
            $builder->where('customer_name', $customer);
        }
        if ($dateFrom !== '') {
            $builder->where('DATE(sale_date) >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $builder->where('DATE(sale_date) <=', $dateTo);
        }
        $sales = $builder->findAll();

        $saleDetails = [];
        foreach ($sales as $sale) {
            $saleDetails[(int) $sale->id] = $this->saleItemModel->where('sale_id', (int) $sale->id)->findAll();
        }

        return view('sales/customer', [
            'title' => 'Customer Purchase Details',
            'customer' => $isWalkIn ? 'Walk-in Customer' : $customer,
            'sales' => $sales,
            'saleDetails' => $saleDetails,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'preset' => $preset,
        ]);
    }

    public function printPeriod()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $preset = trim((string) $this->request->getGet('preset'));
        $dateFrom = trim((string) $this->request->getGet('date_from'));
        $dateTo = trim((string) $this->request->getGet('date_to'));
        $this->applyPresetDates($preset, $dateFrom, $dateTo);

        $builder = $this->salesModel->orderBy('sale_date', 'DESC');
        if ($dateFrom !== '') {
            $builder->where('DATE(sale_date) >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $builder->where('DATE(sale_date) <=', $dateTo);
        }
        $sales = $builder->findAll();
        $total = 0.0;
        foreach ($sales as $sale) {
            $total += (float) ($sale->total_amount ?? 0);
        }

        $detailed = ($preset === 'today');
        $saleItemsMap = [];
        if ($detailed) {
            foreach ($sales as $sale) {
                $saleItemsMap[(int) $sale->id] = $this->saleItemModel->where('sale_id', (int) $sale->id)->findAll();
            }
        }

        return view('sales/print_period', [
            'title' => 'Period Sales Report',
            'preset' => $preset,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sales' => $sales,
            'totalSalesAmount' => $total,
            'detailed' => $detailed,
            'saleItemsMap' => $saleItemsMap,
            'printedBy' => (string) session()->get('username'),
        ]);
    }

    public function pdfPeriod()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $preset = trim((string) $this->request->getGet('preset'));
        $dateFrom = trim((string) $this->request->getGet('date_from'));
        $dateTo = trim((string) $this->request->getGet('date_to'));
        $this->applyPresetDates($preset, $dateFrom, $dateTo);

        $builder = $this->salesModel->orderBy('sale_date', 'DESC');
        if ($dateFrom !== '') {
            $builder->where('DATE(sale_date) >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $builder->where('DATE(sale_date) <=', $dateTo);
        }
        $sales = $builder->findAll();
        $total = 0.0;
        foreach ($sales as $sale) {
            $total += (float) ($sale->total_amount ?? 0);
        }

        $detailed = ($preset === 'today');
        $saleItemsMap = [];
        if ($detailed) {
            foreach ($sales as $sale) {
                $saleItemsMap[(int) $sale->id] = $this->saleItemModel->where('sale_id', (int) $sale->id)->findAll();
            }
        }

        $html = view('sales/pdf_period', [
            'preset' => $preset,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sales' => $sales,
            'totalSalesAmount' => $total,
            'detailed' => $detailed,
            'saleItemsMap' => $saleItemsMap,
            'printedBy' => (string) session()->get('username'),
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'sales-report-' . ($preset ?: 'custom') . '.pdf';
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody((string) $dompdf->output());
    }

    public function delete($id = null)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $sale = $this->salesModel->find($id);
        if (!$sale) {
            return redirect()->to('/sales')->with('error', 'Sale record not found.');
        }

        $items = $this->saleItemModel->where('sale_id', $id)->findAll();
        
        $db = \Config\Database::connect();
        $db->transStart();

        // Restore stock quantities
        foreach ($items as $item) {
            /** @var object $product */
            $product = $this->productModel->find($item->product_id);
            if ($product && property_exists($product, 'id')) {
                $productId = (int) $product->id;
                $currentQuantity = (int) ($product->quantity ?? 0);
                $saleQuantity = (int) ($item->quantity ?? 0);
                $newQuantity = $currentQuantity + $saleQuantity;
                
                $this->productModel->update($productId, [
                    'quantity' => $newQuantity,
                ]);
            }
        }

        // Delete sale items first
        $this->saleItemModel->where('sale_id', $id)->delete();
        
        // Delete the sale record
        $this->salesModel->delete($id);

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->to('/sales')->with('error', 'Failed to delete sale record. Try again.');
        }

        return redirect()->to('/sales')->with('success', 'Sale record deleted successfully and stock restored.');
    }
}
