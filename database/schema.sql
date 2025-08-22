-- Notes App Database Schema
-- Compatible with MariaDB 10.2+ and MySQL 5.7+

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS notes_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE notes_app;

-- Notes table
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hash_id VARCHAR(22) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_hash_id (hash_id),
    INDEX idx_updated_at (updated_at),
    INDEX idx_created_at (created_at),
    INDEX idx_title_content (title, content(100)) -- Partial index for search
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: Insert a sample note for testing
INSERT INTO notes (hash_id, title, content, created_at, updated_at) VALUES
('sample123456789', 'Welcome to Notes App', 'This is your first note! You can use #tags to organize your notes. Try searching for #welcome to find this note again.', NOW(), NOW());
