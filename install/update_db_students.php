<?php
// DB Schema Update Script for Students
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Students Table
    // Linked to users table for login
    $sqlStudents = "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        admission_no VARCHAR(50) NOT NULL UNIQUE,
        roll_no VARCHAR(50),
        class_id INT,
        section_id INT,
        dob DATE,
        gender ENUM('Male', 'Female', 'Other'),
        parent_phone VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL,
        FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE SET NULL
    )";
    $pdo->exec($sqlStudents);

    echo "Database schema updated successfully (Students).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
