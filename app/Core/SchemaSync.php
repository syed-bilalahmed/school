<?php
// app/Core/SchemaSync.php

class SchemaSync {
    private static $synced = false;

    public static function run() {
        if (self::$synced) {
            return;
        }

        // Performance Lock: Prevent running 80+ CREATE/SHOW queries on EVERY single request
        $lockFile = defined('APPROOT') ? (APPROOT . '/schema_synced.lock') : (__DIR__ . '/../schema_synced.lock');
        if (file_exists($lockFile) || !empty($_SESSION['schema_synced'])) {
            self::$synced = true;
            return;
        }

        try {
            $db = new Database();
            
            // 1. academic_sessions table
            $db->query("CREATE TABLE IF NOT EXISTS academic_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                session_name VARCHAR(50) NOT NULL,
                start_date DATE NULL,
                end_date DATE NULL,
                is_current TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // Check if default session exists for active school
            $schoolId = TenantContext::getSchoolId() ?: 1;
            $db->query("SELECT id FROM academic_sessions WHERE school_id = :school_id LIMIT 1");
            $db->bind(':school_id', $schoolId);
            $session = $db->single();
            if (!$session) {
                $db->query("INSERT INTO academic_sessions (school_id, session_name, start_date, end_date, is_current) 
                            VALUES (:school_id, '2026-27', '2026-04-01', '2027-03-31', 1)");
                $db->bind(':school_id', $schoolId);
                $db->execute();
            }

            // 2. Enhance subjects table
            self::addColumnIfNotExists('subjects', 'is_core', 'TINYINT(1) DEFAULT 1');
            self::addColumnIfNotExists('subjects', 'full_marks', 'DECIMAL(5,2) DEFAULT 100.00');
            self::addColumnIfNotExists('subjects', 'passing_marks', 'DECIMAL(5,2) DEFAULT 33.00');
            self::addColumnIfNotExists('subjects', 'credit_hours', 'INT DEFAULT 3');

            // 3. Enhance sections table for class_teacher_id
            self::addColumnIfNotExists('sections', 'class_teacher_id', 'INT NULL');

            // 4. Enhance class_subjects table for periods_per_week
            self::addColumnIfNotExists('class_subjects', 'periods_per_week', 'INT DEFAULT 5');

            // 5. Front CMS Pages enhancements (for attachments, files & documents)
            self::addColumnIfNotExists('front_pages', 'file_path', 'VARCHAR(255) NULL');
            self::addColumnIfNotExists('front_pages', 'file_name', 'VARCHAR(255) NULL');
            self::addColumnIfNotExists('front_pages', 'meta_description', 'VARCHAR(255) NULL');
            self::addColumnIfNotExists('front_menus', 'dropdown_group', "VARCHAR(50) DEFAULT 'none'");

            // ==========================================
            // PHASE 2: STUDENT 360, FAMILIES & INCOMES
            // ==========================================

            self::addColumnIfNotExists('students', 'name', 'VARCHAR(150) NULL');
            self::addColumnIfNotExists('students', 'email', 'VARCHAR(150) NULL');
            self::addColumnIfNotExists('students', 'phone', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'emergency_contact', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'reg_no', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'bform_cnic', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'blood_group', 'VARCHAR(10) NULL');
            self::addColumnIfNotExists('students', 'father_name', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'father_cnic', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'mother_name', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'guardian_name', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'guardian_relation', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'previous_school', 'VARCHAR(255) NULL');
            self::addColumnIfNotExists('students', 'admission_date', 'DATE NULL');
            self::addColumnIfNotExists('students', 'student_photo', 'VARCHAR(255) NULL');
            self::addColumnIfNotExists('students', 'status', "VARCHAR(30) DEFAULT 'Active'");
            self::addColumnIfNotExists('students', 'family_id', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'sibling_discount_percent', 'DECIMAL(5,2) DEFAULT 0.00');
            self::addColumnIfNotExists('students', 'custom_discount_amount', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('students', 'concession_type', "VARCHAR(50) DEFAULT 'None'");
            self::addColumnIfNotExists('students', 'academic_session_id', 'INT NULL');
            self::addColumnIfNotExists('students', 'is_fresh_admission', 'TINYINT(1) DEFAULT 1');
            self::addColumnIfNotExists('students', 'religion', "VARCHAR(50) DEFAULT 'Islam'");
            self::addColumnIfNotExists('students', 'nationality', "VARCHAR(50) DEFAULT 'Pakistani'");
            self::addColumnIfNotExists('students', 'mother_tongue', "VARCHAR(50) DEFAULT 'Urdu'");
            self::addColumnIfNotExists('students', 'father_occupation', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'father_income', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'father_phone', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'mother_cnic', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'mother_occupation', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'guardian_cnic', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'guardian_phone', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'guardian_occupation', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'permanent_address', 'TEXT NULL');
            self::addColumnIfNotExists('students', 'city', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'district', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'tehsil', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'special_needs', 'VARCHAR(255) NULL');
            self::addColumnIfNotExists('students', 'prev_school_name', 'VARCHAR(255) NULL');
            self::addColumnIfNotExists('students', 'prev_school_city', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'prev_class', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'prev_medium', "VARCHAR(50) DEFAULT 'English'");
            self::addColumnIfNotExists('students', 'slc_number', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('students', 'slc_date', 'DATE NULL');
            self::addColumnIfNotExists('students', 'prev_board_roll_no', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('students', 'prev_marks_obtained', 'DECIMAL(6,2) DEFAULT 0.00');
            self::addColumnIfNotExists('students', 'prev_total_marks', 'DECIMAL(6,2) DEFAULT 0.00');
            self::addColumnIfNotExists('students', 'prev_grade', 'VARCHAR(20) NULL');
            self::addColumnIfNotExists('students', 'reason_for_leaving', 'VARCHAR(255) NULL');

            try {
                $db->query("UPDATE students s JOIN users u ON s.user_id = u.id SET s.name = u.name WHERE s.name IS NULL OR s.name = ''");
                $db->execute();
                $db->query("UPDATE students s JOIN users u ON s.user_id = u.id SET s.email = u.email WHERE (s.email IS NULL OR s.email = '') AND u.email IS NOT NULL");
                $db->execute();
                $db->query("UPDATE students SET phone = parent_phone WHERE (phone IS NULL OR phone = '') AND parent_phone IS NOT NULL");
                $db->execute();
            } catch (Exception $e) {}

            // 6. Families / Siblings Table
            $db->query("CREATE TABLE IF NOT EXISTS families (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                family_code VARCHAR(50) NOT NULL,
                father_name VARCHAR(100) NULL,
                father_cnic VARCHAR(50) NULL,
                guardian_phone VARCHAR(20) NULL,
                default_discount_percent DECIMAL(5,2) DEFAULT 10.00,
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // 7. Incomes Table (For Accounting & Auto-Sync with Fee Payments)
            $db->query("CREATE TABLE IF NOT EXISTS incomes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                category VARCHAR(100) DEFAULT 'Student Fee',
                amount DECIMAL(10,2) NOT NULL,
                date DATE NOT NULL,
                payment_mode VARCHAR(50) DEFAULT 'Cash',
                reference_no VARCHAR(100) NULL,
                fee_payment_id INT NULL,
                note TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // ==========================================
            // PHASE 3: STAFF HR, VISITING FACULTY & WORKLOAD PAYROLL
            // ==========================================

            // 8. Staff / Teacher HR Profiles Table
            $db->query("CREATE TABLE IF NOT EXISTS staff (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                user_id INT NOT NULL,
                staff_code VARCHAR(50) NOT NULL,
                cnic VARCHAR(50) NULL,
                department VARCHAR(100) NULL,
                designation VARCHAR(100) NULL,
                employment_type VARCHAR(50) DEFAULT 'Permanent',
                qualification VARCHAR(255) NULL,
                experience_years VARCHAR(50) NULL,
                gender VARCHAR(20) DEFAULT 'Male',
                dob DATE NULL,
                phone VARCHAR(30) NULL,
                emergency_contact VARCHAR(50) NULL,
                address TEXT NULL,
                date_of_joining DATE NULL,
                basic_salary DECIMAL(10,2) DEFAULT 0.00,
                lecture_rate DECIMAL(10,2) DEFAULT 0.00,
                bank_name VARCHAR(100) NULL,
                bank_account_no VARCHAR(100) NULL,
                status VARCHAR(30) DEFAULT 'Active',
                photo VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // Auto-backfill any existing users with staff/teacher roles who lack a staff record
            $db->query("SELECT u.id, u.school_id, u.name, u.role FROM users u 
                        LEFT JOIN staff s ON u.id = s.user_id 
                        WHERE s.id IS NULL AND u.role IN ('teacher', 'admin', 'accountant', 'librarian', 'receptionist')");
            $unlinkedStaff = $db->resultSet();
            if(!empty($unlinkedStaff)){
                foreach($unlinkedStaff as $us){
                    $prefix = ($us->role == 'teacher') ? 'TCH' : 'STF';
                    $code = $prefix . '-' . str_pad($us->id, 3, '0', STR_PAD_LEFT);
                    $desig = ($us->role == 'teacher') ? 'Subject Teacher' : ucfirst($us->role);
                    $dept = ($us->role == 'teacher') ? 'Academics' : 'Administration';
                    $schId = $us->school_id ?: 1;

                    $db->query("INSERT INTO staff (school_id, user_id, staff_code, department, designation, employment_type, status, basic_salary)
                                VALUES (:sch, :uid, :code, :dept, :desig, 'Permanent', 'Active', 35000.00)");
                    $db->bind(':sch', $schId);
                    $db->bind(':uid', $us->id);
                    $db->bind(':code', $code);
                    $db->bind(':dept', $dept);
                    $db->bind(':desig', $desig);
                    $db->execute();
                }
            }

            // 9. Enhance staff_payroll for visiting faculty and structured allowances
            self::addColumnIfNotExists('staff_payroll', 'employment_type', "VARCHAR(50) DEFAULT 'Permanent'");
            self::addColumnIfNotExists('staff_payroll', 'lecture_rate', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('staff_payroll', 'medical_allowance', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('staff_payroll', 'house_rent_allowance', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('staff_payroll', 'conveyance_allowance', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('staff_payroll', 'tax_deduction', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('staff_payroll', 'provident_fund', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('staff_payroll', 'other_deductions', 'DECIMAL(10,2) DEFAULT 0.00');

            // 10. Enhance staff_payslips for visiting lectures and accounting auto-sync
            self::addColumnIfNotExists('staff_payslips', 'employment_type', "VARCHAR(50) DEFAULT 'Permanent'");
            self::addColumnIfNotExists('staff_payslips', 'lectures_delivered', 'INT DEFAULT 0');
            self::addColumnIfNotExists('staff_payslips', 'lecture_rate', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('staff_payslips', 'expense_id', 'INT NULL');

            // ==========================================
            // PHASE 4: EXAM MANAGEMENT, MARKS LOCK & 4-TIER APPROVAL CHAIN
            // ==========================================

            // 11. Enhance exams table for session context and Pakistani exam types
            self::addColumnIfNotExists('exams', 'academic_session_id', 'INT NULL');
            self::addColumnIfNotExists('exams', 'exam_type', "VARCHAR(50) DEFAULT 'Term Exam'");
            self::addColumnIfNotExists('exams', 'start_date', 'DATE NULL');
            self::addColumnIfNotExists('exams', 'end_date', 'DATE NULL');
            self::addColumnIfNotExists('exams', 'is_active', 'TINYINT(1) DEFAULT 1');

            // 12. Enhance exam_schedules for 4-tier approval state machine & marks structure
            self::addColumnIfNotExists('exam_schedules', 'theory_marks', 'DECIMAL(5,2) DEFAULT 75.00');
            self::addColumnIfNotExists('exam_schedules', 'practical_marks', 'DECIMAL(5,2) DEFAULT 25.00');
            self::addColumnIfNotExists('exam_schedules', 'approval_status', "VARCHAR(50) DEFAULT 'draft'");
            self::addColumnIfNotExists('exam_schedules', 'submitted_by_teacher_id', 'INT NULL');
            self::addColumnIfNotExists('exam_schedules', 'submitted_at', 'DATETIME NULL');
            self::addColumnIfNotExists('exam_schedules', 'reviewed_by_class_teacher_id', 'INT NULL');
            self::addColumnIfNotExists('exam_schedules', 'reviewed_at', 'DATETIME NULL');
            self::addColumnIfNotExists('exam_schedules', 'verified_by_vp_id', 'INT NULL');
            self::addColumnIfNotExists('exam_schedules', 'verified_at', 'DATETIME NULL');
            self::addColumnIfNotExists('exam_schedules', 'approved_by_principal_id', 'INT NULL');
            self::addColumnIfNotExists('exam_schedules', 'approved_at', 'DATETIME NULL');
            self::addColumnIfNotExists('exam_schedules', 'rejection_reason', 'TEXT NULL');

            // 13. Enhance exam_results for theory/practical breakdown
            self::addColumnIfNotExists('exam_results', 'theory_marks', 'DECIMAL(5,2) DEFAULT 0.00');
            self::addColumnIfNotExists('exam_results', 'practical_marks', 'DECIMAL(5,2) DEFAULT 0.00');
            self::addColumnIfNotExists('exam_results', 'remarks', 'VARCHAR(255) NULL');

            // ==========================================
            // PHASE 6: ATTENDANCE & PARENT PORTAL
            // ==========================================
            self::addColumnIfNotExists('student_attendance', 'entry_time', 'TIME NULL');
            self::addColumnIfNotExists('student_attendance', 'sms_sent', 'TINYINT(1) DEFAULT 0');

            // ==========================================
            // PHASE 7: PAKISTANI FEE STRUCTURE & 3-COPY CHALLANS
            // ==========================================
            
            // 14. School Bank Accounts for Fee Challans
            $db->query("CREATE TABLE IF NOT EXISTS school_banks (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                bank_name VARCHAR(100) NOT NULL,
                branch_name VARCHAR(100) NULL,
                account_title VARCHAR(150) NOT NULL,
                account_no VARCHAR(50) NOT NULL,
                iban VARCHAR(50) NULL,
                is_default TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // Seed default bank for school if not exists
            $db->query("SELECT id FROM school_banks WHERE school_id = :school_id LIMIT 1");
            $db->bind(':school_id', $schoolId);
            if (!$db->single()) {
                $db->query("INSERT INTO school_banks (school_id, bank_name, branch_name, account_title, account_no, iban, is_default)
                            VALUES (:school_id, 'Habib Bank Limited (HBL)', 'City Campus Branch', 'Pak Academy Model School System', '1029-3847-2910-01', 'PK36HABB0001029384729101', 1)");
                $db->bind(':school_id', $schoolId);
                $db->execute();
            }

            // 15. Enhance student_fees for multi-head monthly challans and sibling discounts
            self::addColumnIfNotExists('student_fees', 'challan_no', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('student_fees', 'billing_month', 'VARCHAR(30) NULL');
            self::addColumnIfNotExists('student_fees', 'session_id', 'INT NULL');
            self::addColumnIfNotExists('student_fees', 'due_date', 'DATE NULL');
            self::addColumnIfNotExists('student_fees', 'sibling_discount', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('student_fees', 'arrears', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('student_fees', 'status', "VARCHAR(20) DEFAULT 'unpaid'");

            // Seed standard Pakistani fee types if empty
            $db->query("SELECT COUNT(*) as count FROM fee_types WHERE school_id = :school_id");
            $db->bind(':school_id', $schoolId);
            $typeCount = $db->single();
            if ($typeCount && $typeCount->count == 0) {
                $defaultTypes = [
                    ['Tuition Fee', 'TUI', 'Regular monthly instructional fee'],
                    ['Admission Fee', 'ADM', 'One-time admission registration fee'],
                    ['Annual Resource & Library Fund', 'ANN', 'Annual stationery, sports and library fund'],
                    ['Computer & Science Lab Charges', 'LAB', 'IT and laboratory equipment maintenance'],
                    ['Examination & Assessment Fee', 'EXM', 'Term exam paper printing and DMC charges'],
                    ['Generator & Utility Surcharge', 'UTL', 'Power backup and facility utilities']
                ];
                foreach ($defaultTypes as $dt) {
                    $db->query("INSERT INTO fee_types (school_id, type_name, type_code, description) VALUES (:sch, :tn, :tc, :desc)");
                    $db->bind(':sch', $schoolId);
                    $db->bind(':tn', $dt[0]);
                    $db->bind(':tc', $dt[1]);
                    $db->bind(':desc', $dt[2]);
                    $db->execute();
                }
            }

            // Seed standard fee group and links if empty
            $db->query("SELECT COUNT(*) as count FROM fee_groups WHERE school_id = :school_id");
            $db->bind(':school_id', $schoolId);
            $groupCount = $db->single();
            if ($groupCount && $groupCount->count == 0) {
                $db->query("INSERT INTO fee_groups (school_id, group_name, description) VALUES (:sch, 'Monthly Composite Fee Package', 'Standard monthly composite fees for all classes')");
                $db->bind(':sch', $schoolId);
                $db->execute();
                $newGroupId = $db->lastInsertId();

                // Link Tuition, Lab, Exam fees
                $db->query("SELECT id, type_code FROM fee_types WHERE school_id = :sch");
                $db->bind(':sch', $schoolId);
                $seededTypes = $db->resultSet();
                foreach ($seededTypes as $st) {
                    $amt = 3500.00;
                    if ($st->type_code == 'LAB') $amt = 500.00;
                    elseif ($st->type_code == 'EXM') $amt = 300.00;
                    elseif ($st->type_code == 'UTL') $amt = 400.00;
                    elseif ($st->type_code == 'ANN') $amt = 1200.00;
                    elseif ($st->type_code == 'ADM') $amt = 5000.00;

                    $db->query("INSERT INTO fee_groups_types (school_id, fee_group_id, fee_type_id, amount, due_date, fine_amount) 
                                VALUES (:sch, :fgid, :ftid, :amt, :due, 200.00)");
                    $db->bind(':sch', $schoolId);
                    $db->bind(':fgid', $newGroupId);
                    $db->bind(':ftid', $st->id);
                    $db->bind(':amt', $amt);
                    $db->bind(':due', date('Y-m-10'));
                    $db->execute();
                }
            }

            // ==========================================
            // PHASE 8: CERTIFICATES & STUDENT CREDENTIALS
            // ==========================================
            
            // 16. Student Issued Certificates Ledger (SLC, Character, Bonafide)
            $db->query("CREATE TABLE IF NOT EXISTS student_certificates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                student_id INT NOT NULL,
                certificate_type VARCHAR(50) NOT NULL,
                certificate_no VARCHAR(50) NOT NULL,
                issue_date DATE NOT NULL,
                leaving_date DATE NULL,
                reason_for_leaving VARCHAR(255) NULL,
                conduct VARCHAR(100) DEFAULT 'Exemplary',
                promoted_to_class VARCHAR(100) NULL,
                remarks TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // Ensure all columns exist on student_certificates table even if pre-existing
            self::addColumnIfNotExists('student_certificates', 'school_id', 'INT NULL');
            self::addColumnIfNotExists('student_certificates', 'student_id', 'INT NULL');
            self::addColumnIfNotExists('student_certificates', 'certificate_type', "VARCHAR(50) DEFAULT 'SLC'");
            self::addColumnIfNotExists('student_certificates', 'certificate_no', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('student_certificates', 'issue_date', 'DATE NULL');
            self::addColumnIfNotExists('student_certificates', 'leaving_date', 'DATE NULL');
            self::addColumnIfNotExists('student_certificates', 'reason_for_leaving', 'VARCHAR(255) NULL');
            self::addColumnIfNotExists('student_certificates', 'conduct', "VARCHAR(100) DEFAULT 'Exemplary'");
            self::addColumnIfNotExists('student_certificates', 'promoted_to_class', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('student_certificates', 'remarks', 'TEXT NULL');

            // Check and enhance certificates table for security barcode / template features
            self::addColumnIfNotExists('certificates', 'certificate_type', "VARCHAR(50) DEFAULT 'General'");

            // ==========================================
            // PHASE 9: FRONT OFFICE, RECEPTION & GATE SECURITY
            // ==========================================

            // 17. Enhance visitor_book for gate security, CNIC and badge passes
            self::addColumnIfNotExists('visitor_book', 'pass_no', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('visitor_book', 'cnic_passport', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('visitor_book', 'person_to_meet', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('visitor_book', 'vehicle_no', 'VARCHAR(50) NULL');
            self::addColumnIfNotExists('visitor_book', 'department', 'VARCHAR(100) NULL');
            self::addColumnIfNotExists('visitor_book', 'status', "VARCHAR(50) DEFAULT 'Checked In'");

            // 18. Student Early Departure Gate Pass (Child Safety Protocol)
            $db->query("CREATE TABLE IF NOT EXISTS student_gate_passes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                pass_no VARCHAR(50) NOT NULL,
                student_id INT NOT NULL,
                pass_date DATE NOT NULL,
                leave_time VARCHAR(20) NOT NULL,
                reason_type VARCHAR(100) NOT NULL,
                reason_details TEXT NULL,
                collected_by_name VARCHAR(150) NOT NULL,
                collected_by_cnic VARCHAR(50) NULL,
                collected_by_relation VARCHAR(50) NOT NULL,
                collected_by_phone VARCHAR(50) NULL,
                approved_by_user_id INT NULL,
                status VARCHAR(50) DEFAULT 'Issued',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // 19. Postal / Courier Inward & Outward Dispatch Log
            $db->query("CREATE TABLE IF NOT EXISTS postal_records (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                dispatch_type VARCHAR(20) NOT NULL,
                reference_no VARCHAR(100) NULL,
                sender_title VARCHAR(150) NOT NULL,
                receiver_title VARCHAR(150) NOT NULL,
                record_date DATE NOT NULL,
                courier_name VARCHAR(100) NULL,
                tracking_id VARCHAR(100) NULL,
                category VARCHAR(100) DEFAULT 'General',
                note TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // 20. Reception Phone Call & Parental Query Log
            $db->query("CREATE TABLE IF NOT EXISTS phone_call_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                call_type VARCHAR(20) DEFAULT 'Incoming',
                caller_name VARCHAR(150) NOT NULL,
                phone VARCHAR(50) NOT NULL,
                call_date DATE NOT NULL,
                call_time VARCHAR(20) NULL,
                duration VARCHAR(50) NULL,
                purpose VARCHAR(150) NULL,
                follow_up_date DATE NULL,
                note TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // 21. Enhance admission_enquiry for lead pipeline
            self::addColumnIfNotExists('admission_enquiry', 'status', "VARCHAR(50) DEFAULT 'New'");
            self::addColumnIfNotExists('admission_enquiry', 'discount_offered', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('admission_enquiry', 'converted_student_id', 'INT NULL');

            // ==========================================
            // PHASE 10: STUDENT EXIT CLEARANCE & PROMOTION ENGINE
            // ==========================================

            // 22. Student Institutional Clearance (5 Departments)
            $db->query("CREATE TABLE IF NOT EXISTS student_clearances (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                student_id INT NOT NULL,
                clearance_no VARCHAR(50) NOT NULL,
                academic_session_id INT NULL,
                reason_for_leaving VARCHAR(255) DEFAULT 'Completed Matriculation / Migration',
                application_date DATE NOT NULL,
                completion_date DATE NULL,
                accounts_status VARCHAR(20) DEFAULT 'Pending',
                accounts_remarks VARCHAR(255) NULL,
                accounts_cleared_by VARCHAR(100) NULL,
                library_status VARCHAR(20) DEFAULT 'Pending',
                library_remarks VARCHAR(255) NULL,
                library_cleared_by VARCHAR(100) NULL,
                lab_status VARCHAR(20) DEFAULT 'Pending',
                lab_remarks VARCHAR(255) NULL,
                lab_cleared_by VARCHAR(100) NULL,
                sports_status VARCHAR(20) DEFAULT 'Pending',
                sports_remarks VARCHAR(255) NULL,
                sports_cleared_by VARCHAR(100) NULL,
                class_teacher_status VARCHAR(20) DEFAULT 'Pending',
                class_teacher_remarks VARCHAR(255) NULL,
                class_teacher_cleared_by VARCHAR(100) NULL,
                overall_status VARCHAR(30) DEFAULT 'In Progress',
                approved_by_principal_id INT NULL,
                approved_at DATETIME NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // 23. Student Academic Session Promotion Log
            $db->query("CREATE TABLE IF NOT EXISTS student_promotions_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL,
                student_id INT NOT NULL,
                from_session_id INT NULL,
                to_session_id INT NOT NULL,
                from_class_id INT NOT NULL,
                to_class_id INT NOT NULL,
                from_section_id INT NULL,
                to_section_id INT NULL,
                promotion_status VARCHAR(50) DEFAULT 'Promoted',
                exam_marks_summary VARCHAR(255) NULL,
                promoted_by_user_id INT NULL,
                promoted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // 24. Enhance students table for clearance tracking
            self::addColumnIfNotExists('students', 'clearance_status', "VARCHAR(50) DEFAULT 'Not Applied'");

            // ==========================================
            // PHASE 11: RBAC PERMISSIONS MATRIX & INSTITUTIONAL SITE SETTINGS
            // ==========================================

            // 25. RBAC Tables: roles, permissions, role_permissions, user_roles
            $db->query("CREATE TABLE IF NOT EXISTS roles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NULL,
                name VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            $db->query("CREATE TABLE IF NOT EXISTS permissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                permission_key VARCHAR(100) NOT NULL UNIQUE,
                description VARCHAR(255) NOT NULL,
                category VARCHAR(50) DEFAULT 'General',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();
            self::addColumnIfNotExists('permissions', 'category', "VARCHAR(50) DEFAULT 'General'");

            $db->query("CREATE TABLE IF NOT EXISTS role_permissions (
                role_id INT NOT NULL,
                permission_id INT NOT NULL,
                PRIMARY KEY (role_id, permission_id)
            )");
            $db->execute();

            $db->query("CREATE TABLE IF NOT EXISTS user_roles (
                user_id INT NOT NULL,
                role_id INT NOT NULL,
                PRIMARY KEY (user_id, role_id)
            )");
            $db->execute();

            // Seed Standard Permissions if not present
            $defaultPerms = [
                'view_students' => ['Can view student profiles and directories', 'Students'],
                'manage_students' => ['Can add, edit, or withdraw students', 'Students'],
                'view_academics' => ['Can view classes, sections, and subjects', 'Academics'],
                'manage_academics' => ['Can create and manage classes and timetable', 'Academics'],
                'view_attendance' => ['Can view student & staff attendance reports', 'Attendance'],
                'manage_attendance' => ['Can mark and modify student/staff attendance', 'Attendance'],
                'view_exams' => ['Can view exam schedules, cards, and marks', 'Examination'],
                'manage_exams' => ['Can create exams, enter marks, and generate report cards', 'Examination'],
                'view_finance' => ['Can view fee statements and invoices', 'Finance'],
                'manage_finance' => ['Can collect fees, issue vouchers, and manage expenses', 'Finance'],
                'view_communication' => ['Can view notice board and messages', 'Communication'],
                'manage_communication' => ['Can broadcast notices and send SMS/WhatsApp alerts', 'Communication'],
                'manage_inventory' => ['Can manage item stocks, categories, and issuances', 'Logistics'],
                'manage_library' => ['Can manage book catalog and member circulations', 'Logistics'],
                'manage_front_office' => ['Can manage visitors, gate passes, and call logs', 'Front Office'],
                'manage_clearance' => ['Can process student institutional exit clearance', 'Clearance'],
                'view_reports' => ['Can view analytics dashboard and operational reports', 'System'],
                'manage_settings' => ['Can modify school configuration, prefixes, and RBAC matrix', 'System']
            ];

            foreach ($defaultPerms as $pkey => $pdata) {
                $db->query("SELECT id FROM permissions WHERE permission_key = :pkey LIMIT 1");
                $db->bind(':pkey', $pkey);
                $pRow = $db->single();
                if (!$pRow) {
                    $db->query("INSERT INTO permissions (permission_key, description, category) VALUES (:pkey, :desc, :cat)");
                    $db->bind(':pkey', $pkey);
                    $db->bind(':desc', $pdata[0]);
                    $db->bind(':cat', $pdata[1]);
                    $db->execute();
                }
            }

            // Seed Global / Default Roles if not present
            $standardRoles = [
                'super_admin' => array_keys($defaultPerms),
                'admin' => array_keys($defaultPerms),
                'teacher' => [
                    'view_students', 'view_academics', 'view_attendance', 'manage_attendance',
                    'view_exams', 'manage_exams', 'view_communication', 'manage_communication', 'manage_clearance'
                ],
                'accountant' => [
                    'view_students', 'view_finance', 'manage_finance', 'view_communication', 'manage_clearance', 'view_reports'
                ],
                'librarian' => [
                    'view_students', 'manage_library', 'view_communication', 'manage_clearance'
                ],
                'receptionist' => [
                    'view_students', 'manage_front_office', 'view_communication'
                ],
                'student' => [
                    'view_academics', 'view_attendance', 'view_exams', 'view_communication', 'view_finance'
                ],
                'parent' => [
                    'view_students', 'view_academics', 'view_attendance', 'view_exams', 'view_communication', 'view_finance'
                ]
            ];

            // Fetch permission id map
            $db->query("SELECT id, permission_key FROM permissions");
            $allPermRows = $db->resultSet();
            $permIdMap = [];
            if ($allPermRows) {
                foreach ($allPermRows as $pr) {
                    $permIdMap[$pr->permission_key] = $pr->id;
                }
            }

            foreach ($standardRoles as $rName => $assignedKeys) {
                $db->query("SELECT id FROM roles WHERE name = :rname LIMIT 1");
                $db->bind(':rname', $rName);
                $existingRole = $db->single();
                if (!$existingRole) {
                    $db->query("INSERT INTO roles (school_id, name) VALUES (NULL, :rname)");
                    $db->bind(':rname', $rName);
                    $db->execute();
                    $roleId = $db->lastInsertId();
                    if (!$roleId) {
                        $db->query("SELECT id FROM roles WHERE name = :rname LIMIT 1");
                        $db->bind(':rname', $rName);
                        $rRow = $db->single();
                        $roleId = $rRow ? $rRow->id : null;
                    }
                } else {
                    $roleId = $existingRole->id;
                }

                // Check if role has any permissions assigned
                $db->query("SELECT COUNT(*) as cnt FROM role_permissions WHERE role_id = :role_id");
                $db->bind(':role_id', $roleId);
                $rpCnt = $db->single();
                if ($rpCnt && $rpCnt->cnt == 0) {
                    foreach ($assignedKeys as $ak) {
                        if (isset($permIdMap[$ak])) {
                            $db->query("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (:role_id, :perm_id)");
                            $db->bind(':role_id', $roleId);
                            $db->bind(':perm_id', $permIdMap[$ak]);
                            $db->execute();
                        }
                    }
                }
            }

            // 26. Site Settings Table & Institutional Keys Seeding
            $db->query("CREATE TABLE IF NOT EXISTS site_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NULL,
                setting_key VARCHAR(100) NOT NULL,
                setting_value TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();
            self::addColumnIfNotExists('site_settings', 'school_id', 'INT NULL');
            self::addColumnIfNotExists('staff_payslips', 'leave_deduction', 'DECIMAL(10,2) DEFAULT 0.00');
            self::addColumnIfNotExists('staff_payslips', 'absent_days', 'DECIMAL(5,2) DEFAULT 0.00');
            self::addColumnIfNotExists('staff_payslips', 'half_days', 'DECIMAL(5,2) DEFAULT 0.00');

            $defaultSettings = [
                'school_name' => 'City Model High School & College',
                'campus_name' => 'Main Executive Campus',
                'affiliation_board' => 'FBISE Islamabad (Affiliation # FBISE-REG-88219)',
                'affiliation_no' => 'FBISE-REG-88219',
                'ntn_number' => 'NTN-7349102-1',
                'school_email' => 'info@citymodelschool.edu.pk',
                'school_phone' => '+92-51-111-222-333',
                'school_address' => 'Plot 45-B, Sector H-8/4, Education City, Islamabad',
                'currency_symbol' => 'Rs.',
                'prefix_student' => 'ADM-',
                'prefix_staff' => 'EMP-',
                'prefix_teacher' => 'TCH-',
                'prefix_family' => 'FAM-',
                'prefix_challan' => 'CHL-',
                'prefix_clearance' => 'CLR-',
                'prefix_visitor' => 'VIS-',
                'prefix_gatepass' => 'GP-',
                'toggle_require_bform' => '1',
                'toggle_require_father_cnic' => '1',
                'toggle_sibling_discount' => '1',
                'toggle_daily_attendance_sms' => '1',
                'toggle_fee_accounting_sync' => '1',
                'toggle_strict_clearance_slc' => '1',
                'payroll_leave_cutting_enabled' => '1',
                'payroll_free_leaves_per_month' => '2',
                'payroll_absent_cutting_percent' => '100',
                'payroll_half_day_cutting_percent' => '50',
                'whatsapp_enabled' => '1',
                'whatsapp_number' => '+92-300-1234567',
                'whatsapp_default_msg' => 'Hello! I would like to inquire about admissions and school programs.',
                'whatsapp_agent_name' => 'Admissions & Helpdesk',
                'whatsapp_popup_enabled' => '1',
                'livechat_enabled' => '1',
                'livechat_provider' => 'builtin',
                'livechat_welcome_title' => 'Live School Support',
                'livechat_welcome_msg' => 'Hello! Welcome to our school helpdesk. How can we assist you today?',
                'livechat_tawk_property_id' => '',
                'livechat_tawk_widget_id' => '',
                'livechat_crisp_website_id' => '',
                'livechat_custom_script' => ''
            ];

            foreach ($defaultSettings as $sKey => $sVal) {
                $db->query("SELECT id FROM site_settings WHERE setting_key = :skey LIMIT 1");
                $db->bind(':skey', $sKey);
                $existSetting = $db->single();
                if (!$existSetting) {
                    $db->query("INSERT INTO site_settings (school_id, setting_key, setting_value) VALUES (:sid, :skey, :sval)");
                    $db->bind(':sid', $schoolId);
                    $db->bind(':skey', $sKey);
                    $db->bind(':sval', $sVal);
                    $db->execute();
                }
            }

            // 27. Notice Board Table & Enhancements
            $db->query("CREATE TABLE IF NOT EXISTS notice_board (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT,
                notice_type VARCHAR(50) DEFAULT 'General Notice',
                priority VARCHAR(20) DEFAULT 'Normal',
                is_visible_to_student VARCHAR(10) DEFAULT 'yes',
                is_visible_to_staff VARCHAR(10) DEFAULT 'yes',
                is_visible_to_parent VARCHAR(10) DEFAULT 'yes',
                publish_date DATE NOT NULL,
                expiry_date DATE NULL,
                status VARCHAR(20) DEFAULT 'Published',
                attachment VARCHAR(255) NULL,
                created_by INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            self::addColumnIfNotExists('notice_board', 'school_id', 'INT NULL');
            self::addColumnIfNotExists('notice_board', 'notice_type', "VARCHAR(50) DEFAULT 'General Notice'");
            self::addColumnIfNotExists('notice_board', 'priority', "VARCHAR(20) DEFAULT 'Normal'");
            self::addColumnIfNotExists('notice_board', 'is_visible_to_student', "VARCHAR(10) DEFAULT 'yes'");
            self::addColumnIfNotExists('notice_board', 'is_visible_to_staff', "VARCHAR(10) DEFAULT 'yes'");
            self::addColumnIfNotExists('notice_board', 'is_visible_to_parent', "VARCHAR(10) DEFAULT 'yes'");
            self::addColumnIfNotExists('notice_board', 'expiry_date', "DATE NULL");
            self::addColumnIfNotExists('notice_board', 'status', "VARCHAR(20) DEFAULT 'Published'");
            self::addColumnIfNotExists('notice_board', 'attachment', "VARCHAR(255) NULL");

            // Front Alumni Table
            $db->query("CREATE TABLE IF NOT EXISTS front_alumni (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL DEFAULT 1,
                name VARCHAR(150) NOT NULL,
                batch_year VARCHAR(50) NOT NULL,
                graduation_class VARCHAR(100) NULL,
                current_position VARCHAR(150) NULL,
                company_organization VARCHAR(150) NULL,
                location VARCHAR(100) NULL,
                image VARCHAR(255) NULL,
                testimonial TEXT NULL,
                linkedin_url VARCHAR(255) NULL,
                email VARCHAR(150) NULL,
                phone VARCHAR(50) NULL,
                is_featured TINYINT(1) DEFAULT 0,
                is_active ENUM('yes', 'no') DEFAULT 'yes',
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // Front Requirements & Emergency Notices Table
            $db->query("CREATE TABLE IF NOT EXISTS front_requirements (
                id INT AUTO_INCREMENT PRIMARY KEY,
                school_id INT NOT NULL DEFAULT 1,
                title VARCHAR(255) NOT NULL,
                category VARCHAR(50) NOT NULL,
                department VARCHAR(100) NULL,
                description LONGTEXT NOT NULL,
                eligibility TEXT NULL,
                deadline DATE NULL,
                vacancies INT DEFAULT 1,
                attachment VARCHAR(255) NULL,
                status ENUM('active', 'closed', 'archived') DEFAULT 'active',
                show_in_menu TINYINT(1) DEFAULT 0,
                menu_title VARCHAR(100) NULL,
                sort_order INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            $db->execute();

            // Enhance front_cms_settings for alumni & requirements toggle
            self::addColumnIfNotExists('front_cms_settings', 'enable_alumni', "ENUM('yes', 'no') DEFAULT 'yes'");
            self::addColumnIfNotExists('front_cms_settings', 'enable_requirements', "ENUM('yes', 'no') DEFAULT 'yes'");

            self::$synced = true;
            $_SESSION['schema_synced'] = true;
            @file_put_contents($lockFile, date('Y-m-d H:i:s'));
        } catch (Exception $e) {
            error_log("SchemaSync error: " . $e->getMessage());
        }
    }

    private static function addColumnIfNotExists($table, $column, $definition) {
        try {
            $db = new Database();
            $db->query("SHOW COLUMNS FROM `$table` LIKE :col");
            $db->bind(':col', $column);
            $res = $db->single();
            if (!$res) {
                $db->query("ALTER TABLE `$table` ADD COLUMN `$column` $definition");
                $db->execute();
            }
        } catch (Exception $e) {
            // Column may already exist or table doesn't exist
        }
    }

    public static function forceSync() {
        self::$synced = false;
        unset($_SESSION['schema_synced']);
        $lockFile = defined('APPROOT') ? (APPROOT . '/schema_synced.lock') : (__DIR__ . '/../schema_synced.lock');
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }
        self::run();
    }

    public static function addIndexIfNotExists($table, $indexName, $columns) {
        try {
            $db = new Database();
            $db->query("SHOW INDEX FROM `$table` WHERE Key_name = :idx");
            $db->bind(':idx', $indexName);
            $res = $db->single();
            if (!$res) {
                $db->query("ALTER TABLE `$table` ADD INDEX `$indexName` ($columns)");
                $db->execute();
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function applyPerformanceIndexes() {
        $results = [];
        $indexes = [
            ['students', 'idx_school_status_class', 'school_id, status, class_id'],
            ['students', 'idx_school_admission_no', 'school_id, admission_no'],
            ['students', 'idx_school_parent_user', 'school_id, parent_user_id'],
            ['student_fees', 'idx_school_student_month', 'school_id, student_id, billing_month'],
            ['student_fees', 'idx_school_challan', 'school_id, challan_no'],
            ['student_fees', 'idx_school_status_due', 'school_id, status, due_date'],
            ['fee_payments', 'idx_student_fee_id', 'student_fee_id'],
            ['fee_payments', 'idx_payment_date', 'payment_date'],
            ['student_attendance', 'idx_school_class_date', 'school_id, class_id, section_id, attendance_date'],
            ['student_attendance', 'idx_school_student_date', 'school_id, student_id, attendance_date'],
            ['users', 'idx_email_school', 'email, school_id'],
            ['users', 'idx_role', 'role'],
            ['incomes', 'idx_school_date', 'school_id, date'],
            ['academic_sessions', 'idx_school_is_current', 'school_id, is_current'],
            ['classes', 'idx_school_id', 'school_id'],
            ['sections', 'idx_school_class', 'school_id, class_id']
        ];

        foreach ($indexes as $item) {
            $table = $item[0];
            $idx   = $item[1];
            $cols  = $item[2];
            $added = self::addIndexIfNotExists($table, $idx, $cols);
            $results[] = [
                'table'  => $table,
                'index'  => $idx,
                'status' => $added ? 'created' : 'already_exists'
            ];
        }

        return $results;
    }
}

