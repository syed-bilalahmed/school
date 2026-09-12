<?php
class Clearance {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getNextClearanceNo(){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $year = date('Y');
        $this->db->query("SELECT COUNT(*) as total FROM student_clearances WHERE school_id = :sch AND YEAR(application_date) = :year");
        $this->db->bind(':sch', $schoolId);
        $this->db->bind(':year', $year);
        $res = $this->db->single();
        $next = ($res ? (int)$res->total : 0) + 1;
        
        $prefix = 'CLR-';
        if (class_exists('SiteSetting')) {
            $sm = new SiteSetting();
            $prefix = $sm->getPrefix('clearance', 'CLR-');
        }
        return $prefix . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function getStats(){
        $schoolId = TenantContext::getSchoolId() ?: 1;

        $this->db->query("SELECT COUNT(*) as total FROM student_clearances WHERE school_id = :sch");
        $this->db->bind(':sch', $schoolId);
        $totalRes = $this->db->single();
        $total = $totalRes ? (int)$totalRes->total : 0;

        $this->db->query("SELECT COUNT(*) as cnt FROM student_clearances WHERE school_id = :sch AND overall_status = 'In Progress'");
        $this->db->bind(':sch', $schoolId);
        $inProgRes = $this->db->single();
        $inProgress = $inProgRes ? (int)$inProgRes->cnt : 0;

        $this->db->query("SELECT COUNT(*) as cnt FROM student_clearances WHERE school_id = :sch AND overall_status = 'Fully Cleared'");
        $this->db->bind(':sch', $schoolId);
        $clearedRes = $this->db->single();
        $fullyCleared = $clearedRes ? (int)$clearedRes->cnt : 0;

        return [
            'total' => $total,
            'in_progress' => $inProgress,
            'fully_cleared' => $fullyCleared
        ];
    }

    public function getClearances($status = null){
        $sql = "SELECT sc.*, s.name as student_name, s.admission_no, s.roll_no, s.student_photo, s.father_name, s.bform_cnic, s.status as student_current_status,
                       c.class_name, sec.section_name, sess.session_name, u.name as principal_name
                FROM student_clearances sc
                JOIN students s ON sc.student_id = s.id
                LEFT JOIN classes c ON s.class_id = c.id
                LEFT JOIN sections sec ON s.section_id = sec.id
                LEFT JOIN academic_sessions sess ON sc.academic_session_id = sess.id
                LEFT JOIN users u ON sc.approved_by_principal_id = u.id
                WHERE sc.school_id = :school_id";
        if (!empty($status)) {
            $sql .= " AND sc.overall_status = :status";
        }
        $sql .= " ORDER BY sc.application_date DESC, sc.id DESC";

        $this->db->query($sql);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        if (!empty($status)) {
            $this->db->bind(':status', $status);
        }
        return $this->db->resultSet();
    }

    public function getClearanceById($id){
        $this->db->query("SELECT sc.*, s.name as student_name, s.admission_no, s.roll_no, s.student_photo, s.father_name, s.father_cnic, s.bform_cnic, s.status as student_current_status, s.phone as student_phone, s.dob, s.gender,
                                 c.class_name, sec.section_name, sess.session_name, u.name as principal_name, u.role as principal_role
                          FROM student_clearances sc
                          JOIN students s ON sc.student_id = s.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          LEFT JOIN academic_sessions sess ON sc.academic_session_id = sess.id
                          LEFT JOIN users u ON sc.approved_by_principal_id = u.id
                          WHERE sc.id = :id AND sc.school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->single();
    }

    public function getClearanceByStudentId($studentId){
        $this->db->query("SELECT * FROM student_clearances WHERE student_id = :sid AND school_id = :school_id ORDER BY id DESC LIMIT 1");
        $this->db->bind(':sid', $studentId);
        $this->db->bind(':school_id', TenantContext::getSchoolId() ?: 1);
        return $this->db->single();
    }

    public function initiateClearance($data){
        $clearanceNo = !empty($data['clearance_no']) ? $data['clearance_no'] : $this->getNextClearanceNo();
        $schoolId = TenantContext::getSchoolId() ?: 1;

        $this->db->query("INSERT INTO student_clearances (school_id, student_id, clearance_no, academic_session_id, reason_for_leaving, application_date, overall_status)
                          VALUES (:school_id, :sid, :cno, :sess, :reason, :app_date, 'In Progress')");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':sid', $data['student_id']);
        $this->db->bind(':cno', $clearanceNo);
        $this->db->bind(':sess', !empty($data['academic_session_id']) ? $data['academic_session_id'] : null);
        $this->db->bind(':reason', !empty($data['reason_for_leaving']) ? $data['reason_for_leaving'] : 'Completed Matriculation / Migration');
        $this->db->bind(':app_date', !empty($data['application_date']) ? $data['application_date'] : date('Y-m-d'));
        $success = $this->db->execute();

        if ($success) {
            // Update student's clearance_status flag
            $this->db->query("UPDATE students SET clearance_status = 'In Progress' WHERE id = :sid AND school_id = :school_id");
            $this->db->bind(':sid', $data['student_id']);
            $this->db->bind(':school_id', $schoolId);
            $this->db->execute();
        }

        return $success;
    }

    public function updateDepartmentSignoff($id, $department, $status, $remarks, $clearedBy){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $allowedDepts = ['accounts', 'library', 'lab', 'sports', 'class_teacher'];
        if (!in_array($department, $allowedDepts)) {
            return false;
        }

        $colStatus = $department . '_status';
        $colRemarks = $department . '_remarks';
        $colCleared = $department . '_cleared_by';

        $this->db->query("UPDATE student_clearances SET `$colStatus` = :st, `$colRemarks` = :rem, `$colCleared` = :clr WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':st', $status);
        $this->db->bind(':rem', $remarks);
        $this->db->bind(':clr', $clearedBy);
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', $schoolId);
        $updated = $this->db->execute();

        if ($updated) {
            $this->evaluateOverallClearance($id);
        }

        return $updated;
    }

    public function evaluateOverallClearance($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $clr = $this->getClearanceById($id);
        if (!$clr) return;

        $depts = [
            $clr->accounts_status,
            $clr->library_status,
            $clr->lab_status,
            $clr->sports_status,
            $clr->class_teacher_status
        ];

        $allCleared = true;
        foreach ($depts as $dStatus) {
            if ($dStatus !== 'Cleared' && $dStatus !== 'Waived') {
                $allCleared = false;
                break;
            }
        }

        if ($allCleared) {
            $today = date('Y-m-d');
            $this->db->query("UPDATE student_clearances SET overall_status = 'Fully Cleared', completion_date = :cdate WHERE id = :id AND school_id = :school_id");
            $this->db->bind(':cdate', $today);
            $this->db->bind(':id', $id);
            $this->db->bind(':school_id', $schoolId);
            $this->db->execute();

            $this->db->query("UPDATE students SET clearance_status = 'Cleared' WHERE id = :sid AND school_id = :school_id");
            $this->db->bind(':sid', $clr->student_id);
            $this->db->bind(':school_id', $schoolId);
            $this->db->execute();
        } else {
            $this->db->query("UPDATE student_clearances SET overall_status = 'In Progress', completion_date = NULL WHERE id = :id AND school_id = :school_id");
            $this->db->bind(':id', $id);
            $this->db->bind(':school_id', $schoolId);
            $this->db->execute();

            $this->db->query("UPDATE students SET clearance_status = 'In Progress' WHERE id = :sid AND school_id = :school_id");
            $this->db->bind(':sid', $clr->student_id);
            $this->db->bind(':school_id', $schoolId);
            $this->db->execute();
        }
    }

    public function principalReleaseStudent($id, $principalUserId){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $clr = $this->getClearanceById($id);
        if (!$clr) return false;

        $this->db->query("UPDATE student_clearances SET approved_by_principal_id = :pid, approved_at = NOW(), overall_status = 'Fully Cleared' WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':pid', $principalUserId);
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', $schoolId);
        $this->db->execute();

        // Update student status to 'Left' or 'Alumni'
        $this->db->query("UPDATE students SET status = 'Left', clearance_status = 'Cleared' WHERE id = :sid AND school_id = :school_id");
        $this->db->bind(':sid', $clr->student_id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // Automated Check of Dues and Library
    public function getAutomatedVitalsCheck($studentId){
        $schoolId = TenantContext::getSchoolId() ?: 1;

        // 1. Fee Balance
        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total_fees FROM student_fees WHERE student_id = :sid AND school_id = :school_id");
        $this->db->bind(':sid', $studentId);
        $this->db->bind(':school_id', $schoolId);
        $resFee = $this->db->single();
        $totalFees = $resFee ? (float)$resFee->total_fees : 0.00;

        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total_paid FROM fee_payments WHERE student_id = :sid AND school_id = :school_id");
        $this->db->bind(':sid', $studentId);
        $this->db->bind(':school_id', $schoolId);
        $resPay = $this->db->single();
        $totalPaid = $resPay ? (float)$resPay->total_paid : 0.00;

        $duesBalance = $totalFees - $totalPaid;

        // 2. Library Books Check (check if book_issues exists)
        $borrowedBooks = 0;
        try {
            $this->db->query("SELECT COUNT(*) as cnt FROM book_issues WHERE student_id = :sid AND is_returned = 0");
            $this->db->bind(':sid', $studentId);
            $resLib = $this->db->single();
            if ($resLib) $borrowedBooks = (int)$resLib->cnt;
        } catch (Exception $e) {
            $borrowedBooks = 0;
        }

        return [
            'dues_balance' => max(0, $duesBalance),
            'is_fee_clear' => ($duesBalance <= 0),
            'borrowed_books' => $borrowedBooks,
            'is_library_clear' => ($borrowedBooks == 0)
        ];
    }
}
