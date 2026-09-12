<?php
// DB Schema Update Script for Parent Linkage
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add parent_user_id to students table
    // Check if column exists first
    $stmt = $pdo->prepare("SHOW COLUMNS FROM students LIKE 'parent_user_id'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE students ADD COLUMN parent_user_id INT NULL AFTER user_id,
                ADD FOREIGN KEY (parent_user_id) REFERENCES users(id) ON DELETE SET NULL";
        $pdo->exec($sql);
        echo "Added parent_user_id column to students table.<br>";
    } else {
        echo "parent_user_id column already exists.<br>";
    }

    echo "Database schema updated successfully (Parents).";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
