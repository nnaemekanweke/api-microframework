<?php

namespace Controllers;

use App\Response;

class ApiController 
{
    public function index($request) 
    {
        return Response::success([
            'name' => 'API MicroFramework',
            'version' => '1.0.0',
            'author' => 'nnaemekanweke',
            'environment' => 'development',
            'server_time' => gmdate('Y-m-d H:i:s') . ' UTC',
            'endpoints' => [
                'GET /' => 'API information',
                'GET /api/v1' => 'API v1 status',
                'GET /api/v1/users' => 'List users',
                'POST /api/v1/users' => 'Create user',
                'GET /api/v1/users/{id}' => 'Get user by ID',
                'PUT /api/v1/users/{id}' => 'Update user',
                'DELETE /api/v1/users/{id}' => 'Delete user'
            ]
        ], 'API MicroFramework by nnaemekanweke');
    }

    public function v1Status($request) 
    {
        return Response::success([
            'version' => '1.0.0',
            'status' => 'active',
            'uptime' => 'Running',
            'current_user' => 'nnaemekanweke',
            'server_time' => gmdate('Y-m-d H:i:s') . ' UTC',
            'memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'php_version' => PHP_VERSION
        ], 'API v1 is healthy and running');
    }

    private function formatBytes($size, $precision = 2) 
    {
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}