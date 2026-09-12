<?php
class NotificationController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_communication');
        if(ob_get_level() === 0){
            ob_start();
        }

        if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])){
            $this->respondJson(['count' => 0, 'notifications' => []]);
        }
    }

    private function respondJson($payload, $statusCode = 200){
        if(ob_get_length()){
            ob_clean();
        }

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    public function fetch(){
        try {
            $notifyModel = $this->model('Notification');
            $role = $_SESSION['user_role'] ?? 'admin';
            $notifications = $notifyModel->getUnreadNotifications($role);

            if(!is_array($notifications)){
                $notifications = [];
            }

            $this->respondJson([
                'count' => count($notifications),
                'notifications' => $notifications
            ]);
        } catch (Throwable $e) {
            $this->respondJson([
                'count' => 0,
                'notifications' => [],
                'error' => 'notification_fetch_failed'
            ], 500);
        }
    }
    
    public function markRead($id){
        $id = (int)$id;
        if($id <= 0){
            $this->respondJson(['status' => 'error', 'message' => 'Invalid notification id'], 400);
        }

        try {
            $notifyModel = $this->model('Notification');
            $notifyModel->markAsRead($id);
            $this->respondJson(['status' => 'success']);
        } catch (Throwable $e) {
            $this->respondJson(['status' => 'error', 'message' => 'mark_read_failed'], 500);
        }
    }
}
