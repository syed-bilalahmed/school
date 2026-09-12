<?php
// Standalone setup script
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create DB
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $pdo->exec("USE " . DB_NAME);

    // Users Table
    $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('super_admin', 'admin', 'student', 'teacher', 'parent') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlUsers);

    // Site Settings Table
    $sqlSettings = "CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(255) NOT NULL UNIQUE,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlSettings);

    // Seed Super Admin
    $password = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, email, password, role) VALUES ('Super Admin', 'super@admin.com', ?, 'super_admin')");
    $stmt->execute([$password]);

    // Seed Default Settings
    $settings = [
        'school_name' => 'My High School',
        'school_address' => '123 Education Lane',
        'contact_email' => 'info@school.com',
        'hero_title' => 'Welcome to Excellence',
        'hero_description' => 'Empowering the next generation of leaders.'
    ];

    foreach($settings as $key => $val){
        $stmt = $pdo->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute([$key, $val]);
    }

    echo "Database initialized successfully.";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
