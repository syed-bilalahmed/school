<?php
class FrontCms {
    private $db;
    private static $schemaEnsured = false;

    public function __construct(){
        $this->db = new Database;
        if (!self::$schemaEnsured) {
            $this->ensureSchema();
            self::$schemaEnsured = true;
        }
    }

    private function ensureSchema(){
        $lockFile = (defined('APPROOT') ? APPROOT : dirname(__DIR__)) . '/cache/schema_front_cms_v1.lock';
        if (self::$schemaEnsured || file_exists($lockFile)) {
            self::$schemaEnsured = true;
            return;
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS front_cms_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            school_id INT(11) DEFAULT NULL,
            is_active_website ENUM('yes', 'no') DEFAULT 'yes',
            enable_online_admission ENUM('yes', 'no') DEFAULT 'yes',
            enable_alumni ENUM('yes', 'no') DEFAULT 'yes',
            enable_requirements ENUM('yes', 'no') DEFAULT 'yes',
            enable_fee_structure ENUM('yes', 'no') DEFAULT 'yes',
            enable_emergency_alert ENUM('yes', 'no') DEFAULT 'no',
            emergency_alert_text TEXT NULL,
            emergency_alert_bg VARCHAR(50) DEFAULT 'danger',
            emergency_alert_link VARCHAR(255) NULL,
            footer_text TEXT,
            facebook_url VARCHAR(255),
            twitter_url VARCHAR(255),
            instagram_url VARCHAR(255),
            youtube_url VARCHAR(255),
            google_plus_url VARCHAR(255),
            linkedin_url VARCHAR(255),
            logo VARCHAR(255),
            theme_color VARCHAR(50) DEFAULT 'default',
            layout_type VARCHAR(50) DEFAULT 'standard',
            maintenance_title VARCHAR(255) DEFAULT 'Site Maintenance in Progress',
            maintenance_message TEXT,
            maintenance_eta VARCHAR(255),
            maintenance_background VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $this->db->execute();

        $requiredColumns = [
            'school_id' => "ALTER TABLE front_cms_settings ADD COLUMN school_id INT(11) DEFAULT NULL",
            'is_active_website' => "ALTER TABLE front_cms_settings ADD COLUMN is_active_website ENUM('yes', 'no') DEFAULT 'yes'",
            'enable_online_admission' => "ALTER TABLE front_cms_settings ADD COLUMN enable_online_admission ENUM('yes', 'no') DEFAULT 'yes'",
            'enable_alumni' => "ALTER TABLE front_cms_settings ADD COLUMN enable_alumni ENUM('yes', 'no') DEFAULT 'yes'",
            'enable_requirements' => "ALTER TABLE front_cms_settings ADD COLUMN enable_requirements ENUM('yes', 'no') DEFAULT 'yes'",
            'enable_fee_structure' => "ALTER TABLE front_cms_settings ADD COLUMN enable_fee_structure ENUM('yes', 'no') DEFAULT 'yes'",
            'enable_emergency_alert' => "ALTER TABLE front_cms_settings ADD COLUMN enable_emergency_alert ENUM('yes', 'no') DEFAULT 'no'",
            'emergency_alert_text' => "ALTER TABLE front_cms_settings ADD COLUMN emergency_alert_text TEXT NULL",
            'emergency_alert_bg' => "ALTER TABLE front_cms_settings ADD COLUMN emergency_alert_bg VARCHAR(50) DEFAULT 'danger'",
            'emergency_alert_link' => "ALTER TABLE front_cms_settings ADD COLUMN emergency_alert_link VARCHAR(255) NULL",
            'footer_text' => "ALTER TABLE front_cms_settings ADD COLUMN footer_text TEXT NULL",
            'facebook_url' => "ALTER TABLE front_cms_settings ADD COLUMN facebook_url VARCHAR(255) NULL",
            'twitter_url' => "ALTER TABLE front_cms_settings ADD COLUMN twitter_url VARCHAR(255) NULL",
            'instagram_url' => "ALTER TABLE front_cms_settings ADD COLUMN instagram_url VARCHAR(255) NULL",
            'youtube_url' => "ALTER TABLE front_cms_settings ADD COLUMN youtube_url VARCHAR(255) NULL",
            'google_plus_url' => "ALTER TABLE front_cms_settings ADD COLUMN google_plus_url VARCHAR(255) NULL",
            'linkedin_url' => "ALTER TABLE front_cms_settings ADD COLUMN linkedin_url VARCHAR(255) NULL",
            'logo' => "ALTER TABLE front_cms_settings ADD COLUMN logo VARCHAR(255) NULL",
            'theme_color' => "ALTER TABLE front_cms_settings ADD COLUMN theme_color VARCHAR(50) DEFAULT 'default'",
            'layout_type' => "ALTER TABLE front_cms_settings ADD COLUMN layout_type VARCHAR(50) DEFAULT 'standard'",
            'maintenance_title' => "ALTER TABLE front_cms_settings ADD COLUMN maintenance_title VARCHAR(255) DEFAULT 'Site Maintenance in Progress'",
            'maintenance_message' => "ALTER TABLE front_cms_settings ADD COLUMN maintenance_message TEXT NULL",
            'maintenance_eta' => "ALTER TABLE front_cms_settings ADD COLUMN maintenance_eta VARCHAR(255) NULL",
            'maintenance_background' => "ALTER TABLE front_cms_settings ADD COLUMN maintenance_background VARCHAR(255) NULL"
        ];

        foreach($requiredColumns as $column => $alterSql){
            try {
                $colSafe = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
                $this->db->query("SHOW COLUMNS FROM front_cms_settings LIKE '$colSafe'");
                $exists = $this->db->single();

                if(!$exists){
                    $this->db->query($alterSql);
                    $this->db->execute();
                }
            } catch (Throwable $e) {
                // Ignore if column already exists or table variance
            }
        }

        // Ensure front_alumni table exists
        $this->db->query("CREATE TABLE IF NOT EXISTS front_alumni (
            id INT AUTO_INCREMENT PRIMARY KEY,
            school_id INT NOT NULL DEFAULT 1,
            name VARCHAR(150) NOT NULL,
            batch_year VARCHAR(50) NOT NULL,
            graduation_class VARCHAR(100) NULL,
            current_position VARCHAR(150) NULL,
            company_organization VARCHAR(150) NULL,
            location VARCHAR(100) NULL,
            image VARCHAR(255) NULL,
            testimonial TEXT NULL,
            linkedin_url VARCHAR(255) NULL,
            email VARCHAR(150) NULL,
            phone VARCHAR(50) NULL,
            is_featured TINYINT(1) DEFAULT 0,
            is_active ENUM('yes', 'no') DEFAULT 'yes',
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $this->db->execute();

        // Ensure front_requirements table exists
        $this->db->query("CREATE TABLE IF NOT EXISTS front_requirements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            school_id INT NOT NULL DEFAULT 1,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(50) NOT NULL,
            department VARCHAR(100) NULL,
            description LONGTEXT NOT NULL,
            eligibility TEXT NULL,
            deadline DATE NULL,
            vacancies INT DEFAULT 1,
            attachment VARCHAR(255) NULL,
            status ENUM('active', 'closed', 'archived') DEFAULT 'active',
            show_in_menu TINYINT(1) DEFAULT 0,
            menu_title VARCHAR(100) NULL,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $this->db->execute();

        $school_id = TenantContext::getSchoolId();
        
        if ($school_id !== null) {
            $this->db->query("SELECT id FROM front_cms_settings WHERE school_id = :school_id LIMIT 1");
            $this->db->bind(':school_id', $school_id);
            $settings = $this->db->single();
            if(!$settings){
                try {
                    $this->db->query("INSERT INTO front_cms_settings (school_id, is_active_website, enable_online_admission, enable_alumni, enable_requirements, enable_fee_structure, enable_emergency_alert, emergency_alert_text, emergency_alert_bg, footer_text, theme_color, layout_type, maintenance_title, maintenance_message, maintenance_eta) VALUES (:school_id, 'yes', 'yes', 'yes', 'yes', 'yes', 'no', '', 'danger', '', 'default', 'standard', 'Site Maintenance in Progress', '', '')");
                    $this->db->bind(':school_id', $school_id);
                    $this->db->execute();
                } catch (Throwable $e) {}
            }
        }

        @touch($lockFile);
        self::$schemaEnsured = true;
    }

    private static $cachedCmsSettings = [];

    public function getSettings(){
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        if (isset(self::$cachedCmsSettings[$schoolId])) {
            return self::$cachedCmsSettings[$schoolId];
        }

        $this->db->query("SELECT * FROM front_cms_settings WHERE school_id = :school_id OR school_id IS NULL ORDER BY school_id DESC LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $settings = $this->db->single();
        if(!$settings){
            $this->db->query("SELECT * FROM front_cms_settings LIMIT 1");
            $settings = $this->db->single();
        }

        // Fallback logo to SiteSetting if not set in CMS
        if ($settings && empty($settings->logo) && class_exists('SiteSetting')) {
            $settings->logo = SiteSetting::getGlobal('logo', '');
        }

        self::$cachedCmsSettings[$schoolId] = $settings;
        return $settings;
    }

    public function updateSettings($data){
        self::$cachedCmsSettings = [];
        $settings = $this->getSettings();
        
        if (!$settings) {
            $this->ensureSchema(); // will insert the default row if it doesn't exist for the school
            $settings = $this->getSettings();
        }
        
        $this->db->query("UPDATE front_cms_settings SET 
            is_active_website = :active,
            enable_online_admission = :online_admission,
            enable_alumni = :alumni,
            enable_requirements = :requirements,
            enable_fee_structure = :fee_structure,
            enable_emergency_alert = :emergency_alert,
            emergency_alert_text = :alert_text,
            emergency_alert_bg = :alert_bg,
            emergency_alert_link = :alert_link,
            footer_text = :footer,
            facebook_url = :fb,
            twitter_url = :tw,
            instagram_url = :insta,
            youtube_url = :yt,
            linkedin_url = :li,
            google_plus_url = :gp,
            theme_color = :theme,
            layout_type = :layout,
            maintenance_title = :maintenance_title,
            maintenance_message = :maintenance_message,
            maintenance_eta = :maintenance_eta
            WHERE id = :id AND school_id = :school_id");
            
        $this->db->bind(':active', $data['is_active_website'] ?? ($settings->is_active_website ?? 'yes'));
        $this->db->bind(':online_admission', $data['enable_online_admission'] ?? ($settings->enable_online_admission ?? 'yes'));
        $this->db->bind(':alumni', $data['enable_alumni'] ?? ($settings->enable_alumni ?? 'yes'));
        $this->db->bind(':requirements', $data['enable_requirements'] ?? ($settings->enable_requirements ?? 'yes'));
        $this->db->bind(':fee_structure', $data['enable_fee_structure'] ?? ($settings->enable_fee_structure ?? 'yes'));
        $this->db->bind(':emergency_alert', $data['enable_emergency_alert'] ?? ($settings->enable_emergency_alert ?? 'no'));
        $this->db->bind(':alert_text', $data['emergency_alert_text'] ?? ($settings->emergency_alert_text ?? ''));
        $this->db->bind(':alert_bg', $data['emergency_alert_bg'] ?? ($settings->emergency_alert_bg ?? 'danger'));
        $this->db->bind(':alert_link', $data['emergency_alert_link'] ?? ($settings->emergency_alert_link ?? ''));
        $this->db->bind(':footer', $data['footer_text'] ?? ($settings->footer_text ?? ''));
        $this->db->bind(':fb', $data['facebook_url'] ?? ($settings->facebook_url ?? ''));
        $this->db->bind(':tw', $data['twitter_url'] ?? ($settings->twitter_url ?? ''));
        $this->db->bind(':insta', $data['instagram_url'] ?? ($settings->instagram_url ?? ''));
        $this->db->bind(':yt', $data['youtube_url'] ?? ($settings->youtube_url ?? ''));
        $this->db->bind(':li', $data['linkedin_url'] ?? ($settings->linkedin_url ?? ''));
        $this->db->bind(':gp', $data['google_plus_url'] ?? ($settings->google_plus_url ?? ''));
        $this->db->bind(':theme', $data['theme_color'] ?? ($settings->theme_color ?? 'default'));
        $this->db->bind(':layout', $data['layout_type'] ?? ($settings->layout_type ?? 'standard'));
        $this->db->bind(':maintenance_title', $data['maintenance_title'] ?? ($settings->maintenance_title ?? 'Site Maintenance in Progress'));
        $this->db->bind(':maintenance_message', $data['maintenance_message'] ?? ($settings->maintenance_message ?? ''));
        $this->db->bind(':maintenance_eta', $data['maintenance_eta'] ?? ($settings->maintenance_eta ?? ''));
        $this->db->bind(':id', $settings->id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        
        return $this->db->execute();
    }
    
    public function updateLogo($path){
         $settings = $this->getSettings();
         if(!$settings) return false;
         
         $this->db->query("UPDATE front_cms_settings SET logo = :logo WHERE id = :id AND school_id = :school_id");
         $this->db->bind(':logo', $path);
         $this->db->bind(':id', $settings->id);
         $this->db->bind(':school_id', TenantContext::getSchoolId());
         return $this->db->execute();
    }

    public function updateMaintenanceBackground($path){
        $settings = $this->getSettings();
        if(!$settings) return false;
        
        $this->db->query("UPDATE front_cms_settings SET maintenance_background = :bg WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':bg', $path);
        $this->db->bind(':id', $settings->id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->execute();
    }

    public function updateFeatureToggle($feature, $val){
        $allowed = ['enable_alumni', 'enable_requirements', 'enable_online_admission', 'enable_fee_structure', 'enable_emergency_alert', 'is_active_website'];
        if(!in_array($feature, $allowed)) return false;

        $settings = $this->getSettings();
        if(!$settings){
            $this->ensureSchema();
            $settings = $this->getSettings();
        }
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $val = ($val === 'yes') ? 'yes' : 'no';

        $this->db->query("UPDATE front_cms_settings SET `$feature` = :val WHERE id = :id AND (school_id = :school_id OR school_id IS NULL)");
        $this->db->bind(':val', $val);
        $this->db->bind(':id', $settings->id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }
}
