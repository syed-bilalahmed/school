<?php
class DownloadCenter {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function addContent($data){
        $this->db->query("INSERT INTO content (school_id, content_title, content_type, available_for, class_id, section_id, file_path, description, upload_date, uploaded_by)
                          VALUES (:school_id, :title, :type, :avail, :cid, :sid, :file, :desc, :date, :uid)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':avail', $data['available_for']);
        $this->db->bind(':cid', $data['class_id']);
        $this->db->bind(':sid', $data['section_id']);
        $this->db->bind(':file', $data['file_path']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':date', $data['upload_date']);
        $this->db->bind(':uid', $data['uploaded_by']);
        return $this->db->execute();
    }

    public function getContent($role, $class_id = null){
        $sql = "SELECT c.*, u.name as uploaded_by_name, cl.class_name 
                FROM content c
                JOIN users u ON c.uploaded_by = u.id
                LEFT JOIN classes cl ON c.class_id = cl.id
                WHERE c.school_id = :school_id";
        
        // Filter Logic
        if($role == 'student'){
             $sql .= " AND (c.available_for = 'all' OR c.available_for = 'student')";
             if($class_id){
                  $sql .= " AND (c.class_id IS NULL OR c.class_id = :cid)";
             }
        } elseif($role == 'staff' || $role == 'teacher'){
             $sql .= " AND (c.available_for = 'all' OR c.available_for = 'staff')";
        }
        // Admin sees all
        
        $sql .= " ORDER BY c.upload_date DESC";
        
        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        if($role == 'student' && $class_id){
            $this->db->bind(':cid', $class_id);
        }
        
        return $this->db->resultSet();
    }

    public function deleteContent($id){
        // First get file path to delete file
        $this->db->query("SELECT file_path FROM content WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        
        if($row){
            if(file_exists($row->file_path)){
                unlink($row->file_path);
            }
        }

        $this->db->query("DELETE FROM content WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
