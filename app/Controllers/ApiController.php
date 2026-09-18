<?php
/**
 * ApiController - Complete Mobile Application & RESTful API Service Engine
 * Exposes full-fledged secure JSON endpoints for Mobile Apps (Flutter, React Native, iOS, Android).
 * Supports both Data Viewing (Show) AND Data Management (Mark Attendance, Post Homework, Enter Marks, Collect Fees, Admissions).
 */
class ApiController extends Controller {
    private $apiKeyModel;

    public function __construct() {
        // Automatically require ApiGuard for all API operations
        if (!class_exists('ApiGuard')) {
            require_once APPROOT . '/Core/ApiGuard.php';
        }
        ApiGuard::handleCors();
        $this->apiKeyModel = $this->model('ApiKey');
    }

    /**
     * Helper to retrieve request JSON body or POST form data
     */
    private function getInputData() {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (is_array($json)) {
            return $json;
        }
        return $_POST;
    }

    /**
     * Helper to verify user permissions for staff/teacher/admin operations
     */
    private function requireStaffOrAdmin($user, $customMessage = 'Only authorized teachers or staff can perform this action.') {
        $allowed = ['super_admin', 'admin', 'principal', 'teacher', 'accountant', 'staff', 'receptionist'];
        if (!in_array(strtolower($user->role), $allowed)) {
            ApiGuard::jsonError($customMessage, 403);
        }
    }

    // =========================================================================
    // 1. PUBLIC & DIAGNOSTICS ENDPOINTS
    // =========================================================================

    /**
     * GET /api/status or /api/ping
     */
    public function status() {
        $client = ApiGuard::requireApiKey();
        
        $schoolId = TenantContext::getSchoolId() ?: (int)$client->school_id;
        $db = new Database();
        $db->query("SELECT name, code FROM schools WHERE id = :id LIMIT 1");
        $db->bind(':id', $schoolId);
        $school = $db->single();

        ApiGuard::jsonSuccess([
            'status' => 'online',
            'api_version' => '2.0.0 (Full-Fledged)',
            'client_name' => $client->client_name,
            'school_id' => $schoolId,
            'school_name' => $school ? $school->name : (defined('SITENAME') ? SITENAME : 'School ERP'),
            'school_code' => $school ? $school->code : 'default',
            'server_time' => date('Y-m-d H:i:s'),
            'rate_limit_per_min' => (int)($client->rate_limit_per_min ?? 120)
        ], 'API service is online and key is valid.');
    }

    public function ping() {
        $this->status();
    }

    /**
     * GET /api/tester or /api/playground
     */
    public function tester() {
        $filePath = dirname(APPROOT) . '/public/api_tester.html';
        if (file_exists($filePath)) {
            header('Content-Type: text/html; charset=utf-8');
            readfile($filePath);
            exit;
        }
        ApiGuard::jsonError('Tester UI file not found.', 404);
    }

    public function playground() {
        $this->tester();
    }

    /**
     * GET /api/school
     */
    public function school() {
        $client = ApiGuard::requireApiKey();
        $schoolId = TenantContext::getSchoolId() ?: (int)$client->school_id;

        $db = new Database();
        $db->query("SELECT * FROM site_settings WHERE school_id = :sid LIMIT 1");
        $db->bind(':sid', $schoolId);
        $settings = $db->single();

        $db->query("SELECT name, code, domain FROM schools WHERE id = :sid LIMIT 1");
        $db->bind(':sid', $schoolId);
        $school = $db->single();

        $data = [
            'school_id' => $schoolId,
            'school_name' => $settings->school_name ?? ($school->name ?? (defined('SITENAME') ? SITENAME : 'School ERP')),
            'campus_name' => $settings->campus_name ?? '',
            'school_email' => $settings->school_email ?? '',
            'school_phone' => $settings->school_phone ?? '',
            'school_address' => $settings->school_address ?? '',
            'currency_symbol' => $settings->currency_symbol ?? 'PKR',
            'logo_url' => !empty($settings->logo) ? (URLROOT . '/' . ltrim($settings->logo, '/')) : null,
            'affiliation_no' => $settings->affiliation_no ?? '',
            'bank_info' => [
                'bank_name' => $settings->bank_name ?? '',
                'account_title' => $settings->bank_account_title ?? '',
                'account_no' => $settings->bank_account_no ?? '',
                'iban' => $settings->bank_iban ?? '',
                'branch' => $settings->bank_branch ?? ''
            ]
        ];

        ApiGuard::jsonSuccess($data, 'School campus information retrieved.');
    }

    // =========================================================================
    // 2. ACADEMIC METADATA & SELECT PICKERS (FOR MOBILE APPS)
    // =========================================================================

    /**
     * GET /api/classes
     * List all classes with their sections
     */
    public function classes() {
        ApiGuard::requireApiKey();
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');

        $classes = $classModel ? $classModel->getClasses() : [];
        $sections = $sectionModel ? $sectionModel->getSections() : [];

        // Group sections under their classes
        $secMap = [];
        foreach ($sections as $s) {
            $secMap[$s->class_id][] = [
                'id' => (int)$s->id,
                'section_name' => $s->section_name,
                'class_teacher_name' => $s->class_teacher_name ?? null
            ];
        }

        $result = [];
        foreach ($classes as $c) {
            $result[] = [
                'id' => (int)$c->id,
                'class_name' => $c->class_name,
                'sections' => $secMap[$c->id] ?? []
            ];
        }

        ApiGuard::jsonSuccess($result, 'Classes and sections list loaded.');
    }

    /**
     * GET /api/subjects
     */
    public function subjects() {
        ApiGuard::requireApiKey();
        $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $sectionId = !empty($_GET['section_id']) ? (int)$_GET['section_id'] : null;

        $subjectModel = $this->model('Subject');
        if ($classId && $sectionId && method_exists($subjectModel, 'getSubjectsByClassSection')) {
            $subjects = $subjectModel->getSubjectsByClassSection($classId, $sectionId);
        } else {
            $db = new Database();
            $schoolId = TenantContext::getSchoolId() ?: 1;
            $db->query("SELECT id, subject_name, subject_code, type, is_core, full_marks, passing_marks FROM subjects WHERE school_id = :sid ORDER BY subject_name ASC");
            $db->bind(':sid', $schoolId);
            $subjects = $db->resultSet();
        }

        ApiGuard::jsonSuccess($subjects, 'Subjects list loaded.');
    }

    /**
     * GET /api/sessions
     */
    public function sessions() {
        ApiGuard::requireApiKey();
        $sessionModel = $this->model('AcademicSession');
        $sessions = $sessionModel ? $sessionModel->getSessions() : [];
        $current = $sessionModel ? $sessionModel->getCurrentSession() : null;

        ApiGuard::jsonSuccess([
            'current_session' => $current,
            'sessions' => $sessions
        ], 'Academic sessions loaded.');
    }

    // =========================================================================
    // 3. AUTHENTICATION & PROFILE
    // =========================================================================

    /**
     * POST /api/login
     */
    public function login() {
        $client = ApiGuard::requireApiKey();
        $schoolId = TenantContext::getSchoolId() ?: (int)$client->school_id;

        $input = $this->getInputData();
        $identifier = trim($input['username'] ?? ($input['email'] ?? ($input['admission_no'] ?? '')));
        $password = trim($input['password'] ?? '');
        $deviceName = trim($input['device_name'] ?? 'Mobile App');
        $devicePlatform = trim($input['device_platform'] ?? 'android');

        if (empty($identifier) || empty($password)) {
            ApiGuard::jsonError('Username/Email and Password are required.', 422);
        }

        $db = new Database();
        
        // 1. Try finding by email
        $db->query("SELECT * FROM users WHERE email = :email AND (school_id = :sid OR role = 'super_admin') LIMIT 1");
        $db->bind(':email', $identifier);
        $db->bind(':sid', $schoolId);
        $user = $db->single();

        // 2. If not found by email, check admission_no
        if (!$user) {
            $db->query("SELECT u.* FROM students s 
                        JOIN users u ON s.user_id = u.id 
                        WHERE s.admission_no = :adm AND s.school_id = :sid 
                        LIMIT 1");
            $db->bind(':adm', $identifier);
            $db->bind(':sid', $schoolId);
            $user = $db->single();
        }

        // 3. Fallback: check roll_no
        if (!$user) {
            $db->query("SELECT u.* FROM students s 
                        JOIN users u ON s.user_id = u.id 
                        WHERE s.roll_no = :roll AND s.school_id = :sid 
                        LIMIT 1");
            $db->bind(':roll', $identifier);
            $db->bind(':sid', $schoolId);
            $user = $db->single();
        }

        if (!$user || !password_verify($password, $user->password)) {
            ApiGuard::jsonError('Invalid credentials. Please verify your username and password.', 401);
        }

        if (isset($user->status) && $user->status !== 'active' && $user->status !== 'Active') {
            ApiGuard::jsonError('Your account has been deactivated. Please contact the administration.', 403);
        }

        // Generate Session Token
        $tokenData = $this->apiKeyModel->createUserToken(
            $user->id,
            $schoolId,
            $client->id,
            $deviceName,
            $devicePlatform,
            45
        );

        if (!$tokenData) {
            ApiGuard::jsonError('Could not generate authentication session.', 500);
        }

        $profile = $this->resolveUserProfile($user->id, $user->role, $schoolId);

        ApiGuard::jsonSuccess([
            'token' => $tokenData['token'],
            'token_type' => 'Bearer',
            'expires_at' => $tokenData['expires_at'],
            'user' => [
                'id' => (int)$user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'school_id' => (int)$user->school_id
            ],
            'profile' => $profile
        ], 'Login successful.');
    }

    /**
     * POST /api/logout
     */
    public function logout() {
        $user = ApiGuard::requireUserAuth();
        $token = ApiGuard::extractBearerToken();

        if ($token) {
            $this->apiKeyModel->revokeUserToken($token);
        }

        ApiGuard::jsonSuccess(null, 'Logged out successfully.');
    }

    /**
     * GET /api/me
     */
    public function me() {
        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;
        $profile = $this->resolveUserProfile($user->id, $user->role, $schoolId);

        ApiGuard::jsonSuccess([
            'user' => [
                'id' => (int)$user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'school_id' => (int)$user->school_id
            ],
            'profile' => $profile
        ], 'User profile loaded.');
    }

    /**
     * POST /api/profile/update
     * Update user details or change password
     */
    public function updateProfile() {
        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;
        $input = $this->getInputData();

        $name = trim($input['name'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $newPassword = trim($input['new_password'] ?? '');
        $currentPassword = trim($input['current_password'] ?? '');

        $db = new Database();

        // If password change is requested
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                ApiGuard::jsonError('Current password is required to set a new password.', 422);
            }
            $db->query("SELECT password FROM users WHERE id = :uid LIMIT 1");
            $db->bind(':uid', $user->id);
            $uRec = $db->single();
            if (!$uRec || !password_verify($currentPassword, $uRec->password)) {
                ApiGuard::jsonError('Current password entered is incorrect.', 401);
            }
            if (strlen($newPassword) < 6) {
                ApiGuard::jsonError('New password must be at least 6 characters long.', 422);
            }
            $newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $db->query("UPDATE users SET password = :pwd WHERE id = :uid");
            $db->bind(':pwd', $newHashed);
            $db->bind(':uid', $user->id);
            $db->execute();
        }

        // Update name if supplied
        if (!empty($name)) {
            $db->query("UPDATE users SET name = :name WHERE id = :uid");
            $db->bind(':name', $name);
            $db->bind(':uid', $user->id);
            $db->execute();

            if ($user->role === 'student') {
                $db->query("UPDATE students SET name = :name WHERE user_id = :uid");
                $db->bind(':name', $name);
                $db->bind(':uid', $user->id);
                $db->execute();
            }
        }

        // Update phone if student
        if (!empty($phone) && $user->role === 'student') {
            $db->query("UPDATE students SET phone = :phone, parent_phone = :phone WHERE user_id = :uid");
            $db->bind(':phone', $phone);
            $db->bind(':uid', $user->id);
            $db->execute();
        }

        ApiGuard::jsonSuccess(null, 'Profile updated successfully.');
    }

    // =========================================================================
    // 4. ATTENDANCE MANAGEMENT (SHOW + MARK / SAVE)
    // =========================================================================

    /**
     * GET /api/attendance OR multiplexed /api/attendance/{action}
     */
    public function attendance($action = null, $param2 = null) {
        if ($action === 'class') {
            return $this->classAttendance();
        }
        if ($action === 'save') {
            return $this->saveAttendance();
        }

        // Default: Student / Parent Personal Attendance History
        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $targetStudentId = $this->authorizeStudentAccess($user, $action, $schoolId);

        $month = !empty($_GET['month']) ? trim($_GET['month']) : date('Y-m');
        $limit = max(1, min(365, (int)($_GET['limit'] ?? 60)));

        $db = new Database();
        $db->query("SELECT id, date, attendance_type, remark, entry_time
                    FROM student_attendance
                    WHERE student_id = :sid AND school_id = :school_id AND date LIKE :month
                    ORDER BY date DESC LIMIT :limit");
        $db->bind(':sid', $targetStudentId);
        $db->bind(':school_id', $schoolId);
        $db->bind(':month', $month . '%');
        $db->bind(':limit', $limit, PDO::PARAM_INT);
        $records = $db->resultSet();

        $presents = 0; $absents = 0; $leaves = 0; $lates = 0;
        foreach ($records as $r) {
            $type = strtolower($r->attendance_type);
            if ($type === 'present') $presents++;
            elseif ($type === 'absent') $absents++;
            elseif ($type === 'leave') $leaves++;
            elseif ($type === 'late') { $lates++; $presents++; }
        }

        $totalDays = count($records);
        $attendancePercentage = $totalDays > 0 ? round(($presents / $totalDays) * 100, 1) : 0;

        ApiGuard::jsonSuccess([
            'student_id' => $targetStudentId,
            'filter_month' => $month,
            'summary' => [
                'total_recorded_days' => $totalDays,
                'presents' => $presents,
                'absents' => $absents,
                'leaves' => $leaves,
                'lates' => $lates,
                'attendance_rate' => $attendancePercentage . '%'
            ],
            'records' => $records
        ], 'Attendance records retrieved.');
    }

    /**
     * GET /api/class-attendance or /api/attendance/class
     * Fetches entire class roll for marking attendance from mobile app
     */
    public function classAttendance() {
        $user = ApiGuard::requireUserAuth();
        $this->requireStaffOrAdmin($user, 'Only teachers and staff can view class attendance sheets.');
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $classId = (int)($_GET['class_id'] ?? 0);
        $sectionId = (int)($_GET['section_id'] ?? 0);
        $date = !empty($_GET['date']) ? trim($_GET['date']) : date('Y-m-d');

        if (!$classId || !$sectionId) {
            ApiGuard::jsonError('Class ID and Section ID are required.', 422);
        }

        $attendanceModel = $this->model('Attendance');
        $records = $attendanceModel ? $attendanceModel->getStudentAttendance($classId, $sectionId, $date) : [];

        ApiGuard::jsonSuccess([
            'class_id' => $classId,
            'section_id' => $sectionId,
            'date' => $date,
            'total_students' => count($records),
            'students' => $records
        ], 'Class attendance sheet loaded.');
    }

    /**
     * POST /api/save-attendance or /api/attendance/save
     * Mark & Save class attendance from mobile application
     */
    public function saveAttendance() {
        $user = ApiGuard::requireUserAuth();
        $this->requireStaffOrAdmin($user, 'Only teachers and staff can mark attendance.');
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $input = $this->getInputData();
        $classId = (int)($input['class_id'] ?? 0);
        $sectionId = (int)($input['section_id'] ?? 0);
        $date = !empty($input['date']) ? trim($input['date']) : date('Y-m-d');
        $students = $input['students'] ?? [];

        if (!$classId || !$sectionId || empty($students) || !is_array($students)) {
            ApiGuard::jsonError('Class ID, Section ID, and Students array are required.', 422);
        }

        $attendanceModel = $this->model('Attendance');
        $savedCount = 0;

        foreach ($students as $st) {
            $studentId = (int)($st['student_id'] ?? ($st['id'] ?? 0));
            if (!$studentId) continue;
            $type = ucfirst(strtolower(trim($st['type'] ?? ($st['attendance_type'] ?? 'Present'))));
            $remark = trim($st['remark'] ?? '');
            $entryTime = !empty($st['entry_time']) ? $st['entry_time'] : null;

            if ($attendanceModel->saveStudentAttendance($studentId, $classId, $sectionId, $date, $type, $remark, $entryTime)) {
                $savedCount++;
            }
        }

        ApiGuard::jsonSuccess([
            'class_id' => $classId,
            'section_id' => $sectionId,
            'date' => $date,
            'saved_count' => $savedCount
        ], "Attendance marked successfully for {$savedCount} students.");
    }

    // =========================================================================
    // 5. HOMEWORK & ASSIGNMENTS (SHOW + CREATE + SUBMIT)
    // =========================================================================

    /**
     * GET /api/homework OR multiplexed /api/homework/{action}
     */
    public function homework($action = null) {
        if ($action === 'create') return $this->createHomework();
        if ($action === 'submit') return $this->submitHomework();

        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;
        $homeworkModel = $this->model('Homework');

        $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $sectionId = !empty($_GET['section_id']) ? (int)$_GET['section_id'] : null;
        $subjectId = !empty($_GET['subject_id']) ? (int)$_GET['subject_id'] : null;

        // If student, lock to their own class & section
        if ($user->role === 'student') {
            $db = new Database();
            $db->query("SELECT class_id, section_id FROM students WHERE user_id = :uid LIMIT 1");
            $db->bind(':uid', $user->id);
            $st = $db->single();
            if ($st) {
                $classId = $st->class_id;
                $sectionId = $st->section_id;
            }
        }

        $records = $homeworkModel ? $homeworkModel->getHomework($classId, $sectionId, $subjectId) : [];

        ApiGuard::jsonSuccess($records, 'Homework assignments loaded.');
    }

    /**
     * POST /api/create-homework or /api/homework/create
     * Teacher posts homework from mobile app
     */
    public function createHomework() {
        $user = ApiGuard::requireUserAuth();
        $this->requireStaffOrAdmin($user, 'Only teachers and staff can assign homework.');
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $input = $this->getInputData();
        $classId = (int)($input['class_id'] ?? 0);
        $sectionId = (int)($input['section_id'] ?? 0);
        $subjectId = (int)($input['subject_id'] ?? 0);
        $homeworkDate = !empty($input['homework_date']) ? trim($input['homework_date']) : date('Y-m-d');
        $submissionDate = !empty($input['submission_date']) ? trim($input['submission_date']) : date('Y-m-d', strtotime('+1 day'));
        $description = trim($input['description'] ?? '');

        if (!$classId || !$sectionId || !$subjectId || empty($description)) {
            ApiGuard::jsonError('Class ID, Section ID, Subject ID, and Description are required.', 422);
        }

        $homeworkModel = $this->model('Homework');
        $data = [
            'class_id' => $classId,
            'section_id' => $sectionId,
            'subject_id' => $subjectId,
            'homework_date' => $homeworkDate,
            'submission_date' => $submissionDate,
            'description' => $description,
            'created_by' => $user->id
        ];

        if ($homeworkModel->addHomework($data)) {
            ApiGuard::jsonSuccess($data, 'Homework created and assigned successfully.');
        } else {
            ApiGuard::jsonError('Failed to create homework.', 500);
        }
    }

    /**
     * POST /api/submit-homework or /api/homework/submit
     * Student marks or submits homework
     */
    public function submitHomework() {
        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;
        $input = $this->getInputData();

        $homeworkId = (int)($input['homework_id'] ?? 0);
        $notes = trim($input['notes'] ?? 'Completed');

        if (!$homeworkId) {
            ApiGuard::jsonError('Homework ID is required.', 422);
        }

        $targetStudentId = $this->authorizeStudentAccess($user, null, $schoolId);

        $db = new Database();
        // Check / update submission status
        $db->query("CREATE TABLE IF NOT EXISTS homework_submissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            homework_id INT NOT NULL,
            student_id INT NOT NULL,
            submission_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            notes TEXT NULL,
            status VARCHAR(20) DEFAULT 'submitted',
            UNIQUE KEY uk_hw_student (homework_id, student_id)
        )");
        $db->execute();

        $db->query("INSERT INTO homework_submissions (homework_id, student_id, notes, status, submission_date)
                    VALUES (:hid, :sid, :notes, 'submitted', NOW())
                    ON DUPLICATE KEY UPDATE notes = :notes_up, submission_date = NOW(), status = 'submitted'");
        $db->bind(':hid', $homeworkId);
        $db->bind(':sid', $targetStudentId);
        $db->bind(':notes', $notes);
        $db->bind(':notes_up', $notes);
        $db->execute();

        ApiGuard::jsonSuccess([
            'homework_id' => $homeworkId,
            'student_id' => $targetStudentId,
            'status' => 'submitted'
        ], 'Homework submitted successfully.');
    }

    // =========================================================================
    // 6. EXAMS, MARKS ENTRY & REPORT CARDS (SHOW + ENTER MARKS)
    // =========================================================================

    /**
     * GET /api/exams OR multiplexed /api/exams/{action}
     */
    public function exams($action = null) {
        if ($action === 'results') return $this->examResults();
        if ($action === 'marks') return $this->saveMarks();

        ApiGuard::requireApiKey();
        $examModel = $this->model('Exam');
        $exams = $examModel ? $examModel->getExams() : [];

        ApiGuard::jsonSuccess($exams, 'Exams list loaded.');
    }

    /**
     * GET /api/exam-results or /api/exams/results
     * Full academic report card with subject breakdown, GPA, and grades
     */
    public function examResults() {
        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $examId = (int)($_GET['exam_id'] ?? 0);
        $requestedStudentId = !empty($_GET['student_id']) ? (int)$_GET['student_id'] : null;

        $targetStudentId = $this->authorizeStudentAccess($user, $requestedStudentId, $schoolId);

        $db = new Database();

        // If exam_id not specified, pick the latest exam
        if (!$examId) {
            $db->query("SELECT id FROM exams WHERE school_id = :sid ORDER BY id DESC LIMIT 1");
            $db->bind(':sid', $schoolId);
            $latest = $db->single();
            if ($latest) $examId = (int)$latest->id;
        }

        if (!$examId) {
            ApiGuard::jsonError('No examinations found for this school.', 404);
        }

        // Fetch exam info
        $db->query("SELECT * FROM exams WHERE id = :id AND school_id = :sid LIMIT 1");
        $db->bind(':id', $examId);
        $db->bind(':sid', $schoolId);
        $examInfo = $db->single();

        // Fetch student info
        $db->query("SELECT s.*, c.class_name, sec.section_name FROM students s
                    LEFT JOIN classes c ON s.class_id = c.id
                    LEFT JOIN sections sec ON s.section_id = sec.id
                    WHERE s.id = :id AND s.school_id = :sid LIMIT 1");
        $db->bind(':id', $targetStudentId);
        $db->bind(':sid', $schoolId);
        $student = $db->single();

        // Fetch marks for each subject in this exam
        $sql = "SELECT es.id as schedule_id, es.date as exam_date, es.full_marks, es.passing_marks,
                       sub.subject_name, sub.subject_code, sub.type as subject_type,
                       em.get_marks, em.theory_marks, em.practical_marks, em.is_absent, em.remarks
                FROM exam_schedules es
                JOIN subjects sub ON es.subject_id = sub.id
                LEFT JOIN exam_marks em ON es.id = em.exam_schedule_id AND em.student_id = :sid
                WHERE es.exam_id = :eid AND es.class_id = :cid AND es.school_id = :school_id
                ORDER BY sub.subject_name ASC";
        
        $db->query($sql);
        $db->bind(':sid', $targetStudentId);
        $db->bind(':eid', $examId);
        $db->bind(':cid', $student ? $student->class_id : 0);
        $db->bind(':school_id', $schoolId);
        $papers = $db->resultSet();

        $totalMarksSum = 0;
        $obtainedMarksSum = 0;
        $failedPapers = 0;

        foreach ($papers as &$p) {
            $full = (float)$p->full_marks;
            $pass = (float)$p->passing_marks;
            $obtained = ($p->is_absent === 'yes') ? 0 : (float)($p->get_marks ?? (($p->theory_marks ?? 0) + ($p->practical_marks ?? 0)));
            $isFailed = ($p->is_absent === 'yes' || $obtained < $pass);
            if ($isFailed) $failedPapers++;

            $totalMarksSum += $full;
            if ($p->is_absent !== 'yes') {
                $obtainedMarksSum += $obtained;
            }

            $pct = $full > 0 ? round(($obtained / $full) * 100, 1) : 0;
            $grade = 'F';
            if (!$isFailed) {
                if ($pct >= 80) $grade = 'A+';
                elseif ($pct >= 70) $grade = 'A';
                elseif ($pct >= 60) $grade = 'B';
                elseif ($pct >= 50) $grade = 'C';
                elseif ($pct >= 40) $grade = 'D';
            }

            $p->calculated_obtained = $obtained;
            $p->percentage = $pct;
            $p->grade = $grade;
            $p->is_failed = $isFailed;
        }

        $overallPct = $totalMarksSum > 0 ? round(($obtainedMarksSum / $totalMarksSum) * 100, 2) : 0;
        $overallGrade = ($failedPapers > 0 || $overallPct < 40) ? 'F' : ($overallPct >= 80 ? 'A+' : ($overallPct >= 70 ? 'A' : ($overallPct >= 60 ? 'B' : 'C')));

        ApiGuard::jsonSuccess([
            'exam' => $examInfo,
            'student' => [
                'id' => (int)$student->id,
                'name' => $student->name,
                'admission_no' => $student->admission_no,
                'roll_no' => $student->roll_no,
                'class_name' => $student->class_name,
                'section_name' => $student->section_name
            ],
            'summary' => [
                'total_max_marks' => $totalMarksSum,
                'total_obtained_marks' => $obtainedMarksSum,
                'percentage' => $overallPct . '%',
                'overall_grade' => $overallGrade,
                'passed' => $failedPapers === 0 && $overallPct >= 40,
                'failed_subjects_count' => $failedPapers
            ],
            'papers' => $papers
        ], 'Student report card loaded.');
    }

    /**
     * POST /api/save-marks or /api/exams/marks
     * Teacher enters exam marks from mobile phone
     */
    public function saveMarks() {
        $user = ApiGuard::requireUserAuth();
        $this->requireStaffOrAdmin($user, 'Only teachers and staff can enter exam marks.');
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $input = $this->getInputData();
        $scheduleId = (int)($input['schedule_id'] ?? 0);
        $students = $input['students'] ?? [];

        if (!$scheduleId || empty($students) || !is_array($students)) {
            ApiGuard::jsonError('Schedule ID and Students marks array are required.', 422);
        }

        $examModel = $this->model('Exam');
        $saved = 0;

        foreach ($students as $st) {
            $studentId = (int)($st['student_id'] ?? ($st['id'] ?? 0));
            if (!$studentId) continue;

            $theory = isset($st['theory']) ? (float)$st['theory'] : (isset($st['theory_marks']) ? (float)$st['theory_marks'] : 0);
            $practical = isset($st['practical']) ? (float)$st['practical'] : (isset($st['practical_marks']) ? (float)$st['practical_marks'] : 0);
            $marks = isset($st['marks']) ? (float)$st['marks'] : (isset($st['get_marks']) ? (float)$st['get_marks'] : ($theory + $practical));
            $absent = (!empty($st['absent']) && ($st['absent'] === true || $st['absent'] === 'yes')) ? 'yes' : 'no';
            $remarks = trim($st['remarks'] ?? '');

            if ($examModel->saveMarks($scheduleId, $studentId, $marks, $absent, $theory, $practical, $remarks)) {
                $saved++;
            }
        }

        ApiGuard::jsonSuccess([
            'schedule_id' => $scheduleId,
            'saved_count' => $saved
        ], "Marks successfully saved for {$saved} students.");
    }

    // =========================================================================
    // 7. FEES MANAGEMENT & COLLECTION
    // =========================================================================

    /**
     * GET /api/fees OR multiplexed /api/fees/{action}
     */
    public function fees($id = null, $param2 = null) {
        if ($id === 'collect') {
            return $this->collectFee();
        }

        // Student's personal fees and dues (existing)
        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;
        $targetStudentId = $this->authorizeStudentAccess($user, $id, $schoolId);

        $db = new Database();
        $db->query("SELECT sf.id as fee_id, sf.challan_no, sf.billing_month, sf.due_date, sf.status as payment_status,
                           sf.amount, sf.fine, sf.discount, sf.paid_amount,
                           (sf.amount + sf.fine - sf.discount - sf.paid_amount) as remaining_due,
                           fgt.amount as base_fee_amount,
                           ft.type_name, fg.group_name
                    FROM student_fees sf
                    LEFT JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id
                    LEFT JOIN fee_types ft ON fgt.fee_type_id = ft.id
                    LEFT JOIN fee_groups fg ON fgt.fee_group_id = fg.id
                    WHERE sf.student_id = :sid AND sf.school_id = :school_id
                    ORDER BY sf.due_date DESC, sf.id DESC");
        $db->bind(':sid', $targetStudentId);
        $db->bind(':school_id', $schoolId);
        $challans = $db->resultSet();

        $db->query("SELECT fp.id as payment_id, fp.payment_date, fp.amount, fp.payment_mode, fp.reference_no, fp.notes
                    FROM fee_payments fp
                    JOIN student_fees sf ON fp.student_fee_id = sf.id
                    WHERE sf.student_id = :sid AND fp.school_id = :school_id
                    ORDER BY fp.payment_date DESC, fp.id DESC");
        $db->bind(':sid', $targetStudentId);
        $db->bind(':school_id', $schoolId);
        $payments = $db->resultSet();

        $totalDues = 0; $totalPaid = 0; $totalOutstanding = 0;
        foreach ($challans as $c) {
            $totalDues += (float)$c->amount;
            $totalPaid += (float)$c->paid_amount;
            $rem = (float)$c->remaining_due;
            if ($rem > 0) $totalOutstanding += $rem;
        }

        ApiGuard::jsonSuccess([
            'student_id' => $targetStudentId,
            'financial_summary' => [
                'total_billed' => round($totalDues, 2),
                'total_paid' => round($totalPaid, 2),
                'total_outstanding' => round($totalOutstanding, 2),
                'has_overdue' => $totalOutstanding > 0
            ],
            'challans' => $challans,
            'recent_payments' => $payments
        ], 'Fee history and challans retrieved.');
    }

    /**
     * POST /api/collect-fee or /api/fees/collect
     * Accountant / Cashier records fee payment from mobile app
     */
    public function collectFee() {
        $user = ApiGuard::requireUserAuth();
        $this->requireStaffOrAdmin($user, 'Only accountants and authorized staff can record fee payments.');
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $input = $this->getInputData();
        $studentFeeId = (int)($input['student_fee_id'] ?? ($input['fee_id'] ?? 0));
        $amount = (float)($input['amount'] ?? 0);
        $paymentMode = trim($input['payment_mode'] ?? 'Cash');
        $paymentDate = !empty($input['payment_date']) ? trim($input['payment_date']) : date('Y-m-d');
        $refNo = trim($input['reference_no'] ?? '');
        $note = trim($input['note'] ?? 'Collected via Mobile App');

        if (!$studentFeeId || $amount <= 0) {
            ApiGuard::jsonError('Valid Student Fee ID and Payment Amount are required.', 422);
        }

        $db = new Database();
        // Fetch fee record
        $db->query("SELECT * FROM student_fees WHERE id = :id AND school_id = :sid LIMIT 1");
        $db->bind(':id', $studentFeeId);
        $db->bind(':sid', $schoolId);
        $feeRecord = $db->single();

        if (!$feeRecord) {
            ApiGuard::jsonError('Fee voucher record not found.', 404);
        }

        // Insert payment
        $db->query("INSERT INTO fee_payments (school_id, student_fee_id, payment_mode, amount, payment_date, reference_no, notes)
                    VALUES (:sid, :sfid, :mode, :amt, :pdate, :ref, :notes)");
        $db->bind(':sid', $schoolId);
        $db->bind(':sfid', $studentFeeId);
        $db->bind(':mode', $paymentMode);
        $db->bind(':amt', $amount);
        $db->bind(':pdate', $paymentDate);
        $db->bind(':ref', $refNo);
        $db->bind(':notes', $note);
        $db->execute();
        $paymentId = $db->lastInsertId();

        // Update student_fees paid_amount and status
        $newPaid = (float)$feeRecord->paid_amount + $amount;
        $totalBill = (float)$feeRecord->amount + (float)$feeRecord->fine - (float)$feeRecord->discount;
        $status = ($newPaid >= $totalBill) ? 'paid' : 'partial';

        $db->query("UPDATE student_fees SET paid_amount = :paid, status = :status WHERE id = :id");
        $db->bind(':paid', $newPaid);
        $db->bind(':status', $status);
        $db->bind(':id', $studentFeeId);
        $db->execute();

        ApiGuard::jsonSuccess([
            'payment_id' => $paymentId,
            'student_fee_id' => $studentFeeId,
            'amount_paid' => $amount,
            'total_paid' => $newPaid,
            'new_status' => $status
        ], 'Fee payment recorded successfully.');
    }

    // =========================================================================
    // 8. STUDENT ADMISSIONS & UPDATES (SHOW + CREATE + UPDATE)
    // =========================================================================

    /**
     * GET /api/students OR multiplexed /api/students/{action}
     */
    public function students($action = null, $param2 = null) {
        if ($action === 'create') return $this->createStudent();
        if ($action === 'update') return $this->updateStudent($param2);

        // Student Directory View
        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        if ($user->role === 'student' || $user->role === 'parent') {
            ApiGuard::jsonError('Unauthorized access to student directory.', 403);
        }

        $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $sectionId = !empty($_GET['section_id']) ? (int)$_GET['section_id'] : null;
        $search = trim($_GET['search'] ?? '');
        $limit = max(1, min(100, (int)($_GET['limit'] ?? 25)));
        $page = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $db = new Database();
        $where = "WHERE s.school_id = :sid";
        $params = [':sid' => $schoolId];

        if ($classId) {
            $where .= " AND s.class_id = :cid";
            $params[':cid'] = $classId;
        }
        if ($sectionId) {
            $where .= " AND s.section_id = :secid";
            $params[':secid'] = $sectionId;
        }
        if (!empty($search)) {
            $where .= " AND (s.name LIKE :search OR s.admission_no LIKE :search OR s.roll_no LIKE :search OR s.father_name LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $db->query("SELECT COUNT(*) as total FROM students s {$where}");
        foreach ($params as $k => $v) $db->bind($k, $v);
        $countRow = $db->single();
        $total = $countRow ? (int)$countRow->total : 0;

        $sql = "SELECT s.id, s.admission_no, s.roll_no, s.name, s.father_name, s.gender, s.dob,
                       s.parent_phone, s.student_photo, s.status,
                       c.class_name, sec.section_name
                FROM students s
                LEFT JOIN classes c ON s.class_id = c.id
                LEFT JOIN sections sec ON s.section_id = sec.id
                {$where}
                ORDER BY c.class_name ASC, CAST(s.roll_no AS UNSIGNED) ASC, s.name ASC
                LIMIT :offset, :limit";
        
        $db->query($sql);
        foreach ($params as $k => $v) $db->bind($k, $v);
        $db->bind(':offset', $offset, PDO::PARAM_INT);
        $db->bind(':limit', $limit, PDO::PARAM_INT);
        $records = $db->resultSet();

        foreach ($records as &$st) {
            if (!empty($st->student_photo)) {
                $st->student_photo = URLROOT . '/' . ltrim($st->student_photo, '/');
            }
        }

        ApiGuard::jsonSuccess($records, 'Students list retrieved.', [
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ]);
    }

    /**
     * GET /api/student/{id}
     */
    public function student($id = null) {
        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $targetStudentId = $this->authorizeStudentAccess($user, $id, $schoolId);

        $db = new Database();
        $db->query("SELECT s.*, c.class_name, sec.section_name, a.session_name
                    FROM students s
                    LEFT JOIN classes c ON s.class_id = c.id
                    LEFT JOIN sections sec ON s.section_id = sec.id
                    LEFT JOIN academic_sessions a ON s.academic_session_id = a.id
                    WHERE s.id = :id AND s.school_id = :sid LIMIT 1");
        $db->bind(':id', $targetStudentId);
        $db->bind(':sid', $schoolId);
        $st = $db->single();

        if (!$st) {
            ApiGuard::jsonError('Student not found.', 404);
        }

        if (!empty($st->student_photo)) {
            $st->student_photo = URLROOT . '/' . ltrim($st->student_photo, '/');
        }

        ApiGuard::jsonSuccess($st, 'Student details retrieved.');
    }

    /**
     * POST /api/create-student or /api/students/create
     * Direct Admission registration from mobile app
     */
    public function createStudent() {
        $user = ApiGuard::requireUserAuth();
        $this->requireStaffOrAdmin($user, 'Only school staff can register admissions.');
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $input = $this->getInputData();
        $name = trim($input['name'] ?? '');
        $admissionNo = trim($input['admission_no'] ?? '');
        $classId = (int)($input['class_id'] ?? 0);
        $sectionId = (int)($input['section_id'] ?? 0);
        $gender = trim($input['gender'] ?? 'Male');
        $dob = !empty($input['dob']) ? trim($input['dob']) : null;
        $parentPhone = trim($input['parent_phone'] ?? '');
        $fatherName = trim($input['father_name'] ?? '');
        $address = trim($input['address'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = !empty($input['password']) ? trim($input['password']) : '123456';

        if (empty($name) || empty($admissionNo) || !$classId) {
            ApiGuard::jsonError('Student Name, Admission Number, and Class ID are required.', 422);
        }

        $db = new Database();
        // Check duplicate admission_no
        $db->query("SELECT id FROM students WHERE admission_no = :adm AND school_id = :sid LIMIT 1");
        $db->bind(':adm', $admissionNo);
        $db->bind(':sid', $schoolId);
        if ($db->single()) {
            ApiGuard::jsonError("Admission number '{$admissionNo}' already exists.", 422);
        }

        // Generate student user email if empty
        if (empty($email)) {
            $cleanAdm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $admissionNo));
            $email = "student_{$cleanAdm}@school.local";
        }

        // Create user
        $userModel = $this->model('User');
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        $db->query("INSERT INTO users (school_id, name, email, password, role) VALUES (:sid, :name, :email, :pwd, 'student')");
        $db->bind(':sid', $schoolId);
        $db->bind(':name', $name);
        $db->bind(':email', $email);
        $db->bind(':pwd', $hashedPass);
        $db->execute();
        $userId = $db->lastInsertId();

        // Register student record
        $studentModel = $this->model('Student');
        $stData = [
            'name' => $name,
            'admission_no' => $admissionNo,
            'roll_no' => trim($input['roll_no'] ?? ''),
            'class_id' => $classId,
            'section_id' => $sectionId ?: null,
            'gender' => $gender,
            'dob' => $dob,
            'parent_phone' => $parentPhone,
            'father_name' => $fatherName,
            'address' => $address,
            'email' => $email,
            'status' => 'Active'
        ];

        $studentId = $studentModel->registerStudent($stData, $userId);

        if ($studentId) {
            ApiGuard::jsonSuccess([
                'student_id' => $studentId,
                'user_id' => $userId,
                'admission_no' => $admissionNo,
                'name' => $name,
                'login_email' => $email,
                'initial_password' => $password
            ], 'Student admission registered successfully.');
        } else {
            ApiGuard::jsonError('Failed to register student admission.', 500);
        }
    }

    /**
     * POST /api/update-student or /api/students/update/{id}
     */
    public function updateStudent($id = null) {
        $user = ApiGuard::requireUserAuth();
        $this->requireStaffOrAdmin($user, 'Only staff can update student details.');
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $input = $this->getInputData();
        $studentId = (int)($id ?: ($input['id'] ?? ($input['student_id'] ?? 0)));

        if (!$studentId) {
            ApiGuard::jsonError('Student ID is required for update.', 422);
        }

        $studentModel = $this->model('Student');
        $existing = $studentModel->getStudentById($studentId);
        if (!$existing) {
            ApiGuard::jsonError('Student not found.', 404);
        }

        $updateData = [
            'name' => trim($input['name'] ?? $existing->name),
            'email' => trim($input['email'] ?? $existing->email),
            'phone' => trim($input['phone'] ?? $existing->phone),
            'roll_no' => trim($input['roll_no'] ?? $existing->roll_no),
            'class_id' => !empty($input['class_id']) ? (int)$input['class_id'] : $existing->class_id,
            'section_id' => !empty($input['section_id']) ? (int)$input['section_id'] : $existing->section_id,
            'dob' => !empty($input['dob']) ? trim($input['dob']) : $existing->dob,
            'gender' => trim($input['gender'] ?? $existing->gender),
            'parent_phone' => trim($input['parent_phone'] ?? $existing->parent_phone),
            'address' => trim($input['address'] ?? $existing->address),
            'father_name' => trim($input['father_name'] ?? $existing->father_name)
        ];

        if ($studentModel->updateStudent($studentId, $updateData)) {
            ApiGuard::jsonSuccess($updateData, 'Student record updated successfully.');
        } else {
            ApiGuard::jsonError('Failed to update student record.', 500);
        }
    }

    // =========================================================================
    // 9. NOTICE BOARD (SHOW + POST + DELETE)
    // =========================================================================

    /**
     * GET /api/notices OR multiplexed /api/notices/{action}
     */
    public function notices($action = null, $param2 = null) {
        if ($action === 'create') return $this->createNotice();
        if ($action === 'delete') return $this->deleteNotice($param2);

        // List notices
        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $db = new Database();
        $where = "WHERE school_id = :sid AND (status = 'Published' OR status = 'active')";
        
        if ($user->role === 'student') {
            $where .= " AND is_visible_to_student = 'yes'";
        } elseif ($user->role === 'parent') {
            $where .= " AND is_visible_to_parent = 'yes'";
        } elseif ($user->role === 'teacher' || $user->role === 'staff') {
            $where .= " AND is_visible_to_staff = 'yes'";
        }

        $db->query("SELECT id, title, message, notice_type, priority, publish_date, expiry_date, attachment
                    FROM notice_board
                    {$where}
                    ORDER BY publish_date DESC, id DESC
                    LIMIT 50");
        $db->bind(':sid', $schoolId);
        $notices = $db->resultSet();

        foreach ($notices as &$n) {
            if (!empty($n->attachment)) {
                $n->attachment_url = URLROOT . '/' . ltrim($n->attachment, '/');
            } else {
                $n->attachment_url = null;
            }
        }

        ApiGuard::jsonSuccess($notices, 'School notices and announcements retrieved.');
    }

    /**
     * POST /api/create-notice or /api/notices/create
     * Staff/Teacher posts an announcement from mobile
     */
    public function createNotice() {
        $user = ApiGuard::requireUserAuth();
        $this->requireStaffOrAdmin($user, 'Only staff and teachers can publish notices.');
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $input = $this->getInputData();
        $title = trim($input['title'] ?? '');
        $message = trim($input['message'] ?? '');
        $noticeType = trim($input['notice_type'] ?? 'General Notice');
        $priority = trim($input['priority'] ?? 'Normal');
        $publishDate = !empty($input['publish_date']) ? trim($input['publish_date']) : date('Y-m-d');
        $visStudent = isset($input['visible_to_student']) ? ($input['visible_to_student'] ? 'yes' : 'no') : 'yes';
        $visParent = isset($input['visible_to_parent']) ? ($input['visible_to_parent'] ? 'yes' : 'no') : 'yes';
        $visStaff = isset($input['visible_to_staff']) ? ($input['visible_to_staff'] ? 'yes' : 'no') : 'yes';

        if (empty($title) || empty($message)) {
            ApiGuard::jsonError('Notice Title and Message are required.', 422);
        }

        $noticeModel = $this->model('Notice');
        $data = [
            'title' => $title,
            'message' => $message,
            'notice_type' => $noticeType,
            'priority' => $priority,
            'publish_date' => $publishDate,
            'is_visible_to_student' => $visStudent,
            'is_visible_to_parent' => $visParent,
            'is_visible_to_staff' => $visStaff,
            'created_by' => $user->id
        ];

        $noticeId = $noticeModel ? $noticeModel->addNotice($data) : false;

        if ($noticeId) {
            ApiGuard::jsonSuccess(['notice_id' => $noticeId, 'title' => $title], 'Notice published successfully.');
        } else {
            ApiGuard::jsonError('Failed to publish notice.', 500);
        }
    }

    /**
     * POST /api/delete-notice or /api/notices/delete/{id}
     */
    public function deleteNotice($id = null) {
        $user = ApiGuard::requireUserAuth();
        $this->requireStaffOrAdmin($user, 'Only staff can delete notices.');
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $input = $this->getInputData();
        $noticeId = (int)($id ?: ($input['notice_id'] ?? ($input['id'] ?? 0)));

        if (!$noticeId) {
            ApiGuard::jsonError('Notice ID is required.', 422);
        }

        $noticeModel = $this->model('Notice');
        if ($noticeModel && $noticeModel->deleteNotice($noticeId)) {
            ApiGuard::jsonSuccess(null, 'Notice deleted successfully.');
        } else {
            ApiGuard::jsonError('Failed to delete notice.', 500);
        }
    }

    // =========================================================================
    // 10. TIMETABLE & SCHEDULES
    // =========================================================================

    /**
     * GET /api/timetable
     */
    public function timetable($id = null) {
        $user = ApiGuard::requireUserAuth();
        $schoolId = TenantContext::getSchoolId() ?: (int)$user->school_id;

        $db = new Database();
        $classId = null;
        $sectionId = null;

        if ($user->role === 'student' || !empty($id)) {
            $targetStudentId = $this->authorizeStudentAccess($user, $id, $schoolId);
            $db->query("SELECT class_id, section_id FROM students WHERE id = :sid AND school_id = :school_id LIMIT 1");
            $db->bind(':sid', $targetStudentId);
            $db->bind(':school_id', $schoolId);
            $st = $db->single();
            if ($st) {
                $classId = $st->class_id;
                $sectionId = $st->section_id;
            }
        } elseif ($user->role === 'teacher') {
            $db->query("SELECT ct.*, s.subject_name, s.subject_code, c.class_name, sec.section_name
                        FROM class_timetables ct
                        JOIN subjects s ON ct.subject_id = s.id
                        JOIN classes c ON ct.class_id = c.id
                        JOIN sections sec ON ct.section_id = sec.id
                        WHERE ct.staff_id = :uid AND ct.school_id = :sid
                        ORDER BY FIELD(ct.day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), ct.time_from");
            $db->bind(':uid', $user->id);
            $db->bind(':sid', $schoolId);
            $slots = $db->resultSet();

            ApiGuard::jsonSuccess([
                'type' => 'teacher_schedule',
                'slots' => $slots
            ], 'Teacher timetable retrieved.');
            return;
        } else {
            $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : null;
            $sectionId = !empty($_GET['section_id']) ? (int)$_GET['section_id'] : null;
        }

        if (!$classId || !$sectionId) {
            ApiGuard::jsonError('Class and Section must be specified to view timetable.', 422);
        }

        $db->query("SELECT ct.*, s.subject_name, s.subject_code, s.type as subject_type, u.name as teacher_name
                    FROM class_timetables ct
                    JOIN subjects s ON ct.subject_id = s.id
                    LEFT JOIN users u ON ct.staff_id = u.id
                    WHERE ct.class_id = :cid AND ct.section_id = :sid AND ct.school_id = :school_id
                    ORDER BY FIELD(ct.day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), ct.time_from");
        $db->bind(':cid', $classId);
        $db->bind(':sid', $sectionId);
        $db->bind(':school_id', $schoolId);
        $slots = $db->resultSet();

        $grouped = [
            'Monday' => [], 'Tuesday' => [], 'Wednesday' => [],
            'Thursday' => [], 'Friday' => [], 'Saturday' => [], 'Sunday' => []
        ];

        foreach ($slots as $slot) {
            $day = ucfirst(strtolower($slot->day_name));
            if (isset($grouped[$day])) {
                $grouped[$day][] = $slot;
            } else {
                $grouped[$day] = [$slot];
            }
        }

        ApiGuard::jsonSuccess([
            'class_id' => $classId,
            'section_id' => $sectionId,
            'schedule_by_day' => $grouped
        ], 'Class timetable retrieved.');
    }

    // =========================================================================
    // PRIVATE / INTERNAL HELPER METHODS
    // =========================================================================

    private function resolveUserProfile($userId, $role, $schoolId) {
        $db = new Database();

        if ($role === 'student') {
            $db->query("SELECT s.*, c.class_name, sec.section_name, a.session_name
                        FROM students s
                        LEFT JOIN classes c ON s.class_id = c.id
                        LEFT JOIN sections sec ON s.section_id = sec.id
                        LEFT JOIN academic_sessions a ON s.academic_session_id = a.id
                        WHERE s.user_id = :uid AND s.school_id = :sid LIMIT 1");
            $db->bind(':uid', $userId);
            $db->bind(':sid', $schoolId);
            $st = $db->single();
            if ($st && !empty($st->student_photo)) {
                $st->student_photo = URLROOT . '/' . ltrim($st->student_photo, '/');
            }
            return $st;

        } elseif ($role === 'parent') {
            $db->query("SELECT s.id, s.admission_no, s.roll_no, s.name, s.student_photo, c.class_name, sec.section_name
                        FROM students s
                        LEFT JOIN classes c ON s.class_id = c.id
                        LEFT JOIN sections sec ON s.section_id = sec.id
                        WHERE s.parent_user_id = :uid AND s.school_id = :sid
                        ORDER BY c.class_name ASC, s.name ASC");
            $db->bind(':uid', $userId);
            $db->bind(':sid', $schoolId);
            $children = $db->resultSet();
            foreach ($children as &$c) {
                if (!empty($c->student_photo)) {
                    $c->student_photo = URLROOT . '/' . ltrim($c->student_photo, '/');
                }
            }
            return ['children' => $children];

        } elseif ($role === 'teacher' || $role === 'staff') {
            $db->query("SELECT s.*, d.department_name, ds.designation_name
                        FROM staff s
                        LEFT JOIN departments d ON s.department_id = d.id
                        LEFT JOIN designations ds ON s.designation_id = ds.id
                        WHERE s.user_id = :uid AND s.school_id = :sid LIMIT 1");
            $db->bind(':uid', $userId);
            $db->bind(':sid', $schoolId);
            return $db->single();
        }

        return null;
    }

    private function authorizeStudentAccess($user, $requestedStudentId = null, $schoolId = 1) {
        $db = new Database();

        if ($user->role === 'student') {
            $db->query("SELECT id FROM students WHERE user_id = :uid AND school_id = :sid LIMIT 1");
            $db->bind(':uid', $user->id);
            $db->bind(':sid', $schoolId);
            $own = $db->single();
            if (!$own) {
                ApiGuard::jsonError('No student record found linked to your user account.', 404);
            }
            return (int)$own->id;
        }

        if ($user->role === 'parent') {
            if (!$requestedStudentId) {
                $db->query("SELECT id FROM students WHERE parent_user_id = :uid AND school_id = :sid ORDER BY id ASC LIMIT 1");
                $db->bind(':uid', $user->id);
                $db->bind(':sid', $schoolId);
                $child = $db->single();
                if (!$child) {
                    ApiGuard::jsonError('No child records linked to your parent account.', 404);
                }
                return (int)$child->id;
            } else {
                $db->query("SELECT id FROM students WHERE id = :sid AND parent_user_id = :uid AND school_id = :school_id LIMIT 1");
                $db->bind(':sid', (int)$requestedStudentId);
                $db->bind(':uid', $user->id);
                $db->bind(':school_id', $schoolId);
                $child = $db->single();
                if (!$child) {
                    ApiGuard::jsonError('Unauthorized: This student does not belong to your parent account.', 403);
                }
                return (int)$child->id;
            }
        }

        if (!empty($requestedStudentId)) {
            return (int)$requestedStudentId;
        }

        // For Staff/Admin without student_id parameter, try fetching the first active student
        $db->query("SELECT id FROM students WHERE school_id = :sid ORDER BY id ASC LIMIT 1");
        $db->bind(':sid', $schoolId);
        $st = $db->single();
        if ($st) {
            return (int)$st->id;
        }

        ApiGuard::jsonError('Student ID parameter is required.', 422);
    }
}
