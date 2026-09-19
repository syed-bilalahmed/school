<?php
class FrontGallery {
    private $db;

    private static $schemaEnsured = false;

    public function __construct(){
        $this->db = new Database;
        if (!self::$schemaEnsured) {
            $this->ensureSchema();
        }
    }

    private function ensureSchema(){
        $lockFile = (defined('APPROOT') ? APPROOT : dirname(__DIR__)) . '/cache/schema_front_gallery_v1.lock';
        if (self::$schemaEnsured || file_exists($lockFile)) {
            self::$schemaEnsured = true;
            return;
        }
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS front_gallery (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL DEFAULT 1,
                title VARCHAR(255) NOT NULL,
                description TEXT NULL,
                featured_image VARCHAR(255) NULL,
                cover_image VARCHAR(255) NULL,
                is_active ENUM('yes', 'no') DEFAULT 'yes',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->db->execute();

            $columnsToEnsure = [
                'school_id' => "ALTER TABLE front_gallery ADD COLUMN school_id INT NOT NULL DEFAULT 1",
                'title' => "ALTER TABLE front_gallery ADD COLUMN title VARCHAR(255) NOT NULL DEFAULT ''",
                'description' => "ALTER TABLE front_gallery ADD COLUMN description TEXT NULL",
                'featured_image' => "ALTER TABLE front_gallery ADD COLUMN featured_image VARCHAR(255) NULL",
                'cover_image' => "ALTER TABLE front_gallery ADD COLUMN cover_image VARCHAR(255) NULL",
                'is_active' => "ALTER TABLE front_gallery ADD COLUMN is_active ENUM('yes', 'no') DEFAULT 'yes'"
            ];

            foreach ($columnsToEnsure as $col => $alterSql) {
                try {
                    $colSafe = preg_replace('/[^a-zA-Z0-9_]/', '', $col);
                    $this->db->query("SHOW COLUMNS FROM front_gallery LIKE '$colSafe'");
                    $exists = $this->db->single();
                    if (!$exists) {
                        $this->db->query($alterSql);
                        $this->db->execute();
                    }
                } catch (Throwable $e) {}
            }

            try {
                $this->db->query("UPDATE front_gallery SET featured_image = cover_image WHERE (featured_image IS NULL OR featured_image = '') AND cover_image IS NOT NULL");
                $this->db->execute();
                $this->db->query("UPDATE front_gallery SET cover_image = featured_image WHERE (cover_image IS NULL OR cover_image = '') AND featured_image IS NOT NULL");
                $this->db->execute();
            } catch (Throwable $e) {}

        } catch (Throwable $e) {
            error_log("FrontGallery ensureSchema error: " . $e->getMessage());
        }

        @touch($lockFile);
        self::$schemaEnsured = true;
    }

    public function getGallery(){
        try {
            $this->db->query("SELECT * FROM front_gallery WHERE school_id = :school_id ORDER BY created_at DESC");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            return $this->db->resultSet();
        } catch (PDOException $e) {
            $this->ensureSchema();
            $this->db->query("SELECT * FROM front_gallery WHERE school_id = :school_id ORDER BY id DESC");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            return $this->db->resultSet();
        }
    }

    public function addImage($data){
        $this->db->query("INSERT INTO front_gallery (school_id, title, description, featured_image) VALUES (:school_id, :title, :desc, :image)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':image', $data['image']);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_gallery_' . TenantContext::getSchoolId());
        }
        return $res;
    }

    public function deleteImage($id){
        // First get image path to delete file
        $this->db->query("SELECT featured_image FROM front_gallery WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $row = $this->db->single();

        if($row){
            if(file_exists($row->featured_image)){
                unlink($row->featured_image);
            }
        }

        $this->db->query("DELETE FROM front_gallery WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_gallery_' . TenantContext::getSchoolId());
        }
        return $res;
    }
}
