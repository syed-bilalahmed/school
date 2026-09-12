<?php
// DB Schema Update Script for Communicate (Notice Board)
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Notice Board
    $sqlNotice = "CREATE TABLE IF NOT EXISTS notice_board (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT,
        is_visible_to_student ENUM('yes', 'no') DEFAULT 'yes',
        is_visible_to_staff ENUM('yes', 'no') DEFAULT 'yes',
        publish_date DATE NOT NULL,
        created_by INT NOT NULL, -- User ID
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlNotice);

    // 2. Email/SMS Log (Placeholder for now)
    $sqlEmail = "CREATE TABLE IF NOT EXISTS email_sms_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255),
        message TEXT,
        send_to VARCHAR(50), -- e.g. 'student', 'staff', 'all'
        type ENUM('email', 'sms'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlEmail);

    echo "Database schema updated successfully (Communicate Module).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
