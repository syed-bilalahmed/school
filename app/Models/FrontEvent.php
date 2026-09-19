<?php
class FrontEvent {
    private $db;

    private static $schemaEnsured = false;

    public function __construct(){
        $this->db = new Database;
        if (!self::$schemaEnsured) {
            $this->ensureSchema();
        }
    }

    private function ensureSchema(){
        $lockFile = (defined('APPROOT') ? APPROOT : dirname(__DIR__)) . '/cache/schema_front_events_v1.lock';
        if (self::$schemaEnsured || file_exists($lockFile)) {
            self::$schemaEnsured = true;
            return;
        }
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS front_events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL DEFAULT 1,
                title VARCHAR(255) NOT NULL,
                venue VARCHAR(255) NULL,
                start_date DATETIME NULL,
                event_date DATE NULL,
                start_time TIME NULL,
                end_time TIME NULL,
                description TEXT NULL,
                image VARCHAR(255) NULL,
                is_active ENUM('yes', 'no') DEFAULT 'yes',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->db->execute();

            $columnsToEnsure = [
                'school_id' => "ALTER TABLE front_events ADD COLUMN school_id INT NOT NULL DEFAULT 1",
                'title' => "ALTER TABLE front_events ADD COLUMN title VARCHAR(255) NOT NULL DEFAULT ''",
                'venue' => "ALTER TABLE front_events ADD COLUMN venue VARCHAR(255) NULL",
                'start_date' => "ALTER TABLE front_events ADD COLUMN start_date DATETIME NULL",
                'event_date' => "ALTER TABLE front_events ADD COLUMN event_date DATE NULL",
                'description' => "ALTER TABLE front_events ADD COLUMN description TEXT NULL",
                'image' => "ALTER TABLE front_events ADD COLUMN image VARCHAR(255) NULL",
                'is_active' => "ALTER TABLE front_events ADD COLUMN is_active ENUM('yes', 'no') DEFAULT 'yes'"
            ];

            foreach ($columnsToEnsure as $col => $alterSql) {
                try {
                    $colSafe = preg_replace('/[^a-zA-Z0-9_]/', '', $col);
                    $this->db->query("SHOW COLUMNS FROM front_events LIKE '$colSafe'");
                    $exists = $this->db->single();
                    if (!$exists) {
                        $this->db->query($alterSql);
                        $this->db->execute();
                    }
                } catch (Throwable $e) {}
            }

            try {
                $this->db->query("UPDATE front_events SET start_date = event_date WHERE start_date IS NULL AND event_date IS NOT NULL");
                $this->db->execute();
                $this->db->query("UPDATE front_events SET event_date = DATE(start_date) WHERE event_date IS NULL AND start_date IS NOT NULL");
                $this->db->execute();
            } catch (Throwable $e) {}

        } catch (Throwable $e) {
            error_log("FrontEvent ensureSchema error: " . $e->getMessage());
        }

        @touch($lockFile);
        self::$schemaEnsured = true;
    }

    public function getEvents(){
        try {
            $this->db->query("SELECT * FROM front_events WHERE school_id = :school_id ORDER BY COALESCE(start_date, event_date, created_at) DESC");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            return $this->db->resultSet();
        } catch (PDOException $e) {
            $this->ensureSchema();
            $this->db->query("SELECT * FROM front_events WHERE school_id = :school_id ORDER BY id DESC");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            return $this->db->resultSet();
        }
    }

    public function getEventById($id){
        $this->db->query("SELECT * FROM front_events WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addEvent($data){
        $this->db->query("INSERT INTO front_events (school_id, title, description, start_date, venue, image) VALUES (:school_id, :title, :desc, :start, :venue, :image)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':start', $data['start_date']);
        $this->db->bind(':venue', $data['venue']);
        $this->db->bind(':image', $data['image']);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_events_' . TenantContext::getSchoolId());
        }
        return $res;
    }

    public function updateEvent($data){
        $this->db->query("UPDATE front_events SET title = :title, description = :desc, start_date = :start, venue = :venue, image = :image WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':start', $data['start_date']);
        $this->db->bind(':venue', $data['venue']);
        $this->db->bind(':image', $data['image']);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_events_' . TenantContext::getSchoolId());
        }
        return $res;
    }

    public function deleteEvent($id){
        $this->db->query("DELETE FROM front_events WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_events_' . TenantContext::getSchoolId());
        }
        return $res;
    }
}
