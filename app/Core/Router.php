<?php
/**
 * Core Router & Intelligent ERP Navigation Engine
 * Resolves controllers, method aliases, kebab-case, snake_case, RESTful IDs,
 * tenant prefixes, direct shortcuts, and graceful fallbacks without breaks.
 */
class Router {
    protected $currentController = 'HomeController';
    protected $currentMethod = 'index';
    protected $params = [];

    // Direct High-Frequency Routine Route Shortcuts
    protected $routeShortcuts = [
        'dashboard'          => ['AdminController', 'dashboard'],
        'login'              => ['AuthController', 'login'],
        'logout'             => ['AuthController', 'logout'],
        'register'           => ['AuthController', 'register'],
        'forgot-password'    => ['AuthController', 'forgotPassword'],
        'admission'          => ['StudentsController', 'admission'],
        'users'              => ['AdminController', 'users'],
        'settings'           => ['SettingController', 'index'],
        'setting'            => ['SettingController', 'index'],
        'enquiry'            => ['FrontOfficeController', 'enquiry'],
        'online-admission'   => ['FrontOfficeController', 'onlineAdmission'],
        'online-admissions'  => ['FrontOfficeController', 'onlineAdmissions'],
        'gatepass'           => ['FrontOfficeController', 'gatePass'],
        'gate-pass'          => ['FrontOfficeController', 'gatePass'],
        'visitor'            => ['FrontOfficeController', 'visitorPass'],
        'visitors'           => ['FrontOfficeController', 'visitorPass'],
        'visitorpass'        => ['FrontOfficeController', 'visitorPass'],
        'visitor-pass'       => ['FrontOfficeController', 'visitorPass'],
        'profile'            => ['ProfileController', 'index'],
        'my-profile'         => ['ProfileController', 'index'],
        'user-profile'       => ['ProfileController', 'index'],
        'health'             => ['HealthController', 'index'],
        'status'             => ['HealthController', 'index'],
        'contact'            => ['HomeController', 'contact'],
        'contact-us'         => ['HomeController', 'contact'],
        'alumni'             => ['HomeController', 'alumni'],
        'alumnis'            => ['HomeController', 'alumni'],
        'requirements'       => ['HomeController', 'requirements'],
        'requirement'        => ['HomeController', 'requirements'],
        'careers'            => ['HomeController', 'requirements'],
        'jobs'               => ['HomeController', 'requirements'],
        'tenders'            => ['HomeController', 'requirements'],
        'api-tester'         => ['ApiController', 'tester'],
        'api-playground'     => ['ApiController', 'tester']
    ];

    // Explicit Controller Alias Map for Common Irregular Names & Plurals
    protected $controllerAliases = [
        'family'           => 'Families',
        'families'         => 'Families',
        'class'            => 'Classes',
        'classes'          => 'Classes',
        'fee'              => 'Fees',
        'fees'             => 'Fees',
        'exam'             => 'Exam',
        'exams'            => 'Exam',
        'examination'      => 'Exam',
        'examinations'     => 'Exam',
        'report'           => 'Reports',
        'reports'          => 'Reports',
        'section'          => 'Sections',
        'sections'         => 'Sections',
        'session'          => 'Sessions',
        'sessions'         => 'Sessions',
        'subject'          => 'Subjects',
        'subjects'         => 'Subjects',
        'income'           => 'Incomes',
        'incomes'          => 'Incomes',
        'expense'          => 'Expense',
        'expenses'         => 'Expense',
        'notice'           => 'Notice',
        'notices'          => 'Notice',
        'notification'     => 'Notification',
        'notifications'    => 'Notification',
        'timetable'        => 'Timetable',
        'timetables'       => 'Timetable',
        'homework'         => 'Homework',
        'attendance'       => 'Attendance',
        'student'          => 'Student',
        'students'         => 'Students',
        'teacher'          => 'Teacher',
        'teachers'         => 'Teacher',
        'parent'           => 'Parent',
        'parents'          => 'Parent',
        'staff'            => 'Staff',
        'payroll'          => 'Payroll',
        'inventory'        => 'Inventory',
        'inventories'      => 'Inventory',
        'library'          => 'Library',
        'libraries'        => 'Library',
        'promote'          => 'Promote',
        'promotion'        => 'Promote',
        'clearance'        => 'Clearance',
        'clearances'       => 'Clearance',
        'certificate'      => 'Certificate',
        'certificates'     => 'Certificate',
        'export'           => 'Export',
        'exports'          => 'Export',
        'frontoffice'      => 'FrontOffice',
        'front-office'     => 'FrontOffice',
        'front_office'     => 'FrontOffice',
        'frontcms'         => 'FrontCms',
        'front-cms'        => 'FrontCms',
        'front_cms'        => 'FrontCms',
        'cms'              => 'FrontCms',
        'downloadcenter'   => 'DownloadCenter',
        'download-center'  => 'DownloadCenter',
        'download_center'  => 'DownloadCenter',
        'downloads'        => 'DownloadCenter',
        'auth'             => 'Auth',
        'admin'            => 'Admin',
        'home'             => 'Home',
        'profile'          => 'Profile',
        'api'              => 'Api'
    ];

    public function __construct(){
        $url = $this->getUrl();

        $firstSegment = strtolower($url[0] ?? 'home');
        $resolved = false;

        // 1. Check Direct Routine Shortcut Routes (e.g. /dashboard, /admission, /login)
        if (isset($this->routeShortcuts[$firstSegment])) {
            $shortcut = $this->routeShortcuts[$firstSegment];
            $controllerClass = $shortcut[0];
            $methodName = $shortcut[1];

            if (file_exists('../app/Controllers/' . $controllerClass . '.php')) {
                $this->currentController = $controllerClass;
                $this->currentMethod = $methodName;
                unset($url[0]);
                $resolved = true;
            }
        }

        // 2. Intelligent Controller Matching if not resolved via shortcut
        if (!$resolved) {
            $rawClean = str_replace(['-', '_'], '', $firstSegment);
            
            $candidates = [];
            // Check alias map first
            if (isset($this->controllerAliases[$firstSegment])) {
                $candidates[] = $this->controllerAliases[$firstSegment];
            }
            if (isset($this->controllerAliases[$rawClean])) {
                $candidates[] = $this->controllerAliases[$rawClean];
            }

            // Normal capitalization candidates
            $candidates[] = ucwords(str_replace(['-', '_'], ' ', $firstSegment));
            $candidates[] = ucfirst($rawClean);
            $candidates[] = rtrim(ucwords($firstSegment), 's');
            $candidates[] = ucwords($firstSegment) . 's';

            foreach ($candidates as $cand) {
                $cand = str_replace(' ', '', $cand);
                if (!empty($cand) && file_exists('../app/Controllers/' . $cand . 'Controller.php')) {
                    $this->currentController = $cand . 'Controller';
                    unset($url[0]);
                    $resolved = true;
                    break;
                }
            }
        }

        // Require and instantiate the matched controller
        require_once '../app/Controllers/' . $this->currentController . '.php';
        $this->currentController = new $this->currentController;

        // 3. Intelligent Method Matching with camelCase, snake_case, kebab-case & RESTful ID support
        if(isset($url[1])){
            $rawMethod = $url[1];

            // RESTful ID check: if method is numeric (e.g. /students/15 or /clearance/4)
            if (is_numeric($rawMethod)) {
                // If controller has profile(), detail(), show(), or view()
                foreach (['profile', 'detail', 'show', 'view'] as $viewMethod) {
                    if (method_exists($this->currentController, $viewMethod)) {
                        $this->currentMethod = $viewMethod;
                        // Keep $url[1] as the first parameter
                        break;
                    }
                }
            } else {
                $methodCandidates = [
                    $rawMethod,
                    lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $rawMethod)))), // print-admission -> printAdmission
                    str_replace(['-', '_'], '', strtolower($rawMethod)),                             // printadmission
                    strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $rawMethod)),                    // printAdmission -> print_admission
                    str_replace('-', '_', strtolower($rawMethod)),                                   // print-admission -> print_admission
                    strtolower($rawMethod)
                ];

                $methodMatched = false;
                foreach ($methodCandidates as $m) {
                    if (method_exists($this->currentController, $m)) {
                        $this->currentMethod = $m;
                        unset($url[1]);
                        $methodMatched = true;
                        break;
                    }
                }

                // If method did not match, check if currentController has index() with parameters
                if (!$methodMatched && !method_exists($this->currentController, $rawMethod)) {
                    $this->currentMethod = 'index';
                }
            }
        }

        // 4. Extract Remaining URL Parameters
        $this->params = $url ? array_values($url) : [];

        // 5. Final Execution with Graceful Fallback
        if (method_exists($this->currentController, $this->currentMethod)) {
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        } else {
            // Graceful fallback: call index() if exists
            if (method_exists($this->currentController, 'index')) {
                call_user_func_array([$this->currentController, 'index'], $this->params);
            } else {
                // Safe 404 handler
                $this->handleNotFound();
            }
        }
    }

    /**
     * Clean and parse the URL from query string
     */
    public function getUrl(){
        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            // Tenant-prefixed URLs are shaped as /s/{school_code}/{controller}/{method}
            if (count($url) >= 2 && strtolower($url[0]) === 's') {
                $url = array_slice($url, 2);
            }

            if (empty($url) || (count($url) === 1 && trim($url[0]) === '')) {
                return ['Home'];
            }

            return $url;
        }
        return ['Home'];
    }

    /**
     * Graceful fallback when a route cannot be resolved
     */
    protected function handleNotFound(){
        http_response_code(404);

        $isApi = (isset($_GET['url']) && strpos($_GET['url'], 'api') === 0)
              || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api') !== false);

        $isAjax = $isApi
               || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
               || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'API endpoint or action was not found.',
                'error' => 'Route not found'
            ]);
            exit;
        }

        // If logged in, redirect to Dashboard with helpful notice
        if (isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'Requested page or action was not found. Redirected to Dashboard.';
            header('Location: ' . URLROOT . '/admin/dashboard');
            exit;
        }

        // Otherwise redirect to Homepage
        header('Location: ' . URLROOT . '/');
        exit;
    }
}
