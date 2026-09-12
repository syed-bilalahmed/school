<?php
// DB Schema Update Script for Subjects
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Subjects Table
    // type: Theory / Practical
    $sqlSubjects = "CREATE TABLE IF NOT EXISTS subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subject_name VARCHAR(100) NOT NULL,
        subject_code VARCHAR(50),
        type ENUM('Theory', 'Practical') DEFAULT 'Theory',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlSubjects);

    // Subject-Class Mapping (Many-to-Many)
    // Because one subject (e.g. Math) can be in multiple classes, OR usually distinct syllabus per class.
    // QDOCS usually assigns subjects to Class-Section groups.
    // Let's create a mapping table `class_subjects`.
    
    $sqlClassSubjects = "CREATE TABLE IF NOT EXISTS class_subjects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        class_id INT NOT NULL,
        section_id INT NOT NULL,
        subject_id INT NOT NULL,
        teacher_id INT, 
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
        FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
        FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
    )";
    $pdo->exec($sqlClassSubjects);

    echo "Database schema updated successfully (Subjects & Associations).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
