<?php
$student = $data['student'] ?? null;
$isBlank = !empty($data['is_blank']) || empty($student);
$settings = $data['settings'] ?? [];
$classes = $data['classes'] ?? [];

$schoolName = !empty($settings['school_name']) ? $settings['school_name'] : 'PAKISTAN HIGHER SECONDARY SCHOOL';
$campusName = !empty($settings['campus_name']) ? $settings['campus_name'] : 'MAIN EXECUTIVE CAMPUS';
$schoolAddress = !empty($settings['school_address']) ? $settings['school_address'] : 'Education City, Islamabad, Pakistan';
$schoolPhone = !empty($settings['school_phone']) ? $settings['school_phone'] : '+92-51-111-222-333';
$schoolEmail = !empty($settings['school_email']) ? $settings['school_email'] : 'admissions@school.edu.pk';
$affiliation = !empty($settings['affiliation_board']) ? $settings['affiliation_board'] : 'Affiliated with Federal Board (FBISE) / BISE';
$schoolLogo = !empty($settings['logo']) ? $settings['logo'] : '';

// Helper to render Pakistani 13-box CNIC / Form-B format
function renderCnicBoxes($cnic = '', $blank = false, $containerId = '') {
    $clean = preg_replace('/[^0-9]/', '', (string)$cnic);
    $digits = str_split(str_pad($clean, 13, ' '));
    $idAttr = !empty($containerId) ? ' id="' . htmlspecialchars($containerId) . '"' : '';
    $html = '<div class="cnic-boxes-container"' . $idAttr . ' title="13-digit Form-B / CNIC">';
    // Part 1: First 5 digits
    for ($i = 0; $i < 5; $i++) {
        $char = (!$blank && isset($digits[$i]) && $digits[$i] !== ' ') ? $digits[$i] : '&nbsp;';
        $html .= '<span class="cnic-box" contenteditable="true">' . $char . '</span>';
    }
    $html .= '<span class="cnic-separator">-</span>';
    // Part 2: Next 7 digits
    for ($i = 5; $i < 12; $i++) {
        $char = (!$blank && isset($digits[$i]) && $digits[$i] !== ' ') ? $digits[$i] : '&nbsp;';
        $html .= '<span class="cnic-box" contenteditable="true">' . $char . '</span>';
    }
    $html .= '<span class="cnic-separator">-</span>';
    // Part 3: Last digit
    $char = (!$blank && isset($digits[12]) && $digits[12] !== ' ') ? $digits[12] : '&nbsp;';
    $html .= '<span class="cnic-box" contenteditable="true">' . $char . '</span>';
    $html .= '</div>';
    return $html;
}

$admNo = !$isBlank ? ($student->admission_no ?? '') : '';
$rollNo = !$isBlank ? ($student->roll_no ?? '') : '';
$regNo = !$isBlank ? ($student->reg_no ?? '') : '';
$admDate = !$isBlank ? (!empty($student->admission_date) ? date('d-M-Y', strtotime($student->admission_date)) : date('d-M-Y')) : '';
$sessionName = !$isBlank ? ($student->session_name ?? 'Current Academic Session') : 'Academic Session: 2026 - 2027';
$className = !$isBlank ? ($student->class_name ?? '') : '';
$secName = !$isBlank ? ($student->section_name ?? '') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Admission Form - <?php echo !$isBlank ? htmlspecialchars($student->name ?? 'Candidate') : 'Blank Registration Form'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Nastaliq+Urdu:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Exact A4 Page Sizing to fit 100% on 1 sheet with ZERO overflow */
        @page {
            size: A4 portrait;
            margin: 4mm 6mm 3mm 6mm;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }
        body {
            background-color: #f1f5f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Screen Non-Print Toolbar */
        .no-print-bar {
            background: #1e293b;
            color: #fff;
            padding: 8px 18px;
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        }

        /* Main A4 Container */
        .a4-sheet-container {
            max-width: 840px;
            margin: 15px auto 35px;
            background: #ffffff;
            border: 1.5px solid #334155;
            border-radius: 4px;
            padding: 8px 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            position: relative;
        }

        /* Inner Clean Border */
        .sheet-inner-border {
            border: 1.2px solid #475569;
            padding: 6px 10px;
            position: relative;
        }

        /* Header Layout: Logo (Left), Clear School Info (Center), Candidate Photo (Right) */
        .form-header-grid {
            display: grid;
            grid-template-columns: 80px 1fr 105px;
            gap: 10px;
            align-items: center;
            border-bottom: 1.5px solid #1e293b;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .school-logo-area {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .school-logo {
            max-height: 68px;
            max-width: 68px;
            object-fit: contain;
        }
        .school-logo-placeholder {
            width: 62px;
            height: 62px;
            border-radius: 8px;
            background: #f1f5f9;
            border: 1.5px solid #cbd5e1;
            color: #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }
        .school-info-area {
            text-align: center;
        }
        .school-title {
            font-size: 21px;
            font-weight: 900;
            letter-spacing: 0.4px;
            color: #000000;
            margin: 0 0 1px 0;
            text-transform: uppercase;
            line-height: 1.15;
        }
        .school-campus {
            font-size: 12px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 1px;
            letter-spacing: 0.3px;
        }
        .school-meta {
            font-size: 10px;
            color: #334155;
            line-height: 1.3;
            font-weight: 500;
        }

        /* Candidate Photo Box on Right */
        .candidate-photo-box {
            width: 100px;
            height: 112px;
            border: 1.5px dashed #475569;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 3px;
            background: #f8fafc;
            margin-left: auto;
        }
        .candidate-photo-box .photo-label {
            font-size: 9.5px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            line-height: 1.15;
        }
        .candidate-photo-box .photo-sub {
            font-size: 8px;
            color: #64748b;
            margin-top: 3px;
            line-height: 1.1;
        }
        .candidate-photo-box .stamp-mark {
            margin-top: 4px;
            font-size: 8px;
            font-weight: 700;
            color: #64748b;
            border: 1px dashed #94a3b8;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
        }

        /* Form Title Strip - Simple, Light & Crisp (No Dark Background) */
        .form-title-strip {
            background: #f8fafc;
            color: #000000;
            border: 1.5px solid #1e293b;
            text-align: center;
            padding: 3px 8px;
            font-weight: 800;
            font-size: 12px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .form-title-strip .urdu-title {
            font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', 'Urdu Typesetting', Tahoma, sans-serif;
            font-size: 13.5px;
            font-weight: 700;
            direction: rtl;
        }

        /* Section Header - Clean Light Header (No Dark Fill) */
        .section-header {
            background: #e2e8f0;
            color: #000000;
            padding: 2.5px 7px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-left: 3.5px solid #0f172a;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            margin-top: 4px;
            margin-bottom: 2.5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-header .urdu-label {
            font-family: 'Noto Nastaliq Urdu', 'Jameel Noori Nastaleeq', 'Urdu Typesetting', Tahoma, sans-serif;
            font-size: 11.5px;
            font-weight: 700;
            direction: rtl;
        }

        /* Clean Table Form Grid */
        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .form-table td, .form-table th {
            border: 1px solid #94a3b8;
            padding: 2px 5px;
            vertical-align: middle;
            font-size: 11px;
        }
        .form-table .label-cell {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #0f172a;
            width: 17%;
            white-space: nowrap;
            font-size: 11px;
        }
        .form-table .val-cell {
            background-color: #ffffff;
            color: #000000;
            font-size: 11px;
        }
        .val-text {
            font-weight: 600;
            display: inline-block;
            width: 100%;
            color: #000000;
        }

        /* 13 Digit Pakistani CNIC / Form-B Boxes */
        .cnic-boxes-container {
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
        }
        .cnic-box {
            display: inline-block;
            width: 16px;
            height: 19px;
            line-height: 18px;
            text-align: center;
            border: 1.2px solid #0f172a;
            background: #ffffff;
            font-weight: 800;
            font-family: Consolas, Monaco, monospace;
            font-size: 11px;
            margin-right: 1px;
            color: #000000;
        }
        .cnic-separator {
            font-weight: 900;
            padding: 0 2px;
            color: #0f172a;
            font-size: 12px;
        }

        /* Office Use Box */
        .office-use-box {
            border: 1.2px solid #64748b;
            background: #f8fafc;
            padding: 2.5px 6px;
            margin-bottom: 2.5px;
        }
        .office-use-title {
            font-weight: 800;
            color: #0f172a;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 2px;
            display: flex;
            justify-content: space-between;
        }

        /* Document Checklist */
        .checklist-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 10px;
            font-size: 9.5px;
            padding: 2px 4px;
            color: #0f172a;
            font-weight: 500;
        }
        .check-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .check-sq {
            width: 12px;
            height: 12px;
            border: 1.2px solid #0f172a;
            display: inline-block;
            background: #fff;
            flex-shrink: 0;
        }

        /* Undertaking Box */
        .undertaking-box {
            border: 1px solid #94a3b8;
            background: #f8fafc;
            padding: 3px 6px;
            font-size: 9px;
            line-height: 1.25;
            margin-top: 2.5px;
            color: #0f172a;
            overflow: hidden;
        }
        .undertaking-urdu {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.35;
            direction: rtl;
            text-align: right;
            margin-top: 2px;
            color: #000000;
            font-weight: 600;
        }

        /* 4-Tier Signatures */
        .signatures-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 8px;
            margin-top: 7px;
            padding-top: 3px;
        }
        .sig-block {
            text-align: center;
            border-top: 1.2px solid #0f172a;
            padding-top: 3px;
        }
        .sig-title {
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            color: #000000;
        }
        .sig-sub {
            font-size: 8.5px;
            color: #334155;
            line-height: 1.2;
            font-weight: 500;
        }

        /* Live Editable Hover & Focus State */
        [contenteditable="true"] {
            transition: background 0.15s ease, outline 0.15s ease;
            cursor: text;
        }
        [contenteditable="true"]:hover {
            outline: 1px dashed #3b82f6;
            background-color: rgba(59, 130, 246, 0.05);
        }
        [contenteditable="true"]:focus {
            outline: 1.5px solid #2563eb;
            background-color: #ffffff;
        }

        /* Quick Add Preset Chips & Drawer styles */
        .preset-chip {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            padding: 3px 9px;
            font-size: 0.72rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .preset-chip:hover {
            background: #e0e7ff;
            color: #3730a3;
            border-color: #a5b4fc;
        }
        .custom-field-row-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 7px 10px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }
        .custom-field-badge {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #1e293b;
            white-space: nowrap;
        }

        /* Print Media Query - Strict 1-Page A4 Guarantee */
        @media print {
            .no-print-bar, .modal, .modal-backdrop, .offcanvas, .offcanvas-backdrop {
                display: none !important;
            }
            html, body {
                background: #ffffff !important;
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif !important;
                font-size: 11px !important;
                color: #000000 !important;
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .a4-sheet-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
            }
            .sheet-inner-border {
                border: 1.2px solid #000000 !important;
                padding: 5px 8px !important;
                page-break-inside: avoid !important;
            }
            .form-header-grid {
                border-bottom: 1.5px solid #000000 !important;
                margin-bottom: 4px !important;
                padding-bottom: 4px !important;
            }
            .school-title {
                color: #000000 !important;
                font-size: 20px !important;
            }
            .school-campus {
                color: #000000 !important;
                font-size: 11.5px !important;
            }
            .school-meta {
                color: #000000 !important;
                font-size: 9.5px !important;
            }
            .form-title-strip {
                background: #f1f5f9 !important;
                border: 1.5px solid #000000 !important;
                color: #000000 !important;
                font-size: 11.5px !important;
            }
            .section-header {
                background: #e2e8f0 !important;
                border-left: 3.5px solid #000000 !important;
                border-top: 1px solid #475569 !important;
                border-bottom: 1px solid #475569 !important;
                color: #000000 !important;
                font-size: 10.5px !important;
                padding: 2px 6px !important;
            }
            .form-table td, .form-table th {
                border-color: #64748b !important;
                color: #000000 !important;
                font-size: 10.5px !important;
                padding: 2px 5px !important;
            }
            .form-table .label-cell {
                background-color: #f1f5f9 !important;
                color: #000000 !important;
                font-weight: 700 !important;
            }
            .form-table .val-cell {
                color: #000000 !important;
            }
            .val-text {
                color: #000000 !important;
            }
            .cnic-box {
                border-color: #000000 !important;
                color: #000000 !important;
            }
            .checklist-grid {
                font-size: 9.5px !important;
                color: #000000 !important;
            }
            .undertaking-box {
                border-color: #64748b !important;
                color: #000000 !important;
                font-size: 9px !important;
                padding: 2.5px 5px !important;
            }
            .undertaking-urdu {
                color: #000000 !important;
                font-size: 10.5px !important;
            }
            [contenteditable="true"] {
                outline: none !important;
                background: transparent !important;
            }
            .signatures-grid {
                margin-top: 12px !important;
            }
            .sig-block {
                border-top: 1.2px solid #000000 !important;
            }
            .sig-title {
                color: #000000 !important;
                font-size: 9.5px !important;
            }
            .sig-sub {
                color: #000000 !important;
                font-size: 8.5px !important;
            }
        }
    </style>
</head>
<body>

<!-- Non-printable Toolbar -->
<div class="no-print-bar d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <i class="fa fa-id-card text-warning fs-5"></i>
        <div>
            <strong class="text-white">Student Admission &amp; Enrolment Form (A4 Official)</strong>
            <span class="badge bg-success ms-2 font-monospace" style="font-size: 0.72rem;">
                <i class="fa fa-pen me-1"></i> Live Screen Editable
            </span>
            <div class="small text-white-50" style="font-size: 0.75rem;">
                <?php echo !$isBlank ? 'Student: ' . htmlspecialchars($student->name) . ' (' . htmlspecialchars($student->admission_no) . ')' : 'Official Blank Form. You can click &amp; edit any field directly or print.'; ?>
            </div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <button type="button" class="btn btn-warning btn-sm px-3 py-1 fw-bold text-dark shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#admissionFormDrawer" title="Open Easy Data Entry Form & Add Custom Fields">
            <i class="fa fa-file-signature me-1"></i> Form Data &amp; Custom Fields
        </button>
        <button type="button" class="btn btn-success btn-sm px-3 py-1 fw-bold shadow-sm" onclick="openAdmissionTokenModal()" title="Generate New Admission Fee Token / Slip">
            <i class="fa fa-ticket-alt me-1"></i> Fee Token / Challan
        </button>
        <button type="button" class="btn btn-outline-light btn-sm px-2 py-1" data-bs-toggle="modal" data-bs-target="#formSettingsModal" title="Customise Header & Settings">
            <i class="fa fa-cog me-1"></i> Form Settings
        </button>

        <?php if(!$isBlank): ?>
            <a href="<?php echo URLROOT; ?>/students/printAdmission?blank=1" class="btn btn-outline-light btn-sm px-2 py-1">
                <i class="fa fa-file me-1"></i> Blank Form
            </a>
            <a href="<?php echo URLROOT; ?>/students/profile/<?php echo (int)$student->id; ?>" class="btn btn-info btn-sm px-2 py-1 text-white">
                <i class="fa fa-user me-1"></i> Profile
            </a>
        <?php else: ?>
            <a href="<?php echo URLROOT; ?>/students/admission" class="btn btn-outline-light btn-sm px-2 py-1">
                <i class="fa fa-laptop-code me-1"></i> Online Entry
            </a>
        <?php endif; ?>

        <button onclick="window.print()" class="btn btn-primary btn-sm px-3 py-1 fw-bold shadow-sm">
            <i class="fa fa-print me-1"></i> Print A4 Form
        </button>
        <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-secondary btn-sm px-2 py-1">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Main Official A4 Document Sheet -->
<div class="a4-sheet-container" id="admissionSheet">
    <div class="sheet-inner-border">

        <!-- Top Header Grid: Logo Left, Clear School Info Center, Candidate Photo Right -->
        <div class="form-header-grid">
            <div class="school-logo-area">
                <?php if(!empty($schoolLogo)): ?>
                    <img src="<?php echo URLROOT . '/' . ltrim($schoolLogo, '/'); ?>" alt="School Logo" id="formSchoolLogo" class="school-logo" onerror="this.style.display='none'">
                <?php else: ?>
                    <div class="school-logo-placeholder" id="formSchoolLogoPlaceholder">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                <?php endif; ?>
            </div>

            <div class="school-info-area">
                <h1 class="school-title" id="formSchoolName" contenteditable="true" title="Click to edit school name"><?php echo htmlspecialchars($schoolName); ?></h1>
                <div class="school-campus" id="formCampusName" contenteditable="true" title="Click to edit campus"><i class="fa fa-building me-1"></i><?php echo htmlspecialchars($campusName); ?></div>
                <div class="school-meta">
                    <div><span id="formAffiliation" contenteditable="true"><?php echo htmlspecialchars($affiliation); ?></span></div>
                    <div><span id="formSchoolAddress" contenteditable="true"><i class="fa fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($schoolAddress); ?></span></div>
                    <div>
                        <span id="formSchoolPhone" contenteditable="true"><i class="fa fa-phone me-1"></i><?php echo htmlspecialchars($schoolPhone); ?></span> &nbsp;|&nbsp; 
                        <span id="formSchoolEmail" contenteditable="true"><i class="fa fa-envelope me-1"></i><?php echo htmlspecialchars($schoolEmail); ?></span>
                    </div>
                </div>
            </div>

            <!-- Candidate Photo Box -->
            <div class="candidate-photo-box" id="formPhotoBox">
                <div class="photo-label">Candidate Photo</div>
                <div class="photo-sub">Paste recent passport size photograph</div>
                <div class="stamp-mark">Seal</div>
            </div>
        </div>

        <!-- Form Title Banner (Light Clean Box, No Dark Black Fill) -->
        <div class="form-title-strip">
            <span class="en-title" id="formMainTitle" contenteditable="true">STUDENT ADMISSION &amp; ENROLMENT FORM</span>
            <span class="urdu-title" contenteditable="true">داخلہ فارم (طلبہ و طالبات)</span>
            <span class="session-title" id="formSessionTitle" contenteditable="true"><?php echo htmlspecialchars($sessionName); ?></span>
        </div>

        <!-- FOR OFFICE USE ONLY -->
        <div class="office-use-box">
            <div class="office-use-title">
                <span><i class="fa fa-clipboard-check me-1"></i>FOR OFFICE USE ONLY / برائے دفتری استعمال</span>
                <span>Category: <span id="cellCategory" contenteditable="true"><?php echo (!$isBlank && !empty($student->concession_type)) ? htmlspecialchars($student->concession_type) : 'Standard Enrolment'; ?></span></span>
            </div>
            <table class="form-table mb-0">
                <tr>
                    <td class="label-cell">Admission No.</td>
                    <td class="val-cell"><span class="val-text font-monospace" id="cellAdmNo" contenteditable="true"><?php echo $admNo ?: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?></span></td>
                    <td class="label-cell">Admission Date</td>
                    <td class="val-cell"><span class="val-text" id="cellAdmDate" contenteditable="true"><?php echo $admDate ?: date('d-M-Y'); ?></span></td>
                    <td class="label-cell">Class Admitted</td>
                    <td class="val-cell"><span class="val-text" id="cellClassName" contenteditable="true"><?php echo $className ?: '______________'; ?></span></td>
                    <td class="label-cell">Section</td>
                    <td class="val-cell"><span class="val-text" id="cellSecName" contenteditable="true"><?php echo $secName ?: '______'; ?></span></td>
                    <td class="label-cell">Roll No.</td>
                    <td class="val-cell"><span class="val-text font-monospace" id="cellRollNo" contenteditable="true"><?php echo $rollNo ?: '______'; ?></span></td>
                </tr>
            </table>
        </div>

        <!-- SECTION A: STUDENT PERSONAL PARTICULARS -->
        <div class="section-header">
            <span>A. STUDENT PERSONAL PARTICULARS</span>
            <span class="urdu-label">الف۔ طالب علم کے ذاتی کوائف</span>
        </div>
        <table class="form-table">
            <tr>
                <td class="label-cell">Full Name (Block Letters)</td>
                <td class="val-cell" colspan="3">
                    <span class="val-text text-uppercase" id="cellStudentName" contenteditable="true" style="letter-spacing: 0.5px; font-size: 11.5px; font-weight: 700;">
                        <?php echo !$isBlank ? htmlspecialchars($student->name ?? '') : '&nbsp;'; ?>
                    </span>
                </td>
                <td class="label-cell">Gender / جنس</td>
                <td class="val-cell">
                    <?php 
                    $genderLower = strtolower(trim($student->gender ?? ''));
                    $isMale = ($genderLower === 'male' || $genderLower === 'm');
                    $isFemale = ($genderLower === 'female' || $genderLower === 'f');
                    ?>
                    <span class="val-text d-inline-flex align-items-center gap-3" id="cellGender" contenteditable="true">
                        <span class="d-inline-flex align-items-center gap-1">
                            <span class="check-sq text-center fw-bold" id="checkGenderMale" style="line-height: 11px; font-size: 10px;"><?php echo (!$isBlank && $isMale) ? '&#10003;' : ''; ?></span> 
                            <span>Male (لڑکا)</span>
                        </span>
                        <span class="d-inline-flex align-items-center gap-1">
                            <span class="check-sq text-center fw-bold" id="checkGenderFemale" style="line-height: 11px; font-size: 10px;"><?php echo (!$isBlank && $isFemale) ? '&#10003;' : ''; ?></span> 
                            <span>Female (لڑکی)</span>
                        </span>
                    </span>
                </td>
            </tr>
            <tr>
                <td class="label-cell">Form-B / B-Form CNIC #</td>
                <td class="val-cell" colspan="3">
                    <?php echo renderCnicBoxes($student->bform_cnic ?? '', $isBlank, 'studentCnicBoxes'); ?>
                </td>
                <td class="label-cell">Date of Birth / تاریخ پیدائش</td>
                <td class="val-cell">
                    <?php
                    $dobDay = ''; $dobMonth = ''; $dobYear = '';
                    if (!$isBlank && !empty($student->dob)) {
                        $dobTime = strtotime($student->dob);
                        $dobDay = date('d', $dobTime);
                        $dobMonth = date('m', $dobTime);
                        $dobYear = date('Y', $dobTime);
                    }
                    ?>
                    <div class="d-inline-flex align-items-center" id="cellDob" contenteditable="true">
                        <span class="cnic-box" title="Day"><?php echo $dobDay ? $dobDay[0] : '&nbsp;'; ?></span><span class="cnic-box" title="Day"><?php echo $dobDay ? $dobDay[1] : '&nbsp;'; ?></span>
                        <span class="cnic-separator">/</span>
                        <span class="cnic-box" title="Month"><?php echo $dobMonth ? $dobMonth[0] : '&nbsp;'; ?></span><span class="cnic-box" title="Month"><?php echo $dobMonth ? $dobMonth[1] : '&nbsp;'; ?></span>
                        <span class="cnic-separator">/</span>
                        <span class="cnic-box" title="Year"><?php echo $dobYear ? $dobYear[0] : '&nbsp;'; ?></span><span class="cnic-box" title="Year"><?php echo $dobYear ? $dobYear[1] : '&nbsp;'; ?></span><span class="cnic-box" title="Year"><?php echo $dobYear ? $dobYear[2] : '&nbsp;'; ?></span><span class="cnic-box" title="Year"><?php echo $dobYear ? $dobYear[3] : '&nbsp;'; ?></span>
                        <span class="text-muted ms-1" style="font-size: 8.5px; font-weight: normal;">(D/M/Y)</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="label-cell">Blood Group</td>
                <td class="val-cell"><span class="val-text" id="cellBloodGroup" contenteditable="true"><?php echo (!$isBlank && !empty($student->blood_group)) ? htmlspecialchars($student->blood_group) : '__________'; ?></span></td>
                <td class="label-cell">Religion / مذہب</td>
                <td class="val-cell"><span class="val-text" id="cellReligion" contenteditable="true"><?php echo (!$isBlank && !empty($student->religion)) ? htmlspecialchars($student->religion) : 'Islam (مسلمان)'; ?></span></td>
                <td class="label-cell">Nationality</td>
                <td class="val-cell"><span class="val-text" id="cellNationality" contenteditable="true"><?php echo (!$isBlank && !empty($student->nationality)) ? htmlspecialchars($student->nationality) : 'Pakistani'; ?></span></td>
            </tr>
            <tr>
                <td class="label-cell">Mother Tongue / زبان</td>
                <td class="val-cell"><span class="val-text" id="cellMotherTongue" contenteditable="true"><?php echo (!$isBlank && !empty($student->mother_tongue)) ? htmlspecialchars($student->mother_tongue) : 'Urdu (اردو)'; ?></span></td>
                <td class="label-cell">Student Portal Email</td>
                <td class="val-cell" colspan="3"><span class="val-text font-monospace" id="cellEmail" contenteditable="true"><?php echo (!$isBlank && !empty($student->email)) ? htmlspecialchars($student->email) : '___________________________________'; ?></span></td>
            </tr>
        </table>

        <!-- SECTION B: PARENTS & GUARDIAN PARTICULARS -->
        <div class="section-header">
            <span>B. PARENTS &amp; GUARDIAN PARTICULARS</span>
            <span class="urdu-label">ب۔ والدین / سرپرست کے کوائف</span>
        </div>
        <table class="form-table">
            <tr>
                <td class="label-cell">Father's Full Name</td>
                <td class="val-cell" colspan="3"><span class="val-text text-uppercase" id="cellFatherName" contenteditable="true"><?php echo (!$isBlank && !empty($student->father_name)) ? htmlspecialchars($student->father_name) : '&nbsp;'; ?></span></td>
                <td class="label-cell">Father WhatsApp / Mobile</td>
                <td class="val-cell">
                    <?php 
                    $fPhone = '';
                    if (!$isBlank && !empty($student)) {
                        if (!empty($student->father_phone)) {
                            $fPhone = $student->father_phone;
                        } elseif (!empty($student->parent_phone)) {
                            $fPhone = $student->parent_phone;
                        } elseif (!empty($student->phone)) {
                            $fPhone = $student->phone;
                        }
                    }
                    ?>
                    <span class="val-text font-monospace" id="cellFatherPhone" contenteditable="true"><?php echo $fPhone ? htmlspecialchars($fPhone) : '03__-_______'; ?></span>
                </td>
            </tr>
            <tr>
                <td class="label-cell">Father's NADRA CNIC</td>
                <td class="val-cell" colspan="3">
                    <?php echo renderCnicBoxes($student->father_cnic ?? '', $isBlank, 'fatherCnicBoxes'); ?>
                </td>
                <td class="label-cell">Father's Profession / پیشہ</td>
                <td class="val-cell"><span class="val-text" id="cellFatherOccupation" contenteditable="true"><?php echo (!$isBlank && !empty($student->father_occupation)) ? htmlspecialchars($student->father_occupation) : '_______________________'; ?></span></td>
            </tr>
            <tr>
                <td class="label-cell">Mother's Full Name</td>
                <td class="val-cell" colspan="3"><span class="val-text text-uppercase" id="cellMotherName" contenteditable="true"><?php echo (!$isBlank && !empty($student->mother_name)) ? htmlspecialchars($student->mother_name) : '&nbsp;'; ?></span></td>
                <td class="label-cell">Mother's Profession</td>
                <td class="val-cell"><span class="val-text" id="cellMotherOccupation" contenteditable="true"><?php echo (!$isBlank && !empty($student->mother_occupation)) ? htmlspecialchars($student->mother_occupation) : 'Housewife'; ?></span></td>
            </tr>
            <tr>
                <td class="label-cell">Mother's NADRA CNIC</td>
                <td class="val-cell" colspan="3">
                    <?php echo renderCnicBoxes($student->mother_cnic ?? '', $isBlank, 'motherCnicBoxes'); ?>
                </td>
                <td class="label-cell">Emergency Contact / رابطہ</td>
                <td class="val-cell"><span class="val-text font-monospace" id="cellEmergencyContact" contenteditable="true"><?php echo (!$isBlank && !empty($student->emergency_contact)) ? htmlspecialchars($student->emergency_contact) : '03__-_______'; ?></span></td>
            </tr>
        </table>

        <!-- SECTION C: RESIDENTIAL ADDRESS & LOCATION -->
        <div class="section-header">
            <span>C. RESIDENTIAL ADDRESS &amp; LOCATION</span>
            <span class="urdu-label">ج۔ رہائشی پتہ اور رابطہ</span>
        </div>
        <table class="form-table">
            <tr>
                <td class="label-cell">Present Residential Address</td>
                <td class="val-cell" colspan="5"><span class="val-text" id="cellAddress" contenteditable="true"><?php echo (!$isBlank && !empty($student->address)) ? htmlspecialchars($student->address) : 'House #, Street #, Mohallah / Sector / Colony, City: _____________________________________'; ?></span></td>
            </tr>
            <tr>
                <td class="label-cell">Permanent Home Address</td>
                <td class="val-cell" colspan="5"><span class="val-text" id="cellPermAddress" contenteditable="true"><?php echo (!$isBlank && !empty($student->permanent_address)) ? htmlspecialchars($student->permanent_address) : 'Village / Town / Tehsil / District: _____________________________________________________'; ?></span></td>
            </tr>
            <tr>
                <td class="label-cell">City / شہر</td>
                <td class="val-cell"><span class="val-text" id="cellCity" contenteditable="true"><?php echo (!$isBlank && !empty($student->city)) ? htmlspecialchars($student->city) : '____________'; ?></span></td>
                <td class="label-cell">District / ضلع</td>
                <td class="val-cell"><span class="val-text" id="cellDistrict" contenteditable="true"><?php echo (!$isBlank && !empty($student->district)) ? htmlspecialchars($student->district) : '____________'; ?></span></td>
                <td class="label-cell">Tehsil / تحصیل</td>
                <td class="val-cell"><span class="val-text" id="cellTehsil" contenteditable="true"><?php echo (!$isBlank && !empty($student->tehsil)) ? htmlspecialchars($student->tehsil) : '____________'; ?></span></td>
            </tr>
        </table>

        <!-- SECTION D: PREVIOUS ACADEMIC RECORD / TRANSFER -->
        <div class="section-header">
            <span>D. PREVIOUS ACADEMIC RECORD &amp; TRANSFER CREDENTIALS</span>
            <span class="urdu-label">د۔ سابقہ تعلیمی ادارہ اور تعلیمی ریکارڈ</span>
        </div>
        <?php 
        $isFresh = $isBlank ? false : (isset($student->is_fresh_admission) ? (int)$student->is_fresh_admission : 1);
        ?>
        <table class="form-table mb-1">
            <tr>
                <td class="label-cell" style="width: 17%;">Admission Category</td>
                <td class="val-cell" colspan="5" contenteditable="true" id="cellAdmType">
                    <span class="d-inline-flex align-items-center gap-4">
                        <span class="d-inline-flex align-items-center gap-1">
                            <span class="check-sq text-center fw-bold" style="line-height: 11px; font-size: 10px;"><?php echo (!$isBlank && $isFresh) ? '&#10003;' : ''; ?></span> 
                            <strong>Fresh Admission (نیا داخلہ)</strong>
                        </span>
                        <span class="d-inline-flex align-items-center gap-1">
                            <span class="check-sq text-center fw-bold" style="line-height: 11px; font-size: 10px;"><?php echo (!$isBlank && !$isFresh) ? '&#10003;' : ''; ?></span> 
                            <strong>Transfer with SLC / TC (منتقلی بذریعہ سرٹیفکیٹ)</strong>
                        </span>
                    </span>
                </td>
            </tr>
        </table>
        <table class="form-table text-center mb-1">
            <thead style="background-color: #f1f5f9; font-weight: 700;">
                <tr>
                    <th style="width: 15%; padding: 2px 4px;">Class / Exam<br><span style="font-size: 9px; font-weight: 600;">سابقہ جماعت</span></th>
                    <th style="width: 33%; padding: 2px 4px;">School / Institute Name<br><span style="font-size: 9px; font-weight: 600;">ادارہ کا نام</span></th>
                    <th style="width: 16%; padding: 2px 4px;">Board / City<br><span style="font-size: 9px; font-weight: 600;">بورڈ / شہر</span></th>
                    <th style="width: 12%; padding: 2px 4px;">SLC / Roll #<br><span style="font-size: 9px; font-weight: 600;">سرٹیفکیٹ نمبر</span></th>
                    <th style="width: 12%; padding: 2px 4px;">Total Marks<br><span style="font-size: 9px; font-weight: 600;">کل نمبر</span></th>
                    <th style="width: 12%; padding: 2px 4px;">Obt. Marks / %<br><span style="font-size: 9px; font-weight: 600;">حاصل کردہ / فیصد</span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="val-cell" style="height: 22px;"><span class="val-text" id="cellPrevClass" contenteditable="true"><?php echo (!$isBlank && !empty($student->prev_class)) ? htmlspecialchars($student->prev_class) : ''; ?></span></td>
                    <td class="val-cell text-start"><span class="val-text" id="cellPrevSchool" contenteditable="true"><?php echo (!$isBlank && !empty($student->prev_school_name)) ? htmlspecialchars($student->prev_school_name) : ''; ?></span></td>
                    <td class="val-cell"><span class="val-text" id="cellPrevCity" contenteditable="true"><?php echo (!$isBlank && !empty($student->prev_school_city)) ? htmlspecialchars($student->prev_school_city) : ''; ?></span></td>
                    <td class="val-cell"><span class="val-text font-monospace" id="cellSlcNo" contenteditable="true"><?php echo (!$isBlank && !empty($student->slc_number)) ? htmlspecialchars($student->slc_number) : ''; ?></span></td>
                    <td class="val-cell"><span class="val-text" id="cellPrevTotal" contenteditable="true"><?php echo (!$isBlank && !empty($student->prev_total_marks)) ? htmlspecialchars($student->prev_total_marks) : ''; ?></span></td>
                    <td class="val-cell"><span class="val-text" id="cellPrevMarks" contenteditable="true"><?php echo (!$isBlank && !empty($student->prev_marks_obtained)) ? htmlspecialchars($student->prev_marks_obtained) : ''; ?></span></td>
                </tr>
                <tr>
                    <td class="val-cell" style="height: 20px;"><span class="val-text" contenteditable="true"></span></td>
                    <td class="val-cell text-start"><span class="val-text" contenteditable="true"></span></td>
                    <td class="val-cell"><span class="val-text" contenteditable="true"></span></td>
                    <td class="val-cell"><span class="val-text font-monospace" contenteditable="true"></span></td>
                    <td class="val-cell"><span class="val-text" contenteditable="true"></span></td>
                    <td class="val-cell"><span class="val-text" contenteditable="true"></span></td>
                </tr>
            </tbody>
        </table>

        <!-- SECTION: ADDITIONAL CUSTOM PARTICULARS (DYNAMICALLY ADDED) -->
        <div id="customFieldsSection" style="display: none; margin-top: 3px;">
            <div class="section-header" style="background: #eef2ff; border-left-color: #6366f1;">
                <span id="customSectionTitle"><i class="fa fa-plus-circle text-primary me-1"></i>ADDITIONAL PARTICULARS &amp; SPECIAL DETAILS</span>
                <span class="urdu-label">اضافی / خصوصی کوائف</span>
            </div>
            <table class="form-table" id="customFieldsTable">
                <tbody id="customFieldsTableBody">
                    <!-- Dynamic rows inserted here -->
                </tbody>
            </table>
        </div>

        <!-- SECTION E: VERIFICATION DOCUMENT CHECKLIST -->
        <div class="section-header">
            <span>E. MANDATORY DOCUMENT CHECKLIST</span>
            <span class="urdu-label">ر۔ ضروری دستاویزات</span>
        </div>
        <div class="checklist-grid" contenteditable="true">
            <div class="check-item"><span class="check-sq"></span> Attested Copy of NADRA Form-B / Child Birth Certificate</div>
            <div class="check-item"><span class="check-sq"></span> Attested Copy of Father's / Guardian's NADRA CNIC</div>
            <div class="check-item"><span class="check-sq"></span> Original School Leaving Certificate (SLC / TC) if transferring</div>
            <div class="check-item"><span class="check-sq"></span> Previous School Result Card / Marksheet</div>
            <div class="check-item"><span class="check-sq"></span> 04 Recent Passport-size Photographs (Sky Blue Background)</div>
            <div class="check-item"><span class="check-sq"></span> Paid Enrolment / Admission Fee Challan Voucher</div>
        </div>

        <!-- SECTION F: PARENT SOLEMN UNDERTAKING -->
        <div class="undertaking-box" contenteditable="true">
            <strong>DECLARATION BY PARENT / GUARDIAN (اقرار نامہ والد / سرپرست):</strong> 
            I solemnly affirm that the details given above are true and correct. I undertake that my child/ward will strictly abide by all institutional rules, discipline, uniform code, and fee payment schedules.
            <div class="undertaking-urdu">
                میں حلفیہ اقرار کرتا/کرتی ہوں کہ اس فارم میں درج تمام کوائف درست ہیں۔ میرا بچہ سکول کے تمام قواعد و ضوابط، نظم و ضبط اور مقررہ فیس کی بر وقت ادائیگی کا پابند رہے گا۔
            </div>
        </div>

        <!-- SECTION G: ADMISSION COMMITTEE & SECTION APPROVAL -->
        <div class="section-header">
            <span>G. ADMISSION COMMITTEE &amp; SECTION APPROVAL</span>
            <span class="urdu-label">ز۔ منظوری شعبہ داخلہ و مجاز دستخط</span>
        </div>
        <div class="office-use-box py-1 px-2 mb-1" style="background: #fafafa; border: 1px solid #94a3b8;">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="font-size: 9.5px; font-weight: 600;">
                <span contenteditable="true"><strong>Allotted Class:</strong> ________ &nbsp;&nbsp; <strong>Section:</strong> ____ &nbsp;&nbsp; <strong>Challan #:</strong> <span id="cellChallanNo">________</span></span>
                <a href="javascript:void(0)" onclick="openAdmissionTokenModal()" class="badge bg-primary text-white text-decoration-none no-print ms-1" style="font-size: 8px; vertical-align: middle;"><i class="fa fa-ticket-alt me-1"></i>Token</a>
            </div>
        </div>
        <div class="signatures-grid" style="margin-top: 6px;">
            <div class="sig-block">
                <div class="sig-title">Parent / Guardian</div>
                <div class="sig-sub">Signature &amp; Thumb Impression<br>دستخط والد / سرپرست</div>
            </div>
            <div class="sig-block">
                <div class="sig-title">Admission Incharge</div>
                <div class="sig-sub">Scrutiny &amp; Verification<br>انچارج شعبہ داخلہ</div>
            </div>
            <div class="sig-block">
                <div class="sig-title">Accounts Officer</div>
                <div class="sig-sub">Fee Clearance &amp; Receipt<br>شعبہ اکاؤنٹس کلیئرنس</div>
            </div>
            <div class="sig-block">
                <div class="sig-title">Principal / Headmaster</div>
                <div class="sig-sub">Official Signature &amp; Seal<br>پرنسپل حتمی مہر و دستخط</div>
            </div>
        </div>

    </div>
</div>

<!-- OFFCANVAS: FULL FORM DATA ENTRY & DYNAMIC CUSTOM FIELDS BUILDER -->
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="admissionFormDrawer" aria-labelledby="admissionFormDrawerLabel" style="width: 600px; max-width: 96vw;">
    <div class="offcanvas-header bg-dark text-white py-3 px-4 border-bottom">
        <div>
            <h5 class="offcanvas-title fw-bold mb-0" id="admissionFormDrawerLabel">
                <i class="fa fa-file-signature text-warning me-2"></i>Admission Form Data &amp; Custom Fields
            </h5>
            <div class="text-white-50 small mt-1" style="font-size: 0.76rem;">
                فارم کوائف اور نئی فیلڈز یہاں سے آسانی سے درج کریں - پرنٹ شیٹ لائیو اپڈیٹ ہوگی
            </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <!-- Nav Tabs inside Drawer -->
    <div class="bg-white border-bottom px-3 pt-2">
        <ul class="nav nav-tabs border-bottom-0" id="drawerTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold small text-primary" id="tab-custom-fields" data-bs-toggle="tab" data-bs-target="#pane-custom-fields" type="button" role="tab">
                    <i class="fa fa-plus-circle text-danger me-1"></i>➕ Add Custom Fields (نئی فیلڈز)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold small text-dark" id="tab-form-data" data-bs-toggle="tab" data-bs-target="#pane-form-data" type="button" role="tab">
                    <i class="fa fa-user-edit text-success me-1"></i>Student Details (کوائف)
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold small text-dark" id="tab-header-settings" data-bs-toggle="tab" data-bs-target="#pane-header-settings" type="button" role="tab">
                    <i class="fa fa-school text-info me-1"></i>School Info (سکول ہیڈر)
                </button>
            </li>
        </ul>
    </div>

    <div class="offcanvas-body p-3 bg-light" style="overflow-y: auto;">
        <div class="tab-content" id="drawerTabContent">

            <!-- TAB 1: ➕ ADD CUSTOM PARTICULARS (HIGHLIGHTED USER FEATURE) -->
            <div class="tab-pane fade show active" id="pane-custom-fields" role="tabpanel">
                <div class="card border-primary shadow-sm mb-3">
                    <div class="card-header bg-primary text-white py-2 px-3 d-flex justify-content-between align-items-center">
                        <strong class="small"><i class="fa fa-layer-group me-1"></i> Add New Field / نئی فیلڈ یا معلومات شامل کریں</strong>
                        <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.7rem;">Direct on A4 Print</span>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-muted small mb-2" style="font-size: 0.8rem; line-height: 1.35;">
                            Agar aapko admission form par koi aisi information add karni hai jo default form mein nahi hai (jaise Transport Route, Hostel Room, Fee Concession, Emergency Contact, Guardian Info, Medical Alert waghera), to yahan se add karein. Yeh form pe khubsoorat table section mein add ho jayegi!
                        </p>

                        <!-- Presets Quick Buttons -->
                        <div class="mb-3">
                            <label class="form-label text-uppercase fw-bold text-muted small" style="font-size: 0.72rem;">Quick Presets (ایک کلک سے شامل کریں):</label>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="preset-chip" onclick="setCustomPreset('Transport Route', 'Bus / Van # 5 - Saddar Route')">
                                    <i class="fa fa-bus me-1 text-primary"></i> Transport Route
                                </button>
                                <button type="button" class="preset-chip" onclick="setCustomPreset('Fee Concession', 'Kinship / Merit 25% Discount')">
                                    <i class="fa fa-percent me-1 text-success"></i> Fee Concession
                                </button>
                                <button type="button" class="preset-chip" onclick="setCustomPreset('Emergency Contact', 'Ch. Tariq (Uncle): 0300-1234567')">
                                    <i class="fa fa-phone-volume me-1 text-danger"></i> Emergency Contact
                                </button>
                                <button type="button" class="preset-chip" onclick="setCustomPreset('Hostel / Boarding', 'Room # 14 - Jinnah Hostel')">
                                    <i class="fa fa-bed me-1 text-secondary"></i> Hostel Room
                                </button>
                                <button type="button" class="preset-chip" onclick="setCustomPreset('Guardian Relation', 'Uncle / Maternal Guardian')">
                                    <i class="fa fa-user-shield me-1 text-info"></i> Guardian Relation
                                </button>
                                <button type="button" class="preset-chip" onclick="setCustomPreset('Medical / Allergy', 'Asthma / Dust Allergy')">
                                    <i class="fa fa-heartbeat me-1 text-warning"></i> Medical Alert
                                </button>
                            </div>
                        </div>

                        <!-- Add Field Form -->
                        <div class="row g-2 mb-2">
                            <div class="col-12 col-md-5">
                                <label class="form-label small fw-bold text-dark mb-1">Field Name / Label (عنوان)</label>
                                <input type="text" id="inputCustomLabel" class="form-control form-control-sm fw-semibold" placeholder="e.g. Transport Route">
                            </div>
                            <div class="col-12 col-md-7">
                                <label class="form-label small fw-bold text-dark mb-1">Field Value / تفصیل</label>
                                <input type="text" id="inputCustomValue" class="form-control form-control-sm" placeholder="e.g. Route # 4 - Saddar Stop">
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm py-2" onclick="addCustomFieldFromDrawer()">
                            <i class="fa fa-plus-circle me-1"></i> Add This Field to Form / فارم پر شامل کریں
                        </button>
                    </div>
                </div>

                <!-- List of Active Custom Fields on Sheet -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                        <strong class="small text-dark"><i class="fa fa-list-check text-primary me-1"></i> Active Custom Fields on Form (<span id="customFieldsCount">0</span>)</strong>
                        <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0 small" onclick="clearAllCustomFields()" id="btnClearAllCustom" style="display: none;">
                            <i class="fa fa-trash me-1"></i> Clear All
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div id="customFieldsDrawerList">
                            <div class="text-center text-muted small py-3" id="noCustomFieldsMsg">
                                <i class="fa fa-info-circle text-muted fs-5 mb-1 d-block"></i>
                                Abhi koi custom field add nahi ki gayi.<br>Upar se field name aur value daal kar "+ Add" dabayein.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: 📋 COMPLETE STUDENT & ENROLMENT FORM -->
            <div class="tab-pane fade" id="pane-form-data" role="tabpanel">

                <!-- 1. Enrolment & Office Use -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white py-2 px-3">
                        <strong class="small text-primary"><i class="fa fa-id-badge me-1"></i> 1. Enrolment &amp; Office Use (داخلہ ریکارڈ)</strong>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Admission No.</label>
                                <input type="text" id="inputAdmNo" class="form-control form-control-sm font-monospace fw-bold" value="<?php echo htmlspecialchars($admNo); ?>" placeholder="e.g. ADM-2026-001">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Admission Date</label>
                                <input type="text" id="inputAdmDate" class="form-control form-control-sm" value="<?php echo htmlspecialchars($admDate ?: date('d-M-Y')); ?>" placeholder="DD-Mon-YYYY">
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Class Admitted</label>
                                <select id="selectClass" class="form-select form-select-sm" onchange="handleClassChange(this)">
                                    <option value="">-- Select Class --</option>
                                    <?php if(!empty($classes)): ?>
                                        <?php foreach($classes as $c): ?>
                                            <option value="<?php echo htmlspecialchars($c->class_name); ?>" <?php echo (!$isBlank && !empty($student->class_name) && $student->class_name == $c->class_name) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($c->class_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <option value="__custom__">✏️ Other / Custom Class...</option>
                                </select>
                                <input type="text" id="inputClassName" class="form-control form-control-sm mt-1" style="display: none;" value="<?php echo htmlspecialchars($className); ?>" placeholder="Type Class Name">
                            </div>
                            <div class="col-3">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Section</label>
                                <input type="text" id="inputSecName" class="form-control form-control-sm" value="<?php echo htmlspecialchars($secName); ?>" placeholder="e.g. A">
                            </div>
                            <div class="col-3">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Roll No.</label>
                                <input type="text" id="inputRollNo" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($rollNo); ?>" placeholder="e.g. 01">
                            </div>
                        </div>
                        <div class="mb-1">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Category / Concession</label>
                            <input type="text" id="inputCategory" class="form-control form-control-sm" value="<?php echo (!$isBlank && !empty($student->concession_type)) ? htmlspecialchars($student->concession_type) : 'Standard Enrolment'; ?>" placeholder="Standard Enrolment / Concession">
                        </div>
                    </div>
                </div>

                <!-- 2. Student Personal Particulars -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white py-2 px-3">
                        <strong class="small text-primary"><i class="fa fa-user me-1"></i> 2. Student Personal Details (طالب علم کے ذاتی کوائف)</strong>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Full Name (Block Letters)</label>
                            <input type="text" id="inputStudentName" class="form-control form-control-sm text-uppercase fw-bold" value="<?php echo htmlspecialchars($student->name ?? ''); ?>" placeholder="STUDENT FULL NAME">
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Gender</label>
                                <select id="inputGender" class="form-select form-select-sm">
                                    <option value="Male" <?php echo (!$isBlank && ($student->gender ?? '') == 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (!$isBlank && ($student->gender ?? '') == 'Female') ? 'selected' : ''; ?>>Female</option>
                                    <option value="Other" <?php echo (!$isBlank && ($student->gender ?? '') == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Date of Birth</label>
                                <input type="text" id="inputDob" class="form-control form-control-sm" value="<?php echo (!$isBlank && !empty($student->dob)) ? date('d-M-Y', strtotime($student->dob)) : ''; ?>" placeholder="DD-Mon-YYYY">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Form-B / CNIC # (13 Digits)</label>
                            <input type="text" id="inputStudentCnic" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($student->bform_cnic ?? ''); ?>" placeholder="e.g. 37405-1234567-1" maxlength="15">
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Blood Group</label>
                                <select id="inputBloodGroup" class="form-select form-select-sm">
                                    <option value="">--</option>
                                    <?php foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg): ?>
                                        <option value="<?php echo $bg; ?>" <?php echo (!$isBlank && ($student->blood_group ?? '') == $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Religion</label>
                                <input type="text" id="inputReligion" class="form-control form-control-sm" value="<?php echo (!$isBlank && !empty($student->religion)) ? htmlspecialchars($student->religion) : 'Islam (مسلمان)'; ?>">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Nationality</label>
                                <input type="text" id="inputNationality" class="form-control form-control-sm" value="<?php echo (!$isBlank && !empty($student->nationality)) ? htmlspecialchars($student->nationality) : 'Pakistani'; ?>">
                            </div>
                        </div>
                        <div class="row g-2 mb-1">
                            <div class="col-5">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Mother Tongue</label>
                                <input type="text" id="inputMotherTongue" class="form-control form-control-sm" value="<?php echo (!$isBlank && !empty($student->mother_tongue)) ? htmlspecialchars($student->mother_tongue) : 'Urdu (اردو)'; ?>">
                            </div>
                            <div class="col-7">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Student Email</label>
                                <input type="text" id="inputStudentEmail" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($student->email ?? ''); ?>" placeholder="student@school.edu.pk">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Parents & Guardian Particulars -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white py-2 px-3">
                        <strong class="small text-primary"><i class="fa fa-users me-1"></i> 3. Parents &amp; Guardian (والدین / سرپرست)</strong>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2 mb-2">
                            <div class="col-7">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Father's Name</label>
                                <input type="text" id="inputFatherName" class="form-control form-control-sm text-uppercase fw-semibold" value="<?php echo htmlspecialchars($student->father_name ?? ''); ?>" placeholder="FATHER FULL NAME">
                            </div>
                            <div class="col-5">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Father Phone / WA</label>
                                <input type="text" id="inputFatherPhone" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($student->father_phone ?: ($student->parent_phone ?? '')); ?>" placeholder="0300-1234567">
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Father's NADRA CNIC</label>
                                <input type="text" id="inputFatherCnic" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($student->father_cnic ?? ''); ?>" placeholder="37405-1234567-1" maxlength="15">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Father's Profession / Job</label>
                                <input type="text" id="inputFatherOccupation" class="form-control form-control-sm" value="<?php echo htmlspecialchars($student->father_occupation ?? ''); ?>" placeholder="e.g. Businessman / Govt. Officer">
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-7">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Mother's Name</label>
                                <input type="text" id="inputMotherName" class="form-control form-control-sm text-uppercase" value="<?php echo htmlspecialchars($student->mother_name ?? ''); ?>" placeholder="MOTHER FULL NAME">
                            </div>
                            <div class="col-5">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Mother Profession</label>
                                <input type="text" id="inputMotherOccupation" class="form-control form-control-sm" value="<?php echo (!$isBlank && !empty($student->mother_occupation)) ? htmlspecialchars($student->mother_occupation) : 'Housewife'; ?>" placeholder="Housewife / Teacher">
                            </div>
                        </div>
                        <div class="row g-2 mb-1">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Mother's NADRA CNIC</label>
                                <input type="text" id="inputMotherCnic" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($student->mother_cnic ?? ''); ?>" placeholder="37405-1234567-2" maxlength="15">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Emergency Contact / رابطہ</label>
                                <input type="text" id="inputEmergencyContact" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($student->emergency_contact ?? ''); ?>" placeholder="0300-1234567">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Residential Address & Previous School -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white py-2 px-3">
                        <strong class="small text-primary"><i class="fa fa-map-marked-alt me-1"></i> 4. Address &amp; Previous School (رہائش اور سابقہ سکول)</strong>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Present Residential Address</label>
                            <input type="text" id="inputAddress" class="form-control form-control-sm" value="<?php echo htmlspecialchars($student->address ?? ''); ?>" placeholder="House #, Street #, Mohallah / Colony">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Permanent Home Address</label>
                            <input type="text" id="inputPermAddress" class="form-control form-control-sm" value="<?php echo htmlspecialchars($student->permanent_address ?? ''); ?>" placeholder="Village / Town / District">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">City</label>
                                <input type="text" id="inputCity" class="form-control form-control-sm" value="<?php echo htmlspecialchars($student->city ?? ''); ?>" placeholder="e.g. Islamabad">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">District</label>
                                <input type="text" id="inputDistrict" class="form-control form-control-sm" value="<?php echo htmlspecialchars($student->district ?? ''); ?>" placeholder="e.g. Rawalpindi">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Tehsil</label>
                                <input type="text" id="inputTehsil" class="form-control form-control-sm" value="<?php echo htmlspecialchars($student->tehsil ?? ''); ?>" placeholder="e.g. Saddar">
                            </div>
                        </div>

                        <hr class="my-2">
                        <strong class="small text-dark d-block mb-2">Previous Academic Record:</strong>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Previous School Name</label>
                            <input type="text" id="inputPrevSchool" class="form-control form-control-sm" value="<?php echo htmlspecialchars($student->prev_school_name ?? ''); ?>" placeholder="Previous School Name">
                        </div>
                        <div class="row g-2 mb-1">
                            <div class="col-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">School City</label>
                                <input type="text" id="inputPrevCity" class="form-control form-control-sm" value="<?php echo htmlspecialchars($student->prev_school_city ?? ''); ?>" placeholder="City">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Last Class Passed</label>
                                <input type="text" id="inputPrevClass" class="form-control form-control-sm" value="<?php echo htmlspecialchars($student->prev_class ?? ''); ?>" placeholder="e.g. 8th">
                            </div>
                            <div class="col-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">SLC / TC Number</label>
                                <input type="text" id="inputSlcNo" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($student->slc_number ?? ''); ?>" placeholder="SLC-0123">
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Marks Obtained / Total Marks</label>
                            <input type="text" id="inputPrevMarks" class="form-control form-control-sm" value="<?php echo (!$isBlank && !empty($student->prev_marks_obtained)) ? htmlspecialchars($student->prev_marks_obtained . ' / ' . $student->prev_total_marks) : ''; ?>" placeholder="e.g. 480 / 550">
                        </div>
                    </div>
                </div>

            </div>

            <!-- TAB 3: 🏫 SCHOOL HEADER SETTINGS -->
            <div class="tab-pane fade" id="pane-header-settings" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-2 px-3">
                        <strong class="small text-primary"><i class="fa fa-school me-1"></i> School Branding &amp; Print Header</strong>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">School Name</label>
                            <input type="text" id="drawerSchoolName" class="form-control form-control-sm fw-bold" value="<?php echo htmlspecialchars($schoolName); ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Campus Name / Branch</label>
                            <input type="text" id="drawerCampusName" class="form-control form-control-sm" value="<?php echo htmlspecialchars($campusName); ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Affiliation / Board</label>
                            <input type="text" id="drawerAffiliation" class="form-control form-control-sm" value="<?php echo htmlspecialchars($affiliation); ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">School Address</label>
                            <input type="text" id="drawerAddress" class="form-control form-control-sm" value="<?php echo htmlspecialchars($schoolAddress); ?>">
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Phone</label>
                                <input type="text" id="drawerPhone" class="form-control form-control-sm" value="<?php echo htmlspecialchars($schoolPhone); ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Email</label>
                                <input type="text" id="drawerEmail" class="form-control form-control-sm" value="<?php echo htmlspecialchars($schoolEmail); ?>">
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Session</label>
                                <input type="text" id="drawerSession" class="form-control form-control-sm" value="<?php echo htmlspecialchars($sessionName); ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Form Title</label>
                                <input type="text" id="drawerTitle" class="form-control form-control-sm" value="STUDENT ADMISSION &amp; ENROLMENT FORM">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Drawer Footer Actions -->
    <div class="offcanvas-footer bg-white border-top p-3 d-flex align-items-center justify-content-between gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="offcanvas">
            <i class="fa fa-times me-1"></i> Close
        </button>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="saveDrawerFormData(true)">
                <i class="fa fa-save me-1"></i> Save Draft
            </button>
            <button type="button" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm" onclick="saveDrawerFormData(false); window.print();">
                <i class="fa fa-print me-1"></i> Print Form
            </button>
        </div>
    </div>
</div>

<!-- MODAL: FORM HEADER SETTINGS (QUICK POPUP) -->
<div class="modal fade" id="formSettingsModal" tabindex="-1" aria-labelledby="formSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-3 px-4 border-bottom bg-light">
                <h6 class="modal-title fw-bold text-dark" id="formSettingsModalLabel">
                    <i class="fa fa-sliders text-primary me-2"></i>Admission Form Header &amp; Branding Settings
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">School Name</label>
                    <input type="text" id="settingSchoolName" class="form-control fw-bold" value="<?php echo htmlspecialchars($schoolName); ?>" placeholder="e.g. PAKISTAN MODEL HIGH SCHOOL">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Campus Name / Branch</label>
                    <input type="text" id="settingCampusName" class="form-control" value="<?php echo htmlspecialchars($campusName); ?>" placeholder="e.g. MAIN EXECUTIVE CAMPUS">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">Affiliation / Board</label>
                    <input type="text" id="settingAffiliation" class="form-control" value="<?php echo htmlspecialchars($affiliation); ?>" placeholder="e.g. Affiliated with Federal Board (FBISE) / BISE">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted text-uppercase">School Address</label>
                    <input type="text" id="settingAddress" class="form-control" value="<?php echo htmlspecialchars($schoolAddress); ?>" placeholder="e.g. Education City, Islamabad, Pakistan">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Phone / Helpline</label>
                        <input type="text" id="settingPhone" class="form-control" value="<?php echo htmlspecialchars($schoolPhone); ?>" placeholder="+92-51-111-222-333">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Email</label>
                        <input type="text" id="settingEmail" class="form-control" value="<?php echo htmlspecialchars($schoolEmail); ?>" placeholder="admissions@school.edu.pk">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Academic Session</label>
                        <input type="text" id="settingSession" class="form-control" value="<?php echo htmlspecialchars($sessionName); ?>" placeholder="e.g. Session: 2026 - 2027">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small text-muted text-uppercase">Form Title</label>
                        <input type="text" id="settingTitle" class="form-control" value="STUDENT ADMISSION &amp; ENROLMENT FORM" placeholder="STUDENT ADMISSION &amp; ENROLMENT FORM">
                    </div>
                </div>
                <div class="alert alert-info py-2 px-3 small border-0 mb-0">
                    <i class="fa fa-info-circle me-1"></i> You can also click directly on any text or field on the form to edit it live before printing!
                </div>
            </div>
            <div class="modal-footer py-2 px-4 border-top bg-light">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="resetFormBrandingDefaults()">
                    <i class="fa fa-undo me-1"></i> Reset Defaults
                </button>
                <button type="button" class="btn btn-primary btn-sm px-4 fw-bold" onclick="applyFormBrandingSettings()">
                    <i class="fa fa-check me-1"></i> Apply &amp; Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: ADMISSION FEE & TOKEN GENERATOR (ON ADMISSION FORM) -->
<div class="modal fade" id="admissionTokenModal" tabindex="-1" aria-labelledby="admissionTokenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="admissionTokenModalLabel">
                        <i class="fa fa-ticket-alt text-warning me-2"></i>Generate Admission Fee Token &amp; Slip
                    </h5>
                    <div class="text-white-50 small mt-0.5" style="font-size: 0.76rem;">
                        نیا داخلہ فیس ٹوکن، پراسپیکٹس فیس یا ابتدائی چالان سلپ فوری تیار کریں
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formAdmissionTokenGenerator" onsubmit="submitAdmissionTokenOnForm(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="formTokStudentId" value="<?php echo (!$isBlank && !empty($student->id)) ? (int)$student->id : 0; ?>">

                    <!-- Meta Details Row -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Candidate Name <span class="text-danger">*</span></label>
                            <input type="text" id="formTokStudentName" class="form-control form-control-sm fw-bold" value="<?php echo (!$isBlank && !empty($student->name)) ? htmlspecialchars($student->name, ENT_QUOTES) : ''; ?>" placeholder="e.g. Muhammad Ali" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Father / Guardian Name</label>
                            <input type="text" id="formTokFatherName" class="form-control form-control-sm" value="<?php echo (!$isBlank && !empty($student->father_name)) ? htmlspecialchars($student->father_name, ENT_QUOTES) : ''; ?>" placeholder="e.g. Muhammad Tariq">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Allotted / Applying Class</label>
                            <input type="text" id="formTokClassName" class="form-control form-control-sm" value="<?php echo (!$isBlank && !empty($student->class_name)) ? htmlspecialchars($student->class_name, ENT_QUOTES) : ''; ?>" placeholder="e.g. Class 9 - Science" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Admission / Form #</label>
                            <input type="text" id="formTokAdmNo" class="form-control form-control-sm" value="<?php echo (!$isBlank && !empty($student->admission_no)) ? htmlspecialchars($student->admission_no, ENT_QUOTES) : ''; ?>" placeholder="e.g. ADM-2026-001">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Contact / WhatsApp #</label>
                            <input type="text" id="formTokPhone" class="form-control form-control-sm" value="<?php echo !empty($fPhone) ? htmlspecialchars($fPhone, ENT_QUOTES) : ''; ?>" placeholder="0300-1234567">
                        </div>
                    </div>

                    <!-- Fee Items Checklist -->
                    <div class="card border mb-3" style="border-radius: 10px;">
                        <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                            <strong class="small text-uppercase text-dark"><i class="fa fa-list-check text-primary me-1"></i> Admission Fee Heads / تفصیلات فیس</strong>
                            <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 fw-bold" style="font-size: 0.74rem;" onclick="addCustomTokenHeadForm()">
                                <i class="fa fa-plus me-1"></i> Add Head
                            </button>
                        </div>
                        <div class="card-body p-2.5">
                            <div id="formTokItemsContainer">
                                <div class="row g-2 align-items-center tok-head-row mb-2">
                                    <div class="col-1 text-center">
                                        <input type="checkbox" class="form-check-input tok-chk" checked onchange="recalcFormTokenTotals()">
                                    </div>
                                    <div class="col-7">
                                        <input type="text" class="form-control form-control-sm tok-title" value="Admission Registration Fee" oninput="recalcFormTokenTotals()">
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                            <input type="number" class="form-control form-control-sm fw-bold tok-amt" value="5000" step="50" oninput="recalcFormTokenTotals()">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 align-items-center tok-head-row mb-2">
                                    <div class="col-1 text-center">
                                        <input type="checkbox" class="form-check-input tok-chk" checked onchange="recalcFormTokenTotals()">
                                    </div>
                                    <div class="col-7">
                                        <input type="text" class="form-control form-control-sm tok-title" value="Prospectus &amp; Admission Token" oninput="recalcFormTokenTotals()">
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                            <input type="number" class="form-control form-control-sm fw-bold tok-amt" value="1000" step="50" oninput="recalcFormTokenTotals()">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 align-items-center tok-head-row mb-2">
                                    <div class="col-1 text-center">
                                        <input type="checkbox" class="form-check-input tok-chk" checked onchange="recalcFormTokenTotals()">
                                    </div>
                                    <div class="col-7">
                                        <input type="text" class="form-control form-control-sm tok-title" value="Security Deposit (Refundable)" oninput="recalcFormTokenTotals()">
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                            <input type="number" class="form-control form-control-sm fw-bold tok-amt" value="2000" step="50" oninput="recalcFormTokenTotals()">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-2 align-items-center tok-head-row mb-2">
                                    <div class="col-1 text-center">
                                        <input type="checkbox" class="form-check-input tok-chk" checked onchange="recalcFormTokenTotals()">
                                    </div>
                                    <div class="col-7">
                                        <input type="text" class="form-control form-control-sm tok-title" value="First Month Tuition Fee" oninput="recalcFormTokenTotals()">
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                            <input type="number" class="form-control form-control-sm fw-bold tok-amt" value="3500" step="50" oninput="recalcFormTokenTotals()">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Totals Box -->
                            <div class="bg-light p-2.5 rounded-2 border mt-2">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-4 text-muted small">
                                        Subtotal: <strong class="text-dark font-monospace" id="formTokSubtotalText">PKR 11,500.00</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white small">Discount</span>
                                            <input type="number" id="formTokDiscountInput" class="form-control form-control-sm fw-bold" value="0" step="50" oninput="recalcFormTokenTotals()">
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <span class="text-muted small">Net Total:</span>
                                        <span class="fs-6 fw-bold text-success font-monospace ms-1" id="formTokNetText">PKR 11,500.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status & Mode Row -->
                    <div class="row g-2 mb-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Status / ادائیگی کیفیت <span class="text-danger">*</span></label>
                            <select id="formTokStatus" class="form-select form-select-sm fw-bold" onchange="toggleFormTokMode(this.value)">
                                <option value="paid" class="text-success">✓ Paid at Counter (فوری موصول)</option>
                                <option value="unpaid" class="text-danger">⏳ Unpaid / Bank Challan (بینک چالان)</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="formTokModeCol">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Payment Mode</label>
                            <select id="formTokMode" class="form-select form-select-sm">
                                <option value="Cash">Cash Counter</option>
                                <option value="Bank Transfer">Bank Transfer / Online</option>
                                <option value="JazzCash">JazzCash / EasyPaisa</option>
                                <option value="Cheque">Bank Cheque</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-1">Valid Till / Due Date</label>
                            <input type="date" id="formTokDueDate" class="form-control form-control-sm" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2.5 px-4 bg-light d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnFormGenerateTok" class="btn btn-success btn-sm px-4 fw-bold shadow-sm">
                        <i class="fa fa-ticket-alt me-1"></i> Generate &amp; Print Token Slip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: DUAL COPY TOKEN PRINT MODAL -->
<div class="modal fade" id="admissionTokenSlipModal" tabindex="-1" aria-labelledby="admissionTokenSlipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2.5 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa fa-ticket-alt text-warning fs-5"></i>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0">Official Admission Fee Token &amp; Enrolment Slip</h6>
                        <span class="text-white-50" style="font-size: 0.72rem;">Dual Copy: Office Record &amp; Student / Parent Copy</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-warning btn-sm fw-bold px-3 py-1 shadow-sm" onclick="printAdmissionTokenSlipDirect()">
                        <i class="fa fa-print me-1"></i> Print Token Slip
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-3 bg-light" id="tokenSlipModalBody" style="max-height: 82vh; overflow-y: auto;">
                <div id="tokenSlipRenderContainer">
                    <!-- Dynamic Dual-Copy Voucher Rendered here by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .modal, .modal-backdrop, .no-print-bar, .offcanvas, .offcanvas-backdrop {
        display: none !important;
    }
    #tokenSlipRenderContainer, #tokenSlipRenderContainer * {
        visibility: visible !important;
    }
    #tokenSlipRenderContainer {
        display: block !important;
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 6mm !important;
        background: #fff !important;
        z-index: 999999 !important;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// State for custom fields
let customFields = [];

window.openAdmissionTokenModal = function() {
    const sheetName = document.getElementById('cellStudentName')?.textContent?.trim();
    const sheetFather = document.getElementById('cellFatherName')?.textContent?.trim();
    const sheetClass = document.getElementById('cellClassName')?.textContent?.trim();
    const sheetAdm = document.getElementById('cellAdmNo')?.textContent?.trim();
    const sheetPhone = document.getElementById('cellFatherPhone')?.textContent?.trim();

    if (sheetName && sheetName !== '&nbsp;') document.getElementById('formTokStudentName').value = sheetName;
    if (sheetFather && sheetFather !== '&nbsp;') document.getElementById('formTokFatherName').value = sheetFather;
    if (sheetClass && sheetClass !== '&nbsp;') document.getElementById('formTokClassName').value = sheetClass;
    if (sheetAdm && sheetAdm !== '&nbsp;') document.getElementById('formTokAdmNo').value = sheetAdm;
    if (sheetPhone && sheetPhone !== '03__-_______') document.getElementById('formTokPhone').value = sheetPhone;

    recalcFormTokenTotals();
    const modalEl = document.getElementById('admissionTokenModal');
    if (modalEl) {
        const m = bootstrap.Modal.getOrCreateInstance(modalEl);
        m.show();
    }
};

window.toggleFormTokMode = function(status) {
    const col = document.getElementById('formTokModeCol');
    if (col) col.style.display = (status === 'paid') ? 'block' : 'none';
};

window.recalcFormTokenTotals = function() {
    let subtotal = 0;
    const rows = document.querySelectorAll('#formTokItemsContainer .tok-head-row');
    rows.forEach(r => {
        const chk = r.querySelector('.tok-chk');
        const amtInput = r.querySelector('.tok-amt');
        if (chk && chk.checked && amtInput) {
            subtotal += parseFloat(amtInput.value) || 0;
        }
    });

    const discInput = document.getElementById('formTokDiscountInput');
    const disc = discInput ? (parseFloat(discInput.value) || 0) : 0;
    const net = Math.max(0, subtotal - disc);

    const subEl = document.getElementById('formTokSubtotalText');
    const netEl = document.getElementById('formTokNetText');
    if (subEl) subEl.textContent = 'PKR ' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2 });
    if (netEl) netEl.textContent = 'PKR ' + net.toLocaleString('en-US', { minimumFractionDigits: 2 });
};

window.addCustomTokenHeadForm = function() {
    const container = document.getElementById('formTokItemsContainer');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'row g-2 align-items-center tok-head-row mb-2';
    div.innerHTML = `
        <div class="col-1 text-center">
            <input type="checkbox" class="form-check-input tok-chk" checked onchange="recalcFormTokenTotals()">
        </div>
        <div class="col-7">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm tok-title" placeholder="e.g. Annual Resource Fund" oninput="recalcFormTokenTotals()">
                <button type="button" class="btn btn-outline-danger btn-sm py-0" onclick="this.closest('.tok-head-row').remove(); recalcFormTokenTotals();">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <div class="col-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                <input type="number" class="form-control form-control-sm fw-bold tok-amt" value="1000" step="50" oninput="recalcFormTokenTotals()">
            </div>
        </div>
    `;
    container.appendChild(div);
    recalcFormTokenTotals();
};

window.submitAdmissionTokenOnForm = function(event) {
    event.preventDefault();
    const btn = document.getElementById('btnFormGenerateTok');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Generating...';

    const studentId = document.getElementById('formTokStudentId')?.value || 0;
    const studentName = document.getElementById('formTokStudentName')?.value || '';
    const fatherName = document.getElementById('formTokFatherName')?.value || '';
    const className = document.getElementById('formTokClassName')?.value || '';
    const admissionNo = document.getElementById('formTokAdmNo')?.value || '';
    const phone = document.getElementById('formTokPhone')?.value || '';
    const paymentStatus = document.getElementById('formTokStatus')?.value || 'paid';
    const paymentMode = document.getElementById('formTokMode')?.value || 'Cash';
    const dueDate = document.getElementById('formTokDueDate')?.value || '';
    const discount = parseFloat(document.getElementById('formTokDiscountInput')?.value) || 0;

    const items = [];
    document.querySelectorAll('#formTokItemsContainer .tok-head-row').forEach(r => {
        const chk = r.querySelector('.tok-chk');
        const titleInput = r.querySelector('.tok-title');
        const amtInput = r.querySelector('.tok-amt');
        if (chk && chk.checked && titleInput && amtInput) {
            const title = titleInput.value.trim();
            const amt = parseFloat(amtInput.value) || 0;
            if (title && amt > 0) items.push({ name: title, amount: amt });
        }
    });

    const fd = new FormData();
    fd.append('student_id', studentId);
    fd.append('student_name', studentName);
    fd.append('father_name', fatherName);
    fd.append('class_name', className);
    fd.append('admission_no', admissionNo);
    fd.append('phone', phone);
    fd.append('payment_status', paymentStatus);
    fd.append('payment_mode', paymentMode);
    fd.append('due_date', dueDate);
    fd.append('discount', discount);
    fd.append('items', JSON.stringify(items));

    fetch('<?php echo URLROOT; ?>/fees/ajaxGenerateAdmissionToken', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = origHtml;

        if (res.success) {
            // Auto update Section G Challan # on admission form
            const challanSpan = document.getElementById('cellChallanNo');
            if (challanSpan) {
                challanSpan.textContent = res.token_no;
                challanSpan.style.color = '#1e3a8a';
                challanSpan.style.fontWeight = 'bold';
            }

            // Hide generator modal
            const genModalEl = document.getElementById('admissionTokenModal');
            if (genModalEl) {
                const modalInst = bootstrap.Modal.getInstance(genModalEl);
                if (modalInst) modalInst.hide();
            }

            // Render dual copy slip and show slip modal
            renderAdmissionTokenSlip(res);
            const slipModalEl = document.getElementById('admissionTokenSlipModal');
            if (slipModalEl) {
                const sm = bootstrap.Modal.getOrCreateInstance(slipModalEl);
                sm.show();
            }
        } else {
            alert('Error: ' + (res.message || 'Could not generate token.'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = origHtml;
        console.error(err);
        alert('Network error while generating admission token.');
    });
};

window.renderAdmissionTokenSlip = function(d) {
    const container = document.getElementById('tokenSlipRenderContainer');
    if (!container) return;

    const schoolName = d.school?.name || document.getElementById('formSchoolName')?.textContent || 'PAK ACADEMY MODEL SCHOOL SYSTEM';
    const campusName = d.school?.campus || document.getElementById('formCampusName')?.textContent || 'MAIN EXECUTIVE CAMPUS';
    const schoolAddress = d.school?.address || document.getElementById('formSchoolAddress')?.textContent || 'City Educational Zone, Pakistan';
    const schoolPhone = d.school?.phone || '+92 42 35889000';

    const isPaid = (d.payment_status === 'paid');

    const renderSingleCopy = (copyTitle, copyUrdu, isOffice) => {
        let itemsHtml = '';
        if (d.items && d.items.length) {
            d.items.forEach((it, idx) => {
                itemsHtml += `
                    <tr>
                        <td style="padding: 3px 6px; border: 1px solid #cbd5e1; font-size: 10px; width: 10%; text-align: center;">${idx + 1}</td>
                        <td style="padding: 3px 6px; border: 1px solid #cbd5e1; font-size: 10px; font-weight: 600;">${it.title || it.name}</td>
                        <td style="padding: 3px 6px; border: 1px solid #cbd5e1; font-size: 10px; text-align: right; font-family: monospace; font-weight: 700;">Rs. ${Number(it.amount).toLocaleString()}</td>
                    </tr>
                `;
            });
        }

        const stampHtml = isPaid 
            ? `<div style="border: 2px solid #16a34a; background: #f0fdf4; color: #15803d; padding: 4px 8px; border-radius: 6px; font-size: 9.5px; font-weight: 700; text-align: center;">
                 <i class="fa fa-check-circle me-1"></i> PAID IN FULL (ادائیگی موصول شد) | Mode: ${d.payment_mode || 'Cash'}
               </div>`
            : `<div style="border: 2px solid #dc2626; background: #fef2f2; color: #b91c1c; padding: 4px 8px; border-radius: 6px; font-size: 9.5px; font-weight: 700; text-align: center;">
                 <i class="fa fa-exclamation-triangle me-1"></i> UNPAID (بینک چالان) - Payable Till: ${d.due_date}
               </div>`;

        return `
            <div class="token-single-copy bg-white p-3 border shadow-xs" style="border-radius: 8px; font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; position: relative;">
                <!-- Copy Badge -->
                <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                    <span class="badge ${isOffice ? 'bg-dark text-white' : 'bg-primary text-white'}" style="font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                        ${copyTitle}
                    </span>
                    <span style="font-size: 9px; font-weight: 600; color: #64748b;">${copyUrdu}</span>
                </div>

                <!-- School Header -->
                <div class="text-center mb-1.5 pb-1 border-bottom">
                    <h6 style="font-weight: 800; color: #0f172a; margin-bottom: 1px; font-size: 12.5px; text-transform: uppercase;">${schoolName}</h6>
                    <div style="font-size: 9.5px; font-weight: 700; color: #2563eb; margin-bottom: 1px;">${campusName}</div>
                    <div style="font-size: 8.5px; color: #64748b;">${schoolAddress} &bull; Ph: ${schoolPhone}</div>
                    <div style="margin-top: 4px; display: inline-block; background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; font-size: 9px; font-weight: 800; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">
                        Fresh Admission Fee Token &amp; Enrolment Slip
                    </div>
                </div>

                <!-- Token & Date Banner -->
                <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; margin-bottom: 6px;" class="d-flex justify-content-between align-items-center">
                    <div>
                        <span style="font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Token Number:</span><br>
                        <span style="font-family: monospace; font-size: 13px; font-weight: 800; color: #1e3a8a;">${d.token_no}</span>
                    </div>
                    <div class="text-end">
                        <span style="font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Issue Date:</span><br>
                        <span style="font-size: 10px; font-weight: 700; color: #334155;">${d.issue_date}</span>
                    </div>
                </div>

                <!-- Candidate Particulars Grid -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px; font-size: 9.5px;">
                    <tr>
                        <td style="padding: 2px 4px; color: #64748b; width: 22%;">Candidate:</td>
                        <td style="padding: 2px 4px; font-weight: 700; color: #0f172a; width: 38%;">${d.student_name}</td>
                        <td style="padding: 2px 4px; color: #64748b; width: 18%;">Father:</td>
                        <td style="padding: 2px 4px; font-weight: 700; color: #0f172a; width: 22%;">${d.father_name}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; color: #64748b;">Class:</td>
                        <td style="padding: 2px 4px; font-weight: 700; color: #0f172a;">${d.class_name}</td>
                        <td style="padding: 2px 4px; color: #64748b;">Adm/Form #:</td>
                        <td style="padding: 2px 4px; font-weight: 700; font-family: monospace; color: #0f172a;">${d.admission_no}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; color: #64748b;">Contact #:</td>
                        <td style="padding: 2px 4px; font-weight: 700; font-family: monospace; color: #0f172a;">${d.phone || 'N/A'}</td>
                        <td style="padding: 2px 4px; color: #64748b;">Due Date:</td>
                        <td style="padding: 2px 4px; font-weight: 700; color: #dc2626;">${d.due_date}</td>
                    </tr>
                </table>

                <!-- Fee Breakdown Table -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
                    <thead>
                        <tr style="background: #f1f5f9; color: #334155; font-size: 9px; font-weight: 700;">
                            <th style="padding: 3px 6px; border: 1px solid #cbd5e1; text-align: center;">#</th>
                            <th style="padding: 3px 6px; border: 1px solid #cbd5e1;">Fee Particular / فیس تفصیل</th>
                            <th style="padding: 3px 6px; border: 1px solid #cbd5e1; text-align: right;">Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                        <tr>
                            <td colspan="2" style="padding: 3px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 9.5px; font-weight: 600;">Subtotal</td>
                            <td style="padding: 3px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 10px; font-family: monospace; font-weight: 700;">Rs. ${Number(d.subtotal).toLocaleString()}</td>
                        </tr>
                        ${d.discount > 0 ? `
                        <tr>
                            <td colspan="2" style="padding: 2px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 9px; color: #16a34a; font-weight: 600;">Special Admission Discount</td>
                            <td style="padding: 2px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 9.5px; font-family: monospace; color: #16a34a; font-weight: 700;">- Rs. ${Number(d.discount).toLocaleString()}</td>
                        </tr>` : ''}
                        <tr style="background: #eef2ff;">
                            <td colspan="2" style="padding: 4px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 10.5px; font-weight: 800; color: #1e3a8a;">Total ${isPaid ? 'Paid' : 'Payable'}</td>
                            <td style="padding: 4px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 12px; font-family: monospace; font-weight: 800; color: #1e3a8a;">Rs. ${Number(d.net_total).toLocaleString()}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Status Stamp -->
                <div class="mb-2">
                    ${stampHtml}
                </div>

                <!-- Signatures Grid -->
                <div class="d-flex justify-content-between text-center pt-3 mt-1" style="font-size: 8px; color: #475569;">
                    <div style="width: 30%;">
                        <div style="border-top: 1px solid #94a3b8; padding-top: 2px; font-weight: 600;">Parent / Guardian</div>
                    </div>
                    <div style="width: 30%;">
                        <div style="border-top: 1px solid #94a3b8; padding-top: 2px; font-weight: 600;">Cashier / Accounts</div>
                    </div>
                    <div style="width: 30%;">
                        <div style="border-top: 1px solid #94a3b8; padding-top: 2px; font-weight: 600;">Admission Officer</div>
                    </div>
                </div>
            </div>
        `;
    };

    container.innerHTML = `
        <div class="row g-2">
            <div class="col-md-6 col-12 pe-md-2" style="border-right: 1.5px dashed #94a3b8;">
                ${renderSingleCopy('Office / School Copy', 'دفتری ریکارڈ', true)}
            </div>
            <div class="col-md-6 col-12 ps-md-2">
                ${renderSingleCopy('Student / Parent Copy', 'امیدوار / والدین کاپی', false)}
            </div>
        </div>
    `;
};

window.printAdmissionTokenSlipDirect = function() {
    window.print();
};

// Real-time synchronization map between drawer inputs and sheet elements
const directFieldMap = {
    'inputAdmNo': 'cellAdmNo',
    'inputAdmDate': 'cellAdmDate',
    'inputClassName': 'cellClassName',
    'inputSecName': 'cellSecName',
    'inputRollNo': 'cellRollNo',
    'inputCategory': 'cellCategory',
    'inputStudentName': 'cellStudentName',
    'inputBloodGroup': 'cellBloodGroup',
    'inputReligion': 'cellReligion',
    'inputNationality': 'cellNationality',
    'inputMotherTongue': 'cellMotherTongue',
    'inputStudentEmail': 'cellEmail',
    'inputFatherName': 'cellFatherName',
    'inputFatherPhone': 'cellFatherPhone',
    'inputFatherOccupation': 'cellFatherOccupation',
    'inputMotherName': 'cellMotherName',
    'inputMotherOccupation': 'cellMotherOccupation',
    'inputEmergencyContact': 'cellEmergencyContact',
    'inputAddress': 'cellAddress',
    'inputPermAddress': 'cellPermAddress',
    'inputCity': 'cellCity',
    'inputDistrict': 'cellDistrict',
    'inputTehsil': 'cellTehsil',
    'inputPrevSchool': 'cellPrevSchool',
    'inputPrevCity': 'cellPrevCity',
    'inputPrevClass': 'cellPrevClass',
    'inputSlcNo': 'cellSlcNo',
    'inputPrevMarks': 'cellPrevMarks',
    // Header
    'drawerSchoolName': 'formSchoolName',
    'drawerAffiliation': 'formAffiliation',
    'drawerSession': 'formSessionTitle',
    'drawerTitle': 'formMainTitle'
};

document.addEventListener('DOMContentLoaded', function() {
    initCustomFields();
    applyLayoutOptions();
    setupDrawerInputListeners();
    loadSavedBranding();
    loadSavedFormData();
});

// Helper to escape HTML characters safely
function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// 1. CUSTOM FIELDS MANAGEMENT
function initCustomFields() {
    try {
        const stored = localStorage.getItem('admission_custom_fields');
        if (stored) {
            customFields = JSON.parse(stored);
        } else {
            <?php if(!empty($data['custom_fields'])): ?>
                customFields = <?php echo json_encode($data['custom_fields']); ?>;
            <?php else: ?>
                customFields = [];
            <?php endif; ?>
        }
    } catch(e) {
        customFields = [];
    }
    renderCustomFields();
}

function applyLayoutOptions() {
    try {
        const raw = localStorage.getItem('admission_layout_options');
        const opts = raw ? JSON.parse(raw) : (<?php echo !empty($data['layout_options']) ? json_encode($data['layout_options']) : '{}'; ?>);
        if (opts) {
            if (opts.chkOfficeUse === false) {
                const oBox = document.querySelector('.office-use-box');
                if (oBox) oBox.style.display = 'none';
            }
            if (opts.chkChecklist === false) {
                const clGrid = document.querySelector('.checklist-grid');
                if (clGrid) {
                    if (clGrid.previousElementSibling && clGrid.previousElementSibling.classList.contains('section-header')) {
                        clGrid.previousElementSibling.style.display = 'none';
                    }
                    clGrid.style.display = 'none';
                }
            }
            if (opts.chkUndertaking === false) {
                const uBox = document.querySelector('.undertaking-box');
                if (uBox) uBox.style.display = 'none';
            }
            if (opts.chkSignatures === false) {
                const sigGrid = document.querySelector('.signatures-grid');
                if (sigGrid) sigGrid.style.display = 'none';
            }
        }
    } catch(e) {}
}

function setCustomPreset(label, valPlaceholder) {
    const labelInput = document.getElementById('inputCustomLabel');
    const valInput = document.getElementById('inputCustomValue');
    if (labelInput) labelInput.value = label;
    if (valInput) {
        valInput.value = '';
        valInput.placeholder = 'e.g. ' + (valPlaceholder || label);
        valInput.focus();
    }
}

function addCustomFieldFromDrawer() {
    const labelInput = document.getElementById('inputCustomLabel');
    const valInput = document.getElementById('inputCustomValue');
    const label = labelInput ? labelInput.value.trim() : '';
    const val = valInput ? valInput.value.trim() : '';

    if (!label) {
        alert('Baraye meherbani field ka name / label darj karein.');
        if (labelInput) labelInput.focus();
        return;
    }

    const newField = {
        id: 'cf_' + Date.now() + '_' + Math.floor(Math.random()*1000),
        label: label,
        value: val || '____________________'
    };

    customFields.push(newField);
    saveCustomFieldsToStorage();
    renderCustomFields();

    // Reset inputs
    if (labelInput) labelInput.value = '';
    if (valInput) {
        valInput.value = '';
        valInput.placeholder = 'e.g. Route # 4 - Saddar Stop';
    }
}

function removeCustomField(id) {
    customFields = customFields.filter(f => f.id !== id);
    saveCustomFieldsToStorage();
    renderCustomFields();
}

function clearAllCustomFields() {
    if (confirm('Kya aap waqai sabhi custom fields khatam karna chahte hain?')) {
        customFields = [];
        saveCustomFieldsToStorage();
        renderCustomFields();
    }
}

function saveCustomFieldsToStorage() {
    localStorage.setItem('admission_custom_fields', JSON.stringify(customFields));
}

function renderCustomFields() {
    const sheetSection = document.getElementById('customFieldsSection');
    const tableBody = document.getElementById('customFieldsTableBody');
    const drawerList = document.getElementById('customFieldsDrawerList');
    const countBadge = document.getElementById('customFieldsCount');
    const clearBtn = document.getElementById('btnClearAllCustom');

    if (countBadge) countBadge.textContent = customFields.length;
    if (clearBtn) clearBtn.style.display = customFields.length > 0 ? 'inline-block' : 'none';

    // 1. Render on A4 Document Sheet
    if (sheetSection && tableBody) {
        if (customFields.length === 0) {
            sheetSection.style.display = 'none';
            tableBody.innerHTML = '';
        } else {
            sheetSection.style.display = 'block';
            let html = '';
            for (let i = 0; i < customFields.length; i += 2) {
                const f1 = customFields[i];
                const f2 = (i + 1 < customFields.length) ? customFields[i + 1] : null;

                html += '<tr>';
                html += '<td class="label-cell">' + escapeHtml(f1.label) + '</td>';
                if (f2) {
                    html += '<td class="val-cell"><span class="val-text" contenteditable="true">' + escapeHtml(f1.value) + '</span></td>';
                    html += '<td class="label-cell">' + escapeHtml(f2.label) + '</td>';
                    html += '<td class="val-cell" colspan="3"><span class="val-text" contenteditable="true">' + escapeHtml(f2.value) + '</span></td>';
                } else {
                    html += '<td class="val-cell" colspan="5"><span class="val-text" contenteditable="true">' + escapeHtml(f1.value) + '</span></td>';
                }
                html += '</tr>';
            }
            tableBody.innerHTML = html;
        }
    }

    // 2. Render inside Drawer list
    if (drawerList) {
        if (customFields.length === 0) {
            drawerList.innerHTML = '<div class="text-center text-muted small py-3" id="noCustomFieldsMsg">' +
                '<i class="fa fa-info-circle text-muted fs-5 mb-1 d-block"></i>' +
                'Abhi koi custom field add nahi ki gayi.<br>Upar se field name aur value daal kar "+ Add" dabayein.</div>';
        } else {
            let dHtml = '';
            customFields.forEach(f => {
                dHtml += '<div class="custom-field-row-card">' +
                    '<div class="d-flex align-items-center gap-2 text-truncate" style="flex: 1;">' +
                        '<span class="custom-field-badge"><i class="fa fa-tag me-1 text-primary"></i>' + escapeHtml(f.label) + '</span>' +
                        '<span class="text-dark small fw-semibold text-truncate">' + escapeHtml(f.value) + '</span>' +
                    '</div>' +
                    '<button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" title="Remove Field" onclick="removeCustomField(\'' + f.id + '\')">' +
                        '<i class="fa fa-times"></i>' +
                    '</button>' +
                '</div>';
            });
            drawerList.innerHTML = dHtml;
        }
    }
}

// 2. REAL-TIME INPUT LISTENERS
function setupDrawerInputListeners() {
    Object.keys(directFieldMap).forEach(inputId => {
        const inputEl = document.getElementById(inputId);
        const targetEl = document.getElementById(directFieldMap[inputId]);
        if (inputEl && targetEl) {
            inputEl.addEventListener('input', function() {
                targetEl.textContent = this.value;
            });
            inputEl.addEventListener('change', function() {
                targetEl.textContent = this.value;
            });
        }
    });

    // Special Header elements
    const campusEl = document.getElementById('drawerCampusName');
    if (campusEl) {
        campusEl.addEventListener('input', function() {
            const formCampus = document.getElementById('formCampusName');
            if (formCampus) formCampus.innerHTML = '<i class="fa fa-building me-1"></i>' + this.value;
        });
    }

    const addrEl = document.getElementById('drawerAddress');
    if (addrEl) {
        addrEl.addEventListener('input', function() {
            const formAddr = document.getElementById('formSchoolAddress');
            if (formAddr) formAddr.innerHTML = '<i class="fa fa-map-marker-alt me-1"></i>' + this.value;
        });
    }

    const phoneEl = document.getElementById('drawerPhone');
    if (phoneEl) {
        phoneEl.addEventListener('input', function() {
            const formPhone = document.getElementById('formSchoolPhone');
            if (formPhone) formPhone.innerHTML = '<i class="fa fa-phone me-1"></i>' + this.value;
        });
    }

    const emailEl = document.getElementById('drawerEmail');
    if (emailEl) {
        emailEl.addEventListener('input', function() {
            const formEmail = document.getElementById('formSchoolEmail');
            if (formEmail) formEmail.innerHTML = '<i class="fa fa-envelope me-1"></i>' + this.value;
        });
    }

    // CNIC inputs
    const sCnic = document.getElementById('inputStudentCnic');
    if (sCnic) {
        sCnic.addEventListener('input', function() {
            updateCnicBoxes('studentCnicBoxes', this.value);
        });
    }

    const fCnic = document.getElementById('inputFatherCnic');
    if (fCnic) {
        fCnic.addEventListener('input', function() {
            updateCnicBoxes('fatherCnicBoxes', this.value);
        });
    }

    const mCnic = document.getElementById('inputMotherCnic');
    if (mCnic) {
        mCnic.addEventListener('input', function() {
            updateCnicBoxes('motherCnicBoxes', this.value);
        });
    }

    // Gender selection handler
    const gSelect = document.getElementById('inputGender');
    if (gSelect) {
        gSelect.addEventListener('change', function() {
            updateGenderSelection(this.value);
        });
    }

    // Date of Birth handler
    const dobInput = document.getElementById('inputDob');
    if (dobInput) {
        dobInput.addEventListener('input', function() {
            updateDobBoxes(this.value);
        });
    }
}

function updateGenderSelection(val) {
    const maleCheck = document.getElementById('checkGenderMale');
    const femaleCheck = document.getElementById('checkGenderFemale');
    const v = (val || '').toLowerCase().trim();
    if (maleCheck) maleCheck.innerHTML = (v === 'male' || v === 'm') ? '&#10003;' : '';
    if (femaleCheck) femaleCheck.innerHTML = (v === 'female' || v === 'f') ? '&#10003;' : '';
}

function updateDobBoxes(dobValue) {
    const container = document.getElementById('cellDob');
    if (!container) return;
    const clean = (dobValue || '').replace(/[^0-9]/g, '');
    const boxes = container.querySelectorAll('.cnic-box');
    for (let i = 0; i < boxes.length; i++) {
        if (i < 8) {
            const digit = clean[i];
            boxes[i].innerHTML = (digit !== undefined && digit !== '') ? digit : '&nbsp;';
        }
    }
}

function updateCnicBoxes(containerId, cnicValue) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const clean = (cnicValue || '').replace(/[^0-9]/g, '');
    const boxes = container.querySelectorAll('.cnic-box');
    for (let i = 0; i < boxes.length; i++) {
        if (i < 13) {
            const digit = clean[i];
            boxes[i].innerHTML = (digit !== undefined && digit !== '') ? digit : '&nbsp;';
        }
    }
}

function handleClassChange(selectEl) {
    const val = selectEl.value;
    const customInput = document.getElementById('inputClassName');
    const cellClass = document.getElementById('cellClassName');
    if (val === '__custom__') {
        if (customInput) {
            customInput.style.display = 'block';
            customInput.value = '';
            customInput.focus();
        }
        if (cellClass) cellClass.textContent = '______________';
    } else {
        if (customInput) {
            customInput.style.display = 'none';
            customInput.value = val;
        }
        if (cellClass) cellClass.textContent = val || '______________';
    }
}

// 3. PERSISTENCE & BRANDING
function loadSavedBranding() {
    const saved = localStorage.getItem('admission_form_branding');
    if (saved) {
        try {
            const b = JSON.parse(saved);
            if (b.schoolName) {
                document.getElementById('formSchoolName').textContent = b.schoolName;
                document.getElementById('settingSchoolName').value = b.schoolName;
                if (document.getElementById('drawerSchoolName')) document.getElementById('drawerSchoolName').value = b.schoolName;
            }
            if (b.campusName) {
                document.getElementById('formCampusName').innerHTML = '<i class="fa fa-building me-1"></i>' + b.campusName;
                document.getElementById('settingCampusName').value = b.campusName;
                if (document.getElementById('drawerCampusName')) document.getElementById('drawerCampusName').value = b.campusName;
            }
            if (b.affiliation) {
                document.getElementById('formAffiliation').textContent = b.affiliation;
                document.getElementById('settingAffiliation').value = b.affiliation;
                if (document.getElementById('drawerAffiliation')) document.getElementById('drawerAffiliation').value = b.affiliation;
            }
            if (b.address) {
                document.getElementById('formSchoolAddress').innerHTML = '<i class="fa fa-map-marker-alt me-1"></i>' + b.address;
                document.getElementById('settingAddress').value = b.address;
                if (document.getElementById('drawerAddress')) document.getElementById('drawerAddress').value = b.address;
            }
            if (b.phone) {
                document.getElementById('formSchoolPhone').innerHTML = '<i class="fa fa-phone me-1"></i>' + b.phone;
                document.getElementById('settingPhone').value = b.phone;
                if (document.getElementById('drawerPhone')) document.getElementById('drawerPhone').value = b.phone;
            }
            if (b.email) {
                document.getElementById('formSchoolEmail').innerHTML = '<i class="fa fa-envelope me-1"></i>' + b.email;
                document.getElementById('settingEmail').value = b.email;
                if (document.getElementById('drawerEmail')) document.getElementById('drawerEmail').value = b.email;
            }
            if (b.session) {
                document.getElementById('formSessionTitle').textContent = b.session;
                document.getElementById('settingSession').value = b.session;
                if (document.getElementById('drawerSession')) document.getElementById('drawerSession').value = b.session;
            }
            if (b.title) {
                document.getElementById('formMainTitle').textContent = b.title;
                document.getElementById('settingTitle').value = b.title;
                if (document.getElementById('drawerTitle')) document.getElementById('drawerTitle').value = b.title;
            }
        } catch(e) {
            console.error('Error parsing saved branding:', e);
        }
    }
}

function applyFormBrandingSettings() {
    const schoolName = document.getElementById('settingSchoolName').value.trim();
    const campusName = document.getElementById('settingCampusName').value.trim();
    const affiliation = document.getElementById('settingAffiliation').value.trim();
    const address = document.getElementById('settingAddress').value.trim();
    const phone = document.getElementById('settingPhone').value.trim();
    const email = document.getElementById('settingEmail').value.trim();
    const session = document.getElementById('settingSession').value.trim();
    const title = document.getElementById('settingTitle').value.trim();

    if (schoolName) document.getElementById('formSchoolName').textContent = schoolName;
    if (campusName) document.getElementById('formCampusName').innerHTML = '<i class="fa fa-building me-1"></i>' + campusName;
    if (affiliation) document.getElementById('formAffiliation').textContent = affiliation;
    if (address) document.getElementById('formSchoolAddress').innerHTML = '<i class="fa fa-map-marker-alt me-1"></i>' + address;
    if (phone) document.getElementById('formSchoolPhone').innerHTML = '<i class="fa fa-phone me-1"></i>' + phone;
    if (email) document.getElementById('formSchoolEmail').innerHTML = '<i class="fa fa-envelope me-1"></i>' + email;
    if (session) document.getElementById('formSessionTitle').textContent = session;
    if (title) document.getElementById('formMainTitle').textContent = title;

    // Sync drawer inputs too
    if (document.getElementById('drawerSchoolName')) document.getElementById('drawerSchoolName').value = schoolName;
    if (document.getElementById('drawerCampusName')) document.getElementById('drawerCampusName').value = campusName;
    if (document.getElementById('drawerAffiliation')) document.getElementById('drawerAffiliation').value = affiliation;
    if (document.getElementById('drawerAddress')) document.getElementById('drawerAddress').value = address;
    if (document.getElementById('drawerPhone')) document.getElementById('drawerPhone').value = phone;
    if (document.getElementById('drawerEmail')) document.getElementById('drawerEmail').value = email;
    if (document.getElementById('drawerSession')) document.getElementById('drawerSession').value = session;
    if (document.getElementById('drawerTitle')) document.getElementById('drawerTitle').value = title;

    // Save to localStorage
    const branding = { schoolName, campusName, affiliation, address, phone, email, session, title };
    localStorage.setItem('admission_form_branding', JSON.stringify(branding));

    // Close modal
    const modalEl = document.getElementById('formSettingsModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();
}

function resetFormBrandingDefaults() {
    localStorage.removeItem('admission_form_branding');
    localStorage.removeItem('admission_form_data');
    localStorage.removeItem('admission_custom_fields');
    location.reload();
}

function saveDrawerFormData(showNotification = true) {
    const data = {};
    Object.keys(directFieldMap).forEach(id => {
        const el = document.getElementById(id);
        if (el) data[id] = el.value;
    });
    data['inputStudentCnic'] = document.getElementById('inputStudentCnic')?.value || '';
    data['inputFatherCnic'] = document.getElementById('inputFatherCnic')?.value || '';
    data['inputMotherCnic'] = document.getElementById('inputMotherCnic')?.value || '';
    data['inputGender'] = document.getElementById('inputGender')?.value || '';
    data['inputDob'] = document.getElementById('inputDob')?.value || '';
    data['selectClass'] = document.getElementById('selectClass')?.value || '';
    data['drawerCampusName'] = document.getElementById('drawerCampusName')?.value || '';
    data['drawerAddress'] = document.getElementById('drawerAddress')?.value || '';
    data['drawerPhone'] = document.getElementById('drawerPhone')?.value || '';
    data['drawerEmail'] = document.getElementById('drawerEmail')?.value || '';

    localStorage.setItem('admission_form_data', JSON.stringify(data));
    saveCustomFieldsToStorage();

    if (showNotification) {
        alert('Form ka tamam data aur custom fields mehfooz (Saved) ho chuki hain!');
    }
}

function loadSavedFormData() {
    try {
        const raw = localStorage.getItem('admission_form_data');
        if (raw) {
            const data = JSON.parse(raw);
            Object.keys(data).forEach(id => {
                const el = document.getElementById(id);
                if (el && data[id]) {
                    el.value = data[id];
                    if (directFieldMap[id]) {
                        const target = document.getElementById(directFieldMap[id]);
                        if (target) target.textContent = data[id];
                    }
                }
            });
            if (data['inputStudentCnic']) updateCnicBoxes('studentCnicBoxes', data['inputStudentCnic']);
            if (data['inputFatherCnic']) updateCnicBoxes('fatherCnicBoxes', data['inputFatherCnic']);
            if (data['inputMotherCnic']) updateCnicBoxes('motherCnicBoxes', data['inputMotherCnic']);
            if (data['inputGender']) updateGenderSelection(data['inputGender']);
            if (data['inputDob']) updateDobBoxes(data['inputDob']);
        }
    } catch(e) {
        console.error('Error loading saved form data', e);
    }
}
</script>

<?php if(isset($_GET['autoprint']) && $_GET['autoprint'] == '1'): ?>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            window.print();
        }, 600);
    });
</script>
<?php endif; ?>

</body>
</html>
