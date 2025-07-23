<?php

namespace App;

class Request 
{
    protected $method;
    protected $uri;
    protected $params;
    protected $query;
    protected $headers;
    protected $body;

    public function __construct() 
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->params = $_POST;
        $this->query = $_GET;
        $this->headers = getallheaders() ?: [];
        $this->body = file_get_contents('php://input');
    }

    public static function capture() 
    {
        return new static();
    }

    public function getMethod() 
    {
        return $this->method;
    }

    public function getUri() 
    {
        return rtrim($this->uri, '/') ?: '/';
    }

    public function input($key = null, $default = null) 
    {
        $data = array_merge($this->params, $this->json());
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }

    public function query($key = null, $default = null) 
    {
        if ($key === null) {
            return $this->query;
        }
        
        return $this->query[$key] ?? $default;
    }

    public function json() 
    {
        return json_decode($this->body, true) ?: [];
    }

    public function header($key) 
    {
        return $this->headers[$key] ?? null;
    }
}