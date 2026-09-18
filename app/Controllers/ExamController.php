<?php
// app/Controllers/ExamController.php

class ExamController extends Controller {
    public function __construct(){
        if (class_exists('AuthGuard')) {
            AuthGuard::requireSchoolContext();
            $urlSegments = explode('/', trim($_GET['url'] ?? '', '/'));
            // Only bypass permission for exactly the reportCard or batchReportCards actions
            $reportCardActions = ['reportCard', 'report_card', 'batchReportCards', 'batch_report_cards'];
            $currentAction = $urlSegments[1] ?? '';
            $isReportCardAction = in_array($currentAction, $reportCardActions, true);
            if (!$isReportCardAction) {
                AuthGuard::requirePermission('manage_exams');
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && class_exists('AuthGuard')) {
            AuthGuard::verifyCSRF();
        }
    }

    public function index(){
        $examModel = $this->model('Exam');
        $sessionModel = $this->model('AcademicSession');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'exam_type' => trim($_POST['exam_type'] ?? 'Term Exam'),
                'academic_session_id' => !empty($_POST['academic_session_id']) ? (int)$_POST['academic_session_id'] : null,
                'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                'description' => trim($_POST['description'] ?? '')
            ];
            $examModel->addExam($data);
            header('Location: ' . URLROOT . '/exam/index?success=created');
            exit;
        }

        $sessions = $sessionModel->getSessions();
        $currentSession = $sessionModel->getCurrentSession();
        $exams = $examModel->getExams();

        $data = [
            'exams' => $exams,
            'sessions' => $sessions,
            'current_session' => $currentSession
        ];
        $this->view('exams/index', $data);
    }

    public function schedule($exam_id = null){
        $examModel = $this->model('Exam');
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $subjectModel = $this->model('Subject');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'exam_id' => (int)$_POST['exam_id'],
                'class_id' => (int)$_POST['class_id'],
                'section_id' => (int)$_POST['section_id'],
                'subject_id' => (int)$_POST['subject_id'],
                'date_of_exam' => $_POST['date_of_exam'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'room_no' => trim($_POST['room_no'] ?? ''),
                'full_marks' => (float)($_POST['full_marks'] ?? 100),
                'passing_marks' => (float)($_POST['passing_marks'] ?? 33),
                'theory_marks' => (float)($_POST['theory_marks'] ?? 75),
                'practical_marks' => (float)($_POST['practical_marks'] ?? 25)
            ];
            $examModel->addSchedule($data);
            header('Location: ' . URLROOT . '/exam/schedule/' . $_POST['exam_id'] . '?class_id=' . $_POST['class_id'] . '&section_id=' . $_POST['section_id'] . '&success=scheduled');
            exit;
        }

        $data = [
            'exam_id' => $exam_id,
            'exams' => $examModel->getExams(),
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'subjects' => [],
            'schedules' => []
        ];

        if($exam_id && isset($_GET['class_id']) && isset($_GET['section_id'])){
             $data['class_id'] = $_GET['class_id'];
             $data['section_id'] = $_GET['section_id'];
             $data['subjects'] = $subjectModel->getSubjectsByClassSection($_GET['class_id'], $_GET['section_id']);
             $data['schedules'] = $examModel->getSchedulesByExam($exam_id, $_GET['class_id'], $_GET['section_id']);
        }

        $this->view('exams/schedule', $data);
    }

    public function marks($exam_id = null){
        $examModel = $this->model('Exam');
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $schedule_id = (int)$_POST['schedule_id'];
            $submitAction = trim($_POST['submit_action'] ?? 'save_draft');
            $currentUserId = $_SESSION['user_id'] ?? 1;

            if(isset($_POST['students']) && is_array($_POST['students'])){
                foreach($_POST['students'] as $sid => $val){
                    $theory = isset($val['theory']) ? (float)$val['theory'] : 0;
                    $practical = isset($val['practical']) ? (float)$val['practical'] : 0;
                    $marks = isset($val['marks']) ? (float)$val['marks'] : ($theory + $practical);
                    $absent = isset($val['absent']) ? 'yes' : 'no';
                    $remarks = trim($val['remarks'] ?? '');

                    $examModel->saveMarks($schedule_id, (int)$sid, $marks, $absent, $theory, $practical, $remarks);
                }
            }

            if($submitAction === 'submit_and_lock'){
                $examModel->submitToClassTeacher($schedule_id, $currentUserId);
                $msg = 'submitted_and_locked';
            } else {
                $msg = 'draft_saved';
            }

            header('Location: ' . URLROOT . '/exam/marks/' . $_POST['exam_id'] . '?class_id=' . $_POST['class_id'] . '&section_id=' . $_POST['section_id'] . '&schedule_id=' . $schedule_id . '&success=' . $msg); 
            exit;
        }

        $data = [
            'exam_id' => $exam_id,
            'exams' => $examModel->getExams(),
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'schedules' => [],
            'schedule_info' => null,
            'students' => []
        ];

        if($exam_id && isset($_GET['class_id']) && isset($_GET['section_id'])){
            $data['class_id'] = $_GET['class_id'];
            $data['section_id'] = $_GET['section_id'];
            $data['schedules'] = $examModel->getSchedulesByExam($exam_id, $_GET['class_id'], $_GET['section_id']);
            
            if(isset($_GET['schedule_id'])){
                $data['schedule_id'] = (int)$_GET['schedule_id'];
                $data['schedule_info'] = $examModel->getScheduleById($data['schedule_id']);
                $data['students'] = $examModel->getStudentsForMarksEntry($_GET['schedule_id'], $_GET['class_id'], $_GET['section_id']);
            }
        }

        $this->view('exams/marks', $data);
    }

    // --- 4-TIER APPROVAL CHAIN DASHBOARD ---
    public function approval(){
        $examModel = $this->model('Exam');
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');

        $exam_id = isset($_GET['exam_id']) ? (int)$_GET['exam_id'] : null;
        $class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : null;

        $exams = $examModel->getExams();
        if(!$exam_id && !empty($exams)){
            $exam_id = $exams[0]->id;
        }

        $progressList = $examModel->getApprovalChainProgress($exam_id, $class_id, $section_id);

        $data = [
            'exam_id' => $exam_id,
            'class_id' => $class_id,
            'section_id' => $section_id,
            'exams' => $exams,
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'progress' => $progressList,
            'current_user_role' => $_SESSION['user_role'] ?? 'teacher'
        ];

        $this->view('exams/approval', $data);
    }

    public function processApproval(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $examModel = $this->model('Exam');
            $scheduleId = (int)$_POST['schedule_id'];
            $action = trim($_POST['action'] ?? '');
            $userId = $_SESSION['user_id'] ?? 1;

            if($action === 'review_class_teacher'){
                $examModel->reviewByClassTeacher($scheduleId, $userId);
            } elseif($action === 'verify_vp'){
                $examModel->verifyByVP($scheduleId, $userId);
            } elseif($action === 'approve_principal'){
                $examModel->approveByPrincipal($scheduleId, $userId, true);
            } elseif($action === 'reject'){
                $reason = trim($_POST['rejection_reason'] ?? 'Returned for revision');
                $examModel->rejectMarks($scheduleId, $userId, $reason);
            }

            $redirectExam = !empty($_POST['exam_id']) ? '?exam_id=' . (int)$_POST['exam_id'] : '';
            header('Location: ' . URLROOT . '/exam/approval' . $redirectExam . '&success=state_updated');
            exit;
        }
        header('Location: ' . URLROOT . '/exam/approval');
        exit;
    }

    // --- MODULE 14: EXAM HALL ATTENDANCE & SIGNATURE SHEET ---
    public function hallSheet($schedule_id){
        $examModel = $this->model('Exam');
        $sheetData = $examModel->getHallSignatureSheet((int)$schedule_id);

        if(!$sheetData || empty($sheetData['schedule'])){
            die('Examination paper schedule not found.');
        }

        $data = [
            'schedule' => $sheetData['schedule'],
            'students' => $sheetData['students']
        ];

        $this->view('exams/hall_sheet', $data);
    }

    // --- CLASS EXAM CANDIDATE SIGNATURE SLIP / ATTENDANCE SHEET ---
    public function signatureSlip($exam_id = null){
        $examModel = $this->model('Exam');
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $studentModel = $this->model('Student');

        if(!$exam_id && isset($_GET['exam_id'])){
            $exam_id = (int)$_GET['exam_id'];
        }

        $class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : null;

        $exams = $examModel->getExams();
        if(!$exam_id && !empty($exams)){
            $exam_id = $exams[0]->id;
        }

        $students = [];
        $schedules = [];
        if($exam_id && $class_id){
            if(!empty($section_id)){
                $students = $studentModel->getStudentsByClassSection($class_id, $section_id);
            } else {
                $students = $studentModel->getStudentsByClass($class_id);
            }
            $schedules = $examModel->getSchedulesByExam($exam_id, $class_id, $section_id ?: 0);
        }

        $selectedExam = null;
        foreach($exams as $ex){
            if($ex->id == $exam_id){
                $selectedExam = $ex;
                break;
            }
        }

        $data = [
            'exam_id' => $exam_id,
            'class_id' => $class_id,
            'section_id' => $section_id,
            'exams' => $exams,
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'students' => $students,
            'schedules' => $schedules,
            'selected_exam' => $selectedExam
        ];

        $this->view('exams/signature_slip', $data);
    }

    // ==========================================
    // PHASE 5: GAZETTE & REPORT CARDS ACTIONS
    // ==========================================

    public function gazette($exam_id = null){
        $examModel = $this->model('Exam');
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');

        $exams = $examModel->getExams();
        if(!$exam_id && isset($_GET['exam_id'])){
            $exam_id = (int)$_GET['exam_id'];
        }
        if(!$exam_id && !empty($exams)){
            $exam_id = $exams[0]->id;
        }

        $class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : null;

        $gazetteData = null;
        if($exam_id && $class_id && $section_id){
            $gazetteData = $examModel->getClassGazette($exam_id, $class_id, $section_id);
        }

        $data = [
            'exam_id' => $exam_id,
            'class_id' => $class_id,
            'section_id' => $section_id,
            'exams' => $exams,
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'gazette' => $gazetteData
        ];

        $this->view('exams/gazette', $data);
    }

    public function results($exam_id = null){
        return $this->gazette($exam_id);
    }

    public function reportCard($exam_id, $student_id){
        $examModel = $this->model('Exam');
        $reportData = $examModel->getStudentReportCard((int)$exam_id, (int)$student_id);

        if(!$reportData || empty($reportData['student'])){
            die('Student examination record not found.');
        }

        $this->view('exams/report_card', $reportData);
    }

    public function batchReportCards($exam_id){
        $examModel = $this->model('Exam');
        $class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : null;

        if(!$exam_id || !$class_id || !$section_id){
            die('Invalid parameters. Exam, Class, and Section are required.');
        }

        $gazette = $examModel->getClassGazette((int)$exam_id, $class_id, $section_id);
        if(!$gazette || empty($gazette['students'])){
            die('No examination results found for this class section.');
        }

        $studentCards = [];
        foreach($gazette['students'] as $st){
            $card = $examModel->getStudentReportCard((int)$exam_id, $st['student_id']);
            if($card){
                $studentCards[] = $card;
            }
        }

        $data = [
            'exam' => $gazette['exam'],
            'class' => $gazette['class'],
            'cards' => $studentCards
        ];

        $this->view('exams/batch_report_cards', $data);
    }
}
