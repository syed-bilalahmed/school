<?php
// Database Settings
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_db');

// App Root
define('APPROOT', dirname(dirname(__FILE__)) . '/app');

// URL Root (Dynamic detection)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script = dirname($_SERVER['SCRIPT_NAME'] ?? '/school/index.php');
// Remove /public if it exists in script path (conflicts with rewrite base sometimes but cleaner)
// Actually, our rewrite base is /school/public. 
// Let's just assume we want the base url to be where index.php is minus public if we want cleaner urls, 
// But our current setup relies on /school being the base for assets maybe?
// Simplest is: protocol + host + /school (if in subfolder)
// But to be robust for "localhost:8080":
define('URLROOT', $protocol . "://" . $host . "/school");

// Site Name
define('SITENAME', 'School Management System');

// Outgoing email configuration (SMTP_* values can be supplied as environment variables).
require_once __DIR__ . '/mail.php';
