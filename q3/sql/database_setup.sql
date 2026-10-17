-- ========================================================
-- Database Setup Script for Student Front-End Portal
-- Database: student_portal
-- ========================================================

CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

-- 1. Users Table for Secure Session Handling & Authentication
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(30) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin & Faculty Users
-- Passwords stored (plain text / hash) for demo authentication:
-- admin / admin123
-- faculty / faculty123
INSERT INTO users (username, password, full_name, role) VALUES
('admin', 'admin123', 'System Administrator', 'admin'),
('faculty', 'faculty123', 'Dr. Sarah Jenkins', 'faculty'),
('student', 'student123', 'Gowtham K', 'student');

-- 2. Students Table
DROP TABLE IF EXISTS student_files;
DROP TABLE IF EXISTS students;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    course VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    semester INT DEFAULT 5,
    cgpa DECIMAL(3,2) DEFAULT 8.50,
    phone VARCHAR(20) DEFAULT '+91 9876543210',
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Student Files Table for Uploaded Images and Documents
CREATE TABLE student_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL UNIQUE,
    file_type VARCHAR(100) NOT NULL,
    file_category VARCHAR(50) NOT NULL, -- 'profile_photo', 'id_card', 'document', 'resume'
    file_size BIGINT NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_student_file FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert Sample Student Profiles
INSERT INTO students (id, name, roll_no, email, course, department, semester, cgpa, phone, bio) VALUES
(1, 'Aarav Sharma', '1001', 'aarav.sharma@example.edu', 'CSE', 'CSE', 5, 9.15, '+91 9876500001', 'Passionate about Web Technologies, Cloud Computing, and Machine Learning algorithms.'),
(2, 'Diya Patel', '1002', 'diya.patel@example.edu', 'IT', 'IT', 5, 8.80, '+91 9876500002', 'Full-stack developer with keen interest in distributed systems and UI/UX design.'),
(3, 'Rohan Verma', '1003', 'rohan.verma@example.edu', 'AIML', 'AIML', 5, 9.45, '+91 9876500003', 'Competitive programmer and researcher working on NLP and Deep Learning models.'),
(4, 'Sneha Reddy', '1004', 'sneha.reddy@example.edu', 'ECE', 'ECE', 5, 8.20, '+91 9876500004', 'Cybersecurity enthusiast exploring network forensics and penetration testing.'),
(5, 'Vikram Mehta', '1005', 'vikram.mehta@example.edu', 'CSE', 'CSE', 5, 7.90, '+91 9876500005', 'Aspiring software engineer focusing on backend API engineering and database optimization.');

-- Insert Associated Uploaded Files
-- Note: Student 5 deliberately has no files to demonstrate conditional empty-state handling
INSERT INTO student_files (student_id, original_name, stored_name, file_type, file_category, file_size, file_path) VALUES
-- Student 1 (Aarav): Profile image, ID card image, and PDF resume
(1, 'aarav_photo.png', 'stud_1_photo.png', 'image/png', 'profile_photo', 45120, 'uploads/stud_1_photo.png'),
(1, 'aarav_id_card.png', 'stud_1_idcard.png', 'image/png', 'id_card', 38900, 'uploads/stud_1_idcard.png'),
(1, 'aarav_resume.pdf', 'stud_1_resume.pdf', 'application/pdf', 'resume', 125400, 'uploads/stud_1_resume.pdf'),

-- Student 2 (Diya): Profile photo and marksheet PDF
(2, 'diya_photo.png', 'stud_2_photo.png', 'image/png', 'profile_photo', 47800, 'uploads/stud_2_photo.png'),
(2, 'diya_marksheet.pdf', 'stud_2_marksheet.pdf', 'application/pdf', 'document', 98200, 'uploads/stud_2_marksheet.pdf'),

-- Student 3 (Rohan): Profile photo only
(3, 'rohan_photo.png', 'stud_3_photo.png', 'image/png', 'profile_photo', 51200, 'uploads/stud_3_photo.png'),

-- Student 4 (Sneha): Only a document PDF (no profile picture, testing fallback avatar)
(4, 'sneha_cyber_cert.pdf', 'stud_4_cert.pdf', 'application/pdf', 'document', 114500, 'uploads/stud_4_cert.pdf');
