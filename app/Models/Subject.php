<?php
// app/Models/Subject.php

class Subject {
    private $db;

    public function __construct(){
        $this->db = new Database();
        $this->ensureSubjectColumns();
    }

    /**
     * Self-healing schema guard: Guarantees subject_name, subject_code, and legacy name/code columns exist and stay synced.
     */
    private function ensureSubjectColumns(): void {
        static $checked = false;
        $lockFile = (defined('APPROOT') ? APPROOT : dirname(__DIR__)) . '/cache/schema_subjects_v1.lock';
        if ($checked || file_exists($lockFile)) {
            $checked = true;
            return;
        }
        $checked = true;

        try {
            $pdo = Database::getWritePdo();
            $existingColumns = [];
            $colStmt = $pdo->query("SHOW COLUMNS FROM `subjects`");
            if ($colStmt) {
                while ($row = $colStmt->fetch(PDO::FETCH_ASSOC)) {
                    $existingColumns[strtolower($row['Field'])] = true;
                }
            }

            // Ensure subject_name exists
            if (!isset($existingColumns['subject_name'])) {
                try {
                    $pdo->exec("ALTER TABLE `subjects` ADD COLUMN `subject_name` VARCHAR(100) NULL");
                } catch (Throwable $e) {}
            }

            // Ensure subject_code exists
            if (!isset($existingColumns['subject_code'])) {
                try {
                    $pdo->exec("ALTER TABLE `subjects` ADD COLUMN `subject_code` VARCHAR(50) NULL");
                } catch (Throwable $e) {}
            }

            // Ensure name exists for backwards compatibility
            if (!isset($existingColumns['name'])) {
                try {
                    $pdo->exec("ALTER TABLE `subjects` ADD COLUMN `name` VARCHAR(100) NULL");
                } catch (Throwable $e) {}
            }

            // Ensure code exists for backwards compatibility
            if (!isset($existingColumns['code'])) {
                try {
                    $pdo->exec("ALTER TABLE `subjects` ADD COLUMN `code` VARCHAR(50) NULL");
                } catch (Throwable $e) {}
            }

            // Ensure is_core exists
            if (!isset($existingColumns['is_core'])) {
                try {
                    $pdo->exec("ALTER TABLE `subjects` ADD COLUMN `is_core` TINYINT(1) DEFAULT 1");
                } catch (Throwable $e) {}
            }

            // Ensure full_marks exists
            if (!isset($existingColumns['full_marks'])) {
                try {
                    $pdo->exec("ALTER TABLE `subjects` ADD COLUMN `full_marks` DECIMAL(5,2) DEFAULT 100.00");
                } catch (Throwable $e) {}
            }

            // Ensure passing_marks exists
            if (!isset($existingColumns['passing_marks'])) {
                try {
                    $pdo->exec("ALTER TABLE `subjects` ADD COLUMN `passing_marks` DECIMAL(5,2) DEFAULT 33.00");
                } catch (Throwable $e) {}
            }

            // Ensure credit_hours exists
            if (!isset($existingColumns['credit_hours'])) {
                try {
                    $pdo->exec("ALTER TABLE `subjects` ADD COLUMN `credit_hours` INT DEFAULT 3");
                } catch (Throwable $e) {}
            }

            // Synchronize data between name <-> subject_name and code <-> subject_code
            try {
                $pdo->exec("UPDATE `subjects` SET `subject_name` = `name` WHERE (`subject_name` IS NULL OR `subject_name` = '') AND (`name` IS NOT NULL AND `name` != '')");
                $pdo->exec("UPDATE `subjects` SET `name` = `subject_name` WHERE (`name` IS NULL OR `name` = '') AND (`subject_name` IS NOT NULL AND `subject_name` != '')");
                $pdo->exec("UPDATE `subjects` SET `subject_code` = `code` WHERE (`subject_code` IS NULL OR `subject_code` = '') AND (`code` IS NOT NULL AND `code` != '')");
                $pdo->exec("UPDATE `subjects` SET `code` = `subject_code` WHERE (`code` IS NULL OR `code` = '') AND (`subject_code` IS NOT NULL AND `subject_code` != '')");
            } catch (Throwable $e) {}

            @touch($lockFile);
        } catch (Throwable $e) {
            // Table may not exist yet during initial installation
            @touch($lockFile);
        }
    }

    // Add a generic subject with Core/Optional, Passing Marks, Full Marks
    public function addSubject($data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $name = trim($data['name']);
        $code = trim($data['code'] ?? '');
        $this->db->query("INSERT INTO subjects (school_id, subject_name, name, subject_code, code, type, is_core, full_marks, passing_marks, credit_hours) 
                          VALUES (:school_id, :name, :name_dup, :code, :code_dup, :type, :is_core, :full_marks, :passing_marks, :credit_hours)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':name', $name);
        $this->db->bind(':name_dup', $name);
        $this->db->bind(':code', $code);
        $this->db->bind(':code_dup', $code);
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
        $name = trim($data['name']);
        $code = trim($data['code'] ?? '');
        $this->db->query("UPDATE subjects 
                          SET subject_name = :name, name = :name_dup, subject_code = :code, code = :code_dup, type = :type, 
                              is_core = :is_core, full_marks = :full_marks, passing_marks = :passing_marks, credit_hours = :credit_hours 
                          WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':name', $name);
        $this->db->bind(':name_dup', $name);
        $this->db->bind(':code', $code);
        $this->db->bind(':code_dup', $code);
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
        $this->db->query("SELECT *, COALESCE(subject_name, name) as subject_name, COALESCE(subject_code, code) as subject_code FROM subjects WHERE id = :id AND school_id = :school_id LIMIT 1");
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
        $this->db->query("SELECT *, COALESCE(subject_name, name) as subject_name, COALESCE(subject_code, code) as subject_code FROM subjects WHERE school_id = :school_id ORDER BY is_core DESC, COALESCE(subject_name, name) ASC");
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
