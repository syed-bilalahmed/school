<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$examId = $data['exam_id'];
$examObj = null;
foreach($data['exams'] as $e){
    if($e->id == $examId) $examObj = $e;
}
?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/exam/index" class="text-decoration-none text-muted">Exams</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Paper Schedule</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Examination Paper Schedule</h2>
        <p class="text-muted mb-0 small">Configure class paper timetables, exam room assignments, theory/practical breakdown, and generate hall signature sheets.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/exam/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Exam List
        </a>
        <?php if($examId): ?>
            <a href="<?php echo URLROOT; ?>/exam/signatureSlip?exam_id=<?php echo $examId; ?><?php echo isset($_GET['class_id']) ? '&class_id='.$_GET['class_id'] : ''; ?><?php echo isset($_GET['section_id']) ? '&section_id='.$_GET['section_id'] : ''; ?>" class="btn btn-warning text-dark fw-bold btn-sm px-3 shadow-xs">
                <i class="fa fa-file-signature me-1"></i> Print Signature Sheet
            </a>
            <a href="<?php echo URLROOT; ?>/exam/approval?exam_id=<?php echo $examId; ?>" class="btn btn-outline-primary btn-sm px-3">
                <i class="fa fa-stamp me-1"></i> Approval Chain
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div>Paper schedule added successfully!</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filter & Selection Bar -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form action="" method="get" class="row g-2 align-items-end">
            <div class="col-md-4 col-sm-6">
                <label class="form-label small text-muted mb-1">Select Examination</label>
                <select name="exam_selection" id="examSelect" class="form-select form-select-sm" onchange="window.location.href='<?php echo URLROOT; ?>/exam/schedule/' + this.value">
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
                <label class="form-label small text-muted mb-1">Class / Grade</label>
                <select name="class_id" id="classSelect" class="form-select form-select-sm" required onchange="filterSections()">
                    <option value="">Select Class</option>
                    <?php foreach($data['classes'] as $class): ?>
                        <option value="<?php echo $class->id; ?>" <?php echo (isset($_GET['class_id']) && $_GET['class_id'] == $class->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class->class_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">Section</label>
                <select name="section_id" id="sectionSelect" class="form-select form-select-sm" required>
                     <option value="">Select Section</option>
                     <?php foreach($data['sections'] as $section): ?>
                         <option value="<?php echo $section->id; ?>" data-class="<?php echo $section->class_id; ?>" class="section-option">
                             <?php echo htmlspecialchars($section->section_name); ?>
                         </option>
                     <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa fa-filter me-1"></i> Load Papers
                </button>
            </div>
            <?php else: ?>
            <div class="col-md-8">
                <div class="alert alert-light border small text-muted mb-0 py-2">
                    <i class="fa fa-info-circle text-primary me-1"></i> Please choose an examination above to configure paper schedules and print hall sheets.
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if(isset($data['subjects']) && !empty($data['subjects'])): ?>
<div class="row g-4">
    <!-- Schedule Paper Form -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 90px;">
            <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                <div class="p-2 bg-success-subtle text-success rounded-3">
                    <i class="fa fa-plus-circle"></i>
                </div>
                <h6 class="fw-bold mb-0 text-dark">Schedule Subject Paper</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo URLROOT; ?>/exam/schedule" method="post">
                    <input type="hidden" name="exam_id" value="<?php echo $examId; ?>">
                    <input type="hidden" name="class_id" value="<?php echo $data['class_id']; ?>">
                    <input type="hidden" name="section_id" value="<?php echo $data['section_id']; ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">Select Subject</option>
                            <?php foreach($data['subjects'] as $sub): ?>
                                <option value="<?php echo $sub->subject_id; ?>">
                                    <?php echo htmlspecialchars($sub->subject_name . ' (' . ($sub->subject_code ?: 'SUB') . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Date of Exam <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_exam" class="form-control" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted">Start Time</label>
                            <input type="time" name="start_time" class="form-control" value="09:00">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">End Time</label>
                            <input type="time" name="end_time" class="form-control" value="12:00">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Examination Room No</label>
                        <input type="text" name="room_no" class="form-control" placeholder="e.g. Hall-1 / Room 104">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted">Theory Marks</label>
                            <input type="number" name="theory_marks" id="schedTheory" class="form-control" value="75" oninput="sumTotalMarks()">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted">Practical Marks</label>
                            <input type="number" name="practical_marks" id="schedPractical" class="form-control" value="25" oninput="sumTotalMarks()">
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Full Marks</label>
                            <input type="number" name="full_marks" id="schedFull" class="form-control fw-bold" value="100" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Passing Marks</label>
                            <input type="number" name="passing_marks" class="form-control" value="33">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                        <i class="fa fa-calendar-plus me-1"></i> Add Paper Schedule
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scheduled Papers Table -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-calendar-check text-primary me-2"></i>Scheduled Papers for Selected Section
                </h6>
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2" style="border-radius: 30px;">
                    <?php echo count($data['schedules']); ?> Papers
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Subject</th>
                                <th>Exam Date</th>
                                <th>Timing</th>
                                <th>Room</th>
                                <th>Marks Breakdown</th>
                                <th>Approval State</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($data['schedules'])): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-calendar-xmark fa-2x mb-2 d-block opacity-50"></i>
                                        No papers scheduled for this class section yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($data['schedules'] as $sch): 
                                    $status = $sch->approval_status ?? 'draft';
                                    $badgeClass = 'bg-secondary';
                                    $statusLabel = 'Draft';

                                    if($status === 'submitted_to_class_teacher'){
                                        $badgeClass = 'bg-info text-dark';
                                        $statusLabel = 'Submitted (Lock)';
                                    } elseif($status === 'reviewed_by_class_teacher'){
                                        $badgeClass = 'bg-primary';
                                        $statusLabel = 'Class Review';
                                    } elseif($status === 'verified_by_vp'){
                                        $badgeClass = 'bg-warning text-dark';
                                        $statusLabel = 'VP Verified';
                                    } elseif($status === 'published' || $status === 'approved_by_principal'){
                                        $badgeClass = 'bg-success';
                                        $statusLabel = 'Approved / Published';
                                    } elseif($status === 'rejected'){
                                        $badgeClass = 'bg-danger';
                                        $statusLabel = 'Rejected / Revision';
                                    }
                                ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($sch->subject_name); ?></div>
                                            <span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($sch->subject_code ?: 'SUB'); ?></span>
                                        </td>
                                        <td class="fw-bold text-dark font-monospace">
                                            <?php echo date('d M, Y', strtotime($sch->date_of_exam)); ?>
                                        </td>
                                        <td class="text-muted">
                                            <i class="fa fa-clock me-1 text-secondary"></i>
                                            <?php echo date('h:i A', strtotime($sch->start_time)); ?> &ndash; <?php echo date('h:i A', strtotime($sch->end_time)); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fa fa-door-open me-1 text-primary"></i><?php echo htmlspecialchars($sch->room_no ?: 'Room 1'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">Total: <?php echo $sch->full_marks; ?></div>
                                            <small class="text-muted">Th: <?php echo $sch->theory_marks ?? 75; ?> | Pr: <?php echo $sch->practical_marks ?? 25; ?> | Pass: <?php echo $sch->passing_marks; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo $statusLabel; ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex align-items-center justify-content-end gap-1">
                                                <a href="<?php echo URLROOT; ?>/exam/hallSheet/<?php echo $sch->id; ?>" class="btn btn-sm btn-outline-dark px-2 py-1" target="_blank" title="Print Exam Hall Signature Sheet">
                                                    <i class="fa fa-print me-1"></i> Hall Sheet
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/exam/marks/<?php echo $examId; ?>?class_id=<?php echo $data['class_id']; ?>&section_id=<?php echo $data['section_id']; ?>&schedule_id=<?php echo $sch->id; ?>" class="btn btn-sm btn-primary px-2 py-1" title="Enter Marks">
                                                    <i class="fa fa-marker me-1"></i> Marks
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
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

    function sumTotalMarks(){
        var th = parseFloat(document.getElementById('schedTheory').value) || 0;
        var pr = parseFloat(document.getElementById('schedPractical').value) || 0;
        document.getElementById('schedFull').value = th + pr;
    }

    document.addEventListener('DOMContentLoaded', function(){
        if(document.getElementById('classSelect') && document.getElementById('classSelect').value) {
            filterSections();
            document.getElementById('sectionSelect').value = "<?php echo isset($_GET['section_id']) ? $_GET['section_id'] : ''; ?>";
        }
    });
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
