<?php require APPROOT . '/Views/layouts/header.php'; 
$submittedInfo = $_SESSION['admission_submitted_info'] ?? null;
unset($_SESSION['admission_submitted_info']);
?>

<div class="container-fluid px-0">
    <!-- Header Title & Quick Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/frontoffice/index" class="text-decoration-none text-muted">Front Office</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Online Admission Inbox</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-inbox text-primary me-2"></i>Online Admission Applications Desk
            </h2>
            <small class="text-muted">Centralized inbox for online admission requests submitted via public website. Filter, sort A-Z, &amp; convert to student accounts.</small>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-success btn-sm px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newOnlineAdmissionModal">
                <i class="fa fa-plus-circle me-1"></i> Log Online Application
            </button>
            <button type="button" class="btn btn-outline-info btn-sm px-3 fw-bold shadow-sm" id="topViewRequirementsBtn">
                <i class="fa fa-clipboard-check me-1"></i> Required Documents Checklist
            </button>
            <a href="<?php echo URLROOT; ?>/setting/index?tab=website" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fa fa-sliders me-1"></i> Settings
            </a>
            <a href="<?php echo URLROOT; ?>/students/admission" class="btn btn-primary btn-sm px-3 fw-bold">
                <i class="fa fa-user-plus me-1"></i> Direct Admission
            </a>
        </div>
    </div>

    <?php 
        $lastEnrolled = $_SESSION['last_enrolled_student'] ?? null;
        unset($_SESSION['last_enrolled_student']);
    ?>
    <?php if(!empty($lastEnrolled)): ?>
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #fff; border-radius: 12px;">
            <div class="card-body p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2.5 text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa fa-user-check fa-lg"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-white"><i class="fa fa-check-circle me-1"></i> Student Successfully Approved &amp; Enrolled!</h6>
                        <p class="small mb-0 text-white-50">
                            <strong><?php echo htmlspecialchars($lastEnrolled['name']); ?></strong> has been admitted and enrolled into <strong><?php echo htmlspecialchars($lastEnrolled['class_name']); ?></strong> (Admission No: <strong><?php echo htmlspecialchars($lastEnrolled['admission_no']); ?></strong>, Roll No: <strong><?php echo htmlspecialchars($lastEnrolled['roll_no']); ?></strong>). Application removed from pending inbox.
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <?php if(!empty($lastEnrolled['id'])): ?>
                        <a href="<?php echo URLROOT; ?>/students/printAdmission?student_id=<?php echo $lastEnrolled['id']; ?>" target="_blank" class="btn btn-light btn-sm fw-bold px-3 shadow-xs">
                            <i class="fa fa-print text-primary me-1"></i> Print Admission Form
                        </a>
                        <a href="<?php echo URLROOT; ?>/fees/challan?student_id=<?php echo $lastEnrolled['id']; ?>" target="_blank" class="btn btn-warning btn-sm fw-bold px-3 text-dark shadow-xs">
                            <i class="fa fa-receipt me-1"></i> Generate Fee Slip / Challan
                        </a>
                        <a href="<?php echo URLROOT; ?>/fees/collect?student_id=<?php echo $lastEnrolled['id']; ?>" class="btn btn-success btn-sm fw-bold px-3 shadow-xs">
                            <i class="fa fa-money-bill-wave me-1"></i> Collect Fee Now
                        </a>
                    <?php endif; ?>
                    <?php if(!empty($lastEnrolled['class_id'])): ?>
                        <a href="<?php echo URLROOT; ?>/students/index?class_id=<?php echo $lastEnrolled['class_id']; ?>" class="btn btn-outline-light btn-sm fw-bold px-2">
                            <i class="fa fa-users me-1"></i> Class Roster
                        </a>
                    <?php endif; ?>
                    <?php if(!empty($lastEnrolled['id'])): ?>
                        <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $lastEnrolled['id']; ?>" class="btn btn-outline-light btn-sm fw-bold px-2">
                            <i class="fa fa-id-badge me-1"></i> Profile
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['flash_success']) && empty($lastEnrolled)): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif(isset($_SESSION['flash_success'])): unset($_SESSION['flash_success']); endif; ?>

    <!-- Summary Stats Bar -->
    <?php
        $counts = $data['counts'] ?? (object)[
            'total_all' => count($data['admissions'] ?? []),
            'total_pending' => 0,
            'total_new' => 0,
            'total_follow' => 0,
            'total_approved' => 0,
            'total_rejected' => 0
        ];
    ?>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=all" class="text-decoration-none">
                <div class="card border-0 shadow-sm bg-white p-3 h-100 hover-shadow transition">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                            <i class="fa fa-list-check fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Total Applications</div>
                            <h4 class="fw-bold mb-0 text-dark"><?php echo $counts->total_all; ?></h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=pending" class="text-decoration-none">
                <div class="card border-0 shadow-sm bg-white p-3 h-100 hover-shadow transition <?php echo ($data['filter_status'] === 'pending' || $data['filter_status'] === 'New') ? 'border border-warning border-2' : ''; ?>">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3">
                            <i class="fa fa-inbox fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Pending Inbox (Active)</div>
                            <h4 class="fw-bold mb-0 text-warning"><?php echo $counts->total_pending; ?></h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Follow+Up" class="text-decoration-none">
                <div class="card border-0 shadow-sm bg-white p-3 h-100 hover-shadow transition">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-info bg-opacity-10 text-info p-3">
                            <i class="fa fa-comments fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Contacted / Follow Up</div>
                            <h4 class="fw-bold mb-0 text-info"><?php echo $counts->total_follow; ?></h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Approved" class="text-decoration-none">
                <div class="card border-0 shadow-sm bg-white p-3 h-100 hover-shadow transition <?php echo (strtolower($data['filter_status']) === 'approved' || $data['filter_status'] === 'enrolled') ? 'border border-success border-2' : ''; ?>">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-success bg-opacity-10 text-success p-3">
                            <i class="fa fa-user-check fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold text-uppercase">Enrolled in Class</div>
                            <h4 class="fw-bold mb-0 text-success"><?php echo $counts->total_approved; ?></h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Navigation Tabs between Pending Inbox and Enrolled Archive -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <ul class="nav nav-pills gap-2 m-0">
            <li class="nav-item">
                <a class="nav-link px-3 py-2 fw-bold d-flex align-items-center gap-2 <?php echo ($data['filter_status'] === 'pending' || $data['filter_status'] === 'New') ? 'active bg-primary shadow-sm text-white' : 'bg-white border text-dark'; ?>" 
                   href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=pending&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>">
                    <i class="fa fa-inbox"></i>
                    <span>Pending Inbox (Needs Action)</span>
                    <span class="badge <?php echo ($data['filter_status'] === 'pending' || $data['filter_status'] === 'New') ? 'bg-white text-primary' : 'bg-warning text-dark'; ?>">
                        <?php echo $counts->total_pending; ?>
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-3 py-2 fw-bold d-flex align-items-center gap-2 <?php echo (strtolower($data['filter_status']) === 'approved' || $data['filter_status'] === 'enrolled') ? 'active bg-success shadow-sm text-white' : 'bg-white border text-dark'; ?>" 
                   href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Approved&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>">
                    <i class="fa fa-user-check"></i>
                    <span>Enrolled in Class (Approved)</span>
                    <span class="badge <?php echo (strtolower($data['filter_status']) === 'approved' || $data['filter_status'] === 'enrolled') ? 'bg-white text-success' : 'bg-success-subtle text-success border border-success-subtle'; ?>">
                        <?php echo $counts->total_approved; ?>
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link px-3 py-2 fw-bold d-flex align-items-center gap-2 <?php echo ($data['filter_status'] === 'all') ? 'active bg-dark shadow-sm text-white' : 'bg-white border text-dark'; ?>" 
                   href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=all&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>">
                    <i class="fa fa-list-ul"></i>
                    <span>All Applications</span>
                    <span class="badge <?php echo ($data['filter_status'] === 'all') ? 'bg-white text-dark' : 'bg-light text-muted border'; ?>">
                        <?php echo $counts->total_all; ?>
                    </span>
                </a>
            </li>
            <?php if(!empty($counts->total_rejected)): ?>
            <li class="nav-item">
                <a class="nav-link px-3 py-2 fw-bold d-flex align-items-center gap-2 <?php echo ($data['filter_status'] === 'Rejected') ? 'active bg-danger shadow-sm text-white' : 'bg-white border text-dark'; ?>" 
                   href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Rejected&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>">
                    <i class="fa fa-ban"></i>
                    <span>Rejected</span>
                    <span class="badge <?php echo ($data['filter_status'] === 'Rejected') ? 'bg-white text-danger' : 'bg-danger-subtle text-danger border border-danger-subtle'; ?>">
                        <?php echo $counts->total_rejected; ?>
                    </span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <?php if($data['filter_status'] === 'pending' || empty($data['filter_status'])): ?>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-3 py-2 fw-semibold">
                <i class="fa fa-info-circle me-1"></i> Once approved, applicants are automatically moved to their target class roster.
            </span>
        <?php elseif(strtolower($data['filter_status']) === 'approved'): ?>
            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fw-semibold">
                <i class="fa fa-check-double me-1"></i> Showing students who were admitted and placed into classes.
            </span>
        <?php endif; ?>
    </div>

    <!-- Filter & Sort Control Toolbar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 bg-white rounded-3">
            <form id="filterForm" method="GET" action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" name="search" id="appSearchInput" class="form-control border-start-0 bg-light" placeholder="Search applicant name, phone, father..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <label class="input-group-text bg-light text-muted small"><i class="fa fa-filter me-1"></i> Status</label>
                        <select name="status" class="form-select auto-submit-select">
                            <option value="pending" <?php echo ($data['filter_status'] === 'pending') ? 'selected' : ''; ?>>Pending / Unprocessed</option>
                            <option value="Approved" <?php echo (strtolower($data['filter_status']) === 'approved' || $data['filter_status'] === 'enrolled') ? 'selected' : ''; ?>>Enrolled / Approved</option>
                            <option value="all" <?php echo ($data['filter_status'] === 'all') ? 'selected' : ''; ?>>All Statuses</option>
                            <option value="New" <?php echo ($data['filter_status'] === 'New') ? 'selected' : ''; ?>>New Only</option>
                            <option value="Follow Up" <?php echo ($data['filter_status'] === 'Follow Up') ? 'selected' : ''; ?>>Follow Up / Contacted</option>
                            <option value="Rejected" <?php echo ($data['filter_status'] === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <label class="input-group-text bg-light text-muted small"><i class="fa fa-graduation-cap me-1"></i> Class</label>
                        <select name="class_id" class="form-select auto-submit-select">
                            <option value="all" <?php echo ($data['filter_class'] === 'all') ? 'selected' : ''; ?>>All Target Classes</option>
                            <?php foreach($data['classes'] as $cls): ?>
                                <option value="<?php echo $cls->id; ?>" <?php echo ($data['filter_class'] == $cls->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cls->class_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <label class="input-group-text bg-light text-muted small"><i class="fa fa-sort me-1"></i> Sort By</label>
                        <select name="sort" class="form-select auto-submit-select">
                            <option value="AZ" <?php echo ($data['sort'] === 'AZ') ? 'selected' : ''; ?>>Applicant Name (A &rarr; Z)</option>
                            <option value="ZA" <?php echo ($data['sort'] === 'ZA') ? 'selected' : ''; ?>>Applicant Name (Z &rarr; A)</option>
                            <option value="DATE_DESC" <?php echo ($data['sort'] === 'DATE_DESC') ? 'selected' : ''; ?>>Submission Date (Newest First)</option>
                            <option value="DATE_ASC" <?php echo ($data['sort'] === 'DATE_ASC') ? 'selected' : ''; ?>>Submission Date (Oldest First)</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions Floating Bar -->
    <div id="bulkActionBar" class="card border-0 shadow-lg bg-dark text-white mb-4 d-none" style="border-radius: 12px; border-left: 5px solid #10b981 !important;">
        <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-success px-3 py-2 fs-6 fw-bold">
                    <i class="fa fa-check-square me-2"></i><span id="selectedCount">0</span> Applications Selected
                </span>
                <span class="small text-white-50">Choose a bulk operation to apply to all selected student applications:</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Bulk Approve Form -->
                <form id="bulkAdmissionsForm" action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="POST" class="d-inline-flex gap-2 align-items-center m-0">
                    <button type="submit" name="bulk_action" value="approve" class="btn btn-success btn-sm fw-bold px-3">
                        <i class="fa fa-check-double me-1"></i> Bulk Approve Selected
                    </button>
                    <button type="button" id="bulkPrintBtn" class="btn btn-primary btn-sm fw-bold px-3">
                        <i class="fa fa-print me-1"></i> Print / Download Forms
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            More Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li>
                                <button type="submit" name="bulk_action" value="follow_up" class="dropdown-item py-2 text-info fw-semibold">
                                    <i class="fa fa-comments me-2"></i>Mark as Follow Up
                                </button>
                            </li>
                            <li>
                                <button type="submit" name="bulk_action" value="reject" class="dropdown-item py-2 text-warning fw-semibold">
                                    <i class="fa fa-ban me-2"></i>Mark as Rejected
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button type="submit" name="bulk_action" value="delete" class="dropdown-item py-2 text-danger fw-semibold" onclick="return confirm('Are you sure you want to permanently delete all selected application records?');">
                                    <i class="fa fa-trash-alt me-2"></i>Delete Selected
                                </button>
                            </li>
                        </ul>
                    </div>
                </form>
                <button type="button" id="deselectAllBtn" class="btn btn-outline-secondary btn-sm text-white-50 px-2" title="Clear selection">
                    <i class="fa fa-times me-1"></i> Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Main Applications Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <?php if($data['filter_status'] === 'pending' || $data['filter_status'] === 'New'): ?>
                    <i class="fa fa-inbox text-warning me-2"></i>Pending Applications Inbox (Action Required)
                <?php elseif(strtolower($data['filter_status']) === 'approved' || $data['filter_status'] === 'enrolled'): ?>
                    <i class="fa fa-user-check text-success me-2"></i>Enrolled Students in Classes (Approved Archive)
                <?php else: ?>
                    <i class="fa fa-list text-primary me-2"></i>Online Admission Applications List
                <?php endif; ?>
            </h6>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="headerPrintAllBtn" class="btn btn-outline-secondary btn-sm px-3" title="Print All Currently Filtered Applications">
                    <i class="fa fa-print me-1"></i> Print Current List
                </button>
                <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-1 fw-bold">
                    <?php echo count($data['admissions']); ?> Records
                </span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="admissionsTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3 text-center" style="width: 4%;">
                            <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Select / Deselect All">
                        </th>
                        <th style="width: 4%;">#</th>
                        <th style="width: 21%;">Applicant Student Name</th>
                        <th style="width: 17%;">Father / Guardian</th>
                        <th style="width: 15%;">Contact Phone / Email</th>
                        <th style="width: 12%;">Target Class</th>
                        <th style="width: 10%;">Submission Date</th>
                        <th class="text-center" style="width: 9%;">Status</th>
                        <th class="text-end pe-3" style="width: 8%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['admissions'])): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <?php if($data['filter_status'] === 'pending' || $data['filter_status'] === 'New'): ?>
                                    <div class="py-4">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center p-3 mb-3" style="width: 70px; height: 70px;">
                                            <i class="fa fa-clipboard-check fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">All Caught Up! Inbox is Clean.</h5>
                                        <p class="small text-muted mb-3">There are no pending admission applications waiting for approval right now.<br>All approved students have been placed into their respective class rosters.</p>
                                        <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Approved" class="btn btn-sm btn-outline-success fw-bold px-3">
                                            <i class="fa fa-user-check me-1"></i> View Enrolled Students
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <i class="fa fa-inbox fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                    <h6 class="fw-bold text-dark">No online admission applications found</h6>
                                    <p class="small text-muted mb-0">Try clearing filter criteria or check website online admission form settings.</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($data['admissions'] as $idx => $adm): ?>
                            <tr class="adm-row" data-id="<?php echo $adm->id; ?>" data-name="<?php echo htmlspecialchars(strtolower($adm->name)); ?>" data-father="<?php echo htmlspecialchars(strtolower($adm->father_name ?? '')); ?>" data-phone="<?php echo htmlspecialchars($adm->phone ?? ''); ?>">
                                <td class="ps-3 text-center">
                                    <input type="checkbox" name="admission_ids[]" value="<?php echo $adm->id; ?>" class="form-check-input adm-checkbox" form="bulkAdmissionsForm">
                                </td>
                                <td class="fw-semibold text-muted"><?php echo $idx + 1; ?></td>
                                <td>
                                    <div class="fw-bold text-dark text-capitalize fs-6 mb-0"><?php echo htmlspecialchars($adm->name); ?></div>
                                    <div class="small text-muted">
                                        <span class="badge bg-light text-dark border me-1"><?php echo htmlspecialchars($adm->gender ?? 'N/A'); ?></span>
                                        <?php if(!empty($adm->dob)): ?>
                                            <span>DOB: <?php echo date('d M Y', strtotime($adm->dob)); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?php echo htmlspecialchars($adm->father_name ?: ($adm->guardian_name ?: 'N/A')); ?></div>
                                    <?php if(!empty($adm->guardian_relation)): ?>
                                        <small class="text-muted">(<?php echo htmlspecialchars($adm->guardian_relation); ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><i class="fa fa-phone text-success me-1"></i><?php echo htmlspecialchars($adm->phone); ?></div>
                                    <?php if(!empty($adm->email)): ?>
                                        <small class="text-muted text-truncate d-block" style="max-width: 160px;"><?php echo htmlspecialchars($adm->email); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $isEnrolled = !empty($adm->enrolled_student_id); ?>
                                    <?php if($isEnrolled): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                            <i class="fa fa-check-circle me-1"></i><?php echo htmlspecialchars($adm->class_name ?? 'Class'); ?>
                                            <?php if(!empty($adm->enrolled_section_name)): ?>
                                                &bull; Sec <?php echo htmlspecialchars($adm->enrolled_section_name); ?>
                                            <?php endif; ?>
                                        </span>
                                        <div class="mt-1 small">
                                            <?php if(!empty($adm->enrolled_roll_no)): ?>
                                                <span class="badge bg-light text-dark border me-1" style="font-size:0.72rem;">Roll: <?php echo htmlspecialchars($adm->enrolled_roll_no); ?></span>
                                            <?php endif; ?>
                                            <?php if(!empty($adm->enrolled_admission_no)): ?>
                                                <span class="badge bg-light text-dark border" style="font-size:0.72rem;">Adm: <?php echo htmlspecialchars($adm->enrolled_admission_no); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold">
                                            <i class="fa fa-graduation-cap me-1"></i><?php echo htmlspecialchars($adm->class_name ?? 'Unassigned'); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark"><?php echo date('d M Y', strtotime($adm->date)); ?></div>
                                    <small class="text-muted"><i class="fa fa-globe text-info me-1"></i>Online Form</small>
                                </td>
                                <td class="text-center">
                                    <?php if(!empty($adm->enrolled_student_id)): ?>
                                        <span class="badge bg-success px-2 py-1 fw-bold shadow-xs">
                                            <i class="fa fa-user-check me-1"></i>Enrolled
                                        </span>
                                    <?php else: ?>
                                        <?php 
                                            $st = strtolower($adm->status ?? 'new');
                                            $badgeClass = 'bg-secondary';
                                            if ($st === 'new') $badgeClass = 'bg-warning text-dark';
                                            elseif ($st === 'follow up' || $st === 'contacted') $badgeClass = 'bg-info text-white';
                                            elseif ($st === 'approved') $badgeClass = 'bg-success text-white';
                                            elseif ($st === 'rejected') $badgeClass = 'bg-danger text-white';
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?> px-2 py-1 fw-bold">
                                            <?php echo htmlspecialchars(ucfirst($adm->status ?? 'New')); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3 text-nowrap">
                                    <?php if(!empty($adm->enrolled_student_id)): ?>
                                        <!-- Enrolled Quick Actions: Fee Slip, Collect Fee, Class Roster, Profile -->
                                        <a href="<?php echo URLROOT; ?>/fees/challan?student_id=<?php echo $adm->enrolled_student_id; ?>" target="_blank" class="btn btn-sm btn-warning text-dark fw-bold me-1 shadow-xs" title="Generate / Print Fee Challan Slip">
                                            <i class="fa fa-receipt me-1"></i>Fee Slip
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/fees/collect?student_id=<?php echo $adm->enrolled_student_id; ?>" class="btn btn-sm btn-success text-white fw-bold me-1 shadow-xs" title="Collect Admission Fee">
                                            <i class="fa fa-money-bill-wave me-1"></i>Collect Fee
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/students/index?class_id=<?php echo $adm->enrolled_class_id ?: $adm->class_id; ?>" class="btn btn-sm btn-outline-secondary me-1 shadow-xs" title="Open Class Roster (View in Class)">
                                            <i class="fa fa-users"></i>
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $adm->enrolled_student_id; ?>" class="btn btn-sm btn-outline-primary me-1" title="View Full Student Profile">
                                            <i class="fa fa-id-badge"></i>
                                        </a>
                                    <?php else: ?>
                                        <!-- Not Enrolled: Direct 1-Click / Modal Approve & Place into Class -->
                                        <button type="button" class="btn btn-sm btn-success text-white fw-bold me-1 shadow-xs btn-open-enroll-modal"
                                            data-id="<?php echo $adm->id; ?>" 
                                            data-name="<?php echo htmlspecialchars($adm->name); ?>" 
                                            data-father="<?php echo htmlspecialchars($adm->father_name ?: ($adm->guardian_name ?: 'N/A')); ?>" 
                                            data-class-id="<?php echo $adm->class_id; ?>" 
                                            data-class-name="<?php echo htmlspecialchars($adm->class_name ?? 'Class'); ?>" 
                                            data-phone="<?php echo htmlspecialchars($adm->phone); ?>" 
                                            title="Approve &amp; Enrol into Class">
                                            <i class="fa fa-check-double me-1"></i>Approve &amp; Enrol
                                        </button>
                                    <?php endif; ?>

                                    <!-- Quick Print Button -->
                                    <a href="<?php echo URLROOT; ?>/frontoffice/printAdmissionForm/<?php echo $adm->id; ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-1" title="Print / Download Admission Form">
                                        <i class="fa fa-print"></i>
                                    </a>

                                    <!-- Quick Required Documents Checklist Button -->
                                    <button type="button" class="btn btn-sm btn-outline-info me-1 btn-open-req-docs" 
                                        data-id="<?php echo $adm->id; ?>" 
                                        data-name="<?php echo htmlspecialchars($adm->name); ?>" 
                                        data-father="<?php echo htmlspecialchars($adm->father_name ?: ($adm->guardian_name ?: 'N/A')); ?>" 
                                        data-class="<?php echo htmlspecialchars($adm->class_name ?? 'Not Assigned'); ?>" 
                                        data-phone="<?php echo htmlspecialchars($adm->phone); ?>" 
                                        title="View Required Documents Checklist">
                                        <i class="fa fa-clipboard-check"></i>
                                    </button>

                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Manage
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <?php if(!empty($adm->enrolled_student_id)): ?>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-warning" href="<?php echo URLROOT; ?>/fees/challan?student_id=<?php echo $adm->enrolled_student_id; ?>" target="_blank">
                                                        <i class="fa fa-receipt me-2 text-warning"></i>Generate Fee Slip / Challan
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-success" href="<?php echo URLROOT; ?>/fees/collect?student_id=<?php echo $adm->enrolled_student_id; ?>">
                                                        <i class="fa fa-money-bill-wave me-2 text-success"></i>Collect Admission Fee
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-dark" href="<?php echo URLROOT; ?>/students/printAdmission?student_id=<?php echo $adm->enrolled_student_id; ?>" target="_blank">
                                                        <i class="fa fa-print me-2 text-dark"></i>Print Official Admission Form (A4)
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-success" href="<?php echo URLROOT; ?>/students/index?class_id=<?php echo $adm->enrolled_class_id ?: $adm->class_id; ?>">
                                                        <i class="fa fa-users me-2 text-success"></i>Open Class Student Roster
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-primary" href="<?php echo URLROOT; ?>/students/profile/<?php echo $adm->enrolled_student_id; ?>">
                                                        <i class="fa fa-id-badge me-2 text-primary"></i>View Student Profile
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                            <?php else: ?>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-success btn-open-enroll-modal" href="javascript:void(0)"
                                                        data-id="<?php echo $adm->id; ?>" 
                                                        data-name="<?php echo htmlspecialchars($adm->name); ?>" 
                                                        data-father="<?php echo htmlspecialchars($adm->father_name ?: ($adm->guardian_name ?: 'N/A')); ?>" 
                                                        data-class-id="<?php echo $adm->class_id; ?>" 
                                                        data-class-name="<?php echo htmlspecialchars($adm->class_name ?? 'Class'); ?>" 
                                                        data-phone="<?php echo htmlspecialchars($adm->phone); ?>">
                                                        <i class="fa fa-check-double me-2 text-success"></i>Approve &amp; Enrol into Class
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="post">
                                                        <input type="hidden" name="id" value="<?php echo $adm->id; ?>">
                                                        <input type="hidden" name="update_status" value="1">
                                                        <input type="hidden" name="status" value="Approved">
                                                        <button type="submit" class="dropdown-item py-1 text-success"><i class="fa fa-bolt me-2 text-warning"></i>Quick 1-Click Auto Enrol</button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                            <?php endif; ?>
                                            <li>
                                                <a class="dropdown-item py-2 fw-semibold text-secondary" href="<?php echo URLROOT; ?>/frontoffice/printAdmissionForm/<?php echo $adm->id; ?>" target="_blank">
                                                    <i class="fa fa-print me-2 text-primary"></i>Print / Download Form
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 fw-semibold text-info btn-open-req-docs" href="javascript:void(0)"
                                                    data-id="<?php echo $adm->id; ?>" 
                                                    data-name="<?php echo htmlspecialchars($adm->name); ?>" 
                                                    data-father="<?php echo htmlspecialchars($adm->father_name ?: ($adm->guardian_name ?: 'N/A')); ?>" 
                                                    data-class="<?php echo htmlspecialchars($adm->class_name ?? 'Not Assigned'); ?>" 
                                                    data-phone="<?php echo htmlspecialchars($adm->phone); ?>">
                                                    <i class="fa fa-clipboard-check me-2"></i>Required Documents Checklist
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><h6 class="dropdown-header small text-muted">Other Status</h6></li>
                                            <li>
                                                <form action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="post">
                                                    <input type="hidden" name="id" value="<?php echo $adm->id; ?>">
                                                    <input type="hidden" name="update_status" value="1">
                                                    <input type="hidden" name="status" value="Follow Up">
                                                    <button type="submit" class="dropdown-item py-1 text-info"><i class="fa fa-comments me-2"></i>Mark Follow Up</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="post">
                                                    <input type="hidden" name="id" value="<?php echo $adm->id; ?>">
                                                    <input type="hidden" name="update_status" value="1">
                                                    <input type="hidden" name="status" value="Rejected">
                                                    <button type="submit" class="dropdown-item py-1 text-danger"><i class="fa fa-times me-2"></i>Mark Rejected</button>
                                                </form>
                                            </li>
                                        </ul>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto submit form on select change
    const autoSelects = document.querySelectorAll('.auto-submit-select');
    autoSelects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Instant client-side DOM filtering for typing into search bar
    const searchInput = document.getElementById('appSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#admissionsTable tbody tr.adm-row');
            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const father = row.getAttribute('data-father') || '';
                const phone = row.getAttribute('data-phone') || '';
                if (!query || name.includes(query) || father.includes(query) || phone.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Checkbox and Bulk Operations Handling
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.adm-checkbox');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCountSpan = document.getElementById('selectedCount');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const bulkPrintBtn = document.getElementById('bulkPrintBtn');
    const headerPrintAllBtn = document.getElementById('headerPrintAllBtn');

    function updateBulkState() {
        const checked = Array.from(rowCheckboxes).filter(cb => cb.checked && cb.closest('tr').style.display !== 'none');
        const count = checked.length;
        if (selectedCountSpan) {
            selectedCountSpan.textContent = count;
        }

        if (count > 0) {
            bulkActionBar.classList.remove('d-none');
        } else {
            bulkActionBar.classList.add('d-none');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }

        if (selectAllCheckbox) {
            const visibleRows = Array.from(rowCheckboxes).filter(cb => cb.closest('tr').style.display !== 'none');
            selectAllCheckbox.checked = visibleRows.length > 0 && visibleRows.every(cb => cb.checked);
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            rowCheckboxes.forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
            updateBulkState();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkState);
    });

    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            rowCheckboxes.forEach(cb => { cb.checked = false; });
            updateBulkState();
        });
    }

    // Bulk Print Selected
    if (bulkPrintBtn) {
        bulkPrintBtn.addEventListener('click', function() {
            const checked = Array.from(rowCheckboxes).filter(cb => cb.checked && cb.closest('tr').style.display !== 'none');
            if (checked.length === 0) {
                alert('Please select at least one application to print.');
                return;
            }
            const ids = checked.map(cb => cb.value);
            const printUrl = '<?php echo URLROOT; ?>/frontoffice/printAdmissionForm?ids=' + ids.join(',');
            window.open(printUrl, '_blank');
        });
    }

    // Print all currently visible applications
    if (headerPrintAllBtn) {
        headerPrintAllBtn.addEventListener('click', function() {
            const visibleRows = Array.from(rowCheckboxes).filter(cb => cb.closest('tr').style.display !== 'none');
            if (visibleRows.length === 0) {
                alert('No application records are currently displayed to print.');
                return;
            }
            const ids = visibleRows.map(cb => cb.value);
            const printUrl = '<?php echo URLROOT; ?>/frontoffice/printAdmissionForm?ids=' + ids.join(',');
            window.open(printUrl, '_blank');
        });
    }

    // Required Documents Modal Interactive Data Binding
    const reqDocsModalEl = document.getElementById('requiredDocsModal');
    let reqDocsModal = null;
    if (reqDocsModalEl) {
        reqDocsModal = new bootstrap.Modal(reqDocsModalEl);
    }

    let currentReqAppId = null;

    function openRequirementsForCandidate(id, name, father, className, phone) {
        currentReqAppId = id || null;
        const refEl = document.getElementById('reqCandidateRef');
        const nameEl = document.getElementById('reqCandidateName');
        const fatherEl = document.getElementById('reqCandidateFather');
        const classEl = document.getElementById('reqCandidateClass');

        if (refEl) refEl.textContent = id ? 'ADM-#' + id : 'ADM-GENERAL';
        if (nameEl) nameEl.textContent = name || 'Student Applicant';
        if (fatherEl) fatherEl.textContent = father || 'Father / Guardian';
        if (classEl) classEl.textContent = className || 'All Classes';

        if (reqDocsModal) {
            reqDocsModal.show();
        }
    }

    document.querySelectorAll('.btn-open-req-docs').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const father = this.getAttribute('data-father');
            const className = this.getAttribute('data-class');
            const phone = this.getAttribute('data-phone');
            openRequirementsForCandidate(id, name, father, className, phone);
        });
    });

    const topViewReqBtn = document.getElementById('topViewRequirementsBtn');
    if (topViewReqBtn) {
        topViewReqBtn.addEventListener('click', function() {
            openRequirementsForCandidate('', 'Prospective Student', 'Father / Guardian', 'Standard Admission', '');
        });
    }

    // Print Admission Form from inside Required Docs Modal
    const reqPrintFormBtn = document.getElementById('reqPrintFormBtn');
    if (reqPrintFormBtn) {
        reqPrintFormBtn.addEventListener('click', function() {
            if (currentReqAppId) {
                window.open('<?php echo URLROOT; ?>/frontoffice/printAdmissionForm/' + currentReqAppId, '_blank');
            } else {
                window.open('<?php echo URLROOT; ?>/frontoffice/printAdmissionForm', '_blank');
            }
        });
    }

    // Print Checklist Slip with Printable Format
    const reqPrintChecklistSlipBtn = document.getElementById('reqPrintChecklistSlipBtn');
    if (reqPrintChecklistSlipBtn) {
        reqPrintChecklistSlipBtn.addEventListener('click', function() {
            const printContent = document.getElementById('requiredDocsPrintArea');
            if (!printContent) return;

            const printWin = window.open('', '_blank', 'width=850,height=900');
            printWin.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Admission Document Submission Checklist</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                    <style>
                        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; background: #fff; padding: 25px; color: #1e293b; }
                        .checklist-header { border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 20px; }
                        .checklist-item { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; margin-bottom: 8px; }
                        @media print {
                            body { padding: 0; }
                            .no-print { display: none !important; }
                        }
                    </style>
                </head>
                <body>
                    <div class="text-center checklist-header">
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($dynamicSchoolName ?? SITENAME, ENT_QUOTES, 'UTF-8'); ?></h4>
                        <div class="text-muted small">Admissions Secretariat &bull; Candidate Required Documents Slip</div>
                        <div class="badge bg-dark mt-2">Submission Deadline: Within 3 Working Days</div>
                    </div>
                    ${printContent.innerHTML}
                    <div class="text-center mt-4 pt-3 border-top text-muted small">
                        Please submit all verified original documents and attested photocopies to the Admissions Desk.
                        <br>Office Timings: Monday &ndash; Saturday (8:00 AM &ndash; 2:00 PM)
                    </div>
                    <script>
                        window.onload = function() { window.print(); }
                    <\/script>
                </body>
                </html>
            `);
            printWin.document.close();
        });
    }

    // Auto popup on submission
    <?php if(!empty($submittedInfo)): ?>
        openRequirementsForCandidate(
            '<?php echo (int)$submittedInfo['id']; ?>',
            '<?php echo addslashes($submittedInfo['name']); ?>',
            '<?php echo addslashes($submittedInfo['father_name'] ?? ''); ?>',
            'Registered Class',
            '<?php echo addslashes($submittedInfo['phone'] ?? ''); ?>'
        );
    <?php endif; ?>
});
</script>

<!-- ========================================================================= -->
<!-- MODAL 1: REQUIRED DOCUMENTS SUBMISSION CHECKLIST & INSTRUCTIONS           -->
<!-- ========================================================================= -->
<div class="modal fade" id="requiredDocsModal" tabindex="-1" aria-labelledby="requiredDocsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa fa-clipboard-check fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="requiredDocsModalLabel">Mandatory Required Documents Checklist</h5>
                        <small class="text-white-50">داخلہ فارم اور ضروری دستاویزات کی دفتر میں جمع آوری</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-light" id="requiredDocsPrintArea">
                <!-- Candidate Brief Summary Banner -->
                <div class="card border-0 shadow-sm mb-3 bg-white">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-6 col-md-3 border-end">
                                <span class="text-muted small d-block">Application Ref:</span>
                                <span class="fw-bold text-primary fs-6" id="reqCandidateRef">ADM-<?php echo !empty($submittedInfo['id']) ? '#' . (int)$submittedInfo['id'] : '---'; ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3 border-end">
                                <span class="text-muted small d-block">Candidate Name:</span>
                                <span class="fw-bold text-dark text-capitalize" id="reqCandidateName"><?php echo htmlspecialchars($submittedInfo['name'] ?? 'Candidate'); ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3 border-end">
                                <span class="text-muted small d-block">Father / Guardian:</span>
                                <span class="fw-bold text-dark text-capitalize" id="reqCandidateFather"><?php echo htmlspecialchars($submittedInfo['father_name'] ?? 'Father / Guardian'); ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <span class="text-muted small d-block">Target Class:</span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold" id="reqCandidateClass">Class</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mandatory Notice Callout Banner -->
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start gap-3 mb-4 p-3 rounded-3" role="alert">
                    <i class="fa fa-exclamation-triangle fs-3 text-warning mt-1"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">
                            Important Admission Notice / داخلہ کے لیے ضروری ہدایات
                        </h6>
                        <p class="small text-muted mb-1" style="line-height: 1.5;">
                            آن لائن داخلہ فارم جمع ہونے کے بعد داخلہ کی حتمی تصدیق کے لیے <strong>درج ذیل تمام مطلوبہ دستاویزات کی تصدیق شدہ فوٹو کاپیاں بمعہ پرنٹ شدہ داخلہ فارم</strong> 3 یوم کے اندر اسکول کے داخلہ دفتر میں جمع کروانا لازمی ہیں۔
                        </p>
                        <p class="small text-muted mb-0" style="line-height: 1.5;">
                            All candidates/parents must submit the attested photocopies of mandatory documents listed below along with the <strong>printed and signed admission form</strong> to the School Admissions Desk within <strong>3 working days</strong> for physical verification and enrolment completion.
                        </p>
                    </div>
                </div>

                <!-- Stylized Checklist of Required Documents -->
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fa fa-folder-open text-primary me-2"></i>Mandatory Required Documents Checklist / لازمی دستاویزات کی فہرست
                </h6>

                <div class="list-group shadow-sm border-0 mb-4">
                    <!-- Item 1 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input fs-5" type="checkbox" checked disabled id="doc1">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">1. Printed &amp; Signed Online Admission Application Form</h6>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">Mandatory (1 Copy)</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                Complete printed copy of the online admission form with signatures of Father/Guardian and Student.
                                <br><span class="text-secondary small">آن لائن داخلہ فارم کا پرنٹ بمعہ والد اور امیدوار کے دستخط</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input fs-5" type="checkbox" checked disabled id="doc2">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">2. Passport Sized Colored Photographs</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">4 Photographs</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                4x Recent passport-sized colored photographs of candidate with sky-blue background (candidate name on back).
                                <br><span class="text-secondary small">امیدوار کی 4 عدد پاسپورٹ سائز تازہ تصاویر (نیلے بیک گراؤنڈ کے ساتھ)</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input fs-5" type="checkbox" checked disabled id="doc3">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">3. Father / Guardian CNIC Copies</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">2 Attested Copies</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                2x Attested photocopies of Father's or Legal Guardian's valid National Identity Card (CNIC / NICOP).
                                <br><span class="text-secondary small">والد یا قانونی سرپرست کے شناختی کارڈ کی 2 عدد تصدیق شدہ فوٹو کاپیاں</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input fs-5" type="checkbox" checked disabled id="doc4">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">4. Student NADRA B-Form / Smart Card</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">2 Attested Copies</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                2x Attested photocopies of NADRA Bay-Form (ب-فارم) or Municipal Birth Registration Certificate.
                                <br><span class="text-secondary small">امیدوار کے نادرا ب-فارم یا پیدائش سرٹیفکیٹ کی 2 عدد تصدیق شدہ نقول</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input fs-5" type="checkbox" checked disabled id="doc5">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">5. Original School Leaving Certificate (SLC / Transfer Certificate)</h6>
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle small">Original Certificate</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                Original SLC/Transfer Certificate from previous school (signed &amp; stamped by Head of Institution).
                                <br><span class="text-secondary small">پچھلے اسکول کا اصل اسکول لیونگ سرٹیفکیٹ (SLC) بمعہ 1 عدد کاپی</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 6 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input fs-5" type="checkbox" checked disabled id="doc6">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">6. Previous Examination Report Card / DMC</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">2 Attested Copies</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                2x Attested photocopies of the last annual/term progress report card / detailed marks certificate (DMC).
                                <br><span class="text-secondary small">سابقہ جماعت کے سالانہ امتحانی رزلٹ کارڈ / DMC کی 2 عدد تصدیق شدہ نقول</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 7 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input fs-5" type="checkbox" checked disabled id="doc7">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">7. Character Certificate</h6>
                                <span class="badge bg-secondary-subtle text-secondary border small">For Class 6 &amp; Above</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                Character &amp; Conduct Certificate issued by Head of previous school (mandatory for Class 6 and above).
                                <br><span class="text-secondary small">سابقہ اسکول کا کیریکٹر سرٹیفکیٹ (چھٹی اور اس سے اوپر کی کلاسز کے لیے)</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 8 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white">
                        <div class="form-check pt-1">
                            <input class="form-check-input fs-5" type="checkbox" checked disabled id="doc8">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">8. Student Vaccination / Health Record Card</h6>
                                <span class="badge bg-info-subtle text-info border border-info-subtle small">1 Photocopy</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                Photocopy of government immunization record or basic fitness certificate from a qualified doctor.
                                <br><span class="text-secondary small">حفاظتی ٹیکوں کا کارڈ یا میڈیکل فٹنس سرٹیفکیٹ کی نقل</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Office Schedule Footer Note -->
                <div class="p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="small text-muted">
                        <i class="fa fa-clock text-primary me-2"></i><strong>Admissions Office Timings:</strong> Monday to Saturday, 8:00 AM &ndash; 2:00 PM
                    </div>
                    <div class="small text-muted">
                        <i class="fa fa-map-marker-alt text-danger me-2"></i><strong>Venue:</strong> School Front Office &amp; Reception Desk
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i> Close
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary fw-bold px-3" id="reqPrintFormBtn">
                        <i class="fa fa-print me-1"></i> Print Admission Form
                    </button>
                    <button type="button" class="btn btn-success fw-bold px-3 shadow-sm" id="reqPrintChecklistSlipBtn">
                        <i class="fa fa-file-invoice me-1"></i> Print Requirements Slip
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: LOG NEW ONLINE ADMISSION APPLICATION (DESK ENTRY)                -->
<!-- ========================================================================= -->
<div class="modal fade" id="newOnlineAdmissionModal" tabindex="-1" aria-labelledby="newOnlineAdmissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa fa-plus-circle fs-4"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="newOnlineAdmissionModalLabel">Register Online Admission Application</h5>
                        <small class="text-white-50">Log new applicant particulars received online or via phone enquiry</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions" method="POST" id="newOnlineAdmForm">
                <input type="hidden" name="create_online_admission" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
                <div class="modal-body p-4 bg-light">
                    <div class="card border-0 shadow-sm p-3 mb-3 bg-white">
                        <h6 class="fw-bold text-primary mb-3"><i class="fa fa-user me-2"></i>1. Student Particulars</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Full Name of Student <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Muhammad Ali Khan">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Date of Birth</label>
                                <input type="date" name="dob" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Target Class <span class="text-danger">*</span></label>
                                <select name="class_id" class="form-select" required>
                                    <option value="">-- Select Class --</option>
                                    <?php foreach($data['classes'] as $cls): ?>
                                        <option value="<?php echo $cls->id; ?>"><?php echo htmlspecialchars($cls->class_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NADRA B-Form / Smart Card No.</label>
                                <input type="text" name="bform_cnic" class="form-control" placeholder="e.g. 61101-1234567-1">
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm p-3 mb-3 bg-white">
                        <h6 class="fw-bold text-primary mb-3"><i class="fa fa-users me-2"></i>2. Guardian &amp; Contact Details</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Father's Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="father_name" class="form-control" required placeholder="e.g. Tariq Mehmood Khan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Father's CNIC No.</label>
                                <input type="text" name="father_cnic" class="form-control" placeholder="e.g. 61101-9876543-1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Mobile / Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required placeholder="e.g. 0300-1234567">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="parent@gmail.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Residential Address</label>
                                <input type="text" name="address" class="form-control" placeholder="House #, Street, City">
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm p-3 bg-white">
                        <h6 class="fw-bold text-primary mb-3"><i class="fa fa-school me-2"></i>3. Previous School &amp; History</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Previous School Name</label>
                                <input type="text" name="previous_school" class="form-control" placeholder="e.g. Army Public School">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Last Class Passed</label>
                                <input type="text" name="last_class" class="form-control" placeholder="e.g. Class 7">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Marks / Grade</label>
                                <input type="text" name="last_grade" class="form-control" placeholder="e.g. 85% / Grade A">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top py-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm">
                        <i class="fa fa-save me-1"></i> Register &amp; View Document Checklist
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- ========================================================================= -->
<!-- MODAL 3: APPROVE & ENROL DIRECTLY INTO CLASS                              -->
<!-- ========================================================================= -->
<div class="modal fade" id="approveEnrollModal" tabindex="-1" aria-labelledby="approveEnrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header bg-success text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-white text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa fa-user-check fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="approveEnrollModalLabel">
                            Approve &amp; Enrol into Class
                        </h5>
                        <small class="text-white-50">Assign student to active class roster &amp; section</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="POST" id="approveEnrollForm">
                <input type="hidden" name="enroll_student" value="1">
                <input type="hidden" name="id" id="enrollAdmId" value="">
                <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">

                <div class="modal-body p-4 bg-light">
                    <!-- Applicant Summary Box -->
                    <div class="p-3 bg-white rounded-3 border mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold">Applicant Student</span>
                                <h5 class="fw-bold text-dark mb-0" id="enrollStudentName">Muhammad Ali</h5>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border px-2 py-1" id="enrollTargetClassBadge">Class 8</span>
                        </div>
                        <div class="row g-2 small text-muted">
                            <div class="col-6">
                                <i class="fa fa-user me-1 text-secondary"></i><span id="enrollFatherName">Father Name</span>
                            </div>
                            <div class="col-6">
                                <i class="fa fa-phone me-1 text-success"></i><span id="enrollPhone">0300-1234567</span>
                            </div>
                        </div>
                    </div>

                    <!-- Enrolment Settings Form -->
                    <div class="bg-white p-3 rounded-3 border">
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase">
                            <i class="fa fa-sliders-h text-primary me-1"></i> Class Placement &amp; Roster Details
                        </h6>

                        <div class="row g-3">
                            <!-- Class Selection -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">Target Class <span class="text-danger">*</span></label>
                                <select name="class_id" id="enrollClassSelect" class="form-select" required>
                                    <option value="">-- Select Class --</option>
                                    <?php foreach($data['classes'] as $cls): ?>
                                        <option value="<?php echo $cls->id; ?>"><?php echo htmlspecialchars($cls->class_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Section Selection -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Section</label>
                                <select name="section_id" id="enrollSectionSelect" class="form-select">
                                    <option value="">-- Auto-Assign Default --</option>
                                    <?php if(!empty($data['sections'])): ?>
                                        <?php foreach($data['sections'] as $sec): ?>
                                            <option value="<?php echo $sec->id; ?>" data-class-id="<?php echo $sec->class_id; ?>">
                                                <?php echo htmlspecialchars($sec->section_name); ?> (<?php echo htmlspecialchars($sec->class_name ?? ''); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Academic Session -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Academic Session</label>
                                <select name="academic_session_id" class="form-select">
                                    <?php if(!empty($data['sessions'])): ?>
                                        <?php foreach($data['sessions'] as $sess): ?>
                                            <option value="<?php echo $sess->id; ?>" <?php echo (!empty($sess->is_current) || (!empty($data['current_session']) && $data['current_session']->id == $sess->id)) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sess->session_name); ?> <?php echo !empty($sess->is_current) ? '(Current)' : ''; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">Current Session</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Roll No & Admission No (Auto if left blank) -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Roll Number</label>
                                <input type="text" name="roll_no" class="form-control font-monospace" placeholder="Auto next in class">
                                <span class="text-muted" style="font-size: 0.7rem;">Leave empty to auto-calculate</span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Admission No</label>
                                <input type="text" name="admission_no" class="form-control font-monospace" placeholder="Auto STD-<?php echo date('y'); ?>-xxx">
                                <span class="text-muted" style="font-size: 0.7rem;">Leave empty to auto-generate</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm" id="btnConfirmEnroll">
                        <i class="fa fa-check-circle me-1"></i> Confirm &amp; Enrol into Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Open Enroll Modal and populate applicant fields
    const enrollModalEl = document.getElementById('approveEnrollModal');
    let enrollModal = null;
    if (enrollModalEl && typeof bootstrap !== 'undefined') {
        enrollModal = new bootstrap.Modal(enrollModalEl);
    }

    document.querySelectorAll('.btn-open-enroll-modal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const name = this.dataset.name || 'Applicant';
            const father = this.dataset.father || 'N/A';
            const phone = this.dataset.phone || 'N/A';
            const classId = this.dataset.classId || '';
            const className = this.dataset.className || 'Target Class';

            document.getElementById('enrollAdmId').value = id;
            document.getElementById('enrollStudentName').textContent = name;
            document.getElementById('enrollFatherName').textContent = father;
            document.getElementById('enrollPhone').textContent = phone;
            document.getElementById('enrollTargetClassBadge').textContent = className;

            const classSelect = document.getElementById('enrollClassSelect');
            if (classSelect && classId) {
                classSelect.value = classId;
                filterSectionsByClass(classId);
            }

            if (enrollModal) {
                enrollModal.show();
            }
        });
    });

    const classSelect = document.getElementById('enrollClassSelect');
    if (classSelect) {
        classSelect.addEventListener('change', function() {
            filterSectionsByClass(this.value);
        });
    }

    function filterSectionsByClass(cId) {
        const secSelect = document.getElementById('enrollSectionSelect');
        if (!secSelect) return;
        const options = secSelect.querySelectorAll('option');
        options.forEach(opt => {
            if (!opt.value) {
                opt.style.display = '';
                return;
            }
            const optClass = opt.dataset.classId;
            if (!cId || optClass == cId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
        secSelect.value = '';
    }
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
