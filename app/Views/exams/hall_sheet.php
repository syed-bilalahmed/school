<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall Attendance Sheet - <?php echo htmlspecialchars($data['schedule']->subject_name . ' (' . $data['schedule']->class_name . '-' . $data['schedule']->section_name . ')'); ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0f172a;
            padding: 25px 15px;
        }
        .sheet-container {
            max-width: 1050px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: 0 10px 25px -3px rgba(15, 23, 42, 0.08);
            padding: 35px;
        }
        .school-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            color: #1e1b4b;
            letter-spacing: -0.5px;
        }
        .sheet-header-badge {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            background: #f8fafc;
            border: 2px solid #0f172a;
            color: #0f172a;
            display: inline-block;
            padding: 6px 20px;
            border-radius: 6px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .table-roster th {
            background: #f8fafc !important;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .table-roster td {
            border: 1px solid #cbd5e1;
            vertical-align: middle;
            font-size: 0.88rem;
        }
        .sig-cell {
            height: 48px;
            min-width: 140px;
            position: relative;
        }
        .sig-line {
            border-bottom: 1px dashed #94a3b8;
            margin-top: 28px;
        }
        .booklet-cell {
            width: 130px;
        }
        .cert-box {
            border: 1px solid #94a3b8;
            border-radius: 8px;
            background: #fcfcfd;
            padding: 20px;
            page-break-inside: avoid;
        }
        .seal-stamp {
            border: 2px dashed #94a3b8;
            border-radius: 8px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
                margin: 0;
                font-size: 11pt;
            }
            .no-print {
                display: none !important;
            }
            .sheet-container {
                max-width: 100% !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
            }
            .table-roster th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .cert-box {
                border: 1px solid #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<?php
$sch = $data['schedule'];
$students = $data['students'] ?? [];
$totalStudents = count($students);
?>

<!-- Action Bar (Screen Only) -->
<div class="container no-print mb-4" style="max-width: 1050px;">
    <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded-3 shadow-sm border">
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/exam/schedule/<?php echo $sch->exam_id; ?>?class_id=<?php echo $sch->class_id; ?>&section_id=<?php echo $sch->section_id; ?>" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fa fa-arrow-left me-1"></i> Back to Schedule
            </a>
            <a href="<?php echo URLROOT; ?>/exam/marks/<?php echo $sch->exam_id; ?>?class_id=<?php echo $sch->class_id; ?>&section_id=<?php echo $sch->section_id; ?>&schedule_id=<?php echo $sch->id; ?>" class="btn btn-outline-primary btn-sm px-3">
                <i class="fa fa-marker me-1"></i> Enter Marks
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-primary px-4 fw-bold shadow-sm">
                <i class="fa fa-print me-2"></i> Print Signature Sheet
            </button>
        </div>
    </div>
</div>

<!-- Main Sheet Container -->
<div class="sheet-container">
    <!-- Institutional Header -->
    <div class="text-center mb-4 pb-3 border-bottom border-2 border-dark">
        <h2 class="school-title mb-1"><?php echo defined('SITENAME') ? SITENAME : 'EXCELLENCE ACADEMY'; ?></h2>
        <div class="text-uppercase fw-bold text-secondary small mb-2" style="letter-spacing: 1px;">
            Department of Examinations & Academic Evaluations
        </div>
        <div class="sheet-header-badge fs-6 mb-2">
            Exam Hall Attendance & Candidate Signature Docket
        </div>
        <div class="small text-muted font-monospace">
            Session: <strong><?php echo htmlspecialchars($sch->session_name ?? '2026-27'); ?></strong> &bull; Docket Ref: <strong>EHD-<?php echo str_pad($sch->id, 5, '0', STR_PAD_LEFT); ?></strong>
        </div>
    </div>

    <!-- Paper Metadata Information Grid -->
    <div class="bg-light p-3 rounded-3 border mb-4">
        <div class="row g-2 small">
            <div class="col-md-4 col-6">
                <span class="text-muted d-block">Examination:</span>
                <strong class="text-dark fs-6"><?php echo htmlspecialchars($sch->exam_name); ?></strong>
            </div>
            <div class="col-md-4 col-6">
                <span class="text-muted d-block">Class & Section:</span>
                <strong class="text-dark fs-6"><?php echo htmlspecialchars($sch->class_name . ' - ' . $sch->section_name); ?></strong>
            </div>
            <div class="col-md-4 col-6">
                <span class="text-muted d-block">Subject Paper:</span>
                <strong class="text-primary fs-6"><?php echo htmlspecialchars($sch->subject_name); ?></strong>
                <span class="badge bg-light text-dark border font-monospace ms-1"><?php echo htmlspecialchars($sch->subject_code ?: 'SUB'); ?></span>
            </div>
            <div class="col-md-4 col-6">
                <span class="text-muted d-block">Date of Examination:</span>
                <strong class="text-dark"><?php echo date('d-M-Y (l)', strtotime($sch->date_of_exam)); ?></strong>
            </div>
            <div class="col-md-4 col-6">
                <span class="text-muted d-block">Examination Timing:</span>
                <strong class="text-dark"><?php echo date('h:i A', strtotime($sch->start_time)) . ' &ndash; ' . date('h:i A', strtotime($sch->end_time)); ?></strong>
            </div>
            <div class="col-md-4 col-6">
                <span class="text-muted d-block">Hall / Room Assignment:</span>
                <strong class="text-dark"><?php echo htmlspecialchars($sch->room_no ?: 'Main Examination Hall'); ?></strong>
            </div>
        </div>
    </div>

    <!-- Candidate Signature Table -->
    <table class="table table-bordered table-roster mb-4 align-middle">
        <thead>
            <tr class="text-center">
                <th style="width: 45px;">Sr #</th>
                <th style="width: 75px;">Roll No</th>
                <th style="width: 100px;">Adm No</th>
                <th class="text-start">Candidate Name</th>
                <th class="text-start">Father / Guardian Name</th>
                <th style="width: 140px;">B-Form / CNIC</th>
                <th class="booklet-cell">Answer Script Serial #</th>
                <th style="width: 150px;">Candidate Signature</th>
                <th style="width: 80px;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($students)): ?>
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        No candidates enrolled in this class section.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach($students as $idx => $st): 
                    $isAbsent = ($st->is_absent === 'yes');
                ?>
                    <tr>
                        <td class="text-center fw-bold text-muted"><?php echo $idx + 1; ?></td>
                        <td class="text-center fw-bold fs-6 font-monospace"><?php echo htmlspecialchars($st->roll_no ?: ($idx + 1)); ?></td>
                        <td class="text-center font-monospace small"><?php echo htmlspecialchars($st->admission_no ?: 'ADM-' . $st->student_id); ?></td>
                        <td>
                            <strong class="text-dark"><?php echo htmlspecialchars($st->name); ?></strong>
                        </td>
                        <td>
                            <span class="text-secondary"><?php echo htmlspecialchars($st->father_name ?: 'N/A'); ?></span>
                        </td>
                        <td class="font-monospace small text-center">
                            <?php echo htmlspecialchars($st->bform_cnic ?: '&mdash;'); ?>
                        </td>
                        <td class="booklet-cell text-center">
                            <span class="text-muted small">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span>
                        </td>
                        <td class="sig-cell">
                            <div class="sig-line"></div>
                        </td>
                        <td class="text-center font-monospace fw-bold">
                            <?php if($isAbsent): ?>
                                <span class="badge bg-danger">ABS</span>
                            <?php else: ?>
                                <span class="text-muted">[ &nbsp; ] P &nbsp; [ &nbsp; ] A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Official Invigilator Declaration & Certification Docket -->
    <div class="cert-box mt-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="p-2 border rounded bg-white small mb-2">
                    <strong>CANDIDATE ATTENDANCE AUDIT:</strong>
                    <div class="d-flex justify-content-between mt-1">
                        <span>Total Registered:</span>
                        <strong class="font-monospace"><?php echo $totalStudents; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span>Total Present:</span>
                        <strong class="font-monospace text-success">__________</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span>Total Absent:</span>
                        <strong class="font-monospace text-danger">__________</strong>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="small text-muted mb-3 fst-italic">
                    "I hereby solemnly declare and certify that all candidates signed this docket in my physical presence in the designated examination hall after due identity verification against official school credentials."
                </div>
                <div class="row g-2 small">
                    <div class="col-6">
                        <div class="border-top border-dark pt-1 text-center">
                            <strong>Invigilator Signature</strong>
                            <div class="text-muted" style="font-size: 10px;">Name: _________________</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border-top border-dark pt-1 text-center">
                            <strong>Class Teacher In-Charge</strong>
                            <div class="text-muted" style="font-size: 10px;"><?php echo htmlspecialchars($sch->class_teacher_name ?: 'Signature'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="seal-stamp text-center">
                    <div>
                        <i class="fa fa-stamp d-block mb-1"></i>
                        Controller of Exams / Superintendent Official Seal
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Docket Print Timestamp -->
    <div class="d-flex justify-content-between small text-muted mt-3 font-monospace" style="font-size: 10px;">
        <span>Generated via School ERP System &bull; Confidential Examination Document</span>
        <span>Date & Time of Print: <?php echo date('d-M-Y h:i:s A'); ?></span>
    </div>
</div>

</body>
</html>
