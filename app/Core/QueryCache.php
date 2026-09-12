<?php
/**
 * QueryCache — High-Performance In-Memory & File Query Cache
 *
 * Automatically leverages APCu shared memory when enabled in PHP.
 * Gracefully falls back to high-speed file cache + per-request static memory.
 * Eliminates redundant DB hits for frequently read reference data
 * (settings, classes, sections, fee types, academic sessions).
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

        // 2. Check APCu shared RAM
        if (self::hasApcu()) {
            $success = false;
            $data = apcu_fetch('qc_' . $key, $success);
            if ($success) {
                self::$requestMemory[$key] = $data;
                return $data;
            }
        } else {
            // 3. Check File Cache
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

        // 4. Miss — execute callback
        $value = $callback();

        // 5. Save in memory & cache
        self::set($key, $value, $ttl);

        return $value;
    }

    /**
     * Store item in cache.
     */
    public static function set(string $key, $value, int $ttl = 300): void {
        self::$requestMemory[$key] = $value;

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

        if (self::hasApcu()) {
            apcu_clear_cache();
        }

        $dir = self::getDir();
        foreach (glob($dir . '/*.cache') ?: [] as $f) {
            @unlink($f);
        }
    }
}
