<?php

namespace App;

class App 
{
    protected $router;
    
    public function __construct() 
    {
        $this->loadEnvironment();
        $this->router = new Router();
        $this->setupCors();
        $this->runMigrations();
        $this->loadRoutes();
    }
    
    protected function loadEnvironment() 
    {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    putenv($line);
                    [$key, $value] = explode('=', $line, 2);
                    $_ENV[$key] = $value;
                }
            }
        }
        
        // Set current user
        $_ENV['CURRENT_USER'] = 'nnaemekanweke';
    }
    
    protected function runMigrations() 
    {
        try {
            Migration::run();
        } catch (\Exception $e) {
            // Migrations will run on first request
        }
    }
    
    protected function setupCors() 
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
    
    protected function loadRoutes() 
    {
        require_once __DIR__ . '/../routes/api.php';
    }
    
    public function get($uri, $handler) 
    {
        return $this->router->get($uri, $handler);
    }
    
    public function post($uri, $handler) 
    {
        return $this->router->post($uri, $handler);
    }
    
    public function put($uri, $handler) 
    {
        return $this->router->put($uri, $handler);
    }
    
    public function delete($uri, $handler) 
    {
        return $this->router->delete($uri, $handler);
    }
    
    public function middleware($middleware) 
    {
        return $this->router->middleware($middleware);
    }
    
    public function run() 
    {
        $request = Request::capture();
        $response = $this->router->dispatch($request);
        $response->send();
    }
}