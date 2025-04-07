-- Xóa database nếu đã tồn tại và tạo lại
DROP DATABASE IF EXISTS chat_app;
CREATE DATABASE chat_app;
USE chat_app;

-- Bảng users đã cập nhật các trường bổ sung
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    birth_date DATE DEFAULT NULL,
    gender VARCHAR(10) DEFAULT NULL,         -- có thể lưu: 'male', 'female', 'other'
    phone VARCHAR(20) DEFAULT NULL,
    contact_email VARCHAR(100) DEFAULT NULL,
    location VARCHAR(100) DEFAULT NULL,
    hide_phone TINYINT DEFAULT 0,              -- 0: hiển thị, 1: ẩn
    hide_email TINYINT DEFAULT 0,              -- 0: hiển thị, 1: ẩn
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    from_id INT NOT NULL,
    to_id INT,
    group_id INT,
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_id) REFERENCES users(id)
);

-- Bảng friend_requests (0: pending, 1: accepted, 2: rejected)
DROP TABLE IF EXISTS friend_requests;
CREATE TABLE friend_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    status TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    file_name VARCHAR(255),
    file_path VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);