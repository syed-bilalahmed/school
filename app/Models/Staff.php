<?php
// app/Models/Staff.php

class Staff {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function getStaffMembers($department = null, $role = null, $employmentType = null){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        
        $sql = "SELECT s.*, u.name, u.email, u.role, sp.net_salary, sp.basic_salary as payroll_basic
                FROM staff s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN staff_payroll sp ON s.id = sp.staff_id AND sp.school_id = :school_id
                WHERE s.school_id = :school_id";
        
        if (!empty($department)) {
            $sql .= " AND s.department = :dept";
        }
        if (!empty($role)) {
            $sql .= " AND u.role = :role";
        }
        if (!empty($employmentType)) {
            $sql .= " AND s.employment_type = :etype";
        }

        $sql .= " ORDER BY s.id DESC";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if (!empty($department)) $this->db->bind(':dept', $department);
        if (!empty($role)) $this->db->bind(':role', $role);
        if (!empty($employmentType)) $this->db->bind(':etype', $employmentType);

        return $this->db->resultSet();
    }

    public function getStaffById($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.*, u.name, u.email, u.role 
                          FROM staff s
                          JOIN users u ON s.user_id = u.id
                          WHERE s.id = :id AND s.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', (int)$id);
        return $this->db->single();
    }

    public function getStaffByUserId($userId){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.*, u.name, u.email, u.role 
                          FROM staff s
                          JOIN users u ON s.user_id = u.id
                          WHERE s.user_id = :uid AND s.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':uid', (int)$userId);
        return $this->db->single();
    }

    public function generateStaffCode($role = 'teacher'){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $prefix = ($role == 'teacher') ? 'TCH' : 'STF';
        $year = date('y');
        $search = "$prefix-$year-";

        $this->db->query("SELECT staff_code FROM staff WHERE school_id = :sch AND staff_code LIKE :prefix ORDER BY id DESC LIMIT 1");
        $this->db->bind(':sch', $schoolId);
        $this->db->bind(':prefix', $search . '%');
        $last = $this->db->single();

        if ($last) {
            $lastNum = intval(str_replace($search, '', $last->staff_code));
            return $search . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
        } else {
            return $search . "001";
        }
    }

    public function createStaff($userData, $staffData){
        $schoolId = TenantContext::getSchoolId() ?: 1;

        // 1. Create or fetch User
        $userModel = new User();
        if ($userModel->findUserByEmail($userData['email'])) {
            return false; // Email duplicate
        }

        if (!$userModel->register($userData)) {
            return false;
        }

        // Fetch new user ID
        $newUser = $userModel->getUserByEmail($userData['email']);
        if (!$newUser) return false;
        $userId = $newUser->id;

        // 2. Generate Staff Code if blank
        $staffCode = !empty($staffData['staff_code']) ? trim($staffData['staff_code']) : $this->generateStaffCode($userData['role']);

        // 3. Insert into staff table
        $sql = "INSERT INTO staff (
                    school_id, user_id, staff_code, cnic, department, designation, employment_type,
                    qualification, experience_years, gender, dob, phone, emergency_contact, address,
                    date_of_joining, basic_salary, lecture_rate, bank_name, bank_account_no, status
                ) VALUES (
                    :school_id, :user_id, :staff_code, :cnic, :department, :designation, :employment_type,
                    :qualification, :experience_years, :gender, :dob, :phone, :emergency_contact, :address,
                    :date_of_joining, :basic_salary, :lecture_rate, :bank_name, :bank_account_no, :status
                )";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':staff_code', $staffCode);
        $this->db->bind(':cnic', trim($staffData['cnic'] ?? ''));
        $this->db->bind(':department', trim($staffData['department'] ?? 'Academics'));
        $this->db->bind(':designation', trim($staffData['designation'] ?? 'Teacher'));
        $this->db->bind(':employment_type', trim($staffData['employment_type'] ?? 'Permanent'));
        $this->db->bind(':qualification', trim($staffData['qualification'] ?? ''));
        $this->db->bind(':experience_years', trim($staffData['experience_years'] ?? ''));
        $this->db->bind(':gender', trim($staffData['gender'] ?? 'Male'));
        $this->db->bind(':dob', !empty($staffData['dob']) ? $staffData['dob'] : null);
        $this->db->bind(':phone', trim($staffData['phone'] ?? ''));
        $this->db->bind(':emergency_contact', trim($staffData['emergency_contact'] ?? ''));
        $this->db->bind(':address', trim($staffData['address'] ?? ''));
        $this->db->bind(':date_of_joining', !empty($staffData['date_of_joining']) ? $staffData['date_of_joining'] : date('Y-m-d'));
        $this->db->bind(':basic_salary', !empty($staffData['basic_salary']) ? (float)$staffData['basic_salary'] : 0.00);
        $this->db->bind(':lecture_rate', !empty($staffData['lecture_rate']) ? (float)$staffData['lecture_rate'] : 0.00);
        $this->db->bind(':bank_name', trim($staffData['bank_name'] ?? ''));
        $this->db->bind(':bank_account_no', trim($staffData['bank_account_no'] ?? ''));
        $this->db->bind(':status', trim($staffData['status'] ?? 'Active'));

        $inserted = $this->db->execute();
        if ($inserted) {
            $staffId = $this->db->lastInsertId();

            // Auto-initialize standard Payroll Structure
            $basic = !empty($staffData['basic_salary']) ? (float)$staffData['basic_salary'] : 0.00;
            $rate = !empty($staffData['lecture_rate']) ? (float)$staffData['lecture_rate'] : 0.00;
            $etype = trim($staffData['employment_type'] ?? 'Permanent');

            $payrollSql = "INSERT INTO staff_payroll (
                                school_id, staff_id, employment_type, basic_salary, lecture_rate, 
                                earnings, deductions, net_salary
                            ) VALUES (
                                :sch, :sid, :etype, :basic, :rate, 0.00, 0.00, :net
                            )";
            $this->db->query($payrollSql);
            $this->db->bind(':sch', $schoolId);
            $this->db->bind(':sid', $staffId);
            $this->db->bind(':etype', $etype);
            $this->db->bind(':basic', $basic);
            $this->db->bind(':rate', $rate);
            $this->db->bind(':net', $basic);
            $this->db->execute();

            return $staffId;
        }

        return false;
    }

    public function updateStaff($id, $data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $staff = $this->getStaffById($id);
        if (!$staff) return false;

        // Update User info (name, email, role)
        if (!empty($data['name']) || !empty($data['email']) || !empty($data['role'])) {
            $this->db->query("UPDATE users SET name = :name, email = :email, role = :role WHERE id = :uid");
            $this->db->bind(':name', trim($data['name'] ?? $staff->name));
            $this->db->bind(':email', trim($data['email'] ?? $staff->email));
            $this->db->bind(':role', trim($data['role'] ?? $staff->role));
            $this->db->bind(':uid', $staff->user_id);
            $this->db->execute();
        }

        // Update Staff table
        $sql = "UPDATE staff SET
                    cnic = :cnic, department = :department, designation = :designation,
                    employment_type = :employment_type, qualification = :qualification,
                    experience_years = :experience_years, gender = :gender, dob = :dob,
                    phone = :phone, emergency_contact = :emergency_contact, address = :address,
                    date_of_joining = :date_of_joining, basic_salary = :basic_salary,
                    lecture_rate = :lecture_rate, bank_name = :bank_name,
                    bank_account_no = :bank_account_no, status = :status
                WHERE id = :id AND school_id = :school_id";

        $this->db->query($sql);
        $this->db->bind(':cnic', trim($data['cnic'] ?? ''));
        $this->db->bind(':department', trim($data['department'] ?? 'Academics'));
        $this->db->bind(':designation', trim($data['designation'] ?? 'Teacher'));
        $this->db->bind(':employment_type', trim($data['employment_type'] ?? 'Permanent'));
        $this->db->bind(':qualification', trim($data['qualification'] ?? ''));
        $this->db->bind(':experience_years', trim($data['experience_years'] ?? ''));
        $this->db->bind(':gender', trim($data['gender'] ?? 'Male'));
        $this->db->bind(':dob', !empty($data['dob']) ? $data['dob'] : null);
        $this->db->bind(':phone', trim($data['phone'] ?? ''));
        $this->db->bind(':emergency_contact', trim($data['emergency_contact'] ?? ''));
        $this->db->bind(':address', trim($data['address'] ?? ''));
        $this->db->bind(':date_of_joining', !empty($data['date_of_joining']) ? $data['date_of_joining'] : null);
        $this->db->bind(':basic_salary', !empty($data['basic_salary']) ? (float)$data['basic_salary'] : 0.00);
        $this->db->bind(':lecture_rate', !empty($data['lecture_rate']) ? (float)$data['lecture_rate'] : 0.00);
        $this->db->bind(':bank_name', trim($data['bank_name'] ?? ''));
        $this->db->bind(':bank_account_no', trim($data['bank_account_no'] ?? ''));
        $this->db->bind(':status', trim($data['status'] ?? 'Active'));
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);

        return $this->db->execute();
    }

    public function deleteStaff($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $staff = $this->getStaffById($id);
        if (!$staff) return false;

        // Mark as Terminated or Resigned for audit compliance
        $this->db->query("UPDATE staff SET status = 'Resigned' WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    public function getStaff360($id){
        $staff = $this->getStaffById($id);
        if (!$staff) return null;

        $schoolId = TenantContext::getSchoolId() ?: 1;

        // 1. Teacher Course Allocation & Workload (if role == teacher)
        $allocations = [];
        $totalPeriodsPerWeek = 0;
        if ($staff->role == 'teacher' || $staff->employment_type == 'Visiting / Per Lecture') {
            $this->db->query("SELECT cs.id as class_subject_id, cs.periods_per_week,
                                     c.class_name, sec.section_name, COALESCE(s.subject_name, s.name) as subject_name, COALESCE(s.subject_code, s.code) as subject_code, s.is_core
                              FROM class_subjects cs
                              JOIN classes c ON cs.class_id = c.id
                              JOIN sections sec ON cs.section_id = sec.id
                              JOIN subjects s ON cs.subject_id = s.id
                              WHERE cs.teacher_id = :uid AND c.school_id = :school_id
                              ORDER BY c.class_name, sec.section_name");
            $this->db->bind(':uid', $staff->user_id);
            $this->db->bind(':school_id', $schoolId);
            $allocations = $this->db->resultSet();

            foreach ($allocations as $a) {
                $totalPeriodsPerWeek += (int)($a->periods_per_week ?: 5);
            }
        }

        // 2. Attendance Summary
        $this->db->query("SELECT 
                            COUNT(*) as total_days,
                            SUM(CASE WHEN attendance_type = 'Present' THEN 1 ELSE 0 END) as present_days,
                            SUM(CASE WHEN attendance_type = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                            SUM(CASE WHEN attendance_type = 'Late' THEN 1 ELSE 0 END) as late_days
                          FROM staff_attendance 
                          WHERE staff_id = :uid");
        $this->db->bind(':uid', $staff->user_id);
        $attSummary = $this->db->single();

        $this->db->query("SELECT date, attendance_type, remark FROM staff_attendance WHERE staff_id = :uid ORDER BY date DESC LIMIT 15");
        $this->db->bind(':uid', $staff->user_id);
        $attLogs = $this->db->resultSet();

        // 3. Payroll Structure & Payslips History
        $this->db->query("SELECT * FROM staff_payroll WHERE staff_id = :sid AND school_id = :school_id LIMIT 1");
        $this->db->bind(':sid', $staff->id);
        $this->db->bind(':school_id', $schoolId);
        $structure = $this->db->single();

        $this->db->query("SELECT * FROM staff_payslips WHERE staff_id = :sid AND school_id = :school_id ORDER BY year DESC, id DESC");
        $this->db->bind(':sid', $staff->id);
        $this->db->bind(':school_id', $schoolId);
        $payslips = $this->db->resultSet();

        return [
            'staff' => $staff,
            'allocations' => $allocations,
            'total_periods_per_week' => $totalPeriodsPerWeek,
            'monthly_visiting_lectures' => $totalPeriodsPerWeek * 4, // 4 weeks in month
            'attendance' => [
                'summary' => $attSummary,
                'logs' => $attLogs,
                'percentage' => ($attSummary && $attSummary->total_days > 0) ? round(($attSummary->present_days / $attSummary->total_days) * 100, 1) : 100
            ],
            'payroll' => [
                'structure' => $structure,
                'payslips' => $payslips
            ]
        ];
    }
}
