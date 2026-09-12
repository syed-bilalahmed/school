<?php
require_once 'app/start.php';

$db = new Database;

// 1. Create Notifications Table
$sql = "CREATE TABLE IF NOT EXISTS notifications (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    role VARCHAR(50) DEFAULT 'admin', -- Target role (e.g., admin, super_admin)
    is_read INT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

try {
    $db->query($sql);
    $db->execute();
    echo "Table 'notifications' created successfully.<br>";
} catch (Throwable $e) {
    echo "Error creating table 'notifications'.<br>";
}

echo "Database update completed.";
