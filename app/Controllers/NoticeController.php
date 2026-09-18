<?php
class NoticeController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();

        $allowedRoles = ['admin', 'super_admin', 'teacher', 'student', 'parent', 'accountant', 'receptionist', 'librarian'];
        $userRole = $_SESSION['user_role'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!in_array($userRole, ['admin', 'super_admin', 'librarian']) && !AuthGuard::hasPermission('manage_communication')) {
                AuthGuard::requirePermission('manage_communication');
            }
            if (class_exists('AuthGuard')) {
                AuthGuard::verifyCSRF();
            }
        } else {
            if (!in_array($userRole, $allowedRoles) && !AuthGuard::hasPermission('manage_communication') && !AuthGuard::hasPermission('view_communication')) {
                AuthGuard::requirePermission('manage_communication');
            }
        }

        if(!isset($_SESSION['user_id'])){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }
    }

    /**
     * Main Notice Board & Circulars Index
     */
    public function index(){
        $noticeModel = $this->model('Notice');
        $userRole = $_SESSION['user_role'] ?? 'admin';
        $isAdmin = in_array($userRole, ['admin', 'super_admin', 'librarian']) || AuthGuard::hasPermission('manage_communication');

        // Handle direct POST notice creation
        if($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin){
            $this->saveNotice(null);
            return;
        }

        // Collect search & filter criteria from GET
        $filters = [
            'search'   => trim($_GET['search'] ?? ($_GET['q'] ?? '')),
            'category' => trim($_GET['category'] ?? 'all'),
            'priority' => trim($_GET['priority'] ?? 'all'),
            'audience' => trim($_GET['audience'] ?? 'all'),
            'status'   => trim($_GET['status'] ?? 'all')
        ];

        // Fetch notices matching criteria and role permissions
        $notices = $noticeModel->getNotices($isAdmin ? null : $userRole, $filters);
        $stats = $noticeModel->getNoticeStats();

        // Get global institutional branding & settings
        $settings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];

        $data = [
            'notices'    => $notices,
            'stats'      => $stats,
            'filters'    => $filters,
            'isAdmin'    => $isAdmin,
            'userRole'   => $userRole,
            'settings'   => $settings
        ];

        $this->view('notice/index', $data);
    }

    /**
     * Explicit Store / Create endpoint
     */
    public function create(){
        $this->saveNotice(null);
    }

    /**
     * Update existing notice
     */
    public function update($id = null){
        if (empty($id) && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
        }
        $this->saveNotice($id);
    }

    /**
     * Common Save Logic (Insert or Update)
     */
    private function saveNotice($id = null){
        $userRole = $_SESSION['user_role'] ?? 'admin';
        $isAdmin = in_array($userRole, ['admin', 'super_admin', 'librarian']) || AuthGuard::hasPermission('manage_communication');

        if (!$isAdmin) {
            $this->respond([
                'status' => 'error',
                'message' => 'Unauthorized: You do not have permission to post or edit circulars.'
            ], 403);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($title) || empty($message)) {
            $_SESSION['flash_error'] = 'Please provide both a circular title and message content.';
            if ($this->isAjax()) {
                $this->respond(['status' => 'error', 'message' => $_SESSION['flash_error']], 422);
                return;
            }
            header('Location: ' . URLROOT . '/notice/index');
            exit;
        }

        $data = [
            'title'                 => $title,
            'message'               => $message,
            'notice_type'           => trim($_POST['notice_type'] ?? ($_POST['category'] ?? 'General Notice')),
            'priority'              => trim($_POST['priority'] ?? 'Normal'),
            'publish_date'          => !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d'),
            'expiry_date'           => !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
            'status'                => trim($_POST['status'] ?? 'Published'),
            'is_visible_to_student' => isset($_POST['visible_student']) ? 'yes' : 'no',
            'is_visible_to_staff'   => isset($_POST['visible_staff']) ? 'yes' : 'no',
            'is_visible_to_parent'  => isset($_POST['visible_parent']) ? 'yes' : 'no',
            'created_by'            => $_SESSION['user_id'] ?? 1
        ];

        // Process PDF or document upload if present
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['attachment'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
            if (in_array($ext, $allowed)) {
                $targetDir = dirname(dirname(__DIR__)) . '/public/uploads/notices';
                if (!is_dir($targetDir)) {
                    @mkdir($targetDir, 0777, true);
                }
                $cleanBase = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
                $newFileName = $cleanBase . '_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $targetDir . '/' . $newFileName)) {
                    $data['attachment'] = 'uploads/notices/' . $newFileName;
                }
            }
        }

        $noticeModel = $this->model('Notice');

        if (!empty($id)) {
            $result = $noticeModel->updateNotice($id, $data);
            $msg = 'Official circular updated successfully.';
        } else {
            $result = $noticeModel->addNotice($data);
            $msg = 'Official circular published successfully to the Notice Board.';
        }

        if ($result) {
            $newNoticeId = is_numeric($result) ? (int)$result : (int)$id;
            // If email broadcast was checked during creation/update
            if (!empty($_POST['dispatch_email'])) {
                $targetAudiences = $_POST['email_targets'] ?? ['all_users'];
                if (!is_array($targetAudiences)) $targetAudiences = [$targetAudiences];
                $allRecipients = [];
                foreach ($targetAudiences as $aud) {
                    $recips = $noticeModel->getRecipientsByAudience($aud);
                    foreach ($recips as $em => $nm) {
                        $allRecipients[$em] = $nm;
                    }
                }
                if (!empty($allRecipients)) {
                    $noticeObj = $noticeModel->getNoticeById($newNoticeId);
                    if ($noticeObj) {
                        $dispatchStats = $this->dispatchNoticeEmail($noticeObj, $allRecipients);
                        $msg .= " Emailed to {$dispatchStats['sent']} recipient(s) via Gmail SMTP.";
                    }
                }
            }

            $_SESSION['flash_success'] = $msg;
            if ($this->isAjax()) {
                $this->respond(['status' => 'success', 'message' => $msg]);
                return;
            }
        } else {
            $_SESSION['flash_error'] = 'Failed to save notice. Please check your inputs and try again.';
            if ($this->isAjax()) {
                $this->respond(['status' => 'error', 'message' => $_SESSION['flash_error']], 500);
                return;
            }
        }

        header('Location: ' . URLROOT . '/notice/index');
        exit;
    }

    /**
     * Broadcast any existing notice to Gmail/Email:
     * - 'all_users': all registered users
     * - 'parents': all parents/guardians
     * - 'staff': all faculty/staff members
     */
    public function broadcastEmail($id = null){
        $userRole = $_SESSION['user_role'] ?? 'admin';
        $isAdmin = in_array($userRole, ['admin', 'super_admin']) || AuthGuard::hasPermission('manage_communication');

        if (!$isAdmin) {
            $this->respond(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
            return;
        }

        if (empty($id) && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
        }

        if (empty($id)) {
            $msg = 'Invalid circular ID specified.';
            $_SESSION['flash_error'] = $msg;
            if ($this->isAjax()) {
                $this->respond(['status' => 'error', 'message' => $msg], 400);
                return;
            }
            header('Location: ' . URLROOT . '/notice/index');
            exit;
        }

        $noticeModel = $this->model('Notice');
        $notice = $noticeModel->getNoticeById($id);
        if (!$notice) {
            $msg = 'Circular record not found.';
            $_SESSION['flash_error'] = $msg;
            if ($this->isAjax()) {
                $this->respond(['status' => 'error', 'message' => $msg], 404);
                return;
            }
            header('Location: ' . URLROOT . '/notice/index');
            exit;
        }

        $targets = $_POST['target_audience'] ?? ['all_users'];
        if (!is_array($targets)) {
            $targets = [$targets];
        }

        $allRecipients = [];
        foreach ($targets as $aud) {
            $recips = $noticeModel->getRecipientsByAudience($aud);
            foreach ($recips as $email => $name) {
                $allRecipients[$email] = $name;
            }
        }

        if (empty($allRecipients)) {
            $msg = 'No recipients with valid email addresses found for the chosen audience.';
            $_SESSION['flash_error'] = $msg;
            if ($this->isAjax()) {
                $this->respond(['status' => 'error', 'message' => $msg], 422);
                return;
            }
            header('Location: ' . URLROOT . '/notice/index');
            exit;
        }

        require_once APPROOT . '/Core/Mailer.php';
        if (!Mailer::isConfigured()) {
            $msg = 'SMTP outgoing mail server is not configured yet. Please configure it in System Settings > Outgoing Mail Server first.';
            $_SESSION['flash_error'] = $msg;
            if ($this->isAjax()) {
                $this->respond(['status' => 'error', 'message' => $msg], 400);
                return;
            }
            header('Location: ' . URLROOT . '/notice/index');
            exit;
        }

        $dispatchResult = $this->dispatchNoticeEmail($notice, $allRecipients);

        $msg = "Notice email broadcast successfully delivered to {$dispatchResult['sent']} recipient(s) via Gmail SMTP.";
        if ($dispatchResult['failed'] > 0) {
            $msg .= " ({$dispatchResult['failed']} failed).";
        }

        $_SESSION['flash_success'] = $msg;

        if ($this->isAjax()) {
            $this->respond([
                'status' => 'success',
                'message' => $msg,
                'sent_count' => $dispatchResult['sent'],
                'failed_count' => $dispatchResult['failed']
            ]);
            return;
        }

        header('Location: ' . URLROOT . '/notice/index');
        exit;
    }

    /**
     * Helper to dispatch styled HTML email notice to recipient array
     */
    private function dispatchNoticeEmail($notice, array $recipients){
        require_once APPROOT . '/Core/Mailer.php';
        $settings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];
        $schoolName = !empty($settings['school_name']) ? $settings['school_name'] : (defined('SITENAME') ? SITENAME : 'School ERP');
        $siteUrl = defined('URLROOT') ? URLROOT : 'http://localhost/school';

        $noticeType = !empty($notice->notice_type) ? $notice->notice_type : 'Official Circular';
        $priority = !empty($notice->priority) ? $notice->priority : 'Normal';
        $publishDate = !empty($notice->publish_date) ? date('d M Y', strtotime($notice->publish_date)) : date('d M Y');

        $subject = "[{$schoolName}] {$noticeType}: {$notice->title}";
        $sent = 0;
        $failed = 0;

        foreach ($recipients as $toEmail => $recipientName) {
            $bodyHtml = '
                <div style="background: #f8fafc; border-left: 4px solid #1769e0; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px;">
                    <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; font-weight: 700; margin-bottom: 4px;">
                        ' . htmlspecialchars($noticeType) . ' &bull; Priority: ' . htmlspecialchars($priority) . '
                    </div>
                    <h2 style="color: #0f172a; margin: 0; font-size: 1.25rem; font-weight: 700; line-height: 1.3;">
                        ' . htmlspecialchars($notice->title) . '
                    </h2>
                    <div style="font-size: 12px; color: #64748b; margin-top: 6px;">
                        Issue Date: <strong>' . $publishDate . '</strong>
                    </div>
                </div>

                <p style="font-size: 14px; color: #334155; margin-bottom: 16px;">
                    Dear <strong>' . htmlspecialchars($recipientName) . '</strong>,
                </p>

                <div style="font-size: 14px; line-height: 1.7; color: #1e293b; background: #ffffff; padding: 18px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 24px; white-space: pre-line;">
                    ' . htmlspecialchars($notice->message) . '
                </div>

                <div style="text-align: center; margin-top: 24px;">
                    <a href="' . htmlspecialchars($siteUrl) . '/notice/index" style="display: inline-block; background: #1769e0; color: #ffffff; text-decoration: none; padding: 10px 24px; font-weight: 600; font-size: 13px; border-radius: 6px;">
                        View Notice on School Portal &rarr;
                    </a>
                </div>
            ';

            $plainText = "Subject: {$notice->title}\nDate: {$publishDate}\nPriority: {$priority}\n\nDear {$recipientName},\n\n{$notice->message}\n\nView on School Portal: {$siteUrl}/notice/index";

            if (Mailer::send($toEmail, $subject, $bodyHtml, $plainText)) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    /**
     * Delete notice
     */
    public function delete($id = null){
        $userRole = $_SESSION['user_role'] ?? 'admin';
        $isAdmin = in_array($userRole, ['admin', 'super_admin', 'librarian']) || AuthGuard::hasPermission('manage_communication');

        if (!$isAdmin) {
            $this->respond(['status' => 'error', 'message' => 'Unauthorized action.'], 403);
            return;
        }

        if (empty($id) && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
        }

        if (empty($id)) {
            $_SESSION['flash_error'] = 'Invalid circular ID specified.';
            header('Location: ' . URLROOT . '/notice/index');
            exit;
        }

        $noticeModel = $this->model('Notice');
        $deleted = $noticeModel->deleteNotice($id);

        if ($deleted) {
            $_SESSION['flash_success'] = 'Circular record deleted successfully.';
            if ($this->isAjax()) {
                $this->respond(['status' => 'success', 'message' => 'Circular deleted successfully.']);
                return;
            }
        } else {
            $_SESSION['flash_error'] = 'Unable to delete circular.';
            if ($this->isAjax()) {
                $this->respond(['status' => 'error', 'message' => 'Unable to delete circular.'], 500);
                return;
            }
        }

        header('Location: ' . URLROOT . '/notice/index');
        exit;
    }

    /**
     * Return single notice details in JSON for dynamic modal pre-filling
     */
    public function getNoticeJson($id = null){
        if (empty($id)) {
            $this->respond(['status' => 'error', 'message' => 'Missing notice ID.'], 400);
            return;
        }

        $noticeModel = $this->model('Notice');
        $notice = $noticeModel->getNoticeById($id);

        if (!$notice) {
            $this->respond(['status' => 'error', 'message' => 'Circular not found.'], 404);
            return;
        }

        $this->respond([
            'status' => 'success',
            'notice' => $notice
        ]);
    }

    /**
     * Official Institutional Printable Circular (Letterhead format)
     */
    public function printNotice($id = null){
        if (empty($id)) {
            header('Location: ' . URLROOT . '/notice/index');
            exit;
        }

        $noticeModel = $this->model('Notice');
        $notice = $noticeModel->getNoticeById($id);

        if (!$notice) {
            $_SESSION['flash_error'] = 'Circular not found for printing.';
            header('Location: ' . URLROOT . '/notice/index');
            exit;
        }

        $settings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];

        $data = [
            'notice'   => $notice,
            'settings' => $settings
        ];

        $this->view('notice/print_notice', $data);
    }

    /**
     * Print alias for clean RESTful route /notice/print/{id}
     */
    public function print($id = null){
        $this->printNotice($id);
    }

    /**
     * Check if request is AJAX
     */
    private function isAjax(){
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    /**
     * JSON Response Helper
     */
    private function respond($data, $code = 200){
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data);
        exit;
    }
}

