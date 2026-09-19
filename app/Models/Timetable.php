<?php
// app/Models/Timetable.php

class Timetable {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function getTimetable($class_id, $section_id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT ct.*, COALESCE(s.subject_name, s.name) as subject_name, COALESCE(s.subject_code, s.code) as subject_code, s.type as subject_type, st.name as staff_name 
                          FROM class_timetables ct
                          JOIN subjects s ON ct.subject_id = s.id
                          LEFT JOIN users st ON ct.staff_id = st.id
                          WHERE ct.class_id = :cid AND ct.section_id = :sid AND ct.school_id = :school_id
                          ORDER BY FIELD(ct.day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), ct.time_from");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':cid', (int)$class_id);
        $this->db->bind(':sid', (int)$section_id);
        return $this->db->resultSet();
    }

    public function getTimetableByClassSection($class_id, $section_id){
        return $this->getTimetable($class_id, $section_id);
    }

    // Automatic Conflict & Clash Detection
    public function checkClash($data, $exclude_id = null){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $day = trim($data['day_name']);
        $from = date('H:i', strtotime($data['time_from']));
        $to = date('H:i', strtotime($data['time_to']));
        $staffId = !empty($data['staff_id']) ? (int)$data['staff_id'] : null;
        $roomNo = !empty($data['room_no']) ? trim($data['room_no']) : null;
        $classId = (int)$data['class_id'];
        $sectionId = (int)$data['section_id'];

        // 1. Check Section Clash (Section already has class in that time)
        $sql = "SELECT ct.*, COALESCE(s.subject_name, s.name) as subject_name, c.class_name, sec.section_name 
                FROM class_timetables ct
                JOIN subjects s ON ct.subject_id = s.id
                JOIN classes c ON ct.class_id = c.id
                JOIN sections sec ON ct.section_id = sec.id
                WHERE ct.school_id = :school_id 
                  AND ct.day_name = :day 
                  AND ct.class_id = :cid 
                  AND ct.section_id = :sid
                  AND (TIME(:new_from) < TIME(ct.time_to) AND TIME(:new_to) > TIME(ct.time_from))";
        if ($exclude_id) $sql .= " AND ct.id != " . (int)$exclude_id;
        
        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':day', $day);
        $this->db->bind(':cid', $classId);
        $this->db->bind(':sid', $sectionId);
        $this->db->bind(':new_from', $from);
        $this->db->bind(':new_to', $to);
        $clash = $this->db->single();
        if ($clash) {
            return [
                'has_clash' => true,
                'message' => "Schedule Conflict: {$clash->class_name} - {$clash->section_name} already has '{$clash->subject_name}' scheduled on {$day} from {$clash->time_from} to {$clash->time_to}."
            ];
        }

        // 2. Check Teacher Clash (Teacher assigned to another class at same time)
        if ($staffId) {
            $sql = "SELECT ct.*, s.subject_name, c.class_name, sec.section_name, u.name as teacher_name
                    FROM class_timetables ct
                    JOIN subjects s ON ct.subject_id = s.id
                    JOIN classes c ON ct.class_id = c.id
                    JOIN sections sec ON ct.section_id = sec.id
                    JOIN users u ON ct.staff_id = u.id
                    WHERE ct.school_id = :school_id 
                      AND ct.day_name = :day 
                      AND ct.staff_id = :staff_id
                      AND (TIME(:new_from) < TIME(ct.time_to) AND TIME(:new_to) > TIME(ct.time_from))";
            if ($exclude_id) $sql .= " AND ct.id != " . (int)$exclude_id;
            
            $this->db->query($sql);
            $this->db->bind(':school_id', $schoolId);
            $this->db->bind(':day', $day);
            $this->db->bind(':staff_id', $staffId);
            $this->db->bind(':new_from', $from);
            $this->db->bind(':new_to', $to);
            $clash = $this->db->single();
            if ($clash) {
                return [
                    'has_clash' => true,
                    'message' => "Teacher Clash: {$clash->teacher_name} is already assigned to {$clash->class_name} - {$clash->section_name} ({$clash->subject_name}) on {$day} from {$clash->time_from} to {$clash->time_to}."
                ];
            }
        }

        // 3. Check Room Clash (Room occupied by another class at same time)
        if (!empty($roomNo)) {
            $sql = "SELECT ct.*, s.subject_name, c.class_name, sec.section_name 
                    FROM class_timetables ct
                    JOIN subjects s ON ct.subject_id = s.id
                    JOIN classes c ON ct.class_id = c.id
                    JOIN sections sec ON ct.section_id = sec.id
                    WHERE ct.school_id = :school_id 
                      AND ct.day_name = :day 
                      AND LOWER(ct.room_no) = LOWER(:room_no)
                      AND (TIME(:new_from) < TIME(ct.time_to) AND TIME(:new_to) > TIME(ct.time_from))";
            if ($exclude_id) $sql .= " AND ct.id != " . (int)$exclude_id;
            
            $this->db->query($sql);
            $this->db->bind(':school_id', $schoolId);
            $this->db->bind(':day', $day);
            $this->db->bind(':room_no', $roomNo);
            $this->db->bind(':new_from', $from);
            $this->db->bind(':new_to', $to);
            $clash = $this->db->single();
            if ($clash) {
                return [
                    'has_clash' => true,
                    'message' => "Room Conflict: Room '{$roomNo}' is already occupied by {$clash->class_name} - {$clash->section_name} ({$clash->subject_name}) on {$day} from {$clash->time_from} to {$clash->time_to}."
                ];
            }
        }

        return ['has_clash' => false];
    }

    public function addTimetable($data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $from = date('H:i', strtotime($data['time_from']));
        $to = date('H:i', strtotime($data['time_to']));

        $this->db->query("INSERT INTO class_timetables (school_id, class_id, section_id, subject_id, staff_id, day_name, time_from, time_to, room_no) 
                          VALUES (:school_id, :cid, :sid, :subid, :tid, :day, :tf, :tt, :room)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':cid', (int)$data['class_id']);
        $this->db->bind(':sid', (int)$data['section_id']);
        $this->db->bind(':subid', (int)$data['subject_id']);
        $this->db->bind(':tid', !empty($data['staff_id']) ? (int)$data['staff_id'] : null);
        $this->db->bind(':day', trim($data['day_name']));
        $this->db->bind(':tf', $from);
        $this->db->bind(':tt', $to);
        $this->db->bind(':room', !empty($data['room_no']) ? trim($data['room_no']) : null);
        return $this->db->execute();
    }
    
    public function deleteTimetable($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("DELETE FROM class_timetables WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', (int)$id);
        return $this->db->execute();
    }
}
