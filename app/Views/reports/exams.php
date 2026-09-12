<?php require APPROOT . '/Views/layouts/header.php'; ?>

<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/reports/index">Reports Center</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Examinations &amp; Academic Performance</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-graduation-cap text-primary me-2"></i>Examination Performance Report
            </h2>
            <small class="text-muted">Analyze term assessments, subject-wise tabulations, and institutional pass/fail rates.</small>
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
            <form action="<?php echo URLROOT; ?>/reports/exams" method="get" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">Select Examination Term</label>
                    <select name="exam_id" class="form-select" required>
                        <option value="">-- Choose Exam Term --</option>
                        <?php foreach($data['exams'] as $ex): ?>
                            <option value="<?php echo $ex->id; ?>" <?php echo ($data['exam_id'] == $ex->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ex->name ?? 'Exam'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Filter by Class (Optional)</label>
                    <select name="class_id" class="form-select">
                        <option value="">-- All Classes --</option>
                        <?php foreach($data['classes'] as $cl): ?>
                            <option value="<?php echo $cl->id; ?>" <?php echo ($data['class_id'] == $cl->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cl->class_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-filter me-1"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Row -->
    <?php if(!empty($data['total_entries'])): 
        $passRate = $data['total_entries'] > 0 ? round(($data['pass_count'] / $data['total_entries']) * 100, 1) : 0;
    ?>
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Total Exam Entries</div>
                <div class="h3 fw-bold text-dark mb-0 mt-1"><?php echo $data['total_entries']; ?></div>
                <small class="text-muted">Subject papers marked</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Pass Rate</div>
                <div class="h3 fw-bold text-success mb-0 mt-1"><?php echo $passRate; ?>%</div>
                <small class="text-muted"><?php echo $data['pass_count']; ?> Passed / <?php echo $data['fail_count']; ?> Failed</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Average Score</div>
                <div class="h3 fw-bold text-primary mb-0 mt-1"><?php echo $data['class_average']; ?> / 100</div>
                <small class="text-muted">Mean marks scored</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Highest Marks</div>
                <div class="h3 fw-bold text-purple mb-0 mt-1" style="color: #9333ea;"><?php echo $data['highest_score']; ?></div>
                <small class="text-muted">Top paper score</small>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Results Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0">Tabulation &amp; Score Sheet</h5>
            <span class="badge bg-light text-dark border px-3 py-1"><?php echo count($data['results']); ?> Records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">S#</th>
                            <th>Roll #</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th class="text-center">Marks Scored</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['results'])): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa fa-clipboard-list fa-3x mb-3 text-secondary opacity-50"></i>
                                    <div>No examination marks records found for selected filters.</div>
                                </td>
                            </tr>
                        <?php else: 
                            $idx = 1;
                            foreach($data['results'] as $r): 
                                $marks = (float)$r->marks_obtained;
                                $isPass = ($marks >= 33);
                        ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?php echo $idx++; ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($r->roll_no ?? $r->admission_no ?? '-'); ?></span></td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($r->student_name); ?></td>
                                <td><?php echo htmlspecialchars($r->class_name); ?></td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1"><?php echo htmlspecialchars($r->subject_name); ?></span></td>
                                <td class="text-center fw-bold <?php echo $isPass ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $marks; ?> / 100
                                </td>
                                <td class="text-center">
                                    <?php if($isPass): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1">Pass</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1">Fail</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
