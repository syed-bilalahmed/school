<?php
class StudentHouse {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getHouses(){
        $this->db->query("SELECT * FROM student_houses WHERE school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function addHouse($data){
        $this->db->query("INSERT INTO student_houses (school_id, house_name, description) VALUES (:school_id, :name, :desc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':name', $data['house_name']);
        $this->db->bind(':desc', $data['description']);
        return $this->db->execute();
    }

    public function deleteHouse($id){
        $this->db->query("DELETE FROM student_houses WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
