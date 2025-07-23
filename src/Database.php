<?php

namespace App;

use PDO;
use PDOException;

class Database 
{
    protected static $connections = [];
    protected static $config = null;

    public static function connection($name = null) 
    {
        if (self::$config === null) {
            self::loadConfig();
        }

        $name = $name ?: self::$config['default'];

        if (!isset(self::$connections[$name])) {
            self::$connections[$name] = self::createConnection($name);
        }

        return self::$connections[$name];
    }

    protected static function loadConfig() 
    {
        self::$config = require __DIR__ . '/../config/database.php';
    }

    protected static function createConnection($name) 
    {
        $config = self::$config['connections'][$name];
        
        try {
            if ($config['driver'] === 'mysql') {
                // Build DSN based on available options
                if (!empty($config['unix_socket'])) {
                    // Use Unix socket if specified
                    $dsn = "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']};charset={$config['charset']}";
                } else {
                    // Use TCP/IP connection
                    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
                }
                
                return new PDO($dsn, $config['username'], $config['password'], $config['options']);
            }
            
            if ($config['driver'] === 'sqlite') {
                $dbPath = $config['database'];
                
                if (!str_starts_with($dbPath, '/')) {
                    $dbPath = __DIR__ . '/../' . $dbPath;
                }
                
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    if (!mkdir($dir, 0755, true)) {
                        throw new \Exception("Cannot create database directory: {$dir}");
                    }
                }
                
                if (!file_exists($dbPath)) {
                    if (!touch($dbPath)) {
                        throw new \Exception("Cannot create database file: {$dbPath}");
                    }
                    chmod($dbPath, 0664);
                }
                
                $dsn = "sqlite:" . $dbPath;
                return new PDO($dsn, null, null, $config['options']);
            }
            
            throw new \Exception("Unsupported database driver: {$config['driver']}");
            
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public static function query($sql, $params = []) 
    {
        $pdo = self::connection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function select($sql, $params = []) 
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function selectOne($sql, $params = []) 
    {
        return self::query($sql, $params)->fetch();
    }

    public static function insert($sql, $params = []) 
    {
        self::query($sql, $params);
        return self::connection()->lastInsertId();
    }

    public static function update($sql, $params = []) 
    {
        return self::query($sql, $params)->rowCount();
    }

    public static function delete($sql, $params = []) 
    {
        return self::query($sql, $params)->rowCount();
    }
}