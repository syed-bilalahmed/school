<?php
class HomeworkController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!AuthGuard::hasPermission('manage_academics') && !in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'teacher'])) {
                AuthGuard::requirePermission('manage_academics');
            }
            AuthGuard::verifyCSRF();
        } else {
            if (!AuthGuard::hasPermission('manage_academics') && !AuthGuard::hasPermission('view_academics') && !in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'teacher', 'student', 'parent'])) {
                AuthGuard::requirePermission('manage_academics');
            }
        }
        if(!isset($_SESSION['user_id'])){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }
    }

    public function index(){
        $homeworkModel = $this->model('Homework');
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $subjectModel = $this->model('Subject');
        
        // Add Homework Logic
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['add_homework'])){
                $data = [
                    'class_id' => $_POST['class_id'],
                    'section_id' => $_POST['section_id'],
                    'subject_id' => $_POST['subject_id'],
                    'homework_date' => $_POST['homework_date'],
                    'submission_date' => $_POST['submission_date'],
                    'description' => $_POST['description'],
                    'created_by' => $_SESSION['user_id']
                ];
                $homeworkModel->addHomework($data);
                header('Location: ' . URLROOT . '/homework/index');
                exit;
            }
        }

        $data = [
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'subjects' => [], // Potentially need to load all subjects or by generic?
                              // For the form, we need to pick subjects dynamically via JS, 
                              // OR load ALL subjects if not filtering.
                              // Let's load generic subjects for now and rely on correct selection.
                              // Better: Reuse the 'getSubjectsByClassSection' via AJAX strictly.
                              // For simplicity in non-ajax, let's just list all distinct subjects? 
                              // No, let's just list filtering if GET params set.
            'homeworks' => $homeworkModel->getHomework() 
            // Filter logic can be added later
        ];
        
        // Load subjects for initial dropdown if needed, or rely on JS.
        // For simplicity, we just won't populate Subject dropdown initially unless Class/Section selected.
        // But since we are doing a simple reload...
        
        if(isset($_GET['class_id']) && isset($_GET['section_id'])){
            $data['subjects'] = $subjectModel->getSubjectsByClassSection($_GET['class_id'], $_GET['section_id']);
            $data['class_id'] = $_GET['class_id'];
            $data['section_id'] = $_GET['section_id'];
        }

        $this->view('homework/index', $data);
    }
    
    // JS Helper to get subjects
    public function get_subjects($class_id, $section_id){
        $subjectModel = $this->model('Subject');
        $subjects = $subjectModel->getSubjectsByClassSection($class_id, $section_id);
        echo json_encode($subjects);
    }

    public function evaluate($id){
        $homeworkModel = $this->model('Homework');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Save Evaluation
            if(isset($_POST['students'])){
                foreach($_POST['students'] as $sid => $val){
                    // Checkbox for status? Or dropdown?
                    // Let's assume checkbox = completed? 
                    // Or explicit status. 
                    $status = isset($val['status']) ? $val['status'] : 'Pending';
                    $marks = isset($val['marks']) ? $val['marks'] : 0;
                    $note = isset($val['note']) ? $val['note'] : '';
                    
                    $homeworkModel->saveEvaluation($id, $sid, $status, $marks, $note);
                }
            }
            header('Location: ' . URLROOT . '/homework/evaluate/' . $id);
            exit;
        }

        $hw = $homeworkModel->getHomeworkById($id);
        $data = [
            'homework' => $hw,
            'students' => $homeworkModel->getEvaluation($id)
        ];
        $this->view('homework/evaluate', $data);
    }
}
