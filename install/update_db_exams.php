<?php
// DB Schema Update Script for Examinations
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Exams Table
    // Defines the Exam Name (e.g. Mid Term, Final Term)
    $sqlExams = "CREATE TABLE IF NOT EXISTS exams (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlExams);

    // 2. Exam Schedules
    // Links an Exam to a Class/Section and specific Subjects with Date/Time/Marks.
    // In QDOCS, Exam Group -> Exam -> Exam Subject.
    // Simplified: Exam -> Class/Section -> Subject.
    $sqlExamSchedules = "CREATE TABLE IF NOT EXISTS exam_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        exam_id INT NOT NULL,
        class_id INT NOT NULL,
        section_id INT NOT NULL,
        subject_id INT NOT NULL,
        date_of_exam DATE,
        start_time TIME,
        end_time TIME,
        room_no VARCHAR(50),
        full_marks DECIMAL(5,2) DEFAULT 100.00,
        passing_marks DECIMAL(5,2) DEFAULT 33.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
        FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlExamSchedules);

    // 3. Exam Results
    // Stores the marks obtained by a student for a specific schedule item.
    $sqlExamResults = "CREATE TABLE IF NOT EXISTS exam_results (
        id INT AUTO_INCREMENT PRIMARY KEY,
        exam_schedule_id INT NOT NULL,
        student_id INT NOT NULL,
        get_marks DECIMAL(5,2) DEFAULT 0.00,
        is_absent ENUM('yes', 'no') DEFAULT 'no',
        note VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (exam_schedule_id) REFERENCES exam_schedules(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        UNIQUE KEY unique_result (exam_schedule_id, student_id)
    )";
    $pdo->exec($sqlExamResults);

    echo "Database schema updated successfully (Examination Module).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
