<?php
// app/Models/Payroll.php

class Payroll {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // --- Salary Structure ---
    public function getStaffPayroll($staff_id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM staff_payroll WHERE staff_id = :sid AND school_id = :school_id LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':sid', (int)$staff_id);
        return $this->db->single();
    }

    public function savePayroll($data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $existing = $this->getStaffPayroll($data['staff_id']);
        
        $basic = !empty($data['basic_salary']) ? (float)$data['basic_salary'] : 0.00;
        $rate = !empty($data['lecture_rate']) ? (float)$data['lecture_rate'] : 0.00;
        $med = !empty($data['medical_allowance']) ? (float)$data['medical_allowance'] : 0.00;
        $rent = !empty($data['house_rent_allowance']) ? (float)$data['house_rent_allowance'] : 0.00;
        $conv = !empty($data['conveyance_allowance']) ? (float)$data['conveyance_allowance'] : 0.00;
        $otherEarn = !empty($data['other_allowance']) ? (float)$data['other_allowance'] : 0.00;
        $totalEarn = $med + $rent + $conv + $otherEarn + (!empty($data['earnings']) ? (float)$data['earnings'] : 0.00);

        $tax = !empty($data['tax_deduction']) ? (float)$data['tax_deduction'] : 0.00;
        $pf = !empty($data['provident_fund']) ? (float)$data['provident_fund'] : 0.00;
        $otherDeduc = !empty($data['other_deductions']) ? (float)$data['other_deductions'] : 0.00;
        $totalDeduc = $tax + $pf + $otherDeduc + (!empty($data['deductions']) ? (float)$data['deductions'] : 0.00);

        $net = !empty($data['net_salary']) ? (float)$data['net_salary'] : max(0, $basic + $totalEarn - $totalDeduc);
        $etype = trim($data['employment_type'] ?? 'Permanent');

        if($existing){
            $sql = "UPDATE staff_payroll SET 
                        employment_type = :etype, basic_salary = :basic, lecture_rate = :rate,
                        medical_allowance = :med, house_rent_allowance = :rent, conveyance_allowance = :conv,
                        tax_deduction = :tax, provident_fund = :pf, other_deductions = :odeduc,
                        earnings = :earning, deductions = :deduc, net_salary = :net 
                    WHERE staff_id = :sid AND school_id = :school_id";
        } else {
            $sql = "INSERT INTO staff_payroll (
                        school_id, staff_id, employment_type, basic_salary, lecture_rate,
                        medical_allowance, house_rent_allowance, conveyance_allowance,
                        tax_deduction, provident_fund, other_deductions,
                        earnings, deductions, net_salary
                    ) VALUES (
                        :school_id, :sid, :etype, :basic, :rate,
                        :med, :rent, :conv,
                        :tax, :pf, :odeduc,
                        :earning, :deduc, :net
                    )";
        }
        
        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':sid', (int)$data['staff_id']);
        $this->db->bind(':etype', $etype);
        $this->db->bind(':basic', $basic);
        $this->db->bind(':rate', $rate);
        $this->db->bind(':med', $med);
        $this->db->bind(':rent', $rent);
        $this->db->bind(':conv', $conv);
        $this->db->bind(':tax', $tax);
        $this->db->bind(':pf', $pf);
        $this->db->bind(':odeduc', $otherDeduc);
        $this->db->bind(':earning', $totalEarn);
        $this->db->bind(':deduc', $totalDeduc);
        $this->db->bind(':net', $net);

        return $this->db->execute();
    }

    // --- Payslips ---
    public function getPayslips($month = null, $year = null){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        
        $sql = "SELECT sp.*, 
                       COALESCE(u.name, 'Staff Member') as name, 
                       COALESCE(u.role, 'staff') as role, 
                       u.email, 
                       s.staff_code, 
                       s.department, 
                       s.designation, 
                       s.employment_type as staff_etype, 
                       s.bank_name, 
                       s.bank_account_no
                FROM staff_payslips sp
                LEFT JOIN staff s ON sp.staff_id = s.id AND s.school_id = :school_id
                LEFT JOIN users u ON s.user_id = u.id
                WHERE sp.school_id = :school_id ";
                
        if($month && $year){
            $sql .= " AND sp.month = :m AND sp.year = :y";
        }
        
        $sql .= " GROUP BY sp.id ORDER BY sp.year DESC, sp.id DESC";
        
        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if($month && $year){
            $this->db->bind(':m', $month);
            $this->db->bind(':y', $year);
        }
        return $this->db->resultSet();
    }

    public function getPayslipById($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT sp.*, 
                                 COALESCE(u.name, 'Staff Member') as name, 
                                 COALESCE(u.role, 'staff') as role, 
                                 u.email, 
                                 s.staff_code, 
                                 s.department, 
                                 s.designation, 
                                 s.cnic, 
                                 s.phone, 
                                 s.bank_name, 
                                 s.bank_account_no
                          FROM staff_payslips sp
                          LEFT JOIN staff s ON sp.staff_id = s.id AND s.school_id = :school_id
                          LEFT JOIN users u ON s.user_id = u.id
                          WHERE sp.id = :id AND sp.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', (int)$id);
        return $this->db->single();
    }

    public function generatePayslip($staff_id, $month, $year, $deliveredLectures = 0){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        
        // Fetch staff profile & payroll structure
        $staffModel = new Staff();
        $staff = $staffModel->getStaffById($staff_id);
        if(!$staff) {
            $staff = $staffModel->getStaffByUserId($staff_id);
        }

        $structure = $this->getStaffPayroll($staff ? $staff->id : $staff_id);
        
        $etype = $staff ? $staff->employment_type : ($structure->employment_type ?? 'Permanent');
        $basic = (float)($structure->basic_salary ?? ($staff->basic_salary ?? 0));
        $rate = (float)($structure->lecture_rate ?? ($staff->lecture_rate ?? 0));
        $allowances = (float)($structure->earnings ?? 0);
        $deductions = (float)($structure->deductions ?? 0);

        // Auto Attendance-based Unpaid Leave Deductions (Absent & Half Days)
        $leaveDeduction = 0.00;
        $absentDaysCount = 0.0;
        $halfDaysCount = 0.0;
        try {
            $monthNum = date('m', strtotime($month . " 1 " . $year));
            $stfId = $staff ? $staff->id : $staff_id;
            $userId = $staff ? $staff->user_id : $staff_id;

            // Fetch Leave Cutting Settings from site_settings
            $this->db->query("SELECT setting_key, setting_value FROM site_settings 
                              WHERE (school_id = :sch OR school_id IS NULL) 
                                AND setting_key IN ('payroll_leave_cutting_enabled', 'payroll_free_leaves_per_month', 'payroll_absent_cutting_percent', 'payroll_half_day_cutting_percent')");
            $this->db->bind(':sch', $schoolId);
            $settingsRows = $this->db->resultSet();
            $pSettings = [];
            foreach ($settingsRows as $sr) {
                $pSettings[$sr->setting_key] = $sr->setting_value;
            }

            $leaveCuttingEnabled = ($pSettings['payroll_leave_cutting_enabled'] ?? '1') === '1';
            $allowedFreeLeaves = (float)($pSettings['payroll_free_leaves_per_month'] ?? 2);
            $absentPercent = (float)($pSettings['payroll_absent_cutting_percent'] ?? 100);
            $halfDayPercent = (float)($pSettings['payroll_half_day_cutting_percent'] ?? 50);

            $this->db->query("SELECT 
                                SUM(CASE WHEN attendance_type = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                                SUM(CASE WHEN attendance_type = 'Half Day' THEN 1 ELSE 0 END) as half_days
                              FROM staff_attendance 
                              WHERE (staff_id = :sid OR staff_id = :uid)
                                AND MONTH(date) = :m AND YEAR(date) = :y 
                                AND school_id = :sch");
            $this->db->bind(':sid', $stfId);
            $this->db->bind(':uid', $userId);
            $this->db->bind(':m', $monthNum);
            $this->db->bind(':y', $year);
            $this->db->bind(':sch', $schoolId);
            $attSummary = $this->db->single();

            if ($attSummary && strpos($etype, 'Visiting') === false && $leaveCuttingEnabled) {
                $absentDaysCount = (float)($attSummary->absent_days ?? 0);
                $halfDaysCount = (float)($attSummary->half_days ?? 0);

                // Deduct only after exhausting allowed monthly free leaves
                $chargeableAbsents = max(0, $absentDaysCount - $allowedFreeLeaves);
                $chargeableHalfDays = $halfDaysCount;

                $countableUnpaidUnits = ($chargeableAbsents * ($absentPercent / 100)) + ($chargeableHalfDays * ($halfDayPercent / 100));

                if ($countableUnpaidUnits > 0 && $basic > 0) {
                    $dailyRate = $basic / 30; // Standard 30-day salary basis
                    $leaveDeduction = round($countableUnpaidUnits * $dailyRate, 2);
                    $deductions += $leaveDeduction;
                }
            }
        } catch(Exception $e) {
            error_log("Payroll attendance auto-deduction notice: " . $e->getMessage());
        }

        // If Visiting faculty, compute based on delivered lectures
        if (strpos($etype, 'Visiting') !== false) {
            if ($deliveredLectures <= 0) {
                // Auto calculate from workload timetable (weekly periods * 4 weeks)
                $workload = $staffModel->getStaff360($staff ? $staff->id : $staff_id);
                $deliveredLectures = $workload ? $workload['monthly_visiting_lectures'] : 16;
            }
            $basic = $deliveredLectures * $rate;
        }

        $netSalary = max(0, $basic + $allowances - $deductions);

        // Check if already generated for this staff, month, year
        $this->db->query("SELECT id FROM staff_payslips WHERE staff_id = :sid AND month = :m AND year = :y AND school_id = :sch LIMIT 1");
        $this->db->bind(':sid', $staff ? $staff->id : $staff_id);
        $this->db->bind(':m', $month);
        $this->db->bind(':y', $year);
        $this->db->bind(':sch', $schoolId);
        $existing = $this->db->single();

        if ($existing) {
            // Update existing
            $sql = "UPDATE staff_payslips SET 
                        employment_type = :etype, lectures_delivered = :deliv, lecture_rate = :rate,
                        basic_salary = :basic, total_allowance = :allow, total_deduction = :deduc, 
                        net_salary = :net, leave_deduction = :ldeduc, absent_days = :abdays, half_days = :hdays
                    WHERE id = :id AND school_id = :sch";
            $this->db->query($sql);
            $this->db->bind(':id', $existing->id);
            $this->db->bind(':sch', $schoolId);
        } else {
            $sql = "INSERT INTO staff_payslips (
                        school_id, staff_id, employment_type, lectures_delivered, lecture_rate,
                        month, year, basic_salary, total_allowance, total_deduction, net_salary, 
                        leave_deduction, absent_days, half_days, status
                    ) VALUES (
                        :sch, :sid, :etype, :deliv, :rate,
                        :m, :y, :basic, :allow, :deduc, :net, 
                        :ldeduc, :abdays, :hdays, 'Generated'
                    )";
            $this->db->query($sql);
            $this->db->bind(':sch', $schoolId);
            $this->db->bind(':sid', $staff ? $staff->id : $staff_id);
            $this->db->bind(':m', $month);
            $this->db->bind(':y', $year);
        }

        $this->db->bind(':etype', $etype);
        $this->db->bind(':deliv', (int)$deliveredLectures);
        $this->db->bind(':rate', $rate);
        $this->db->bind(':basic', $basic);
        $this->db->bind(':allow', $allowances);
        $this->db->bind(':deduc', $deductions);
        $this->db->bind(':net', $netSalary);
        $this->db->bind(':ldeduc', $leaveDeduction);
        $this->db->bind(':abdays', $absentDaysCount);
        $this->db->bind(':hdays', $halfDaysCount);
        
        return $this->db->execute();
    }
    
    public function payPayslip($id, $mode, $date, $note){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $payslip = $this->getPayslipById($id);
        if (!$payslip) return false;

        $paymentDate = !empty($date) ? $date : date('Y-m-d');
        $paymentMode = !empty($mode) ? $mode : 'Cash';

        // 1. Accounting Auto-Sync: Create Expense Record
        $expenseId = null;
        try {
            // Find or create 'Staff Salary' Expense Head
            $this->db->query("SELECT id FROM expense_heads WHERE school_id = :sch AND (exp_category LIKE '%Salary%' OR exp_category LIKE '%Payroll%') LIMIT 1");
            $this->db->bind(':sch', $schoolId);
            $head = $this->db->single();
            $headId = $head ? $head->id : null;

            if(!$headId) {
                $this->db->query("INSERT INTO expense_heads (school_id, exp_category, description) VALUES (:sch, 'Staff Salary & Payroll', 'Faculty and staff salary disbursements')");
                $this->db->bind(':sch', $schoolId);
                $this->db->execute();
                $headId = $this->db->lastInsertId();
            }

            // Insert into expenses
            $expName = "Staff Salary: " . ($payslip->name ?? 'Staff') . " (" . $payslip->month . " " . $payslip->year . ")";
            $invNo = "PAY-" . str_pad($payslip->id, 5, '0', STR_PAD_LEFT);
            $expDesc = "Disbursed via " . $paymentMode . ($note ? " - " . $note : "");

            $this->db->query("INSERT INTO expenses (school_id, exp_head_id, name, invoice_no, date, amount, description)
                              VALUES (:sch, :hid, :name, :inv, :date, :amt, :desc)");
            $this->db->bind(':sch', $schoolId);
            $this->db->bind(':hid', $headId);
            $this->db->bind(':name', $expName);
            $this->db->bind(':inv', $invNo);
            $this->db->bind(':date', $paymentDate);
            $this->db->bind(':amt', (float)$payslip->net_salary);
            $this->db->bind(':desc', $expDesc);
            $this->db->execute();
            $expenseId = $this->db->lastInsertId();
        } catch(Exception $e) {
            error_log("Payroll expense auto-sync notice: " . $e->getMessage());
        }

        // 2. Mark Payslip as Paid
        $this->db->query("UPDATE staff_payslips SET 
                            status = 'Paid', payment_mode = :mode, payment_date = :date, 
                            note = :note, expense_id = :exp_id 
                          WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':mode', $paymentMode);
        $this->db->bind(':date', $paymentDate);
        $this->db->bind(':note', trim($note ?? 'Disbursed'));
        $this->db->bind(':exp_id', $expenseId);
        return $this->db->execute();
    }
}
