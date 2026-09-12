<?php
/**
 * install/apply_rbac_controllers.php
 * 
 * Script to automatically map basic permission checks to all controllers.
 * 
 * Maps generic controller names to specific permission keys defined in seed_rbac.php.
 */

$dir = 'c:/xampp/htdocs/school/app/Controllers/';
$files = glob($dir . '*.php');

$permissionMap = [
    // Academics
    'ClassesController.php' => 'manage_academics',
    'SectionsController.php' => 'manage_academics',
    'SubjectsController.php' => 'manage_academics',
    'TimetableController.php' => 'manage_academics', // or view_academics depending on method
    
    // Attendance
    'AttendanceController.php' => 'manage_attendance',
    
    // Exams
    'ExamController.php' => 'manage_exams',
    
    // Finance
    'FeesController.php' => 'manage_finance',
    'ExpenseController.php' => 'manage_finance',
    'PayrollController.php' => 'manage_finance',
    
    // Communication & FrontCMS
    'NoticeController.php' => 'manage_communication',
    'NotificationController.php' => 'manage_communication',
    'FrontCmsController.php' => 'manage_settings',
    
    // Inventory & Library
    'InventoryController.php' => 'manage_inventory',
    'LibraryController.php' => 'manage_library',
    
    // Front Office
    'FrontOfficeController.php' => 'manage_front_office',
    
    // Others
    'AdminController.php' => 'manage_settings',
    'SettingController.php' => 'manage_settings',
    'HomeworkController.php' => 'manage_academics',
    'CertificateController.php' => 'manage_academics'
];

foreach ($files as $file) {
    preg_match('#[\\\/]([^\\\/]+)$#', $file, $matches);
    $filename = $matches[1];
    
    if (!isset($permissionMap[$filename])) continue;
    
    $requiredPerm = $permissionMap[$filename];
    $content = file_get_contents($file);
    
    // Look for __construct where we inject requireSchoolContext
    // We want to add AuthGuard::requirePermission('$requiredPerm');
    
    // Check if it already has a permission check
    if (strpos($content, 'requirePermission(') !== false) {
        // echo "Skipping $filename - already has permission check\n";
        continue;
    }
    
    if (preg_match('/AuthGuard::requireSchoolContext\s*\(\)\s*;/i', $content, $match, PREG_OFFSET_CAPTURE)) {
         $pos = $match[0][1] + strlen($match[0][0]);
         $injection = "\n        AuthGuard::requirePermission('$requiredPerm');";
         $newContent = substr($content, 0, $pos) . $injection . substr($content, $pos);
         file_put_contents($file, $newContent);
         echo "Applied $requiredPerm to $filename\n";
    }
}
echo "RBAC Controller injection complete.\n";
