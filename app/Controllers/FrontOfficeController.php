<?php
class FrontOfficeController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        if(!isset($_SESSION['user_id']) || (!in_array($_SESSION['user_role'], ['admin', 'super_admin', 'receptionist', 'principal', 'teacher']))){
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        // CSRF protection for all state-changing POST operations
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthGuard::verifyCSRF();
        }
    }

    // --- 1. Reception Command Center Dashboard ---
    public function index(){
        $officeModel = $this->model('FrontOffice');
        $noticeModel = $this->model('Notice');
        $newsModel = $this->model('FrontNews');

        $stats = $officeModel->getStats();
        $visitors = $officeModel->getVisitors(date('Y-m-d'));
        $gatePasses = $officeModel->getGatePasses(date('Y-m-d'));
        $enquiries = array_slice($officeModel->getEnquiries(), 0, 5);
        $dispatches = array_slice($officeModel->getPostalRecords(), 0, 5);

        // Fetch active circulars and latest news for front desk operations
        $notices = [];
        try {
            $notices = $noticeModel->getNotices(null, ['status' => 'Published']);
            if (empty($notices)) {
                $notices = $noticeModel->getNotices();
            }
        } catch(Throwable $e) {
            $notices = [];
        }

        $latestNews = [];
        try {
            $latestNews = $newsModel->getNews();
        } catch(Throwable $e) {
            $latestNews = [];
        }

        $data = [
            'stats' => $stats,
            'today_visitors' => $visitors,
            'today_gate_passes' => $gatePasses,
            'recent_enquiries' => $enquiries,
            'recent_dispatches' => $dispatches,
            'notices' => $notices,
            'latest_news' => $latestNews
        ];

        $this->view('front_office/index', $data);
    }

    public function notices(){
        header('Location: ' . URLROOT . '/notice/index');
        exit;
    }

    // --- 2. Visitor Management & Security Book ---
    public function visitor(){
        $officeModel = $this->model('FrontOffice');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'purpose' => trim($_POST['purpose'] ?? 'General Visit'),
                'name' => trim($_POST['name'] ?? ''),
                'contact' => trim($_POST['contact'] ?? ''),
                'cnic_passport' => trim($_POST['cnic_passport'] ?? ''),
                'id_proof' => trim($_POST['id_proof'] ?? 'CNIC'),
                'person_to_meet' => trim($_POST['person_to_meet'] ?? 'Principal Office'),
                'vehicle_no' => trim($_POST['vehicle_no'] ?? ''),
                'department' => trim($_POST['department'] ?? 'Administration'),
                'no_of_person' => !empty($_POST['no_of_person']) ? (int)$_POST['no_of_person'] : 1,
                'date' => !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d'),
                'in_time' => !empty($_POST['in_time']) ? $_POST['in_time'] : date('h:i A'),
                'out_time' => null,
                'note' => trim($_POST['note'] ?? '')
            ];
            $officeModel->addVisitor($data);
            header('Location: ' . URLROOT . '/frontoffice/visitor?success=checked_in');
            exit;
        }

        $filterDate = isset($_GET['date']) ? trim($_GET['date']) : null;
        $settingModel = $this->model('SiteSetting');
        $data = [
            'visitors' => $officeModel->getVisitors($filterDate),
            'filter_date' => $filterDate,
            'next_pass_no' => $officeModel->getNextVisitorPassNo(),
            'settings' => (object)$settingModel->getAllSettings()
        ];
        $this->view('front_office/visitor', $data);
    }

    public function checkoutVisitor($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/frontoffice/visitor');
            exit;
        }
        $officeModel = $this->model('FrontOffice');
        $officeModel->checkoutVisitor($id, date('h:i A'));
        header('Location: ' . URLROOT . '/frontoffice/visitor?success=checked_out');
        exit;
    }

    public function visitorPass($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/frontoffice/visitor');
            exit;
        }
        $officeModel = $this->model('FrontOffice');
        $visitor = $officeModel->getVisitorById($id);
        if(!$visitor){
            die("Visitor record not found.");
        }

        $settingModel = $this->model('SiteSetting');
        $data = [
            'visitor' => $visitor,
            'settings' => (object)$settingModel->getAllSettings()
        ];
        $this->view('front_office/visitor_pass', $data);
    }

    // --- 3. Student Early Departure Gate Pass ---
    public function gatePass(){
        $officeModel = $this->model('FrontOffice');
        $studentModel = $this->model('Student');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'student_id' => (int)$_POST['student_id'],
                'pass_date' => !empty($_POST['pass_date']) ? $_POST['pass_date'] : date('Y-m-d'),
                'leave_time' => !empty($_POST['leave_time']) ? $_POST['leave_time'] : date('h:i A'),
                'reason_type' => trim($_POST['reason_type'] ?? 'Emergency'),
                'reason_details' => trim($_POST['reason_details'] ?? ''),
                'collected_by_name' => trim($_POST['collected_by_name'] ?? ''),
                'collected_by_cnic' => trim($_POST['collected_by_cnic'] ?? ''),
                'collected_by_relation' => trim($_POST['collected_by_relation'] ?? 'Father'),
                'collected_by_phone' => trim($_POST['collected_by_phone'] ?? ''),
                'approved_by_user_id' => !empty($_POST['approved_by_user_id']) ? (int)$_POST['approved_by_user_id'] : ($_SESSION['user_id'] ?? null)
            ];
            $officeModel->addGatePass($data);
            header('Location: ' . URLROOT . '/frontoffice/gatePass?success=issued');
            exit;
        }

        $db = new Database();
        $db->query("SELECT id, name, role FROM users WHERE role IN ('admin', 'teacher', 'principal', 'vice_principal') ORDER BY name ASC");
        $approvers = $db->resultSet();

        $students = $studentModel->getStudents();

        $filterDate = isset($_GET['date']) ? trim($_GET['date']) : null;
        $data = [
            'gate_passes' => $officeModel->getGatePasses($filterDate),
            'students' => $students,
            'approvers' => $approvers,
            'filter_date' => $filterDate,
            'next_pass_no' => $officeModel->getNextGatePassNo()
        ];
        $this->view('front_office/gate_pass', $data);
    }

    public function markGateDeparted($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/frontoffice/gatePass');
            exit;
        }
        $officeModel = $this->model('FrontOffice');
        $officeModel->markGateDeparted($id);
        header('Location: ' . URLROOT . '/frontoffice/gatePass?success=departed');
        exit;
    }

    public function printGatePass($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/frontoffice/gatePass');
            exit;
        }
        $officeModel = $this->model('FrontOffice');
        $pass = $officeModel->getGatePassById($id);
        if(!$pass){
            die("Gate pass record not found.");
        }

        $data = [
            'pass' => $pass
        ];
        $this->view('front_office/gate_pass_slip', $data);
    }

    // --- 4. Admission Enquiry & Leads Pipeline ---
    public function enquiry(){
        $officeModel = $this->model('FrontOffice');
        $classModel = $this->model('SchoolClass');
        
        $db = new Database();
        $db->query("SELECT id, name FROM users WHERE role IN ('admin', 'teacher', 'receptionist')");
        $staffs = $db->resultSet();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'date' => !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d'),
                'next_follow_up_date' => !empty($_POST['next_follow_up_date']) ? $_POST['next_follow_up_date'] : null,
                'assigned_to' => !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null,
                'reference' => trim($_POST['reference'] ?? ''),
                'source' => trim($_POST['source'] ?? 'Direct Walk-in'),
                'class_id' => !empty($_POST['class_id']) ? $_POST['class_id'] : null,
                'no_of_child' => !empty($_POST['no_of_child']) ? (int)$_POST['no_of_child'] : 1,
                'father_name' => trim($_POST['father_name'] ?? ''),
                'mother_name' => trim($_POST['mother_name'] ?? ''),
                'dob' => !empty($_POST['dob']) ? $_POST['dob'] : null,
                'gender' => trim($_POST['gender'] ?? 'Male'),
                'guardian_name' => trim($_POST['guardian_name'] ?? ''),
                'guardian_relation' => trim($_POST['guardian_relation'] ?? ''),
                'previous_school' => trim($_POST['previous_school'] ?? ''),
                'status' => trim($_POST['status'] ?? 'New'),
                'discount_offered' => !empty($_POST['discount_offered']) ? (float)$_POST['discount_offered'] : 0.00
            ];
            $officeModel->addEnquiry($data);
            header('Location: ' . URLROOT . '/frontoffice/enquiry?success=created');
            exit;
        }

        $filterStatus = isset($_GET['status']) ? trim($_GET['status']) : null;
        $data = [
            'enquiries' => $officeModel->getEnquiries($filterStatus),
            'classes' => $classModel->getClasses(),
            'staffs' => $staffs,
            'filter_status' => $filterStatus
        ];
        
        $this->view('front_office/enquiry', $data);
    }

    public function updateEnquiryStatus($id = null){
        if(empty($id) || $_SERVER['REQUEST_METHOD'] != 'POST'){
            header('Location: ' . URLROOT . '/frontoffice/enquiry');
            exit;
        }
        $officeModel = $this->model('FrontOffice');
        $status = trim($_POST['status'] ?? 'Follow Up');
        $officeModel->updateEnquiryStatus($id, $status);
        header('Location: ' . URLROOT . '/frontoffice/enquiry?success=updated');
        exit;
    }

    public function deleteEnquiry($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/frontoffice/enquiry');
            exit;
        }
        $officeModel = $this->model('FrontOffice');
        $officeModel->deleteEnquiry($id);
        header('Location: ' . URLROOT . '/frontoffice/enquiry?success=deleted');
        exit;
    }

    public function printEnquiry($id = null){
        $officeModel = $this->model('FrontOffice');
        $filterStatus = isset($_GET['status']) ? trim($_GET['status']) : null;
        
        $enquiries = [];
        $single = null;
        if(!empty($id) && is_numeric($id)){
            $single = $officeModel->getEnquiryById($id);
            if(!$single){
                die("Admission enquiry record not found.");
            }
            $enquiries = [$single];
        } else {
            $enquiries = $officeModel->getEnquiries($filterStatus);
        }

        $settingModel = class_exists('SiteSetting') ? new SiteSetting() : null;
        $schoolSettings = $settingModel ? $settingModel->getAllSettings() : [];

        $data = [
            'enquiries' => $enquiries,
            'single' => $single,
            'settings' => $schoolSettings,
            'filter_status' => $filterStatus
        ];

        $this->view('front_office/print_enquiry', $data);
    }

    public function onlineAdmission(){
        return $this->onlineAdmissions();
    }

    // --- 4b. Online Admission Applications Desk ---
    public function onlineAdmissions(){
        $officeModel = $this->model('FrontOffice');
        $classModel = $this->model('SchoolClass');
        
        $filterStatus = isset($_GET['status']) ? trim($_GET['status']) : 'pending';
        $filterClass = isset($_GET['class_id']) ? trim($_GET['class_id']) : 'all';
        $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'AZ';
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_online_admission'])){
            $descParts = [];
            if (!empty($_POST['bform_cnic'])) $descParts[] = "Student B-Form: " . trim($_POST['bform_cnic']);
            if (!empty($_POST['father_cnic'])) $descParts[] = "Father CNIC: " . trim($_POST['father_cnic']);
            if (!empty($_POST['previous_school'])) $descParts[] = "Previous School: " . trim($_POST['previous_school']);
            if (!empty($_POST['last_class'])) $descParts[] = "Last Class: " . trim($_POST['last_class']);
            if (!empty($_POST['last_grade'])) $descParts[] = "Grade/Marks: " . trim($_POST['last_grade']);
            if (!empty($_POST['emergency_contact'])) $descParts[] = "Emergency: " . trim($_POST['emergency_contact']);
            if (!empty($_POST['remarks'])) $descParts[] = "Remarks: " . trim($_POST['remarks']);

            $compiledDescription = implode(" | ", $descParts);

            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'description' => $compiledDescription,
                'date' => date('Y-m-d'),
                'next_follow_up_date' => date('Y-m-d', strtotime('+3 days')),
                'assigned_to' => $_SESSION['user_id'] ?? null,
                'reference' => 'Front Office Desk',
                'source' => 'online',
                'class_id' => trim($_POST['class_id'] ?? ''),
                'no_of_child' => 1,
                'father_name' => trim($_POST['father_name'] ?? ''),
                'mother_name' => trim($_POST['mother_name'] ?? ''),
                'dob' => trim($_POST['dob'] ?? ''),
                'gender' => trim($_POST['gender'] ?? 'Male'),
                'guardian_name' => trim($_POST['guardian_name'] ?? ($_POST['father_name'] ?? '')),
                'guardian_relation' => trim($_POST['guardian_relation'] ?? 'Father'),
                'previous_school' => trim($_POST['previous_school'] ?? '')
            ];

            $newId = $officeModel->addEnquiry($data);
            if($newId){
                $_SESSION['flash_success'] = "New admission application has been registered successfully!";
                $_SESSION['admission_submitted_info'] = [
                    'id' => is_numeric($newId) ? (int)$newId : 0,
                    'name' => $data['name'],
                    'father_name' => $data['father_name'],
                    'phone' => $data['phone'],
                    'class_id' => $data['class_id']
                ];
            } else {
                $_SESSION['flash_error'] = "Failed to register admission application.";
            }
            header('Location: ' . URLROOT . '/frontoffice/onlineAdmissions?status=' . urlencode($filterStatus) . '&class_id=' . urlencode($filterClass) . '&sort=' . urlencode($sort) . '&submitted=1');
            exit;
        }

        // Dedicated Action: Approve & Enroll directly into Class
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll_student'])){
            $id = (int)$_POST['id'];
            $result = $officeModel->enrollEnquiryAsStudent($id, $_POST);
            if($result['success']){
                $studentName = htmlspecialchars($result['student_name'] ?? 'Applicant');
                $_SESSION['flash_success'] = "Application approved! Student '<strong>{$studentName}</strong>' has been enrolled into Class '<strong>{$result['class_name']}</strong>' (Admission No: <strong>{$result['admission_no']}</strong>, Roll No: <strong>{$result['roll_no']}</strong>). Transferred from inbox to class roster.";
                $_SESSION['last_enrolled_student'] = [
                    'id' => $result['student_id'],
                    'name' => $result['student_name'] ?? 'Student',
                    'class_name' => $result['class_name'],
                    'class_id' => $result['class_id'],
                    'admission_no' => $result['admission_no'],
                    'roll_no' => $result['roll_no']
                ];
            } else {
                $_SESSION['flash_error'] = "Could not enroll student: " . $result['message'];
            }
            $redirStatus = (strtolower($filterStatus) === 'approved') ? 'Approved' : 'pending';
            header('Location: ' . URLROOT . '/frontoffice/onlineAdmissions?status=' . urlencode($redirStatus) . '&class_id=' . urlencode($filterClass) . '&sort=' . urlencode($sort));
            exit;
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])){
            $id = (int)$_POST['id'];
            $status = trim($_POST['status']);
            if (strtolower($status) === 'approved') {
                $result = $officeModel->enrollEnquiryAsStudent($id, $_POST);
                if ($result['success']) {
                    $studentName = htmlspecialchars($result['student_name'] ?? 'Applicant');
                    $_SESSION['flash_success'] = "Application approved! Student '<strong>{$studentName}</strong>' has been enrolled into Class '<strong>{$result['class_name']}</strong>' (Admission No: <strong>{$result['admission_no']}</strong>, Roll No: <strong>{$result['roll_no']}</strong>). Transferred from inbox to class roster.";
                    $_SESSION['last_enrolled_student'] = [
                        'id' => $result['student_id'],
                        'name' => $result['student_name'] ?? 'Student',
                        'class_name' => $result['class_name'],
                        'class_id' => $result['class_id'],
                        'admission_no' => $result['admission_no'],
                        'roll_no' => $result['roll_no']
                    ];
                } else {
                    $_SESSION['flash_error'] = "Status updated, but could not auto-enroll: " . $result['message'];
                }
                $redirStatus = (strtolower($filterStatus) === 'approved') ? 'Approved' : 'pending';
                header('Location: ' . URLROOT . '/frontoffice/onlineAdmissions?status=' . urlencode($redirStatus) . '&class_id=' . urlencode($filterClass) . '&sort=' . urlencode($sort));
                exit;
            } else {
                $officeModel->updateEnquiryStatus($id, $status);
                $_SESSION['flash_success'] = "Application status updated to '{$status}'.";
                header('Location: ' . URLROOT . '/frontoffice/onlineAdmissions?status=' . urlencode($filterStatus) . '&class_id=' . urlencode($filterClass) . '&sort=' . urlencode($sort));
                exit;
            }
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulk_action'])){
            $action = trim($_POST['bulk_action']);
            $ids = $_POST['admission_ids'] ?? [];
            if(!is_array($ids)) {
                $ids = explode(',', (string)$ids);
            }
            $cleanIds = array_filter(array_map('intval', $ids));

            if(empty($cleanIds)){
                $_SESSION['flash_error'] = "Please select at least one admission application using the checkboxes.";
            } else {
                $count = count($cleanIds);
                if($action === 'approve'){
                    $enrolledCount = 0;
                    $errors = [];
                    foreach($cleanIds as $admId){
                        $res = $officeModel->enrollEnquiryAsStudent($admId);
                        if($res['success']){
                            $enrolledCount++;
                        } else {
                            $errors[] = "#{$admId}: " . $res['message'];
                        }
                    }
                    if($enrolledCount > 0){
                        $_SESSION['flash_success'] = "{$enrolledCount} admission application(s) approved and transferred to their classes successfully! They are now in the class roster.";
                    }
                    if(!empty($errors)){
                        $_SESSION['flash_error'] = implode("<br>", $errors);
                    }
                    $redirStatus = (strtolower($filterStatus) === 'approved') ? 'Approved' : 'pending';
                    header('Location: ' . URLROOT . '/frontoffice/onlineAdmissions?status=' . urlencode($redirStatus) . '&class_id=' . urlencode($filterClass) . '&sort=' . urlencode($sort));
                    exit;
                } elseif($action === 'follow_up'){
                    $officeModel->bulkUpdateEnquiryStatus($cleanIds, 'Follow Up');
                    $_SESSION['flash_success'] = "{$count} admission application(s) marked as Follow Up.";
                } elseif($action === 'reject'){
                    $officeModel->bulkUpdateEnquiryStatus($cleanIds, 'Rejected');
                    $_SESSION['flash_success'] = "{$count} admission application(s) marked as Rejected.";
                } elseif($action === 'delete'){
                    foreach($cleanIds as $delId){
                        $officeModel->deleteEnquiry($delId);
                    }
                    $_SESSION['flash_success'] = "{$count} admission application record(s) deleted successfully.";
                }
            }
            header('Location: ' . URLROOT . '/frontoffice/onlineAdmissions?status=' . urlencode($filterStatus) . '&class_id=' . urlencode($filterClass) . '&sort=' . urlencode($sort));
            exit;
        }

        $counts = $officeModel->getOnlineAdmissionCounts();
        $admissions = $officeModel->getOnlineAdmissions($filterStatus, $filterClass, $sort, $search);
        $classes = $classModel->getClasses();
        $sectionModel = $this->model('Section');
        $sections = $sectionModel->getSections();
        $sessionModel = $this->model('AcademicSession');
        $sessions = $sessionModel->getSessions();
        $currentSession = $sessionModel->getCurrentSession();

        $data = [
            'admissions' => $admissions,
            'counts' => $counts,
            'classes' => $classes,
            'sections' => $sections,
            'sessions' => $sessions,
            'current_session' => $currentSession,
            'filter_status' => $filterStatus,
            'filter_class' => $filterClass,
            'sort' => $sort,
            'search' => $search
        ];

        $this->view('front_office/online_admissions', $data);
    }

    /**
     * Printable Official Admission Enquiry / Application Form (Single or Batch)
     */
    public function printAdmissionForm($id = null){
        $officeModel = $this->model('FrontOffice');
        $enquiries = [];

        // Check if multiple IDs passed via query string ?ids=1,2,3
        $idsParam = $_GET['ids'] ?? null;
        if(!empty($idsParam)){
            $enquiries = $officeModel->getEnquiriesByIds($idsParam);
        } elseif(!empty($id)){
            $single = $officeModel->getEnquiryById((int)$id);
            if($single){
                $enquiries = [$single];
            }
        }

        if(empty($enquiries)){
            $_SESSION['flash_error'] = 'No admission application records found to print.';
            header('Location: ' . URLROOT . '/frontoffice/onlineAdmissions');
            exit;
        }

        $settings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];

        $data = [
            'enquiries' => $enquiries,
            'settings'  => $settings,
            'single'    => count($enquiries) === 1 ? $enquiries[0] : null
        ];

        $this->view('front_office/print_enquiry', $data);
    }

    public function printAdmissionForms($id = null){
        $this->printAdmissionForm($id);
    }

    // --- 5. Postal / Courier Inward & Outward Dispatch ---
    public function dispatch(){
        $officeModel = $this->model('FrontOffice');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'dispatch_type' => trim($_POST['dispatch_type'] ?? 'Inward'),
                'reference_no' => trim($_POST['reference_no'] ?? ''),
                'sender_title' => trim($_POST['sender_title'] ?? ''),
                'receiver_title' => trim($_POST['receiver_title'] ?? ''),
                'record_date' => !empty($_POST['record_date']) ? $_POST['record_date'] : date('Y-m-d'),
                'courier_name' => trim($_POST['courier_name'] ?? 'TCS Courier'),
                'tracking_id' => trim($_POST['tracking_id'] ?? ''),
                'category' => trim($_POST['category'] ?? 'Official Board Circular'),
                'note' => trim($_POST['note'] ?? '')
            ];
            $officeModel->addPostalRecord($data);
            header('Location: ' . URLROOT . '/frontoffice/dispatch?success=saved');
            exit;
        }

        $filterType = isset($_GET['type']) ? trim($_GET['type']) : null;
        $data = [
            'dispatches' => $officeModel->getPostalRecords($filterType),
            'filter_type' => $filterType
        ];
        $this->view('front_office/dispatch', $data);
    }

    public function deleteDispatch($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/frontoffice/dispatch');
            exit;
        }
        $officeModel = $this->model('FrontOffice');
        $officeModel->deletePostalRecord($id);
        header('Location: ' . URLROOT . '/frontoffice/dispatch?success=deleted');
        exit;
    }

    // --- 6. Phone Call & Query Log ---
    public function callLog(){
        $officeModel = $this->model('FrontOffice');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'call_type' => trim($_POST['call_type'] ?? 'Incoming'),
                'caller_name' => trim($_POST['caller_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'call_date' => !empty($_POST['call_date']) ? $_POST['call_date'] : date('Y-m-d'),
                'call_time' => !empty($_POST['call_time']) ? $_POST['call_time'] : date('h:i A'),
                'duration' => trim($_POST['duration'] ?? '2 mins'),
                'purpose' => trim($_POST['purpose'] ?? 'General Inquiry'),
                'follow_up_date' => !empty($_POST['follow_up_date']) ? $_POST['follow_up_date'] : null,
                'note' => trim($_POST['note'] ?? '')
            ];
            $officeModel->addCallLog($data);
            header('Location: ' . URLROOT . '/frontoffice/callLog?success=saved');
            exit;
        }

        $data = [
            'call_logs' => $officeModel->getCallLogs()
        ];
        $this->view('front_office/call_log', $data);
    }

    public function deleteCallLog($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/frontoffice/callLog');
            exit;
        }
        $officeModel = $this->model('FrontOffice');
        $officeModel->deleteCallLog($id);
        header('Location: ' . URLROOT . '/frontoffice/callLog?success=deleted');
        exit;
    }
}

if (!class_exists('FrontofficeController', false)) {
    class_alias('FrontOfficeController', 'FrontofficeController');
}

