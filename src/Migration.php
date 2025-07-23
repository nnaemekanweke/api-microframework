<?php

namespace App;

class Migration 
{
    public static function run() 
    {
        $migrationsPath = __DIR__ . '/../database/migrations';
        
        if (!is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0755, true);
        }

        // Create migrations table if it doesn't exist
        self::createMigrationsTable();

        $files = glob($migrationsPath . '/*.php');
        sort($files);

        foreach ($files as $file) {
            $migrationName = basename($file, '.php');
            
            if (!self::migrationExists($migrationName)) {
                echo "Running migration: {$migrationName}\n";
                echo "=====================================\n";
                require_once $file;
                self::recordMigration($migrationName);
                echo "✅ Migration {$migrationName} completed\n\n";
            } else {
                echo "⏭️  Skipping {$migrationName} (already run)\n";
            }
        }

        echo "All migrations completed!\n";
    }

    protected static function createMigrationsTable() 
    {
        // MySQL version of migrations table
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_migration (migration)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        Database::query($sql);
    }

    protected static function migrationExists($migrationName) 
    {
        $result = Database::selectOne(
            "SELECT migration FROM migrations WHERE migration = ?",
            [$migrationName]
        );

        return $result !== false && $result !== null;
    }

    protected static function recordMigration($migrationName) 
    {
        Database::insert(
            "INSERT INTO migrations (migration, executed_at) VALUES (?, NOW())",
            [$migrationName]
        );
    }
}