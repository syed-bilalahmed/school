<?php
require_once '../config/config.php';

// Session Lifespan & Configuration
ini_set('session.gc_maxlifetime', 86400);

if (function_exists('opcache_reset')) {
    @opcache_reset();
}
if (function_exists('opcache_invalidate')) {
    $filesToInvalidate = [
        __DIR__ . '/../app/Views/home/page.php',
        __DIR__ . '/../app/Views/home/academics.php',
        __DIR__ . '/../app/Views/home/facilities.php',
        __DIR__ . '/../app/Views/home/events.php',
        __DIR__ . '/../app/Views/home/gallery.php',
        __DIR__ . '/../app/Views/home/partials/navbar.php',
        __DIR__ . '/../app/Views/home/index.php',
        __DIR__ . '/../app/Views/auth/login.php',
        __DIR__ . '/../app/Controllers/AuthController.php',
        __DIR__ . '/../app/Controllers/HomeController.php',
        __DIR__ . '/../app/Models/FrontPage.php',
        __DIR__ . '/../app/Models/FrontMenu.php',
        __DIR__ . '/../app/Models/User.php',
        __DIR__ . '/../app/Models/Notice.php',
        __DIR__ . '/../app/Controllers/NoticeController.php',
        __DIR__ . '/../app/Views/notice/index.php',
        __DIR__ . '/../app/Core/AuthGuard.php',
        __DIR__ . '/../app/Core/TenantContext.php'
    ];
    foreach ($filesToInvalidate as $f) {
        $rp = realpath($f);
        if ($rp) {
            @opcache_invalidate($rp, true);
            @opcache_invalidate(str_replace('\\', '/', $rp), true);
        }
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

// Request Rate Limiting & DoS Shield (3000+ Users Protection)
if (class_exists('RateLimiter')) {
    $clientIp = RateLimiter::clientIp();
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

// Init Core Library
$init = new Router();

