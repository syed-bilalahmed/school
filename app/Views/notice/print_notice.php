<?php
$settings = $data['settings'] ?? [];
$notice = $data['notice'] ?? null;

$schoolName = !empty($settings['school_name']) ? $settings['school_name'] : 'CITY MODEL HIGH SCHOOL & COLLEGE';
$campusName = !empty($settings['campus_name']) ? $settings['campus_name'] : 'MAIN EXECUTIVE CAMPUS';
$schoolAddress = !empty($settings['school_address']) ? $settings['school_address'] : 'Plot 45-B, Sector H-8/4, Education City, Islamabad';
$schoolPhone = !empty($settings['school_phone']) ? $settings['school_phone'] : '+92-51-111-222-333';
$schoolEmail = !empty($settings['school_email']) ? $settings['school_email'] : 'info@citymodelschool.edu.pk';
$affiliation = !empty($settings['affiliation_board']) ? $settings['affiliation_board'] : '';
$schoolLogo = !empty($settings['logo']) ? $settings['logo'] : '';

$refNo = 'CIR-' . date('Y', strtotime($notice->publish_date ?? 'now')) . '/' . str_pad($notice->id ?? 1, 4, '0', STR_PAD_LEFT);
$publishDate = !empty($notice->publish_date) ? date('d F, Y', strtotime($notice->publish_date)) : date('d F, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Circular - <?php echo htmlspecialchars($notice->title ?? 'Notice'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm 16mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Arial, 'Helvetica Neue', sans-serif;
            color: #000000;
            font-size: 13.5px;
            line-height: 1.6;
            margin: 0;
            padding: 20px 0;
        }

        /* Screen-Only Control Toolbar */
        .print-toolbar {
            max-width: 800px;
            margin: 0 auto 16px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 12px 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: gap;
            gap: 12px;
        }

        /* The Printable Letterhead Sheet */
        .circular-sheet {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border: none;
            padding: 24px 32px 30px 32px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }

        /* Clean Institutional Header (Logo on Far Left, Text Centered) */
        .institution-header {
            padding-bottom: 12px;
            margin-bottom: 15px;
            border-bottom: 2px solid #000000;
        }
        .inst-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }
        .inst-logo-box {
            flex: 0 0 85px;
            text-align: left;
        }
        .inst-logo-img {
            max-height: 75px;
            max-width: 85px;
            object-fit: contain;
        }
        .inst-logo-placeholder {
            width: 70px;
            height: 70px;
            border: 1px dashed #94a3b8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #64748b;
        }
        .inst-details-box {
            flex: 1 1 auto;
            text-align: center;
        }
        .inst-spacer-box {
            flex: 0 0 85px; /* Keeps centered text balanced with left logo */
        }
        .inst-name {
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000000;
            margin: 0 0 2px 0;
            line-height: 1.2;
        }
        .inst-campus {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .inst-affil {
            font-size: 11.5px;
            color: #475569;
            margin-bottom: 3px;
        }
        .inst-contact {
            font-size: 11px;
            color: #334155;
        }

        /* Top Ref & Date Bar */
        .circular-ref-date-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 22px;
            color: #000000;
        }

        /* Pure Center Heading (NO "CIRCULAR", NO "SUBJECT:") */
        .circular-heading-block {
            text-align: center;
            margin: 18px 0 22px 0;
        }
        .circular-clean-heading {
            font-size: 17px;
            font-weight: 800;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.4;
            display: inline-block;
            outline: none;
        }

        /* Audience / Circulation Line */
        .distribution-line {
            font-size: 12px;
            color: #334155;
            margin-bottom: 18px;
            font-style: italic;
        }

        /* Circular Body Content */
        .circular-body-text {
            font-size: 13.5px;
            line-height: 1.8;
            color: #000000;
            white-space: pre-line;
            text-align: justify;
            min-height: 220px;
            margin-bottom: 25px;
            outline: none;
        }

        /* Expiry / Deadline note if present */
        .expiry-note {
            font-size: 12px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #000000;
        }

        /* Bottom Section: Copy To (Left) & Signature (Right) */
        .circular-bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 35px;
            padding-top: 10px;
            gap: 25px;
        }
        .copy-to-wrapper {
            flex: 1 1 60%;
            font-size: 12px;
            color: #000000;
        }
        .copy-to-title {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .copy-to-list {
            margin: 0;
            padding-left: 20px;
            line-height: 1.6;
            outline: none;
        }
        .copy-to-list li {
            padding-left: 2px;
        }
        .signatory-wrapper {
            flex: 0 0 220px;
            text-align: center;
        }
        .sign-spacer {
            height: 48px;
        }
        .sign-border-line {
            border-top: 1.5px solid #000000;
            padding-top: 4px;
            font-weight: 700;
            font-size: 13px;
            color: #000000;
            outline: none;
        }
        .sign-school-name {
            font-size: 11.5px;
            color: #334155;
            margin-top: 2px;
        }

        /* Print Specific Overrides */
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .print-toolbar {
                display: none !important;
            }
            .circular-sheet {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .circular-clean-heading, .copy-to-list, .circular-body-text, .sign-border-line {
                border: none !important;
                outline: none !important;
            }
        }
    </style>
</head>
<body>

<!-- Interactive Toolbar (Screen only - Hidden in Print) -->
<div class="print-toolbar">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="<?php echo URLROOT; ?>/notice/index" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fa fa-arrow-left me-1"></i> Notice Board
        </a>

        <!-- Signatory Title Selector -->
        <div class="d-flex align-items-center gap-1">
            <label class="small text-muted fw-bold mb-0">Sign By:</label>
            <select id="signatorySelect" class="form-select form-select-sm" style="width: auto; border-radius: 6px;" onchange="updateSignatory(this.value)">
                <option value="Principal" selected>Principal</option>
                <option value="Vice Principal">Vice Principal</option>
                <option value="Headmaster / Headmistress">Headmaster</option>
                <option value="Administrator">Administrator</option>
                <option value="Authorized Signatory">Authorized Signatory</option>
            </select>
        </div>

        <!-- Add Copy-To Item Button -->
        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" onclick="addCopyLine()" title="Add another recipient to the Copy-to list">
            <i class="fa fa-plus me-1"></i> Add Copy To
        </button>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="text-muted smaller d-none d-md-inline" style="font-size: 11px;">
            <i class="fa fa-pen me-1 text-primary"></i> Click any text on the letterhead to edit before print
        </span>
        <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-bold rounded-pill shadow-sm">
            <i class="fa fa-print me-1"></i> Print Circular (A4)
        </button>
    </div>
</div>

<div class="circular-sheet">
    <!-- Clean Institutional Letterhead Header (Logo Left, Info Centered) -->
    <div class="institution-header">
        <div class="inst-header-row">
            <!-- Left Side Logo -->
            <div class="inst-logo-box">
                <?php if(!empty($schoolLogo)): ?>
                    <img src="<?php echo URLROOT . '/' . htmlspecialchars($schoolLogo); ?>" alt="Logo" class="inst-logo-img">
                <?php else: ?>
                    <div class="inst-logo-placeholder">
                        <i class="fa fa-school"></i>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Center Institution Details -->
            <div class="inst-details-box">
                <h1 class="inst-name"><?php echo htmlspecialchars($schoolName); ?></h1>
                <?php if(!empty($campusName)): ?>
                    <div class="inst-campus"><?php echo htmlspecialchars($campusName); ?></div>
                <?php endif; ?>
                <?php if(!empty($affiliation)): ?>
                    <div class="inst-affil"><?php echo htmlspecialchars($affiliation); ?></div>
                <?php endif; ?>
                <div class="inst-contact">
                    <?php if(!empty($schoolAddress)): ?><span><?php echo htmlspecialchars($schoolAddress); ?></span><?php endif; ?>
                    <?php if(!empty($schoolPhone)): ?> | <span>Tel: <?php echo htmlspecialchars($schoolPhone); ?></span><?php endif; ?>
                    <?php if(!empty($schoolEmail)): ?> | <span>Email: <?php echo htmlspecialchars($schoolEmail); ?></span><?php endif; ?>
                </div>
            </div>

            <!-- Right Balancing Spacer -->
            <div class="inst-spacer-box d-none d-sm-block"></div>
        </div>
    </div>

    <!-- Ref No & Date (Simple text, Left & Right) -->
    <div class="circular-ref-date-bar">
        <div>
            <strong>Ref No:</strong> <span contenteditable="true"><?php echo htmlspecialchars($refNo); ?></span>
        </div>
        <div>
            <strong>Date:</strong> <span contenteditable="true"><?php echo htmlspecialchars($publishDate); ?></span>
        </div>
    </div>

    <!-- Pure Center Heading (NO "CIRCULAR" word, NO "SUBJECT:" prefix) -->
    <div class="circular-heading-block">
        <h2 class="circular-clean-heading" contenteditable="true" title="Click to edit heading before printing">
            <u><?php echo strtoupper(htmlspecialchars($notice->title ?? '')); ?></u>
        </h2>
    </div>

    <!-- Circulation / Target Audience Line (if applicable) -->
    <?php
        $dists = [];
        if (($notice->is_visible_to_student ?? 'no') === 'yes') $dists[] = 'All Students';
        if (($notice->is_visible_to_staff ?? 'no') === 'yes') $dists[] = 'Teaching Faculty & Staff';
        if (($notice->is_visible_to_parent ?? 'no') === 'yes') $dists[] = 'Parents & Guardians';
        if(!empty($dists)):
    ?>
    <div class="distribution-line">
        <strong>Circulation:</strong> <?php echo implode(' | ', $dists); ?>
    </div>
    <?php endif; ?>

    <!-- Circular Message Body (Directly editable on screen before print) -->
    <div class="circular-body-text" contenteditable="true" title="Click to edit body before printing">
<?php echo htmlspecialchars($notice->message ?? ''); ?>
    </div>

    <!-- Expiry Note if any -->
    <?php if(!empty($notice->expiry_date)): ?>
    <div class="expiry-note" contenteditable="true">
        Note: This notification is valid until <u><?php echo date('d F, Y', strtotime($notice->expiry_date)); ?></u>.
    </div>
    <?php endif; ?>

    <!-- Bottom Section: Copy To (Bottom-Left) & Signatory (Bottom-Right) -->
    <div class="circular-bottom-section">
        <!-- Bottom Left: Copy forwarded to -->
        <div class="copy-to-wrapper">
            <div class="copy-to-title">
                <strong>Copy forwarded for information &amp; necessary action to:</strong>
            </div>
            <ol class="copy-to-list" id="copyToList" contenteditable="true" title="Click to edit or add recipients directly">
                <li>Vice Principal / Section Incharge</li>
                <li>All Notice Boards (Junior &amp; Senior Wing)</li>
                <li>Staff Room Notice Board</li>
                <li>Accounts &amp; Fee Department</li>
                <li>Office Master File / Guard File</li>
            </ol>
        </div>

        <!-- Bottom Right: Signatory (Principal / Vice Principal) -->
        <div class="signatory-wrapper">
            <div class="sign-spacer"></div>
            <div class="sign-border-line">
                <span id="signatoryTitleDisplay" contenteditable="true">Principal</span>
            </div>
            <div class="sign-school-name">
                <?php echo htmlspecialchars($schoolName); ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Update Signatory Title
    function updateSignatory(title) {
        var display = document.getElementById('signatoryTitleDisplay');
        if (display) {
            display.innerText = title;
        }
    }

    // Add another item to the Copy-to list
    function addCopyLine() {
        var list = document.getElementById('copyToList');
        if (list) {
            var li = document.createElement('li');
            li.textContent = 'Enter recipient name/office';
            list.appendChild(li);
            li.focus();
        }
    }

    // Auto print if requested via query param
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autoprint') === '1') {
        window.addEventListener('load', () => window.print());
    }
</script>
</body>
</html>
