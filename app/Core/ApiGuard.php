<?php
/**
 * ApiGuard - Core Security & Authentication Middleware for Mobile Apps & APIs
 * Handles CORS, API Key verification, multi-tenant isolation, and User Bearer Tokens.
 */
class ApiGuard {
    private static $currentClient = null;
    private static $currentUser = null;

    /**
     * Handle CORS preflight requests and inject required cross-origin headers
     */
    public static function handleCors() {
        // Set cross-origin headers
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY, x-api-key, Accept, Origin, X-Requested-With");
        header("Access-Control-Max-Age: 86400");

        // Handle pre-flight OPTIONS request
        if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Extract API key from headers or request parameters
     */
    public static function extractApiKey() {
        if (!empty($_SERVER['HTTP_X_API_KEY'])) {
            return trim($_SERVER['HTTP_X_API_KEY']);
        }
        if (!empty($_SERVER['X_API_KEY'])) {
            return trim($_SERVER['X_API_KEY']);
        }

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $val) {
                if (strtolower($key) === 'x-api-key') {
                    return trim($val);
                }
            }
        }

        // Fallback: only standard HTTP headers are accepted for credentials (no query string leakage)
        return null;
    }

    /**
     * Extract Bearer token from headers
     */
    public static function extractBearerToken() {
        $authHeader = '';

        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        } elseif (function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $key => $val) {
                if (strtolower($key) === 'authorization') {
                    $authHeader = trim($val);
                    break;
                }
            }
        }

        if (!empty($authHeader) && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Require a valid API Key. Establishes TenantContext and checks active status.
     */
    public static function requireApiKey() {
        self::handleCors();

        $apiKey = self::extractApiKey();

        if (empty($apiKey)) {
            self::jsonError('API Key is missing. Please provide a valid X-API-KEY header.', 401);
        }

        try {
            $db = new Database();
            $keyHash = hash('sha256', $apiKey);
            $db->query("SELECT * FROM api_keys WHERE (api_key = :api_key OR api_key = :key_hash) LIMIT 1");
            $db->bind(':api_key', $apiKey);
            $db->bind(':key_hash', $keyHash);
            $client = $db->single();

            if (!$client) {
                self::jsonError('Invalid API Key provided.', 403);
            }

            if ((int)$client->is_active !== 1) {
                self::jsonError('This API Key has been deactivated or revoked.', 403);
            }

            // Update last_used_at timestamp
            $db->query("UPDATE api_keys SET last_used_at = NOW() WHERE id = :id");
            $db->bind(':id', $client->id);
            $db->execute();

            // Establish TenantContext for strict data isolation
            if (class_exists('TenantContext')) {
                TenantContext::setSchoolId((int)$client->school_id);
            }

            self::$currentClient = $client;
            return $client;

        } catch (Exception $e) {
            error_log('ApiGuard::requireApiKey exception: ' . $e->getMessage());
            self::jsonError('Internal server error during authentication.', 500);
        }
    }

    /**
     * Require both valid API Key and an authenticated User Bearer Token (Student, Parent, Teacher, Admin)
     */
    public static function requireUserAuth() {
        // First ensure valid client API key
        $client = self::requireApiKey();

        $token = self::extractBearerToken();
        if (empty($token)) {
            self::jsonError('Authorization Bearer token is missing. Please log in via /api/login first.', 401);
        }

        try {
            $db = new Database();
            $tokenHash = hash('sha256', $token);
            $db->query("SELECT t.id as token_id, t.expires_at, t.device_name,
                               u.id, u.name, u.email, u.role, u.school_id, u.status
                        FROM api_user_tokens t
                        INNER JOIN users u ON t.user_id = u.id
                        WHERE (t.token = :token OR t.token = :token_hash) AND t.school_id = :school_id
                        LIMIT 1");
            $db->bind(':token', $token);
            $db->bind(':token_hash', $tokenHash);
            $db->bind(':school_id', $client->school_id);
            $user = $db->single();

            if (!$user) {
                self::jsonError('Invalid or expired access token. Please log in again.', 401);
            }

            // Check expiry
            if (strtotime($user->expires_at) < time()) {
                self::jsonError('Session token has expired. Please log in again.', 401);
            }

            // Check if user is active
            if (isset($user->status) && $user->status !== 'active' && $user->status !== 'Active') {
                self::jsonError('Your user account is inactive or suspended.', 403);
            }

            self::$currentUser = $user;
            return $user;

        } catch (Exception $e) {
            error_log('ApiGuard::requireUserAuth exception: ' . $e->getMessage());
            self::jsonError('Authentication error processing user session token.', 500);
        }
    }

    /**
     * Get the authenticated client record
     */
    public static function getClient() {
        return self::$currentClient;
    }

    /**
     * Get the authenticated user record
     */
    public static function getUser() {
        return self::$currentUser;
    }

    /**
     * Send standard JSON error response
     */
    public static function jsonError($message, $statusCode = 400, $errors = []) {
        $response = [
            'success' => false,
            'message' => $message
        ];
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        self::jsonResponse($response, $statusCode);
    }

    /**
     * Send standard JSON success response
     */
    public static function jsonSuccess($data = null, $message = 'Success', $meta = []) {
        $response = [
            'success' => true,
            'message' => $message
        ];
        if ($data !== null) {
            $response['data'] = $data;
        }
        if (!empty($meta)) {
            $response['meta'] = $meta;
        }
        self::jsonResponse($response, 200);
    }

    /**
     * Emit HTTP response with JSON payload
     */
    public static function jsonResponse($data, $statusCode = 200) {
        if (ob_get_length()) {
            ob_clean();
        }
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
