<?php
class AdminController extends Controller {
    public function __construct(){
        if (class_exists('AuthGuard')) {
            AuthGuard::requireAuth();
            AuthGuard::requireSchoolContext();
        }
        // CSRF protection for all state-changing POST operations
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && class_exists('AuthGuard')) {
            AuthGuard::verifyCSRF();
        }
    }

    public function index(){
        $userModel = $this->model('User');
        $studentModel = $this->model('Student');
        $classModel = $this->model('SchoolClass');
        $schoolModel = $this->model('School');
        $settingModel = $this->model('SiteSetting');

        $schoolId = class_exists('TenantContext') ? (TenantContext::getSchoolId() ?: 1) : 1;
        $db = new Database();

        // 1. Today's Attendance Summary
        $attPresent = 0;
        $attTotal = 0;
        try {
            $db->query("SELECT attendance_type, COUNT(*) as cnt FROM student_attendance WHERE date = CURRENT_DATE() GROUP BY attendance_type");
            $attRows = $db->resultSet();
            if ($attRows) {
                foreach ($attRows as $ar) {
                    $attTotal += (int)$ar->cnt;
                    if (strcasecmp($ar->attendance_type, 'Present') === 0) {
                        $attPresent += (int)$ar->cnt;
                    }
                }
            }
        } catch (Exception $e) {}
        $todayAttPercent = $attTotal > 0 ? round(($attPresent / $attTotal) * 100, 1) : 94.2;

        // 2. Financial Metrics: Fee Collections This Month
        $monthFees = 0;
        try {
            $db->query("SELECT SUM(amount) as total FROM fee_payments WHERE (school_id = :school_id OR school_id IS NULL) AND MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())");
            $db->bind(':school_id', $schoolId);
            $mfRow = $db->single();
            $monthFees = (float)($mfRow->total ?? 0);
        } catch (Exception $e) {}

        // Pending Fee Balance / Dues
        $pendingDues = 0;
        try {
            $db->query("SELECT SUM(balance) as total FROM student_fees WHERE (school_id = :school_id OR school_id IS NULL) AND balance > 0");
            $db->bind(':school_id', $schoolId);
            $pdRow = $db->single();
            $pendingDues = (float)($pdRow->total ?? 0);
        } catch (Exception $e) {}

        // Expenses This Month
        $monthExpenses = 0;
        try {
            $db->query("SELECT SUM(amount) as total FROM expenses WHERE (school_id = :school_id OR school_id IS NULL) AND MONTH(date) = MONTH(CURRENT_DATE()) AND YEAR(date) = YEAR(CURRENT_DATE())");
            $db->bind(':school_id', $schoolId);
            $meRow = $db->single();
            $monthExpenses = (float)($meRow->total ?? 0);
        } catch (Exception $e) {}

        // 3. Operational: Visitors Today & Exit Clearances
        $todayVisitors = 0;
        try {
            $db->query("SELECT COUNT(*) as total FROM visitor_book WHERE (school_id = :school_id OR school_id IS NULL) AND date = CURRENT_DATE()");
            $db->bind(':school_id', $schoolId);
            $vRow = $db->single();
            $todayVisitors = (int)($vRow->total ?? 0);
        } catch (Exception $e) {}

        $pendingClearances = 0;
        try {
            $db->query("SELECT COUNT(*) as total FROM student_clearances WHERE (school_id = :school_id OR school_id IS NULL) AND overall_status = 'In Progress'");
            $db->bind(':school_id', $schoolId);
            $cRow = $db->single();
            $pendingClearances = (int)($cRow->total ?? 0);
        } catch (Exception $e) {}

        // 4. 6-Month Income vs Expenses Trend
        $monthsTrend = [];
        $incomeTrend = [];
        $expenseTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $mTime = strtotime("-$i month");
            $mLabel = date('M Y', $mTime);
            $mNum = date('n', $mTime);
            $yNum = date('Y', $mTime);
            $monthsTrend[] = $mLabel;

            $incVal = 0;
            try {
                $db->query("SELECT SUM(amount) as total FROM fee_payments WHERE (school_id = :school_id OR school_id IS NULL) AND MONTH(date) = :m AND YEAR(date) = :y");
                $db->bind(':school_id', $schoolId);
                $db->bind(':m', $mNum);
                $db->bind(':y', $yNum);
                $incR = $db->single();
                $incVal = (float)($incR->total ?? 0);
            } catch (Exception $e) {}
            $incomeTrend[] = $incVal;

            $expVal = 0;
            try {
                $db->query("SELECT SUM(amount) as total FROM expenses WHERE (school_id = :school_id OR school_id IS NULL) AND MONTH(date) = :m AND YEAR(date) = :y");
                $db->bind(':school_id', $schoolId);
                $db->bind(':m', $mNum);
                $db->bind(':y', $yNum);
                $expR = $db->single();
                $expVal = (float)($expR->total ?? 0);
            } catch (Exception $e) {}
            $expenseTrend[] = $expVal;
        }

        // 5. Class-wise Student Distribution
        $classDistribution = [];
        try {
            $db->query("SELECT c.class_name, COUNT(s.id) as total_students 
                        FROM classes c 
                        LEFT JOIN students s ON c.id = s.class_id AND (s.school_id = :school_id OR s.school_id IS NULL)
                        WHERE c.school_id = :school_id OR c.school_id IS NULL
                        GROUP BY c.id 
                        ORDER BY c.id ASC 
                        LIMIT 8");
            $db->bind(':school_id', $schoolId);
            $classDistribution = $db->resultSet() ?: [];
        } catch (Exception $e) {}

        // 6. Section Count & Gender Breakdown Metrics (Male vs Female)
        $sectionCount = 0;
        try {
            $db->query("SELECT COUNT(*) as total FROM sections WHERE school_id = :school_id");
            $db->bind(':school_id', $schoolId);
            $secRow = $db->single();
            $sectionCount = (int)($secRow->total ?? 0);
        } catch (Exception $e) {}

        $maleCount = 0;
        $femaleCount = 0;
        try {
            $db->query("SELECT LOWER(gender) as g, COUNT(*) as total FROM students WHERE school_id = :school_id GROUP BY LOWER(gender)");
            $db->bind(':school_id', $schoolId);
            $gRows = $db->resultSet();
            if ($gRows) {
                foreach ($gRows as $gr) {
                    if ($gr->g === 'male' || $gr->g === 'm') {
                        $maleCount += (int)$gr->total;
                    } elseif ($gr->g === 'female' || $gr->g === 'f') {
                        $femaleCount += (int)$gr->total;
                    }
                }
            }
        } catch (Exception $e) {}

        $currency = $settingModel->getSetting('currency_symbol', 'Rs.');

        $data = [
            'count_students' => $studentModel->countStudents(),
            'count_classes' => $classModel->countClasses(),
            'count_sections' => $sectionCount,
            'count_male' => $maleCount,
            'count_female' => $femaleCount,
            'count_staff' => $userModel->countUsersByRole('teacher') + $userModel->countUsersByRole('admin'),
            'count_schools' => (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'super_admin') ? $schoolModel->countSchools() : null,
            'today_attendance_percent' => $todayAttPercent,
            'today_attendance_present' => $attPresent,
            'today_attendance_total' => $attTotal,
            'month_fees' => $monthFees,
            'pending_dues' => $pendingDues,
            'month_expenses' => $monthExpenses,
            'net_operating_balance' => ($monthFees - $monthExpenses),
            'today_visitors' => $todayVisitors,
            'pending_clearances' => $pendingClearances,
            'months_trend' => $monthsTrend,
            'income_trend' => $incomeTrend,
            'expense_trend' => $expenseTrend,
            'class_distribution' => $classDistribution,
            'currency' => $currency
        ];

        $this->view('admin/dashboard', $data);
    }

    public function dashboard(){
        $this->index();
    }

    public function settings(){
        $settingModel = $this->model('SiteSetting');
        $data = [
            'settings' => $settingModel->getAllSettings()
        ];
        
        $this->view('admin/settings', $data);
    }

    public function updateSettings(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $settingModel = $this->model('SiteSetting');
            
            // Loop through posted data and update
            // We assume key names match database setting_key
            $keys = ['school_name', 'school_address', 'contact_email', 'hero_title', 'hero_description'];
            
            foreach($keys as $key){
                if(isset($_POST[$key])){
                   $settingModel->updateSetting($key, trim($_POST[$key])); 
                }
            }

            // Redirect back with success message (simplified)
            header('Location: ' . URLROOT . '/admin/settings');
        } else {
            $this->settings();
        }
    }

    public function schools(){
        $this->requireSuperAdmin();

        $schoolModel = $this->model('School');
        $data = [
            'schools' => $schoolModel->getAllSchools(),
            'form' => [
                'name' => '',
                'code' => '',
                'domain' => '',
                'status' => 'active'
            ],
            'errors' => [
                'name' => '',
                'code' => '',
                'domain' => ''
            ]
        ];

        $this->view('admin/schools', $data);
    }

    public function addSchool(){
        $this->requireSuperAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }

        if (class_exists('AuthGuard')) {
            AuthGuard::verifyCSRF();
        }

        $schoolModel = $this->model('School');
        $name = trim($_POST['name'] ?? '');
        $code = strtolower(trim($_POST['code'] ?? ''));
        $domain = strtolower(trim($_POST['domain'] ?? ''));
        $status = trim($_POST['status'] ?? 'active');

        // Auto-generate branch code from branch name if left empty
        if ($code === '' && $name !== '') {
            $baseCode = strtolower(preg_replace('/[^a-z0-9_-]/', '-', strtolower($name)));
            $baseCode = trim(preg_replace('/-+/', '-', $baseCode), '-');
            if (strlen($baseCode) < 2) {
                $baseCode = 'branch-' . substr(md5($name), 0, 4);
            }
            $code = $baseCode;
            $counter = 1;
            while ($schoolModel->existsByCode($code)) {
                $counter++;
                $code = $baseCode . '-' . $counter;
            }
        }

        $data = [
            'schools' => $schoolModel->getAllSchools(),
            'form' => [
                'name' => $name,
                'code' => $code,
                'domain' => $domain,
                'status' => $status
            ],
            'errors' => [
                'name' => '',
                'code' => '',
                'domain' => ''
            ]
        ];

        if ($name === '') {
            $data['errors']['name'] = 'School / Branch name is required.';
        }

        if ($code === '' || !preg_match('/^[a-z0-9_-]{2,50}$/', $code)) {
            $data['errors']['code'] = 'School code must be 2-50 chars using a-z, 0-9, _ or -.';
        } elseif ($schoolModel->existsByCode($code)) {
            $data['errors']['code'] = 'This school code already exists.';
        }

        if ($domain !== '' && !filter_var('http://' . $domain, FILTER_VALIDATE_URL)) {
            $data['errors']['domain'] = 'Please enter a valid domain (example: school.example.com).';
        } elseif ($domain !== '' && $schoolModel->existsByDomain($domain)) {
            $data['errors']['domain'] = 'This domain is already assigned to another school.';
        }

        if (!in_array($status, ['active', 'suspended', 'pending'], true)) {
            $status = 'active';
        }

        $hasErrors = $data['errors']['name'] || $data['errors']['code'] || $data['errors']['domain'];
        if ($hasErrors) {
            $this->view('admin/schools', $data);
            return;
        }

        $schoolModel->createSchool([
            'name' => $name,
            'code' => $code,
            'domain' => $domain,
            'status' => $status,
            'plan_id' => null
        ]);

        $_SESSION['flash_success'] = "New branch '{$name}' successfully created! Direct Branch URL: " . URLROOT . "/?branch={$code}";

        header('Location: ' . URLROOT . '/admin/schools');
        exit;
    }

    public function switchSchool($id = null){
        $this->requireSuperAdmin();
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }

        $schoolModel = $this->model('School');
        $school = $schoolModel->findById($id);

        if (!$school) {
            $_SESSION['flash_error'] = 'School / Branch not found.';
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }

        if ($school->status !== 'active') {
            $_SESSION['flash_error'] = "Cannot switch to '{$school->name}' because its status is '{$school->status}'. Please activate it first.";
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }

        TenantContext::setSchoolId($school->id, $school->code);
        $_SESSION['school_id'] = (int)$school->id;
        $_SESSION['school_code'] = $school->code;
        $_SESSION['school_name'] = $school->name;
        if (isset($_SESSION['user_id'])) {
            $_SESSION['user_school_id'] = (int)$school->id;
        }

        if (class_exists('SiteSetting')) {
            SiteSetting::clearCache();
        }

        $_SESSION['flash_success'] = "Switched active branch to: '{$school->name}' (#{$school->id} / {$school->code}). All operational data (students, fees, classes, staff) is now filtered for this campus.";
        
        $referer = $_SERVER['HTTP_REFERER'] ?? (URLROOT . '/admin/schools');
        header('Location: ' . $referer);
        exit;
    }

    public function switchBranch($id = null){
        $this->switchSchool($id);
    }

    public function schoolStatus($id = null, $status = null){
        $this->requireSuperAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }
        if (class_exists('AuthGuard')) {
            AuthGuard::verifyCSRF();
        }

        if (!$id || !in_array($status, ['active', 'suspended', 'pending'], true)) {
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }

        $schoolModel = $this->model('School');
        $schoolModel->updateStatus((int)$id, $status);

        header('Location: ' . URLROOT . '/admin/schools');
        exit;
    }

    public function deleteSchool($id = null){
        if (($_SESSION['user_role'] ?? '') !== 'super_admin') {
            $_SESSION['flash_error'] = 'Access Denied: Only Super Administrator can permanently delete a branch.';
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }
        if (class_exists('AuthGuard')) {
            AuthGuard::verifyCSRF();
        }

        $id = (int)$id;
        if (!$id) {
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }

        if ($id <= 1) {
            $_SESSION['flash_error'] = 'The primary default system school is protected and cannot be deleted.';
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }

        if (isset($_SESSION['school_id']) && (int)$_SESSION['school_id'] === $id) {
            $_SESSION['flash_error'] = 'You cannot delete the school you are currently logged into.';
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }

        $schoolModel = $this->model('School');
        $school = $schoolModel->findById($id);

        if (!$school) {
            $_SESSION['flash_error'] = 'School record not found.';
            header('Location: ' . URLROOT . '/admin/schools');
            exit;
        }

        if ($schoolModel->deleteSchool($id)) {
            $_SESSION['flash_success'] = 'School "' . htmlspecialchars($school->name) . '" deleted successfully.';
        } else {
            $_SESSION['flash_error'] = 'Failed to delete school.';
        }

        header('Location: ' . URLROOT . '/admin/schools');
        exit;
    }

    public function users(){
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $userModel = $this->model('User');
        $search = trim($_GET['search'] ?? '');
        $role = trim($_GET['role'] ?? '');

        $users = $userModel->getAllUsers($search, $role);

        $stats = [
            'total' => count($users),
            'admins' => 0,
            'teachers' => 0,
            'students' => 0,
            'parents' => 0,
            'staff' => 0
        ];
        foreach ($users as $u) {
            if (in_array($u->role, ['admin', 'super_admin'])) $stats['admins']++;
            elseif ($u->role === 'teacher') { $stats['teachers']++; $stats['staff']++; }
            elseif ($u->role === 'student') $stats['students']++;
            elseif ($u->role === 'parent') $stats['parents']++;
            else $stats['staff']++;
        }

        $data = [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'total_count' => count($users),
            'stats' => $stats
        ];

        $this->view('admin/users', $data);
    }

    public function updateUser($id = null){
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (class_exists('AuthGuard')) {
                AuthGuard::verifyCSRF();
            }

            $userId = !empty($id) ? (int)$id : (int)($_POST['user_id'] ?? 0);
            if (!$userId) {
                $_SESSION['flash_error'] = 'Invalid user account ID.';
                header('Location: ' . URLROOT . '/admin/users');
                exit;
            }

            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'role' => trim($_POST['role'] ?? ''),
                'password' => trim($_POST['password'] ?? '')
            ];

            $userModel = $this->model('User');
            $result = $userModel->updateUserByAdmin($userId, $data);

            if ($result['success']) {
                $_SESSION['flash_success'] = $result['message'];
            } else {
                $_SESSION['flash_error'] = $result['message'];
            }
        }

        header('Location: ' . URLROOT . '/admin/users');
        exit;
    }

    public function createUser(){
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (class_exists('AuthGuard')) {
                AuthGuard::verifyCSRF();
            }

            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? 'teacher');
            $password = trim($_POST['password'] ?? '');

            $userModel = $this->model('User');
            $result = $userModel->createUserByAdmin([
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'password' => $password
            ]);

            if ($result['success']) {
                $plainPass = htmlspecialchars($result['plain_password']);
                $loginUrl = URLROOT . '/auth/login';
                $mailSent = Mailer::send(
                    $result['email'],
                    'Your ' . SITENAME . ' account is ready',
                    '<h2>Welcome to ' . htmlspecialchars(SITENAME, ENT_QUOTES, 'UTF-8') . '</h2><p>Hello ' . htmlspecialchars($result['name'], ENT_QUOTES, 'UTF-8') . ',</p><p>Your account has been created. Use these credentials to sign in:</p><p><strong>Email:</strong> ' . htmlspecialchars($result['email'], ENT_QUOTES, 'UTF-8') . '<br><strong>Temporary password:</strong> ' . htmlspecialchars($result['plain_password'], ENT_QUOTES, 'UTF-8') . '</p><p><a href="' . htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') . '">Open Login Page</a></p><p>Please change your password after signing in.</p>',
                    "Welcome to " . SITENAME . "\n\nLogin URL: {$loginUrl}\nEmail: {$result['email']}\nTemporary password: {$result['plain_password']}\n\nPlease change your password after signing in."
                );
                $_SESSION['flash_created_user'] = [
                    'name' => $result['name'],
                    'email' => $result['email'],
                    'role' => $result['role'],
                    'password' => $result['plain_password']
                ];
                $mailNotice = $mailSent ? ' Login credentials were emailed to the user.' : ' SMTP is not configured, so share the temporary password manually.';
                $_SESSION['flash_success'] = "New user account '{$name}' created successfully! System Generated Password: <strong class='font-monospace bg-dark text-warning px-2 py-1 rounded'>{$plainPass}</strong> (The user can change their password after logging in).{$mailNotice}";
            } else {
                $_SESSION['flash_error'] = $result['message'];
            }
        }

        header('Location: ' . URLROOT . '/admin/users');
        exit;
    }

    public function getUserJson($id = null){
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        header('Content-Type: application/json');
        $id = (int)$id;
        $userModel = $this->model('User');
        $user = $userModel->getUserById($id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            exit;
        }

        echo json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'school_id' => $user->school_id
        ]);
        exit;
    }

    public function deleteUser($id = null){
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'super_admin'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }
        if (class_exists('AuthGuard')) {
            AuthGuard::verifyCSRF();
        }

        $id = (int)$id;
        if (!$id) {
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }

        if ($id <= 1 || $id === (int)($_SESSION['user_id'] ?? 0)) {
            $_SESSION['flash_error'] = 'Primary system administrator account cannot be deleted.';
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }

        $userModel = $this->model('User');
        $user = $userModel->getUserById($id);

        if (!$user) {
            $_SESSION['flash_error'] = 'User account not found.';
            header('Location: ' . URLROOT . '/admin/users');
            exit;
        }

        if ($userModel->deleteUser($id)) {
            $_SESSION['flash_success'] = 'User "' . htmlspecialchars($user->email) . '" was deleted successfully.';
        } else {
            $_SESSION['flash_error'] = 'Failed to delete user account.';
        }

        header('Location: ' . URLROOT . '/admin/users');
        exit;
    }

    private function requireSuperAdmin(){
        $role = $_SESSION['user_role'] ?? '';
        if (!in_array($role, ['super_admin', 'admin'], true)) {
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                   || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
            if ($isAjax) {
                if (ob_get_length()) ob_clean();
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Access Denied: Only Administrator and Super Administrator are authorized to manage or switch branches.', 'error' => 'forbidden']);
                exit;
            }
            $_SESSION['flash_error'] = 'Access Denied: Only Administrator and Super Administrator are authorized to manage or switch branches.';
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }
    }
}
