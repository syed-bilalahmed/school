<?php
// DB Schema Update Script for System Settings
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Sessions Table
    $sqlSession = "CREATE TABLE IF NOT EXISTS sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session VARCHAR(50) NOT NULL, -- e.g. '2023-24'
        is_active ENUM('yes', 'no') DEFAULT 'no',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlSession);
    
    // Seed generic session if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM sessions");
    if($stmt->fetchColumn() == 0){
        $pdo->exec("INSERT INTO sessions (session, is_active) VALUES ('2025-26', 'yes')");
    }

    // 2. Update site_settings if needed (ensure columns exist)
    // We assume site_settings exists from setup_db.php usually.
    // Let's ensure it has necessary columns.
    $sqlSettings = "CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_name VARCHAR(255),
        email VARCHAR(100),
        phone VARCHAR(50),
        address TEXT,
        logo VARCHAR(255),
        currency_symbol VARCHAR(10) DEFAULT '$',
        session_id INT, -- Current Session Link
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlSettings);

    // Ensure session_id column exists if table existed before
    try {
        $pdo->exec("ALTER TABLE site_settings ADD COLUMN session_id INT");
    } catch(PDOException $e) { /* Column likely exists */ }
    
    try {
        $pdo->exec("ALTER TABLE site_settings ADD COLUMN currency_symbol VARCHAR(10) DEFAULT '$'");
    } catch(PDOException $e) { /* Column likely exists */ }

    echo "Database schema updated successfully (System Settings).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
