<?php
/**
 * RateLimiter — File-based rate limiting (no Redis required)
 * Works on XAMPP/Windows. Atomic file writes via rename().
 *
 * Usage:
 *   RateLimiter::throttle('login_' . $ip, 5, 60);     // 5 req/min
 *   RateLimiter::throttle('global_' . $ip, 120, 60);  // 120 req/min global cap
 */
class RateLimiter {

    private static $cacheDir = null;

    /**
     * Get or create the rate-limit cache directory.
     */
    private static function dir(): string {
        if (self::$cacheDir !== null) {
            return self::$cacheDir;
        }
        $base = defined('APPROOT') ? APPROOT : dirname(__DIR__);
        $dir  = $base . '/cache/ratelimit';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        self::$cacheDir = $dir;
        return $dir;
    }

    /**
     * Check if the given key has exceeded the allowed request rate.
     *
     * @param  string $key         Unique identifier (e.g. 'login_192.168.1.1')
     * @param  int    $maxRequests Maximum allowed requests in the window
     * @param  int    $windowSecs  Time window in seconds
     * @return bool   true = allowed, false = rate limit exceeded
     */
    public static function check(string $key, int $maxRequests, int $windowSecs): bool {
        $dir  = self::dir();
        $file = $dir . '/' . md5($key) . '.rl';
        $now  = time();

        // Read current state
        $data = ['hits' => [], 'blocked_until' => 0];
        if (file_exists($file)) {
            $raw = @file_get_contents($file);
            if ($raw !== false) {
                $decoded = @json_decode($raw, true);
                if (is_array($decoded)) {
                    $data = $decoded;
                }
            }
        }

        // Still in hard block window?
        if (!empty($data['blocked_until']) && $now < $data['blocked_until']) {
            return false;
        }

        // Remove hits outside the sliding window
        $windowStart = $now - $windowSecs;
        $data['hits'] = array_values(array_filter($data['hits'], function($t) use ($windowStart) {
            return $t > $windowStart;
        }));

        // Check if limit reached
        if (count($data['hits']) >= $maxRequests) {
            // Block for remainder of window + 10s penalty
            $oldest = !empty($data['hits']) ? min($data['hits']) : $now;
            $data['blocked_until'] = $now + max(10, $windowSecs - ($now - $oldest));
            self::atomicWrite($file, $data);
            return false;
        }

        // Record this hit
        $data['hits'][] = $now;
        $data['blocked_until'] = 0;
        self::atomicWrite($file, $data);
        return true;
    }

    /**
     * Check and auto-respond with 429 if rate limit exceeded.
     */
    public static function throttle(string $key, int $maxRequests, int $windowSecs): void {
        if (!self::check($key, $maxRequests, $windowSecs)) {
            self::respondTooManyRequests();
        }
    }

    /**
     * Get the client IP address (handles proxies safely).
     */
    public static function clientIp(): string {
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Check proxy headers only if REMOTE_ADDR is an established local or configured trusted proxy
        $trustedProxies = ['127.0.0.1', '::1'];
        if (defined('TRUSTED_PROXIES') && is_array(TRUSTED_PROXIES)) {
            $trustedProxies = array_merge($trustedProxies, TRUSTED_PROXIES);
        }

        if (in_array($remoteAddr, $trustedProxies, true)) {
            $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP'];
            foreach ($headers as $h) {
                if (!empty($_SERVER[$h])) {
                    $ip = filter_var(trim($_SERVER[$h]), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                    if ($ip) return $ip;
                }
            }
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $ip) {
                    $ip = filter_var(trim($ip), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
                    if ($ip) return $ip;
                }
            }
        }

        return $remoteAddr;
    }

    /**
     * Send HTTP 429 Too Many Requests and exit.
     */
    private static function respondTooManyRequests(): void {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                   strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        http_response_code(429);
        header('Retry-After: 60');
        header('X-RateLimit-Limit: exceeded');

        if ($isAjax) {
            if (ob_get_length()) ob_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error'   => 'Too many requests. Please slow down and try again in a minute.',
                'code'    => 429
            ]);
        } else {
            echo '<!DOCTYPE html><html><head><title>Too Many Requests</title>'
               . '<meta charset="utf-8"><style>body{font-family:system-ui,-apple-system,sans-serif;text-align:center;padding:80px;background:#f8f9fa}'
               . 'h1{color:#dc3545}p{color:#6c757d}a{color:#0d6efd;text-decoration:none}</style></head><body>'
               . '<h1>&#9888; Too Many Requests (429)</h1>'
               . '<p>You are sending requests too fast. Please wait a moment and try again.</p>'
               . '<p><a href="javascript:history.back()">&larr; Go Back</a></p></body></html>';
        }
        exit;
    }

    /**
     * Atomic file write to prevent race conditions.
     */
    private static function atomicWrite(string $file, array $data): void {
        $tmp = $file . '.tmp.' . getmypid() . '.' . mt_rand();
        @file_put_contents($tmp, json_encode($data));
        @rename($tmp, $file);
    }
}
