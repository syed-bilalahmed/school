<?php
// DB Schema Update Script for Homework
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Homework Table
    $sqlHomework = "CREATE TABLE IF NOT EXISTS homework (
        id INT AUTO_INCREMENT PRIMARY KEY,
        class_id INT NOT NULL,
        section_id INT NOT NULL,
        subject_id INT NOT NULL,
        homework_date DATE NOT NULL,
        submission_date DATE NOT NULL,
        description TEXT,
        created_by INT NOT NULL,
        document_file VARCHAR(255), -- Path to uploaded file
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
        -- Foreign keys for class, section, subject omitted for brevity but should exist
    )";
    $pdo->exec($sqlHomework);

    // 2. Homework Evaluation (Student Submission Status)
    // Simplified: Teacher marks students as 'Completed' or 'Incomplete' with marks/comments.
    // In full QDOCS, students upload their work. Here we'll stick to teacher evaluation tracking first.
    
    $sqlEvaluation = "CREATE TABLE IF NOT EXISTS homework_evaluation (
        id INT AUTO_INCREMENT PRIMARY KEY,
        homework_id INT NOT NULL,
        student_id INT NOT NULL,
        status ENUM('Pending', 'submitted', 'complete', 'incomplete') DEFAULT 'Pending',
        marks DECIMAL(5, 2),
        note TEXT,
        evaluation_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (homework_id) REFERENCES homework(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        UNIQUE KEY unique_eval (homework_id, student_id)
    )";
    $pdo->exec($sqlEvaluation);

    echo "Database schema updated successfully (Homework Module).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
