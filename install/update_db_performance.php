<?php
// Performance index update script
require_once 'config/config.php';

function tableExists(PDO $pdo, $tableName) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :db AND table_name = :table");
    $stmt->execute([':db' => DB_NAME, ':table' => $tableName]);
    return (int)$stmt->fetchColumn() > 0;
}

function indexExists(PDO $pdo, $tableName, $indexName) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = :db AND table_name = :table AND index_name = :idx");
    $stmt->execute([':db' => DB_NAME, ':table' => $tableName, ':idx' => $indexName]);
    return (int)$stmt->fetchColumn() > 0;
}

function addIndexIfMissing(PDO $pdo, $tableName, $indexName, $columnsSql) {
    if (!tableExists($pdo, $tableName)) {
        echo "Skipped: table '{$tableName}' does not exist.<br>";
        return;
    }

    if (indexExists($pdo, $tableName, $indexName)) {
        echo "Exists: {$tableName}.{$indexName}<br>";
        return;
    }

    $sql = "ALTER TABLE {$tableName} ADD INDEX {$indexName} ({$columnsSql})";
    $pdo->exec($sql);
    echo "Added: {$tableName}.{$indexName} ({$columnsSql})<br>";
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Students and users
    addIndexIfMissing($pdo, 'students', 'idx_students_class_section', 'class_id, section_id');
    addIndexIfMissing($pdo, 'students', 'idx_students_user_id', 'user_id');
    addIndexIfMissing($pdo, 'students', 'idx_students_roll_no', 'roll_no');
    addIndexIfMissing($pdo, 'users', 'idx_users_role', 'role');

    // Notifications
    addIndexIfMissing($pdo, 'notifications', 'idx_notifications_role_read_created', 'role, is_read, created_at');

    // Fees
    addIndexIfMissing($pdo, 'student_fees', 'idx_student_fees_student_id', 'student_id');
    addIndexIfMissing($pdo, 'fee_payments', 'idx_fee_payments_student_fee_date', 'student_fee_id, payment_date');

    // Exams
    addIndexIfMissing($pdo, 'exam_results', 'idx_exam_results_schedule_student', 'exam_schedule_id, student_id');
    addIndexIfMissing($pdo, 'exam_schedules', 'idx_exam_schedules_class_section', 'class_id, section_id');

    // Attendance
    addIndexIfMissing($pdo, 'student_attendance', 'idx_student_attendance_class_section_date', 'class_id, section_id, date');
    addIndexIfMissing($pdo, 'staff_attendance', 'idx_staff_attendance_staff_date', 'staff_id, date');

    echo "<br>Performance index update completed.";
} catch (PDOException $e) {
    die('DB ERROR: ' . $e->getMessage());
}
