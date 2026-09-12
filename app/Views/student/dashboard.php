<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$student = $data['student'] ?? null;
$dossier = $data['dossier'] ?? null;
$schedule = $data['schedule'] ?? [];
$homework = $data['homework'] ?? [];
$todayAtt = $data['today_attendance'] ?? null;
$notices = $data['notices'] ?? [];
$userRole = $data['user_role'] ?? 'student';

$attSummary = $dossier['attendance']['summary'] ?? null;
$attPercent = $dossier['attendance']['percentage'] ?? 100;
$finance = $dossier['finance'] ?? null;
$balance = $finance['balance'] ?? 0;
$fees = $finance['fees'] ?? [];
$payments = $finance['payments'] ?? [];
$exams = $dossier['exams'] ?? [];

// Today's classes
$todayDayName = date('l');
$todayClasses = [];
foreach($schedule as $sc){
    if(strcasecmp($sc->day_name ?? '', $todayDayName) === 0){
        $todayClasses[] = $sc;
    }
}
?>

<div class="container-fluid px-0">
    <!-- Breadcrumb & Portal Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/student/index" class="text-decoration-none text-muted">Portal</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Student Academic Hub</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-user-graduate text-primary me-2"></i>Student Portal &amp; Academic Desk
            </h2>
            <small class="text-muted">Real-time gate attendance, lecture schedule, homework diary, fee ledger, and official DMC progress reports.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if($student): ?>
                <a href="<?php echo URLROOT; ?>/fees/challan/<?php echo $student->id; ?>" target="_blank" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                    <i class="fa fa-print me-1"></i> Print Fee Challan
                </a>
                <a href="<?php echo URLROOT; ?>/student/results" class="btn btn-primary btn-sm px-3 shadow-xs">
                    <i class="fa fa-award me-1"></i> Exam Results
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if(!$student): ?>
        <div class="card shadow-sm border-0 py-5 text-center text-muted">
            <div class="card-body">
                <i class="fa fa-user-graduate fa-4x text-secondary opacity-50 mb-3"></i>
                <h5>No Enrolled Student Profile Found</h5>
                <p class="small text-muted mb-0" style="max-width: 480px; margin: 0 auto;">
                    Your account is not currently linked to an active student admission record. Please contact the campus administration to verify your admission particulars.
                </p>
            </div>
        </div>
    <?php else: ?>

        <!-- 1. Student Profile Hero Banner -->
        <div class="card border-0 shadow-sm mb-4 overflow-hidden rounded-3">
            <div class="card-body p-4 bg-light-subtle border-bottom">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student->name); ?>&size=80&background=4f46e5&color=fff&bold=true" class="rounded-circle border border-3 border-white shadow-sm" width="75" height="75" alt="Avatar">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($student->name); ?></h4>
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">Active Enrolled</span>
                                <?php if($userRole !== 'student'): ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-monospace">Admin Preview</span>
                                <?php endif; ?>
                            </div>
                            <div class="small text-muted d-flex flex-wrap align-items-center gap-3">
                                <span><i class="fa fa-chalkboard text-secondary me-1"></i> Class: <strong><?php echo htmlspecialchars($student->class_name . ' - ' . $student->section_name); ?></strong></span>
                                <span><i class="fa fa-id-badge text-secondary me-1"></i> Roll #: <strong><?php echo htmlspecialchars($student->roll_no ?: '-'); ?></strong></span>
                                <span><i class="fa fa-hashtag text-secondary me-1"></i> Admission #: <strong><?php echo htmlspecialchars($student->admission_no); ?></strong></span>
                                <?php if(!empty($student->father_name)): ?>
                                    <span><i class="fa fa-user text-secondary me-1"></i> Father: <strong><?php echo htmlspecialchars($student->father_name); ?></strong></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Attendance Real-Time Status Widget -->
                    <div class="bg-white p-3 rounded-3 border shadow-xs d-flex align-items-center gap-3">
                        <div class="text-center">
                            <span class="d-block small text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Today's Gate Punch</span>
                            <span class="fw-bold text-dark" style="font-size: 0.85rem;"><?php echo date('D, d M Y'); ?></span>
                        </div>
                        <div class="vr"></div>
                        <div>
                            <?php 
                            $statusType = $todayAtt->attendance_type ?? 'Not Marked';
                            $badgeClass = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                            $iconClass = 'fa-clock';
                            if($statusType === 'Present') {
                                $badgeClass = 'bg-success-subtle text-success border-success-subtle';
                                $iconClass = 'fa-circle-check';
                            } elseif($statusType === 'Late') {
                                $badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
                                $iconClass = 'fa-triangle-exclamation';
                            } elseif($statusType === 'Absent') {
                                $badgeClass = 'bg-danger-subtle text-danger border-danger-subtle';
                                $iconClass = 'fa-circle-xmark';
                            } elseif($statusType === 'Half Day') {
                                $badgeClass = 'bg-info-subtle text-info border-info-subtle';
                                $iconClass = 'fa-hourglass-half';
                            }
                            ?>
                            <span class="badge <?php echo $badgeClass; ?> border px-3 py-2 d-flex align-items-center gap-2 fs-6 rounded-pill">
                                <i class="fa <?php echo $iconClass; ?>"></i>
                                <span><?php echo htmlspecialchars($statusType); ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Academic KPI Statistics Cards -->
        <div class="row g-3 mb-4">
            <!-- Attendance Rate -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Attendance Rate</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1"><?php echo $attPercent; ?>%</h3>
                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                <?php echo (int)($attSummary->present_days ?? 0); ?> of <?php echo (int)($attSummary->total_days ?? 0); ?> Days Present
                            </div>
                        </div>
                        <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                            <i class="fa fa-calendar-check fs-4"></i>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo min(100, $attPercent); ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Fee Status / Balance -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Tuition Fee Balance</span>
                            <h3 class="fw-bold mb-0 <?php echo $balance > 0 ? 'text-danger' : 'text-success'; ?> mt-1">
                                <?php echo htmlspecialchars($_SESSION['currency_symbol'] ?? 'Rs.'); ?> <?php echo number_format($balance); ?>
                            </h3>
                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                <?php if($balance > 0): ?>
                                    <span class="text-danger fw-semibold"><i class="fa fa-clock me-1"></i>Dues Pending</span>
                                <?php else: ?>
                                    <span class="text-success fw-semibold"><i class="fa fa-check-circle me-1"></i>All Cleared</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="p-2 <?php echo $balance > 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success'; ?> rounded-3">
                            <i class="fa fa-receipt fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-end">
                        <a href="<?php echo URLROOT; ?>/fees/challan/<?php echo $student->id; ?>" target="_blank" class="small text-decoration-none text-primary fw-semibold" style="font-size: 0.75rem;">
                            View Bank Challan <i class="fa fa-arrow-right fa-xs ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Today's Classes -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Today's Classes</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1"><?php echo count($todayClasses); ?></h3>
                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                Scheduled on <?php echo $todayDayName; ?>
                            </div>
                        </div>
                        <div class="p-2 bg-info bg-opacity-10 text-info rounded-3">
                            <i class="fa fa-chalkboard-user fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-end">
                        <a href="#tab-schedule" class="small text-decoration-none text-info fw-semibold" style="font-size: 0.75rem;" onclick="document.getElementById('pills-schedule-tab').click();">
                            View Schedule <i class="fa fa-arrow-right fa-xs ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Homework -->
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Homework Diary</span>
                            <h3 class="fw-bold mb-0 text-dark mt-1"><?php echo count($homework); ?></h3>
                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                Active Assignments Posted
                            </div>
                        </div>
                        <div class="p-2 bg-purple bg-opacity-10 text-purple rounded-3" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                            <i class="fa fa-book-reader fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-2 text-end">
                        <a href="#tab-homework" class="small text-decoration-none fw-semibold" style="font-size: 0.75rem; color: #8b5cf6;" onclick="document.getElementById('pills-hw-tab').click();">
                            Open Diary <i class="fa fa-arrow-right fa-xs ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Navigation Tabs -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-2 bg-white rounded-3">
                <ul class="nav nav-pills nav-justified flex-column flex-md-row gap-1" id="studentTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link py-2 px-3 fw-semibold active" id="pills-schedule-tab" data-bs-toggle="pill" data-bs-target="#tab-schedule" type="button" role="tab">
                            <i class="fa fa-clock me-2"></i>Timetable &amp; Schedule
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-2 px-3 fw-semibold" id="pills-hw-tab" data-bs-toggle="pill" data-bs-target="#tab-homework" type="button" role="tab">
                            <i class="fa fa-book-reader me-2"></i>Homework Diary (<?php echo count($homework); ?>)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-2 px-3 fw-semibold" id="pills-att-tab" data-bs-toggle="pill" data-bs-target="#tab-attendance" type="button" role="tab">
                            <i class="fa fa-calendar-check me-2"></i>Attendance Log
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-2 px-3 fw-semibold" id="pills-fees-tab" data-bs-toggle="pill" data-bs-target="#tab-fees" type="button" role="tab">
                            <i class="fa fa-receipt me-2"></i>Fee Ledger &amp; Challans
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-2 px-3 fw-semibold" id="pills-exam-tab" data-bs-toggle="pill" data-bs-target="#tab-exams" type="button" role="tab">
                            <i class="fa fa-award me-2"></i>Examination DMC
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-2 px-3 fw-semibold" id="pills-notices-tab" data-bs-toggle="pill" data-bs-target="#tab-notices" type="button" role="tab">
                            <i class="fa fa-bullhorn me-2"></i>Campus Notices
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 4. Tab Contents -->
        <div class="tab-content mb-5" id="studentTabsContent">
            
            <!-- TAB 1: TIMETABLE & SCHEDULE -->
            <div class="tab-pane fade show active" id="tab-schedule" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa fa-calendar-day text-primary me-2"></i>Class Schedule &mdash; <?php echo htmlspecialchars($student->class_name . ' (' . $student->section_name . ')'); ?>
                        </h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                            Today: <?php echo $todayDayName; ?>
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <?php if(empty($schedule)): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fa fa-calendar-times fa-3x text-secondary opacity-50 mb-2"></i>
                                <p class="mb-0 small">No class timetable slots currently published for this section.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Day</th>
                                            <th>Time Slot</th>
                                            <th>Subject</th>
                                            <th>Subject Type</th>
                                            <th>Teacher / Instructor</th>
                                            <th>Room #</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($schedule as $slot): 
                                            $isToday = (strcasecmp($slot->day_name ?? '', $todayDayName) === 0);
                                        ?>
                                            <tr class="<?php echo $isToday ? 'table-primary bg-opacity-25' : ''; ?>">
                                                <td class="ps-4 fw-bold">
                                                    <?php echo htmlspecialchars($slot->day_name); ?>
                                                    <?php if($isToday): ?>
                                                        <span class="badge bg-primary ms-1" style="font-size: 9px;">TODAY</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="font-monospace fw-semibold text-dark">
                                                    <?php echo date('h:i A', strtotime($slot->time_from)); ?> &ndash; <?php echo date('h:i A', strtotime($slot->time_to)); ?>
                                                </td>
                                                <td class="fw-bold text-primary">
                                                    <?php echo htmlspecialchars($slot->subject_name); ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 11px;">
                                                        <?php echo htmlspecialchars($slot->subject_type ?? 'Theory'); ?>
                                                    </span>
                                                </td>
                                                <td class="text-secondary">
                                                    <i class="fa fa-chalkboard-user me-1 text-muted"></i><?php echo htmlspecialchars($slot->staff_name ?: 'Faculty Assigned'); ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-secondary border font-monospace">
                                                        <?php echo htmlspecialchars($slot->room_no ?: 'Room 101'); ?>
                                                    </span>
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

            <!-- TAB 2: HOMEWORK DIARY -->
            <div class="tab-pane fade" id="tab-homework" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa fa-book-reader text-primary me-2"></i>Assigned Homework &amp; Coursework Tasks
                        </h6>
                        <span class="badge bg-light text-dark border font-monospace">
                            <?php echo count($homework); ?> Assignments
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <?php if(empty($homework)): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fa fa-check-double fa-3x text-success opacity-50 mb-2"></i>
                                <p class="mb-0 small">No pending homework assignments recorded! You are completely up to date.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Subject</th>
                                            <th>Task Description</th>
                                            <th>Assigned Date</th>
                                            <th>Submission Deadline</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($homework as $hw): 
                                            $isOverdue = (!empty($hw->submission_date) && strtotime($hw->submission_date) < strtotime(date('Y-m-d')));
                                        ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-primary">
                                                    <i class="fa fa-book me-1 text-muted"></i><?php echo htmlspecialchars($hw->subject_name); ?>
                                                </td>
                                                <td class="text-dark" style="max-width: 420px;">
                                                    <?php echo nl2br(htmlspecialchars($hw->description)); ?>
                                                </td>
                                                <td class="text-muted small">
                                                    <?php echo !empty($hw->homework_date) ? date('d M Y', strtotime($hw->homework_date)) : '-'; ?>
                                                </td>
                                                <td class="fw-bold font-monospace <?php echo $isOverdue ? 'text-danger' : 'text-dark'; ?>">
                                                    <?php echo !empty($hw->submission_date) ? date('d M Y', strtotime($hw->submission_date)) : '-'; ?>
                                                </td>
                                                <td>
                                                    <?php if($isOverdue): ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace">Overdue</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-monospace">Pending Due</span>
                                                    <?php endif; ?>
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

            <!-- TAB 3: ATTENDANCE LOG -->
            <div class="tab-pane fade" id="tab-attendance" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa fa-calendar-check text-primary me-2"></i>Gate Attendance Register &amp; Roll-Call Logs
                        </h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                            Overall: <?php echo $attPercent; ?>% Present
                        </span>
                    </div>
                    <div class="card-body p-3">
                        <!-- Summary Row -->
                        <div class="row g-2 mb-3">
                            <div class="col-3">
                                <div class="p-2 border rounded-3 text-center bg-light">
                                    <span class="small text-muted d-block" style="font-size: 11px;">Present Days</span>
                                    <strong class="text-success fs-5"><?php echo (int)($attSummary->present_days ?? 0); ?></strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-2 border rounded-3 text-center bg-light">
                                    <span class="small text-muted d-block" style="font-size: 11px;">Absents</span>
                                    <strong class="text-danger fs-5"><?php echo (int)($attSummary->absent_days ?? 0); ?></strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-2 border rounded-3 text-center bg-light">
                                    <span class="small text-muted d-block" style="font-size: 11px;">Lates</span>
                                    <strong class="text-warning fs-5"><?php echo (int)($attSummary->late_days ?? 0); ?></strong>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-2 border rounded-3 text-center bg-light">
                                    <span class="small text-muted d-block" style="font-size: 11px;">Half Days</span>
                                    <strong class="text-info fs-5"><?php echo (int)($attSummary->half_days ?? 0); ?></strong>
                                </div>
                            </div>
                        </div>

                        <!-- Logs Table -->
                        <?php $attLogs = $dossier['attendance']['logs'] ?? []; ?>
                        <?php if(empty($attLogs)): ?>
                            <p class="text-muted text-center py-3 mb-0 small">No attendance punch logs found on record.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Date</th>
                                            <th>Day</th>
                                            <th>Recorded Status</th>
                                            <th>Supervisor Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($attLogs as $log): ?>
                                            <tr>
                                                <td class="ps-3 fw-bold font-monospace"><?php echo date('d M Y', strtotime($log->date)); ?></td>
                                                <td class="text-muted"><?php echo date('l', strtotime($log->date)); ?></td>
                                                <td>
                                                    <?php 
                                                    $type = $log->attendance_type;
                                                    $cls = 'bg-secondary text-white';
                                                    if($type === 'Present') $cls = 'bg-success text-white';
                                                    elseif($type === 'Absent') $cls = 'bg-danger text-white';
                                                    elseif($type === 'Late') $cls = 'bg-warning text-dark';
                                                    elseif($type === 'Half Day') $cls = 'bg-info text-dark';
                                                    ?>
                                                    <span class="badge <?php echo $cls; ?> font-monospace"><?php echo htmlspecialchars($type); ?></span>
                                                </td>
                                                <td class="text-secondary small"><?php echo htmlspecialchars($log->remark ?: '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- TAB 4: FEE LEDGER & CHALLANS -->
            <div class="tab-pane fade" id="tab-fees" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa fa-money-check-dollar text-primary me-2"></i>Institutional Tuition Fee Ledger
                        </h6>
                        <a href="<?php echo URLROOT; ?>/fees/challan/<?php echo $student->id; ?>" target="_blank" class="btn btn-primary btn-sm px-3 shadow-xs">
                            <i class="fa fa-print me-1"></i> Print 3-Copy Bank Challan
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if(empty($fees)): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fa fa-receipt fa-3x text-secondary opacity-50 mb-2"></i>
                                <p class="mb-0 small">No fee structures assigned for this student record.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Fee Particular</th>
                                            <th>Due Date</th>
                                            <th>Assigned Amount</th>
                                            <th>Paid Amount</th>
                                            <th>Balance</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($fees as $f): 
                                            $fBal = max(0, (float)$f->amount - (float)$f->total_paid);
                                            $isPaid = ($fBal <= 0);
                                        ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-dark">
                                                    <?php echo htmlspecialchars($f->group_name . ' &mdash; ' . $f->type_name); ?>
                                                </td>
                                                <td class="text-muted font-monospace small">
                                                    <?php echo !empty($f->due_date) ? date('d M Y', strtotime($f->due_date)) : '10th of Month'; ?>
                                                </td>
                                                <td class="fw-bold font-monospace">
                                                    <?php echo htmlspecialchars($_SESSION['currency_symbol'] ?? 'Rs.'); ?> <?php echo number_format($f->amount); ?>
                                                </td>
                                                <td class="fw-bold font-monospace text-success">
                                                    <?php echo htmlspecialchars($_SESSION['currency_symbol'] ?? 'Rs.'); ?> <?php echo number_format($f->total_paid); ?>
                                                </td>
                                                <td class="fw-bold font-monospace <?php echo $fBal > 0 ? 'text-danger' : 'text-success'; ?>">
                                                    <?php echo htmlspecialchars($_SESSION['currency_symbol'] ?? 'Rs.'); ?> <?php echo number_format($fBal); ?>
                                                </td>
                                                <td>
                                                    <?php if($isPaid): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">Paid</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace">Unpaid</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Payment Receipts History -->
                <?php if(!empty($payments)): ?>
                    <div class="card border-0 shadow-sm rounded-3">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-receipt text-success me-2"></i>Official Bank &amp; Cash Payment Receipts
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Receipt / Voucher #</th>
                                            <th>Payment Date</th>
                                            <th>Fee Category</th>
                                            <th>Amount Paid</th>
                                            <th>Payment Mode</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($payments as $p): ?>
                                            <tr>
                                                <td class="ps-3 fw-bold font-monospace text-primary">
                                                    RCP-<?php echo str_pad($p->id, 5, '0', STR_PAD_LEFT); ?>
                                                </td>
                                                <td class="font-monospace text-muted"><?php echo date('d M Y', strtotime($p->payment_date)); ?></td>
                                                <td class="fw-semibold text-dark"><?php echo htmlspecialchars($p->type_name ?? 'Tuition Fee'); ?></td>
                                                <td class="fw-bold font-monospace text-success">
                                                    <?php echo htmlspecialchars($_SESSION['currency_symbol'] ?? 'Rs.'); ?> <?php echo number_format($p->amount); ?>
                                                </td>
                                                <td><span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($p->mode ?? 'Cash'); ?></span></td>
                                                <td class="text-muted small"><?php echo htmlspecialchars($p->note ?: '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- TAB 5: EXAMINATION DMC -->
            <div class="tab-pane fade" id="tab-exams" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa fa-award text-primary me-2"></i>Official Academic Performance &amp; DMC Result Cards
                        </h6>
                        <a href="<?php echo URLROOT; ?>/student/results" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                            <i class="fa fa-external-link-alt me-1"></i> Full Results Gazette
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <?php if(empty($exams)): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fa fa-file-lines fa-3x text-secondary opacity-50 mb-2"></i>
                                <p class="mb-0 small">No examination marks uploaded for this student yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Examination</th>
                                            <th>Subject</th>
                                            <th>Exam Date</th>
                                            <th>Marks Obtained</th>
                                            <th>Max Marks</th>
                                            <th>Pass Marks</th>
                                            <th>Status</th>
                                            <th class="text-end pe-4">Progress Card</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($exams as $ex): 
                                            $isAbsent = ($ex->is_absent ?? 'no') === 'yes';
                                            $marksObtained = $isAbsent ? 0 : (float)($ex->marks_obtained ?? 0);
                                            $passMarks = (float)($ex->passing_marks ?? 33);
                                            $isPassed = (!$isAbsent && $marksObtained >= $passMarks);
                                        ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-dark">
                                                    <?php echo htmlspecialchars($ex->exam_name); ?>
                                                </td>
                                                <td class="fw-semibold text-primary">
                                                    <?php echo htmlspecialchars($ex->subject_name); ?>
                                                </td>
                                                <td class="text-muted font-monospace small">
                                                    <?php echo !empty($ex->date_of_exam) ? date('d M Y', strtotime($ex->date_of_exam)) : '-'; ?>
                                                </td>
                                                <td class="fw-bold font-monospace <?php echo $isPassed ? 'text-success' : 'text-danger'; ?>">
                                                    <?php echo $isAbsent ? 'Absent' : $marksObtained; ?>
                                                </td>
                                                <td class="font-monospace text-muted"><?php echo (float)$ex->full_marks; ?></td>
                                                <td class="font-monospace text-muted"><?php echo $passMarks; ?></td>
                                                <td>
                                                    <?php if($isAbsent): ?>
                                                        <span class="badge bg-danger text-white font-monospace">Absent</span>
                                                    <?php elseif($isPassed): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">Pass</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace">Fail</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <?php if(!empty($ex->exam_id)): ?>
                                                        <a href="<?php echo URLROOT; ?>/exam/reportCard/<?php echo $ex->exam_id; ?>/<?php echo $student->id; ?>" target="_blank" class="btn btn-sm btn-outline-secondary px-2 py-1" title="Print Official DMC Card">
                                                            <i class="fa fa-print me-1"></i> DMC
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
                </div>
            </div>

            <!-- TAB 6: NOTICES -->
            <div class="tab-pane fade" id="tab-notices" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa fa-bullhorn text-primary me-2"></i>Official Institutional Notices &amp; Circulars
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <?php if(empty($notices)): ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fa fa-bell-slash fa-3x text-secondary opacity-50 mb-2"></i>
                                <p class="mb-0 small">No official notices currently published for students.</p>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach($notices as $notif): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 bg-light-subtle h-100 shadow-xs">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                                                    Circular
                                                </span>
                                                <span class="small text-muted font-monospace">
                                                    <i class="fa fa-calendar me-1"></i><?php echo date('d M Y', strtotime($notif->publish_date)); ?>
                                                </span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($notif->title); ?></h6>
                                            <p class="small text-muted mb-0"><?php echo nl2br(htmlspecialchars($notif->message)); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

    <?php endif; ?>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
