<?php
// app/Controllers/SectionsController.php

class SectionsController extends Controller {
    private $sectionModel;
    private $classModel;
    private $userModel;

    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_academics');
        if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'super_admin' && $_SESSION['user_role'] != 'admin')){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }

        $this->sectionModel = $this->model('Section');
        $this->classModel = $this->model('SchoolClass');
        $this->userModel = $this->model('User');
    }

    public function index(){
        $sections = $this->sectionModel->getSections();
        $classes = $this->classModel->getClasses();
        $teachers = $this->userModel->getUsersByRole('teacher');

        $data = [
            'sections' => $sections,
            'classes' => $classes,
            'teachers' => $teachers
        ];

        $this->view('sections/index', $data);
    }

    public function add(){
         if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'class_id' => trim($_POST['class_id'] ?? ''),
                'section_name' => trim($_POST['section_name'] ?? ''),
                'class_teacher_id' => !empty($_POST['class_teacher_id']) ? (int)$_POST['class_teacher_id'] : null
            ];

            if(!empty($data['section_name']) && !empty($data['class_id'])){
                $this->sectionModel->addSection($data);
                header('Location: ' . URLROOT . '/sections/index?success=created');
                exit;
            }
        }
        header('Location: ' . URLROOT . '/sections/index');
        exit;
    }

    public function edit($id = null){
        if(!$id && isset($_POST['id'])) $id = (int)$_POST['id'];

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'id' => (int)$id,
                'class_id' => trim($_POST['class_id'] ?? ''),
                'section_name' => trim($_POST['section_name'] ?? ''),
                'class_teacher_id' => !empty($_POST['class_teacher_id']) ? (int)$_POST['class_teacher_id'] : null
            ];

            if(!empty($data['section_name']) && !empty($data['class_id']) && $id){
                $this->sectionModel->updateSection($data);
                header('Location: ' . URLROOT . '/sections/index?success=updated');
                exit;
            }
        }

        $section = $this->sectionModel->getSectionById($id);
        if(isset($_GET['ajax'])){
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$section, 'section' => $section]);
            exit;
        }

        header('Location: ' . URLROOT . '/sections/index');
        exit;
    }

    public function delete($id){
         if($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['confirm'])){
             $this->sectionModel->deleteSection((int)$id);
             header('Location: ' . URLROOT . '/sections/index?success=deleted');
             exit;
         }
         header('Location: ' . URLROOT . '/sections/index');
         exit;
    }
}
