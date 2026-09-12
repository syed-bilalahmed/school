<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$classId = $data['class_id'];
$sectionId = $data['section_id'];
$year = $data['year'];
$month = $data['month'];
$register = $data['register'] ?? null;
?>

<!-- Screen Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 no-print">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/attendance/student" class="text-decoration-none text-muted">Attendance</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Monthly Register Matrix</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Monthly Attendance Register Matrix</h2>
        <p class="text-muted mb-0 small">31-day academic attendance ledger, weekend tracking, and student attendance percentage compliance.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <?php if($register): ?>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm px-3 shadow-sm">
                <i class="fa fa-print me-1"></i> Print Register
            </button>
        <?php endif; ?>
        <a href="<?php echo URLROOT; ?>/attendance/student<?php echo ($classId && $sectionId) ? '?class_id=' . $classId . '&section_id=' . $sectionId : ''; ?>" class="btn btn-outline-primary btn-sm px-3">
            <i class="fa fa-clipboard-user me-1"></i> Daily Roll Call
        </a>
    </div>
</div>

<!-- Selection Filter Bar (No Print) -->
<div class="card shadow-sm border-0 mb-4 no-print">
    <div class="card-body p-3">
        <form action="<?php echo URLROOT; ?>/attendance/monthly" method="get" class="row g-2 align-items-end">
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">Class / Grade</label>
                <select name="class_id" id="classSelect" class="form-select form-select-sm" required onchange="filterSections()">
                    <option value="">Select Class</option>
                    <?php foreach($data['classes'] as $class): ?>
                        <option value="<?php echo $class->id; ?>" <?php echo ($classId == $class->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class->class_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">Section</label>
                <select name="section_id" id="sectionSelect" class="form-select form-select-sm" required>
                     <option value="">Select Section</option>
                     <?php foreach($data['sections'] as $section): ?>
                         <option value="<?php echo $section->id; ?>" data-class="<?php echo $section->class_id; ?>" class="section-option" <?php echo ($sectionId == $section->id) ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($section->section_name); ?>
                         </option>
                     <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label small text-muted mb-1">Month</label>
                <select name="month" class="form-select form-select-sm" required>
                    <?php for($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php echo ($month == $m) ? 'selected' : ''; ?>>
                            <?php echo date('F', mktime(0, 0, 0, $m, 10)); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <label class="form-label small text-muted mb-1">Year</label>
                <select name="year" class="form-select form-select-sm" required>
                    <?php for($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>>
                            <?php echo $y; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-6">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                    <i class="fa fa-table me-1"></i> Load Matrix
                </button>
            </div>
        </form>
    </div>
</div>

<?php if($register): ?>
    <!-- Printable Institutional Header -->
    <div class="print-only mb-3 text-center border-bottom border-2 border-dark pb-2 d-none">
        <h3 class="fw-bold mb-0 text-uppercase"><?php echo defined('SITENAME') ? SITENAME : 'EXCELLENCE ACADEMY'; ?></h3>
        <div class="fw-bold text-secondary small text-uppercase" style="letter-spacing: 1px;">Monthly Attendance Register & Audit Docket</div>
        <div class="small text-muted font-monospace mt-1">
            Month: <strong><?php echo $register['month_name']; ?></strong> &bull;
            Class: <strong><?php echo $register['students'][0]['name'] ? 'Selected Class' : ''; ?></strong> &bull;
            Working Days: <strong><?php echo $register['working_days']; ?></strong> &bull;
            Enrolled: <strong><?php echo $register['total_enrolled']; ?></strong>
        </div>
    </div>

    <!-- Executive Summary Tiles (No Print) -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Month & Year</div>
                <h5 class="fw-bold mb-0 text-dark"><?php echo $register['month_name']; ?></h5>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Enrolled Students</div>
                <h5 class="fw-bold mb-0 text-primary"><?php echo $register['total_enrolled']; ?></h5>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Total Working Days</div>
                <h5 class="fw-bold mb-0 text-success"><?php echo $register['working_days']; ?> Days</h5>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Calendar Days</div>
                <h5 class="fw-bold mb-0 text-secondary"><?php echo $register['days_in_month']; ?> Days</h5>
            </div>
        </div>
    </div>

    <!-- Monthly Matrix Table -->
    <div class="card shadow-sm border-0 mb-5 matrix-card">
        <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa fa-calendar-days text-primary me-2"></i><?php echo $register['month_name']; ?> &bull; Day-by-Day Roll Call Ledger
            </h6>
            <div class="small d-flex align-items-center gap-3">
                <span><span class="badge bg-success-subtle text-success border me-1">P</span> Present</span>
                <span><span class="badge bg-danger me-1">A</span> Absent</span>
                <span><span class="badge bg-warning text-dark me-1">L</span> Late</span>
                <span><span class="badge bg-info text-dark me-1">H</span> Half Day</span>
                <span><span class="badge bg-secondary me-1">Sun</span> Weekend</span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 matrix-table">
                    <thead class="bg-light text-center">
                        <tr>
                            <th style="min-width: 45px;" class="ps-2">Roll</th>
                            <th style="min-width: 150px;" class="text-start">Candidate</th>
                            <?php foreach($register['days_meta'] as $dm): 
                                $isSun = $dm['is_weekend'];
                            ?>
                                <th style="width: 26px; padding: 2px 1px;" class="<?php echo $isSun ? 'bg-secondary-subtle text-danger' : ''; ?>">
                                    <div style="font-size: 11px;"><?php echo $dm['day']; ?></div>
                                    <div style="font-size: 8px;" class="text-muted"><?php echo substr($dm['day_name'], 0, 1); ?></div>
                                </th>
                            <?php endforeach; ?>
                            <th style="min-width: 35px;" class="text-success">P</th>
                            <th style="min-width: 35px;" class="text-danger">A</th>
                            <th style="min-width: 35px;" class="text-warning">L</th>
                            <th style="min-width: 50px;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($register['students'])): ?>
                            <tr>
                                <td colspan="<?php echo 6 + $register['days_in_month']; ?>" class="text-center py-5 text-muted">
                                    No students enrolled in this class section.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($register['students'] as $st): 
                                $pct = $st['percentage'];
                                $pctBadge = ($pct >= 80) ? 'text-success fw-bold' : (($pct >= 75) ? 'text-primary' : 'text-danger fw-bold');
                            ?>
                                <tr>
                                    <td class="text-center font-monospace fw-bold ps-2 small">
                                        <?php echo htmlspecialchars($st['roll_no']); ?>
                                    </td>
                                    <td class="text-start">
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 140px; font-size: 11px;">
                                            <?php echo htmlspecialchars($st['name']); ?>
                                        </div>
                                    </td>

                                    <!-- 1..N Day Grid -->
                                    <?php for($d = 1; $d <= $register['days_in_month']; $d++): 
                                        $dm = $register['days_meta'][$d];
                                        $isSun = $dm['is_weekend'];
                                        $type = $st['days'][$d] ?? null;
                                    ?>
                                        <td class="text-center p-0 <?php echo $isSun ? 'bg-secondary-subtle' : ''; ?>" style="font-size: 10px; height: 30px;">
                                            <?php if($isSun): ?>
                                                <span class="text-muted" style="font-size: 8px;">&bull;</span>
                                            <?php elseif($type === 'Present'): ?>
                                                <span class="text-success fw-bold">P</span>
                                            <?php elseif($type === 'Absent'): ?>
                                                <span class="badge bg-danger p-0 px-1" style="font-size: 8px;">A</span>
                                            <?php elseif($type === 'Late'): ?>
                                                <span class="text-warning fw-bold">L</span>
                                            <?php elseif($type === 'Half Day'): ?>
                                                <span class="text-info fw-bold">H</span>
                                            <?php else: ?>
                                                <span class="text-muted" style="font-size: 9px;">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endfor; ?>

                                    <!-- Total Columns -->
                                    <td class="text-center font-monospace text-success fw-bold small"><?php echo $st['present']; ?></td>
                                    <td class="text-center font-monospace text-danger fw-bold small"><?php echo $st['absent']; ?></td>
                                    <td class="text-center font-monospace text-warning fw-bold small"><?php echo $st['late']; ?></td>
                                    <td class="text-center font-monospace small <?php echo $pctBadge; ?>">
                                        <?php echo $pct; ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-light text-center small fw-bold">
                        <tr>
                            <td colspan="2" class="text-end pe-2">Daily Present:</td>
                            <?php for($d = 1; $d <= $register['days_in_month']; $d++): 
                                $dm = $register['days_meta'][$d];
                                $isSun = $dm['is_weekend'];
                            ?>
                                <td class="p-0 font-monospace <?php echo $isSun ? 'bg-secondary-subtle text-muted' : 'text-primary'; ?>" style="font-size: 9px;">
                                    <?php echo $isSun ? '&bull;' : ($register['daily_presence'][$d] ?? 0); ?>
                                </td>
                            <?php endfor; ?>
                            <td colspan="4">&mdash;</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Official Certification Footer on Print -->
        <div class="print-only card-footer bg-white border-top border-dark pt-4 pb-3 d-none">
            <div class="row text-center">
                <div class="col-4">
                    <div class="border-top border-dark pt-1">
                        <strong>Class In-Charge Signature</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border-top border-dark pt-1">
                        <strong>Attendance Clerk / In-Charge</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border-top border-dark pt-1">
                        <strong>Principal Official Seal &amp; Signature</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card shadow-sm border-0 py-5 text-center text-muted">
        <div class="card-body">
            <i class="fa fa-calendar-days fa-3x mb-3 text-primary opacity-50"></i>
            <h5>Select Class, Section, Month &amp; Year to Load Monthly Register</h5>
            <p class="small mb-0">The matrix will render the complete 31-day presence breakdown, highlight Sundays, and calculate percentage compliance.</p>
        </div>
    </div>
<?php endif; ?>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    .print-only {
        display: block !important;
    }
    body {
        background: #ffffff !important;
        font-size: 9pt !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .matrix-card {
        box-shadow: none !important;
        border: none !important;
    }
    .matrix-table {
        font-size: 7.5pt !important;
        width: 100% !important;
    }
    .matrix-table th, .matrix-table td {
        padding: 1px 2px !important;
    }
}
</style>

<script>
    function filterSections(){
        var classId = document.getElementById('classSelect').value;
        var options = document.getElementsByClassName('section-option');
        for(var i=0; i<options.length; i++){
            if(!classId || options[i].getAttribute('data-class') == classId){
                options[i].style.display = '';
            } else {
                options[i].style.display = 'none';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function(){
        if(document.getElementById('classSelect') && document.getElementById('classSelect').value) {
            filterSections();
        }
    });
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
