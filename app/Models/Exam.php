<?php
// app/Models/Exam.php

class Exam {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // --- Exams Management ---
    public function addExam($data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "INSERT INTO exams (school_id, academic_session_id, name, exam_type, start_date, end_date, description, is_active) 
                VALUES (:school_id, :sess_id, :name, :type, :start, :end, :desc, :active)";
        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':sess_id', !empty($data['academic_session_id']) ? (int)$data['academic_session_id'] : null);
        $this->db->bind(':name', trim($data['name']));
        $this->db->bind(':type', trim($data['exam_type'] ?? 'Term Exam'));
        $this->db->bind(':start', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':end', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':desc', trim($data['description'] ?? ''));
        $this->db->bind(':active', isset($data['is_active']) ? (int)$data['is_active'] : 1);
        return $this->db->execute();
    }

    public function updateExam($id, $data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "UPDATE exams SET 
                    academic_session_id = :sess_id, name = :name, exam_type = :type,
                    start_date = :start, end_date = :end, description = :desc, is_active = :active
                WHERE id = :id AND school_id = :school_id";
        $this->db->query($sql);
        $this->db->bind(':sess_id', !empty($data['academic_session_id']) ? (int)$data['academic_session_id'] : null);
        $this->db->bind(':name', trim($data['name']));
        $this->db->bind(':type', trim($data['exam_type'] ?? 'Term Exam'));
        $this->db->bind(':start', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':end', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':desc', trim($data['description'] ?? ''));
        $this->db->bind(':active', isset($data['is_active']) ? (int)$data['is_active'] : 1);
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    public function getExams($sessionId = null){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "SELECT e.*, acs.session_name,
                       (SELECT COUNT(*) FROM exam_schedules es WHERE es.exam_id = e.id) as total_schedules
                FROM exams e
                LEFT JOIN academic_sessions acs ON e.academic_session_id = acs.id
                WHERE e.school_id = :school_id ";
        if(!empty($sessionId)){
            $sql .= " AND e.academic_session_id = :sess_id ";
        }
        $sql .= " ORDER BY e.id DESC";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if(!empty($sessionId)) $this->db->bind(':sess_id', (int)$sessionId);
        return $this->db->resultSet();
    }

    public function getExamById($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT e.*, acs.session_name 
                          FROM exams e 
                          LEFT JOIN academic_sessions acs ON e.academic_session_id = acs.id
                          WHERE e.id = :id AND e.school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->single();
    }

    public function deleteExam($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("DELETE FROM exams WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // --- Schedules Management ---
    public function addSchedule($data){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "INSERT INTO exam_schedules (
                    school_id, exam_id, class_id, section_id, subject_id, date_of_exam,
                    start_time, end_time, room_no, full_marks, passing_marks, theory_marks, practical_marks, approval_status
                ) VALUES (
                    :school_id, :eid, :cid, :secid, :subid, :date,
                    :start, :end, :room, :full, :pass, :theory, :practical, 'draft'
                )";
        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':eid', (int)$data['exam_id']);
        $this->db->bind(':cid', (int)$data['class_id']);
        $this->db->bind(':secid', (int)$data['section_id']);
        $this->db->bind(':subid', (int)$data['subject_id']);
        $this->db->bind(':date', $data['date_of_exam']);
        $this->db->bind(':start', $data['start_time']);
        $this->db->bind(':end', $data['end_time']);
        $this->db->bind(':room', trim($data['room_no'] ?? ''));
        $this->db->bind(':full', (float)($data['full_marks'] ?? 100));
        $this->db->bind(':pass', (float)($data['passing_marks'] ?? 33));
        $this->db->bind(':theory', (float)($data['theory_marks'] ?? 75));
        $this->db->bind(':practical', (float)($data['practical_marks'] ?? 25));
        return $this->db->execute();
    }

    public function getSchedulesByExam($exam_id, $class_id = null, $section_id = null){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "SELECT es.*, COALESCE(s.subject_name, s.name) as subject_name, COALESCE(s.subject_code, s.code) as subject_code, c.class_name, sec.section_name,
                       u_sub.name as submitter_name, u_rev.name as reviewer_name, u_app.name as approver_name
                FROM exam_schedules es
                JOIN subjects s ON es.subject_id = s.id
                JOIN classes c ON es.class_id = c.id
                JOIN sections sec ON es.section_id = sec.id
                LEFT JOIN users u_sub ON es.submitted_by_teacher_id = u_sub.id
                LEFT JOIN users u_rev ON es.reviewed_by_class_teacher_id = u_rev.id
                LEFT JOIN users u_app ON es.approved_by_principal_id = u_app.id
                WHERE es.exam_id = :eid AND es.school_id = :school_id";
        
        if(!empty($class_id)){
            $sql .= " AND es.class_id = :cid";
        }
        if(!empty($section_id)){
            $sql .= " AND es.section_id = :secid";
        }

        $sql .= " ORDER BY es.date_of_exam ASC, es.start_time ASC";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':eid', (int)$exam_id);
        if(!empty($class_id)) $this->db->bind(':cid', (int)$class_id);
        if(!empty($section_id)) $this->db->bind(':secid', (int)$section_id);
        return $this->db->resultSet();
    }

    public function getScheduleById($id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT es.*, ex.name as exam_name, ex.exam_type, COALESCE(s.subject_name, s.name) as subject_name, COALESCE(s.subject_code, s.code) as subject_code,
                                 c.class_name, sec.section_name, acs.session_name,
                                 sec.class_teacher_id,
                                 u_teach.name as class_teacher_name
                          FROM exam_schedules es
                          JOIN exams ex ON es.exam_id = ex.id
                          LEFT JOIN academic_sessions acs ON ex.academic_session_id = acs.id
                          JOIN subjects s ON es.subject_id = s.id
                          JOIN classes c ON es.class_id = c.id
                          JOIN sections sec ON es.section_id = sec.id
                          LEFT JOIN users u_teach ON sec.class_teacher_id = u_teach.id
                          WHERE es.id = :id AND es.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', (int)$id);
        return $this->db->single();
    }

    // --- Marks Entry & Students ---
    public function getStudentsForMarksEntry($schedule_id, $class_id, $section_id){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT s.id as student_id, u.name, s.roll_no, s.admission_no, s.bform_cnic, s.father_name,
                                 er.id as result_id, er.theory_marks, er.practical_marks, er.get_marks, er.is_absent, er.remarks
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN exam_results er ON s.id = er.student_id AND er.exam_schedule_id = :sid
                          WHERE s.class_id = :cid AND s.section_id = :secid AND s.school_id = :school_id
                          ORDER BY CAST(s.roll_no AS UNSIGNED) ASC, u.name ASC");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':sid', (int)$schedule_id);
        $this->db->bind(':cid', (int)$class_id);
        $this->db->bind(':secid', (int)$section_id);
        return $this->db->resultSet();
    }

    public function saveMarks($schedule_id, $student_id, $marks, $is_absent, $theory = 0, $practical = 0, $remarks = ''){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "INSERT INTO exam_results (
                    school_id, exam_schedule_id, student_id, theory_marks, practical_marks, get_marks, is_absent, remarks
                ) VALUES (
                    :school_id, :sid, :stid, :theory, :practical, :marks, :absent, :rem
                )
                ON DUPLICATE KEY UPDATE
                    theory_marks = :theory_up, practical_marks = :practical_up,
                    get_marks = :marks_up, is_absent = :absent_up, remarks = :rem_up";
        
        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':sid', (int)$schedule_id);
        $this->db->bind(':stid', (int)$student_id);
        $this->db->bind(':theory', (float)$theory);
        $this->db->bind(':practical', (float)$practical);
        $this->db->bind(':marks', (float)$marks);
        $this->db->bind(':absent', $is_absent);
        $this->db->bind(':rem', trim($remarks));

        $this->db->bind(':theory_up', (float)$theory);
        $this->db->bind(':practical_up', (float)$practical);
        $this->db->bind(':marks_up', (float)$marks);
        $this->db->bind(':absent_up', $is_absent);
        $this->db->bind(':rem_up', trim($remarks));
        return $this->db->execute();
    }

    // --- 4-TIER APPROVAL CHAIN STATE MACHINE ---
    // Tier 1: Subject Teacher Submits & Locks Marks
    public function submitToClassTeacher($scheduleId, $teacherId){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "UPDATE exam_schedules SET 
                    approval_status = 'submitted_to_class_teacher',
                    submitted_by_teacher_id = :uid,
                    submitted_at = NOW(),
                    rejection_reason = NULL
                WHERE id = :id AND school_id = :school_id";
        $this->db->query($sql);
        $this->db->bind(':uid', (int)$teacherId);
        $this->db->bind(':id', (int)$scheduleId);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // Tier 2: Class Teacher Verification
    public function reviewByClassTeacher($scheduleId, $classTeacherId){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "UPDATE exam_schedules SET 
                    approval_status = 'reviewed_by_class_teacher',
                    reviewed_by_class_teacher_id = :uid,
                    reviewed_at = NOW(),
                    rejection_reason = NULL
                WHERE id = :id AND school_id = :school_id";
        $this->db->query($sql);
        $this->db->bind(':uid', (int)$classTeacherId);
        $this->db->bind(':id', (int)$scheduleId);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // Tier 3: Vice Principal Academic Review
    public function verifyByVP($scheduleId, $vpId){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "UPDATE exam_schedules SET 
                    approval_status = 'verified_by_vp',
                    verified_by_vp_id = :uid,
                    verified_at = NOW(),
                    rejection_reason = NULL
                WHERE id = :id AND school_id = :school_id";
        $this->db->query($sql);
        $this->db->bind(':uid', (int)$vpId);
        $this->db->bind(':id', (int)$scheduleId);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // Tier 4: Principal Final Approval & Publication
    public function approveByPrincipal($scheduleId, $principalId, $publish = true){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $status = $publish ? 'published' : 'approved_by_principal';
        $sql = "UPDATE exam_schedules SET 
                    approval_status = :status,
                    approved_by_principal_id = :uid,
                    approved_at = NOW(),
                    rejection_reason = NULL
                WHERE id = :id AND school_id = :school_id";
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':uid', (int)$principalId);
        $this->db->bind(':id', (int)$scheduleId);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // Universal Rejection / Send Back for Revision (Unlocks for Teacher)
    public function rejectMarks($scheduleId, $userId, $reason){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "UPDATE exam_schedules SET 
                    approval_status = 'rejected',
                    rejection_reason = :reason
                WHERE id = :id AND school_id = :school_id";
        $this->db->query($sql);
        $this->db->bind(':reason', trim($reason));
        $this->db->bind(':id', (int)$scheduleId);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    public function getApprovalChainProgress($examId = null, $classId = null, $sectionId = null){
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $sql = "SELECT es.id, es.approval_status, es.exam_id, es.class_id, es.section_id,
                       COALESCE(s.subject_name, s.name) as subject_name, COALESCE(s.subject_code, s.code) as subject_code, c.class_name, sec.section_name,
                       ex.name as exam_name,
                       u_sub.name as submitter_name, es.submitted_at,
                       u_rev.name as reviewer_name, es.reviewed_at,
                       u_vp.name as vp_name, es.verified_at,
                       u_app.name as principal_name, es.approved_at,
                       es.rejection_reason
                FROM exam_schedules es
                JOIN exams ex ON es.exam_id = ex.id
                JOIN subjects s ON es.subject_id = s.id
                JOIN classes c ON es.class_id = c.id
                JOIN sections sec ON es.section_id = sec.id
                LEFT JOIN users u_sub ON es.submitted_by_teacher_id = u_sub.id
                LEFT JOIN users u_rev ON es.reviewed_by_class_teacher_id = u_rev.id
                LEFT JOIN users u_vp ON es.verified_by_vp_id = u_vp.id
                LEFT JOIN users u_app ON es.approved_by_principal_id = u_app.id
                WHERE es.school_id = :school_id ";
        
        if(!empty($examId)) $sql .= " AND es.exam_id = :eid ";
        if(!empty($classId)) $sql .= " AND es.class_id = :cid ";
        if(!empty($sectionId)) $sql .= " AND es.section_id = :secid ";

        $sql .= " ORDER BY c.class_name, sec.section_name, s.subject_name";

        $this->db->query($sql);
        $this->db->bind(':school_id', $schoolId);
        if(!empty($examId)) $this->db->bind(':eid', (int)$examId);
        if(!empty($classId)) $this->db->bind(':cid', (int)$classId);
        if(!empty($sectionId)) $this->db->bind(':secid', (int)$sectionId);
        return $this->db->resultSet();
    }

    // --- Hall Signature Sheet ---
    public function getHallSignatureSheet($scheduleId){
        $schedule = $this->getScheduleById($scheduleId);
        if(!$schedule) return null;

        $students = $this->getStudentsForMarksEntry($scheduleId, $schedule->class_id, $schedule->section_id);

        return [
            'schedule' => $schedule,
            'students' => $students
        ];
    }

    // ==========================================
    // PHASE 5: RESULT PROCESSING, GAZETTE & REPORT CARDS
    // ==========================================

    public static function getGradeAndGpa($percentage, $hasFailedSubject = false){
        if($hasFailedSubject || $percentage < 40.0){
            return [
                'grade' => 'F',
                'gpa' => 0.0,
                'remarks' => 'Fail / Compartment',
                'badge' => 'bg-danger'
            ];
        }
        if($percentage >= 80.0){
            return [
                'grade' => 'A+',
                'gpa' => 4.0,
                'remarks' => 'Exceptional / Outstanding',
                'badge' => 'bg-success'
            ];
        }
        if($percentage >= 70.0){
            return [
                'grade' => 'A',
                'gpa' => 3.7,
                'remarks' => 'Excellent',
                'badge' => 'bg-primary'
            ];
        }
        if($percentage >= 60.0){
            return [
                'grade' => 'B',
                'gpa' => 3.0,
                'remarks' => 'Very Good',
                'badge' => 'bg-info text-dark'
            ];
        }
        if($percentage >= 50.0){
            return [
                'grade' => 'C',
                'gpa' => 2.0,
                'remarks' => 'Good / Satisfactory',
                'badge' => 'bg-warning text-dark'
            ];
        }
        return [
            'grade' => 'D',
            'gpa' => 1.0,
            'remarks' => 'Fair / Pass',
            'badge' => 'bg-secondary'
        ];
    }

    public function getClassGazette($examId, $classId, $sectionId){
        $schoolId = TenantContext::getSchoolId() ?: 1;

        // 1. Exam Info
        $this->db->query("SELECT ex.*, acs.session_name 
                          FROM exams ex 
                          LEFT JOIN academic_sessions acs ON ex.academic_session_id = acs.id 
                          WHERE ex.id = :eid AND ex.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':eid', (int)$examId);
        $exam = $this->db->single();
        if(!$exam) return null;

        // 2. Class & Section Info
        $this->db->query("SELECT c.class_name, sec.section_name, sec.class_teacher_id, u.name as class_teacher_name
                          FROM classes c 
                          JOIN sections sec ON sec.class_id = c.id
                          LEFT JOIN users u ON sec.class_teacher_id = u.id
                          WHERE c.id = :cid AND sec.id = :secid AND c.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':cid', (int)$classId);
        $this->db->bind(':secid', (int)$sectionId);
        $classSec = $this->db->single();
        if(!$classSec) return null;

        // 3. Scheduled Papers for this Exam and Class Section
        $this->db->query("SELECT es.*, COALESCE(s.subject_name, s.name) as subject_name, COALESCE(s.subject_code, s.code) as subject_code, s.is_core
                          FROM exam_schedules es
                          JOIN subjects s ON es.subject_id = s.id
                          WHERE es.exam_id = :eid AND es.class_id = :cid AND es.section_id = :secid AND es.school_id = :school_id
                          ORDER BY es.date_of_exam ASC, COALESCE(s.subject_name, s.name) ASC");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':eid', (int)$examId);
        $this->db->bind(':cid', (int)$classId);
        $this->db->bind(':secid', (int)$sectionId);
        $schedules = $this->db->resultSet();

        // 4. Enrolled Students
        $this->db->query("SELECT s.id as student_id, u.name, s.roll_no, s.admission_no, s.bform_cnic, s.father_name, s.student_photo
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          WHERE s.class_id = :cid AND s.section_id = :secid AND s.school_id = :school_id
                          ORDER BY CAST(s.roll_no AS UNSIGNED) ASC, u.name ASC");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':cid', (int)$classId);
        $this->db->bind(':secid', (int)$sectionId);
        $students = $this->db->resultSet();

        // 5. Results Map
        $this->db->query("SELECT er.*, es.subject_id
                          FROM exam_results er
                          JOIN exam_schedules es ON er.exam_schedule_id = es.id
                          WHERE es.exam_id = :eid AND es.class_id = :cid AND es.section_id = :secid AND er.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':eid', (int)$examId);
        $this->db->bind(':cid', (int)$classId);
        $this->db->bind(':secid', (int)$sectionId);
        $rawResults = $this->db->resultSet();

        $resultsMap = [];
        foreach($rawResults as $r){
            $resultsMap[$r->student_id][$r->exam_schedule_id] = $r;
        }

        // 6. Aggregate Student Performances
        $totalPaperMaxSum = 0;
        foreach($schedules as $sch){
            $totalPaperMaxSum += (float)$sch->full_marks;
        }

        $processedStudents = [];
        $totalClassScore = 0;
        $appearedCount = 0;
        $passedCount = 0;
        $failedCount = 0;

        foreach($students as $st){
            $studentScore = 0;
            $failedSubjects = 0;
            $subjectBreakdown = [];
            $hasAnyMark = false;
            $allAbsent = true;

            foreach($schedules as $sch){
                $res = $resultsMap[$st->student_id][$sch->id] ?? null;
                $isAbs = ($res && $res->is_absent === 'yes');
                $th = $res ? (float)$res->theory_marks : 0;
                $pr = $res ? (float)$res->practical_marks : 0;
                $tot = $res ? (float)$res->get_marks : ($th + $pr);
                $rem = $res ? $res->remarks : '';

                if($res !== null) $hasAnyMark = true;
                if(!$isAbs && $res !== null) $allAbsent = false;

                $full = (float)$sch->full_marks;
                $pass = (float)$sch->passing_marks;
                $subPct = $full > 0 ? round(($tot / $full) * 100, 1) : 0;
                $subFailed = ($isAbs || $tot < $pass);
                if($subFailed) $failedSubjects++;

                $subGrade = self::getGradeAndGpa($subPct, $subFailed);

                $subjectBreakdown[$sch->id] = [
                    'schedule_id' => $sch->id,
                    'subject_id' => $sch->subject_id,
                    'subject_name' => $sch->subject_name,
                    'subject_code' => $sch->subject_code,
                    'full_marks' => $full,
                    'theory_marks' => $th,
                    'practical_marks' => $pr,
                    'obtained_marks' => $tot,
                    'passing_marks' => $pass,
                    'is_absent' => $isAbs,
                    'percentage' => $subPct,
                    'grade' => $subGrade['grade'],
                    'gpa' => $subGrade['gpa'],
                    'is_failed' => $subFailed,
                    'remarks' => $rem
                ];

                if(!$isAbs){
                    $studentScore += $tot;
                }
            }

            $overallPct = $totalPaperMaxSum > 0 ? round(($studentScore / $totalPaperMaxSum) * 100, 2) : 0;
            $hasFailed = ($failedSubjects > 0 || $overallPct < 40.0 || $allAbsent);
            $gradeObj = self::getGradeAndGpa($overallPct, $hasFailed);

            if($hasAnyMark && !$allAbsent){
                $appearedCount++;
                $totalClassScore += $studentScore;
                if($hasFailed) $failedCount++;
                else $passedCount++;
            }

            $processedStudents[] = [
                'student_id' => $st->student_id,
                'name' => $st->name,
                'roll_no' => $st->roll_no,
                'admission_no' => $st->admission_no,
                'bform_cnic' => $st->bform_cnic,
                'father_name' => $st->father_name,
                'student_photo' => $st->student_photo,
                'subjects' => $subjectBreakdown,
                'total_obtained' => $studentScore,
                'total_full_marks' => $totalPaperMaxSum,
                'percentage' => $overallPct,
                'failed_subjects' => $failedSubjects,
                'status' => $hasFailed ? 'FAIL' : 'PASS',
                'grade' => $gradeObj['grade'],
                'gpa' => $gradeObj['gpa'],
                'grade_remarks' => $gradeObj['remarks'],
                'grade_badge' => $gradeObj['badge'],
                'position' => 0,
                'position_badge' => ''
            ];
        }

        // 7. Calculate Ranks / Positions
        // Sorting: Passing students ranked first by total marks descending, then failed students
        usort($processedStudents, function($a, $b){
            if($a['status'] !== $b['status']){
                return $a['status'] === 'PASS' ? -1 : 1;
            }
            if($b['total_obtained'] != $a['total_obtained']){
                return $b['total_obtained'] <=> $a['total_obtained'];
            }
            return $b['percentage'] <=> $a['percentage'];
        });

        $rank = 1;
        $podium = [];
        foreach($processedStudents as $k => &$pst){
            if($pst['status'] === 'PASS'){
                $pst['position'] = $rank;
                if($rank === 1){
                    $pst['position_badge'] = '🥇 1st Position';
                    $podium[] = $pst;
                } elseif($rank === 2){
                    $pst['position_badge'] = '🥈 2nd Position';
                    $podium[] = $pst;
                } elseif($rank === 3){
                    $pst['position_badge'] = '🥉 3rd Position';
                    $podium[] = $pst;
                } else {
                    $pst['position_badge'] = $rank . 'th';
                }
                $rank++;
            } else {
                $pst['position'] = null;
                $pst['position_badge'] = 'N/A';
            }
        }
        unset($pst);

        // Sort back by Roll No for gazette display if desired, or provide both
        $sortedByRoll = $processedStudents;
        usort($sortedByRoll, function($a, $b){
            return (int)$a['roll_no'] <=> (int)$b['roll_no'];
        });

        $highestMarks = !empty($processedStudents) ? $processedStudents[0]['total_obtained'] : 0;
        $classAverage = $appearedCount > 0 ? round($totalClassScore / $appearedCount, 2) : 0;
        $passPercentage = $appearedCount > 0 ? round(($passedCount / $appearedCount) * 100, 1) : 0;

        return [
            'exam' => $exam,
            'class' => $classSec,
            'class_id' => (int)$classId,
            'section_id' => (int)$sectionId,
            'schedules' => $schedules,
            'total_full_marks' => $totalPaperMaxSum,
            'students' => $sortedByRoll, // Roster sorted by roll no with positions embedded
            'merit_list' => $processedStudents, // Roster sorted by position/merit
            'podium' => $podium,
            'summary' => [
                'total_enrolled' => count($students),
                'total_appeared' => $appearedCount,
                'total_passed' => $passedCount,
                'total_failed' => $failedCount,
                'pass_percentage' => $passPercentage,
                'highest_marks' => $highestMarks,
                'class_average' => $classAverage
            ]
        ];
    }

    public function getStudentReportCard($examId, $studentId){
        $schoolId = TenantContext::getSchoolId() ?: 1;

        // Fetch Student Info
        $this->db->query("SELECT s.*, u.name, u.email, c.class_name, sec.section_name, acs.session_name,
                                 sec.class_teacher_id, u_teach.name as class_teacher_name
                          FROM students s
                          JOIN users u ON s.user_id = u.id
                          LEFT JOIN classes c ON s.class_id = c.id
                          LEFT JOIN sections sec ON s.section_id = sec.id
                          LEFT JOIN users u_teach ON sec.class_teacher_id = u_teach.id
                          LEFT JOIN academic_sessions acs ON s.academic_session_id = acs.id
                          WHERE s.id = :id AND s.school_id = :school_id");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':id', (int)$studentId);
        $student = $this->db->single();
        if(!$student) return null;

        // Fetch Class Gazette to ensure position and class statistics match perfectly
        $gazette = $this->getClassGazette($examId, $student->class_id, $student->section_id);
        if(!$gazette) return null;

        // Locate this student in gazette
        $studentGazetteData = null;
        foreach($gazette['merit_list'] as $pst){
            if($pst['student_id'] == $student->id){
                $studentGazetteData = $pst;
                break;
            }
        }

        // Attendance stats
        $this->db->query("SELECT COUNT(*) as total_days,
                                 SUM(CASE WHEN attendance_type = 'Present' THEN 1 ELSE 0 END) as present_days,
                                 SUM(CASE WHEN attendance_type = 'Absent' THEN 1 ELSE 0 END) as absent_days
                          FROM student_attendance 
                          WHERE student_id = :id");
        $this->db->bind(':id', (int)$studentId);
        $att = $this->db->single();
        $attPct = ($att && $att->total_days > 0) ? round(($att->present_days / $att->total_days) * 100, 1) : 100;

        // Auto-generate pedagogical remark
        $gpa = $studentGazetteData['gpa'] ?? 0;
        $grade = $studentGazetteData['grade'] ?? 'F';
        if($grade === 'A+'){
            $pedagogicalRemarks = "Exceptional academic performance! Demonstrated exemplary mastery and analytical brilliance across all subjects.";
        } elseif($grade === 'A'){
            $pedagogicalRemarks = "Outstanding achievement! Consistently exhibits strong work ethic, intellectual discipline, and academic dedication.";
        } elseif($grade === 'B'){
            $pedagogicalRemarks = "Very good progress. Capable of higher distinctions with dedicated attention to complex problem solving.";
        } elseif($grade === 'C'){
            $pedagogicalRemarks = "Satisfactory effort. Needs to focus on core concepts and consistent revision to improve grades.";
        } elseif($grade === 'D'){
            $pedagogicalRemarks = "Marginal pass. Urgent attention and parent-teacher collaboration required in weak subjects.";
        } else {
            $pedagogicalRemarks = "Unsatisfactory result. Failed to achieve minimum passing requirements. Remedial support and review required.";
        }

        return [
            'student' => $student,
            'exam' => $gazette['exam'],
            'class' => $gazette['class'],
            'performance' => $studentGazetteData,
            'class_summary' => $gazette['summary'],
            'attendance' => [
                'total_days' => $att ? $att->total_days : 0,
                'present_days' => $att ? $att->present_days : 0,
                'absent_days' => $att ? $att->absent_days : 0,
                'percentage' => $attPct
            ],
            'pedagogical_remarks' => $pedagogicalRemarks
        ];
    }
}
