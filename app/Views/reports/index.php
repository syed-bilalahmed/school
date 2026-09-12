<?php require APPROOT . '/Views/layouts/header.php'; ?>

<div class="container-fluid px-0">
    <!-- Header Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reports Center</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-chart-line text-primary me-2"></i>Institutional Reports &amp; Executive Intelligence Center
            </h2>
            <small class="text-muted">Generate formal printable audit reports, operational ledgers, and academic summaries.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-tachometer-alt me-1"></i> Executive Dashboard
            </a>
            <a href="<?php echo URLROOT; ?>/setting/index" class="btn btn-secondary btn-sm">
                <i class="fa fa-sliders me-1"></i> Settings &amp; RBAC
            </a>
        </div>
    </div>

    <!-- Executive Analytics Spotlight Card -->
    <div class="card border-0 shadow-sm p-4 mb-4 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 12px;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-white bg-opacity-25 text-white mb-2 px-3 py-1">
                    <i class="fa fa-tachometer-alt me-1"></i> Real-time Analytics Module 21
                </span>
                <h4 class="fw-bold mb-1">Executive Principal Analytics Dashboard</h4>
                <p class="mb-0 text-white-50">View real-time student attendance rate, 6-month comparative fee vs expense performance graph, class enrollment distribution, and front desk activity pulse.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-light text-primary fw-bold shadow-sm px-4">
                    <i class="fa fa-chart-pie me-1"></i> Open Principal Analytics
                </a>
            </div>
        </div>
    </div>

    <!-- Reports Grid Row -->
    <div class="row g-4">
        
        <!-- 1. Student Information -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary">
                            <i class="fa fa-user-graduate fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Student Information</h5>
                    </div>
                </div>
                <div class="card-body px-4">
                    <p class="small text-muted mb-3">Enrolment rosters, class section filters, family clustering, and demographic reports.</p>
                    <div class="list-group list-group-flush small">
                        <a href="<?php echo URLROOT; ?>/reports/student" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-primary me-2"></i>Class-wise Student Directory</span>
                            <span class="badge bg-light text-muted border">Printable</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/reports/student?status=Active" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-primary me-2"></i>Active Students Census</span>
                            <span class="badge bg-light text-muted border">Census</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/students/admission" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-primary me-2"></i>Admission Enrolment Register</span>
                            <span class="badge bg-light text-muted border">Module 3</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Financial & Fee Accounting -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success">
                            <i class="fa fa-money-bill-wave fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Finance &amp; Collections</h5>
                    </div>
                </div>
                <div class="card-body px-4">
                    <p class="small text-muted mb-3">Daily fee collection receipts, mode-wise breakdowns, operating expenses, and cash book balance.</p>
                    <div class="list-group list-group-flush small">
                        <a href="<?php echo URLROOT; ?>/reports/finance" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-success me-2"></i>Fee Collections Ledger</span>
                            <span class="badge bg-light text-muted border">Cash &amp; Bank</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/fees/defaulters" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-success me-2"></i>Fee Defaulters &amp; Overdue Dues</span>
                            <span class="badge bg-light text-muted border">Module 9</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/incomes/index" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-success me-2"></i>Incomes &amp; Cash Book</span>
                            <span class="badge bg-light text-muted border">Module 17</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/expense/index" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-success me-2"></i>Disbursement &amp; Expenses</span>
                            <span class="badge bg-light text-muted border">Module 18</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Attendance Intelligence -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 bg-info bg-opacity-10 text-info">
                            <i class="fa fa-calendar-check fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Attendance Audit</h5>
                    </div>
                </div>
                <div class="card-body px-4">
                    <p class="small text-muted mb-3">Daily student presence logs, class absentee lists, late arrival frequency, and leave records.</p>
                    <div class="list-group list-group-flush small">
                        <a href="<?php echo URLROOT; ?>/reports/attendance" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-info me-2"></i>Daily Student Attendance Audit</span>
                            <span class="badge bg-light text-muted border">Summary</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/reports/attendance?attendance_type=Absent" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-info me-2"></i>Absentees &amp; Truancy Report</span>
                            <span class="badge bg-light text-muted border">SMS Trigger</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/attendance/student" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-info me-2"></i>Live Attendance Register</span>
                            <span class="badge bg-light text-muted border">Module 4</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Academics & Curriculum -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary">
                            <i class="fa fa-book-open fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Academics &amp; Curriculum</h5>
                    </div>
                </div>
                <div class="card-body px-4">
                    <p class="small text-muted mb-3">Academic grades, sections, course subjects, teacher class allocations, and daily timetables.</p>
                    <div class="list-group list-group-flush small">
                        <a href="<?php echo URLROOT; ?>/classes/index" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-primary me-2"></i>Class &amp; Section Directory</span>
                            <span class="badge bg-light text-muted border">Structure</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/subjects/index" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-primary me-2"></i>Course Subjects &amp; Syllabus</span>
                            <span class="badge bg-light text-muted border">Subjects</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/timetable/index" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-primary me-2"></i>Class Timetable Schedule</span>
                            <span class="badge bg-light text-muted border">Schedule</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/promote/index" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-primary me-2"></i>Promotion Committee Ledger</span>
                            <span class="badge bg-light text-muted border">Roll-Over</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Examinations & Assessments -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 bg-purple bg-opacity-10 text-purple" style="color: #9333ea; background-color: rgba(147, 51, 234, 0.1);">
                            <i class="fa fa-award fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Examinations &amp; Results</h5>
                    </div>
                </div>
                <div class="card-body px-4">
                    <p class="small text-muted mb-3">Term assessment tabulations, subject pass percentages, highest scores, and gazette cards.</p>
                    <div class="list-group list-group-flush small">
                        <a href="<?php echo URLROOT; ?>/reports/exams" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-purple me-2"></i>Term Examination Score Sheet</span>
                            <span class="badge bg-light text-muted border">Score Sheet</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/exam/marks" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-purple me-2"></i>Subject Marks Entry Grid</span>
                            <span class="badge bg-light text-muted border">Marks Grid</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/exam/gazette" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-purple me-2"></i>Result Gazette &amp; DMC</span>
                            <span class="badge bg-light text-muted border">Official DMC</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/certificate/batchAdmitCards" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-purple me-2"></i>Batch Exam Roll Admit Cards</span>
                            <span class="badge bg-light text-muted border">Roll Slips</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Human Resources & Workload -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 bg-warning bg-opacity-10 text-warning">
                            <i class="fa fa-users-gear fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Faculty &amp; HR Workload</h5>
                    </div>
                </div>
                <div class="card-body px-4">
                    <p class="small text-muted mb-3">Faculty directory, visiting lecture rates, monthly payroll obligations, and staff roles.</p>
                    <div class="list-group list-group-flush small">
                        <a href="<?php echo URLROOT; ?>/reports/staff" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-warning me-2"></i>Staff &amp; Faculty Remuneration</span>
                            <span class="badge bg-light text-muted border">Phase 11</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/payroll/index" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-warning me-2"></i>Monthly Staff Payroll Ledger</span>
                            <span class="badge bg-light text-muted border">Module 19</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/staff/index" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-warning me-2"></i>Faculty Profiles &amp; Workload</span>
                            <span class="badge bg-light text-muted border">Module 6</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Institutional Security & Clearance -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 p-2 bg-danger bg-opacity-10 text-danger">
                            <i class="fa fa-shield-halved fa-lg"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">Front Office &amp; Clearance</h5>
                    </div>
                </div>
                <div class="card-body px-4">
                    <p class="small text-muted mb-3">Institutional exit clearance progress, gate visitor logs, child safety gate passes, and leads.</p>
                    <div class="list-group list-group-flush small">
                        <a href="<?php echo URLROOT; ?>/clearance/index" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-danger me-2"></i>Exit Clearance Audit</span>
                            <span class="badge bg-light text-muted border">Module 20</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/frontoffice/visitor" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-danger me-2"></i>Visitor Gate Book Log</span>
                            <span class="badge bg-light text-muted border">Front Desk</span>
                        </a>
                        <a href="<?php echo URLROOT; ?>/frontoffice/gatePass" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-2">
                            <span><i class="fa fa-angle-right text-danger me-2"></i>Student Gate Pass Register</span>
                            <span class="badge bg-light text-muted border">Safety</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

