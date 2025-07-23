<?php

require_once 'autoload.php';

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

use App\Database;

try {
    echo "Testing MySQL connection...\n";
    echo "Host: " . $_ENV['DB_HOST'] . "\n";
    echo "Database: " . $_ENV['DB_DATABASE'] . "\n";
    echo "Username: " . $_ENV['DB_USERNAME'] . "\n";
    echo "Connection: " . $_ENV['DB_CONNECTION'] . "\n";
    echo "===========================\n";
    
    $pdo = Database::connection();
    echo "✅ MySQL connection successful!\n";
    
    // Test a simple query
    $result = Database::selectOne("SELECT 1 as test, NOW() as current_time");
    echo "✅ Database query test: " . $result['test'] . "\n";
    echo "✅ Server time: " . $result['current_time'] . "\n";
    
    // Check if database exists
    $databases = Database::select("SHOW DATABASES LIKE ?", [$_ENV['DB_DATABASE']]);
    if (empty($databases)) {
        echo "❌ Database '{$_ENV['DB_DATABASE']}' does not exist!\n";
        echo "Please create it first:\n";
        echo "CREATE DATABASE {$_ENV['DB_DATABASE']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
    } else {
        echo "✅ Database '{$_ENV['DB_DATABASE']}' exists!\n";
    }
    
} catch (Exception $e) {
    echo "❌ MySQL connection failed: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Make sure MySQL server is running\n";
    echo "2. Check your credentials in .env file\n";
    echo "3. Create the database: CREATE DATABASE microframework;\n";
    echo "4. Grant permissions if needed\n";
}