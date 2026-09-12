<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$children = $data['children'] ?? [];
$activeChild = $data['active_child'] ?? null;
$dossier = $data['dossier'] ?? null;
$todayAtt = $data['today_attendance'] ?? null;
$homework = $data['homework'] ?? [];
$schedule = $data['schedule'] ?? [];

$attSummary = $dossier['attendance']['summary'] ?? null;
$attPercent = $dossier['attendance']['percentage'] ?? 100;
$finance = $dossier['finance'] ?? null;
$balance = $finance['balance'] ?? 0;
$exams = $dossier['exams'] ?? [];
?>

<!-- Portal Welcome Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Portal</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Parent &amp; Family Hub</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Parent &amp; Family Portal</h2>
        <p class="text-muted mb-0 small">Real-time gate attendance alerts, monthly tuition fee challans, homework diaries, and official DMC progress reports.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="fa fa-user-shield text-primary me-1"></i>
            Logged in: <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Parent Account'); ?></strong>
        </span>
    </div>
</div>

<?php if(empty($children)): ?>
    <div class="card shadow-sm border-0 py-5 text-center text-muted">
        <div class="card-body">
            <i class="fa fa-people-roof fa-4x text-secondary opacity-50 mb-3"></i>
            <h5>No Enrolled Students Linked to this Account</h5>
            <p class="small text-muted mb-0" style="max-width: 460px; margin: 0 auto;">
                Please contact the school administrative office to link your children's admission records and NADRA B-Form to your parent phone or login credentials.
            </p>
        </div>
    </div>
<?php else: ?>
    <!-- Multi-Child Sibling Switcher Bar -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <span class="small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                    <i class="fa fa-child-reaching text-primary me-1"></i> Your Enrolled Children (<?php echo count($children); ?>):
                </span>
                <span class="small text-muted">Select child to view personal records &amp; challans</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach($children as $ch): 
                    $isSelected = ($activeChild && $activeChild->id == $ch->id);
                ?>
                    <a href="<?php echo URLROOT; ?>/parent/index?student_id=<?php echo $ch->id; ?>" 
                       class="btn btn-sm d-flex align-items-center gap-2 p-2 px-3 rounded-pill <?php echo $isSelected ? 'btn-primary shadow-sm' : 'btn-outline-secondary'; ?>">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($ch->name); ?>&size=32&background=random" class="rounded-circle" width="24" height="24" alt="">
                        <span class="fw-bold"><?php echo htmlspecialchars($ch->name); ?></span>
                        <span class="badge <?php echo $isSelected ? 'bg-white text-primary' : 'bg-light text-dark border'; ?> font-monospace" style="font-size: 10px;">
                            <?php echo htmlspecialchars($ch->class_name . ' - ' . $ch->section_name); ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php if($activeChild && $dossier): 
        $stObj = $dossier['student'];
    ?>
        <!-- Active Child Hero Banner -->
        <div class="card shadow-sm border-0 mb-4 overflow-hidden">
            <div class="card-body p-4 bg-light-subtle border-bottom">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($stObj->name); ?>&size=80&background=4f46e5&color=fff" class="rounded-circle border border-3 border-white shadow-sm" width="70" height="70" alt="">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($stObj->name); ?></h4>
                                <span class="badge bg-success-subtle text-success border font-monospace">Active Student</span>
                            </div>
                            <div class="small text-muted d-flex flex-wrap align-items-center gap-3">
                                <span><i class="fa fa-layer-group text-secondary me-1"></i> Class: <strong><?php echo htmlspecialchars($stObj->class_name . ' - ' . $stObj->section_name); ?></strong></span>
                                <span><i class="fa fa-id-badge text-secondary me-1"></i> Roll #: <strong><?php echo htmlspecialchars($stObj->roll_no ?: '-'); ?></strong></span>
                                <span><i class="fa fa-fingerprint text-secondary me-1"></i> B-Form: <strong><?php echo htmlspecialchars($stObj->bform_cnic ?: 'N/A'); ?></strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <?php if((float)($stObj->sibling_discount_percent ?? 0) > 0): ?>
                            <span class="badge bg-purple-subtle text-purple border px-3 py-2 fs-6 rounded-pill" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.1);">
                                <i class="fa fa-tag me-1"></i> <?php echo (float)$stObj->sibling_discount_percent; ?>% Sibling Relief Applied
                            </span>
                        <?php endif; ?>
                        <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $stObj->id; ?>" class="btn btn-sm btn-outline-dark px-3 fw-bold">
                            <i class="fa fa-id-card me-1"></i> Full Dossier
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 High-Impact KPI Metric Tiles -->
        <div class="row g-3 mb-4">
            <!-- Tile 1: Today's Gate Status -->
            <div class="col-lg-3 col-sm-6">
                <div class="card shadow-sm border-0 h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted fw-bold text-uppercase">Gate Attendance Today</span>
                        <i class="fa fa-door-open text-primary fs-5"></i>
                    </div>
                    <?php if(!$todayAtt): ?>
                        <h5 class="fw-bold mb-0 text-muted">Roll Call Pending</h5>
                        <small class="text-muted" style="font-size: 11px;">Gate punch / register in progress</small>
                    <?php elseif($todayAtt->attendance_type === 'Present'): ?>
                        <h5 class="fw-bold mb-0 text-success">Present In School</h5>
                        <small class="text-muted font-monospace" style="font-size: 11px;">Arrival: <?php echo !empty($todayAtt->entry_time) ? date('h:i A', strtotime($todayAtt->entry_time)) : '08:00 AM'; ?></small>
                    <?php elseif($todayAtt->attendance_type === 'Late'): ?>
                        <h5 class="fw-bold mb-0 text-warning">Late Arrival</h5>
                        <small class="text-muted font-monospace" style="font-size: 11px;">Arrived at: <?php echo !empty($todayAtt->entry_time) ? date('h:i A', strtotime($todayAtt->entry_time)) : 'Late'; ?></small>
                    <?php elseif($todayAtt->attendance_type === 'Absent'): ?>
                        <h5 class="fw-bold mb-0 text-danger">Marked Absent</h5>
                        <small class="text-danger" style="font-size: 11px;">Please notify office if sick</small>
                    <?php else: ?>
                        <h5 class="fw-bold mb-0 text-info">Half Day</h5>
                        <small class="text-muted" style="font-size: 11px;">Authorized early leave</small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tile 2: Overall Attendance Ratio -->
            <div class="col-lg-3 col-sm-6">
                <div class="card shadow-sm border-0 h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted fw-bold text-uppercase">Attendance Ratio</span>
                        <i class="fa fa-calendar-check text-success fs-5"></i>
                    </div>
                    <h5 class="fw-bold mb-0 <?php echo $attPercent >= 75 ? 'text-success' : 'text-danger'; ?>">
                        <?php echo $attPercent; ?>%
                    </h5>
                    <small class="text-muted" style="font-size: 11px;">
                        <?php echo $attSummary ? $attSummary->present_days : 0; ?> of <?php echo $attSummary ? $attSummary->total_days : 0; ?> School Days
                    </small>
                </div>
            </div>

            <!-- Tile 3: Fee Dues Balance -->
            <div class="col-lg-3 col-sm-6">
                <div class="card shadow-sm border-0 h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted fw-bold text-uppercase">Fee Due Balance</span>
                        <i class="fa fa-file-invoice-dollar text-warning fs-5"></i>
                    </div>
                    <?php if($balance <= 0): ?>
                        <h5 class="fw-bold mb-0 text-success">Rs. 0</h5>
                        <small class="text-success" style="font-size: 11px;"><i class="fa fa-check-circle me-1"></i>All vouchers cleared</small>
                    <?php else: ?>
                        <h5 class="fw-bold mb-0 text-danger">Rs. <?php echo number_format($balance); ?></h5>
                        <small class="text-danger" style="font-size: 11px;">Pending fee invoice</small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tile 4: Academic Progress -->
            <div class="col-lg-3 col-sm-6">
                <div class="card shadow-sm border-0 h-100 p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-muted fw-bold text-uppercase">Academic Standing</span>
                        <i class="fa fa-award text-primary fs-5"></i>
                    </div>
                    <?php if(!empty($exams)): 
                        $latestExam = $exams[0];
                    ?>
                        <h5 class="fw-bold mb-0 text-primary"><?php echo htmlspecialchars($latestExam->exam_name); ?></h5>
                        <small class="text-muted" style="font-size: 11px;">Score: <strong><?php echo (float)$latestExam->marks_obtained; ?> / <?php echo (float)$latestExam->full_marks; ?></strong></small>
                    <?php else: ?>
                        <h5 class="fw-bold mb-0 text-muted">Term Exam Ahead</h5>
                        <small class="text-muted" style="font-size: 11px;">No published exams yet</small>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 4 Interactive Portal Tabs -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-header bg-white border-0 border-bottom p-0">
                <ul class="nav nav-tabs card-header-tabs m-0 border-0" id="parentTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active py-3 px-4 fw-bold border-0" id="att-tab" data-bs-toggle="tab" data-bs-target="#tab-p-att" type="button" role="tab">
                            <i class="fa fa-calendar-check text-primary me-2"></i>Gate Attendance Log
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 px-4 fw-bold text-dark border-0" id="fee-tab" data-bs-toggle="tab" data-bs-target="#tab-p-fee" type="button" role="tab">
                            <i class="fa fa-receipt text-success me-2"></i>Fee Challans &amp; Ledger
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 px-4 fw-bold text-dark border-0" id="exam-tab" data-bs-toggle="tab" data-bs-target="#tab-p-exam" type="button" role="tab">
                            <i class="fa fa-id-card text-warning me-2"></i>Exam Results &amp; DMC
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-3 px-4 fw-bold text-dark border-0" id="hw-tab" data-bs-toggle="tab" data-bs-target="#tab-p-hw" type="button" role="tab">
                            <i class="fa fa-book-reader text-info me-2"></i>Homework &amp; Diary
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content">
                    <!-- Tab 1: Attendance Log -->
                    <div class="tab-pane fade show active" id="tab-p-att" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0">Recent Daily Attendance Records</h6>
                            <span class="badge bg-light text-dark border font-monospace">Last 15 School Days</span>
                        </div>
                        <?php if(empty($dossier['attendance']['logs'])): ?>
                            <div class="text-center py-4 text-muted small bg-light rounded-3">
                                No attendance entries recorded for this term yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border small mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Day</th>
                                            <th>Status</th>
                                            <th>Arrival Time</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($dossier['attendance']['logs'] as $log): 
                                            $badgeClass = 'bg-success';
                                            if($log->attendance_type === 'Absent') $badgeClass = 'bg-danger';
                                            elseif($log->attendance_type === 'Late') $badgeClass = 'bg-warning text-dark';
                                            elseif($log->attendance_type === 'Half Day') $badgeClass = 'bg-info';
                                        ?>
                                            <tr>
                                                <td class="fw-bold font-monospace"><?php echo date('d M, Y', strtotime($log->date)); ?></td>
                                                <td class="text-muted"><?php echo date('l', strtotime($log->date)); ?></td>
                                                <td><span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($log->attendance_type); ?></span></td>
                                                <td class="font-monospace text-muted"><?php echo !empty($log->entry_time) ? date('h:i A', strtotime($log->entry_time)) : '08:00 AM'; ?></td>
                                                <td class="text-muted"><?php echo htmlspecialchars($log->remark ?: '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 2: Fee Challans -->
                    <div class="tab-pane fade" id="tab-p-fee" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0">Assigned Invoices &amp; Payment History</h6>
                            <div class="d-flex gap-2">
                                <a href="<?php echo URLROOT; ?>/fees/challan/<?php echo $activeChild->id; ?>" target="_blank" class="btn btn-sm btn-primary fw-bold px-3">
                                    <i class="fa fa-print me-1"></i> Print 3-Copy Bank Challan
                                </a>
                                <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-sm btn-outline-success fw-bold px-3">
                                    <i class="fa fa-receipt me-1"></i> Open Fee Ledger
                                </a>
                            </div>
                        </div>
                        <?php if(empty($finance['fees'])): ?>
                            <div class="text-center py-4 text-muted small bg-light rounded-3">
                                No fee invoices currently assigned.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border small mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Fee Description</th>
                                            <th>Due Date</th>
                                            <th>Amount (Rs.)</th>
                                            <th>Paid (Rs.)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($finance['fees'] as $fee): 
                                            $isPaid = (float)$fee->total_paid >= (float)$fee->amount;
                                        ?>
                                            <tr>
                                                <td class="fw-bold text-dark">
                                                    <?php echo htmlspecialchars($fee->group_name . ' - ' . $fee->type_name); ?>
                                                </td>
                                                <td class="font-monospace text-muted"><?php echo date('d M, Y', strtotime($fee->due_date)); ?></td>
                                                <td class="fw-bold font-monospace">Rs. <?php echo number_format((float)$fee->amount); ?></td>
                                                <td class="text-success font-monospace fw-bold">Rs. <?php echo number_format((float)$fee->total_paid); ?></td>
                                                <td>
                                                    <?php if($isPaid): ?>
                                                        <span class="badge bg-success">Paid</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab 3: Exam Results & DMC -->
                    <div class="tab-pane fade" id="tab-p-exam" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0">Examination Results &amp; Detailed Progress Reports</h6>
                        </div>
                        <?php if(empty($exams)): ?>
                            <div class="text-center py-4 text-muted small bg-light rounded-3">
                                No examination results available for this term yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border small mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Examination</th>
                                            <th>Subject</th>
                                            <th>Marks Obtained</th>
                                            <th>Full Marks</th>
                                            <th>Status</th>
                                            <th class="text-end pe-3">Detailed DMC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($exams as $ex): 
                                            $pass = (float)$ex->marks_obtained >= (float)$ex->passing_marks;
                                        ?>
                                            <tr>
                                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($ex->exam_name); ?></td>
                                                <td><?php echo htmlspecialchars($ex->subject_name); ?></td>
                                                <td class="font-monospace fw-bold text-primary"><?php echo (float)$ex->marks_obtained; ?></td>
                                                <td class="font-monospace text-muted"><?php echo (float)$ex->full_marks; ?></td>
                                                <td>
                                                    <span class="badge <?php echo $pass ? 'bg-success' : 'bg-danger'; ?>"><?php echo $pass ? 'Pass' : 'Fail'; ?></span>
                                                </td>
                                                <td class="text-end pe-3">
                                                    <?php if(!empty($ex->exam_id)): ?>
                                                        <a href="<?php echo URLROOT; ?>/exam/reportCard/<?php echo $ex->exam_id; ?>/<?php echo $activeChild->id; ?>" target="_blank" class="btn btn-sm btn-outline-primary px-3 py-1 fw-bold">
                                                            <i class="fa fa-id-card me-1"></i> View Official DMC
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

                    <!-- Tab 4: Homework Diary -->
                    <div class="tab-pane fade" id="tab-p-hw" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold text-dark mb-0">Class Homework &amp; Diary Assignments</h6>
                            <span class="badge bg-info-subtle text-info border font-monospace">Active Class Section</span>
                        </div>
                        <?php if(empty($homework)): ?>
                            <div class="text-center py-4 text-muted small bg-light rounded-3">
                                <i class="fa fa-book-open me-1"></i> No homework tasks assigned for today.
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach($homework as $hw): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded-3 bg-light h-100">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="badge bg-primary"><?php echo htmlspecialchars($hw->subject_name ?? 'Subject'); ?></span>
                                                <span class="small text-muted font-monospace">Due: <?php echo !empty($hw->submission_date) ? date('d M, Y', strtotime($hw->submission_date)) : 'Tomorrow'; ?></span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($hw->title ?? 'Daily Assignment'); ?></h6>
                                            <p class="small text-muted mb-0"><?php echo htmlspecialchars($hw->description ?? ''); ?></p>
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
<?php endif; ?>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
