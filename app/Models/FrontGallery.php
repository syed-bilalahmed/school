<?php
class FrontGallery {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getGallery(){
        $this->db->query("SELECT * FROM front_gallery WHERE school_id = :school_id ORDER BY created_at DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function addImage($data){
        $this->db->query("INSERT INTO front_gallery (school_id, title, description, featured_image) VALUES (:school_id, :title, :desc, :image)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':image', $data['image']);
        return $this->db->execute();
    }

    public function deleteImage($id){
        // First get image path to delete file
        $this->db->query("SELECT featured_image FROM front_gallery WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        if($row){
            if(file_exists($row->featured_image)){
                unlink($row->featured_image);
            }
        }

        $this->db->query("DELETE FROM front_gallery WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
