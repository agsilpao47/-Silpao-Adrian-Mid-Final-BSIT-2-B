<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'product_code',
        'product_name',
        'category',
        'cylinder_weight',
        'description',
        'quantity',
        'unit',
        'buying_price',
        'selling_price',
        'supplier'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'product_code' => 'required|is_unique[products.product_code,id,{id}]',
        'product_name' => 'required|min_length[3]|max_length[150]',
        'category' => 'permit_empty|max_length[100]',
        'cylinder_weight' => 'permit_empty|in_list[2.7kg,5kg,7kg,11kg,22kg,50kg]',
        'description' => 'permit_empty|max_length[500]',
        'quantity' => 'required|numeric|greater_than_equal_to[0]',
        'unit' => 'permit_empty|in_list[pcs,box,ream,kg,ltr,crates]',
        'buying_price' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'selling_price' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'supplier' => 'permit_empty|max_length[150]'
    ];

    protected $validationMessages = [
        'product_code' => [
            'required' => 'Product code is required',
            'is_unique' => 'This product code already exists'
        ],
        'product_name' => [
            'required' => 'Product name is required'
        ],
        'selling_price' => [
            'greater_than_equal_to' => 'Selling price cannot be negative.'
        ]
    ];

    /**
     * Get all products
     */
    public function getAllProducts()
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get low stock products
     */
    public function getLowStock($threshold = 10)
    {
        return $this->where('quantity <', $threshold)->findAll();
    }

    /**
     * Search products
     */
    public function searchProducts($keyword)
    {
        return $this->like('product_name', $keyword)
                    ->orLike('product_code', $keyword)
                    ->orLike('category', $keyword)
                    ->orLike('supplier', $keyword)
                    ->findAll();
    }

    /**
     * Get products by category
     */
    public function getByCategory($category)
    {
        return $this->where('category', $category)->findAll();
    }
}