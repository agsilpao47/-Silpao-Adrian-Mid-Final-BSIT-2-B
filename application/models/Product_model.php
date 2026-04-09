<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Get all products
     */
    public function get_all_products($limit = NULL, $offset = NULL) {
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('products');
        return $query->result();
    }

    /**
     * Get product by ID
     */
    public function get_product_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('products');
        return $query->row();
    }

    /**
     * Get product by code
     */
    public function get_product_by_code($code) {
        $this->db->where('product_code', $code);
        $query = $this->db->get('products');
        return $query->row();
    }

    /**
     * Insert new product
     */
    public function insert_product($data) {
        $this->db->insert('products', $data);
        return $this->db->insert_id();
    }

    /**
     * Update product
     */
    public function update_product($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('products', $data);
    }

    /**
     * Delete product
     */
    public function delete_product($id) {
        $this->db->where('id', $id);
        return $this->db->delete('products');
    }

    /**
     * Count all products
     */
    public function count_products() {
        return $this->db->count_all('products');
    }

    /**
     * Search products
     */
    public function search_products($keyword) {
        $this->db->like('product_name', $keyword);
        $this->db->or_like('product_code', $keyword);
        $this->db->or_like('category', $keyword);
        $this->db->or_like('supplier', $keyword);
        $query = $this->db->get('products');
        return $query->result();
    }

    /**
     * Get products by category
     */
    public function get_products_by_category($category) {
        $this->db->where('category', $category);
        $query = $this->db->get('products');
        return $query->result();
    }

    /**
     * Get low stock products
     */
    public function get_low_stock($threshold = 10) {
        $this->db->where('quantity <', $threshold);
        $query = $this->db->get('products');
        return $query->result();
    }

    /**
     * Get all categories
     */
    public function get_categories() {
        $query = $this->db->get('categories');
        return $query->result();
    }
}