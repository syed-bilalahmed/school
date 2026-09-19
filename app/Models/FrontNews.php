<?php
class FrontNews {
    private $db;

    private static $schemaEnsured = false;

    public function __construct(){
        $this->db = new Database;
        if (!self::$schemaEnsured) {
            $this->ensureSchema();
        }
    }

    private function ensureSchema(){
        $lockFile = (defined('APPROOT') ? APPROOT : dirname(__DIR__)) . '/cache/schema_front_news_v1.lock';
        if (self::$schemaEnsured || file_exists($lockFile)) {
            self::$schemaEnsured = true;
            return;
        }
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS front_news (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL DEFAULT 1,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NULL,
                description LONGTEXT NULL,
                content LONGTEXT NULL,
                image VARCHAR(255) NULL,
                news_date DATE NULL,
                publish_date DATE NULL,
                is_active ENUM('yes', 'no') DEFAULT 'yes',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $this->db->execute();

            $columnsToEnsure = [
                'school_id' => "ALTER TABLE front_news ADD COLUMN school_id INT NOT NULL DEFAULT 1",
                'title' => "ALTER TABLE front_news ADD COLUMN title VARCHAR(255) NOT NULL DEFAULT ''",
                'slug' => "ALTER TABLE front_news ADD COLUMN slug VARCHAR(255) NULL",
                'description' => "ALTER TABLE front_news ADD COLUMN description LONGTEXT NULL",
                'content' => "ALTER TABLE front_news ADD COLUMN content LONGTEXT NULL",
                'image' => "ALTER TABLE front_news ADD COLUMN image VARCHAR(255) NULL",
                'news_date' => "ALTER TABLE front_news ADD COLUMN news_date DATE NULL",
                'publish_date' => "ALTER TABLE front_news ADD COLUMN publish_date DATE NULL",
                'is_active' => "ALTER TABLE front_news ADD COLUMN is_active ENUM('yes', 'no') DEFAULT 'yes'"
            ];

            foreach ($columnsToEnsure as $col => $alterSql) {
                try {
                    $colSafe = preg_replace('/[^a-zA-Z0-9_]/', '', $col);
                    $this->db->query("SHOW COLUMNS FROM front_news LIKE '$colSafe'");
                    $exists = $this->db->single();
                    if (!$exists) {
                        $this->db->query($alterSql);
                        $this->db->execute();
                    }
                } catch (Throwable $e) {}
            }

            // Sync news_date with publish_date and description with content if they were null
            try {
                $this->db->query("UPDATE front_news SET news_date = publish_date WHERE news_date IS NULL AND publish_date IS NOT NULL");
                $this->db->execute();
                $this->db->query("UPDATE front_news SET publish_date = news_date WHERE publish_date IS NULL AND news_date IS NOT NULL");
                $this->db->execute();
                $this->db->query("UPDATE front_news SET description = content WHERE description IS NULL AND content IS NOT NULL");
                $this->db->execute();
                $this->db->query("UPDATE front_news SET content = description WHERE content IS NULL AND description IS NOT NULL");
                $this->db->execute();
            } catch (Throwable $e) {}

        } catch (Throwable $e) {
            error_log("FrontNews ensureSchema error: " . $e->getMessage());
        }

        @touch($lockFile);
        self::$schemaEnsured = true;
    }

    public function getNews(){
        try {
            $this->db->query("SELECT * FROM front_news WHERE school_id = :school_id ORDER BY COALESCE(news_date, publish_date, created_at) DESC");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            return $this->db->resultSet();
        } catch (PDOException $e) {
            $this->ensureSchema();
            $this->db->query("SELECT * FROM front_news WHERE school_id = :school_id ORDER BY id DESC");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            return $this->db->resultSet();
        }
    }

    public function addNews($data){
        $date = !empty($data['news_date']) ? $data['news_date'] : date('Y-m-d');
        $desc = $data['description'] ?? '';
        $title = $data['title'] ?? '';
        $image = $data['image'] ?? null;
        $schoolId = TenantContext::getSchoolId();

        $this->db->query("INSERT INTO front_news (school_id, title, description, content, news_date, publish_date, image) 
                          VALUES (:school_id, :title, :desc, :content, :date, :pub_date, :image)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':title', $title);
        $this->db->bind(':desc', $desc);
        $this->db->bind(':content', $desc);
        $this->db->bind(':date', $date);
        $this->db->bind(':pub_date', $date);
        $this->db->bind(':image', $image);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_news_' . $schoolId);
        }
        return $res;
    }

    public function deleteNews($id){
        $schoolId = TenantContext::getSchoolId();
        $this->db->query("SELECT image FROM front_news WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        if($row && !empty($row->image) && file_exists($row->image)){
             unlink($row->image);
        }

        $this->db->query("DELETE FROM front_news WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $id);
        $res = $this->db->execute();
        if ($res && class_exists('QueryCache')) {
            QueryCache::forget('front_news_' . $schoolId);
        }
        return $res;
    }
}
