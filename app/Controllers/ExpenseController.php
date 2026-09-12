<?php
class ExpenseController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_finance');
         if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin')){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthGuard::verifyCSRF();
        }
    }

    public function index(){
        $expModel = $this->model('Expense');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['add_head'])){
                $data = [
                    'exp_category' => trim($_POST['exp_category']),
                    'description' => trim($_POST['description'])
                ];
                $expModel->addHead($data);
                 
            } elseif(isset($_POST['delete_head'])){
                $expModel->deleteHead($_POST['head_id']);
                
            } elseif(isset($_POST['add_expense'])){
                 $data = [
                     'exp_head_id' => $_POST['exp_head_id'],
                     'name' => trim($_POST['name']),
                     'invoice_no' => trim($_POST['invoice_no']),
                     'date' => $_POST['date'],
                     'amount' => (float)$_POST['amount'],
                     'description' => trim($_POST['description']),
                     'documents' => ''
                 ];
                 
                 // Handle File Upload
                 if(isset($_FILES['documents']) && $_FILES['documents']['error'] == 0){
                     require_once APPROOT . '/Core/UploadHandler.php';
                     $upload = UploadHandler::processUpload($_FILES['documents'], 'expenses');
                     if($upload['success']){
                         $data['documents'] = $upload['path'];
                     } else {
                         die("Upload failed: " . $upload['error']);
                     }
                 }
                 
                 $expModel->addExpense($data);
                 
            } elseif(isset($_POST['delete_expense'])){
                $expModel->deleteExpense($_POST['expense_id']);
            }
            
            header('Location: ' . URLROOT . '/expense/index');
            exit;
        }

        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $perPage = 25;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $totalExpenses = $expModel->countExpenses($search);
        $totalPages = max(1, (int)ceil($totalExpenses / $perPage));
        if($page > $totalPages){
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;
        
        $data = [
            'heads' => $expModel->getHeads(),
            'expenses' => $expModel->getExpensesPaginated($search, $perPage, $offset),
            'search' => $search,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_expenses' => $totalExpenses
        ];
        
        $this->view('expenses/index', $data);
    }
}

if (!class_exists('ExpensesController', false)) {
    class_alias('ExpenseController', 'ExpensesController');
}
if (!class_exists('Expensecontroller', false)) {
    class_alias('ExpenseController', 'Expensecontroller');
}

