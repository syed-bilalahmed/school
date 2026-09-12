<?php
// DB Schema Update Script for Download Center
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Content Table
    $sqlContent = "CREATE TABLE IF NOT EXISTS content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content_title VARCHAR(255) NOT NULL,
        content_type VARCHAR(100) NOT NULL, -- e.g. 'Assignment', 'Study Material', 'Syllabus', 'Other'
        available_for VARCHAR(50) DEFAULT 'all', -- 'all', 'student', 'staff' or Class ID specific logic later
        class_id INT DEFAULT NULL, -- Optional: If specific to class
        section_id INT DEFAULT NULL,
        file_path VARCHAR(255) NOT NULL,
        description TEXT,
        upload_date DATE NOT NULL,
        uploaded_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlContent);

    echo "Database schema updated successfully (Download Center).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
