<?php
// DB Schema Update Script for Front Office
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Admission Enquiry
    $sqlEnquiry = "CREATE TABLE IF NOT EXISTS admission_enquiry (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT,
        description TEXT,
        note TEXT,
        date DATE NOT NULL,
        next_follow_up_date DATE,
        assigned_to INT, -- User ID (Staff)
        reference VARCHAR(100), -- Google, Friend
        source VARCHAR(100), -- Online, Front Desk
        class_id INT, -- Interested Class
        no_of_child INT DEFAULT 1,
        status ENUM('active', 'dead', 'won', 'lost') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
    )";
    $pdo->exec($sqlEnquiry);

    // 2. Visitor Book
    $sqlVisitor = "CREATE TABLE IF NOT EXISTS visitor_book (
        id INT AUTO_INCREMENT PRIMARY KEY,
        purpose VARCHAR(255),
        name VARCHAR(255) NOT NULL,
        contact VARCHAR(20),
        id_proof VARCHAR(100),
        no_of_person INT DEFAULT 1,
        date DATE NOT NULL,
        in_time TIME,
        out_time TIME,
        note TEXT,
        image VARCHAR(255), -- Visitor card or photo path
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlVisitor);

    echo "Database schema updated successfully (Front Office Module).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
