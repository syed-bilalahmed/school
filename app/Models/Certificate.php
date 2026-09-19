<?php
class Certificate {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function addCertificate($data){
        $this->db->query("INSERT INTO certificates (school_id, certificate_name, certificate_text, left_header, center_header, right_header, left_footer, center_footer, right_footer, background_image) 
                          VALUES (:school_id, :name, :text, :lh, :ch, :rh, :lf, :cf, :rf, :bg)");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':name', $data['certificate_name']);
        $this->db->bind(':text', $data['certificate_text']);
        $this->db->bind(':lh', $data['left_header']);
        $this->db->bind(':ch', $data['center_header']);
        $this->db->bind(':rh', $data['right_header']);
        $this->db->bind(':lf', $data['left_footer']);
        $this->db->bind(':cf', $data['center_footer']);
        $this->db->bind(':rf', $data['right_footer']);
        $this->db->bind(':bg', $data['background_image']);
        return $this->db->execute();
    }

    public function getCertificates(){
        $this->db->query("SELECT * FROM certificates WHERE school_id = :school_id ORDER BY id DESC");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->resultSet();
    }

    public function getCertificateById($id){
        $this->db->query("SELECT * FROM certificates WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', $id);
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        return $this->db->single();
    }

    public function deleteCertificate($id){
        $this->db->query("DELETE FROM certificates WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':school_id', TenantContext::getSchoolId());
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // ==========================================
    // PHASE 8: PAKISTANI CERTIFICATES & CREDENTIALS
    // ==========================================

    // Module 20: School Leaving Certificate (SLC) / Transfer Certificate (TC)
    public function getStudentSlcData($student_id, $overrideData = []) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $studentId = (int)$student_id;

        // Fetch student bio
        $this->db->query("SELECT s.*, u.name as student_name, u.email as student_email,
                                 c.class_name, sec.section_name
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE s.id = :sid AND s.school_id = :school_id LIMIT 1");
        $this->db->bind(':sid', $studentId);
        $this->db->bind(':school_id', $schoolId);
        $student = $this->db->single();

        if (!$student) return null;

        // Fetch School Info
        $this->db->query("SELECT * FROM schools WHERE id = :school_id LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $school = $this->db->single();

        // Active Session
        $this->db->query("SELECT session_name FROM academic_sessions WHERE school_id = :school_id AND is_current = 1 LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $sess = $this->db->single();
        $sessionName = $sess ? $sess->session_name : (date('Y') . '-' . (date('y') + 1));

        // Attendance stats
        $this->db->query("SELECT 
                            COUNT(*) as total_days,
                            SUM(CASE WHEN attendance_type = 'Present' THEN 1 ELSE 0 END) as present_days
                          FROM student_attendance 
                          WHERE student_id = :sid AND school_id = :school_id");
        $this->db->bind(':sid', $studentId);
        $this->db->bind(':school_id', $schoolId);
        $att = $this->db->single();
        $totalDays = $att ? (int)$att->total_days : 0;
        $presentDays = $att ? (int)$att->present_days : 0;

        // Financial status
        $this->db->query("SELECT 
                            SUM(fgt.amount + fgt.fine_amount) as total_fee,
                            (SELECT COALESCE(SUM(amount + discount), 0) FROM fee_payments WHERE student_fee_id IN (SELECT id FROM student_fees WHERE student_id = :sid1)) as total_paid
                          FROM student_fees sf
                          JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id
                          WHERE sf.student_id = :sid2 AND sf.school_id = :school_id");
        $this->db->bind(':sid1', $studentId);
        $this->db->bind(':sid2', $studentId);
        $this->db->bind(':school_id', $schoolId);
        $fin = $this->db->single();

        $balance = max(0, ((float)($fin->total_fee ?? 0)) - ((float)($fin->total_paid ?? 0)));
        $duesCleared = ($balance <= 0) ? 'All School Dues Cleared up to ' . date('F Y') : 'Outstanding Balance: Rs. ' . number_format($balance) . '/-';

        // Check if previously issued record exists
        $this->db->query("SELECT * FROM student_certificates WHERE student_id = :sid AND certificate_type = 'SLC' AND school_id = :school_id ORDER BY id DESC LIMIT 1");
        $this->db->bind(':sid', $studentId);
        $this->db->bind(':school_id', $schoolId);
        $existing = $this->db->single();

        $certNo = $existing ? $existing->certificate_no : ('SLC-' . date('Y') . '-' . str_pad($studentId, 4, '0', STR_PAD_LEFT));
        $issueDate = $existing ? $existing->issue_date : date('Y-m-d');
        $leavingDate = !empty($overrideData['leaving_date']) ? $overrideData['leaving_date'] : ($existing ? $existing->leaving_date : date('Y-m-d'));
        $reason = !empty($overrideData['reason_for_leaving']) ? $overrideData['reason_for_leaving'] : ($existing ? $existing->reason_for_leaving : "Parent's Request / Relocation");
        $conduct = !empty($overrideData['conduct']) ? $overrideData['conduct'] : ($existing ? $existing->conduct : 'Exemplary & Obedient');
        $promotedClass = !empty($overrideData['promoted_to_class']) ? $overrideData['promoted_to_class'] : ($existing ? $existing->promoted_to_class : 'Promoted to Next Higher Class');
        $remarks = !empty($overrideData['remarks']) ? $overrideData['remarks'] : ($existing ? $existing->remarks : 'Passed all assessments with satisfactory academic progress.');

        $dobWords = !empty($student->dob) ? self::dateToWords($student->dob) : 'Date of Birth on Record';

        return (object)[
            'certificate_no' => $certNo,
            'barcode' => 'SLC' . date('Y') . str_pad($studentId, 4, '0', STR_PAD_LEFT),
            'student' => $student,
            'school' => $school,
            'session_name' => $sessionName,
            'issue_date' => $issueDate,
            'leaving_date' => $leavingDate,
            'dob_in_words' => $dobWords,
            'attendance_ratio' => $presentDays . ' / ' . max(1, $totalDays) . ' Days (' . round(($presentDays / max(1, $totalDays)) * 100, 1) . '%)',
            'dues_cleared_text' => $duesCleared,
            'conduct' => $conduct,
            'reason_for_leaving' => $reason,
            'promoted_to_class' => $promotedClass,
            'remarks' => $remarks
        ];
    }

    // Module 20: Character & Conduct Certificate
    public function getStudentCharacterData($student_id, $overrideData = []) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $studentId = (int)$student_id;

        $this->db->query("SELECT s.*, u.name as student_name, c.class_name, sec.section_name
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE s.id = :sid AND s.school_id = :school_id LIMIT 1");
        $this->db->bind(':sid', $studentId);
        $this->db->bind(':school_id', $schoolId);
        $student = $this->db->single();
        if (!$student) return null;

        $this->db->query("SELECT * FROM schools WHERE id = :school_id LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $school = $this->db->single();

        $certNo = 'CC-' . date('Y') . '-' . str_pad($studentId, 4, '0', STR_PAD_LEFT);
        $conduct = !empty($overrideData['conduct']) ? $overrideData['conduct'] : 'Excellent & Exemplary';
        $coCurricular = !empty($overrideData['co_curricular']) ? $overrideData['co_curricular'] : 'Actively participated in Sports, Debates, and Academic Competitions.';
        $remarks = !empty($overrideData['remarks']) ? $overrideData['remarks'] : 'He/She bears a pleasing personality, respectful demeanor toward faculty, and high moral integrity.';

        return (object)[
            'certificate_no' => $certNo,
            'barcode' => 'CC' . date('Y') . str_pad($studentId, 4, '0', STR_PAD_LEFT),
            'student' => $student,
            'school' => $school,
            'issue_date' => date('Y-m-d'),
            'conduct' => $conduct,
            'co_curricular' => $coCurricular,
            'remarks' => $remarks
        ];
    }

    // Module 20: Bonafide / Enrollment Verification Certificate
    public function getStudentBonafideData($student_id, $overrideData = []) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $studentId = (int)$student_id;

        $this->db->query("SELECT s.*, u.name as student_name, c.class_name, sec.section_name
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE s.id = :sid AND s.school_id = :school_id LIMIT 1");
        $this->db->bind(':sid', $studentId);
        $this->db->bind(':school_id', $schoolId);
        $student = $this->db->single();
        if (!$student) return null;

        $this->db->query("SELECT * FROM schools WHERE id = :school_id LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $school = $this->db->single();

        $this->db->query("SELECT session_name FROM academic_sessions WHERE school_id = :school_id AND is_current = 1 LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $sess = $this->db->single();
        $sessionName = $sess ? $sess->session_name : (date('Y') . '-' . (date('y') + 1));

        $certNo = 'BC-' . date('Y') . '-' . str_pad($studentId, 4, '0', STR_PAD_LEFT);
        $purpose = !empty($overrideData['purpose']) ? $overrideData['purpose'] : 'Passport / Visa / NADRA Smart Card / Scholarship Verification';

        return (object)[
            'certificate_no' => $certNo,
            'barcode' => 'BC' . date('Y') . str_pad($studentId, 4, '0', STR_PAD_LEFT),
            'student' => $student,
            'school' => $school,
            'session_name' => $sessionName,
            'issue_date' => date('Y-m-d'),
            'purpose' => $purpose
        ];
    }

    // Module 21: Examination Roll Number Slip / Admit Card
    public function getAdmitCardData($student_id, $exam_id = null) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $studentId = (int)$student_id;

        // Fetch student bio
        $this->db->query("SELECT s.*, u.name as student_name, c.class_name, sec.section_name
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          WHERE s.id = :sid AND s.school_id = :school_id LIMIT 1");
        $this->db->bind(':sid', $studentId);
        $this->db->bind(':school_id', $schoolId);
        $student = $this->db->single();
        if (!$student) return null;

        // Fetch Exam
        if (!$exam_id) {
            $this->db->query("SELECT * FROM exams WHERE school_id = :school_id ORDER BY id DESC LIMIT 1");
            $this->db->bind(':school_id', $schoolId);
            $exam = $this->db->single();
        } else {
            $this->db->query("SELECT * FROM exams WHERE id = :id AND school_id = :school_id LIMIT 1");
            $this->db->bind(':id', (int)$exam_id);
            $this->db->bind(':school_id', $schoolId);
            $exam = $this->db->single();
        }

        if (!$exam) {
            $exam = (object)[
                'id' => 0,
                'name' => 'First Term Examination 2026-27',
                'exam_type' => 'Term Exam',
                'start_date' => date('Y-m-15'),
                'end_date' => date('Y-m-25')
            ];
        }

        // Fetch Papers Timetable Schedule for this Class & Exam
        $this->db->query("SELECT es.*, COALESCE(sub.subject_name, sub.name) as subject_name, COALESCE(sub.subject_code, sub.code) as subject_code, sub.is_core, sub.full_marks
                          FROM exam_schedules es
                          JOIN subjects sub ON es.subject_id = sub.id
                          WHERE es.exam_id = :eid AND es.class_id = :cid AND es.school_id = :school_id
                          ORDER BY es.date_of_exam ASC, es.start_time ASC");
        $this->db->bind(':eid', $exam->id);
        $this->db->bind(':cid', $student->class_id);
        $this->db->bind(':school_id', $schoolId);
        $papers = $this->db->resultSet();

        // If no scheduled papers in DB yet, generate realistic schedule so admit card is never blank
        if (empty($papers)) {
            $this->db->query("SELECT id, subject_name, subject_code, full_marks FROM subjects WHERE school_id = :school_id ORDER BY id ASC LIMIT 6");
            $this->db->bind(':school_id', $schoolId);
            $classSubs = $this->db->resultSet();

            $sampleDate = !empty($exam->start_date) ? strtotime($exam->start_date) : strtotime('+7 days');
            $papers = [];
            foreach ($classSubs as $idx => $cs) {
                $paperDate = date('Y-m-d', strtotime("+$idx days", $sampleDate));
                // Skip Sunday
                if (date('N', strtotime($paperDate)) == 7) {
                    $sampleDate = strtotime("+1 day", $sampleDate);
                    $paperDate = date('Y-m-d', strtotime("+$idx days", $sampleDate));
                }
                $papers[] = (object)[
                    'subject_name' => $cs->subject_name,
                    'subject_code' => $cs->subject_code,
                    'exam_date' => $paperDate,
                    'day' => date('l', strtotime($paperDate)),
                    'start_time' => '08:30:00',
                    'end_time' => '11:30:00',
                    'room_no' => 'Examination Hall #' . (floor($idx / 3) + 1),
                    'full_marks' => $cs->full_marks ?: 100.00
                ];
            }
        } else {
            foreach ($papers as &$p) {
                if (!isset($p->exam_date) && isset($p->date_of_exam)) {
                    $p->exam_date = $p->date_of_exam;
                }
                $p->day = !empty($p->exam_date) ? date('l', strtotime($p->exam_date)) : 'TBD';
                if (empty($p->room_no)) $p->room_no = 'Main Examination Hall';
            }
        }

        // Fetch School Info
        $this->db->query("SELECT * FROM schools WHERE id = :school_id LIMIT 1");
        $this->db->bind(':school_id', $schoolId);
        $school = $this->db->single();

        $admitCardNo = 'ADM-' . date('Y') . '-' . str_pad($studentId, 4, '0', STR_PAD_LEFT);

        return (object)[
            'admit_card_no' => $admitCardNo,
            'barcode' => 'ADM' . date('Y') . str_pad($studentId, 4, '0', STR_PAD_LEFT),
            'student' => $student,
            'exam' => $exam,
            'school' => $school,
            'papers' => $papers,
            'reporting_time' => '08:00 AM (Sharp)',
            'exam_timing' => '08:30 AM &ndash; 11:30 AM',
            'issue_date' => date('Y-m-d')
        ];
    }

    public function getClassAdmitCards($class_id, $exam_id = null) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT id FROM students WHERE class_id = :class_id AND school_id = :school_id ORDER BY roll_no ASC, id ASC");
        $this->db->bind(':class_id', (int)$class_id);
        $this->db->bind(':school_id', $schoolId);
        $students = $this->db->resultSet();

        $cards = [];
        if (!empty($students)) {
            foreach ($students as $s) {
                $card = $this->getAdmitCardData($s->id, $exam_id);
                if ($card) $cards[] = $card;
            }
        }
        return $cards;
    }

    public function logIssuedCertificate($data) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("INSERT INTO student_certificates (school_id, student_id, certificate_type, certificate_no, issue_date, leaving_date, reason_for_leaving, conduct, promoted_to_class, remarks)
                          VALUES (:sch, :sid, :type, :cno, :idate, :ldate, :reason, :conduct, :pclass, :rem)");
        $this->db->bind(':sch', $schoolId);
        $this->db->bind(':sid', (int)$data['student_id']);
        $this->db->bind(':type', trim($data['certificate_type']));
        $this->db->bind(':cno', trim($data['certificate_no']));
        $this->db->bind(':idate', $data['issue_date']);
        $this->db->bind(':ldate', $data['leaving_date'] ?? null);
        $this->db->bind(':reason', trim($data['reason_for_leaving'] ?? ''));
        $this->db->bind(':conduct', trim($data['conduct'] ?? ''));
        $this->db->bind(':pclass', trim($data['promoted_to_class'] ?? ''));
        $this->db->bind(':rem', trim($data['remarks'] ?? ''));
        return $this->db->execute();
    }

    public function getIssuedCertificatesLog($type = null) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "SELECT sc.*, u.name as student_name, s.admission_no, s.roll_no, c.class_name, sec.section_name
                FROM student_certificates sc
                JOIN students s ON sc.student_id = s.id
                JOIN users u ON s.user_id = u.id
                LEFT JOIN classes c ON s.class_id = c.id
                LEFT JOIN sections sec ON s.section_id = sec.id
                WHERE sc.school_id = :school_id";
        if ($type) {
            $sql .= " AND sc.certificate_type = :type";
        }
        $sql .= " ORDER BY sc.id DESC";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if ($type) {
            $this->db->bind(':type', $type);
        }
        return $this->db->resultSet();
    }

    public static function dateToWords($date) {
        if (empty($date)) return 'N/A';
        $timestamp = strtotime($date);
        $day = (int)date('j', $timestamp);
        $month = date('F', $timestamp);
        $year = (int)date('Y', $timestamp);

        $days = [
            1 => 'First', 2 => 'Second', 3 => 'Third', 4 => 'Fourth', 5 => 'Fifth',
            6 => 'Sixth', 7 => 'Seventh', 8 => 'Eighth', 9 => 'Ninth', 10 => 'Tenth',
            11 => 'Eleventh', 12 => 'Twelfth', 13 => 'Thirteenth', 14 => 'Fourteenth', 15 => 'Fifteenth',
            16 => 'Sixteenth', 17 => 'Seventeenth', 18 => 'Eighteenth', 19 => 'Nineteenth', 20 => 'Twentieth',
            21 => 'Twenty-First', 22 => 'Twenty-Second', 23 => 'Twenty-Third', 24 => 'Twenty-Fourth', 25 => 'Twenty-Fifth',
            26 => 'Twenty-Sixth', 27 => 'Twenty-Seventh', 28 => 'Twenty-Eighth', 29 => 'Twenty-Ninth', 30 => 'Thirtieth',
            31 => 'Thirty-First'
        ];

        $dayWord = $days[$day] ?? $day;
        $yearWord = self::yearToWords($year);

        return $dayWord . ' ' . $month . ' ' . $yearWord;
    }

    private static function yearToWords($year) {
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
        if ($year >= 2000) {
            $words = 'Two Thousand';
            $rem = $year - 2000;
            if ($rem > 0) {
                if ($rem < 20) {
                    $words .= ' and ' . $ones[$rem];
                } else {
                    $words .= ' and ' . $tens[(int)($rem / 10)] . ' ' . $ones[$rem % 10];
                }
            }
        } elseif ($year >= 1900) {
            $rem = $year - 1900;
            $words = 'Nineteen';
            if ($rem < 20) {
                $words .= ' ' . $ones[$rem];
            } else {
                $words .= ' ' . $tens[(int)($rem / 10)] . ' ' . $ones[$rem % 10];
            }
        }
        return trim($words);
    }

    // Get student fee balance for certificate clearance unlock
    public function getStudentFeeBalance($studentId){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT 
                            (SELECT COALESCE(SUM(fgt.amount + fgt.fine_amount), 0) FROM student_fees sf JOIN fee_groups_types fgt ON sf.fee_groups_types_id = fgt.id WHERE sf.student_id = :sid1 AND sf.school_id = :school_id) as total_assigned,
                            (SELECT COALESCE(SUM(fp.amount + fp.discount), 0) FROM fee_payments fp JOIN student_fees sf ON fp.student_fee_id = sf.id WHERE sf.student_id = :sid2 AND sf.school_id = :school_id) as total_paid");
        $this->db->bind(':sid1', (int)$studentId);
        $this->db->bind(':sid2', (int)$studentId);
        $this->db->bind(':school_id', $schoolId);
        $res = $this->db->single();
        if(!$res) return 0.00;
        return max(0.00, ((float)$res->total_assigned) - ((float)$res->total_paid));
    }
}

