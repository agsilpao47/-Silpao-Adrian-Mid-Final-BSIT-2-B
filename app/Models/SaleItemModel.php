<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleItemModel extends Model
{
    protected $table = 'sale_items';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $useTimestamps = false;
    protected $allowedFields = [
        'sale_id',
        'product_id',
        'product_name',
        'cylinder_weight',
        'quantity',
        'unit_price',
        'subtotal',
    ];
}
