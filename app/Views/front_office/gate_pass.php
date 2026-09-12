<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$gatePasses = $data['gate_passes'] ?? [];
$students = $data['students'] ?? [];
$approvers = $data['approvers'] ?? [];
$filterDate = $data['filter_date'] ?? null;
$nextPassNo = $data['next_pass_no'] ?? 'GP-' . date('Y') . '-0001';

$totalCount = count($gatePasses);
$departedCount = 0;
foreach($gatePasses as $gp) {
    if($gp->status == 'Departed') $departedCount++;
}
$pendingCount = $totalCount - $departedCount;
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/frontoffice/index" class="text-decoration-none text-muted">Front Office</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Student Gate Passes</li>
            </ol>
        </nav>
        <h3 class="mb-0 fw-bold">Student Early Exit Gate Pass &amp; Child Protection</h3>
        <p class="text-muted small mb-0">Authorized early dismissal tracking, emergency sick leaves, parent/guardian CNIC verification, and gate checkpoint slips.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/frontoffice/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Reception
        </a>
        <button type="button" class="btn btn-warning btn-sm px-3 shadow-sm text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#issueGatePassModal">
            <i class="fa fa-plus-circle me-1"></i> Issue Student Gate Pass
        </button>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <?php if($_GET['success'] == 'issued'): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa fa-check-circle fs-5"></i>
            <div><strong>Gate Pass Issued!</strong> Student early leave authorization has been registered. You can print the gate pass slip now.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif($_GET['success'] == 'departed'): ?>
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa fa-shield-alt fs-5"></i>
            <div><strong>Exit Cleared!</strong> Student has been marked as departed through the security gate checkpoint.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- QUICK STATS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Gate Passes</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?php echo $totalCount; ?></h3>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-3">
                    <i class="fa fa-ticket-alt fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Active / Awaiting Exit</span>
                    <h3 class="fw-bold mb-0 text-primary mt-1"><?php echo $pendingCount; ?></h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="fa fa-hourglass-half fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Gate Cleared / Departed</span>
                    <h3 class="fw-bold mb-0 text-success mt-1"><?php echo $departedCount; ?></h3>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="fa fa-check-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GATE PASSES DIRECTORY -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 bg-warning-subtle text-warning rounded-2">
                <i class="fa fa-door-open"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark">Authorized Gate Passes Register</h6>
                <small class="text-muted">Early leave departures authorized by administration</small>
            </div>
        </div>
        <!-- Date Filter -->
        <form method="get" action="<?php echo URLROOT; ?>/frontoffice/gatePass" class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="fa fa-calendar-alt text-muted"></i></span>
                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filterDate ?: date('Y-m-d')); ?>">
                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
            </div>
            <?php if(!empty($filterDate)): ?>
                <a href="<?php echo URLROOT; ?>/frontoffice/gatePass" class="btn btn-outline-secondary btn-sm" title="Clear Filter">
                    <i class="fa fa-times"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card-body p-0">
        <?php if(empty($gatePasses)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-shield-alt fa-3x mb-3 text-secondary opacity-50"></i>
                <h5>No Student Gate Passes Found</h5>
                <p class="small text-muted mb-3">No early departure gate passes have been recorded for the selected date.</p>
                <button type="button" class="btn btn-warning btn-sm text-dark fw-bold px-4" data-bs-toggle="modal" data-bs-target="#issueGatePassModal">
                    <i class="fa fa-plus-circle me-1"></i> Issue New Gate Pass
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Pass #</th>
                            <th>Student Information</th>
                            <th>Class &amp; Roll #</th>
                            <th>Reason for Departure</th>
                            <th>Collected By (Guardian)</th>
                            <th>Departure Time</th>
                            <th>Authorized By</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($gatePasses as $gp): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-monospace fw-bold">
                                        <?php echo htmlspecialchars($gp->pass_no); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-circle-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 13px;">
                                            <?php echo strtoupper(substr($gp->student_name, 0, 1)); ?>
                                        </div>
                                        <div>
                                            <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $gp->student_id; ?>" class="fw-bold text-dark text-decoration-none hover-primary">
                                                <?php echo htmlspecialchars($gp->student_name); ?>
                                            </a>
                                            <div class="text-muted fs-xs">Adm: <?php echo htmlspecialchars($gp->admission_no); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary"><?php echo htmlspecialchars($gp->class_name ?? 'N/A'); ?></span>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($gp->section_name ?? 'A'); ?></span>
                                    <div class="text-muted fs-xs">Roll: <?php echo htmlspecialchars($gp->roll_no ?: 'None'); ?></div>
                                </td>
                                <td>
                                    <?php
                                    $rBadge = 'bg-danger-subtle text-danger';
                                    if ($gp->reason_type == 'Medical / Sick Bay') $rBadge = 'bg-danger-subtle text-danger';
                                    elseif ($gp->reason_type == 'Family Emergency') $rBadge = 'bg-warning-subtle text-dark';
                                    elseif ($gp->reason_type == 'Doctor Appointment') $rBadge = 'bg-info-subtle text-info';
                                    else $rBadge = 'bg-primary-subtle text-primary';
                                    ?>
                                    <span class="badge <?php echo $rBadge; ?> fw-bold px-2 py-1"><?php echo htmlspecialchars($gp->reason_type); ?></span>
                                    <?php if(!empty($gp->reason_details)): ?>
                                        <div class="text-muted fs-xs text-truncate mt-1" style="max-width: 150px;" title="<?php echo htmlspecialchars($gp->reason_details); ?>">
                                            <?php echo htmlspecialchars($gp->reason_details); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($gp->collected_by_name); ?></div>
                                    <div class="text-muted fs-xs">
                                        <span class="badge bg-light text-dark border px-1"><?php echo htmlspecialchars($gp->collected_by_relation); ?></span>
                                        <?php if(!empty($gp->collected_by_phone)): ?>
                                            &bull; <?php echo htmlspecialchars($gp->collected_by_phone); ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if(!empty($gp->collected_by_cnic)): ?>
                                        <div class="text-primary fs-xs font-monospace"><?php echo htmlspecialchars($gp->collected_by_cnic); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($gp->leave_time); ?></div>
                                    <div class="text-muted fs-xs"><?php echo date('d M, Y', strtotime($gp->pass_date)); ?></div>
                                </td>
                                <td>
                                    <span class="text-secondary fw-semibold"><?php echo htmlspecialchars($gp->approver_name ?: 'Administration'); ?></span>
                                </td>
                                <td>
                                    <?php if($gp->status == 'Departed'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fa fa-check-double me-1"></i>Departed
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1">
                                            <i class="fa fa-hourglass-half me-1"></i>Issued
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo URLROOT; ?>/frontoffice/printGatePass/<?php echo $gp->id; ?>" target="_blank" class="btn btn-outline-warning text-dark fw-bold" title="Print Dual Gate Pass Slip">
                                            <i class="fa fa-print me-1"></i> Slip
                                        </a>
                                        <?php if($gp->status != 'Departed'): ?>
                                            <a href="<?php echo URLROOT; ?>/frontoffice/markGateDeparted/<?php echo $gp->id; ?>" class="btn btn-outline-success" title="Mark Gate Departed" onclick="return confirm('Confirm student departure through main gate?');">
                                                <i class="fa fa-check"></i>
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

<!-- MODAL: ISSUE STUDENT GATE PASS -->
<div class="modal fade" id="issueGatePassModal" tabindex="-1" aria-labelledby="issueGatePassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold" id="issueGatePassModalLabel">
                    <i class="fa fa-door-open me-2"></i> Issue Student Early Exit Gate Pass
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontoffice/gatePass" method="post">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Student Selector -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">Select Student <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select" required>
                                <option value="">-- Choose Enrolled Student --</option>
                                <?php foreach($students as $st): ?>
                                    <option value="<?php echo $st->id; ?>">
                                        <?php echo htmlspecialchars($st->name); ?> (Adm #<?php echo htmlspecialchars($st->admission_no); ?>) &bull; Class <?php echo htmlspecialchars($st->class_name ?? ''); ?> (<?php echo htmlspecialchars($st->section_name ?? ''); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Departure Date & Time -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Departure Date <span class="text-danger">*</span></label>
                            <input type="date" name="pass_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Leaving Time <span class="text-danger">*</span></label>
                            <input type="text" name="leave_time" class="form-control" value="<?php echo date('h:i A'); ?>" required>
                        </div>

                        <!-- Reason Category -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Reason Category <span class="text-danger">*</span></label>
                            <select name="reason_type" class="form-select" required>
                                <option value="Medical / Sick Bay">Medical / Sick Bay (Fever, Injury)</option>
                                <option value="Family Emergency">Family Emergency</option>
                                <option value="Doctor Appointment">Scheduled Doctor Appointment</option>
                                <option value="Early Departure (Parent Request)">Early Departure (Parent Request)</option>
                                <option value="School Representation">School Sports / Debate Representation</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Approving Authority <span class="text-danger">*</span></label>
                            <select name="approved_by_user_id" class="form-select" required>
                                <?php foreach($approvers as $ap): ?>
                                    <option value="<?php echo $ap->id; ?>" <?php echo ($ap->id == ($_SESSION['user_id'] ?? 0)) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ap->name); ?> (<?php echo ucfirst(str_replace('_', ' ', $ap->role)); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Reason Details / Medical Symptoms</label>
                            <textarea name="reason_details" class="form-control" rows="2" placeholder="e.g. Severe headache, sent by Class Teacher with parent telephonic consent."></textarea>
                        </div>

                        <!-- Parent / Guardian Collecting Info -->
                        <div class="col-12">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mt-2">
                                <i class="fa fa-user-shield text-warning me-2"></i>Authorized Person Collecting Student
                            </h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Collector Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="collected_by_name" class="form-control" placeholder="e.g. Tariq Mehmood" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Relationship with Student <span class="text-danger">*</span></label>
                            <select name="collected_by_relation" class="form-select" required>
                                <option value="Father">Father</option>
                                <option value="Mother">Mother</option>
                                <option value="Brother">Brother</option>
                                <option value="Sister">Sister</option>
                                <option value="Uncle / Guardian">Uncle / Guardian</option>
                                <option value="Van Driver / Transporter">Van Driver / Transporter</option>
                                <option value="Self (Senior Student)">Self (Senior Student)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Collector CNIC Number</label>
                            <input type="text" name="collected_by_cnic" class="form-control font-monospace" placeholder="35202-1234567-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Collector Contact / Mobile <span class="text-danger">*</span></label>
                            <input type="text" name="collected_by_phone" class="form-control" placeholder="0300-1234567" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4 text-dark fw-bold">
                        <i class="fa fa-check me-1"></i> Issue Gate Pass &amp; Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
