<?php require APPROOT . '/Views/layouts/header.php'; 
    $t = $data['teacher'] ?? null;
    $todayAtt = $data['today_attendance'] ?? null;
    $mStats = $data['month_stats'] ?? [];
    $todaySchedule = $data['today_schedule'] ?? [];
    $assignedClasses = $data['assigned_classes'] ?? [];
    $subjectAllocations = $data['subject_allocations'] ?? [];
    $attLogs = $data['attendance_logs'] ?? [];
    $notices = $data['notices'] ?? [];
    $payslips = $data['payslips'] ?? [];

    $teacherName = $t->name ?? ($_SESSION['user_name'] ?? 'Faculty Member');
    $staffCode = $t->staff_code ?? 'TCH-001';
    $designation = $t->designation ?? 'Subject Teacher';
    $department = $t->department ?? 'Academics';

    $todayStatus = $todayAtt ? $todayAtt->attendance_type : 'Not Marked';
    $todayRemark = $todayAtt ? $todayAtt->remark : 'Not checked in yet today';
?>

<div class="container-fluid px-0">
    <!-- 1. Breadcrumb & Executive Welcome Banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Portal</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Teacher Hub</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-chalkboard-user text-primary me-2"></i>Teacher Control Hub &amp; Academic Workspace
            </h2>
            <small class="text-muted">Manage daily attendance, classroom schedules, homework diaries, and student examinations.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-dark border px-3 py-2 shadow-xs">
                <i class="fa fa-id-badge text-primary me-1"></i> <?php echo htmlspecialchars($staffCode); ?> &bull; <strong><?php echo htmlspecialchars($teacherName); ?></strong>
            </span>
            <a href="<?php echo URLROOT; ?>/timetable/index" class="btn btn-outline-secondary btn-sm px-3 shadow-xs">
                <i class="fa fa-calendar-alt me-1"></i> Full Timetable
            </a>
            <a href="<?php echo URLROOT; ?>/attendance/student" class="btn btn-primary btn-sm px-3 shadow-xs fw-bold">
                <i class="fa fa-clipboard-check me-1"></i> Student Attendance
            </a>
        </div>
    </div>

    <!-- 2. Daily Attendance Check-In & Status Hero Card -->
    <div class="card border-0 shadow-sm mb-4 bg-white overflow-hidden" style="border-left: 4px solid #3b82f6 !important;">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-4 col-md-5 border-end-md">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 52px; height: 52px; background: rgba(59, 130, 246, 0.1);">
                            <i class="fa fa-fingerprint fa-2x"></i>
                        </div>
                        <div>
                            <div class="text-uppercase text-muted fw-bold small" style="letter-spacing: 0.5px; font-size: 0.72rem;">
                                Today's Attendance &bull; <?php echo date('l, d M Y'); ?>
                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span id="todayStatusBadge" class="badge <?php 
                                    if($todayStatus == 'Present') echo 'bg-success';
                                    elseif($todayStatus == 'Late') echo 'bg-warning text-dark';
                                    elseif($todayStatus == 'Half Day') echo 'bg-info text-dark';
                                    elseif($todayStatus == 'Absent') echo 'bg-danger';
                                    else echo 'bg-secondary';
                                ?> px-3 py-1 fs-6 fw-bold">
                                    <i class="fa <?php 
                                        if($todayStatus == 'Present') echo 'fa-check-circle';
                                        elseif($todayStatus == 'Late') echo 'fa-clock';
                                        elseif($todayStatus == 'Half Day') echo 'fa-adjust';
                                        elseif($todayStatus == 'Absent') echo 'fa-calendar-times';
                                        else echo 'fa-question-circle';
                                    ?> me-1"></i>
                                    <span id="todayStatusText"><?php echo htmlspecialchars($todayStatus); ?></span>
                                </span>
                            </div>
                            <small id="todayStatusRemark" class="text-muted d-block mt-1 font-monospace" style="font-size: 0.75rem;">
                                <?php echo htmlspecialchars($todayRemark); ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Instant Toggle / Punch Action Buttons -->
                <div class="col-lg-4 col-md-7 border-end-lg">
                    <div class="text-muted fw-semibold small mb-2" style="font-size: 0.75rem;">
                        <i class="fa fa-toggle-on text-primary me-1"></i> Quick Attendance Punch:
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-success fw-bold px-3 shadow-xs" onclick="punchAttendance('Present')">
                            <i class="fa fa-check me-1"></i> Check In (Present)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning px-2" onclick="punchAttendance('Late')">
                            <i class="fa fa-clock me-1"></i> Late
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info px-2" onclick="punchAttendance('Half Day')">
                            <i class="fa fa-adjust me-1"></i> Half Day
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger px-2" onclick="showLeavePrompt()">
                            <i class="fa fa-plane-departure me-1"></i> Leave
                        </button>
                    </div>
                </div>

                <!-- Month-to-Date Summary Counters -->
                <div class="col-lg-4 col-12">
                    <div class="d-flex align-items-center justify-content-between text-center bg-light rounded-3 p-2">
                        <div class="px-2">
                            <span class="d-block text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Presents</span>
                            <span id="statPresents" class="fw-bold fs-6 text-success"><?php echo $mStats['present'] ?? 0; ?></span>
                            <small class="text-muted d-block smaller">Days</small>
                        </div>
                        <div class="border-start px-2">
                            <span class="d-block text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Late/Half</span>
                            <span id="statLateHalf" class="fw-bold fs-6 text-warning"><?php echo ($mStats['late'] ?? 0) + ($mStats['half_day'] ?? 0); ?></span>
                            <small class="text-muted d-block smaller">Days</small>
                        </div>
                        <div class="border-start px-2">
                            <span class="d-block text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Absents</span>
                            <span id="statAbsents" class="fw-bold fs-6 text-danger"><?php echo $mStats['absent'] ?? 0; ?></span>
                            <small class="text-muted d-block smaller">Days</small>
                        </div>
                        <div class="border-start px-2">
                            <span class="d-block text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Attendance %</span>
                            <span id="statPercent" class="fw-bold fs-6 text-primary"><?php echo $mStats['percentage'] ?? 100; ?>%</span>
                            <small class="text-muted d-block smaller"><?php echo date('F'); ?></small>
                        </div>
                    </div>
                    <?php if(($mStats['estimated_deduction'] ?? 0) > 0): ?>
                        <div class="mt-2 text-danger small text-end" style="font-size: 0.75rem;">
                            <i class="fa fa-info-circle me-1"></i> Est. Leave Cutting: <strong>-Rs. <?php echo number_format($mStats['estimated_deduction']); ?></strong> (After <?php echo $mStats['free_leaves']; ?> free leaves)
                        </div>
                    <?php else: ?>
                        <div class="mt-2 text-success small text-end" style="font-size: 0.75rem;">
                            <i class="fa fa-shield-check me-1"></i> No salary deduction &bull; Full attendance record
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Key Academic KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm py-2 px-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center text-primary flex-shrink-0" style="width: 42px; height: 42px; background: rgba(59, 130, 246, 0.1);">
                        <i class="fa fa-chalkboard fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Class Teacher Of</div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <?php if(!empty($assignedClasses)): ?>
                                <?php echo htmlspecialchars($assignedClasses[0]->class_name . ' - ' . $assignedClasses[0]->section_name); ?>
                            <?php else: ?>
                                Subject Faculty
                            <?php endif; ?>
                        </h6>
                        <small class="text-muted" style="font-size: 0.72rem;"><?php echo count($assignedClasses); ?> Class Inchargeship</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm py-2 px-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center text-success flex-shrink-0" style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.1);">
                        <i class="fa fa-calendar-day fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Today's Lectures</div>
                        <h6 class="fw-bold mb-0 text-success"><?php echo count($todaySchedule); ?> Periods Scheduled</h6>
                        <small class="text-muted" style="font-size: 0.72rem;"><?php echo date('l'); ?> timetable</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm py-2 px-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center text-info flex-shrink-0" style="width: 42px; height: 42px; background: rgba(6, 182, 212, 0.1);">
                        <i class="fa fa-book-open fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Teaching Subjects</div>
                        <h6 class="fw-bold mb-0 text-info"><?php echo count($subjectAllocations); ?> Courses</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">Across secondary &amp; primary</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm py-2 px-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 d-flex align-items-center justify-content-center text-purple flex-shrink-0" style="width: 42px; height: 42px; color: #7c3aed; background: rgba(124, 58, 237, 0.1);">
                        <i class="fa fa-file-invoice-dollar fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.5px;">Salary Voucher</div>
                        <h6 class="fw-bold mb-0" style="color: #7c3aed;">
                            <?php if(!empty($payslips)): ?>
                                <?php echo htmlspecialchars($payslips[0]->month . ' ' . $payslips[0]->year); ?>
                            <?php else: ?>
                                Current Month
                            <?php endif; ?>
                        </h6>
                        <small class="text-muted" style="font-size: 0.72rem;">
                            <?php if(!empty($payslips)): ?>
                                Status: <strong class="text-success"><?php echo $payslips[0]->status; ?></strong>
                            <?php else: ?>
                                Vouchers ready
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Main Body Grid (Left: Schedule & Classes | Right: Actions & Attendance Logs) -->
    <div class="row g-4">
        <!-- Left Column (8 Cols) -->
        <div class="col-lg-8">
            <!-- Today's Teaching Schedule (Live from Timetable) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-clock text-primary fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">Today's Teaching Schedule &mdash; <?php echo date('l'); ?></h6>
                    </div>
                    <span class="badge bg-primary-subtle text-primary px-3 py-1 font-monospace">
                        <?php echo count($todaySchedule); ?> Periods Today
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="width: 100%; table-layout: fixed;">
                            <thead class="bg-light small">
                                <tr>
                                    <th class="ps-3" style="width: 25%;">Period / Time</th>
                                    <th style="width: 25%;">Class &amp; Section</th>
                                    <th style="width: 25%;">Subject</th>
                                    <th style="width: 13%;">Room</th>
                                    <th class="text-end pe-3" style="width: 12%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($todaySchedule)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            <i class="fa fa-coffee fa-2x mb-2 d-block opacity-50"></i>
                                            No class periods scheduled for today (<?php echo date('l'); ?>).
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($todaySchedule as $sch): 
                                        $fromFormatted = date('h:i A', strtotime($sch->time_from));
                                        $toFormatted = date('h:i A', strtotime($sch->time_to));
                                    ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-bold text-dark small"><?php echo $fromFormatted; ?> - <?php echo $toFormatted; ?></div>
                                                <span class="badge bg-light text-muted border py-0 px-1 font-monospace" style="font-size: 0.68rem;">Period</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary small"><?php echo htmlspecialchars($sch->class_name); ?></span>
                                                <div class="text-muted smaller" style="font-size: 0.72rem;"><?php echo htmlspecialchars($sch->section_name); ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark small"><?php echo htmlspecialchars($sch->subject_name); ?></div>
                                                <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.68rem;"><?php echo htmlspecialchars($sch->subject_code ?: 'SUB'); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace small">
                                                    <i class="fa fa-door-closed text-muted me-1"></i><?php echo htmlspecialchars($sch->room_no ?: 'Room 101'); ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="<?php echo URLROOT; ?>/attendance/student?class_id=<?php echo $sch->class_id; ?>&section_id=<?php echo $sch->section_id; ?>" class="btn btn-sm btn-outline-primary py-1 px-2 small" title="Take Student Attendance">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- My Allocated Subjects & Classes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-layer-group text-info fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">My Course Allocations &amp; Workload</h6>
                    </div>
                    <span class="badge bg-light text-muted border px-2 py-1 small">
                        <?php echo count($subjectAllocations); ?> Assigned Batches
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="width: 100%; table-layout: fixed;">
                            <thead class="bg-light small">
                                <tr>
                                    <th class="ps-3" style="width: 25%;">Subject</th>
                                    <th style="width: 25%;">Class &amp; Section</th>
                                    <th style="width: 20%;">Workload</th>
                                    <th style="width: 15%;">Enrolled</th>
                                    <th class="text-end pe-3" style="width: 15%;">Quick Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($subjectAllocations)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            No course allocations assigned yet. Please contact academic coordinator.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($subjectAllocations as $sa): ?>
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-bold text-dark small"><?php echo htmlspecialchars($sa->subject_name); ?></div>
                                                <small class="text-muted font-monospace" style="font-size: 0.72rem;"><?php echo htmlspecialchars($sa->subject_code); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1 font-monospace">
                                                    <?php echo htmlspecialchars($sa->class_name . ' - ' . $sa->section_name); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="small fw-semibold text-muted"><?php echo (int)($sa->periods_per_week ?: 5); ?> Periods / Wk</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary font-monospace">
                                                    <?php echo (int)($sa->student_count ?? 0); ?> Students
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?php echo URLROOT; ?>/attendance/student?class_id=<?php echo $sa->class_id; ?>&section_id=<?php echo $sa->section_id; ?>" class="btn btn-outline-primary" title="Student Attendance">
                                                        <i class="fa fa-user-check"></i>
                                                    </a>
                                                    <a href="<?php echo URLROOT; ?>/homework/index?class_id=<?php echo $sa->class_id; ?>&section_id=<?php echo $sa->section_id; ?>" class="btn btn-outline-secondary" title="Homework Diary">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </a>
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
        </div>

        <!-- Right Column (4 Cols) -->
        <div class="col-lg-4">
            <!-- Quick Management Shortcuts -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-bolt text-warning me-2"></i>Teacher Tools</h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/attendance/student" class="btn btn-light border d-flex align-items-center justify-content-between p-2 text-dark text-start">
                            <div>
                                <i class="fa fa-calendar-check text-primary me-2"></i>
                                <span class="fw-bold small">Take Class Attendance</span>
                            </div>
                            <i class="fa fa-chevron-right text-muted small"></i>
                        </a>
                        <a href="<?php echo URLROOT; ?>/exam/marks" class="btn btn-light border d-flex align-items-center justify-content-between p-2 text-dark text-start">
                            <div>
                                <i class="fa fa-marker text-success me-2"></i>
                                <span class="fw-bold small">Enter Exam Marks</span>
                            </div>
                            <i class="fa fa-chevron-right text-muted small"></i>
                        </a>
                        <a href="<?php echo URLROOT; ?>/homework/index" class="btn btn-light border d-flex align-items-center justify-content-between p-2 text-dark text-start">
                            <div>
                                <i class="fa fa-book-reader text-warning me-2"></i>
                                <span class="fw-bold small">Homework Diary</span>
                            </div>
                            <i class="fa fa-chevron-right text-muted small"></i>
                        </a>
                        <a href="<?php echo URLROOT; ?>/timetable/index" class="btn btn-light border d-flex align-items-center justify-content-between p-2 text-dark text-start">
                            <div>
                                <i class="fa fa-calendar-alt text-info me-2"></i>
                                <span class="fw-bold small">My Master Timetable</span>
                            </div>
                            <i class="fa fa-chevron-right text-muted small"></i>
                        </a>
                        <a href="<?php echo URLROOT; ?>/payroll/index" class="btn btn-light border d-flex align-items-center justify-content-between p-2 text-dark text-start">
                            <div>
                                <i class="fa fa-receipt text-purple me-2"></i>
                                <span class="fw-bold small">My Salary Slips</span>
                            </div>
                            <i class="fa fa-chevron-right text-muted small"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Attendance Punch History (Last 10 Logs) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-history text-secondary me-2"></i>Attendance Log</h6>
                    <small class="text-muted">Recent</small>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush small" id="attendanceLogList">
                        <?php if(empty($attLogs)): ?>
                            <li class="list-group-item py-3 text-center text-muted small">
                                No attendance entries recorded yet this month.
                            </li>
                        <?php else: ?>
                            <?php foreach(array_slice($attLogs, 0, 7) as $log): 
                                $badgeClass = 'bg-success';
                                if($log->attendance_type == 'Late') $badgeClass = 'bg-warning text-dark';
                                elseif($log->attendance_type == 'Half Day') $badgeClass = 'bg-info text-dark';
                                elseif($log->attendance_type == 'Absent') $badgeClass = 'bg-danger';
                            ?>
                                <li class="list-group-item d-flex align-items-center justify-content-between py-2">
                                    <div>
                                        <div class="fw-bold"><?php echo date('D, d M Y', strtotime($log->date)); ?></div>
                                        <small class="text-muted font-monospace" style="font-size: 0.72rem;"><?php echo htmlspecialchars($log->remark ?: 'Daily Punch'); ?></small>
                                    </div>
                                    <span class="badge <?php echo $badgeClass; ?> px-2 py-1">
                                        <?php echo htmlspecialchars($log->attendance_type); ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Staff Notices -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-bullhorn text-danger me-2"></i>Staff Notices</h6>
                    <small class="text-muted">Announcements</small>
                </div>
                <div class="card-body p-3">
                    <?php if(empty($notices)): ?>
                        <div class="text-center py-3 text-muted small">No active staff notices.</div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach(array_slice($notices, 0, 3) as $n): ?>
                                <div class="list-group-item px-0 py-2">
                                    <div class="fw-bold small text-dark"><?php echo htmlspecialchars($n->title); ?></div>
                                    <p class="text-muted mb-1 small" style="font-size: 0.75rem;"><?php echo htmlspecialchars(substr($n->message, 0, 100)); ?>...</p>
                                    <small class="text-muted font-monospace" style="font-size: 0.68rem;"><?php echo date('d M Y', strtotime($n->publish_date)); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Attendance Check-In Script -->
<script>
async function punchAttendance(status, note = '') {
    const badge = document.getElementById('todayStatusBadge');
    const text = document.getElementById('todayStatusText');
    const remark = document.getElementById('todayStatusRemark');

    const originalText = text ? text.textContent : '';
    if (text) text.textContent = 'Saving...';

    try {
        const formData = new FormData();
        formData.append('status', status);
        if (note) formData.append('note', note);
        if (window.CSRF_TOKEN) formData.append('csrf_token', window.CSRF_TOKEN);

        const response = await fetch('<?php echo URLROOT; ?>/teacher/ajaxCheckIn', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const rawText = await response.text();
        let result;
        try {
            result = JSON.parse(rawText);
        } catch(e) {
            throw new Error('Invalid server response');
        }

        if (result && result.success) {
            if (typeof window.showToast === 'function') {
                window.showToast(result.message, 'success');
            }

            // Update Badge Color & Text
            if (text) text.textContent = result.status;
            if (remark) remark.textContent = result.remark;

            if (badge) {
                badge.className = 'badge px-3 py-1 fs-6 fw-bold';
                if (result.status === 'Present') {
                    badge.classList.add('bg-success');
                    badge.innerHTML = '<i class="fa fa-check-circle me-1"></i><span>Present</span>';
                } else if (result.status === 'Late') {
                    badge.classList.add('bg-warning', 'text-dark');
                    badge.innerHTML = '<i class="fa fa-clock me-1"></i><span>Late</span>';
                } else if (result.status === 'Half Day') {
                    badge.classList.add('bg-info', 'text-dark');
                    badge.innerHTML = '<i class="fa fa-adjust me-1"></i><span>Half Day</span>';
                } else if (result.status === 'Absent') {
                    badge.classList.add('bg-danger');
                    badge.innerHTML = '<i class="fa fa-calendar-times me-1"></i><span>On Leave</span>';
                }
            }
        } else {
            throw new Error(result.message || 'Failed to record attendance');
        }
    } catch(err) {
        if (text) text.textContent = originalText;
        if (typeof window.showToast === 'function') {
            window.showToast(err.message, 'danger');
        } else {
            alert(err.message);
        }
    }
}

function showLeavePrompt() {
    const reason = prompt('Please enter reason for leave application:');
    if (reason !== null) {
        punchAttendance('Absent', reason);
    }
}
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

