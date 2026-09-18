<?php
class StudentsController extends Controller {
    public function __construct(){
         if (class_exists('AuthGuard')) {
             AuthGuard::requireSchoolContext();
             // Broad permission check for all functions. 
             // We can be more granular in specific methods if needed.
             AuthGuard::requirePermission('view_students');
         }
         // CSRF protection for all state-changing POST operations
         if ($_SERVER['REQUEST_METHOD'] === 'POST' && class_exists('AuthGuard')) {
             AuthGuard::verifyCSRF();
         }
    }

    public function index(){
        $studentModel = $this->model('Student');
        $classModel = $this->model('SchoolClass');
        $perPage = 25;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        
        $students = [];
        $search_type = '';
        $is_searched = false;
        $total_students = 0;
        $total_pages = 1;
        $keyword = trim($_REQUEST['keyword'] ?? $_GET['admission_no'] ?? '');
        $raw_class_id = $_REQUEST['class_id'] ?? '';
        $class_id = ($raw_class_id !== '' && $raw_class_id !== 'all') ? (int)$raw_class_id : $raw_class_id;
        $section_id = !empty($_REQUEST['section_id']) ? (int)$_REQUEST['section_id'] : null;
        
        $total_enrolled = $studentModel->countStudents();

        if (!empty($keyword)) {
            $search_type = 'keyword';
            $is_searched = true;
            $students = $studentModel->searchStudentsByKeyword($keyword);
            $total_students = count($students);
        } elseif ($raw_class_id === 'all') {
            $search_type = 'all';
            $is_searched = true;
            $students = $studentModel->getStudents();
            $total_students = count($students);
        } elseif (!empty($class_id)) {
            $search_type = 'class';
            $is_searched = true;
            if (!empty($section_id)) {
                $students = $studentModel->getStudentsByClassSection($class_id, $section_id);
            } else {
                $students = $studentModel->getStudentsByClass($class_id);
            }
            $total_students = count($students);
        } else {
            // Default view: 0 entries until class or filter is selected
            $search_type = '';
            $is_searched = false;
            $students = [];
            $total_students = 0;
            $total_pages = 0;
        }

        $settingModel = class_exists('SiteSetting') ? new SiteSetting() : null;
        $schoolSettings = $settingModel ? $settingModel->getAllSettings() : [];

        $data = [
            'students' => $students,
            'classes' => $classModel->getClasses(),
            'search_type' => $search_type,
            'is_searched' => $is_searched,
            'keyword' => $keyword,
            'class_id' => $class_id,
            'section_id' => $section_id,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_students' => $total_students,
            'total_enrolled' => $total_enrolled,
            'total_pages' => $total_pages,
            'settings' => $schoolSettings
        ];

        $this->view('students/index', $data);
    }

    public function ajaxGetSections($class_id){
        AuthGuard::requirePermission('view_students'); // or view_academics
        header('Content-Type: application/json; charset=utf-8');
        $sectionModel = $this->model('Section');
        $sections = $sectionModel->getSectionsByClassId($class_id);
        echo json_encode($sections);
        exit;
    }

    public function admission(){
        AuthGuard::requirePermission('manage_students');
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $sessionModel = $this->model('AcademicSession');
        $familyModel = $this->model('Family');

        $conflictEmail = $_SESSION['admission_error_email'] ?? ($_GET['email'] ?? '');
        $conflictUser = $_SESSION['admission_error_user'] ?? null;
        if (!$conflictUser && !empty($conflictEmail)) {
            $userModel = $this->model('User');
            $existing = $userModel->getUserByEmail($conflictEmail);
            if ($existing) {
                $conflictUser = [
                    'id' => $existing->id,
                    'name' => $existing->name,
                    'role' => $existing->role,
                    'email' => $existing->email
                ];
            }
        }

        $data = [
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'sessions' => $sessionModel->getSessions(),
            'current_session' => $sessionModel->getCurrentSession(),
            'families' => $familyModel->getFamilies(),
            'old_post' => $_SESSION['admission_old_post'] ?? [],
            'conflict_email' => $conflictEmail,
            'conflict_user' => $conflictUser
        ];

        unset($_SESSION['admission_old_post'], $_SESSION['admission_error_email'], $_SESSION['admission_error_user']);

        $this->view('students/admission', $data);
    }

    public function store(){
        AuthGuard::requirePermission('manage_students');
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW) ?? [];

            $userModel = $this->model('User');
            $studentModel = $this->model('Student');
            $familyModel = $this->model('Family');
            $plainPassword = !empty($_POST['password']) ? trim($_POST['password']) : '123456';

            $userData = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => password_hash($plainPassword, PASSWORD_DEFAULT),
                'role' => 'student'
            ];

            $existingUser = $userModel->getUserByEmail($userData['email']);
            if($existingUser){
                $_SESSION['admission_error_email'] = $userData['email'];
                $_SESSION['admission_error_user'] = [
                    'id' => $existingUser->id,
                    'name' => $existingUser->name,
                    'role' => $existingUser->role,
                    'email' => $existingUser->email
                ];
                $_SESSION['admission_old_post'] = $_POST;
                header('Location: ' . URLROOT . '/students/admission?error=email_exists&email=' . urlencode($userData['email']));
                exit;
            }

            // Register User
            if($userModel->register($userData)){
                Mailer::send(
                    $userData['email'],
                    'Your ' . SITENAME . ' student portal account is ready',
                    '<h2>Student portal account created</h2><p>Hello ' . htmlspecialchars($userData['name'], ENT_QUOTES, 'UTF-8') . ',</p><p><strong>Login:</strong> ' . htmlspecialchars($userData['email'], ENT_QUOTES, 'UTF-8') . '<br><strong>Temporary password:</strong> ' . htmlspecialchars($plainPassword, ENT_QUOTES, 'UTF-8') . '</p><p><a href="' . URLROOT . '/auth/login">Open Login Page</a></p><p>Please change your password after signing in.</p>',
                    "Student portal account created\nLogin: {$userData['email']}\nTemporary password: {$plainPassword}\nLogin URL: " . URLROOT . '/auth/login'
                );
                $newUser = $userModel->login($userData['email'], $plainPassword);
                
                // Admission & Roll Number
                $admission_no = trim($_POST['admission_no'] ?? '');
                if (empty($admission_no)) {
                    $settingModel = $this->model('SiteSetting');
                    $settings = $settingModel->getAllSettings();
                    $prefix = isset($settings['admission_prefix']) ? $settings['admission_prefix'] : 'STD';
                    $year = date('y');
                    $searchPrefix = "$prefix-$year-";
                    $lastStudent = $studentModel->getLastAdmissionNo($searchPrefix);
                    $newNo = $lastStudent ? (intval(str_replace($searchPrefix, '', $lastStudent->admission_no)) + 1) : 1;
                    $admission_no = $searchPrefix . str_pad($newNo, 3, '0', STR_PAD_LEFT);
                }

                $roll_no = trim($_POST['roll_no'] ?? '');
                if (empty($roll_no)) {
                    $lastRoll = $studentModel->getLastRollNo($_POST['class_id'], $_POST['section_id']);
                    $roll_no = str_pad($lastRoll + 1, 3, '0', STR_PAD_LEFT);
                }

                $parent_user_id = null;
                if(!empty($_POST['parent_email'])){
                    $parentUserObj = $userModel->getUserByEmail(trim($_POST['parent_email']));
                    if($parentUserObj && $parentUserObj->role == 'parent'){
                        $parent_user_id = $parentUserObj->id;
                    }
                }

                // Family & Sibling Auto-Detection
                $fatherCnic = trim($_POST['father_cnic'] ?? '');
                $fatherName = trim($_POST['father_name'] ?? '');
                $parentPhone = trim($_POST['parent_phone'] ?? '');
                $familyId = trim($_POST['family_id'] ?? '');

                if (empty($familyId) && !empty($fatherCnic)) {
                    $familyId = $familyModel->findOrCreateFamilyByCnic($fatherCnic, $fatherName, $parentPhone);
                }

                $studentData = [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'admission_no' => $admission_no,
                    'reg_no' => trim($_POST['reg_no'] ?? ''),
                    'roll_no' => $roll_no,
                    'class_id' => !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null,
                    'section_id' => !empty($_POST['section_id']) ? (int)$_POST['section_id'] : null,
                    'dob' => !empty($_POST['dob']) ? $_POST['dob'] : null,
                    'gender' => trim($_POST['gender'] ?? 'Male'),
                    'blood_group' => trim($_POST['blood_group'] ?? ''),
                    'bform_cnic' => trim($_POST['bform_cnic'] ?? ''),
                    'father_name' => $fatherName,
                    'father_cnic' => $fatherCnic,
                    'mother_name' => trim($_POST['mother_name'] ?? ''),
                    'guardian_name' => trim($_POST['guardian_name'] ?? ''),
                    'guardian_relation' => trim($_POST['guardian_relation'] ?? 'Father'),
                    'parent_phone' => $parentPhone,
                    'address' => trim($_POST['address'] ?? ''),
                    'previous_school' => trim($_POST['previous_school'] ?? ($_POST['prev_school_name'] ?? '')),
                    'admission_date' => !empty($_POST['admission_date']) ? $_POST['admission_date'] : date('Y-m-d'),
                    'status' => trim($_POST['status'] ?? 'Active'),
                    'family_id' => $familyId,
                    'concession_type' => trim($_POST['concession_type'] ?? 'None'),
                    'sibling_discount_percent' => !empty($_POST['sibling_discount_percent']) ? (float)$_POST['sibling_discount_percent'] : 0.00,
                    'custom_discount_amount' => !empty($_POST['custom_discount_amount']) ? (float)$_POST['custom_discount_amount'] : 0.00,
                    'academic_session_id' => !empty($_POST['academic_session_id']) ? (int)$_POST['academic_session_id'] : null,
                    'parent_user_id' => $parent_user_id,

                    // Pakistani Demographics
                    'is_fresh_admission' => isset($_POST['is_fresh_admission']) ? (int)$_POST['is_fresh_admission'] : 1,
                    'religion' => trim($_POST['religion'] ?? 'Islam'),
                    'nationality' => trim($_POST['nationality'] ?? 'Pakistani'),
                    'mother_tongue' => trim($_POST['mother_tongue'] ?? 'Urdu'),
                    'father_occupation' => trim($_POST['father_occupation'] ?? ''),
                    'father_income' => trim($_POST['father_income'] ?? ''),
                    'father_phone' => trim($_POST['father_phone'] ?? $parentPhone),
                    'mother_cnic' => trim($_POST['mother_cnic'] ?? ''),
                    'mother_occupation' => trim($_POST['mother_occupation'] ?? ''),
                    'guardian_cnic' => trim($_POST['guardian_cnic'] ?? ''),
                    'guardian_phone' => trim($_POST['guardian_phone'] ?? ''),
                    'guardian_occupation' => trim($_POST['guardian_occupation'] ?? ''),
                    'permanent_address' => trim($_POST['permanent_address'] ?? ($_POST['address'] ?? '')),
                    'city' => trim($_POST['city'] ?? ''),
                    'district' => trim($_POST['district'] ?? ''),
                    'tehsil' => trim($_POST['tehsil'] ?? ''),
                    'special_needs' => trim($_POST['special_needs'] ?? ''),

                    // Previous school / transfer credentials
                    'prev_school_name' => trim($_POST['prev_school_name'] ?? ($_POST['previous_school'] ?? '')),
                    'prev_school_city' => trim($_POST['prev_school_city'] ?? ''),
                    'prev_class' => trim($_POST['prev_class'] ?? ''),
                    'prev_medium' => trim($_POST['prev_medium'] ?? 'English'),
                    'slc_number' => trim($_POST['slc_number'] ?? ''),
                    'slc_date' => !empty($_POST['slc_date']) ? $_POST['slc_date'] : null,
                    'prev_board_roll_no' => trim($_POST['prev_board_roll_no'] ?? ''),
                    'prev_marks_obtained' => !empty($_POST['prev_marks_obtained']) ? (float)$_POST['prev_marks_obtained'] : 0.00,
                    'prev_total_marks' => !empty($_POST['prev_total_marks']) ? (float)$_POST['prev_total_marks'] : 0.00,
                    'prev_grade' => trim($_POST['prev_grade'] ?? ''),
                    'reason_for_leaving' => trim($_POST['reason_for_leaving'] ?? '')
                ];

                $newStudentId = $studentModel->registerStudent($studentData, $newUser->id);
                if($newStudentId){
                    // Auto-calculate & apply sibling discounts if family exists
                    if (!empty($familyId)) {
                        $familyModel->autoApplySiblingDiscount($familyId);
                    }
                    $_SESSION['flash_success'] = "Student '{$userData['name']}' admitted successfully! (Admission No: {$admission_no})";

                    // If user clicked 'Enrol & Print Official Form'
                    if (!empty($_POST['action_print'])) {
                        header('Location: ' . URLROOT . '/students/printAdmission/' . (int)$newStudentId . '?autoprint=1');
                        exit;
                    }

                    header('Location: ' . URLROOT . '/students/admission?success=admitted&student_id=' . (int)$newStudentId . '&admission_no=' . urlencode($admission_no));
                    exit;
                } else {
                    die('Student registration failed');
                }
            } else {
                die('User account registration failed');
            }
        } else {
            $this->admission();
        }
    }

    public function edit($id = null){
        AuthGuard::requirePermission('manage_students');
        if(!$id) {
            header('Location: ' . URLROOT . '/students/index');
            exit;
        }

        $studentModel = $this->model('Student');
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $sessionModel = $this->model('AcademicSession');
        $familyModel = $this->model('Family');

        $student = $studentModel->getStudentById((int)$id);
        if(!$student){
            die('Student record not found.');
        }

        $data = [
            'student' => $student,
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'sessions' => $sessionModel->getSessions(),
            'families' => $familyModel->getFamilies()
        ];

        $this->view('students/edit', $data);
    }

    public function update($id){
        AuthGuard::requirePermission('manage_students');
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW) ?? [];
            $studentModel = $this->model('Student');
            $familyModel = $this->model('Family');

            $familyId = trim($_POST['family_id'] ?? '');
            $fatherCnic = trim($_POST['father_cnic'] ?? '');
            if(empty($familyId) && !empty($fatherCnic)){
                $familyId = $familyModel->findOrCreateFamilyByCnic($fatherCnic, trim($_POST['father_name'] ?? ''), trim($_POST['parent_phone'] ?? ''));
            }

            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'roll_no' => trim($_POST['roll_no'] ?? ''),
                'reg_no' => trim($_POST['reg_no'] ?? ''),
                'class_id' => !empty($_POST['class_id']) ? (int)$_POST['class_id'] : null,
                'section_id' => !empty($_POST['section_id']) ? (int)$_POST['section_id'] : null,
                'dob' => !empty($_POST['dob']) ? $_POST['dob'] : null,
                'gender' => trim($_POST['gender'] ?? 'Male'),
                'blood_group' => trim($_POST['blood_group'] ?? ''),
                'bform_cnic' => trim($_POST['bform_cnic'] ?? ''),
                'father_name' => trim($_POST['father_name'] ?? ''),
                'father_cnic' => $fatherCnic,
                'mother_name' => trim($_POST['mother_name'] ?? ''),
                'guardian_name' => trim($_POST['guardian_name'] ?? ''),
                'guardian_relation' => trim($_POST['guardian_relation'] ?? ''),
                'parent_phone' => trim($_POST['parent_phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'previous_school' => trim($_POST['previous_school'] ?? ''),
                'admission_date' => !empty($_POST['admission_date']) ? $_POST['admission_date'] : null,
                'status' => trim($_POST['status'] ?? 'Active'),
                'family_id' => $familyId,
                'sibling_discount_percent' => !empty($_POST['sibling_discount_percent']) ? (float)$_POST['sibling_discount_percent'] : 0.00,
                'custom_discount_amount' => !empty($_POST['custom_discount_amount']) ? (float)$_POST['custom_discount_amount'] : 0.00,
                'concession_type' => trim($_POST['concession_type'] ?? 'None'),
                'academic_session_id' => !empty($_POST['academic_session_id']) ? (int)$_POST['academic_session_id'] : null
            ];

            $studentModel->updateStudent((int)$id, $data);

            if(!empty($familyId)){
                $familyModel->autoApplySiblingDiscount($familyId);
            }

            header('Location: ' . URLROOT . '/students/profile/' . (int)$id . '?success=updated');
            exit;
        }
        header('Location: ' . URLROOT . '/students/index');
        exit;
    }

    public function profile($id){
        AuthGuard::requirePermission('view_students');
        $studentModel = $this->model('Student');
        
        $profile360 = $studentModel->getStudent360((int)$id);
        if(!$profile360 || empty($profile360['student'])){
            die('Student record not found.');
        }

        $clearanceModel = $this->model('Clearance');
        $clearance = $clearanceModel->getClearanceByStudentId((int)$id);

        $data = [
            'student' => $profile360['student'],
            'profile' => $profile360,
            'clearance' => $clearance
        ];

        $this->view('students/profile', $data);
    }

    public function categories(){
        AuthGuard::requirePermission('manage_students');
        $catModel = $this->model('StudentCategory');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['delete_id'])){
                $catModel->deleteCategory($_POST['delete_id']);
            } else {
                 $catModel->addCategory($_POST['category_name']);
            }
            header('Location: ' . URLROOT . '/students/categories');
            exit;
        }

        $data = [
            'categories' => $catModel->getCategories()
        ];
        $this->view('students/categories', $data);
    }

    public function houses(){
        AuthGuard::requirePermission('manage_students');
        $houseModel = $this->model('StudentHouse');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['delete_id'])){
                $houseModel->deleteHouse($_POST['delete_id']);
            } else {
                 $data = [
                     'house_name' => $_POST['house_name'],
                     'description' => $_POST['description']
                 ];
                 $houseModel->addHouse($data);
            }
            header('Location: ' . URLROOT . '/students/houses');
            exit;
        }

        $data = [
            'houses' => $houseModel->getHouses()
        ];
        $this->view('students/houses', $data);
    }
    public function import(){
        AuthGuard::requirePermission('manage_students');
        // Get classes and sections for the reference table in view
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $classes = $classModel->getClasses();
        $sections = $sectionModel->getSections();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
                $file = $_FILES['file']['tmp_name'];
                $handle = fopen($file, "r");
                
                // Skip header row
                fgetcsv($handle);
                
                $userModel = $this->model('User');
                $studentModel = $this->model('Student');
                
                $count = 0;
                $errors = 0;

                while(($row = fgetcsv($handle)) !== false){
                    // CSV Order: Name, Email, Password, Admission No, Roll No, Class ID, Section ID, DOB (Y-m-d), Gender, Phone, Address
                    // Basic validation: check required fields
                    if(count($row) < 7) continue; 

                    $name = trim($row[0]);
                    $email = trim($row[1]);
                    $password = trim($row[2]);
                    // If password empty, use default
                    if(empty($password)) $password = '123456';

                    // Check duplicate email
                    if($userModel->findUserByEmail($email)){
                        $errors++;
                        continue;
                    }

                    // Create User
                    $userData = [
                        'name' => $name,
                        'email' => $email,
                        'password' => password_hash($password, PASSWORD_DEFAULT),
                        'role' => 'student'
                    ];

                    if($userModel->register($userData)){
                         // Login trick to get ID (optimize later)
                         $newUser = $userModel->login($email, $password);
                         
                         if($newUser){
                             $studentData = [
                                 'admission_no' => trim($row[3]),
                                 'roll_no' => trim($row[4]),
                                 'class_id' => trim($row[5]),
                                 'section_id' => trim($row[6]),
                                 'dob' => trim($row[7]),
                                 'gender' => trim($row[8]),
                                 'parent_phone' => trim($row[9]),
                                 'address' => trim($row[10])
                             ];
                             
                             if($studentModel->registerStudent($studentData, $newUser->id)){
                                 $count++;
                             } else {
                                 // cleanup user if student fail? For now just count error
                                 $errors++;
                             }
                         }
                    } else {
                        $errors++;
                    }
                }
                fclose($handle);
                
                // Redirect with message (using simple echo/exit or session flash if we had it)
                // For now redirect to index with query param
                header('Location: ' . URLROOT . '/students/index?msg=imported&count='.$count.'&errors='.$errors);
                exit;

            }
        }

        $data = [
            'classes' => $classes,
            'sections' => $sections
        ];
        $this->view('students/import', $data);
    }

    public function downloadSample(){
        AuthGuard::requirePermission('manage_students');
        $filename = "student_import_sample.csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        // Header Row
        fputcsv($output, ['Name', 'Email', 'Password', 'Admission No', 'Roll No', 'Class ID', 'Section ID', 'DOB (YYYY-MM-DD)', 'Gender', 'Phone', 'Address']);
        // Sample Row
        fputcsv($output, ['John Doe', 'john@example.com', '123456', 'ADM001', '101', '1', '1', '2010-01-01', 'Male', '1234567890', '123 Main St']);
        fclose($output);
        exit;
    }

    public function delete($id = null){
        if (class_exists('AuthGuard')) {
            AuthGuard::requirePermission('manage_students');
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($id)){
            $studentModel = $this->model('Student');
            if($studentModel->deleteStudent((int)$id)){
                header('Location: ' . URLROOT . '/students/index?msg=deleted&count=1');
                exit;
            }
        }
        header('Location: ' . URLROOT . '/students/index');
        exit;
    }

    public function bulkDelete(){
        if (class_exists('AuthGuard')) {
            AuthGuard::requirePermission('manage_students');
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $studentIds = $_POST['student_ids'] ?? [];
            if(!empty($studentIds) && is_array($studentIds)){
                $studentModel = $this->model('Student');
                $deletedCount = $studentModel->deleteStudents($studentIds);
                header('Location: ' . URLROOT . '/students/index?msg=bulk_deleted&count=' . $deletedCount);
                exit;
            }
        }
        header('Location: ' . URLROOT . '/students/index');
        exit;
    }

    public function printAdmission($id = null){
        if (class_exists('AuthGuard')) {
            AuthGuard::requirePermission('view_students');
        }

        $studentModel = $this->model('Student');
        $student = null;
        $isBlank = false;

        if (isset($_GET['blank']) && $_GET['blank'] == '1') {
            $isBlank = true;
        } elseif (!empty($id)) {
            $student = $studentModel->getStudentById((int)$id);
        } elseif (!empty($_GET['admission_no'])) {
            $student = $studentModel->getStudentByAdmissionNo(trim($_GET['admission_no']));
        } elseif (!empty($_GET['student_id'])) {
            $student = $studentModel->getStudentById((int)$_GET['student_id']);
        } else {
            // Default to blank physical registration form if no student specified
            $isBlank = true;
        }

        $settingModel = class_exists('SiteSetting') ? new SiteSetting() : null;
        $schoolSettings = $settingModel ? $settingModel->getAllSettings() : [];

        $classModel = $this->model('SchoolClass');
        $classes = $classModel ? $classModel->getClasses() : [];

        $customFields = !empty($schoolSettings['admission_custom_fields']) ? json_decode($schoolSettings['admission_custom_fields'], true) : [];
        $layoutOptions = !empty($schoolSettings['admission_layout_options']) ? json_decode($schoolSettings['admission_layout_options'], true) : [];

        $data = [
            'student' => $student,
            'is_blank' => $isBlank,
            'settings' => $schoolSettings,
            'classes' => $classes,
            'custom_fields' => $customFields,
            'layout_options' => $layoutOptions
        ];

        $this->view('students/print_admission', $data);
    }

    public function saveAdmissionFormSettings(){
        header('Content-Type: application/json');
        try {
            $settingModel = class_exists('SiteSetting') ? new SiteSetting() : null;
            if (!$settingModel) {
                echo json_encode(['success' => false, 'message' => 'Settings model not available']);
                return;
            }

            $rawInput = file_get_contents('php://input');
            $payload = json_decode($rawInput, true);

            if (!$payload && !empty($_POST)) {
                $payload = $_POST;
            }

            if (isset($payload['custom_fields'])) {
                $cfVal = is_string($payload['custom_fields']) ? $payload['custom_fields'] : json_encode($payload['custom_fields']);
                $settingModel->updateSetting('admission_custom_fields', $cfVal);
            }

            if (!empty($payload['branding']) && is_array($payload['branding'])) {
                foreach ($payload['branding'] as $k => $v) {
                    $settingModel->updateSetting($k, trim((string)$v));
                }
            }

            if (isset($payload['layout_options'])) {
                $loVal = is_string($payload['layout_options']) ? $payload['layout_options'] : json_encode($payload['layout_options']);
                $settingModel->updateSetting('admission_layout_options', $loVal);
            }

            echo json_encode(['success' => true, 'message' => 'Admission form settings & fields saved successfully!']);
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
