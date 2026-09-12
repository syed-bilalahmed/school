<?php
class ProfileController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        if(!isset($_SESSION['user_id'])){
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthGuard::verifyCSRF();
        }
    }

    public function index(){
        $userModel = $this->model('User');
        $userId = (int)($_SESSION['user_id'] ?? 0);
        $user = $userModel->getUserById($userId);
        if (!$user) {
            header('Location: ' . URLROOT . '/auth/logout');
            exit;
        }
        $activeTab = $_GET['tab'] ?? ($_POST['tab'] ?? 'info');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $action = $_POST['action'] ?? 'update_profile';
            $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                   || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
                   || isset($_POST['ajax_submit']);

            $error = '';
            $success = '';

            if($action === 'change_password'){
                $activeTab = 'security';
                $current_password = (string)($_POST['current_password'] ?? '');
                $new_password = (string)($_POST['new_password'] ?? '');
                $confirm_password = (string)($_POST['confirm_password'] ?? '');

                if (empty($current_password) || empty($new_password)) {
                    $error = 'Both current password and new password are required.';
                } elseif (!password_verify($current_password, $user->password)) {
                    $error = 'Current password entered is incorrect.';
                } elseif (strlen($new_password) < 6) {
                    $error = 'New password must be at least 6 characters long.';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'New password and confirmation do not match.';
                } else {
                    if ($userModel->changePassword($userId, $new_password)) {
                        $success = 'Password changed successfully.';
                    } else {
                        $error = 'Failed to update password in database.';
                    }
                }
            } else {
                // Profile & Bio update
                $activeTab = 'info';
                $name = trim($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $bio = trim($_POST['bio'] ?? '');
                $address = trim($_POST['address'] ?? '');

                if (empty($name)) {
                    $error = 'Full name cannot be blank.';
                } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Please enter a valid email address.';
                } elseif ($userModel->isEmailTakenByOtherUser($email, $userId)) {
                    $error = "Email address '{$email}' is already in use by another user account.";
                } else {
                    $updateData = [
                        'id' => $userId,
                        'name' => $name,
                        'email' => $email,
                        'phone' => $phone,
                        'bio' => $bio,
                        'address' => $address
                    ];

                    // Avatar photo upload handling
                    if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'])) {
                        if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                            require_once APPROOT . '/Core/UploadHandler.php';
                            $upload = UploadHandler::processUpload($_FILES['avatar'], 'avatars', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                            if ($upload['success']) {
                                $updateData['avatar'] = $upload['path'];
                                $_SESSION['user_avatar'] = $upload['path'];
                            } else {
                                $error = $upload['error'] ?? 'Avatar upload failed.';
                            }
                        }
                    }

                    if (empty($error)) {
                        if ($userModel->updateProfile($updateData)) {
                            $_SESSION['user_name'] = $name;
                            $_SESSION['user_email'] = $email;
                            $success = 'Profile details and bio updated successfully.';
                        } else {
                            $error = 'Failed to save profile changes to database.';
                        }
                    }
                }
            }

            // Reload user data
            $user = $userModel->getUserById($userId);

            if ($isAjax) {
                if (ob_get_length()) ob_clean();
                header('Content-Type: application/json');
                if (!headers_sent() && !empty($_SESSION['csrf_token'])) {
                    header('X-CSRF-Token: ' . $_SESSION['csrf_token']);
                }
                http_response_code(200);
                echo json_encode([
                    'success' => empty($error),
                    'message' => !empty($error) ? $error : $success,
                    'user_name' => $user->name ?? '',
                    'user_email' => $user->email ?? '',
                    'avatar_url' => !empty($user->avatar) ? URLROOT . '/' . $user->avatar : null,
                    'tab' => $activeTab,
                    'csrf_token' => $_SESSION['csrf_token'] ?? ''
                ]);
                exit;
            }

            if (!empty($success)) {
                $_SESSION['flash_success'] = $success;
            }
            if (!empty($error)) {
                $_SESSION['flash_error'] = $error;
            }

            header('Location: ' . URLROOT . '/profile/index?tab=' . $activeTab);
            exit;
        }

        $data = [
            'user' => $user,
            'active_tab' => $activeTab
        ];

        $this->view('profile/index', $data);
    }

    public function password(){
        header('Location: ' . URLROOT . '/profile/index?tab=security');
        exit;
    }
}
