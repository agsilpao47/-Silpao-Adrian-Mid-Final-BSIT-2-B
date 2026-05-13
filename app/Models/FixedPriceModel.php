<?php

namespace App\Models;

use CodeIgniter\Model;

class FixedPriceModel extends Model
{
    protected $table = 'fixed_prices';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'product_id',
        'buying_price',
        'selling_price',
    ];

    protected $validationRules = [
        'product_id' => 'required|is_natural_no_zero|is_unique[fixed_prices.product_id,id,{id}]',
        'buying_price' => 'required|numeric|greater_than_equal_to[0]',
        'selling_price' => 'required|numeric|greater_than_equal_to[0]',
    ];

    public function getAllWithProduct(): array
    {
        return $this->select('fixed_prices.*, products.product_name, products.product_code, products.category, products.cylinder_weight')
            ->join('products', 'products.id = fixed_prices.product_id', 'inner')
            ->orderBy('products.product_name', 'ASC')
            ->findAll();
    }

    public function getByProductId(int $productId): ?object
    {
        $row = $this->where('product_id', $productId)->first();
        return $row ?: null;
    }
}
