<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default route - Login page
$routes->get('/', 'Auth::login');

// Authentication Routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('store/select', 'Store::select');
$routes->post('store/select', 'Store::select');
$routes->post('store/currency', 'Store::setCurrency');

// Dashboard Routes
$routes->get('dashboard', 'Dashboard::index');

// Stock Routes
$routes->get('stock/inventory', 'Inventory::index');
$routes->get('stock/inventory/add', 'Inventory::add');
$routes->post('stock/inventory/add', 'Inventory::add');
$routes->get('stock/inventory/edit/(:num)', 'Inventory::edit/$1');
$routes->post('stock/inventory/edit/(:num)', 'Inventory::edit/$1');
$routes->get('stock/inventory/view/(:num)', 'Inventory::view/$1');
$routes->post('stock/inventory/delete/(:num)', 'Inventory::delete/$1');
$routes->get('stock/inventory/search', 'Inventory::search');
$routes->get('stock/fixed-prices', 'Inventory::fixedPrices');
$routes->post('stock/fixed-prices', 'Inventory::fixedPrices');

// Sales Routes
$routes->get('sales', 'Sales::index');
$routes->get('sales/create', 'Sales::create');
$routes->post('sales/create', 'Sales::create');
$routes->get('sales/view/(:num)', 'Sales::view/$1');
$routes->get('sales/print/(:num)', 'Sales::print/$1');
$routes->get('sales/pdf/(:num)', 'Sales::pdf/$1');
$routes->get('sales/customer', 'Sales::customer');
$routes->get('sales/print-period', 'Sales::printPeriod');
$routes->get('sales/pdf-period', 'Sales::pdfPeriod');
$routes->post('sales/delete/(:num)', 'Sales::delete/$1');

// Customer Routes
$routes->get('customer', 'Customer::index');
$routes->get('customer/create', 'Customer::create');
$routes->post('customer/create', 'Customer::create');
$routes->get('customer/edit/(:num)', 'Customer::edit/$1');
$routes->post('customer/edit/(:num)', 'Customer::edit/$1');
$routes->get('customer/view-sales/(:any)', 'Customer::viewSales/$1');
$routes->get('customer/create-sale', 'Customer::createSale');
$routes->post('customer/create-sale', 'Customer::createSale');
$routes->post('customer/process-all-to-sales', 'Customer::processAllToSales');
$routes->get('customer/select-products', 'Customer::selectProducts');
$routes->post('customer/select-products', 'Customer::selectProducts');
$routes->get('customer/search-products', 'Customer::searchProducts');
$routes->post('customer/preview-sale', 'Customer::previewSale');
$routes->post('customer/confirm-sale', 'Customer::confirmSale');
$routes->post('customer/delete/(:num)', 'Customer::delete/$1');
