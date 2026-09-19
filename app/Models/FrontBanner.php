<?php
class FrontBanner {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getBanners(){
        $this->db->query("SELECT * FROM front_banners WHERE school_id = :school_id ORDER BY sort_order ASC, created_at DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function addBanner($data){
        $this->db->query("INSERT INTO front_banners (school_id, title, image, link, description, sort_order) VALUES (:school_id, :title, :image, :link, :desc, :order)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':link', $data['link']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':order', $data['sort_order']);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_banners_' . TenantContext::getSchoolId());
        }
        return $res;
    }

    public function deleteBanner($id){
        $this->db->query("SELECT image FROM front_banners WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        if($row && file_exists($row->image)){
            unlink($row->image);
        }

        $this->db->query("DELETE FROM front_banners WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_banners_' . TenantContext::getSchoolId());
        }
        return $res;
    }
}
