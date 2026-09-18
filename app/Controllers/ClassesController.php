<?php
class ClassesController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_academics');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && class_exists('AuthGuard')) {
            AuthGuard::verifyCSRF();
        }
    }

    public function index(){
        $classModel = $this->model('SchoolClass');
        $classes = $classModel->getClassesWithStats();

        // Calculate summary metrics
        $totalClasses = count($classes);
        $totalSections = 0;
        $totalStudents = 0;

        foreach ($classes as $cls) {
            $totalSections += (int)($cls->section_count ?? 0);
            $totalStudents += (int)($cls->student_count ?? 0);
        }

        $avgStudentsPerClass = $totalClasses > 0 ? round($totalStudents / $totalClasses, 1) : 0;

        $data = [
            'classes' => $classes,
            'total_classes' => $totalClasses,
            'total_sections' => $totalSections,
            'total_students' => $totalStudents,
            'avg_students' => $avgStudentsPerClass
        ];

        $this->view('classes/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (class_exists('AuthGuard')) AuthGuard::verifyCSRF();

            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                   || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                   || isset($_POST['ajax_submit']);

            $className = trim((string)($_POST['class_name'] ?? ''));
            $classModel = $this->model('SchoolClass');

            if (empty($className)) {
                $errorMsg = 'Class name cannot be empty.';
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
                $_SESSION['flash_error'] = $errorMsg;
                header('Location: ' . URLROOT . '/classes/index');
                exit;
            }

            if ($classModel->isClassNameTaken($className)) {
                $errorMsg = "Class '{$className}' already exists.";
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
                $_SESSION['flash_error'] = $errorMsg;
                header('Location: ' . URLROOT . '/classes/index');
                exit;
            }

            $insertId = $classModel->addClass(['class_name' => $className]);

            if ($insertId) {
                $successMsg = "Class '{$className}' added successfully.";
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => $successMsg,
                        'id' => $insertId,
                        'class_name' => $className
                    ]);
                    exit;
                }
                $_SESSION['flash_success'] = $successMsg;
            } else {
                $errorMsg = 'Failed to create class in database.';
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
                $_SESSION['flash_error'] = $errorMsg;
            }

            header('Location: ' . URLROOT . '/classes/index');
            exit;
        }

        header('Location: ' . URLROOT . '/classes/index');
        exit;
    }

    public function edit($id = 0){
        $id = (int)$id;
        $classModel = $this->model('SchoolClass');
        $cls = $classModel->getClassById($id);

        if (!$cls) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                if (ob_get_length()) ob_clean();
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Class not found.']);
                exit;
            }
            $_SESSION['flash_error'] = 'Class record not found.';
            header('Location: ' . URLROOT . '/classes/index');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->update($id);
            return;
        }

        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'class' => $cls
        ]);
        exit;
    }

    public function update($id = 0){
        $id = (int)($id ?: ($_POST['id'] ?? 0));
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (class_exists('AuthGuard')) AuthGuard::verifyCSRF();

            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                   || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                   || isset($_POST['ajax_submit']);

            $className = trim((string)($_POST['class_name'] ?? ''));
            $classModel = $this->model('SchoolClass');

            if ($id <= 0) {
                $errorMsg = 'Invalid class identifier.';
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
                $_SESSION['flash_error'] = $errorMsg;
                header('Location: ' . URLROOT . '/classes/index');
                exit;
            }

            if (empty($className)) {
                $errorMsg = 'Class name cannot be empty.';
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
                $_SESSION['flash_error'] = $errorMsg;
                header('Location: ' . URLROOT . '/classes/index');
                exit;
            }

            if ($classModel->isClassNameTaken($className, $id)) {
                $errorMsg = "Another class with name '{$className}' already exists.";
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
                $_SESSION['flash_error'] = $errorMsg;
                header('Location: ' . URLROOT . '/classes/index');
                exit;
            }

            $updated = $classModel->updateClass(['id' => $id, 'class_name' => $className]);

            if ($updated) {
                $successMsg = "Class updated to '{$className}' successfully.";
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => $successMsg,
                        'id' => $id,
                        'class_name' => $className
                    ]);
                    exit;
                }
                $_SESSION['flash_success'] = $successMsg;
            } else {
                $errorMsg = 'Failed to update class in database.';
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
                $_SESSION['flash_error'] = $errorMsg;
            }

            header('Location: ' . URLROOT . '/classes/index');
            exit;
        }

        header('Location: ' . URLROOT . '/classes/index');
        exit;
    }

    public function delete($id = 0){
        $id = (int)($id ?: ($_POST['id'] ?? 0));

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (class_exists('AuthGuard')) AuthGuard::verifyCSRF();

            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                   || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                   || isset($_POST['ajax_submit']);

            $classModel = $this->model('SchoolClass');
            $cls = $classModel->getClassById($id);

            if (!$cls) {
                $errorMsg = 'Class record not found.';
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
                $_SESSION['flash_error'] = $errorMsg;
                header('Location: ' . URLROOT . '/classes/index');
                exit;
            }

            // Enrollment safety check: prevent deleting classes with active students
            $studentCount = $classModel->getClassStudentCount($id);
            if ($studentCount > 0) {
                $errorMsg = "Cannot delete '{$cls->class_name}': {$studentCount} student(s) are currently enrolled in this class. Please reassign or promote students before deleting.";
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
                $_SESSION['flash_error'] = $errorMsg;
                header('Location: ' . URLROOT . '/classes/index');
                exit;
            }

            if ($classModel->deleteClass($id)) {
                $successMsg = "Class '{$cls->class_name}' deleted successfully.";
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => $successMsg, 'id' => $id]);
                    exit;
                }
                $_SESSION['flash_success'] = $successMsg;
            } else {
                $errorMsg = 'Failed to delete class from database.';
                if ($isAjax) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => $errorMsg]);
                    exit;
                }
                $_SESSION['flash_error'] = $errorMsg;
            }

            header('Location: ' . URLROOT . '/classes/index');
            exit;
        }

        header('Location: ' . URLROOT . '/classes/index');
        exit;
    }
}
