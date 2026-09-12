<?php
// DB Schema Update for Library
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Books Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        book_title VARCHAR(255) NOT NULL,
        book_no VARCHAR(50) UNIQUE,
        isbn VARCHAR(50),
        author VARCHAR(255),
        publisher VARCHAR(255),
        rack_no VARCHAR(50),
        qty INT DEFAULT 0,
        price DECIMAL(10,2),
        post_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Book Issues Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS book_issues (
        id INT AUTO_INCREMENT PRIMARY KEY,
        book_id INT,
        user_id INT, -- Student or Staff
        user_type ENUM('student', 'staff') DEFAULT 'student',
        issue_date DATE,
        due_date DATE,
        return_date DATE NULL,
        is_returned BOOLEAN DEFAULT FALSE,
        fine DECIMAL(10,2) DEFAULT 0.00,
        FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    echo "Library tables created successfully.";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
