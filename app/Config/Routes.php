<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default route - Inventory Dashboard
$routes->get('/', 'Inventory::index');

// Inventory Routes
$routes->get('inventory', 'Inventory::index');
$routes->get('inventory/add', 'Inventory::add');
$routes->post('inventory/add', 'Inventory::add');
$routes->get('inventory/edit/(:num)', 'Inventory::edit/$1');
$routes->post('inventory/edit/(:num)', 'Inventory::edit/$1');
$routes->get('inventory/view/(:num)', 'Inventory::view/$1');
$routes->get('inventory/delete/(:num)', 'Inventory::delete/$1');
$routes->get('inventory/search', 'Inventory::search');
