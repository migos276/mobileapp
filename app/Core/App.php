<?php
namespace App\Core;

class App {
    private $router;
    private $database;

    public function __construct() {
        $this->database = Database::getInstance();
        $this->router = new Router();
        $this->setupRoutes();
    }

    private function setupRoutes() {
        // Routes publiques
        $this->router->get('/', 'HomeController@index');
        $this->router->get('/login', 'AuthController@login');
        $this->router->post('/login', 'AuthController@processLogin');
        $this->router->get('/register', 'AuthController@register');
        $this->router->post('/register', 'AuthController@processRegister');
        $this->router->get('/logout', 'AuthController@logout');

        // Routes API
        $this->router->get('/api/stations/nearby', 'ApiController@nearbyStations');
        $this->router->get('/api/stations/by-product/{productId}', 'ApiController@stationsByProduct');
        $this->router->get('/api/products', 'ApiController@products');
        $this->router->post('/api/orders', 'ApiController@createOrder');
        $this->router->get('/api/orders/{id}/status', 'ApiController@orderStatus');

        // Routes client
        $this->router->get('/customer/dashboard', 'CustomerController@dashboard');
        $this->router->get('/customer/orders', 'CustomerController@orders');
        $this->router->get('/customer/profile', 'CustomerController@profile');
        $this->router->post('/customer/profile', 'CustomerController@updateProfile');

        // Routes station
        $this->router->get('/station/dashboard', 'StationController@dashboard');
        $this->router->get('/station/orders', 'StationController@orders');
        $this->router->get('/station/stock', 'StationController@stock');
        $this->router->post('/station/stock', 'StationController@updateStock');
        $this->router->post('/station/orders/{id}/update', 'StationController@updateOrder');

        // Routes admin
        $this->router->get('/admin/dashboard', 'AdminController@dashboard');
        $this->router->get('/admin/stations', 'AdminController@stations');
        $this->router->post('/admin/stations/approve/{id}', 'AdminController@approveStation');
        $this->router->get('/admin/orders', 'AdminController@orders');
        $this->router->get('/admin/analytics', 'AdminController@analytics');
    }

    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        $this->router->dispatch($method, $uri);
    }
}