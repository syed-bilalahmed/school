<?php
class FrontMenu {
    private $db;

    public function __construct(){
        $this->db = new Database;
        $this->ensureTableSchema();
    }

    public function ensureTableSchema(){
        try {
            $this->db->query("SHOW COLUMNS FROM front_menus LIKE 'dropdown_group'");
            if(!$this->db->single()){
                $this->db->query("ALTER TABLE front_menus ADD COLUMN dropdown_group VARCHAR(50) DEFAULT 'none' AFTER sort_order");
                $this->db->execute();
            }
        } catch(Throwable $e){
            // Graceful fallback
        }
    }

    public function getMenus(){
        $this->ensureTableSchema();
        $schoolId = class_exists('TenantContext') ? TenantContext::getSchoolId() : null;
        if ($schoolId) {
            $this->db->query("SELECT m.*, COALESCE(m.dropdown_group, 'none') as dropdown_group, p.slug as page_slug 
                              FROM front_menus m 
                              LEFT JOIN front_pages p ON m.page_id = p.id 
                              WHERE (m.school_id = :school_id OR m.school_id IS NULL) 
                              ORDER BY m.sort_order ASC");
            $this->db->bind(':school_id', $schoolId);
        } else {
            $this->db->query("SELECT m.*, COALESCE(m.dropdown_group, 'none') as dropdown_group, p.slug as page_slug 
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
        $this->db->query("INSERT INTO front_menus (school_id, title, link, page_id, sort_order, dropdown_group) 
                          VALUES (:school_id, :title, :link, :pid, :order, :dropdown_group)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':link', $data['link']);
        $this->db->bind(':pid', $data['page_id']);
        $this->db->bind(':order', $data['sort_order'] ?? 10);
        $this->db->bind(':dropdown_group', $dropdownGroup);
        return $this->db->execute();
    }

    public function updateMenu($data){
        $this->ensureTableSchema();
        $dropdownGroup = !empty($data['dropdown_group']) ? trim($data['dropdown_group']) : 'none';
        $this->db->query("UPDATE front_menus 
                          SET title = :title, link = :link, page_id = :pid, sort_order = :order, dropdown_group = :dropdown_group 
                          WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':link', $data['link']);
        $this->db->bind(':pid', $data['page_id']);
        $this->db->bind(':order', $data['sort_order'] ?? 10);
        $this->db->bind(':dropdown_group', $dropdownGroup);
        return $this->db->execute();
    }

    public function deleteMenu($id){
        $this->db->query("DELETE FROM front_menus WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
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
