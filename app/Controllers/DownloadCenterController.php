<?php
class DownloadCenterController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
         if(!isset($_SESSION['user_id'])){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }
    }

    public function index(){
        $downloadModel = $this->model('DownloadCenter');
        $classModel = $this->model('SchoolClass');
        $roles = $_SESSION['user_role'];
        
        if($_SERVER['REQUEST_METHOD'] == 'POST' && ($roles == 'admin' || $roles == 'super_admin' || $roles == 'teacher')){
            // Handle Upload
            $data = [
                'title' => trim($_POST['title']),
                'type' => $_POST['content_type'],
                'available_for' => $_POST['available_for'],
                'class_id' => !empty($_POST['class_id']) ? $_POST['class_id'] : null,
                'section_id' => null, // Simplified
                'description' => trim($_POST['description']),
                'upload_date' => date('Y-m-d'),
                'uploaded_by' => $_SESSION['user_id'],
                'file_path' => ''
            ];

            // File Upload
            if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
                require_once APPROOT . '/Core/UploadHandler.php';
                $upload = UploadHandler::processUpload($_FILES['file'], 'content');
                if($upload['success']){
                    $data['file_path'] = $upload['path'];
                    $downloadModel->addContent($data);
                } else {
                    die("Upload failed: " . $upload['error']);
                }
            }
            header('Location: ' . URLROOT . '/downloadcenter/index');
            exit;
        }
        
        $class_id = null;
        if($roles == 'student'){
            // Get Student Class
             $studentModel = $this->model('Student');
             $student = $studentModel->getStudentByUserId($_SESSION['user_id']);
             if($student) $class_id = $student->class_id;
        }

        $data = [
            'contents' => $downloadModel->getContent($roles, $class_id),
            'classes' => $classModel->getClasses()
        ];
        
        $this->view('download_center/index', $data);
    }
    
    public function delete($id){
        if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'super_admin'){
             $model = $this->model('DownloadCenter');
             $model->deleteContent($id);
        }
        header('Location: ' . URLROOT . '/downloadcenter/index');
    }
}
