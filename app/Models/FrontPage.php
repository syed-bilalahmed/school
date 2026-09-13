<?php
class FrontPage {
    private $db;
    private static $schemaChecked = false;
    private static $defaultsChecked = false;

    public function __construct(){
        $this->db = new Database;
    }

    public function ensureTableSchema(){
        if (self::$schemaChecked) return;
        try {
            $this->db->query("SHOW COLUMNS FROM front_pages LIKE 'file_path'");
            if(!$this->db->single()){
                $this->db->query("ALTER TABLE front_pages ADD COLUMN file_path VARCHAR(255) NULL AFTER is_active");
                $this->db->execute();
            }
            $this->db->query("SHOW COLUMNS FROM front_pages LIKE 'file_name'");
            if(!$this->db->single()){
                $this->db->query("ALTER TABLE front_pages ADD COLUMN file_name VARCHAR(255) NULL AFTER file_path");
                $this->db->execute();
            }
            $this->db->query("SHOW COLUMNS FROM front_pages LIKE 'meta_description'");
            if(!$this->db->single()){
                $this->db->query("ALTER TABLE front_pages ADD COLUMN meta_description VARCHAR(255) NULL AFTER file_name");
                $this->db->execute();
            }
            self::$schemaChecked = true;
        } catch(Throwable $e){
            self::$schemaChecked = true;
        }
    }

    public function ensureCoreDefaultPages(){
        if (self::$defaultsChecked) return;
        self::$defaultsChecked = true;
        $defaults = [
            [
                'title' => 'Academic Pathways & Curriculum',
                'slug' => 'academics',
                'meta_description' => 'Nurturing intellectual curiosity, character development, and academic mastery across all educational phases.',
                'content' => '<p class="lead">At our institution, education is carefully calibrated across key development phases to cultivate rigorous inquiry, creative innovation, and ethical leadership.</p>
<div class="row g-4 my-4">
    <div class="col-md-6">
        <div class="card h-100 border shadow-sm p-4 rounded-3 bg-light">
            <span class="badge bg-primary mb-2" style="width: fit-content;">Pre-K &ndash; Grade 2</span>
            <h4 class="fw-bold text-dark">Early Discovery Phase</h4>
            <p class="text-muted">Play-based sensory learning, phonics immersion, and social collaboration cultivating curiosity and confidence in young minds.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border shadow-sm p-4 rounded-3 bg-light">
            <span class="badge bg-success mb-2" style="width: fit-content;">Grades 3 &ndash; 5</span>
            <h4 class="fw-bold text-dark">Elementary Honors</h4>
            <p class="text-muted">Structured conceptual mastery in arithmetic, linguistic expression, introductory sciences, and interactive social studies.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border shadow-sm p-4 rounded-3 bg-light">
            <span class="badge bg-warning text-dark mb-2" style="width: fit-content;">Grades 6 &ndash; 8</span>
            <h4 class="fw-bold text-dark">Middle School Academy</h4>
            <p class="text-muted">Analytical laboratory experiments, algebra foundations, debate societies, and inter-scholastic athletic opportunities.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border shadow-sm p-4 rounded-3 bg-light">
            <span class="badge bg-danger mb-2" style="width: fit-content;">Grades 9 &ndash; 12</span>
            <h4 class="fw-bold text-dark">Senior High &amp; AP/IB</h4>
            <p class="text-muted">Rigorous Advanced Placement courses, university guidance counseling, student governance, and capstone research programs.</p>
        </div>
    </div>
</div>
<h3>Curriculum Standards &amp; Accreditation</h3>
<p>Our academic syllabi strictly conform to premier national standards and modern pedagogical methodologies, fostering critical thinking and cross-disciplinary proficiency.</p>'
            ],
            [
                'title' => 'Campus Life & Facilities',
                'slug' => 'facilities',
                'meta_description' => 'World-class infrastructure, high-tech research laboratories, modern digital libraries, and athletics complexes.',
                'content' => '<p class="lead">Our sprawling modern campus provides students with state-of-the-art facilities engineered to stimulate intellect, artistic expression, and athletic teamwork.</p>
<div class="row g-4 my-4">
    <div class="col-md-6">
        <div class="card h-100 border shadow-sm p-4 rounded-3 bg-light">
            <h4 class="fw-bold text-dark mb-2">Modern Digital Library &amp; Commons</h4>
            <p class="text-muted">Over 40,000 physical volumes, access to international research databases, and private collaborative study suites.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border shadow-sm p-4 rounded-3 bg-light">
            <h4 class="fw-bold text-dark mb-2">STEAM &amp; Robotics Research Suite</h4>
            <p class="text-muted">High-speed workstations, 3D additive printing, microcontroller electronics kits, and experimental physics apparatus.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border shadow-sm p-4 rounded-3 bg-light">
            <h4 class="fw-bold text-dark mb-2">Athletics &amp; Aquatic Complex</h4>
            <p class="text-muted">Olympic-standard 25m indoor swimming pool, FIFA-grade synthetic soccer pitch, and multi-court basketball arena.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border shadow-sm p-4 rounded-3 bg-light">
            <h4 class="fw-bold text-dark mb-2">Performing Arts Center &amp; Theater</h4>
            <p class="text-muted">Acoustically tuned 650-seat theater hosting international guest speakers, symphonic recitals, and dramatic productions.</p>
        </div>
    </div>
</div>'
            ],
            [
                'title' => 'Upcoming Events & Activities',
                'slug' => 'events',
                'meta_description' => 'Stay informed about academic schedules, sports competitions, symposia, and cultural galas.',
                'content' => '<p class="lead">Welcome to our institutional events and announcements chronicle. Review upcoming academic milestones, parent-teacher conferences, sports tournaments, and student exhibitions below.</p>'
            ],
            [
                'title' => 'Campus Photo Gallery',
                'slug' => 'gallery',
                'meta_description' => 'Visual moments of academic achievement, athletic victories, cultural celebrations, and campus life.',
                'content' => '<p class="lead">Explore our visual archive capturing the vibrant scholastic, cultural, and sports life of our academy. From groundbreaking science lab discoveries to symphonic performances, celebrate our student community.</p>'
            ],
            [
                'title' => 'About Our School & Leadership',
                'slug' => 'about',
                'meta_description' => 'Discover our institutional heritage, core mission, pedagogical vision, and dedicated administrative leadership.',
                'content' => '<p class="lead">Established with a vision for scholastic excellence and moral character development, our academy provides an inspiring environment where young minds flourish into compassionate global leaders.</p>
<div class="row g-4 my-4">
    <div class="col-md-6">
        <div class="card h-100 border shadow-sm p-4 rounded-3 bg-white">
            <h4 class="fw-bold text-primary mb-2"><i class="fa fa-bullseye me-2"></i>Our Core Mission</h4>
            <p class="text-muted">To nurture holistic character, cultivate critical analytical inquiry, and prepare visionary leaders equipped for 21st-century global challenges.</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 border shadow-sm p-4 rounded-3 bg-white">
            <h4 class="fw-bold text-success mb-2"><i class="fa fa-eye me-2"></i>Our Educational Vision</h4>
            <p class="text-muted">An inclusive, technology-empowered learning sanctuary distinguished by academic rigour, research innovation, and high moral standards.</p>
        </div>
    </div>
</div>
<h3>Message from the Principal</h3>
<p>Welcome to our vibrant academic community. We believe every student possesses unique potential waiting to be ignited through compassionate mentorship, disciplined curiosity, and experiential learning.</p>'
            ],
            [
                'title' => 'Tuition & Fee Structure (2026-2027)',
                'slug' => 'fees',
                'meta_description' => 'Transparent, structured tuition fees, sibling concessions, scholarship programs, and official payment guidelines at our academy.',
                'content' => '<p class="lead">We believe in providing world-class, holistic education with complete financial transparency. Our fee schedule is structured with predictable, all-inclusive academic facilities with zero hidden charges.</p>
<div class="row g-4 my-4">
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border shadow-sm p-3 rounded-3 bg-white text-center">
            <span class="badge bg-primary mb-2 mx-auto">Pre-School Wing</span>
            <h5 class="fw-bold text-dark">Playgroup &ndash; Prep</h5>
            <div class="fs-4 fw-extrabold text-primary my-2">Rs. 6,500 <small class="text-muted fs-6 fw-normal">/mo</small></div>
            <p class="text-muted small">Activity-based learning, phonics kits, sensory play, and psychomotor development tools included.</p>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border shadow-sm p-3 rounded-3 bg-white text-center">
            <span class="badge bg-success mb-2 mx-auto">Primary Wing</span>
            <h5 class="fw-bold text-dark">Grades 1 &ndash; 5</h5>
            <div class="fs-4 fw-extrabold text-success my-2">Rs. 8,500 <small class="text-muted fs-6 fw-normal">/mo</small></div>
            <p class="text-muted small">Language arts, mathematics lab, foundation science, arts, and physical education access.</p>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border shadow-sm p-3 rounded-3 bg-white text-center">
            <span class="badge bg-warning text-dark mb-2 mx-auto">Middle Wing</span>
            <h5 class="fw-bold text-dark">Grades 6 &ndash; 8</h5>
            <div class="fs-4 fw-extrabold text-dark my-2">Rs. 10,500 <small class="text-muted fs-6 fw-normal">/mo</small></div>
            <p class="text-muted small">Advanced computer coding, science laboratories, inter-scholastic sports, and foreign languages.</p>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border shadow-sm p-3 rounded-3 bg-white text-center">
            <span class="badge bg-danger mb-2 mx-auto">Senior Wing</span>
            <h5 class="fw-bold text-dark">Grades 9 &ndash; 10 (Matric / O-Levels)</h5>
            <div class="fs-4 fw-extrabold text-danger my-2">Rs. 13,500 <small class="text-muted fs-6 fw-normal">/mo</small></div>
            <p class="text-muted small">Comprehensive board preparation, physics & chemistry practical labs, career mentorship, and mock exam series.</p>
        </div>
    </div>
</div>
<h3>Concessions &amp; Financial Support</h3>
<p>We are dedicated to ensuring financial considerations never hinder a talented student\'s future. We offer a <strong>10% Sibling Concession</strong> for the 2nd child and <strong>20% Sibling Concession</strong> for the 3rd child onwards, in addition to merit scholarships for board position holders.</p>'
            ]
        ];

        try {
            $schoolId = class_exists('TenantContext') ? TenantContext::getSchoolId() : null;
            foreach($defaults as $d){
                $this->db->query("SELECT id FROM front_pages WHERE slug = :slug LIMIT 1");
                $this->db->bind(':slug', $d['slug']);
                if(!$this->db->single()){
                    $this->db->query("INSERT INTO front_pages (school_id, title, slug, content, is_active, meta_description) 
                                      VALUES (:school_id, :title, :slug, :content, 'yes', :meta_desc)");
                    $this->db->bind(':school_id', $schoolId);
                    $this->db->bind(':title', $d['title']);
                    $this->db->bind(':slug', $d['slug']);
                    $this->db->bind(':content', $d['content']);
                    $this->db->bind(':meta_desc', $d['meta_description']);
                    $this->db->execute();
                }
            }
        } catch(Throwable $e){
            // Graceful fallback
        }
    }

    public function getPages(){
        $this->ensureTableSchema();
        $this->ensureCoreDefaultPages();
        $schoolId = class_exists('TenantContext') ? TenantContext::getSchoolId() : null;
        if ($schoolId) {
            $this->db->query("SELECT * FROM front_pages WHERE school_id = :school_id OR school_id IS NULL ORDER BY created_at DESC");
            $this->db->bind(':school_id', $schoolId);
        } else {
            $this->db->query("SELECT * FROM front_pages ORDER BY created_at DESC");
        }
        return $this->db->resultSet();
    }

    public function getPagesWithMenuStatus(){
        $this->ensureTableSchema();
        $this->ensureCoreDefaultPages();
        $schoolId = class_exists('TenantContext') ? TenantContext::getSchoolId() : null;
        if ($schoolId) {
            $this->db->query("SELECT p.*, m.id as menu_id, m.title as menu_title, m.sort_order as menu_order, m.link as menu_link, COALESCE(m.dropdown_group, 'none') as dropdown_group 
                              FROM front_pages p 
                              LEFT JOIN front_menus m ON m.page_id = p.id AND (m.school_id = :school_id OR m.school_id IS NULL) 
                              WHERE p.school_id = :school_id OR p.school_id IS NULL 
                              ORDER BY p.created_at DESC");
            $this->db->bind(':school_id', $schoolId);
        } else {
            $this->db->query("SELECT p.*, m.id as menu_id, m.title as menu_title, m.sort_order as menu_order, m.link as menu_link, COALESCE(m.dropdown_group, 'none') as dropdown_group 
                              FROM front_pages p 
                              LEFT JOIN front_menus m ON m.page_id = p.id 
                              ORDER BY p.created_at DESC");
        }
        return $this->db->resultSet();
    }

    public function getPageById($id){
        $this->ensureTableSchema();
        $this->ensureCoreDefaultPages();
        $schoolId = class_exists('TenantContext') ? TenantContext::getSchoolId() : null;
        if ($schoolId) {
            $this->db->query("SELECT p.*, m.id as menu_id, m.title as menu_title, m.sort_order as menu_order, COALESCE(m.dropdown_group, 'none') as dropdown_group 
                              FROM front_pages p 
                              LEFT JOIN front_menus m ON m.page_id = p.id 
                              WHERE p.id = :id AND (p.school_id = :school_id OR p.school_id IS NULL)");
            $this->db->bind(':id', $id);
            $this->db->bind(':school_id', $schoolId);
        } else {
            $this->db->query("SELECT p.*, m.id as menu_id, m.title as menu_title, m.sort_order as menu_order, COALESCE(m.dropdown_group, 'none') as dropdown_group 
                              FROM front_pages p 
                              LEFT JOIN front_menus m ON m.page_id = p.id 
                              WHERE p.id = :id");
            $this->db->bind(':id', $id);
        }
        return $this->db->single();
    }

    public function getPageBySlug($slug){
        $this->ensureTableSchema();
        $this->ensureCoreDefaultPages();
        $schoolId = class_exists('TenantContext') ? TenantContext::getSchoolId() : null;
        if ($schoolId) {
            $this->db->query("SELECT * FROM front_pages WHERE slug = :slug AND (school_id = :school_id OR school_id IS NULL) ORDER BY id DESC LIMIT 1");
            $this->db->bind(':slug', $slug);
            $this->db->bind(':school_id', $schoolId);
        } else {
            $this->db->query("SELECT * FROM front_pages WHERE slug = :slug ORDER BY id DESC LIMIT 1");
            $this->db->bind(':slug', $slug);
        }
        return $this->db->single();
    }

    public function addPage($data){
        $this->ensureTableSchema();
        $this->db->query("INSERT INTO front_pages (school_id, title, slug, content, is_active, file_path, file_name, meta_description) 
                          VALUES (:school_id, :title, :slug, :content, :active, :file_path, :file_name, :meta_desc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':active', $data['is_active'] ?? 'yes');
        $this->db->bind(':file_path', $data['file_path'] ?? null);
        $this->db->bind(':file_name', $data['file_name'] ?? null);
        $this->db->bind(':meta_desc', $data['meta_description'] ?? null);
        if($this->db->execute()){
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function updatePage($data){
        $this->ensureTableSchema();
        $updateFileSql = "";
        if(isset($data['file_path'])){
            $updateFileSql = ", file_path = :file_path, file_name = :file_name";
        }
        $this->db->query("UPDATE front_pages SET title = :title, slug = :slug, content = :content, is_active = :active, meta_description = :meta_desc $updateFileSql 
                          WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':active', $data['is_active'] ?? 'yes');
        $this->db->bind(':meta_desc', $data['meta_description'] ?? null);
        if(isset($data['file_path'])){
            $this->db->bind(':file_path', $data['file_path']);
            $this->db->bind(':file_name', $data['file_name']);
        }
        return $this->db->execute();
    }

    public function deletePage($id){
        // Automatically cleanup any navigation menu item linked to this page
        try {
            $this->db->query("DELETE FROM front_menus WHERE page_id = :page_id AND school_id = :school_id");
            $this->db->bind(':school_id', TenantContext::getSchoolId());
            $this->db->bind(':page_id', $id);
            $this->db->execute();
        } catch(Throwable $e){}

        $this->db->query("DELETE FROM front_pages WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
