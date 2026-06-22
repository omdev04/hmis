-- ====================================================
-- InfyHMS - MySQL Initialization Script
-- ====================================================

-- Set charset
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `hms`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Grant permissions to hms_user
GRANT ALL PRIVILEGES ON `hms`.* TO 'hms_user'@'%';
FLUSH PRIVILEGES;

-- Use the database
USE `hms`;
