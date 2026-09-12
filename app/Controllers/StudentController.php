<?php
class StudentController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        
        $allowedRoles = ['student', 'parent', 'admin', 'super_admin'];
        $userRole = $_SESSION['user_role'] ?? '';
        if(!isset($_SESSION['user_id']) || !in_array($userRole, $allowedRoles)){
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    public function index(){
        $studentModel = $this->model('Student');
        $timetableModel = $this->model('Timetable');
        $homeworkModel = $this->model('Homework');
        $noticeModel = $this->model('Notice');
        $userRole = $_SESSION['user_role'] ?? 'student';
        $userId = (int)$_SESSION['user_id'];

        $student = null;
        if($userRole === 'student'){
            $student = $studentModel->getStudentByUserId($userId);
        } elseif($userRole === 'parent'){
            $reqStudentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;
            $myKids = $studentModel->getStudentsByParentId($userId);
            if($reqStudentId){
                foreach($myKids as $k){
                    if($k->id == $reqStudentId){
                        $student = $k;
                        break;
                    }
                }
            }
            if(!$student && !empty($myKids)){
                $student = $myKids[0];
            }
        } else {
            // Admin/Super Admin preview
            $reqStudentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;
            if($reqStudentId){
                $student = $studentModel->getStudentById($reqStudentId);
            } else {
                $all = $studentModel->getStudents();
                $student = !empty($all) ? $all[0] : null;
            }
        }

        $dossier = null;
        $schedule = [];
        $homework = [];
        $todayAttendance = null;
        $notices = [];

        if($student){
            // 1. Student 360 Unified Dossier (Attendance summary, fees, payments, exam results)
            $dossier = $studentModel->getStudent360($student->id);

            // 2. Class Timetable
            if(!empty($student->class_id) && !empty($student->section_id)){
                $schedule = $timetableModel->getTimetable($student->class_id, $student->section_id);
                $homework = $homeworkModel->getHomework($student->class_id, $student->section_id);
            }

            // 3. Today's Gate Attendance Punch
            $db = new Database();
            $db->query("SELECT * FROM student_attendance WHERE student_id = :sid AND date = CURDATE()");
            $db->bind(':sid', $student->id);
            $todayAttendance = $db->single();

            // 4. Student Notices
            $notices = $noticeModel->getNotices('student');
        }

        $data = [
            'student' => $student,
            'dossier' => $dossier,
            'schedule' => $schedule,
            'homework' => $homework,
            'today_attendance' => $todayAttendance,
            'notices' => $notices,
            'user_role' => $userRole
        ];

        $this->view('student/dashboard', $data);
    }

    public function results(){
        $studentModel = $this->model('Student');
        $examModel = $this->model('Exam');
        $userRole = $_SESSION['user_role'] ?? 'student';
        $userId = (int)$_SESSION['user_id'];

        $student = null;
        if($userRole === 'student'){
            $student = $studentModel->getStudentByUserId($userId);
        } else {
            $reqStudentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;
            if($reqStudentId){
                $student = $studentModel->getStudentById($reqStudentId);
            } else {
                $all = $studentModel->getStudents();
                $student = !empty($all) ? $all[0] : null;
            }
        }
        
        if(!$student){
            die('Student record not found');
        }

        $results = $examModel->getStudentResults($student->id);
        $exams = $examModel->getExams();

        $data = [
            'student' => $student,
            'results' => $results,
            'exams' => $exams,
            'user_role' => $userRole
        ];

        $this->view('student/results', $data);
    }
}
