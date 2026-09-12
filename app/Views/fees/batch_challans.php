<?php
$challans = $data['challans'] ?? [];
$classObj = $data['class'] ?? null;
$month = $data['month'] ?? date('F');
$year = $data['year'] ?? date('Y');
$bank = $data['bank'] ?? null;
$classes = $data['classes'] ?? [];
$feeTypes = $data['fee_types'] ?? [];
$feeGroups = $data['fee_groups'] ?? [];
$lateFine = $data['late_fine'] ?? 200.00;
$instructions = $data['instructions'] ?? '';
$defaultParticulars = $data['default_particulars'] ?? '';
$currentClassId = !empty($classObj) ? $classObj->id : (int)($_GET['class_id'] ?? 1);
$currentSectionId = $data['current_section_id'] ?? ($_GET['section_id'] ?? null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Fee Challans - <?php echo htmlspecialchars($classObj ? $classObj->class_name : 'Class'); ?> (<?php echo $month . ' ' . $year; ?>)</title>
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap 5 CSS for Modals & UI Controls -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: #f1f5f9;
            color: #1e293b;
            padding: 16px;
            font-size: 11px;
        }
        .no-print {
            max-width: 1320px;
            margin: 0 auto 16px auto;
        }
        .top-toolbar {
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .btn-custom {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-custom-primary { background: #2563eb; color: #fff; }
        .btn-custom-primary:hover { background: #1d4ed8; color: #fff; }
        .btn-custom-secondary { background: #64748b; color: #fff; }
        .btn-custom-secondary:hover { background: #475569; color: #fff; }
        .btn-custom-success { background: #10b981; color: #fff; }
        .btn-custom-success:hover { background: #059669; color: #fff; }
        .btn-custom-warning { background: #f59e0b; color: #fff; }
        .btn-custom-warning:hover { background: #d97706; color: #fff; }
        .btn-custom-outline { background: #fff; border: 1px solid #cbd5e1; color: #334155; }
        .btn-custom-outline:hover { background: #f8fafc; color: #0f172a; }

        /* LIVE EDIT MODE HIGHLIGHTS */
        body.live-edit-active .live-editable {
            background-color: #fef08a !important;
            outline: 1.5px dashed #ca8a04 !important;
            cursor: text !important;
            padding: 1px 3px !important;
            border-radius: 3px !important;
            transition: all 0.15s ease;
        }
        body.live-edit-active .live-editable:hover,
        body.live-edit-active .live-editable:focus {
            background-color: #fde047 !important;
            outline: 2px solid #b45309 !important;
        }

        /* 3-COPY CHALLAN SHEET */
        .challan-page {
            max-width: 1320px;
            margin: 0 auto 26px auto;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            background: #ffffff;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            page-break-after: always;
            break-after: page;
        }

        .voucher-copy {
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #ffffff;
            position: relative;
        }
        .voucher-copy:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -8px;
            top: 0;
            bottom: 0;
            border-right: 1.5px dashed #94a3b8;
        }

        /* HEADER */
        .voucher-header {
            text-align: center;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }
        .copy-tag {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 10px;
            border-radius: 4px;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .school-title {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            line-height: 1.2;
        }
        .school-sub {
            font-size: 8.5px;
            color: #475569;
            margin-top: 2px;
        }

        /* BANK SECTION */
        .bank-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 5px 8px;
            margin-bottom: 6px;
        }
        .bank-name {
            font-size: 10px;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            display: flex;
            justify-content: space-between;
        }
        .bank-detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 8.5px;
            color: #334155;
            margin-top: 1px;
        }

        /* METADATA BAR */
        .meta-strip {
            display: flex;
            justify-content: space-between;
            background: #f1f5f9;
            padding: 4px 6px;
            font-size: 9px;
            font-weight: 700;
            border-radius: 4px;
            margin-bottom: 6px;
        }
        .challan-id {
            color: #dc2626;
            font-family: monospace;
            font-size: 10px;
        }

        /* STUDENT BIO TABLE */
        .bio-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 9px;
        }
        .bio-table td {
            padding: 2px 3px;
            border-bottom: 1px solid #f1f5f9;
        }
        .bio-label {
            color: #64748b;
            font-weight: 600;
            width: 32%;
        }
        .bio-val {
            color: #0f172a;
            font-weight: 700;
        }

        /* PARTICULARS TABLE */
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            font-size: 9px;
        }
        .fee-table th {
            background: #0f172a;
            color: #ffffff;
            font-weight: 700;
            padding: 4px;
            text-align: left;
            font-size: 8.5px;
            text-transform: uppercase;
        }
        .fee-table th.text-end, .fee-table td.text-end {
            text-align: right;
        }
        .fee-table td {
            padding: 3px 4px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }
        .fee-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .fee-table .total-row td {
            background: #f1f5f9;
            font-weight: 800;
            border-top: 1.5px solid #0f172a;
            border-bottom: 1.5px solid #0f172a;
            color: #0f172a;
        }
        .concession-row td {
            color: #16a34a !important;
            font-weight: 700;
        }

        /* HIGHLIGHT PAYABLE BOX */
        .payable-card {
            background: #f8fafc;
            border: 1.5px solid #0f172a;
            border-radius: 4px;
            padding: 6px 8px;
            margin-bottom: 6px;
        }
        .payable-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2px;
        }
        .payable-line.final {
            border-top: 1px dashed #cbd5e1;
            padding-top: 3px;
            margin-top: 3px;
        }
        .payable-amount {
            font-size: 13px;
            font-weight: 900;
            color: #0f172a;
        }
        .payable-amount.after-due {
            color: #dc2626;
            font-size: 12px;
        }
        .words-box {
            font-size: 8px;
            font-style: italic;
            color: #475569;
            line-height: 1.2;
            margin-top: 3px;
        }

        /* DATES */
        .dates-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 4px;
            margin-bottom: 6px;
            text-align: center;
        }
        .date-chip {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 3px 2px;
        }
        .date-chip span {
            display: block;
            font-size: 7.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }
        .date-chip strong {
            font-size: 8.5px;
            color: #0f172a;
        }
        .date-chip.due-highlight {
            border-color: #dc2626;
            background: #fef2f2;
        }
        .date-chip.due-highlight strong {
            color: #dc2626;
        }

        /* INSTRUCTIONS & FOOTER */
        .instructions {
            font-size: 7.5px;
            color: #64748b;
            line-height: 1.2;
            margin-bottom: 8px;
            border-top: 1px solid #f1f5f9;
            padding-top: 4px;
        }
        .stamp-row {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 16px;
        }
        .stamp-box {
            border-top: 1px solid #0f172a;
            width: 46%;
            text-align: center;
            font-size: 8px;
            font-weight: 700;
            color: #0f172a;
            padding-top: 2px;
        }

        .barcode-strip {
            text-align: center;
            letter-spacing: 4px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 11px;
            color: #0f172a;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            margin-top: 4px;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                color: #000000 !important;
            }
            .no-print {
                display: none !important;
            }
            .modal, .modal-backdrop {
                display: none !important;
            }
            .challan-page {
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                gap: 8px !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            .voucher-copy {
                border: 1px solid #000000 !important;
                page-break-inside: avoid !important;
            }
            .voucher-copy:not(:last-child)::after {
                border-right: 1px dashed #000000 !important;
            }
            @page {
                size: A4 landscape;
                margin: 6mm;
            }
        }
    </style>
</head>
<body>

    <!-- TOP ACTION CONTROLS (HIDDEN ON PRINT) -->
    <div class="no-print">
        <?php if(isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3 py-2 px-3 fw-bold" role="alert">
                <i class="fa fa-check-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="top-toolbar">
            <!-- Left Controls: Navigation & Class Filters -->
            <div class="d-flex align-items-center flex-wrap gap-2">
                <a href="<?php echo URLROOT; ?>/fees/challan" class="btn-custom btn-custom-secondary">
                    <i class="fa fa-arrow-left"></i> Challan Hub
                </a>
                <a href="<?php echo URLROOT; ?>/fees/collect" class="btn-custom btn-custom-outline">
                    <i class="fa fa-cash-register me-1 text-primary"></i> Fee Hub
                </a>

                <!-- Class Switcher -->
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light fw-bold text-muted small"><i class="fa fa-graduation-cap me-1"></i> Class</span>
                    <select class="form-select form-select-sm fw-bold text-dark" onchange="location.href='<?php echo URLROOT; ?>/fees/batchChallans?class_id=' + this.value + '&month=<?php echo urlencode($month); ?>&year=<?php echo $year; ?>'">
                        <?php foreach($classes as $cls): ?>
                            <option value="<?php echo $cls->id; ?>" <?php echo ($currentClassId == $cls->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cls->class_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Month & Year Switcher -->
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light fw-bold text-muted small"><i class="fa fa-calendar me-1"></i> Month</span>
                    <select class="form-select form-select-sm fw-bold text-dark" onchange="location.href='<?php echo URLROOT; ?>/fees/batchChallans?class_id=<?php echo $currentClassId; ?>&month=' + this.value + '&year=<?php echo $year; ?>'">
                        <?php 
                        $monthsList = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                        foreach($monthsList as $m): 
                        ?>
                            <option value="<?php echo $m; ?>" <?php echo (strtolower($month) === strtolower($m)) ? 'selected' : ''; ?>>
                                <?php echo $m; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-select form-select-sm fw-bold text-dark" onchange="location.href='<?php echo URLROOT; ?>/fees/batchChallans?class_id=<?php echo $currentClassId; ?>&month=<?php echo urlencode($month); ?>&year=' + this.value">
                        <?php for($y = (int)date('Y') - 1; $y <= (int)date('Y') + 1; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 fw-bold" style="font-size: 0.82rem;">
                    <i class="fa fa-users me-1"></i> <?php echo count($challans); ?> Student Challans
                </span>
            </div>

            <!-- Right Controls: Customization Modals, Live Edit, & Print -->
            <div class="d-flex align-items-center flex-wrap gap-2">
                <!-- Button 1: Bank & Fine Settings -->
                <button type="button" class="btn-custom btn-custom-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#bankChallanModal">
                    <i class="fa fa-landmark"></i> Bank &amp; Fine (Rs. <?php echo number_format($lateFine); ?>)
                </button>

                <!-- Button 2: Fee Particulars Setup -->
                <button type="button" class="btn-custom btn-custom-outline shadow-sm" data-bs-toggle="modal" data-bs-target="#feeParticularsModal">
                    <i class="fa fa-list-check text-primary"></i> Fee Particulars Guide
                </button>

                <!-- Button 3: Live In-Screen Edit Toggle -->
                <button type="button" class="btn-custom btn-custom-outline shadow-sm" id="toggleLiveEditBtn" title="Click to edit bank details, late fine, dates, or student text directly on this page before printing">
                    <i class="fa fa-edit text-success"></i> <span id="liveEditText">Live Edit On</span>
                </button>

                <!-- Button 4: Print All Challans -->
                <button onclick="window.print()" class="btn-custom btn-custom-primary shadow-sm fw-bold px-3">
                    <i class="fa fa-print"></i> Print All <?php echo count($challans); ?> Challans
                </button>
            </div>
        </div>

        <!-- Floating helper alert when live edit is active -->
        <div id="liveEditAlert" class="alert alert-warning border-0 shadow-sm mt-2 py-2 px-3 d-none align-items-center justify-content-between" style="border-radius: 8px;">
            <div>
                <i class="fa fa-pencil-alt me-1 text-warning-emphasis"></i>
                <strong>Live Edit Active:</strong> You can click directly on the highlighted bank name, account number, late fee fine (Rs. 200), and fee heads on any voucher to tweak them live before printing!
            </div>
            <button type="button" class="btn-close" onclick="document.getElementById('toggleLiveEditBtn').click()"></button>
        </div>
    </div>

    <?php if(empty($challans)): ?>
        <div style="max-width: 600px; margin: 60px auto; background: #fff; padding: 40px; border-radius: 16px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <i class="fa fa-receipt" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
            <h3 style="font-weight: 800; color: #0f172a; margin-bottom: 8px;">No Students Found in <?php echo htmlspecialchars($classObj ? $classObj->class_name : 'Class'); ?></h3>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">There are no active enrolled students in this class to generate fee challans for.</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="<?php echo URLROOT; ?>/fees/challan" class="btn-custom btn-custom-primary">Back to Challan Hub</a>
                <a href="<?php echo URLROOT; ?>/students/admission" class="btn-custom btn-custom-outline">Add New Admission</a>
            </div>
        </div>
    <?php else: ?>

        <!-- LOOP THROUGH EACH STUDENT CHALLAN SHEET -->
        <?php foreach($challans as $c): 
            $student = $c->student;
            $schoolName = !empty($c->school->name) ? $c->school->name : 'PAK ACADEMY MODEL HIGH SCHOOL';
            $schoolAddress = !empty($c->school->address) ? $c->school->address : 'Main Campus, Educational Block, Lahore, Pakistan';
            $schoolPhone = !empty($c->school->phone) ? $c->school->phone : '+92 42 35889000';
            $studentBank = $c->bank ?: $bank;
        ?>
        <div class="challan-page">
            
            <?php
            $copies = [
                'BANK COPY' => 'To be retained by Bank',
                'SCHOOL COPY' => 'To be submitted to School Accounts Office',
                'STUDENT / PARENT COPY' => 'To be retained by Student/Parent'
            ];

            foreach ($copies as $copyTitle => $copySub):
            ?>
            <div class="voucher-copy">
                <div>
                    <!-- HEADER -->
                    <div class="voucher-header">
                        <span class="copy-tag"><?php echo $copyTitle; ?></span>
                        <div class="school-title live-editable"><?php echo htmlspecialchars($schoolName); ?></div>
                        <div class="school-sub"><?php echo htmlspecialchars($schoolAddress); ?> &bull; <?php echo htmlspecialchars($schoolPhone); ?></div>
                    </div>

                    <!-- BANK INFO BOX -->
                    <div class="bank-box">
                        <div class="bank-name">
                            <span class="live-editable bank-name-text"><?php echo htmlspecialchars($studentBank->bank_name); ?></span>
                            <span style="font-size: 8px; color: #64748b;" class="live-editable bank-branch-text"><?php echo htmlspecialchars($studentBank->branch_name ?: 'Online All Branches'); ?></span>
                        </div>
                        <div class="bank-detail-row">
                            <span>A/C Title: <strong class="live-editable bank-title-text"><?php echo htmlspecialchars($studentBank->account_title); ?></strong></span>
                        </div>
                        <div class="bank-detail-row">
                            <span>A/C No: <strong class="live-editable bank-acc-text"><?php echo htmlspecialchars($studentBank->account_no); ?></strong></span>
                            <span>IBAN: <strong class="live-editable bank-iban-text"><?php echo htmlspecialchars($studentBank->iban ?: 'N/A'); ?></strong></span>
                        </div>
                    </div>

                    <!-- METADATA STRIP -->
                    <div class="meta-strip">
                        <span>Challan: <strong class="challan-id"><?php echo htmlspecialchars($c->challan_no); ?></strong></span>
                        <span>Session: <?php echo htmlspecialchars($c->session_name); ?></span>
                        <span>Period: <strong><?php echo htmlspecialchars($c->billing_period); ?></strong></span>
                    </div>

                    <!-- STUDENT BIO TABLE -->
                    <table class="bio-table">
                        <tr>
                            <td class="bio-label">Student Name:</td>
                            <td class="bio-val live-editable"><?php echo htmlspecialchars($student->student_name); ?></td>
                        </tr>
                        <tr>
                            <td class="bio-label">Father's Name:</td>
                            <td class="bio-val live-editable"><?php echo htmlspecialchars($student->father_name ?: 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="bio-label">Class &amp; Section:</td>
                            <td class="bio-val"><?php echo htmlspecialchars($student->class_name . ' (' . ($student->section_name ?: 'General') . ')'); ?></td>
                        </tr>
                        <tr>
                            <td class="bio-label">Admission / Roll:</td>
                            <td class="bio-val">#<?php echo htmlspecialchars($student->admission_no ?: '-'); ?> &bull; Roll: <?php echo htmlspecialchars($student->roll_no ?: '-'); ?></td>
                        </tr>
                        <?php if(!empty($student->family_code)): ?>
                        <tr>
                            <td class="bio-label">Family Code:</td>
                            <td class="bio-val"><?php echo htmlspecialchars($student->family_code); ?> <?php if($c->sibling_discount_percent > 0): ?><span style="color:#16a34a;">(<?php echo (float)$c->sibling_discount_percent; ?>% Concession)</span><?php endif; ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>

                    <!-- FEE HEADS PARTICULARS TABLE -->
                    <table class="fee-table">
                        <thead>
                            <tr>
                                <th style="width: 25px;">#</th>
                                <th>Fee Particulars</th>
                                <th class="text-end" style="width: 70px;">Amount (PKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sr = 1;
                            foreach($c->fee_items as $item): 
                            ?>
                            <tr>
                                <td><?php echo $sr++; ?></td>
                                <td class="live-editable"><?php echo htmlspecialchars($item->type_name); ?></td>
                                <td class="text-end live-editable fee-item-amt"><?php echo number_format($item->amount, 2); ?></td>
                            </tr>
                            <?php endforeach; ?>

                            <!-- Subtotal -->
                            <tr class="total-row">
                                <td colspan="2">Gross Payable:</td>
                                <td class="text-end gross-subtotal-val"><?php echo number_format($c->gross_subtotal, 2); ?></td>
                            </tr>

                            <!-- Sibling Concession if applicable -->
                            <?php if($c->sibling_discount_amount > 0): ?>
                            <tr class="concession-row">
                                <td colspan="2"><i class="fa fa-tag"></i> Sibling Concession (<?php echo (float)$c->sibling_discount_percent; ?>%):</td>
                                <td class="text-end">- <?php echo number_format($c->sibling_discount_amount, 2); ?></td>
                            </tr>
                            <?php endif; ?>

                            <!-- Arrears if any -->
                            <?php if($c->arrears > 0): ?>
                            <tr>
                                <td colspan="2" style="color: #dc2626; font-weight: 700;">Previous Arrears:</td>
                                <td class="text-end" style="color: #dc2626; font-weight: 700;"><?php echo number_format($c->arrears, 2); ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- PAYABLE CARDS -->
                    <div class="payable-card">
                        <div class="payable-line">
                            <span style="font-weight: 700; color: #0f172a; text-transform: uppercase; font-size: 9px;">Payable Within Due Date:</span>
                            <span class="payable-amount net-within-due-val">Rs. <?php echo number_format($c->net_payable_within_due_date); ?>/-</span>
                        </div>
                        <div class="words-box">
                            (<?php echo htmlspecialchars($c->amount_in_words); ?>)
                        </div>

                        <div class="payable-line final">
                            <span style="font-size: 8px; color: #dc2626; font-weight: 600;">
                                Late Fee Surcharge: Rs. <strong class="live-editable late-fine-val"><?php echo number_format($c->late_fine); ?></strong>
                            </span>
                        </div>
                        <div class="payable-line">
                            <span style="font-weight: 800; color: #dc2626; text-transform: uppercase; font-size: 8.5px;">Payable After Due Date:</span>
                            <span class="payable-amount after-due after-due-val">Rs. <?php echo number_format($c->net_payable_after_due_date); ?>/-</span>
                        </div>
                    </div>

                    <!-- DATES SCHEDULE -->
                    <div class="dates-grid">
                        <div class="date-chip">
                            <span>Issue Date</span>
                            <strong class="live-editable"><?php echo date('d-M-Y', strtotime($c->issue_date)); ?></strong>
                        </div>
                        <div class="date-chip due-highlight">
                            <span>Due Date</span>
                            <strong class="live-editable"><?php echo date('d-M-Y', strtotime($c->due_date)); ?></strong>
                        </div>
                        <div class="date-chip">
                            <span>Validity</span>
                            <strong class="live-editable"><?php echo date('d-M-Y', strtotime($c->validity_date)); ?></strong>
                        </div>
                    </div>

                    <!-- INSTRUCTIONS -->
                    <div class="instructions live-editable">
                        <?php if(!empty($c->instructions)): ?>
                            <?php echo nl2br(htmlspecialchars($c->instructions)); ?>
                        <?php else: ?>
                            &bull; Fee is payable at any online branch of <span class="bank-name-text"><?php echo htmlspecialchars($studentBank->bank_name); ?></span>.<br>
                            &bull; Payment after due date incurs late surcharge of Rs. <span class="late-fine-val"><?php echo number_format($c->late_fine); ?></span>.<br>
                            &bull; Retain this voucher copy securely for future record &amp; roll slip issuance.
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <!-- STAMPS & SIGNATURES -->
                    <div class="stamp-row">
                        <div class="stamp-box">
                            School Accounts Officer
                        </div>
                        <div class="stamp-box">
                            Bank Teller Stamp &amp; Date
                        </div>
                    </div>

                    <!-- BARCODE STRIP -->
                    <div class="barcode-strip">
                        ||||| | |||| || ||||| ||| |||||||
                        <div style="font-size: 8px; font-weight: 600; letter-spacing: 1px;"><?php echo htmlspecialchars($c->barcode); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
        <?php endforeach; ?>

    <?php endif; ?>

    <!-- ============================================================== -->
    <!-- MODAL 1: BANK DETAILS & LATE FINE SETTINGS                     -->
    <!-- ============================================================== -->
    <div class="modal fade no-print" id="bankChallanModal" tabindex="-1" aria-labelledby="bankChallanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom py-3 px-4 bg-primary text-white" style="border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold text-white mb-0" id="bankChallanModalLabel">
                        <i class="fa fa-landmark me-2"></i>Collection Bank &amp; Challan Rules
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo URLROOT; ?>/fees/saveChallanSettings" method="POST">
                    <input type="hidden" name="redirect_to" value="/fees/batchChallans?class_id=<?php echo $currentClassId; ?>&month=<?php echo urlencode($month); ?>&year=<?php echo $year; ?>&success=bank_updated">
                    <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">

                    <div class="modal-body p-4 bg-light">
                        <!-- BANK PARTICULARS CARD -->
                        <div class="bg-white p-3 rounded-3 border mb-3">
                            <h6 class="fw-bold text-dark mb-3 text-uppercase small">
                                <i class="fa fa-building-columns text-primary me-1"></i> Bank Account Information
                            </h6>
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($bank ? $bank->bank_name : 'Habib Bank Limited (HBL)'); ?>" required placeholder="e.g. Habib Bank Limited (HBL) / Meezan Bank / MCB">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Branch Name / Code</label>
                                <input type="text" name="branch_name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($bank ? $bank->branch_name : 'Main Boulevard Branch (0412)'); ?>" placeholder="e.g. Main Boulevard Branch (0412) / All Online Branches">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Account Title</label>
                                <input type="text" name="account_title" class="form-control form-control-sm" value="<?php echo htmlspecialchars($bank ? $bank->account_title : 'Pak Academy Model School System'); ?>" required placeholder="e.g. Pak Academy Model High School">
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Account Number</label>
                                    <input type="text" name="account_no" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($bank ? $bank->account_no : '1029-3847-2910-01'); ?>" required placeholder="e.g. 1029-3847-2910-01">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">IBAN Number</label>
                                    <input type="text" name="iban" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($bank ? $bank->iban : 'PK36HABB0001029384729101'); ?>" placeholder="e.g. PK36HABB0001029384729101">
                                </div>
                            </div>
                        </div>

                        <!-- LATE FINE & CHALLAN POLICY -->
                        <div class="bg-white p-3 rounded-3 border">
                            <h6 class="fw-bold text-dark mb-3 text-uppercase small">
                                <i class="fa fa-clock text-danger me-1"></i> Late Fee Fine Surcharge &amp; Rules
                            </h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-danger text-uppercase mb-1">
                                    Late Fee Fine Amount (Rs.)
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light fw-bold text-danger">PKR</span>
                                    <input type="number" step="10" name="late_fine" class="form-control fw-bold fs-6 text-danger" value="<?php echo htmlspecialchars($lateFine); ?>" required placeholder="e.g. 200">
                                    <span class="input-group-text bg-light small">/-</span>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Aap yahan fine change kar sakte hain (e.g. 200, 300, 500 Rs). Ye amount due date guzarne ke baad challan ke total me auto-add hogi.
                                </small>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1">Challan Instructions / Terms (Printed on Voucher)</label>
                                <textarea name="instructions" class="form-control form-control-sm" rows="3" placeholder="&bull; Fee is payable at any branch...&#10;&bull; Late payment surcharge applies after due date..."><?php echo htmlspecialchars($instructions); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-4 bg-white" style="border-radius: 0 0 16px 16px;">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
                            <i class="fa fa-save me-1"></i> Save Bank &amp; Fine Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- MODAL 2: FEE PARTICULARS (HEADS) ROADMAP & SETUP               -->
    <!-- ============================================================== -->
    <div class="modal fade no-print" id="feeParticularsModal" tabindex="-1" aria-labelledby="feeParticularsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom py-3 px-4 bg-dark text-white" style="border-radius: 16px 16px 0 0;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-white text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="fa fa-list-check"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="feeParticularsModalLabel">
                                Fee Particulars (Heads) &amp; Structure Setup
                            </h5>
                            <small class="text-white-50">Fee heads kahan se add hoti hain aur challan me kaise aati hain</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- Workflow 3-Step Guide Card -->
                    <div class="bg-white p-3 rounded-3 border mb-4">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="fa fa-lightbulb me-1 text-warning"></i> Fee Particulars Kahan Se Manage Hoti Hain?
                        </h6>
                        <p class="small text-muted mb-3">
                            Challan me aane wali tamam fees (jaise Tuition Fee, Lab Fund, Exam Fee, Admission Fee) 3 aasan steps me system ke andar configure hoti hain:
                        </p>

                        <div class="row g-3">
                            <!-- Step 1 -->
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-primary px-2 py-1">Step 1</span>
                                            <i class="fa fa-tags text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Fee Heads / Types</h6>
                                        <p class="small text-muted mb-2" style="font-size: 0.78rem;">
                                            Yahan naye fee heads bante hain (e.g. Monthly Tuition, Lab Fund, Sports, Exam Fee).
                                        </p>
                                    </div>
                                    <a href="<?php echo URLROOT; ?>/fees/types" target="_blank" class="btn btn-primary btn-sm fw-bold w-100">
                                        <i class="fa fa-external-link-alt me-1"></i> Manage Fee Heads
                                    </a>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-success px-2 py-1">Step 2</span>
                                            <i class="fa fa-layer-group text-success"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Fee Packages (Groups)</h6>
                                        <p class="small text-muted mb-2" style="font-size: 0.78rem;">
                                            Yahan har class ya wing ke liye in fee heads ke monthly rates aur late fines set hote hain.
                                        </p>
                                    </div>
                                    <a href="<?php echo URLROOT; ?>/fees/groups" target="_blank" class="btn btn-success btn-sm fw-bold w-100">
                                        <i class="fa fa-external-link-alt me-1"></i> Set Package Rates
                                    </a>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-info px-2 py-1 text-white">Step 3</span>
                                            <i class="fa fa-user-plus text-info"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Assign to Class</h6>
                                        <p class="small text-muted mb-2" style="font-size: 0.78rem;">
                                            Class 1 ya kisi bhi class ke bacho ko wo package assign kar diya jata hai.
                                        </p>
                                    </div>
                                    <a href="<?php echo URLROOT; ?>/fees/assign" target="_blank" class="btn btn-info text-white btn-sm fw-bold w-100">
                                        <i class="fa fa-external-link-alt me-1"></i> Assign to Class
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Add Fee Head Form Directly from this Modal -->
                    <div class="bg-white p-3 rounded-3 border mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0 small text-uppercase">
                                <i class="fa fa-plus-circle text-success me-1"></i> Add New Fee Particular &amp; Apply to Class
                            </h6>
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1" style="font-size: 0.72rem;">
                                Instant Auto-Assign to <?php echo htmlspecialchars($classObj ? $classObj->class_name : 'Selected Class'); ?>
                            </span>
                        </div>
                        <p class="text-muted small mb-3" style="font-size: 0.8rem;">
                            Yahan naya fee particular (head) aur uski amount likhein. Ye foran Fee Module me add ho jayega aur is class ke sabhi students ke challans me include ho jayega:
                        </p>
                        <form action="<?php echo URLROOT; ?>/fees/saveChallanSettings" method="POST" class="row g-2 align-items-end">
                            <input type="hidden" name="redirect_to" value="/fees/batchChallans?class_id=<?php echo $currentClassId; ?>&month=<?php echo urlencode($month); ?>&year=<?php echo $year; ?>&success=type_added">
                            <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
                            <input type="hidden" name="class_id" value="<?php echo $currentClassId; ?>">

                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Particular Name</label>
                                <input type="text" name="new_fee_type_name" class="form-control form-control-sm" placeholder="e.g. Transport, Computer Fund, Sports" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Code</label>
                                <input type="text" name="new_fee_type_code" class="form-control form-control-sm text-uppercase" placeholder="e.g. TRN" maxlength="6">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Monthly Amount (Rs.)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light fw-bold text-muted">PKR</span>
                                    <input type="number" step="50" name="new_fee_type_amount" class="form-control form-control-sm fw-bold" placeholder="e.g. 500" value="500" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success btn-sm fw-bold w-100 py-1.5 shadow-sm">
                                    <i class="fa fa-plus me-1"></i> Add &amp; Apply Now
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Currently Active Fee Heads in System -->
                    <div class="bg-white p-3 rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0 small text-uppercase">
                                <i class="fa fa-list-ul text-primary me-1"></i> Active Fee Heads in System (<?php echo count($feeTypes); ?>)
                            </h6>
                            <a href="<?php echo URLROOT; ?>/fees/types" target="_blank" class="small text-decoration-none fw-bold">Full Screen &rarr;</a>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if(!empty($feeTypes)): ?>
                                <?php foreach($feeTypes as $ft): ?>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5" style="font-size: 0.8rem;">
                                        <strong><?php echo htmlspecialchars($ft->type_name); ?></strong>
                                        <span class="text-muted ms-1">(<?php echo htmlspecialchars($ft->type_code); ?>)</span>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted small">No custom fee heads created yet. System default heads (Tuition, Lab, Exam, Utility) are being used.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-white" style="border-radius: 0 0 16px 16px;">
                    <a href="<?php echo URLROOT; ?>/fees/collect" target="_blank" class="btn btn-outline-primary btn-sm px-3 fw-bold me-auto">
                        <i class="fa fa-bolt me-1"></i> Batch Generate Monthly Vouchers
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- JAVASCRIPT FOR LIVE INLINE EDITING & REAL-TIME RECALCULATION   -->
    <!-- ============================================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var liveEditBtn = document.getElementById('toggleLiveEditBtn');
            var liveEditText = document.getElementById('liveEditText');
            var liveEditAlert = document.getElementById('liveEditAlert');
            var isLiveEdit = false;

            if (liveEditBtn) {
                liveEditBtn.addEventListener('click', function() {
                    isLiveEdit = !isLiveEdit;
                    var editables = document.querySelectorAll('.live-editable');

                    if (isLiveEdit) {
                        document.body.classList.add('live-edit-active');
                        liveEditBtn.classList.remove('btn-custom-outline');
                        liveEditBtn.classList.add('btn-custom-success');
                        liveEditText.textContent = 'Live Edit Active (Click Text)';
                        if (liveEditAlert) liveEditAlert.classList.remove('d-none');

                        editables.forEach(function(el) {
                            el.setAttribute('contenteditable', 'true');
                        });
                    } else {
                        document.body.classList.remove('live-edit-active');
                        liveEditBtn.classList.add('btn-custom-outline');
                        liveEditBtn.classList.remove('btn-custom-success');
                        liveEditText.textContent = 'Live Edit Off';
                        if (liveEditAlert) liveEditAlert.classList.add('d-none');

                        editables.forEach(function(el) {
                            el.removeAttribute('contenteditable');
                        });
                    }
                });
            }

            // Sync bank changes across all vouchers when user edits in live mode
            document.addEventListener('input', function(e) {
                if (!isLiveEdit) return;

                // Sync Bank Name
                if (e.target.classList.contains('bank-name-text')) {
                    var val = e.target.textContent;
                    document.querySelectorAll('.bank-name-text').forEach(function(node) {
                        if (node !== e.target) node.textContent = val;
                    });
                }
                // Sync Branch Name
                if (e.target.classList.contains('bank-branch-text')) {
                    var val = e.target.textContent;
                    document.querySelectorAll('.bank-branch-text').forEach(function(node) {
                        if (node !== e.target) node.textContent = val;
                    });
                }
                // Sync Late Fine Amount & Recalculate After Due Date Total
                if (e.target.classList.contains('late-fine-val')) {
                    var rawVal = e.target.textContent.replace(/[^0-9.]/g, '');
                    var fineAmt = parseFloat(rawVal) || 0;

                    // Update all voucher copies
                    document.querySelectorAll('.voucher-copy').forEach(function(voucher) {
                        var fineNode = voucher.querySelector('.late-fine-val');
                        if (fineNode && fineNode !== e.target) {
                            fineNode.textContent = fineAmt;
                        }

                        var withinDueNode = voucher.querySelector('.net-within-due-val');
                        var afterDueNode = voucher.querySelector('.after-due-val');
                        if (withinDueNode && afterDueNode) {
                            var withinVal = parseFloat(withinDueNode.textContent.replace(/[^0-9.]/g, '')) || 0;
                            var newTotal = withinVal + fineAmt;
                            afterDueNode.textContent = 'Rs. ' + newTotal.toLocaleString() + '/-';
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
