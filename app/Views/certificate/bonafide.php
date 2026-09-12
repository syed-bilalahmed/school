<?php
$b = $data['bonafide'];
$s = $b->student;
$sch = $b->school;
$schoolName = !empty($sch->name) ? $sch->name : 'PAK ACADEMY MODEL HIGH SCHOOL';
$schoolAddress = !empty($sch->address) ? $sch->address : 'Main Educational Complex, Lahore, Pakistan';
$schoolPhone = !empty($sch->phone) ? $sch->phone : '+92 42 35889000';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bonafide Certificate - <?php echo htmlspecialchars($s->student_name); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
        }
        body {
            background: #f1f5f9;
            color: #0f172a;
            padding: 30px 15px;
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
            font-family: 'Segoe UI', sans-serif;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-family: 'Segoe UI', sans-serif;
        }
        .btn-primary { background: #1e3a8a; color: #fff; }
        .btn-secondary { background: #64748b; color: #fff; }

        .bonafide-sheet {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 45px 55px;
            border: 2px solid #0f172a;
            outline: 6px double #0f172a;
            outline-offset: 4px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            position: relative;
        }

        .letterhead {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 14px;
            margin-bottom: 24px;
        }
        .school-name {
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .school-sub {
            font-size: 11px;
            color: #475569;
            margin-top: 3px;
        }

        .meta-strip {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 25px;
        }

        .title-box {
            text-align: center;
            margin: 25px 0 35px 0;
        }
        .title-text {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 4px;
            display: inline-block;
        }

        .cert-body {
            font-size: 15px;
            line-height: 2.1;
            color: #1e293b;
            text-align: justify;
            margin-bottom: 30px;
        }
        .cert-body strong {
            color: #0f172a;
            border-bottom: 1px dotted #0f172a;
            padding: 0 4px;
        }

        .bio-summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }
        .bio-summary-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
        }
        .bio-summary-table .label-col {
            background: #f8fafc;
            font-weight: 700;
            color: #334155;
            width: 35%;
        }
        .bio-summary-table .val-col {
            font-weight: 700;
            color: #0f172a;
        }

        .purpose-box {
            background: #f8fafc;
            border-left: 4px solid #1e3a8a;
            padding: 10px 14px;
            font-size: 12px;
            color: #334155;
            margin-bottom: 35px;
        }

        .sig-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 50px;
        }
        .sig-box {
            width: 220px;
            border-top: 1.5px solid #0f172a;
            padding-top: 6px;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            color: #0f172a;
        }

        @media print {
            body { background: #ffffff; padding: 0; }
            .no-print { display: none !important; }
            .bonafide-sheet {
                box-shadow: none;
                margin: 0;
                width: 100%;
                max-width: 100%;
                padding: 30px 40px;
            }
            @page { size: A4 portrait; margin: 12mm; }
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
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print Bonafide Certificate
            </button>
        </div>
    </div>

    <div class="bonafide-sheet">
        <div class="letterhead">
            <div class="school-name"><?php echo htmlspecialchars($schoolName); ?></div>
            <div class="school-sub"><?php echo htmlspecialchars($schoolAddress); ?> &bull; Tel: <?php echo htmlspecialchars($schoolPhone); ?></div>
        </div>

        <div class="meta-strip">
            <div>Ref / Certificate No: <strong style="color: #dc2626; font-family: monospace;"><?php echo htmlspecialchars($b->certificate_no); ?></strong></div>
            <div>Date of Issue: <strong><?php echo date('d-F-Y', strtotime($b->issue_date)); ?></strong></div>
        </div>

        <div class="title-box">
            <span class="title-text">Bonafide Student Certificate</span>
        </div>

        <div class="cert-body">
            TO WHOM IT MAY CONCERN:
            <br><br>
            This is to certify that <strong><?php echo strtoupper(htmlspecialchars($s->student_name)); ?></strong>, son / daughter of <strong><?php echo strtoupper(htmlspecialchars($s->father_name ?: 'N/A')); ?></strong>, is a genuine and bonafide regular student currently enrolled and studying in Class <strong><?php echo htmlspecialchars($s->class_name . ' (' . ($s->section_name ?: 'General') . ')'); ?></strong> of this institution under Academic Session <strong><?php echo htmlspecialchars($b->session_name); ?></strong>.
        </div>

        <table class="bio-summary-table">
            <tr>
                <td class="label-col">Admission / Enrolment No:</td>
                <td class="val-col font-monospace">#<?php echo htmlspecialchars($s->admission_no ?: '-'); ?></td>
            </tr>
            <tr>
                <td class="label-col">Class Roll Number:</td>
                <td class="val-col font-monospace"><?php echo htmlspecialchars($s->roll_no ?: '-'); ?></td>
            </tr>
            <tr>
                <td class="label-col">NADRA B-Form / CNIC No:</td>
                <td class="val-col font-monospace"><?php echo htmlspecialchars($s->bform_cnic ?: 'Verified on Record'); ?></td>
            </tr>
            <tr>
                <td class="label-col">Date of Birth:</td>
                <td class="val-col"><?php echo !empty($s->dob) ? date('d-F-Y', strtotime($s->dob)) : 'N/A'; ?></td>
            </tr>
            <tr>
                <td class="label-col">Residential Address:</td>
                <td class="val-col"><?php echo htmlspecialchars($s->address ?: 'On School Record'); ?></td>
            </tr>
        </table>

        <div class="purpose-box">
            <strong>Purpose of Certificate:</strong> This certificate is issued upon the formal request of the parent / guardian for <strong><?php echo htmlspecialchars($b->purpose); ?></strong> and bears no financial liability on part of the institution.
        </div>

        <div class="sig-row">
            <div class="sig-box">
                Verified By<br>
                <span style="font-size: 10.5px; font-weight: normal; color: #64748b;">Office Superintendent / Registrar</span>
            </div>
            <div class="sig-box">
                Principal / Head of Institution<br>
                <span style="font-size: 10.5px; font-weight: normal; color: #64748b;">(Official Seal &amp; Signature)</span>
            </div>
        </div>
    </div>

</body>
</html>
