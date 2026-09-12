<?php
class SchoolClass {
    private $db;
    private static $columnsEnsured = false;

    public function __construct(){
        $this->db = new Database;
    }

    public function ensureColumns(){
        if (self::$columnsEnsured) return;
        try {
            $this->db->query("SHOW COLUMNS FROM classes LIKE 'school_id'");
            if (!$this->db->single()) {
                $this->db->query("ALTER TABLE classes ADD COLUMN school_id INT NULL DEFAULT 1 AFTER id");
                $this->db->execute();
            }
            self::$columnsEnsured = true;
        } catch (Throwable $e) {}
    }

    public function getClasses(){
        $this->ensureColumns();
        $schoolId = TenantContext::getSchoolId() ?: 1;
        if (class_exists('QueryCache')) {
            return QueryCache::remember('classes_list_' . $schoolId, 300, function() use ($schoolId) {
                $this->db->query("SELECT * FROM classes WHERE school_id = :school_id ORDER BY class_name ASC");
                $this->db->bind(':school_id', $schoolId);
                return $this->db->resultSet();
            });
        }
        $this->db->query("SELECT * FROM classes WHERE school_id = :school_id ORDER BY class_name ASC");
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }

    public function getClassesWithStats(){
        $this->ensureColumns();
        $schoolId = TenantContext::getSchoolId() ?: 1;
        try {
            $this->db->query("SELECT 
                                c.id,
                                c.class_name,
                                c.created_at,
                                COUNT(DISTINCT s.id) AS section_count,
                                GROUP_CONCAT(DISTINCT s.section_name ORDER BY s.section_name SEPARATOR ', ') AS section_names,
                                COUNT(DISTINCT st.id) AS student_count
                              FROM classes c
                              LEFT JOIN sections s ON s.class_id = c.id AND s.school_id = c.school_id
                              LEFT JOIN students st ON st.class_id = c.id AND (st.status = 'active' OR st.status IS NULL)
                              WHERE c.school_id = :school_id
                              GROUP BY c.id, c.class_name, c.created_at
                              ORDER BY c.class_name ASC");
            $this->db->bind(':school_id', $schoolId);
            return $this->db->resultSet();
        } catch (Throwable $e) {
            return $this->getClasses();
        }
    }

    public function getClassById($id){
        $this->ensureColumns();
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM classes WHERE id = :id AND school_id = :school_id LIMIT 1");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->single();
    }

    public function isClassNameTaken($name, $excludeId = null){
        $this->ensureColumns();
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "SELECT id FROM classes WHERE LOWER(TRIM(class_name)) = LOWER(TRIM(:name)) AND school_id = :school_id";
        if (!empty($excludeId)) {
            $sql .= " AND id != :exclude_id";
        }
        $sql .= " LIMIT 1";

        $this->db->query($sql);
        $this->db->bind(':name', trim($name));
        $this->db->bind(':school_id', $schoolId);
        if (!empty($excludeId)) {
            $this->db->bind(':exclude_id', (int)$excludeId);
        }
        $row = $this->db->single();
        return !empty($row);
    }

    public function addClass($data){
        $this->ensureColumns();
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("INSERT INTO classes (school_id, class_name) VALUES (:school_id, :class_name)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':class_name', trim($data['class_name']));

        if($this->db->execute()){
            if (class_exists('QueryCache')) { QueryCache::forget('classes_list_' . $schoolId); }
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function updateClass($data){
        $this->ensureColumns();
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("UPDATE classes SET class_name = :class_name WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':class_name', trim($data['class_name']));
        $this->db->bind(':id', (int)$data['id']);
        $this->db->bind(':school_id', $schoolId);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) { QueryCache::forget('classes_list_' . $schoolId); }
        return $res;
    }

    public function getClassStudentCount($classId){
        try {
            $this->db->query("SELECT COUNT(*) AS total FROM students WHERE class_id = :class_id");
            $this->db->bind(':class_id', (int)$classId);
            $row = $this->db->single();
            return $row ? (int)$row->total : 0;
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function getClassSectionCount($classId){
        try {
            $this->db->query("SELECT COUNT(*) AS total FROM sections WHERE class_id = :class_id");
            $this->db->bind(':class_id', (int)$classId);
            $row = $this->db->single();
            return $row ? (int)$row->total : 0;
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function deleteClass($id){
        $this->ensureColumns();
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("DELETE FROM classes WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', (int)$id);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) { QueryCache::forget('classes_list_' . $schoolId); }
        return $res;
    }

    public function countClasses(){
        $this->ensureColumns();
        try {
            $this->db->query("SELECT COUNT(*) as total FROM classes WHERE school_id = :school_id");
            $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
            $row = $this->db->single();
            return $row ? (int)$row->total : 0;
        } catch (Throwable $e) {
            return 0;
        }
    }
}
