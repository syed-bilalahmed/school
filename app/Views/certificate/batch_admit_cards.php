<?php
$cards = $data['cards'] ?? [];
$classObj = $data['class'] ?? null;
$examObj = $data['exam'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Admit Cards - <?php echo htmlspecialchars($classObj ? $classObj->class_name : 'Class'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f1f5f9;
            color: #0f172a;
            padding: 20px;
            font-size: 11px;
        }
        .no-print {
            max-width: 800px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #64748b; color: #fff; }

        .admit-card-sheet {
            max-width: 800px;
            margin: 0 auto 30px auto;
            background: #ffffff;
            padding: 20px 24px;
            border: 2px solid #1e3a8a;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            page-break-after: always;
            break-after: page;
        }

        .card-header-box {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .school-title {
            font-size: 17px;
            font-weight: 900;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-sub {
            font-size: 9.5px;
            color: #475569;
        }
        .exam-title-badge {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 3px 14px;
            border-radius: 4px;
            margin-top: 4px;
            letter-spacing: 0.5px;
        }

        .meta-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f1f5f9;
            padding: 4px 8px;
            font-size: 9.5px;
            font-weight: 700;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .candidate-row {
            display: flex;
            gap: 12px;
            margin-bottom: 10px;
        }
        .candidate-bio-table {
            flex-grow: 1;
            border-collapse: collapse;
            font-size: 10px;
        }
        .candidate-bio-table td {
            padding: 2px 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        .candidate-bio-table .label-cell {
            color: #64748b;
            font-weight: 600;
            width: 32%;
        }
        .candidate-bio-table .val-cell {
            color: #0f172a;
            font-weight: 700;
        }

        .photo-box {
            width: 85px;
            height: 105px;
            border: 1.5px dashed #94a3b8;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            flex-shrink: 0;
            text-align: center;
            padding: 4px;
        }
        .photo-box img {
            max-width: 100%;
            max-height: 100%;
            border-radius: 4px;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9.5px;
        }
        .schedule-table th {
            background: #1e3a8a;
            color: #ffffff;
            padding: 4px 6px;
            text-align: left;
            text-transform: uppercase;
            font-size: 9px;
        }
        .schedule-table td {
            padding: 3px 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .schedule-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        .rules-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 4px;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 8.5px;
            line-height: 1.35;
            color: #7f1d1d;
        }

        .sig-row {
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            padding-top: 8px;
            text-align: center;
        }
        .sig-col {
            width: 30%;
            border-top: 1.5px solid #0f172a;
            padding-top: 3px;
            font-size: 9px;
            font-weight: 700;
            color: #0f172a;
        }

        .barcode-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            margin-top: 8px;
            font-size: 8px;
            color: #64748b;
        }

        @media print {
            body { background: #ffffff; padding: 0; }
            .no-print { display: none !important; }
            .admit-card-sheet {
                box-shadow: none;
                margin: 0;
                width: 100%;
                max-width: 100%;
                padding: 12px 16px;
                border: 1px solid #000;
                page-break-after: always;
                break-after: page;
            }
            @page { size: A4 portrait; margin: 8mm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div style="display: flex; align-items: center; gap: 10px;">
            <a href="<?php echo URLROOT; ?>/certificate/hub" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Credentials Hub
            </a>
            <div>
                <strong><?php echo htmlspecialchars($classObj ? $classObj->class_name : 'Selected Class'); ?></strong> &bull;
                <span class="text-muted"><?php echo count($cards); ?> Candidate Admit Cards</span>
            </div>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print All <?php echo count($cards); ?> Admit Cards
            </button>
        </div>
    </div>

    <?php if(empty($cards)): ?>
        <div style="max-width: 600px; margin: 50px auto; background: #fff; padding: 40px; border-radius: 12px; text-align: center;">
            <i class="fa fa-id-card text-muted fa-3x mb-3"></i>
            <h5>No Enrolled Candidates in this Class</h5>
            <p class="text-muted small">Please select another class from the Credentials Hub.</p>
        </div>
    <?php else: ?>

        <?php foreach($cards as $card): 
            $s = $card->student;
            $exam = $card->exam;
            $sch = $card->school;
            $schoolName = !empty($sch->name) ? $sch->name : 'PAK ACADEMY MODEL HIGH SCHOOL';
            $schoolAddress = !empty($sch->address) ? $sch->address : 'Main Educational Complex, Lahore, Pakistan';
        ?>
        <div class="admit-card-sheet">
            <div class="card-header-box">
                <div class="school-title"><?php echo htmlspecialchars($schoolName); ?></div>
                <div class="school-sub"><?php echo htmlspecialchars($schoolAddress); ?></div>
                <div class="exam-title-badge">
                    <?php echo htmlspecialchars($exam->name); ?> &bull; Examination Roll Number Slip
                </div>
            </div>

            <div class="meta-strip">
                <span>Roll Slip #: <strong style="color: #dc2626; font-family: monospace;"><?php echo htmlspecialchars($card->admit_card_no); ?></strong></span>
                <span>Reporting Time: <strong style="color: #dc2626;"><?php echo htmlspecialchars($card->reporting_time); ?></strong></span>
                <span>Date: <strong><?php echo date('d-M-Y', strtotime($card->issue_date)); ?></strong></span>
            </div>

            <div class="candidate-row">
                <table class="candidate-bio-table">
                    <tr>
                        <td class="label-cell">Candidate Name:</td>
                        <td class="val-cell"><?php echo strtoupper(htmlspecialchars($s->student_name)); ?></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Father's Name:</td>
                        <td class="val-cell"><?php echo strtoupper(htmlspecialchars($s->father_name ?: 'N/A')); ?></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Class &amp; Section:</td>
                        <td class="val-cell"><?php echo htmlspecialchars($s->class_name . ' (' . ($s->section_name ?: 'General') . ')'); ?></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Exam Roll Number:</td>
                        <td class="val-cell font-monospace" style="color: #1e3a8a; font-size: 12px;">#<?php echo htmlspecialchars($s->roll_no ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Registration / Adm No:</td>
                        <td class="val-cell font-monospace">Adm: <?php echo htmlspecialchars($s->admission_no ?: '-'); ?> &bull; Reg: <?php echo htmlspecialchars($s->reg_no ?: '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="label-cell">B-Form / CNIC:</td>
                        <td class="val-cell font-monospace"><?php echo htmlspecialchars($s->bform_cnic ?: 'Verified'); ?></td>
                    </tr>
                </table>

                <div class="photo-box">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($s->student_name); ?>&size=80&background=1e3a8a&color=fff" alt="Candidate">
                    <div style="font-size: 8px; color: #94a3b8; margin-top: 2px;">Photo Verified</div>
                </div>
            </div>

            <table class="schedule-table">
                <thead>
                    <tr>
                        <th style="width: 25px;">#</th>
                        <th>Date &amp; Day</th>
                        <th>Subject Title</th>
                        <th>Code</th>
                        <th>Timing</th>
                        <th>Hall / Room</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sr = 1; foreach($card->papers as $p): ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td style="font-weight: 700;"><?php echo date('d-M-Y', strtotime($p->exam_date)); ?> (<?php echo $p->day; ?>)</td>
                        <td style="font-weight: 700;"><?php echo htmlspecialchars($p->subject_name); ?></td>
                        <td class="font-monospace text-muted"><?php echo htmlspecialchars($p->subject_code ?: '-'); ?></td>
                        <td class="font-monospace"><?php echo date('h:i A', strtotime($p->start_time)); ?> &ndash; <?php echo date('h:i A', strtotime($p->end_time)); ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($p->room_no); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="rules-box">
                <strong><i class="fa fa-exclamation-circle me-1"></i> Exam Rules:</strong> 1. Report 30 mins prior. 2. Original Slip mandatory. 3. Mobile phones prohibited. 4. Unfair means leads to paper cancellation.
            </div>

            <div class="sig-row">
                <div class="sig-col">Candidate Signature</div>
                <div class="sig-col">Invigilator Stamp</div>
                <div class="sig-col">Controller of Examinations</div>
            </div>

            <div class="barcode-strip">
                <div>Computerized Examination Docket</div>
                <div style="font-family: monospace; font-weight: 900;"><?php echo htmlspecialchars($card->barcode); ?></div>
                <div>Valid for <?php echo htmlspecialchars($exam->name); ?></div>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>

</body>
</html>
