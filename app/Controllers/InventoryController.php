<?php
class InventoryController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        AuthGuard::requirePermission('manage_inventory');
         if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'super_admin')){
             header('Location: ' . URLROOT . '/auth/login');
             exit;
        }
    }

    public function index(){
        $this->items(); // Default view
    }

    public function items(){
        $invModel = $this->model('Inventory');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'name' => trim($_POST['name']),
                'item_category_id' => $_POST['item_category_id'],
                'unit' => trim($_POST['unit']),
                'description' => trim($_POST['description'])
            ];
            $invModel->addItem($data);
            header('Location: ' . URLROOT . '/inventory/items');
            exit;
        }
        
        $data = [
            'items' => $invModel->getItems(),
            'categories' => $invModel->getCategories()
        ];
        $this->view('inventory/items', $data);
    }

    public function add_stock(){
        $invModel = $this->model('Inventory');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
             $data = [
                'item_id' => $_POST['item_id'],
                'supplier_id' => $_POST['supplier_id'],
                'store_id' => $_POST['store_id'],
                'quantity' => (int)$_POST['quantity'],
                'date' => $_POST['date'],
                'attachment' => '',
                'description' => trim($_POST['description'])
            ];
            
            // Handle Attachment
             if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0){
                 $targetDir = 'uploads/inventory/';
                 if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);
                 $targetFile = $targetDir . time() . "_" . basename($_FILES['attachment']['name']);
                 move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile);
                 $data['attachment'] = $targetFile;
             }

            $invModel->addStock($data);
            header('Location: ' . URLROOT . '/inventory/add_stock');
            exit;
        }

        $data = [
            'stock_list' => $invModel->getStockList(),
            'items' => $invModel->getItems(),
            'suppliers' => $invModel->getSuppliers(),
            'stores' => $invModel->getStores()
        ];
        $this->view('inventory/stock', $data);
    }

    public function issue_item(){
        $invModel = $this->model('Inventory');
        // Simple user fetch, ideally separate Staff/Student models or universal user search
        $db = new Database;
        $db->query("SELECT id, name, role FROM users"); // Simple list for dropdown
        $users = $db->resultSet();

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['return_id'])){
                $invModel->returnItem($_POST['return_id']);
            } else {
                 $data = [
                    'issue_to' => $_POST['issue_to'], // user_id
                    'issue_type' => 'user', 
                    'issue_by' => $_SESSION['user_id'], // current admin
                    'issue_date' => $_POST['issue_date'],
                    'return_date' => $_POST['return_date'],
                    'note' => trim($_POST['note']),
                    'item_category_id' => $_POST['item_category_id'],
                    'item_id' => $_POST['item_id'],
                    'quantity' => (int)$_POST['quantity']
                ];
                if(!$invModel->issueItem($data)){
                    // Error handle for stock
                    die('Insufficient Stock!');
                }
            }
            header('Location: ' . URLROOT . '/inventory/issue_item');
            exit;
        }

        $data = [
            'issued_items' => $invModel->getIssuedItems(),
            'items' => $invModel->getItems(),
            'categories' => $invModel->getCategories(),
            'users' => $users
        ];
        $this->view('inventory/issue', $data);
    }
    
    // Helper to manage masters (Category, Store, Supplier) - Simplified to one view or separate
    public function setup(){
        $invModel = $this->model('Inventory');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
             if(isset($_POST['add_category'])){
                 $invModel->addCategory(['item_category' => $_POST['item_category'], 'description' => $_POST['description']]);
             } elseif(isset($_POST['add_store'])){
                 $invModel->addStore(['item_store' => $_POST['item_store'], 'code' => $_POST['code'], 'description' => $_POST['description']]);
             } elseif(isset($_POST['add_supplier'])){
                 $invModel->addSupplier([
                     'item_supplier' => $_POST['item_supplier'],
                     'phone' => $_POST['phone'],
                     'email' => $_POST['email'],
                     'address' => $_POST['address'],
                     'contact_person_name' => $_POST['contact_person_name']
                 ]);
             }
             header('Location: ' . URLROOT . '/inventory/setup');
             exit;
        }

        $data = [
            'categories' => $invModel->getCategories(),
            'stores' => $invModel->getStores(),
            'suppliers' => $invModel->getSuppliers()
        ];
        $this->view('inventory/setup', $data);
    }
}
