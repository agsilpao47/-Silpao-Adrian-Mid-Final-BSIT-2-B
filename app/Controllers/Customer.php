<?php

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\ProductModel;
use App\Models\SalesModel;
use App\Models\SaleItemModel;

class Customer extends BaseController
{
    protected $customerModel;
    protected $productModel;
    protected $salesModel;
    protected $saleItemModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
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

    public function index()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $search = trim((string) $this->request->getGet('search'));
        $customers = [];

        if ($search !== '') {
            $customers = $this->customerModel->searchCustomers($search);
        } else {
            $customers = $this->customerModel->getAllCustomers();
        }

        // Get customer summary
        $customerSummary = $this->customerModel->getCustomerSummary();
        $categorySummary = $this->customerModel->getCategorySummary();

        $data = [
            'title' => 'Customer Records',
            'customers' => $customers,
            'customerSummary' => $customerSummary,
            'categorySummary' => $categorySummary,
            'search' => $search,
        ];

        return view('customer/index', $data);
    }

    public function create()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $data = [
            'title' => 'Add Customer Record',
            'products' => $this->productModel->orderBy('product_name', 'ASC')->findAll(),
            'categories' => $this->productModel->select('category')->distinct()->findAll(),
        ];

        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('customer/create', $data);
        }

        // Debug: Log all POST data
        log_message('info', '=== Customer Form Submission ===');
        log_message('info', 'Raw POST data: ' . json_encode($this->request->getPost()));

        // Convert datetime-local format (Y-m-dTH:i) to database format (Y-m-d H:i:s)
        $purchaseDate = trim((string) $this->request->getPost('purchase_date'));
        log_message('info', 'Original purchase_date: ' . $purchaseDate);
        $purchaseDate = str_replace('T', ' ', $purchaseDate) . ':00';
        log_message('info', 'Converted purchase_date: ' . $purchaseDate);

        $validation = $this->validate([
    'customer_name' => 'required|min_length[2]|max_length[150]',
    'product_bought' => 'required|min_length[2]|max_length[255]',
    'product_category' => 'required|max_length[100]',
    'quantity' => 'required|integer|greater_than[0]',
    'price' => 'required|numeric|greater_than_equal_to[0]',
    'purchase_date' => 'required',
    'notes' => 'permit_empty|max_length[500]'
    ]);

        if (!$validation) {
            // Get detailed validation errors
            $errors = $this->validator->getErrors();
            log_message('error', 'Customer form validation errors: ' . json_encode($errors));
            log_message('error', 'POST data received: ' . json_encode($this->request->getPost()));
            
            return redirect()->back()->withInput()->with('error', 'Please correct the errors below.')->with('validation', $errors);
        }
$productBought = trim((string) $this->request->getPost('product_bought'));
$productCategory = trim((string) $this->request->getPost('product_category'));
$priceValue = trim((string) $this->request->getPost('price_hidden') ?: $this->request->getPost('price'));
 
// If product is selected, auto-fill category from product data
if ($productBought && $productCategory === '') {
    $selectedProduct = $this->productModel->where('product_name', $productBought)->first();
    if ($selectedProduct) {
        $productCategory = $selectedProduct->category;
        $priceValue = $selectedProduct->selling_price;
    }
}
        $customerData = [
    'customer_name' => trim((string) $this->request->getPost('customer_name')),
    'product_bought' => $productBought,
    'product_category' => $productCategory ?: trim((string) $this->request->getPost('product_category')),
    'quantity' => (int) $this->request->getPost('quantity'),
    'price' => (float) $priceValue,
    'purchase_date' => $purchaseDate,
    'notes' => trim((string) $this->request->getPost('notes')),
];

        if ($this->customerModel->insert($customerData)) {
            return redirect()->to('/customer')->with('success', 'Customer record added successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to add customer record. Please try again.');
        }
    }

    public function edit($id = null)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return redirect()->to('/customer')->with('error', 'Customer record not found.');
        }

        $data = [
            'title' => 'Edit Customer Record',
            'customer' => $customer,
            'products' => $this->productModel->orderBy('product_name', 'ASC')->findAll(),
            'categories' => $this->productModel->select('category')->distinct()->findAll(),
        ];

        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('customer/edit', $data);
        }

        $validation = $this->validate([
            'customer_name' => 'required|min_length[2]|max_length[150]',
            'product_bought' => 'required|min_length[2]|max_length[255]',
            'product_category' => 'required|max_length[100]',
            'quantity' => 'required|integer|greater_than[0]',
            'price' => 'required|numeric|greater_than_equal_to[0]',
            'purchase_date' => 'required',
            'notes' => 'permit_empty|max_length[500]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('error', 'Please correct the errors below.');
        }

        // Convert datetime-local format (Y-m-dTH:i) to database format (Y-m-d H:i:s)
        $purchaseDate = trim((string) $this->request->getPost('purchase_date'));
        $purchaseDate = str_replace('T', ' ', $purchaseDate) . ':00';

        $customerData = [
            'customer_name' => trim((string) $this->request->getPost('customer_name')),
            'product_bought' => trim((string) $this->request->getPost('product_bought')),
            'product_category' => trim((string) $this->request->getPost('product_category')),
            'quantity' => (int) $this->request->getPost('quantity'),
            'price' => (float) $this->request->getPost('price'),
            'purchase_date' => $purchaseDate,
            'notes' => trim((string) $this->request->getPost('notes')),
        ];

        if ($this->customerModel->update($id, $customerData)) {
            return redirect()->to('/customer')->with('success', 'Customer record updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update customer record. Please try again.');
        }
    }

    public function delete($id = null)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $customer = $this->customerModel->find($id);
        if (!$customer) {
            return redirect()->to('/customer')->with('error', 'Customer record not found.');
        }

        if ($this->customerModel->delete($id)) {
            return redirect()->to('/customer')->with('success', 'Customer record deleted successfully.');
        } else {
            return redirect()->to('/customer')->with('error', 'Failed to delete customer record. Please try again.');
        }
    }

    public function viewSales($customerName = null)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        if (!$customerName) {
            return redirect()->to('/customer')->with('error', 'Customer name is required.');
        }

        // Get customer records
        $customerRecords = $this->customerModel->getCustomerByName(urldecode($customerName));
        
        // Get sales records for this customer
        $sales = $this->salesModel->where('customer_name', urldecode($customerName))->orderBy('sale_date', 'DESC')->findAll();
        
        $saleDetails = [];
        foreach ($sales as $sale) {
            $saleDetails[(int) $sale->id] = $this->saleItemModel->where('sale_id', (int) $sale->id)->findAll();
        }

        $data = [
            'title' => 'Customer Sales Overview',
            'customerName' => urldecode($customerName),
            'customerRecords' => $customerRecords,
            'sales' => $sales,
            'saleDetails' => $saleDetails,
        ];

        return view('customer/sales', $data);
    }

    public function createSale()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        // Handle POST request - process sale directly
        if ($this->request->getMethod() === 'post') {
            return $this->processDirectSale();
        }

        // Handle GET request - show customer records and direct sale option
        $allCustomers = $this->customerModel->getAllCustomers();
        
        if (empty($allCustomers)) {
            return redirect()->to('/customer')->with('error', 'No customer records found to process.');
        }

        $customerSummary = $this->customerModel->getCustomerSummary();
        
        // Get detailed purchase history for each customer
        $customerPurchases = [];
        foreach ($customerSummary as $customer) {
            $customerPurchases[$customer->id] = $this->customerModel->getCustomerByName($customer->customer_name);
        }

        $data = [
            'title' => 'Process All Customer Records to Sales',
            'customers' => $customerSummary,
            'customerPurchases' => $customerPurchases,
            'allCustomers' => $allCustomers,
        ];

        return view('customer/create_sale', $data);
    }

    public function processDirectSale()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        // Get all customer records for processing
        $allCustomers = $this->customerModel->getAllCustomers();
        
        if (empty($allCustomers)) {
            return redirect()->to('/customer')->with('error', 'No customer records found to process.');
        }

        $saleDate = $this->request->getPost('sale_date') ?: date('Y-m-d H:i:s');
        $notes = trim((string) $this->request->getPost('notes'));

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create sale record for each customer group
            $processedCustomers = [];
            $totalAmount = 0.0;

            // Group customers by name for better organization
            $customerGroups = [];
            foreach ($allCustomers as $customer) {
                $customerGroups[$customer->customer_name][] = $customer;
            }

            foreach ($customerGroups as $customerName => $customers) {
                // Create sale record for this customer group
                $invoiceNo = $this->generateInvoiceNo();
                $groupTotal = 0.0;

                // Calculate total amount for this customer group
                foreach ($customers as $customer) {
                    $groupTotal += (float) $customer->price * (int) $customer->quantity;
                }
                $totalAmount += $groupTotal;

                $salePayload = [
                    'invoice_no' => $invoiceNo,
                    'sale_date' => $saleDate,
                    'customer_name' => $customerName,
                    'notes' => $notes . ' - Processed ' . count($customers) . ' purchases',
                    'total_amount' => $groupTotal,
                    'created_by' => (int) session()->get('userId'),
                ];

                $this->salesModel->insert($salePayload);
                $saleId = $this->salesModel->getInsertID();

                // Create sale items for each customer record
                foreach ($customers as $customer) {
                    // Find matching product by name
                    $product = $this->productModel->where('product_name', $customer->product_bought)->first();
                    
                    if (!$product || is_array($product)) {
                        // Create a generic product entry if not found
                        $this->saleItemModel->insert([
                            'sale_id' => $saleId,
                            'product_id' => 0,
                            'product_name' => $customer->product_bought,
                            'cylinder_weight' => 'N/A',
                            'quantity' => (int) $customer->quantity,
                            'unit_price' => (float) $customer->price,
                            'subtotal' => (float) $customer->price * (int) $customer->quantity,
                        ]);
                    } else {
                        // Check stock availability
                        if ((int) $product->quantity >= (int) $customer->quantity) {
                            // Add sale item
                            $this->saleItemModel->insert([
                                'sale_id' => $saleId,
                                'product_id' => (int) $product->id,
                                'product_name' => $product->product_name,
                                'cylinder_weight' => $product->cylinder_weight,
                                'quantity' => (int) $customer->quantity,
                                'unit_price' => (float) $customer->price,
                                'subtotal' => (float) $customer->price * (int) $customer->quantity,
                            ]);

                            // Reduce product stock
                            $this->productModel->update($product->id, [
                                'quantity' => ((int) $product->quantity) - (int) $customer->quantity,
                            ]);
                        } else {
                            throw new \Exception('Insufficient stock for ' . $customer->product_bought . '. Available: ' . $product->quantity . ', Required: ' . $customer->quantity);
                        }
                    }
                    $processedCustomers[] = $customer->customer_name;
                }
            }

            $db->transComplete();

            // Clear all customer records after successful sale
            $this->customerModel->clearAllRecords();

            return redirect()->to('/customer')->with('success', 'Sale completed successfully! ' . count($allCustomers) . ' customer records processed and cleared. Total sales: ' . count($customerGroups) . ' invoices created.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Failed to process sale: ' . $e->getMessage());
        }
    }

    public function selectProducts()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        // Get all customers for processing
        $allCustomers = $this->customerModel->getAllCustomers();
        
        if (empty($allCustomers)) {
            return redirect()->to('/customer/create-sale')->with('error', 'No customer records found.');
        }

        $data = [
            'title' => 'Select Products for Sale',
            'customers' => $allCustomers,
            'products' => $this->productModel->orderBy('product_name', 'ASC')->findAll(),
        ];

        return view('customer/select_products', $data);
    }

    public function searchProducts()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $term = $this->request->getGet('term');
        $products = $this->productModel
            ->like('product_name', $term)
            ->orLike('product_code', $term)
            ->findAll();

        return $this->response->setJSON($products);
    }

    public function previewSale()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        // Check if processing all customers
        $processAll = $this->request->getPost('process_all_customers');
        
        if (!$processAll) {
            return redirect()->back()->with('error', 'Invalid request. Please process all customers.');
        }
        
        // Get all customers for processing
        $allCustomers = $this->customerModel->getAllCustomers();
        
        if (empty($allCustomers)) {
            return redirect()->back()->with('error', 'No customer records found to process.');
        }
        
        $productIds = $this->request->getPost('product_id');
        $quantities = $this->request->getPost('quantity');
        $saleDate = $this->request->getPost('sale_date');
        $notes = trim((string) $this->request->getPost('notes'));

        if (!is_array($productIds) || !is_array($quantities)) {
            return redirect()->back()->with('error', 'Invalid sale data.');
        }

        $lineItems = [];
        $totalAmount = 0.0;

        foreach ($productIds as $index => $productId) {
            $quantity = isset($quantities[$index]) ? (int) $quantities[$index] : 0;
            if ($quantity <= 0) continue;

            $product = $this->productModel->find($productId);
            if (!$product || is_array($product)) {
                return redirect()->back()->with('error', 'Product not found.');
            }

            if ((int) $product->quantity < $quantity) {
                return redirect()->back()->with('error', 'Insufficient stock for ' . $product->product_name . '. Available: ' . $product->quantity . ', Requested: ' . $quantity);
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
            return redirect()->back()->with('error', 'Please add at least one valid item.');
        }

        // Store preview data in session
        session()->set('sale_preview', [
            'process_all_customers' => true,
            'all_customers' => $allCustomers,
            'sale_date' => $saleDate ?: date('Y-m-d H:i:s'),
            'notes' => $notes,
            'line_items' => $lineItems,
            'total_amount' => $totalAmount,
        ]);

        return view('customer/preview_sale', [
            'title' => 'Sale Preview',
            'process_all_customers' => true,
            'all_customers' => $allCustomers,
            'sale_date' => $saleDate ?: date('Y-m-d H:i:s'),
            'notes' => $notes,
            'line_items' => $lineItems,
            'total_amount' => $totalAmount,
        ]);
    }

    public function confirmSale()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $preview = session()->get('sale_preview');
        if (!$preview) {
            return redirect()->to('/customer/create-sale')->with('error', 'Sale preview expired. Please try again.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create sale record
            $invoiceNo = $this->generateInvoiceNo();
            $salePayload = [
                'invoice_no' => $invoiceNo,
                'sale_date' => $preview['sale_date'],
                'customer_name' => 'All Customer Records Processed',
                'notes' => $preview['notes'],
                'total_amount' => $preview['total_amount'],
                'created_by' => (int) session()->get('userId'),
            ];

            $this->salesModel->insert($salePayload);
            $saleId = $this->salesModel->getInsertID();

            // Create sale items and reduce stock
            foreach ($preview['line_items'] as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];

                // Add sale item
                $this->saleItemModel->insert([
                    'sale_id' => $saleId,
                    'product_id' => (int) $product->id,
                    'product_name' => $product->product_name,
                    'cylinder_weight' => $product->cylinder_weight,
                    'quantity' => $quantity,
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Reduce product stock
                $this->productModel->update($product->id, [
                    'quantity' => ((int) $product->quantity) - $quantity,
                ]);
            }

            // Add customer records for all existing customers
            foreach ($preview['all_customers'] as $customer) {
                $this->customerModel->insert([
                    'customer_name' => $customer->customer_name,
                    'product_bought' => 'Bulk Sale Processing',
                    'product_category' => 'Mixed',
                    'quantity' => 1,
                    'price' => 0.00,
                    'purchase_date' => $preview['sale_date'],
                    'notes' => 'Processed in bulk sale - ' . $preview['notes'],
                ]);
            }

            $db->transComplete();

            // Clear all customer records after successful sale
            $this->customerModel->clearAllRecords();

            // Clear preview from session
            session()->remove('sale_preview');

            return redirect()->to('/customer')->with('success', 'Sale completed successfully! Stock has been reduced, customer record added, and all customer records cleared for clean slate.');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Failed to process sale: ' . $e->getMessage());
        }
    }

    public function processAllToSales()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        // Get all customer records for processing
        $allCustomers = $this->customerModel->getAllCustomers();
        
        if (empty($allCustomers)) {
            return redirect()->to('/customer')->with('error', 'No customer records found to process.');
        }

        $saleDate = $this->request->getPost('sale_date') ?: date('Y-m-d H:i:s');
        $notes = trim((string) $this->request->getPost('notes'));

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create sale record for each customer group
            $processedCustomers = [];
            $totalAmount = 0.0;

            // Group customers by name for better organization
            $customerGroups = [];
            foreach ($allCustomers as $customer) {
                $customerGroups[$customer->customer_name][] = $customer;
            }

            foreach ($customerGroups as $customerName => $customers) {
                // Create sale record for this customer group
                $invoiceNo = $this->generateInvoiceNo();
                $groupTotal = 0.0;

                // Calculate total amount for this customer group
                foreach ($customers as $customer) {
                    $groupTotal += (float) $customer->price * (int) $customer->quantity;
                }
                $totalAmount += $groupTotal;

                $salePayload = [
                    'invoice_no' => $invoiceNo,
                    'sale_date' => $saleDate,
                    'customer_name' => $customerName,
                    'notes' => $notes . ' - Processed ' . count($customers) . ' purchases',
                    'total_amount' => $groupTotal,
                    'created_by' => (int) session()->get('userId') ?: 1, // Default to 1 if session not set
                ];

                // Debug: Log the sale payload
                log_message('debug', 'Sale payload: ' . json_encode($salePayload));

                if (!$this->salesModel->insert($salePayload)) {
                    $errors = $this->salesModel->errors();
                    throw new \Exception('Failed to insert sale record: ' . json_encode($errors));
                }
                
                $saleId = $this->salesModel->getInsertID();
                
                if (!$saleId) {
                    throw new \Exception('Failed to get sale ID after insertion');
                }

                // Create sale items for each customer record
                foreach ($customers as $customer) {
                    // Find matching product by name
                    $product = $this->productModel->where('product_name', $customer->product_bought)->first();
                    
                    if (!$product || is_array($product)) {
                        // Find any existing product to use as a reference for foreign key
                        $fallbackProduct = $this->productModel->first();
                        if ($fallbackProduct) {
                            // Use existing product ID but with customer's product details
                            $saleItemData = [
                                'sale_id' => $saleId,
                                'product_id' => (int) $fallbackProduct->id,
                                'product_name' => $customer->product_bought,
                                'cylinder_weight' => 'N/A',
                                'quantity' => (int) $customer->quantity,
                                'unit_price' => (float) $customer->price,
                                'subtotal' => (float) $customer->price * (int) $customer->quantity,
                            ];
                        } else {
                            // If no products exist at all, create a dummy product first
                            $dummyProductData = [
                                'product_code' => 'DUMMY-' . uniqid(),
                                'product_name' => $customer->product_bought,
                                'category' => 'Unknown',
                                'cylinder_weight' => 'N/A',
                                'description' => 'Auto-created for sale processing',
                                'quantity' => 0,
                                'unit' => 'pcs',
                                'buying_price' => 0,
                                'selling_price' => (float) $customer->price,
                                'supplier' => 'System Generated',
                            ];
                            
                            if (!$this->productModel->insert($dummyProductData)) {
                                throw new \Exception('Failed to create dummy product for ' . $customer->product_bought);
                            }
                            
                            $dummyProductId = $this->productModel->getInsertID();
                            
                            $saleItemData = [
                                'sale_id' => $saleId,
                                'product_id' => $dummyProductId,
                                'product_name' => $customer->product_bought,
                                'cylinder_weight' => 'N/A',
                                'quantity' => (int) $customer->quantity,
                                'unit_price' => (float) $customer->price,
                                'subtotal' => (float) $customer->price * (int) $customer->quantity,
                            ];
                        }
                        
                        log_message('debug', 'Generic sale item: ' . json_encode($saleItemData));
                        
                        if (!$this->saleItemModel->insert($saleItemData)) {
                            $errors = $this->saleItemModel->errors();
                            throw new \Exception('Failed to insert generic sale item: ' . json_encode($errors));
                        }
                    } else {
                        // Check stock availability
                        if ((int) $product->quantity >= (int) $customer->quantity) {
                            // Add sale item
                            $saleItemData = [
                                'sale_id' => $saleId,
                                'product_id' => (int) $product->id,
                                'product_name' => $product->product_name,
                                'cylinder_weight' => $product->cylinder_weight,
                                'quantity' => (int) $customer->quantity,
                                'unit_price' => (float) $customer->price,
                                'subtotal' => (float) $customer->price * (int) $customer->quantity,
                            ];
                            
                            log_message('debug', 'Product sale item: ' . json_encode($saleItemData));
                            
                            if (!$this->saleItemModel->insert($saleItemData)) {
                                $errors = $this->saleItemModel->errors();
                                throw new \Exception('Failed to insert sale item: ' . json_encode($errors));
                            }

                            // Reduce product stock
                            if (!$this->productModel->update($product->id, [
                                'quantity' => ((int) $product->quantity) - (int) $customer->quantity,
                            ])) {
                                throw new \Exception('Failed to update product stock for ' . $customer->product_bought);
                            }
                        } else {
                            throw new \Exception('Insufficient stock for ' . $customer->product_bought . '. Available: ' . $product->quantity . ', Required: ' . $customer->quantity);
                        }
                    }
                    $processedCustomers[] = $customer->customer_name;
                }
            }

            // Check transaction status
            if (!$db->transStatus()) {
                throw new \Exception('Transaction failed during processing');
            }
            
            $db->transComplete();

            // Clear all customer records after successful sale
            if (!$this->customerModel->clearAllRecords()) {
                throw new \Exception('Failed to clear customer records');
            }

            return redirect()->to('/customer')->with('success', 'Sale completed successfully! ' . count($allCustomers) . ' customer records processed and cleared. Total sales: ' . count($customerGroups) . ' invoices created.');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Process sale error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process sale: ' . $e->getMessage());
        }
    }

    private function generateInvoiceNo(): string
    {
        return 'INV-' . date('Ymd-His') . '-' . random_int(100, 999);
    }
}
