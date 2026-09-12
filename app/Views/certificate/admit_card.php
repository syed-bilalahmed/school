<?php
$card = $data['card'];
$s = $card->student;
$exam = $card->exam;
$sch = $card->school;
$schoolName = !empty($sch->name) ? $sch->name : 'PAK ACADEMY MODEL HIGH SCHOOL';
$schoolAddress = !empty($sch->address) ? $sch->address : 'Main Educational Complex, Lahore, Pakistan';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Card - <?php echo htmlspecialchars($s->student_name . ' - ' . $exam->name); ?></title>
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
            padding: 25px 15px;
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

        /* ADMIT CARD SHEET (A4 PORTRAIT) */
        .admit-card {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 24px 30px;
            border: 2px solid #1e3a8a;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            position: relative;
        }

        /* HEADER */
        .card-header-box {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .school-title {
            font-size: 18px;
            font-weight: 900;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-sub {
            font-size: 10px;
            color: #475569;
        }
        .exam-title-badge {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 16px;
            border-radius: 4px;
            margin-top: 6px;
            letter-spacing: 0.5px;
        }

        /* METADATA STRIP */
        .meta-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f1f5f9;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 4px;
            margin-bottom: 12px;
        }
        .slip-no {
            color: #dc2626;
            font-family: monospace;
            font-size: 11px;
        }

        /* CANDIDATE BIO & PHOTO ROW */
        .candidate-row {
            display: flex;
            gap: 15px;
            margin-bottom: 14px;
        }
        .candidate-bio-table {
            flex-grow: 1;
            border-collapse: collapse;
            font-size: 10.5px;
        }
        .candidate-bio-table td {
            padding: 3px 6px;
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
            width: 100px;
            height: 120px;
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
        .photo-placeholder {
            font-size: 9px;
            color: #94a3b8;
            font-weight: 600;
        }

        /* TIMETABLE PAPERS SCHEDULE */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10px;
        }
        .schedule-table th {
            background: #1e3a8a;
            color: #ffffff;
            padding: 5px 8px;
            text-align: left;
            text-transform: uppercase;
            font-size: 9.5px;
        }
        .schedule-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .schedule-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        /* RULES BOX */
        .rules-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 9px;
            line-height: 1.4;
            color: #7f1d1d;
        }
        .rules-box strong {
            color: #991b1b;
            text-transform: uppercase;
        }

        /* SIGNATURES */
        .sig-row {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 10px;
            text-align: center;
        }
        .sig-col {
            width: 30%;
            border-top: 1.5px solid #0f172a;
            padding-top: 4px;
            font-size: 9.5px;
            font-weight: 700;
            color: #0f172a;
        }

        .barcode-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            margin-top: 12px;
            font-size: 8.5px;
            color: #64748b;
        }
        .barcode-text {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 3px;
            font-weight: 900;
            font-size: 10px;
            color: #0f172a;
        }

        @media print {
            body { background: #ffffff; padding: 0; }
            .no-print { display: none !important; }
            .admit-card {
                box-shadow: none;
                margin: 0;
                width: 100%;
                max-width: 100%;
                padding: 16px 20px;
                border: 1px solid #000;
            }
            @page { size: A4 portrait; margin: 10mm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div>
            <a href="<?php echo URLROOT; ?>/certificate/hub" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Credentials Hub
            </a>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="<?php echo URLROOT; ?>/certificate/batchAdmitCards?class_id=<?php echo $s->class_id; ?>&exam_id=<?php echo $exam->id; ?>" class="btn btn-secondary">
                <i class="fa fa-layer-group"></i> Print Class Batch
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print Roll No Slip
            </button>
        </div>
    </div>

    <div class="admit-card">
        <!-- HEADER -->
        <div class="card-header-box">
            <div class="school-title"><?php echo htmlspecialchars($schoolName); ?></div>
            <div class="school-sub"><?php echo htmlspecialchars($schoolAddress); ?></div>
            <div class="exam-title-badge">
                <?php echo htmlspecialchars($exam->name); ?> &bull; Examination Roll Number Slip
            </div>
        </div>

        <!-- METADATA STRIP -->
        <div class="meta-strip">
            <span>Roll Slip #: <strong class="slip-no"><?php echo htmlspecialchars($card->admit_card_no); ?></strong></span>
            <span>Reporting Time: <strong style="color: #dc2626;"><?php echo htmlspecialchars($card->reporting_time); ?></strong></span>
            <span>Date of Issue: <strong><?php echo date('d-M-Y', strtotime($card->issue_date)); ?></strong></span>
        </div>

        <!-- CANDIDATE DETAILS & PHOTO -->
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
                    <td class="val-cell font-monospace" style="color: #1e3a8a; font-size: 13px;">
                        #<?php echo htmlspecialchars($s->roll_no ?: '-'); ?>
                    </td>
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

            <!-- PHOTO BOX -->
            <div class="photo-box">
                <?php if(!empty($s->student_photo)): ?>
                    <img src="<?php echo URLROOT . '/' . $s->student_photo; ?>" alt="Candidate">
                <?php else: ?>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($s->student_name); ?>&size=90&background=1e3a8a&color=fff" alt="Candidate">
                <?php endif; ?>
                <div class="photo-placeholder" style="margin-top: 2px;">Candidate Photo</div>
            </div>
        </div>

        <!-- PAPERS SCHEDULE -->
        <div style="font-weight: 800; font-size: 10px; color: #1e3a8a; text-transform: uppercase; margin-bottom: 4px;">
            <i class="fa fa-calendar-alt me-1"></i> Examination Schedule &amp; Room Allotment
        </div>
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 25px;">#</th>
                    <th>Date &amp; Day</th>
                    <th>Subject Title</th>
                    <th>Paper Code</th>
                    <th>Timing</th>
                    <th>Hall / Room</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sr = 1;
                foreach($card->papers as $p): 
                ?>
                <tr>
                    <td><?php echo $sr++; ?></td>
                    <td style="font-weight: 700;"><?php echo date('d-M-Y', strtotime($p->exam_date)); ?> (<?php echo $p->day; ?>)</td>
                    <td style="font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($p->subject_name); ?></td>
                    <td class="font-monospace text-muted"><?php echo htmlspecialchars($p->subject_code ?: '-'); ?></td>
                    <td class="font-monospace fw-bold"><?php echo date('h:i A', strtotime($p->start_time)); ?> &ndash; <?php echo date('h:i A', strtotime($p->end_time)); ?></td>
                    <td class="fw-bold"><?php echo htmlspecialchars($p->room_no); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- CANDIDATE RULES -->
        <div class="rules-box">
            <strong><i class="fa fa-exclamation-circle me-1"></i> Instructions &amp; Examination Conduct Rules:</strong><br>
            1. Candidates must arrive in the examination center <strong>30 minutes before</strong> paper commencement.<br>
            2. This original Admit Card is mandatory for entry. No candidate will be admitted without a valid slip.<br>
            3. Mobile phones, smartwatches, calculators, and unauthorized paper materials are strictly prohibited in the exam hall.<br>
            4. Any form of unfair means (cheating/impersonation) will result in immediate paper cancellation and disciplinary action.
        </div>

        <!-- SIGNATURES -->
        <div class="sig-row">
            <div class="sig-col">
                Candidate's Signature
            </div>
            <div class="sig-col">
                Invigilator's Stamp
            </div>
            <div class="sig-col">
                Controller of Examinations<br>
                <span style="font-size: 8px; font-weight: normal; color: #64748b;">(Official Seal)</span>
            </div>
        </div>

        <!-- BARCODE -->
        <div class="barcode-strip">
            <div>Verified Computerized Examination Docket</div>
            <div class="barcode-text">||||| | |||| || ||||| ||| <?php echo htmlspecialchars($card->barcode); ?></div>
            <div>Valid for <?php echo htmlspecialchars($exam->name); ?></div>
        </div>
    </div>

</body>
</html>
