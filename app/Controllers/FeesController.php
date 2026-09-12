<?php
class FeesController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        
        $url = trim($_GET['url'] ?? '', '/');
        $isChallanOrNotice = (strpos($url, 'fees/challan') !== false || strpos($url, 'fees/printNotice') !== false);
        $userRole = $_SESSION['user_role'] ?? '';

        if($isChallanOrNotice){
            $allowedRoles = ['super_admin', 'admin', 'accountant', 'student', 'parent'];
            if(!in_array($userRole, $allowedRoles)){
                header('Location: ' . URLROOT . '/auth/login');
                exit;
            }
        } else {
            $allowedRoles = ['super_admin', 'admin', 'accountant'];
            if(!in_array($userRole, $allowedRoles)){
                header('Location: ' . URLROOT . '/auth/login');
                exit;
            }
            if($userRole !== 'super_admin' && $userRole !== 'accountant'){
                AuthGuard::requirePermission('manage_finance');
            }
        }

        // CSRF protection: verify token on every state-changing POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthGuard::verifyCSRF();
        }
    }

    public function index(){
        header('Location: ' . URLROOT . '/fees/collect');
        exit;
    }

    // --- Types ---
    public function types(){
        $feeModel = $this->model('Fee');
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];
            $feeModel->addType($data);
            header('Location: ' . URLROOT . '/fees/types?success=1');
            exit;
        } else {
            $data = ['types' => $feeModel->getTypes()];
            $this->view('fees/types', $data);
        }
    }

    public function editType($id = null){
        $feeModel = $this->model('Fee');
        if(!$id && isset($_POST['id'])) $id = (int)$_POST['id'];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'id' => (int)$id,
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];
            $feeModel->updateType($data);
            header('Location: ' . URLROOT . '/fees/types?success=updated');
            exit;
        }
        $type = $feeModel->getTypeById($id);
        if(isset($_GET['ajax'])){
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$type, 'type' => $type]);
            exit;
        }
        $data = ['type' => $type, 'types' => $feeModel->getTypes()];
        $this->view('fees/types', $data);
    }

    public function deleteType($id = null){
        $feeModel = $this->model('Fee');
        if(!$id && isset($_POST['id'])) $id = (int)$_POST['id'];
        if($id){
            $feeModel->deleteType($id);
        }
        header('Location: ' . URLROOT . '/fees/types?success=deleted');
        exit;
    }

    public function ajaxSaveType(){
        header('Content-Type: application/json');
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        $feeModel = $this->model('Fee');
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if(empty($name) || empty($code)){
            echo json_encode(['success' => false, 'message' => 'Type Name and Code are required.']);
            exit;
        }

        $data = [
            'id' => $id,
            'name' => $name,
            'code' => $code,
            'description' => $description
        ];

        if($id){
            $feeModel->updateType($data);
            $msg = 'Fee type updated successfully!';
        } else {
            $feeModel->addType($data);
            $msg = 'Fee type added successfully!';
        }

        echo json_encode([
            'success' => true,
            'message' => $msg,
            'types' => $feeModel->getTypes()
        ]);
        exit;
    }

    public function ajaxGetTypes(){
        $feeModel = $this->model('Fee');
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'types' => $feeModel->getTypes()
        ]);
        exit;
    }

    public function ajaxDeleteType($id){
        $feeModel = $this->model('Fee');
        $deleted = $feeModel->deleteType((int)$id);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $deleted,
            'message' => $deleted ? 'Fee type deleted successfully.' : 'Could not delete fee type.',
            'types' => $feeModel->getTypes()
        ]);
        exit;
    }

    // --- Groups ---
    public function groups(){
        $feeModel = $this->model('Fee');
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];
            $feeModel->addGroup($data);
            header('Location: ' . URLROOT . '/fees/groups?success=1');
            exit;
        } else {
            $data = ['groups' => $feeModel->getGroups()];
            $this->view('fees/groups', $data);
        }
    }

    public function editGroup($id = null){
        $feeModel = $this->model('Fee');
        if(!$id && isset($_POST['id'])) $id = (int)$_POST['id'];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'id' => (int)$id,
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];
            $feeModel->updateGroup($data);
            header('Location: ' . URLROOT . '/fees/groups?success=updated');
            exit;
        }
        $group = $feeModel->getGroupById($id);
        if(isset($_GET['ajax'])){
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool)$group, 'group' => $group]);
            exit;
        }
        $data = ['group' => $group, 'groups' => $feeModel->getGroups()];
        $this->view('fees/groups', $data);
    }

    public function deleteGroup($id = null){
        $feeModel = $this->model('Fee');
        if(!$id && isset($_POST['id'])) $id = (int)$_POST['id'];
        if($id){
            $feeModel->deleteGroup($id);
        }
        header('Location: ' . URLROOT . '/fees/groups?success=deleted');
        exit;
    }

    public function ajaxSaveGroup(){
        header('Content-Type: application/json');
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        $feeModel = $this->model('Fee');
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if(empty($name)){
            echo json_encode(['success' => false, 'message' => 'Group Name is required.']);
            exit;
        }

        $data = [
            'id' => $id,
            'name' => $name,
            'description' => $description
        ];

        if($id){
            $feeModel->updateGroup($data);
            $msg = 'Fee group updated successfully!';
        } else {
            $feeModel->addGroup($data);
            $msg = 'Fee group added successfully!';
        }

        echo json_encode([
            'success' => true,
            'message' => $msg,
            'groups' => $feeModel->getGroups()
        ]);
        exit;
    }

    public function ajaxGetGroups(){
        $feeModel = $this->model('Fee');
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'groups' => $feeModel->getGroups()
        ]);
        exit;
    }

    public function ajaxDeleteGroup($id){
        $feeModel = $this->model('Fee');
        $deleted = $feeModel->deleteGroup((int)$id);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $deleted,
            'message' => $deleted ? 'Fee group deleted successfully.' : 'Could not delete fee group.',
            'groups' => $feeModel->getGroups()
        ]);
        exit;
    }

    // --- Master (Link Group-Type) ---
    public function master($group_id = null){
        $feeModel = $this->model('Fee');
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $data = [
                'id' => $id,
                'group_id' => $_POST['group_id'],
                'type_id' => $_POST['type_id'],
                'amount' => $_POST['amount'],
                'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null,
                'fine' => !empty($_POST['fine']) ? $_POST['fine'] : 0
            ];
            if($id){
                $feeModel->updateMaster($data);
                header('Location: ' . URLROOT . '/fees/master/' . $_POST['group_id'] . '?success=updated');
                exit;
            } else {
                $feeModel->addMaster($data);
                header('Location: ' . URLROOT . '/fees/master/' . $_POST['group_id'] . '?success=1');
                exit;
            }
        } else {
            if(!$group_id){
                header('Location: ' . URLROOT . '/fees/groups');
                exit;
            } else {
                $group = $feeModel->getGroupById($group_id);
                $data = [
                    'group_id' => $group_id,
                    'group' => $group,
                    'types' => $feeModel->getTypes(),
                    'master_fees' => $feeModel->getMasterByGroup($group_id)
                ];
                $this->view('fees/master', $data);
            }
        }
    }

    public function editMaster($id = null){
        $feeModel = $this->model('Fee');
        if(!$id && isset($_POST['id'])) $id = (int)$_POST['id'];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $groupId = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null;
            $data = [
                'id' => (int)$id,
                'type_id' => !empty($_POST['type_id']) ? (int)$_POST['type_id'] : null,
                'amount' => (float)($_POST['amount'] ?? 0),
                'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null,
                'fine' => (float)($_POST['fine'] ?? 0)
            ];
            $feeModel->updateMaster($data);
            if($groupId){
                header('Location: ' . URLROOT . '/fees/master/' . $groupId . '?success=updated');
            } else {
                header('Location: ' . URLROOT . '/fees/groups?success=updated');
            }
            exit;
        }
        $master = $feeModel->getMasterById($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$master, 'master' => $master]);
        exit;
    }

    public function deleteMaster($id = null){
        $feeModel = $this->model('Fee');
        if(!$id && isset($_POST['id'])) $id = (int)$_POST['id'];
        $groupId = !empty($_GET['group_id']) ? (int)$_GET['group_id'] : null;
        if($id){
            $feeModel->deleteMaster($id);
        }
        if($groupId){
            header('Location: ' . URLROOT . '/fees/master/' . $groupId . '?success=deleted');
        } else {
            header('Location: ' . URLROOT . '/fees/groups?success=deleted');
        }
        exit;
    }

    public function ajaxGetGroupMasters($group_id){
        $feeModel = $this->model('Fee');
        $groupId = (int)$group_id;
        $group = $feeModel->getGroupById($groupId);
        $masters = $feeModel->getMasterByGroup($groupId);
        $types = $feeModel->getTypes();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'group' => $group,
            'masters' => $masters,
            'types' => $types
        ]);
        exit;
    }

    public function ajaxSaveMaster(){
        header('Content-Type: application/json');
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        $feeModel = $this->model('Fee');
        $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $groupId = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
        $typeId = !empty($_POST['type_id']) ? (int)$_POST['type_id'] : 0;
        $amount = (float)($_POST['amount'] ?? 0);
        $dueDate = !empty($_POST['due_date']) ? trim($_POST['due_date']) : null;
        $fine = (float)($_POST['fine'] ?? 0);

        if(!$groupId){
            echo json_encode(['success' => false, 'message' => 'Fee Group is required.']);
            exit;
        }

        $data = [
            'id' => $id,
            'group_id' => $groupId,
            'type_id' => $typeId,
            'amount' => $amount,
            'due_date' => $dueDate,
            'fine' => $fine
        ];

        if($id){
            $feeModel->updateMaster($data);
            $msg = 'Fee structure updated successfully!';
        } else {
            if(!$typeId){
                echo json_encode(['success' => false, 'message' => 'Fee Type is required.']);
                exit;
            }
            $feeModel->addMaster($data);
            $msg = 'Fee type added to group!';
        }

        echo json_encode([
            'success' => true,
            'message' => $msg,
            'masters' => $feeModel->getMasterByGroup($groupId)
        ]);
        exit;
    }

    public function ajaxDeleteMaster($id){
        $feeModel = $this->model('Fee');
        $masterId = (int)$id;
        $groupId = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null;
        $deleted = $feeModel->deleteMaster($masterId);
        
        $remaining = [];
        if($groupId){
            $remaining = $feeModel->getMasterByGroup($groupId);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $deleted,
            'message' => $deleted ? 'Fee type removed from group.' : 'Could not remove fee item.',
            'masters' => $remaining
        ]);
        exit;
    }

    // --- Assign to Class ---
    public function assign(){
        $classModel = $this->model('SchoolClass');
        $feeModel = $this->model('Fee');
        $sectionModel = $this->model('Section');
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $redirect = AuthGuard::sanitizeRedirect($_POST['redirect_to'] ?? '', '/fees/assign?success=1');
            $feeModel->assignToClass($_POST['fee_group_id'], $_POST['class_id'], $_POST['section_id'] ?? null);
            header('Location: ' . URLROOT . $redirect);
            exit;
        } else {
            $data = [
                'classes' => $classModel->getClasses(),
                'sections' => $sectionModel->getSections(),
                'groups' => $feeModel->getGroups()
            ];
            $this->view('fees/assign', $data);
        }
    }

    public function ajaxAssignToClass(){
        header('Content-Type: application/json');
        if($_SERVER['REQUEST_METHOD'] != 'POST'){
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        $feeModel = $this->model('Fee');
        $groupId = !empty($_POST['fee_group_id']) ? (int)$_POST['fee_group_id'] : 0;
        $classId = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
        $sectionId = !empty($_POST['section_id']) ? (int)$_POST['section_id'] : null;

        if(!$groupId || !$classId){
            echo json_encode(['success' => false, 'message' => 'Please select both Fee Group and Class.']);
            exit;
        }

        $assigned = $feeModel->assignToClass($groupId, $classId, $sectionId);
        echo json_encode([
            'success' => $assigned,
            'message' => 'Fee group assigned successfully to students!'
        ]);
        exit;
    }

    public function ajaxGetClassFeeParticulars($classId){
        header('Content-Type: application/json');
        $classId = (int)$classId;
        $classModel = $this->model('SchoolClass');
        $feeModel = $this->model('Fee');
        $schoolId = TenantContext::getSchoolId();

        $cls = $classModel->getClassById($classId);
        if(!$cls){
            echo json_encode(['success' => false, 'message' => 'Class not found.']);
            exit;
        }

        // Count students in this class
        $db = new Database();
        $db->query("SELECT COUNT(*) as total FROM students WHERE class_id = :cid AND school_id = :sid");
        $db->bind(':cid', $classId);
        $db->bind(':sid', $schoolId);
        $studentCount = (int)($db->single()->total ?? 0);

        // Find existing fee group matching this class
        $groups = $feeModel->getGroups();
        $matchedGroup = null;
        foreach($groups as $g){
            if(stripos($g->group_name, $cls->class_name) !== false){
                $matchedGroup = $g;
                break;
            }
        }

        // If no group name matches, check if any student in this class has an assigned fee group
        if(!$matchedGroup){
            $db->query("SELECT DISTINCT fgt.fee_group_id 
                        FROM student_fees sf
                        JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id
                        JOIN students s ON sf.student_id = s.id
                        WHERE s.class_id = :cid AND sf.school_id = :sid
                        LIMIT 1");
            $db->bind(':cid', $classId);
            $db->bind(':sid', $schoolId);
            $assignedRow = $db->single();
            if($assignedRow){
                $matchedGroup = $feeModel->getGroupById($assignedRow->fee_group_id);
            }
        }

        // Particulars list
        $particulars = [];
        if($matchedGroup){
            $particulars = $feeModel->getMasterByGroup($matchedGroup->id);
        }

        $allTypes = $feeModel->getTypes();

        echo json_encode([
            'success' => true,
            'class' => $cls,
            'student_count' => $studentCount,
            'group' => $matchedGroup,
            'particulars' => $particulars,
            'types' => $allTypes,
            'default_group_name' => 'Class ' . $cls->class_name . ' Fee Package'
        ]);
        exit;
    }

    public function ajaxSaveClassFeeParticulars(){
        header('Content-Type: application/json');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $feeModel = $this->model('Fee');
        $classModel = $this->model('SchoolClass');
        $schoolId = TenantContext::getSchoolId();

        $classId = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
        $groupId = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
        $groupName = trim($_POST['group_name'] ?? '');
        $lateFine = isset($_POST['late_fine']) && is_numeric($_POST['late_fine']) ? (float)$_POST['late_fine'] : 200.00;
        $syncAllStudents = !empty($_POST['sync_all_students']);

        $cls = $classModel->getClassById($classId);
        if(!$cls){
            echo json_encode(['success' => false, 'message' => 'Invalid class selected.']);
            exit;
        }

        if(empty($groupName)){
            $groupName = 'Class ' . $cls->class_name . ' Fee Package';
        }

        // 1. Create or update fee group
        if($groupId){
            $feeModel->updateGroup([
                'id' => $groupId,
                'name' => $groupName,
                'description' => 'Class fee structure for ' . $cls->class_name
            ]);
        } else {
            $groupId = (int)$feeModel->addGroup([
                'name' => $groupName,
                'description' => 'Class fee structure for ' . $cls->class_name
            ]);
        }

        if(!$groupId){
            echo json_encode(['success' => false, 'message' => 'Failed to create or update Fee Group.']);
            exit;
        }

        // 2. Process particulars
        $itemsJson = $_POST['items_json'] ?? '';
        $items = !empty($itemsJson) ? json_decode($itemsJson, true) : [];

        $savedMasterIds = [];
        $existingMasters = $feeModel->getMasterByGroup($groupId);
        $existingMap = [];
        foreach($existingMasters as $em){
            $existingMap[$em->fee_type_id] = $em;
        }

        if(is_array($items)){
            foreach($items as $it){
                $typeId = !empty($it['type_id']) ? (int)$it['type_id'] : 0;
                $amt = isset($it['amount']) && is_numeric($it['amount']) ? (float)$it['amount'] : 0.00;
                $fine = isset($it['fine']) && is_numeric($it['fine']) ? (float)$it['fine'] : $lateFine;
                $dueDate = !empty($it['due_date']) ? trim($it['due_date']) : date('Y-m-10');

                if(!$typeId && !empty($it['type_name'])){
                    $tName = trim($it['type_name']);
                    $tCode = !empty($it['type_code']) ? strtoupper(trim($it['type_code'])) : strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $tName), 0, 4));
                    $typeId = (int)$feeModel->addType([
                        'name' => $tName,
                        'code' => $tCode,
                        'description' => 'Fee head created for ' . $cls->class_name
                    ]);
                }

                if($typeId && $amt >= 0){
                    if(isset($existingMap[$typeId])){
                        $mId = (int)$existingMap[$typeId]->id;
                        $feeModel->updateMaster([
                            'id' => $mId,
                            'type_id' => $typeId,
                            'amount' => $amt,
                            'due_date' => $dueDate,
                            'fine' => $fine
                        ]);
                        $savedMasterIds[] = $mId;
                    } else {
                        $mId = (int)$feeModel->addMaster([
                            'group_id' => $groupId,
                            'type_id' => $typeId,
                            'amount' => $amt,
                            'due_date' => $dueDate,
                            'fine' => $fine
                        ]);
                        if($mId) $savedMasterIds[] = $mId;
                    }
                }
            }

            // Remove masters not in the updated list
            foreach($existingMasters as $em){
                if(!in_array((int)$em->id, $savedMasterIds)){
                    $feeModel->deleteMaster((int)$em->id);
                }
            }
        }

        // 3. Sync to all students of this class
        $syncedCount = 0;
        if($syncAllStudents && $classId){
            $db = new Database();
            $db->query("SELECT id FROM students WHERE class_id = :cid AND school_id = :sid");
            $db->bind(':cid', $classId);
            $db->bind(':sid', $schoolId);
            $students = $db->resultSet();

            $masters = $feeModel->getMasterByGroup($groupId);
            if(!empty($students)){
                foreach($students as $st){
                    $syncedCount++;
                    foreach($masters as $mst){
                        $db->query("SELECT id FROM student_fees WHERE student_id = :sid AND fee_groups_types_id = :fgtid AND school_id = :scid LIMIT 1");
                        $db->bind(':sid', $st->id);
                        $db->bind(':fgtid', $mst->id);
                        $db->bind(':scid', $schoolId);
                        $ex = $db->single();
                        if(!$ex){
                            $db->query("INSERT INTO student_fees (school_id, student_id, fee_groups_types_id) VALUES (:scid, :sid, :fgtid)");
                            $db->bind(':scid', $schoolId);
                            $db->bind(':sid', $st->id);
                            $db->bind(':fgtid', $mst->id);
                            $db->execute();
                        }
                    }
                }
            }
        }

        echo json_encode([
            'success' => true,
            'message' => "Class fee structure for {$cls->class_name} saved successfully!" . ($syncAllStudents ? " Synced to {$syncedCount} students." : ""),
            'group_id' => $groupId,
            'particulars' => $feeModel->getMasterByGroup($groupId)
        ]);
        exit;
    }

    public function ajaxQuickAssignStudentFee(){
        header('Content-Type: application/json');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $studentId = !empty($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
        if(!$studentId){
            echo json_encode(['success' => false, 'message' => 'Invalid student ID.']);
            exit;
        }

        $studentModel = $this->model('Student');
        $feeModel = $this->model('Fee');
        $student = $studentModel->getStudentById($studentId);

        if(!$student){
            echo json_encode(['success' => false, 'message' => 'Student not found.']);
            exit;
        }

        // Find appropriate fee group for this student's class
        $groupId = null;
        $groups = $feeModel->getGroups();
        if(!empty($student->class_name)){
            foreach($groups as $g){
                if(stripos($g->group_name, $student->class_name) !== false){
                    $groupId = (int)$g->id;
                    break;
                }
            }
        }

        // Fallback to first available fee group
        if(!$groupId && !empty($groups)){
            $groupId = (int)$groups[0]->id;
        }

        // If still no group exists at all, auto-create a standard class fee package
        if(!$groupId){
            $className = $student->class_name ?? 'General';
            $groupId = (int)$feeModel->addGroup([
                'name' => "Class {$className} Fee Package",
                'description' => "Standard Fee Structure for {$className}"
            ]);
            $types = $feeModel->getTypes();
            if(empty($types)){
                $tId = (int)$feeModel->addType(['name' => 'Monthly Tuition Fee', 'code' => 'TUIT', 'description' => 'Tuition fee']);
            } else {
                $tId = (int)$types[0]->id;
            }
            $feeModel->addMaster([
                'group_id' => $groupId,
                'type_id' => $tId,
                'amount' => 3000.00,
                'due_date' => date('Y-m-10'),
                'fine' => 200.00
            ]);
        }

        // Assign fee group to this student
        $feeModel->assignToStudent($groupId, $studentId);

        // Fetch refreshed student fee records
        $fees = $feeModel->getStudentFees($studentId);
        $history = $feeModel->getPaymentHistory($studentId);

        $totalAssigned = 0;
        $totalPaid = 0;
        foreach($fees as $f){
            $totalAssigned += (float)$f->amount;
            $totalPaid += (float)$f->total_paid;
        }
        $balance = max(0, $totalAssigned - $totalPaid);

        echo json_encode([
            'success' => true,
            'message' => 'Class fee structure successfully assigned to student!',
            'student_id' => $studentId,
            'fees' => $fees,
            'history' => $history,
            'total_assigned' => $totalAssigned,
            'total_paid' => $totalPaid,
            'balance' => $balance
        ]);
        exit;
    }

    // --- Collection ---
    public function collect(){
        $feeModel = $this->model('Fee');
        $studentModel = $this->model('Student');

        // Payment Screen for a specific student (direct access fallback)
        if(isset($_GET['student_id'])){
            $studentId = (int)$_GET['student_id'];
            $student = $studentModel->getStudentById($studentId);
            
            $data = [
                'fees' => $feeModel->getStudentFees($studentId),
                'student_id' => $studentId,
                'student' => $student
            ];
            $this->view('fees/payment', $data);
            return;
        }

        // AJAX runtime fetch for class / section
        if(isset($_GET['ajax']) && $_GET['ajax'] == 1){
            $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : null;
            $sectionId = !empty($_GET['section_id']) ? (int)$_GET['section_id'] : null;
            $studentsFiltered = $feeModel->getStudentsFeeOverview($classId, $sectionId);
            header('Content-Type: application/json');
            echo json_encode($studentsFiltered);
            exit;
        }

        // Main Collect Fees Screen
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');

        $classes = $classModel->getClasses();
        $sections = $sectionModel->getSections();
        $students = $feeModel->getStudentsFeeOverview();
        $groups = $feeModel->getGroups();
        $types = $feeModel->getTypes();

        $data = [
            'classes' => $classes,
            'sections' => $sections,
            'students' => $students,
            'groups' => $groups,
            'types' => $types
        ];
        $this->view('fees/collect', $data);
    }

    // AJAX endpoint to get student details and fees for modal popup
    public function ajaxGetStudentFees($student_id){
        $feeModel = $this->model('Fee');
        $studentModel = $this->model('Student');
        $studentId = (int)$student_id;
        
        $student = $studentModel->getStudentById($studentId);
        $fees = $feeModel->getStudentFees($studentId);
        $history = $feeModel->getPaymentHistory($studentId);

        $totalAssigned = 0;
        $totalPaid = 0;
        foreach($fees as $f){
            $totalAssigned += (float)$f->amount;
            $totalPaid += (float)$f->total_paid;
        }
        $balance = max(0, $totalAssigned - $totalPaid);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'student' => $student,
            'fees' => $fees,
            'history' => $history,
            'total_assigned' => $totalAssigned,
            'total_paid' => $totalPaid,
            'balance' => $balance
        ]);
        exit;
    }

    // AJAX endpoint to get payment history
    public function ajaxGetPaymentHistory($student_id){
        $feeModel = $this->model('Fee');
        $history = $feeModel->getPaymentHistory((int)$student_id);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'history' => $history
        ]);
        exit;
    }

    // AJAX endpoint to record payment without full page reload
    public function ajaxPayFee(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $feeModel = $this->model('Fee');
            $studentId = (int)$_POST['student_id'];
            $studentFeeId = (int)$_POST['student_fee_id'];
            $amount = (float)$_POST['amount'];
            $mode = trim($_POST['mode'] ?? 'Cash');
            $note = trim($_POST['note'] ?? '');

            $data = [
                'student_fee_id' => $studentFeeId,
                'mode' => $mode,
                'amount' => $amount,
                'discount' => 0,
                'fine' => 0,
                'payment_date' => date('Y-m-d'),
                'note' => $note
            ];
            $saved = $feeModel->addPayment($data);

            // Auto-sync into Accounting Incomes Ledger
            if ($saved) {
                $studentModel = $this->model('Student');
                $studentObj = $studentModel->getStudentById($studentId);
                $studentName = $studentObj ? $studentObj->name : ('Student #' . $studentId);
                if (class_exists('Income')) {
                    Income::syncFeePayment($saved, $studentName, $amount, date('Y-m-d'), $mode, 'REC-' . $saved);
                }
            }

            // Re-fetch updated fee records for student
            $updatedFees = $feeModel->getStudentFees($studentId);
            $history = $feeModel->getPaymentHistory($studentId);
            $totalAssigned = 0;
            $totalPaid = 0;
            foreach($updatedFees as $f){
                $totalAssigned += (float)$f->amount;
                $totalPaid += (float)$f->total_paid;
            }
            $balance = max(0, $totalAssigned - $totalPaid);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => $saved,
                'message' => 'Payment of $' . number_format($amount, 2) . ' recorded successfully!',
                'student_id' => $studentId,
                'total_assigned' => $totalAssigned,
                'total_paid' => $totalPaid,
                'balance' => $balance,
                'fees' => $updatedFees,
                'history' => $history
            ]);
            exit;
        }
    }

    // Void / Delete payment via AJAX
    public function ajaxDeletePayment($payment_id){
        $feeModel = $this->model('Fee');
        $studentId = !empty($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
        
        $deleted = $feeModel->deletePayment((int)$payment_id);

        $updatedFees = [];
        $history = [];
        $totalAssigned = 0;
        $totalPaid = 0;
        $balance = 0;

        if($studentId){
            $updatedFees = $feeModel->getStudentFees($studentId);
            $history = $feeModel->getPaymentHistory($studentId);
            foreach($updatedFees as $f){
                $totalAssigned += (float)$f->amount;
                $totalPaid += (float)$f->total_paid;
            }
            $balance = max(0, $totalAssigned - $totalPaid);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $deleted,
            'message' => $deleted ? 'Payment transaction voided and removed successfully!' : 'Could not void payment.',
            'student_id' => $studentId,
            'total_assigned' => $totalAssigned,
            'total_paid' => $totalPaid,
            'balance' => $balance,
            'fees' => $updatedFees,
            'history' => $history
        ]);
        exit;
    }

    // AJAX endpoint to get receipt details for printing
    public function ajaxGetReceipt($payment_id){
        $feeModel = $this->model('Fee');
        $payment = $feeModel->getPaymentById((int)$payment_id);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => (bool)$payment,
            'payment' => $payment
        ]);
        exit;
    }

    public function deletePayment($payment_id = null){
        $feeModel = $this->model('Fee');
        if(!$payment_id && isset($_POST['id'])) $payment_id = (int)$_POST['id'];
        $studentId = !empty($_REQUEST['student_id']) ? (int)$_REQUEST['student_id'] : null;
        if($payment_id){
            $feeModel->deletePayment($payment_id);
        }
        if($studentId){
            header('Location: ' . URLROOT . '/fees/collect?student_id=' . $studentId . '&success=payment_voided');
        } else {
            header('Location: ' . URLROOT . '/fees/collect?success=payment_voided');
        }
        exit;
    }

    public function ajaxGetSections($class_id){
        $sectionModel = $this->model('Section');
        $sections = $sectionModel->getSectionsByClassId((int)$class_id);
        header('Content-Type: application/json');
        echo json_encode($sections);
        exit;
    }

    public function pay(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $feeModel = $this->model('Fee');
            $data = [
                'student_fee_id' => $_POST['student_fee_id'],
                'mode' => $_POST['mode'],
                'amount' => $_POST['amount'],
                'discount' => 0,
                'fine' => 0,
                'payment_date' => date('Y-m-d'),
                'note' => $_POST['note']
            ];
            $feeModel->addPayment($data);
            header('Location: ' . URLROOT . '/fees/collect?student_id=' . $_POST['student_id']);
            exit;
        }
    }

    // ==========================================
    // PHASE 7: PAKISTANI 3-COPY BANK CHALLANS
    // ==========================================

    public function challan($student_id = null){
        $feeModel = $this->model('Fee');
        $classModel = $this->model('SchoolClass');
        $studentModel = $this->model('Student');

        if(!$student_id && isset($_GET['student_id'])){
            $student_id = (int)$_GET['student_id'];
        }

        $month = !empty($_GET['month']) ? trim($_GET['month']) : date('F');
        $year = !empty($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

        if($student_id){
            $challanData = $feeModel->getStudentChallanData($student_id, $month, $year);
            if(!$challanData){
                header('Location: ' . URLROOT . '/fees/challan?error=student_not_found');
                exit;
            }
            $data = [
                'challan' => $challanData,
                'bank' => $feeModel->getSchoolBank(),
                'month' => $month,
                'year' => $year
            ];
            $this->view('fees/challan', $data);
            return;
        }

        // Overview / Selector screen if no specific student is picked
        $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $classes = $classModel->getClasses();
        $students = $studentModel->getStudents();
        $bank = $feeModel->getSchoolBank();
        $groups = $feeModel->getGroups();

        $data = [
            'classes' => $classes,
            'students' => $students,
            'bank' => $bank,
            'groups' => $groups,
            'selected_class' => $classId,
            'month' => $month,
            'year' => $year
        ];
        $this->view('fees/challan_hub', $data);
    }

    public function batchChallans(){
        $feeModel = $this->model('Fee');
        $classModel = $this->model('SchoolClass');

        $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
        $sectionId = !empty($_GET['section_id']) ? (int)$_GET['section_id'] : null;
        $month = !empty($_GET['month']) ? trim($_GET['month']) : date('F');
        $year = !empty($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

        if(!$classId){
            $allClasses = $classModel->getClasses();
            if(!empty($allClasses)){
                $classId = (int)$allClasses[0]->id;
            } else {
                header('Location: ' . URLROOT . '/fees/challan?error=select_class');
                exit;
            }
        }

        $options = [];
        if (!empty($_GET['student_ids'])) {
            $rawIds = is_array($_GET['student_ids']) ? $_GET['student_ids'] : explode(',', (string)$_GET['student_ids']);
            $options['student_ids'] = array_filter(array_map('intval', $rawIds));
        }
        if (isset($_GET['late_fine']) && is_numeric($_GET['late_fine'])) {
            $options['late_fine'] = (float)$_GET['late_fine'];
        }
        if (!empty($_GET['issue_date'])) $options['issue_date'] = trim($_GET['issue_date']);
        if (!empty($_GET['due_date'])) $options['due_date'] = trim($_GET['due_date']);
        if (!empty($_GET['validity_date'])) $options['validity_date'] = trim($_GET['validity_date']);
        if (!empty($_GET['instructions'])) $options['instructions'] = trim($_GET['instructions']);

        $challans = $feeModel->getClassChallans($classId, $sectionId, $month, $year, $options);
        $classObj = $classModel->getClassById($classId);
        $bank = $feeModel->getSchoolBank();
        $allClasses = $classModel->getClasses();
        $feeTypes = $feeModel->getTypes();
        $feeGroups = $feeModel->getGroups();

        $savedFine = class_exists('SiteSetting') ? SiteSetting::getGlobal('challan_late_fine', '200') : '200';
        $savedInstructions = class_exists('SiteSetting') ? SiteSetting::getGlobal('challan_instructions', '') : '';
        $savedParticulars = class_exists('SiteSetting') ? SiteSetting::getGlobal('default_challan_particulars', '') : '';

        $data = [
            'challans' => $challans,
            'class' => $classObj,
            'month' => $month,
            'year' => $year,
            'bank' => $bank,
            'classes' => $allClasses,
            'fee_types' => $feeTypes,
            'fee_groups' => $feeGroups,
            'late_fine' => !empty($options['late_fine']) ? $options['late_fine'] : (float)$savedFine,
            'instructions' => $savedInstructions,
            'default_particulars' => $savedParticulars,
            'current_section_id' => $sectionId
        ];
        $this->view('fees/batch_challans', $data);
    }

    // Module 16: Defaulters & Aging Ledger
    public function defaulters(){
        $feeModel = $this->model('Fee');
        $classModel = $this->model('SchoolClass');

        $classId = !empty($_GET['class_id']) ? (int)$_GET['class_id'] : null;
        $aging = !empty($_GET['aging']) ? trim($_GET['aging']) : 'all';

        $ledger = $feeModel->getDefaultersLedger($classId, $aging);
        $classes = $classModel->getClasses();

        $data = [
            'ledger' => $ledger,
            'classes' => $classes,
            'selected_class' => $classId,
            'selected_aging' => $aging
        ];
        $this->view('fees/defaulters', $data);
    }

    // Batch generate monthly fee vouchers
    public function generateMonthly(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $feeModel = $this->model('Fee');
            $classId = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
            $month = !empty($_POST['month']) ? trim($_POST['month']) : date('F');
            $year = !empty($_POST['year']) ? (int)$_POST['year'] : (int)date('Y');
            $dueDate = !empty($_POST['due_date']) ? trim($_POST['due_date']) : date('Y-m-10');
            $groupId = !empty($_POST['fee_group_id']) ? (int)$_POST['fee_group_id'] : null;

            $billingMonth = $month . ' ' . $year;
            $count = $feeModel->generateBatchMonthlyInvoices($classId, $billingMonth, $dueDate, $groupId);

            header('Location: ' . URLROOT . '/fees/collect?success=invoices_generated&count=' . $count);
            exit;
        }
        header('Location: ' . URLROOT . '/fees/collect');
        exit;
    }

    // AJAX: Generate Admission Fee Token / Voucher for Fresh Admissions & Walk-in Candidates
    public function ajaxGenerateAdmissionToken(){
        header('Content-Type: application/json');
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $feeModel = $this->model('Fee');
        $schoolId = TenantContext::getSchoolId() ?: 1;

        $studentId = !empty($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
        $studentName = trim($_POST['student_name'] ?? '');
        $fatherName = trim($_POST['father_name'] ?? '');
        $className = trim($_POST['class_name'] ?? 'General');
        $admissionNo = trim($_POST['admission_no'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $paymentStatus = trim($_POST['payment_status'] ?? 'paid');
        $paymentMode = trim($_POST['payment_mode'] ?? 'Cash');
        $dueDate = !empty($_POST['due_date']) ? trim($_POST['due_date']) : date('Y-m-d', strtotime('+7 days'));
        $discount = !empty($_POST['discount']) ? (float)$_POST['discount'] : 0.00;
        $remarks = trim($_POST['remarks'] ?? 'Fresh Admission Registration & Token Voucher');

        $rawItems = $_POST['items'] ?? [];
        if(is_string($rawItems)){
            $rawItems = json_decode($rawItems, true) ?: [];
        }

        $items = [];
        $subtotal = 0;
        if(!empty($rawItems) && is_array($rawItems)){
            foreach($rawItems as $item){
                $amt = (float)($item['amount'] ?? 0);
                $title = trim($item['name'] ?? $item['title'] ?? '');
                if(!empty($title) && $amt > 0){
                    $items[] = [
                        'title' => $title,
                        'amount' => $amt
                    ];
                    $subtotal += $amt;
                }
            }
        }

        if(empty($items)){
            $items = [
                ['title' => 'Admission Registration Fee', 'amount' => 5000.00],
                ['title' => 'Prospectus & Admission Token', 'amount' => 1000.00],
                ['title' => 'Security Deposit (Refundable)', 'amount' => 2000.00]
            ];
            $subtotal = 8000.00;
        }

        $netTotal = max(0, $subtotal - $discount);
        $randomSeq = str_pad((string)mt_rand(100, 9999), 4, '0', STR_PAD_LEFT);
        $tokenNo = 'TKN-ADM-' . date('Y') . '-' . $randomSeq;

        if($studentId > 0){
            $studentModel = $this->model('Student');
            $stObj = $studentModel->getStudentById($studentId);
            if($stObj){
                if(empty($studentName)) $studentName = $stObj->name;
                if(empty($fatherName)) $fatherName = $stObj->father_name;
                if(empty($className)) $className = $stObj->class_name;
                if(empty($admissionNo)) $admissionNo = $stObj->admission_no;
                if(empty($phone)) $phone = $stObj->father_phone ?: $stObj->parent_phone;
            }

            $groups = $feeModel->getGroups();
            $groupId = !empty($groups) ? (int)$groups[0]->id : 0;
            if(!$groupId){
                $groupId = (int)$feeModel->addGroup([
                    'name' => 'Admission & Enrolment Package',
                    'description' => 'One-time admission registration fee package'
                ]);
            }

            $types = $feeModel->getTypes();
            $typeMap = [];
            foreach($types as $t){
                $typeMap[strtolower(trim($t->type_name))] = (int)$t->id;
            }

            foreach($items as $it){
                $itName = $it['title'];
                $itAmt  = (float)$it['amount'];
                $itKey  = strtolower(trim($itName));

                // Resolve or create fee type
                $tId = $typeMap[$itKey] ?? null;
                if(!$tId){
                    $tId = (int)$feeModel->addType([
                        'name'        => $itName,
                        'code'        => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $itName), 0, 4)) ?: 'ADM',
                        'description' => 'Admission Token item'
                    ]);
                    $typeMap[$itKey] = $tId;
                }

                // Resolve or create fee_groups_types master row (via model — no raw DB)
                $masterRow = $feeModel->getMasterByGroupAndType($groupId, $tId);
                $masterId  = $masterRow ? (int)$masterRow->id : null;

                if(!$masterId){
                    $masterId = (int)$feeModel->addMaster([
                        'group_id' => $groupId,
                        'type_id'  => $tId,
                        'amount'   => $itAmt,
                        'due_date' => $dueDate,
                        'fine'     => 0
                    ]);
                }

                // Insert student_fees record via model (no raw DB)
                $status = ($paymentStatus === 'paid') ? 'paid' : 'unpaid';
                $sfId   = (int)$feeModel->addStudentFeeForToken([
                    'student_id'          => $studentId,
                    'fee_groups_types_id' => $masterId,
                    'challan_no'          => $tokenNo,
                    'billing_month'       => 'Admission ' . date('Y'),
                    'due_date'            => $dueDate,
                    'status'              => $status
                ]);

                if($paymentStatus === 'paid' && $sfId > 0){
                    $feeModel->addPayment([
                        'student_fee_id' => $sfId,
                        'mode'           => $paymentMode,
                        'amount'         => $itAmt,
                        'discount'       => 0,
                        'fine'           => 0,
                        'payment_date'   => date('Y-m-d'),
                        'note'           => "Admission Token # {$tokenNo} ({$itName})"
                    ]);
                }
            }

            // Sync to income ledger — load model explicitly so class is guaranteed in memory
            if($paymentStatus === 'paid'){
                $incomeModel = $this->model('Income');
                if(method_exists($incomeModel, 'syncFeePayment')){
                    $incomeModel->syncFeePayment(0, $studentName ?: ('Student #' . $studentId), $netTotal, date('Y-m-d'), $paymentMode, $tokenNo);
                }
            }
        }

        $schoolName = class_exists('SiteSetting') ? SiteSetting::getGlobal('school_name', 'PAK ACADEMY MODEL SCHOOL SYSTEM') : 'PAK ACADEMY MODEL SCHOOL SYSTEM';
        $campusName = class_exists('SiteSetting') ? SiteSetting::getGlobal('campus_name', 'MAIN EXECUTIVE CAMPUS') : 'MAIN EXECUTIVE CAMPUS';
        $schoolAddress = class_exists('SiteSetting') ? SiteSetting::getGlobal('school_address', 'Education Complex, Lahore, Pakistan') : 'Education Complex, Lahore, Pakistan';
        $schoolPhone = class_exists('SiteSetting') ? SiteSetting::getGlobal('school_phone', '+92 42 35889000') : '+92 42 35889000';
        $schoolLogo = class_exists('SiteSetting') ? SiteSetting::getGlobal('logo', '') : '';

        $bank = $feeModel->getDefaultBank();
        if(!$bank){
            $bank = (object)[
                'bank_name'     => 'Habib Bank Limited (HBL)',
                'branch_name'   => 'City Campus Branch',
                'account_title' => $schoolName,
                'account_no'    => '1029-3847-2910-01',
                'iban'          => 'PK36HABB0001029384729101'
            ];
        }

        echo json_encode([
            'success' => true,
            'message' => 'Admission Fee Token generated successfully!',
            'token_no' => $tokenNo,
            'student_id' => $studentId,
            'student_name' => $studentName ?: 'Walk-in Candidate',
            'father_name' => $fatherName ?: 'Guardian',
            'class_name' => $className,
            'admission_no' => $admissionNo ?: 'PROV-' . $randomSeq,
            'phone' => $phone,
            'issue_date' => date('d-M-Y h:i A'),
            'due_date' => date('d-M-Y', strtotime($dueDate)),
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'net_total' => $netTotal,
            'payment_status' => $paymentStatus,
            'payment_mode' => $paymentMode,
            'remarks' => $remarks,
            'school' => [
                'name' => $schoolName,
                'campus' => $campusName,
                'address' => $schoolAddress,
                'phone' => $schoolPhone,
                'logo' => $schoolLogo
            ],
            'bank' => $bank
        ]);
        exit;
    }

    // Save Bank particulars & Challan Settings
    public function saveBank(){
        return $this->saveChallanSettings();
    }

    public function saveChallanSettings(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $feeModel = $this->model('Fee');
            $siteSetting = class_exists('SiteSetting') ? new SiteSetting() : null;

            // 1. Save Bank Particulars
            $bankData = [
                'bank_name' => trim($_POST['bank_name'] ?? 'Habib Bank Limited (HBL)'),
                'branch_name' => trim($_POST['branch_name'] ?? ''),
                'account_title' => trim($_POST['account_title'] ?? ''),
                'account_no' => trim($_POST['account_no'] ?? ''),
                'iban' => trim($_POST['iban'] ?? '')
            ];
            $feeModel->saveSchoolBank($bankData);

            // 2. Save Late Fine & Instructions into SiteSettings
            if ($siteSetting) {
                if (isset($_POST['late_fine']) && is_numeric($_POST['late_fine'])) {
                    $siteSetting->updateSetting('challan_late_fine', (float)$_POST['late_fine']);
                }
                if (isset($_POST['instructions'])) {
                    $siteSetting->updateSetting('challan_instructions', trim($_POST['instructions']));
                }
                if (!empty($_POST['default_particulars_json'])) {
                    $siteSetting->updateSetting('default_challan_particulars', trim($_POST['default_particulars_json']));
                }
            }

            // 3. Optional: Add new Fee Head/Type on the fly & link to Fee Module
            if (!empty($_POST['new_fee_type_name'])) {
                $typeName = trim($_POST['new_fee_type_name']);
                $typeCode = !empty($_POST['new_fee_type_code']) ? strtoupper(trim($_POST['new_fee_type_code'])) : strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $typeName), 0, 4));
                $amount = isset($_POST['new_fee_type_amount']) && is_numeric($_POST['new_fee_type_amount']) ? (float)$_POST['new_fee_type_amount'] : 0.00;
                $targetClassId = !empty($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
                $groupId = !empty($_POST['fee_group_id']) ? (int)$_POST['fee_group_id'] : 0;

                $newTypeId = $feeModel->addType([
                    'name' => $typeName,
                    'code' => $typeCode,
                    'description' => trim($_POST['new_fee_type_desc'] ?? '')
                ]);

                // If user entered an amount or wants to link to Fee Module directly
                if ($newTypeId) {
                    // Find or create Fee Group
                    if (!$groupId) {
                        $groups = $feeModel->getGroups();
                        if (!empty($groups)) {
                            $groupId = (int)$groups[0]->id;
                        } else {
                            $groupId = (int)$feeModel->addGroup([
                                'name' => 'General Academic Fee Group',
                                'description' => 'Default system fee package'
                            ]);
                        }
                    }

                    // Add to Fee Master (fee_groups_types) with the specified amount and late fine
                    $savedFine = class_exists('SiteSetting') ? (float)SiteSetting::getGlobal('challan_late_fine', 200.00) : 200.00;
                    $masterId = $feeModel->addMaster([
                        'group_id' => $groupId,
                        'type_id' => $newTypeId,
                        'amount' => $amount > 0 ? $amount : 500.00,
                        'due_date' => date('Y-m-10'),
                        'fine' => $savedFine
                    ]);

                    // If a class is active, assign this fee head directly to students of the class
                    if ($targetClassId && $masterId) {
                        // Fetch students of this class
                        $db = new Database();
                        $db->query("SELECT id FROM students WHERE class_id = :cid AND school_id = :sid");
                        $db->bind(':cid', $targetClassId);
                        $db->bind(':sid', TenantContext::getSchoolId());
                        $students = $db->resultSet();

                        if (!empty($students)) {
                            foreach ($students as $st) {
                                // Check if already assigned
                                $db->query("SELECT id FROM student_fees WHERE student_id = :sid AND fee_groups_types_id = :fgtid AND school_id = :scid");
                                $db->bind(':sid', $st->id);
                                $db->bind(':fgtid', $masterId);
                                $db->bind(':scid', TenantContext::getSchoolId());
                                if (!$db->single()) {
                                    $db->query("INSERT INTO student_fees (school_id, student_id, fee_groups_types_id) VALUES (:scid, :sid, :fgtid)");
                                    $db->bind(':scid', TenantContext::getSchoolId());
                                    $db->bind(':sid', $st->id);
                                    $db->bind(':fgtid', $masterId);
                                    $db->execute();
                                }
                            }
                        }
                    }
                }
            }

            $_SESSION['flash_success'] = "Fee Particular added, assigned to class, and saved successfully in Fee Module!";
            $redirect = AuthGuard::sanitizeRedirect($_POST['redirect_to'] ?? '', '/fees/challan?success=bank_updated');
            header('Location: ' . URLROOT . $redirect);
            exit;
        }
    }

    // Formal printable Fee Default Demand Notice
    public function printNotice($student_id){
        $feeModel = $this->model('Fee');
        $studentId = (int)$student_id;
        $challanData = $feeModel->getStudentChallanData($studentId);

        if(!$challanData){
            header('Location: ' . URLROOT . '/fees/defaulters?error=student_not_found');
            exit;
        }

        $data = [
            'challan' => $challanData,
            'bank' => $feeModel->getSchoolBank()
        ];
        $this->view('fees/notice', $data);
    }
}

