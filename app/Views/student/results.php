<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$student = $data['student'] ?? null;
$results = $data['results'] ?? [];
$exams = $data['exams'] ?? [];

$totalObtained = 0;
$totalFull = 0;
$passedCount = 0;
$totalSubjects = count($results);

foreach($results as $r){
    if(($r->is_absent ?? 'no') !== 'yes'){
        $totalObtained += (float)($r->get_marks ?? 0);
        if((float)$r->get_marks >= (float)$r->passing_marks){
            $passedCount++;
        }
    }
    $totalFull += (float)($r->full_marks ?? 100);
}

$overallPct = ($totalFull > 0) ? round(($totalObtained / $totalFull) * 100, 1) : 0;
?>

<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/student/index" class="text-decoration-none text-muted">Portal</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Examination DMC &amp; Progress</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-award text-primary me-2"></i>My Examination Results &amp; Academic Progress
            </h2>
            <small class="text-muted">Subject-wise marks obtained, full marks, pass benchmarks, and official progress reports.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/student/index" class="btn btn-outline-secondary btn-sm px-3 shadow-xs">
                <i class="fa fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Student Info & Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($student->name ?? 'Student'); ?>&size=60&background=4f46e5&color=fff&bold=true" class="rounded-circle shadow-xs" width="54" height="54" alt="">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($student->name ?? ''); ?></h6>
                        <span class="text-muted small">
                            Class: <strong><?php echo htmlspecialchars(($student->class_name ?? '') . ' (' . ($student->section_name ?? '') . ')'); ?></strong>
                        </span>
                        <div class="small text-muted">
                            Roll #: <strong><?php echo htmlspecialchars($student->roll_no ?? '-'); ?></strong> | Adm: <strong><?php echo htmlspecialchars($student->admission_no ?? '-'); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="row g-2 h-100">
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center h-100">
                        <span class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Marks</span>
                        <h4 class="fw-bold mb-0 text-dark mt-1"><?php echo $totalObtained; ?> <small class="text-muted fs-6">/ <?php echo $totalFull; ?></small></h4>
                        <span class="small text-muted" style="font-size: 0.75rem;">Aggregated Score</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center h-100">
                        <span class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Overall Percentage</span>
                        <h4 class="fw-bold mb-0 text-primary mt-1"><?php echo $overallPct; ?>%</h4>
                        <span class="small text-muted" style="font-size: 0.75rem;">Overall Grade: <strong><?php echo ($overallPct >= 80 ? 'A+' : ($overallPct >= 70 ? 'A' : ($overallPct >= 60 ? 'B' : ($overallPct >= 50 ? 'C' : ($overallPct >= 33 ? 'D' : 'F'))))); ?></strong></span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white text-center h-100">
                        <span class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Subjects Passed</span>
                        <h4 class="fw-bold mb-0 text-success mt-1"><?php echo $passedCount; ?> <small class="text-muted fs-6">/ <?php echo $totalSubjects; ?></small></h4>
                        <span class="small text-muted" style="font-size: 0.75rem;">Clearance Status</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-5">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa fa-list-check text-primary me-2"></i>Subject-Wise Examination Transcript
            </h6>
            <span class="badge bg-light text-dark border font-monospace">
                <?php echo count($results); ?> Papers Evaluated
            </span>
        </div>
        <div class="card-body p-0">
            <?php if(empty($results)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa fa-award fa-3x text-secondary opacity-50 mb-3"></i>
                    <h5>No Examination Results Found</h5>
                    <p class="small text-muted mb-0">Examination results have not yet been published or verified for this student record.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Examination</th>
                                <th>Subject</th>
                                <th>Marks Obtained</th>
                                <th>Full Marks</th>
                                <th>Pass Benchmark</th>
                                <th>Percentage</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Official DMC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($results as $res): 
                                $isAbsent = ($res->is_absent ?? 'no') === 'yes';
                                $marks = $isAbsent ? 0 : (float)$res->get_marks;
                                $full = (float)$res->full_marks;
                                $pass = (float)$res->passing_marks;
                                $pct = ($full > 0) ? round(($marks / $full) * 100, 1) : 0;
                                $isPass = (!$isAbsent && $marks >= $pass);
                            ?>
                                <tr class="<?php echo $isAbsent ? 'table-danger bg-opacity-25' : ''; ?>">
                                    <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($res->exam_name); ?></td>
                                    <td class="fw-semibold text-primary"><?php echo htmlspecialchars($res->subject_name); ?></td>
                                    <td class="fw-bold font-monospace <?php echo $isPass ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo $isAbsent ? 'Absent' : $marks; ?>
                                    </td>
                                    <td class="font-monospace text-muted"><?php echo $full; ?></td>
                                    <td class="font-monospace text-muted"><?php echo $pass; ?></td>
                                    <td class="font-monospace fw-semibold"><?php echo $isAbsent ? '-' : ($pct . '%'); ?></td>
                                    <td>
                                        <?php if($isAbsent): ?>
                                            <span class="badge bg-danger text-white font-monospace">Absent</span>
                                        <?php elseif($isPass): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">Pass</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace">Fail</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if(!empty($res->exam_id)): ?>
                                            <a href="<?php echo URLROOT; ?>/exam/reportCard/<?php echo $res->exam_id; ?>/<?php echo $student->id; ?>" target="_blank" class="btn btn-sm btn-outline-primary px-3 shadow-xs">
                                                <i class="fa fa-print me-1"></i> Print DMC
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
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

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
