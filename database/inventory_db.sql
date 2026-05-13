-- Inventory System Database
-- Created for CodeIgniter 4 Application
-- ERD Diagram: See inventory_erd.html for the Entity Relationship Diagram

CREATE DATABASE IF NOT EXISTS inventory_system;
USE inventory_system;

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_code VARCHAR(50) UNIQUE NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    cylinder_weight VARCHAR(10),
    description TEXT,
    quantity INT DEFAULT 0,
    unit VARCHAR(20),
    buying_price DECIMAL(10,2) DEFAULT 0.00,
    selling_price DECIMAL(10,2) DEFAULT 0.00,
    supplier VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table (for login)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sales Table
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(50) UNIQUE NOT NULL,
    sale_date DATETIME NOT NULL,
    customer_name VARCHAR(150),
    notes VARCHAR(500),
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_sales_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Sale Items Table
CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    cylinder_weight VARCHAR(10),
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_sale_items_sale FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    CONSTRAINT fk_sale_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- Fixed Prices Table (single source of truth for product pricing)
CREATE TABLE IF NOT EXISTS fixed_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL UNIQUE,
    buying_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    selling_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_fixed_prices_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Customers Table for Customer Records
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    product_bought VARCHAR(255) NOT NULL,
    product_category VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    purchase_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert Sample Categories
INSERT INTO categories (category_name, description) VALUES 
('LPG', 'LPG cylinders and related items');

-- Insert Default Login User
-- Username: admin
-- Password: admin123
INSERT INTO users (username, full_name, password) VALUES
('admin', 'System Administrator', SHA2('admin123', 256));

-- Insert Sample Products
INSERT INTO products (product_code, product_name, category, cylinder_weight, description, quantity, unit, buying_price, selling_price, supplier) VALUES 
('LPG-2.7', 'LPG Cylinder', 'LPG', '2.7kg', 'Household LPG cylinder 2.7kg', 80, 'pcs', 8.00, 10.50, 'LPG Main Supplier'),
('LPG-5', 'LPG Cylinder', 'LPG', '5kg', 'Household LPG cylinder 5kg', 70, 'pcs', 12.00, 15.50, 'LPG Main Supplier'),
('LPG-7', 'LPG Cylinder', 'LPG', '7kg', 'LPG cylinder 7kg', 65, 'pcs', 15.00, 19.00, 'LPG Main Supplier'),
('LPG-11', 'LPG Cylinder', 'LPG', '11kg', 'Standard LPG cylinder 11kg', 50, 'pcs', 22.00, 27.50, 'LPG Main Supplier'),
('LPG-22', 'LPG Cylinder', 'LPG', '22kg', 'Commercial LPG cylinder 22kg', 35, 'pcs', 40.00, 48.00, 'LPG Main Supplier'),
('LPG-50', 'LPG Cylinder', 'LPG', '50kg', 'Industrial LPG cylinder 50kg', 20, 'pcs', 80.00, 95.00, 'LPG Main Supplier');