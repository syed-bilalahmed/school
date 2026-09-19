<?php
class AuthController extends Controller {
    public function login(){
        // Allow user to view login page and switch roles manually without forced direct auto-login
        if (isset($_SESSION['user_id']) && isset($_GET['dashboard'])) {
            $role = $_SESSION['user_role'] ?? 'admin';
            if ($role === 'teacher') header('Location: ' . URLROOT . '/teacher/index');
            elseif ($role === 'student') header('Location: ' . URLROOT . '/student/index');
            elseif ($role === 'parent') header('Location: ' . URLROOT . '/parent/index');
            elseif ($role === 'receptionist') header('Location: ' . URLROOT . '/frontoffice/index');
            elseif ($role === 'accountant') header('Location: ' . URLROOT . '/fees/collect');
            elseif ($role === 'librarian') header('Location: ' . URLROOT . '/library/index');
            else header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        // Check for POST
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (class_exists('AuthGuard')) AuthGuard::verifyCSRF();
            
            // Rate Limiting Check
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
            }
            if ($_SESSION['login_attempts'] >= 5) {
                if (isset($_SESSION['last_login_attempt']) && (time() - $_SESSION['last_login_attempt'] < 300)) {
                    $data = [
                        'email' => trim($_POST['email'] ?? ''),
                        'password' => '',
                        'email_err' => 'Too many failed login attempts. Please try again in 5 minutes.',
                        'password_err' => ''
                    ];
                    $this->view('auth/login', $data);
                    return;
                } else {
                    // Reset lockout after 5 minutes
                    $_SESSION['login_attempts'] = 0;
                }
            }

            // Process form
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW) ?? [];
            
            $data = [
                'email' => trim($_POST['email'] ?? ''),
                'password' => trim($_POST['password'] ?? ''),
                'email_err' => '',
                'password_err' => ''
            ];

            // Validate Email
            if(empty($data['email'])){
                $data['email_err'] = 'Please enter your email address';
            }

            // Validate Password
            if(empty($data['password'])){
                $data['password_err'] = 'Please enter your password';
            }

            // Only attempt login if basic validation passes
            if(empty($data['email_err']) && empty($data['password_err'])){
                $userModel = $this->model('User');
                // Ensure all standard role demo accounts exist (accountant, teacher, etc.)
                $userModel->ensureRoleDemoAccounts();
                $loggedInUser = $userModel->login($data['email'], $data['password']);

                if($loggedInUser){
                    // Reset attempts on successful login
                    unset($_SESSION['login_attempts']);
                    unset($_SESSION['last_login_attempt']);

                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $_SESSION['login_attempts']++;
                    $_SESSION['last_login_attempt'] = time();

                    // Generic message prevents user enumeration (don't reveal if email exists)
                    $data['email_err'] = 'Invalid email or password. Please try again.';
                    $this->view('auth/login', $data);
                }
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['last_login_attempt'] = time();

                // Load view with errors
                $this->view('auth/login', $data);
            }

        } else {
            $userModel = $this->model('User');
            $demoRoles = $userModel->ensureRoleDemoAccounts();
            
            // Init data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => '',
                'demoRoles' => $demoRoles
            ];

            // Load view
            $this->view('auth/login', $data);
        }
    }

    public function forgotPassword(){
        $this->ensurePasswordResetTable();
        $data = ['email' => '', 'error' => '', 'success' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (class_exists('AuthGuard')) AuthGuard::verifyCSRF();
            $data['email'] = trim($_POST['email'] ?? '');

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['error'] = 'Please enter a valid email address.';
            } else {
                $userModel = $this->model('User');
                $user = $userModel->getUserByEmail($data['email']);
                $data['success'] = 'If an account matches this email, a password reset link has been created.';

                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $pin = (string)random_int(100000, 999999);
                    if ($userModel->createPasswordResetToken($user->id, hash('sha256', $token), password_hash($pin, PASSWORD_DEFAULT))) {
                        $resetLink = URLROOT . '/auth/resetPassword/' . $token;
                        $schoolName = class_exists('SiteSetting') ? SiteSetting::getGlobal('school_name', SITENAME) : SITENAME;
                        $htmlContent = '
                            <div style="padding: 10px 0;">
                                <h2 style="color: #0f172a; margin-top: 0; font-size: 1.3rem;">Password Reset Request</h2>
                                <p style="color: #475569; font-size: 0.95rem; line-height: 1.6;">
                                    Hello <strong>' . htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') . '</strong>,<br>
                                    We received a request to reset your password for your <strong>' . htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8') . '</strong> account.
                                </p>
                                <div style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px; text-align: center; margin: 25px 0;">
                                    <span style="display: block; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; margin-bottom: 8px;">Your One-Time Security PIN</span>
                                    <span style="font-size: 32px; font-weight: 800; letter-spacing: 8px; color: #1769e0; font-family: monospace;">' . $pin . '</span>
                                </div>
                                <div style="text-align: center; margin: 25px 0;">
                                    <a href="' . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . '" style="display: inline-block; background: #1769e0; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 0.95rem;">
                                        Reset Your Password &rarr;
                                    </a>
                                </div>
                                <p style="color: #64748b; font-size: 0.84rem; line-height: 1.5; margin-bottom: 0;">
                                    This PIN and link will expire in <strong>60 minutes</strong>.<br>
                                    If you did not request a password reset, you can safely disregard this email.
                                </p>
                            </div>
                        ';
                        $mailSent = Mailer::send(
                            $user->email,
                            'Password Reset PIN for ' . $schoolName,
                            $htmlContent,
                            "Password Reset PIN for {$schoolName}\n\nHello {$user->name},\nYour One-Time PIN is: {$pin}\nReset Link: {$resetLink}\n\nThis PIN expires in 60 minutes."
                        );

                        if ($mailSent) {
                            $data['success'] = 'A security PIN and password reset link have been emailed to your address. Please check your inbox.';
                        } else {
                            $lastErr = Mailer::getLastError();
                            error_log('[PasswordReset] Mail delivery failed for ' . $user->email . ': ' . $lastErr . ' (PIN: ' . $pin . ')');
                            $data['success'] = 'If an account matches this email, password reset instructions have been generated. Please check your inbox or contact your school administrator if delivery is delayed.';
                        }
                    }
                }
            }
        }

        $this->view('auth/forgot_password', $data);
    }

    public function resetPassword($token = ''){
        $this->ensurePasswordResetTable();
        $token = trim($token);
        $userModel = $this->model('User');
        $user = $token !== '' ? $userModel->getUserByPasswordResetToken(hash('sha256', $token)) : false;
        $tokenHash = hash('sha256', $token);
        $sessionKey = 'password_reset_verified_' . $tokenHash;
        $pinVerified = !empty($_SESSION[$sessionKey]) && $_SESSION[$sessionKey] > time();
        $data = ['token' => $token, 'error' => '', 'success' => '', 'valid' => (bool)$user, 'pin_verified' => $pinVerified];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (class_exists('AuthGuard')) AuthGuard::verifyCSRF();
            $pin = trim((string)($_POST['pin'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            $confirmation = (string)($_POST['password_confirmation'] ?? '');

            if (!$user) {
                $data['error'] = 'This reset link is invalid or has expired.';
            } elseif (!$pinVerified && $pin !== '') {
                if (preg_match('/^\d{6}$/', $pin) && $userModel->verifyPasswordResetPin($tokenHash, $pin)) {
                    $_SESSION[$sessionKey] = time() + 900;
                    $data['pin_verified'] = true;
                } else {
                    $data['error'] = 'The PIN is incorrect or has expired.';
                }
            } elseif (!$pinVerified) {
                $data['error'] = 'Enter the six-digit PIN sent to your email.';
            } elseif (strlen($password) < 6) {
                $data['error'] = 'Password must be at least 6 characters long.';
            } elseif ($password !== $confirmation) {
                $data['error'] = 'Passwords do not match.';
            } elseif ($userModel->changePassword($user->id, $password)) {
                $userModel->deletePasswordResetToken(hash('sha256', $token));
                unset($_SESSION[$sessionKey]);
                $data['success'] = 'Your password has been updated. You can now sign in.';
                $data['valid'] = false;
            } else {
                $data['error'] = 'Unable to update the password. Please try again.';
            }
        }

        $this->view('auth/reset_password', $data);
    }

    private function ensurePasswordResetTable(){
        $db = new Database;
        $db->query('CREATE TABLE IF NOT EXISTS password_resets (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, token_hash CHAR(64) NOT NULL UNIQUE, pin_hash VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_password_resets_user (user_id), INDEX idx_password_resets_expiry (expires_at))');
        $db->execute();
        try {
            $db->query('ALTER TABLE password_resets ADD COLUMN pin_hash VARCHAR(255) NULL AFTER token_hash');
            $db->execute();
        } catch (Throwable $e) {
            // Column already exists on established installations.
        }
    }

    public function createUserSession($user){
        // Regenerate session ID to prevent Session Fixation attacks
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $schoolId = !empty($user->school_id) ? (int)$user->school_id : 1;
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['user_school_id'] = $schoolId;
        $_SESSION['school_id'] = $schoolId;
        $_SESSION['user_avatar'] = !empty($user->avatar) ? $user->avatar : '';
        
        // Fetch and store user permissions
        $userModel = $this->model('User');
        $_SESSION['user_permissions'] = $userModel->getUserPermissions($user->id);
        
        // Initialize tenant context for this session
        if (class_exists('TenantContext')) {
            TenantContext::setSchoolId($schoolId);
        }
        
        // Redirect based on role
        if($user->role == 'super_admin' || $user->role == 'admin'){
             header('Location: ' . URLROOT . '/admin/dashboard');
             exit;
        } elseif($user->role == 'student'){
             header('Location: ' . URLROOT . '/student/index');
             exit;
        } elseif($user->role == 'teacher'){
             header('Location: ' . URLROOT . '/teacher/index');
             exit;
        } elseif($user->role == 'parent'){
             header('Location: ' . URLROOT . '/parent/index');
             exit;
        } elseif($user->role == 'receptionist'){
             header('Location: ' . URLROOT . '/frontoffice/index');
             exit;
        } elseif($user->role == 'accountant'){
             header('Location: ' . URLROOT . '/fees/collect');
             exit;
        } elseif($user->role == 'librarian'){
             header('Location: ' . URLROOT . '/library/index');
             exit;
        } else {
             // Default to role home or dashboard
             header('Location: ' . URLROOT . '/admin/dashboard');
             exit;
        }
    }

    public function logout(){
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            if (class_exists('TenantContext')) TenantContext::clear();
            session_destroy();
        }
        header('Location: ' . URLROOT . '/auth/login');
        exit;
    }
}
