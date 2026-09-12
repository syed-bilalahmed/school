<?php
// app/Controllers/SubjectsController.php

class SubjectsController extends Controller {
    private $subjectModel;

    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_academics');

        if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'super_admin' && $_SESSION['user_role'] != 'admin')){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthGuard::verifyCSRF();
        }

        $this->subjectModel = $this->model('Subject');
    }

    // List all subjects
    public function index(){
        $subjects = $this->subjectModel->getSubjects();
        $data = [
            'subjects' => $subjects
        ];

        $this->view('subjects/index', $data);
    }

    // Add generic subject
    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'type' => trim($_POST['type'] ?? 'Theory'),
                'is_core' => isset($_POST['is_core']) ? (int)$_POST['is_core'] : 1,
                'full_marks' => !empty($_POST['full_marks']) ? (float)$_POST['full_marks'] : 100.00,
                'passing_marks' => !empty($_POST['passing_marks']) ? (float)$_POST['passing_marks'] : 33.00,
                'credit_hours' => !empty($_POST['credit_hours']) ? (int)$_POST['credit_hours'] : 3
            ];

            if(!empty($data['name'])){
                $this->subjectModel->addSubject($data);
                header('Location: ' . URLROOT . '/subjects/index?success=created');
                exit;
            }
        }
        header('Location: ' . URLROOT . '/subjects/index');
        exit;
    }

    // Edit subject
    public function edit($id = null){
        if(!$id && isset($_POST['id'])) $id = (int)$_POST['id'];

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'id' => (int)$id,
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'type' => trim($_POST['type'] ?? 'Theory'),
                'is_core' => isset($_POST['is_core']) ? (int)$_POST['is_core'] : 1,
                'full_marks' => !empty($_POST['full_marks']) ? (float)$_POST['full_marks'] : 100.00,
                'passing_marks' => !empty($_POST['passing_marks']) ? (float)$_POST['passing_marks'] : 33.00,
                'credit_hours' => !empty($_POST['credit_hours']) ? (int)$_POST['credit_hours'] : 3
            ];

            if(!empty($data['name']) && $id){
                $this->subjectModel->updateSubject($data);
                header('Location: ' . URLROOT . '/subjects/index?success=updated');
                exit;
            }
        }

        $subject = $this->subjectModel->getSubjectById($id);
        if(isset($_GET['ajax'])){
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$subject, 'subject' => $subject]);
            exit;
        }

        header('Location: ' . URLROOT . '/subjects/index');
        exit;
    }

    // Delete subject
    public function delete($id){
        if($id){
            $this->subjectModel->deleteSubject((int)$id);
        }
        header('Location: ' . URLROOT . '/subjects/index?success=deleted');
        exit;
    }

    // Assign Subject to Class & Workload Dashboard
    public function assign(){
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $userModel = $this->model('User');

        $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $sectionId = !empty($_GET['section_id']) ? (int)$_GET['section_id'] : null;

        $data = [
            'classes' => $classModel->getClasses(),
            'sections' => $sectionModel->getSections(),
            'subjects' => $this->subjectModel->getSubjects(),
            'teachers' => $userModel->getUsersByRole('teacher'),
            'allocations' => $this->subjectModel->getAllAllocations($classId, $sectionId),
            'workloads' => $this->subjectModel->getTeacherWorkloadSummary(),
            'selected_class' => $classId,
            'selected_section' => $sectionId
        ];

        $this->view('subjects/assign', $data);
    }

    public function store_assign(){
       if($_SERVER['REQUEST_METHOD'] == 'POST'){
           $data = [
               'class_id' => (int)$_POST['class_id'],
               'section_id' => (int)$_POST['section_id'],
               'subject_id' => (int)$_POST['subject_id'],
               'teacher_id' => !empty($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : null,
               'periods_per_week' => !empty($_POST['periods_per_week']) ? (int)$_POST['periods_per_week'] : 5
           ];
           
           if(!empty($data['class_id']) && !empty($data['section_id']) && !empty($data['subject_id'])){
               $this->subjectModel->assignSubject($data);
               header('Location: ' . URLROOT . '/subjects/assign?class_id=' . $data['class_id'] . '&section_id=' . $data['section_id'] . '&success=assigned');
               exit;
           }
       }
       header('Location: ' . URLROOT . '/subjects/assign');
       exit;
    }

    public function delete_assign($id){
        if($id){
            $this->subjectModel->deleteClassSubject((int)$id);
        }
        $referer = $_SERVER['HTTP_REFERER'] ?? URLROOT . '/subjects/assign';
        header('Location: ' . $referer . (strpos($referer, '?') !== false ? '&' : '?') . 'success=deleted');
        exit;
    }
}
