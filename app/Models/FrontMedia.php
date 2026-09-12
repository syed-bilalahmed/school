<?php
class FrontMedia {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getMediaFiles(){
        $this->db->query("SELECT * FROM front_media WHERE school_id = :school_id ORDER BY uploaded_at DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function addFile($data){
        $this->db->query("INSERT INTO front_media (school_id, file_name, file_path, file_type) VALUES (:school_id, :name, :path, :type)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':name', $data['file_name']);
        $this->db->bind(':path', $data['file_path']);
        $this->db->bind(':type', $data['file_type']);
        return $this->db->execute();
    }

    public function deleteFile($id){
        $this->db->query("SELECT file_path FROM front_media WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        if($row && file_exists($row->file_path)){
             unlink($row->file_path);
        }

        $this->db->query("DELETE FROM front_media WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
