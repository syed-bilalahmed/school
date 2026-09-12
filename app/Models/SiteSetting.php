<?php
class SiteSetting {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    private static $cachedSettings = null;

    // Static helper to retrieve any setting globally without instantiating model
    public static function getGlobal($key, $default = ''){
        if(self::$cachedSettings === null){
            self::loadGlobalCache();
        }
        return isset(self::$cachedSettings[$key]) ? self::$cachedSettings[$key] : $default;
    }

    public static function getGlobalSettings(){
        if(self::$cachedSettings === null){
            self::loadGlobalCache();
        }
        return self::$cachedSettings ?: [];
    }

    public static function clearCache(){
        self::$cachedSettings = null;
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        if (class_exists('QueryCache')) {
            QueryCache::forget('site_settings_' . $schoolId);
        }
    }

    private static function loadGlobalCache(){
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        if (class_exists('QueryCache')) {
            self::$cachedSettings = QueryCache::remember('site_settings_' . $schoolId, 300, function() use ($schoolId) {
                return self::fetchSettingsFromDb($schoolId);
            });
        } else {
            self::$cachedSettings = self::fetchSettingsFromDb($schoolId);
        }
    }

    private static function fetchSettingsFromDb($schoolId){
        try {
            $db = new Database();
            $db->query("SELECT setting_key, setting_value FROM site_settings WHERE school_id = :school_id OR school_id IS NULL ORDER BY school_id ASC");
            $db->bind(':school_id', $schoolId);
            $rows = $db->resultSet() ?: [];
            $settings = [];
            foreach($rows as $r){
                $settings[$r->setting_key] = $r->setting_value;
            }
            return $settings;
        } catch (Exception $e) {
            return [];
        }
    }

    // Get all settings as an associative array
    public function getAllSettings(){
        self::clearCache();
        return self::getGlobalSettings();
    }

    // Get specific setting with default fallback
    public function getSetting($key, $default = ''){
        return self::getGlobal($key, $default);
    }

    // Update specific setting
    public function updateSetting($key, $value){
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $this->db->query("SELECT id FROM site_settings WHERE setting_key = :key AND (school_id = :school_id OR school_id IS NULL) LIMIT 1");
        $this->db->bind(':key', $key);
        $this->db->bind(':school_id', $schoolId);
        $existing = $this->db->single();

        if ($existing) {
            $this->db->query("UPDATE site_settings SET setting_value = :value WHERE id = :id");
            $this->db->bind(':value', $value);
            $this->db->bind(':id', $existing->id);
            $res = $this->db->execute();
        } else {
            $this->db->query("INSERT INTO site_settings (school_id, setting_key, setting_value) VALUES (:school_id, :key, :value)");
            $this->db->bind(':school_id', $schoolId);
            $this->db->bind(':key', $key);
            $this->db->bind(':value', $value);
            $res = $this->db->execute();
        }
        self::clearCache();
        return $res;
    }

    // Update all settings from associative array
    public function updateSettings($data){
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        foreach($data as $key => $value){
             if($key == 'logo' && empty($value)) continue; // Skip if empty logo
             
             $this->db->query("SELECT id FROM site_settings WHERE setting_key = :key AND (school_id = :school_id OR school_id IS NULL) LIMIT 1");
             $this->db->bind(':key', $key);
             $this->db->bind(':school_id', $schoolId);
             $existing = $this->db->single();

             if($existing){
                 $this->db->query("UPDATE site_settings SET setting_value = :value WHERE id = :id");
                 $this->db->bind(':id', $existing->id);
                 $this->db->bind(':value', $value);
                 $this->db->execute();
             } else {
                 $this->db->query("INSERT INTO site_settings (school_id, setting_key, setting_value) VALUES (:school_id, :key, :value)");
                 $this->db->bind(':school_id', $schoolId);
                 $this->db->bind(':key', $key);
                 $this->db->bind(':value', $value);
                 $this->db->execute();
             }
        }

        // Sync school name and logo to schools table
        try {
            if(!empty($data['school_name'])){
                $this->db->query("UPDATE schools SET name = :name WHERE id = :id");
                $this->db->bind(':name', $data['school_name']);
                $this->db->bind(':id', $schoolId);
                $this->db->execute();
            }
            if(!empty($data['logo'])){
                $this->db->query("UPDATE schools SET logo = :logo WHERE id = :id");
                $this->db->bind(':logo', $data['logo']);
                $this->db->bind(':id', $schoolId);
                $this->db->execute();
            }
        } catch(Exception $e) {
            // Ignore if schools table does not match
        }

        self::clearCache();
        return true;
    }

    // Fetch all roles for RBAC matrix
    public function getRoles(){
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $this->db->query("SELECT * FROM roles WHERE school_id = :school_id OR school_id IS NULL ORDER BY id ASC");
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet() ?: [];
    }

    // Add custom role
    public function addRole($name){
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $name)));
        if (empty($cleanName)) return false;

        $this->db->query("SELECT id FROM roles WHERE name = :name AND (school_id = :school_id OR school_id IS NULL) LIMIT 1");
        $this->db->bind(':name', $cleanName);
        $this->db->bind(':school_id', $schoolId);
        if ($this->db->single()) {
            return false;
        }

        $this->db->query("INSERT INTO roles (school_id, name) VALUES (:school_id, :name)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':name', $cleanName);
        return $this->db->execute();
    }

    // Fetch all permissions grouped by category
    public function getPermissions(){
        $this->db->query("SELECT * FROM permissions ORDER BY category ASC, id ASC");
        return $this->db->resultSet() ?: [];
    }

    // Fetch role-permission matrix: [role_id => [permission_id => true]]
    public function getRolePermissionMatrix(){
        $this->db->query("SELECT role_id, permission_id FROM role_permissions");
        $rows = $this->db->resultSet();
        $matrix = [];
        if ($rows) {
            foreach ($rows as $r) {
                $matrix[$r->role_id][$r->permission_id] = true;
            }
        }
        return $matrix;
    }

    // Sync role permissions from posted array
    public function syncRolePermissions($rolePermissionsMap){
        $roles = $this->getRoles();
        foreach ($roles as $role) {
            $roleId = (int)$role->id;
            // Super Admin always maintains all permissions
            if ($role->name === 'super_admin') {
                continue;
            }

            // Clear existing permissions for this role
            $this->db->query("DELETE FROM role_permissions WHERE role_id = :role_id");
            $this->db->bind(':role_id', $roleId);
            $this->db->execute();

            // Insert selected permissions
            if (isset($rolePermissionsMap[$roleId]) && is_array($rolePermissionsMap[$roleId])) {
                foreach ($rolePermissionsMap[$roleId] as $permId) {
                    $this->db->query("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :perm_id)");
                    $this->db->bind(':role_id', $roleId);
                    $this->db->bind(':perm_id', (int)$permId);
                    $this->db->execute();
                }
            }
        }
        return true;
    }

    // Prefix & Feature Toggle Helpers
    public function getPrefix($type, $default = ''){
        return $this->getSetting('prefix_' . $type, $default);
    }

    public function isFeatureEnabled($key, $default = true){
        $val = $this->getSetting($key, $default ? '1' : '0');
        return ($val === '1' || $val === 'true' || $val === 'on' || $val === 1);
    }

    // Session Management
    public function getSessions(){
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $this->db->query("SELECT * FROM academic_sessions WHERE school_id = :school_id ORDER BY id DESC");
        $this->db->bind(':school_id', $schoolId);
        $sessions = $this->db->resultSet();
        if (!$sessions) {
            $this->db->query("SELECT * FROM sessions ORDER BY id DESC");
            $sessions = $this->db->resultSet();
        }
        return $sessions ?: [];
    }

    public function addSession($session){
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $this->db->query("INSERT INTO academic_sessions (school_id, session_name, is_current) VALUES (:school_id, :sess, 0)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':sess', $session);
        return $this->db->execute();
    }
    
    public function deleteSession($id){
        $this->db->query("DELETE FROM academic_sessions WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}

