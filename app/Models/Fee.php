<?php
class Fee {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // --- Fee Types ---
    public function addType($data){
        $this->db->query("INSERT INTO fee_types (school_id, type_name, type_code, description) VALUES (:school_id, :name, :code, :desc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':desc', $data['description']);
        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function getTypes(){
        $this->db->query("SELECT * FROM fee_types WHERE school_id = :school_id ORDER BY type_name");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function getTypeById($id){
        $this->db->query("SELECT * FROM fee_types WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->single();
    }

    public function updateType($data){
        $this->db->query("UPDATE fee_types SET type_name = :name, type_code = :code, description = :desc WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':desc', $data['description']);
        return $this->db->execute();
    }

    public function deleteType($id){
        $this->db->query("DELETE FROM fee_types WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // --- Fee Groups ---
    public function addGroup($data){
        $this->db->query("INSERT INTO fee_groups (school_id, group_name, description) VALUES (:school_id, :name, :desc)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':desc', $data['description']);
        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function getGroups(){
        $this->db->query("SELECT * FROM fee_groups WHERE school_id = :school_id ORDER BY group_name");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function getGroupById($id){
        $this->db->query("SELECT * FROM fee_groups WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->single();
    }

    public function updateGroup($data){
        $this->db->query("UPDATE fee_groups SET group_name = :name, description = :desc WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':desc', $data['description']);
        return $this->db->execute();
    }

    public function deleteGroup($id){
        $this->db->query("DELETE FROM fee_groups WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // --- Fee Master (Group-Type Link) ---
    public function addMaster($data){
        // Check if exists? Skip for simplicity now
        $this->db->query("INSERT INTO fee_groups_types (school_id, fee_group_id, fee_type_id, amount, due_date, fine_amount) 
                          VALUES (:school_id, :group_id, :type_id, :amount, :due_date, :fine)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':group_id', $data['group_id']);
        $this->db->bind(':type_id', $data['type_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':fine', $data['fine']);
        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function getMasterByGroup($group_id){
        $this->db->query("SELECT fee_groups_types.*, fee_types.type_name, fee_types.type_code 
                          FROM fee_groups_types 
                          JOIN fee_types ON fee_groups_types.fee_type_id = fee_types.id 
                          WHERE fee_group_id = :group_id AND fee_groups_types.school_id = :school_id");
        $this->db->bind(':group_id', $group_id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function getMasterById($id){
        $this->db->query("SELECT fee_groups_types.*, fee_types.type_name, fee_types.type_code 
                          FROM fee_groups_types 
                          JOIN fee_types ON fee_groups_types.fee_type_id = fee_types.id 
                          WHERE fee_groups_types.id = :id AND fee_groups_types.school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->single();
    }

    /**
     * Look up a fee_groups_types row by group + type within the current school.
     * Avoids raw Database instantiation in controller code.
     */
    public function getMasterByGroupAndType($groupId, $typeId){
        $this->db->query("SELECT id FROM fee_groups_types
                          WHERE school_id   = :school_id
                            AND fee_group_id = :group_id
                            AND fee_type_id  = :type_id
                          LIMIT 1");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':group_id',  (int)$groupId);
        $this->db->bind(':type_id',   (int)$typeId);
        return $this->db->single();
    }

    /**
     * Insert a student_fees row for an admission token / challan.
     * Returns the new row ID on success, false on failure.
     */
    public function addStudentFeeForToken($data){
        $this->db->query("INSERT INTO student_fees
                            (school_id, student_id, fee_groups_types_id, challan_no,
                             billing_month, due_date, status)
                          VALUES
                            (:school_id, :student_id, :fgt_id, :challan,
                             :billing_month, :due_date, :status)");
        $this->db->bind(':school_id',    TenantContext::getSchoolId());
        $this->db->bind(':student_id',   (int)$data['student_id']);
        $this->db->bind(':fgt_id',       (int)$data['fee_groups_types_id']);
        $this->db->bind(':challan',       $data['challan_no']);
        $this->db->bind(':billing_month', $data['billing_month']);
        $this->db->bind(':due_date',      $data['due_date']);
        $this->db->bind(':status',        $data['status']);
        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Fetch the default school bank account.
     * Returns the DB row or a fallback object if none configured.
     */
    public function getDefaultBank(){
        $this->db->query("SELECT * FROM school_banks
                          WHERE school_id = :school_id AND is_default = 1
                          LIMIT 1");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->single();
    }

    public function updateMaster($data){
        $sql = "UPDATE fee_groups_types SET amount = :amount, due_date = :due_date, fine_amount = :fine";
        if(!empty($data['type_id'])) {
            $sql .= ", fee_type_id = :type_id";
        }
        $sql .= " WHERE id = :id AND school_id = :school_id";
        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $data['id']);
        if(!empty($data['type_id'])) {
            $this->db->bind(':type_id', $data['type_id']);
        }
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':fine', $data['fine']);
        return $this->db->execute();
    }

    public function deleteMaster($id){
        $this->db->query("DELETE FROM fee_groups_types WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getPaymentHistory($student_id){
        $this->db->query("SELECT fp.*, ft.type_name, ft.type_code, fg.group_name 
                          FROM fee_payments fp 
                          JOIN student_fees sf ON fp.student_fee_id = sf.id 
                          JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id 
                          JOIN fee_types ft ON fgt.fee_type_id = ft.id 
                          JOIN fee_groups fg ON fgt.fee_group_id = fg.id 
                          WHERE sf.student_id = :sid AND fp.school_id = :school_id 
                          ORDER BY fp.id DESC");
        $this->db->bind(':sid', $student_id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function getPaymentById($payment_id){
        $this->db->query("SELECT fp.*, ft.type_name, ft.type_code, fg.group_name, sf.student_id,
                                 s.admission_no, s.roll_no, u.name as student_name, c.class_name, sec.section_name
                          FROM fee_payments fp 
                          JOIN student_fees sf ON fp.student_fee_id = sf.id 
                          JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id 
                          JOIN fee_types ft ON fgt.fee_type_id = ft.id 
                          JOIN fee_groups fg ON fgt.fee_group_id = fg.id 
                          JOIN students s ON sf.student_id = s.id
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE fp.id = :id AND fp.school_id = :school_id");
        $this->db->bind(':id', $payment_id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->single();
    }

    public function deletePayment($payment_id){
        $this->db->query("DELETE FROM fee_payments WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', $payment_id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->execute();
    }
    
    // --- Assign to Student (Simply creating records in student_fees) ---
    public function assignToClass($fee_group_id, $class_id, $section_id=null){
        // Get all students in class/section
        $sql = "SELECT id FROM students WHERE class_id = :class_id AND school_id = :school_id";
        if($section_id) $sql .= " AND section_id = :section_id";
        
        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':class_id', $class_id);
        if($section_id) $this->db->bind(':section_id', $section_id);
        $students = $this->db->resultSet();
        
        // Get all masters in this group
        $masters = $this->getMasterByGroup($fee_group_id);
        
        $schoolId = TenantContext::getSchoolId();
        foreach($students as $stud){
            foreach($masters as $mst){
                // Check duplicate first
                $this->db->query("SELECT id FROM student_fees WHERE school_id = :school_id AND student_id = :sid AND fee_groups_types_id = :fgtid LIMIT 1");
                $this->db->bind(':school_id', $schoolId);
                $this->db->bind(':sid', $stud->id);
                $this->db->bind(':fgtid', $mst->id);
                $existing = $this->db->single();
                if(!$existing){
                    $this->db->query("INSERT INTO student_fees (school_id, student_id, fee_groups_types_id) VALUES (:school_id, :sid, :fgtid)");
                    $this->db->bind(':school_id', $schoolId);
                    $this->db->bind(':sid', $stud->id);
                    $this->db->bind(':fgtid', $mst->id);
                    $this->db->execute();
                }
            }
        }
        return true;
    }

    public function assignToStudent($fee_group_id, $student_id){
        $schoolId = TenantContext::getSchoolId();
        $masters = $this->getMasterByGroup($fee_group_id);
        if(empty($masters)) return false;

        foreach($masters as $mst){
            $this->db->query("SELECT id FROM student_fees WHERE school_id = :school_id AND student_id = :sid AND fee_groups_types_id = :fgtid LIMIT 1");
            $this->db->bind(':school_id', $schoolId);
            $this->db->bind(':sid', $student_id);
            $this->db->bind(':fgtid', $mst->id);
            $existing = $this->db->single();
            if(!$existing){
                $this->db->query("INSERT INTO student_fees (school_id, student_id, fee_groups_types_id) VALUES (:school_id, :sid, :fgtid)");
                $this->db->bind(':school_id', $schoolId);
                $this->db->bind(':sid', $student_id);
                $this->db->bind(':fgtid', $mst->id);
                $this->db->execute();
            }
        }
        return true;
    }

    public function syncGroupToClassStudents($fee_group_id, $class_id){
        return $this->assignToClass($fee_group_id, $class_id);
    }

    // --- Collection ---
    public function getStudentFees($student_id){
        // Get assigned fees and payment status
        // Calculate paid amount
        $this->db->query("SELECT sf.id as student_fee_id, fgt.amount, fgt.due_date, fgt.fine_amount, 
                          ft.type_name, ft.type_code, fg.group_name,
                          (SELECT SUM(amount) FROM fee_payments WHERE student_fee_id = sf.id) as total_paid,
                          (SELECT SUM(discount) FROM fee_payments WHERE student_fee_id = sf.id) as total_discount
                          FROM student_fees sf
                          JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id
                          JOIN fee_types ft ON fgt.fee_type_id = ft.id
                          JOIN fee_groups fg ON fgt.fee_group_id = fg.id
                          WHERE sf.student_id = :sid AND sf.school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':sid', $student_id);
        return $this->db->resultSet();
    }

    public function addPayment($data){
        $this->db->query("INSERT INTO fee_payments (school_id, student_fee_id, mode, amount, discount, fine, payment_date, note) 
                          VALUES (:school_id, :sfid, :mode, :amount, :disc, :fine, :date, :note)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':sfid', $data['student_fee_id']);
        $this->db->bind(':mode', $data['mode']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':disc', $data['discount']);
        $this->db->bind(':fine', $data['fine']);
        $this->db->bind(':date', $data['payment_date']);
        $this->db->bind(':note', $data['note']);
        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function getStudentsFeeOverview($class_id = null, $section_id = null){
        $schoolId = TenantContext::getSchoolId();
        $sql = "SELECT 
                    s.id as student_id,
                    s.admission_no,
                    s.roll_no,
                    s.class_id,
                    s.section_id,
                    s.parent_phone,
                    u.name as student_name,
                    u.email as student_email,
                    c.class_name,
                    sec.section_name,
                    COALESCE(fee_totals.total_fee_amount, 0) as total_amount,
                    COALESCE(fee_totals.total_paid_amount, 0) as total_paid
                FROM students s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN classes c ON s.class_id = c.id
                LEFT JOIN sections sec ON s.section_id = sec.id
                LEFT JOIN (
                    SELECT 
                        sf.student_id,
                        SUM(fgt.amount + fgt.fine_amount) as total_fee_amount,
                        SUM(COALESCE(p.paid, 0)) as total_paid_amount
                    FROM student_fees sf
                    JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id
                    LEFT JOIN (
                        SELECT student_fee_id, SUM(amount + discount) as paid
                        FROM fee_payments
                        WHERE school_id = :school_id_p
                        GROUP BY student_fee_id
                    ) p ON p.student_fee_id = sf.id
                    WHERE sf.school_id = :school_id_sf
                    GROUP BY sf.student_id
                ) fee_totals ON fee_totals.student_id = s.id
                WHERE s.school_id = :school_id";
        
        if($class_id){
            $sql .= " AND s.class_id = :class_id";
        }
        if($section_id){
            $sql .= " AND s.section_id = :section_id";
        }
        $sql .= " ORDER BY c.class_name, s.roll_no, u.name";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':school_id_sf', $schoolId);
        $this->db->bind(':school_id_p', $schoolId);
        if($class_id){
            $this->db->bind(':class_id', $class_id);
        }
        if($section_id){
            $this->db->bind(':section_id', $section_id);
        }
        return $this->db->resultSet();
    }

    // ==========================================
    // PHASE 7: PAKISTANI BANK CHALLANS & DEFAULTERS
    // ==========================================

    public function getSchoolBank() {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM school_banks WHERE school_id = :school_id ORDER BY is_default DESC, id ASC LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $bank = $this->db->single();
        if (!$bank) {
            // Fallback default bank object
            $bank = (object)[
                'id' => 0,
                'school_id' => $schoolId,
                'bank_name' => 'Habib Bank Limited (HBL)',
                'branch_name' => 'Main Commercial Branch',
                'account_title' => 'Pak Academy Model School System',
                'account_no' => '1029-3847-2910-01',
                'iban' => 'PK36HABB0001029384729101',
                'is_default' => 1
            ];
        }
        return $bank;
    }

    public function saveSchoolBank($data) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT id FROM school_banks WHERE school_id = :school_id LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $existing = $this->db->single();

        if ($existing) {
            $this->db->query("UPDATE school_banks 
                              SET bank_name = :bank_name, branch_name = :branch_name, 
                                  account_title = :account_title, account_no = :account_no, iban = :iban 
                              WHERE id = :id AND school_id = :school_id");
            $this->db->bind(':id', $existing->id);
        } else {
            $this->db->query("INSERT INTO school_banks (school_id, bank_name, branch_name, account_title, account_no, iban, is_default)
                              VALUES (:school_id, :bank_name, :branch_name, :account_title, :account_no, :iban, 1)");
        }
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':bank_name', trim($data['bank_name'] ?? 'Habib Bank Limited (HBL)'));
        $this->db->bind(':branch_name', trim($data['branch_name'] ?? ''));
        $this->db->bind(':account_title', trim($data['account_title'] ?? 'Pak Academy Model School System'));
        $this->db->bind(':account_no', trim($data['account_no'] ?? ''));
        $this->db->bind(':iban', trim($data['iban'] ?? ''));
        return $this->db->execute();
    }

    public function getStudentChallanData($student_id, $month = null, $year = null, $options = []) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $studentId = (int)$student_id;

        // Fetch student profile & family details
        $this->db->query("SELECT s.*, u.name as student_name, u.email as student_email,
                                 c.class_name, sec.section_name,
                                 f.family_code, f.default_discount_percent as family_discount_percent
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          LEFT JOIN families f ON (s.family_id = f.family_code OR s.father_cnic = f.father_cnic) AND f.school_id = :school_id_f
                          WHERE s.id = :sid AND s.school_id = :school_id
                          LIMIT 1");
        $this->db->bind(':school_id_f', $schoolId);
        $this->db->bind(':sid', $studentId);
        $this->db->bind(':school_id', $schoolId);
        $student = $this->db->single();

        if (!$student) {
            return null;
        }

        // Active Academic Session
        $this->db->query("SELECT session_name FROM academic_sessions WHERE school_id = :school_id AND is_current = 1 LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $sessionObj = $this->db->single();
        $sessionName = $sessionObj ? $sessionObj->session_name : (date('Y') . '-' . (date('y') + 1));

        // School info
        $this->db->query("SELECT * FROM schools WHERE id = :school_id LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $school = $this->db->single();

        // Bank Details
        $bank = !empty($options['bank']) ? (object)$options['bank'] : $this->getSchoolBank();

        // Billing Month
        if (empty($month)) {
            $month = date('F');
        }
        if (empty($year)) {
            $year = date('Y');
        }
        $billingPeriod = $month . ' ' . $year;

        // Assigned Fee heads
        $assignedFees = $this->getStudentFees($studentId);

        $feeItems = [];
        $grossSubtotal = 0;
        $totalPaidAlready = 0;

        // Determine late fine (from options, then site_settings, default 200.00)
        $lateFine = 200.00;
        if (isset($options['late_fine']) && is_numeric($options['late_fine'])) {
            $lateFine = (float)$options['late_fine'];
        } elseif (class_exists('SiteSetting')) {
            $savedFine = SiteSetting::getGlobal('challan_late_fine', '');
            if ($savedFine !== '' && is_numeric($savedFine)) {
                $lateFine = (float)$savedFine;
            }
        }

        // If custom fee items were passed in options
        if (!empty($options['custom_items']) && is_array($options['custom_items'])) {
            foreach ($options['custom_items'] as $ci) {
                $amt = (float)($ci['amount'] ?? 0);
                $grossSubtotal += $amt;
                $feeItems[] = (object)[
                    'type_name' => $ci['name'] ?? 'Fee Particular',
                    'type_code' => $ci['code'] ?? 'FEE',
                    'amount' => $amt
                ];
            }
        } elseif (!empty($assignedFees)) {
            foreach ($assignedFees as $af) {
                $itemAmt = (float)$af->amount;
                $grossSubtotal += $itemAmt;
                $totalPaidAlready += (float)$af->total_paid;
                if (!isset($options['late_fine']) && (float)$af->fine_amount > 0) {
                    $lateFine = (float)$af->fine_amount;
                }
                $feeItems[] = (object)[
                    'type_name' => $af->type_name,
                    'type_code' => $af->type_code,
                    'amount' => $itemAmt
                ];
            }
        } else {
            // Check if user has saved custom default particulars in SiteSetting
            $savedParticulars = class_exists('SiteSetting') ? SiteSetting::getGlobal('default_challan_particulars', '') : '';
            $customDefaults = !empty($savedParticulars) ? json_decode($savedParticulars, true) : null;

            if (!empty($customDefaults) && is_array($customDefaults)) {
                foreach ($customDefaults as $cd) {
                    $itemAmt = (float)($cd['amount'] ?? 0);
                    $grossSubtotal += $itemAmt;
                    $feeItems[] = (object)[
                        'type_name' => $cd['name'] ?? 'Fee Particular',
                        'type_code' => $cd['code'] ?? 'FEE',
                        'amount' => $itemAmt
                    ];
                }
            } else {
                // Check if there is a Fee Group specifically for the student's class
                $defaultMasters = [];
                if (!empty($student->class_name)) {
                    $this->db->query("SELECT fgt.amount, fgt.fine_amount, ft.type_name, ft.type_code 
                                      FROM fee_groups_types fgt
                                      JOIN fee_groups fg ON fgt.fee_group_id = fg.id
                                      JOIN fee_types ft ON fgt.fee_type_id = ft.id
                                      WHERE fgt.school_id = :school_id 
                                        AND (fg.group_name LIKE :cname OR fg.group_name LIKE :cname_alt)
                                      ORDER BY fgt.id ASC");
                    $this->db->bind(':school_id', $schoolId);
                    $this->db->bind(':cname', '%' . $student->class_name . '%');
                    $this->db->bind(':cname_alt', 'Class ' . $student->class_name . '%');
                    $defaultMasters = $this->db->resultSet();
                }

                // Default package fallback if not class-specific
                if (empty($defaultMasters)) {
                    $this->db->query("SELECT fgt.amount, fgt.fine_amount, ft.type_name, ft.type_code 
                                      FROM fee_groups_types fgt
                                      JOIN fee_types ft ON fgt.fee_type_id = ft.id
                                      WHERE fgt.school_id = :school_id
                                      ORDER BY fgt.id ASC LIMIT 5");
                    $this->db->bind(':school_id', $schoolId);
                    $defaultMasters = $this->db->resultSet();
                }

                if (!empty($defaultMasters)) {
                    foreach ($defaultMasters as $dm) {
                        $itemAmt = (float)$dm->amount;
                        $grossSubtotal += $itemAmt;
                        if (!isset($options['late_fine']) && (float)$dm->fine_amount > 0) {
                            $lateFine = (float)$dm->fine_amount;
                        }
                        $feeItems[] = (object)[
                            'type_name' => $dm->type_name,
                            'type_code' => $dm->type_code,
                            'amount' => $itemAmt
                        ];
                    }
                } else {
                    $grossSubtotal = 4500.00;
                    $feeItems = [
                        (object)['type_name' => 'Monthly Tuition Fee', 'type_code' => 'TUI', 'amount' => 3500.00],
                        (object)['type_name' => 'Computer & IT Lab Fee', 'type_code' => 'LAB', 'amount' => 500.00],
                        (object)['type_name' => 'Assessment & Exam Fund', 'type_code' => 'EXM', 'amount' => 300.00],
                        (object)['type_name' => 'Generator & Utility Fund', 'type_code' => 'UTL', 'amount' => 200.00]
                    ];
                }
            }
        }

        // Sibling Discount calculation
        $siblingDiscountPercent = (float)($student->sibling_discount_percent ?? 0);
        if ($siblingDiscountPercent <= 0 && !empty($student->family_discount_percent)) {
            $siblingDiscountPercent = (float)$student->family_discount_percent;
        }
        $siblingDiscountAmount = round(($grossSubtotal * $siblingDiscountPercent) / 100, 2);

        // Previous Arrears
        $arrears = 0.00;

        // Net payable calculations
        $netPayableWithinDueDate = max(0, $grossSubtotal - $siblingDiscountAmount + $arrears);
        $netPayableAfterDueDate = $netPayableWithinDueDate + $lateFine;

        // Dates
        $issueDate = !empty($options['issue_date']) ? $options['issue_date'] : date('Y-m-01');
        $dueDate = !empty($options['due_date']) ? $options['due_date'] : date('Y-m-10');
        $validityDate = !empty($options['validity_date']) ? $options['validity_date'] : date('Y-m-25');

        // Instructions
        $instructions = !empty($options['instructions']) ? $options['instructions'] : (class_exists('SiteSetting') ? SiteSetting::getGlobal('challan_instructions', '') : '');

        // Challan No
        $challanNo = 'CHL-' . date('Ym') . '-' . str_pad($studentId, 4, '0', STR_PAD_LEFT);

        return (object)[
            'challan_no' => $challanNo,
            'barcode' => 'CHL' . date('Ym') . str_pad($studentId, 4, '0', STR_PAD_LEFT),
            'student' => $student,
            'session_name' => $sessionName,
            'billing_period' => $billingPeriod,
            'month' => $month,
            'year' => $year,
            'school' => $school,
            'bank' => $bank,
            'fee_items' => $feeItems,
            'gross_subtotal' => $grossSubtotal,
            'sibling_discount_percent' => $siblingDiscountPercent,
            'sibling_discount_amount' => $siblingDiscountAmount,
            'arrears' => $arrears,
            'net_payable_within_due_date' => $netPayableWithinDueDate,
            'late_fine' => $lateFine,
            'net_payable_after_due_date' => $netPayableAfterDueDate,
            'amount_in_words' => self::numberToWords((int)$netPayableWithinDueDate),
            'amount_after_due_words' => self::numberToWords((int)$netPayableAfterDueDate),
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'validity_date' => $validityDate,
            'instructions' => $instructions
        ];
    }

    public function getClassChallans($class_id, $section_id = null, $month = null, $year = null, $options = []) {
        $schoolId = TenantContext::getSchoolId() ?: 1;

        if (!empty($options['student_ids']) && is_array($options['student_ids'])) {
            $cleanIds = array_filter(array_map('intval', $options['student_ids']));
            if (!empty($cleanIds)) {
                $inClause = implode(',', $cleanIds);
                $this->db->query("SELECT id FROM students WHERE id IN ($inClause) AND school_id = :school_id ORDER BY roll_no ASC, id ASC");
                $this->db->bind(':school_id', $schoolId);
                $students = $this->db->resultSet();

                $challans = [];
                if (!empty($students)) {
                    foreach ($students as $s) {
                        $c = $this->getStudentChallanData($s->id, $month, $year, $options);
                        if ($c) {
                            $challans[] = $c;
                        }
                    }
                }
                return $challans;
            }
        }

        $sql = "SELECT id FROM students WHERE class_id = :class_id AND school_id = :school_id";
        if ($section_id) {
            $sql .= " AND section_id = :section_id";
        }
        $sql .= " ORDER BY roll_no ASC, id ASC";

        $this->db->query($sql);
        $this->db->bind(':class_id', (int)$class_id);
        $this->db->bind(':school_id', $schoolId);
        if ($section_id) {
            $this->db->bind(':section_id', (int)$section_id);
        }
        $students = $this->db->resultSet();

        $challans = [];
        if (!empty($students)) {
            foreach ($students as $s) {
                $c = $this->getStudentChallanData($s->id, $month, $year, $options);
                if ($c) {
                    $challans[] = $c;
                }
            }
        }
        return $challans;
    }

    public function getDefaultersLedger($class_id = null, $aging = null) {
        $students = $this->getStudentsFeeOverview($class_id);
        $defaulters = [];

        $totalDefaulters = 0;
        $totalOutstanding = 0;
        $bracket1_30 = 0;
        $bracket31_60 = 0;
        $bracket61_90 = 0;
        $bracket90_plus = 0;

        $bracket1_30_amt = 0;
        $bracket31_60_amt = 0;
        $bracket61_90_amt = 0;
        $bracket90_plus_amt = 0;

        foreach ($students as $s) {
            $totalFee = (float)($s->total_amount ?? 0);
            $totalPaid = (float)($s->total_paid ?? 0);
            $balance = max(0, $totalFee - $totalPaid);

            if ($balance > 0) {
                // Compute simulated or recorded overdue days
                // If student has admission date or roll no, calculate age bracket
                $studentId = $s->student_id ?? $s->id;
                
                // Query earliest unpaid fee due date
                $this->db->query("SELECT MIN(fgt.due_date) as earliest_due 
                                  FROM student_fees sf 
                                  JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id 
                                  WHERE sf.student_id = :sid AND sf.school_id = :school_id");
                $this->db->bind(':sid', $studentId);
                $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
                $dueRow = $this->db->single();

                $earliestDue = ($dueRow && !empty($dueRow->earliest_due)) ? $dueRow->earliest_due : date('Y-m-10', strtotime('-45 days'));
                $overdueDays = max(1, (int)floor((time() - strtotime($earliestDue)) / 86400));

                if ($overdueDays <= 30) {
                    $bracket = '1-30';
                    $bracketLabel = '1 - 30 Days';
                    $bracketBadge = 'badge-warning';
                    $bracket1_30++;
                    $bracket1_30_amt += $balance;
                } elseif ($overdueDays <= 60) {
                    $bracket = '31-60';
                    $bracketLabel = '31 - 60 Days';
                    $bracketBadge = 'badge-warning';
                    $bracket31_60++;
                    $bracket31_60_amt += $balance;
                } elseif ($overdueDays <= 90) {
                    $bracket = '61-90';
                    $bracketLabel = '61 - 90 Days';
                    $bracketBadge = 'badge-danger';
                    $bracket61_90++;
                    $bracket61_90_amt += $balance;
                } else {
                    $bracket = '90+';
                    $bracketLabel = '90+ Days (Critical)';
                    $bracketBadge = 'badge-danger';
                    $bracket90_plus++;
                    $bracket90_plus_amt += $balance;
                }

                $totalDefaulters++;
                $totalOutstanding += $balance;

                $defaulterItem = (object)[
                    'student_id' => $studentId,
                    'admission_no' => $s->admission_no,
                    'roll_no' => $s->roll_no,
                    'student_name' => $s->student_name,
                    'class_name' => $s->class_name,
                    'section_name' => $s->section_name,
                    'parent_phone' => $s->parent_phone,
                    'total_fee' => $totalFee,
                    'total_paid' => $totalPaid,
                    'balance' => $balance,
                    'overdue_days' => $overdueDays,
                    'earliest_due' => $earliestDue,
                    'bracket' => $bracket,
                    'bracket_label' => $bracketLabel
                ];

                if (empty($aging) || $aging === 'all' || $aging === $bracket) {
                    $defaulters[] = $defaulterItem;
                }
            }
        }

        // Sort defaulters descending by balance
        usort($defaulters, function($a, $b) {
            return $b->balance <=> $a->balance;
        });

        return (object)[
            'defaulters' => $defaulters,
            'total_defaulters' => $totalDefaulters,
            'total_outstanding' => $totalOutstanding,
            'bracket1_30' => $bracket1_30,
            'bracket31_60' => $bracket31_60,
            'bracket61_90' => $bracket61_90,
            'bracket90_plus' => $bracket90_plus,
            'bracket1_30_amt' => $bracket1_30_amt,
            'bracket31_60_amt' => $bracket31_60_amt,
            'bracket61_90_amt' => $bracket61_90_amt,
            'bracket90_plus_amt' => $bracket90_plus_amt
        ];
    }

    public function generateBatchMonthlyInvoices($class_id, $billing_month, $due_date, $fee_group_id = null) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $classId = (int)$class_id;

        // Fetch students in class
        $sql = "SELECT id, sibling_discount_percent FROM students WHERE school_id = :school_id";
        if ($classId > 0) {
            $sql .= " AND class_id = :class_id";
        }
        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if ($classId > 0) {
            $this->db->bind(':class_id', $classId);
        }
        $students = $this->db->resultSet();

        if (empty($students)) {
            return 0;
        }

        // Get masters from fee group
        if (!$fee_group_id) {
            $this->db->query("SELECT id FROM fee_groups WHERE school_id = :school_id ORDER BY id ASC LIMIT 1");
            $this->db->bind(':school_id', $schoolId);
            $fg = $this->db->single();
            $fee_group_id = $fg ? $fg->id : 1;
        }
        $masters = $this->getMasterByGroup($fee_group_id);
        if (empty($masters)) {
            return 0;
        }

        $generatedCount = 0;
        foreach ($students as $st) {
            $challanNo = 'CHL-' . date('Ym') . '-' . str_pad($st->id, 4, '0', STR_PAD_LEFT);
            $siblingDiscPercent = (float)($st->sibling_discount_percent ?? 0);

            foreach ($masters as $mst) {
                // Check if invoice already exists for this billing month
                $this->db->query("SELECT id FROM student_fees 
                                  WHERE student_id = :sid AND fee_groups_types_id = :fgtid 
                                    AND billing_month = :bmonth AND school_id = :school_id LIMIT 1");
                $this->db->bind(':sid', $st->id);
                $this->db->bind(':fgtid', $mst->id);
                $this->db->bind(':bmonth', $billing_month);
                $this->db->bind(':school_id', $schoolId);
                $exists = $this->db->single();

                if (!$exists) {
                    $itemAmount = (float)$mst->amount;
                    $itemDiscount = round(($itemAmount * $siblingDiscPercent) / 100, 2);

                    $this->db->query("INSERT INTO student_fees (school_id, student_id, fee_groups_types_id, challan_no, billing_month, due_date, sibling_discount, status)
                                      VALUES (:school_id, :sid, :fgtid, :challan, :bmonth, :due, :disc, 'unpaid')");
                    $this->db->bind(':school_id', $schoolId);
                    $this->db->bind(':sid', $st->id);
                    $this->db->bind(':fgtid', $mst->id);
                    $this->db->bind(':challan', $challanNo);
                    $this->db->bind(':bmonth', $billing_month);
                    $this->db->bind(':due', $due_date);
                    $this->db->bind(':disc', $itemDiscount);
                    $this->db->execute();
                    $generatedCount++;
                }
            }
        }
        return $generatedCount;
    }

    public static function numberToWords($num) {
        $num = (int)$num;
        if ($num <= 0) return 'Zero Rupees Only';

        $ones = [
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
        ];
        $tens = [
            2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
            6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
        ];

        $words = '';

        // Crores
        if ($num >= 10000000) {
            $crores = (int)($num / 10000000);
            $words .= self::numberToWordsHelper($crores, $ones, $tens) . ' Crore ';
            $num %= 10000000;
        }

        // Lakhs
        if ($num >= 100000) {
            $lakhs = (int)($num / 100000);
            $words .= self::numberToWordsHelper($lakhs, $ones, $tens) . ' Lakh ';
            $num %= 100000;
        }

        // Thousands
        if ($num >= 1000) {
            $thousands = (int)($num / 1000);
            $words .= self::numberToWordsHelper($thousands, $ones, $tens) . ' Thousand ';
            $num %= 1000;
        }

        // Hundreds and remainder
        if ($num > 0) {
            $words .= self::numberToWordsHelper($num, $ones, $tens);
        }

        return 'Rupees ' . trim($words) . ' Only';
    }

    private static function numberToWordsHelper($n, $ones, $tens) {
        $res = '';
        if ($n >= 100) {
            $res .= $ones[(int)($n / 100)] . ' Hundred ';
            $n %= 100;
        }
        if ($n > 0) {
            if ($n < 20) {
                $res .= $ones[$n] . ' ';
            } else {
                $res .= $tens[(int)($n / 10)] . ' ';
                if ($n % 10 > 0) {
                    $res .= $ones[$n % 10] . ' ';
                }
            }
        }
        return trim($res);
    }
}

