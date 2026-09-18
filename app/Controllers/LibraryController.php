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
            $bookTitle = trim($_POST['book_title'] ?? '');
            $bookNo = trim($_POST['book_no'] ?? '');
            $qty = max(1, (int)($_POST['qty'] ?? 1));
            $price = max(0, (float)($_POST['price'] ?? 0));

            if(empty($bookTitle)){
                $_SESSION['flash_error'] = 'Book title is required.';
                header('Location: ' . URLROOT . '/library/index');
                exit;
            }

            // Auto-generate Book No if empty
            if(empty($bookNo)){
                $bookNo = 'BK-' . strtoupper(substr(uniqid(), -6));
            }

            $data = [
                'book_title' => $bookTitle,
                'book_no'    => $bookNo,
                'isbn'       => trim($_POST['isbn'] ?? ''),
                'author'     => trim($_POST['author'] ?? ''),
                'publisher'  => trim($_POST['publisher'] ?? ''),
                'rack_no'    => trim($_POST['rack_no'] ?? ''),
                'qty'        => $qty,
                'price'      => $price,
                'post_date'  => date('Y-m-d')
            ];
            
            $libModel = $this->model('Library');
            if($libModel->addBook($data)){
                $_SESSION['flash_success'] = 'Book "' . htmlspecialchars($bookTitle) . '" successfully added to library catalog!';
            } else {
                $_SESSION['flash_error'] = 'Failed to add book. Please try again.';
            }
            header('Location: ' . URLROOT . '/library/index');
            exit;
        }
    }

    public function delete($id = 0){
        if($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin' && $_SESSION['user_role'] != 'librarian'){
            header('Location: ' . URLROOT . '/library/index');
            exit;
        }

        $id = (int)$id;
        if($id > 0){
            $libModel = $this->model('Library');
            if($libModel->deleteBook($id)){
                $_SESSION['flash_success'] = 'Book successfully removed from library.';
            } else {
                $_SESSION['flash_error'] = 'Could not delete book.';
            }
        }
        header('Location: ' . URLROOT . '/library/index');
        exit;
    }

    public function issue_return(){
        if($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin' && $_SESSION['user_role'] != 'librarian'){
             header('Location: ' . URLROOT . '/library/index');
             exit;
        }
        
        $libModel = $this->model('Library');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['issue_book'])){
                $bookId = (int)($_POST['book_id'] ?? 0);
                $memberType = trim($_POST['member_type'] ?? 'student');
                
                // Determine user_id based on selected tab/type
                $userId = 0;
                if ($memberType === 'faculty') {
                    $userId = (int)($_POST['faculty_user_id'] ?? 0);
                    $actualType = 'faculty';
                } else {
                    $userId = (int)($_POST['student_user_id'] ?? 0);
                    $actualType = 'student';
                }

                // Fallback to general user_id if specific is empty
                if ($userId <= 0 && !empty($_POST['user_id'])) {
                    $userId = (int)$_POST['user_id'];
                }

                $issueDate = !empty($_POST['issue_date']) ? $_POST['issue_date'] : date('Y-m-d');
                $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : date('Y-m-d', strtotime('+14 days'));

                if ($bookId <= 0 || $userId <= 0) {
                    $_SESSION['flash_error'] = 'Please select both a valid Book and Member to issue.';
                    header('Location: ' . URLROOT . '/library/issue_return');
                    exit;
                }

                $data = [
                    'book_id'    => $bookId,
                    'user_id'    => $userId,
                    'user_type'  => $actualType,
                    'issue_date' => $issueDate,
                    'due_date'   => $dueDate
                ];

                if($libModel->issueBook($data)){
                    $_SESSION['flash_success'] = 'Book issued successfully!';
                } else {
                    $_SESSION['flash_error'] = 'Book could not be issued. Please check book stock availability.';
                }
            } elseif(isset($_POST['return_book'])){
                $issueId = (int)($_POST['issue_id'] ?? 0);
                $fine = max(0, (float)($_POST['fine'] ?? 0));
                if($issueId > 0 && $libModel->returnBook($issueId, date('Y-m-d'), $fine)){
                    if ($fine > 0) {
                        $curr = $_SESSION['currency_symbol'] ?? 'Rs.';
                        $_SESSION['flash_success'] = 'Book returned successfully! Overdue fine of ' . $curr . ' ' . number_format($fine, 2) . ' recorded.';
                    } else {
                        $_SESSION['flash_success'] = 'Book returned successfully and stock updated!';
                    }
                } else {
                    $_SESSION['flash_error'] = 'Failed to process book return.';
                }
            }
            header('Location: ' . URLROOT . '/library/issue_return');
            exit;
        }

        $filter = isset($_GET['filter']) ? trim($_GET['filter']) : null;

        $data = [
            'issued_books'   => $libModel->getIssuedBooks($filter),
            'books'          => $libModel->getBooks(),
            'students'       => $libModel->getStudentsForIssue(),
            'faculty'        => $libModel->getStaffForIssue(),
            'members'        => $libModel->searchMembers(''),
            'current_filter' => $filter
        ];
        $this->view('library/issue_return', $data);
    }
    
    public function send_reminder(){
        if($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin' && $_SESSION['user_role'] != 'librarian'){
            header('Location: ' . URLROOT . '/library/issue_return');
            exit;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $issueId = (int)($_POST['issue_id'] ?? 0);
            $userEmail = trim($_POST['email'] ?? '');
            $userName = trim($_POST['user_name'] ?? 'Library Member');
            $bookTitle = trim($_POST['book_title'] ?? 'Library Book');
            $dueDate = trim($_POST['due_date'] ?? date('Y-m-d'));
            $overdueDays = max(1, (int)($_POST['overdue_days'] ?? 1));
            $fineAmount = max(0, (float)($_POST['fine_amount'] ?? 0));

            $curr = $_SESSION['currency_symbol'] ?? 'Rs.';
            $fineNote = $fineAmount > 0 ? " Current calculated overdue fine is {$curr} " . number_format($fineAmount, 2) . "." : "";

            // 1. Post automated notification into Notice Board
            try {
                $noticeModel = $this->model('Notice');
                $noticeData = [
                    'title' => "Overdue Book Return Notice: " . $bookTitle,
                    'message' => "Dear {$userName},<br><br>The library book <strong>\"{$bookTitle}\"</strong> issued to you was due on <strong>" . date('d M Y', strtotime($dueDate)) . "</strong> and is currently <strong>{$overdueDays} day(s) overdue</strong>.{$fineNote}<br><br>Please return the book to the Library Circulation Desk immediately to avoid further overdue charges or account restrictions.",
                    'notice_type' => 'Library & Reading Notice',
                    'priority' => 'Urgent',
                    'publish_date' => date('Y-m-d'),
                    'status' => 'Published',
                    'is_visible_to_student' => 'yes',
                    'is_visible_to_staff' => 'yes',
                    'is_visible_to_parent' => 'no',
                    'created_by' => $_SESSION['user_id'] ?? 1
                ];
                $noticeModel->addNotice($noticeData);
            } catch (Exception $e) {
                // Ignore notice board insertion if failed
            }

            // 2. Send email reminder if Mailer is configured and email is valid
            $emailSent = false;
            if (!empty($userEmail) && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                require_once APPROOT . '/Core/Mailer.php';
                if (Mailer::isConfigured()) {
                    $settings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];
                    $schoolName = $settings['school_name'] ?? 'School Library';
                    $subject = "[{$schoolName}] Overdue Library Book Reminder: {$bookTitle}";
                    $bodyHtml = "
                        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                            <div style='background: #fee2e2; border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 4px; margin-bottom: 18px;'>
                                <strong style='color: #b91c1c; text-transform: uppercase; font-size: 11px;'>Urgent Circulation Notice</strong>
                                <h3 style='margin: 4px 0 0 0; color: #7f1d1d;'>Overdue Book Return Reminder</h3>
                            </div>
                            <p>Dear <strong>" . htmlspecialchars($userName) . "</strong>,</p>
                            <p>This is a notification from the campus library desk that the following book issued to your account has exceeded its due date:</p>
                            <table style='width: 100%; border-collapse: collapse; margin: 15px 0;'>
                                <tr style='background: #f8fafc;'><td style='padding: 8px; font-weight: bold;'>Book Title:</td><td style='padding: 8px;'>" . htmlspecialchars($bookTitle) . "</td></tr>
                                <tr><td style='padding: 8px; font-weight: bold;'>Due Date:</td><td style='padding: 8px; color: #dc2626;'>" . date('d M Y', strtotime($dueDate)) . " (" . $overdueDays . " days overdue)</td></tr>
                                <tr style='background: #f8fafc;'><td style='padding: 8px; font-weight: bold;'>Overdue Fine:</td><td style='padding: 8px; font-weight: bold; color: #dc2626;'>" . $curr . " " . number_format($fineAmount, 2) . "</td></tr>
                            </table>
                            <p>Please return the book to the Library Circulation Desk as soon as possible.</p>
                            <p style='color: #64748b; font-size: 12px; margin-top: 24px; border-top: 1px solid #e2e8f0; padding-top: 12px;'>Central Campus Library &bull; " . htmlspecialchars($schoolName) . "</p>
                        </div>
                    ";
                    $plainText = "Dear {$userName},\n\nThe book \"{$bookTitle}\" was due on " . date('d M Y', strtotime($dueDate)) . " and is {$overdueDays} days overdue. Calculated fine: {$curr} " . number_format($fineAmount, 2) . ".\nPlease return the book to the library desk immediately.";
                    $emailSent = Mailer::send($userEmail, $subject, $bodyHtml, $plainText);
                }
            }

            if ($emailSent) {
                $_SESSION['flash_success'] = "Overdue alert & email notification successfully sent to " . htmlspecialchars($userName) . " (" . htmlspecialchars($userEmail) . ")!";
            } else {
                $_SESSION['flash_success'] = "Overdue alert posted to Notice Board for " . htmlspecialchars($userName) . "!";
            }
        }
        header('Location: ' . URLROOT . '/library/issue_return');
        exit;
    }

    public function get_members(){
        $term = isset($_GET['q']) ? trim($_GET['q']) : '';
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $libModel = $this->model('Library');
        
        if ($type === 'student') {
            $members = $libModel->getStudentsForIssue();
        } elseif ($type === 'faculty') {
            $members = $libModel->getStaffForIssue();
        } else {
            $members = $libModel->searchMembers($term);
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($members);
        exit;
    }
}


