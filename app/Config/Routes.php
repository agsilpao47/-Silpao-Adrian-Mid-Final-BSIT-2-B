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

// Inventory Routes
$routes->get('inventory', 'Inventory::index');
$routes->get('inventory/add', 'Inventory::add');
$routes->post('inventory/add', 'Inventory::add');
$routes->get('inventory/edit/(:num)', 'Inventory::edit/$1');
$routes->post('inventory/edit/(:num)', 'Inventory::edit/$1');
$routes->get('inventory/view/(:num)', 'Inventory::view/$1');
$routes->get('inventory/delete/(:num)', 'Inventory::delete/$1');
$routes->get('inventory/search', 'Inventory::search');
