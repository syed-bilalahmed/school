<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$examId = $data['exam_id'];
$classId = $data['class_id'];
$sectionId = $data['section_id'];
$gazette = $data['gazette'] ?? null;
$podium = $gazette ? $gazette['podium'] : [];
$summary = $gazette ? $gazette['summary'] : null;
?>

<!-- Screen Action Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 no-print">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/exam/index" class="text-decoration-none text-muted">Exams</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Result Gazette & DMC</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Official Result Gazette & Position Ledger</h2>
        <p class="text-muted mb-0 small">Automated term marks aggregation, weighted GPA, class merit positions, and Detailed Marks Certificates (DMC).</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <?php if($gazette): ?>
            <button onclick="window.print()" class="btn btn-outline-dark btn-sm px-3 shadow-sm">
                <i class="fa fa-print me-1"></i> Print Gazette
            </button>
            <a href="<?php echo URLROOT; ?>/exam/batchReportCards/<?php echo $examId; ?>?class_id=<?php echo $classId; ?>&section_id=<?php echo $sectionId; ?>" target="_blank" class="btn btn-primary btn-sm px-3 shadow-sm fw-bold">
                <i class="fa fa-id-card me-1"></i> Print All Report Cards (DMC)
            </a>
        <?php endif; ?>
        <a href="<?php echo URLROOT; ?>/exam/approval<?php echo $examId ? '?exam_id=' . $examId : ''; ?>" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-stamp me-1"></i> Approval Chain
        </a>
    </div>
</div>

<!-- Criteria Selector (No Print) -->
<div class="card shadow-sm border-0 mb-4 no-print">
    <div class="card-body p-3">
        <form action="" method="get" class="row g-2 align-items-end">
            <div class="col-md-4 col-sm-6">
                <label class="form-label small text-muted mb-1">Select Examination</label>
                <select name="exam_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php foreach($data['exams'] as $exam): ?>
                        <option value="<?php echo $exam->id; ?>" <?php echo ($examId == $exam->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($exam->name . ' (' . ($exam->session_name ?? '2026-27') . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
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
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                    <i class="fa fa-chart-line me-1"></i> Generate Gazette
                </button>
            </div>
        </form>
    </div>
</div>

<?php if($gazette): ?>
    <!-- Institutional Printable Gazette Header (Shows on Print & Screen) -->
    <div class="print-only mb-4 text-center border-bottom border-2 border-dark pb-3 d-none">
        <h2 class="fw-bold mb-0 text-uppercase"><?php echo defined('SITENAME') ? SITENAME : 'EXCELLENCE ACADEMY'; ?></h2>
        <div class="fw-bold text-secondary small text-uppercase" style="letter-spacing: 1px;">Office of the Controller of Examinations</div>
        <h4 class="fw-bold mt-2 mb-1">OFFICIAL EXAMINATION RESULT GAZETTE</h4>
        <div class="small text-muted font-monospace">
            <strong><?php echo htmlspecialchars($gazette['exam']->name); ?></strong> &bull;
            Session: <strong><?php echo htmlspecialchars($gazette['exam']->session_name ?? '2026-27'); ?></strong> &bull;
            Class: <strong><?php echo htmlspecialchars($gazette['class']->class_name . ' - ' . $gazette['class']->section_name); ?></strong> &bull;
            Date: <strong><?php echo date('d-M-Y'); ?></strong>
        </div>
    </div>

    <!-- Position Holders Podium / Honors Deck (Module 13) -->
    <?php if(!empty($podium)): ?>
    <div class="mb-4 no-print">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0">
                <i class="fa fa-trophy text-warning me-2"></i>Class Position Holders & Honours Deck
            </h5>
            <span class="badge bg-warning-subtle text-dark border fw-bold px-3 py-1 rounded-pill">
                Merit Rank 1, 2, 3
            </span>
        </div>
        <div class="row g-3">
            <?php foreach($podium as $p): 
                $rankColor = 'warning';
                $trophyIcon = 'fa-trophy';
                $badgeTitle = '1st Position';
                $cardBorder = 'border-warning';
                $bgClass = 'bg-warning-subtle';

                if($p['position'] == 2){
                    $rankColor = 'secondary';
                    $trophyIcon = 'fa-medal';
                    $badgeTitle = '2nd Position';
                    $cardBorder = 'border-secondary';
                    $bgClass = 'bg-light';
                } elseif($p['position'] == 3){
                    $rankColor = 'danger';
                    $trophyIcon = 'fa-award';
                    $badgeTitle = '3rd Position';
                    $cardBorder = 'border-danger';
                    $bgClass = 'bg-danger-subtle';
                }
            ?>
                <div class="col-md-4">
                    <div class="card shadow-sm border-2 <?php echo $cardBorder; ?> h-100 position-relative overflow-hidden">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-<?php echo $rankColor; ?> text-white fw-bold px-3 py-1 rounded-pill fs-6">
                                    <i class="fa <?php echo $trophyIcon; ?> me-1"></i> <?php echo $badgeTitle; ?>
                                </span>
                                <span class="badge bg-light text-dark border font-monospace">
                                    Roll # <?php echo htmlspecialchars($p['roll_no']); ?>
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-3 my-2">
                                <div class="p-2 <?php echo $bgClass; ?> rounded-circle text-center" style="width: 48px; height: 48px; line-height: 32px;">
                                    <i class="fa fa-user-graduate fs-4 text-<?php echo $rankColor; ?>"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate"><?php echo htmlspecialchars($p['name']); ?></h6>
                                    <div class="small text-muted text-truncate">S/O: <?php echo htmlspecialchars($p['father_name'] ?: 'N/A'); ?></div>
                                </div>
                            </div>

                            <div class="p-2 bg-light rounded-3 d-flex justify-content-around text-center mt-2 small">
                                <div>
                                    <span class="text-muted d-block" style="font-size: 11px;">Score</span>
                                    <strong class="text-dark font-monospace"><?php echo $p['total_obtained']; ?> / <?php echo $p['total_full_marks']; ?></strong>
                                </div>
                                <div class="border-start"></div>
                                <div>
                                    <span class="text-muted d-block" style="font-size: 11px;">Percentage</span>
                                    <strong class="text-primary font-monospace"><?php echo $p['percentage']; ?>%</strong>
                                </div>
                                <div class="border-start"></div>
                                <div>
                                    <span class="text-muted d-block" style="font-size: 11px;">Grade</span>
                                    <span class="badge <?php echo $p['grade_badge']; ?> px-2"><?php echo $p['grade']; ?></span>
                                </div>
                            </div>

                            <div class="mt-3 text-end">
                                <a href="<?php echo URLROOT; ?>/exam/reportCard/<?php echo $examId; ?>/<?php echo $p['student_id']; ?>" target="_blank" class="btn btn-sm btn-outline-dark w-100 fw-bold">
                                    <i class="fa fa-id-card me-1"></i> View Detailed DMC
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Class Statistics Summary Bar (No Print) -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Total Enrolled</div>
                <h4 class="fw-bold mb-0 text-dark"><?php echo $summary['total_enrolled']; ?></h4>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Appeared</div>
                <h4 class="fw-bold mb-0 text-secondary"><?php echo $summary['total_appeared']; ?></h4>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Passed</div>
                <h4 class="fw-bold mb-0 text-success"><?php echo $summary['total_passed']; ?></h4>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Failed</div>
                <h4 class="fw-bold mb-0 text-danger"><?php echo $summary['total_failed']; ?></h4>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Pass Rate</div>
                <h4 class="fw-bold mb-0 text-primary"><?php echo $summary['pass_percentage']; ?>%</h4>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Class Average</div>
                <h4 class="fw-bold mb-0 text-dark font-monospace"><?php echo $summary['class_average']; ?></h4>
            </div>
        </div>
    </div>

    <!-- Official Gazette Matrix Table -->
    <div class="card shadow-sm border-0 mb-5 gazette-card">
        <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-scroll text-primary me-2"></i>Class Result Gazette Roster
                </h5>
                <small class="text-muted">
                    Class: <strong><?php echo htmlspecialchars($gazette['class']->class_name . ' - ' . $gazette['class']->section_name); ?></strong> &bull;
                    Exam: <strong><?php echo htmlspecialchars($gazette['exam']->name); ?></strong> &bull;
                    Class Teacher: <strong><?php echo htmlspecialchars($gazette['class']->class_teacher_name ?: 'Not Assigned'); ?></strong>
                </small>
            </div>
            <div class="no-print">
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 rounded-pill">
                    <?php echo count($gazette['students']); ?> Students Tabulated
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 small gazette-table">
                    <thead class="bg-light text-center">
                        <tr>
                            <th style="width: 55px;">Roll</th>
                            <th style="width: 85px;">Adm No</th>
                            <th class="text-start" style="min-width: 170px;">Candidate Details</th>
                            <?php foreach($gazette['schedules'] as $sch): ?>
                                <th style="min-width: 90px;">
                                    <div><?php echo htmlspecialchars($sch->subject_name); ?></div>
                                    <small class="text-muted font-monospace">(<?php echo $sch->full_marks; ?>)</small>
                                </th>
                            <?php endforeach; ?>
                            <th style="width: 95px;">Total (<?php echo $gazette['total_full_marks']; ?>)</th>
                            <th style="width: 65px;">%</th>
                            <th style="width: 65px;">Grade</th>
                            <th style="width: 55px;">GPA</th>
                            <th style="width: 90px;">Position</th>
                            <th style="width: 75px;">Status</th>
                            <th class="no-print" style="width: 90px;">DMC</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($gazette['students'])): ?>
                            <tr>
                                <td colspan="<?php echo 10 + count($gazette['schedules']); ?>" class="text-center py-5 text-muted">
                                    No students enrolled in this class and section.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($gazette['students'] as $st): 
                                $isPass = ($st['status'] === 'PASS');
                            ?>
                                <tr class="<?php echo !$isPass ? 'table-danger-subtle' : ''; ?>">
                                    <td class="text-center fw-bold font-monospace">
                                        <?php echo htmlspecialchars($st['roll_no']); ?>
                                    </td>
                                    <td class="text-center font-monospace text-muted small">
                                        <?php echo htmlspecialchars($st['admission_no']); ?>
                                    </td>
                                    <td class="text-start">
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($st['name']); ?></div>
                                        <div class="text-muted small" style="font-size: 11px;">S/O: <?php echo htmlspecialchars($st['father_name'] ?: 'N/A'); ?></div>
                                    </td>

                                    <!-- Subject Marks Columns -->
                                    <?php foreach($gazette['schedules'] as $sch): 
                                        $sub = $st['subjects'][$sch->id] ?? null;
                                    ?>
                                        <td class="text-center font-monospace <?php echo ($sub && $sub['is_failed']) ? 'text-danger fw-bold bg-danger-subtle' : ''; ?>">
                                            <?php if(!$sub): ?>
                                                <span class="text-muted">&mdash;</span>
                                            <?php elseif($sub['is_absent']): ?>
                                                <span class="badge bg-danger">ABS</span>
                                            <?php else: ?>
                                                <span><?php echo $sub['obtained_marks']; ?></span>
                                                <small class="d-block text-muted" style="font-size: 9px;">(<?php echo $sub['grade']; ?>)</small>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>

                                    <!-- Total Marks -->
                                    <td class="text-center fw-bold font-monospace fs-6 text-dark">
                                        <?php echo $st['total_obtained']; ?>
                                    </td>

                                    <!-- Percentage -->
                                    <td class="text-center font-monospace">
                                        <?php echo $st['percentage']; ?>%
                                    </td>

                                    <!-- Grade -->
                                    <td class="text-center">
                                        <span class="badge <?php echo $st['grade_badge']; ?> px-2 py-1">
                                            <?php echo $st['grade']; ?>
                                        </span>
                                    </td>

                                    <!-- GPA -->
                                    <td class="text-center font-monospace fw-bold">
                                        <?php echo number_format($st['gpa'], 1); ?>
                                    </td>

                                    <!-- Class Position -->
                                    <td class="text-center">
                                        <?php if($st['position'] == 1): ?>
                                            <span class="badge bg-warning text-dark fw-bold">🥇 1st</span>
                                        <?php elseif($st['position'] == 2): ?>
                                            <span class="badge bg-secondary text-white fw-bold">🥈 2nd</span>
                                        <?php elseif($st['position'] == 3): ?>
                                            <span class="badge bg-danger text-white fw-bold">🥉 3rd</span>
                                        <?php elseif($st['position']): ?>
                                            <span class="badge bg-light text-dark border"><?php echo $st['position']; ?>th</span>
                                        <?php else: ?>
                                            <span class="text-muted">&mdash;</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Status -->
                                    <td class="text-center">
                                        <?php if($isPass): ?>
                                            <span class="badge bg-success">PASS</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">FAIL</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Action (DMC) -->
                                    <td class="text-center no-print">
                                        <a href="<?php echo URLROOT; ?>/exam/reportCard/<?php echo $examId; ?>/<?php echo $st['student_id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary px-2 py-0" title="Print Detailed DMC">
                                            <i class="fa fa-print"></i> DMC
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Official Institutional Signatures on Printed Gazette -->
        <div class="print-only card-footer bg-white border-top border-dark pt-4 pb-3 d-none">
            <div class="row text-center">
                <div class="col-3">
                    <div class="border-top border-dark pt-1">
                        <strong>Tabulator / Clerk</strong>
                    </div>
                </div>
                <div class="col-3">
                    <div class="border-top border-dark pt-1">
                        <strong>Class Teacher In-Charge</strong>
                    </div>
                </div>
                <div class="col-3">
                    <div class="border-top border-dark pt-1">
                        <strong>Controller of Examinations</strong>
                    </div>
                </div>
                <div class="col-3">
                    <div class="border-top border-dark pt-1">
                        <strong>Principal Official Seal</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card shadow-sm border-0 py-5 text-center text-muted">
        <div class="card-body">
            <i class="fa fa-award fa-3x mb-3 text-primary opacity-50"></i>
            <h5>Select Examination, Class & Section to Load Result Gazette</h5>
            <p class="small mb-0">The automated engine will compile all student subject scores, compute GPA, rank positions, and display the official gazette roster.</p>
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
        font-size: 10pt !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .gazette-card {
        box-shadow: none !important;
        border: none !important;
    }
    .gazette-table {
        font-size: 8.5pt !important;
        width: 100% !important;
    }
    .gazette-table th, .gazette-table td {
        padding: 4px 6px !important;
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
