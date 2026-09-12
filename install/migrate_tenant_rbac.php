<?php
// install/migrate_tenant_rbac.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Starting Tenant & RBAC Migration...\n";

    // 1. Create Core Tenant Tables
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

    // 2. Create RBAC Tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        school_id INT NULL, -- NULL means global role (like super_admin)
        name VARCHAR(100) NOT NULL,
        UNIQUE(school_id, name),
        FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        permission_key VARCHAR(100) NOT NULL UNIQUE,
        description VARCHAR(255)
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS role_permissions (
        role_id INT NOT NULL,
        permission_id INT NOT NULL,
        PRIMARY KEY (role_id, permission_id),
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
        FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS user_roles (
        user_id INT NOT NULL,
        role_id INT NOT NULL,
        PRIMARY KEY (user_id, role_id),
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
    )");

    // 3. Seed Default School if none exists
    $stmt = $pdo->query("SELECT * FROM schools LIMIT 1");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("INSERT INTO schools (code, name, domain) VALUES ('default', 'Default Main School', 'localhost')");
        $defaultSchoolId = $pdo->lastInsertId();
    } else {
        $defaultSchoolId = $stmt->fetch(PDO::FETCH_OBJ)->id;
    }

    echo "Default School ID: $defaultSchoolId\n";

    // 4. Alter existing tables to add school_id
    $tablesQuery = $pdo->query("SHOW TABLES");
    $excludeTables = ['schools', 'school_plans', 'school_subscriptions', 'roles', 'permissions', 'role_permissions', 'user_roles', 'site_settings']; // global tables
    
    while ($row = $tablesQuery->fetch(PDO::FETCH_NUM)) {
        $table = $row[0];
        if (in_array($table, $excludeTables)) continue;

        // Check if school_id exists
        $columnsQuery = $pdo->query("SHOW COLUMNS FROM `$table` LIKE 'school_id'");
        if ($columnsQuery->rowCount() == 0) {
            echo "Adding school_id to $table...\n";
            // Add column allowing NULL first
            $pdo->exec("ALTER TABLE `$table` ADD COLUMN school_id INT NULL");
            
            // Backfill existing rows with default school ID
            $pdo->exec("UPDATE `$table` SET school_id = $defaultSchoolId WHERE school_id IS NULL");
            
            // Add index
            $pdo->exec("ALTER TABLE `$table` ADD INDEX idx_school_id (school_id)");
        }
    }

    echo "Migration Completed Successfully!\n";

} catch (PDOException $e) {
    die("MIGRATION ERROR: " . $e->getMessage() . "\n");
}
