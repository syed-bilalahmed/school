<?php
// app/Core/TenantContext.php

class TenantContext {
    private static $schoolId = null;
    private static $schoolCode = null;

    /**
     * Resolves and sets the current school context based on session or request
     */
    public static function resolve() {
        if (isset($_SESSION['school_id']) && (int)$_SESSION['school_id'] > 0) {
            self::$schoolId = (int)$_SESSION['school_id'];
            self::$schoolCode = $_SESSION['school_code'] ?? self::fetchSchoolCodeById(self::$schoolId);
            if (self::$schoolCode) {
                $_SESSION['school_code'] = self::$schoolCode;
            }
            return;
        }

        // Auto-recover from user session if set
        if (isset($_SESSION['user_school_id']) && (int)$_SESSION['user_school_id'] > 0) {
            self::setSchoolId((int)$_SESSION['user_school_id']);
            return;
        }

        $codeFromPath = self::extractSchoolCodeFromPath();
        if ($codeFromPath) {
            $school = self::findSchoolByCode($codeFromPath);
            if ($school) {
                self::setSchool($school->id, $school->code);
                return;
            }
        }

        $domain = self::extractDomain();
        if ($domain) {
            $school = self::findSchoolByDomain($domain);
            if ($school) {
                self::setSchool($school->id, $school->code);
                return;
            }
        }

        // For authenticated sessions or localhost/single-tenant environment, fallback to primary default school
        $defaultFallbackId = !empty($_SESSION['school_id']) ? (int)$_SESSION['school_id'] : (!empty($_SESSION['user_school_id']) ? (int)$_SESSION['user_school_id'] : 1);
        self::setSchoolId($defaultFallbackId);
        if (isset($_SESSION['user_id'])) {
            $_SESSION['user_school_id'] = $defaultFallbackId;
        }
    }

    /**
     * Set the current school ID explicitly (e.g. at login)
     */
    public static function setSchoolId($id, $code = null) {
        $id = (int)$id;
        if ($id <= 0) {
            $id = 1;
        }

        $resolvedCode = $code ?: self::fetchSchoolCodeById($id);
        self::setSchool($id, $resolvedCode);
    }

    /**
     * Get the currently active school ID
     */
    public static function getSchoolId() {
        return self::$schoolId ?: (!empty($_SESSION['school_id']) ? (int)$_SESSION['school_id'] : 1);
    }

    public static function getSchoolCode() {
        return self::$schoolCode ?: 'default';
    }

    /**
     * Checks if a tenant context is established
     */
    public static function isEstablished() {
        return !is_null(self::$schoolId) && self::$schoolId > 0;
    }
    
    /**
     * Clear the context (e.g. at logout)
     */
    public static function clear() {
        self::$schoolId = null;
        self::$schoolCode = null;
        unset($_SESSION['school_id']);
        unset($_SESSION['school_code']);
    }

    private static function setSchool($id, $code = null) {
        self::$schoolId = (int)$id;
        self::$schoolCode = $code ?: null;
        $_SESSION['school_id'] = self::$schoolId;

        if (self::$schoolCode) {
            $_SESSION['school_code'] = self::$schoolCode;
        } else {
            unset($_SESSION['school_code']);
        }
    }

    private static function extractSchoolCodeFromPath() {
        if (!isset($_GET['url'])) {
            return null;
        }

        $parts = explode('/', trim($_GET['url'], '/'));
        if (count($parts) >= 2 && strtolower($parts[0]) === 's') {
            $candidate = trim($parts[1]);
            if ($candidate !== '' && preg_match('/^[a-zA-Z0-9_-]+$/', $candidate)) {
                return strtolower($candidate);
            }
        }

        return null;
    }

    private static function extractDomain() {
        if (empty($_SERVER['HTTP_HOST'])) {
            return null;
        }

        $host = strtolower(trim($_SERVER['HTTP_HOST']));
        return preg_replace('/:\\d+$/', '', $host);
    }

    private static function findSchoolByCode($code) {
        try {
            $db = new Database();
            $db->query("SELECT id, code FROM schools WHERE code = :code AND status = 'active' LIMIT 1");
            $db->bind(':code', $code);
            return $db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    private static function findSchoolByDomain($domain) {
        try {
            $db = new Database();
            $db->query("SELECT id, code FROM schools WHERE domain = :domain AND status = 'active' LIMIT 1");
            $db->bind(':domain', $domain);
            return $db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    private static function fetchSchoolCodeById($id) {
        try {
            $db = new Database();
            $db->query("SELECT code FROM schools WHERE id = :id LIMIT 1");
            $db->bind(':id', (int)$id);
            $school = $db->single();
            return $school ? $school->code : null;
        } catch (PDOException $e) {
            return null;
        }
    }
}
