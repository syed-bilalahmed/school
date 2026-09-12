<?php
// install/update_db_saas_core.php
// Adds missing SaaS core schema pieces for tenant routing and auth.

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_db');

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Running SaaS core update...\n";

    // Core tenant tables from roadmap week-1 (idempotent)
    $pdo->exec("CREATE TABLE IF NOT EXISTS school_plans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        limits_json TEXT,
        price DECIMAL(10,2) DEFAULT 0.00
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS schools (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) NOT NULL UNIQUE,
        name VARCHAR(255) NOT NULL,
        domain VARCHAR(255) NULL UNIQUE,
        status ENUM('active', 'suspended', 'pending') DEFAULT 'active',
        plan_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (plan_id) REFERENCES school_plans(id) ON DELETE SET NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS school_subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NOT NULL,
        plan_id INT NOT NULL,
        status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
        start_at DATE,
        end_at DATE,
        billing_provider VARCHAR(50),
        external_id VARCHAR(100),
        FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE,
        FOREIGN KEY (plan_id) REFERENCES school_plans(id)
    )");

    // Ensure at least one default school exists
    $exists = $pdo->query("SELECT id FROM schools LIMIT 1")->fetch(PDO::FETCH_OBJ);
    if (!$exists) {
        $pdo->exec("INSERT INTO schools (code, name, domain, status) VALUES ('default', 'Default Main School', 'localhost', 'active')");
        echo "Inserted default school.\n";
    }

    $defaultSchool = $pdo->query("SELECT id FROM schools ORDER BY id ASC LIMIT 1")->fetch(PDO::FETCH_OBJ);
    $defaultSchoolId = $defaultSchool ? (int)$defaultSchool->id : 1;

    // Ensure users table has school_id + index + backfill
    $usersHasSchool = $pdo->query("SHOW COLUMNS FROM users LIKE 'school_id'")->rowCount() > 0;
    if (!$usersHasSchool) {
        $pdo->exec("ALTER TABLE users ADD COLUMN school_id INT NULL");
        echo "Added users.school_id.\n";
    }
    $pdo->exec("UPDATE users SET school_id = $defaultSchoolId WHERE school_id IS NULL");

    $usersIdx = $pdo->query("SHOW INDEX FROM users WHERE Key_name = 'idx_users_school_id'")->rowCount() > 0;
    if (!$usersIdx) {
        $pdo->exec("ALTER TABLE users ADD INDEX idx_users_school_id (school_id)");
        echo "Added idx_users_school_id.\n";
    }

    // Keep existing global super_admin support but enforce tenant uniqueness for regular users.
    $usersEmailSchoolUnique = $pdo->query("SHOW INDEX FROM users WHERE Key_name = 'uniq_users_school_email'")->rowCount() > 0;
    if (!$usersEmailSchoolUnique) {
        $pdo->exec("ALTER TABLE users ADD UNIQUE KEY uniq_users_school_email (school_id, email)");
        echo "Added uniq_users_school_email.\n";
    }

    echo "SaaS core update completed successfully.\n";
} catch (PDOException $e) {
    die('UPDATE ERROR: ' . $e->getMessage() . "\n");
}
