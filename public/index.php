<?php
require_once '../config/config.php';

// Session Lifespan & Configuration
ini_set('session.gc_maxlifetime', 86400);

// Optional manual OPcache purge on ?clear_cache=1
if (isset($_GET['clear_cache']) || isset($_GET['reset_cache'])) {
    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Generate CSRF Token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// User Agent tracking without destructive session purge
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
}

// Autoloader
spl_autoload_register(function($className){
    // Convert namespace to path
    // Simple autoloader assuming classes are in app/
    
    // Check Core
    if(file_exists('../app/Core/' . $className . '.php')){
        require_once '../app/Core/' . $className . '.php';
    } 
    // Check Controllers
    elseif(file_exists('../app/Controllers/' . $className . '.php')){
        require_once '../app/Controllers/' . $className . '.php';
    }
    // Check Models
    elseif(file_exists('../app/Models/' . $className . '.php')){
        require_once '../app/Models/' . $className . '.php';
    }
});

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
    $isLocal = in_array($clientIp, ['127.0.0.1', '::1']);

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

