<?php
require_once 'config/config.php';
require_once 'app/Core/Database.php';

$db = new Database;

// 1. Student Categories
echo "Creating student_categories table...\n";
$db->query("CREATE TABLE IF NOT EXISTS student_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
$db->execute();

// Seed Categories
$db->query("SELECT id FROM student_categories");
if($db->rowCount() == 0){
    $db->query("INSERT INTO student_categories (category_name) VALUES ('General'), ('OBC'), ('SC'), ('ST')");
    $db->execute();
     echo "Seeded student_categories.\n";
}


// 2. Student Houses
echo "Creating student_houses table...\n";
$db->query("CREATE TABLE IF NOT EXISTS student_houses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    house_name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
$db->execute();

// Seed Houses
$db->query("SELECT id FROM student_houses");
if($db->rowCount() == 0){
    $db->query("INSERT INTO student_houses (house_name, description) VALUES ('Red House', 'Power'), ('Blue House', 'Wisdom'), ('Green House', 'Harmony'), ('Yellow House', 'Energy')");
    $db->execute();
    echo "Seeded student_houses.\n";
}

echo "Database update for Student Extras completed.\n";
