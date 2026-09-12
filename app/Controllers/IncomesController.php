<?php
// app/Controllers/IncomesController.php

class IncomesController extends Controller {
    private $incomeModel;

    public function __construct() {
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_finance');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthGuard::verifyCSRF();
        }

        $this->incomeModel = $this->model('Income');
    }

    public function index() {
        $incomes = $this->incomeModel->getIncomes();
        $closing = $this->incomeModel->getCashBookDailyClosing();

        $data = [
            'incomes' => $incomes,
            'closing' => $closing
        ];

        $this->view('incomes/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'category' => trim($_POST['category'] ?? 'Other'),
                'amount' => (float)($_POST['amount'] ?? 0),
                'date' => !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d'),
                'payment_mode' => trim($_POST['payment_mode'] ?? 'Cash'),
                'reference_no' => trim($_POST['reference_no'] ?? ''),
                'note' => trim($_POST['note'] ?? '')
            ];

            if (!empty($data['title']) && $data['amount'] > 0) {
                $this->incomeModel->addIncome($data);
                header('Location: ' . URLROOT . '/incomes/index?success=created');
                exit;
            }
        }
        header('Location: ' . URLROOT . '/incomes/index');
        exit;
    }

    public function delete($id) {
        if ($id) {
            $this->incomeModel->deleteIncome((int)$id);
        }
        header('Location: ' . URLROOT . '/incomes/index?success=deleted');
        exit;
    }
}
