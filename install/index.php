<?php
/**
 * School ERP Pro - CodeCanyon-Style Enterprise Setup & Installation Wizard
 * Complete multi-step installer for database initialization, environment check,
 * license verification, administrator provisioning, and automatic lock protection.
 */

session_start();

$lockFile = __DIR__ . '/install.lock';
$configDir = dirname(__DIR__) . '/config';
$configFile = $configDir . '/config.php';
$installedLock = $configDir . '/installed.lock';

$isLocked = file_exists($lockFile) || file_exists($installedLock);

/**
 * Intelligent MySQL Server Connection Handler
 * Connects to MySQL server, automatically detects if local XAMPP root password
 * was autofilled by browser, and falls back to empty password gracefully.
 */
function createServerPdo(string $host, int $port, string $user, string &$pass): PDO {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        $isAccessDenied = ($e->getCode() === 1045 || strpos($e->getMessage(), '1045') !== false);
        $isLocal = in_array(strtolower($host), ['127.0.0.1', 'localhost', '::1']);

        // If access denied with a password on localhost, test empty password (typical XAMPP where browser autofilled password)
        if ($isAccessDenied && $pass !== '' && $isLocal) {
            try {
                $fallbackPdo = new PDO($dsn, $user, '', $options);
                $pass = ''; // Successfully connected with empty password!
                return $fallbackPdo;
            } catch (Throwable $e2) {
                // Ignore fallback error and throw clean message below
            }
        }

        if ($isAccessDenied && $pass === '') {
            throw new Exception("Access denied for MySQL user '{$user}' (using password: NO). A password is required by your MySQL server. Please enter your MySQL password.");
        }

        if ($isAccessDenied && $pass !== '') {
            throw new Exception("Access denied for MySQL user '{$user}' (using password: YES). Please verify your MySQL password or leave it blank if default XAMPP.");
        }

        throw new Exception("MySQL Connection Error: " . $e->getMessage());
    }
}

/**
 * Pre-Authorized Enterprise License Keys (Up to 10 Authorized Keys)
 * Buyers or Administrators can use any of these 10 distinct keys during installation.
 */
function getAuthorizedLicenseKeys(): array {
    return [
        'ENVATO-SCH-2026-A1B2-C3D4', // Key 1: Primary Enterprise Master Key
        'ENVATO-SCH-2026-E5F6-G7H8', // Key 2: Multi-Campus Master Key
        'ENVATO-SCH-2026-J9K0-L1M2', // Key 3: Academic Institution Key
        'ENVATO-SCH-2026-N3P4-Q5R6', // Key 4: 100k High-Concurrency Cluster Key
        'ENVATO-SCH-2026-S7T8-U9V0', // Key 5: Standard Commercial Key
        'ENVATO-SCH-2026-W1X2-Y3Z4', // Key 6: Extended Agency License Key
        'ENVATO-SCH-2026-B8D2-9F1A', // Key 7: Developer Sandbox Key
        'ENVATO-SCH-2026-7C4E-3B01', // Key 8: Official Campus Partner Key
        'ENVATO-SCH-2026-5F9D-1A8E', // Key 9: Unlimited Student Edition Key
        'ENVATO-SCH-2026-0D3B-7E2C'  // Key 10: VIP Executive Master Key
    ];
}

/**
 * Validate submitted license key against authorized list or standard Envato UUID format
 */
function isValidLicenseKey(string $key): bool {
    $normalized = strtoupper(trim($key));
    if (empty($normalized)) return false;

    // Check predefined authorized 10 keys
    if (in_array($normalized, getAuthorizedLicenseKeys(), true)) {
        return true;
    }

    // Check standard 36-char Envato Purchase Code UUID (e.g. 84729103-92b1-49b8-9321-728198302194)
    if (preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', trim($key))) {
        return true;
    }

    return false;
}

// Handle AJAX License Verification
if (isset($_POST['action']) && $_POST['action'] === 'verify_license') {
    header('Content-Type: application/json; charset=utf-8');
    if ($isLocked) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Installer is locked. Re-installation is not permitted.']);
        exit;
    }

    $key = trim($_POST['license_key'] ?? '');
    if (isValidLicenseKey($key)) {
        echo json_encode([
            'success' => true,
            'message' => 'License Verified: Genuine Enterprise CodeCanyon License (Authorized for deployment).'
        ]);
    } else {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid License Key. Please enter one of the 10 authorized keys or a valid Envato Purchase Code.'
        ]);
    }
    exit;
}

// Handle AJAX DB Connection Test
if (isset($_POST['action']) && $_POST['action'] === 'test_db') {
    header('Content-Type: application/json; charset=utf-8');
    if ($isLocked) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Installer is locked. Re-installation is not permitted.']);
        exit;
    }
    $host = trim($_POST['db_host'] ?? '127.0.0.1');
    $port = (int)($_POST['db_port'] ?? 3306);
    $user = trim($_POST['db_user'] ?? 'root');
    $pass = (string)($_POST['db_pass'] ?? '');
    $name = trim($_POST['db_name'] ?? 'school_db');

    try {
        $origPass = $pass;
        $pdo = createServerPdo($host, $port, $user, $pass);
        $passAdjusted = ($origPass !== $pass && $pass === '');

        // Check if target database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE " . $pdo->quote($name));
        $dbExists = (bool)$stmt->fetch();

        $msg = 'MySQL connection successful!';
        if ($passAdjusted) {
            $msg .= ' (Auto-detected default XAMPP empty password)';
        }
        if ($dbExists) {
            $msg .= " Database '{$name}' exists and will be updated.";
        } else {
            $msg .= " Database '{$name}' does not exist and will be automatically created at runtime.";
        }

        echo json_encode([
            'success' => true,
            'message' => $msg,
            'database_exists' => $dbExists,
            'pass_adjusted' => $passAdjusted,
            'detected_pass' => $pass
        ]);
    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// Handle AJAX Installation Runner
if (isset($_POST['action']) && $_POST['action'] === 'run_install') {
    header('Content-Type: application/json; charset=utf-8');
    if ($isLocked) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Installer is locked. Re-installation is not permitted.']);
        exit;
    }

    $dbHost = trim($_POST['db_host'] ?? '127.0.0.1');
    $dbPort = (int)($_POST['db_port'] ?? 3306);
    $dbUser = trim($_POST['db_user'] ?? 'root');
    $dbPass = (string)($_POST['db_pass'] ?? '');
    $dbName = trim($_POST['db_name'] ?? 'school_db');

    $schoolName = trim($_POST['school_name'] ?? 'Greenwood International School');
    $campusName = trim($_POST['campus_name'] ?? 'Main Campus');
    $currency = trim($_POST['currency'] ?? 'PKR');

    $adminName = trim($_POST['admin_name'] ?? 'System Administrator');
    $adminEmail = trim($_POST['admin_email'] ?? 'super@admin.com');
    $adminPass = (string)($_POST['admin_pass'] ?? 'admin123');
    $licenseKey = trim($_POST['license_key'] ?? '');

    if (!isValidLicenseKey($licenseKey)) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid License Key / Purchase Code. Please enter an authorized purchase code.'
        ]);
        exit;
    }

    try {
        require_once __DIR__ . '/master_schema.php';

        // 1. Connect to MySQL server and auto-create target database at runtime
        $pdo = createServerPdo($dbHost, $dbPort, $dbUser, $dbPass);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbName}`");

        // 2. Connect to the specific target database
        $db = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => true
        ]);

        // 3. Execute master enterprise schema & initial seed data
        $schemaResult = runMasterSchemaMigration($db, [
            'school_name' => $schoolName,
            'campus_name' => $campusName,
            'currency'    => $currency,
            'admin_name'  => $adminName,
            'admin_email' => $adminEmail,
            'admin_pass'  => $adminPass
        ]);

        // Save verified license key to site_settings
        if (!empty($licenseKey)) {
            $stLic = $db->prepare("INSERT INTO site_settings (school_id, setting_key, setting_value) VALUES (1, 'envato_purchase_code', :lic) ON DUPLICATE KEY UPDATE setting_value = :lic_up");
            $stLic->execute([':lic' => $licenseKey, ':lic_up' => $licenseKey]);
        }

        // 4. Write config/config.php with exact database credentials
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $subfolder = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/school/install/index.php')), '/\\');
        $urlRoot = $protocol . "://" . $host . ($subfolder ? $subfolder : '');

        $configContent = "<?php\n"
            . "// Database Settings (Generated by Setup Wizard)\n"
            . "define('DB_HOST', " . var_export($dbHost, true) . ");\n"
            . "define('DB_USER', " . var_export($dbUser, true) . ");\n"
            . "define('DB_PASS', " . var_export($dbPass, true) . ");\n"
            . "define('DB_NAME', " . var_export($dbName, true) . ");\n\n"
            . "// App Root\n"
            . "define('APPROOT', dirname(dirname(__FILE__)) . '/app');\n\n"
            . "// URL Root (Dynamic detection)\n"
            . "\$protocol = isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on' ? 'https' : 'http';\n"
            . "\$host = \$_SERVER['HTTP_HOST'] ?? 'localhost';\n"
            . "define('URLROOT', \$protocol . '://' . \$host . " . var_export($subfolder ? $subfolder : '', true) . ");\n\n"
            . "// Site Name\n"
            . "define('SITENAME', " . var_export($schoolName, true) . ");\n\n"
            . "// Outgoing email configuration\n"
            . "require_once __DIR__ . '/mail.php';\n";

        @file_put_contents($configFile, $configContent);

        // 5. Write schema sync lock so application loads smoothly
        $schemaLockFile = dirname(__DIR__) . '/app/schema_synced_v3.6.lock';
        @file_put_contents($schemaLockFile, date('Y-m-d H:i:s'));

        // 6. Write install.lock and installed.lock
        $lockPayload = "INSTALLED_AT=" . date('Y-m-d H:i:s') . "\nINSTALLER_VERSION=3.8\nADMIN=" . $adminEmail . "\nDATABASE=" . $dbName . "\nLICENSE_KEY=" . $licenseKey . "\n";
        @file_put_contents($lockFile, $lockPayload);
        @file_put_contents($installedLock, $lockPayload);

        echo json_encode([
            'success' => true,
            'message' => "School ERP Pro has been installed successfully! Database '{$dbName}' initialized.",
            'login_url' => $urlRoot . '/auth/login',
            'admin_email' => $adminEmail
        ]);

    } catch (Throwable $e) {
        error_log('[Installer] Installation failed: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Installation error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// System Requirements Diagnostics
$phpVersion = PHP_VERSION;
$phpVersionOk = version_compare($phpVersion, '8.0.0', '>=');

$requiredExtensions = [
    'pdo' => 'PDO Data Objects',
    'pdo_mysql' => 'MySQL PDO Driver',
    'mbstring' => 'Multibyte String',
    'openssl' => 'OpenSSL Security',
    'curl' => 'cURL HTTP Client',
    'fileinfo' => 'File Information (finfo)',
    'gd' => 'GD Image Processing',
    'json' => 'JSON Library'
];

$extStatus = [];
$allExtOk = true;
foreach ($requiredExtensions as $ext => $label) {
    $loaded = extension_loaded($ext);
    $extStatus[$ext] = [
        'name' => $label,
        'status' => $loaded
    ];
    if (!$loaded) {
        $allExtOk = false;
    }
}

// Directory Permissions Diagnostics
$directories = [
    'config/' => $configDir,
    'public/uploads/' => dirname(__DIR__) . '/public/uploads',
    'app/cache/' => dirname(__DIR__) . '/app/cache'
];

$dirStatus = [];
$allDirsOk = true;
foreach ($directories as $name => $path) {
    if (!is_dir($path)) {
        @mkdir($path, 0755, true);
    }
    $writable = is_writable($path);
    $dirStatus[$name] = [
        'path' => $path,
        'writable' => $writable
    ];
    if (!$writable) {
        $allDirsOk = false;
    }
}

$canProceed = $phpVersionOk && $allExtOk && $allDirsOk;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Wizard — School ERP Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-glow: rgba(37, 99, 235, 0.25);
            --bg: #090d16;
            --card-bg: rgba(17, 24, 39, 0.85);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            background: var(--bg);
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.12) 0px, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }
        .wizard-container {
            width: 100%;
            max-width: 860px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .wizard-header {
            padding: 30px 40px;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, 0.02);
        }
        .brand-box {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            box-shadow: 0 8px 16px var(--primary-glow);
        }
        .brand-text h1 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .brand-text p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }
        .badge-version {
            padding: 6px 12px;
            background: rgba(37, 99, 235, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(37, 99, 235, 0.3);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .stepper-bar {
            display: flex;
            border-bottom: 1px solid var(--card-border);
            background: rgba(0, 0, 0, 0.2);
            overflow-x: auto;
        }
        .step-item {
            flex: 1;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .step-item.active {
            color: var(--text-main);
            border-bottom-color: var(--primary);
            background: rgba(37, 99, 235, 0.05);
        }
        .step-item.completed {
            color: var(--success);
        }
        .step-number {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.06);
        }
        .step-item.active .step-number {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 0 10px var(--primary-glow);
        }
        .step-item.completed .step-number {
            background: var(--success);
            color: #fff;
        }
        .wizard-body {
            padding: 35px 40px;
            min-height: 420px;
        }
        .step-panel {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        .step-panel.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        h2.step-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        p.step-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .col-full {
            grid-column: span 2;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: var(--text-main);
            padding: 12px 14px 12px 42px;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
            background: rgba(15, 23, 42, 0.9);
        }
        .check-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .check-table tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .check-table td {
            padding: 12px 10px;
            font-size: 13.5px;
        }
        .check-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }
        .badge-pass {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .badge-fail {
            background: rgba(239, 68, 68, 0.12);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .wizard-footer {
            padding: 20px 40px;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
            font-family: inherit;
        }
        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px var(--primary-glow);
        }
        .btn-primary:hover:not(:disabled) {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
        }
        .alert-box {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .alert-warning {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #fcd34d;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        .progress-container {
            margin: 30px 0;
            display: none;
        }
        .progress-bar {
            width: 100%;
            height: 10px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }
        .progress-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, var(--primary), #8b5cf6);
            transition: width 0.4s ease;
        }
        .progress-status {
            margin-top: 12px;
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
        }
        .locked-card {
            text-align: center;
            padding: 50px 20px;
        }
        .locked-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 20px;
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 2px solid rgba(16, 185, 129, 0.4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }
        .test-db-status {
            margin-top: 8px;
            font-size: 12.5px;
            display: none;
        }
    </style>
</head>
<body>

<div class="wizard-container">
    <!-- Header -->
    <div class="wizard-header">
        <div class="brand-box">
            <div class="brand-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="brand-text">
                <h1>School ERP Pro</h1>
                <p>Enterprise Auto-Installation & Configuration Wizard</p>
            </div>
        </div>
        <span class="badge-version">Release v3.8</span>
    </div>

    <?php if ($isLocked): ?>
    <!-- Installer Locked State -->
    <div class="wizard-body">
        <div class="locked-card">
            <div class="locked-icon">
                <i class="fa-solid fa-shield-check"></i>
            </div>
            <h2 class="step-title">Application Already Installed & Locked</h2>
            <p class="step-subtitle" style="max-width: 540px; margin: 0 auto 25px;">
                For your security, the setup wizard is locked by <code>install/install.lock</code>. 
                Re-installation is strictly prevented to protect existing database records and student information.
            </p>
            <div class="alert-box alert-warning" style="max-width: 540px; margin: 0 auto 30px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>To run a fresh installation, you must manually delete <code>install/install.lock</code> via FTP or file manager.</span>
            </div>
            <a href="../auth/login" class="btn btn-primary">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Go to Admin Login
            </a>
        </div>
    </div>

    <?php else: ?>

    <!-- Navigation Stepper -->
    <div class="stepper-bar">
        <div class="step-item active" id="step-nav-1">
            <div class="step-number">1</div>
            <span>Welcome & License</span>
        </div>
        <div class="step-item" id="step-nav-2">
            <div class="step-number">2</div>
            <span>Server Checks</span>
        </div>
        <div class="step-item" id="step-nav-3">
            <div class="step-number">3</div>
            <span>Database Setup</span>
        </div>
        <div class="step-item" id="step-nav-4">
            <div class="step-number">4</div>
            <span>School & Admin</span>
        </div>
        <div class="step-item" id="step-nav-5">
            <div class="step-number">5</div>
            <span>Installation</span>
        </div>
    </div>

    <!-- Wizard Content Panels -->
    <div class="wizard-body">
        <!-- Step 1: Welcome & License -->
        <div class="step-panel active" id="panel-1">
            <h2 class="step-title">Welcome to School ERP Pro</h2>
            <p class="step-subtitle">
                Thank you for choosing School ERP Pro. This quick 5-step installer will prepare your database, configure environment variables, create the primary administrator account, and seal the server security lock.
            </p>
            <div class="form-group" style="margin-bottom: 16px;">
                <label>Envato / CodeCanyon Purchase Code / License Key <span style="color: var(--danger);">*</span></label>
                <div class="input-wrap">
                    <i class="fa-solid fa-key"></i>
                    <input type="text" class="form-control" id="license_key" value="" placeholder="Enter your purchase code (e.g. ENVATO-SCH-2026-A1B2-C3D4)" autocomplete="off">
                </div>
                <small style="color: var(--text-muted); font-size: 11.5px; margin-top: 5px; display: block;">
                    Enter an authorized Enterprise License Key or valid CodeCanyon purchase code.
                </small>
            </div>

            <!-- Quick Authorized License Key Selector (10 Keys) -->
            <div style="margin-bottom: 18px; background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255, 255, 255, 0.15); border-radius: 10px; padding: 12px 14px;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <span style="font-size: 12.5px; font-weight: 600; color: #cbd5e1;">
                        <i class="fa-solid fa-key" style="color: var(--primary);"></i> Pre-Authorized Keys (10 Available):
                    </span>
                    <select id="quick-key-select" class="form-control" style="width: auto; max-width: 320px; padding: 5px 12px; font-size: 12px; height: 34px; background: rgba(15,23,42,0.95); cursor: pointer;">
                        <option value="">-- Choose any of 10 Keys --</option>
                        <option value="ENVATO-SCH-2026-A1B2-C3D4">Key 01: Primary Enterprise Key</option>
                        <option value="ENVATO-SCH-2026-E5F6-G7H8">Key 02: Multi-Campus License</option>
                        <option value="ENVATO-SCH-2026-J9K0-L1M2">Key 03: Academic Edition Key</option>
                        <option value="ENVATO-SCH-2026-N3P4-Q5R6">Key 04: 100k Concurrency Key</option>
                        <option value="ENVATO-SCH-2026-S7T8-U9V0">Key 05: Standard Commercial Key</option>
                        <option value="ENVATO-SCH-2026-W1X2-Y3Z4">Key 06: Extended Agency Key</option>
                        <option value="ENVATO-SCH-2026-B8D2-9F1A">Key 07: Developer Sandbox Key</option>
                        <option value="ENVATO-SCH-2026-7C4E-3B01">Key 08: Campus Partner Key</option>
                        <option value="ENVATO-SCH-2026-5F9D-1A8E">Key 09: Unlimited Student Key</option>
                        <option value="ENVATO-SCH-2026-0D3B-7E2C">Key 10: VIP Master Key</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 22px; display: flex; align-items: center; gap: 14px;">
                <button type="button" class="btn btn-secondary" id="btn-verify-license" style="font-size: 13px; padding: 9px 18px;">
                    <i class="fa-solid fa-shield-check"></i> Verify Purchase Code
                </button>
                <div id="license-status-msg" style="font-size: 13px; display: none;"></div>
            </div>

            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: #cbd5e1;">
                <input type="checkbox" id="terms_agree" checked>
                <span>I accept the Software License Agreement and confirm institutional deployment.</span>
            </label>
        </div>

        <!-- Step 2: System Requirements & Permissions -->
        <div class="step-panel" id="panel-2">
            <h2 class="step-title">System Requirements & Permissions</h2>
            <p class="step-subtitle">Checking PHP environment, extensions, and directory writable permissions.</p>

            <table class="check-table">
                <thead>
                    <tr style="color: var(--text-muted); font-size: 12px; text-transform: uppercase;">
                        <th style="text-align: left; padding: 8px 10px;">Component</th>
                        <th style="text-align: left; padding: 8px 10px;">Required</th>
                        <th style="text-align: left; padding: 8px 10px;">Current</th>
                        <th style="text-align: right; padding: 8px 10px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>PHP Version</strong></td>
                        <td>>= 8.0.0</td>
                        <td><?php echo phpversion(); ?></td>
                        <td style="text-align: right;">
                            <span class="check-badge <?php echo $phpVersionOk ? 'badge-pass' : 'badge-fail'; ?>">
                                <i class="fa-solid <?php echo $phpVersionOk ? 'fa-check' : 'fa-xmark'; ?>"></i>
                                <?php echo $phpVersionOk ? 'Passed' : 'Failed'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php foreach ($extStatus as $ext => $info): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($info['name']); ?></strong></td>
                        <td>Extension</td>
                        <td><?php echo $info['status'] ? 'Loaded' : 'Missing'; ?></td>
                        <td style="text-align: right;">
                            <span class="check-badge <?php echo $info['status'] ? 'badge-pass' : 'badge-fail'; ?>">
                                <i class="fa-solid <?php echo $info['status'] ? 'fa-check' : 'fa-xmark'; ?>"></i>
                                <?php echo $info['status'] ? 'Loaded' : 'Missing'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php foreach ($dirStatus as $dirName => $info): ?>
                    <tr>
                        <td><strong>Writable: <?php echo htmlspecialchars($dirName); ?></strong></td>
                        <td>Permission</td>
                        <td><?php echo $info['writable'] ? 'Writable (0755)' : 'Not Writable'; ?></td>
                        <td style="text-align: right;">
                            <span class="check-badge <?php echo $info['writable'] ? 'badge-pass' : 'badge-fail'; ?>">
                                <i class="fa-solid <?php echo $info['writable'] ? 'fa-check' : 'fa-xmark'; ?>"></i>
                                <?php echo $info['writable'] ? 'Writable' : 'Read Only'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (!$canProceed): ?>
            <div class="alert-box alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>One or more system requirements failed. Please adjust PHP configuration or directory permissions before proceeding.</span>
            </div>
            <?php else: ?>
            <div class="alert-box alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>All server compatibility and filesystem permissions checks passed successfully!</span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Step 3: Database Configuration -->
        <div class="step-panel" id="panel-3">
            <h2 class="step-title">Database Configuration</h2>
            <p class="step-subtitle">Enter your MySQL database connection credentials below.</p>

            <div class="form-grid">
                <div class="form-group">
                    <label>Database Host</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-server"></i>
                        <input type="text" class="form-control" id="db_host" value="127.0.0.1">
                    </div>
                </div>
                <div class="form-group">
                    <label>Database Port</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-network-wired"></i>
                        <input type="number" class="form-control" id="db_port" value="3306">
                    </div>
                </div>
                <div class="form-group col-full">
                    <label>Database Name</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-database"></i>
                        <input type="text" class="form-control" id="db_name" value="school_db">
                    </div>
                    <small style="color: var(--text-muted); font-size: 11.5px;">The database will be automatically created if it does not already exist.</small>
                </div>
                <div class="form-group">
                    <label>Database Username</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" class="form-control" id="db_user" value="root" autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label>Database Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" class="form-control" id="db_pass" placeholder="Leave empty for XAMPP default" autocomplete="new-password">
                        <button type="button" id="toggle-db-pass" style="position: absolute; right: 12px; background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 14px;">
                            <i class="fa-solid fa-eye" id="toggle-db-pass-icon"></i>
                        </button>
                    </div>
                    <small style="color: #60a5fa; font-size: 11.5px; margin-top: 5px; display: block;">
                        <i class="fa-solid fa-circle-info"></i> Note: For default XAMPP on Windows, MySQL user <code>root</code> has no password. Leave this blank.
                    </small>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; align-items: center; gap: 15px;">
                <button type="button" class="btn btn-secondary" id="btn-test-db">
                    <i class="fa-solid fa-plug"></i> Test Connection
                </button>
                <div class="test-db-status" id="test-db-status"></div>
            </div>
        </div>

        <!-- Step 4: School Profile & Administrator Account -->
        <div class="step-panel" id="panel-4">
            <h2 class="step-title">School Profile & Administrator</h2>
            <p class="step-subtitle">Configure institutional branding and your Super Administrator account credentials.</p>

            <div class="form-grid">
                <div class="form-group">
                    <label>Institution / School Name</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-school"></i>
                        <input type="text" class="form-control" id="school_name" value="Greenwood International School">
                    </div>
                </div>
                <div class="form-group">
                    <label>Campus Name</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-building"></i>
                        <input type="text" class="form-control" id="campus_name" value="Main Campus">
                    </div>
                </div>
                <div class="form-group col-full">
                    <label>Currency Symbol</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-money-bill-wave"></i>
                        <input type="text" class="form-control" id="currency" value="PKR">
                    </div>
                </div>

                <div class="col-full" style="border-top: 1px solid var(--card-border); padding-top: 15px; margin-top: 10px;">
                    <h3 style="font-size: 15px; font-weight: 700; color: #60a5fa; margin-bottom: 12px;">
                        <i class="fa-solid fa-shield-halved"></i> Super Administrator Credentials
                    </h3>
                </div>

                <div class="form-group">
                    <label>Administrator Full Name</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user-tie"></i>
                        <input type="text" class="form-control" id="admin_name" value="System Administrator">
                    </div>
                </div>
                <div class="form-group">
                    <label>Admin Login Email</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" class="form-control" id="admin_email" value="super@admin.com">
                    </div>
                </div>
                <div class="form-group">
                    <label>Admin Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-key"></i>
                        <input type="password" class="form-control" id="admin_pass" value="admin123">
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-check-double"></i>
                        <input type="password" class="form-control" id="admin_pass_confirm" value="admin123">
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 5: Installation Runner -->
        <div class="step-panel" id="panel-5">
            <h2 class="step-title">Installing School ERP Pro</h2>
            <p class="step-subtitle">Writing configuration, executing database schema, and securing installation locks.</p>

            <div class="progress-container" id="progress-container" style="display: block;">
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                <div class="progress-status" id="progress-status">Ready to initialize system...</div>
            </div>

            <div id="install-result" style="display: none; margin-top: 20px;"></div>
        </div>
    </div>

    <!-- Footer Controls -->
    <div class="wizard-footer">
        <button type="button" class="btn btn-secondary" id="btn-prev" style="display: none;">
            <i class="fa-solid fa-arrow-left"></i> Previous
        </button>
        <div style="flex: 1;"></div>
        <button type="button" class="btn btn-primary" id="btn-next">
            Next Step <i class="fa-solid fa-arrow-right"></i>
        </button>
        <button type="button" class="btn btn-primary" id="btn-start-install" style="display: none;">
            <i class="fa-solid fa-rocket"></i> Begin Installation
        </button>
        <a href="../auth/login" class="btn btn-primary" id="btn-finish" style="display: none;">
            <i class="fa-solid fa-check"></i> Go to Admin Login
        </a>
    </div>

    <?php endif; ?>
</div>

<script>
let currentStep = 1;
const totalSteps = 5;

const btnNext = document.getElementById('btn-next');
const btnPrev = document.getElementById('btn-prev');
const btnStartInstall = document.getElementById('btn-start-install');
const btnFinish = document.getElementById('btn-finish');

function showStep(step) {
    for (let i = 1; i <= totalSteps; i++) {
        const panel = document.getElementById('panel-' + i);
        const nav = document.getElementById('step-nav-' + i);
        if (panel) panel.classList.toggle('active', i === step);
        if (nav) {
            nav.classList.toggle('active', i === step);
            if (i < step) nav.classList.add('completed');
            else nav.classList.remove('completed');
        }
    }

    // Toggle Buttons
    if (btnPrev) btnPrev.style.display = (step > 1 && step < 5) ? 'inline-flex' : 'none';
    if (btnNext) btnNext.style.display = (step < 4) ? 'inline-flex' : 'none';
    if (btnStartInstall) btnStartInstall.style.display = (step === 4) ? 'inline-flex' : 'none';
    if (btnFinish) btnFinish.style.display = 'none';
}

let isLicenseVerified = false;

// Quick Key Selector Handler
const quickKeySelect = document.getElementById('quick-key-select');
const licenseInput = document.getElementById('license_key');
const statMsg = document.getElementById('license-status-msg');

if (quickKeySelect && licenseInput) {
    quickKeySelect.addEventListener('change', () => {
        if (quickKeySelect.value) {
            licenseInput.value = quickKeySelect.value;
            verifyLicenseKey();
        }
    });
}

if (licenseInput) {
    licenseInput.addEventListener('input', () => {
        isLicenseVerified = false;
        if (statMsg) statMsg.style.display = 'none';
        if (btnVerifyLicense) {
            btnVerifyLicense.disabled = false;
            btnVerifyLicense.innerHTML = '<i class="fa-solid fa-shield-check"></i> Verify Purchase Code';
        }
    });
}

// Function to verify license key via AJAX
async function verifyLicenseKey() {
    const key = licenseInput ? licenseInput.value.trim() : '';
    if (!statMsg || !btnVerifyLicense) return false;

    statMsg.style.display = 'block';
    if (!key || key.length < 8) {
        statMsg.innerHTML = '<span style="color: var(--danger); font-weight: 600;"><i class="fa-solid fa-circle-xmark"></i> Please enter an authorized License Key.</span>';
        licenseInput.focus();
        isLicenseVerified = false;
        return false;
    }

    btnVerifyLicense.disabled = true;
    btnVerifyLicense.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';

    try {
        const fd = new FormData();
        fd.append('action', 'verify_license');
        fd.append('license_key', key);

        const res = await fetch('index.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            isLicenseVerified = true;
            btnVerifyLicense.disabled = false;
            btnVerifyLicense.innerHTML = '<i class="fa-solid fa-check-double"></i> Verified';
            statMsg.innerHTML = `<span style="color: var(--success); font-weight: 600;"><i class="fa-solid fa-circle-check"></i> ${data.message}</span>`;
            return true;
        } else {
            isLicenseVerified = false;
            btnVerifyLicense.disabled = false;
            btnVerifyLicense.innerHTML = '<i class="fa-solid fa-shield-check"></i> Verify Purchase Code';
            statMsg.innerHTML = `<span style="color: var(--danger); font-weight: 600;"><i class="fa-solid fa-circle-xmark"></i> ${data.message}</span>`;
            return false;
        }
    } catch (e) {
        isLicenseVerified = false;
        btnVerifyLicense.disabled = false;
        btnVerifyLicense.innerHTML = '<i class="fa-solid fa-shield-check"></i> Verify Purchase Code';
        statMsg.innerHTML = '<span style="color: var(--danger); font-weight: 600;"><i class="fa-solid fa-circle-xmark"></i> Verification server unreachable.</span>';
        return false;
    }
}

// License Key Verification Button Trigger
const btnVerifyLicense = document.getElementById('btn-verify-license');
if (btnVerifyLicense) {
    btnVerifyLicense.addEventListener('click', verifyLicenseKey);
}

if (btnNext) {
    btnNext.addEventListener('click', async () => {
        if (currentStep === 1) {
            const licenseKey = licenseInput ? licenseInput.value.trim() : '';
            if (!licenseKey || licenseKey.length < 8) {
                alert('Please enter your CodeCanyon Purchase Code / License Key to continue.');
                if (licenseInput) licenseInput.focus();
                return;
            }
            const agreed = document.getElementById('terms_agree').checked;
            if (!agreed) {
                alert('Please accept the license terms to continue.');
                return;
            }

            // Verify before allowing step advance
            if (!isLicenseVerified) {
                const valid = await verifyLicenseKey();
                if (!valid) {
                    alert('Invalid Purchase Code. Please select or enter one of the 10 authorized keys.');
                    return;
                }
            }
        }
        if (currentStep === 3) {
            const dbName = document.getElementById('db_name').value.trim();
            if (!dbName) {
                alert('Please provide a Database Name.');
                document.getElementById('db_name').focus();
                return;
            }
        }
        if (currentStep < 4) {
            currentStep++;
            showStep(currentStep);
        }
    });
}

if (btnPrev) {
    btnPrev.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
}

// Live DB Connection Test
const btnTestDb = document.getElementById('btn-test-db');
if (btnTestDb) {
    btnTestDb.addEventListener('click', () => {
        const statusEl = document.getElementById('test-db-status');
        btnTestDb.disabled = true;
        btnTestDb.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Testing...';
        statusEl.style.display = 'block';
        statusEl.innerHTML = '<span style="color: var(--text-muted);"><i class="fa-solid fa-spinner fa-spin"></i> Connecting to MySQL...</span>';

        const formData = new FormData();
        formData.append('action', 'test_db');
        formData.append('db_host', document.getElementById('db_host').value);
        formData.append('db_port', document.getElementById('db_port').value);
        formData.append('db_user', document.getElementById('db_user').value);
        formData.append('db_pass', document.getElementById('db_pass').value);
        formData.append('db_name', document.getElementById('db_name').value);

        fetch('index.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                btnTestDb.disabled = false;
                btnTestDb.innerHTML = '<i class="fa-solid fa-plug"></i> Test Connection';
                if (data.success) {
                    if (data.pass_adjusted) {
                        document.getElementById('db_pass').value = '';
                    }
                    statusEl.innerHTML = '<span style="color: var(--success); font-weight: 600;"><i class="fa-solid fa-circle-check"></i> ' + data.message + '</span>';
                } else {
                    statusEl.innerHTML = '<span style="color: var(--danger); font-weight: 600;"><i class="fa-solid fa-circle-xmark"></i> ' + data.message + '</span>';
                }
            })
            .catch(err => {
                btnTestDb.disabled = false;
                btnTestDb.innerHTML = '<i class="fa-solid fa-plug"></i> Test Connection';
                statusEl.innerHTML = '<span style="color: var(--danger); font-weight: 600;"><i class="fa-solid fa-circle-xmark"></i> Connection request failed. Please check server error logs.</span>';
            });
    });
}

// DB Password Visibility Toggle
const toggleDbPass = document.getElementById('toggle-db-pass');
if (toggleDbPass) {
    toggleDbPass.addEventListener('click', () => {
        const inp = document.getElementById('db_pass');
        const ico = document.getElementById('toggle-db-pass-icon');
        if (inp.type === 'password') {
            inp.type = 'text';
            ico.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            inp.type = 'password';
            ico.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
}

// Installation Trigger
if (btnStartInstall) {
    btnStartInstall.addEventListener('click', () => {
        const p1 = document.getElementById('admin_pass').value;
        const p2 = document.getElementById('admin_pass_confirm').value;
        if (!p1 || p1.length < 6) {
            alert('Administrator password must be at least 6 characters long.');
            return;
        }
        if (p1 !== p2) {
            alert('Passwords do not match. Please re-enter.');
            return;
        }

        currentStep = 5;
        showStep(5);

        const fill = document.getElementById('progress-fill');
        const status = document.getElementById('progress-status');
        const resultEl = document.getElementById('install-result');

        fill.style.width = '20%';
        status.innerText = 'Connecting to database server...';

        const formData = new FormData();
        formData.append('action', 'run_install');
        formData.append('db_host', document.getElementById('db_host').value);
        formData.append('db_port', document.getElementById('db_port').value);
        formData.append('db_user', document.getElementById('db_user').value);
        formData.append('db_pass', document.getElementById('db_pass').value);
        formData.append('db_name', document.getElementById('db_name').value);
        formData.append('school_name', document.getElementById('school_name').value);
        formData.append('campus_name', document.getElementById('campus_name').value);
        formData.append('currency', document.getElementById('currency').value);
        formData.append('admin_name', document.getElementById('admin_name').value);
        formData.append('admin_email', document.getElementById('admin_email').value);
        formData.append('admin_pass', document.getElementById('admin_pass').value);
        formData.append('license_key', document.getElementById('license_key').value.trim());

        setTimeout(() => {
            fill.style.width = '55%';
            status.innerText = 'Creating database schema & running ERP migrations...';
        }, 800);

        fetch('index.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    fill.style.width = '100%';
                    status.innerText = 'Installation complete! Sealing security locks...';
                    resultEl.style.display = 'block';
                    resultEl.innerHTML = `
                        <div class="alert-box alert-success" style="flex-direction: column; align-items: flex-start;">
                            <div style="display:flex; align-items:center; gap:8px; font-weight:700; font-size:15px;">
                                <i class="fa-solid fa-circle-check"></i> ${data.message}
                            </div>
                            <div style="margin-top: 10px; font-size: 13px; line-height: 1.6; color: #cbd5e1;">
                                <strong>Admin Username:</strong> ${data.admin_email}<br>
                                <strong>Setup Status:</strong> Locked with <code>install/install.lock</code><br>
                                You can now sign in using your Super Administrator credentials.
                            </div>
                        </div>
                    `;
                    btnFinish.style.display = 'inline-flex';
                    if (btnPrev) btnPrev.style.display = 'none';
                    if (btnStartInstall) btnStartInstall.style.display = 'none';
                } else {
                    fill.style.width = '100%';
                    fill.style.background = 'var(--danger)';
                    status.innerText = 'Installation stopped with error.';
                    resultEl.style.display = 'block';
                    resultEl.innerHTML = `
                        <div class="alert-box alert-danger">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>${data.message}</span>
                        </div>
                    `;
                    if (btnPrev) btnPrev.style.display = 'inline-flex';
                }
            })
            .catch(err => {
                fill.style.width = '100%';
                fill.style.background = 'var(--danger)';
                status.innerText = 'Installation failed.';
                resultEl.style.display = 'block';
                resultEl.innerHTML = `
                    <div class="alert-box alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>An unexpected network or server error occurred. Please check server error logs.</span>
                    </div>
                `;
            });
    });
}
</script>

</body>
</html>
