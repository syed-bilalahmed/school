<?php
// app/Controllers/FamiliesController.php

class FamiliesController extends Controller {
    private $familyModel;

    public function __construct() {
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_students');

        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        $this->familyModel = $this->model('Family');
    }

    public function index() {
        $families = $this->familyModel->getFamilies();
        
        $data = [
            'families' => $families
        ];

        $this->view('families/index', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'family_code' => trim($_POST['family_code'] ?? ''),
                'father_name' => trim($_POST['father_name'] ?? ''),
                'father_cnic' => trim($_POST['father_cnic'] ?? ''),
                'guardian_phone' => trim($_POST['guardian_phone'] ?? ''),
                'default_discount_percent' => !empty($_POST['default_discount_percent']) ? (float)$_POST['default_discount_percent'] : 10.00,
                'notes' => trim($_POST['notes'] ?? '')
            ];

            $code = $this->familyModel->createFamily($data);
            if ($code) {
                // Auto check if any students match this father CNIC and link them
                if (!empty($data['father_cnic'])) {
                    $this->familyModel->autoApplySiblingDiscount($code);
                }
                header('Location: ' . URLROOT . '/families/index?success=created');
                exit;
            }
        }
        header('Location: ' . URLROOT . '/families/index');
        exit;
    }

    public function edit($id = null) {
        if (!$id && isset($_POST['id'])) $id = (int)$_POST['id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => (int)$id,
                'father_name' => trim($_POST['father_name'] ?? ''),
                'father_cnic' => trim($_POST['father_cnic'] ?? ''),
                'guardian_phone' => trim($_POST['guardian_phone'] ?? ''),
                'default_discount_percent' => !empty($_POST['default_discount_percent']) ? (float)$_POST['default_discount_percent'] : 10.00,
                'notes' => trim($_POST['notes'] ?? '')
            ];

            if ($id) {
                $this->familyModel->updateFamily($data);
                $family = $this->familyModel->getFamilyById($id);
                if ($family) {
                    $this->familyModel->autoApplySiblingDiscount($family->family_code);
                }
                header('Location: ' . URLROOT . '/families/index?success=updated');
                exit;
            }
        }

        $family = $this->familyModel->getFamilyById($id);
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$family, 'family' => $family]);
            exit;
        }

        header('Location: ' . URLROOT . '/families/index');
        exit;
    }

    public function view_children($family_code) {
        $children = $this->familyModel->getChildrenByFamilyCode($family_code);
        $family = $this->familyModel->getFamilyByCode($family_code);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'family' => $family,
            'children' => $children
        ]);
        exit;
    }

    public function recalculate($family_code) {
        $this->familyModel->autoApplySiblingDiscount(trim($family_code));
        header('Location: ' . URLROOT . '/families/index?success=recalculated');
        exit;
    }

    public function delete($id) {
        if ($id) {
            $this->familyModel->deleteFamily((int)$id);
        }
        header('Location: ' . URLROOT . '/families/index?success=deleted');
        exit;
    }
}
