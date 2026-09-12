<?php
class PromoteController extends Controller {
    public function __construct(){
        AuthGuard::requireAuth();
        AuthGuard::requireSchoolContext();
        if(!isset($_SESSION['user_id']) || (!in_array($_SESSION['user_role'], ['admin', 'super_admin', 'principal', 'vice_principal']))){
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    public function index(){
        $classModel = $this->model('SchoolClass');
        $sectionModel = $this->model('Section');
        $studentModel = $this->model('Student');
        $sessionModel = $this->model('AcademicSession');
        $examModel = $this->model('Exam');
        $db = new Database();

        $classes = $classModel->getClasses();
        $sections = $sectionModel->getSections();
        $sessions = $sessionModel->getSessions();
        $exams = $examModel->getExams();

        $students = [];
        $selectedClassId = $_GET['class_id'] ?? null;
        $selectedSectionId = $_GET['section_id'] ?? null;
        $selectedExamId = $_GET['exam_id'] ?? null;

        // Fetch students & evaluate exam performance
        if(!empty($selectedClassId) && !empty($selectedSectionId)){
            $students = $studentModel->getStudentsByClassSection($selectedClassId, $selectedSectionId);

            foreach($students as $st){
                $eval = [
                    'exam_name' => 'None',
                    'total_marks' => 0,
                    'obtained_marks' => 0,
                    'percentage' => 0,
                    'grade' => 'N/A',
                    'is_pass' => true,
                    'failed_subjects' => 0
                ];

                if(!empty($selectedExamId)){
                    $db->query("SELECT er.marks_obtained, es.full_marks, es.passing_marks, sub.subject_name
                                FROM exam_results er
                                JOIN exam_schedules es ON er.exam_schedule_id = es.id
                                JOIN subjects sub ON es.subject_id = sub.id
                                WHERE er.student_id = :sid AND es.exam_id = :eid");
                    $db->bind(':sid', $st->id);
                    $db->bind(':eid', $selectedExamId);
                    $results = $db->resultSet();

                    if(!empty($results)){
                        $totFull = 0;
                        $totObt = 0;
                        $failCount = 0;
                        foreach($results as $r){
                            $totFull += (float)$r->full_marks;
                            $totObt += (float)$r->marks_obtained;
                            if((float)$r->marks_obtained < (float)$r->passing_marks){
                                $failCount++;
                            }
                        }

                        $pct = $totFull > 0 ? round(($totObt / $totFull) * 100, 1) : 0;
                        $grade = 'F';
                        if($pct >= 80) $grade = 'A+';
                        elseif($pct >= 70) $grade = 'A';
                        elseif($pct >= 60) $grade = 'B';
                        elseif($pct >= 50) $grade = 'C';
                        elseif($pct >= 40) $grade = 'D';

                        $eval = [
                            'exam_name' => 'Selected Exam',
                            'total_marks' => $totFull,
                            'obtained_marks' => $totObt,
                            'percentage' => $pct,
                            'grade' => $grade,
                            'is_pass' => ($failCount == 0 && $pct >= 40),
                            'failed_subjects' => $failCount
                        ];
                    }
                }

                $st->exam_eval = $eval;
            }
        }

        // Handle POST Promotion Action
        $successMsg = null;
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['promote'])){
            $sourceClass = (int)$_POST['source_class_id'];
            $sourceSection = (int)$_POST['source_section_id'];
            $targetSession = (int)$_POST['target_session_id'];
            $targetClass = (int)$_POST['target_class_id'];
            $targetSection = (int)$_POST['target_section_id'];
            $selectedStudents = $_POST['students'] ?? [];
            $actions = $_POST['action'] ?? [];

            $promotedCount = 0;
            $schoolId = TenantContext::getSchoolId() ?: 1;
            $promotedBy = $_SESSION['user_id'] ?? 1;

            if(!empty($selectedStudents) && is_array($selectedStudents)){
                foreach($selectedStudents as $sid){
                    $studentId = (int)$sid;
                    $actionType = $actions[$studentId] ?? 'promote';

                    if($actionType == 'promote'){
                        $db->query("UPDATE students SET class_id = :cid, section_id = :sid, academic_session_id = :sess WHERE id = :id AND school_id = :sch");
                        $db->bind(':cid', $targetClass);
                        $db->bind(':sid', $targetSection);
                        $db->bind(':sess', $targetSession);
                        $db->bind(':id', $studentId);
                        $db->bind(':sch', $schoolId);
                        $db->execute();

                        $statusText = 'Promoted';
                    } elseif($actionType == 'repeat'){
                        // Kept in same class for new session
                        $db->query("UPDATE students SET academic_session_id = :sess WHERE id = :id AND school_id = :sch");
                        $db->bind(':sess', $targetSession);
                        $db->bind(':id', $studentId);
                        $db->bind(':sch', $schoolId);
                        $db->execute();

                        $statusText = 'Detained / Repeat';
                    } elseif($actionType == 'graduate'){
                        // Completed Class 10 / Alumni
                        $db->query("UPDATE students SET status = 'Graduated', clearance_status = 'Pending' WHERE id = :id AND school_id = :sch");
                        $db->bind(':id', $studentId);
                        $db->bind(':sch', $schoolId);
                        $db->execute();

                        $statusText = 'Graduated (Alumni)';
                    }

                    // Log the promotion
                    $db->query("INSERT INTO student_promotions_log (school_id, student_id, from_session_id, to_session_id, from_class_id, to_class_id, from_section_id, to_section_id, promotion_status, promoted_by_user_id)
                                VALUES (:sch, :sid, :fsess, :tsess, :fcls, :tcls, :fsec, :tsec, :pstatus, :pby)");
                    $db->bind(':sch', $schoolId);
                    $db->bind(':sid', $studentId);
                    $db->bind(':fsess', !empty($st->academic_session_id) ? $st->academic_session_id : null);
                    $db->bind(':tsess', $targetSession);
                    $db->bind(':fcls', $sourceClass);
                    $db->bind(':tcls', ($actionType == 'promote') ? $targetClass : $sourceClass);
                    $db->bind(':fsec', $sourceSection);
                    $db->bind(':tsec', ($actionType == 'promote') ? $targetSection : $sourceSection);
                    $db->bind(':pstatus', $statusText);
                    $db->bind(':pby', $promotedBy);
                    $db->execute();

                    $promotedCount++;
                }

                $successMsg = "Promotion Committee decision executed successfully for " . $promotedCount . " students!";
                $students = []; // Clear current list after successful promotion
            }
        }

        // Fetch recent promotion logs
        $db->query("SELECT spl.*, s.name as student_name, s.admission_no,
                           fc.class_name as from_class, tc.class_name as to_class,
                           ts.session_name as to_session
                    FROM student_promotions_log spl
                    JOIN students s ON spl.student_id = s.id
                    LEFT JOIN classes fc ON spl.from_class_id = fc.id
                    LEFT JOIN classes tc ON spl.to_class_id = tc.id
                    LEFT JOIN academic_sessions ts ON spl.to_session_id = ts.id
                    WHERE spl.school_id = :sch
                    ORDER BY spl.promoted_at DESC LIMIT 15");
        $db->bind(':sch', TenantContext::getSchoolId() ?: 1);
        $recentLogs = $db->resultSet();

        $data = [
            'classes' => $classes,
            'sections' => $sections,
            'sessions' => $sessions,
            'exams' => $exams,
            'students' => $students,
            'selected_class' => $selectedClassId,
            'selected_section' => $selectedSectionId,
            'selected_exam' => $selectedExamId,
            'success' => $successMsg,
            'recent_logs' => $recentLogs
        ];

        $this->view('promote/index', $data);
    }
}

if (!class_exists('Promotecontroller', false)) {
    class_alias('PromoteController', 'Promotecontroller');
}

