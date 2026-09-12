<?php
// app/Models/Subject.php

class Subject {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    // Add a generic subject with Core/Optional, Passing Marks, Full Marks
    public function addSubject($data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("INSERT INTO subjects (school_id, subject_name, subject_code, type, is_core, full_marks, passing_marks, credit_hours) 
                          VALUES (:school_id, :name, :code, :type, :is_core, :full_marks, :passing_marks, :credit_hours)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':name', trim($data['name']));
        $this->db->bind(':code', trim($data['code']));
        $this->db->bind(':type', !empty($data['type']) ? $data['type'] : 'Theory');
        $this->db->bind(':is_core', isset($data['is_core']) ? (int)$data['is_core'] : 1);
        $this->db->bind(':full_marks', !empty($data['full_marks']) ? (float)$data['full_marks'] : 100.00);
        $this->db->bind(':passing_marks', !empty($data['passing_marks']) ? (float)$data['passing_marks'] : 33.00);
        $this->db->bind(':credit_hours', !empty($data['credit_hours']) ? (int)$data['credit_hours'] : 3);
        return $this->db->execute();
    }

    // Update subject
    public function updateSubject($data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("UPDATE subjects 
                          SET subject_name = :name, subject_code = :code, type = :type, 
                              is_core = :is_core, full_marks = :full_marks, passing_marks = :passing_marks, credit_hours = :credit_hours 
                          WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':name', trim($data['name']));
        $this->db->bind(':code', trim($data['code']));
        $this->db->bind(':type', !empty($data['type']) ? $data['type'] : 'Theory');
        $this->db->bind(':is_core', isset($data['is_core']) ? (int)$data['is_core'] : 1);
        $this->db->bind(':full_marks', !empty($data['full_marks']) ? (float)$data['full_marks'] : 100.00);
        $this->db->bind(':passing_marks', !empty($data['passing_marks']) ? (float)$data['passing_marks'] : 33.00);
        $this->db->bind(':credit_hours', !empty($data['credit_hours']) ? (int)$data['credit_hours'] : 3);
        $this->db->bind(':id', (int)$data['id']);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // Get single subject by ID
    public function getSubjectById($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM subjects WHERE id = :id AND school_id = :school_id LIMIT 1");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->single();
    }

    // Delete subject
    public function deleteSubject($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("DELETE FROM subjects WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // Get all generic subjects
    public function getSubjects(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM subjects WHERE school_id = :school_id ORDER BY is_core DESC, subject_name ASC");
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }

    // Assign subject to Class & Section with Periods/week
    public function assignSubject($data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        
        // Check if already assigned
        $this->db->query("SELECT id FROM class_subjects 
                          WHERE school_id = :school_id AND class_id = :cid AND section_id = :sid AND subject_id = :subid LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':cid', $data['class_id']);
        $this->db->bind(':sid', $data['section_id']);
        $this->db->bind(':subid', $data['subject_id']);
        $existing = $this->db->single();

        if($existing){
            $this->db->query("UPDATE class_subjects 
                              SET teacher_id = :tid, periods_per_week = :periods 
                              WHERE id = :id AND school_id = :school_id");
            $this->db->bind(':tid', !empty($data['teacher_id']) ? $data['teacher_id'] : null);
            $this->db->bind(':periods', !empty($data['periods_per_week']) ? (int)$data['periods_per_week'] : 5);
            $this->db->bind(':id', $existing->id);
            $this->db->bind(':school_id', $schoolId);
            return $this->db->execute();
        }

        $this->db->query("INSERT INTO class_subjects (school_id, class_id, section_id, subject_id, teacher_id, periods_per_week) 
                          VALUES (:school_id, :class_id, :section_id, :subject_id, :teacher_id, :periods)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':class_id', $data['class_id']);
        $this->db->bind(':section_id', $data['section_id']);
        $this->db->bind(':subject_id', $data['subject_id']);
        $this->db->bind(':teacher_id', !empty($data['teacher_id']) ? $data['teacher_id'] : null);
        $this->db->bind(':periods', !empty($data['periods_per_week']) ? (int)$data['periods_per_week'] : 5);
        return $this->db->execute();
    }

    // Delete class subject allocation
    public function deleteClassSubject($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("DELETE FROM class_subjects WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // Get subjects for a specific Class & Section
    public function getSubjectsByClassSection($class_id, $section_id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT cs.*, s.subject_name, s.subject_code, s.type, s.is_core, s.full_marks, s.passing_marks, u.name as teacher_name
                          FROM class_subjects cs
                          JOIN subjects s ON cs.subject_id = s.id
                          LEFT JOIN users u ON cs.teacher_id = u.id
                          WHERE cs.class_id = :class_id AND cs.section_id = :section_id AND cs.school_id = :school_id
                          ORDER BY s.is_core DESC, s.subject_name ASC");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':class_id', $class_id);
        $this->db->bind(':section_id', $section_id);
        return $this->db->resultSet();
    }

    // Get all allocations across school
    public function getAllAllocations($class_id = null, $section_id = null){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "SELECT cs.*, c.class_name, sec.section_name, s.subject_name, s.subject_code, s.type, s.is_core, s.full_marks, s.passing_marks, u.name as teacher_name
                FROM class_subjects cs
                JOIN classes c ON cs.class_id = c.id
                JOIN sections sec ON cs.section_id = sec.id
                JOIN subjects s ON cs.subject_id = s.id
                LEFT JOIN users u ON cs.teacher_id = u.id
                WHERE cs.school_id = :school_id";
        
        if($class_id) $sql .= " AND cs.class_id = :cid";
        if($section_id) $sql .= " AND cs.section_id = :secid";
        $sql .= " ORDER BY c.class_name ASC, sec.section_name ASC, s.subject_name ASC";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if($class_id) $this->db->bind(':cid', $class_id);
        if($section_id) $this->db->bind(':secid', $section_id);
        return $this->db->resultSet();
    }

    // Calculate Teacher Workload metrics
    public function getTeacherWorkloadSummary(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT u.id as teacher_id, u.name as teacher_name, u.email as teacher_email,
                                 COUNT(DISTINCT cs.class_id) as total_classes,
                                 COUNT(DISTINCT cs.section_id) as total_sections,
                                 COUNT(DISTINCT cs.subject_id) as total_subjects,
                                 COALESCE(SUM(cs.periods_per_week), 0) as total_periods_per_week
                          FROM users u
                          LEFT JOIN class_subjects cs ON u.id = cs.teacher_id AND cs.school_id = :school_id
                          WHERE u.role = 'teacher' AND (u.school_id = :school_id OR u.school_id IS NULL)
                          GROUP BY u.id, u.name, u.email
                          ORDER BY total_periods_per_week DESC, u.name ASC");
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }
}
