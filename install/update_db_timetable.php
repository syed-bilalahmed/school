<?php
// DB Schema Update for Timetable
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Class Timetable Table
    // Stores weekly schedule slots
    $pdo->exec("CREATE TABLE IF NOT EXISTS class_timetables (
        id INT AUTO_INCREMENT PRIMARY KEY,
        class_id INT,
        section_id INT,
        subject_id INT,
        staff_id INT, -- Teacher
        day_name VARCHAR(20), -- Monday, Tuesday...
        time_from VARCHAR(20), -- 10:00 AM
        time_to VARCHAR(20), -- 11:00 AM
        room_no VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
        FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
    )");

    echo "Timetable table created successfully.";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
