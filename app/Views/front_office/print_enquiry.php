<?php
$enquiries = $data['enquiries'] ?? [];
$settings = $data['settings'] ?? [];
$schoolName = !empty($settings['school_name']) ? $settings['school_name'] : 'PAKISTAN HIGHER SECONDARY SCHOOL';
$campusName = !empty($settings['campus_name']) ? $settings['campus_name'] : 'MAIN EXECUTIVE CAMPUS';
$schoolAddress = !empty($settings['school_address']) ? $settings['school_address'] : 'Education City, Islamabad, Pakistan';
$schoolPhone = !empty($settings['school_phone']) ? $settings['school_phone'] : '+92-51-111-222-333';
$schoolEmail = !empty($settings['school_email']) ? $settings['school_email'] : 'admissions@school.edu.pk';
$affiliation = !empty($settings['affiliation_board']) ? $settings['affiliation_board'] : 'Affiliated with Federal Board (FBISE)';
$schoolLogo = !empty($settings['logo']) ? $settings['logo'] : '';
$isSingle = !empty($data['single']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Enquiry Form - <?php echo $isSingle ? htmlspecialchars($data['single']->name) : 'All Enquiries'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #0f172a;
            font-size: 12px;
            line-height: 1.35;
        }
        .a4-container {
            max-width: 820px;
            margin: 15px auto;
        }
        .form-sheet {
            background: #ffffff;
            border: 2px solid #1e293b;
            border-radius: 6px;
            padding: 20px 24px;
            margin-bottom: 25px;
            position: relative;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            page-break-after: always;
        }
        .form-sheet:last-child {
            page-break-after: auto;
        }
        /* Top Header Grid: School info LEFT, Candidate photo box RIGHT */
        .header-grid {
            display: grid;
            grid-template-columns: 1fr 140px;
            gap: 15px;
            align-items: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }
        .school-brand-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .school-logo-img {
            max-height: 64px;
            max-width: 64px;
            object-fit: contain;
        }
        .school-title {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .school-sub {
            font-size: 11px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 2px;
        }
        .school-meta {
            font-size: 10px;
            color: #64748b;
        }
        /* Candidate Photo Box on Right */
        .photo-box-right {
            width: 125px;
            height: 145px;
            border: 2px dashed #475569;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 6px;
            background: #f8fafc;
            color: #64748b;
            font-size: 9.5px;
            font-weight: 600;
            line-height: 1.2;
            margin-left: auto;
        }
        .photo-box-right i {
            font-size: 26px;
            margin-bottom: 6px;
            color: #94a3b8;
        }
        /* Document Title Banner */
        .slip-title-banner {
            background: #0f172a;
            color: #ffffff;
            text-align: center;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 3px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-bar {
            background: #f1f5f9;
            border-left: 4px solid #0f172a;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #0f172a;
            margin: 10px 0 6px 0;
            letter-spacing: 0.3px;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
        }
        .grid-table td {
            padding: 4px 6px;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }
        .f-lbl {
            color: #475569;
            font-weight: 600;
            font-size: 10.5px;
            text-transform: uppercase;
            width: 18%;
        }
        .f-val {
            color: #0f172a;
            font-weight: 700;
            width: 32%;
        }
        .stamp-box {
            border: 1px dashed #94a3b8;
            height: 50px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
        }
        .sign-line {
            border-top: 1px solid #334155;
            margin-top: 35px;
            padding-top: 3px;
            font-size: 10.5px;
            font-weight: 700;
            text-align: center;
            color: #1e293b;
        }
        @media print {
            body {
                background: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .a4-container {
                max-width: 100% !important;
                margin: 0 !important;
            }
            .form-sheet {
                border: 2px solid #000 !important;
                box-shadow: none !important;
                padding: 12px 16px !important;
                margin-bottom: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .slip-title-banner {
                background: #000 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .section-bar {
                background: #e2e8f0 !important;
                border-left: 4px solid #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<!-- Floating Print & Action Controls (Hidden on Print) -->
<div class="container text-center my-3 no-print">
    <div class="d-inline-flex align-items-center gap-2 bg-white p-2 px-3 rounded shadow-sm border">
        <button onclick="window.print()" class="btn btn-primary fw-bold px-4">
            <i class="fa fa-print me-2"></i> Print / Download Admission Form<?php echo count($enquiries) > 1 ? 's (' . count($enquiries) . ')' : ''; ?>
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3">
            <i class="fa fa-times me-1"></i> Close
        </button>
    </div>
</div>

<div class="a4-container">
    <?php if(empty($enquiries)): ?>
        <div class="alert alert-warning text-center">No admission enquiry records found to print.</div>
    <?php else: ?>
        <?php foreach($enquiries as $idx => $e): 
            $formSerial = 'ENQ-' . date('Y') . '-' . str_pad($e->id, 4, '0', STR_PAD_LEFT);
        ?>
        <div class="form-sheet">

            <!-- 1. TOP HEADER: School Crest / Legal Name on LEFT & Photo Box on RIGHT -->
            <div class="header-grid">
                <!-- Left Side: School Branding & Official Identity -->
                <div class="school-brand-left">
                    <?php if(!empty($schoolLogo)): ?>
                        <img src="<?php echo URLROOT . '/' . htmlspecialchars($schoolLogo); ?>" alt="School Logo" class="school-logo-img">
                    <?php else: ?>
                        <div class="p-2 bg-primary-subtle text-primary rounded border">
                            <i class="fa fa-graduation-cap fa-2x"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div class="school-title"><?php echo htmlspecialchars($schoolName); ?></div>
                        <div class="school-sub"><?php echo htmlspecialchars($campusName); ?> &bull; <?php echo htmlspecialchars($affiliation); ?></div>
                        <div class="school-meta">
                            <i class="fa fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($schoolAddress); ?> | 
                            <i class="fa fa-phone me-1"></i><?php echo htmlspecialchars($schoolPhone); ?> | 
                            <i class="fa fa-envelope me-1"></i><?php echo htmlspecialchars($schoolEmail); ?>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Candidate Passport Size Photo Box -->
                <div class="photo-box-right">
                    <i class="fa fa-user-circle"></i>
                    <span>CANDIDATE PHOTO</span>
                    <span style="font-size: 8px; color: #94a3b8; margin-top: 3px;">3.5cm x 4.5cm<br>Passport Size</span>
                </div>
            </div>

            <!-- 2. DOCUMENT TITLE BANNER -->
            <div class="slip-title-banner">
                <span><i class="fa fa-file-signature me-2"></i>ADMISSION ENQUIRY &amp; CANDIDATE REGISTRATION DOSSIER</span>
                <span class="font-monospace">FORM NO: <?php echo htmlspecialchars($formSerial); ?></span>
            </div>

            <!-- Meta Quick Strip -->
            <table class="grid-table mb-1" style="background: #f8fafc; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                <tr>
                    <td class="f-lbl">Inquiry Date:</td>
                    <td class="f-val"><?php echo date('d-F-Y', strtotime($e->date)); ?></td>
                    <td class="f-lbl">Class Desired:</td>
                    <td class="f-val text-primary fs-6 fw-bold"><?php echo htmlspecialchars($e->class_name ?: 'General Admission'); ?></td>
                </tr>
                <tr>
                    <td class="f-lbl">Lead Source:</td>
                    <td class="f-val"><?php echo htmlspecialchars($e->source ?: 'Direct Walk-in'); ?></td>
                    <td class="f-lbl">Admission Status:</td>
                    <td class="f-val">
                        <span class="badge bg-dark px-2 py-1"><?php echo htmlspecialchars($e->status ?: 'New Lead'); ?></span>
                    </td>
                </tr>
            </table>

            <!-- SECTION A: CANDIDATE PERSONAL PARTICULARS -->
            <div class="section-bar">
                <i class="fa fa-user me-1 text-primary"></i> 1. Candidate Personal Particulars (طالب علم کے کوائف)
            </div>
            <table class="grid-table">
                <tr>
                    <td class="f-lbl">Full Student Name:</td>
                    <td class="f-val text-uppercase text-primary"><?php echo htmlspecialchars($e->name); ?></td>
                    <td class="f-lbl">Gender:</td>
                    <td class="f-val"><?php echo htmlspecialchars($e->gender ?: 'Male'); ?></td>
                </tr>
                <tr>
                    <td class="f-lbl">Date of Birth:</td>
                    <td class="f-val"><?php echo !empty($e->dob) ? date('d-M-Y', strtotime($e->dob)) : 'As per B-Form / Birth Cert'; ?></td>
                    <td class="f-lbl">Children in Family:</td>
                    <td class="f-val"><?php echo (int)($e->no_of_child ?: 1); ?> Child(ren)</td>
                </tr>
            </table>

            <!-- SECTION B: PARENT & GUARDIAN PARTICULARS -->
            <div class="section-bar">
                <i class="fa fa-users me-1 text-primary"></i> 2. Parent &amp; Guardian Particulars (والدین / سرپرست کی معلومات)
            </div>
            <table class="grid-table">
                <tr>
                    <td class="f-lbl">Father's Name:</td>
                    <td class="f-val"><?php echo htmlspecialchars($e->father_name ?: 'N/A'); ?></td>
                    <td class="f-lbl">Mother's Name:</td>
                    <td class="f-val"><?php echo htmlspecialchars($e->mother_name ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="f-lbl">Primary Mobile / WhatsApp:</td>
                    <td class="f-val font-monospace fw-bold text-dark"><?php echo htmlspecialchars($e->phone); ?></td>
                    <td class="f-lbl">Email Address:</td>
                    <td class="f-val"><?php echo htmlspecialchars($e->email ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="f-lbl">Guardian (If applicable):</td>
                    <td class="f-val"><?php echo htmlspecialchars($e->guardian_name ?: ($e->father_name ?: 'Father')); ?> (<?php echo htmlspecialchars($e->guardian_relation ?: 'Father'); ?>)</td>
                    <td class="f-lbl">Counselor In-charge:</td>
                    <td class="f-val"><?php echo htmlspecialchars($e->staff_name ?: 'Front Desk Admissions'); ?></td>
                </tr>
                <tr>
                    <td class="f-lbl">Residential Address:</td>
                    <td class="f-val" colspan="3"><?php echo htmlspecialchars($e->address ?: 'Not Specified'); ?></td>
                </tr>
            </table>

            <!-- SECTION C: PREVIOUS SCHOOL RECORD & CONCESSION -->
            <div class="section-bar">
                <i class="fa fa-history me-1 text-primary"></i> 3. Academic Background &amp; Fee Concession (سابقہ تعلیمی پس منظر)
            </div>
            <table class="grid-table">
                <tr>
                    <td class="f-lbl">Last School Attended:</td>
                    <td class="f-val" colspan="3"><?php echo htmlspecialchars($e->previous_school ?: 'Fresh Schooling / Direct Entry'); ?></td>
                </tr>
                <tr>
                    <td class="f-lbl">Special Concession:</td>
                    <td class="f-val text-success">
                        <?php if(!empty($e->discount_offered) && $e->discount_offered > 0): ?>
                            PKR <?php echo number_format($e->discount_offered); ?> (Approved Concession)
                        <?php else: ?>
                            Standard Regular Fee Matrix
                        <?php endif; ?>
                    </td>
                    <td class="f-lbl">Next Follow-up Date:</td>
                    <td class="f-val">
                        <?php echo !empty($e->next_follow_up_date) ? date('d-M-Y', strtotime($e->next_follow_up_date)) : 'N/A'; ?>
                    </td>
                </tr>
                <?php if(!empty($e->description)): ?>
                <tr>
                    <td class="f-lbl">Inquiry Notes / Remarks:</td>
                    <td class="f-val" colspan="3"><?php echo htmlspecialchars($e->description); ?></td>
                </tr>
                <?php endif; ?>
            </table>

            <!-- SECTION D: MANDATORY DOCUMENTS CHECKLIST -->
            <div class="section-bar">
                <i class="fa fa-clipboard-check me-1 text-primary"></i> 4. Required Admission Documents Checklist (ضروری دستاویزات برائے داخلہ)
            </div>
            <div class="p-2 mb-2 border rounded bg-light" style="font-size: 10px;">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="d-flex align-items-center mb-1">
                            <span style="display:inline-block; width:12px; height:12px; border:1.5px solid #0f172a; border-radius:2px; margin-right:6px; flex-shrink:0;"></span>
                            <span><strong>1. Printed Admission Form:</strong> Signed by Parent (دستخط شدہ داخلہ فارم)</span>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <span style="display:inline-block; width:12px; height:12px; border:1.5px solid #0f172a; border-radius:2px; margin-right:6px; flex-shrink:0;"></span>
                            <span><strong>2. 4x Passport Photos:</strong> Blue background (چار عدد پاسپورٹ سائز تصاویر)</span>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <span style="display:inline-block; width:12px; height:12px; border:1.5px solid #0f172a; border-radius:2px; margin-right:6px; flex-shrink:0;"></span>
                            <span><strong>3. Father / Guardian CNIC:</strong> 2 attested copies (والد/سرپرست شناختی کارڈ)</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span style="display:inline-block; width:12px; height:12px; border:1.5px solid #0f172a; border-radius:2px; margin-right:6px; flex-shrink:0;"></span>
                            <span><strong>4. Student NADRA B-Form:</strong> Attested copy (نادرا ب-فارم یا برتھ سرٹیفکیٹ)</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center mb-1">
                            <span style="display:inline-block; width:12px; height:12px; border:1.5px solid #0f172a; border-radius:2px; margin-right:6px; flex-shrink:0;"></span>
                            <span><strong>5. School Leaving Certificate (SLC):</strong> Original (سابقہ اسکول کا اصل سرٹیفکیٹ)</span>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <span style="display:inline-block; width:12px; height:12px; border:1.5px solid #0f172a; border-radius:2px; margin-right:6px; flex-shrink:0;"></span>
                            <span><strong>6. Progress Report Card / DMC:</strong> Last class passed (سابقہ رزلٹ کارڈ)</span>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <span style="display:inline-block; width:12px; height:12px; border:1.5px solid #0f172a; border-radius:2px; margin-right:6px; flex-shrink:0;"></span>
                            <span><strong>7. Character Certificate:</strong> From previous school (اگر لاگو ہو)</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span style="display:inline-block; width:12px; height:12px; border:1.5px solid #0f172a; border-radius:2px; margin-right:6px; flex-shrink:0;"></span>
                            <span><strong>8. Vaccination / Medical Record:</strong> Immunization card (میڈیکل کارڈ)</span>
                        </div>
                    </div>
                </div>
                <div class="mt-1 pt-1 border-top text-muted" style="font-size: 8.5px;">
                    <i class="fa fa-info-circle me-1"></i> Please submit all listed documents along with this signed application form to the admission desk within <strong>3 working days</strong>.
                </div>
            </div>

            <!-- SECTION E: UNDERTAKING & OFFICIAL SIGNATURES -->
            <div class="section-bar">
                <i class="fa fa-signature me-1 text-primary"></i> 5. Undertaking &amp; Authorizations (تصدیق نامہ و دستخط)
            </div>
            <div class="p-2 mb-3 bg-light border rounded text-muted" style="font-size: 10px; line-height: 1.3;">
                <strong>Parent Declaration:</strong> I hereby declare that the information provided above is correct to the best of my knowledge. I understand that submission of this enquiry form does not guarantee admission and is subject to seat availability, document verification, and campus assessment.
            </div>

            <div class="row pt-2 align-items-end text-center">
                <div class="col-4">
                    <div class="sign-line">Parent / Guardian Signature</div>
                    <small class="text-muted" style="font-size: 9px;">Dated: ____/____/202___</small>
                </div>
                <div class="col-4">
                    <div class="sign-line">Admission Counselor / Front Desk</div>
                    <small class="text-muted" style="font-size: 9px;">Verified &amp; Recorded</small>
                </div>
                <div class="col-4">
                    <div class="stamp-box mb-1">OFFICIAL SCHOOL STAMP</div>
                    <div class="sign-line" style="margin-top: 5px;">Principal / Campus Head</div>
                </div>
            </div>

        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
