<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $allowedFields = [
        'category_name',
        'description'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    /**
     * Get all categories
     */
    public function getAllCategories()
    {
        return $this->orderBy('category_name', 'ASC')->findAll();
    }
}