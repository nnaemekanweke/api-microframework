-- Create database
CREATE DATABASE IF NOT EXISTS microframework CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE microframework;

-- Create a user (optional, if you want a dedicated user)
-- CREATE USER 'microframework_user'@'localhost' IDENTIFIED BY 'password';
-- GRANT ALL PRIVILEGES ON microframework.* TO 'microframework_user'@'localhost';
-- FLUSH PRIVILEGES;

-- Show status
SELECT 'Database created successfully!' AS status;