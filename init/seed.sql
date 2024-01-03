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

INSERT INTO users (username, password, full_name, email, phone_number, role)
VALUES ('admin', MD5('admin'), 'admin', 'user@example.com', '123456789', 'admin');
