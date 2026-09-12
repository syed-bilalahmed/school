<?php
class HomeController extends Controller {
    private function renderMaintenancePage($cmsSettings, $siteSettings){
        http_response_code(503);

        $this->view('home/maintenance', [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings
        ]);
        exit;
    }

    private function ensurePublicWebsiteEnabled($cmsSettings, $siteSettings){
        if(!$cmsSettings || (($cmsSettings->is_active_website ?? 'yes') === 'no')){
            $this->renderMaintenancePage($cmsSettings, $siteSettings);
        }
    }

    public function index(){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $bannerModel = $this->model('FrontBanner');
        $newsModel = $this->model('FrontNews');
        $eventModel = $this->model('FrontEvent');
        $galleryModel = $this->model('FrontGallery');
        
        $noticeModel = $this->model('Notice');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();

        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        // Fetch recent active notices for public popups / ticker with attachments
        $publicNotices = [];
        try {
            $publicNotices = Notice::getPublicNoticesWithAttachments();
        } catch(Throwable $e) {
            $publicNotices = [];
        }

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus(),
            'banners' => $bannerModel->getBanners(),
            'news' => $newsModel->getNews(),
            'events' => $eventModel->getEvents(),
            'gallery' => $galleryModel->getGallery(),
            'notices' => $publicNotices
        ];
        
        $this->view('home/index', $data);
    }

    public function page($slug = ''){
        $slug = strtolower(trim($slug));
        if ($slug === 'campus-life') $slug = 'facilities';
        if ($slug === 'events-news') $slug = 'events';
        if ($slug === 'fee-structure' || $slug === 'fees') {
            header('Location: ' . URLROOT . '/home/fees');
            exit;
        }

        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $pageModel = $this->model('FrontPage');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();
        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        $page = $pageModel->getPageBySlug($slug);

        if(!$page && is_numeric($slug)){
             $page = $pageModel->getPageById($slug);
        }

        if(!$page || ($page->is_active ?? 'yes') == 'no'){
            header('Location: ' . URLROOT);
            exit;
        }

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus(),
            'page' => $page
        ];
        
        $this->view('home/page', $data);
    }

    public function gallery(){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $galleryModel = $this->model('FrontGallery');
        $pageModel = $this->model('FrontPage');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();
        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        $page = $pageModel->getPageBySlug('gallery');

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus(),
            'gallery' => $galleryModel->getGallery(),
            'page' => $page
        ];
        
        $this->view('home/gallery', $data);
    }

    public function events(){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $eventModel = $this->model('FrontEvent');
        $pageModel = $this->model('FrontPage');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();
        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        $page = $pageModel->getPageBySlug('events') ?: $pageModel->getPageBySlug('events-news');

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus(),
            'events' => $eventModel->getEvents(),
            'page' => $page
        ];
        
        $this->view('home/events', $data);
    }

    public function news(){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $newsModel = $this->model('FrontNews');
        $pageModel = $this->model('FrontPage');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();
        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        $page = $pageModel->getPageBySlug('news');

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus(),
            'news' => $newsModel->getNews(),
            'page' => $page
        ];
        
        $this->view('home/news', $data);
    }

    public function academics(){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $pageModel = $this->model('FrontPage');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();
        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        $page = $pageModel->getPageBySlug('academics');

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus(),
            'page' => $page
        ];
        
        $this->view('home/academics', $data);
    }

    public function facilities(){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $pageModel = $this->model('FrontPage');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();
        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        $page = $pageModel->getPageBySlug('facilities') ?: $pageModel->getPageBySlug('campus-life');

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus(),
            'page' => $page
        ];
        
        $this->view('home/facilities', $data);
    }

    public function fees(){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $pageModel = $this->model('FrontPage');
        $classModel = $this->model('SchoolClass');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();
        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        if(($cmsSettings->enable_fee_structure ?? 'yes') !== 'yes'){
            header('Location: ' . URLROOT);
            exit;
        }

        $page = $pageModel->getPageBySlug('fees');

        // Fetch classes to populate the fee calculator
        $classes = [];
        try {
            $classes = $classModel->getClasses();
        } catch(Throwable $e) {
            $classes = [];
        }

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus(),
            'page' => $page,
            'classes' => $classes
        ];
        
        $this->view('home/fees', $data);
    }

    public function fee_structure(){
        header('Location: ' . URLROOT . '/home/fees');
        exit;
    }

    public function admission(){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $officeModel = $this->model('FrontOffice');
        $notifyModel = $this->model('Notification');
        $classModel = $this->model('SchoolClass');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();

        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        if(!$cmsSettings || (($cmsSettings->enable_online_admission ?? 'yes') !== 'yes')){
            header('Location: ' . URLROOT);
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW) ?? [];
            
            $prevSchool = trim($_POST['previous_school'] ?? '');
            $lastClass = trim($_POST['last_class'] ?? '');
            $lastGrade = trim($_POST['last_grade'] ?? '');
            $transferReason = trim($_POST['transfer_reason'] ?? '');
            $bformCnic = trim($_POST['bform_cnic'] ?? '');
            $fatherCnic = trim($_POST['father_cnic'] ?? '');
            $emergencyContact = trim($_POST['emergency_contact'] ?? '');
            $remarks = trim($_POST['description'] ?? '');

            // Compile rich description summary of academic history & background
            $descParts = [];
            if (!empty($bformCnic)) $descParts[] = "Student B-Form: " . $bformCnic;
            if (!empty($fatherCnic)) $descParts[] = "Father CNIC: " . $fatherCnic;
            if (!empty($lastClass)) $descParts[] = "Previous Class Passed: " . $lastClass;
            if (!empty($lastGrade)) $descParts[] = "Grade/Marks: " . $lastGrade;
            if (!empty($transferReason)) $descParts[] = "Reason for Leaving: " . $transferReason;
            if (!empty($emergencyContact)) $descParts[] = "Emergency Contact: " . $emergencyContact;
            if (!empty($remarks)) $descParts[] = "Additional Remarks: " . $remarks;
            
            $compiledDescription = implode(" | ", $descParts);

            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'description' => $compiledDescription,
                'date' => date('Y-m-d'),
                'next_follow_up_date' => date('Y-m-d', strtotime('+3 days')),
                'assigned_to' => null,
                'reference' => 'Online Website',
                'source' => 'online',
                'class_id' => trim($_POST['class_id'] ?? ''),
                'no_of_child' => 1,
                
                'father_name' => trim($_POST['father_name'] ?? ''),
                'mother_name' => trim($_POST['mother_name'] ?? ''),
                'dob' => trim($_POST['dob'] ?? ''),
                'gender' => trim($_POST['gender'] ?? ''),
                'guardian_name' => trim($_POST['guardian_name'] ?? ''),
                'guardian_relation' => trim($_POST['guardian_relation'] ?? ''),
                'previous_school' => $prevSchool
            ];
            
            $newId = $officeModel->addEnquiry($data);
            if($newId){
                $notifData = [
                    'title' => 'New Online Admission Enquiry',
                    'message' => 'New application received from ' . $data['name'],
                    'link' => URLROOT . '/frontoffice/onlineAdmissions',
                    'role' => 'admin'
                ];
                $notifyModel->addNotification($notifData);
                
                $data['success_message'] = "Thank you! Your official online admission dossier has been submitted. Please review the mandatory document submission requirements below.";
                $data['submitted_application'] = [
                    'id' => is_numeric($newId) ? (int)$newId : 0,
                    'name' => $data['name'],
                    'father_name' => $data['father_name'],
                    'phone' => $data['phone'],
                    'class_id' => $data['class_id'],
                    'date' => $data['date']
                ];
            } else {
                 $data['error_message'] = "Something went wrong while saving your application. Please check your entries and try again.";
            }
        }
        
        $data['cms'] = $cmsSettings;
        $data['school'] = $siteSettings;
        $data['settings'] = $cmsSettings;
        $data['menus'] = $menuModel->getMenus();
        $data['classes'] = $classModel->getClasses();

        $this->view('home/admission', $data);
    }

    public function contact(){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();
        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $subject = trim($_POST['subject'] ?? 'General Inquiry');
            $message = trim($_POST['message'] ?? '');

            if(empty($name) || empty($phone) || empty($message)){
                $_SESSION['flash_error'] = 'Please provide your full name, phone number, and message.';
                header('Location: ' . URLROOT . '/home/contact');
                exit;
            }

            try {
                $officeModel = $this->model('FrontOffice');
                $enquiryData = [
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => 'Website Contact Page Submission',
                    'description' => "[Subject: " . $subject . "] " . $message,
                    'date' => date('Y-m-d'),
                    'next_follow_up_date' => date('Y-m-d', strtotime('+2 days')),
                    'assigned_to' => null,
                    'reference' => 'Website Contact Form',
                    'source' => 'Website Contact Us Page',
                    'class_id' => '',
                    'no_of_child' => 1,
                    'father_name' => '',
                    'mother_name' => '',
                    'dob' => '',
                    'gender' => '',
                    'guardian_name' => '',
                    'guardian_relation' => '',
                    'previous_school' => ''
                ];
                $officeModel->addEnquiry($enquiryData);

                try {
                    $notifyModel = $this->model('Notification');
                    $notifyModel->addNotification([
                        'title' => 'New Contact Form Enquiry',
                        'message' => "Enquiry from {$name} ({$phone}) - {$subject}",
                        'link' => URLROOT . '/frontoffice/enquiry',
                        'role' => 'admin'
                    ]);
                } catch(Throwable $ne) {}

                $_SESSION['flash_success'] = "Thank you, {$name}! Your message has been received. Our campus administration desk will contact you shortly.";
            } catch(Throwable $e) {
                $_SESSION['flash_error'] = "Could not submit your enquiry: " . $e->getMessage();
            }

            header('Location: ' . URLROOT . '/home/contact');
            exit;
        }

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus()
        ];

        $this->view('home/contact', $data);
    }

    public function alumni($batch = null){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $alumniModel = $this->model('FrontAlumni');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();
        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        if(($cmsSettings->enable_alumni ?? 'yes') !== 'yes'){
            header('Location: ' . URLROOT);
            exit;
        }

        // Handle Alumni Self-Registration submission
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $name = trim($_POST['name'] ?? '');
            $batchYear = trim($_POST['batch_year'] ?? '');
            if(!empty($name) && !empty($batchYear)){
                $regData = [
                    'name' => $name,
                    'batch_year' => $batchYear,
                    'graduation_class' => trim($_POST['graduation_class'] ?? ''),
                    'current_position' => trim($_POST['current_position'] ?? ''),
                    'company_organization' => trim($_POST['company_organization'] ?? ''),
                    'location' => trim($_POST['location'] ?? ''),
                    'testimonial' => trim($_POST['testimonial'] ?? ''),
                    'linkedin_url' => trim($_POST['linkedin_url'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'is_featured' => 0,
                    'is_active' => 'yes',
                    'sort_order' => 50,
                    'image' => ''
                ];

                if(isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK){
                    require_once APPROOT . '/Core/UploadHandler.php';
                    $upload = UploadHandler::processUpload($_FILES['image'], 'alumni');
                    if($upload['success']){
                        $regData['image'] = $upload['path'];
                    }
                }

                $alumniModel->addAlumni($regData);

                try {
                    $notifyModel = $this->model('Notification');
                    $notifyModel->addNotification([
                        'title' => 'New Alumni Network Registration',
                        'message' => "Alumni profile submitted by {$name} (Class of {$batchYear})",
                        'link' => URLROOT . '/frontcms/alumni',
                        'role' => 'admin'
                    ]);
                } catch(Throwable $ne) {}

                $_SESSION['flash_success'] = "Welcome to the Alumni Network, {$name}! Your alumni profile has been registered.";
            } else {
                $_SESSION['flash_error'] = "Please provide your full name and graduating batch year.";
            }
            header('Location: ' . URLROOT . '/home/alumni');
            exit;
        }

        $selectedBatch = isset($_GET['batch']) ? trim($_GET['batch']) : ($batch ? trim($batch) : null);
        $alumni = $alumniModel->getAlumni(true, $selectedBatch);
        $featured = $alumniModel->getFeaturedAlumni();
        $batches = $alumniModel->getBatches();

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus(),
            'alumni' => $alumni,
            'featured' => $featured,
            'batches' => $batches,
            'selectedBatch' => $selectedBatch
        ];

        $this->view('home/alumni', $data);
    }

    public function requirements($category = null){
        $cmsModel = $this->model('FrontCms');
        $settingModel = $this->model('Setting');
        $menuModel = $this->model('FrontMenu');
        $reqModel = $this->model('FrontRequirement');
        
        $cmsSettings = $cmsModel->getSettings();
        $siteSettings = $settingModel->getSettings();
        $this->ensurePublicWebsiteEnabled($cmsSettings, $siteSettings);

        if(($cmsSettings->enable_requirements ?? 'yes') !== 'yes'){
            header('Location: ' . URLROOT);
            exit;
        }

        $selectedCat = isset($_GET['category']) ? trim($_GET['category']) : ($category ? trim($category) : null);
        $requirements = $reqModel->getRequirements(true, $selectedCat);
        $categories = $reqModel->getCategories();

        $data = [
            'cms' => $cmsSettings,
            'school' => $siteSettings,
            'settings' => $cmsSettings,
            'menus' => $menuModel->getMenus(),
            'requirements' => $requirements,
            'categories' => $categories,
            'selectedCat' => $selectedCat
        ];

        $this->view('home/requirements', $data);
    }

    /**
     * Runtime Public Live Chat & WhatsApp API Endpoint
     * Handles: config retrieval, bot natural language query answering, and direct visitor inquiry persistence
     */
    public function livechatApi(){
        header('Content-Type: application/json; charset=utf-8');

        $rawInput = file_get_contents('php://input');
        $jsonBody = json_decode($rawInput, true) ?: [];
        $action = trim($_POST['action'] ?? ($jsonBody['action'] ?? 'config'));

        $s = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];
        $schoolName = !empty($s['school_name']) ? $s['school_name'] : (defined('SITENAME') ? SITENAME : 'Our School');
        $schoolPhone = !empty($s['school_phone']) ? $s['school_phone'] : '+92-51-111-222-333';
        $schoolEmail = !empty($s['school_email']) ? $s['school_email'] : 'info@citymodelschool.edu.pk';
        $schoolAddress = !empty($s['school_address']) ? $s['school_address'] : 'Main Campus, Sector H-8, Islamabad';

        // 1. Return Active Configuration & State
        if ($action === 'config') {
            echo json_encode([
                'success' => true,
                'school_name' => $schoolName,
                'school_phone' => $schoolPhone,
                'school_email' => $schoolEmail,
                'school_address' => $schoolAddress,
                'whatsapp' => [
                    'enabled' => ($s['whatsapp_enabled'] ?? '1') !== '0',
                    'number' => $s['whatsapp_number'] ?? '+92-300-1234567',
                    'default_msg' => $s['whatsapp_default_msg'] ?? 'Hello! I would like to inquire about admissions and school programs.',
                    'agent_name' => $s['whatsapp_agent_name'] ?? 'Admissions & Helpdesk',
                    'popup_enabled' => ($s['whatsapp_popup_enabled'] ?? '1') !== '0'
                ],
                'livechat' => [
                    'enabled' => ($s['livechat_enabled'] ?? '1') !== '0',
                    'provider' => $s['livechat_provider'] ?? 'builtin',
                    'welcome_title' => $s['livechat_welcome_title'] ?? 'Live School Support',
                    'welcome_msg' => $s['livechat_welcome_msg'] ?? 'Hello! Welcome to our school helpdesk. How can we assist you today?',
                    'tawk_property_id' => $s['livechat_tawk_property_id'] ?? '',
                    'tawk_widget_id' => $s['livechat_tawk_widget_id'] ?? '',
                    'crisp_website_id' => $s['livechat_crisp_website_id'] ?? '',
                    'has_custom_script' => !empty($s['livechat_custom_script'])
                ]
            ]);
            exit;
        }

        // 2. Intelligent Smart Assistant Natural Language Q&A
        if ($action === 'query') {
            $msg = strtolower(trim($_POST['message'] ?? ($jsonBody['message'] ?? '')));
            $reply = '';
            $quickReplies = [];

            // Urgent Escalation & Human Agent Assistance Handler
            $isUrgent = preg_match('/(urgent|emergency|jaldi|zaroori|fauri|immediate|immediately|right now|asap|human|person|counselor|agent|team|member|baat|call me|rabta|rabita|kisi se|help desk|live agent|talk to someone)/i', $msg);

            if ($isUrgent) {
                $cleanPhone = preg_replace('/[^0-9]/', '', $schoolPhone);
                $wpEscalateUrl = "https://api.whatsapp.com/send?phone=" . urlencode($cleanPhone) . "&text=" . rawurlencode("Hello, I need urgent assistance regarding school inquiries. Please connect me with a counselor.");

                $reply = "<div class='urgent-bot-card p-3 rounded-3 mb-2' style='background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-left: 4px solid #ef4444;'>
                    <div class='d-flex align-items-center gap-2 mb-2'>
                        <span class='badge bg-danger text-white px-2 py-1'><i class='fa fa-bolt me-1'></i>URGENT ASSISTANCE</span>
                    </div>
                    <h6 class='fw-bold text-danger mb-1'>🚨 ابھی ہماری کونسلنگ اور سپورٹ ٹیم کا نمائندہ آپ سے رابطہ کرتا ہے!</h6>
                    <p class='small text-dark mb-2' style='line-height: 1.5;'>
                        Our dedicated counselor &amp; admissions desk member has been alerted. You can connect right away using any of these immediate options:
                    </p>
                    <div class='d-flex flex-wrap gap-2 mt-2'>
                        <a href='tel:" . htmlspecialchars($schoolPhone, ENT_QUOTES, 'UTF-8') . "' class='btn btn-sm btn-danger fw-bold rounded-pill px-3 shadow-sm'>
                            <i class='fa fa-phone me-1'></i> Call Helpline: " . htmlspecialchars($schoolPhone, ENT_QUOTES, 'UTF-8') . "
                        </a>
                        <a href='" . htmlspecialchars($wpEscalateUrl, ENT_QUOTES, 'UTF-8') . "' target='_blank' class='btn btn-sm btn-success fw-bold rounded-pill px-3 shadow-sm'>
                            <i class='fab fa-whatsapp me-1'></i> WhatsApp Desk
                        </a>
                    </div>
                </div>
                <div class='chat-callback-box p-2 bg-white rounded-3 border mt-2'>
                    <div class='small fw-bold text-dark mb-1'><i class='fa fa-headset text-primary me-1'></i>یا اپنا نمبر درج کریں، ہم فوراً کال بیک کریں گے:</div>
                    <div class='input-group input-group-sm mb-1'>
                        <input type='text' id='inlineCallbackName' class='form-control form-control-sm' placeholder='Your Name' style='font-size: 0.78rem;'>
                        <input type='tel' id='inlineCallbackPhone' class='form-control form-control-sm' placeholder='Phone / Mobile No.' style='font-size: 0.78rem;'>
                        <button type='button' class='btn btn-sm btn-primary fw-bold' onclick='submitInlineCallback(this)'>Submit</button>
                    </div>
                    <div id='inlineCallbackMsg' class='small text-success' style='display:none;'></div>
                </div>";

                $quickReplies = ['Call Helpline 📞', 'WhatsApp Counselor 💬', 'Admission Details 🎓', 'Fee Structure 💳'];
            } elseif (preg_match('/(admission|apply|register|dakhla|admit|form|online admission)/i', $msg)) {
                $reply = "🎓 <strong>Admissions are currently OPEN!</strong><br>You can easily submit an online application through our portal or visit our Admissions Office.<br><br>👉 <a href='" . URLROOT . "/home/admission' class='chat-link-action' target='_blank'><i class='fa fa-edit me-1'></i>Open Online Admission Form</a><br>📞 For admission counseling, call: <strong>" . htmlspecialchars($schoolPhone, ENT_QUOTES, 'UTF-8') . "</strong>";
                $quickReplies = ['Fee Structure 💳', 'School Timings ⏰', 'Required Documents 📋', 'Chat on WhatsApp 💬'];
            } elseif (preg_match('/(fee|fees|dues|challan|payment|cost|charges|bank|kharcha)/i', $msg)) {
                $bankName = $s['bank_name'] ?? 'Meezan Bank';
                $bankAcc = $s['bank_account_no'] ?? '';
                $reply = "💳 <strong>Fee Structure & Payment:</strong><br>Tuition fees are structured class-wise and payable on a monthly basis.<br>Payments can be made via <strong>" . htmlspecialchars($bankName) . "</strong>" . ($bankAcc ? " (A/C: <code>" . htmlspecialchars($bankAcc) . "</code>)" : "") . " or at our campus accounts counter.<br><br>Registered students and parents can access official fee challans directly via the <a href='" . URLROOT . "/auth/login?role=parent' class='chat-link-action'>Parent Portal</a>.";
                $quickReplies = ['Admission Process 🎓', 'Contact Accounts 📞', 'School Timings ⏰'];
            } elseif (preg_match('/(timing|timings|time|hours|open|schedule|chutti|auqat|watan)/i', $msg)) {
                $reply = "⏰ <strong>Campus Timings & Office Hours:</strong><br>• <strong>Classes:</strong> Monday – Friday, 8:00 AM to 2:00 PM<br>• <strong>Administration / Accounts:</strong> 8:00 AM to 4:30 PM<br>• <strong>Admissions Desk:</strong> Monday – Saturday, 8:30 AM to 3:30 PM.<br><em>Friday prayers break: 12:45 PM – 1:45 PM.</em>";
                $quickReplies = ['Admission Form 🎓', 'Campus Location 📍', 'Contact Phone 📞'];
            } elseif (preg_match('/(location|address|kahan|where|pata|campus|map|directions)/i', $msg)) {
                $reply = "📍 <strong>Campus Location:</strong><br>" . htmlspecialchars($schoolAddress, ENT_QUOTES, 'UTF-8') . "<br><br>You are warmly invited to visit during working hours for a campus tour!";
                $quickReplies = ['School Timings ⏰', 'Contact Phone 📞', 'Admissions 🎓'];
            } elseif (preg_match('/(contact|phone|number|mobile|email|call|rabta|helpline)/i', $msg)) {
                $reply = "📞 <strong>Contact & Support Desk:</strong><br>• <strong>Helpline / Phone:</strong> <a href='tel:" . htmlspecialchars($schoolPhone) . "'>" . htmlspecialchars($schoolPhone) . "</a><br>• <strong>Official Email:</strong> <a href='mailto:" . htmlspecialchars($schoolEmail) . "'>" . htmlspecialchars($schoolEmail) . "</a><br>• <strong>WhatsApp:</strong> Instant chat available via our WhatsApp desk button!";
                $quickReplies = ['Open WhatsApp 💬', 'Request a Callback 📞', 'Admission Query 🎓'];
            } elseif (preg_match('/(class|classes|grade|curriculum|syllabus|board|subjects|matric|inter|o.?level|fsc)/i', $msg)) {
                $board = $s['affiliation_board'] ?? 'Federal Board (FBISE)';
                $reply = "📚 <strong>Academic Programs & Curriculum:</strong><br>We provide certified education affiliated with <strong>" . htmlspecialchars($board) . "</strong> across:<br>• Early Childhood / Montessori<br>• Primary & Middle School<br>• High School (Matric & O-Levels)<br>• College Intermediate (Pre-Medical, Pre-Engineering, ICS).<br><br>👉 <a href='" . URLROOT . "/home/academics' class='chat-link-action'><i class='fa fa-book-open me-1'></i>Explore All Academic Programs</a>";
                $quickReplies = ['Admission Form 🎓', 'Fee Structure 💳', 'Campus Life 🏆'];
            } elseif (preg_match('/(document|documents|requirement|bform|cnic|eligibility)/i', $msg)) {
                $reply = "📋 <strong>Mandatory Admission Documents:</strong><br>1. Student B-Form / Birth Certificate Copy<br>2. Father / Guardian CNIC Copy<br>3. 4 Passport-size photographs with blue background<br>4. Previous School Leaving Certificate (SLC) & Character Certificate<br>5. Previous Class Academic Report Card.";
                $quickReplies = ['Apply Online 🎓', 'Fee Details 💳', 'Speak to Counselor 💬'];
            } else {
                $reply = "Hello! 👋 Thank you for contacting <strong>" . htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8') . "</strong>.<br>How may I assist you today? You can select any topic below or type your question:";
                $quickReplies = ['Admission Inquiry 🎓', 'Fee Structure 💳', 'School Timings ⏰', 'Campus Location 📍', '🚨 Urgent Help / رابطہ کونسلر'];
            }

            echo json_encode([
                'success' => true,
                'reply' => $reply,
                'quick_replies' => $quickReplies
            ]);
            exit;
        }

        // 3. Direct Visitor Inquiry Submission to CRM
        if ($action === 'submit_enquiry') {
            $name = trim($_POST['name'] ?? ($jsonBody['name'] ?? ''));
            $phone = trim($_POST['phone'] ?? ($jsonBody['phone'] ?? ''));
            $email = trim($_POST['email'] ?? ($jsonBody['email'] ?? ''));
            $message = trim($_POST['message'] ?? ($jsonBody['message'] ?? ''));
            $classId = trim($_POST['class_id'] ?? ($jsonBody['class_id'] ?? ''));
            $priority = trim($_POST['priority'] ?? ($jsonBody['priority'] ?? 'Normal'));

            if (empty($name) || empty($phone)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please provide at least your full name and contact phone number.'
                ]);
                exit;
            }

            try {
                $officeModel = $this->model('FrontOffice');
                $isEscalated = (strtolower($priority) === 'urgent' || strpos(strtolower($message), 'urgent') !== false);
                $enquiryData = [
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => 'Website Live Chat Inquiry',
                    'description' => ($isEscalated ? '[URGENT BOT ESCALATION] ' : '') . ($message ?: 'Visitor submitted callback inquiry via Website Live Chat Assistant'),
                    'date' => date('Y-m-d'),
                    'next_follow_up_date' => date('Y-m-d', strtotime('+1 days')),
                    'assigned_to' => null,
                    'reference' => $isEscalated ? 'Website Live Chat (URGENT)' : 'Website Live Chat',
                    'source' => 'Live Chat',
                    'class_id' => !empty($classId) ? $classId : '',
                    'no_of_child' => 1,
                    'father_name' => '',
                    'mother_name' => '',
                    'dob' => '',
                    'gender' => '',
                    'guardian_name' => '',
                    'guardian_relation' => '',
                    'previous_school' => ''
                ];
                $newId = $officeModel->addEnquiry($enquiryData);

                try {
                    $notifyModel = $this->model('Notification');
                    $notifyModel->addNotification([
                        'title' => 'New Live Chat Inquiry',
                        'message' => 'Visitor inquiry from ' . $name . ' (' . $phone . ') via Live Chat',
                        'link' => URLROOT . '/frontoffice/enquiry',
                        'role' => 'admin'
                    ]);
                } catch(Throwable $ne) {
                    // Ignore notification errors
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Thank you, ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '! Your inquiry has been submitted. Our admission team will contact you shortly at ' . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . '.'
                ]);
            } catch (Throwable $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Could not save inquiry: ' . $e->getMessage()
                ]);
            }
            exit;
        }

        echo json_encode(['success' => false, 'message' => 'Invalid action parameter.']);
        exit;
    }

    public function livechatConfig(){
        $_POST['action'] = 'config';
        $this->livechatApi();
    }
}
