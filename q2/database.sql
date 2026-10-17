-- ========================================================
-- Database Schema: student_portal
-- Shared Database for Question 2 (PHP) & Question 3 (Java)
-- ========================================================

CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

-- 1. Users Table (Authentication)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(30) DEFAULT 'admin'
);

-- 2. Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    course VARCHAR(100) NOT NULL
);

-- 3. Student Files Table
CREATE TABLE IF NOT EXISTS student_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL UNIQUE,
    file_type VARCHAR(100) NOT NULL,
    file_size BIGINT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Seed Data (Continuous Numeric Roll Numbers)
INSERT INTO users (username, password, full_name, role) VALUES
('admin', 'admin123', 'System Administrator', 'admin')
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name);

INSERT INTO students (name, roll_no, email, course) VALUES
('Aravind Kumar', '1001', 'aravind.k@example.com', 'Computer Science'),
('Bhavana Rao', '1002', 'bhavana.r@example.com', 'Information Technology'),
('Chetan Sharma', '1003', 'chetan.s@example.com', 'Artificial Intelligence'),
('Divya Nair', '1004', 'divya.n@example.com', 'Electronics & Communication'),
('Eashwar Prasad', '1005', 'eashwar.p@example.com', 'Computer Science')
ON DUPLICATE KEY UPDATE course=VALUES(course);
