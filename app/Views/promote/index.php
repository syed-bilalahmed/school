<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$classes = $data['classes'] ?? [];
$sections = $data['sections'] ?? [];
$sessions = $data['sessions'] ?? [];
$exams = $data['exams'] ?? [];
$students = $data['students'] ?? [];
$selectedClass = $data['selected_class'] ?? null;
$selectedSection = $data['selected_section'] ?? null;
$selectedExam = $data['selected_exam'] ?? null;
$recentLogs = $data['recent_logs'] ?? [];
?>

<!-- Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa fa-user-graduate text-primary me-2"></i>Student Session Promotion &amp; Roll-Over
        </h5>
        <div class="text-muted small">Evaluate student results &amp; roll over eligible students to next academic session</div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/sessions/index" class="btn btn-outline-primary btn-sm px-2 py-1" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-calendar-alt me-1"></i> Academic Sessions
        </a>
        <a href="<?php echo URLROOT; ?>/clearance/index" class="btn btn-outline-secondary btn-sm px-2 py-1" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-clipboard-check me-1"></i> Clearance Hub
        </a>
    </div>
</div>

<?php if(isset($data['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm small" style="border-radius: 10px;" role="alert">
        <i class="fa fa-check-circle me-1"></i>
        <strong>Success!</strong> <?php echo htmlspecialchars($data['success']); ?>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- 4 Compact KPI Cards -->
<?php
$totalCandidateStudents = count($students);
$passedStudents = 0;
$failedStudents = 0;
foreach($students as $s){
    $ev = $s->exam_eval ?? ['is_pass' => true];
    if($ev['is_pass']) $passedStudents++;
    else $failedStudents++;
}
$totalLogsCount = count($recentLogs);
?>
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-users"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">CANDIDATES</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $totalCandidateStudents; ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-user-check"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">ELIGIBLE (PASS)</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $passedStudents; ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-user-xmark"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">DETAINED (FAIL)</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $failedStudents; ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-history"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">PROMOTION LOGS</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $totalLogsCount; ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 1. SEARCH CRITERIA BAR -->
<div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
    <div class="card-body p-2 px-3">
        <form action="<?php echo URLROOT; ?>/promote/index" method="get" class="row g-2 align-items-center">
            <input type="hidden" name="search" value="1">
            
            <div class="col-md-4">
                <select name="class_id" id="classSelect" class="form-select form-select-sm" onchange="filterSections()" required style="border-radius: 6px;">
                    <option value="">-- Current Class --</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?php echo $c->id; ?>" <?php echo ($selectedClass == $c->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c->class_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <select name="section_id" id="sectionSelect" class="form-select form-select-sm" required style="border-radius: 6px;">
                    <option value="">-- Current Section --</option>
                    <?php foreach($sections as $s): ?>
                        <option value="<?php echo $s->id; ?>" data-class="<?php echo $s->class_id; ?>" class="section-option" <?php echo ($selectedSection == $s->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s->section_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <select name="exam_id" class="form-select form-select-sm" style="border-radius: 6px;">
                    <option value="">-- Benchmark Exam (Optional) --</option>
                    <?php foreach($exams as $ex): ?>
                        <option value="<?php echo $ex->id; ?>" <?php echo ($selectedExam == $ex->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ex->exam_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm" style="border-radius: 6px;">
                    <i class="fa fa-filter"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. PROMOTION COMMITTEE DECISION MATRIX -->
<?php if(!empty($students)): ?>
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-bold small text-dark">
                <i class="fa fa-user-graduate text-primary me-1"></i> Promotion Decision &amp; Target Placement
            </span>
            <span class="badge bg-light text-dark border px-2 py-1 font-monospace" style="font-size: 0.72rem;">
                <?php echo count($students); ?> Candidates
            </span>
        </div>

        <div class="card-body p-3">
            <form action="<?php echo URLROOT; ?>/promote/index" method="post">
                <input type="hidden" name="promote" value="1">
                <input type="hidden" name="source_class_id" value="<?php echo htmlspecialchars($selectedClass); ?>">
                <input type="hidden" name="source_section_id" value="<?php echo htmlspecialchars($selectedSection); ?>">

                <!-- TARGET DESTINATION CONTROLS -->
                <div class="p-2 px-3 bg-light rounded-2 border mb-3">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.75rem;">Next Session <span class="text-danger">*</span></label>
                            <select name="target_session_id" class="form-select form-select-sm" required style="border-radius: 6px;">
                                <?php foreach($sessions as $sess): ?>
                                    <option value="<?php echo $sess->id; ?>">
                                        <?php echo htmlspecialchars($sess->session_name); ?> <?php echo ($sess->is_current) ? '(Active)' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.75rem;">Target Class <span class="text-danger">*</span></label>
                            <select name="target_class_id" id="targetClassSelect" class="form-select form-select-sm" onchange="filterTargetSections()" required style="border-radius: 6px;">
                                <option value="">-- Choose Next Class --</option>
                                <?php foreach($classes as $c): ?>
                                    <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->class_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1" style="font-size: 0.75rem;">Target Section <span class="text-danger">*</span></label>
                            <select name="target_section_id" id="targetSectionSelect" class="form-select form-select-sm" required style="border-radius: 6px;">
                                <option value="">-- Choose Section --</option>
                                <?php foreach($sections as $s): ?>
                                    <option value="<?php echo $s->id; ?>" data-class="<?php echo $s->class_id; ?>" class="target-section-option" style="display:none;">
                                        <?php echo htmlspecialchars($s->section_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- STUDENT ROSTER & EXAM EVALUATION TABLE -->
                <div class="table-responsive mb-3" style="max-height: 380px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th width="40"><input type="checkbox" id="selectAll" checked></th>
                                <th>Admission #</th>
                                <th>Student Details</th>
                                <th>Father Name</th>
                                <th>Exam Performance</th>
                                <th>Eligibility Status</th>
                                <th style="width: 220px;">Committee Decision</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $s): 
                                $ev = $s->exam_eval ?? ['is_pass' => true, 'percentage' => 0, 'grade' => 'N/A', 'total_marks' => 0, 'obtained_marks' => 0];
                                $isPass = $ev['is_pass'];
                            ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="students[]" value="<?php echo $s->id; ?>" class="student-checkbox" <?php echo $isPass ? 'checked' : ''; ?>>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($s->admission_no); ?></span>
                                        <div class="text-muted fs-xs">Roll: <?php echo htmlspecialchars($s->roll_no ?: '-'); ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($s->name); ?></div>
                                        <div class="text-muted fs-xs">DOB: <?php echo !empty($s->dob) ? date('d-m-Y', strtotime($s->dob)) : 'N/A'; ?></div>
                                    </td>
                                    <td>
                                        <div class="text-dark"><?php echo htmlspecialchars($s->father_name ?: 'N/A'); ?></div>
                                    </td>
                                    <td>
                                        <?php if($ev['total_marks'] > 0): ?>
                                            <div class="fw-bold text-dark"><?php echo $ev['obtained_marks']; ?> / <?php echo $ev['total_marks']; ?></div>
                                            <div class="text-muted fs-xs">
                                                <span><?php echo $ev['percentage']; ?>%</span> &bull; 
                                                <strong class="text-primary"><?php echo $ev['grade']; ?> Grade</strong>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted fs-xs">No exam marks</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($ev['total_marks'] > 0): ?>
                                            <?php if($isPass): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                                    <i class="fa fa-check me-1"></i>Eligible (Passed)
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold">
                                                    <i class="fa fa-times me-1"></i>Failed (<?php echo $ev['failed_subjects']; ?> Subj)
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1">Manual Review</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select name="action[<?php echo $s->id; ?>]" class="form-select form-select-sm">
                                            <option value="promote" <?php echo $isPass ? 'selected' : ''; ?>>Promote to Next Class</option>
                                            <option value="repeat" <?php echo !$isPass ? 'selected' : ''; ?>>Detain / Repeat Class</option>
                                            <option value="graduate">Graduate (Alumni)</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        <i class="fa fa-info-circle me-1"></i> Checked students will have their session &amp; class updated.
                    </div>
                    <button type="submit" class="btn btn-success btn-sm px-3 py-2 shadow-sm fw-bold" style="border-radius: 6px;">
                        <i class="fa fa-level-up-alt me-1"></i> Execute Promotion Decision
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php elseif(isset($_GET['search'])): ?>
    <div class="alert alert-warning py-3 text-center mb-3 small" style="border-radius: 10px;">
        <i class="fa fa-user-slash fa-2x mb-1 text-warning d-block"></i>
        <strong>No Students Found:</strong> There are no active students enrolled in the selected class and section.
    </div>
<?php endif; ?>

<!-- 3. PROMOTION HISTORY LOG -->
<?php if(!empty($recentLogs)): ?>
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-bold small text-dark">
                <i class="fa fa-history text-primary me-1"></i> Recent Promotion History
            </span>
            <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1" style="border-radius: 20px; font-size: 0.72rem;">
                Audit Trail
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th class="ps-3 py-2">Student</th>
                            <th class="py-2">Admission #</th>
                            <th class="py-2">Transition</th>
                            <th class="py-2">Target Session</th>
                            <th class="py-2">Status</th>
                            <th class="text-end pe-3 py-2">Date Executed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentLogs as $log): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($log->student_name); ?></td>
                                <td><span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($log->admission_no); ?></span></td>
                                <td>
                                    <span class="text-muted"><?php echo htmlspecialchars($log->from_class ?? 'Previous'); ?></span>
                                    <i class="fa fa-arrow-right text-primary mx-2"></i>
                                    <strong class="text-dark"><?php echo htmlspecialchars($log->to_class ?? 'Target'); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-bold font-monospace"><?php echo htmlspecialchars($log->to_session ?? 'Next Session'); ?></span>
                                </td>
                                <td>
                                    <?php
                                    $pBadge = 'bg-success-subtle text-success';
                                    if ($log->promotion_status == 'Detained / Repeat') $pBadge = 'bg-danger-subtle text-danger';
                                    elseif ($log->promotion_status == 'Graduated (Alumni)') $pBadge = 'bg-primary-subtle text-primary';
                                    ?>
                                    <span class="badge <?php echo $pBadge; ?> fw-bold px-2 py-1"><?php echo htmlspecialchars($log->promotion_status); ?></span>
                                </td>
                                <td class="text-end pe-3 text-muted">
                                    <?php echo date('d M, Y &bull; h:i A', strtotime($log->promoted_at)); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    function filterSections(){
        var classId = document.getElementById('classSelect').value;
        var options = document.getElementsByClassName('section-option');
        for(var i=0; i<options.length; i++){
            if(options[i].getAttribute('data-class') == classId){
                options[i].style.display = 'block';
            } else {
                options[i].style.display = 'none';
            }
        }
    }

    function filterTargetSections(){
        var classId = document.getElementById('targetClassSelect').value;
        var options = document.getElementsByClassName('target-section-option');
        for(var i=0; i<options.length; i++){
            if(options[i].getAttribute('data-class') == classId){
                options[i].style.display = 'block';
            } else {
                options[i].style.display = 'none';
            }
        }
        document.getElementById('targetSectionSelect').value = "";
    }
    
    // Initial Filter on load
    if(document.getElementById('classSelect') && document.getElementById('classSelect').value) {
        filterSections();
    }

    // Select All Checkbox
    var selectAllBox = document.getElementById('selectAll');
    if (selectAllBox) {
        selectAllBox.addEventListener('change', function(){
            var checkboxes = document.getElementsByClassName('student-checkbox');
            for(var i=0; i<checkboxes.length; i++){
                checkboxes[i].checked = this.checked;
            }
        });
    }
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
