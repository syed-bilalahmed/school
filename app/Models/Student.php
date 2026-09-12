<?php
// app/Models/Student.php

class Student {
    private $db;

    public function __construct(){
        $this->db = new Database();
    }

    public function registerStudent($data, $userId){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $stName = trim($data['name'] ?? '');
        $stEmail = trim($data['email'] ?? '');
        $stPhone = trim($data['phone'] ?? ($data['parent_phone'] ?? ''));
        
        $sql = "INSERT INTO students (
                    school_id, user_id, name, email, phone, admission_no, roll_no, class_id, section_id, dob, gender, 
                    parent_phone, address, parent_user_id, reg_no, bform_cnic, blood_group, 
                    father_name, father_cnic, mother_name, guardian_name, guardian_relation, 
                    previous_school, admission_date, status, family_id, sibling_discount_percent, 
                    custom_discount_amount, concession_type, academic_session_id,
                    is_fresh_admission, religion, nationality, mother_tongue,
                    father_occupation, father_income, father_phone, mother_cnic, mother_occupation,
                    guardian_cnic, guardian_phone, guardian_occupation, permanent_address,
                    city, district, tehsil, special_needs,
                    prev_school_name, prev_school_city, prev_class, prev_medium,
                    slc_number, slc_date, prev_board_roll_no, prev_marks_obtained, prev_total_marks, prev_grade, reason_for_leaving
                ) VALUES (
                    :school_id, :user_id, :name, :email, :phone, :admission_no, :roll_no, :class_id, :section_id, :dob, :gender, 
                    :parent_phone, :address, :parent_user_id, :reg_no, :bform_cnic, :blood_group, 
                    :father_name, :father_cnic, :mother_name, :guardian_name, :guardian_relation, 
                    :previous_school, :admission_date, :status, :family_id, :sibling_discount_percent, 
                    :custom_discount_amount, :concession_type, :academic_session_id,
                    :is_fresh_admission, :religion, :nationality, :mother_tongue,
                    :father_occupation, :father_income, :father_phone, :mother_cnic, :mother_occupation,
                    :guardian_cnic, :guardian_phone, :guardian_occupation, :permanent_address,
                    :city, :district, :tehsil, :special_needs,
                    :prev_school_name, :prev_school_city, :prev_class, :prev_medium,
                    :slc_number, :slc_date, :prev_board_roll_no, :prev_marks_obtained, :prev_total_marks, :prev_grade, :reason_for_leaving
                )";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':name', $stName);
        $this->db->bind(':email', $stEmail);
        $this->db->bind(':phone', $stPhone);
        $this->db->bind(':admission_no', trim($data['admission_no']));
        $this->db->bind(':roll_no', trim($data['roll_no'] ?? ''));
        $this->db->bind(':class_id', !empty($data['class_id']) ? (int)$data['class_id'] : null);
        $this->db->bind(':section_id', !empty($data['section_id']) ? (int)$data['section_id'] : null);
        $this->db->bind(':dob', !empty($data['dob']) ? $data['dob'] : null);
        $this->db->bind(':gender', !empty($data['gender']) ? $data['gender'] : 'Male');
        $this->db->bind(':parent_phone', trim($data['parent_phone'] ?? ''));
        $this->db->bind(':address', trim($data['address'] ?? ''));
        $this->db->bind(':parent_user_id', !empty($data['parent_user_id']) ? (int)$data['parent_user_id'] : null);
        $this->db->bind(':reg_no', trim($data['reg_no'] ?? ''));
        $this->db->bind(':bform_cnic', trim($data['bform_cnic'] ?? ''));
        $this->db->bind(':blood_group', trim($data['blood_group'] ?? ''));
        $this->db->bind(':father_name', trim($data['father_name'] ?? ''));
        $this->db->bind(':father_cnic', trim($data['father_cnic'] ?? ''));
        $this->db->bind(':mother_name', trim($data['mother_name'] ?? ''));
        $this->db->bind(':guardian_name', trim($data['guardian_name'] ?? ''));
        $this->db->bind(':guardian_relation', trim($data['guardian_relation'] ?? ''));
        $this->db->bind(':previous_school', trim($data['previous_school'] ?? ($data['prev_school_name'] ?? '')));
        $this->db->bind(':admission_date', !empty($data['admission_date']) ? $data['admission_date'] : date('Y-m-d'));
        $this->db->bind(':status', !empty($data['status']) ? $data['status'] : 'Active');
        $this->db->bind(':family_id', trim($data['family_id'] ?? ''));
        $this->db->bind(':sibling_discount_percent', !empty($data['sibling_discount_percent']) ? (float)$data['sibling_discount_percent'] : 0.00);
        $this->db->bind(':custom_discount_amount', !empty($data['custom_discount_amount']) ? (float)$data['custom_discount_amount'] : 0.00);
        $this->db->bind(':concession_type', !empty($data['concession_type']) ? $data['concession_type'] : 'None');
        $this->db->bind(':academic_session_id', !empty($data['academic_session_id']) ? (int)$data['academic_session_id'] : null);

        // Demographic and Pakistani specific fields
        $this->db->bind(':is_fresh_admission', isset($data['is_fresh_admission']) ? (int)$data['is_fresh_admission'] : 1);
        $this->db->bind(':religion', trim($data['religion'] ?? 'Islam'));
        $this->db->bind(':nationality', trim($data['nationality'] ?? 'Pakistani'));
        $this->db->bind(':mother_tongue', trim($data['mother_tongue'] ?? 'Urdu'));
        $this->db->bind(':father_occupation', trim($data['father_occupation'] ?? ''));
        $this->db->bind(':father_income', trim($data['father_income'] ?? ''));
        $this->db->bind(':father_phone', trim($data['father_phone'] ?? ($data['parent_phone'] ?? '')));
        $this->db->bind(':mother_cnic', trim($data['mother_cnic'] ?? ''));
        $this->db->bind(':mother_occupation', trim($data['mother_occupation'] ?? ''));
        $this->db->bind(':guardian_cnic', trim($data['guardian_cnic'] ?? ''));
        $this->db->bind(':guardian_phone', trim($data['guardian_phone'] ?? ''));
        $this->db->bind(':guardian_occupation', trim($data['guardian_occupation'] ?? ''));
        $this->db->bind(':permanent_address', trim($data['permanent_address'] ?? ($data['address'] ?? '')));
        $this->db->bind(':city', trim($data['city'] ?? ''));
        $this->db->bind(':district', trim($data['district'] ?? ''));
        $this->db->bind(':tehsil', trim($data['tehsil'] ?? ''));
        $this->db->bind(':special_needs', trim($data['special_needs'] ?? ''));

        // Previous school / transfer credentials
        $this->db->bind(':prev_school_name', trim($data['prev_school_name'] ?? ($data['previous_school'] ?? '')));
        $this->db->bind(':prev_school_city', trim($data['prev_school_city'] ?? ''));
        $this->db->bind(':prev_class', trim($data['prev_class'] ?? ''));
        $this->db->bind(':prev_medium', trim($data['prev_medium'] ?? 'English'));
        $this->db->bind(':slc_number', trim($data['slc_number'] ?? ''));
        $this->db->bind(':slc_date', !empty($data['slc_date']) ? $data['slc_date'] : null);
        $this->db->bind(':prev_board_roll_no', trim($data['prev_board_roll_no'] ?? ''));
        $this->db->bind(':prev_marks_obtained', !empty($data['prev_marks_obtained']) ? (float)$data['prev_marks_obtained'] : 0.00);
        $this->db->bind(':prev_total_marks', !empty($data['prev_total_marks']) ? (float)$data['prev_total_marks'] : 0.00);
        $this->db->bind(':prev_grade', trim($data['prev_grade'] ?? ''));
        $this->db->bind(':reason_for_leaving', trim($data['reason_for_leaving'] ?? ''));

        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function updateStudent($id, $data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $student = $this->getStudentById($id);
        if (!$student) return false;

        // Update user name and email
        if (!empty($data['name']) || !empty($data['email'])) {
            $this->db->query("UPDATE users SET name = :name, email = :email WHERE id = :uid");
            $this->db->bind(':name', trim($data['name'] ?? $student->name));
            $this->db->bind(':email', trim($data['email'] ?? $student->email));
            $this->db->bind(':uid', $student->user_id);
            $this->db->execute();
        }

        $sql = "UPDATE students SET 
                    name = :name, email = :email, phone = :phone,
                    roll_no = :roll_no, class_id = :class_id, section_id = :section_id, 
                    dob = :dob, gender = :gender, parent_phone = :parent_phone, address = :address, 
                    reg_no = :reg_no, bform_cnic = :bform_cnic, blood_group = :blood_group, 
                    father_name = :father_name, father_cnic = :father_cnic, mother_name = :mother_name, 
                    guardian_name = :guardian_name, guardian_relation = :guardian_relation, 
                    previous_school = :previous_school, admission_date = :admission_date, 
                    status = :status, family_id = :family_id, sibling_discount_percent = :sibling_discount_percent, 
                    custom_discount_amount = :custom_discount_amount, concession_type = :concession_type,
                    academic_session_id = :academic_session_id,
                    is_fresh_admission = :is_fresh_admission, religion = :religion, nationality = :nationality, mother_tongue = :mother_tongue,
                    father_occupation = :father_occupation, father_income = :father_income, father_phone = :father_phone,
                    mother_cnic = :mother_cnic, mother_occupation = :mother_occupation,
                    guardian_cnic = :guardian_cnic, guardian_phone = :guardian_phone, guardian_occupation = :guardian_occupation,
                    permanent_address = :permanent_address, city = :city, district = :district, tehsil = :tehsil, special_needs = :special_needs,
                    prev_school_name = :prev_school_name, prev_school_city = :prev_school_city, prev_class = :prev_class, prev_medium = :prev_medium,
                    slc_number = :slc_number, slc_date = :slc_date, prev_board_roll_no = :prev_board_roll_no,
                    prev_marks_obtained = :prev_marks_obtained, prev_total_marks = :prev_total_marks, prev_grade = :prev_grade, reason_for_leaving = :reason_for_leaving
                WHERE id = :id AND school_id = :school_id";

        $this->db->query($sql);
        $this->db->bind(':name', trim($data['name'] ?? $student->name));
        $this->db->bind(':email', trim($data['email'] ?? $student->email));
        $this->db->bind(':phone', trim($data['parent_phone'] ?? $student->phone));
        $this->db->bind(':roll_no', trim($data['roll_no'] ?? ''));
        $this->db->bind(':class_id', !empty($data['class_id']) ? (int)$data['class_id'] : null);
        $this->db->bind(':section_id', !empty($data['section_id']) ? (int)$data['section_id'] : null);
        $this->db->bind(':dob', !empty($data['dob']) ? $data['dob'] : null);
        $this->db->bind(':gender', !empty($data['gender']) ? $data['gender'] : 'Male');
        $this->db->bind(':parent_phone', trim($data['parent_phone'] ?? ''));
        $this->db->bind(':address', trim($data['address'] ?? ''));
        $this->db->bind(':reg_no', trim($data['reg_no'] ?? ''));
        $this->db->bind(':bform_cnic', trim($data['bform_cnic'] ?? ''));
        $this->db->bind(':blood_group', trim($data['blood_group'] ?? ''));
        $this->db->bind(':father_name', trim($data['father_name'] ?? ''));
        $this->db->bind(':father_cnic', trim($data['father_cnic'] ?? ''));
        $this->db->bind(':mother_name', trim($data['mother_name'] ?? ''));
        $this->db->bind(':guardian_name', trim($data['guardian_name'] ?? ''));
        $this->db->bind(':guardian_relation', trim($data['guardian_relation'] ?? ''));
        $this->db->bind(':previous_school', trim($data['previous_school'] ?? ($data['prev_school_name'] ?? ($student->previous_school ?? ''))));
        $this->db->bind(':admission_date', !empty($data['admission_date']) ? $data['admission_date'] : null);
        $this->db->bind(':status', !empty($data['status']) ? $data['status'] : 'Active');
        $this->db->bind(':family_id', trim($data['family_id'] ?? ''));
        $this->db->bind(':sibling_discount_percent', !empty($data['sibling_discount_percent']) ? (float)$data['sibling_discount_percent'] : 0.00);
        $this->db->bind(':custom_discount_amount', !empty($data['custom_discount_amount']) ? (float)$data['custom_discount_amount'] : 0.00);
        $this->db->bind(':concession_type', !empty($data['concession_type']) ? $data['concession_type'] : 'None');
        $this->db->bind(':academic_session_id', !empty($data['academic_session_id']) ? (int)$data['academic_session_id'] : null);

        // Demographic and Pakistani specific fields
        $this->db->bind(':is_fresh_admission', isset($data['is_fresh_admission']) ? (int)$data['is_fresh_admission'] : (isset($student->is_fresh_admission) ? (int)$student->is_fresh_admission : 1));
        $this->db->bind(':religion', trim($data['religion'] ?? ($student->religion ?? 'Islam')));
        $this->db->bind(':nationality', trim($data['nationality'] ?? ($student->nationality ?? 'Pakistani')));
        $this->db->bind(':mother_tongue', trim($data['mother_tongue'] ?? ($student->mother_tongue ?? 'Urdu')));
        $this->db->bind(':father_occupation', trim($data['father_occupation'] ?? ($student->father_occupation ?? '')));
        $this->db->bind(':father_income', trim($data['father_income'] ?? ($student->father_income ?? '')));
        $this->db->bind(':father_phone', trim($data['father_phone'] ?? ($data['parent_phone'] ?? ($student->father_phone ?? ''))));
        $this->db->bind(':mother_cnic', trim($data['mother_cnic'] ?? ($student->mother_cnic ?? '')));
        $this->db->bind(':mother_occupation', trim($data['mother_occupation'] ?? ($student->mother_occupation ?? '')));
        $this->db->bind(':guardian_cnic', trim($data['guardian_cnic'] ?? ($student->guardian_cnic ?? '')));
        $this->db->bind(':guardian_phone', trim($data['guardian_phone'] ?? ($student->guardian_phone ?? '')));
        $this->db->bind(':guardian_occupation', trim($data['guardian_occupation'] ?? ($student->guardian_occupation ?? '')));
        $this->db->bind(':permanent_address', trim($data['permanent_address'] ?? ($data['address'] ?? ($student->permanent_address ?? ''))));
        $this->db->bind(':city', trim($data['city'] ?? ($student->city ?? '')));
        $this->db->bind(':district', trim($data['district'] ?? ($student->district ?? '')));
        $this->db->bind(':tehsil', trim($data['tehsil'] ?? ($student->tehsil ?? '')));
        $this->db->bind(':special_needs', trim($data['special_needs'] ?? ($student->special_needs ?? '')));

        // Previous school / transfer credentials
        $this->db->bind(':prev_school_name', trim($data['prev_school_name'] ?? ($data['previous_school'] ?? ($student->prev_school_name ?? ''))));
        $this->db->bind(':prev_school_city', trim($data['prev_school_city'] ?? ($student->prev_school_city ?? '')));
        $this->db->bind(':prev_class', trim($data['prev_class'] ?? ($student->prev_class ?? '')));
        $this->db->bind(':prev_medium', trim($data['prev_medium'] ?? ($student->prev_medium ?? 'English')));
        $this->db->bind(':slc_number', trim($data['slc_number'] ?? ($student->slc_number ?? '')));
        $this->db->bind(':slc_date', !empty($data['slc_date']) ? $data['slc_date'] : ($student->slc_date ?? null));
        $this->db->bind(':prev_board_roll_no', trim($data['prev_board_roll_no'] ?? ($student->prev_board_roll_no ?? '')));
        $this->db->bind(':prev_marks_obtained', !empty($data['prev_marks_obtained']) ? (float)$data['prev_marks_obtained'] : (isset($student->prev_marks_obtained) ? (float)$student->prev_marks_obtained : 0.00));
        $this->db->bind(':prev_total_marks', !empty($data['prev_total_marks']) ? (float)$data['prev_total_marks'] : (isset($student->prev_total_marks) ? (float)$student->prev_total_marks : 0.00));
        $this->db->bind(':prev_grade', trim($data['prev_grade'] ?? ($student->prev_grade ?? '')));
        $this->db->bind(':reason_for_leaving', trim($data['reason_for_leaving'] ?? ($student->reason_for_leaving ?? '')));

        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);

        return $this->db->execute();
    }

    public function getStudents(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT students.*, users.name, users.email, classes.class_name, sections.section_name 
                          FROM students
                          JOIN users ON students.user_id = users.id
                          LEFT JOIN classes ON students.class_id = classes.id
                          LEFT JOIN sections ON students.section_id = sections.id
                          WHERE students.school_id = :school_id
                          ORDER BY classes.class_name, students.roll_no");
        $this->db->bind(':school_id', $schoolId);
        return $this->db->resultSet();
    }

    public function getStudentsPaginated($limit, $offset){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT students.*, users.name, users.email, classes.class_name, sections.section_name
                          FROM students
                          JOIN users ON students.user_id = users.id
                          LEFT JOIN classes ON students.class_id = classes.id
                          LEFT JOIN sections ON students.section_id = sections.id
                          WHERE students.school_id = :school_id
                          ORDER BY classes.class_name, students.roll_no
                          LIMIT :limit OFFSET :offset");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getStudentsByClassSection($class_id, $section_id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.*, u.name, u.email, c.class_name, sec.section_name 
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          JOIN classes c ON s.class_id = c.id
                          JOIN sections sec ON s.section_id = sec.id
                          WHERE s.class_id = :cid AND s.section_id = :secid AND s.school_id = :school_id
                          ORDER BY s.roll_no");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':cid', $class_id);
        $this->db->bind(':secid', $section_id);
        return $this->db->resultSet();
    }

    public function getStudentsByClass($class_id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.*, u.name, u.email, c.class_name, sec.section_name 
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE s.class_id = :cid AND s.school_id = :school_id
                          ORDER BY sec.section_name, s.roll_no");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':cid', (int)$class_id);
        return $this->db->resultSet();
    }

    public function getStudentByUserId($user_id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.*, u.name, u.email, c.class_name, sec.section_name 
                          FROM students s 
                          JOIN users u ON s.user_id = u.id
                          JOIN classes c ON s.class_id = c.id
                          JOIN sections sec ON s.section_id = sec.id
                          WHERE s.user_id = :uid AND s.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':uid', $user_id);
        return $this->db->single();
    }

    public function searchStudentsByKeyword($keyword){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT students.*, users.name, users.email, classes.class_name, sections.section_name 
                          FROM students
                          JOIN users ON students.user_id = users.id
                          LEFT JOIN classes ON students.class_id = classes.id
                          LEFT JOIN sections ON students.section_id = sections.id
                          WHERE (users.name LIKE :k1 OR students.admission_no LIKE :k2 OR students.roll_no LIKE :k3 OR students.bform_cnic LIKE :k4 OR students.father_name LIKE :k5)
                          AND students.school_id = :school_id
                          ORDER BY classes.class_name, students.roll_no");
        $term = "%$keyword%";
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':k1', $term);
        $this->db->bind(':k2', $term);
        $this->db->bind(':k3', $term);
        $this->db->bind(':k4', $term);
        $this->db->bind(':k5', $term);
        return $this->db->resultSet();
    }

    public function getStudentById($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.*, u.name, u.email, c.class_name, sec.section_name, acs.session_name 
                          FROM students s 
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          LEFT JOIN academic_sessions acs ON s.academic_session_id = acs.id
                          WHERE s.id = :id AND s.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', (int)$id);
        return $this->db->single();
    }

    public function getStudentByAdmissionNo($admission_no){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.*, u.name, u.email, c.class_name, sec.section_name, acs.session_name 
                          FROM students s 
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          LEFT JOIN academic_sessions acs ON s.academic_session_id = acs.id
                          WHERE s.admission_no = :admission_no AND s.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':admission_no', trim($admission_no));
        return $this->db->single();
    }

    // ==========================================
    // STUDENT 360° UNIFIED DATA AGGREGATOR
    // ==========================================
    public function getStudent360($student_id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $id = (int)$student_id;
        
        $student = $this->getStudentById($id);
        if (!$student) return null;

        // 1. Siblings in the same school
        $siblings = [];
        if (!empty($student->family_id) || !empty($student->father_cnic)) {
            $this->db->query("SELECT s.id, s.admission_no, s.roll_no, s.bform_cnic, s.status, u.name, c.class_name, sec.section_name 
                              FROM students s
                              JOIN users u ON s.user_id = u.id
                              LEFT JOIN classes c ON s.class_id = c.id
                              LEFT JOIN sections sec ON s.section_id = sec.id
                              WHERE s.school_id = :school_id 
                                AND s.id != :self_id 
                                AND ((s.family_id = :family_id AND :family_id != '') 
                                     OR (s.father_cnic = :cnic AND :cnic != ''))
                              ORDER BY s.id ASC");
            $this->db->bind(':school_id', $schoolId);
            $this->db->bind(':self_id', $id);
            $this->db->bind(':family_id', $student->family_id ?? '');
            $this->db->bind(':cnic', $student->father_cnic ?? '');
            $siblings = $this->db->resultSet();
        }

        // 2. Attendance Summary
        $this->db->query("SELECT 
                            COUNT(*) as total_days,
                            SUM(CASE WHEN attendance_type = 'Present' THEN 1 ELSE 0 END) as present_days,
                            SUM(CASE WHEN attendance_type = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                            SUM(CASE WHEN attendance_type = 'Late' THEN 1 ELSE 0 END) as late_days,
                            SUM(CASE WHEN attendance_type = 'Half Day' THEN 1 ELSE 0 END) as half_days
                          FROM student_attendance 
                          WHERE student_id = :id");
        $this->db->bind(':id', $id);
        $attSummary = $this->db->single();

        // Recent 15 Attendance Logs
        $this->db->query("SELECT date, attendance_type, remark FROM student_attendance WHERE student_id = :id ORDER BY date DESC LIMIT 15");
        $this->db->bind(':id', $id);
        $attendanceLogs = $this->db->resultSet();

        // 3. Fees Overview & Ledger
        $this->db->query("SELECT sf.id as student_fee_id, sf.is_active, fgt.amount, fgt.due_date, fgt.fine_amount, 
                                 ft.type_name, ft.type_code, fg.group_name,
                                 COALESCE(SUM(fp.amount), 0) as total_paid
                          FROM student_fees sf
                          JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id
                          JOIN fee_types ft ON fgt.fee_type_id = ft.id
                          JOIN fee_groups fg ON fgt.fee_group_id = fg.id
                          LEFT JOIN fee_payments fp ON sf.id = fp.student_fee_id
                          WHERE sf.student_id = :id
                          GROUP BY sf.id, fgt.amount, fgt.due_date, fgt.fine_amount, ft.type_name, ft.type_code, fg.group_name, sf.is_active
                          ORDER BY fgt.due_date ASC");
        $this->db->bind(':id', $id);
        $fees = $this->db->resultSet();

        $totalAssigned = 0;
        $totalPaid = 0;
        foreach($fees as $f){
            $totalAssigned += (float)$f->amount;
            $totalPaid += (float)$f->total_paid;
        }

        // Apply student's sibling/custom discounts if applicable
        $discountRate = (float)($student->sibling_discount_percent ?? 0);
        $discountAmount = ($totalAssigned * ($discountRate / 100)) + (float)($student->custom_discount_amount ?? 0);
        $netPayable = max(0, $totalAssigned - $discountAmount);
        $balance = max(0, $netPayable - $totalPaid);

        // Payment Receipts / History
        $this->db->query("SELECT fp.*, ft.type_name
                          FROM fee_payments fp
                          JOIN student_fees sf ON fp.student_fee_id = sf.id
                          JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id
                          JOIN fee_types ft ON fgt.fee_type_id = ft.id
                          WHERE sf.student_id = :id
                          ORDER BY fp.payment_date DESC, fp.id DESC");
        $this->db->bind(':id', $id);
        $payments = $this->db->resultSet();

        // 4. Examination Results
        $this->db->query("SELECT er.*, er.get_marks as marks_obtained, es.exam_id, es.date_of_exam, es.full_marks, es.passing_marks, e.name as exam_name, s.subject_name
                          FROM exam_results er
                          JOIN exam_schedules es ON er.exam_schedule_id = es.id
                          JOIN exams e ON es.exam_id = e.id
                          JOIN subjects s ON es.subject_id = s.id
                          WHERE er.student_id = :id
                          ORDER BY es.date_of_exam DESC");
        $this->db->bind(':id', $id);
        $exams = $this->db->resultSet();

        return [
            'student' => $student,
            'siblings' => $siblings,
            'attendance' => [
                'summary' => $attSummary,
                'logs' => $attendanceLogs,
                'percentage' => ($attSummary && $attSummary->total_days > 0) ? round(($attSummary->present_days / $attSummary->total_days) * 100, 1) : 100
            ],
            'finance' => [
                'fees' => $fees,
                'payments' => $payments,
                'total_assigned' => $totalAssigned,
                'discount_percent' => $discountRate,
                'discount_amount' => $discountAmount,
                'net_payable' => $netPayable,
                'total_paid' => $totalPaid,
                'balance' => $balance
            ],
            'exams' => $exams
        ];
    }

    public function getLastAdmissionNo($yearPrefix){
        $this->db->query("SELECT admission_no FROM students WHERE admission_no LIKE :prefix ORDER BY id DESC LIMIT 1");
        $this->db->bind(':prefix', $yearPrefix . '%');
        return $this->db->single();
    }

    public function getLastRollNo($class_id, $section_id){
        $this->db->query("SELECT MAX(CAST(roll_no AS UNSIGNED)) as max_roll FROM students WHERE class_id = :cid AND section_id = :sid");
        $this->db->bind(':cid', (int)$class_id);
        $this->db->bind(':sid', (int)$section_id);
        $row = $this->db->single();
        return $row ? (int)$row->max_roll : 0;
    }

    public function getStudentsByParentId($parent_id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.*, u.name, u.email, c.class_name, sec.section_name 
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE s.parent_user_id = :pid AND s.school_id = :school_id
                          ORDER BY c.class_name, CAST(s.roll_no AS UNSIGNED) ASC");
        $this->db->bind(':pid', (int)$parent_id);
        $this->db->bind(':school_id', $schoolId);
        $students = $this->db->resultSet();

        if(empty($students)){
            // Fallback: check if parent user has username/email matching father_cnic or guardian_phone
            $this->db->query("SELECT email, phone FROM users WHERE id = :uid");
            $this->db->bind(':uid', (int)$parent_id);
            $pUser = $this->db->single();
            if($pUser){
                $this->db->query("SELECT s.*, u.name, u.email, c.class_name, sec.section_name 
                                  FROM students s
                                  JOIN users u ON s.user_id = u.id
                                  LEFT JOIN classes c ON s.class_id = c.id
                                  LEFT JOIN sections sec ON s.section_id = sec.id
                                  WHERE (s.father_cnic = :ident OR s.parent_phone = :phone) AND s.school_id = :school_id
                                  ORDER BY c.class_name, CAST(s.roll_no AS UNSIGNED) ASC");
                $this->db->bind(':ident', $pUser->email ?? '');
                $this->db->bind(':phone', $pUser->phone ?? '');
                $this->db->bind(':school_id', $schoolId);
                $students = $this->db->resultSet();
            }
        }
        return $students;
    }

    public function countStudents(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT COUNT(*) as total FROM students WHERE school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $row = $this->db->single();
        return $row ? (int)$row->total : 0;
    }

    public function deleteStudent($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT user_id FROM students WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        $student = $this->db->single();
        
        if(!$student){
            return false;
        }

        try {
            $this->db->query("DELETE FROM student_attendance WHERE student_id = :id");
            $this->db->bind(':id', (int)$id);
            $this->db->execute();
        } catch (Exception $e) {}

        try {
            $this->db->query("DELETE FROM exam_results WHERE student_id = :id");
            $this->db->bind(':id', (int)$id);
            $this->db->execute();
        } catch (Exception $e) {}

        try {
            $this->db->query("DELETE FROM student_fees WHERE student_id = :id");
            $this->db->bind(':id', (int)$id);
            $this->db->execute();
        } catch (Exception $e) {}

        $this->db->query("DELETE FROM students WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        $deleted = $this->db->execute();

        if($deleted && !empty($student->user_id)){
            try {
                $this->db->query("DELETE FROM users WHERE id = :uid");
                $this->db->bind(':uid', $student->user_id);
                $this->db->execute();
            } catch (Exception $e) {}
        }

        return (bool)$deleted;
    }

    public function deleteStudents(array $ids){
        $deletedCount = 0;
        foreach($ids as $id){
            if(!empty($id) && is_numeric($id)){
                if($this->deleteStudent((int)$id)){
                    $deletedCount++;
                }
            }
        }
        return $deletedCount;
    }
}
