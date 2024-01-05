-- drop database "LMS" if exists 

DROP DATABASE IF EXISTS lms;

-- create database "LMS"

CREATE DATABASE IF NOT EXISTS lms;
USE lms;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    role VARCHAR(20) NOT NULL,
    avatar VARCHAR(255)
);
--deploy public kèm hai tài khoản giáo viên và hai tài khoản sinh viên (tài khoản: teacher1 / 123456a@A ; teacher2 / 123456a@A; student1 / 123456a@A ; student2 / 123456a@A).
INSERT INTO users (username, password, full_name, email, phone_number, role)
VALUES ('teacher1', MD5('123456a@A'), 'teacher1', 'user@example.com', '123456789', 'admin');

INSERT INTO users (username, password, full_name, email, phone_number, role)
VALUES ('teacher2', MD5('123456a@A'), 'teacher2', 'user@example.com', '123456789', 'admin');

INSERT INTO users (username, password, full_name, email, phone_number, role)
VALUES ('student1', MD5('123456a@A'), 'student1', 'user@example.com', '123456789', 'student');

INSERT INTO users (username, password, full_name, email, phone_number, role)
VALUES ('student2', MD5('123456a@A'), 'student2', 'user@example.com', '123456789', 'student');

INSERT INTO users (username, password, full_name, email, phone_number, role)
VALUES ('admin', MD5('admin'), 'admin', 'user@example.com', '123456789', 'admin');

-- create table "Assignments"

CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    due_date DATE NOT NULL,
    file VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- create table for "messages"
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS returnAssignment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    student_id INT NOT NULL,
    description VARCHAR(255),
    file VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES assignments(id),
    FOREIGN KEY (student_id) REFERENCES users(id)
);