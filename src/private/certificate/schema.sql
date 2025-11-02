-- schema.sql - set up database
-- Run these in your MySQL (e.g. via phpMyAdmin or the mysql CLI).

-- 1) Create database (change names if you want)
CREATE DATABASE IF NOT EXISTS parish_records CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2) Create a user (optional; you can also use 'root' during local dev)
-- GRANT ALL ON parish_records.* TO 'parish_user'@'localhost' IDENTIFIED BY 'parish_pass';

USE parish_records;

-- 3) Single table to keep this project very simple (student-style)
CREATE TABLE IF NOT EXISTS submissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('baptism','wedding') NOT NULL,
  display_name VARCHAR(255) DEFAULT NULL,
  data_json LONGTEXT NOT NULL,
  ip_addr VARCHAR(64) DEFAULT NULL,
  user_agent VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
