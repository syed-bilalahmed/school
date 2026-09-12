<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip - <?php echo htmlspecialchars($data['payslip']->name); ?> (<?php echo htmlspecialchars($data['payslip']->month . ' ' . $data['payslip']->year); ?>)</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-slate: #1e293b;
            --accent-blue: #2563eb;
            --border-soft: #eef2f6;
            --bg-subtle: #f8fafc;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #334155;
            padding: 20px 10px;
            font-size: 0.84rem;
            line-height: 1.45;
        }

        .slip-card {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 20px -3px rgba(15, 23, 42, 0.06);
            padding: 28px 32px;
            position: relative;
        }

        /* Header Logo Branding */
        .school-logo-img {
            max-height: 52px;
            max-width: 180px;
            object-fit: contain;
        }

        .school-logo-badge {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
        }

        .school-name {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: #0f172a;
            font-size: 1.35rem;
            letter-spacing: -0.3px;
        }

        .voucher-badge {
            font-family: monospace;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Subtle Section Labels */
        .section-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 6px;
        }

        /* Soft Table Styling - NO HEAVY BOLD BORDERS */
        .clean-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #eef2f6;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 0;
        }

        .clean-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 7px 12px;
            border-bottom: 1px solid #eef2f6;
            border-top: none;
        }

        .clean-table td {
            padding: 6px 12px;
            font-size: 0.82rem;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
        }

        .clean-table tr:last-child td {
            border-bottom: none;
        }

        .info-grid {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 8px;
            padding: 10px 14px;
        }

        .info-grid td {
            padding: 4px 6px;
            font-size: 0.82rem;
            border: none;
        }

        .info-grid td.label-cell {
            color: #64748b;
            font-weight: 500;
            width: 38%;
        }

        .info-grid td.val-cell {
            color: #0f172a;
            font-weight: 600;
        }

        /* Net Payable Hero Card */
        .net-card {
            background: #0f172a;
            color: #ffffff;
            border-radius: 10px;
            padding: 14px 20px;
        }

        .net-val {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: #38bdf8;
        }

        .words-text {
            font-size: 0.76rem;
            color: #94a3b8;
            font-style: italic;
        }

        /* Signature Lines */
        .sig-box {
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
            margin-top: 26px;
            text-align: center;
            font-size: 0.78rem;
            font-weight: 600;
            color: #475569;
        }

        /* Screen Toolbar */
        .toolbar-wrap {
            max-width: 800px;
            margin: 0 auto 14px auto;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 6mm 8mm;
            }
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .slip-card {
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                page-break-inside: avoid;
            }
            .toolbar-wrap {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<?php
$p = $data['payslip'];
$settings = $data['settings'] ?? null;
$schoolName = $settings->school_name ?? SITENAME;
$schoolLogo = $settings->logo ?? '';
$schoolPhone = $settings->phone ?? '';
$schoolEmail = $settings->email ?? '';
$schoolAddr = $settings->address ?? '';

$isVisiting = strpos($p->employment_type ?? '', 'Visiting') !== false;
$isPaid = ($p->status == 'Paid');

// Helper function: Convert number to English Words
if (!function_exists('numToWords')) {
    function numToWords($number) {
        $hyphen      = ' ';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $dictionary  = array(
            0                   => 'Zero',
            1                   => 'One',
            2                   => 'Two',
            3                   => 'Three',
            4                   => 'Four',
            5                   => 'Five',
            6                   => 'Six',
            7                   => 'Seven',
            8                   => 'Eight',
            9                   => 'Nine',
            10                  => 'Ten',
            11                  => 'Eleven',
            12                  => 'Twelve',
            13                  => 'Thirteen',
            14                  => 'Fourteen',
            15                  => 'Fifteen',
            16                  => 'Sixteen',
            17                  => 'Seventeen',
            18                  => 'Eighteen',
            19                  => 'Nineteen',
            20                  => 'Twenty',
            30                  => 'Thirty',
            40                  => 'Forty',
            50                  => 'Fifty',
            60                  => 'Sixty',
            70                  => 'Seventy',
            80                  => 'Eighty',
            90                  => 'Ninety',
            100                 => 'Hundred',
            1000                => 'Thousand',
            100000              => 'Lakh',
            10000000            => 'Crore'
        );
        $number = (int)$number;
        if ($number < 0) return $negative . numToWords(abs($number));
        if ($number == 0) return 'Zero';

        if ($number < 21) return $dictionary[$number];
        if ($number < 100) {
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            return $dictionary[$tens] . ($units ? $hyphen . $dictionary[$units] : '');
        }
        if ($number < 1000) {
            $hundreds  = (int)($number / 100);
            $remainder = $number % 100;
            return $dictionary[$hundreds] . ' ' . $dictionary[100] . ($remainder ? $conjunction . numToWords($remainder) : '');
        }
        if ($number < 100000) {
            $thousands = (int)($number / 1000);
            $remainder = $number % 1000;
            return numToWords($thousands) . ' ' . $dictionary[1000] . ($remainder ? $separator . numToWords($remainder) : '');
        }
        if ($number < 10000000) {
            $lakhs     = (int)($number / 100000);
            $remainder = $number % 100000;
            return numToWords($lakhs) . ' ' . $dictionary[100000] . ($remainder ? $separator . numToWords($remainder) : '');
        }
        $crores    = (int)($number / 10000000);
        $remainder = $number % 10000000;
        return numToWords($crores) . ' ' . $dictionary[10000000] . ($remainder ? $separator . numToWords($remainder) : '');
    }
}
$amountWords = numToWords((float)$p->net_salary) . ' Rupees Only';
?>

<!-- On-Screen Action Toolbar -->
<div class="toolbar-wrap d-flex align-items-center justify-content-between">
    <a href="<?php echo URLROOT; ?>/payroll/index" class="btn btn-sm btn-outline-secondary px-3">
        <i class="fa fa-arrow-left me-1"></i> Back to Payroll
    </a>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-primary px-3 fw-semibold shadow-xs" onclick="window.print()">
            <i class="fa fa-print me-1"></i> Print Payslip
        </button>
    </div>
</div>

<div class="slip-card">
    <!-- 1. Header Section with School Logo -->
    <div class="row align-items-center pb-3 mb-3 border-bottom">
        <div class="col-7">
            <div class="d-flex align-items-center gap-3">
                <?php if(!empty($schoolLogo) && file_exists(APPROOT . '/../public/' . $schoolLogo)): ?>
                    <img src="<?php echo URLROOT . '/' . htmlspecialchars($schoolLogo); ?>" alt="School Logo" class="school-logo-img">
                <?php else: ?>
                    <div class="school-logo-badge flex-shrink-0">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h3 class="school-name mb-0"><?php echo htmlspecialchars($schoolName); ?></h3>
                    <div class="text-muted small" style="font-size: 0.78rem;">
                        Salary Disbursement Voucher &bull; Confidential
                        <?php if($schoolPhone): ?> &bull; <?php echo htmlspecialchars($schoolPhone); ?><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-5 text-end">
            <div class="voucher-badge d-inline-block mb-1">VOUCHER #PAY-<?php echo str_pad($p->id, 5, '0', STR_PAD_LEFT); ?></div>
            <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($p->month . ' ' . $p->year); ?></div>
            <?php if($isPaid): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 mt-1 fw-bold" style="font-size: 0.72rem;">
                    <i class="fa fa-check-circle me-1"></i>PAID &amp; DISBURSED
                </span>
            <?php else: ?>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 mt-1 fw-bold" style="font-size: 0.72rem;">
                    <i class="fa fa-clock me-1"></i>GENERATED / DUE
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. Employee Profile Particulars Table -->
    <div class="mb-3">
        <div class="section-title"><i class="fa fa-id-card me-1 text-primary"></i> Personal &amp; Employee Information</div>
        <table class="clean-table">
            <tbody>
                <tr>
                    <td class="bg-light fw-semibold text-muted" style="width: 18%;">Employee Name</td>
                    <td class="fw-bold text-dark" style="width: 32%;"><?php echo htmlspecialchars($p->name); ?></td>
                    <td class="bg-light fw-semibold text-muted" style="width: 18%;">NADRA CNIC</td>
                    <td class="fw-semibold font-monospace" style="width: 32%;"><?php echo htmlspecialchars($p->cnic ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <td class="bg-light fw-semibold text-muted">Staff Code / ID</td>
                    <td><span class="badge bg-primary-subtle text-primary font-monospace"><?php echo htmlspecialchars($p->staff_code ?: 'STF-001'); ?></span></td>
                    <td class="bg-light fw-semibold text-muted">Employment Type</td>
                    <td>
                        <?php if($isVisiting): ?>
                            <span class="badge bg-purple-subtle text-purple border" style="color: #7c3aed; background: rgba(124, 58, 237, 0.1);">Visiting Faculty</span>
                        <?php else: ?>
                            <span class="badge bg-success-subtle text-success border"><?php echo htmlspecialchars($p->employment_type ?? 'Permanent'); ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="bg-light fw-semibold text-muted">Designation</td>
                    <td class="fw-semibold"><?php echo htmlspecialchars($p->designation ?: ucfirst($p->role)); ?></td>
                    <td class="bg-light fw-semibold text-muted">Bank &amp; Account</td>
                    <td class="fw-semibold font-monospace"><?php echo htmlspecialchars($p->bank_name ?: 'Cash Window'); ?> &bull; <?php echo htmlspecialchars($p->bank_account_no ?: 'Counter'); ?></td>
                </tr>
                <tr>
                    <td class="bg-light fw-semibold text-muted">Department</td>
                    <td class="fw-semibold"><?php echo htmlspecialchars($p->department ?: 'Academics'); ?></td>
                    <td class="bg-light fw-semibold text-muted">Disbursement</td>
                    <td class="fw-semibold text-success"><?php echo htmlspecialchars($p->payment_mode ?? 'Cash'); ?> <?php echo !empty($p->payment_date) ? '<small class="text-muted">('.date('d M, Y', strtotime($p->payment_date)).')</small>' : ''; ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- 3. Component Breakdown (SOFT CLEAN BORDERS) -->
    <div class="mb-3">
        <div class="section-title"><i class="fa fa-calculator me-1 text-primary"></i> Earnings &amp; Deductions</div>
        <div class="row g-3">
            <!-- Earnings -->
            <div class="col-6">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>Earnings / Allowances</th>
                            <th class="text-end">Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($isVisiting): ?>
                            <tr>
                                <td>
                                    Visiting Delivered Lectures
                                    <div class="smaller text-muted" style="font-size: 0.72rem;"><?php echo $p->lectures_delivered; ?> Lecs &times; Rs. <?php echo number_format((float)$p->lecture_rate); ?></div>
                                </td>
                                <td class="text-end fw-semibold">Rs. <?php echo number_format((float)$p->basic_salary); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td>Basic Pay</td>
                                <td class="text-end fw-semibold">Rs. <?php echo number_format((float)$p->basic_salary); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Medical &amp; Conveyance Allowance</td>
                            <td class="text-end fw-semibold text-success">+Rs. <?php echo number_format((float)$p->total_allowance); ?></td>
                        </tr>
                        <tr style="background: #f8fafc; font-weight: 600;">
                            <td class="text-dark">Gross Earnings (A):</td>
                            <td class="text-end text-success fw-bold">Rs. <?php echo number_format((float)$p->basic_salary + (float)$p->total_allowance); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Deductions -->
            <div class="col-6">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>Deductions &amp; Withholdings</th>
                            <th class="text-end">Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $leaveCut = (float)($p->leave_deduction ?? 0);
                        $statutoryDeduc = max(0, (float)$p->total_deduction - $leaveCut);
                        ?>
                        <?php if($leaveCut > 0): ?>
                            <tr>
                                <td>
                                    Attendance Leave Cutting
                                    <div class="smaller text-muted" style="font-size: 0.72rem;">
                                        <?php echo (float)($p->absent_days ?? 0); ?> Absents &bull; <?php echo (float)($p->half_days ?? 0); ?> Half Days
                                    </div>
                                </td>
                                <td class="text-end fw-semibold text-danger">-Rs. <?php echo number_format($leaveCut); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Income Tax &amp; Statutory Withholdings</td>
                            <td class="text-end fw-semibold text-danger">-Rs. <?php echo number_format($statutoryDeduc); ?></td>
                        </tr>
                        <tr>
                            <td>Provident Fund / Other Loans</td>
                            <td class="text-end text-muted">-Rs. 0</td>
                        </tr>
                        <tr style="background: #f8fafc; font-weight: 600;">
                            <td class="text-dark">Total Deductions (B):</td>
                            <td class="text-end text-danger fw-bold">-Rs. <?php echo number_format((float)$p->total_deduction); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 4. Net Salary Hero Card -->
    <div class="net-card d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="text-uppercase fw-semibold text-white-50" style="font-size: 0.68rem; letter-spacing: 0.6px;">Net Disbursed Amount (A - B)</div>
            <div class="net-val">Rs. <?php echo number_format((float)$p->net_salary); ?></div>
            <div class="words-text"><i class="fa fa-quote-left me-1 opacity-50"></i><?php echo $amountWords; ?><i class="fa fa-quote-right ms-1 opacity-50"></i></div>
        </div>
        <div class="text-end d-none d-sm-block text-white-50">
            <i class="fa fa-shield-check fa-2x opacity-75"></i>
        </div>
    </div>

    <!-- 5. Remarks (if any) -->
    <?php if(!empty($p->note)): ?>
        <div class="alert alert-light border py-1 px-3 mb-3 small text-muted" style="font-size: 0.78rem;">
            <strong class="text-dark">Disbursement Remarks:</strong> <?php echo htmlspecialchars($p->note); ?>
        </div>
    <?php endif; ?>

    <!-- 6. Official Signature Lines -->
    <div class="row g-3">
        <div class="col-4">
            <div class="sig-box">
                Employee Signature<br>
                <span class="text-muted fw-normal smaller"><?php echo htmlspecialchars($p->name); ?></span>
            </div>
        </div>
        <div class="col-4">
            <div class="sig-box">
                Prepared By (Accounts)<br>
                <span class="text-muted fw-normal smaller">Finance Officer</span>
            </div>
        </div>
        <div class="col-4">
            <div class="sig-box">
                Authorized By (Principal)<br>
                <small class="text-muted fw-normal smaller">School Official Stamp</small>
            </div>
        </div>
    </div>

    <!-- 7. Footer Notice -->
    <div class="text-center text-muted mt-3 pt-2 border-top" style="font-size: 0.7rem;">
        Computer-generated official payslip of <?php echo htmlspecialchars($schoolName); ?> &bull; System Ref: PAY-<?php echo str_pad($p->id, 5, '0', STR_PAD_LEFT); ?> &bull; Printed: <?php echo date('d M, Y H:i'); ?>
    </div>
</div>

</body>
</html>
