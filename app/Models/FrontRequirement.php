<?php
class FrontRequirement {
    private static $tableEnsured = false;

    public function __construct(){
        $this->db = new Database;
        if(!self::$tableEnsured){
            $this->ensureTable();
            self::$tableEnsured = true;
        }
    }

    private function ensureTable(){
        $this->db->query("CREATE TABLE IF NOT EXISTS front_requirements (
            id INT AUTO_INCREMENT PRIMARY KEY,
            school_id INT NOT NULL DEFAULT 1,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(50) NOT NULL,
            department VARCHAR(100) NULL,
            description LONGTEXT NOT NULL,
            eligibility TEXT NULL,
            deadline DATE NULL,
            vacancies INT DEFAULT 1,
            attachment VARCHAR(255) NULL,
            status ENUM('active', 'closed', 'archived') DEFAULT 'active',
            show_in_menu TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        $this->db->execute();

        try {
            $schoolId = $this->getSchoolId();
            $this->db->query("SELECT COUNT(*) as cnt FROM front_requirements WHERE school_id = :school_id");
            $this->db->bind(':school_id', $schoolId);
            $res = $this->db->single();
            if(empty($res) || (int)($res->cnt ?? 0) === 0){
                $this->db->query("INSERT INTO front_requirements (school_id, title, category, department, description, eligibility, deadline, vacancies, status, show_in_menu, menu_title, sort_order) VALUES
                (:sid, 'Senior Physics &amp; Mathematics Faculty (Cambridge O/A Levels)', 'Faculty Career', 'Science &amp; STEM Wing', '<p>We are inviting applications from experienced educators passionate about inquiry-based learning, Cambridge curriculum delivery, and laboratory mentoring.</p><ul><li>Conduct high-impact lectures and practical science experiments.</li><li>Prepare students for annual external board examinations.</li><li>Supervise science club exhibitions and STEM olympiads.</li></ul>', 'Master\\'s or Bachelor\\'s in Physics/Mathematics with 3+ years teaching Cambridge syllabus.', '2026-05-30', 2, 'active', 0, 'Teaching Vacancy', 1),
                (:sid, 'Campus Computer Science &amp; Robotics Lab Procurement 2026-27', 'Procurement &amp; Tender', 'IT &amp; Digital Infrastructure', '<p>Sealed competitive quotations are invited from registered hardware vendors and authorized distributors for upgrading computer science laboratory workstations and robotics components.</p>', 'Registered NTN/STRN vendors with authorized dealership certificates.', '2026-04-15', 1, 'active', 0, 'Lab Tender', 2),
                (:sid, '🚨 Emergency: Transport Route Supervisor &amp; Safety Escort', 'Emergency Urgent Requirement', 'Campus Transport &amp; Logistics', '<p>Immediate urgent opening for a certified campus safety escort officer to oversee route coordination and student welfare during transit.</p>', 'Minimum Intermediate / Graduation with clean background verification.', '2026-03-31', 1, 'active', 1, 'Urgent Vacancy', 3)");
                $this->db->bind(':sid', $schoolId);
                $this->db->execute();
            }
        } catch(Throwable $e) {}
    }

    private function getSchoolId(){
        return class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
    }

    public function getRequirements($onlyActive = true, $category = null){
        $schoolId = $this->getSchoolId();
        $sql = "SELECT * FROM front_requirements WHERE school_id = :school_id";
        if($onlyActive){
            $sql .= " AND status = 'active'";
        }
        if(!empty($category) && $category !== 'all'){
            $sql .= " AND category = :category";
        }
        $sql .= " ORDER BY sort_order ASC, created_at DESC";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if(!empty($category) && $category !== 'all'){
            $this->db->bind(':category', $category);
        }
        return $this->db->resultSet() ?: [];
    }

    public function getCategories(){
        $schoolId = $this->getSchoolId();
        $this->db->query("SELECT DISTINCT category FROM front_requirements WHERE school_id = :school_id AND category IS NOT NULL AND category != '' ORDER BY category ASC");
        $this->db->bind(':school_id', $schoolId);
        $rows = $this->db->resultSet() ?: [];
        $cats = [];
        foreach($rows as $r){
            $cats[] = $r->category;
        }
        return $cats;
    }

    public function getRequirementById($id){
        $schoolId = $this->getSchoolId();
        $this->db->query("SELECT * FROM front_requirements WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addRequirement($data){
        $schoolId = $this->getSchoolId();
        $this->db->query("INSERT INTO front_requirements (
            school_id, title, category, department, description, eligibility, 
            deadline, vacancies, attachment, status, show_in_menu, menu_title, sort_order
        ) VALUES (
            :school_id, :title, :category, :department, :description, :eligibility, 
            :deadline, :vacancies, :attachment, :status, :show_in_menu, :menu_title, :sort_order
        )");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':category', $data['category'] ?? 'Career');
        $this->db->bind(':department', $data['department'] ?? '');
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':eligibility', $data['eligibility'] ?? '');
        $this->db->bind(':deadline', !empty($data['deadline']) ? $data['deadline'] : null);
        $this->db->bind(':vacancies', (int)($data['vacancies'] ?? 1));
        $this->db->bind(':attachment', $data['attachment'] ?? '');
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':show_in_menu', !empty($data['show_in_menu']) ? 1 : 0);
        $this->db->bind(':menu_title', $data['menu_title'] ?? $data['title']);
        $this->db->bind(':sort_order', (int)($data['sort_order'] ?? 0));
        
        if($this->db->execute()){
            $newId = $this->db->lastInsertId();
            if(!empty($data['show_in_menu'])){
                $this->syncMenuLink($newId, $data['menu_title'] ?? $data['title']);
            }
            return $newId;
        }
        return false;
    }

    public function updateRequirement($data){
        $schoolId = $this->getSchoolId();
        $sql = "UPDATE front_requirements SET 
            title = :title, 
            category = :category, 
            department = :department, 
            description = :description, 
            eligibility = :eligibility, 
            deadline = :deadline, 
            vacancies = :vacancies, 
            status = :status, 
            show_in_menu = :show_in_menu, 
            menu_title = :menu_title, 
            sort_order = :sort_order";

        if(isset($data['attachment']) && $data['attachment'] !== false){
            $sql .= ", attachment = :attachment";
        }

        $sql .= " WHERE id = :id AND school_id = :school_id";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':category', $data['category'] ?? 'Career');
        $this->db->bind(':department', $data['department'] ?? '');
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':eligibility', $data['eligibility'] ?? '');
        $this->db->bind(':deadline', !empty($data['deadline']) ? $data['deadline'] : null);
        $this->db->bind(':vacancies', (int)($data['vacancies'] ?? 1));
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':show_in_menu', !empty($data['show_in_menu']) ? 1 : 0);
        $this->db->bind(':menu_title', $data['menu_title'] ?? $data['title']);
        $this->db->bind(':sort_order', (int)($data['sort_order'] ?? 0));
        if(isset($data['attachment']) && $data['attachment'] !== false){
            $this->db->bind(':attachment', $data['attachment']);
        }

        $res = $this->db->execute();
        if($res){
            if(!empty($data['show_in_menu'])){
                $this->syncMenuLink($data['id'], $data['menu_title'] ?? $data['title']);
            } else {
                $this->removeMenuLink($data['id']);
            }
        }
        return $res;
    }

    public function deleteRequirement($id){
        $schoolId = $this->getSchoolId();
        $this->removeMenuLink($id);
        $this->db->query("DELETE FROM front_requirements WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function toggleStatus($id){
        $schoolId = $this->getSchoolId();
        $this->db->query("UPDATE front_requirements SET status = IF(status = 'active', 'closed', 'active') WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function toggleMenu($id){
        $schoolId = $this->getSchoolId();
        $req = $this->getRequirementById($id);
        if(!$req) return false;

        $newMenuVal = ($req->show_in_menu == 1) ? 0 : 1;
        $this->db->query("UPDATE front_requirements SET show_in_menu = :val WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', $id);
        $this->db->bind(':val', $newMenuVal);
        $this->db->execute();

        if($newMenuVal == 1){
            $this->syncMenuLink($id, $req->menu_title ?: $req->title);
        } else {
            $this->removeMenuLink($id);
        }
        return $newMenuVal;
    }

    private function syncMenuLink($reqId, $title){
        try {
            $schoolId = $this->getSchoolId();
            $link = URLROOT . '/home/requirements#req-' . $reqId;
            
            // Check if already in front_menus
            $this->db->query("SELECT id FROM front_menus WHERE school_id = :school_id AND link = :link LIMIT 1");
            $this->db->bind(':school_id', $schoolId);
            $this->db->bind(':link', $link);
            $existing = $this->db->single();

            if($existing){
                $this->db->query("UPDATE front_menus SET title = :title WHERE id = :id");
                $this->db->bind(':title', $title);
                $this->db->bind(':id', $existing->id);
                $this->db->execute();
            } else {
                $this->db->query("INSERT INTO front_menus (school_id, title, link, page_id, sort_order) VALUES (:school_id, :title, :link, 0, 50)");
                $this->db->bind(':school_id', $schoolId);
                $this->db->bind(':title', $title);
                $this->db->bind(':link', $link);
                $this->db->execute();
            }
        } catch(Throwable $e) {
            // Ignore menu sync error
        }
    }

    private function removeMenuLink($reqId){
        try {
            $schoolId = $this->getSchoolId();
            $link = URLROOT . '/home/requirements#req-' . $reqId;
            $this->db->query("DELETE FROM front_menus WHERE school_id = :school_id AND link = :link");
            $this->db->bind(':school_id', $schoolId);
            $this->db->bind(':link', $link);
            $this->db->execute();
        } catch(Throwable $e) {
            // Ignore
        }
    }
}
