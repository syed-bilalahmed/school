<?php
class Homework {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function addHomework($data){
        $this->db->query("INSERT INTO homework (school_id, class_id, section_id, subject_id, homework_date, submission_date, description, created_by) 
                          VALUES (:school_id, :cid, :secid, :subid, :hdate, :sdate, :desc, :uid)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':cid', $data['class_id']);
        $this->db->bind(':secid', $data['section_id']);
        $this->db->bind(':subid', $data['subject_id']);
        $this->db->bind(':hdate', $data['homework_date']);
        $this->db->bind(':sdate', $data['submission_date']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':uid', $data['created_by']);
        return $this->db->execute();
    }

    public function getHomework($class_id=null, $section_id=null, $subject_id=null){
        $sql = "SELECT h.*, c.class_name, sec.section_name, sub.subject_name 
                FROM homework h
                JOIN classes c ON h.class_id = c.id
                JOIN sections sec ON h.section_id = sec.id
                JOIN subjects sub ON h.subject_id = sub.id
                WHERE h.school_id = :school_id";
        
        if($class_id) $sql .= " AND h.class_id = :cid";
        if($section_id) $sql .= " AND h.section_id = :secid";
        if($subject_id) $sql .= " AND h.subject_id = :subid";
        
        $sql .= " ORDER BY h.homework_date DESC";
        
        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        if($class_id) $this->db->bind(':cid', $class_id);
        if($section_id) $this->db->bind(':secid', $section_id);
        if($subject_id) $this->db->bind(':subid', $subject_id);
        
        return $this->db->resultSet();
    }

    public function getHomeworkById($id){
        $this->db->query("SELECT h.*, c.class_name, sec.section_name, sub.subject_name 
                FROM homework h
                JOIN classes c ON h.class_id = c.id
                JOIN sections sec ON h.section_id = sec.id
                JOIN subjects sub ON h.subject_id = sub.id
                WHERE h.id = :id AND h.school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->single();
    }

    // Evaluation
    public function getEvaluation($homework_id){
         // Get all students for this homework's class/section and their status
         // First get homework details to know class/section
         $hw = $this->getHomeworkById($homework_id);
         if(!$hw) return [];
         
         $this->db->query("SELECT s.id as student_id, s.name, s.roll_no, s.admission_no,
                           he.status, he.marks, he.note
                           FROM students s
                           LEFT JOIN homework_evaluation he ON s.id = he.student_id AND he.homework_id = :hid
                           WHERE s.class_id = :cid AND s.section_id = :secid AND s.school_id = :school_id
                           ORDER BY s.roll_no");
         $this->db->bind(':school_id', TenantContext::getSchoolId());
         $this->db->bind(':hid', $homework_id);
         $this->db->bind(':cid', $hw->class_id);
         $this->db->bind(':secid', $hw->section_id);
         return $this->db->resultSet();
    }

    public function saveEvaluation($homework_id, $student_id, $status, $marks, $note){
        $this->db->query("INSERT INTO homework_evaluation (school_id, homework_id, student_id, status, marks, note, evaluation_date)
                          VALUES (:school_id, :hid, :sid, :status, :marks, :note, :date)
                          ON DUPLICATE KEY UPDATE
                          status = :status_up, marks = :marks_up, note = :note_up, evaluation_date = :date_up");
        
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':hid', $homework_id);
        $this->db->bind(':sid', $student_id);
        $this->db->bind(':status', $status);
        $this->db->bind(':marks', $marks);
        $this->db->bind(':note', $note);
        $this->db->bind(':date', date('Y-m-d'));

        $this->db->bind(':status_up', $status);
        $this->db->bind(':marks_up', $marks);
        $this->db->bind(':note_up', $note);
        $this->db->bind(':date_up', date('Y-m-d'));
        
        return $this->db->execute();
    }
}
