<?php
/**
 * School ERP Pro - Root Entrypoint Router & Installation Guard
 * Directs traffic seamlessly:
 * 1. If not yet installed -> Redirects to /install/
 * 2. If already installed -> Redirects to /public/
 */

$installLock = __DIR__ . '/install/install.lock';
$installedLock = __DIR__ . '/config/installed.lock';

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/school/index.php');
$subfolder = rtrim(str_replace('\\', '/', $scriptDir), '/');

if (!file_exists($installLock) && !file_exists($installedLock)) {
    header("Location: {$protocol}://{$host}{$subfolder}/install/");
    exit;
}

// Redirect to public web root
header("Location: {$protocol}://{$host}{$subfolder}/public/");
exit;
