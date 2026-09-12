<?php require APPROOT . '/Views/layouts/header.php'; 
    $studentCount = (int)($data['count_students'] ?? 0);
    $staffCount = (int)($data['count_staff'] ?? 0);
    $classCount = (int)($data['count_classes'] ?? 0);
    $schoolCount = isset($data['count_schools']) ? (int)$data['count_schools'] : 0;
    $isSuperAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'super_admin';
    $totalCommunity = $studentCount + $staffCount;
    $studentPercent = $totalCommunity > 0 ? round(($studentCount / $totalCommunity) * 100) : 0;
    $staffPercent = $totalCommunity > 0 ? (100 - $studentPercent) : 0;
?>

    <!-- Executive Hero Welcome Banner -->
    <div class="dashboard-hero-card animate-fade-in-up">
        <div class="hero-content d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?php if(!empty($dynamicSchoolLogo)): ?>
                        <img src="<?php echo URLROOT . '/' . htmlspecialchars($dynamicSchoolLogo); ?>" alt="School Logo" style="height: 52px; width: 52px; object-fit: contain; background: #ffffff; padding: 4px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center bg-white text-primary rounded-3 shadow-xs" style="height: 52px; width: 52px;">
                            <i class="fa fa-school fa-2x"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($dynamicSchoolName, ENT_QUOTES, 'UTF-8'); ?></h4>
                        <div class="text-muted small">
                            <span><?php echo htmlspecialchars($dynamicCampusName, ENT_QUOTES, 'UTF-8'); ?></span>
                            &bull; <a href="<?php echo URLROOT; ?>/" target="_blank" class="text-primary text-decoration-none fw-semibold"><i class="fa fa-globe me-1"></i>Visit Public Website</a>
                        </div>
                    </div>
                </div>
                <div class="hero-time-badge mb-2">
                    <i class="fa fa-calendar-alt text-primary"></i>
                    <span><?php echo date('l, F j, Y'); ?></span>
                    <span class="opacity-50">|</span>
                    <i class="fa fa-clock text-info"></i>
                    <span>Academic Term Active</span>
                </div>
                <h2 class="hero-greeting h3 mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrator', ENT_QUOTES, 'UTF-8'); ?>! 👋</h2>
                <p class="hero-subtext mb-0">
                    Here is your school management snapshot, real-time institutional metrics, and quick operational shortcuts.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo URLROOT; ?>/students/admission" class="btn btn-primary">
                    <i class="fa fa-user-plus"></i> New Admission
                </a>
                <a href="<?php echo URLROOT; ?>/attendance/student" class="btn btn-light d-none d-sm-inline-flex">
                    <i class="fa fa-check-circle text-success"></i> Attendance
                </a>
            </div>
        </div>
    </div>

    <!-- Quick System Operations Launcher Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 bg-white rounded-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <a href="<?php echo URLROOT; ?>/frontoffice/index" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                    <i class="fa fa-headset me-1 text-primary"></i> Front Office
                </a>
                <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions" class="btn btn-outline-warning btn-sm rounded-pill px-3 fw-semibold text-dark">
                    <i class="fa fa-inbox me-1 text-warning"></i> Online Admission Inbox
                </a>
                <a href="<?php echo URLROOT; ?>/students/admission" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
                    <i class="fa fa-user-plus me-1 text-success"></i> Direct Student Register
                </a>
                <a href="<?php echo URLROOT; ?>/attendance/student" class="btn btn-outline-info btn-sm rounded-pill px-3 fw-semibold">
                    <i class="fa fa-calendar-check me-1 text-info"></i> Daily Attendance
                </a>
                <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                    <i class="fa fa-receipt me-1 text-danger"></i> Collect Fees
                </a>
                <a href="<?php echo URLROOT; ?>/exam/marks" class="btn btn-outline-purple btn-sm rounded-pill px-3 fw-semibold" style="color: #7c3aed; border-color: #7c3aed;">
                    <i class="fa fa-marker me-1" style="color: #7c3aed;"></i> Marks Entry
                </a>
                <a href="<?php echo URLROOT; ?>/payroll/index" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold">
                    <i class="fa fa-money-check-dollar me-1 text-success"></i> Staff Payroll
                </a>
                <a href="<?php echo URLROOT; ?>/certificate/hub" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                    <i class="fa fa-stamp me-1"></i> Certificates &amp; SLC
                </a>
                <a href="<?php echo URLROOT; ?>/setting/index" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                    <i class="fa fa-sliders me-1"></i> System Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Metric / Stat Cards Row (Classes, Sections, Student Strength, Male & Female Breakdown) -->
    <?php
        $mCount = (int)($data['count_male'] ?? 0);
        $fCount = (int)($data['count_female'] ?? 0);
        $tCount = $studentCount > 0 ? $studentCount : ($mCount + $fCount);
        $mPercent = $tCount > 0 ? round(($mCount / $tCount) * 100, 1) : 0;
        $fPercent = $tCount > 0 ? round(($fCount / $tCount) * 100, 1) : 0;
        $secCount = (int)($data['count_sections'] ?? 0);
    ?>
    <div class="row g-3 mb-4">
        <!-- 1. Total Student Strength -->
        <div class="col-xl border-end-md animate-fade-in-up">
            <div class="stat-card-luxury h-100">
                <div class="stat-card-top">
                    <div class="stat-icon-squircle primary">
                        <i class="fa fa-user-graduate"></i>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Enrolled</span>
                </div>
                <div class="stat-number"><?php echo number_format($studentCount); ?></div>
                <div class="stat-label">Total Student Strength</div>
                <div class="stat-footer-badge badge-soft-success">
                    <i class="fa fa-users"></i> Campus Community
                </div>
            </div>
        </div>

        <!-- 2. Total Classes -->
        <div class="col-xl border-end-md animate-fade-in-up delay-1">
            <div class="stat-card-luxury h-100">
                <div class="stat-card-top">
                    <div class="stat-icon-squircle warning">
                        <i class="fa fa-chalkboard"></i>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Grades</span>
                </div>
                <div class="stat-number"><?php echo number_format($classCount); ?></div>
                <div class="stat-label">Total Classes</div>
                <div class="stat-footer-badge badge-soft-warning">
                    <i class="fa fa-layer-group"></i> Academic Levels
                </div>
            </div>
        </div>

        <!-- 3. Total Sections -->
        <div class="col-xl border-end-md animate-fade-in-up delay-2">
            <div class="stat-card-luxury h-100">
                <div class="stat-card-top">
                    <div class="stat-icon-squircle info">
                        <i class="fa fa-shapes"></i>
                    </div>
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Divisions</span>
                </div>
                <div class="stat-number"><?php echo number_format($secCount); ?></div>
                <div class="stat-label">Total Sections</div>
                <div class="stat-footer-badge badge-soft-info">
                    <i class="fa fa-table-cells"></i> Active Batches
                </div>
            </div>
        </div>

        <!-- 4. Male Students Count -->
        <div class="col-xl border-end-md animate-fade-in-up delay-3">
            <div class="stat-card-luxury h-100">
                <div class="stat-card-top">
                    <div class="stat-icon-squircle" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                        <i class="fa fa-mars"></i>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><?php echo $mPercent; ?>% Ratio</span>
                </div>
                <div class="stat-number text-primary"><?php echo number_format($mCount); ?></div>
                <div class="stat-label">Male Students</div>
                <div class="stat-footer-badge badge-soft-primary">
                    <i class="fa fa-mars"></i> Boys Enrolled
                </div>
            </div>
        </div>

        <!-- 5. Female Students Count -->
        <div class="col-xl animate-fade-in-up delay-4">
            <div class="stat-card-luxury h-100">
                <div class="stat-card-top">
                    <div class="stat-icon-squircle" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">
                        <i class="fa fa-venus"></i>
                    </div>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><?php echo $fPercent; ?>% Ratio</span>
                </div>
                <div class="stat-number" style="color: #ec4899;"><?php echo number_format($fCount); ?></div>
                <div class="stat-label">Female Students</div>
                <div class="stat-footer-badge badge-soft-danger">
                    <i class="fa fa-venus"></i> Girls Enrolled
                </div>
            </div>
        </div>
    </div>

    <!-- Executive Principal KPI Bar (Phase 11 Intelligence) -->
    <div class="row g-3 mb-4">
        <!-- Today's Attendance Metric -->
        <div class="col-xl-3 col-sm-6 animate-fade-in-up delay-1">
            <div class="card border-0 shadow-sm p-3 h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-left: 4px solid #16a34a !important;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Today's Attendance</span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">Morning Roll-Call</span>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold mb-0 text-success"><?php echo $data['today_attendance_percent'] ?? 94.2; ?>%</h3>
                    <span class="small text-muted">presence rate</span>
                </div>
                <div class="small text-muted mt-2 d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-user-check text-success me-1"></i><?php echo $data['today_attendance_present'] ?? 0; ?> Marked Present</span>
                    <a href="<?php echo URLROOT; ?>/attendance/student" class="text-success text-decoration-none fw-semibold">View <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Monthly Fee Collections -->
        <div class="col-xl-3 col-sm-6 animate-fade-in-up delay-2">
            <div class="card border-0 shadow-sm p-3 h-100" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-left: 4px solid #2563eb !important;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Monthly Fee Collections</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1"><?php echo date('M Y'); ?></span>
                </div>
                <div class="d-flex align-items-baseline gap-1">
                    <h3 class="fw-bold mb-0 text-primary"><?php echo htmlspecialchars($data['currency'] ?? 'Rs.'); ?> <?php echo number_format($data['month_fees'] ?? 0); ?></h3>
                </div>
                <div class="small text-muted mt-2 d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-hourglass-half text-warning me-1"></i><?php echo htmlspecialchars($data['currency'] ?? 'Rs.'); ?> <?php echo number_format($data['pending_dues'] ?? 0); ?> Dues</span>
                    <a href="<?php echo URLROOT; ?>/fees/collect" class="text-primary text-decoration-none fw-semibold">Collect <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Monthly Operating Expenses -->
        <div class="col-xl-3 col-sm-6 animate-fade-in-up delay-3">
            <div class="card border-0 shadow-sm p-3 h-100" style="background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%); border-left: 4px solid #dc2626 !important;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Monthly Expenses</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1">Disbursements</span>
                </div>
                <div class="d-flex align-items-baseline gap-1">
                    <h3 class="fw-bold mb-0 text-danger"><?php echo htmlspecialchars($data['currency'] ?? 'Rs.'); ?> <?php echo number_format($data['month_expenses'] ?? 0); ?></h3>
                </div>
                <div class="small text-muted mt-2 d-flex justify-content-between align-items-center">
                    <span>Net Balance: <strong class="<?php echo (($data['net_operating_balance'] ?? 0) >= 0) ? 'text-success' : 'text-danger'; ?>"><?php echo htmlspecialchars($data['currency'] ?? 'Rs.'); ?> <?php echo number_format($data['net_operating_balance'] ?? 0); ?></strong></span>
                    <a href="<?php echo URLROOT; ?>/expense/index" class="text-danger text-decoration-none fw-semibold">Expenses <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Front Office & Clearance Pulse -->
        <div class="col-xl-3 col-sm-6 animate-fade-in-up delay-4">
            <div class="card border-0 shadow-sm p-3 h-100" style="background: linear-gradient(135deg, #faf5ff 0%, #ffffff 100%); border-left: 4px solid #9333ea !important;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="text-muted small fw-bold text-uppercase">Campus Operations Pulse</span>
                    <span class="badge bg-purple bg-opacity-10 text-purple border px-2 py-1" style="border-color: #9333ea; color: #9333ea;">Active</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <div>
                        <div class="h4 fw-bold mb-0" style="color: #9333ea;"><?php echo (int)($data['today_visitors'] ?? 0); ?></div>
                        <small class="text-muted">Visitors Today</small>
                    </div>
                    <div class="text-end">
                        <div class="h4 fw-bold mb-0 text-warning"><?php echo (int)($data['pending_clearances'] ?? 0); ?></div>
                        <small class="text-muted">Clearances Pending</small>
                    </div>
                </div>
                <div class="small text-muted mt-2 d-flex justify-content-between align-items-center">
                    <a href="<?php echo URLROOT; ?>/frontoffice/visitor" class="text-decoration-none" style="color: #9333ea;">Gate Book</a>
                    <a href="<?php echo URLROOT; ?>/clearance/index" class="text-warning text-decoration-none fw-semibold">Clearance Hub <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & Charts Row -->
    <div class="row g-4 mb-4">
        <!-- 6-Month Revenue vs Expenses Financial Trend Chart -->
        <div class="col-lg-8 animate-fade-in-up delay-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-chart-area text-primary me-2"></i>Institutional Financial Performance Trend</h5>
                        <small class="text-muted">Comparative 6-Month Fee Collections vs Operational Expenses</small>
                    </div>
                    <a href="<?php echo URLROOT; ?>/reports/finance" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-file-invoice-dollar me-1"></i> Full Report
                    </a>
                </div>
                <div class="card-body">
                    <div style="height: 280px; position: relative;">
                        <canvas id="financialTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grade-wise Student Strength Distribution Chart -->
        <div class="col-lg-4 animate-fade-in-up delay-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-users-rectangle text-info me-2"></i>Class Strength</h5>
                        <small class="text-muted">Student enrollment per grade</small>
                    </div>
                    <a href="<?php echo URLROOT; ?>/reports/student" class="btn btn-sm btn-outline-info">
                        <i class="fa fa-list me-1"></i> Roster
                    </a>
                </div>
                <div class="card-body">
                    <div style="height: 280px; position: relative;">
                        <canvas id="classDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Community Overview & Quick Action Section -->
    <div class="row g-4 mb-4">
        <!-- Gender & Community Demographics Doughnut Card -->
        <div class="col-lg-8 animate-fade-in-up delay-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-chart-pie text-primary me-2"></i>Student Gender &amp; Campus Demographics</h5>
                        <small class="text-muted">Male vs Female student strength distribution and academic divisions</small>
                    </div>
                    <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-sm btn-outline-primary fw-bold">
                        <i class="fa fa-users me-1"></i> Student Directory
                    </a>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center mb-3 mb-md-0">
                            <div style="height: 240px; position: relative;" class="d-flex align-items-center justify-content-center">
                                <canvas id="genderRatioChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <!-- Male Students Card -->
                                <div class="p-3 rounded-3" style="background: rgba(37, 99, 235, 0.08); border-left: 4px solid #2563eb;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small fw-bold"><i class="fa fa-mars text-primary me-1"></i> Male Students (Boys)</span>
                                        <span class="badge bg-primary px-2 py-1"><?php echo number_format($mCount); ?></span>
                                    </div>
                                    <div class="h5 fw-bold mb-0 mt-1 text-primary"><?php echo $mPercent; ?>% of Total Strength</div>
                                </div>
                                
                                <!-- Female Students Card -->
                                <div class="p-3 rounded-3" style="background: rgba(236, 72, 153, 0.08); border-left: 4px solid #ec4899;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small fw-bold"><i class="fa fa-venus me-1" style="color: #ec4899;"></i> Female Students (Girls)</span>
                                        <span class="badge px-2 py-1 text-white" style="background: #ec4899;"><?php echo number_format($fCount); ?></span>
                                    </div>
                                    <div class="h5 fw-bold mb-0 mt-1" style="color: #ec4899;"><?php echo $fPercent; ?>% of Total Strength</div>
                                </div>

                                <!-- Classes & Sections Breakdown Card -->
                                <div class="p-2 px-3 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block">Academic Structure</small>
                                        <span class="fw-bold text-dark"><i class="fa fa-chalkboard text-warning me-1"></i><?php echo $classCount; ?> Classes</span> &bull; 
                                        <span class="fw-bold text-dark"><i class="fa fa-shapes text-info me-1"></i><?php echo $secCount; ?> Sections</span>
                                    </div>
                                    <span class="badge bg-dark text-white font-monospace"><?php echo number_format($studentCount); ?> Total</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Operations Hub -->
        <div class="col-lg-4 animate-fade-in-up delay-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold">Quick Operations Hub</h5>
                    <small class="text-muted">Instant shortcuts for frequent tasks</small>
                </div>
                <div class="card-body">
                    <a href="<?php echo URLROOT; ?>/students/admission" class="quick-action-tile">
                        <div class="action-tile-icon" style="background: rgba(79, 70, 229, 0.1); color: #4f46e5;">
                            <i class="fa fa-user-plus"></i>
                        </div>
                        <div class="action-tile-text">
                            <div class="action-tile-title">Admit Student</div>
                            <div class="action-tile-desc">Register new student admission</div>
                        </div>
                        <div class="action-tile-arrow">
                            <i class="fa fa-chevron-right"></i>
                        </div>
                    </a>

                    <a href="<?php echo URLROOT; ?>/staff/add" class="quick-action-tile">
                        <div class="action-tile-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="fa fa-user-tie"></i>
                        </div>
                        <div class="action-tile-text">
                            <div class="action-tile-title">Onboard Staff</div>
                            <div class="action-tile-desc">Add teacher or staff member</div>
                        </div>
                        <div class="action-tile-arrow">
                            <i class="fa fa-chevron-right"></i>
                        </div>
                    </a>

                    <a href="<?php echo URLROOT; ?>/fees/collect" class="quick-action-tile">
                        <div class="action-tile-icon" style="background: rgba(6, 182, 212, 0.1); color: #0891b2;">
                            <i class="fa fa-receipt"></i>
                        </div>
                        <div class="action-tile-text">
                            <div class="action-tile-title">Collect Fees</div>
                            <div class="action-tile-desc">Process student invoices & dues</div>
                        </div>
                        <div class="action-tile-arrow">
                            <i class="fa fa-chevron-right"></i>
                        </div>
                    </a>

                    <a href="<?php echo URLROOT; ?>/reports/index" class="quick-action-tile">
                        <div class="action-tile-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                            <i class="fa fa-chart-line"></i>
                        </div>
                        <div class="action-tile-text">
                            <div class="action-tile-title">Reports Center</div>
                            <div class="action-tile-desc">Print financial, academic & HR reports</div>
                        </div>
                        <div class="action-tile-arrow">
                            <i class="fa fa-chevron-right"></i>
                        </div>
                    </a>

                    <?php if($_SESSION['user_role'] == 'super_admin' || $_SESSION['user_role'] == 'admin'): ?>
                    <a href="<?php echo URLROOT; ?>/setting/index" class="quick-action-tile">
                        <div class="action-tile-icon" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                            <i class="fa fa-cog"></i>
                        </div>
                        <div class="action-tile-text">
                            <div class="action-tile-title">Institutional Settings</div>
                            <div class="action-tile-desc">School profile, prefixes & RBAC</div>
                        </div>
                        <div class="action-tile-arrow">
                            <i class="fa fa-chevron-right"></i>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Shortcuts Command Hub -->
    <div class="row g-4">
        <div class="col-12 animate-fade-in-up delay-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold">Academic Management Hub</h5>
                        <small class="text-muted">Direct modules for day-to-day school workflows</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <a href="<?php echo URLROOT; ?>/classes/index" class="card h-100 border text-decoration-none p-3 hover-elevate">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon-squircle primary" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                        <i class="fa fa-chalkboard"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Academics &amp; Classes</div>
                                        <small class="text-muted">Grades, Sections &amp; Subjects</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?php echo URLROOT; ?>/timetable/index" class="card h-100 border text-decoration-none p-3 hover-elevate">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon-squircle warning" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                        <i class="fa fa-clock"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Class Timetables</div>
                                        <small class="text-muted">Weekly Period Schedule</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?php echo URLROOT; ?>/exam/index" class="card h-100 border text-decoration-none p-3 hover-elevate">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon-squircle" style="width: 40px; height: 40px; font-size: 1.1rem; background: rgba(147, 51, 234, 0.1); color: #9333ea;">
                                        <i class="fa fa-file-signature"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Exam Management</div>
                                        <small class="text-muted">Exam Papers &amp; Schedule</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="<?php echo URLROOT; ?>/exam/gazette" class="card h-100 border text-decoration-none p-3 hover-elevate">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="stat-icon-squircle success" style="width: 40px; height: 40px; font-size: 1.1rem;">
                                        <i class="fa fa-award"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Result Gazette &amp; DMC</div>
                                        <small class="text-muted">Print Official Scorecards</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    function initDashboardCharts() {
        // 1. Male vs Female Gender Ratio Doughnut Chart
        const ctxGender = document.getElementById('genderRatioChart');
        if (ctxGender && !ctxGender._chartInstance) {
            ctxGender._chartInstance = new Chart(ctxGender.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Male Students', 'Female Students'],
                    datasets: [{
                        data: [
                            <?php echo $mCount; ?>, 
                            <?php echo $fCount; ?>
                        ],
                        backgroundColor: [
                            '#2563eb',
                            '#ec4899'
                        ],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600', size: 12 }
                            }
                        }
                    }
                }
            });
        }

        // 2. Financial Trend Comparative Chart (Income vs Expense)
        const ctxFinance = document.getElementById('financialTrendChart');
        if (ctxFinance && !ctxFinance._chartInstance) {
            ctxFinance._chartInstance = new Chart(ctxFinance.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($data['months_trend'] ?? ['Month 1', 'Month 2', 'Month 3', 'Month 4', 'Month 5', 'Month 6']); ?>,
                    datasets: [
                        {
                            label: 'Fee Collections (Income)',
                            data: <?php echo json_encode($data['income_trend'] ?? [0,0,0,0,0,0]); ?>,
                            backgroundColor: 'rgba(37, 99, 235, 0.85)',
                            borderColor: '#2563eb',
                            borderRadius: 6
                        },
                        {
                            label: 'Operational Expenses',
                            data: <?php echo json_encode($data['expense_trend'] ?? [0,0,0,0,0,0]); ?>,
                            backgroundColor: 'rgba(239, 68, 68, 0.85)',
                            borderColor: '#ef4444',
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600', size: 12 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': <?php echo $data['currency'] ?? 'Rs.'; ?> ' + Number(context.raw).toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // 3. Class-wise Student Distribution Chart
        const ctxClass = document.getElementById('classDistributionChart');
        if (ctxClass && !ctxClass._chartInstance) {
            <?php 
                $cLabels = [];
                $cCounts = [];
                if (!empty($data['class_distribution'])) {
                    foreach ($data['class_distribution'] as $cd) {
                        $cLabels[] = $cd->class_name;
                        $cCounts[] = (int)$cd->total_students;
                    }
                }
            ?>
            ctxClass._chartInstance = new Chart(ctxClass.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(!empty($cLabels) ? $cLabels : ['Class 1', 'Class 2', 'Class 3']); ?>,
                    datasets: [{
                        label: 'Students Enrolled',
                        data: <?php echo json_encode(!empty($cCounts) ? $cCounts : [25, 30, 20]); ?>,
                        backgroundColor: '#0891b2',
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        y: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboardCharts);
    } else {
        initDashboardCharts();
    }
</script>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
