<?php
class LibraryController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!AuthGuard::hasPermission('manage_library') && !in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'librarian'])) {
                AuthGuard::requirePermission('manage_library');
            }
            if (class_exists('AuthGuard')) {
                AuthGuard::verifyCSRF();
            }
        } else {
            if (!AuthGuard::hasPermission('manage_library') && !in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'librarian', 'teacher', 'student'])) {
                AuthGuard::requirePermission('manage_library');
            }
        }
        if(!isset($_SESSION['user_id'])){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }
    }

    public function index(){
        $libModel = $this->model('Library');
        $perPage = 20;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $totalBooks = $libModel->countBooks();
        $totalPages = max(1, (int)ceil($totalBooks / $perPage));
        if($page > $totalPages){
            $page = $totalPages;
        }
        $offset = ($page - 1) * $perPage;

        $data = [
            'books' => $libModel->getBooksPaginated($perPage, $offset),
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_books' => $totalBooks
        ];
        $this->view('library/index', $data);
    }
    
    public function add(){
        if($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin' && $_SESSION['user_role'] != 'librarian'){
             header('Location: ' . URLROOT . '/library/index');
             exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'book_title' => trim($_POST['book_title']),
                'book_no' => trim($_POST['book_no']),
                'isbn' => trim($_POST['isbn']),
                'author' => trim($_POST['author']),
                'publisher' => trim($_POST['publisher']),
                'rack_no' => trim($_POST['rack_no']),
                'qty' => (int)$_POST['qty'],
                'price' => (float)$_POST['price'],
                'post_date' => date('Y-m-d')
            ];
            
            $libModel = $this->model('Library');
            if($libModel->addBook($data)){
                header('Location: ' . URLROOT . '/library/index');
            } else {
                die("Something went wrong");
            }
        }
    }

    public function issue_return(){
        if($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin' && $_SESSION['user_role'] != 'librarian'){
             header('Location: ' . URLROOT . '/library/index');
             exit;
        }
        
        $libModel = $this->model('Library');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['issue_book'])){
                $data = [
                    'book_id' => $_POST['book_id'],
                    'user_id' => $_POST['user_id'],
                    'user_type' => 'student', // simplified or fetch from user
                    'issue_date' => $_POST['issue_date'],
                    'due_date' => $_POST['due_date']
                ];
                $libModel->issueBook($data);
            } elseif(isset($_POST['return_book'])){
                $libModel->returnBook($_POST['issue_id'], date('Y-m-d'));
            }
            header('Location: ' . URLROOT . '/library/issue_return');
            exit;
        }

        $data = [
            'issued_books' => $libModel->getIssuedBooks(),
            'books' => $libModel->getBooks(),
            'members' => $libModel->searchMembers('') // Improve with AJAX in real app
        ];
        $this->view('library/issue_return', $data);
    }
    
    // AJAX helper for member search could go here
    public function get_members(){
        // Simple JSON return for Select2 or similar if implemented
        $term = isset($_GET['q']) ? $_GET['q'] : '';
        $libModel = $this->model('Library');
        $members = $libModel->searchMembers($term);
        echo json_encode($members);
    }
}
