<?php
$c = $data['clearance'];
$schoolName = "PAKISTAN HIGHER SECONDARY SCHOOL";
$boardAffiliation = "Affiliated with Board of Intermediate & Secondary Education (BISE) Lahore";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Certificate (NOC) - <?php echo htmlspecialchars($c->clearance_no); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Times New Roman', Times, serif;
            color: #1e293b;
        }
        .cert-container {
            max-width: 850px;
            margin: 30px auto;
            background: #ffffff;
            padding: 40px 50px;
            border: 3px double #0f172a;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }
        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            font-weight: bold;
            color: rgba(15, 23, 42, 0.04);
            letter-spacing: 12px;
            text-transform: uppercase;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
        }
        .cert-content {
            position: relative;
            z-index: 1;
        }
        .cert-title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: 2px solid #0f172a;
            display: inline-block;
            padding-bottom: 4px;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .cert-table th, .cert-table td {
            font-size: 13px;
            padding: 8px 10px;
            border-color: #cbd5e1;
        }
        .seal-box {
            width: 90px;
            height: 90px;
            border: 2px dashed #94a3b8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #94a3b8;
            font-size: 11px;
            text-align: center;
            text-transform: uppercase;
        }
        @media print {
            body {
                background: none;
                margin: 0;
            }
            .cert-container {
                margin: 0 auto;
                border: 2px double #000;
                box-shadow: none;
                padding: 30px 40px;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container text-center my-3 no-print">
    <div class="d-inline-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary px-4 shadow-sm fw-bold">
            <i class="fa fa-print me-2"></i> Print Clearance Certificate (NOC)
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3">
            <i class="fa fa-times me-1"></i> Close
        </button>
    </div>
</div>

<div class="cert-container">
    <div class="cert-watermark">INSTITUTIONAL NOC</div>

    <div class="cert-content">
        <!-- Header -->
        <div class="text-center mb-3">
            <div class="d-flex align-items-center justify-content-center gap-3 mb-1">
                <i class="fa fa-school fa-2x text-dark"></i>
                <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: 0.5px;"><?php echo $schoolName; ?></h3>
            </div>
            <p class="small text-muted mb-0"><?php echo $boardAffiliation; ?></p>
            <p class="small text-muted mb-1">G.T. Road, Model Town Campus, Lahore &bull; Phone: (042) 35889901</p>
            <div class="cert-title">INSTITUTIONAL CLEARANCE CERTIFICATE &amp; NOC</div>
        </div>

        <!-- Meta Line -->
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom small">
            <div>
                <strong>Clearance Certificate No:</strong>
                <span class="font-monospace text-primary ms-1 fw-bold"><?php echo htmlspecialchars($c->clearance_no); ?></span>
            </div>
            <div>
                <strong>Issue Date:</strong>
                <span class="ms-1"><?php echo date('d F, Y', strtotime($c->completion_date ?: date('Y-m-d'))); ?></span>
            </div>
        </div>

        <!-- Preamble / Testimonial Paragraph -->
        <p class="text-justify mb-3" style="font-size: 15px; line-height: 1.8;">
            This is to certify that <strong><u><?php echo htmlspecialchars($c->student_name); ?></u></strong>, 
            Son / Daughter of <strong><u><?php echo htmlspecialchars($c->father_name ?: 'N/A'); ?></u></strong>, 
            bearing Admission No. <strong><u><?php echo htmlspecialchars($c->admission_no); ?></u></strong> 
            and Class Roll No. <strong><u><?php echo htmlspecialchars($c->roll_no ?: 'None'); ?></u></strong>, 
            was a bona fide student of Class <strong><u><?php echo htmlspecialchars($c->class_name ?? ''); ?> (<?php echo htmlspecialchars($c->section_name ?? 'A'); ?>)</u></strong> 
            during Academic Session <strong><u><?php echo htmlspecialchars($c->session_name ?? '2026-27'); ?></u></strong>. 
            The student has applied for institutional clearance on account of <em><?php echo htmlspecialchars($c->reason_for_leaving); ?></em>.
        </p>

        <p class="text-justify mb-3" style="font-size: 14px; line-height: 1.6;">
            It is hereby certified that all institutional dues, library assets, scientific and computer laboratory apparatus, athletic equipment, and civil cards have been thoroughly audited and cleared by the respective departmental in-charges as recorded below:
        </p>

        <!-- 5-Department Clearance Matrix Table -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle cert-table mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">Department / Section</th>
                        <th style="width: 15%;">Clearance Status</th>
                        <th style="width: 35%;">Remarks &amp; Liability Assessment</th>
                        <th style="width: 20%;">Officer Sign-Off</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center fw-bold">1</td>
                        <td><strong>Accounts &amp; Fee Section</strong></td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                <?php echo htmlspecialchars($c->accounts_status); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($c->accounts_remarks ?: 'Zero balance. All monthly and term fees paid in full.'); ?></td>
                        <td><small class="text-muted fw-bold"><?php echo htmlspecialchars($c->accounts_cleared_by ?: 'Accounts Officer'); ?></small></td>
                    </tr>
                    <tr>
                        <td class="text-center fw-bold">2</td>
                        <td><strong>Library &amp; Reading Room</strong></td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                <?php echo htmlspecialchars($c->library_status); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($c->library_remarks ?: 'All borrowed books returned. No overdue fines.'); ?></td>
                        <td><small class="text-muted fw-bold"><?php echo htmlspecialchars($c->library_cleared_by ?: 'Head Librarian'); ?></small></td>
                    </tr>
                    <tr>
                        <td class="text-center fw-bold">3</td>
                        <td><strong>Science &amp; Computer Labs</strong></td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                <?php echo htmlspecialchars($c->lab_status); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($c->lab_remarks ?: 'Physics/Chem/Bio apparatus and IT equipment verified.'); ?></td>
                        <td><small class="text-muted fw-bold"><?php echo htmlspecialchars($c->lab_cleared_by ?: 'Lab In-Charge'); ?></small></td>
                    </tr>
                    <tr>
                        <td class="text-center fw-bold">4</td>
                        <td><strong>Sports &amp; Physical Education</strong></td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                <?php echo htmlspecialchars($c->sports_status); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($c->sports_remarks ?: 'All sports gear and uniforms accounted for.'); ?></td>
                        <td><small class="text-muted fw-bold"><?php echo htmlspecialchars($c->sports_cleared_by ?: 'Sports Director'); ?></small></td>
                    </tr>
                    <tr>
                        <td class="text-center fw-bold">5</td>
                        <td><strong>Class Teacher &amp; Discipline</strong></td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                <?php echo htmlspecialchars($c->class_teacher_status); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($c->class_teacher_remarks ?: 'Student ID card surrendered. Satisfactory character.'); ?></td>
                        <td><small class="text-muted fw-bold"><?php echo htmlspecialchars($c->class_teacher_cleared_by ?: 'Class In-Charge'); ?></small></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- No Objection Statement -->
        <div class="alert alert-light border py-2 px-3 mb-4 small">
            <strong>NO-OBJECTION STATEMENT:</strong> The competent authority of this institution has <u>NO OBJECTION</u> to the issuance of the official School Leaving Certificate (SLC) / Transfer Certificate (TC) or the student's migration / admission into any college, higher secondary school, or educational board.
        </div>

        <!-- Signatures & Stamp Deck -->
        <div class="row pt-4 text-center align-items-end">
            <div class="col-4">
                <div style="height: 35px; border-bottom: 1px solid #000;"></div>
                <small class="fw-bold d-block mt-1">Accounts Officer</small>
                <span class="text-muted fs-xs">Finance Section</span>
            </div>
            <div class="col-4">
                <div class="seal-box">
                    Official<br>School Seal
                </div>
            </div>
            <div class="col-4">
                <div style="height: 35px; border-bottom: 1px solid #000;"></div>
                <small class="fw-bold d-block mt-1">Principal / Headmaster</small>
                <span class="text-muted fs-xs">Executive Seal &amp; Authority</span>
            </div>
        </div>

    </div>
</div>

</body>
</html>
