<?php
class TeacherController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        
        $allowedRoles = ['teacher', 'admin', 'super_admin'];
        if (!in_array($_SESSION['user_role'] ?? '', $allowedRoles) && !AuthGuard::hasPermission('manage_academics')) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthGuard::verifyCSRF();
        }
    }

    public function index(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $userId = (int)$_SESSION['user_id'];
        $db = new Database();

        // 1. Resolve Staff Profile
        $staffModel = $this->model('Staff');
        $staff = $staffModel->getStaffByUserId($userId);
        $staffId = $staff ? (int)$staff->id : $userId;

        // 2. Today's Attendance Record
        $todayDate = date('Y-m-d');
        $db->query("SELECT * FROM staff_attendance 
                    WHERE (staff_id = :uid OR staff_id = :sid) 
                      AND date = :today 
                      AND school_id = :sch 
                    LIMIT 1");
        $db->bind(':uid', $userId);
        $db->bind(':sid', $staffId);
        $db->bind(':today', $todayDate);
        $db->bind(':sch', $schoolId);
        $todayAttendance = $db->single();

        // 3. Current Month Attendance Summary
        $curMonth = (int)date('m');
        $curYear = (int)date('Y');
        $db->query("SELECT 
                        COUNT(*) as total_logged_days,
                        SUM(CASE WHEN attendance_type = 'Present' THEN 1 ELSE 0 END) as present_days,
                        SUM(CASE WHEN attendance_type = 'Late' THEN 1 ELSE 0 END) as late_days,
                        SUM(CASE WHEN attendance_type = 'Half Day' THEN 1 ELSE 0 END) as half_days,
                        SUM(CASE WHEN attendance_type = 'Absent' THEN 1 ELSE 0 END) as absent_days
                    FROM staff_attendance 
                    WHERE (staff_id = :uid OR staff_id = :sid) 
                      AND MONTH(date) = :m 
                      AND YEAR(date) = :y 
                      AND school_id = :sch");
        $db->bind(':uid', $userId);
        $db->bind(':sid', $staffId);
        $db->bind(':m', $curMonth);
        $db->bind(':y', $curYear);
        $db->bind(':sch', $schoolId);
        $monthStats = $db->single();

        // Attendance policy settings
        $db->query("SELECT setting_key, setting_value FROM site_settings 
                    WHERE (school_id = :sch OR school_id IS NULL) 
                      AND setting_key IN ('payroll_free_leaves_per_month', 'payroll_absent_cutting_percent', 'payroll_half_day_cutting_percent')");
        $db->bind(':sch', $schoolId);
        $sRows = $db->resultSet();
        $pSettings = [];
        foreach ($sRows as $sr) {
            $pSettings[$sr->setting_key] = $sr->setting_value;
        }

        $freeLeavesAllowed = (float)($pSettings['payroll_free_leaves_per_month'] ?? 2);
        $absents = (float)($monthStats->absent_days ?? 0);
        $halfDays = (float)($monthStats->half_days ?? 0);
        $chargeableAbsents = max(0, $absents - $freeLeavesAllowed);
        
        $basicSalary = (float)($staff->basic_salary ?? 0);
        $dailyRate = $basicSalary > 0 ? ($basicSalary / 30) : 0;
        $chargeableUnits = $chargeableAbsents + ($halfDays * 0.5);
        $estDeduction = round($chargeableUnits * $dailyRate, 2);

        $totalLogged = (int)($monthStats->total_logged_days ?? 0);
        $presentCount = (int)($monthStats->present_days ?? 0);
        $attPercentage = $totalLogged > 0 ? round(($presentCount / $totalLogged) * 100, 1) : 100;

        // 4. Assigned Classes (Class Teacher of)
        $db->query("SELECT sec.*, c.class_name, 
                           (SELECT COUNT(*) FROM students st WHERE st.class_id = sec.class_id AND st.section_id = sec.id AND st.school_id = :sch) as student_count
                    FROM sections sec
                    JOIN classes c ON sec.class_id = c.id
                    WHERE sec.class_teacher_id = :uid AND c.school_id = :sch");
        $db->bind(':uid', $userId);
        $db->bind(':sch', $schoolId);
        $assignedClasses = $db->resultSet();

        // 5. Subject Allocations Taught
        $db->query("SELECT cs.*, c.class_name, sec.section_name, s.subject_name, s.subject_code,
                           (SELECT COUNT(*) FROM students st WHERE st.class_id = cs.class_id AND st.section_id = cs.section_id AND st.school_id = :sch) as student_count
                    FROM class_subjects cs
                    JOIN classes c ON cs.class_id = c.id
                    JOIN sections sec ON cs.section_id = sec.id
                    JOIN subjects s ON cs.subject_id = s.id
                    WHERE cs.teacher_id = :uid AND c.school_id = :sch
                    ORDER BY c.class_name, sec.section_name");
        $db->bind(':uid', $userId);
        $db->bind(':sch', $schoolId);
        $subjectAllocations = $db->resultSet();

        // 6. Today's Class Timetable Schedule
        $dayName = date('l'); // e.g. Monday, Tuesday, Wednesday...
        $db->query("SELECT ct.*, s.subject_name, s.subject_code, c.class_name, sec.section_name 
                    FROM class_timetables ct
                    JOIN subjects s ON ct.subject_id = s.id
                    JOIN classes c ON ct.class_id = c.id
                    JOIN sections sec ON ct.section_id = sec.id
                    WHERE ct.staff_id = :uid AND ct.day_name = :day AND ct.school_id = :sch 
                    ORDER BY ct.time_from ASC");
        $db->bind(':uid', $userId);
        $db->bind(':day', $dayName);
        $db->bind(':sch', $schoolId);
        $todaySchedule = $db->resultSet();

        // 7. Recent Personal Attendance Punch Logs (Last 15 records)
        $db->query("SELECT * FROM staff_attendance 
                    WHERE (staff_id = :uid OR staff_id = :sid) AND school_id = :sch 
                    ORDER BY date DESC LIMIT 15");
        $db->bind(':uid', $userId);
        $db->bind(':sid', $staffId);
        $db->bind(':sch', $schoolId);
        $attendanceLogs = $db->resultSet();

        // 8. Recent Payslips
        $db->query("SELECT * FROM staff_payslips 
                    WHERE staff_id = :sid AND school_id = :sch 
                    ORDER BY year DESC, id DESC LIMIT 6");
        $db->bind(':sid', $staffId);
        $db->bind(':sch', $schoolId);
        $payslips = $db->resultSet();

        // 9. Recent Staff Notices
        $noticeModel = $this->model('Notice');
        $notices = $noticeModel->getNotices('teacher');

        $data = [
            'teacher' => $staff,
            'today_attendance' => $todayAttendance,
            'month_stats' => [
                'total_days' => $totalLogged,
                'present' => $presentCount,
                'late' => (int)($monthStats->late_days ?? 0),
                'half_day' => (int)($monthStats->half_days ?? 0),
                'absent' => (int)($monthStats->absent_days ?? 0),
                'percentage' => $attPercentage,
                'free_leaves' => $freeLeavesAllowed,
                'chargeable_absents' => $chargeableAbsents,
                'estimated_deduction' => $estDeduction
            ],
            'assigned_classes' => $assignedClasses,
            'subject_allocations' => $subjectAllocations,
            'today_schedule' => $todaySchedule,
            'attendance_logs' => $attendanceLogs,
            'payslips' => $payslips,
            'notices' => $notices
        ];

        $this->view('teacher/index', $data);
    }

    /**
     * AJAX action for instant daily attendance toggle / check-in from Teacher Dashboard
     */
    public function ajaxCheckIn(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $status = trim($_POST['status'] ?? 'Present');
        $customNote = trim($_POST['note'] ?? '');

        $allowedStatuses = ['Present', 'Late', 'Half Day', 'Absent'];
        if (!in_array($status, $allowedStatuses)) {
            $status = 'Present';
        }

        $currentTime = date('h:i A');
        $todayDate = date('Y-m-d');
        
        $remark = "Checked In via Teacher Portal at {$currentTime}";
        if ($status === 'Absent') {
            $remark = "Leave applied via Teacher Portal at {$currentTime}" . ($customNote ? ": {$customNote}" : "");
        } elseif (!empty($customNote)) {
            $remark .= " ({$customNote})";
        }

        $attendanceModel = $this->model('Attendance');
        $saved = $attendanceModel->saveStaffAttendance($userId, $todayDate, $status, $remark);

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        if ($saved) {
            echo json_encode([
                'success' => true,
                'message' => "Attendance status marked as '{$status}' at {$currentTime}.",
                'status' => $status,
                'time' => $currentTime,
                'remark' => $remark
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Unable to record attendance. Please try again or contact administration.'
            ]);
        }
        exit;
    }

    public function results(){
        $examModel = $this->model('Exam');
        $data = [
            'exams' => $examModel->getExams()
        ];
        $this->view('teacher/results', $data);
    }

    public function mySlips(){
        $userId = (int)$_SESSION['user_id'];
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $staffModel = $this->model('Staff');
        $staff = $staffModel->getStaffByUserId($userId);
        $staffId = $staff ? (int)$staff->id : $userId;

        $db = new Database();
        $db->query("SELECT * FROM staff_payslips WHERE staff_id = :sid AND school_id = :sch ORDER BY year DESC, id DESC");
        $db->bind(':sid', $staffId);
        $db->bind(':sch', $schoolId);
        $payslips = $db->resultSet();

        $data = [
            'staff' => $staff,
            'payslips' => $payslips
        ];
        $this->view('teacher/my_slips', $data);
    }
}
