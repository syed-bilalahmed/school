<?php
// app/Controllers/StaffController.php

class StaffController extends Controller {
    public function __construct(){
        if (class_exists('AuthGuard')) {
            AuthGuard::requireSchoolContext();
            AuthGuard::requirePermission('view_staff');
        }
        // CSRF protection for all state-changing POST operations
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && class_exists('AuthGuard')) {
            AuthGuard::verifyCSRF();
        }
    }

    public function index(){
        $staffModel = $this->model('Staff');
        
        $dept = $_GET['dept'] ?? null;
        $role = $_GET['role'] ?? null;
        $etype = $_GET['etype'] ?? null;

        $staffMembers = $staffModel->getStaffMembers($dept, $role, $etype);

        $data = [
            'staff' => $staffMembers,
            'current_dept' => $dept,
            'current_role' => $role,
            'current_etype' => $etype
        ];

        $this->view('staff/index', $data);
    }

    public function create(){
        if (class_exists('AuthGuard')) {
            AuthGuard::requirePermission('manage_staff');
        }
        $this->view('staff/create');
    }

    public function add(){
        if (class_exists('AuthGuard')) {
            AuthGuard::requirePermission('manage_staff');
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW) ?? [];

            $plainPassword = !empty($_POST['password']) ? trim($_POST['password']) : '123456';

            $userData = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => password_hash($plainPassword, PASSWORD_DEFAULT),
                'role' => trim($_POST['role'] ?? 'teacher')
            ];

            $staffData = [
                'staff_code' => trim($_POST['staff_code'] ?? ''),
                'cnic' => trim($_POST['cnic'] ?? ''),
                'department' => trim($_POST['department'] ?? 'Academics'),
                'designation' => trim($_POST['designation'] ?? 'Teacher'),
                'employment_type' => trim($_POST['employment_type'] ?? 'Permanent'),
                'qualification' => trim($_POST['qualification'] ?? ''),
                'experience_years' => trim($_POST['experience_years'] ?? ''),
                'gender' => trim($_POST['gender'] ?? 'Male'),
                'dob' => !empty($_POST['dob']) ? $_POST['dob'] : null,
                'phone' => trim($_POST['phone'] ?? ''),
                'emergency_contact' => trim($_POST['emergency_contact'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'date_of_joining' => !empty($_POST['date_of_joining']) ? $_POST['date_of_joining'] : date('Y-m-d'),
                'basic_salary' => !empty($_POST['basic_salary']) ? (float)$_POST['basic_salary'] : 0.00,
                'lecture_rate' => !empty($_POST['lecture_rate']) ? (float)$_POST['lecture_rate'] : 0.00,
                'bank_name' => trim($_POST['bank_name'] ?? ''),
                'bank_account_no' => trim($_POST['bank_account_no'] ?? ''),
                'status' => trim($_POST['status'] ?? 'Active')
            ];

            $staffModel = $this->model('Staff');
            $staffId = $staffModel->createStaff($userData, $staffData);

            if($staffId){
                Mailer::send(
                    $userData['email'],
                    'Your ' . SITENAME . ' staff account is ready',
                    '<h2>Staff portal account created</h2><p>Hello ' . htmlspecialchars($userData['name'], ENT_QUOTES, 'UTF-8') . ',</p><p><strong>Login:</strong> ' . htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8') . '<br><strong>Temporary password:</strong> ' . htmlspecialchars($plainPassword, ENT_QUOTES, 'UTF-8') . '</p><p><a href="' . URLROOT . '/auth/login">Open Login Page</a></p><p>Please change your password after signing in.</p>',
                    "Staff portal account created\nLogin: {$userData['email']}\nTemporary password: {$plainPassword}\nLogin URL: " . URLROOT . '/auth/login'
                );
                header('Location: ' . URLROOT . '/staff/profile/' . $staffId . '?success=created');
                exit;
            } else {
                header('Location: ' . URLROOT . '/staff/create?error=email_exists');
                exit;
            }
        } else {
            $this->create();
        }
    }

    public function edit($id){
        if (class_exists('AuthGuard')) {
            AuthGuard::requirePermission('manage_staff');
        }
        $staffModel = $this->model('Staff');
        $staff = $staffModel->getStaffById((int)$id);
        if(!$staff){
            die('Staff member not found');
        }

        $data = [
            'staff' => $staff
        ];
        $this->view('staff/edit', $data);
    }

    public function update($id){
        if (class_exists('AuthGuard')) {
            AuthGuard::requirePermission('manage_staff');
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW) ?? [];

            $staffModel = $this->model('Staff');

            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'role' => trim($_POST['role'] ?? 'teacher'),
                'cnic' => trim($_POST['cnic'] ?? ''),
                'department' => trim($_POST['department'] ?? 'Academics'),
                'designation' => trim($_POST['designation'] ?? 'Teacher'),
                'employment_type' => trim($_POST['employment_type'] ?? 'Permanent'),
                'qualification' => trim($_POST['qualification'] ?? ''),
                'experience_years' => trim($_POST['experience_years'] ?? ''),
                'gender' => trim($_POST['gender'] ?? 'Male'),
                'dob' => !empty($_POST['dob']) ? $_POST['dob'] : null,
                'phone' => trim($_POST['phone'] ?? ''),
                'emergency_contact' => trim($_POST['emergency_contact'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'date_of_joining' => !empty($_POST['date_of_joining']) ? $_POST['date_of_joining'] : null,
                'basic_salary' => !empty($_POST['basic_salary']) ? (float)$_POST['basic_salary'] : 0.00,
                'lecture_rate' => !empty($_POST['lecture_rate']) ? (float)$_POST['lecture_rate'] : 0.00,
                'bank_name' => trim($_POST['bank_name'] ?? ''),
                'bank_account_no' => trim($_POST['bank_account_no'] ?? ''),
                'status' => trim($_POST['status'] ?? 'Active')
            ];

            $staffModel->updateStaff((int)$id, $data);
            header('Location: ' . URLROOT . '/staff/profile/' . (int)$id . '?success=updated');
            exit;
        } else {
            header('Location: ' . URLROOT . '/staff/index');
            exit;
        }
    }

    public function profile($id){
        $staffModel = $this->model('Staff');
        $staff360 = $staffModel->getStaff360((int)$id);
        if(!$staff360 || empty($staff360['staff'])){
            die('Staff member profile not found');
        }

        $data = [
            'staff' => $staff360['staff'],
            'profile' => $staff360
        ];

        $this->view('staff/profile', $data);
    }

    public function delete($id){
        if (class_exists('AuthGuard')) {
            AuthGuard::requirePermission('manage_staff');
        }
        $staffModel = $this->model('Staff');
        $staffModel->deleteStaff((int)$id);
        header('Location: ' . URLROOT . '/staff/index?success=deleted');
        exit;
    }
}
