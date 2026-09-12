<?php
class ExportController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
         if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin')){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }
    }

    public function index(){
        $this->view('export/index');
    }

    // Generic Export Helper
    private function exportCSV($filename, $header, $data){
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $header);
        
        foreach($data as $row){
             // Ensure row matches header order/content
             // We'll rely on controller to pass correct assoc or array, or we cast.
             // But fputcsv expects array. if object, convert.
             if(is_object($row)) $row = (array)$row;
             fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public function students(){
        $studentModel = $this->model('Student');
        // Fetch All Students
        $students = $studentModel->getStudents(); 
        // Need specific columns for CSV?
        // Let's refine the query or map data.
        
        $header = ['ID', 'Admission No', 'Roll No', 'Name', 'Class', 'Section', 'Email', 'Gender', 'Father Name', 'Mobile'];
        $exportData = [];
        
        foreach($students as $s){
            $exportData[] = [
                $s->id,
                $s->admission_no,
                $s->roll_no,
                $s->name,
                $s->class_name,
                $s->section_name,
                $s->email,
                $s->gender,
                $s->father_name,
                $s->mobileno
            ];
        }
        
        $this->exportCSV('students_' . date('Y-m-d') . '.csv', $header, $exportData);
    }

    public function staff(){
        // Get Users where role != student
        $db = new Database;
        $db->query("SELECT * FROM users WHERE role != 'student' AND role != 'parent'");
        $staffs = $db->resultSet();
        
        $header = ['ID', 'Name', 'Email', 'Role', 'Status'];
        $exportData = [];
        
        foreach($staffs as $st){
             $exportData[] = [
                 $st->id,
                 $st->name,
                 $st->email,
                 $st->role,
                 'Active' // Assuming no status col
             ];
        }
        
        $this->exportCSV('staff_' . date('Y-m-d') . '.csv', $header, $exportData);
    }
}
