<?php
/**
 * SessionManager — Centralized High-Concurrency Session Handler.
 *
 * Supports:
 * - 'file': Local disk sessions (standard single-server).
 * - 'redis': Ultra-fast in-memory distributed sessions across multi-node web clusters.
 * - 'database': Centralized MySQL sessions via DbSessionHandler for load-balanced servers.
 */
class SessionManager {

    private static bool $started = false;

    public static function start(): void {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // 1. Session Security & Lifespan defaults
        ini_set('session.gc_maxlifetime', 86400);
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1000);

        // 2. Cookie Security Configuration
        $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

        session_set_cookie_params([
            'lifetime' => 86400,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // 3. Driver Selection
        $driver = defined('SESSION_DRIVER') ? strtolower(SESSION_DRIVER) : 'file';

        if ($driver === 'redis' && extension_loaded('redis')) {
            $redisHost = defined('REDIS_HOST') ? REDIS_HOST : '127.0.0.1';
            $redisPort = defined('REDIS_PORT') ? REDIS_PORT : 6379;
            $redisPass = defined('REDIS_PASSWORD') ? REDIS_PASSWORD : '';

            $savePath = "tcp://{$redisHost}:{$redisPort}?prefix=school_sess:";
            if (!empty($redisPass)) {
                $savePath .= "&auth=" . urlencode($redisPass);
            }

            ini_set('session.save_handler', 'redis');
            ini_set('session.save_path', $savePath);
        } elseif ($driver === 'database') {
            if (class_exists('DbSessionHandler')) {
                $handler = new DbSessionHandler();
                session_set_save_handler($handler, true);
            }
        }

        session_start();
        self::$started = true;

        // 4. CSRF Token initialization
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
