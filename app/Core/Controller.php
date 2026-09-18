<?php
class Controller {
    // Load Model
    public function model($model){
        // Require model file
        if(file_exists('../app/Models/' . $model . '.php')){
            require_once '../app/Models/' . $model . '.php';
            // Instantiate model
            return new $model();
        } else {
            die("Model $model does not exist");
        }
    }

    // Load View
    public function view($view, $data = []){
        // Automatically inject system currency if not explicitly provided
        if (!isset($data['currency'])) {
            if (empty($_SESSION['currency_symbol'])) {
                try {
                    if (file_exists('../app/Models/SiteSetting.php')) {
                        require_once '../app/Models/SiteSetting.php';
                        $ss = new SiteSetting();
                        $_SESSION['currency_symbol'] = $ss->getSetting('currency_symbol', 'PKR');
                    }
                } catch (Exception $e) {
                    $_SESSION['currency_symbol'] = 'PKR';
                }
            }
            $data['currency'] = !empty($_SESSION['currency_symbol']) ? $_SESSION['currency_symbol'] : 'PKR';
        }

        // Check for view file
        if(file_exists('../app/Views/' . $view . '.php')){
            require_once '../app/Views/' . $view . '.php';
        } else {
            // View does not exist
            die("View does not exist");
        }
    }

    // =========================================================================
    // SAAS MULTI-TENANCY & CENTRALIZED RBAC GUARD HELPERS
    // =========================================================================

    /**
     * Enforce authenticated user session
     */
    protected function requireAuth(){
        if (class_exists('AuthGuard')) {
            AuthGuard::requireAuth();
        } elseif (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    /**
     * Enforce tenant school context
     */
    protected function requireSchoolContext(){
        if (class_exists('AuthGuard')) {
            AuthGuard::requireSchoolContext();
        }
    }

    /**
     * Require specific permission or abort
     */
    protected function requirePermission($permissionKey){
        if (class_exists('AuthGuard')) {
            AuthGuard::requirePermission($permissionKey);
        }
    }

    /**
     * Check if current user has specific permission
     */
    protected function hasPermission($permissionKey){
        if (class_exists('AuthGuard')) {
            return AuthGuard::hasPermission($permissionKey);
        }
        return true;
    }

    /**
     * Get active tenant school ID
     */
    protected function getSchoolId(){
        if (class_exists('TenantContext')) {
            return TenantContext::getSchoolId();
        }
        return !empty($_SESSION['school_id']) ? (int)$_SESSION['school_id'] : 1;
    }

    /**
     * Get active tenant school code
     */
    protected function getSchoolCode(){
        if (class_exists('TenantContext')) {
            return TenantContext::getSchoolCode();
        }
        return !empty($_SESSION['school_code']) ? $_SESSION['school_code'] : 'default';
    }

    /**
     * Verify CSRF token on POST
     */
    protected function verifyCSRF(){
        if (class_exists('AuthGuard')) {
            AuthGuard::verifyCSRF();
        }
    }

    /**
     * Detect AJAX, PJAX, or JSON API requests
     */
    protected function isAjaxRequest(){
        if (class_exists('AuthGuard')) {
            return AuthGuard::isAjaxRequest();
        }
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            || !empty($_SERVER['HTTP_X_PJAX'])
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
            || isset($_POST['ajax_submit']);
    }
}
