<?php
class FrontNews {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getNews(){
        $this->db->query("SELECT * FROM front_news WHERE school_id = :school_id ORDER BY news_date DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function addNews($data){
        $this->db->query("INSERT INTO front_news (school_id, title, description, news_date, image) VALUES (:school_id, :title, :desc, :date, :image)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':date', $data['news_date']);
        $this->db->bind(':image', $data['image']);
        return $this->db->execute();
    }

    public function deleteNews($id){
        $this->db->query("SELECT image FROM front_news WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        if($row && file_exists($row->image)){
             unlink($row->image);
        }

        $this->db->query("DELETE FROM front_news WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
