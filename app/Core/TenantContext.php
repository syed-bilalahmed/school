<?php
// app/Core/TenantContext.php

class TenantContext {
    private static $schoolId = null;
    private static $schoolCode = null;

    /**
     * Resolves and sets the current school context based on session or request
     */
    public static function resolve() {
        $loggedInRole = $_SESSION['user_role'] ?? null;
        $isLoggedIn = !empty($_SESSION['user_id']);
        $canSwitchBranch = !$isLoggedIn || in_array($loggedInRole, ['super_admin', 'admin'], true);

        // Security Lock: If a non-admin user is logged in (teacher, student, parent, staff), strictly lock to their school_id
        if ($isLoggedIn && !$canSwitchBranch && !empty($_SESSION['user_school_id'])) {
            self::setSchoolId((int)$_SESSION['user_school_id']);
            return;
        }

        // 1. Direct branch switch via query string (?branch=code_or_id or ?school=code_or_id) - only for public or admin/super_admin
        $branchQuery = !empty($_GET['branch']) ? trim($_GET['branch']) : (!empty($_GET['school']) ? trim($_GET['school']) : null);
        if ($branchQuery && $canSwitchBranch) {
            $matchedSchool = is_numeric($branchQuery) ? self::findSchoolById((int)$branchQuery) : self::findSchoolByCode(strtolower($branchQuery));
            if ($matchedSchool && $matchedSchool->status === 'active') {
                self::setSchool($matchedSchool->id, $matchedSchool->code);
                if (!empty($matchedSchool->name)) {
                    $_SESSION['school_name'] = $matchedSchool->name;
                }
                return;
            }
        }

        if (isset($_SESSION['school_id']) && (int)$_SESSION['school_id'] > 0) {
            self::$schoolId = (int)$_SESSION['school_id'];
            self::$schoolCode = $_SESSION['school_code'] ?? self::fetchSchoolCodeById(self::$schoolId);
            if (self::$schoolCode) {
                $_SESSION['school_code'] = self::$schoolCode;
            }
            if (empty($_SESSION['school_name'])) {
                $sch = self::findSchoolById(self::$schoolId);
                if ($sch && !empty($sch->name)) {
                    $_SESSION['school_name'] = $sch->name;
                }
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
                if (!empty($school->name)) {
                    $_SESSION['school_name'] = $school->name;
                }
                return;
            }
        }

        $domain = self::extractDomain();
        if ($domain) {
            $school = self::findSchoolByDomain($domain);
            if ($school) {
                self::setSchool($school->id, $school->code);
                if (!empty($school->name)) {
                    $_SESSION['school_name'] = $school->name;
                }
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

    public static function findSchoolById($id) {
        try {
            $db = new Database();
            $db->query("SELECT id, code, name, status FROM schools WHERE id = :id LIMIT 1");
            $db->bind(':id', (int)$id);
            return $db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    private static function findSchoolByCode($code) {
        try {
            $db = new Database();
            $db->query("SELECT id, code, name, status FROM schools WHERE code = :code AND status = 'active' LIMIT 1");
            $db->bind(':code', $code);
            return $db->single();
        } catch (PDOException $e) {
            return null;
        }
    }

    private static function findSchoolByDomain($domain) {
        try {
            $db = new Database();
            $db->query("SELECT id, code, name, status FROM schools WHERE domain = :domain AND status = 'active' LIMIT 1");
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

    /**
     * Retrieves the complete active school tenant object with fallback to SiteSetting
     */
    public static function getSchoolDetails() {
        $schoolId = self::getSchoolId();
        try {
            $db = new Database();
            $db->query("SELECT * FROM schools WHERE id = :id LIMIT 1");
            $db->bind(':id', $schoolId);
            $school = $db->single();
            if ($school) {
                return $school;
            }
        } catch (Exception $e) {}

        // Fallback for single-tenant local environment
        return (object)[
            'id' => $schoolId,
            'code' => self::getSchoolCode(),
            'name' => defined('SITENAME') ? SITENAME : 'School Management System',
            'domain' => null,
            'status' => 'active'
        ];
    }

    /**
     * Get all active schools for school switcher or multi-tenant directory
     */
    public static function getAllActiveSchools() {
        try {
            $db = new Database();
            $db->query("SELECT id, code, name, domain, status FROM schools WHERE status = 'active' ORDER BY id ASC");
            return $db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }
}
