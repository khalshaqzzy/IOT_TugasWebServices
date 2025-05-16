<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->post('sensor', 'Sensor::create');
$routes->get('sensor/data', 'Sensor::getData');

$routes->get('sensor/view', function() {
    return view('sensor_view');
});
$routes->get('/', 'Home::index');