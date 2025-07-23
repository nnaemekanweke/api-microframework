<?php

spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/';
    
    $namespaces = [
        'App\\' => 'src/',
        'Controllers\\' => 'controllers/',
        'Models\\' => 'models/',
        'Middleware\\' => 'middleware/',
    ];
    
    foreach ($namespaces as $namespace => $dir) {
        if (strpos($class, $namespace) === 0) {
            $relativeClass = substr($class, strlen($namespace));
            $file = $baseDir . $dir . str_replace('\\', '/', $relativeClass) . '.php';
            
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});