<?php
class FrontMenu {
    private $db;

    private static $schemaChecked = false;

    public function __construct(){
        $this->db = new Database;
        $this->ensureTableSchema();
    }

    public function ensureTableSchema(){
        $lockFile = (defined('APPROOT') ? APPROOT : dirname(__DIR__)) . '/cache/schema_front_menus_v1.lock';
        if (self::$schemaChecked || file_exists($lockFile)) {
            self::$schemaChecked = true;
            return;
        }
        try {
            $pdo = Database::getWritePdo();
            $existingColumns = [];
            $colStmt = $pdo->query("SHOW COLUMNS FROM `front_menus`");
            if ($colStmt) {
                while ($row = $colStmt->fetch(PDO::FETCH_ASSOC)) {
                    $existingColumns[strtolower($row['Field'])] = true;
                }
            }

            if (!isset($existingColumns['title'])) {
                try { $pdo->exec("ALTER TABLE `front_menus` ADD COLUMN `title` VARCHAR(150) NULL"); } catch (Throwable $e) {}
            }
            if (!isset($existingColumns['menu_title'])) {
                try { $pdo->exec("ALTER TABLE `front_menus` ADD COLUMN `menu_title` VARCHAR(150) NULL"); } catch (Throwable $e) {}
            }
            if (!isset($existingColumns['link'])) {
                try { $pdo->exec("ALTER TABLE `front_menus` ADD COLUMN `link` VARCHAR(255) NULL"); } catch (Throwable $e) {}
            }
            if (!isset($existingColumns['menu_url'])) {
                try { $pdo->exec("ALTER TABLE `front_menus` ADD COLUMN `menu_url` VARCHAR(255) NULL"); } catch (Throwable $e) {}
            }
            if (!isset($existingColumns['page_id'])) {
                try { $pdo->exec("ALTER TABLE `front_menus` ADD COLUMN `page_id` INT DEFAULT 0"); } catch (Throwable $e) {}
            }
            if (!isset($existingColumns['dropdown_group'])) {
                try { $pdo->exec("ALTER TABLE `front_menus` ADD COLUMN `dropdown_group` VARCHAR(50) DEFAULT 'none'"); } catch (Throwable $e) {}
            }
            if (!isset($existingColumns['sort_order'])) {
                try { $pdo->exec("ALTER TABLE `front_menus` ADD COLUMN `sort_order` INT DEFAULT 0"); } catch (Throwable $e) {}
            }

            // Sync data between title <-> menu_title and link <-> menu_url
            try {
                $pdo->exec("UPDATE `front_menus` SET `title` = `menu_title` WHERE (`title` IS NULL OR `title` = '') AND (`menu_title` IS NOT NULL AND `menu_title` != '')");
                $pdo->exec("UPDATE `front_menus` SET `menu_title` = `title` WHERE (`menu_title` IS NULL OR `menu_title` = '') AND (`title` IS NOT NULL AND `title` != '')");
                $pdo->exec("UPDATE `front_menus` SET `link` = `menu_url` WHERE (`link` IS NULL OR `link` = '') AND (`menu_url` IS NOT NULL AND `menu_url` != '')");
                $pdo->exec("UPDATE `front_menus` SET `menu_url` = `link` WHERE (`menu_url` IS NULL OR `menu_url` = '') AND (`link` IS NOT NULL AND `link` != '')");
            } catch (Throwable $e) {}

            @touch($lockFile);
            self::$schemaChecked = true;
        } catch(Throwable $e){
            // Graceful fallback
            @touch($lockFile);
            self::$schemaChecked = true;
        }
    }

    public function getMenus(){
        $this->ensureTableSchema();
        $schoolId = class_exists('TenantContext') ? TenantContext::getSchoolId() : null;
        if ($schoolId) {
            $this->db->query("SELECT m.*, COALESCE(m.title, m.menu_title) as title, COALESCE(m.menu_title, m.title) as menu_title, COALESCE(m.link, m.menu_url) as link, COALESCE(m.menu_url, m.link) as menu_url, COALESCE(m.dropdown_group, 'none') as dropdown_group, p.slug as page_slug 
                              FROM front_menus m 
                              LEFT JOIN front_pages p ON m.page_id = p.id 
                              WHERE (m.school_id = :school_id OR m.school_id IS NULL) 
                              ORDER BY m.sort_order ASC");
            $this->db->bind(':school_id', $schoolId);
        } else {
            $this->db->query("SELECT m.*, COALESCE(m.title, m.menu_title) as title, COALESCE(m.menu_title, m.title) as menu_title, COALESCE(m.link, m.menu_url) as link, COALESCE(m.menu_url, m.link) as menu_url, COALESCE(m.dropdown_group, 'none') as dropdown_group, p.slug as page_slug 
                              FROM front_menus m 
                              LEFT JOIN front_pages p ON m.page_id = p.id 
                              ORDER BY m.sort_order ASC");
        }
        return $this->db->resultSet();
    }

    public function getMenuById($id){
        $this->ensureTableSchema();
        $schoolId = class_exists('TenantContext') ? TenantContext::getSchoolId() : null;
        if ($schoolId) {
            $this->db->query("SELECT * FROM front_menus WHERE id = :id AND (school_id = :school_id OR school_id IS NULL)");
            $this->db->bind(':school_id', $schoolId);
        } else {
            $this->db->query("SELECT * FROM front_menus WHERE id = :id");
        }
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addMenu($data){
        $this->ensureTableSchema();
        $dropdownGroup = !empty($data['dropdown_group']) ? trim($data['dropdown_group']) : 'none';
        $title = trim($data['title'] ?? $data['menu_title'] ?? '');
        $link = trim($data['link'] ?? $data['menu_url'] ?? '');
        $this->db->query("INSERT INTO front_menus (school_id, title, menu_title, link, menu_url, page_id, sort_order, dropdown_group) 
                          VALUES (:school_id, :title, :title_dup, :link, :link_dup, :pid, :order, :dropdown_group)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $title);
        $this->db->bind(':title_dup', $title);
        $this->db->bind(':link', $link);
        $this->db->bind(':link_dup', $link);
        $this->db->bind(':pid', $data['page_id'] ?? 0);
        $this->db->bind(':order', $data['sort_order'] ?? 10);
        $this->db->bind(':dropdown_group', $dropdownGroup);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_menus_' . TenantContext::getSchoolId());
        }
        return $res;
    }

    public function updateMenu($data){
        $this->ensureTableSchema();
        $dropdownGroup = !empty($data['dropdown_group']) ? trim($data['dropdown_group']) : 'none';
        $title = trim($data['title'] ?? $data['menu_title'] ?? '');
        $link = trim($data['link'] ?? $data['menu_url'] ?? '');
        $this->db->query("UPDATE front_menus 
                          SET title = :title, menu_title = :title_dup, link = :link, menu_url = :link_dup, page_id = :pid, sort_order = :order, dropdown_group = :dropdown_group 
                          WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $title);
        $this->db->bind(':title_dup', $title);
        $this->db->bind(':link', $link);
        $this->db->bind(':link_dup', $link);
        $this->db->bind(':pid', $data['page_id'] ?? 0);
        $this->db->bind(':order', $data['sort_order'] ?? 10);
        $this->db->bind(':dropdown_group', $dropdownGroup);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_menus_' . TenantContext::getSchoolId());
        }
        return $res;
    }

    public function deleteMenu($id){
        $this->db->query("DELETE FROM front_menus WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_menus_' . TenantContext::getSchoolId());
        }
        return $res;
    }

    public function getMenuByPageId($pageId){
        $this->ensureTableSchema();
        $this->db->query("SELECT * FROM front_menus WHERE page_id = :pid AND (school_id = :school_id OR school_id IS NULL) LIMIT 1");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':pid', $pageId);
        return $this->db->single();
    }

    public function deleteMenuByPageId($pageId){
        $this->db->query("DELETE FROM front_menus WHERE page_id = :pid AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':pid', $pageId);
        return $this->db->execute();
    }

    public function getCustomLinks(){
        $this->ensureTableSchema();
        $this->db->query("SELECT * FROM front_menus WHERE (page_id = 0 OR page_id IS NULL) AND (school_id = :school_id OR school_id IS NULL) ORDER BY sort_order ASC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }
}
