<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$stats = $data['stats'] ?? [];
$todayVisitors = $data['today_visitors'] ?? [];
$todayGatePasses = $data['today_gate_passes'] ?? [];
$recentEnquiries = $data['recent_enquiries'] ?? [];
$recentDispatches = $data['recent_dispatches'] ?? [];
$notices = $data['notices'] ?? [];
$latestNews = $data['latest_news'] ?? [];
?>

<!-- Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa fa-building-user text-primary me-2"></i>Front Office &amp; Reception Desk
        </h5>
        <div class="text-muted small">Campus security, visitor check-in, student gate passes, inquiries &amp; dispatches</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo URLROOT; ?>/notice/index" class="btn btn-warning btn-sm px-2.5 py-1.5 shadow-sm fw-bold text-dark" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-bullhorn me-1"></i> Notice Board
        </a>
        <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions" class="btn btn-info btn-sm px-2.5 py-1.5 shadow-sm fw-semibold text-white" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-inbox me-1"></i> Online Admissions
        </a>
        <a href="<?php echo URLROOT; ?>/frontoffice/visitor" class="btn btn-primary btn-sm px-2.5 py-1.5 shadow-sm fw-semibold" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-user-check me-1"></i> Visitor Check-In
        </a>
        <a href="<?php echo URLROOT; ?>/frontoffice/gatePass" class="btn btn-outline-warning btn-sm px-2.5 py-1.5 shadow-sm fw-bold text-dark bg-white" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-door-open me-1"></i> Gate Pass
        </a>
        <a href="<?php echo URLROOT; ?>/frontoffice/enquiry" class="btn btn-success btn-sm px-2.5 py-1.5 shadow-sm fw-semibold" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-user-plus me-1"></i> Inquiries
        </a>
        <a href="<?php echo URLROOT; ?>/frontoffice/dispatch" class="btn btn-outline-secondary btn-sm px-2.5 py-1.5 bg-white fw-semibold" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-envelope-open-text me-1"></i> Dispatches
        </a>
        <a href="<?php echo URLROOT; ?>/frontoffice/callLog" class="btn btn-outline-dark btn-sm px-2.5 py-1.5 bg-white fw-semibold" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-phone-alt me-1"></i> Call Log
        </a>
    </div>
</div>

<!-- NOTICE BOARD POPUP TRIGGER BANNER -->
<?php if(!empty($notices)): 
    $frontNotice = $notices[0];
?>
<div class="alert alert-primary border-0 shadow-sm py-2 px-3 mb-3 d-flex align-items-center justify-content-between gap-3" style="border-radius: 10px; background: #eef2ff;">
    <div class="d-flex align-items-center gap-2 overflow-hidden">
        <span class="badge bg-danger text-uppercase px-2 py-1 fw-bold flex-shrink-0" style="font-size: 0.7rem;">
            <i class="fa fa-bullhorn me-1"></i> Active Circular
        </span>
        <div class="text-truncate small text-dark">
            <strong><?php echo htmlspecialchars($frontNotice->title); ?>:</strong> 
            <span class="text-muted"><?php echo htmlspecialchars(substr(strip_tags($frontNotice->message), 0, 95)); ?>...</span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-shrink-0">
        <button type="button" class="btn btn-primary btn-sm px-2.5 py-1 text-nowrap fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#frontOfficeNoticeModal" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-eye me-1"></i> Read
        </button>
        <a href="<?php echo URLROOT; ?>/notice/index" class="btn btn-outline-primary btn-sm px-2.5 py-1 text-nowrap fw-semibold bg-white" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-bullhorn me-1"></i> Notice Board
        </a>
    </div>
</div>
<?php endif; ?>

<!-- 5 SLEEK COMPACT KPI CARDS -->
<div class="row g-2 mb-3">
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm py-2.5 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; font-size: 1rem;">
                    <i class="fa fa-address-book"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem; font-weight: 600;">TODAY'S VISITORS</div>
                    <div class="d-flex align-items-baseline gap-1">
                        <h5 class="fw-bold mb-0 text-dark"><?php echo $stats['today_visitors'] ?? 0; ?></h5>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5" style="font-size: 0.65rem;">
                            <?php echo $stats['currently_inside'] ?? 0; ?> inside
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm py-2.5 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; font-size: 1rem;">
                    <i class="fa fa-door-open"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem; font-weight: 600;">GATE PASSES</div>
                    <div class="d-flex align-items-baseline gap-1">
                        <h5 class="fw-bold mb-0 text-dark"><?php echo $stats['today_gate_passes'] ?? 0; ?></h5>
                        <span class="text-muted" style="font-size: 0.7rem;">today</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm py-2.5 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; font-size: 1rem;">
                    <i class="fa fa-user-graduate"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem; font-weight: 600;">INQUIRIES</div>
                    <div class="d-flex align-items-baseline gap-1">
                        <h5 class="fw-bold mb-0 text-dark"><?php echo $stats['pending_enquiries'] ?? 0; ?></h5>
                        <span class="text-muted" style="font-size: 0.7rem;">leads</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm py-2.5 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; font-size: 1rem;">
                    <i class="fa fa-envelope-open-text"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem; font-weight: 600;">POSTAL MAIL</div>
                    <div class="d-flex align-items-baseline gap-1">
                        <h5 class="fw-bold mb-0 text-dark"><?php echo $stats['total_dispatches'] ?? 0; ?></h5>
                        <span class="text-muted" style="font-size: 0.7rem;">dispatches</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md">
        <div class="card border-0 shadow-sm py-2.5 px-3 h-100" style="border-radius: 10px; background: #fffdf5; border-left: 3px solid #f59e0b !important;">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; font-size: 1rem;">
                    <i class="fa fa-bullhorn"></i>
                </div>
                <div class="min-w-0 flex-grow-1">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem; font-weight: 600;">NOTICE BOARD</div>
                    <div class="d-flex align-items-baseline gap-1">
                        <h5 class="fw-bold mb-0 text-dark"><?php echo count($notices); ?></h5>
                        <a href="<?php echo URLROOT; ?>/notice/index" class="text-warning text-decoration-none fw-bold small" style="font-size: 0.7rem;">
                            view circulars &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 1: TODAY'S VISITORS & STUDENT GATE PASSES -->
<div class="row g-3 mb-3">
    <!-- LEFT: TODAY'S VISITORS DESK -->
    <div class="col-xl-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; overflow: hidden;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-1.5 bg-primary-subtle text-primary rounded-2">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Today's Visitors on Campus</h6>
                        <div class="text-muted fs-xs">Live entry registry for campus visitors</div>
                    </div>
                </div>
                <a href="<?php echo URLROOT; ?>/frontoffice/visitor" class="btn btn-outline-primary btn-xs px-2 py-1" style="border-radius: 6px; font-size: 0.76rem;">
                    View All <i class="fa fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if(empty($todayVisitors)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-user-clock fa-2x mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0 small">No visitors recorded today.</p>
                        <a href="<?php echo URLROOT; ?>/frontoffice/visitor" class="btn btn-sm btn-link text-primary text-decoration-none small mt-1">
                            <i class="fa fa-plus-circle me-1"></i>Check-in New Visitor
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 85px;">Pass #</th>
                                    <th>Visitor Details</th>
                                    <th>Meeting With</th>
                                    <th>In Time</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($todayVisitors as $v): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($v->pass_no ?: ('VP-' . $v->id)); ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($v->name); ?></div>
                                            <div class="text-muted fs-xs"><?php echo htmlspecialchars($v->contact ?: 'No Phone'); ?></div>
                                            <?php if(!empty($v->cnic_passport)): ?>
                                                <div class="text-primary fs-xs font-monospace"><?php echo htmlspecialchars($v->cnic_passport); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-secondary"><?php echo htmlspecialchars($v->person_to_meet ?: 'Principal Office'); ?></span>
                                            <div class="text-muted fs-xs text-truncate" style="max-width: 140px;"><?php echo htmlspecialchars($v->purpose); ?></div>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-bold"><?php echo htmlspecialchars($v->in_time); ?></span>
                                        </td>
                                        <td>
                                            <?php if(!empty($v->out_time)): ?>
                                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                                    Out: <?php echo htmlspecialchars($v->out_time); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                    <i class="fa fa-dot-circle text-success me-1"></i> Inside
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo URLROOT; ?>/frontoffice/visitorPass/<?php echo $v->id; ?>" target="_blank" class="btn btn-outline-secondary btn-sm py-1 px-2" title="Print Visitor Security Badge">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                                <?php if(empty($v->out_time)): ?>
                                                    <a href="<?php echo URLROOT; ?>/frontoffice/checkoutVisitor/<?php echo $v->id; ?>" class="btn btn-outline-danger btn-sm py-1 px-2" title="Mark Out">
                                                        <i class="fa fa-sign-out-alt"></i> Out
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
    </div>

    <!-- RIGHT: TODAY'S STUDENT GATE PASSES -->
    <div class="col-xl-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; overflow: hidden;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-1.5 bg-warning-subtle text-warning rounded-2">
                        <i class="fa fa-door-open"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Today's Student Gate Passes</h6>
                        <div class="text-muted fs-xs">Authorized early departure logs</div>
                    </div>
                </div>
                <a href="<?php echo URLROOT; ?>/frontoffice/gatePass" class="btn btn-outline-warning btn-xs text-dark fw-bold px-2 py-1" style="border-radius: 6px; font-size: 0.76rem;">
                    View All <i class="fa fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if(empty($todayGatePasses)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-shield-alt fa-2x mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0 small">No student early leave passes issued today.</p>
                        <a href="<?php echo URLROOT; ?>/frontoffice/gatePass" class="btn btn-sm btn-link text-warning text-dark text-decoration-none small mt-1">
                            <i class="fa fa-plus-circle me-1"></i>Issue Gate Pass
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width: 85px;">Pass #</th>
                                    <th>Student &amp; Class</th>
                                    <th>Reason</th>
                                    <th>Picked By</th>
                                    <th>Time</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($todayGatePasses as $gp): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-monospace fw-bold">
                                                <?php echo htmlspecialchars($gp->pass_no); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($gp->student_name); ?></div>
                                            <div class="text-muted fs-xs">
                                                <?php echo htmlspecialchars($gp->class_name ?? ''); ?> (<?php echo htmlspecialchars($gp->section_name ?? ''); ?>) | Adm: <?php echo htmlspecialchars($gp->admission_no); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger-subtle text-danger px-2 py-0.5 fw-bold"><?php echo htmlspecialchars($gp->reason_type); ?></span>
                                            <div class="text-muted fs-xs text-truncate" style="max-width: 130px;"><?php echo htmlspecialchars($gp->reason_details); ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($gp->collected_by_name); ?></div>
                                            <div class="text-muted fs-xs"><?php echo htmlspecialchars($gp->collected_by_relation); ?> (<?php echo htmlspecialchars($gp->collected_by_phone ?: 'No Phone'); ?>)</div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($gp->leave_time); ?></span>
                                            <div>
                                                <?php if($gp->status == 'Departed'): ?>
                                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-0 fs-xs">Departed</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-subtle text-dark px-2 py-0 fs-xs">Issued</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo URLROOT; ?>/frontoffice/printGatePass/<?php echo $gp->id; ?>" target="_blank" class="btn btn-outline-warning btn-sm text-dark fw-bold py-1 px-2" title="Print Dual-Copy Gate Pass">
                                                    <i class="fa fa-print"></i>
                                                </a>
                                                <?php if($gp->status != 'Departed'): ?>
                                                    <a href="<?php echo URLROOT; ?>/frontoffice/markGateDeparted/<?php echo $gp->id; ?>" class="btn btn-outline-success btn-sm py-1 px-2" title="Mark Gate Departed">
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
    </div>
</div>

<!-- ROW 2: ADMISSION LEADS & RECENT DISPATCHES -->
<div class="row g-3 mb-3">
    <!-- RECENT ADMISSION LEADS -->
    <div class="col-xl-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; overflow: hidden;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-1.5 bg-success-subtle text-success rounded-2">
                        <i class="fa fa-user-plus"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Recent Admission Inquiries</h6>
                        <div class="text-muted fs-xs">Prospective candidate follow-ups</div>
                    </div>
                </div>
                <a href="<?php echo URLROOT; ?>/frontoffice/enquiry" class="btn btn-outline-success btn-xs px-2 py-1" style="border-radius: 6px; font-size: 0.76rem;">
                    All Inquiries <i class="fa fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if(empty($recentEnquiries)): ?>
                    <div class="text-center py-4 text-muted small">No recent inquiries logged.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Applicant Name</th>
                                    <th>Class</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Follow-up</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recentEnquiries as $enq): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($enq->name); ?></div>
                                            <div class="text-muted fs-xs">Father: <?php echo htmlspecialchars($enq->father_name ?: 'N/A'); ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($enq->class_name ?: 'General'); ?></span>
                                        </td>
                                        <td>
                                            <div class="text-dark fw-semibold"><?php echo htmlspecialchars($enq->phone); ?></div>
                                            <div class="text-muted fs-xs"><?php echo htmlspecialchars($enq->source ?: 'Walk-in'); ?></div>
                                        </td>
                                        <td>
                                            <?php
                                            $stClass = 'bg-primary-subtle text-primary';
                                            if ($enq->status == 'Converted') $stClass = 'bg-success-subtle text-success';
                                            elseif ($enq->status == 'Follow Up') $stClass = 'bg-warning-subtle text-dark';
                                            elseif ($enq->status == 'Closed') $stClass = 'bg-secondary-subtle text-secondary';
                                            ?>
                                            <span class="badge <?php echo $stClass; ?> px-2 py-0.5 fw-bold"><?php echo htmlspecialchars($enq->status ?: 'New'); ?></span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="text-muted fs-xs"><?php echo !empty($enq->next_follow_up_date) ? date('d M, Y', strtotime($enq->next_follow_up_date)) : 'None'; ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- RECENT POSTAL DISPATCHES -->
    <div class="col-xl-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; overflow: hidden;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-1.5 bg-info-subtle text-info rounded-2">
                        <i class="fa fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Postal Dispatches &amp; Mail</h6>
                        <div class="text-muted fs-xs">Inward official letters &amp; Outward shipments</div>
                    </div>
                </div>
                <a href="<?php echo URLROOT; ?>/frontoffice/dispatch" class="btn btn-outline-info btn-xs px-2 py-1" style="border-radius: 6px; font-size: 0.76rem;">
                    All Dispatches <i class="fa fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if(empty($recentDispatches)): ?>
                    <div class="text-center py-4 text-muted small">No postal dispatch records logged.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Type</th>
                                    <th>Ref / Tracking #</th>
                                    <th>Sender / Addressee</th>
                                    <th>Category</th>
                                    <th class="text-end pe-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recentDispatches as $d): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <?php if($d->dispatch_type == 'Inward'): ?>
                                                <span class="badge bg-success-subtle text-success fw-bold"><i class="fa fa-arrow-down me-1"></i>Inward</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary-subtle text-primary fw-bold"><i class="fa fa-arrow-up me-1"></i>Outward</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold font-monospace text-dark"><?php echo htmlspecialchars($d->reference_no ?: 'No Ref'); ?></div>
                                            <div class="text-muted fs-xs"><?php echo htmlspecialchars($d->courier_name ?? 'Courier'); ?> <?php echo htmlspecialchars($d->tracking_id ? ('(' . $d->tracking_id . ')') : ''); ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($d->sender_title); ?></div>
                                            <div class="text-muted fs-xs">To: <?php echo htmlspecialchars($d->receiver_title); ?></div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($d->category ?: 'General'); ?></span>
                                        </td>
                                        <td class="text-end text-muted fs-xs pe-3">
                                            <?php echo date('d M, Y', strtotime($d->record_date)); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ROW 2.5: CAMPUS NOTICE BOARD & OFFICIAL CIRCULARS CARD -->
<div class="card border-0 shadow-sm mb-3" style="border-radius: 10px; overflow: hidden;">
    <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="p-1.5 bg-warning-subtle text-warning rounded-2">
                <i class="fa fa-bullhorn"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Campus Notice Board &amp; Official Circulars</h6>
                <div class="text-muted fs-xs">Live institutional broadcasts for students, parents, and faculty</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/notice/index" class="btn btn-warning btn-xs px-2.5 py-1 fw-bold text-dark shadow-xs" style="border-radius: 6px; font-size: 0.78rem;">
                <i class="fa fa-clipboard-list me-1"></i> Full Notice Board <i class="fa fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if(empty($notices)): ?>
            <div class="text-center py-4 text-muted small">
                <i class="fa fa-bullhorn fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                No active campus notices posted.
                <div class="mt-1">
                    <a href="<?php echo URLROOT; ?>/notice/index" class="btn btn-sm btn-link text-primary text-decoration-none">Post a Circular on Notice Board</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 140px;">Category</th>
                            <th>Notice Title &amp; Brief Summary</th>
                            <th style="width: 130px;">Audience</th>
                            <th style="width: 120px;">Publish Date</th>
                            <th class="text-end pe-3" style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach(array_slice($notices, 0, 5) as $n): ?>
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 fw-bold">
                                        <?php echo htmlspecialchars($n->notice_type ?? 'General'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 400px;" title="<?php echo htmlspecialchars($n->title); ?>">
                                        <?php echo htmlspecialchars($n->title); ?>
                                    </div>
                                    <div class="text-muted fs-xs text-truncate" style="max-width: 400px;">
                                        <?php echo htmlspecialchars(substr(strip_tags($n->message), 0, 85)); ?>...
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa fa-users text-muted me-1"></i><?php echo htmlspecialchars(ucfirst($n->target_role ?? 'All')); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-dark fw-semibold"><?php echo date('d M, Y', strtotime($n->publish_date ?? 'now')); ?></div>
                                </td>
                                <td class="text-end pe-3 text-nowrap">
                                    <button type="button" class="btn btn-outline-primary btn-xs px-2 py-1 btn-read-notice" 
                                        data-title="<?php echo htmlspecialchars($n->title); ?>"
                                        data-type="<?php echo htmlspecialchars($n->notice_type ?? 'General Notice'); ?>"
                                        data-date="<?php echo date('d M, Y', strtotime($n->publish_date ?? 'now')); ?>"
                                        data-message="<?php echo htmlspecialchars($n->message); ?>"
                                        style="border-radius: 5px; font-size: 0.75rem;">
                                        <i class="fa fa-eye me-1"></i> Read
                                    </button>
                                    <a href="<?php echo URLROOT; ?>/notice/index?search=<?php echo urlencode($n->title); ?>" class="btn btn-light border btn-xs px-2 py-1 text-muted" title="View in Notice Board" style="border-radius: 5px; font-size: 0.75rem;">
                                        <i class="fa fa-external-link-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ROW 3: LATEST INSTITUTIONAL NEWS & UPDATES (FOR RECEPTION DESK) -->
<div class="card border-0 shadow-sm mb-3" style="border-radius: 10px; overflow: hidden;">
    <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
        <span class="fw-bold small text-dark">
            <i class="fa fa-newspaper text-primary me-1.5"></i> Latest Institutional News &amp; Portal Bulletins
        </span>
        <a href="<?php echo URLROOT; ?>/frontcms/news" class="btn btn-outline-primary btn-xs px-2 py-1" style="border-radius: 6px; font-size: 0.76rem;">
            <i class="fa fa-plus-circle me-1"></i> Manage News
        </a>
    </div>
    <div class="card-body p-3">
        <?php if(!empty($latestNews)): ?>
            <div class="row g-3">
                <?php foreach(array_slice($latestNews, 0, 3) as $nItem): 
                    $nImg = !empty($nItem->image) ? URLROOT . '/' . $nItem->image : 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=600&auto=format&fit=crop';
                ?>
                    <div class="col-md-4">
                        <div class="card h-100 border shadow-xs" style="border-radius: 8px; overflow: hidden;">
                            <img src="<?php echo $nImg; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($nItem->title); ?>" style="height: 110px; object-fit: cover;">
                            <div class="card-body p-2.5">
                                <div class="text-muted small mb-1" style="font-size: 0.72rem;">
                                    <i class="fa fa-calendar-alt text-primary me-1"></i> <?php echo date('d M, Y', strtotime($nItem->news_date ?? 'now')); ?>
                                </div>
                                <h6 class="fw-bold mb-1 text-dark text-truncate" style="font-size: 0.85rem;" title="<?php echo htmlspecialchars($nItem->title); ?>">
                                    <?php echo htmlspecialchars($nItem->title); ?>
                                </h6>
                                <p class="text-muted small mb-0" style="font-size: 0.76rem; line-height: 1.35;">
                                    <?php echo htmlspecialchars(substr($nItem->description ?? '', 0, 75)); ?>...
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-3 text-muted small">
                No recent news items published yet. <a href="<?php echo URLROOT; ?>/frontcms/news" class="text-primary text-decoration-none">Publish news now</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- NOTICE POPUP MODAL FOR FRONT OFFICE -->
<?php if(!empty($notices)): 
    $mNotice = $notices[0];
?>
<div class="modal fade" id="frontOfficeNoticeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa fa-bullhorn"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="frontNoticeModalHead">Campus Circular &amp; Announcement</h6>
                        <span class="badge bg-warning text-dark font-monospace" id="frontNoticeModalType" style="font-size: 0.68rem;">
                            <?php echo htmlspecialchars($mNotice->notice_type ?? 'General Notice'); ?>
                        </span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <h5 class="fw-bold text-dark mb-0" id="frontNoticeModalTitle"><?php echo htmlspecialchars($mNotice->title); ?></h5>
                    <span class="text-muted small"><i class="fa fa-calendar-alt text-primary me-1"></i> <span id="frontNoticeModalDate"><?php echo date('d M, Y', strtotime($mNotice->publish_date ?? 'now')); ?></span></span>
                </div>
                <div class="text-secondary lh-base mb-3" id="frontNoticeModalMessage" style="font-size: 0.92rem; white-space: pre-line;">
                    <?php echo htmlspecialchars($mNotice->message); ?>
                </div>

                <?php if(count($notices) > 1): ?>
                    <div class="mt-4 pt-3 border-top">
                        <span class="fw-bold text-dark small text-uppercase mb-2 d-block">Other Active Notices</span>
                        <div class="list-group list-group-flush rounded-2 border">
                            <?php foreach(array_slice($notices, 1, 3) as $oNotice): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 small">
                                    <div class="text-truncate me-2">
                                        <strong><?php echo htmlspecialchars($oNotice->title); ?></strong> &ndash; 
                                        <span class="text-muted"><?php echo htmlspecialchars(substr(strip_tags($oNotice->message), 0, 65)); ?>...</span>
                                    </div>
                                    <span class="text-muted" style="font-size: 0.72rem;"><?php echo date('d M', strtotime($oNotice->publish_date ?? 'now')); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer bg-light px-4 py-2 border-0">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal" style="border-radius: 6px;">Close</button>
                <a href="<?php echo URLROOT; ?>/notice/index" class="btn btn-primary btn-sm px-3 fw-bold" style="border-radius: 6px;">Notice Board Full Archive</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const noticeModalEl = document.getElementById('frontOfficeNoticeModal');
    let noticeModal = null;
    if (noticeModalEl && typeof bootstrap !== 'undefined') {
        noticeModal = new bootstrap.Modal(noticeModalEl);
    }

    document.querySelectorAll('.btn-read-notice').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const title = this.dataset.title || '';
            const type = this.dataset.type || 'General Notice';
            const date = this.dataset.date || '';
            const message = this.dataset.message || '';

            const titleEl = document.getElementById('frontNoticeModalTitle');
            const typeEl = document.getElementById('frontNoticeModalType');
            const dateEl = document.getElementById('frontNoticeModalDate');
            const msgEl = document.getElementById('frontNoticeModalMessage');

            if (titleEl) titleEl.textContent = title;
            if (typeEl) typeEl.textContent = type;
            if (dateEl) dateEl.textContent = date;
            if (msgEl) msgEl.textContent = message;

            if (noticeModal) {
                noticeModal.show();
            }
        });
    });
});
</script>
<?php endif; ?>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
