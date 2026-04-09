<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->helper(array('url', 'form'));
        $this->load->library('form_validation');
    }

    /**
     * Index - Display all products
     */
    public function index() {
        $data['title'] = 'Inventory Dashboard';
        $data['products'] = $this->product_model->get_all_products();
        $data['total_products'] = $this->product_model->count_products();
        $data['low_stock'] = $this->product_model->get_low_stock(10);
        
        $this->load->view('templates/header', $data);
        $this->load->view('inventory/index', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Add new product
     */
    public function add() {
        $data['title'] = 'Add New Product';
        $data['categories'] = $this->product_model->get_categories();
        
        // Form validation rules
        $this->form_validation->set_rules('product_code', 'Product Code', 'required|is_unique[products.product_code]');
        $this->form_validation->set_rules('product_name', 'Product Name', 'required|min_length[3]');
        $this->form_validation->set_rules('quantity', 'Quantity', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('buying_price', 'Buying Price', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('selling_price', 'Selling Price', 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('inventory/add', $data);
            $this->load->view('templates/footer');
        } else {
            $product_data = array(
                'product_code' => $this->input->post('product_code'),
                'product_name' => $this->input->post('product_name'),
                'category' => $this->input->post('category'),
                'description' => $this->input->post('description'),
                'quantity' => $this->input->post('quantity'),
                'unit' => $this->input->post('unit'),
                'buying_price' => $this->input->post('buying_price'),
                'selling_price' => $this->input->post('selling_price'),
                'supplier' => $this->input->post('supplier')
            );

            $this->product_model->insert_product($product_data);
            $this->session->set_flashdata('success', 'Product added successfully!');
            redirect('inventory');
        }
    }

    /**
     * Edit product
     */
    public function edit($id) {
        $data['title'] = 'Edit Product';
        $data['product'] = $this->product_model->get_product_by_id($id);
        $data['categories'] = $this->product_model->get_categories();

        if (empty($data['product'])) {
            show_404();
        }

        // Form validation rules
        $this->form_validation->set_rules('product_name', 'Product Name', 'required|min_length[3]');
        $this->form_validation->set_rules('quantity', 'Quantity', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('buying_price', 'Buying Price', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('selling_price', 'Selling Price', 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('inventory/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $product_data = array(
                'product_name' => $this->input->post('product_name'),
                'category' => $this->input->post('category'),
                'description' => $this->input->post('description'),
                'quantity' => $this->input->post('quantity'),
                'unit' => $this->input->post('unit'),
                'buying_price' => $this->input->post('buying_price'),
                'selling_price' => $this->input->post('selling_price'),
                'supplier' => $this->input->post('supplier')
            );

            $this->product_model->update_product($id, $product_data);
            $this->session->set_flashdata('success', 'Product updated successfully!');
            redirect('inventory');
        }
    }

    /**
     * Delete product
     */
    public function delete($id) {
        $product = $this->product_model->get_product_by_id($id);
        
        if (empty($product)) {
            show_404();
        }

        $this->product_model->delete_product($id);
        $this->session->set_flashdata('success', 'Product deleted successfully!');
        redirect('inventory');
    }

    /**
     * View product details
     */
    public function view($id) {
        $data['title'] = 'Product Details';
        $data['product'] = $this->product_model->get_product_by_id($id);

        if (empty($data['product'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('inventory/view', $data);
        $this->load->view('templates/footer');
    }

    /**
     * Search products
     */
    public function search() {
        $keyword = $this->input->get('keyword');
        $data['title'] = 'Search Results';
        $data['products'] = $this->product_model->search_products($keyword);
        $data['keyword'] = $keyword;

        $this->load->view('templates/header', $data);
        $this->load->view('inventory/index', $data);
        $this->load->view('templates/footer');
    }
}