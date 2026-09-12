<?php
$slc = $data['slc'];
$s = $slc->student;
$sch = $slc->school;
$schoolName = !empty($sch->name) ? $sch->name : 'PAK ACADEMY MODEL HIGH SCHOOL';
$schoolAddress = !empty($sch->address) ? $sch->address : 'Main Educational Complex, Lahore, Pakistan';
$schoolPhone = !empty($sch->phone) ? $sch->phone : '+92 42 35889000';
$boardAffiliation = 'Affiliated with Board of Intermediate & Secondary Education (BISE) &bull; Code: 5129';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Leaving Certificate - <?php echo htmlspecialchars($s->student_name); ?></title>
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
            max-width: 860px;
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

        /* CERTIFICATE CONTAINER (A4 PORTRAIT) */
        .slc-certificate {
            max-width: 860px;
            margin: 0 auto;
            background: #ffffff;
            padding: 36px 44px;
            border: 4px double #1e3a8a;
            outline: 2px solid #94a3b8;
            outline-offset: -10px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        /* WATERMARK */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72px;
            font-weight: 900;
            color: rgba(30, 58, 138, 0.04);
            text-transform: uppercase;
            letter-spacing: 6px;
            pointer-events: none;
            white-space: nowrap;
            z-index: 0;
        }

        .cert-inner {
            position: relative;
            z-index: 1;
        }

        /* HEADER */
        .cert-header {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .school-name {
            font-size: 26px;
            font-weight: 900;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .school-tagline {
            font-size: 12px;
            color: #475569;
            font-style: italic;
        }
        .school-address {
            font-size: 11.5px;
            color: #334155;
            margin-top: 2px;
        }
        .affiliation-text {
            font-size: 11px;
            font-weight: 700;
            color: #1e3a8a;
            margin-top: 4px;
        }

        /* TITLE BANNER */
        .title-banner {
            text-align: center;
            margin: 16px 0;
        }
        .title-text {
            display: inline-block;
            font-size: 18px;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            border-bottom: 2px solid #1e3a8a;
            border-top: 2px solid #1e3a8a;
            padding: 4px 24px;
            letter-spacing: 1.5px;
        }

        /* SERIAL & DATES */
        .meta-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #0f172a;
        }
        .cert-no {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            color: #dc2626;
        }

        /* CREDENTIALS PARTICULARS LIST */
        .particulars-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            line-height: 1.7;
        }
        .particulars-table td {
            padding: 4px 6px;
            vertical-align: middle;
        }
        .item-num {
            width: 28px;
            font-weight: 700;
            color: #1e3a8a;
        }
        .item-label {
            width: 320px;
            color: #334155;
            font-weight: 600;
        }
        .item-val {
            border-bottom: 1px dotted #475569;
            color: #0f172a;
            font-weight: 700;
            padding-left: 6px;
        }

        /* SIGNATURES */
        .sig-row {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 20px;
            text-align: center;
        }
        .sig-col {
            width: 28%;
            border-top: 1.5px solid #1e3a8a;
            padding-top: 6px;
            font-size: 11.5px;
            font-weight: 700;
            color: #1e3a8a;
        }

        /* SECURITY FOOTER */
        .sec-footer {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9.5px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-family: 'Segoe UI', sans-serif;
        }
        .barcode {
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 3px;
            font-weight: 900;
            font-size: 11px;
            color: #0f172a;
        }

        @media print {
            body { background: #ffffff; padding: 0; }
            .no-print { display: none !important; }
            .slc-certificate {
                box-shadow: none;
                margin: 0;
                width: 100%;
                max-width: 100%;
                padding: 25px 30px;
            }
            @page { size: A4 portrait; margin: 8mm; }
        }
    </style>
</head>
<body>

    <!-- NO PRINT CONTROLS -->
    <div class="no-print">
        <div>
            <a href="<?php echo URLROOT; ?>/certificate/hub" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Credentials Hub
            </a>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print Official SLC
            </button>
        </div>
    </div>

    <!-- CERTIFICATE -->
    <div class="slc-certificate">
        <div class="watermark">OFFICIAL SLC</div>
        
        <div class="cert-inner">
            <!-- HEADER -->
            <div class="cert-header">
                <div class="school-name"><?php echo htmlspecialchars($schoolName); ?></div>
                <div class="school-tagline">"Excellence in Academic Discipline, Character &amp; Faith"</div>
                <div class="school-address"><?php echo htmlspecialchars($schoolAddress); ?> &bull; Tel: <?php echo htmlspecialchars($schoolPhone); ?></div>
                <div class="affiliation-text"><?php echo $boardAffiliation; ?></div>
            </div>

            <!-- TITLE BANNER -->
            <div class="title-banner">
                <span class="title-text">School Leaving Certificate</span>
                <div style="font-size: 11px; font-style: italic; color: #475569; margin-top: 2px;">(Transfer Certificate / Discharge Docket)</div>
            </div>

            <!-- SERIAL STRIP -->
            <div class="meta-strip">
                <div>Certificate No: <strong class="cert-no"><?php echo htmlspecialchars($slc->certificate_no); ?></strong></div>
                <div>Session: <strong><?php echo htmlspecialchars($slc->session_name); ?></strong></div>
                <div>Date of Issue: <strong><?php echo date('d-F-Y', strtotime($slc->issue_date)); ?></strong></div>
            </div>

            <!-- 16 ITEMIZED PARTICULARS TABLE -->
            <table class="particulars-table">
                <tr>
                    <td class="item-num">1.</td>
                    <td class="item-label">Name of Pupil (in Block Letters):</td>
                    <td class="item-val"><?php echo strtoupper(htmlspecialchars($s->student_name)); ?></td>
                </tr>
                <tr>
                    <td class="item-num">2.</td>
                    <td class="item-label">Father's Name (in Full):</td>
                    <td class="item-val"><?php echo strtoupper(htmlspecialchars($s->father_name ?: 'N/A')); ?></td>
                </tr>
                <tr>
                    <td class="item-num">3.</td>
                    <td class="item-label">Mother's Name:</td>
                    <td class="item-val"><?php echo strtoupper(htmlspecialchars($s->mother_name ?: 'N/A')); ?></td>
                </tr>
                <tr>
                    <td class="item-num">4.</td>
                    <td class="item-label">NADRA B-Form / CNIC No:</td>
                    <td class="item-val font-monospace"><?php echo htmlspecialchars($s->bform_cnic ?: 'Record Verified'); ?></td>
                </tr>
                <tr>
                    <td class="item-num">5.</td>
                    <td class="item-label">Nationality &amp; Religion:</td>
                    <td class="item-val">Pakistani &bull; Muslim</td>
                </tr>
                <tr>
                    <td class="item-num">6.</td>
                    <td class="item-label">School Admission &amp; Enrolment No:</td>
                    <td class="item-val font-monospace">Adm #<?php echo htmlspecialchars($s->admission_no ?: '-'); ?> &bull; Reg #<?php echo htmlspecialchars($s->reg_no ?: '-'); ?></td>
                </tr>
                <tr>
                    <td class="item-num">7.</td>
                    <td class="item-label">Date of First Admission in this School:</td>
                    <td class="item-val"><?php echo !empty($s->admission_date) ? date('d-F-Y', strtotime($s->admission_date)) : 'Admission on Record'; ?></td>
                </tr>
                <tr>
                    <td class="item-num">8.</td>
                    <td class="item-label">Date of Birth (in Figures):</td>
                    <td class="item-val font-monospace"><?php echo !empty($s->dob) ? date('d-m-Y', strtotime($s->dob)) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td class="item-num">9.</td>
                    <td class="item-label">Date of Birth (in Words):</td>
                    <td class="item-val" style="font-style: italic;"><?php echo htmlspecialchars($slc->dob_in_words); ?></td>
                </tr>
                <tr>
                    <td class="item-num">10.</td>
                    <td class="item-label">Class in which the pupil last studied:</td>
                    <td class="item-val"><?php echo htmlspecialchars($s->class_name . ' (' . ($s->section_name ?: 'General') . ')'); ?></td>
                </tr>
                <tr>
                    <td class="item-num">11.</td>
                    <td class="item-label">Promotion Status to Higher Class:</td>
                    <td class="item-val" style="color: #16a34a;"><?php echo htmlspecialchars($slc->promoted_to_class); ?></td>
                </tr>
                <tr>
                    <td class="item-num">12.</td>
                    <td class="item-label">Month up to which all school dues cleared:</td>
                    <td class="item-val"><?php echo htmlspecialchars($slc->dues_cleared_text); ?></td>
                </tr>
                <tr>
                    <td class="item-num">13.</td>
                    <td class="item-label">Total School Days &amp; Attendance:</td>
                    <td class="item-val"><?php echo htmlspecialchars($slc->attendance_ratio); ?></td>
                </tr>
                <tr>
                    <td class="item-num">14.</td>
                    <td class="item-label">General Conduct &amp; Moral Character:</td>
                    <td class="item-val"><?php echo htmlspecialchars($slc->conduct); ?></td>
                </tr>
                <tr>
                    <td class="item-num">15.</td>
                    <td class="item-label">Date of Discharge / Leaving:</td>
                    <td class="item-val"><?php echo date('d-F-Y', strtotime($slc->leaving_date)); ?></td>
                </tr>
                <tr>
                    <td class="item-num">16.</td>
                    <td class="item-label">Reason for Leaving School:</td>
                    <td class="item-val"><?php echo htmlspecialchars($slc->reason_for_leaving); ?></td>
                </tr>
            </table>

            <!-- REMARKS BOX -->
            <div style="margin-top: 14px; font-size: 11.5px; font-style: italic; color: #334155; padding: 6px 10px; background: #f8fafc; border-left: 3px solid #1e3a8a;">
                <strong>General Remarks:</strong> <?php echo htmlspecialchars($slc->remarks); ?>
            </div>

            <!-- SIGNATURES -->
            <div class="sig-row">
                <div class="sig-col">
                    Class Teacher In-Charge
                </div>
                <div class="sig-col">
                    Accounts Officer / Auditor
                </div>
                <div class="sig-col">
                    Principal / Headmaster<br>
                    <span style="font-size: 9.5px; font-weight: normal;">(Official Seal &amp; Stamp)</span>
                </div>
            </div>

            <!-- SECURITY FOOTER -->
            <div class="sec-footer">
                <div>Security Verification Code: <span class="barcode"><?php echo htmlspecialchars($slc->barcode); ?></span></div>
                <div>Verified from School Admission &amp; Withdrawal Register No. <?php echo date('Y'); ?></div>
            </div>
        </div>
    </div>

</body>
</html>
