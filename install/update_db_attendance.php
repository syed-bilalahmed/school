<?php
// DB Schema Update Script for Attendance
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Student Attendance Table
    // Stores daily attendance for each student.
    // Unique constraint on (student_id, date) to prevent duplicate entries for same day.
    $sqlStudentAttendance = "CREATE TABLE IF NOT EXISTS student_attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        class_id INT NOT NULL,
        section_id INT NOT NULL,
        date DATE NOT NULL,
        attendance_type ENUM('Present', 'Absent', 'Late', 'Half Day') NOT NULL,
        remark VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
        FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
        UNIQUE KEY unique_student_date (student_id, date)
    )";
    $pdo->exec($sqlStudentAttendance);

    // 2. Staff Attendance Table
    // Stores daily attendance for staff/teachers.
    $sqlStaffAttendance = "CREATE TABLE IF NOT EXISTS staff_attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        staff_id INT NOT NULL,
        date DATE NOT NULL,
        attendance_type ENUM('Present', 'Absent', 'Late', 'Half Day') NOT NULL,
        remark VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_staff_date (staff_id, date)
    )";
    $pdo->exec($sqlStaffAttendance);

    echo "Database schema updated successfully (Attendance Module).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
