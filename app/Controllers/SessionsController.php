<?php
// app/Controllers/SessionsController.php

class SessionsController extends Controller {
    private $sessionModel;

    public function __construct() {
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_academics');

        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'super_admin' && $_SESSION['user_role'] != 'admin')) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $this->sessionModel = $this->model('AcademicSession');
    }

    public function index() {
        $sessions = $this->sessionModel->getSessions();
        $current = $this->sessionModel->getCurrentSession();

        $data = [
            'sessions' => $sessions,
            'current_session' => $current
        ];

        $this->view('sessions/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'session_name' => trim($_POST['session_name'] ?? ''),
                'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                'is_current' => isset($_POST['is_current']) ? 1 : 0
            ];

            if (!empty($data['session_name'])) {
                $this->sessionModel->createSession($data);
                header('Location: ' . URLROOT . '/sessions/index?success=created');
                exit;
            }
        }
        header('Location: ' . URLROOT . '/sessions/index');
        exit;
    }

    public function edit($id = null) {
        if (!$id && isset($_POST['id'])) $id = (int)$_POST['id'];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => (int)$id,
                'session_name' => trim($_POST['session_name'] ?? ''),
                'start_date' => !empty($_POST['start_date']) ? $_POST['start_date'] : null,
                'end_date' => !empty($_POST['end_date']) ? $_POST['end_date'] : null,
                'is_current' => isset($_POST['is_current']) ? 1 : 0
            ];

            if (!empty($data['session_name']) && $id) {
                $this->sessionModel->updateSession($data);
                header('Location: ' . URLROOT . '/sessions/index?success=updated');
                exit;
            }
        }

        $session = $this->sessionModel->getSessionById($id);
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$session, 'session' => $session]);
            exit;
        }

        header('Location: ' . URLROOT . '/sessions/index');
        exit;
    }

    public function setCurrent($id) {
        if ($id) {
            $this->sessionModel->setCurrentSession((int)$id);
            $session = $this->sessionModel->getSessionById($id);
            if ($session) {
                $_SESSION['active_session_id'] = $session->id;
                $_SESSION['active_session_name'] = $session->session_name;
            }
        }
        header('Location: ' . URLROOT . '/sessions/index?success=current_set');
        exit;
    }

    public function switchSession($id) {
        $session = $this->sessionModel->getSessionById((int)$id);
        if ($session) {
            $_SESSION['active_session_id'] = $session->id;
            $_SESSION['active_session_name'] = $session->session_name;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'session_name' => $session ? $session->session_name : '']);
            exit;
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? URLROOT . '/admin/dashboard';
        header('Location: ' . $referer);
        exit;
    }

    public function delete($id) {
        if ($id) {
            $deleted = $this->sessionModel->deleteSession((int)$id);
            if (!$deleted) {
                header('Location: ' . URLROOT . '/sessions/index?error=cannot_delete_current');
                exit;
            }
        }
        header('Location: ' . URLROOT . '/sessions/index?success=deleted');
        exit;
    }
}
