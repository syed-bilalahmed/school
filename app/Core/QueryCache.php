<?php
/**
 * QueryCache — High-Performance Distributed In-Memory & File Query Cache
 *
 * Designed for 100,000+ Concurrent Users:
 * 1. Per-Request Static RAM: Zero-latency instantaneous reads within the same request lifecycle.
 * 2. Redis Distributed RAM: Shared across all load-balanced web servers in a multi-node cluster.
 * 3. APCu Shared RAM: Local in-memory cache when running on single-node PHP-FPM.
 * 4. High-Speed Atomic File Cache: Durable zero-dependency fallback.
 *
 * Usage:
 *   $settings = QueryCache::remember('school_settings_' . $schoolId, 300, function() use ($schoolId) {
 *       return $this->fetchSettingsFromDb($schoolId);
 *   });
 */
class QueryCache {

    private static array $requestMemory = [];
    private static ?string $cacheDir = null;
    private static ?bool $hasApcu = null;
    private static $redisInstance = null;
    private static bool $redisChecked = false;

    /**
     * Get or initialize Redis connection if configured.
     */
    private static function getRedis() {
        if (self::$redisChecked) {
            return self::$redisInstance;
        }
        self::$redisChecked = true;

        if (defined('REDIS_HOST') && extension_loaded('redis')) {
            try {
                $redis = new Redis();
                $host = REDIS_HOST;
                $port = defined('REDIS_PORT') ? (int)REDIS_PORT : 6379;
                $pass = defined('REDIS_PASSWORD') ? REDIS_PASSWORD : null;
                $timeout = 1.0; // Fast timeout for cache

                if ($redis->connect($host, $port, $timeout)) {
                    if (!empty($pass)) {
                        $redis->auth($pass);
                    }
                    $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
                    $redis->setOption(Redis::OPT_PREFIX, 'qc:');
                    self::$redisInstance = $redis;
                    return self::$redisInstance;
                }
            } catch (Throwable $e) {
                error_log('[QueryCache Redis] ' . $e->getMessage());
            }
        }
        return null;
    }

    /**
     * Check if APCu is available and active.
     */
    public static function hasApcu(): bool {
        if (self::$hasApcu === null) {
            self::$hasApcu = extension_loaded('apcu') && ini_get('apc.enabled');
        }
        return self::$hasApcu;
    }

    /**
     * Directory for file-based fallback cache.
     */
    private static function getDir(): string {
        if (self::$cacheDir !== null) {
            return self::$cacheDir;
        }
        $base = defined('APPROOT') ? APPROOT : dirname(__DIR__);
        $dir = $base . '/cache/queries';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        self::$cacheDir = $dir;
        return $dir;
    }

    /**
     * Cache-aside helper: fetch from cache or execute callback and store.
     *
     * @param string   $key      Cache key
     * @param int      $ttl      Time to live in seconds (e.g. 300 for 5 minutes)
     * @param callable $callback Generator callback
     * @return mixed
     */
    public static function remember(string $key, int $ttl, callable $callback) {
        // 1. Check per-request memory cache (zero I/O)
        if (array_key_exists($key, self::$requestMemory)) {
            return self::$requestMemory[$key];
        }

        // 2. Check Redis (distributed across all load-balanced web servers)
        $redis = self::getRedis();
        if ($redis !== null) {
            try {
                $data = $redis->get($key);
                if ($data !== false) {
                    self::$requestMemory[$key] = $data;
                    return $data;
                }
            } catch (Throwable $e) {
                error_log('[QueryCache Redis Read] ' . $e->getMessage());
            }
        }

        // 3. Check APCu shared RAM
        if (self::hasApcu()) {
            $success = false;
            $data = apcu_fetch('qc_' . $key, $success);
            if ($success) {
                self::$requestMemory[$key] = $data;
                return $data;
            }
        } else {
            // 4. Check File Cache
            $file = self::getDir() . '/' . md5($key) . '.cache';
            if (file_exists($file)) {
                $raw = @file_get_contents($file);
                if ($raw !== false) {
                    $item = @unserialize($raw);
                    if (is_array($item) && isset($item['expires_at']) && $item['expires_at'] > time()) {
                        self::$requestMemory[$key] = $item['data'];
                        return $item['data'];
                    }
                }
            }
        }

        // 5. Miss — execute callback
        $value = $callback();

        // 6. Save in memory & cache
        self::set($key, $value, $ttl);

        return $value;
    }

    /**
     * Store item in cache.
     */
    public static function set(string $key, $value, int $ttl = 300): void {
        self::$requestMemory[$key] = $value;

        // Save in Redis
        $redis = self::getRedis();
        if ($redis !== null) {
            try {
                $redis->setex($key, $ttl, $value);
                return;
            } catch (Throwable $e) {
                error_log('[QueryCache Redis Write] ' . $e->getMessage());
            }
        }

        if (self::hasApcu()) {
            apcu_store('qc_' . $key, $value, $ttl);
        } else {
            $file = self::getDir() . '/' . md5($key) . '.cache';
            $payload = serialize([
                'expires_at' => time() + $ttl,
                'data'       => $value
            ]);
            $tmp = $file . '.tmp.' . getmypid() . '.' . mt_rand();
            @file_put_contents($tmp, $payload);
            @rename($tmp, $file);
        }
    }

    /**
     * Invalidate a specific key.
     */
    public static function forget(string $key): void {
        unset(self::$requestMemory[$key]);

        $redis = self::getRedis();
        if ($redis !== null) {
            try {
                $redis->del($key);
            } catch (Throwable $e) {}
        }

        if (self::hasApcu()) {
            apcu_delete('qc_' . $key);
        }

        $file = self::getDir() . '/' . md5($key) . '.cache';
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Clear all query cache.
     */
    public static function flush(): void {
        self::$requestMemory = [];

        $redis = self::getRedis();
        if ($redis !== null) {
            try {
                $keys = $redis->keys('qc:*');
                if (!empty($keys)) {
                    $redis->del($keys);
                }
            } catch (Throwable $e) {}
        }

        if (self::hasApcu()) {
            apcu_clear_cache();
        }

        $dir = self::getDir();
        foreach (glob($dir . '/*.cache') ?: [] as $f) {
            @unlink($f);
        }
    }
}
