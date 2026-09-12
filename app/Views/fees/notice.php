<?php
$c = $data['challan'];
$student = $c->student;
$bank = $data['bank'] ?? $c->bank;
$schoolName = !empty($c->school->name) ? $c->school->name : 'PAK ACADEMY MODEL HIGH SCHOOL';
$schoolAddress = !empty($c->school->address) ? $c->school->address : 'Main Campus, Educational Block, Lahore, Pakistan';
$schoolPhone = !empty($c->school->phone) ? $c->school->phone : '+92 42 35889000';
$refNo = 'REF/ACC/' . date('Y') . '/' . str_pad($student->id, 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Default Notice - <?php echo htmlspecialchars($student->student_name); ?></title>
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
        }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-secondary { background: #64748b; color: #fff; }

        /* OFFICIAL NOTICE SHEET */
        .notice-sheet {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border-top: 6px solid #dc2626;
            position: relative;
        }

        /* LETTERHEAD */
        .letterhead {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }
        .school-title {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .school-sub {
            font-size: 11px;
            color: #475569;
            margin-top: 4px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 12px;
            color: #334155;
        }

        .recipient-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 24px;
        }
        .subject-line {
            font-size: 14px;
            font-weight: 800;
            color: #dc2626;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 16px;
        }

        .notice-body {
            font-size: 13px;
            line-height: 1.6;
            color: #1e293b;
            margin-bottom: 20px;
        }

        .dues-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
        }
        .dues-table th {
            background: #0f172a;
            color: #ffffff;
            padding: 8px 12px;
            text-align: left;
            text-transform: uppercase;
        }
        .dues-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .dues-table .total-row td {
            font-weight: 900;
            background: #fee2e2;
            color: #dc2626;
            font-size: 14px;
            border-top: 2px solid #dc2626;
        }

        .warning-box {
            background: #fff1f2;
            border-left: 4px solid #e11d48;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 12px;
            color: #881337;
            margin-bottom: 24px;
        }

        .signatures-row {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding-top: 20px;
        }
        .sig-box {
            width: 200px;
            text-align: center;
            border-top: 1.5px solid #0f172a;
            padding-top: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }

        @media print {
            body { background: #ffffff; padding: 0; }
            .no-print { display: none !important; }
            .notice-sheet { box-shadow: none; border-radius: 0; padding: 20px 30px; }
            @page { size: A4 portrait; margin: 15mm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div>
            <a href="<?php echo URLROOT; ?>/fees/defaulters" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to Defaulters Ledger
            </a>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="<?php echo URLROOT; ?>/fees/challan/<?php echo $student->id; ?>" target="_blank" class="btn btn-secondary">
                <i class="fa fa-receipt"></i> View 3-Copy Challan
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print Demand Notice
            </button>
        </div>
    </div>

    <!-- OFFICIAL DEMAND NOTICE -->
    <div class="notice-sheet">
        <!-- LETTERHEAD -->
        <div class="letterhead">
            <div class="school-title"><?php echo htmlspecialchars($schoolName); ?></div>
            <div class="school-sub"><?php echo htmlspecialchars($schoolAddress); ?></div>
            <div class="school-sub">Phone: <?php echo htmlspecialchars($schoolPhone); ?> &bull; Accounts &amp; Recovery Division</div>
        </div>

        <!-- REFERENCE & DATE -->
        <div class="meta-row">
            <div>Ref No: <strong class="font-monospace"><?php echo $refNo; ?></strong></div>
            <div>Date of Issue: <strong><?php echo date('d-F-Y'); ?></strong></div>
        </div>

        <!-- RECIPIENT CARD -->
        <div class="recipient-card">
            <div>To,</div>
            <div style="font-weight: 800; font-size: 14px; margin-top: 2px;">
                Parent / Guardian of: <?php echo htmlspecialchars($student->student_name); ?>
            </div>
            <div style="font-size: 12px; color: #475569; margin-top: 4px;">
                Father: <strong><?php echo htmlspecialchars($student->father_name ?: 'N/A'); ?></strong> &bull;
                Admission No: <strong>#<?php echo htmlspecialchars($student->admission_no ?: '-'); ?></strong> &bull;
                Class: <strong><?php echo htmlspecialchars($student->class_name . ' (' . ($student->section_name ?: 'General') . ')'); ?></strong> &bull;
                Roll No: <strong><?php echo htmlspecialchars($student->roll_no ?: '-'); ?></strong>
            </div>
        </div>

        <!-- SUBJECT -->
        <div class="subject-line">
            Subject: FINAL NOTICE &amp; DEMAND FOR SETTLEMENT OF OVERDUE SCHOOL DUES
        </div>

        <!-- BODY -->
        <div class="notice-body">
            <p style="margin-bottom: 12px;">
                Respected Parent / Guardian,
            </p>
            <p style="margin-bottom: 12px;">
                It has come to the urgent attention of the School Accounts &amp; Finance Directorate that the institutional composite tuition fees for your ward, <strong><?php echo htmlspecialchars($student->student_name); ?></strong>, have remained unpaid despite standard scheduled fee vouchers having been issued for the billing cycle.
            </p>
            <p style="margin-bottom: 12px;">
                As an institution committed to providing quality education, sustained utilities, and state-of-the-art laboratory access, timely clearance of scheduled dues is essential for operational continuity. The outstanding breakdown is summarized below:
            </p>

            <!-- DUES BREAKDOWN TABLE -->
            <table class="dues-table">
                <thead>
                    <tr>
                        <th>Fee Head / Particulars</th>
                        <th>Billing Cycle</th>
                        <th style="text-align: right;">Amount (PKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($c->fee_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item->type_name); ?></td>
                        <td><?php echo htmlspecialchars($c->billing_period); ?></td>
                        <td style="text-align: right;"><?php echo number_format($item->amount, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if($c->sibling_discount_amount > 0): ?>
                    <tr style="color: #16a34a; font-weight: 600;">
                        <td colspan="2">Sibling Concession (<?php echo (float)$c->sibling_discount_percent; ?>% Applied):</td>
                        <td style="text-align: right;">- Rs. <?php echo number_format($c->sibling_discount_amount, 2); ?></td>
                    </tr>
                    <?php endif; ?>

                    <tr class="total-row">
                        <td colspan="2">Total Net Overdue Payable:</td>
                        <td style="text-align: right;">Rs. <?php echo number_format($c->net_payable_within_due_date); ?>/-</td>
                    </tr>
                </tbody>
            </table>

            <div style="font-size: 11px; font-style: italic; color: #475569; margin-bottom: 16px;">
                Amount in Words: <strong><?php echo htmlspecialchars($c->amount_in_words); ?></strong>
            </div>

            <!-- WARNING BOX -->
            <div class="warning-box">
                <strong><i class="fa fa-exclamation-triangle me-1"></i> IMPORTANT REGULATORY DIRECTIVE:</strong><br>
                Please deposit the outstanding dues at any online branch of <strong><?php echo htmlspecialchars($bank->bank_name); ?></strong> (A/C: <?php echo htmlspecialchars($bank->account_no); ?>) or at the School Cash Counter within <strong>five (05) working days</strong> of this notice. Failure to do so may compel the administration to withhold the student's examination roll number slip, term result DMC, or suspend parent portal privileges.
            </div>

            <p>
                If payment has already been deposited within the last 48 hours, please disregard this notice and share a copy of the stamped bank deposit slip with the Accounts Office for ledger reconciliation.
            </p>
            <p style="margin-top: 12px;">
                Thanking you for your cooperation and understanding.
            </p>
        </div>

        <!-- SIGNATURES -->
        <div class="signatures-row">
            <div class="sig-box">
                Accounts Officer<br>
                <span style="font-size: 10px; color: #64748b; font-weight: normal;">Finance &amp; Audit Division</span>
            </div>
            <div class="sig-box">
                Principal / Head of Institution<br>
                <span style="font-size: 10px; color: #64748b; font-weight: normal;">(Official Stamp &amp; Seal)</span>
            </div>
        </div>
    </div>

</body>
</html>
