<?php
class AttendanceController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        // Allow admin, teacher, receptionist roles OR explicit permission
        if (!AuthGuard::hasPermission('manage_attendance') && !in_array($_SESSION['user_role'] ?? '', ['super_admin', 'admin', 'teacher', 'receptionist'])) {
            AuthGuard::requirePermission('manage_attendance');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthGuard::verifyCSRF();
        }
    }

    // Student Attendance Page (Module 6 & 7)
    public function student(){
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $attendanceModel = $this->model('Attendance');

        $data = [
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'class_id' => '',
            'section_id' => '',
            'date' => date('Y-m-d'),
            'students' => [],
            'absentees' => []
        ];

        if($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['search'])){
            // If "Save Attendance" button clicked
            if(isset($_POST['save_attendance'])){
                $date = $_POST['date'];
                $class_id = $_POST['class_id'];
                $section_id = $_POST['section_id'];
                
                if(isset($_POST['student'])){
                    foreach($_POST['student'] as $id => $val){
                         $type = isset($val['type']) ? $val['type'] : 'Absent';
                         $remark = isset($val['remark']) ? $val['remark'] : '';
                         $entryTime = isset($val['entry_time']) && !empty($val['entry_time']) ? $val['entry_time'] : null;
                         
                         $attendanceModel->saveStudentAttendance($id, $class_id, $section_id, $date, $type, $remark, $entryTime);
                    }
                    $data['success'] = "Attendance Saved Successfully";
                }
            }

            // Load Student List (for Search OR after Save)
            $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : (isset($_GET['class_id']) ? $_GET['class_id'] : '');
            $section_id = isset($_POST['section_id']) ? $_POST['section_id'] : (isset($_GET['section_id']) ? $_GET['section_id'] : '');
            $date = isset($_POST['date']) ? $_POST['date'] : (isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'));

            if($class_id && $section_id){
                $data['class_id'] = $class_id;
                $data['section_id'] = $section_id;
                $data['date'] = $date;
                $data['students'] = $attendanceModel->getStudentAttendance($class_id, $section_id, $date);
                $data['absentees'] = $attendanceModel->getTodayAbsentNotificationList($class_id, $section_id, $date);
            }
        }
        
        $this->view('attendance/student', $data);
    }

    // --- MODULE 7: MONTHLY ATTENDANCE REGISTER MATRIX ---
    public function monthly(){
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $attendanceModel = $this->model('Attendance');

        $class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : null;
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');

        $register = null;
        if($class_id && $section_id){
            $register = $attendanceModel->getMonthlyAttendanceRegister($class_id, $section_id, $year, $month);
        }

        $data = [
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'class_id' => $class_id,
            'section_id' => $section_id,
            'year' => $year,
            'month' => $month,
            'register' => $register
        ];

        $this->view('attendance/monthly', $data);
    }

    // Staff Attendance Page
    public function staff(){
        $attendanceModel = $this->model('Attendance');

        // Roles for filter
        $roles = ['admin', 'teacher', 'librarian', 'receptionist'];

        $data = [
            'date' => date('Y-m-d'),
            'role' => '',
            'staffs' => [],
            'roles' => $roles
        ];

        if($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['search'])){
            // Save?
            if(isset($_POST['save_attendance'])){
                $date = $_POST['date'];
                
                if(isset($_POST['staff'])){
                    foreach($_POST['staff'] as $id => $val){
                         $type = isset($val['type']) ? $val['type'] : 'Absent';
                         $remark = isset($val['remark']) ? $val['remark'] : '';
                         $attendanceModel->saveStaffAttendance($id, $date, $type, $remark);
                    }
                    $data['success'] = "Staff Attendance Saved Successfully";
                }
            }

            // Load List
            $date = isset($_POST['date']) ? $_POST['date'] : (isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'));
            $role = isset($_POST['role']) ? $_POST['role'] : (isset($_GET['role']) ? $_GET['role'] : '');

            $data['date'] = $date;
            $data['role'] = $role;
            $data['staffs'] = $attendanceModel->getStaffAttendance($date, $role);
        } else {
             // Load default (today, all roles)
             $data['staffs'] = $attendanceModel->getStaffAttendance(date('Y-m-d'));
        }
        
        $this->view('attendance/staff', $data);
    }
}
