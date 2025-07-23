<?php

namespace App;

class Response 
{
    protected $content;
    protected $statusCode;
    protected $headers = [];

    public function __construct($content = '', $statusCode = 200, $headers = []) 
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = array_merge(['Content-Type' => 'application/json'], $headers);
    }

    public static function json($data, $statusCode = 200) 
    {
        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        return new static($content, $statusCode);
    }

    public static function success($data = null, $message = 'Success') 
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'timestamp' => gmdate('Y-m-d H:i:s') . ' UTC'
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        return static::json($response);
    }

    public static function error($message, $statusCode = 400, $errors = null) 
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'timestamp' => gmdate('Y-m-d H:i:s') . ' UTC'
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        return static::json($response, $statusCode);
    }

    public function send() 
    {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
        
        echo $this->content;
        return $this;
    }
}