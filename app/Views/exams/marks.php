<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$examId = $data['exam_id'];
$scheduleInfo = $data['schedule_info'] ?? null;
$status = $scheduleInfo ? ($scheduleInfo->approval_status ?? 'draft') : null;
$isLocked = ($status && $status !== 'draft' && $status !== 'rejected');
$isRejected = ($status === 'rejected');

$theoryMax = $scheduleInfo ? (float)($scheduleInfo->theory_marks ?? 75) : 75;
$practicalMax = $scheduleInfo ? (float)($scheduleInfo->practical_marks ?? 25) : 25;
$fullMarks = $scheduleInfo ? (float)($scheduleInfo->full_marks ?? 100) : 100;
$passMarks = $scheduleInfo ? (float)($scheduleInfo->passing_marks ?? 33) : 33;
?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/exam/index" class="text-decoration-none text-muted">Exams</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Online Marks Entry</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Examination Marks Entry</h2>
        <p class="text-muted mb-0 small">Secure marks recording with automated theory/practical computation and multi-tier approval locking.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/exam/schedule<?php echo $examId ? '/' . $examId : ''; ?>" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-calendar-alt me-1"></i> Paper Schedule
        </a>
        <a href="<?php echo URLROOT; ?>/exam/approval<?php echo $examId ? '?exam_id=' . $examId : ''; ?>" class="btn btn-outline-primary btn-sm px-3">
            <i class="fa fa-stamp me-1"></i> Approval Chain
        </a>
        <?php if($scheduleInfo): ?>
            <a href="<?php echo URLROOT; ?>/exam/hallSheet/<?php echo $scheduleInfo->id; ?>" target="_blank" class="btn btn-outline-dark btn-sm px-3">
                <i class="fa fa-print me-1"></i> Hall Sheet
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Alert Notifications -->
<?php if(isset($_GET['success'])): ?>
    <?php if($_GET['success'] === 'draft_saved'): ?>
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <i class="fa fa-save fs-5"></i>
            <div><strong>Draft Saved!</strong> Marks draft has been saved successfully. You can continue updating until you submit for review.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif($_GET['success'] === 'submitted_and_locked'): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
            <i class="fa fa-lock fs-5"></i>
            <div><strong>Submitted & Locked!</strong> Marks have been submitted to the Class Teacher for verification. Editing is now locked.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Criteria Selection Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form action="" method="get" class="row g-2 align-items-end">
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">1. Select Examination</label>
                <select name="exam_selection" id="examSelect" class="form-select form-select-sm" onchange="window.location.href='<?php echo URLROOT; ?>/exam/marks/' + this.value">
                    <option value="">-- Choose Exam --</option>
                    <?php foreach($data['exams'] as $exam): ?>
                        <option value="<?php echo $exam->id; ?>" <?php echo ($examId == $exam->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($exam->name . ' (' . ($exam->session_name ?? '2026-27') . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if($examId): ?>
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">2. Class / Grade</label>
                <select name="class_id" id="classSelect" class="form-select form-select-sm" required onchange="filterSections()">
                    <option value="">Select Class</option>
                    <?php foreach($data['classes'] as $class): ?>
                        <option value="<?php echo $class->id; ?>" <?php echo (isset($_GET['class_id']) && $_GET['class_id'] == $class->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class->class_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label small text-muted mb-1">3. Section</label>
                <select name="section_id" id="sectionSelect" class="form-select form-select-sm" required onchange="this.form.submit()">
                     <option value="">Select Section</option>
                     <?php foreach($data['sections'] as $section): ?>
                         <option value="<?php echo $section->id; ?>" data-class="<?php echo $section->class_id; ?>" class="section-option">
                             <?php echo htmlspecialchars($section->section_name); ?>
                         </option>
                     <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">4. Subject Paper</label>
                 <select name="schedule_id" class="form-select form-select-sm" required>
                     <option value="">-- Choose Paper --</option>
                     <?php foreach($data['schedules'] as $sch): ?>
                         <option value="<?php echo $sch->id; ?>" <?php echo (isset($_GET['schedule_id']) && $_GET['schedule_id'] == $sch->id) ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($sch->subject_name . ' (' . ($sch->subject_code ?: 'SUB') . ')'); ?>
                         </option>
                     <?php endforeach; ?>
                 </select>
            </div>
            <div class="col-md-1 col-sm-6">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                    <i class="fa fa-arrow-right"></i> Load
                </button>
            </div>
            <?php else: ?>
            <div class="col-md-9">
                <div class="alert alert-light border small text-muted mb-0 py-2">
                    <i class="fa fa-info-circle text-primary me-1"></i> Please choose an active examination above to begin recording subject marks.
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if($scheduleInfo): ?>
    <!-- Paper Meta Details & Lock Banner -->
    <div class="card shadow-sm border-0 mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-0 border-bottom">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-4">
                        <i class="fa fa-book-open fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">
                            <?php echo htmlspecialchars($scheduleInfo->subject_name); ?>
                            <span class="badge bg-light text-dark border font-monospace ms-2"><?php echo htmlspecialchars($scheduleInfo->subject_code ?: 'SUB'); ?></span>
                        </h5>
                        <div class="small text-muted d-flex flex-wrap align-items-center gap-3">
                            <span><i class="fa fa-layer-group me-1 text-secondary"></i> Class: <strong><?php echo htmlspecialchars($scheduleInfo->class_name . ' - ' . $scheduleInfo->section_name); ?></strong></span>
                            <span><i class="fa fa-calendar-day me-1 text-secondary"></i> Exam Date: <strong><?php echo date('d M, Y', strtotime($scheduleInfo->date_of_exam)); ?></strong></span>
                            <span><i class="fa fa-chalkboard-user me-1 text-secondary"></i> Class In-charge: <strong><?php echo htmlspecialchars($scheduleInfo->class_teacher_name ?: 'Not Assigned'); ?></strong></span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <?php if($status === 'draft'): ?>
                        <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill"><i class="fa fa-pencil me-1"></i> Draft / In Progress</span>
                    <?php elseif($status === 'submitted_to_class_teacher'): ?>
                        <span class="badge bg-info text-dark px-3 py-2 fs-6 rounded-pill"><i class="fa fa-lock me-1"></i> Under Class Teacher Review</span>
                    <?php elseif($status === 'reviewed_by_class_teacher'): ?>
                        <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill"><i class="fa fa-user-check me-1"></i> Under Vice Principal Review</span>
                    <?php elseif($status === 'verified_by_vp'): ?>
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill"><i class="fa fa-shield-halved me-1"></i> Under Principal Approval</span>
                    <?php elseif($status === 'published' || $status === 'approved_by_principal'): ?>
                        <span class="badge bg-success px-3 py-2 fs-6 rounded-pill"><i class="fa fa-check-double me-1"></i> Approved & Published</span>
                    <?php elseif($status === 'rejected'): ?>
                        <span class="badge bg-danger px-3 py-2 fs-6 rounded-pill"><i class="fa fa-rotate-left me-1"></i> Revision Requested</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card-body bg-light-subtle py-3 border-bottom">
            <div class="row g-3 text-center">
                <div class="col-6 col-md-3">
                    <div class="small text-muted mb-1">Theory Max Marks</div>
                    <h5 class="fw-bold mb-0 text-dark font-monospace"><?php echo $theoryMax; ?></h5>
                </div>
                <div class="col-6 col-md-3 border-start">
                    <div class="small text-muted mb-1">Practical Max Marks</div>
                    <h5 class="fw-bold mb-0 text-dark font-monospace"><?php echo $practicalMax; ?></h5>
                </div>
                <div class="col-6 col-md-3 border-start">
                    <div class="small text-muted mb-1">Total Full Marks</div>
                    <h5 class="fw-bold mb-0 text-primary font-monospace"><?php echo $fullMarks; ?></h5>
                </div>
                <div class="col-6 col-md-3 border-start">
                    <div class="small text-muted mb-1">Minimum Passing Marks</div>
                    <h5 class="fw-bold mb-0 text-danger font-monospace"><?php echo $passMarks; ?></h5>
                </div>
            </div>
        </div>

        <?php if($isRejected): ?>
            <div class="bg-danger-subtle text-danger px-4 py-3 d-flex align-items-center gap-3">
                <i class="fa fa-circle-exclamation fs-4"></i>
                <div>
                    <strong class="d-block">Rejection Notice / Correction Needed:</strong>
                    <span><?php echo htmlspecialchars($scheduleInfo->rejection_reason ?: 'Marks returned by reviewer for revisions.'); ?></span>
                </div>
            </div>
        <?php elseif($isLocked): ?>
            <div class="bg-warning-subtle text-warning-emphasis px-4 py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-lock fs-4"></i>
                    <div>
                        <strong class="d-block">Marks Locked for Review:</strong>
                        <span>This paper is submitted in the approval workflow. Modifications are disabled to preserve integrity.</span>
                    </div>
                </div>
                <a href="<?php echo URLROOT; ?>/exam/approval?exam_id=<?php echo $examId; ?>" class="btn btn-sm btn-outline-dark fw-bold">
                    <i class="fa fa-stamp me-1"></i> Track in Approval Chain
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Real-time Metrics Bar -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Total Students</div>
                <h4 class="fw-bold mb-0 text-dark" id="statTotal"><?php echo count($data['students']); ?></h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Present</div>
                <h4 class="fw-bold mb-0 text-success" id="statPresent">0</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Absent</div>
                <h4 class="fw-bold mb-0 text-danger" id="statAbsent">0</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Passing Rate</div>
                <h4 class="fw-bold mb-0 text-primary" id="statPassRate">0%</h4>
            </div>
        </div>
    </div>

    <!-- Marks Entry Form -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa fa-users text-primary me-2"></i>Class Student Roster & Marks
            </h6>
            <?php if(!$isLocked): ?>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="markAllPresent()">
                        <i class="fa fa-user-check me-1"></i> Mark All Present
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-body p-0">
            <form id="marksForm" action="<?php echo URLROOT; ?>/exam/marks" method="post">
                <input type="hidden" name="exam_id" value="<?php echo $examId; ?>">
                <input type="hidden" name="class_id" value="<?php echo $data['class_id']; ?>">
                <input type="hidden" name="section_id" value="<?php echo $data['section_id']; ?>">
                <input type="hidden" name="schedule_id" value="<?php echo $data['schedule_id']; ?>">
                <input type="hidden" name="submit_action" id="submitActionInput" value="save_draft">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="marksTable">
                        <thead class="bg-light small">
                            <tr>
                                <th class="ps-4" style="width: 80px;">Roll No</th>
                                <th style="width: 110px;">Adm No</th>
                                <th>Student & Father Name</th>
                                <th class="text-center" style="width: 100px;">Absent?</th>
                                <th style="width: 130px;">Theory (Max <?php echo $theoryMax; ?>)</th>
                                <th style="width: 130px;">Practical (Max <?php echo $practicalMax; ?>)</th>
                                <th style="width: 110px;">Total (<?php echo $fullMarks; ?>)</th>
                                <th class="text-center" style="width: 90px;">Status</th>
                                <th class="pe-4" style="width: 220px;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($data['students'])): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fa fa-user-graduate fa-2x mb-2 d-block opacity-50"></i>
                                        No students found enrolled in this class and section.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($data['students'] as $idx => $st): 
                                    $isAbs = ($st->is_absent === 'yes');
                                    $thMarks = $st->theory_marks !== null ? (float)$st->theory_marks : '';
                                    $prMarks = $st->practical_marks !== null ? (float)$st->practical_marks : '';
                                    $totMarks = $st->get_marks !== null ? (float)$st->get_marks : '';
                                ?>
                                    <tr class="student-row <?php echo $isAbs ? 'table-light text-muted' : ''; ?>" id="row-<?php echo $st->student_id; ?>">
                                        <td class="ps-4">
                                            <span class="badge bg-primary-subtle text-primary font-monospace px-2 py-1">
                                                <?php echo htmlspecialchars($st->roll_no ?: ($idx + 1)); ?>
                                            </span>
                                        </td>
                                        <td class="font-monospace small text-muted">
                                            <?php echo htmlspecialchars($st->admission_no ?: 'ADM-' . $st->student_id); ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($st->name); ?></div>
                                            <small class="text-muted">S/O, D/O: <?php echo htmlspecialchars($st->father_name ?: 'N/A'); ?></small>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input absent-check" type="checkbox" 
                                                       name="students[<?php echo $st->student_id; ?>][absent]" 
                                                       value="yes" 
                                                       data-sid="<?php echo $st->student_id; ?>"
                                                       <?php echo $isAbs ? 'checked' : ''; ?>
                                                       <?php echo $isLocked ? 'disabled' : ''; ?>
                                                       onchange="toggleAbsent(this, <?php echo $st->student_id; ?>)">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" min="0" max="<?php echo $theoryMax; ?>" 
                                                   name="students[<?php echo $st->student_id; ?>][theory]" 
                                                   id="theory-<?php echo $st->student_id; ?>"
                                                   class="form-control form-control-sm text-end fw-bold theory-input" 
                                                   value="<?php echo $thMarks; ?>" 
                                                   placeholder="0"
                                                   <?php echo ($isAbs || $isLocked) ? 'readonly' : ''; ?>
                                                   oninput="calcStudentTotal(<?php echo $st->student_id; ?>)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" min="0" max="<?php echo $practicalMax; ?>" 
                                                   name="students[<?php echo $st->student_id; ?>][practical]" 
                                                   id="practical-<?php echo $st->student_id; ?>"
                                                   class="form-control form-control-sm text-end fw-bold practical-input" 
                                                   value="<?php echo $prMarks; ?>" 
                                                   placeholder="0"
                                                   <?php echo ($isAbs || $isLocked) ? 'readonly' : ''; ?>
                                                   oninput="calcStudentTotal(<?php echo $st->student_id; ?>)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.5" 
                                                   name="students[<?php echo $st->student_id; ?>][marks]" 
                                                   id="total-<?php echo $st->student_id; ?>"
                                                   class="form-control form-control-sm text-end fw-bold bg-light total-input" 
                                                   value="<?php echo $totMarks; ?>" 
                                                   readonly>
                                        </td>
                                        <td class="text-center" id="badge-<?php echo $st->student_id; ?>">
                                            <?php if($isAbs): ?>
                                                <span class="badge bg-danger">ABS</span>
                                            <?php elseif($totMarks !== '' && (float)$totMarks >= $passMarks): ?>
                                                <span class="badge bg-success">PASS</span>
                                            <?php elseif($totMarks !== ''): ?>
                                                <span class="badge bg-danger">FAIL</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border">--</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="pe-4">
                                            <input type="text" 
                                                   name="students[<?php echo $st->student_id; ?>][remarks]" 
                                                   class="form-control form-control-sm" 
                                                   placeholder="e.g. Excellent / Weak in Q2"
                                                   value="<?php echo htmlspecialchars($st->remarks ?? ''); ?>"
                                                   <?php echo $isLocked ? 'readonly' : ''; ?>>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Save / Lock Action Controls -->
                <div class="p-3 bg-light border-top d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                    <div class="small text-muted">
                        <i class="fa fa-info-circle me-1 text-primary"></i>
                        Saving draft preserves entered values for later edits. <strong>Submit & Lock</strong> forwards marks to the Class Teacher and seals inputs.
                    </div>

                    <?php if(!$isLocked && !empty($data['students'])): ?>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-secondary px-4 fw-bold" onclick="submitForm('save_draft')">
                                <i class="fa fa-floppy-disk me-1"></i> Save Draft
                            </button>
                            <button type="button" class="btn btn-success px-4 fw-bold shadow-sm" onclick="confirmSubmitAndLock()">
                                <i class="fa fa-paper-plane me-1"></i> Submit & Lock for Review
                            </button>
                        </div>
                    <?php elseif($isLocked): ?>
                        <div class="d-flex align-items-center gap-2">
                            <a href="<?php echo URLROOT; ?>/exam/approval?exam_id=<?php echo $examId; ?>" class="btn btn-primary px-4 fw-bold">
                                <i class="fa fa-stamp me-1"></i> Open in Approval Chain
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<!-- Confirmation Modal for Submit & Lock -->
<div class="modal fade" id="lockConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fa fa-lock text-warning me-2"></i>Lock & Submit Examination Marks?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-3 text-muted">
                    Are you sure you want to lock and submit the marks for <strong><?php echo htmlspecialchars($scheduleInfo->subject_name ?? ''); ?></strong>?
                </p>
                <div class="alert alert-warning small mb-0">
                    <i class="fa fa-triangle-exclamation me-1"></i>
                    Once locked, you will <strong>no longer be able to edit or change marks</strong> unless the Class Teacher or Vice Principal returns the paper for revision.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success fw-bold px-4" onclick="executeSubmitAndLock()">
                    Yes, Lock & Submit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const PASS_MARKS = <?php echo $passMarks; ?>;
    const THEORY_MAX = <?php echo $theoryMax; ?>;
    const PRACTICAL_MAX = <?php echo $practicalMax; ?>;

    function filterSections(){
        var classId = document.getElementById('classSelect').value;
        var options = document.getElementsByClassName('section-option');
        for(var i=0; i<options.length; i++){
            if(!classId || options[i].getAttribute('data-class') == classId){
                options[i].style.display = '';
            } else {
                options[i].style.display = 'none';
            }
        }
    }

    function toggleAbsent(checkbox, sid){
        var row = document.getElementById('row-' + sid);
        var thInput = document.getElementById('theory-' + sid);
        var prInput = document.getElementById('practical-' + sid);
        var totInput = document.getElementById('total-' + sid);
        var badge = document.getElementById('badge-' + sid);

        if(checkbox.checked){
            row.classList.add('table-light', 'text-muted');
            thInput.value = 0;
            prInput.value = 0;
            totInput.value = 0;
            thInput.readOnly = true;
            prInput.readOnly = true;
            badge.innerHTML = '<span class="badge bg-danger">ABS</span>';
        } else {
            row.classList.remove('table-light', 'text-muted');
            thInput.readOnly = false;
            prInput.readOnly = false;
            calcStudentTotal(sid);
        }
        recalculateStats();
    }

    function calcStudentTotal(sid){
        var thInput = document.getElementById('theory-' + sid);
        var prInput = document.getElementById('practical-' + sid);
        var totInput = document.getElementById('total-' + sid);
        var badge = document.getElementById('badge-' + sid);

        var th = parseFloat(thInput.value) || 0;
        var pr = parseFloat(prInput.value) || 0;

        if(th > THEORY_MAX){
            thInput.value = THEORY_MAX;
            th = THEORY_MAX;
        }
        if(pr > PRACTICAL_MAX){
            prInput.value = PRACTICAL_MAX;
            pr = PRACTICAL_MAX;
        }

        var total = th + pr;
        totInput.value = total;

        if(total >= PASS_MARKS){
            badge.innerHTML = '<span class="badge bg-success">PASS</span>';
        } else {
            badge.innerHTML = '<span class="badge bg-danger">FAIL</span>';
        }
        recalculateStats();
    }

    function markAllPresent(){
        var checks = document.getElementsByClassName('absent-check');
        for(var i=0; i<checks.length; i++){
            if(checks[i].checked){
                checks[i].checked = false;
                var sid = checks[i].getAttribute('data-sid');
                toggleAbsent(checks[i], sid);
            }
        }
        recalculateStats();
    }

    function recalculateStats(){
        var total = 0;
        var present = 0;
        var absent = 0;
        var passed = 0;

        var rows = document.querySelectorAll('.student-row');
        total = rows.length;

        rows.forEach(function(row){
            var check = row.querySelector('.absent-check');
            var totInput = row.querySelector('.total-input');
            if(check && check.checked){
                absent++;
            } else {
                present++;
                if(totInput && parseFloat(totInput.value) >= PASS_MARKS){
                    passed++;
                }
            }
        });

        if(document.getElementById('statTotal')) document.getElementById('statTotal').innerText = total;
        if(document.getElementById('statPresent')) document.getElementById('statPresent').innerText = present;
        if(document.getElementById('statAbsent')) document.getElementById('statAbsent').innerText = absent;

        var passRate = present > 0 ? Math.round((passed / present) * 100) : 0;
        if(document.getElementById('statPassRate')) document.getElementById('statPassRate').innerText = passRate + '%';
    }

    function submitForm(action){
        document.getElementById('submitActionInput').value = action;
        document.getElementById('marksForm').submit();
    }

    function confirmSubmitAndLock(){
        var modal = new bootstrap.Modal(document.getElementById('lockConfirmModal'));
        modal.show();
    }

    function executeSubmitAndLock(){
        submitForm('submit_and_lock');
    }

    document.addEventListener('DOMContentLoaded', function(){
        if(document.getElementById('classSelect') && document.getElementById('classSelect').value) {
            filterSections();
            document.getElementById('sectionSelect').value = "<?php echo isset($_GET['section_id']) ? $_GET['section_id'] : ''; ?>";
        }
        recalculateStats();
    });
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
