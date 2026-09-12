<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card (DMC) - <?php echo htmlspecialchars($data['student']->name); ?> - <?php echo htmlspecialchars($data['exam']->name); ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;800&family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            padding: 30px 15px;
        }
        .dmc-container {
            max-width: 960px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 12px;
            box-shadow: 0 10px 25px -3px rgba(15, 23, 42, 0.1);
            padding: 40px;
            position: relative;
        }
        .dmc-border-inner {
            border: 1px solid #cbd5e1;
            padding: 25px;
            border-radius: 8px;
        }
        .school-title {
            font-family: 'Cinzel', serif;
            font-weight: 800;
            color: #1e1b4b;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .dmc-badge {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            background: #0f172a;
            color: #ffffff;
            letter-spacing: 1.5px;
            padding: 6px 24px;
            border-radius: 30px;
            display: inline-block;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        .info-label {
            font-size: 0.78rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }
        .info-val {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.92rem;
        }
        .table-marks th {
            background: #f8fafc !important;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .table-marks td {
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            font-size: 0.88rem;
        }
        .table-scale {
            font-size: 0.75rem;
            border: 1px solid #cbd5e1;
        }
        .table-scale th, .table-scale td {
            padding: 3px 6px;
            border: 1px solid #cbd5e1;
        }
        .seal-box {
            border: 2px dashed #94a3b8;
            border-radius: 8px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 10pt !important;
            }
            .no-print {
                display: none !important;
            }
            .dmc-container {
                max-width: 100% !important;
                border: 2px solid #000 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 15px !important;
            }
            .dmc-border-inner {
                border: 1px solid #000 !important;
                padding: 15px !important;
            }
            .table-marks th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .dmc-badge {
                background: #000 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

<?php
$st = $data['student'];
$exam = $data['exam'];
$cls = $data['class'];
$perf = $data['performance'];
$att = $data['attendance'];
$summary = $data['class_summary'];
$isPass = ($perf['status'] === 'PASS');
$position = $perf['position'] ?? null;
?>

<!-- Screen Action Bar -->
<div class="container no-print mb-4" style="max-width: 960px;">
    <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 shadow-sm border">
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/exam/gazette?exam_id=<?php echo $exam->id; ?>&class_id=<?php echo $st->class_id; ?>&section_id=<?php echo $st->section_id; ?>" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fa fa-arrow-left me-1"></i> Back to Gazette
            </a>
            <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $st->id; ?>" class="btn btn-outline-dark btn-sm px-3">
                <i class="fa fa-user-graduate me-1"></i> Student 360° Profile
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-primary px-4 fw-bold shadow-sm">
                <i class="fa fa-print me-2"></i> Print Report Card (DMC)
            </button>
        </div>
    </div>
</div>

<!-- Main DMC Document -->
<div class="dmc-container">
    <div class="dmc-border-inner">
        <!-- School Crest & Header -->
        <div class="text-center mb-3">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                <i class="fa fa-graduation-cap fa-2x text-dark"></i>
                <h2 class="school-title mb-0"><?php echo defined('SITENAME') ? SITENAME : 'EXCELLENCE ACADEMY'; ?></h2>
            </div>
            <div class="text-uppercase fw-bold text-secondary small" style="letter-spacing: 1px;">
                Board of Secondary & Intermediate Education Registered &bull; Quality Academic Standards
            </div>
            <div class="my-2">
                <div class="dmc-badge">
                    Detailed Marks Certificate &bull; Progress Report
                </div>
            </div>
            <div class="small text-muted font-monospace">
                Examination: <strong><?php echo htmlspecialchars($exam->name); ?></strong> &bull;
                Academic Session: <strong><?php echo htmlspecialchars($exam->session_name ?? '2026-27'); ?></strong> &bull;
                Certificate Ref: <strong>DMC-<?php echo str_pad($st->id, 5, '0', STR_PAD_LEFT); ?>-<?php echo $exam->id; ?></strong>
            </div>
        </div>

        <hr class="my-3 border-dark">

        <!-- Student Particulars Grid -->
        <div class="row g-2 mb-3 bg-light p-3 rounded-3 border">
            <div class="col-md-3 col-6">
                <div class="info-label">Student Name</div>
                <div class="info-val text-primary text-uppercase"><?php echo htmlspecialchars($st->name); ?></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-label">Father's Name</div>
                <div class="info-val"><?php echo htmlspecialchars($st->father_name ?: 'N/A'); ?></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-label">Roll Number</div>
                <div class="info-val font-monospace fs-6"><?php echo htmlspecialchars($st->roll_no ?: 'N/A'); ?></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-label">Admission / Reg No</div>
                <div class="info-val font-monospace"><?php echo htmlspecialchars($st->admission_no ?: 'ADM-' . $st->id); ?></div>
            </div>

            <div class="col-md-3 col-6">
                <div class="info-label">Class &amp; Section</div>
                <div class="info-val"><?php echo htmlspecialchars($st->class_name . ' - ' . $st->section_name); ?></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-label">B-Form / CNIC</div>
                <div class="info-val font-monospace"><?php echo htmlspecialchars($st->bform_cnic ?: '&mdash;'); ?></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-label">Class Teacher In-Charge</div>
                <div class="info-val"><?php echo htmlspecialchars($st->class_teacher_name ?: 'Faculty In-Charge'); ?></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="info-label">Attendance Compliance</div>
                <div class="info-val">
                    <span class="font-monospace"><?php echo $att['present_days']; ?> / <?php echo $att['total_days'] ?: 1; ?> Days</span>
                    <span class="badge bg-success-subtle text-success ms-1"><?php echo $att['percentage']; ?>%</span>
                </div>
            </div>
        </div>

        <!-- Academic Performance Highlight Banner -->
        <div class="p-3 mb-3 rounded-3 border d-flex flex-wrap align-items-center justify-content-between gap-3 <?php echo $isPass ? 'bg-success-subtle border-success' : 'bg-danger-subtle border-danger'; ?>">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 rounded-circle bg-white shadow-sm text-center" style="width: 48px; height: 48px; line-height: 32px;">
                    <?php if($position == 1): ?>
                        <i class="fa fa-trophy text-warning fs-4"></i>
                    <?php elseif($position == 2): ?>
                        <i class="fa fa-medal text-secondary fs-4"></i>
                    <?php elseif($position == 3): ?>
                        <i class="fa fa-award text-danger fs-4"></i>
                    <?php else: ?>
                        <i class="fa fa-star text-primary fs-4"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">
                        <?php if($position == 1): ?>
                            🥇 1st Position Holder in Class
                        <?php elseif($position == 2): ?>
                            🥈 2nd Position Holder
                        <?php elseif($position == 3): ?>
                            🥉 3rd Position Holder
                        <?php elseif($position): ?>
                            Class Merit Rank: <?php echo $position; ?> of <?php echo $summary['total_enrolled']; ?>
                        <?php else: ?>
                            Examination Result Docket
                        <?php endif; ?>
                    </h5>
                    <div class="small text-muted">
                        Total Marks: <strong><?php echo $perf['total_obtained']; ?> / <?php echo $perf['total_full_marks']; ?></strong> &bull;
                        Percentage: <strong><?php echo $perf['percentage']; ?>%</strong> &bull;
                        Overall GPA: <strong><?php echo number_format($perf['gpa'], 1); ?></strong>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <span class="badge <?php echo $isPass ? 'bg-success' : 'bg-danger'; ?> fs-6 px-3 py-2 text-uppercase">
                    <?php echo $isPass ? 'PROMOTED / PASS' : 'FAILED / COMPARTMENT'; ?>
                </span>
                <span class="badge <?php echo $perf['grade_badge']; ?> fs-6 px-3 py-2 ms-1">
                    Grade: <?php echo $perf['grade']; ?>
                </span>
            </div>
        </div>

        <!-- Subject Marks Statement Table -->
        <table class="table table-bordered table-marks mb-3 align-middle">
            <thead>
                <tr class="text-center">
                    <th style="width: 45px;">Sr #</th>
                    <th class="text-start">Subject Title</th>
                    <th style="width: 80px;">Full</th>
                    <th style="width: 75px;">Theory</th>
                    <th style="width: 75px;">Practical</th>
                    <th style="width: 85px;">Obtained</th>
                    <th style="width: 65px;">Pass</th>
                    <th style="width: 65px;">%</th>
                    <th style="width: 65px;">Grade</th>
                    <th style="width: 60px;">GPA</th>
                    <th style="width: 75px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $sr = 1; foreach($perf['subjects'] as $sub): 
                    $subPass = !$sub['is_failed'];
                ?>
                    <tr>
                        <td class="text-center fw-bold text-muted"><?php echo $sr++; ?></td>
                        <td>
                            <strong class="text-dark"><?php echo htmlspecialchars($sub['subject_name']); ?></strong>
                            <small class="text-muted font-monospace ms-1">[<?php echo htmlspecialchars($sub['subject_code'] ?: 'SUB'); ?>]</small>
                        </td>
                        <td class="text-center font-monospace"><?php echo $sub['full_marks']; ?></td>
                        <td class="text-center font-monospace"><?php echo $sub['theory_marks']; ?></td>
                        <td class="text-center font-monospace"><?php echo $sub['practical_marks']; ?></td>
                        <td class="text-center font-monospace fw-bold fs-6 <?php echo !$subPass ? 'text-danger' : 'text-primary'; ?>">
                            <?php if($sub['is_absent']): ?>
                                <span class="badge bg-danger">ABS</span>
                            <?php else: ?>
                                <?php echo $sub['obtained_marks']; ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center font-monospace text-muted"><?php echo $sub['passing_marks']; ?></td>
                        <td class="text-center font-monospace"><?php echo $sub['percentage']; ?>%</td>
                        <td class="text-center fw-bold"><?php echo $sub['grade']; ?></td>
                        <td class="text-center font-monospace"><?php echo number_format($sub['gpa'], 1); ?></td>
                        <td class="text-center">
                            <?php if($subPass): ?>
                                <span class="badge bg-success-subtle text-success border">PASS</span>
                            <?php else: ?>
                                <span class="badge bg-danger">FAIL</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-light fw-bold">
                <tr>
                    <td colspan="2" class="text-end text-uppercase">Grand Total:</td>
                    <td class="text-center font-monospace"><?php echo $perf['total_full_marks']; ?></td>
                    <td colspan="2" class="text-muted small text-center">&mdash;</td>
                    <td class="text-center font-monospace fs-6 text-primary"><?php echo $perf['total_obtained']; ?></td>
                    <td class="text-muted small text-center">&mdash;</td>
                    <td class="text-center font-monospace"><?php echo $perf['percentage']; ?>%</td>
                    <td class="text-center"><?php echo $perf['grade']; ?></td>
                    <td class="text-center font-monospace"><?php echo number_format($perf['gpa'], 1); ?></td>
                    <td class="text-center">
                        <span class="badge <?php echo $isPass ? 'bg-success' : 'bg-danger'; ?>"><?php echo $perf['status']; ?></span>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Grading Legend & Behavioral Rating -->
        <div class="row g-2 mb-3">
            <div class="col-md-7">
                <div class="p-2 border rounded-3 bg-white h-100">
                    <strong class="d-block small text-muted text-uppercase mb-1" style="font-size: 10px;">Institutional Grading Standard</strong>
                    <table class="table table-scale text-center mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Range</th>
                                <th>Grade</th>
                                <th>GPA</th>
                                <th>Standard Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>80% &ndash; 100%</td>
                                <td class="fw-bold text-success">A+</td>
                                <td>4.0</td>
                                <td>Exceptional / Distinction</td>
                            </tr>
                            <tr>
                                <td>70% &ndash; 79%</td>
                                <td class="fw-bold text-primary">A</td>
                                <td>3.7</td>
                                <td>Excellent</td>
                            </tr>
                            <tr>
                                <td>60% &ndash; 69%</td>
                                <td class="fw-bold text-info">B</td>
                                <td>3.0</td>
                                <td>Very Good</td>
                            </tr>
                            <tr>
                                <td>50% &ndash; 59%</td>
                                <td class="fw-bold text-warning">C</td>
                                <td>2.0</td>
                                <td>Good / Satisfactory</td>
                            </tr>
                            <tr>
                                <td>40% &ndash; 49%</td>
                                <td class="fw-bold text-secondary">D</td>
                                <td>1.0</td>
                                <td>Fair / Pass</td>
                            </tr>
                            <tr>
                                <td>Below 40%</td>
                                <td class="fw-bold text-danger">F</td>
                                <td>0.0</td>
                                <td>Fail / Compartment</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-5">
                <div class="p-2 border rounded-3 bg-white h-100">
                    <strong class="d-block small text-muted text-uppercase mb-1" style="font-size: 10px;">Conduct &amp; Character Assessment</strong>
                    <table class="table table-scale mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold">Moral Conduct &amp; Discipline:</td>
                                <td class="text-end text-success fw-bold">Exemplary</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Punctuality &amp; Regularity:</td>
                                <td class="text-end text-primary fw-bold">Consistent</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Classroom Participation:</td>
                                <td class="text-end text-dark fw-bold">Active &amp; Keen</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Neatness &amp; Uniform:</td>
                                <td class="text-end text-dark fw-bold">Well Groomed</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Social &amp; Leadership Skills:</td>
                                <td class="text-end text-primary fw-bold">Positive</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pedagogical Remarks -->
        <div class="p-2 border rounded-3 bg-light mb-4">
            <strong class="small text-muted text-uppercase d-block mb-1" style="font-size: 10px;">Teacher &amp; Institutional Evaluation:</strong>
            <p class="mb-0 small text-dark fst-italic">
                "<?php echo htmlspecialchars($data['pedagogical_remarks']); ?>"
            </p>
        </div>

        <!-- Official Signatures & Seal -->
        <div class="row text-center mt-4 pt-2">
            <div class="col-4">
                <div class="border-top border-dark pt-2">
                    <strong class="d-block small text-dark">Class Teacher In-Charge</strong>
                    <span class="text-muted small" style="font-size: 11px;"><?php echo htmlspecialchars($st->class_teacher_name ?: 'Teacher Signature'); ?></span>
                </div>
            </div>
            <div class="col-4">
                <div class="seal-box">
                    <div>
                        <i class="fa fa-stamp d-block mb-1"></i>
                        Controller of Examinations
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="border-top border-dark pt-2">
                    <strong class="d-block small text-dark">Principal / Head of Institution</strong>
                    <span class="text-muted small" style="font-size: 11px;">Official Seal &amp; Signature</span>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between small text-muted mt-3 pt-2 border-top font-monospace" style="font-size: 9px;">
            <span>Official Computer-Generated Progress Docket &bull; Excellence Academic System</span>
            <span>Date of Issue: <?php echo date('d-M-Y'); ?></span>
        </div>
    </div>
</div>

</body>
</html>
