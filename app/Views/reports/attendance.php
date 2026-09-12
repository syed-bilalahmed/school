<?php require APPROOT . '/Views/layouts/header.php'; ?>

<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/reports/index">Reports Center</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Attendance Intelligence</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-calendar-check text-info me-2"></i>Daily Attendance &amp; Absentees Audit Report
            </h2>
            <small class="text-muted">Inspect morning roll-call attendance, absentees, late arrivals, and leave status.</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print();" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-print me-1"></i> Print Report
            </button>
            <a href="<?php echo URLROOT; ?>/reports/index" class="btn btn-primary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Reports Center
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="<?php echo URLROOT; ?>/reports/attendance" method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Attendance Date</label>
                    <input type="date" name="date" class="form-control" value="<?php echo $data['date']; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">-- All Classes --</option>
                        <?php foreach($data['classes'] as $c): ?>
                            <option value="<?php echo $c->id; ?>" <?php echo ($data['class_id'] == $c->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c->class_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Filter Attendance Type</label>
                    <select name="attendance_type" class="form-select">
                        <option value="All" <?php echo ($data['attendance_type'] === 'All') ? 'selected' : ''; ?>>All Records</option>
                        <option value="Present" <?php echo ($data['attendance_type'] === 'Present') ? 'selected' : ''; ?>>Present Only</option>
                        <option value="Absent" <?php echo ($data['attendance_type'] === 'Absent') ? 'selected' : ''; ?>>Absent Only</option>
                        <option value="Late" <?php echo ($data['attendance_type'] === 'Late') ? 'selected' : ''; ?>>Late Arrivals</option>
                        <option value="Half Day" <?php echo ($data['attendance_type'] === 'Half Day') ? 'selected' : ''; ?>>Half Day</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-filter me-1"></i> Audit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <?php 
        $presenceRate = $data['total_students'] > 0 ? round(($data['count_present'] / $data['total_students']) * 100, 1) : 0;
    ?>
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Presence Rate</div>
                <div class="h3 fw-bold text-success mb-0 mt-1"><?php echo $presenceRate; ?>%</div>
                <small class="text-muted"><?php echo $data['count_present']; ?> of <?php echo $data['total_students']; ?> marked</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Absentees</div>
                <div class="h3 fw-bold text-danger mb-0 mt-1"><?php echo $data['count_absent']; ?></div>
                <small class="text-muted">Requires parental follow-up</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Late Arrivals</div>
                <div class="h3 fw-bold text-warning mb-0 mt-1"><?php echo $data['count_late']; ?></div>
                <small class="text-muted">Recorded with time delay</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Leaves Granted</div>
                <div class="h3 fw-bold text-info mb-0 mt-1"><?php echo $data['count_leave']; ?></div>
                <small class="text-muted">Approved absence / sick</small>
            </div>
        </div>
    </div>

    <!-- Attendance Roster Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0">Attendance Register on <?php echo date('d M, Y', strtotime($data['date'])); ?></h5>
            <span class="badge bg-light text-dark border px-3 py-1"><?php echo count($data['attendance']); ?> Records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Roll #</th>
                            <th>Admission #</th>
                            <th>Student Name</th>
                            <th>Class &amp; Section</th>
                            <th class="text-center">Status</th>
                            <th>Remarks / Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['attendance'])): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa fa-calendar-xmark fa-3x mb-3 text-secondary opacity-50"></i>
                                    <div>No attendance records marked or found for <?php echo htmlspecialchars($data['date']); ?>.</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['attendance'] as $a): 
                                $status = strtolower($a->attendance_type);
                                $badgeClass = 'bg-secondary';
                                if ($status === 'present') $badgeClass = 'bg-success';
                                elseif ($status === 'absent') $badgeClass = 'bg-danger';
                                elseif ($status === 'late') $badgeClass = 'bg-warning text-dark';
                                elseif ($status === 'half day') $badgeClass = 'bg-info text-dark';
                            ?>
                                <tr>
                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($a->roll_no ?? '-'); ?></td>
                                    <td><span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($a->admission_no ?? '-'); ?></span></td>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($a->name); ?></td>
                                    <td><?php echo htmlspecialchars(($a->class_name ?? '') . ' (' . ($a->section_name ?? '') . ')'); ?></td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-1"><?php echo htmlspecialchars($a->attendance_type); ?></span>
                                    </td>
                                    <td class="text-muted small"><?php echo htmlspecialchars($a->remark ?? $a->remarks ?? '-'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

