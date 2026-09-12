<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$selectedExamId = $data['exam_id'];
$progressList = $data['progress'] ?? [];

// Calculate counts
$countTotal = count($progressList);
$countDraft = 0;
$countClassReview = 0;
$countVpReview = 0;
$countPrincipalReview = 0;
$countPublished = 0;
$countRejected = 0;

foreach($progressList as $item){
    $st = $item->approval_status ?? 'draft';
    if($st === 'draft') $countDraft++;
    elseif($st === 'submitted_to_class_teacher') $countClassReview++;
    elseif($st === 'reviewed_by_class_teacher') $countVpReview++;
    elseif($st === 'verified_by_vp') $countPrincipalReview++;
    elseif($st === 'published' || $st === 'approved_by_principal') $countPublished++;
    elseif($st === 'rejected') $countRejected++;
}
?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/exam/index" class="text-decoration-none text-muted">Exams</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Result Approval Chain</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Examination Result Approval Chain</h2>
        <p class="text-muted mb-0 small">4-Tier institutional governance: Subject Teacher &rarr; Class Teacher &rarr; Vice Principal &rarr; Principal Final Authorization & Publication.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/exam/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-list me-1"></i> Exam List
        </a>
        <a href="<?php echo URLROOT; ?>/exam/schedule<?php echo $selectedExamId ? '/' . $selectedExamId : ''; ?>" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-calendar-alt me-1"></i> Paper Schedule
        </a>
        <a href="<?php echo URLROOT; ?>/exam/marks<?php echo $selectedExamId ? '/' . $selectedExamId : ''; ?>" class="btn btn-outline-primary btn-sm px-3">
            <i class="fa fa-marker me-1"></i> Enter Marks
        </a>
    </div>
</div>

<!-- Alert Notifications -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div><strong>Approval State Updated!</strong> The examination paper has moved to the next step in the governance chain.</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Workflow Visual Explanation Banner -->
<div class="card shadow-sm border-0 mb-4 bg-white overflow-hidden">
    <div class="card-body p-4">
        <h6 class="fw-bold text-dark mb-3"><i class="fa fa-sitemap text-primary me-2"></i>Institutional 4-Tier Examination Governance Architecture</h6>
        <div class="row g-3">
            <div class="col-md-3 col-sm-6">
                <div class="p-3 rounded-3 bg-light border-start border-4 border-secondary h-100">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge bg-secondary">Tier 1</span>
                        <i class="fa fa-chalkboard-user text-secondary"></i>
                    </div>
                    <div class="fw-bold text-dark">Subject Teacher</div>
                    <div class="small text-muted">Enters Theory & Practical, verifies answer books, and locks submission.</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="p-3 rounded-3 bg-light border-start border-4 border-info h-100">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge bg-info text-dark">Tier 2</span>
                        <i class="fa fa-user-check text-info"></i>
                    </div>
                    <div class="fw-bold text-dark">Class Teacher</div>
                    <div class="small text-muted">Audits student roll call, cross-checks missing marks, and forwards to VP.</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="p-3 rounded-3 bg-light border-start border-4 border-warning h-100">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge bg-warning text-dark">Tier 3</span>
                        <i class="fa fa-shield-halved text-warning"></i>
                    </div>
                    <div class="fw-bold text-dark">Vice Principal</div>
                    <div class="small text-muted">Ensures academic grade parity, moderates boundary marks, and recommends approval.</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="p-3 rounded-3 bg-light border-start border-4 border-success h-100">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge bg-success">Tier 4</span>
                        <i class="fa fa-award text-success"></i>
                    </div>
                    <div class="fw-bold text-dark">Principal Final Seal</div>
                    <div class="small text-muted">Authorizes official Gazette, unseals report cards, and publishes results to parents.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Metrics Counters -->
<div class="row g-3 mb-4">
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card shadow-sm border-0 text-center py-2">
            <div class="small text-muted">Total Papers</div>
            <h4 class="fw-bold mb-0 text-dark"><?php echo $countTotal; ?></h4>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card shadow-sm border-0 text-center py-2">
            <div class="small text-muted">Draft / Entry</div>
            <h4 class="fw-bold mb-0 text-secondary"><?php echo $countDraft; ?></h4>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card shadow-sm border-0 text-center py-2">
            <div class="small text-muted">Class Review</div>
            <h4 class="fw-bold mb-0 text-info"><?php echo $countClassReview; ?></h4>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card shadow-sm border-0 text-center py-2">
            <div class="small text-muted">VP Review</div>
            <h4 class="fw-bold mb-0 text-warning"><?php echo $countVpReview; ?></h4>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card shadow-sm border-0 text-center py-2">
            <div class="small text-muted">Principal Review</div>
            <h4 class="fw-bold mb-0 text-primary"><?php echo $countPrincipalReview; ?></h4>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card shadow-sm border-0 text-center py-2">
            <div class="small text-muted">Published</div>
            <h4 class="fw-bold mb-0 text-success"><?php echo $countPublished; ?></h4>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form action="" method="get" class="row g-2 align-items-end">
            <div class="col-md-4 col-sm-6">
                <label class="form-label small text-muted mb-1">Select Examination</label>
                <select name="exam_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php foreach($data['exams'] as $exam): ?>
                        <option value="<?php echo $exam->id; ?>" <?php echo ($selectedExamId == $exam->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($exam->name . ' (' . ($exam->session_name ?? '2026-27') . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">Class / Grade</label>
                <select name="class_id" id="classSelect" class="form-select form-select-sm" onchange="filterSections()">
                    <option value="">All Classes</option>
                    <?php foreach($data['classes'] as $class): ?>
                        <option value="<?php echo $class->id; ?>" <?php echo ($data['class_id'] == $class->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class->class_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">Section</label>
                <select name="section_id" id="sectionSelect" class="form-select form-select-sm">
                     <option value="">All Sections</option>
                     <?php foreach($data['sections'] as $section): ?>
                         <option value="<?php echo $section->id; ?>" data-class="<?php echo $section->class_id; ?>" class="section-option" <?php echo ($data['section_id'] == $section->id) ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($section->section_name); ?>
                         </option>
                     <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                    <i class="fa fa-filter me-1"></i> Filter Chain
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Papers Approval Roster Table -->
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa fa-stamp text-primary me-2"></i>Subject Papers & Verification Status
        </h6>
        <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">
            <?php echo count($progressList); ?> Papers In Scope
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 220px;">Paper / Class</th>
                        <th style="width: 140px;">Current Status</th>
                        <th>Workflow Stepper</th>
                        <th>Audit Trail Log</th>
                        <th class="text-end pe-4" style="width: 260px;">Governance Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($progressList)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                                No examination papers found matching the selected filter criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($progressList as $item): 
                            $status = $item->approval_status ?? 'draft';
                            $stepIndex = 0;
                            $statusBadge = 'bg-secondary';
                            $statusText = 'Draft / Teacher Working';

                            if($status === 'submitted_to_class_teacher'){
                                $stepIndex = 1;
                                $statusBadge = 'bg-info text-dark';
                                $statusText = 'Pending Class Review';
                            } elseif($status === 'reviewed_by_class_teacher'){
                                $stepIndex = 2;
                                $statusBadge = 'bg-warning text-dark';
                                $statusText = 'Pending VP Verification';
                            } elseif($status === 'verified_by_vp'){
                                $stepIndex = 3;
                                $statusBadge = 'bg-primary';
                                $statusText = 'Pending Principal Final Approval';
                            } elseif($status === 'published' || $status === 'approved_by_principal'){
                                $stepIndex = 4;
                                $statusBadge = 'bg-success';
                                $statusText = 'Approved & Published';
                            } elseif($status === 'rejected'){
                                $stepIndex = -1;
                                $statusBadge = 'bg-danger';
                                $statusText = 'Returned / Revision';
                            }
                        ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($item->subject_name); ?></div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($item->subject_code ?: 'SUB'); ?></span>
                                        <span class="badge bg-primary-subtle text-primary"><?php echo htmlspecialchars($item->class_name . ' - ' . $item->section_name); ?></span>
                                    </div>
                                </td>

                                <td>
                                    <span class="badge <?php echo $statusBadge; ?> px-2 py-1">
                                        <?php echo $statusText; ?>
                                    </span>
                                    <?php if($status === 'rejected' && !empty($item->rejection_reason)): ?>
                                        <div class="small text-danger mt-1">
                                            <i class="fa fa-info-circle me-1"></i><?php echo htmlspecialchars($item->rejection_reason); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <!-- Mini Stepper Indicator -->
                                    <div class="d-flex align-items-center gap-1" style="max-width: 260px;">
                                        <!-- Step 1: Teacher -->
                                        <div class="flex-grow-1 text-center">
                                            <div class="py-1 rounded-pill small fw-bold <?php echo ($stepIndex >= 1) ? 'bg-success text-white' : ($stepIndex == 0 ? 'bg-secondary text-white' : 'bg-light text-muted'); ?>" style="font-size: 10px;">
                                                <i class="fa fa-pencil me-1"></i>T1
                                            </div>
                                            <div class="text-muted" style="font-size: 9px;">Teacher</div>
                                        </div>
                                        <i class="fa fa-chevron-right text-muted" style="font-size: 9px;"></i>

                                        <!-- Step 2: Class In-charge -->
                                        <div class="flex-grow-1 text-center">
                                            <div class="py-1 rounded-pill small fw-bold <?php echo ($stepIndex >= 2) ? 'bg-success text-white' : ($stepIndex == 1 ? 'bg-info text-dark' : 'bg-light text-muted'); ?>" style="font-size: 10px;">
                                                <i class="fa fa-check me-1"></i>T2
                                            </div>
                                            <div class="text-muted" style="font-size: 9px;">Class</div>
                                        </div>
                                        <i class="fa fa-chevron-right text-muted" style="font-size: 9px;"></i>

                                        <!-- Step 3: VP -->
                                        <div class="flex-grow-1 text-center">
                                            <div class="py-1 rounded-pill small fw-bold <?php echo ($stepIndex >= 3) ? 'bg-success text-white' : ($stepIndex == 2 ? 'bg-warning text-dark' : 'bg-light text-muted'); ?>" style="font-size: 10px;">
                                                <i class="fa fa-shield me-1"></i>T3
                                            </div>
                                            <div class="text-muted" style="font-size: 9px;">VP</div>
                                        </div>
                                        <i class="fa fa-chevron-right text-muted" style="font-size: 9px;"></i>

                                        <!-- Step 4: Principal -->
                                        <div class="flex-grow-1 text-center">
                                            <div class="py-1 rounded-pill small fw-bold <?php echo ($stepIndex >= 4) ? 'bg-success text-white' : ($stepIndex == 3 ? 'bg-primary text-white' : 'bg-light text-muted'); ?>" style="font-size: 10px;">
                                                <i class="fa fa-award me-1"></i>T4
                                            </div>
                                            <div class="text-muted" style="font-size: 9px;">Principal</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="small">
                                        <?php if($item->submitter_name): ?>
                                            <div><span class="text-muted">Teacher:</span> <strong><?php echo htmlspecialchars($item->submitter_name); ?></strong> <span class="text-muted font-monospace">(<?php echo date('d/m H:i', strtotime($item->submitted_at)); ?>)</span></div>
                                        <?php endif; ?>
                                        <?php if($item->reviewer_name): ?>
                                            <div><span class="text-muted">Class Review:</span> <strong><?php echo htmlspecialchars($item->reviewer_name); ?></strong> <span class="text-muted font-monospace">(<?php echo date('d/m H:i', strtotime($item->reviewed_at)); ?>)</span></div>
                                        <?php endif; ?>
                                        <?php if($item->vp_name): ?>
                                            <div><span class="text-muted">VP Verified:</span> <strong><?php echo htmlspecialchars($item->vp_name); ?></strong> <span class="text-muted font-monospace">(<?php echo date('d/m H:i', strtotime($item->verified_at)); ?>)</span></div>
                                        <?php endif; ?>
                                        <?php if($item->principal_name): ?>
                                            <div><span class="text-muted">Principal:</span> <strong><?php echo htmlspecialchars($item->principal_name); ?></strong> <span class="text-muted font-monospace">(<?php echo date('d/m H:i', strtotime($item->approved_at)); ?>)</span></div>
                                        <?php endif; ?>
                                        <?php if(!$item->submitter_name && !$item->reviewer_name && !$item->vp_name && !$item->principal_name): ?>
                                            <span class="text-muted fst-italic">Marks not submitted yet</span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td class="text-end pe-4">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <!-- View / Enter Marks -->
                                        <a href="<?php echo URLROOT; ?>/exam/marks/<?php echo $item->exam_id; ?>?class_id=<?php echo $item->class_id; ?>&section_id=<?php echo $item->section_id; ?>&schedule_id=<?php echo $item->id; ?>" class="btn btn-sm btn-outline-secondary px-2 py-1" title="Inspect Marks">
                                            <i class="fa fa-eye me-1"></i> Marks
                                        </a>

                                        <?php if($status === 'submitted_to_class_teacher'): ?>
                                            <!-- Tier 2 Action: Class Teacher Review -->
                                            <form action="<?php echo URLROOT; ?>/exam/processApproval" method="post" class="d-inline">
                                                <input type="hidden" name="schedule_id" value="<?php echo $item->id; ?>">
                                                <input type="hidden" name="exam_id" value="<?php echo $item->exam_id; ?>">
                                                <input type="hidden" name="action" value="review_class_teacher">
                                                <button type="submit" class="btn btn-sm btn-info px-2 py-1 fw-bold text-dark" title="Verify as Class Teacher and Forward to Vice Principal">
                                                    <i class="fa fa-check me-1"></i> Pass to VP
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" onclick="openRejectModal(<?php echo $item->id; ?>, '<?php echo htmlspecialchars(addslashes($item->subject_name)); ?>')" title="Return to Subject Teacher">
                                                <i class="fa fa-rotate-left"></i>
                                            </button>

                                        <?php elseif($status === 'reviewed_by_class_teacher'): ?>
                                            <!-- Tier 3 Action: VP Verification -->
                                            <form action="<?php echo URLROOT; ?>/exam/processApproval" method="post" class="d-inline">
                                                <input type="hidden" name="schedule_id" value="<?php echo $item->id; ?>">
                                                <input type="hidden" name="exam_id" value="<?php echo $item->exam_id; ?>">
                                                <input type="hidden" name="action" value="verify_vp">
                                                <button type="submit" class="btn btn-sm btn-warning px-2 py-1 fw-bold text-dark" title="Verify Academic Standards & Forward to Principal">
                                                    <i class="fa fa-shield-halved me-1"></i> VP Verify
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" onclick="openRejectModal(<?php echo $item->id; ?>, '<?php echo htmlspecialchars(addslashes($item->subject_name)); ?>')" title="Return for Revisions">
                                                <i class="fa fa-rotate-left"></i>
                                            </button>

                                        <?php elseif($status === 'verified_by_vp'): ?>
                                            <!-- Tier 4 Action: Principal Final Approval & Gazette Release -->
                                            <form action="<?php echo URLROOT; ?>/exam/processApproval" method="post" class="d-inline">
                                                <input type="hidden" name="schedule_id" value="<?php echo $item->id; ?>">
                                                <input type="hidden" name="exam_id" value="<?php echo $item->exam_id; ?>">
                                                <input type="hidden" name="action" value="approve_principal">
                                                <button type="submit" class="btn btn-sm btn-success px-2 py-1 fw-bold shadow-sm" title="Authorize & Publish Gazette">
                                                    <i class="fa fa-stamp me-1"></i> Approve & Publish
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" onclick="openRejectModal(<?php echo $item->id; ?>, '<?php echo htmlspecialchars(addslashes($item->subject_name)); ?>')" title="Return for Revisions">
                                                <i class="fa fa-rotate-left"></i>
                                            </button>

                                        <?php elseif($status === 'published' || $status === 'approved_by_principal'): ?>
                                            <span class="badge bg-success-subtle text-success border px-2 py-1">
                                                <i class="fa fa-check-double me-1"></i> Authorized
                                            </span>

                                        <?php elseif($status === 'draft'): ?>
                                            <span class="badge bg-light text-muted border px-2 py-1">
                                                <i class="fa fa-hourglass-start me-1"></i> Pending Teacher
                                            </span>
                                        <?php endif; ?>
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

<!-- Rejection Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="<?php echo URLROOT; ?>/exam/processApproval" method="post">
                <input type="hidden" name="schedule_id" id="rejectScheduleId">
                <input type="hidden" name="exam_id" value="<?php echo $selectedExamId; ?>">
                <input type="hidden" name="action" value="reject">

                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="fa fa-rotate-left me-2"></i>Return Paper for Revisions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="small text-muted mb-3">
                        Returning this paper will unlock marks entry for the Subject Teacher. Please record the specific discrepancy or instruction.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Subject Paper</label>
                        <input type="text" id="rejectSubjectName" class="form-control form-control-sm bg-light fw-bold" readonly>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Reason for Return / Revision <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g. Practical marks missing for Roll # 12, 14. Or total theory marks exceed max limit."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4">
                        <i class="fa fa-rotate-left me-1"></i> Send Back
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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

    function openRejectModal(scheduleId, subjectName){
        document.getElementById('rejectScheduleId').value = scheduleId;
        document.getElementById('rejectSubjectName').value = subjectName;
        var modal = new bootstrap.Modal(document.getElementById('rejectModal'));
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function(){
        if(document.getElementById('classSelect') && document.getElementById('classSelect').value) {
            filterSections();
        }
    });
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
