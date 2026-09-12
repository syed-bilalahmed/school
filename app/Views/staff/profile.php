<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$staff = $data['staff'];
$profile = $data['profile'];
$allocations = $profile['allocations'] ?? [];
$totalPeriods = $profile['total_periods_per_week'] ?? 0;
$monthlyLectures = $profile['monthly_visiting_lectures'] ?? 0;
$attendance = $profile['attendance'] ?? [];
$attSummary = $attendance['summary'] ?? null;
$attLogs = $attendance['logs'] ?? [];
$attPercent = $attendance['percentage'] ?? 100;
$payroll = $profile['payroll'] ?? [];
$structure = $payroll['structure'] ?? null;
$payslips = $payroll['payslips'] ?? [];

$isVisiting = strpos($staff->employment_type ?? '', 'Visiting') !== false;

// Status styling
$statusClass = 'bg-success';
if ($staff->status == 'On Leave') $statusClass = 'bg-warning text-dark';
elseif ($staff->status == 'Resigned' || $staff->status == 'Terminated') $statusClass = 'bg-danger';
?>

<!-- Print-Only Styling -->
<style>
@media print {
    .top-navbar, .sidebar, .sidebar-overlay, .no-print, .btn, .nav-pills {
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
</style>

<!-- Top Navigation & Action Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 no-print">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/staff/index" class="text-decoration-none text-muted">Staff Directory</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page"><?php echo htmlspecialchars($staff->name); ?></li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h3 class="mb-0 fw-bold"><?php echo htmlspecialchars($staff->name); ?></h3>
            <span class="badge <?php echo $statusClass; ?> rounded-pill px-3 py-1"><?php echo htmlspecialchars($staff->status ?? 'Active'); ?></span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-3 py-1"><?php echo htmlspecialchars($staff->staff_code); ?></span>
        </div>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/staff/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Directory
        </a>
        <a href="<?php echo URLROOT; ?>/staff/edit/<?php echo $staff->id; ?>" class="btn btn-primary btn-sm px-3">
            <i class="fa fa-edit me-1"></i> Edit Profile
        </a>
        <a href="<?php echo URLROOT; ?>/payroll/structure/<?php echo $staff->id; ?>" class="btn btn-success btn-sm px-3">
            <i class="fa fa-coins me-1"></i> Salary Structure
        </a>
        <?php if($staff->role == 'teacher'): ?>
            <a href="<?php echo URLROOT; ?>/subjects/assign" class="btn btn-outline-primary btn-sm px-3">
                <i class="fa fa-user-check me-1"></i> Course Allocation
            </a>
        <?php endif; ?>
        <button type="button" class="btn btn-dark btn-sm px-3" onclick="window.print()">
            <i class="fa fa-print me-1"></i> Print Dossier
        </button>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 no-print" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div>
            <?php 
                if($_GET['success'] == 'created') echo "Staff profile successfully registered and credentials activated!";
                elseif($_GET['success'] == 'updated') echo "Staff profile records have been updated!";
                elseif($_GET['success'] == 'salary_updated') echo "Salary compensation structure updated successfully!";
                else echo "Action completed successfully!";
            ?>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- LEFT COLUMN: Identity & Master Information -->
    <div class="col-xl-4 col-lg-5">
        <!-- Hero Profile Card -->
        <div class="card shadow-sm border-0 mb-4 text-center overflow-hidden">
            <div style="height: 90px; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%);"></div>
            <div class="card-body pt-0 position-relative" style="margin-top: -50px;">
                <div class="position-relative d-inline-block mb-3">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($staff->name); ?>&size=120&background=random&bold=true" 
                         class="rounded-circle border border-4 border-white shadow-md" width="110" height="110" alt="<?php echo htmlspecialchars($staff->name); ?>">
                    <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-white rounded-circle" title="Active Account"></span>
                </div>
                
                <h4 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($staff->name); ?></h4>
                <p class="text-muted small mb-2"><?php echo htmlspecialchars($staff->email); ?></p>

                <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
                    <span class="badge bg-primary px-3 py-2" style="font-size: 0.82rem;">
                        <i class="fa fa-briefcase me-1"></i> <?php echo htmlspecialchars($staff->designation ?: 'Staff'); ?>
                    </span>
                    <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 0.82rem;">
                        <i class="fa fa-building me-1 text-primary"></i> <?php echo htmlspecialchars($staff->department ?: 'Academics'); ?>
                    </span>
                </div>

                <!-- Mini Key Metrics Row -->
                <div class="row g-2 text-center pt-3 border-top">
                    <div class="col-4">
                        <div class="kpi-metric-box">
                            <div class="text-muted smaller text-uppercase fw-bold" style="font-size: 0.68rem;">Workload</div>
                            <div class="h6 mb-0 fw-bold text-dark"><?php echo $totalPeriods; ?> <small class="text-muted smaller">p/w</small></div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="kpi-metric-box">
                            <div class="text-muted smaller text-uppercase fw-bold" style="font-size: 0.68rem;">Attendance</div>
                            <div class="h6 mb-0 fw-bold <?php echo $attPercent >= 85 ? 'text-success' : 'text-warning'; ?>">
                                <?php echo $attPercent; ?>%
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="kpi-metric-box">
                            <div class="text-muted smaller text-uppercase fw-bold" style="font-size: 0.68rem;">Tenure</div>
                            <div class="h6 mb-0 fw-bold text-primary" style="font-size: 0.78rem;">
                                <?php echo $isVisiting ? 'Visiting' : 'Permanent'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pakistani Civil Identity & Contact -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <span class="fw-bold text-dark"><i class="fa fa-id-card text-primary me-2"></i>Civil &amp; Contact Records</span>
                <span class="badge badge-soft-info font-monospace"><?php echo htmlspecialchars($staff->staff_code); ?></span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">NADRA CNIC</span>
                        <span class="fw-bold text-primary font-monospace"><?php echo htmlspecialchars($staff->cnic ?: 'Not Recorded'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Primary Phone</span>
                        <?php if(!empty($staff->phone)): ?>
                            <a href="tel:<?php echo htmlspecialchars($staff->phone); ?>" class="fw-bold text-success text-decoration-none">
                                <i class="fa fa-phone me-1"></i><?php echo htmlspecialchars($staff->phone); ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">N/A</span>
                        <?php endif; ?>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Emergency Contact</span>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($staff->emergency_contact ?: 'None Listed'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Date of Birth</span>
                        <span class="fw-bold text-dark"><?php echo !empty($staff->dob) ? date('d M, Y', strtotime($staff->dob)) : 'N/A'; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Gender</span>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($staff->gender ?? 'Male'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Date of Joining</span>
                        <span class="fw-bold text-dark"><?php echo !empty($staff->date_of_joining) ? date('d M, Y', strtotime($staff->date_of_joining)) : 'N/A'; ?></span>
                    </li>
                    <li class="list-group-item py-2 px-3">
                        <div class="text-muted mb-1">Residential Address:</div>
                        <div class="text-dark small"><?php echo nl2br(htmlspecialchars($staff->address ?: 'No address specified.')); ?></div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bank & Payroll Details -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
                <span class="fw-bold text-dark"><i class="fa fa-building-columns text-success me-2"></i>Bank &amp; Salary Account</span>
                <span class="badge bg-success-subtle text-success">Verified</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Bank Name</span>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($staff->bank_name ?: 'Bank Account Pending'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Account / IBAN</span>
                        <span class="fw-bold text-dark font-monospace"><?php echo htmlspecialchars($staff->bank_account_no ?: 'N/A'); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="text-muted">Compensation Type</span>
                        <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($staff->employment_type ?? 'Permanent'); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Tabbed 360° Panels -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <!-- Navigation Tabs -->
            <div class="card-header bg-white p-3 border-0 border-bottom">
                <ul class="nav nav-pills nav-pills-custom gap-2 flex-wrap" id="staff360Tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center gap-2" id="profile-tab" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab">
                            <i class="fa fa-id-badge"></i> <span>Professional Dossier</span>
                        </button>
                    </li>
                    <?php if($staff->role == 'teacher' || $isVisiting): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center gap-2" id="workload-tab" data-bs-toggle="pill" data-bs-target="#tab-workload" type="button" role="tab">
                                <i class="fa fa-book-open"></i> <span>Teaching Workload</span>
                                <span class="badge bg-white text-dark rounded-pill ms-1"><?php echo $totalPeriods; ?></span>
                            </button>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="attendance-tab" data-bs-toggle="pill" data-bs-target="#tab-attendance" type="button" role="tab">
                            <i class="fa fa-calendar-check"></i> <span>Attendance</span>
                            <span class="badge bg-white text-dark rounded-pill ms-1"><?php echo $attPercent; ?>%</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="payroll-tab" data-bs-toggle="pill" data-bs-target="#tab-payroll" type="button" role="tab">
                            <i class="fa fa-money-check-dollar"></i> <span>Payroll &amp; Payslips</span>
                            <span class="badge bg-white text-dark rounded-pill ms-1"><?php echo count($payslips); ?></span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="staff360TabContent">

                    <!-- TAB 1: PROFESSIONAL DOSSIER -->
                    <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
                        <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                            <i class="fa fa-graduation-cap text-primary"></i> Academic Qualifications &amp; Institutional Placement
                        </h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Credentials &amp; Education</h6>
                                    <table class="table table-borderless table-sm mb-0 small">
                                        <tr>
                                            <td class="text-muted" style="width: 45%;">Highest Degree:</td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($staff->qualification ?: 'Master Degree / Not specified'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Teaching Experience:</td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($staff->experience_years ?: 'Not specified'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Security Role:</td>
                                            <td><span class="badge bg-primary text-capitalize"><?php echo htmlspecialchars($staff->role); ?></span></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Department:</td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($staff->department ?: 'Academics'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-light h-100">
                                    <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Contract &amp; Tenure</h6>
                                    <table class="table table-borderless table-sm mb-0 small">
                                        <tr>
                                            <td class="text-muted" style="width: 45%;">Employment Type:</td>
                                            <td class="fw-bold">
                                                <?php if($isVisiting): ?>
                                                    <span class="badge bg-purple-subtle text-purple" style="color: #7c3aed;">Visiting / Per Lecture</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success-subtle text-success">Permanent / Regular</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Designation:</td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($staff->designation ?: 'Staff Member'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Base Pay / Rate:</td>
                                            <td class="fw-bold text-success">
                                                <?php if($isVisiting): ?>
                                                    Rs. <?php echo number_format((float)$staff->lecture_rate); ?> / Lecture
                                                <?php else: ?>
                                                    Rs. <?php echo number_format((float)$staff->basic_salary); ?> / Month
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Joining Date:</td>
                                            <td class="fw-bold"><?php echo !empty($staff->date_of_joining) ? date('d M, Y', strtotime($staff->date_of_joining)) : 'N/A'; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- System Access Card -->
                        <div class="p-3 border rounded-3 bg-white mb-4">
                            <h6 class="fw-bold text-dark mb-2">ERP User Account &amp; System Access</h6>
                            <p class="text-muted small mb-3">
                                This staff member has an active user account connected to role <strong><?php echo htmlspecialchars($staff->role); ?></strong>.
                            </p>
                            <div class="row g-2 text-center small">
                                <div class="col-md-4">
                                    <div class="p-2 border rounded bg-light">
                                        <div class="text-muted">Username / Email</div>
                                        <div class="fw-bold text-dark font-monospace"><?php echo htmlspecialchars($staff->email); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 border rounded bg-light">
                                        <div class="text-muted">Permission Level</div>
                                        <div class="fw-bold text-primary text-capitalize"><?php echo str_replace('_', ' ', $staff->role); ?> Level</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 border rounded bg-light">
                                        <div class="text-muted">Account Status</div>
                                        <div class="fw-bold text-success">Enabled</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: TEACHING WORKLOAD & ALLOCATIONS -->
                    <?php if($staff->role == 'teacher' || $isVisiting): ?>
                    <div class="tab-pane fade" id="tab-workload" role="tabpanel">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">
                                    <i class="fa fa-book-open text-primary me-2"></i>Curriculum Allocations &amp; Weekly Periods
                                </h5>
                                <p class="text-muted small mb-0">
                                    Classes, sections, and subjects assigned to this faculty member with weekly period counts.
                                </p>
                            </div>
                            <a href="<?php echo URLROOT; ?>/subjects/assign" class="btn btn-outline-primary btn-sm px-3">
                                <i class="fa fa-plus-circle me-1"></i> Manage Allocations
                            </a>
                        </div>

                        <!-- Workload Summary Strip -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4 col-12">
                                <div class="p-3 border rounded-3 bg-light text-center">
                                    <div class="text-muted small fw-bold">ASSIGNED COURSES</div>
                                    <div class="h4 mb-0 fw-bold text-dark"><?php echo count($allocations); ?></div>
                                </div>
                            </div>
                            <div class="col-sm-4 col-6">
                                <div class="p-3 border rounded-3 bg-primary-subtle text-center">
                                    <div class="text-primary small fw-bold">WEEKLY PERIODS</div>
                                    <div class="h4 mb-0 fw-bold text-primary"><?php echo $totalPeriods; ?></div>
                                </div>
                            </div>
                            <div class="col-sm-4 col-6">
                                <div class="p-3 border rounded-3 bg-purple-subtle text-center" style="color: #7c3aed; background: rgba(124, 58, 237, 0.1);">
                                    <div class="small fw-bold">MONTHLY VISITING LOAD</div>
                                    <div class="h4 mb-0 fw-bold"><?php echo $monthlyLectures; ?> Lectures</div>
                                </div>
                            </div>
                        </div>

                        <?php if(empty($allocations)): ?>
                            <div class="text-center py-5 border rounded-3 bg-light">
                                <i class="fa fa-chalkboard-user fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                <h6 class="fw-bold text-dark">No Subjects Allocated Yet</h6>
                                <p class="text-muted small mb-3">Assign class subjects to this faculty member to calculate timetable workload.</p>
                                <a href="<?php echo URLROOT; ?>/subjects/assign" class="btn btn-primary btn-sm px-4">
                                    <i class="fa fa-plus me-1"></i> Assign Course
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border small">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Class &amp; Section</th>
                                            <th>Subject</th>
                                            <th>Course Code</th>
                                            <th>Type</th>
                                            <th>Periods / Week</th>
                                            <th>Monthly Visiting Pay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($allocations as $al): 
                                            $periods = (int)($al->periods_per_week ?: 5);
                                            $monthlyRate = $isVisiting ? ($periods * 4 * (float)$staff->lecture_rate) : 0;
                                        ?>
                                            <tr>
                                                <td class="fw-bold text-dark">
                                                    <span class="badge bg-light text-dark border">
                                                        <?php echo htmlspecialchars($al->class_name); ?> - Sec <?php echo htmlspecialchars($al->section_name); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($al->subject_name); ?></div>
                                                </td>
                                                <td class="font-monospace text-muted"><?php echo htmlspecialchars($al->subject_code ?: 'SUB'); ?></td>
                                                <td>
                                                    <?php if($al->is_core): ?>
                                                        <span class="badge bg-primary-subtle text-primary">Core</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-secondary border">Elective</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="fw-bold text-center">
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 fs-6">
                                                        <?php echo $periods; ?> Periods
                                                    </span>
                                                </td>
                                                <td class="fw-bold text-success">
                                                    <?php if($isVisiting): ?>
                                                        Rs. <?php echo number_format($monthlyRate); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Included in Base</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- TAB 3: ATTENDANCE ANALYTICS -->
                    <div class="tab-pane fade" id="tab-attendance" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-calendar-alt text-primary me-2"></i>Staff Attendance Performance
                            </h5>
                            <a href="<?php echo URLROOT; ?>/attendance/staff" class="btn btn-outline-primary btn-sm px-3">
                                <i class="fa fa-clipboard-check me-1"></i> Staff Attendance Register
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
                                    <div class="text-warning small fw-bold">LATE ARRIVAL</div>
                                    <div class="h4 mb-0 fw-bold text-warning"><?php echo $attSummary->late_days ?? 0; ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Logs Table -->
                        <h6 class="fw-bold text-dark mb-3">Recent 15 Attendance Records</h6>
                        <?php if(empty($attLogs)): ?>
                            <div class="text-center py-4 border rounded-3 bg-light text-muted small">
                                <i class="fa fa-calendar-times me-1"></i> No daily staff attendance logs recorded yet.
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
                                        ?>
                                            <tr>
                                                <td class="fw-bold text-dark font-monospace"><?php echo date('d M, Y', strtotime($log->date)); ?></td>
                                                <td class="text-muted small"><?php echo date('l', strtotime($log->date)); ?></td>
                                                <td><span class="badge <?php echo $badgeClass; ?> px-2 py-1"><?php echo htmlspecialchars($log->attendance_type); ?></span></td>
                                                <td class="text-muted small"><?php echo htmlspecialchars($log->remark ?: '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAB 4: PAYROLL & SALARY HISTORY -->
                    <div class="tab-pane fade" id="tab-payroll" role="tabpanel">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">
                                    <i class="fa fa-money-check-dollar text-success me-2"></i>Compensation &amp; Payslip Records
                                </h5>
                                <p class="text-muted small mb-0">
                                    Configured salary allowances, deductions, and monthly salary disbursement vouchers.
                                </p>
                            </div>
                            <a href="<?php echo URLROOT; ?>/payroll/structure/<?php echo $staff->id; ?>" class="btn btn-success btn-sm px-3">
                                <i class="fa fa-cog me-1"></i> Adjust Salary Structure
                            </a>
                        </div>

                        <!-- Active Salary Structure Summary -->
                        <div class="card border-0 bg-light p-3 mb-4">
                            <h6 class="fw-bold text-dark mb-3">Configured Salary Structure Breakdown</h6>
                            <div class="row g-3 small">
                                <div class="col-md-3 col-6">
                                    <div class="text-muted">Basic / Base Rate:</div>
                                    <div class="h6 mb-0 fw-bold text-dark">
                                        Rs. <?php echo number_format((float)($structure->basic_salary ?? $staff->basic_salary)); ?>
                                        <?php if($isVisiting): ?><small class="text-muted">/ Lec</small><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="text-muted">Allowances Total:</div>
                                    <div class="h6 mb-0 fw-bold text-success">+Rs. <?php echo number_format((float)($structure->earnings ?? 0)); ?></div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="text-muted">Deductions Total:</div>
                                    <div class="h6 mb-0 fw-bold text-danger">-Rs. <?php echo number_format((float)($structure->deductions ?? 0)); ?></div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="text-muted">Estimated Net Pay:</div>
                                    <div class="h6 mb-0 fw-bold text-primary">Rs. <?php echo number_format((float)($structure->net_salary ?? $staff->basic_salary)); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Payslip Vouchers History Table -->
                        <h6 class="fw-bold text-dark mb-3">Disbursed Salary Slips History</h6>
                        <?php if(empty($payslips)): ?>
                            <div class="text-center py-4 border rounded-3 bg-light text-muted small">
                                <i class="fa fa-receipt me-1"></i> No salary slips generated for this staff member yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border small">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Voucher #</th>
                                            <th>Month / Year</th>
                                            <th>Delivered Lectures</th>
                                            <th>Net Salary</th>
                                            <th>Status</th>
                                            <th class="text-end pe-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($payslips as $ps): ?>
                                            <tr>
                                                <td class="fw-bold font-monospace text-primary">PAY-<?php echo str_pad($ps->id, 5, '0', STR_PAD_LEFT); ?></td>
                                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($ps->month . ' ' . $ps->year); ?></td>
                                                <td>
                                                    <?php if($ps->lectures_delivered > 0): ?>
                                                        <span class="badge bg-purple-subtle text-purple"><?php echo $ps->lectures_delivered; ?> Lectures</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Regular</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="fw-bold text-success fs-6">Rs. <?php echo number_format((float)$ps->net_salary); ?></td>
                                                <td>
                                                    <?php if($ps->status == 'Paid'): ?>
                                                        <span class="badge bg-success">Paid</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">Generated / Due</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end pe-3">
                                                    <a href="<?php echo URLROOT; ?>/payroll/slip/<?php echo $ps->id; ?>" class="btn btn-outline-dark btn-sm px-3" target="_blank">
                                                        <i class="fa fa-print me-1"></i> Print Slip
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
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
