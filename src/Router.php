<?php

namespace App;

class Router 
{
    protected $routes = [];
    protected $middleware = [];

    public function get($uri, $handler) 
    {
        return $this->addRoute('GET', $uri, $handler);
    }

    public function post($uri, $handler) 
    {
        return $this->addRoute('POST', $uri, $handler);
    }

    public function put($uri, $handler) 
    {
        return $this->addRoute('PUT', $uri, $handler);
    }

    public function delete($uri, $handler) 
    {
        return $this->addRoute('DELETE', $uri, $handler);
    }

    protected function addRoute($method, $uri, $handler) 
    {
        $uri = rtrim($uri, '/') ?: '/';
        $this->routes[$method][$uri] = [
            'handler' => $handler,
            'middleware' => $this->middleware
        ];
        
        $this->middleware = []; // Reset middleware for next route
        return $this;
    }

    public function middleware($middleware) 
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function dispatch(Request $request) 
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        if (!isset($this->routes[$method][$uri])) {
            return Response::error("Endpoint not found: {$method} {$uri}", 404);
        }

        $route = $this->routes[$method][$uri];
        
        try {
            // Execute middleware
            foreach ($route['middleware'] as $middleware) {
                $middlewareInstance = new $middleware();
                $middlewareInstance->handle($request);
            }
            
            // Execute handler
            $handler = $route['handler'];
            
            if (is_string($handler) && strpos($handler, '@') !== false) {
                [$controller, $method] = explode('@', $handler);
                $controllerInstance = new $controller();
                return $controllerInstance->$method($request);
            }
            
            if (is_callable($handler)) {
                return $handler($request);
            }
            
            return Response::error('Invalid route handler', 500);
            
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }
}