<?php
// app/Models/AcademicSession.php

class AcademicSession {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getSessions() {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM academic_sessions WHERE school_id = :school_id ORDER BY id DESC");
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }

    public function getCurrentSession() {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        if (class_exists('QueryCache')) {
            return QueryCache::remember('current_session_' . $schoolId, 600, function() use ($schoolId) {
                return $this->fetchCurrentSessionFromDb($schoolId);
            });
        }
        return $this->fetchCurrentSessionFromDb($schoolId);
    }

    private function fetchCurrentSessionFromDb($schoolId) {
        $this->db->query("SELECT * FROM academic_sessions WHERE school_id = :school_id AND is_current = 1 LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $res = $this->db->single();
        if (!$res) {
            $this->db->query("SELECT * FROM academic_sessions WHERE school_id = :school_id ORDER BY id DESC LIMIT 1");
            $this->db->bind(':school_id', $schoolId);
            $res = $this->db->single();
        }
        return $res;
    }

    public function getSessionById($id) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM academic_sessions WHERE id = :id AND school_id = :school_id LIMIT 1");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->single();
    }

    public function createSession($data) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $isCurrent = !empty($data['is_current']) ? 1 : 0;

        if ($isCurrent) {
            $this->unsetAllCurrent($schoolId);
        }

        $this->db->query("INSERT INTO academic_sessions (school_id, session_name, start_date, end_date, is_current) 
                          VALUES (:school_id, :session_name, :start_date, :end_date, :is_current)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':session_name', trim($data['session_name']));
        $this->db->bind(':start_date', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':end_date', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':is_current', $isCurrent);

        return $this->db->execute();
    }

    public function updateSession($data) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $id = (int)$data['id'];
        $isCurrent = !empty($data['is_current']) ? 1 : 0;

        if ($isCurrent) {
            $this->unsetAllCurrent($schoolId);
        }

        $this->db->query("UPDATE academic_sessions 
                          SET session_name = :session_name, start_date = :start_date, end_date = :end_date, is_current = :is_current 
                          WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':session_name', trim($data['session_name']));
        $this->db->bind(':start_date', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':end_date', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':is_current', $isCurrent);
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', $schoolId);

        return $this->db->execute();
    }

    public function setCurrentSession($id) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->unsetAllCurrent($schoolId);

        $this->db->query("UPDATE academic_sessions SET is_current = 1 WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    public function deleteSession($id) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        // Don't delete if it's the current session or only session left
        $current = $this->getCurrentSession();
        if ($current && $current->id == $id) {
            return false;
        }

        $this->db->query("DELETE FROM academic_sessions WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    private function unsetAllCurrent($schoolId) {
        if (class_exists('QueryCache')) {
            QueryCache::forget('current_session_' . $schoolId);
        }
        $this->db->query("UPDATE academic_sessions SET is_current = 0 WHERE school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->execute();
    }
}
