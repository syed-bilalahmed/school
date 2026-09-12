<?php
class Notification {
    private $db;

    public function __construct(){
        $this->db = new Database;
        $this->ensureTable();
    }

    private function ensureTable(){
        $this->db->query("CREATE TABLE IF NOT EXISTS notifications (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            school_id INT(11) DEFAULT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            link VARCHAR(255) DEFAULT NULL,
            role VARCHAR(50) DEFAULT 'admin',
            is_read INT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $this->db->execute();
    }

    public function addNotification($data){
        $this->db->query("INSERT INTO notifications (school_id, title, message, link, role, is_read) VALUES (:school_id, :title, :message, :link, :role, 0)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':message', $data['message']);
        $this->db->bind(':link', $data['link']);
        $this->db->bind(':role', $data['role']);
        return $this->db->execute();
    }

    public function getUnreadNotifications($role = 'admin'){
        try {
            $this->db->query("SELECT * FROM notifications WHERE (role = :role OR role = 'super_admin') AND is_read = 0 AND school_id = :school_id ORDER BY created_at DESC");
            $this->db->bind(':role', $role);
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            return $this->db->resultSet();
        } catch (Throwable $e) {
            return [];
        }
    }
    
    public function markAsRead($id){
        try {
            $this->db->query("UPDATE notifications SET is_read = 1 WHERE id = :id AND school_id = :school_id");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (Throwable $e) {
            return false;
        }
    }
    
    public function deleteNotification($id){
        $this->db->query("DELETE FROM notifications WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
