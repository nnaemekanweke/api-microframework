#!/usr/bin/env php
<?php

require_once __DIR__ . '/autoload.php';

use App\Migration;

echo "Running database migrations...\n";
echo "================================\n";

try {
    // Load environment
    $envFile = __DIR__ . '/.env';
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

    Migration::run();
    echo "================================\n";
    echo "Migrations completed successfully!\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}