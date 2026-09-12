<?php
class FrontAlumni {
    private static $tableEnsured = false;

    public function __construct(){
        $this->db = new Database;
        if(!self::$tableEnsured){
            $this->ensureTable();
            self::$tableEnsured = true;
        }
    }

    private function ensureTable(){
        $this->db->query("CREATE TABLE IF NOT EXISTS front_alumni (
            id INT AUTO_INCREMENT PRIMARY KEY,
            school_id INT NOT NULL DEFAULT 1,
            name VARCHAR(150) NOT NULL,
            batch_year VARCHAR(50) NOT NULL,
            graduation_class VARCHAR(100) NULL,
            current_position VARCHAR(150) NULL,
            company_organization VARCHAR(150) NULL,
            location VARCHAR(100) NULL,
            image VARCHAR(255) NULL,
            testimonial TEXT NULL,
            linkedin_url VARCHAR(255) NULL,
            email VARCHAR(150) NULL,
            phone VARCHAR(50) NULL,
            is_featured TINYINT(1) DEFAULT 0,
            is_active ENUM('yes', 'no') DEFAULT 'yes',
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $this->db->execute();

        try {
            $schoolId = $this->getSchoolId();
            $this->db->query("SELECT COUNT(*) as cnt FROM front_alumni WHERE school_id = :school_id");
            $this->db->bind(':school_id', $schoolId);
            $res = $this->db->single();
            if(empty($res) || (int)($res->cnt ?? 0) === 0){
                $this->db->query("INSERT INTO front_alumni (school_id, name, batch_year, graduation_class, current_position, company_organization, location, testimonial, linkedin_url, email, is_featured, is_active, sort_order) VALUES
                (:sid, 'Dr. Sarah Ahmed', '2019', 'FSc Pre-Medical', 'Clinical Research Fellow', 'University of Oxford', 'Oxford, United Kingdom', 'My formative years at the school nurtured my love for biomedical science and research methodology.', 'https://linkedin.com', 'sarah.ahmed@alumni.edu.pk', 1, 'yes', 1),
                (:sid, 'Engr. Bilal Hassan', '2021', 'A-Levels Science', 'Senior Cloud Solutions Architect', 'Microsoft Corporation', 'Seattle, USA / Remote', 'The rigorous computer lab sessions and robotics competitions provided a springboard for my engineering career.', 'https://linkedin.com', 'bilal.hassan@alumni.edu.pk', 1, 'yes', 2),
                (:sid, 'Barrister Zainab Fatima', '2023', 'Matriculation (Bio-Science)', 'Corporate Law Associate &amp; Advocate', 'Lahore High Court Bar', 'Islamabad, Pakistan', 'Public speaking contests and student council debates developed the poise and analytical clarity I use every day in court.', 'https://linkedin.com', 'zainab.fatima@alumni.edu.pk', 0, 'yes', 3)");
                $this->db->bind(':sid', $schoolId);
                $this->db->execute();
            }
        } catch(Throwable $e) {}
    }

    private function getSchoolId(){
        return class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
    }

    public function getAlumni($onlyActive = true, $batch = null){
        $schoolId = $this->getSchoolId();
        $sql = "SELECT * FROM front_alumni WHERE school_id = :school_id";
        if($onlyActive){
            $sql .= " AND is_active = 'yes'";
        }
        if(!empty($batch)){
            $sql .= " AND batch_year = :batch";
        }
        $sql .= " ORDER BY is_featured DESC, sort_order ASC, created_at DESC";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if(!empty($batch)){
            $this->db->bind(':batch', $batch);
        }
        return $this->db->resultSet() ?: [];
    }

    public function getFeaturedAlumni($limit = 6){
        $schoolId = $this->getSchoolId();
        $this->db->query("SELECT * FROM front_alumni WHERE school_id = :school_id AND is_active = 'yes' AND is_featured = 1 ORDER BY sort_order ASC, created_at DESC LIMIT :limit");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':limit', (int)$limit);
        return $this->db->resultSet() ?: [];
    }

    public function getBatches(){
        $schoolId = $this->getSchoolId();
        $this->db->query("SELECT DISTINCT batch_year FROM front_alumni WHERE school_id = :school_id AND is_active = 'yes' AND batch_year IS NOT NULL AND batch_year != '' ORDER BY batch_year DESC");
        $this->db->bind(':school_id', $schoolId);
        $rows = $this->db->resultSet() ?: [];
        $batches = [];
        foreach($rows as $r){
            $batches[] = $r->batch_year;
        }
        return $batches;
    }

    public function getAlumniById($id){
        $schoolId = $this->getSchoolId();
        $this->db->query("SELECT * FROM front_alumni WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addAlumni($data){
        $schoolId = $this->getSchoolId();
        $this->db->query("INSERT INTO front_alumni (
            school_id, name, batch_year, graduation_class, current_position, 
            company_organization, location, image, testimonial, linkedin_url, 
            email, phone, is_featured, is_active, sort_order
        ) VALUES (
            :school_id, :name, :batch_year, :graduation_class, :current_position, 
            :company_organization, :location, :image, :testimonial, :linkedin_url, 
            :email, :phone, :is_featured, :is_active, :sort_order
        )");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':batch_year', $data['batch_year'] ?? date('Y'));
        $this->db->bind(':graduation_class', $data['graduation_class'] ?? '');
        $this->db->bind(':current_position', $data['current_position'] ?? '');
        $this->db->bind(':company_organization', $data['company_organization'] ?? '');
        $this->db->bind(':location', $data['location'] ?? '');
        $this->db->bind(':image', $data['image'] ?? '');
        $this->db->bind(':testimonial', $data['testimonial'] ?? '');
        $this->db->bind(':linkedin_url', $data['linkedin_url'] ?? '');
        $this->db->bind(':email', $data['email'] ?? '');
        $this->db->bind(':phone', $data['phone'] ?? '');
        $this->db->bind(':is_featured', !empty($data['is_featured']) ? 1 : 0);
        $this->db->bind(':is_active', $data['is_active'] ?? 'yes');
        $this->db->bind(':sort_order', (int)($data['sort_order'] ?? 0));
        return $this->db->execute();
    }

    public function updateAlumni($data){
        $schoolId = $this->getSchoolId();
        $sql = "UPDATE front_alumni SET 
            name = :name, 
            batch_year = :batch_year, 
            graduation_class = :graduation_class, 
            current_position = :current_position, 
            company_organization = :company_organization, 
            location = :location, 
            testimonial = :testimonial, 
            linkedin_url = :linkedin_url, 
            email = :email, 
            phone = :phone, 
            is_featured = :is_featured, 
            is_active = :is_active, 
            sort_order = :sort_order";

        if(!empty($data['image'])){
            $sql .= ", image = :image";
        }

        $sql .= " WHERE id = :id AND school_id = :school_id";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':batch_year', $data['batch_year']);
        $this->db->bind(':graduation_class', $data['graduation_class'] ?? '');
        $this->db->bind(':current_position', $data['current_position'] ?? '');
        $this->db->bind(':company_organization', $data['company_organization'] ?? '');
        $this->db->bind(':location', $data['location'] ?? '');
        $this->db->bind(':testimonial', $data['testimonial'] ?? '');
        $this->db->bind(':linkedin_url', $data['linkedin_url'] ?? '');
        $this->db->bind(':email', $data['email'] ?? '');
        $this->db->bind(':phone', $data['phone'] ?? '');
        $this->db->bind(':is_featured', !empty($data['is_featured']) ? 1 : 0);
        $this->db->bind(':is_active', $data['is_active'] ?? 'yes');
        $this->db->bind(':sort_order', (int)($data['sort_order'] ?? 0));
        if(!empty($data['image'])){
            $this->db->bind(':image', $data['image']);
        }
        return $this->db->execute();
    }

    public function deleteAlumni($id){
        $schoolId = $this->getSchoolId();
        $this->db->query("DELETE FROM front_alumni WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function toggleStatus($id){
        $schoolId = $this->getSchoolId();
        $this->db->query("UPDATE front_alumni SET is_active = IF(is_active = 'yes', 'no', 'yes') WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function toggleFeatured($id){
        $schoolId = $this->getSchoolId();
        $this->db->query("UPDATE front_alumni SET is_featured = IF(is_featured = 1, 0, 1) WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
