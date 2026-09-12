<?php
// DB Schema Update for Certificates
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Certificates (Templates)
    $pdo->exec("CREATE TABLE IF NOT EXISTS certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        certificate_name VARCHAR(255) NOT NULL,
        certificate_text TEXT, -- The main body content with placeholders
        left_header VARCHAR(255),
        center_header VARCHAR(255),
        right_header VARCHAR(255),
        left_footer VARCHAR(255),
        center_footer VARCHAR(255),
        right_footer VARCHAR(255),
        background_image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_active INT DEFAULT 1
    )");

    // 2. Student Certificates (Generated Log)
    $pdo->exec("CREATE TABLE IF NOT EXISTS student_certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        certificate_id INT,
        student_id INT,
        certificate_no VARCHAR(100),
        generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (certificate_id) REFERENCES certificates(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )");

    echo "Certificate tables created successfully.";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
