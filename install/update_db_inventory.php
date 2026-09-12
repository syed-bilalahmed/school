<?php
// DB Schema Update for Inventory
require_once 'config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Item Category
    $pdo->exec("CREATE TABLE IF NOT EXISTS item_category (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_category VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Item Store
    $pdo->exec("CREATE TABLE IF NOT EXISTS item_store (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_store VARCHAR(255) NOT NULL,
        code VARCHAR(100),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 3. Item Supplier
    $pdo->exec("CREATE TABLE IF NOT EXISTS item_supplier (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_supplier VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        email VARCHAR(100),
        address TEXT,
        contact_person_name VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Items
    $pdo->exec("CREATE TABLE IF NOT EXISTS items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_category_id INT,
        name VARCHAR(255) NOT NULL,
        unit VARCHAR(50),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (item_category_id) REFERENCES item_category(id) ON DELETE SET NULL
    )");

    // 5. Item Stock
    $pdo->exec("CREATE TABLE IF NOT EXISTS item_stock (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_id INT,
        supplier_id INT,
        store_id INT,
        symbol VARCHAR(10) DEFAULT '+',
        quantity INT,
        date DATE,
        attachment VARCHAR(255),
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
        FOREIGN KEY (supplier_id) REFERENCES item_supplier(id) ON DELETE SET NULL,
        FOREIGN KEY (store_id) REFERENCES item_store(id) ON DELETE SET NULL
    )");

    // 6. Item Issue
    $pdo->exec("CREATE TABLE IF NOT EXISTS item_issue (
        id INT AUTO_INCREMENT PRIMARY KEY,
        issue_type VARCHAR(50), -- staff, student
        issue_to INT, -- user_id
        issue_by VARCHAR(255), -- staff name
        issue_date DATE,
        return_date DATE,
        note TEXT,
        item_category_id INT,
        item_id INT,
        quantity INT,
        is_returned INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
    )");

    echo "Inventory tables created successfully.";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
