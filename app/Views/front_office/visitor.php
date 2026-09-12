<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$visitors = $data['visitors'] ?? [];
$filterDate = $data['filter_date'] ?? null;
$nextPassNo = $data['next_pass_no'] ?? 'VP-' . date('Y') . '-0001';

$totalCount = count($visitors);
$insideCount = 0;
foreach($visitors as $v) {
    if(empty($v->out_time) && ($v->status == 'Checked In' || empty($v->status))) {
        $insideCount++;
    }
}
$checkedOutCount = $totalCount - $insideCount;

$siteSettings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];
$schoolName = !empty($data['settings']->school_name) ? $data['settings']->school_name : (!empty($siteSettings['school_name']) ? $siteSettings['school_name'] : SITENAME);
?>

<!-- Header & Action Controls -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/frontoffice/index" class="text-decoration-none text-muted">Front Office</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Visitor Book</li>
            </ol>
        </nav>
        <h3 class="mb-0 fw-bold">Visitor Management &amp; Security Gate Book</h3>
        <p class="text-muted small mb-0"><strong class="text-dark"><?php echo htmlspecialchars($schoolName); ?></strong> &bull; Record visitors, issue official gate security passes, log CNICs, notes &amp; departure timestamps.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/frontoffice/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Reception Desk
        </a>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newVisitorModal">
            <i class="fa fa-user-plus me-1"></i> New Visitor Check-In
        </button>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <?php if($_GET['success'] == 'checked_in'): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa fa-check-circle fs-5"></i>
            <div><strong>Success!</strong> Visitor has been logged and checked in successfully.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif($_GET['success'] == 'checked_out'): ?>
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa fa-info-circle fs-5"></i>
            <div><strong>Updated!</strong> Visitor exit timestamp has been recorded and marked as Checked Out.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- QUICK STATS TILES -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Visitors</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?php echo $totalCount; ?></h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="fa fa-users fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Currently on Campus</span>
                    <h3 class="fw-bold mb-0 text-success mt-1"><?php echo $insideCount; ?></h3>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="fa fa-clock fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Departed / Checked Out</span>
                    <h3 class="fw-bold mb-0 text-secondary mt-1"><?php echo $checkedOutCount; ?></h3>
                </div>
                <div class="p-3 bg-secondary-subtle text-secondary rounded-3">
                    <i class="fa fa-sign-out-alt fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MAIN VISITOR DIRECTORY CARD -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 bg-primary-subtle text-primary rounded-2">
                <i class="fa fa-address-book"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark">Security Gate &amp; Reception Log</h6>
                <small class="text-muted">Filtered visitor register</small>
            </div>
        </div>
        <!-- Date Filter Form -->
        <form method="get" action="<?php echo URLROOT; ?>/frontoffice/visitor" class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="fa fa-calendar-alt text-muted"></i></span>
                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filterDate ?: date('Y-m-d')); ?>">
                <button type="submit" class="btn btn-primary btn-sm px-3">Filter</button>
            </div>
            <?php if(!empty($filterDate)): ?>
                <a href="<?php echo URLROOT; ?>/frontoffice/visitor" class="btn btn-outline-secondary btn-sm" title="Clear Filter">
                    <i class="fa fa-times"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card-body p-0">
        <?php if(empty($visitors)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-id-card-alt fa-3x mb-3 text-secondary opacity-50"></i>
                <h5>No Visitors Recorded</h5>
                <p class="small text-muted mb-3">There are no visitor entries matching the selected date.</p>
                <button type="button" class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#newVisitorModal">
                    <i class="fa fa-user-plus me-1"></i> Check-In First Visitor
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Pass #</th>
                            <th>Visitor Details</th>
                            <th>Host / Person to Meet</th>
                            <th>Purpose &amp; Department</th>
                            <th>Vehicle No</th>
                            <th>Date &amp; Timing</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($visitors as $v): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace fw-bold">
                                        <?php echo htmlspecialchars($v->pass_no ?: ('VP-' . $v->id)); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($v->name); ?></div>
                                    <div class="text-muted fs-xs"><i class="fa fa-phone me-1"></i><?php echo htmlspecialchars($v->contact ?: 'Not Given'); ?></div>
                                    <?php if(!empty($v->cnic_passport)): ?>
                                        <div class="text-primary fs-xs font-monospace">
                                            <i class="fa fa-id-card me-1"></i><?php echo htmlspecialchars($v->cnic_passport); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-secondary"><?php echo htmlspecialchars($v->person_to_meet ?: 'Principal Office'); ?></span>
                                    <div class="text-muted fs-xs">
                                        Persons: <span class="badge bg-light text-dark border px-1"><?php echo htmlspecialchars($v->no_of_person ?: 1); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary px-2 py-0.5 fw-bold"><?php echo htmlspecialchars($v->purpose); ?></span>
                                    <div class="text-muted fs-xs mt-1"><?php echo htmlspecialchars($v->department ?: 'Administration'); ?></div>
                                    <?php if(!empty($v->note)): ?>
                                        <div class="mt-1">
                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle fs-xs text-truncate d-inline-block" style="max-width: 140px;" title="<?php echo htmlspecialchars($v->note); ?>">
                                                <i class="fa fa-sticky-note text-warning me-1"></i><?php echo htmlspecialchars($v->note); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($v->vehicle_no)): ?>
                                        <span class="badge bg-light text-dark border font-monospace">
                                            <i class="fa fa-car me-1 text-muted"></i><?php echo htmlspecialchars($v->vehicle_no); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted fs-xs">On Foot</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-dark fw-bold"><?php echo date('d M, Y', strtotime($v->date)); ?></div>
                                    <div class="text-muted fs-xs">
                                        In: <strong class="text-dark"><?php echo htmlspecialchars($v->in_time); ?></strong>
                                        <?php if(!empty($v->out_time)): ?>
                                            &bull; Out: <strong class="text-danger"><?php echo htmlspecialchars($v->out_time); ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if(!empty($v->out_time)): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                            <i class="fa fa-check-circle me-1"></i>Checked Out
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fa fa-dot-circle text-success me-1"></i>Inside Campus
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-info" title="View Full Profile &amp; Security Notes" data-bs-toggle="modal" data-bs-target="#viewVisitorModal_<?php echo $v->id; ?>">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        <a href="<?php echo URLROOT; ?>/frontoffice/visitorPass/<?php echo $v->id; ?>" target="_blank" class="btn btn-outline-primary" title="Print Security Pass Token / Save PDF">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        <?php if(empty($v->out_time)): ?>
                                            <a href="<?php echo URLROOT; ?>/frontoffice/checkoutVisitor/<?php echo $v->id; ?>" class="btn btn-outline-danger" title="Mark Visitor Checked Out" onclick="return confirm('Confirm visitor check-out from campus?');">
                                                <i class="fa fa-sign-out-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- MODALS FOR DETAILED VISITOR & NOTES VIEW -->
            <?php foreach($visitors as $v): ?>
            <div class="modal fade" id="viewVisitorModal_<?php echo $v->id; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                        <div class="modal-header bg-dark text-white py-3 px-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="p-2 bg-primary rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="fa fa-id-card"></i>
                                </div>
                                <div>
                                    <h6 class="modal-title fw-bold mb-0 text-white">
                                        Visitor Record &mdash; <?php echo htmlspecialchars($v->pass_no ?: ('VP-' . $v->id)); ?>
                                    </h6>
                                    <small class="text-white-50"><?php echo htmlspecialchars($schoolName); ?> Campus Security</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($v->name); ?></h4>
                                    <div class="text-muted small"><i class="fa fa-phone me-1"></i><?php echo htmlspecialchars($v->contact ?: 'No contact number'); ?></div>
                                </div>
                                <div>
                                    <?php if(!empty($v->out_time)): ?>
                                        <span class="badge bg-secondary px-3 py-2 fs-6">
                                            <i class="fa fa-check-circle me-1"></i> Checked Out
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success px-3 py-2 fs-6">
                                            <i class="fa fa-dot-circle me-1"></i> Inside Campus
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-3 bg-light border">
                                        <span class="text-muted small fw-bold d-block text-uppercase mb-1">Identification &amp; Transport</span>
                                        <div><strong>CNIC / ID:</strong> <span class="font-monospace text-primary"><?php echo htmlspecialchars($v->cnic_passport ?: 'Not Provided'); ?></span></div>
                                        <div class="mt-1"><strong>Vehicle No:</strong> <span class="badge bg-white text-dark border"><?php echo htmlspecialchars($v->vehicle_no ?: 'On Foot'); ?></span></div>
                                        <div class="mt-1"><strong>Total Visitors:</strong> <?php echo htmlspecialchars($v->no_of_person ?: 1); ?> Person(s)</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-3 bg-light border">
                                        <span class="text-muted small fw-bold d-block text-uppercase mb-1">Host &amp; Meeting Objective</span>
                                        <div><strong>Host / Meeting With:</strong> <span class="text-danger fw-bold"><?php echo htmlspecialchars($v->person_to_meet ?: 'Principal Office'); ?></span></div>
                                        <div class="mt-1"><strong>Department:</strong> <?php echo htmlspecialchars($v->department ?: 'Administration'); ?></div>
                                        <div class="mt-1"><strong>Purpose:</strong> <span class="badge bg-primary-subtle text-primary"><?php echo htmlspecialchars($v->purpose); ?></span></div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 rounded-3 bg-light border">
                                        <span class="text-muted small fw-bold d-block text-uppercase mb-1">Security Timestamps</span>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <span class="text-muted small">Date:</span> <strong><?php echo date('d M, Y', strtotime($v->date)); ?></strong>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="text-muted small">Check-In Time:</span> <strong class="text-success"><?php echo htmlspecialchars($v->in_time); ?></strong>
                                            </div>
                                            <div class="col-sm-4">
                                                <span class="text-muted small">Check-Out Time:</span> <strong class="text-danger"><?php echo htmlspecialchars($v->out_time ?: 'Not yet departed'); ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 rounded-3 bg-warning-subtle border border-warning-subtle">
                                        <span class="text-dark small fw-bold d-block text-uppercase mb-1">
                                            <i class="fa fa-sticky-note text-warning me-1"></i> Security Notes &amp; Special Remarks
                                        </span>
                                        <div class="text-dark mt-1" style="white-space: pre-wrap; font-size: 0.92rem;">
                                            <?php echo !empty($v->note) ? nl2br(htmlspecialchars($v->note)) : '<span class="text-muted fst-italic">No special notes recorded for this visitor.</span>'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2.5 px-4 border-top d-flex justify-content-between">
                            <div>
                                <a href="<?php echo URLROOT; ?>/frontoffice/visitorPass/<?php echo $v->id; ?>" target="_blank" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm">
                                    <i class="fa fa-print me-1"></i> Open &amp; Print Pass / PDF
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <?php if(empty($v->out_time)): ?>
                                    <a href="<?php echo URLROOT; ?>/frontoffice/checkoutVisitor/<?php echo $v->id; ?>" class="btn btn-outline-danger btn-sm px-3" onclick="return confirm('Confirm visitor check-out from campus?');">
                                        <i class="fa fa-sign-out-alt me-1"></i> Check-Out Now
                                    </a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL: NEW VISITOR CHECK-IN -->
<div class="modal fade" id="newVisitorModal" tabindex="-1" aria-labelledby="newVisitorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="newVisitorModalLabel">
                    <i class="fa fa-id-badge me-2"></i> Visitor Check-In &amp; Pass Issuance
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontoffice/visitor" method="post">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Visitor Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Muhammad Aslam" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Contact / Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="contact" class="form-control" placeholder="0300-1234567" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">CNIC / ID Card Number</label>
                            <input type="text" name="cnic_passport" class="form-control font-monospace" placeholder="35202-1234567-1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">ID Document Type</label>
                            <select name="id_proof" class="form-select">
                                <option value="CNIC">NADRA CNIC</option>
                                <option value="Driving License">Driving License</option>
                                <option value="Passport">Passport</option>
                                <option value="Official Service Card">Official Service Card</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Purpose of Visit <span class="text-danger">*</span></label>
                            <select name="purpose" class="form-select" required>
                                <option value="Parent Teacher Meeting">Parent Teacher Meeting</option>
                                <option value="Fee Deposit &amp; Accounts">Fee Deposit &amp; Accounts</option>
                                <option value="Admission Enquiry">Admission Enquiry</option>
                                <option value="Vendor / Supply Delivery">Vendor / Supply Delivery</option>
                                <option value="Official Board / Govt Inspection">Official Board / Govt Inspection</option>
                                <option value="Meeting with Principal">Meeting with Principal</option>
                                <option value="General Visit">General Visit</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Host / Person to Meet <span class="text-danger">*</span></label>
                            <input type="text" name="person_to_meet" class="form-control" placeholder="e.g. Principal / Mr. Tariq (Accounts)" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Department</label>
                            <select name="department" class="form-select">
                                <option value="Administration">Administration</option>
                                <option value="Accounts">Accounts &amp; Fee Section</option>
                                <option value="Academics">Academics / Faculty</option>
                                <option value="Examination">Examination Cell</option>
                                <option value="Principal Secretariat">Principal Secretariat</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Vehicle Reg No.</label>
                            <input type="text" name="vehicle_no" class="form-control font-monospace" placeholder="e.g. LEA-4590 / Bike 70cc">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Number of Persons</label>
                            <input type="number" name="no_of_person" class="form-control" value="1" min="1" max="20">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Entry Date</label>
                            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">In Time</label>
                            <input type="text" name="in_time" class="form-control" value="<?php echo date('h:i A'); ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Remarks / Security Notes</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Specific notes or security clearance information..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fa fa-save me-1"></i> Check In &amp; Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
