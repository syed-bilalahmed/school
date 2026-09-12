<?php
class ParentController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        if(!isset($_SESSION['user_id'])){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }

        $allowedRoles = ['parent', 'admin', 'super_admin'];
        if(!in_array($_SESSION['user_role'], $allowedRoles)){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }
    }

    public function index(){
        $studentModel = $this->model('Student');
        $attendanceModel = $this->model('Attendance');
        $homeworkModel = $this->model('Homework');
        $timetableModel = $this->model('Timetable');

        $userRole = $_SESSION['user_role'] ?? 'parent';
        $userId = (int)$_SESSION['user_id'];
        $requestedStudentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;

        // 1. Resolve Children List
        if($userRole === 'parent'){
            $children = $studentModel->getStudentsByParentId($userId);
        } else {
            // Admin/Super-Admin preview mode
            if($requestedStudentId){
                $sObj = $studentModel->getStudentById($requestedStudentId);
                if($sObj && !empty($sObj->father_cnic)){
                    $children = $studentModel->getStudentsByParentId($sObj->parent_user_id ?: 0);
                    if(empty($children)){
                        $children = [$sObj];
                    }
                } else {
                    $children = $sObj ? [$sObj] : [];
                }
            } else {
                // Default to first student in school
                $allStudents = $studentModel->getStudents();
                $first = !empty($allStudents) ? $allStudents[0] : null;
                $children = $first ? [$first] : [];
            }
        }

        // 2. Resolve Active Selected Child
        $activeChild = null;
        if(!empty($children)){
            if($requestedStudentId){
                foreach($children as $ch){
                    if($ch->id == $requestedStudentId){
                        $activeChild = $ch;
                        break;
                    }
                }
            }
            if(!$activeChild){
                $activeChild = $children[0];
            }
        }

        // 3. Aggregate Active Child Data (Student 360)
        $childDossier = null;
        $todayAttendance = null;
        $homeworkList = [];
        $todaySchedule = [];

        if($activeChild){
            $childDossier = $studentModel->getStudent360($activeChild->id);

            // Today's Gate Attendance Punch
            $db = new Database();
            $db->query("SELECT * FROM student_attendance WHERE student_id = :sid AND date = CURDATE()");
            $db->bind(':sid', $activeChild->id);
            $todayAttendance = $db->single();

            // Homework for active class-section
            if(!empty($activeChild->class_id) && !empty($activeChild->section_id)){
                $homeworkList = $homeworkModel->getHomework($activeChild->class_id, $activeChild->section_id);
                $todaySchedule = $timetableModel->getTimetable($activeChild->class_id, $activeChild->section_id);
            }
        }

        $data = [
            'children' => $children,
            'active_child' => $activeChild,
            'dossier' => $childDossier,
            'today_attendance' => $todayAttendance,
            'homework' => $homeworkList,
            'schedule' => $todaySchedule,
            'user_role' => $userRole
        ];

        $this->view('parent/index', $data);
    }

    public function results($student_id = null){
        $examModel = $this->model('Exam');
        if(!$student_id){
            header('Location: ' . URLROOT . '/parent/index');
            exit;
        }
        // Redirect to official DMC progress report
        $exams = $examModel->getExams();
        $latestExamId = !empty($exams) ? $exams[0]->id : 1;
        header('Location: ' . URLROOT . '/exam/reportCard/' . $latestExamId . '/' . (int)$student_id);
        exit;
    }

    public function attendance($student_id = null){
        header('Location: ' . URLROOT . '/parent/index?student_id=' . (int)$student_id . '#p-att');
        exit;
    }

    public function homework($student_id = null){
        header('Location: ' . URLROOT . '/parent/index?student_id=' . (int)$student_id . '#p-hw');
        exit;
    }
}
