<?php
// app/Controllers/PayrollController.php

class PayrollController extends Controller {
    public function __construct(){
        if (class_exists('AuthGuard')) {
            AuthGuard::requireSchoolContext();
            AuthGuard::requirePermission('manage_finance');
        }
        // CSRF protection for all state-changing POST operations
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && class_exists('AuthGuard')) {
            AuthGuard::verifyCSRF();
        }
    }

    public function index(){
        $payrollModel = $this->model('Payroll');
        $staffModel = $this->model('Staff');
        
        $month = isset($_GET['month']) ? $_GET['month'] : date('F');
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        
        $payslips = $payrollModel->getPayslips($month, $year);
        $allStaff = $staffModel->getStaffMembers();

        // Calculate summary KPIs
        $totalPayroll = 0;
        $paidCount = 0;
        $pendingCount = 0;
        $visitingCount = 0;

        foreach($payslips as $p){
            $totalPayroll += (float)$p->net_salary;
            if($p->status == 'Paid'){
                $paidCount++;
            } else {
                $pendingCount++;
            }
            if(strpos($p->employment_type ?? '', 'Visiting') !== false){
                $visitingCount++;
            }
        }

        $data = [
            'month' => $month,
            'year' => $year,
            'payslips' => $payslips,
            'staff_list' => $allStaff,
            'kpis' => [
                'total_amount' => $totalPayroll,
                'paid_count' => $paidCount,
                'pending_count' => $pendingCount,
                'visiting_count' => $visitingCount
            ]
        ];
        
        $this->view('payroll/index', $data);
    }
    
    public function create(){
        $payrollModel = $this->model('Payroll');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $month = trim($_POST['month'] ?? date('F'));
            $year = (int)($_POST['year'] ?? date('Y'));
            $staff_id = (int)($_POST['staff_id'] ?? 0);
            $lectures = (int)($_POST['lectures_delivered'] ?? 0);
            
            if($staff_id > 0){
                if($payrollModel->generatePayslip($staff_id, $month, $year, $lectures)){
                    header('Location: ' . URLROOT . '/payroll/index?month='.$month.'&year='.$year.'&success=generated');
                    exit;
                } else {
                    header('Location: ' . URLROOT . '/payroll/index?month='.$month.'&year='.$year.'&error=failed');
                    exit;
                }
            }
        }
        header('Location: ' . URLROOT . '/payroll/index');
        exit;
    }

    public function generateAll(){
        $payrollModel = $this->model('Payroll');
        $staffModel = $this->model('Staff');

        $month = trim($_POST['month'] ?? date('F'));
        $year = (int)($_POST['year'] ?? date('Y'));
        
        $allStaff = $staffModel->getStaffMembers();
        $generated = 0;

        foreach($allStaff as $stf){
            if($stf->status == 'Active'){
                $payrollModel->generatePayslip($stf->id, $month, $year);
                $generated++;
            }
        }

        header('Location: ' . URLROOT . '/payroll/index?month='.$month.'&year='.$year.'&success=all_generated&count='.$generated);
        exit;
    }

    public function structure($staff_id){
        $payrollModel = $this->model('Payroll');
        $staffModel = $this->model('Staff');

        $staff = $staffModel->getStaffById((int)$staff_id);
        if(!$staff){
            $staff = $staffModel->getStaffByUserId((int)$staff_id);
        }
        if(!$staff){
            die('Staff member not found.');
        }
         
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $basic = (float)($_POST['basic_salary'] ?? 0);
            $rate = (float)($_POST['lecture_rate'] ?? 0);
            $med = (float)($_POST['medical_allowance'] ?? 0);
            $rent = (float)($_POST['house_rent_allowance'] ?? 0);
            $conv = (float)($_POST['conveyance_allowance'] ?? 0);
            $otherEarn = (float)($_POST['other_allowance'] ?? 0);
            
            $tax = (float)($_POST['tax_deduction'] ?? 0);
            $pf = (float)($_POST['provident_fund'] ?? 0);
            $otherDeduc = (float)($_POST['other_deductions'] ?? 0);

            $data = [
                'staff_id' => $staff->id,
                'employment_type' => trim($_POST['employment_type'] ?? 'Permanent'),
                'basic_salary' => $basic,
                'lecture_rate' => $rate,
                'medical_allowance' => $med,
                'house_rent_allowance' => $rent,
                'conveyance_allowance' => $conv,
                'other_allowance' => $otherEarn,
                'tax_deduction' => $tax,
                'provident_fund' => $pf,
                'other_deductions' => $otherDeduc,
                'earnings' => $med + $rent + $conv + $otherEarn,
                'deductions' => $tax + $pf + $otherDeduc,
                'net_salary' => max(0, $basic + ($med + $rent + $conv + $otherEarn) - ($tax + $pf + $otherDeduc))
            ];

            $payrollModel->savePayroll($data);
            header('Location: ' . URLROOT . '/staff/profile/' . $staff->id . '?success=salary_updated');
            exit;
        } else {
            $data = [
                'staff' => $staff,
                'payroll' => $payrollModel->getStaffPayroll($staff->id)
            ];
            $this->view('payroll/structure', $data);
        }
    }

    public function pay($id){
        $payrollModel = $this->model('Payroll');
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $mode = trim($_POST['mode'] ?? 'Cash');
            $date = !empty($_POST['payment_date']) ? $_POST['payment_date'] : date('Y-m-d');
            $note = trim($_POST['note'] ?? 'Disbursed');

            $payrollModel->payPayslip((int)$id, $mode, $date, $note);
            header('Location: ' . URLROOT . '/payroll/index?success=paid');
            exit;
        }
        header('Location: ' . URLROOT . '/payroll/index');
        exit;
    }

    public function slip($id){
        $payrollModel = $this->model('Payroll');
        $settingModel = $this->model('Setting');
        $payslip = $payrollModel->getPayslipById((int)$id);
        if(!$payslip){
            die('Payslip voucher not found.');
        }

        $data = [
            'payslip' => $payslip,
            'settings' => $settingModel ? $settingModel->getSettings() : null
        ];

        $this->view('payroll/slip', $data);
    }
}
