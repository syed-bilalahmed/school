<?php
// Load Balancer SSL Offloading & Reverse Proxy Normalization
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

// Auto-Redirect to Setup Wizard if application is not installed yet
$installLock = dirname(__DIR__) . '/install/install.lock';
$installedLock = dirname(__DIR__) . '/config/installed.lock';

if (!file_exists($installLock) && !file_exists($installedLock)) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/school/public/index.php'));
    $subfolder = rtrim(str_replace('\\', '/', $scriptDir), '/');
    header("Location: {$protocol}://{$host}{$subfolder}/install/");
    exit;
}

// Direct Load Balancer Health Check Route
$rawUrl = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
if ($rawUrl === 'health' || $rawUrl === 'health.php') {
    require_once __DIR__ . '/health.php';
    exit;
}

require_once '../config/config.php';

// Optional manual OPcache purge on ?clear_cache=1
if (isset($_GET['clear_cache']) || isset($_GET['reset_cache'])) {
    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }
}

// Autoloader (Load early for Core classes including SessionManager)
spl_autoload_register(function($className){
    if(file_exists('../app/Core/' . $className . '.php')){
        require_once '../app/Core/' . $className . '.php';
    } elseif(file_exists('../app/Controllers/' . $className . '.php')){
        require_once '../app/Controllers/' . $className . '.php';
    } elseif(file_exists('../app/Models/' . $className . '.php')){
        require_once '../app/Models/' . $className . '.php';
    } elseif(file_exists('../app/Middleware/' . $className . '.php')){
        require_once '../app/Middleware/' . $className . '.php';
    }
});

// Check if request is an API request (Stateless Bearer Token auth; skip session locking for 100,000 users)
$isApi = (strpos($rawUrl, 'api/') === 0 || $rawUrl === 'api');

if (!$isApi) {
    if (class_exists('SessionManager')) {
        SessionManager::start();
    } elseif (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 86400,
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }
}

// Generate CSRF Token if not exists
if (isset($_SESSION) && empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// User Agent tracking without destructive session purge
if (isset($_SESSION) && isset($_SERVER['HTTP_USER_AGENT'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}

// Initialize Tenant Context globally
if (class_exists('TenantContext')) {
    TenantContext::resolve();
}

// Auto-sync Database Schema
if (class_exists('SchemaSync')) {
    SchemaSync::run();
}

// Request Rate Limiting & DoS Shield (Bypassed on localhost for zero file I/O lag)
if (class_exists('RateLimiter')) {
    $clientIp = RateLimiter::clientIp();
    $isLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);

    if (!$isLocal) {
        $rawUrl = isset($_GET['url']) ? trim($_GET['url'], '/') : '';

        // Exempt health monitoring endpoint
        if ($rawUrl !== 'health' && $rawUrl !== 'api/health') {
            // Strict rate limit on login attempts (15 attempts/minute per IP)
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && (strpos($rawUrl, 'login') !== false || strpos($rawUrl, 'auth') !== false)) {
                RateLimiter::throttle('login_' . $clientIp, 15, 60);
            }

            // Global baseline sliding-window limit: 180 requests/min per IP
            RateLimiter::throttle('global_' . $clientIp, 180, 60);
        }
    }
}

// Init Core Library
$init = new Router();

