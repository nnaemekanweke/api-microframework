<?php

use App\Database;

echo "Creating users table...\n";

// Create users table for MySQL
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    created_by VARCHAR(255) NOT NULL,
    updated_by VARCHAR(255) NULL,
    INDEX idx_email (email),
    INDEX idx_name (name),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

Database::query($sql);
echo "Users table created!\n";

// Check if we already have data
$existingUsers = Database::selectOne("SELECT COUNT(*) as count FROM users");
if ($existingUsers['count'] > 0) {
    echo "Users table already has data, skipping seed.\n";
    return;
}

echo "Seeding users table...\n";

// Insert sample data with current timestamp
$users = [
    [
        'name' => 'nnaemekanweke',
        'email' => 'nnaemeka@example.com',
        'created_by' => 'nnaemekanweke'
    ],
    [
        'name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'created_by' => 'nnaemekanweke'
    ],
    [
        'name' => 'Bob Johnson',
        'email' => 'bob@example.com',
        'created_by' => 'nnaemekanweke'
    ],
    [
        'name' => 'Alice Brown',
        'email' => 'alice@example.com',
        'created_by' => 'nnaemekanweke'
    ],
    [
        'name' => 'Charlie Wilson',
        'email' => 'charlie@example.com',
        'created_by' => 'nnaemekanweke'
    ]
];

foreach ($users as $user) {
    try {
        Database::insert(
            "INSERT INTO users (name, email, created_by) VALUES (?, ?, ?)",
            [$user['name'], $user['email'], $user['created_by']]
        );
        echo "Inserted user: {$user['name']}\n";
    } catch (Exception $e) {
        echo "Failed to insert user {$user['name']}: " . $e->getMessage() . "\n";
    }
}

echo "Users table seeded successfully!\n";