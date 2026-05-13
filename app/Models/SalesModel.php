<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useAutoIncrement = true;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $allowedFields = [
        'invoice_no',
        'sale_date',
        'customer_name',
        'notes',
        'total_amount',
        'created_by',
    ];

    protected $validationRules = [
        'invoice_no' => 'required|max_length[50]|is_unique[sales.invoice_no,id,{id}]',
        'sale_date' => 'required',
        'customer_name' => 'permit_empty|max_length[150]',
        'notes' => 'permit_empty|max_length[500]',
        'total_amount' => 'required|numeric|greater_than[0]',
    ];
}
