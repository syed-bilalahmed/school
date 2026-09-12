<?php
// app/Models/Section.php

class Section {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    // Get Sections (with Class Name and Assigned Class Teacher)
    public function getSections(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT sections.*, classes.class_name, users.name as class_teacher_name 
                          FROM sections 
                          JOIN classes ON sections.class_id = classes.id 
                          LEFT JOIN users ON sections.class_teacher_id = users.id
                          WHERE sections.school_id = :school_id
                          ORDER BY classes.class_name, sections.section_name");
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }
    
    // Get Sections by specific Class ID
    public function getSectionsByClassId($class_id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.*, u.name as class_teacher_name 
                          FROM sections s
                          LEFT JOIN users u ON s.class_teacher_id = u.id
                          WHERE s.class_id = :class_id AND s.school_id = :school_id 
                          ORDER BY s.section_name");
        $this->db->bind(':class_id', (int)$class_id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }

    public function getSectionById($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM sections WHERE id = :id AND school_id = :school_id LIMIT 1");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->single();
    }

    public function addSection($data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("INSERT INTO sections (school_id, class_id, section_name, class_teacher_id) 
                          VALUES (:school_id, :class_id, :section_name, :teacher_id)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':class_id', (int)$data['class_id']);
        $this->db->bind(':section_name', trim($data['section_name']));
        $this->db->bind(':teacher_id', !empty($data['class_teacher_id']) ? (int)$data['class_teacher_id'] : null);
        return $this->db->execute();
    }

    public function updateSection($data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("UPDATE sections 
                          SET class_id = :class_id, section_name = :section_name, class_teacher_id = :teacher_id 
                          WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':class_id', (int)$data['class_id']);
        $this->db->bind(':section_name', trim($data['section_name']));
        $this->db->bind(':teacher_id', !empty($data['class_teacher_id']) ? (int)$data['class_teacher_id'] : null);
        $this->db->bind(':id', (int)$data['id']);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    public function deleteSection($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("DELETE FROM sections WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', (int)$id);
        return $this->db->execute();
    }
}
