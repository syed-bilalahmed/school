<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$clearances = $data['clearances'] ?? [];
$stats = $data['stats'] ?? [];
$students = $data['students'] ?? [];
$sessions = $data['sessions'] ?? [];
$filterStatus = $data['filter_status'] ?? null;
$nextClearanceNo = $data['next_clearance_no'] ?? 'CLR-' . date('Y') . '-0001';
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/certificate/hub" class="text-decoration-none text-muted">Certificates</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Institutional Clearance</li>
            </ol>
        </nav>
        <h3 class="mb-0 fw-bold">Student Exit Clearance &amp; Departure Hub</h3>
        <p class="text-muted small mb-0">5-department institutional clearance protocol (Accounts, Library, Science/Computer Lab, Sports, Class Teacher) and one-click leaving package.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/certificate/hub" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-stamp me-1"></i> Credentials Hub
        </a>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#initiateClearanceModal">
            <i class="fa fa-file-signature me-1"></i> Initiate Student Clearance
        </button>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div><strong>Success!</strong> Student clearance application has been registered successfully.</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- KPI TILES -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Applications</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?php echo $stats['total'] ?? 0; ?></h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="fa fa-clipboard-list fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Clearance In Progress</span>
                    <h3 class="fw-bold mb-0 text-warning mt-1"><?php echo $stats['in_progress'] ?? 0; ?></h3>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-3">
                    <i class="fa fa-hourglass-half fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Fully Cleared (Leaving Ready)</span>
                    <h3 class="fw-bold mb-0 text-success mt-1"><?php echo $stats['fully_cleared'] ?? 0; ?></h3>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="fa fa-check-double fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CLEARANCE APPLICATIONS DIRECTORY -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <ul class="nav nav-pills small gap-1">
            <li class="nav-item">
                <a class="nav-link <?php echo empty($filterStatus) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/clearance/index">
                    All Applications
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($filterStatus == 'In Progress') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/clearance/index?status=In+Progress">
                    In Progress
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($filterStatus == 'Fully Cleared') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/clearance/index?status=Fully+Cleared">
                    Fully Cleared
                </a>
            </li>
        </ul>

        <div class="text-muted small">Total: <strong><?php echo count($clearances); ?></strong> applications</div>
    </div>

    <div class="card-body p-0">
        <?php if(empty($clearances)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-clipboard-check fa-3x mb-3 text-secondary opacity-50"></i>
                <h5>No Clearance Records Found</h5>
                <p class="small text-muted mb-3">No student institutional clearance applications matching this status.</p>
                <button type="button" class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#initiateClearanceModal">
                    <i class="fa fa-file-signature me-1"></i> Initiate First Clearance
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Clearance #</th>
                            <th>Student Information</th>
                            <th>Class &amp; Roll #</th>
                            <th>Reason for Leaving</th>
                            <th>Department Sign-Off Matrix</th>
                            <th>Overall Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clearances as $c): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace fw-bold">
                                        <?php echo htmlspecialchars($c->clearance_no); ?>
                                    </span>
                                    <div class="text-muted fs-xs"><?php echo date('d M, Y', strtotime($c->application_date)); ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 13px;">
                                            <?php echo strtoupper(substr($c->student_name, 0, 1)); ?>
                                        </div>
                                        <div>
                                            <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $c->student_id; ?>" class="fw-bold text-dark text-decoration-none hover-primary">
                                                <?php echo htmlspecialchars($c->student_name); ?>
                                            </a>
                                            <div class="text-muted fs-xs">
                                                Father: <?php echo htmlspecialchars($c->father_name ?: 'N/A'); ?> &bull; Adm: <?php echo htmlspecialchars($c->admission_no); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-secondary"><?php echo htmlspecialchars($c->class_name ?? ''); ?></span>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($c->section_name ?? 'A'); ?></span>
                                    <div class="text-muted fs-xs">Roll: <?php echo htmlspecialchars($c->roll_no ?: 'None'); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><?php echo htmlspecialchars($c->reason_for_leaving); ?></span>
                                </td>
                                <td>
                                    <!-- 5-Department Mini Sign-off Pills -->
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge <?php echo ($c->accounts_status == 'Cleared' || $c->accounts_status == 'Waived') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark'; ?> px-2 py-1" title="Accounts / Fee Clearance: <?php echo $c->accounts_status; ?>">
                                            <i class="fa <?php echo ($c->accounts_status == 'Cleared' || $c->accounts_status == 'Waived') ? 'fa-check' : 'fa-clock'; ?> me-1"></i>Fees
                                        </span>
                                        <span class="badge <?php echo ($c->library_status == 'Cleared' || $c->library_status == 'Waived') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark'; ?> px-2 py-1" title="Library Clearance: <?php echo $c->library_status; ?>">
                                            <i class="fa <?php echo ($c->library_status == 'Cleared' || $c->library_status == 'Waived') ? 'fa-check' : 'fa-clock'; ?> me-1"></i>Library
                                        </span>
                                        <span class="badge <?php echo ($c->lab_status == 'Cleared' || $c->lab_status == 'Waived') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark'; ?> px-2 py-1" title="Science & Computer Labs: <?php echo $c->lab_status; ?>">
                                            <i class="fa <?php echo ($c->lab_status == 'Cleared' || $c->lab_status == 'Waived') ? 'fa-check' : 'fa-clock'; ?> me-1"></i>Lab
                                        </span>
                                        <span class="badge <?php echo ($c->sports_status == 'Cleared' || $c->sports_status == 'Waived') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark'; ?> px-2 py-1" title="Sports Department: <?php echo $c->sports_status; ?>">
                                            <i class="fa <?php echo ($c->sports_status == 'Cleared' || $c->sports_status == 'Waived') ? 'fa-check' : 'fa-clock'; ?> me-1"></i>Sports
                                        </span>
                                        <span class="badge <?php echo ($c->class_teacher_status == 'Cleared' || $c->class_teacher_status == 'Waived') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-dark'; ?> px-2 py-1" title="Class Teacher & ID: <?php echo $c->class_teacher_status; ?>">
                                            <i class="fa <?php echo ($c->class_teacher_status == 'Cleared' || $c->class_teacher_status == 'Waived') ? 'fa-check' : 'fa-clock'; ?> me-1"></i>Teacher
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php if($c->overall_status == 'Fully Cleared'): ?>
                                        <span class="badge bg-success text-white border px-2 py-1 fw-bold">
                                            <i class="fa fa-check-double me-1"></i>Fully Cleared
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark border px-2 py-1 fw-bold">
                                            <i class="fa fa-hourglass-half me-1"></i>In Progress
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo URLROOT; ?>/clearance/detail/<?php echo $c->id; ?>" class="btn btn-outline-primary" title="Review & Sign Off">
                                            <i class="fa fa-edit me-1"></i> Review
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/clearance/certificate/<?php echo $c->id; ?>" target="_blank" class="btn btn-outline-secondary" title="Print Official Clearance NOC">
                                            <i class="fa fa-print"></i> NOC
                                        </a>
                                        <?php if($c->overall_status == 'Fully Cleared'): ?>
                                            <a href="<?php echo URLROOT; ?>/clearance/package/<?php echo $c->id; ?>" class="btn btn-success fw-bold" title="Download Complete Leaving Package (SLC, Character, DMC)">
                                                <i class="fa fa-box-open me-1"></i> Package
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL: INITIATE CLEARANCE -->
<div class="modal fade" id="initiateClearanceModal" tabindex="-1" aria-labelledby="initiateClearanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="initiateClearanceModalLabel">
                    <i class="fa fa-file-signature me-2"></i> Initiate Student Exit Clearance
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/clearance/apply" method="post">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">Select Student Applying for Clearance <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select" required>
                                <option value="">-- Choose Enrolled Student --</option>
                                <?php foreach($students as $st): ?>
                                    <option value="<?php echo $st->id; ?>">
                                        <?php echo htmlspecialchars($st->name); ?> (Adm: <?php echo htmlspecialchars($st->admission_no); ?>) &bull; Class <?php echo htmlspecialchars($st->class_name ?? ''); ?> (<?php echo htmlspecialchars($st->section_name ?? ''); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Academic Session</label>
                            <select name="academic_session_id" class="form-select">
                                <?php foreach($sessions as $sess): ?>
                                    <option value="<?php echo $sess->id; ?>" <?php echo ($sess->is_current) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sess->session_name); ?> <?php echo ($sess->is_current) ? '(Active)' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Application Date <span class="text-danger">*</span></label>
                            <input type="date" name="application_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Reason for Leaving / Withdrawal <span class="text-danger">*</span></label>
                            <select name="reason_for_leaving" class="form-select" required>
                                <option value="Completed Matriculation (Class 10th Passing)">Completed Matriculation (Class 10th Passing)</option>
                                <option value="Parent Relocation / Out of City">Parent Relocation / Out of City</option>
                                <option value="Inter-School Migration to Another Board">Inter-School Migration to Another Board</option>
                                <option value="Financial Hardship / Personal Reasons">Financial Hardship / Personal Reasons</option>
                                <option value="Admission in Higher College / Cadet College">Admission in Higher College / Cadet College</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info py-2 small mb-0 d-flex align-items-center gap-2">
                                <i class="fa fa-info-circle fs-5"></i>
                                <div>
                                    Initiating clearance generates a formal tracking dossier across Accounts, Library, Laboratories, Sports, and Class Teacher. Once cleared by all 5 departments, the student can be officially marked as Left and their Leaving Package (SLC/DMC) unlocked.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fa fa-play me-1"></i> Start Clearance Workflow
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
