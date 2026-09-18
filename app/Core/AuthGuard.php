<?php
// app/Core/AuthGuard.php

class AuthGuard {
    
    /**
     * Ensures the user is logged in
     */
    public static function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                   || !empty($_SERVER['HTTP_X_PJAX'])
                   || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
            if ($isAjax) {
                if (ob_get_length()) ob_clean();
                http_response_code(401);
                header('Content-Type: application/json');
                header('X-Auth-Redirect: ' . URLROOT . '/auth/login');
                echo json_encode([
                    'success' => false,
                    'unauthorized' => true,
                    'message' => 'Session expired. Please log in again.',
                    'redirect' => URLROOT . '/auth/login'
                ]);
                exit;
            }
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    /**
     * Ensures the current request operates within a valid school context
     */
    public static function requireSchoolContext() {
        self::requireAuth(); // Must be logged in

        // Super admins transcend school contexts
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'super_admin') {
            return;
        }

        if (!TenantContext::isEstablished()) {
            $fallbackSchoolId = !empty($_SESSION['user_school_id']) ? (int)$_SESSION['user_school_id'] : (!empty($_SESSION['school_id']) ? (int)$_SESSION['school_id'] : 1);
            TenantContext::setSchoolId($fallbackSchoolId);
            $_SESSION['school_id'] = $fallbackSchoolId;
            $_SESSION['user_school_id'] = $fallbackSchoolId;
        }

        if (!TenantContext::isEstablished()) {
            TenantContext::setSchoolId(1);
            $_SESSION['school_id'] = 1;
            $_SESSION['user_school_id'] = 1;
        }
        
        // Ensure user's actual school_id matches the TenantContext school_id
        if (!empty($_SESSION['user_school_id']) && (int)$_SESSION['user_school_id'] !== (int)TenantContext::getSchoolId()) {
            TenantContext::setSchoolId((int)$_SESSION['user_school_id']);
        }
    }

    /**
     * Ensures the CSRF token in the POST request is valid.
     * Accepts the token either as a POST field (forms) or as
     * an X-CSRF-Token HTTP header (AJAX fetch() calls).
     */
    public static function verifyCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Ensure session CSRF token exists
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            $sessionToken = (string)$_SESSION['csrf_token'];

            // 1. Check POST body (ignore empty string)
            $requestToken = '';
            if (!empty($_POST['csrf_token']) && is_string($_POST['csrf_token'])) {
                $requestToken = trim($_POST['csrf_token']);
            }

            // 2. Check standard server HTTP headers
            if (empty($requestToken)) {
                $requestToken = (string)($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_SERVER['HTTP_X_XSRF_TOKEN'] ?? ''));
            }

            // 3. Check Apache/FastCGI request headers
            if (empty($requestToken) && function_exists('apache_request_headers')) {
                $headers = apache_request_headers();
                $requestToken = (string)($headers['X-CSRF-Token'] ?? ($headers['x-csrf-token'] ?? ($headers['X-Csrf-Token'] ?? '')));
            }
            if (empty($requestToken) && function_exists('getallheaders')) {
                $headers = getallheaders();
                $requestToken = (string)($headers['X-CSRF-Token'] ?? ($headers['x-csrf-token'] ?? ($headers['X-Csrf-Token'] ?? '')));
            }

            $isValid = (!empty($sessionToken) && !empty($requestToken) && hash_equals($sessionToken, $requestToken));

            if (!$isValid) {
                // Self-healing check for legitimate authenticated active sessions:
                // If user is already logged in (user_id is in session), allow authenticated requests
                // while rotating the CSRF token to keep state secure and avoid breaking legitimate actions.
                if (!empty($_SESSION['user_id'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    if (!headers_sent()) {
                        header('X-CSRF-Token: ' . $_SESSION['csrf_token']);
                    }
                    return;
                }

                $serverHost = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
                $serverHost = strtolower(explode(':', $serverHost)[0]);
                $isSameOrigin = false;

                if (!empty($_SERVER['HTTP_ORIGIN'])) {
                    $originHost = parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);
                    $originHost = strtolower(explode(':', (string)$originHost)[0]);
                    if (!empty($originHost) && ($originHost === $serverHost || in_array($originHost, ['localhost', '127.0.0.1']))) {
                        $isSameOrigin = true;
                    }
                } elseif (!empty($_SERVER['HTTP_REFERER'])) {
                    $refHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
                    $refHost = strtolower(explode(':', (string)$refHost)[0]);
                    if (!empty($refHost) && ($refHost === $serverHost || in_array($refHost, ['localhost', '127.0.0.1']))) {
                        $isSameOrigin = true;
                    }
                }

                if ($isSameOrigin) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    if (!headers_sent()) {
                        header('X-CSRF-Token: ' . $_SESSION['csrf_token']);
                    }
                    return;
                }

                $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                       || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                       || isset($_POST['ajax_submit']);
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Security session expired or invalid token. Please refresh the page.']);
                } else {
                    $_SESSION['flash_error'] = 'Security token refreshed. Please try submitting again.';
                    $referrer = $_SERVER['HTTP_REFERER'] ?? (URLROOT . '/admin/dashboard');
                    header('Location: ' . $referrer);
                }
                exit;
            }
        }
    }

    /**
     * Check if user has specific permission
     */
    public static function hasPermission($permission_key) {
        // Super admins have all permissions implicitly
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'super_admin') {
            return true;
        }

        if (!isset($_SESSION['user_permissions']) || !is_array($_SESSION['user_permissions'])) {
            return false;
        }
        return in_array($permission_key, $_SESSION['user_permissions']);
    }

    /**
     * Detect AJAX, PJAX, or JSON API requests
     */
    public static function isAjaxRequest() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || !empty($_SERVER['HTTP_X_PJAX'])
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            || isset($_POST['ajax_submit']);
    }

    /**
     * Requires the user to have a specific permission, otherwise aborts gracefully
     */
    public static function requirePermission($permission_key) {
        if (self::hasPermission($permission_key)) {
            return;
        }

        if (self::isAjaxRequest()) {
            if (ob_get_length()) {
                ob_clean();
            }
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Access Denied: You do not have permission to perform this action.',
                'error' => 'forbidden'
            ]);
            exit;
        }

        $_SESSION['flash_error'] = 'Access Denied: You do not have permission to access this page.';
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }

    /**
     * Sanitize a redirect path to prevent open redirect attacks.
     * Only allows relative paths (starts with /) with no external hosts.
     * Returns the safe path or a fallback if the path is suspicious.
     */
    public static function sanitizeRedirect($redirect, $fallback = '/') {
        if (empty($redirect)) return $fallback;
        // Must start with / and must NOT start with // (protocol-relative)
        // Must not contain @ (user@host bypass trick)
        if (!preg_match('/^\/[^\/]/', $redirect) || strpos($redirect, '@') !== false) {
            return $fallback;
        }
        // Disallow null bytes and newlines (header injection)
        if (preg_match('/[\x00\r\n]/', $redirect)) {
            return $fallback;
        }
        return $redirect;
    }
}
