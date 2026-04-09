-- Inventory System Database
-- Created for CodeIgniter 4 Application

CREATE DATABASE IF NOT EXISTS inventory_system;
USE inventory_system;

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_code VARCHAR(50) UNIQUE NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
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

-- Insert Sample Categories
INSERT INTO categories (category_name, description) VALUES 
('Electronics', 'Electronic devices and gadgets'),
('Office Supplies', 'Office and stationery items'),
('Furniture', 'Office and home furniture');

-- Insert Sample Products
INSERT INTO products (product_code, product_name, category, description, quantity, unit, buying_price, selling_price, supplier) VALUES 
('PROD-001', 'Wireless Mouse', 'Electronics', 'Ergonomic wireless mouse with USB receiver', 50, 'pcs', 15.00, 25.00, 'TechSupply Co.'),
('PROD-002', 'Mechanical Keyboard', 'Electronics', 'RGB mechanical keyboard with blue switches', 30, 'pcs', 45.00, 75.00, 'TechSupply Co.'),
('PROD-003', 'A4 Paper (Ream)', 'Office Supplies', '500 sheets high quality A4 paper', 100, 'ream', 5.00, 8.00, 'OfficeWorld'),
('PROD-004', 'Ballpoint Pens (Box)', 'Office Supplies', 'Box of 12 blue ballpoint pens', 75, 'box', 3.00, 5.00, 'OfficeWorld'),
('PROD-005', 'Office Chair', 'Furniture', 'Ergonomic office chair with lumbar support', 15, 'pcs', 120.00, 180.00, 'FurniturePro');