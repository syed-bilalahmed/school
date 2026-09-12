<?php
class FrontOffice {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // --- Front Office KPI Stats ---
    public function getStats(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $today = date('Y-m-d');

        // Today Visitors
        $this->db->query("SELECT COUNT(*) as cnt FROM visitor_book WHERE school_id = :sch AND date = :today");
        $this->db->bind(':sch', $schoolId);
        $this->db->bind(':today', $today);
        $resTodayVis = $this->db->single();
        $todayVisitors = $resTodayVis ? (int)$resTodayVis->cnt : 0;

        // Currently Inside
        $this->db->query("SELECT COUNT(*) as cnt FROM visitor_book WHERE school_id = :sch AND (status = 'Checked In' OR status IS NULL OR status = '') AND (out_time IS NULL OR out_time = '') AND date = :today");
        $this->db->bind(':sch', $schoolId);
        $this->db->bind(':today', $today);
        $resInside = $this->db->single();
        $currentlyInside = $resInside ? (int)$resInside->cnt : 0;

        // Today's Student Gate Passes
        $this->db->query("SELECT COUNT(*) as cnt FROM student_gate_passes WHERE school_id = :sch AND pass_date = :today");
        $this->db->bind(':sch', $schoolId);
        $this->db->bind(':today', $today);
        $resGatePass = $this->db->single();
        $todayGatePasses = $resGatePass ? (int)$resGatePass->cnt : 0;

        // Pending Admission Enquiries
        $this->db->query("SELECT COUNT(*) as cnt FROM admission_enquiry WHERE school_id = :sch AND (status = 'New' OR status = 'Follow Up' OR status IS NULL)");
        $this->db->bind(':sch', $schoolId);
        $resEnq = $this->db->single();
        $pendingEnquiries = $resEnq ? (int)$resEnq->cnt : 0;

        // Total Dispatches
        $this->db->query("SELECT COUNT(*) as cnt FROM postal_records WHERE school_id = :sch");
        $this->db->bind(':sch', $schoolId);
        $resDisp = $this->db->single();
        $totalDispatches = $resDisp ? (int)$resDisp->cnt : 0;

        return [
            'today_visitors' => $todayVisitors,
            'currently_inside' => $currentlyInside,
            'today_gate_passes' => $todayGatePasses,
            'pending_enquiries' => $pendingEnquiries,
            'total_dispatches' => $totalDispatches
        ];
    }

    // --- Admission Enquiry CRM ---
    public function addEnquiry($data){
        $this->db->query("INSERT INTO admission_enquiry (school_id, name, phone, email, address, description, date, next_follow_up_date, assigned_to, reference, source, class_id, no_of_child, father_name, mother_name, dob, gender, guardian_name, guardian_relation, previous_school, status, discount_offered)
                          VALUES (:school_id, :name, :phone, :email, :address, :desc, :date, :next, :assign, :ref, :src, :cid, :child, :fname, :mname, :dob, :gender, :gname, :grel, :prev_school, :status, :disc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email'] ?? '');
        $this->db->bind(':address', $data['address'] ?? '');
        $this->db->bind(':desc', $data['description'] ?? '');
        $this->db->bind(':date', !empty($data['date']) ? $data['date'] : date('Y-m-d'));
        $this->db->bind(':next', !empty($data['next_follow_up_date']) ? $data['next_follow_up_date'] : null);
        $this->db->bind(':assign', !empty($data['assigned_to']) ? $data['assigned_to'] : null);
        $this->db->bind(':ref', $data['reference'] ?? '');
        $this->db->bind(':src', $data['source'] ?? 'Direct Walk-in');
        $this->db->bind(':cid', !empty($data['class_id']) ? $data['class_id'] : null);
        $this->db->bind(':child', !empty($data['no_of_child']) ? (int)$data['no_of_child'] : 1);
        $this->db->bind(':fname', $data['father_name'] ?? '');
        $this->db->bind(':mname', $data['mother_name'] ?? '');
        $this->db->bind(':dob', !empty($data['dob']) ? $data['dob'] : null);
        $this->db->bind(':gender', $data['gender'] ?? 'Male');
        $this->db->bind(':gname', $data['guardian_name'] ?? '');
        $this->db->bind(':grel', $data['guardian_relation'] ?? '');
        $this->db->bind(':prev_school', $data['previous_school'] ?? '');
        $this->db->bind(':status', $data['status'] ?? 'New');
        $this->db->bind(':disc', !empty($data['discount_offered']) ? $data['discount_offered'] : 0.00);
        
        if ($this->db->execute()) {
            $lastId = $this->db->lastInsertId();
            return !empty($lastId) ? (int)$lastId : true;
        }
        return false;
    }

    public function getEnquiries($filterStatus = null){
        $sql = "SELECT ae.*, u.name as staff_name, c.class_name
                FROM admission_enquiry ae
                LEFT JOIN users u ON ae.assigned_to = u.id
                LEFT JOIN classes c ON ae.class_id = c.id
                WHERE ae.school_id = :school_id";
        if (!empty($filterStatus)) {
            $sql .= " AND ae.status = :status";
        }
        $sql .= " ORDER BY ae.date DESC, ae.id DESC";

        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        if (!empty($filterStatus)) {
            $this->db->bind(':status', $filterStatus);
        }
        return $this->db->resultSet();
    }

    public function getOnlineAdmissionCounts(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT 
            COUNT(*) as total_all,
            SUM(CASE WHEN (converted_student_id IS NULL OR converted_student_id = 0) AND (status IS NULL OR status NOT IN ('Approved', 'Rejected')) THEN 1 ELSE 0 END) as total_pending,
            SUM(CASE WHEN status = 'New' AND (converted_student_id IS NULL OR converted_student_id = 0) THEN 1 ELSE 0 END) as total_new,
            SUM(CASE WHEN status = 'Follow Up' AND (converted_student_id IS NULL OR converted_student_id = 0) THEN 1 ELSE 0 END) as total_follow,
            SUM(CASE WHEN (converted_student_id IS NOT NULL AND converted_student_id > 0) OR status = 'Approved' THEN 1 ELSE 0 END) as total_approved,
            SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as total_rejected
        FROM admission_enquiry 
        WHERE school_id = :sch");
        $this->db->bind(':sch', $schoolId);
        $res = $this->db->single();
        if(!$res){
            return (object)[
                'total_all' => 0,
                'total_pending' => 0,
                'total_new' => 0,
                'total_follow' => 0,
                'total_approved' => 0,
                'total_rejected' => 0
            ];
        }
        $res->total_all = (int)($res->total_all ?? 0);
        $res->total_pending = (int)($res->total_pending ?? 0);
        $res->total_new = (int)($res->total_new ?? 0);
        $res->total_follow = (int)($res->total_follow ?? 0);
        $res->total_approved = (int)($res->total_approved ?? 0);
        $res->total_rejected = (int)($res->total_rejected ?? 0);
        return $res;
    }

    public function getOnlineAdmissions($filterStatus = null, $classId = null, $sort = 'AZ', $search = null){
        $sql = "SELECT ae.*, u.name as staff_name, 
                       COALESCE(c_enrolled.class_name, c.class_name) as class_name,
                       st.admission_no as enrolled_admission_no, 
                       st.roll_no as enrolled_roll_no,
                       st.id as enrolled_student_id, 
                       st.class_id as enrolled_class_id,
                       sec.section_name as enrolled_section_name
                FROM admission_enquiry ae
                LEFT JOIN users u ON ae.assigned_to = u.id
                LEFT JOIN classes c ON ae.class_id = c.id
                LEFT JOIN students st ON ae.converted_student_id = st.id
                LEFT JOIN classes c_enrolled ON st.class_id = c_enrolled.id
                LEFT JOIN sections sec ON st.section_id = sec.id
                WHERE ae.school_id = :school_id";

        // Filter by online source if needed, or include all web submissions
        // $sql .= " AND ae.source = 'online'";

        $statusParam = null;
        if (empty($filterStatus) || $filterStatus === 'pending') {
            // Default pending inbox: only applicants not yet approved or enrolled into class
            $sql .= " AND (ae.converted_student_id IS NULL OR ae.converted_student_id = 0) AND (ae.status IS NULL OR ae.status NOT IN ('Approved', 'Rejected'))";
        } elseif (strtolower($filterStatus) === 'approved' || $filterStatus === 'enrolled') {
            // Enrolled & approved archive
            $sql .= " AND ((ae.converted_student_id IS NOT NULL AND ae.converted_student_id > 0) OR ae.status = 'Approved')";
        } elseif ($filterStatus === 'all') {
            // All records regardless of status
        } else {
            $sql .= " AND ae.status = :status";
            $statusParam = $filterStatus;
            if ($filterStatus === 'New' || $filterStatus === 'Follow Up') {
                $sql .= " AND (ae.converted_student_id IS NULL OR ae.converted_student_id = 0)";
            }
        }

        if (!empty($classId) && $classId !== 'all') {
            $sql .= " AND (ae.class_id = :class_id OR st.class_id = :class_id)";
        }
        if (!empty($search)) {
            $sql .= " AND (ae.name LIKE :search OR ae.father_name LIKE :search OR ae.phone LIKE :search OR ae.email LIKE :search)";
        }

        if ($sort === 'AZ') {
            $sql .= " ORDER BY ae.name ASC, ae.id DESC";
        } elseif ($sort === 'ZA') {
            $sql .= " ORDER BY ae.name DESC, ae.id DESC";
        } elseif ($sort === 'DATE_ASC') {
            $sql .= " ORDER BY ae.date ASC, ae.id ASC";
        } else {
            $sql .= " ORDER BY ae.date DESC, ae.id DESC";
        }

        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        if ($statusParam !== null) {
            $this->db->bind(':status', $statusParam);
        }
        if (!empty($classId) && $classId !== 'all') {
            $this->db->bind(':class_id', $classId);
        }
        if (!empty($search)) {
            $this->db->bind(':search', '%' . $search . '%');
        }
        return $this->db->resultSet();
    }

    /**
     * Converts & Enrolls an approved Online Admission Enquiry directly into its Class & Students roster
     */
    public function enrollEnquiryAsStudent($id, $options = []){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $enquiry = $this->getEnquiryById($id);
        if(!$enquiry){
            return ['success' => false, 'message' => 'Admission application record not found.'];
        }

        // If already converted, check if student exists
        if(!empty($enquiry->converted_student_id)){
            $this->db->query("SELECT id, admission_no, roll_no, class_id FROM students WHERE id = :sid AND school_id = :sch");
            $this->db->bind(':sid', $enquiry->converted_student_id);
            $this->db->bind(':sch', $schoolId);
            $existingStd = $this->db->single();
            if($existingStd){
                $this->updateEnquiryStatus($id, 'Approved');
                return [
                    'success' => true,
                    'student_id' => $existingStd->id,
                    'admission_no' => $existingStd->admission_no,
                    'roll_no' => $existingStd->roll_no,
                    'class_id' => $existingStd->class_id,
                    'class_name' => $enquiry->class_name ?: 'Selected Class',
                    'student_name' => trim($enquiry->name),
                    'message' => 'Student is already enrolled in this class.'
                ];
            }
        }

        // 1. Determine Class
        $classId = !empty($options['class_id']) ? (int)$options['class_id'] : (int)($enquiry->class_id ?? 0);
        if($classId <= 0){
            $this->db->query("SELECT id, class_name FROM classes WHERE school_id = :sch ORDER BY id ASC LIMIT 1");
            $this->db->bind(':sch', $schoolId);
            $defClass = $this->db->single();
            if($defClass){
                $classId = (int)$defClass->id;
                $className = $defClass->class_name;
            } else {
                return ['success' => false, 'message' => 'No school classes found. Please set up a class first.'];
            }
        } else {
            $this->db->query("SELECT class_name FROM classes WHERE id = :cid AND school_id = :sch");
            $this->db->bind(':cid', $classId);
            $this->db->bind(':sch', $schoolId);
            $cRow = $this->db->single();
            $className = $cRow ? $cRow->class_name : 'Class';
        }

        // 2. Determine Section
        $sectionId = !empty($options['section_id']) ? (int)$options['section_id'] : null;
        if(empty($sectionId)){
            $this->db->query("SELECT id, section_name FROM sections WHERE class_id = :cid AND school_id = :sch ORDER BY id ASC LIMIT 1");
            $this->db->bind(':cid', $classId);
            $this->db->bind(':sch', $schoolId);
            $defSec = $this->db->single();
            if($defSec){
                $sectionId = (int)$defSec->id;
            }
        }

        // 3. Determine Academic Session
        $sessionId = !empty($options['academic_session_id']) ? (int)$options['academic_session_id'] : null;
        if(empty($sessionId)){
            $this->db->query("SELECT id FROM academic_sessions WHERE school_id = :sch AND is_current = 1 LIMIT 1");
            $this->db->bind(':sch', $schoolId);
            $curSess = $this->db->single();
            if(!$curSess){
                $this->db->query("SELECT id FROM academic_sessions WHERE school_id = :sch ORDER BY id DESC LIMIT 1");
                $this->db->bind(':sch', $schoolId);
                $curSess = $this->db->single();
            }
            $sessionId = $curSess ? (int)$curSess->id : null;
        }

        // 4. Generate Admission Number
        $admissionNo = trim($options['admission_no'] ?? '');
        if(empty($admissionNo)){
            $prefix = 'STD';
            if(class_exists('SiteSetting')){
                $siteSetting = new SiteSetting();
                $allSettings = $siteSetting->getAllSettings();
                if(!empty($allSettings['admission_prefix'])){
                    $prefix = $allSettings['admission_prefix'];
                }
            }
            $year = date('y');
            $searchPrefix = "{$prefix}-{$year}-";
            
            $this->db->query("SELECT admission_no FROM students WHERE school_id = :sch AND admission_no LIKE :pfx ORDER BY id DESC LIMIT 1");
            $this->db->bind(':sch', $schoolId);
            $this->db->bind(':pfx', $searchPrefix . '%');
            $lastStd = $this->db->single();
            
            $newSeq = 1;
            if($lastStd && !empty($lastStd->admission_no)){
                $numPart = intval(str_replace($searchPrefix, '', $lastStd->admission_no));
                $newSeq = $numPart + 1;
            }
            $admissionNo = $searchPrefix . str_pad($newSeq, 3, '0', STR_PAD_LEFT);
        }

        // 5. Generate Roll Number
        $rollNo = trim($options['roll_no'] ?? '');
        if(empty($rollNo)){
            if($sectionId){
                $this->db->query("SELECT MAX(CAST(roll_no AS UNSIGNED)) as max_roll FROM students WHERE school_id = :sch AND class_id = :cid AND section_id = :sec");
                $this->db->bind(':sch', $schoolId);
                $this->db->bind(':cid', $classId);
                $this->db->bind(':sec', $sectionId);
            } else {
                $this->db->query("SELECT MAX(CAST(roll_no AS UNSIGNED)) as max_roll FROM students WHERE school_id = :sch AND class_id = :cid");
                $this->db->bind(':sch', $schoolId);
                $this->db->bind(':cid', $classId);
            }
            $rRow = $this->db->single();
            $nextRoll = ($rRow && $rRow->max_roll) ? ((int)$rRow->max_roll + 1) : 1;
            $rollNo = str_pad($nextRoll, 3, '0', STR_PAD_LEFT);
        }

        // 6. User Account (Portal Login)
        $studentEmail = trim($enquiry->email ?? '');
        if(empty($studentEmail)){
            $cleanCode = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $admissionNo));
            $studentEmail = "std_" . $cleanCode . "@school.local";
        }

        $this->db->query("SELECT id FROM users WHERE email = :em LIMIT 1");
        $this->db->bind(':em', $studentEmail);
        $userRow = $this->db->single();
        if($userRow){
            $userId = (int)$userRow->id;
        } else {
            $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);
            $this->db->query("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :pwd, 'student')");
            $this->db->bind(':name', trim($enquiry->name));
            $this->db->bind(':email', $studentEmail);
            $this->db->bind(':pwd', $defaultPassword);
            if(!$this->db->execute()){
                return ['success' => false, 'message' => 'Failed to initialize user credentials for student.'];
            }
            $userId = (int)$this->db->lastInsertId();
        }

        // 7. Parse B-Form / CNIC from description if present
        $bformCnic = '';
        $fatherCnic = '';
        if(!empty($enquiry->description)){
            if(preg_match('/Student B-Form:\s*([^|]+)/i', $enquiry->description, $m)){
                $bformCnic = trim($m[1]);
            }
            if(preg_match('/Father CNIC:\s*([^|]+)/i', $enquiry->description, $m)){
                $fatherCnic = trim($m[1]);
            }
        }
        if(!empty($options['bform_cnic'])) $bformCnic = trim($options['bform_cnic']);
        if(!empty($options['father_cnic'])) $fatherCnic = trim($options['father_cnic']);

        // 8. Family linking
        $familyId = '';
        if(class_exists('Family') && !empty($fatherCnic)){
            $famModel = new Family();
            $familyId = $famModel->findOrCreateFamilyByCnic($fatherCnic, $enquiry->father_name ?? '', $enquiry->phone ?? '');
        }

        // 9. Register Student
        require_once APPROOT . '/Models/Student.php';
        $studentModel = new Student();

        $studentData = [
            'name' => trim($enquiry->name),
            'email' => $studentEmail,
            'phone' => trim($enquiry->phone ?? ''),
            'parent_phone' => trim($enquiry->phone ?? ''),
            'admission_no' => $admissionNo,
            'reg_no' => trim($options['reg_no'] ?? ''),
            'roll_no' => $rollNo,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'dob' => !empty($enquiry->dob) ? $enquiry->dob : null,
            'gender' => !empty($enquiry->gender) ? $enquiry->gender : 'Male',
            'blood_group' => trim($options['blood_group'] ?? ''),
            'bform_cnic' => $bformCnic,
            'father_name' => trim($enquiry->father_name ?? ''),
            'father_cnic' => $fatherCnic,
            'mother_name' => trim($enquiry->mother_name ?? ''),
            'guardian_name' => trim($enquiry->guardian_name ?? ($enquiry->father_name ?? '')),
            'guardian_relation' => trim($enquiry->guardian_relation ?? 'Father'),
            'address' => trim($enquiry->address ?? ''),
            'permanent_address' => trim($enquiry->address ?? ''),
            'previous_school' => trim($enquiry->previous_school ?? ''),
            'admission_date' => date('Y-m-d'),
            'status' => 'Active',
            'family_id' => $familyId,
            'concession_type' => 'None',
            'sibling_discount_percent' => 0.00,
            'custom_discount_amount' => !empty($enquiry->discount_offered) ? (float)$enquiry->discount_offered : 0.00,
            'academic_session_id' => $sessionId,
            'is_fresh_admission' => 1,
            'religion' => 'Islam',
            'nationality' => 'Pakistani',
            'mother_tongue' => 'Urdu'
        ];

        $newStudentId = $studentModel->registerStudent($studentData, $userId);
        if(!$newStudentId){
            return ['success' => false, 'message' => 'Database error while creating student record.'];
        }

        // Auto-assign class fee structure to newly enrolled student
        try {
            require_once APPROOT . '/Models/Fee.php';
            $feeModel = new Fee();
            $groups = $feeModel->getGroups();
            $targetGroupId = null;
            if(!empty($className)){
                foreach($groups as $g){
                    if(stripos($g->group_name, $className) !== false){
                        $targetGroupId = (int)$g->id;
                        break;
                    }
                }
            }
            if(!$targetGroupId && !empty($groups)){
                $targetGroupId = (int)$groups[0]->id;
            }
            if($targetGroupId){
                $feeModel->assignToStudent($targetGroupId, $newStudentId);
            }
        } catch (Throwable $e) {}

        // 10. Update Enquiry Record
        $this->db->query("UPDATE admission_enquiry 
                          SET status = 'Approved', 
                              converted_student_id = :sid, 
                              class_id = :cid 
                          WHERE id = :eid AND school_id = :sch");
        $this->db->bind(':sid', $newStudentId);
        $this->db->bind(':cid', $classId);
        $this->db->bind(':eid', $id);
        $this->db->bind(':sch', $schoolId);
        $this->db->execute();

        return [
            'success' => true,
            'student_id' => $newStudentId,
            'admission_no' => $admissionNo,
            'roll_no' => $rollNo,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'class_name' => $className,
            'student_name' => trim($enquiry->name),
            'message' => "Successfully approved and enrolled into Class {$className}!"
        ];
    }

    public function getEnquiryById($id){
        $this->db->query("SELECT ae.*, u.name as staff_name, c.class_name
                          FROM admission_enquiry ae
                          LEFT JOIN users u ON ae.assigned_to = u.id
                          LEFT JOIN classes c ON ae.class_id = c.id
                          WHERE ae.id = :id AND ae.school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->single();
    }

    public function updateEnquiryStatus($id, $status){
        $this->db->query("UPDATE admission_enquiry SET status = :status WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->execute();
    }

    public function bulkUpdateEnquiryStatus($ids, $status){
        if (empty($ids) || !is_array($ids)) return false;
        $cleanIds = array_filter(array_map('intval', $ids));
        if (empty($cleanIds)) return false;
        $inClause = implode(',', $cleanIds);
        $this->db->query("UPDATE admission_enquiry SET status = :status WHERE id IN ({$inClause}) AND school_id = :school_id");
        $this->db->bind(':status', $status);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->execute();
    }

    public function getEnquiriesByIds($ids){
        if (empty($ids)) return [];
        if (!is_array($ids)) {
            $ids = explode(',', (string)$ids);
        }
        $cleanIds = array_filter(array_map('intval', $ids));
        if (empty($cleanIds)) return [];
        $inClause = implode(',', $cleanIds);
        $this->db->query("SELECT ae.*, u.name as staff_name, c.class_name
                          FROM admission_enquiry ae
                          LEFT JOIN users u ON ae.assigned_to = u.id
                          LEFT JOIN classes c ON ae.class_id = c.id
                          WHERE ae.id IN ({$inClause}) AND ae.school_id = :school_id
                          ORDER BY FIELD(ae.id, {$inClause})");
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->resultSet() ?: [];
    }

    public function deleteEnquiry($id){
        $this->db->query("DELETE FROM admission_enquiry WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->execute();
    }

    // --- Visitor Book & Pass Generation ---
    public function getNextVisitorPassNo(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $year = date('Y');
        $this->db->query("SELECT COUNT(*) as total FROM visitor_book WHERE school_id = :sch AND YEAR(date) = :year");
        $this->db->bind(':sch', $schoolId);
        $this->db->bind(':year', $year);
        $res = $this->db->single();
        $next = ($res ? (int)$res->total : 0) + 1;
        
        $prefix = 'VIS-';
        if (class_exists('SiteSetting')) {
            $sm = new SiteSetting();
            $prefix = $sm->getPrefix('visitor', 'VIS-');
        }
        return $prefix . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function addVisitor($data){
        $passNo = !empty($data['pass_no']) ? $data['pass_no'] : $this->getNextVisitorPassNo();
        $this->db->query("INSERT INTO visitor_book (school_id, pass_no, purpose, name, contact, cnic_passport, id_proof, person_to_meet, vehicle_no, department, no_of_person, date, in_time, out_time, note, status)
                          VALUES (:school_id, :pass_no, :purp, :name, :cont, :cnic, :proof, :meet, :veh, :dept, :no, :date, :in, :out, :note, :status)");
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        $this->db->bind(':pass_no', $passNo);
        $this->db->bind(':purp', $data['purpose']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':cont', $data['contact']);
        $this->db->bind(':cnic', !empty($data['cnic_passport']) ? $data['cnic_passport'] : null);
        $this->db->bind(':proof', !empty($data['id_proof']) ? $data['id_proof'] : null);
        $this->db->bind(':meet', !empty($data['person_to_meet']) ? $data['person_to_meet'] : null);
        $this->db->bind(':veh', !empty($data['vehicle_no']) ? $data['vehicle_no'] : null);
        $this->db->bind(':dept', !empty($data['department']) ? $data['department'] : null);
        $this->db->bind(':no', !empty($data['no_of_person']) ? (int)$data['no_of_person'] : 1);
        $this->db->bind(':date', !empty($data['date']) ? $data['date'] : date('Y-m-d'));
        $this->db->bind(':in', !empty($data['in_time']) ? $data['in_time'] : date('H:i'));
        $this->db->bind(':out', !empty($data['out_time']) ? $data['out_time'] : null);
        $this->db->bind(':note', !empty($data['note']) ? $data['note'] : null);
        $this->db->bind(':status', 'Checked In');
        return $this->db->execute();
    }

    public function getVisitors($date = null){
        $sql = "SELECT * FROM visitor_book WHERE school_id = :school_id";
        if (!empty($date)) {
            $sql .= " AND date = :date";
        }
        $sql .= " ORDER BY date DESC, id DESC";
        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        if (!empty($date)) {
            $this->db->bind(':date', $date);
        }
        return $this->db->resultSet();
    }

    public function getVisitorById($id){
        $this->db->query("SELECT * FROM visitor_book WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->single();
    }

    public function checkoutVisitor($id, $outTime = null){
        $out = $outTime ?: date('H:i');
        $this->db->query("UPDATE visitor_book SET out_time = :out, status = 'Checked Out' WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':out', $out);
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->execute();
    }

    // --- Student Early Exit Gate Pass ---
    public function getNextGatePassNo(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $year = date('Y');
        $this->db->query("SELECT COUNT(*) as total FROM student_gate_passes WHERE school_id = :sch AND YEAR(pass_date) = :year");
        $this->db->bind(':sch', $schoolId);
        $this->db->bind(':year', $year);
        $res = $this->db->single();
        $next = ($res ? (int)$res->total : 0) + 1;
        
        $prefix = 'GP-';
        if (class_exists('SiteSetting')) {
            $sm = new SiteSetting();
            $prefix = $sm->getPrefix('gatepass', 'GP-');
        }
        return $prefix . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function addGatePass($data){
        $passNo = !empty($data['pass_no']) ? $data['pass_no'] : $this->getNextGatePassNo();
        $this->db->query("INSERT INTO student_gate_passes (school_id, pass_no, student_id, pass_date, leave_time, reason_type, reason_details, collected_by_name, collected_by_cnic, collected_by_relation, collected_by_phone, approved_by_user_id, status)
                          VALUES (:school_id, :pass_no, :student_id, :pass_date, :leave_time, :reason_type, :reason_details, :collected_by_name, :collected_by_cnic, :collected_by_relation, :collected_by_phone, :approved_by, 'Issued')");
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        $this->db->bind(':pass_no', $passNo);
        $this->db->bind(':student_id', $data['student_id']);
        $this->db->bind(':pass_date', !empty($data['pass_date']) ? $data['pass_date'] : date('Y-m-d'));
        $this->db->bind(':leave_time', !empty($data['leave_time']) ? $data['leave_time'] : date('h:i A'));
        $this->db->bind(':reason_type', $data['reason_type']);
        $this->db->bind(':reason_details', $data['reason_details'] ?? '');
        $this->db->bind(':collected_by_name', $data['collected_by_name']);
        $this->db->bind(':collected_by_cnic', $data['collected_by_cnic'] ?? '');
        $this->db->bind(':collected_by_relation', $data['collected_by_relation'] ?? 'Parent');
        $this->db->bind(':collected_by_phone', $data['collected_by_phone'] ?? '');
        $this->db->bind(':approved_by', !empty($data['approved_by_user_id']) ? $data['approved_by_user_id'] : ($_SESSION['user_id'] ?? null));
        return $this->db->execute();
    }

    public function getGatePasses($date = null){
        $sql = "SELECT gp.*, s.name as student_name, s.admission_no, s.roll_no, s.student_photo, s.father_name, s.bform_cnic,
                       c.class_name, sec.section_name, u.name as approver_name
                FROM student_gate_passes gp
                JOIN students s ON gp.student_id = s.id
                LEFT JOIN classes c ON s.class_id = c.id
                LEFT JOIN sections sec ON s.section_id = sec.id
                LEFT JOIN users u ON gp.approved_by_user_id = u.id
                WHERE gp.school_id = :school_id";
        if (!empty($date)) {
            $sql .= " AND gp.pass_date = :date";
        }
        $sql .= " ORDER BY gp.pass_date DESC, gp.id DESC";

        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        if (!empty($date)) {
            $this->db->bind(':date', $date);
        }
        return $this->db->resultSet();
    }

    public function getGatePassById($id){
        $this->db->query("SELECT gp.*, s.name as student_name, s.admission_no, s.roll_no, s.student_photo, s.father_name, s.bform_cnic, s.emergency_contact, s.phone as student_phone,
                                 c.class_name, sec.section_name, u.name as approver_name, u.role as approver_role
                          FROM student_gate_passes gp
                          JOIN students s ON gp.student_id = s.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          LEFT JOIN users u ON gp.approved_by_user_id = u.id
                          WHERE gp.id = :id AND gp.school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->single();
    }

    public function markGateDeparted($id){
        $this->db->query("UPDATE student_gate_passes SET status = 'Departed' WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->execute();
    }

    // --- Postal Records (Inward & Outward Dispatches) ---
    public function addPostalRecord($data){
        $this->db->query("INSERT INTO postal_records (school_id, dispatch_type, reference_no, sender_title, receiver_title, record_date, courier_name, tracking_id, category, note)
                          VALUES (:school_id, :type, :ref, :sender, :receiver, :date, :courier, :tracking, :cat, :note)");
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        $this->db->bind(':type', $data['dispatch_type']);
        $this->db->bind(':ref', $data['reference_no'] ?? '');
        $this->db->bind(':sender', $data['sender_title']);
        $this->db->bind(':receiver', $data['receiver_title']);
        $this->db->bind(':date', !empty($data['record_date']) ? $data['record_date'] : date('Y-m-d'));
        $this->db->bind(':courier', $data['courier_name'] ?? '');
        $this->db->bind(':tracking', $data['tracking_id'] ?? '');
        $this->db->bind(':cat', $data['category'] ?? 'General');
        $this->db->bind(':note', $data['note'] ?? '');
        return $this->db->execute();
    }

    public function getPostalRecords($type = null){
        $sql = "SELECT * FROM postal_records WHERE school_id = :school_id";
        if (!empty($type)) {
            $sql .= " AND dispatch_type = :type";
        }
        $sql .= " ORDER BY record_date DESC, id DESC";
        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        if (!empty($type)) {
            $this->db->bind(':type', $type);
        }
        return $this->db->resultSet();
    }

    public function deletePostalRecord($id){
        $this->db->query("DELETE FROM postal_records WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->execute();
    }

    // --- Phone Call Logs ---
    public function addCallLog($data){
        $this->db->query("INSERT INTO phone_call_logs (school_id, call_type, caller_name, phone, call_date, call_time, duration, purpose, follow_up_date, note)
                          VALUES (:school_id, :type, :name, :phone, :cdate, :ctime, :duration, :purpose, :follow, :note)");
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        $this->db->bind(':type', $data['call_type'] ?? 'Incoming');
        $this->db->bind(':name', $data['caller_name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':cdate', !empty($data['call_date']) ? $data['call_date'] : date('Y-m-d'));
        $this->db->bind(':ctime', !empty($data['call_time']) ? $data['call_time'] : date('h:i A'));
        $this->db->bind(':duration', $data['duration'] ?? '2 mins');
        $this->db->bind(':purpose', $data['purpose'] ?? 'General Inquiry');
        $this->db->bind(':follow', !empty($data['follow_up_date']) ? $data['follow_up_date'] : null);
        $this->db->bind(':note', $data['note'] ?? '');
        return $this->db->execute();
    }

    public function getCallLogs(){
        $this->db->query("SELECT * FROM phone_call_logs WHERE school_id = :school_id ORDER BY call_date DESC, id DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->resultSet();
    }

    public function deleteCallLog($id){
        $this->db->query("DELETE FROM phone_call_logs WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->execute();
    }
}

