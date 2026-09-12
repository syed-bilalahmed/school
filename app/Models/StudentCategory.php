<?php
class StudentCategory {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getCategories(){
        $this->db->query("SELECT * FROM student_categories WHERE school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function addCategory($name){
        $this->db->query("INSERT INTO student_categories (school_id, category_name) VALUES (:school_id, :name)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':name', $name);
        return $this->db->execute();
    }

    public function deleteCategory($id){
        $this->db->query("DELETE FROM student_categories WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
