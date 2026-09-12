<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$student = $data['student'];
$profile = $data['profile'];
$siblings = $profile['siblings'] ?? [];
$attendance = $profile['attendance'] ?? [];
$attSummary = $attendance['summary'] ?? null;
$attLogs = $attendance['logs'] ?? [];
$attPercent = $attendance['percentage'] ?? 100;
$finance = $profile['finance'] ?? [];
$fees = $finance['fees'] ?? [];
$payments = $finance['payments'] ?? [];
$exams = $profile['exams'] ?? [];
$clearance = $data['clearance'] ?? null;

// Calculate Age from DOB
$ageStr = 'N/A';
if (!empty($student->dob)) {
    try {
        $dobDate = new DateTime($student->dob);
        $now = new DateTime();
        $ageStr = $dobDate->diff($now)->y . ' Years';
    } catch(Exception $e) {}
}

// Status Color helper
$statusClass = 'bg-success';
if ($student->status == 'Left') $statusClass = 'bg-secondary';
elseif ($student->status == 'Suspended') $statusClass = 'bg-danger';
elseif ($student->status == 'Alumni' || $student->status == 'Graduated') $statusClass = 'bg-primary';

// Balance Color
$balance = (float)($finance['balance'] ?? 0);
$balanceColor = $balance > 0 ? 'text-danger' : 'text-success';
?>

<!-- Print-Only Styling -->
<style>
@media print {
    .top-navbar, .sidebar, .sidebar-overlay, .no-print, .btn, .nav-tabs {
        display: none !important;
    }
    .content-area, .main-content-container {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .card {
        border: 1px solid #ccc !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
    .tab-pane {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
}
.kpi-metric-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 14px;
    transition: all 0.2s ease;
}
.kpi-metric-box:hover {
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.nav-pills-custom .nav-link {
    color: #64748b;
    font-weight: 600;
    border-radius: 10px;
    padding: 10px 18px;
    transition: all 0.2s;
    border: 1px solid transparent;
}
.nav-pills-custom .nav-link.active {
    background: var(--primary, #4f46e5);
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
}
.nav-pills-custom .nav-link:hover:not(.active) {
    background: #f1f5f9;
    color: #0f172a;
}
.profile-page-wrapper {
    max-width: 100%;
    overflow-x: hidden;
}
.profile-page-wrapper .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.profile-page-wrapper .list-group-item {
    word-break: break-word;
}
</style>

<div class="profile-page-wrapper">

<!-- Top Navigation & Action Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 no-print">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/students/index" class="text-decoration-none text-muted">Students</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page"><?php echo htmlspecialchars($student->name); ?></li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h3 class="mb-0 fw-bold"><?php echo htmlspecialchars($student->name); ?></h3>
            <span class="badge <?php echo $statusClass; ?> rounded-pill px-3 py-1"><?php echo htmlspecialchars($student->status ?? 'Active'); ?></span>
            <?php if(!empty($clearance)): ?>
                <a href="<?php echo URLROOT; ?>/clearance/detail/<?php echo $clearance->id; ?>" class="badge <?php echo $clearance->overall_status == 'Fully Cleared' ? 'bg-success text-white' : 'bg-warning text-dark'; ?> text-decoration-none rounded-pill px-3 py-1" title="Institutional Exit Clearance Status">
                    <i class="fa fa-clipboard-check me-1"></i> Clearance: <?php echo htmlspecialchars($clearance->overall_status); ?>
                </a>
            <?php endif; ?>
            <?php if(!empty($student->family_id)): ?>
                <a href="<?php echo URLROOT; ?>/families/index" class="badge bg-purple text-white text-decoration-none rounded-pill px-3 py-1" style="background: #8b5cf6;" title="Family Group Unit">
                    <i class="fa fa-users me-1"></i> <?php echo htmlspecialchars($student->family_id); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Directory
        </a>
        <a href="<?php echo URLROOT; ?>/students/edit/<?php echo $student->id; ?>" class="btn btn-primary btn-sm px-3">
            <i class="fa fa-edit me-1"></i> Edit Profile
        </a>
        <a href="<?php echo URLROOT; ?>/students/printAdmission/<?php echo $student->id; ?>" target="_blank" class="btn btn-dark btn-sm px-3 shadow-xs">
            <i class="fa fa-file-invoice me-1 text-warning"></i> Official A4 Admission Form
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="fa fa-print me-1"></i> Print 360° View
        </button>
        <div class="dropdown d-inline-block">
            <button class="btn btn-outline-dark btn-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-certificate me-1 text-warning"></i> Certificates
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li><h6 class="dropdown-header text-uppercase fs-xs fw-bold text-muted">Issue Credentials</h6></li>
                <li>
                    <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/certificate/slc/<?php echo $student->id; ?>" target="_blank">
                        <i class="fa fa-file-contract text-danger me-2"></i> School Leaving Certificate (SLC)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/certificate/character/<?php echo $student->id; ?>" target="_blank">
                        <i class="fa fa-award text-primary me-2"></i> Character Certificate
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/certificate/bonafide/<?php echo $student->id; ?>" target="_blank">
                        <i class="fa fa-stamp text-success me-2"></i> Bonafide / Enrollment Cert.
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/frontoffice/gatePass" target="_blank">
                        <i class="fa fa-door-open text-warning me-2"></i> Issue Student Gate Pass
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><h6 class="dropdown-header text-uppercase fs-xs fw-bold text-muted">Exit & Clearance</h6></li>
                <li>
                    <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/clearance/index">
                        <i class="fa fa-clipboard-check text-info me-2"></i> Exit Clearance Hub
                    </a>
                </li>
                <?php if(!empty($clearance)): ?>
                    <li>
                        <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/clearance/detail/<?php echo $clearance->id; ?>">
                            <i class="fa fa-tasks text-primary me-2"></i> Clearance Form (#<?php echo htmlspecialchars($clearance->clearance_no); ?>)
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/clearance/certificate/<?php echo $clearance->id; ?>" target="_blank">
                            <i class="fa fa-file-invoice text-success me-2"></i> Print Clearance NOC
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/clearance/package/<?php echo $clearance->id; ?>">
                            <i class="fa fa-archive text-purple me-2" style="color: #8b5cf6;"></i> Exit Credentials Package
                        </a>
                    </li>
                <?php endif; ?>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item py-2 text-primary" href="<?php echo URLROOT; ?>/certificate/hub">
                        <i class="fa fa-external-link-alt me-2"></i> Credentials Hub
                    </a>
                </li>
            </ul>
        </div>
        <a href="<?php echo URLROOT; ?>/fees/challan/<?php echo $student->id; ?>" target="_blank" class="btn btn-outline-primary btn-sm px-3">
            <i class="fa fa-receipt me-1"></i> Bank Challan
        </a>
        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-success btn-sm px-3">
            <i class="fa fa-cash-register me-1"></i> Fee Counter
        </a>
    </div>
</div>

<?php if(isset($_GET['success']) && $_GET['success'] == 'updated'): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 no-print" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div><strong>Success!</strong> Student profile record has been updated successfully.</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- LEFT COLUMN: Identity & 360 Core Cards -->
    <div class="col-xl-4 col-lg-5">
        <!-- Student Hero Card -->
        <div class="card shadow-sm border-0 mb-4 text-center overflow-hidden">
            <div style="height: 90px; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);"></div>
            <div class="card-body pt-0 position-relative" style="margin-top: -50px;">
                <div class="position-relative d-inline-block mb-3">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student->name); ?>&size=120&background=random&bold=true" 
                         class="rounded-circle border border-4 border-white shadow-md" width="110" height="110" alt="<?php echo htmlspecialchars($student->name); ?>">
                    <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white rounded-circle" title="Active Account"></span>
                </div>
                
                <h4 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($student->name); ?></h4>
                <p class="text-muted small mb-2"><?php echo htmlspecialchars($student->email ?? 'No email assigned'); ?></p>

                <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
                    <span class="badge bg-primary px-3 py-2" style="font-size: 0.82rem;">
                        <i class="fa fa-graduation-cap me-1"></i> <?php echo htmlspecialchars($student->class_name ?? 'Class'); ?> - <?php echo htmlspecialchars($student->section_name ?? 'Sec'); ?>
                    </span>
                    <?php if(!empty($student->session_name)): ?>
                        <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 0.82rem;">
                            <i class="fa fa-calendar-alt me-1 text-primary"></i> <?php echo htmlspecialchars($student->session_name); ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Mini Key Metrics Row -->
                <div class="row g-2 text-center pt-3 border-top">
                    <div class="col-4">
                        <div class="kpi-metric-box">
                            <div class="text-muted smaller text-uppercase fw-bold" style="font-size: 0.7rem;">Roll No</div>
                            <div class="h6 mb-0 fw-bold text-dark"><?php echo htmlspecialchars($student->roll_no ?: '-'); ?></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="kpi-metric-box">
                            <div class="text-muted smaller text-uppercase fw-bold" style="font-size: 0.7rem;">Attendance</div>
                            <div class="h6 mb-0 fw-bold <?php echo $attPercent >= 80 ? 'text-success' : ($attPercent >= 65 ? 'text-warning' : 'text-danger'); ?>">
                                <?php echo $attPercent; ?>%
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="kpi-metric-box">
                            <div class="text-muted smaller text-uppercase fw-bold" style="font-size: 0.7rem;">Fee Dues</div>
                            <div class="h6 mb-0 fw-bold <?php echo $balanceColor; ?>">
                                <?php echo number_format($balance); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pakistani Civil Identity & Vitals Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <span class="fw-bold text-dark"><i class="fa fa-id-card text-primary me-2"></i>Civil & Identity Records</span>
                <span class="badge badge-soft-info" style="font-family: monospace;"><?php echo htmlspecialchars($student->admission_no); ?></span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Admission No</span>
                        <span class="fw-bold text-dark font-monospace"><?php echo htmlspecialchars($student->admission_no); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Registration / Enrolment #</span>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($student->reg_no ?: 'None'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">NADRA B-Form / CNIC</span>
                        <span class="fw-bold text-primary font-monospace"><?php echo htmlspecialchars($student->bform_cnic ?: 'Not Recorded'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Date of Birth & Age</span>
                        <span class="fw-bold text-dark">
                            <?php echo !empty($student->dob) ? date('d M, Y', strtotime($student->dob)) : 'N/A'; ?> 
                            <small class="text-muted">(<?php echo $ageStr; ?>)</small>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Gender</span>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($student->gender ?? 'Male'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Blood Group</span>
                        <?php if(!empty($student->blood_group)): ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold">
                                <i class="fa fa-tint me-1"></i><?php echo htmlspecialchars($student->blood_group); ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">Unspecified</span>
                        <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Admission Date</span>
                        <span class="fw-bold text-dark"><?php echo !empty($student->admission_date) ? date('d M, Y', strtotime($student->admission_date)) : 'N/A'; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Previous School</span>
                        <span class="text-dark text-truncate" style="max-width: 170px;" title="<?php echo htmlspecialchars($student->previous_school ?? ''); ?>">
                            <?php echo htmlspecialchars($student->previous_school ?: 'Fresh Admission'); ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Family Unit & Concession Policy Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <span class="fw-bold text-dark"><i class="fa fa-users text-purple me-2" style="color: #8b5cf6;"></i>Family & Fee Policy</span>
                <?php if(!empty($student->family_id)): ?>
                    <span class="badge bg-purple-subtle text-purple fw-bold font-monospace"><?php echo htmlspecialchars($student->family_id); ?></span>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Father / Guardian Name</span>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($student->father_name ?: ($student->guardian_name ?: 'N/A')); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Father CNIC</span>
                        <span class="fw-bold text-dark font-monospace"><?php echo htmlspecialchars($student->father_cnic ?: 'N/A'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Mother Name</span>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($student->mother_name ?: 'N/A'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Primary Phone</span>
                        <?php if(!empty($student->parent_phone)): ?>
                            <a href="tel:<?php echo htmlspecialchars($student->parent_phone); ?>" class="fw-bold text-success text-decoration-none">
                                <i class="fa fa-phone-alt me-1"></i><?php echo htmlspecialchars($student->parent_phone); ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Concession Category</span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                            <?php echo htmlspecialchars($student->concession_type ?? 'Standard'); ?>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Sibling Discount Rate</span>
                        <span class="fw-bold text-success"><?php echo (float)($student->sibling_discount_percent ?? 0); ?>% Off</span>
                    </li>
                    <?php if(!empty($student->custom_discount_amount) && (float)$student->custom_discount_amount > 0): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                            <span class="text-muted">Special Relief / Subsidy</span>
                            <span class="fw-bold text-primary">Rs. <?php echo number_format((float)$student->custom_discount_amount); ?></span>
                        </li>
                    <?php endif; ?>
                    <li class="list-group-item py-2 px-3">
                        <div class="text-muted mb-1">Residential Address:</div>
                        <div class="text-dark small"><?php echo nl2br(htmlspecialchars($student->address ?: 'No address specified.')); ?></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: 360° Comprehensive Tabs -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <!-- Navigation Tabs -->
            <div class="card-header bg-white p-3 border-0 border-bottom">
                <ul class="nav nav-pills nav-pills-custom gap-2 flex-wrap" id="student360Tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center gap-2" id="overview-tab" data-bs-toggle="pill" data-bs-target="#tab-overview" type="button" role="tab">
                            <i class="fa fa-id-badge"></i> <span>Overview</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="siblings-tab" data-bs-toggle="pill" data-bs-target="#tab-siblings" type="button" role="tab">
                            <i class="fa fa-users"></i> <span>Siblings</span>
                            <span class="badge bg-white text-dark rounded-pill ms-1"><?php echo count($siblings); ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="attendance-tab" data-bs-toggle="pill" data-bs-target="#tab-attendance" type="button" role="tab">
                            <i class="fa fa-calendar-check"></i> <span>Attendance</span>
                            <span class="badge bg-white text-dark rounded-pill ms-1"><?php echo $attPercent; ?>%</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="fees-tab" data-bs-toggle="pill" data-bs-target="#tab-fees" type="button" role="tab">
                            <i class="fa fa-file-invoice-dollar"></i> <span>Fees & Ledger</span>
                            <?php if($balance > 0): ?>
                                <span class="badge bg-danger text-white rounded-pill ms-1">Due</span>
                            <?php else: ?>
                                <span class="badge bg-success text-white rounded-pill ms-1">Clear</span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="exams-tab" data-bs-toggle="pill" data-bs-target="#tab-exams" type="button" role="tab">
                            <i class="fa fa-award"></i> <span>Exam Results</span>
                            <span class="badge bg-white text-dark rounded-pill ms-1"><?php echo count($exams); ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="certificates-tab" data-bs-toggle="pill" data-bs-target="#tab-certificates" type="button" role="tab">
                            <i class="fa fa-certificate text-warning"></i> <span>Certificates</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="student360TabContent">

                    <!-- TAB 1: OVERVIEW & PLACEMENT -->
                    <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                            <i class="fa fa-graduation-cap text-primary"></i> Academic Placement & Guardian Dossier
                        </h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Academic Enrolment</h6>
                                    <table class="table table-borderless table-sm mb-0 small">
                                        <tr>
                                            <td class="text-muted" style="width: 45%;">Academic Session:</td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($student->session_name ?? 'Default Session'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Assigned Class:</td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($student->class_name ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Section:</td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($student->section_name ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Class Roll Number:</td>
                                            <td class="fw-bold text-primary font-monospace"><?php echo htmlspecialchars($student->roll_no ?: 'None'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Admission Status:</td>
                                            <td><span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($student->status ?? 'Active'); ?></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Parents & Guardian Profile</h6>
                                    <table class="table table-borderless table-sm mb-0 small">
                                        <tr>
                                            <td class="text-muted" style="width: 45%;">Father's Name:</td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($student->father_name ?: 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Father's CNIC:</td>
                                            <td class="fw-bold font-monospace"><?php echo htmlspecialchars($student->father_cnic ?: 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Mother's Name:</td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($student->mother_name ?: 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Legal Guardian:</td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($student->guardian_name ?: 'Father'); ?> (<?php echo htmlspecialchars($student->guardian_relation ?: 'Parent'); ?>)</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Emergency Phone:</td>
                                            <td class="fw-bold text-success"><?php echo htmlspecialchars($student->parent_phone ?: 'N/A'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Concession & Fee Relief Overview -->
                        <div class="p-3 border rounded-3 bg-white mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-bold text-dark mb-0">Fee Policy & Concession Structure</h6>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                                    <?php echo (float)($student->sibling_discount_percent ?? 0); ?>% Sibling Relief
                                </span>
                            </div>
                            <p class="text-muted small mb-3">
                                Automated billing applies sibling concession tiers and custom fee relief on tuition fee categories during monthly voucher generations.
                            </p>
                            <div class="row g-2 text-center small">
                                <div class="col-md-4">
                                    <div class="p-2 border rounded bg-light">
                                        <div class="text-muted">Category</div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($student->concession_type ?? 'Standard'); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 border rounded bg-light">
                                        <div class="text-muted">Sibling Discount Rate</div>
                                        <div class="fw-bold text-success"><?php echo (float)($student->sibling_discount_percent ?? 0); ?>%</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 border rounded bg-light">
                                        <div class="text-muted">Special Relief / Concession</div>
                                        <div class="fw-bold text-primary">Rs. <?php echo number_format((float)($student->custom_discount_amount ?? 0)); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Previous School / Transfer Info -->
                        <?php if(!empty($student->previous_school)): ?>
                            <div class="p-3 border rounded-3 bg-light">
                                <h6 class="fw-bold text-secondary mb-2">Transfer & Previous Education Background</h6>
                                <p class="text-muted small mb-0">
                                    <i class="fa fa-school text-primary me-2"></i>
                                    Attended <strong><?php echo htmlspecialchars($student->previous_school); ?></strong> prior to enrolling in this institution.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 2: FAMILY & SIBLINGS -->
                    <div class="tab-pane fade" id="tab-siblings" role="tabpanel">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">
                                    <i class="fa fa-users text-purple me-2" style="color: #8b5cf6;"></i>Family Group: <?php echo htmlspecialchars($student->family_id ?: 'Unlinked Family'); ?>
                                </h5>
                                <p class="text-muted small mb-0">
                                    Brothers and sisters enrolled in the same campus under Father CNIC <strong><?php echo htmlspecialchars($student->father_cnic ?: 'N/A'); ?></strong>.
                                </p>
                            </div>
                            <?php if(!empty($student->family_id)): ?>
                                <a href="<?php echo URLROOT; ?>/families/index" class="btn btn-outline-primary btn-sm px-3">
                                    <i class="fa fa-users-cog me-1"></i> Manage Family Units
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Policy Info Banner -->
                        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 mb-4" style="background: rgba(6, 182, 212, 0.08);">
                            <i class="fa fa-info-circle text-info fs-4"></i>
                            <div class="small">
                                <strong>Pakistani School Multi-Tier Sibling Discount Policy:</strong>
                                1st Child: 0% Concession | 2nd Child: 10% Sibling Relief | 3rd+ Child: 20% Sibling Relief on monthly tuition fee.
                            </div>
                        </div>

                        <?php if(empty($siblings)): ?>
                            <div class="text-center py-5 border rounded-3 bg-light">
                                <i class="fa fa-user-friends fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">No Enrolled Siblings Detected</h6>
                                <p class="text-muted small mb-3" style="max-width: 420px; margin: 0 auto;">
                                    There are currently no other active students enrolled with the same Family Code or Father CNIC (<?php echo htmlspecialchars($student->father_cnic ?: 'N/A'); ?>).
                                </p>
                                <a href="<?php echo URLROOT; ?>/students/admission" class="btn btn-primary btn-sm px-4">
                                    <i class="fa fa-user-plus me-1"></i> Admit New Sibling
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Sibling Name</th>
                                            <th>Admission #</th>
                                            <th>Class & Section</th>
                                            <th>Roll #</th>
                                            <th>B-Form / CNIC</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($siblings as $sib): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($sib->name); ?>&size=36&background=random" class="rounded-circle" width="32" height="32" alt="">
                                                        <div>
                                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($sib->name); ?></div>
                                                            <span class="badge bg-purple-subtle text-purple" style="font-size: 0.7rem;">Family Member</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-soft-primary font-monospace"><?php echo htmlspecialchars($sib->admission_no); ?></span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($sib->class_name ?? 'N/A'); ?></span>
                                                    <small class="text-muted">(<?php echo htmlspecialchars($sib->section_name ?? 'N/A'); ?>)</small>
                                                </td>
                                                <td class="fw-bold"><?php echo htmlspecialchars($sib->roll_no ?: '-'); ?></td>
                                                <td class="font-monospace small text-muted"><?php echo htmlspecialchars($sib->bform_cnic ?: 'N/A'); ?></td>
                                                <td>
                                                    <span class="badge <?php echo ($sib->status == 'Active') ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?php echo htmlspecialchars($sib->status ?? 'Active'); ?>
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $sib->id; ?>" class="btn btn-outline-primary btn-sm px-3">
                                                        <i class="fa fa-user me-1"></i> View 360°
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 3: ATTENDANCE ANALYTICS -->
                    <div class="tab-pane fade" id="tab-attendance" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-calendar-alt text-primary me-2"></i>Attendance Performance Analytics
                            </h5>
                            <a href="<?php echo URLROOT; ?>/attendance/student" class="btn btn-outline-primary btn-sm px-3">
                                <i class="fa fa-clipboard-check me-1"></i> Attendance Portal
                            </a>
                        </div>

                        <!-- 4 Stat Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-3 col-6">
                                <div class="p-3 border rounded-3 bg-light text-center">
                                    <div class="text-muted small fw-bold">TOTAL DAYS</div>
                                    <div class="h4 mb-0 fw-bold text-dark"><?php echo $attSummary->total_days ?? 0; ?></div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="p-3 border rounded-3 bg-success-subtle text-center">
                                    <div class="text-success small fw-bold">PRESENT</div>
                                    <div class="h4 mb-0 fw-bold text-success"><?php echo $attSummary->present_days ?? 0; ?></div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="p-3 border rounded-3 bg-danger-subtle text-center">
                                    <div class="text-danger small fw-bold">ABSENT</div>
                                    <div class="h4 mb-0 fw-bold text-danger"><?php echo $attSummary->absent_days ?? 0; ?></div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="p-3 border rounded-3 bg-warning-subtle text-center">
                                    <div class="text-warning small fw-bold">LATE / HALF</div>
                                    <div class="h4 mb-0 fw-bold text-warning">
                                        <?php echo ($attSummary->late_days ?? 0) + ($attSummary->half_days ?? 0); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Rate Progress -->
                        <div class="p-3 border rounded-3 bg-white mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark small">Attendance Ratio</span>
                                <span class="fw-bold <?php echo $attPercent >= 80 ? 'text-success' : 'text-danger'; ?>"><?php echo $attPercent; ?>%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar <?php echo $attPercent >= 80 ? 'bg-success' : ($attPercent >= 65 ? 'bg-warning' : 'bg-danger'); ?>" 
                                     role="progressbar" style="width: <?php echo $attPercent; ?>%;" aria-valuenow="<?php echo $attPercent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 smaller text-muted">
                                <span>Minimum Requirement: 75%</span>
                                <span>Status: <?php echo $attPercent >= 75 ? '<span class="text-success fw-bold">Compliant</span>' : '<span class="text-danger fw-bold">Short Attendance</span>'; ?></span>
                            </div>
                        </div>

                        <!-- Recent Logs Table -->
                        <h6 class="fw-bold text-dark mb-3">Recent 15 Attendance Records</h6>
                        <?php if(empty($attLogs)): ?>
                            <div class="text-center py-4 border rounded-3 bg-light text-muted small">
                                <i class="fa fa-calendar-times me-1"></i> No daily attendance logs recorded yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle border">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Day</th>
                                            <th>Attendance Status</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($attLogs as $log): 
                                            $badgeClass = 'bg-success';
                                            if($log->attendance_type == 'Absent') $badgeClass = 'bg-danger';
                                            elseif($log->attendance_type == 'Late') $badgeClass = 'bg-warning text-dark';
                                            elseif($log->attendance_type == 'Half Day') $badgeClass = 'bg-info';
                                        ?>
                                            <tr>
                                                <td class="fw-bold text-dark font-monospace"><?php echo date('d M, Y', strtotime($log->date)); ?></td>
                                                <td class="text-muted small"><?php echo date('l', strtotime($log->date)); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $badgeClass; ?> px-2 py-1">
                                                        <?php echo htmlspecialchars($log->attendance_type); ?>
                                                    </span>
                                                </td>
                                                <td class="text-muted small"><?php echo htmlspecialchars($log->remark ?: '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 4: FEES & FINANCIAL LEDGER -->
                    <div class="tab-pane fade" id="tab-fees" role="tabpanel">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">
                                    <i class="fa fa-file-invoice-dollar text-success me-2"></i>Student Financial Ledger
                                </h5>
                                <p class="text-muted small mb-0">
                                    Assigned monthly tuition fees, admission/exam charges, auto-synced payments & receipts.
                                </p>
                            </div>
                            <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-success btn-sm px-4">
                                <i class="fa fa-cash-register me-1"></i> Collect Fee
                            </a>
                        </div>

                        <!-- Financial Summary Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-3 col-6">
                                <div class="p-3 border rounded-3 bg-light text-center">
                                    <div class="text-muted small fw-bold">TOTAL ASSIGNED</div>
                                    <div class="h5 mb-0 fw-bold text-dark">Rs. <?php echo number_format($finance['total_assigned'] ?? 0); ?></div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="p-3 border rounded-3 bg-info-subtle text-center">
                                    <div class="text-info small fw-bold">CONCESSION / RELIEF</div>
                                    <div class="h5 mb-0 fw-bold text-info">Rs. <?php echo number_format($finance['discount_amount'] ?? 0); ?></div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="p-3 border rounded-3 bg-success-subtle text-center">
                                    <div class="text-success small fw-bold">TOTAL PAID</div>
                                    <div class="h5 mb-0 fw-bold text-success">Rs. <?php echo number_format($finance['total_paid'] ?? 0); ?></div>
                                </div>
                            </div>
                            <div class="col-sm-3 col-6">
                                <div class="p-3 border rounded-3 <?php echo $balance > 0 ? 'bg-danger-subtle' : 'bg-light'; ?> text-center">
                                    <div class="<?php echo $balance > 0 ? 'text-danger' : 'text-muted'; ?> small fw-bold">OUTSTANDING BALANCE</div>
                                    <div class="h5 mb-0 fw-bold <?php echo $balanceColor; ?>">Rs. <?php echo number_format($balance); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Fee Invoices Table -->
                        <h6 class="fw-bold text-dark mb-3">Assigned Fee Invoices</h6>
                        <?php if(empty($fees)): ?>
                            <div class="text-center py-4 border rounded-3 bg-light text-muted small mb-4">
                                <i class="fa fa-receipt me-1"></i> No fee invoices currently assigned to this student.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive mb-4">
                                <table class="table table-hover align-middle border small">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Fee Group / Type</th>
                                            <th>Due Date</th>
                                            <th>Amount (Rs.)</th>
                                            <th>Paid (Rs.)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($fees as $f): 
                                            $isPaid = (float)$f->total_paid >= (float)$f->amount;
                                            $isPartial = (float)$f->total_paid > 0 && !$isPaid;
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($f->group_name); ?></div>
                                                    <small class="text-muted"><?php echo htmlspecialchars($f->type_name); ?></small>
                                                </td>
                                                <td class="font-monospace text-muted">
                                                    <?php echo !empty($f->due_date) ? date('d M, Y', strtotime($f->due_date)) : 'N/A'; ?>
                                                </td>
                                                <td class="fw-bold text-dark">Rs. <?php echo number_format((float)$f->amount); ?></td>
                                                <td class="fw-bold text-success">Rs. <?php echo number_format((float)$f->total_paid); ?></td>
                                                <td>
                                                    <?php if($isPaid): ?>
                                                        <span class="badge bg-success">Paid</span>
                                                    <?php elseif($isPartial): ?>
                                                        <span class="badge bg-warning text-dark">Partial</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Unpaid</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Payment Receipts History Table -->
                        <h6 class="fw-bold text-dark mb-3">Fee Payment Receipts History</h6>
                        <?php if(empty($payments)): ?>
                            <div class="text-center py-4 border rounded-3 bg-light text-muted small">
                                <i class="fa fa-hand-holding-usd me-1"></i> No payment receipts logged yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border small">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Receipt #</th>
                                            <th>Payment Date</th>
                                            <th>Fee Type</th>
                                            <th>Amount Paid</th>
                                            <th>Payment Mode</th>
                                            <th>Notes / Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($payments as $p): ?>
                                            <tr>
                                                <td class="fw-bold font-monospace text-primary">RCPT-<?php echo str_pad($p->id, 5, '0', STR_PAD_LEFT); ?></td>
                                                <td class="text-muted"><?php echo date('d M, Y', strtotime($p->payment_date)); ?></td>
                                                <td class="text-dark"><?php echo htmlspecialchars($p->type_name ?? 'Fee Payment'); ?></td>
                                                <td class="fw-bold text-success">Rs. <?php echo number_format((float)$p->amount); ?></td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="fa fa-wallet me-1 text-secondary"></i><?php echo htmlspecialchars($p->payment_mode ?? 'Cash'); ?>
                                                    </span>
                                                </td>
                                                <td class="text-muted"><?php echo htmlspecialchars($p->note ?: '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 5: EXAM RESULTS -->
                    <div class="tab-pane fade" id="tab-exams" role="tabpanel">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-award text-warning me-2"></i>Examinations & Academic History
                            </h5>
                            <div class="d-flex align-items-center gap-2">
                                <?php if(!empty($student->class_id) && !empty($student->section_id)): ?>
                                    <a href="<?php echo URLROOT; ?>/exam/gazette?class_id=<?php echo $student->class_id; ?>&section_id=<?php echo $student->section_id; ?>" class="btn btn-outline-secondary btn-sm px-3">
                                        <i class="fa fa-scroll me-1"></i> Class Gazette
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo URLROOT; ?>/exam/index" class="btn btn-outline-primary btn-sm px-3">
                                    <i class="fa fa-clipboard-list me-1"></i> Examination Hub
                                </a>
                            </div>
                        </div>

                        <?php if(empty($exams)): ?>
                            <div class="text-center py-5 border rounded-3 bg-light">
                                <i class="fa fa-graduation-cap fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">No Examination Records Found</h6>
                                <p class="text-muted small mb-0">
                                    Examination marks and grading will appear here once term/final exams are scheduled and marks entered.
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border small">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Exam Name</th>
                                            <th>Subject</th>
                                            <th>Exam Date</th>
                                            <th>Full Marks</th>
                                            <th>Passing Marks</th>
                                            <th>Marks Obtained</th>
                                            <th>Percentage</th>
                                            <th>Status</th>
                                            <th class="text-end pe-3">Detailed DMC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($exams as $ex): 
                                            $full = (float)($ex->full_marks ?: 100);
                                            $obtained = (float)($ex->marks_obtained ?: 0);
                                            $percent = $full > 0 ? round(($obtained / $full) * 100, 1) : 0;
                                            $isPass = $obtained >= (float)($ex->passing_marks ?: 40);
                                        ?>
                                            <tr>
                                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($ex->exam_name); ?></td>
                                                <td><?php echo htmlspecialchars($ex->subject_name); ?></td>
                                                <td class="text-muted"><?php echo !empty($ex->date_of_exam) ? date('d M, Y', strtotime($ex->date_of_exam)) : 'N/A'; ?></td>
                                                <td class="fw-bold"><?php echo $full; ?></td>
                                                <td class="text-muted"><?php echo $ex->passing_marks ?: 40; ?></td>
                                                <td class="fw-bold text-primary"><?php echo $obtained; ?></td>
                                                <td class="fw-bold"><?php echo $percent; ?>%</td>
                                                <td>
                                                    <?php if($isPass): ?>
                                                        <span class="badge bg-success">Passed</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Failed</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end pe-3">
                                                    <?php if(!empty($ex->exam_id)): ?>
                                                        <a href="<?php echo URLROOT; ?>/exam/reportCard/<?php echo $ex->exam_id; ?>/<?php echo $student->id; ?>" target="_blank" class="btn btn-sm btn-outline-primary px-2 py-1" title="Print Official DMC Progress Report">
                                                            <i class="fa fa-id-card me-1"></i> Print DMC
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 6: CERTIFICATES & CREDENTIALS -->
                    <div class="tab-pane fade" id="tab-certificates" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="fa fa-certificate text-warning"></i> Official Certificates & Student Credentials
                            </h5>
                            <a href="<?php echo URLROOT; ?>/certificate/hub" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-external-link-alt me-1"></i> Central Certificate Hub
                            </a>
                        </div>
                        <p class="text-muted small mb-4">Generate and print government-compliant, seal-ready educational certificates and examination credentials directly for <strong><?php echo htmlspecialchars($student->name); ?></strong>.</p>

                        <div class="row g-3">
                            <!-- SLC Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border shadow-none bg-light p-3 rounded-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="p-2 bg-danger-subtle text-danger rounded-2">
                                            <i class="fa fa-file-contract fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">School Leaving Certificate (SLC)</h6>
                                            <span class="badge bg-danger-subtle text-danger px-2 py-0 fs-xs">Transfer Certificate</span>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-3 flex-grow-1">
                                        Formal legal credential required for BISE Board registration, inter-district transfer, and migration. Includes dues clearance, conduct, and promotion class.
                                    </p>
                                    <a href="<?php echo URLROOT; ?>/certificate/slc/<?php echo $student->id; ?>" target="_blank" class="btn btn-outline-danger btn-sm w-100 fw-bold">
                                        <i class="fa fa-print me-1"></i> Generate SLC / TC
                                    </a>
                                </div>
                            </div>

                            <!-- Character Certificate Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border shadow-none bg-light p-3 rounded-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="p-2 bg-primary-subtle text-primary rounded-2">
                                            <i class="fa fa-award fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Character Certificate</h6>
                                            <span class="badge bg-primary-subtle text-primary px-2 py-0 fs-xs">Conduct Testimonial</span>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-3 flex-grow-1">
                                        Institutional testimonial certifying moral conduct, civic character, co-curricular participation, and discipline during the student's study period.
                                    </p>
                                    <a href="<?php echo URLROOT; ?>/certificate/character/<?php echo $student->id; ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100 fw-bold">
                                        <i class="fa fa-print me-1"></i> Issue Character Cert.
                                    </a>
                                </div>
                            </div>

                            <!-- Bonafide Certificate Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border shadow-none bg-light p-3 rounded-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="p-2 bg-success-subtle text-success rounded-2">
                                            <i class="fa fa-stamp fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Bonafide / Enrollment Cert.</h6>
                                            <span class="badge bg-success-subtle text-success px-2 py-0 fs-xs">NADRA & Passport Verification</span>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-3 flex-grow-1">
                                        Official student enrollment verification required by NADRA for Smart Card / B-Form, Passport Office, Embassy visa processing, or Scholarships.
                                    </p>
                                    <a href="<?php echo URLROOT; ?>/certificate/bonafide/<?php echo $student->id; ?>" target="_blank" class="btn btn-outline-success btn-sm w-100 fw-bold">
                                        <i class="fa fa-print me-1"></i> Issue Bonafide Cert.
                                    </a>
                                </div>
                            </div>

                            <!-- Roll No Slip / Admit Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border shadow-none bg-light p-3 rounded-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="p-2 bg-warning-subtle text-warning rounded-2">
                                            <i class="fa fa-id-badge fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Examination Roll No Slip</h6>
                                            <span class="badge bg-warning-subtle text-dark px-2 py-0 fs-xs">Admit Card</span>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-3 flex-grow-1">
                                        Official examination entry pass featuring candidate photograph, roll number, scheduled paper dates, hall timings, and exam instructions.
                                    </p>
                                    <a href="<?php echo URLROOT; ?>/certificate/admitCard/<?php echo $student->id; ?>" target="_blank" class="btn btn-outline-warning btn-sm w-100 fw-bold text-dark">
                                        <i class="fa fa-print me-1"></i> Print Roll No Slip
                                    </a>
                                </div>
                            </div>

                            <!-- Institutional Exit Clearance (NOC) Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border shadow-none bg-light p-3 rounded-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="p-2 bg-info-subtle text-info rounded-2">
                                                <i class="fa fa-clipboard-check fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark">Institutional Exit Clearance (NOC)</h6>
                                                <span class="badge bg-info-subtle text-info px-2 py-0 fs-xs">5-Department Audit</span>
                                            </div>
                                        </div>
                                        <?php if(!empty($clearance)): ?>
                                            <span class="badge <?php echo $clearance->overall_status == 'Fully Cleared' ? 'bg-success' : 'bg-warning text-dark'; ?> rounded-pill px-2 py-1">
                                                <?php echo htmlspecialchars($clearance->overall_status); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-muted rounded-pill px-2 py-1">Not Initiated</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="small text-muted mb-3 flex-grow-1">
                                        Mandatory 5-tier departmental clearance verifying fee dues, library book liability, science & computer lab breakages, sports kits, and ID card surrender.
                                    </p>
                                    <div class="d-flex gap-2">
                                        <?php if(!empty($clearance)): ?>
                                            <a href="<?php echo URLROOT; ?>/clearance/detail/<?php echo $clearance->id; ?>" class="btn btn-outline-info btn-sm flex-fill fw-bold">
                                                <i class="fa fa-tasks me-1"></i> Sign-Offs (<?php echo htmlspecialchars($clearance->clearance_no); ?>)
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/clearance/certificate/<?php echo $clearance->id; ?>" target="_blank" class="btn btn-info btn-sm text-white px-3 fw-bold" title="Print Official Clearance NOC Certificate">
                                                <i class="fa fa-print"></i> NOC
                                            </a>
                                        <?php else: ?>
                                            <a href="<?php echo URLROOT; ?>/clearance/index" class="btn btn-outline-info btn-sm w-100 fw-bold">
                                                <i class="fa fa-clipboard-list me-1"></i> Initiate Clearance Workflow
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Graduation & Exit Package Bundle Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border shadow-none bg-light p-3 rounded-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="p-2 text-white rounded-2" style="background: linear-gradient(135deg, #8b5cf6, #ec4899);">
                                            <i class="fa fa-archive fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">Graduation & Exit Package</h6>
                                            <span class="badge bg-purple-subtle text-purple px-2 py-0 fs-xs" style="color: #8b5cf6; background: #f3e8ff;">Consolidated Dossier</span>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-3 flex-grow-1">
                                        Single-click institutional exit bundle assembling School Leaving Certificate (SLC), Character Certificate, Bonafide Verification, Clearance NOC, DMC, and Student Dossier.
                                    </p>
                                    <?php if(!empty($clearance)): ?>
                                        <a href="<?php echo URLROOT; ?>/clearance/package/<?php echo $clearance->id; ?>" class="btn btn-outline-purple btn-sm w-100 fw-bold" style="color: #8b5cf6; border-color: #8b5cf6;">
                                            <i class="fa fa-folder-open me-1"></i> Open Graduation & Exit Bundle
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo URLROOT; ?>/clearance/index" class="btn btn-outline-secondary btn-sm w-100 fw-bold">
                                            <i class="fa fa-external-link-alt me-1"></i> Clearance Required for Exit Bundle
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info d-flex align-items-center gap-2 mt-4 mb-0 py-2 small">
                            <i class="fa fa-info-circle fs-5"></i>
                            <div>
                                <strong>Institutional Registry:</strong> All printed credentials are automatically registered in the system's certificate log with verified serial numbers and timestamps.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
</div>
</div>

</div> <!-- end .profile-page-wrapper -->

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
