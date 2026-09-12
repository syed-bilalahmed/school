<?php
class ClearanceController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        if(!isset($_SESSION['user_id']) || (!in_array($_SESSION['user_role'], ['admin', 'super_admin', 'principal', 'vice_principal', 'teacher', 'accountant', 'librarian']))){
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    // --- 1. Institutional Clearance Hub ---
    public function index(){
        $clearanceModel = $this->model('Clearance');
        $studentModel = $this->model('Student');
        $sessionModel = $this->model('AcademicSession');

        $filterStatus = isset($_GET['status']) ? trim($_GET['status']) : null;
        $clearances = $clearanceModel->getClearances($filterStatus);
        $stats = $clearanceModel->getStats();
        $students = $studentModel->getStudents();
        $sessions = $sessionModel->getSessions();

        $data = [
            'clearances' => $clearances,
            'stats' => $stats,
            'students' => $students,
            'sessions' => $sessions,
            'filter_status' => $filterStatus,
            'next_clearance_no' => $clearanceModel->getNextClearanceNo()
        ];

        $this->view('clearance/index', $data);
    }

    // --- 2. Initiate Student Clearance ---
    public function apply(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $clearanceModel = $this->model('Clearance');
            $studentId = (int)$_POST['student_id'];

            // Check if already applied
            $existing = $clearanceModel->getClearanceByStudentId($studentId);
            if($existing && $existing->overall_status == 'In Progress'){
                header('Location: ' . URLROOT . '/clearance/detail/' . $existing->id);
                exit;
            }

            $data = [
                'student_id' => $studentId,
                'academic_session_id' => !empty($_POST['academic_session_id']) ? (int)$_POST['academic_session_id'] : null,
                'reason_for_leaving' => trim($_POST['reason_for_leaving'] ?? 'Completed Matriculation / Migration'),
                'application_date' => !empty($_POST['application_date']) ? $_POST['application_date'] : date('Y-m-d')
            ];

            $clearanceModel->initiateClearance($data);
            $newRecord = $clearanceModel->getClearanceByStudentId($studentId);
            if($newRecord){
                header('Location: ' . URLROOT . '/clearance/detail/' . $newRecord->id . '?success=initiated');
                exit;
            }
        }
        header('Location: ' . URLROOT . '/clearance/index');
        exit;
    }

    // --- 3. 5-Department Clearance Review & Signoff Dashboard ---
    public function detail($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/clearance/index');
            exit;
        }

        $clearanceModel = $this->model('Clearance');
        $clearance = $clearanceModel->getClearanceById($id);
        if(!$clearance){
            die("Clearance application not found.");
        }

        // Automated Vitals (Dues & Library)
        $autoCheck = $clearanceModel->getAutomatedVitalsCheck($clearance->student_id);

        $data = [
            'clearance' => $clearance,
            'auto_check' => $autoCheck
        ];

        $this->view('clearance/detail', $data);
    }

    // --- 4. Department Signoff Action ---
    public function signoff($id = null){
        if(empty($id) || $_SERVER['REQUEST_METHOD'] != 'POST'){
            header('Location: ' . URLROOT . '/clearance/index');
            exit;
        }

        $clearanceModel = $this->model('Clearance');
        $dept = trim($_POST['department'] ?? '');
        $status = trim($_POST['status'] ?? 'Cleared');
        $remarks = trim($_POST['remarks'] ?? 'Clearance granted with zero liability.');
        $clearedBy = trim($_POST['cleared_by'] ?? ($_SESSION['user_name'] ?? 'Authorized Officer'));

        $clearanceModel->updateDepartmentSignoff($id, $dept, $status, $remarks, $clearedBy);
        header('Location: ' . URLROOT . '/clearance/detail/' . $id . '?success=signed');
        exit;
    }

    // --- 5. Principal Final Release ---
    public function release($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/clearance/index');
            exit;
        }

        $clearanceModel = $this->model('Clearance');
        $clearanceModel->principalReleaseStudent($id, $_SESSION['user_id'] ?? 1);
        header('Location: ' . URLROOT . '/clearance/detail/' . $id . '?success=released');
        exit;
    }

    // --- 6. Printable Clearance Certificate / NOC ---
    public function certificate($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/clearance/index');
            exit;
        }

        $clearanceModel = $this->model('Clearance');
        $clearance = $clearanceModel->getClearanceById($id);
        if(!$clearance){
            die("Clearance application not found.");
        }

        $data = [
            'clearance' => $clearance
        ];

        $this->view('clearance/certificate', $data);
    }

    // --- 7. Leaving Package Bundle (SLC, Character, NOC, DMC) ---
    public function package($id = null){
        if(empty($id)){
            header('Location: ' . URLROOT . '/clearance/index');
            exit;
        }

        $clearanceModel = $this->model('Clearance');
        $certModel = $this->model('Certificate');
        $examModel = $this->model('Exam');

        $clearance = $clearanceModel->getClearanceById($id);
        if(!$clearance){
            die("Clearance record not found.");
        }

        $studentId = $clearance->student_id;
        $slcData = $certModel->getStudentSlcData($studentId);

        // Fetch student's latest exams for DMC link
        $exams = $examModel->getExams();

        $data = [
            'clearance' => $clearance,
            'student' => $slcData,
            'exams' => $exams
        ];

        $this->view('clearance/package', $data);
    }
}

if (!class_exists('Clearancecontroller', false)) {
    class_alias('ClearanceController', 'Clearancecontroller');
}

