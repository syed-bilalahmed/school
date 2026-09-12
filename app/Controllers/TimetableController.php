<?php
// app/Controllers/TimetableController.php

class TimetableController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthGuard::requirePermission('manage_academics');
            AuthGuard::verifyCSRF();
        } else {
            if (!AuthGuard::hasPermission('manage_academics') && !AuthGuard::hasPermission('view_academics') && !in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'teacher', 'student', 'parent'])) {
                AuthGuard::requirePermission('manage_academics');
            }
        }
        if(!isset($_SESSION['user_id'])){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }
    }

    public function index(){
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $subjectModel = $this->model('Subject');
        $timetTableModel = $this->model('Timetable');

        $classes = $classModel->getClasses();
        $sections = $sectionModel->getSections();

        $selectedClass = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : ($classes ? $classes[0]->id : null);
        $selectedSection = !empty($_GET['section_id']) ? (int)$_GET['section_id'] : null;

        // If no section selected, pick first section of selected class
        if(!$selectedSection && $selectedClass){
            foreach($sections as $sec){
                if($sec->class_id == $selectedClass){
                    $selectedSection = $sec->id;
                    break;
                }
            }
        }

        // Fetch subjects assigned to this class/section or all subjects as fallback
        $classSubjects = ($selectedClass && $selectedSection) ? 
                         $subjectModel->getSubjectsByClassSection($selectedClass, $selectedSection) : [];
        if(empty($classSubjects)){
            $classSubjects = $subjectModel->getSubjects();
        }

        $timetableSlots = ($selectedClass && $selectedSection) ? 
                          $timetTableModel->getTimetable($selectedClass, $selectedSection) : [];

        // Group timetable by Day
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $matrix = [];
        foreach($days as $d){
            $matrix[$d] = [];
        }
        foreach($timetableSlots as $slot){
            if(isset($matrix[$slot->day_name])){
                $matrix[$slot->day_name][] = $slot;
            }
        }

        $data = [
            'classes' => $classes,
            'sections' => $sections,
            'subjects' => $classSubjects,
            'all_subjects' => $subjectModel->getSubjects(),
            'staff' => $this->getStaffList(),
            'timetable' => $timetableSlots,
            'matrix' => $matrix,
            'days' => $days,
            'selected_class' => $selectedClass,
            'selected_section' => $selectedSection
        ];

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin') exit;
            
            if(isset($_POST['add_schedule'])){
                $schedule = [
                    'class_id' => (int)$_POST['class_id'],
                    'section_id' => (int)$_POST['section_id'],
                    'subject_id' => (int)$_POST['subject_id'],
                    'staff_id' => !empty($_POST['staff_id']) ? (int)$_POST['staff_id'] : null,
                    'day_name' => trim($_POST['day_name']),
                    'time_from' => trim($_POST['time_from']),
                    'time_to' => trim($_POST['time_to']),
                    'room_no' => trim($_POST['room_no'] ?? '')
                ];

                // Check conflict & clash
                $clash = $timetTableModel->checkClash($schedule);
                if($clash['has_clash']){
                    header('Location: ' . URLROOT . '/timetable/index?class_id=' . $schedule['class_id'] . '&section_id=' . $schedule['section_id'] . '&clash_error=' . urlencode($clash['message']));
                    exit;
                }

                $timetTableModel->addTimetable($schedule);
                header('Location: ' . URLROOT . '/timetable/index?class_id=' . $schedule['class_id'] . '&section_id=' . $schedule['section_id'] . '&success=added');
                exit;
            } elseif(isset($_POST['delete_schedule'])){
                $timetTableModel->deleteTimetable((int)$_POST['id']);
                header('Location: ' . URLROOT . '/timetable/index?class_id=' . (int)$_POST['class_id'] . '&section_id=' . (int)$_POST['section_id'] . '&success=deleted');
                exit;
            }
        }

        $this->view('timetable/index', $data);
    }

    public function ajaxCheckClash(){
        header('Content-Type: application/json');
        $timetTableModel = $this->model('Timetable');
        $clash = $timetTableModel->checkClash($_POST);
        echo json_encode($clash);
        exit;
    }

    private function getStaffList(){
        $db = new Database;
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $db->query("SELECT * FROM users WHERE role = 'teacher' AND (school_id = :school_id OR school_id IS NULL) ORDER BY name ASC");
        $db->bind(':school_id', $schoolId);
        return $db->resultSet();
    }
}
