#!/usr/bin/env php
<?php

echo "MySQL Connection Diagnostics\n";
echo "============================\n";

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

echo "Configuration:\n";
echo "- Host: " . ($_ENV['DB_HOST'] ?? 'localhost') . "\n";
echo "- Port: " . ($_ENV['DB_PORT'] ?? '3306') . "\n";
echo "- Database: " . ($_ENV['DB_DATABASE'] ?? 'microframework') . "\n";
echo "- Username: " . ($_ENV['DB_USERNAME'] ?? 'root') . "\n";
echo "- Password: " . (empty($_ENV['DB_PASSWORD']) ? '(empty)' : '(set)') . "\n\n";

// Check if MySQL extension is loaded
if (!extension_loaded('pdo_mysql')) {
    echo "❌ PDO MySQL extension is not loaded!\n";
    echo "Install it with: sudo apt-get install php-mysql (Ubuntu/Debian)\n";
    echo "Or: brew install php (macOS with Homebrew)\n";
    exit(1);
}
echo "✅ PDO MySQL extension is loaded\n";

// Test different connection methods
$configs = [
    'TCP/IP with 127.0.0.1' => [
        'dsn' => 'mysql:host=127.0.0.1;port=3306',
        'user' => $_ENV['DB_USERNAME'] ?? 'root',
        'pass' => $_ENV['DB_PASSWORD'] ?? ''
    ],
    'TCP/IP with localhost' => [
        'dsn' => 'mysql:host=localhost;port=3306',
        'user' => $_ENV['DB_USERNAME'] ?? 'root',
        'pass' => $_ENV['DB_PASSWORD'] ?? ''
    ],
    'Unix Socket (common paths)' => [
        'dsn' => 'mysql:unix_socket=/tmp/mysql.sock',
        'user' => $_ENV['DB_USERNAME'] ?? 'root',
        'pass' => $_ENV['DB_PASSWORD'] ?? ''
    ],
    'Unix Socket (alternative)' => [
        'dsn' => 'mysql:unix_socket=/var/run/mysqld/mysqld.sock',
        'user' => $_ENV['DB_USERNAME'] ?? 'root',
        'pass' => $_ENV['DB_PASSWORD'] ?? ''
    ]
];

$workingConfig = null;

foreach ($configs as $name => $config) {
    echo "\nTesting {$name}...\n";
    try {
        $pdo = new PDO(
            $config['dsn'], 
            $config['user'], 
            $config['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo "✅ Connection successful!\n";
        
        $result = $pdo->query("SELECT VERSION() as version")->fetch();
        echo "✅ MySQL Version: " . $result['version'] . "\n";
        
        $workingConfig = $config;
        break;
        
    } catch (PDOException $e) {
        echo "❌ Failed: " . $e->getMessage() . "\n";
    }
}

if ($workingConfig) {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "SOLUTION FOUND!\n";
    echo str_repeat("=", 50) . "\n";
    
    // Parse the working DSN
    if (strpos($workingConfig['dsn'], 'unix_socket') !== false) {
        preg_match('/unix_socket=([^;]+)/', $workingConfig['dsn'], $matches);
        echo "Update your .env file:\n";
        echo "DB_HOST=\n";
        echo "DB_PORT=\n";
        echo "DB_SOCKET={$matches[1]}\n";
    } else {
        preg_match('/host=([^;]+)/', $workingConfig['dsn'], $hostMatches);
        preg_match('/port=([^;]+)/', $workingConfig['dsn'], $portMatches);
        echo "Update your .env file:\n";
        echo "DB_HOST={$hostMatches[1]}\n";
        echo "DB_PORT=" . ($portMatches[1] ?? '3306') . "\n";
        echo "DB_SOCKET=\n";
    }
    
    // Test database creation
    echo "\nTesting database access...\n";
    try {
        $dbName = $_ENV['DB_DATABASE'] ?? 'microframework';
        $fullDsn = $workingConfig['dsn'] . ";dbname={$dbName}";
        $pdo = new PDO($fullDsn, $workingConfig['user'], $workingConfig['pass']);
        echo "✅ Database '{$dbName}' exists and accessible!\n";
    } catch (PDOException $e) {
        echo "❌ Database access failed: " . $e->getMessage() . "\n";
        echo "Create the database:\n";
        echo "CREATE DATABASE {$dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
    }
} else {
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "NO WORKING CONNECTION FOUND!\n";
    echo str_repeat("=", 50) . "\n";
    echo "Possible solutions:\n";
    echo "1. Start MySQL server:\n";
    echo "   - sudo systemctl start mysql (Linux)\n";
    echo "   - brew services start mysql (macOS)\n";
    echo "   - Start MySQL via XAMPP/MAMP\n\n";
    echo "2. Check MySQL is listening:\n";
    echo "   - netstat -an | grep 3306\n";
    echo "   - sudo lsof -i :3306\n\n";
    echo "3. Reset MySQL root password if needed\n";
    echo "4. Check firewall settings\n";
}