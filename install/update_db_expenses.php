<?php
// DB Schema Update for Expenses
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Expense Heads Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS expense_heads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        exp_category VARCHAR(255) NOT NULL,
        description TEXT,
        is_active ENUM('yes', 'no') DEFAULT 'yes',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Expenses Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS expenses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        exp_head_id INT,
        name VARCHAR(255) NOT NULL,
        invoice_no VARCHAR(100),
        date DATE,
        amount DECIMAL(10,2),
        documents VARCHAR(255),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (exp_head_id) REFERENCES expense_heads(id) ON DELETE SET NULL
    )");

    // Seed some heads
    $stmt = $pdo->query("SELECT COUNT(*) FROM expense_heads");
    if($stmt->fetchColumn() == 0){
        $pdo->exec("INSERT INTO expense_heads (exp_category) VALUES ('Electricity Bill'), ('Internet'), ('Stationery'), ('Maintenance'), ('Salaries')");
    }

    echo "Expenses tables created successfully.";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
