<?php
// DB Schema Update Script for Payroll (HR)
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Staff Payroll / Salary Structure
    // Defines the basic salary and allowances/deductions for a staff member.
    $sqlStaffPayroll = "CREATE TABLE IF NOT EXISTS staff_payroll (
        id INT AUTO_INCREMENT PRIMARY KEY,
        staff_id INT NOT NULL,
        basic_salary DECIMAL(10, 2) NOT NULL,
        earnings DECIMAL(10, 2) DEFAULT 0.00, -- Total Earnings (Allowance etc)
        deductions DECIMAL(10, 2) DEFAULT 0.00, -- Total Deductions (Tax etc)
        net_salary DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_staff_payroll (staff_id)
    )";
    $pdo->exec($sqlStaffPayroll);

    // 2. Staff Payslips (Monthly Generated Salary)
    $sqlStaffPayslips = "CREATE TABLE IF NOT EXISTS staff_payslips (
        id INT AUTO_INCREMENT PRIMARY KEY,
        staff_id INT NOT NULL,
        month VARCHAR(20) NOT NULL, -- e.g. 'January 2025'
        year INT NOT NULL,
        basic_salary DECIMAL(10, 2) NOT NULL,
        total_allowance DECIMAL(10, 2) DEFAULT 0.00,
        total_deduction DECIMAL(10, 2) DEFAULT 0.00,
        net_salary DECIMAL(10, 2) NOT NULL,
        status ENUM('Generated', 'Paid') DEFAULT 'Generated',
        payment_mode VARCHAR(50),
        payment_date DATE,
        note TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_staff_payslip (staff_id, month, year)
    )";
    $pdo->exec($sqlStaffPayslips);

    echo "Database schema updated successfully (HR/Payroll Module).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
