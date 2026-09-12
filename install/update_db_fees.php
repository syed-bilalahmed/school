<?php
// DB Schema Update Script for Fees
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Fee Types (e.g., Admission Fee, Tuition Fee, Exam Fee)
    $sqlFeeTypes = "CREATE TABLE IF NOT EXISTS fee_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type_name VARCHAR(100) NOT NULL,
        type_code VARCHAR(50) NOT NULL UNIQUE,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlFeeTypes);

    // 2. Fee Groups (e.g., Class 1 General, Class 10 Science)
    $sqlFeeGroups = "CREATE TABLE IF NOT EXISTS fee_groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlFeeGroups);

    // 3. Fee Master / Session (Linking Groups -> Types with Amount)
    // This defines what fees are in a group.
    $sqlFeeGroupsTypes = "CREATE TABLE IF NOT EXISTS fee_groups_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fee_group_id INT NOT NULL,
        fee_type_id INT NOT NULL,
        amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        due_date DATE,
        fine_amount DECIMAL(10, 2) DEFAULT 0.00,
        FOREIGN KEY (fee_group_id) REFERENCES fee_groups(id) ON DELETE CASCADE,
        FOREIGN KEY (fee_type_id) REFERENCES fee_types(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlFeeGroupsTypes);

    // 4. Student Fee Payment (Tracking payments)
    // This tracks which student has paid what part of the fee master.
    // In a session-based system, we usually assign fee_groups to students or classes.
    // For simplicity, we will assume a direct tracking: Student -> FeeGroupType -> Payment
    
    // First, verify assignments (Allocated fees to students)
    // In QDOCS, you "Assign" a Fee Group to a Class (Allocating it to all students) or specific students.
    // We'll create a table to track these allocations if we want to track "Due" vs "Paid".
    
    $sqlStudentFees = "CREATE TABLE IF NOT EXISTS student_fees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        fee_groups_types_id INT NOT NULL,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (fee_groups_types_id) REFERENCES fee_groups_types(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlStudentFees);

    // 5. Fee Payments (The actual transaction)
    $sqlFeePayments = "CREATE TABLE IF NOT EXISTS fee_payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_fee_id INT NOT NULL,
        mode ENUM('Cash', 'Cheque', 'Online', 'Bank Transfer') NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        fine DECIMAL(10, 2) DEFAULT 0.00,
        discount DECIMAL(10, 2) DEFAULT 0.00,
        payment_date DATE NOT NULL,
        transaction_id VARCHAR(100),
        note TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_fee_id) REFERENCES student_fees(id) ON DELETE CASCADE
    )";
    $pdo->exec($sqlFeePayments);

    echo "Database schema updated successfully (Fees Module).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
