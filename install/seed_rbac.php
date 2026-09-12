<?php
// install/seed_rbac.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_db');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Starting RBAC Seeding...\n";

    // 1. Define Standard Permissions
    $permissions = [
        'manage_students' => 'Can add, edit, or delete students',
        'view_students' => 'Can view student profiles and directories',
        'manage_academics' => 'Can manage classes, sections, and subjects',
        'view_academics' => 'Can view class and timetable details',
        'manage_attendance' => 'Can take and modify attendance records',
        'view_attendance' => 'Can view attendance records',
        'manage_exams' => 'Can create exams and enter marks',
        'view_exams' => 'Can view exam schedules and results',
        'manage_finance' => 'Can manage fees, expenses, and payroll',
        'view_finance' => 'Can view fee statements and basic finance info',
        'manage_communication' => 'Can post notices and send messages',
        'view_communication' => 'Can view notices and messages',
        'manage_inventory' => 'Can manage inventory stock and assets',
        'manage_library' => 'Can add books and issue them to users',
        'manage_front_office' => 'Can handle visitor logs and admission enquiries',
        'manage_settings' => 'Can modify system or CMS settings directly'
    ];

    echo "Seeding Permissions...\n";
    $stmt = $pdo->prepare("INSERT IGNORE INTO permissions (permission_key, description) VALUES (:key, :desc)");
    foreach ($permissions as $key => $desc) {
        $stmt->execute([':key' => $key, ':desc' => $desc]);
    }

    // Get all permission IDs
    $permIds = [];
    $stmt = $pdo->query("SELECT id, permission_key FROM permissions");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $permIds[$row['permission_key']] = $row['id'];
    }

    // 2. Define Standard Global Roles (school_id = NULL)
    // admin, teacher, student, parent, librarian, accountant
    $roles_map = [
        'super_admin' => array_keys($permissions), // Get all standard permissions
        'admin' => array_keys($permissions), // Admin of a school gets all school-level perms
        'teacher' => [
            'view_students', 'view_academics', 'manage_attendance', 'view_attendance', 
            'manage_exams', 'view_exams', 'view_communication', 'manage_communication'
        ],
        'student' => [
            'view_academics', 'view_attendance', 'view_exams', 'view_communication', 'view_finance'
        ],
        'parent' => [
            'view_students', 'view_academics', 'view_attendance', 'view_exams', 'view_communication', 'view_finance'
        ],
        'librarian' => [
            'view_students', 'manage_library', 'view_communication'
        ],
        'accountant' => [
            'view_students', 'manage_finance', 'view_finance', 'view_communication'
        ]
    ];

    echo "Seeding Global Roles and Permissions...\n";
    $roleStmt = $pdo->prepare("INSERT IGNORE INTO roles (school_id, name) VALUES (NULL, :name)");
    $getRoleStmt = $pdo->prepare("SELECT id FROM roles WHERE name = :name AND school_id IS NULL");
    $rolePermStmt = $pdo->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :perm_id)");

    foreach ($roles_map as $roleName => $assignedPermKeys) {
        $roleStmt->execute([':name' => $roleName]);
        
        $getRoleStmt->execute([':name' => $roleName]);
        $roleId = $getRoleStmt->fetchColumn();

        if ($roleId) {
            foreach ($assignedPermKeys as $permKey) {
                if (isset($permIds[$permKey])) {
                    $rolePermStmt->execute([
                        ':role_id' => $roleId,
                        ':perm_id' => $permIds[$permKey]
                    ]);
                }
            }
        }
    }

    echo "RBAC Seeding Completed Successfully!\n";

} catch (PDOException $e) {
    die("SEEDING ERROR: " . $e->getMessage() . "\n");
}
