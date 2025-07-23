#!/usr/bin/env php
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
    echo "MySQL Database Inspector\n";
    echo "========================\n";
    
    // Show database info
    $dbInfo = Database::selectOne("SELECT DATABASE() as current_db, VERSION() as version, NOW() as current_time");
    echo "Current Database: {$dbInfo['current_db']}\n";
    echo "MySQL Version: {$dbInfo['version']}\n";
    echo "Current Time: {$dbInfo['current_time']}\n\n";
    
    // Show tables
    $tables = Database::select("SHOW TABLES");
    echo "Tables:\n";
    foreach ($tables as $table) {
        $tableName = array_values($table)[0];
        echo "  - {$tableName}\n";
    }
    
    echo "\nUsers:\n";
    $users = Database::select("SELECT * FROM users ORDER BY created_at DESC");
    foreach ($users as $user) {
        echo "  - ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Created: {$user['created_at']}\n";
    }
    
    echo "\nMigrations:\n";
    $migrations = Database::select("SELECT * FROM migrations ORDER BY executed_at DESC");
    foreach ($migrations as $migration) {
        echo "  - {$migration['migration']} (executed: {$migration['executed_at']})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}