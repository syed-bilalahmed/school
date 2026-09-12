<?php
$c = $data['challan'];
$student = $c->student;
$bank = $data['bank'] ?? $c->bank;
$schoolName = !empty($c->school->name) ? $c->school->name : 'PAK ACADEMY MODEL HIGH SCHOOL';
$schoolAddress = !empty($c->school->address) ? $c->school->address : 'Main Campus, Educational Block, Lahore, Pakistan';
$schoolPhone = !empty($c->school->phone) ? $c->school->phone : '+92 42 35889000';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Challan - <?php echo htmlspecialchars($c->challan_no . ' - ' . $student->student_name); ?></title>
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
            color: #1e293b;
            padding: 20px;
            font-size: 11px;
        }
        .no-print {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1300px;
            margin: 0 auto 20px auto;
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
            transition: all 0.2s ease;
        }
        .btn-primary {
            background: #2563eb;
            color: #fff;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: #64748b;
            color: #fff;
        }
        .btn-secondary:hover {
            background: #475569;
        }

        /* 3-COPY CHALLAN CONTAINER */
        .challan-container {
            max-width: 1300px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            background: #ffffff;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        /* SINGLE COPY VOUCHER */
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
        .bank-detail-row strong {
            font-weight: 600;
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
        .late-row td {
            color: #dc2626 !important;
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

        /* BARCODE EMULATION */
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
                background: #ffffff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .challan-container {
                box-shadow: none;
                padding: 0;
                width: 100%;
                max-width: 100%;
                gap: 8px;
            }
            .voucher-copy {
                border: 1px solid #000000;
                page-break-inside: avoid;
            }
            .voucher-copy:not(:last-child)::after {
                border-right: 1px dashed #000000;
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
        <div style="display: flex; align-items: center; gap: 8px;">
            <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Fee Register
            </a>
            <a href="<?php echo URLROOT; ?>/fees/challan" class="btn btn-secondary">
                <i class="fa fa-university"></i> Challan Hub
            </a>
            <div>
                <strong style="font-size: 14px;"><?php echo htmlspecialchars($student->student_name); ?></strong>
                <span style="color: #64748b; font-size: 12px; margin-left: 6px;">Class: <?php echo htmlspecialchars($student->class_name . ' - ' . ($student->section_name ?: 'General')); ?></span>
                <span style="color: #dc2626; font-weight: 700; font-size: 12px; margin-left: 8px;"><?php echo htmlspecialchars($c->challan_no); ?></span>
            </div>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="<?php echo URLROOT; ?>/fees/batchChallans?class_id=<?php echo $student->class_id; ?>" class="btn btn-secondary">
                <i class="fa fa-layer-group"></i> Batch Print Class
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print 3-Copy Challan
            </button>
        </div>
    </div>

    <!-- 3-COPY CHALLAN CONTAINER -->
    <div class="challan-container">
        
        <?php
        $copies = [
            'BANK COPY' => 'To be retained by Bank',
            'SCHOOL COPY' => 'To be submitted to School Accounts Office',
            'STUDENT / PARENT COPY' => 'To be retained by Student/Parent'
        ];

        foreach ($copies as $copyTitle => $copySub):
        ?>
        <!-- SINGLE COPY VOUCHER -->
        <div class="voucher-copy">
            <div>
                <!-- HEADER -->
                <div class="voucher-header">
                    <span class="copy-tag"><?php echo $copyTitle; ?></span>
                    <div class="school-title"><?php echo htmlspecialchars($schoolName); ?></div>
                    <div class="school-sub"><?php echo htmlspecialchars($schoolAddress); ?> &bull; <?php echo htmlspecialchars($schoolPhone); ?></div>
                </div>

                <!-- BANK INFO BOX -->
                <div class="bank-box">
                    <div class="bank-name">
                        <span><?php echo htmlspecialchars($bank->bank_name); ?></span>
                        <span style="font-size: 8px; color: #64748b;"><?php echo htmlspecialchars($bank->branch_name ?: 'Online All Branches'); ?></span>
                    </div>
                    <div class="bank-detail-row">
                        <span>A/C Title: <strong><?php echo htmlspecialchars($bank->account_title); ?></strong></span>
                    </div>
                    <div class="bank-detail-row">
                        <span>A/C No: <strong><?php echo htmlspecialchars($bank->account_no); ?></strong></span>
                        <span>IBAN: <strong><?php echo htmlspecialchars($bank->iban ?: 'N/A'); ?></strong></span>
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
                        <td class="bio-val"><?php echo htmlspecialchars($student->student_name); ?></td>
                    </tr>
                    <tr>
                        <td class="bio-label">Father's Name:</td>
                        <td class="bio-val"><?php echo htmlspecialchars($student->father_name ?: 'N/A'); ?></td>
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
                            <td><?php echo htmlspecialchars($item->type_name); ?></td>
                            <td class="text-end"><?php echo number_format($item->amount, 2); ?></td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Subtotal -->
                        <tr class="total-row">
                            <td colspan="2">Gross Payable:</td>
                            <td class="text-end"><?php echo number_format($c->gross_subtotal, 2); ?></td>
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
                            <td colspan="2" style="color: #dc2626; font-weight: 700;">Previous Arrears / Unpaid:</td>
                            <td class="text-end" style="color: #dc2626; font-weight: 700;"><?php echo number_format($c->arrears, 2); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- PAYABLE CARDS -->
                <div class="payable-card">
                    <div class="payable-line">
                        <span style="font-weight: 700; color: #0f172a; text-transform: uppercase; font-size: 9px;">Payable Within Due Date:</span>
                        <span class="payable-amount">Rs. <?php echo number_format($c->net_payable_within_due_date); ?>/-</span>
                    </div>
                    <div class="words-box">
                        (<?php echo htmlspecialchars($c->amount_in_words); ?>)
                    </div>

                    <div class="payable-line final">
                        <span style="font-size: 8px; color: #dc2626; font-weight: 600;">Late Fee Surcharge: Rs. <?php echo number_format($c->late_fine); ?></span>
                    </div>
                    <div class="payable-line">
                        <span style="font-weight: 800; color: #dc2626; text-transform: uppercase; font-size: 8.5px;">Payable After Due Date:</span>
                        <span class="payable-amount after-due">Rs. <?php echo number_format($c->net_payable_after_due_date); ?>/-</span>
                    </div>
                </div>

                <!-- DATES SCHEDULE -->
                <div class="dates-grid">
                    <div class="date-chip">
                        <span>Issue Date</span>
                        <strong><?php echo date('d-M-Y', strtotime($c->issue_date)); ?></strong>
                    </div>
                    <div class="date-chip due-highlight">
                        <span>Due Date</span>
                        <strong><?php echo date('d-M-Y', strtotime($c->due_date)); ?></strong>
                    </div>
                    <div class="date-chip">
                        <span>Validity</span>
                        <strong><?php echo date('d-M-Y', strtotime($c->validity_date)); ?></strong>
                    </div>
                </div>

                <!-- INSTRUCTIONS -->
                <div class="instructions">
                    &bull; Fee is payable at any online branch of <?php echo htmlspecialchars($bank->bank_name); ?>.<br>
                    &bull; Payment after due date incurs late surcharge of Rs. <?php echo number_format($c->late_fine); ?>.<br>
                    &bull; Retain this voucher copy securely for future record &amp; roll slip issuance.
                </div>
            </div>

            <div>
                <!-- STAMPS & SIGNATURES -->
                <div class="stamp-row">
                    <div class="stamp-box">
                        School Cashier / Accounts
                    </div>
                    <div class="stamp-box">
                        Bank Teller &amp; Scroll Stamp
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

</body>
</html>
