<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

error_reporting(E_ALL);
ini_set("display_errors", 1);

require __DIR__ . '/../vendor/autoload.php';

// Create Router instance
$router = new \Bramus\Router\Router();

$router->setNamespace('Controllers');

// routes for the appointments endpoint
$router->get('/appointments', 'AppointmentController@getAll');
$router->get('/appointments/(.*)', 'AppointmentController@getOne');
$router->post('/appointments', 'AppointmentController@create');
$router->put('/appointments/(.*)', 'AppointmentController@update');
$router->delete('/appointments/(.*)', 'AppointmentController@delete');

// routes for the cats endpoint
$router->get('/cats', 'CatController@getAll');
$router->get('/cats/(\d+)', 'CatController@getOne');
$router->post('/cats', 'CatController@create');
$router->put('/cats/(\d+)', 'CatController@update');
$router->delete('/cats/(\d+)', 'CatController@delete');

// routes for the users endpoint
$router->get('/users', 'UserController@getAll');
$router->get('/users/(\d+)', 'UserController@getOne');
$router->post('/users/login', 'UserController@login');
$router->post('/users/register', 'UserController@register');
$router->put('/users/(\d+)', 'UserController@update');
$router->delete('/users/(\d+)', 'UserController@delete');

// Run it!
$router->run();