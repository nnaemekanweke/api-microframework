<?php

// API Information
$this->get('/', 'Controllers\ApiController@index');

// API v1 Status
$this->get('/api/v1', 'Controllers\ApiController@v1Status');

// User routes
$this->get('/api/v1/users', 'Controllers\UserController@index');
$this->post('/api/v1/users', 'Controllers\UserController@store');

// Individual user routes (we'll need to enhance router for parameters later)
$this->get('/api/v1/users/1', function($request) {
    $controller = new Controllers\UserController();
    return $controller->show($request, 1);
});

$this->get('/api/v1/users/2', function($request) {
    $controller = new Controllers\UserController();
    return $controller->show($request, 2);
});

$this->get('/api/v1/users/3', function($request) {
    $controller = new Controllers\UserController();
    return $controller->show($request, 3);
});

$this->get('/api/v1/users/4', function($request) {
    $controller = new Controllers\UserController();
    return $controller->show($request, 4);
});

$this->put('/api/v1/users/1', function($request) {
    $controller = new Controllers\UserController();
    return $controller->update($request, 1);
});

$this->delete('/api/v1/users/1', function($request) {
    $controller = new Controllers\UserController();
    return $controller->destroy($request, 1);
});