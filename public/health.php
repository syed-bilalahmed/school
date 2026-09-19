<?php
/**
 * Load Balancer Health Check Endpoint
 *
 * Used by AWS ALB, NGINX, HAProxy, and Cloudflare Load Balancers to monitor node health.
 * Performs fast diagnostics:
 * - Master Database write connectivity
 * - Read Replica read connectivity & latency
 * - Memory allocation & load average
 *
 * Returns HTTP 200 { "status": "UP" } if healthy.
 * Returns HTTP 503 { "status": "DOWN" } if critical dependency fails.
 */

// Bypass output buffering
if (ob_get_level()) ob_end_clean();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

$startTime = microtime(true);
$checks = [
    'status'        => 'UP',
    'timestamp'     => date('c'),
    'cluster_node'  => gethostname() ?: 'web-node',
    'environment'   => 'production',
    'database'      => [
        'master'    => 'UNKNOWN',
        'replica'   => 'UNKNOWN',
        'latency_ms'=> 0
    ],
    'cache_driver'  => 'file',
    'memory_mb'     => round(memory_get_usage(true) / 1048576, 2)
];

$isHealthy = true;

// 1. Check Configuration & Database
$configFile = dirname(__DIR__) . '/config/config.php';
if (file_exists($configFile)) {
    require_once $configFile;
    require_once dirname(__DIR__) . '/app/Core/Database.php';

    // Ping Master
    try {
        $dbStart = microtime(true);
        $writePdo = Database::getWritePdo();
        $writePdo->query("SELECT 1");
        $checks['database']['master'] = 'CONNECTED';
        $checks['database']['latency_ms'] = round((microtime(true) - $dbStart) * 1000, 2);
    } catch (Throwable $e) {
        $checks['database']['master'] = 'UNAVAILABLE';
        $checks['database']['master_error'] = $e->getMessage();
        $isHealthy = false;
    }

    // Ping Read Replica
    try {
        $readPdo = Database::getReadPdo();
        $readPdo->query("SELECT 1");
        $checks['database']['replica'] = 'CONNECTED';
        $checks['database']['replica_host'] = Database::getActiveReadHost() ?: DB_HOST;
    } catch (Throwable $e) {
        $checks['database']['replica'] = 'DEGRADED_FALLBACK_TO_MASTER';
    }
} else {
    $checks['status'] = 'UNINSTALLED';
}

// 2. Check Cache
if (defined('REDIS_HOST') && extension_loaded('redis')) {
    $checks['cache_driver'] = 'redis';
} elseif (extension_loaded('apcu') && ini_get('apc.enabled')) {
    $checks['cache_driver'] = 'apcu';
}

// 3. System Load (if on Linux)
if (function_exists('sys_getloadavg')) {
    $load = sys_getloadavg();
    $checks['system_load'] = [
        '1m'  => round($load[0] ?? 0, 2),
        '5m'  => round($load[1] ?? 0, 2),
        '15m' => round($load[2] ?? 0, 2)
    ];
}

$checks['response_time_ms'] = round((microtime(true) - $startTime) * 1000, 2);

if (!$isHealthy) {
    $checks['status'] = 'DOWN';
    http_response_code(503);
} else {
    http_response_code(200);
}

echo json_encode($checks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
