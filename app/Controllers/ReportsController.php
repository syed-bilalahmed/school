<?php
class ReportsController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'super_admin' && !AuthGuard::hasPermission('view_reports')) {
            AuthGuard::requirePermission('view_reports');
        }
    }

    public function index(){
        $this->view('reports/index');
    }

    public function dashboard(){
        header('Location: ' . URLROOT . '/admin/dashboard');
        exit;
    }

    public function student(){
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $studentModel = $this->model('Student');
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $db = new Database;
        
        $data = [
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'students' => [],
            'class_id' => $_GET['class_id'] ?? '',
            'section_id' => $_GET['section_id'] ?? '',
            'status' => $_GET['status'] ?? 'Active',
            'gender' => $_GET['gender'] ?? ''
        ];

        if(isset($_GET['search']) || (isset($_GET['class_id']) && !empty($_GET['class_id']))){
            $sql = "SELECT s.*, u.name, u.email, c.class_name, sec.section_name 
                    FROM students s
                    JOIN users u ON s.user_id = u.id
                    LEFT JOIN classes c ON s.class_id = c.id
                    LEFT JOIN sections sec ON s.section_id = sec.id
                    WHERE (s.school_id = :school_id OR s.school_id IS NULL)";
            
            $params = [':school_id' => $schoolId];

            if (!empty($data['class_id'])) {
                $sql .= " AND s.class_id = :class_id";
                $params[':class_id'] = $data['class_id'];
            }
            if (!empty($data['section_id'])) {
                $sql .= " AND s.section_id = :section_id";
                $params[':section_id'] = $data['section_id'];
            }
            if (!empty($data['status'])) {
                $sql .= " AND s.status = :status";
                $params[':status'] = $data['status'];
            }
            if (!empty($data['gender'])) {
                $sql .= " AND s.gender = :gender";
                $params[':gender'] = $data['gender'];
            }

            $sql .= " ORDER BY c.class_name, sec.section_name, s.roll_no";

            $db->query($sql);
            foreach ($params as $k => $v) {
                $db->bind($k, $v);
            }
            $data['students'] = $db->resultSet() ?: [];
        }

        $this->view('reports/student', $data);
    }

    public function finance(){
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $settingModel = $this->model('SiteSetting');
        $currency = $settingModel->getSetting('currency_symbol', 'Rs.');
        
        $data = [
            'from_date' => $_GET['from_date'] ?? date('Y-m-01'),
            'to_date' => $_GET['to_date'] ?? date('Y-m-d'),
            'mode' => $_GET['payment_mode'] ?? '',
            'payments' => [],
            'total_collected' => 0,
            'total_cash' => 0,
            'total_bank' => 0,
            'total_expenses' => 0,
            'currency' => $currency
        ];
        
        $db = new Database;
        $sql = "SELECT fp.*, s.name as student_name, s.admission_no, fgt.measure_code as fee_type, fg.name as fee_group
                FROM fee_payments fp
                JOIN student_fees sf ON fp.student_fee_id = sf.id
                JOIN students s ON sf.student_id = s.id
                JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id
                JOIN fee_groups fg ON fgt.fee_groups_id = fg.id
                WHERE fp.date BETWEEN :from AND :to AND (s.school_id = :school_id OR s.school_id IS NULL)";
        
        if (!empty($data['mode'])) {
            $sql .= " AND fp.mode = :mode";
        }
        $sql .= " ORDER BY fp.date DESC, fp.id DESC";

        $db->query($sql);
        $db->bind(':from', $data['from_date']);
        $db->bind(':to', $data['to_date']);
        $db->bind(':school_id', $schoolId);
        if (!empty($data['mode'])) {
            $db->bind(':mode', $data['mode']);
        }
        $data['payments'] = $db->resultSet() ?: [];

        foreach ($data['payments'] as $p) {
            $amt = (float)$p->amount;
            $data['total_collected'] += $amt;
            if (stripos($p->mode ?? '', 'Cash') !== false) {
                $data['total_cash'] += $amt;
            } else {
                $data['total_bank'] += $amt;
            }
        }

        // Query Expenses in same period
        try {
            $db->query("SELECT SUM(amount) as total_exp FROM expenses WHERE date BETWEEN :from AND :to AND (school_id = :school_id OR school_id IS NULL)");
            $db->bind(':from', $data['from_date']);
            $db->bind(':to', $data['to_date']);
            $db->bind(':school_id', $schoolId);
            $expRow = $db->single();
            $data['total_expenses'] = (float)($expRow->total_exp ?? 0);
        } catch (Exception $e) {}

        $this->view('reports/finance', $data);
    }

    public function attendance(){
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;

        $data = [
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'date' => $_GET['date'] ?? date('Y-m-d'),
            'class_id' => $_GET['class_id'] ?? '',
            'attendance_type' => $_GET['attendance_type'] ?? 'All',
            'attendance' => [],
            'count_present' => 0,
            'count_absent' => 0,
            'count_late' => 0,
            'count_leave' => 0,
            'total_students' => 0
        ];

        $db = new Database;
        $sql = "SELECT sa.*, s.name, s.admission_no, s.roll_no, c.class_name, sec.section_name
                FROM student_attendance sa
                JOIN students s ON sa.student_id = s.id
                JOIN classes c ON s.class_id = c.id
                JOIN sections sec ON s.section_id = sec.id
                WHERE sa.date = :date AND (s.school_id = :school_id OR s.school_id IS NULL)";

        if (!empty($data['class_id'])) {
            $sql .= " AND s.class_id = :class_id";
        }
        if (!empty($data['attendance_type']) && $data['attendance_type'] !== 'All') {
            $sql .= " AND sa.attendance_type = :att_type";
        }

        $sql .= " ORDER BY c.class_name, sec.section_name, s.roll_no";

        $db->query($sql);
        $db->bind(':date', $data['date']);
        $db->bind(':school_id', $schoolId);
        if (!empty($data['class_id'])) {
            $db->bind(':class_id', $data['class_id']);
        }
        if (!empty($data['attendance_type']) && $data['attendance_type'] !== 'All') {
            $db->bind(':att_type', $data['attendance_type']);
        }

        $data['attendance'] = $db->resultSet() ?: [];

        foreach ($data['attendance'] as $item) {
            $data['total_students']++;
            $type = strtolower($item->attendance_type);
            if ($type === 'present') {
                $data['count_present']++;
            } elseif ($type === 'absent') {
                $data['count_absent']++;
            } elseif ($type === 'late') {
                $data['count_late']++;
            } else {
                $data['count_leave']++;
            }
        }

        $this->view('reports/attendance', $data);
    }

    public function exams(){
        $classModel = $this->model('SchoolClass');
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $db = new Database;

        // Fetch available exams
        $exams = [];
        try {
            $db->query("SELECT * FROM exams WHERE school_id = :school_id OR school_id IS NULL ORDER BY id DESC");
            $db->bind(':school_id', $schoolId);
            $exams = $db->resultSet() ?: [];
        } catch (Exception $e) {}

        $data = [
            'exams' => $exams,
            'classes' => $classModel->getClasses(),
            'exam_id' => $_GET['exam_id'] ?? ($exams[0]->id ?? ''),
            'class_id' => $_GET['class_id'] ?? '',
            'results' => [],
            'highest_score' => 0,
            'class_average' => 0,
            'pass_count' => 0,
            'fail_count' => 0,
            'total_entries' => 0
        ];

        if (!empty($data['exam_id'])) {
            $sql = "SELECT em.*, s.name as student_name, s.admission_no, s.roll_no, c.class_name, sub.name as subject_name
                    FROM exam_marks em
                    JOIN students s ON em.student_id = s.id
                    JOIN classes c ON s.class_id = c.id
                    JOIN subjects sub ON em.subject_id = sub.id
                    WHERE em.exam_id = :exam_id AND (s.school_id = :school_id OR s.school_id IS NULL)";
            
            if (!empty($data['class_id'])) {
                $sql .= " AND s.class_id = :class_id";
            }

            $sql .= " ORDER BY c.class_name, s.roll_no, sub.name";

            try {
                $db->query($sql);
                $db->bind(':exam_id', $data['exam_id']);
                $db->bind(':school_id', $schoolId);
                if (!empty($data['class_id'])) {
                    $db->bind(':class_id', $data['class_id']);
                }
                $data['results'] = $db->resultSet() ?: [];
            } catch (Exception $e) {}

            $totalMarksSum = 0;
            foreach ($data['results'] as $r) {
                $marks = (float)$r->marks_obtained;
                $data['total_entries']++;
                $totalMarksSum += $marks;
                if ($marks > $data['highest_score']) {
                    $data['highest_score'] = $marks;
                }
                if ($marks >= 33) {
                    $data['pass_count']++;
                } else {
                    $data['fail_count']++;
                }
            }
            if ($data['total_entries'] > 0) {
                $data['class_average'] = round($totalMarksSum / $data['total_entries'], 1);
            }
        }

        $this->view('reports/exams', $data);
    }

    public function staff(){
        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $settingModel = $this->model('SiteSetting');
        $currency = $settingModel->getSetting('currency_symbol', 'Rs.');
        $db = new Database;

        $data = [
            'department' => $_GET['department'] ?? '',
            'employment_type' => $_GET['employment_type'] ?? '',
            'status' => $_GET['status'] ?? 'Active',
            'staff_list' => [],
            'total_staff' => 0,
            'total_permanent' => 0,
            'total_visiting' => 0,
            'monthly_payroll_estimate' => 0,
            'currency' => $currency
        ];

        $sql = "SELECT s.*, u.name, u.email, u.role
                FROM staff s
                JOIN users u ON s.user_id = u.id
                WHERE (s.school_id = :school_id OR s.school_id IS NULL)";

        $params = [':school_id' => $schoolId];

        if (!empty($data['department'])) {
            $sql .= " AND s.department = :dept";
            $params[':dept'] = $data['department'];
        }
        if (!empty($data['employment_type'])) {
            $sql .= " AND s.employment_type = :etype";
            $params[':etype'] = $data['employment_type'];
        }
        if (!empty($data['status'])) {
            $sql .= " AND s.status = :status";
            $params[':status'] = $data['status'];
        }

        $sql .= " ORDER BY s.department, s.designation, u.name";

        try {
            $db->query($sql);
            foreach ($params as $k => $v) {
                $db->bind($k, $v);
            }
            $data['staff_list'] = $db->resultSet() ?: [];
        } catch (Exception $e) {}

        foreach ($data['staff_list'] as $st) {
            $data['total_staff']++;
            if (strcasecmp($st->employment_type, 'Visiting') === 0) {
                $data['total_visiting']++;
            } else {
                $data['total_permanent']++;
                $data['monthly_payroll_estimate'] += (float)$st->basic_salary;
            }
        }

        $this->view('reports/staff', $data);
    }
}
