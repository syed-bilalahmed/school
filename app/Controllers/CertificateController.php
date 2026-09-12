<?php
class CertificateController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_academics');
         if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin')){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }
    }

    public function index(){
        $certModel = $this->model('Certificate');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'certificate_name' => trim($_POST['certificate_name']),
                'certificate_text' => trim($_POST['certificate_text']),
                'left_header' => trim($_POST['left_header']),
                'center_header' => trim($_POST['center_header']),
                'right_header' => trim($_POST['right_header']),
                'left_footer' => trim($_POST['left_footer']),
                'center_footer' => trim($_POST['center_footer']),
                'right_footer' => trim($_POST['right_footer']),
                'background_image' => ''
            ];

            if(isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0){
                 require_once APPROOT . '/Core/UploadHandler.php';
                 $upload = UploadHandler::processUpload($_FILES['background_image'], 'certificates');
                 if($upload['success']){
                     $data['background_image'] = $upload['path'];
                 } else {
                     die("Upload failed: " . $upload['error']);
                 }
            }

            $certModel->addCertificate($data);
            header('Location: ' . URLROOT . '/certificate/index');
            exit;
        }

        $data = [
            'certificates' => $certModel->getCertificates()
        ];
        $this->view('certificate/index', $data);
    }
    
    public function delete($id){
        $certModel = $this->model('Certificate');
        $certModel->deleteCertificate($id);
        header('Location: ' . URLROOT . '/certificate/index');
    }

    public function generate(){
        $certModel = $this->model('Certificate');
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $studentModel = $this->model('Student');

        $data = [
            'certificates' => $certModel->getCertificates(),
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'students' => []
        ];

        if(isset($_GET['class_id']) && isset($_GET['section_id'])){
            $data['class_id'] = $_GET['class_id'];
            $data['section_id'] = $_GET['section_id'];
            $data['students'] = $studentModel->getStudentsByClassSection($_GET['class_id'], $_GET['section_id']);
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['print'])){
             // Logic to show print view
             $certificate = $certModel->getCertificateById($_POST['certificate_id']);
             $selected_students = [];
             if(isset($_POST['students'])){
                 foreach($_POST['students'] as $sid){
                      // Fetch individual student details to process placeholders
                      // For now, simplify and just pass IDs to a view that fetches details
                      $selected_students[] = $sid;
                 }
             }

             if(empty($selected_students)){
                 header('Location: ' . URLROOT . '/certificate/generate'); // Error: select students
                 exit;
             }

             // Render Print View directly
             $this->print_view($certificate, $selected_students);
             return;
        }

        $this->view('certificate/generate', $data);
    }

    private function print_view($certificate, $student_ids){
         $studentModel = $this->model('Student'); // Need a method to get multiple or loop
         $students = [];
         
         // Inefficient loop for demo, optimize with WHERE IN later
         // Inefficient loop for demo, optimize with WHERE IN later
         foreach($student_ids as $id){
             $s = $studentModel->getStudentById($id);
             if($s){
                 $students[] = $s;
             }
         }

         $data = [
             'certificate' => $certificate,
             'students' => $students
         ];
         
         $this->view('certificate/print', $data);
    }

    // ==========================================
    // PHASE 8: PAKISTANI CERTIFICATES & ADMIT CARDS
    // ==========================================

    private function checkFeeDuesLock($student_id) {
        $certModel = $this->model('Certificate');
        $balance = $certModel->getStudentFeeBalance($student_id);

        if (isset($_GET['override']) && $_GET['override'] == '1') {
            return false;
        }

        return ($balance > 0) ? $balance : false;
    }

    // Central Credentials & Certificates Hub
    public function hub(){
        $certModel = $this->model('Certificate');
        $studentModel = $this->model('Student');
        $classModel = $this->model('SchoolClass');
        $examModel = $this->model('Exam');

        $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $classes = $classModel->getClasses();
        $students = $classId ? $studentModel->getStudentsByClass($classId) : $studentModel->getStudents();
        
        foreach ($students as &$s) {
            $s->fee_balance = $certModel->getStudentFeeBalance($s->id);
            $s->is_fee_cleared = ($s->fee_balance <= 0);
        }

        $exams = $examModel->getExams();
        $issuedLog = $certModel->getIssuedCertificatesLog();

        $data = [
            'classes' => $classes,
            'students' => $students,
            'exams' => $exams,
            'issued_log' => $issuedLog,
            'selected_class' => $classId
        ];
        $this->view('certificate/hub', $data);
    }

    // Module 20: School Leaving Certificate (SLC) / Transfer Certificate
    public function slc($student_id = null){
        $certModel = $this->model('Certificate');
        $studentModel = $this->model('Student');

        if(!$student_id && isset($_GET['student_id'])){
            $student_id = (int)$_GET['student_id'];
        }

        if(!$student_id){
            header('Location: ' . URLROOT . '/certificate/hub?error=select_student');
            exit;
        }

        $duesBalance = $this->checkFeeDuesLock($student_id);
        if ($duesBalance !== false) {
            $student = $studentModel->getStudentById($student_id);
            $data = [
                'student' => $student,
                'balance' => $duesBalance,
                'target_url' => $_SERVER['REQUEST_URI']
            ];
            $this->view('certificate/locked', $data);
            return;
        }

        $override = [];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $override = [
                'leaving_date' => !empty($_POST['leaving_date']) ? trim($_POST['leaving_date']) : date('Y-m-d'),
                'reason_for_leaving' => trim($_POST['reason_for_leaving'] ?? "Parent's Request / Relocation"),
                'conduct' => trim($_POST['conduct'] ?? 'Exemplary & Obedient'),
                'promoted_to_class' => trim($_POST['promoted_to_class'] ?? 'Promoted to Next Higher Class'),
                'remarks' => trim($_POST['remarks'] ?? 'Passed all assessments with satisfactory academic progress.')
            ];

            // Log issuance
            $slcData = $certModel->getStudentSlcData($student_id, $override);
            $certModel->logIssuedCertificate([
                'student_id' => $student_id,
                'certificate_type' => 'SLC',
                'certificate_no' => $slcData->certificate_no,
                'issue_date' => $slcData->issue_date,
                'leaving_date' => $slcData->leaving_date,
                'reason_for_leaving' => $slcData->reason_for_leaving,
                'conduct' => $slcData->conduct,
                'promoted_to_class' => $slcData->promoted_to_class,
                'remarks' => $slcData->remarks
            ]);
        }

        $slc = $certModel->getStudentSlcData($student_id, $override);
        if(!$slc){
            header('Location: ' . URLROOT . '/certificate/hub?error=student_not_found');
            exit;
        }

        $data = ['slc' => $slc];
        $this->view('certificate/slc', $data);
    }

    // Module 20: Certificate of Good Moral Character
    public function character($student_id = null){
        $certModel = $this->model('Certificate');
        $studentModel = $this->model('Student');
        if(!$student_id && isset($_GET['student_id'])) $student_id = (int)$_GET['student_id'];

        if(!$student_id){
            header('Location: ' . URLROOT . '/certificate/hub?error=select_student');
            exit;
        }

        $duesBalance = $this->checkFeeDuesLock($student_id);
        if ($duesBalance !== false) {
            $student = $studentModel->getStudentById($student_id);
            $data = [
                'student' => $student,
                'balance' => $duesBalance,
                'target_url' => $_SERVER['REQUEST_URI']
            ];
            $this->view('certificate/locked', $data);
            return;
        }

        $override = [];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $override = [
                'conduct' => trim($_POST['conduct'] ?? 'Excellent & Exemplary'),
                'co_curricular' => trim($_POST['co_curricular'] ?? 'Actively participated in Sports, Debates, and Academic Competitions.'),
                'remarks' => trim($_POST['remarks'] ?? 'He/She bears a pleasing personality, respectful demeanor toward faculty, and high moral integrity.')
            ];

            $cData = $certModel->getStudentCharacterData($student_id, $override);
            $certModel->logIssuedCertificate([
                'student_id' => $student_id,
                'certificate_type' => 'Character',
                'certificate_no' => $cData->certificate_no,
                'issue_date' => $cData->issue_date,
                'conduct' => $cData->conduct,
                'remarks' => $cData->remarks
            ]);
        }

        $charData = $certModel->getStudentCharacterData($student_id, $override);
        if(!$charData){
            header('Location: ' . URLROOT . '/certificate/hub?error=student_not_found');
            exit;
        }

        $data = ['character' => $charData];
        $this->view('certificate/character', $data);
    }

    // Module 20: Bonafide / Student Status Certificate
    public function bonafide($student_id = null){
        $certModel = $this->model('Certificate');
        $studentModel = $this->model('Student');
        if(!$student_id && isset($_GET['student_id'])) $student_id = (int)$_GET['student_id'];

        if(!$student_id){
            header('Location: ' . URLROOT . '/certificate/hub?error=select_student');
            exit;
        }

        $duesBalance = $this->checkFeeDuesLock($student_id);
        if ($duesBalance !== false) {
            $student = $studentModel->getStudentById($student_id);
            $data = [
                'student' => $student,
                'balance' => $duesBalance,
                'target_url' => $_SERVER['REQUEST_URI']
            ];
            $this->view('certificate/locked', $data);
            return;
        }

        $override = [];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $override = [
                'purpose' => trim($_POST['purpose'] ?? 'Passport / Visa / NADRA Smart Card / Scholarship Verification')
            ];

            $bData = $certModel->getStudentBonafideData($student_id, $override);
            $certModel->logIssuedCertificate([
                'student_id' => $student_id,
                'certificate_type' => 'Bonafide',
                'certificate_no' => $bData->certificate_no,
                'issue_date' => $bData->issue_date,
                'remarks' => 'Issued for purpose: ' . $bData->purpose
            ]);
        }

        $bonafideData = $certModel->getStudentBonafideData($student_id, $override);
        if(!$bonafideData){
            header('Location: ' . URLROOT . '/certificate/hub?error=student_not_found');
            exit;
        }

        $data = ['bonafide' => $bonafideData];
        $this->view('certificate/bonafide', $data);
    }

    // Module 21: Examination Roll Number Slip / Admit Card
    public function admitCard($student_id = null, $exam_id = null){
        $certModel = $this->model('Certificate');
        $studentModel = $this->model('Student');
        if(!$student_id && isset($_GET['student_id'])) $student_id = (int)$_GET['student_id'];
        if(!$exam_id && isset($_GET['exam_id'])) $exam_id = (int)$_GET['exam_id'];

        if(!$student_id){
            header('Location: ' . URLROOT . '/certificate/hub?error=select_student');
            exit;
        }

        $duesBalance = $this->checkFeeDuesLock($student_id);
        if ($duesBalance !== false) {
            $student = $studentModel->getStudentById($student_id);
            $data = [
                'student' => $student,
                'balance' => $duesBalance,
                'target_url' => $_SERVER['REQUEST_URI']
            ];
            $this->view('certificate/locked', $data);
            return;
        }

        $card = $certModel->getAdmitCardData($student_id, $exam_id);
        if(!$card){
            header('Location: ' . URLROOT . '/certificate/hub?error=student_not_found');
            exit;
        }

        $data = ['card' => $card];
        $this->view('certificate/admit_card', $data);
    }

    // Module 21: Class Batch Roll Number Slips Mass Printing
    public function batchAdmitCards(){
        $certModel = $this->model('Certificate');
        $classModel = $this->model('SchoolClass');
        $examModel = $this->model('Exam');

        $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
        $examId = !empty($_GET['exam_id']) ? (int)$_GET['exam_id'] : null;

        if(!$classId){
            header('Location: ' . URLROOT . '/certificate/hub?error=select_class');
            exit;
        }

        $cards = $certModel->getClassAdmitCards($classId, $examId);
        $classObj = $classModel->getClassById($classId);
        $examObj = $examId ? $examModel->getExamById($examId) : null;

        $data = [
            'cards' => $cards,
            'class' => $classObj,
            'exam' => $examObj
        ];
        $this->view('certificate/batch_admit_cards', $data);
    }
}

