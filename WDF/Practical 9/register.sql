CREATE DATABASE register;

USE register;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    password VARCHAR(100) NOT NULL
);