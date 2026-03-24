-- Create Database
CREATE DATABASE IF NOT EXISTS bioelectrodeai;
USE bioelectrodeai;

-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    bio TEXT NULL,
    profile_photo VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Verify table structure
DESCRIBE users;
