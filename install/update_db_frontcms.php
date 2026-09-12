<?php
// DB Schema Update for Front CMS
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Front CMS Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS front_cms_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        is_active_website ENUM('yes', 'no') DEFAULT 'yes',
        enable_online_admission ENUM('yes', 'no') DEFAULT 'yes',
        footer_text TEXT,
        facebook_url VARCHAR(255),
        twitter_url VARCHAR(255),
        instagram_url VARCHAR(255),
        youtube_url VARCHAR(255),
        google_plus_url VARCHAR(255),
        linkedin_url VARCHAR(255),
        logo VARCHAR(255),
        theme_color VARCHAR(50) DEFAULT 'default',
        layout_type VARCHAR(50) DEFAULT 'standard',
        maintenance_title VARCHAR(255) DEFAULT 'Site Maintenance in Progress',
        maintenance_message TEXT,
        maintenance_eta VARCHAR(255),
        maintenance_background VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Seed default settings if empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM front_cms_settings");
    if($stmt->fetchColumn() == 0){
        $pdo->exec("INSERT INTO front_cms_settings (is_active_website, enable_online_admission, footer_text, theme_color, layout_type, maintenance_title, maintenance_message, maintenance_eta) VALUES ('yes', 'yes', '© 2025 Smart School. All rights reserved.', 'default', 'standard', 'Site Maintenance in Progress', '', '')");
    }

    $missingColumns = [
        'enable_online_admission' => "ALTER TABLE front_cms_settings ADD COLUMN enable_online_admission ENUM('yes', 'no') DEFAULT 'yes' AFTER is_active_website",
        'theme_color' => "ALTER TABLE front_cms_settings ADD COLUMN theme_color VARCHAR(50) DEFAULT 'default' AFTER linkedin_url",
        'layout_type' => "ALTER TABLE front_cms_settings ADD COLUMN layout_type VARCHAR(50) DEFAULT 'standard' AFTER theme_color",
        'logo' => "ALTER TABLE front_cms_settings ADD COLUMN logo VARCHAR(255) NULL AFTER linkedin_url",
        'maintenance_title' => "ALTER TABLE front_cms_settings ADD COLUMN maintenance_title VARCHAR(255) DEFAULT 'Site Maintenance in Progress' AFTER layout_type",
        'maintenance_message' => "ALTER TABLE front_cms_settings ADD COLUMN maintenance_message TEXT NULL AFTER maintenance_title",
        'maintenance_eta' => "ALTER TABLE front_cms_settings ADD COLUMN maintenance_eta VARCHAR(255) NULL AFTER maintenance_message",
        'maintenance_background' => "ALTER TABLE front_cms_settings ADD COLUMN maintenance_background VARCHAR(255) NULL AFTER maintenance_eta"
    ];

    foreach($missingColumns as $column => $alterSql){
        $colStmt = $pdo->query("SHOW COLUMNS FROM front_cms_settings LIKE '{$column}'");
        if($colStmt->rowCount() == 0){
            $pdo->exec($alterSql);
        }
    }

    echo "Front CMS tables created successfully.";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
