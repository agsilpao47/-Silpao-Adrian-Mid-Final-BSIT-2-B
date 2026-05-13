<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'customer_name',
        'product_bought',
        'product_category',
        'quantity',
        'price',
        'purchase_date',
        'notes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'customer_name' => 'required|min_length[2]|max_length[150]',
        'product_bought' => 'required|min_length[2]|max_length[255]',
        'product_category' => 'required|max_length[100]',
        'quantity' => 'required|integer|greater_than[0]',
        'price' => 'required|numeric|greater_than_equal_to[0]',
        'purchase_date' => 'required',
        'notes' => 'permit_empty|max_length[500]'
    ];

    protected $validationMessages = [
        'customer_name' => [
            'required' => 'Customer name is required',
            'min_length' => 'Customer name must be at least 2 characters long'
        ],
        'product_bought' => [
            'required' => 'Product name is required'
        ],
        'product_category' => [
            'required' => 'Product category is required'
        ],
        'quantity' => [
            'required' => 'Quantity is required',
            'greater_than' => 'Quantity must be greater than 0'
        ],
        'price' => [
            'required' => 'Price is required',
            'greater_than_equal_to' => 'Price cannot be negative'
        ]
    ];

    /**
     * Get all customer records
     */
    public function getAllCustomers()
    {
        return $this->orderBy('purchase_date', 'DESC')->findAll();
    }

    /**
     * Get customer records by customer name
     */
    public function getCustomerByName($customerName)
    {
        return $this->where('customer_name', $customerName)->findAll();
    }

    /**
     * Search customer records
     */
    public function searchCustomers($keyword)
    {
        return $this->like('customer_name', $keyword)
                    ->orLike('product_bought', $keyword)
                    ->orLike('product_category', $keyword)
                    ->orderBy('purchase_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get customer summary statistics
     */
    public function getCustomerSummary()
    {
        return $this->select('MIN(id) as id, customer_name, COUNT(*) as total_purchases, SUM(quantity) as total_quantity, SUM(price * quantity) as total_spent')
                    ->groupBy('customer_name')
                    ->orderBy('total_spent', 'DESC')
                    ->findAll();
    }

    /**
     * Get category summary
     */
    public function getCategorySummary()
    {
        return $this->select('product_category, COUNT(*) as total_sales, SUM(quantity) as total_quantity, SUM(price * quantity) as total_revenue')
                    ->groupBy('product_category')
                    ->orderBy('total_revenue', 'DESC')
                    ->findAll();
    }

    /**
     * Clear all customer records
     */
    public function clearAllRecords()
    {
        return $this->emptyTable();
    }
}
