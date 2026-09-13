<?php
$schoolName = $data['school']->school_name ?? $data['settings']->school_name ?? SITENAME ?? 'Executive Model High School & College';
$activePage = 'fees';
$theme = $data['settings']->theme_color ?? 'default';

// Color adaptation
$themeColor = '#4f46e5';
if ($theme === 'red') $themeColor = '#e11d48';
elseif ($theme === 'green') $themeColor = '#059669';
elseif ($theme === 'dark') $themeColor = '#0f172a';

$student = $data['student'] ?? null;
$challan = $data['challan'] ?? null;
$bank = $data['bank'] ?? null;
$month = $data['month'] ?? date('F');
$year = $data['year'] ?? (int)date('Y');
$admissionNo = $data['admissionNo'] ?? '';

$monthsList = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Bank Fee Challan &mdash; <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Luxury Frontend Design System -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/frontend.css?v=3.3">

    <style>
        .challan-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 40px 0 50px;
            color: #ffffff;
        }

        .challan-search-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
            padding: 24px;
            margin-top: -30px;
            position: relative;
            z-index: 10;
        }

        /* 3-Copy Bank Challan Grid */
        .challan-sheet {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            padding: 24px;
            margin-bottom: 40px;
        }

        .challan-col {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 14px;
            background: #ffffff;
            font-size: 0.75rem;
            position: relative;
            color: #1e293b;
        }

        .challan-col-header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 10px;
            text-align: center;
        }

        .challan-copy-badge {
            display: inline-block;
            background: #0f172a;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 2px 10px;
            border-radius: 4px;
            margin-bottom: 4px;
        }

        .challan-school-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1px;
            line-height: 1.2;
        }

        .challan-meta-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #e2e8f0;
            padding: 3px 0;
            font-size: 0.72rem;
        }

        .challan-fee-table {
            width: 100%;
            margin: 8px 0;
            border-collapse: collapse;
            font-size: 0.7rem;
        }
        .challan-fee-table th {
            background: #f1f5f9;
            padding: 4px 6px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
            font-weight: 700;
        }
        .challan-fee-table td {
            padding: 3px 6px;
            border-bottom: 1px solid #f1f5f9;
        }

        .challan-total-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 6px 8px;
            margin: 8px 0;
        }

        .barcode-strip {
            font-family: monospace;
            letter-spacing: 4px;
            font-weight: bold;
            text-align: center;
            padding: 4px;
            background: #f1f5f9;
            border-radius: 4px;
            font-size: 0.7rem;
        }

        /* PRINT STYLING - Standard A4 3-Copy */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .d-print-none, .site-header, .site-footer, .emergency-alert-bar, .whatsapp-float-widget {
                display: none !important;
            }
            .challan-sheet {
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
            }
            .container {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
            }
            .challan-col {
                border: 1px solid #000000 !important;
                page-break-inside: avoid !important;
            }
            @page {
                size: A4 landscape;
                margin: 6mm;
            }
        }
    </style>
</head>
<body style="background: #f8fafc;">

    <!-- TOP NAVIGATION BAR -->
    <div class="d-print-none">
        <?php require_once APPROOT . '/Views/home/partials/navbar.php'; ?>
    </div>

    <!-- HERO HEADER -->
    <header class="challan-hero d-print-none">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                            <i class="fa fa-receipt me-1"></i> Official Pakistani Bank Format
                        </span>
                        <span class="text-white-50 small">&bull; 1Link 1Bill &amp; HBL Partnered</span>
                    </div>
                    <h1 class="h2 fw-extrabold text-white mb-2">Student Bank Fee Challan Portal</h1>
                    <p class="text-white-50 mb-0" style="max-width: 620px;">
                        Instant lookup and 1-click A4 print of your child's 3-copy fee voucher. Accepted at all 1Link ATM machines, mobile banking apps, and designated branch tellers.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="<?php echo URLROOT; ?>/home/fees" class="btn btn-outline-light rounded-pill px-4 fw-semibold">
                        <i class="fa fa-calculator me-1"></i> Fee Calculator &amp; Rates
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container pb-5">
        <!-- SEARCH BAR (HIDDEN IN PRINT) -->
        <div class="challan-search-card d-print-none mb-4">
            <form action="<?php echo URLROOT; ?>/home/challan" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-id-card"></i></span>
                        <input type="text" name="admission_no" class="form-control bg-light border-start-0 ps-0" placeholder="Enter Admission No (e.g. STD-26-001) or Roll No" value="<?php echo htmlspecialchars($admissionNo, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="month" class="form-select bg-light">
                        <?php foreach($monthsList as $m): ?>
                            <option value="<?php echo $m; ?>" <?php echo ($m === $month) ? 'selected' : ''; ?>><?php echo $m; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="year" class="form-select bg-light">
                        <option value="<?php echo date('Y'); ?>" <?php echo ($year == date('Y')) ? 'selected' : ''; ?>><?php echo date('Y'); ?></option>
                        <option value="<?php echo date('Y')+1; ?>" <?php echo ($year == date('Y')+1) ? 'selected' : ''; ?>><?php echo date('Y')+1; ?></option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3 shadow-sm">
                        <i class="fa fa-search me-1"></i> Fetch Challan
                    </button>
                </div>
            </form>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="text-muted smaller" style="font-size: 0.75rem;">
                    <i class="fa fa-info-circle text-primary me-1"></i> Enter the Student Admission No found on Student ID card, previous fee slip, or admission dossier.
                </span>
                <?php if($student): ?>
                    <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 fw-bold shadow-sm" onclick="window.print()">
                        <i class="fa fa-print me-1"></i> Print 3-Copy Challan (A4)
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if($student && $challan): 
            $copies = [
                ['title' => 'Bank Copy', 'desc' => 'To be retained by Bank Branch', 'badge' => 'bg-dark'],
                ['title' => 'School Copy', 'desc' => 'To be submitted to Campus Accounts', 'badge' => 'bg-primary'],
                ['title' => 'Student Copy', 'desc' => 'To be retained by Parent / Scholar', 'badge' => 'bg-success']
            ];
            $bankName = $bank->bank_name ?? 'Habib Bank Limited (HBL)';
            $acTitle = $bank->account_title ?? $schoolName;
            $acNo = $bank->account_no ?? '0123-45678901-03';
            $iban = $bank->iban ?? 'PK36HABB00012345678901';
            $billerId = $bank->biller_id ?? ('100' . str_pad($student->id, 5, '0', STR_PAD_LEFT));
        ?>

            <!-- ACTION BAR (HIDDEN IN PRINT) -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 d-print-none">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-pill px-3 py-2">
                        <i class="fa fa-check-circle me-1"></i> Verified Challan Record
                    </span>
                    <span class="text-muted small">
                        Scholar: <strong><?php echo htmlspecialchars($student->name, ENT_QUOTES, 'UTF-8'); ?></strong> &bull; 
                        Class: <strong><?php echo htmlspecialchars($student->class_name ?? 'General', ENT_QUOTES, 'UTF-8'); ?></strong> &bull; 
                        Billing Month: <strong><?php echo htmlspecialchars($month . ' ' . $year, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm" onclick="window.print()">
                        <i class="fa fa-print me-2"></i> Print 3-Copy Challan (A4)
                    </button>
                </div>
            </div>

            <!-- THE 3-COPY CHALLAN SHEET -->
            <div class="challan-sheet">
                <div class="row g-2">
                    <?php foreach($copies as $copy): ?>
                        <div class="col-md-4">
                            <div class="challan-col">
                                <!-- Copy Header -->
                                <div class="challan-col-header">
                                    <div class="challan-copy-badge <?php echo $copy['badge']; ?>">
                                        <?php echo $copy['title']; ?>
                                    </div>
                                    <div class="challan-school-title">
                                        <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.65rem;">
                                        Main Executive Campus &bull; Fee Collection Voucher
                                    </div>
                                    <div class="text-secondary fw-semibold mt-1" style="font-size: 0.65rem;">
                                        <?php echo $copy['desc']; ?>
                                    </div>
                                </div>

                                <!-- Bank Particulars -->
                                <div class="bg-light p-2 rounded mb-2 border">
                                    <div class="fw-bold text-dark" style="font-size: 0.72rem;"><?php echo htmlspecialchars($bankName, ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="d-flex justify-content-between" style="font-size: 0.68rem;">
                                        <span class="text-muted">A/C Title:</span>
                                        <strong><?php echo htmlspecialchars($acTitle, ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between" style="font-size: 0.68rem;">
                                        <span class="text-muted">A/C No:</span>
                                        <span class="font-monospace fw-bold"><?php echo htmlspecialchars($acNo, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between" style="font-size: 0.68rem;">
                                        <span class="text-muted">1Link 1Bill ID:</span>
                                        <span class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($billerId, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </div>

                                <!-- Challan Meta -->
                                <div class="challan-meta-row">
                                    <span class="text-muted">Challan No:</span>
                                    <strong class="font-monospace"><?php echo htmlspecialchars($challan->challan_no, ENT_QUOTES, 'UTF-8'); ?></strong>
                                </div>
                                <div class="challan-meta-row">
                                    <span class="text-muted">Issue Date:</span>
                                    <span><?php echo htmlspecialchars($challan->issue_date, ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="challan-meta-row">
                                    <span class="text-muted">Due Date:</span>
                                    <strong class="text-danger"><?php echo htmlspecialchars($challan->due_date, ENT_QUOTES, 'UTF-8'); ?></strong>
                                </div>
                                <div class="challan-meta-row">
                                    <span class="text-muted">Billing Month:</span>
                                    <strong><?php echo htmlspecialchars($month . ' ' . $year, ENT_QUOTES, 'UTF-8'); ?></strong>
                                </div>

                                <!-- Student Particulars -->
                                <div class="my-2 py-1 border-top border-bottom">
                                    <div class="challan-meta-row">
                                        <span class="text-muted">Student Name:</span>
                                        <strong class="text-dark"><?php echo htmlspecialchars($student->name, ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </div>
                                    <div class="challan-meta-row">
                                        <span class="text-muted">Father Name:</span>
                                        <span><?php echo htmlspecialchars($student->father_name ?: 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="challan-meta-row">
                                        <span class="text-muted">Admission No:</span>
                                        <span class="font-monospace fw-bold"><?php echo htmlspecialchars($student->admission_no, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="challan-meta-row">
                                        <span class="text-muted">Class &amp; Section:</span>
                                        <strong><?php echo htmlspecialchars(($student->class_name ?? 'Class') . ' - ' . ($student->section_name ?? 'A'), ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </div>
                                    <div class="challan-meta-row">
                                        <span class="text-muted">Roll No:</span>
                                        <span><?php echo htmlspecialchars($student->roll_no ?: '001', ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </div>

                                <!-- Fee Breakdown Table -->
                                <table class="challan-fee-table">
                                    <thead>
                                        <tr>
                                            <th>Particulars</th>
                                            <th class="text-end">Amount (Rs.)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($challan->fee_items)): ?>
                                            <?php foreach($challan->fee_items as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item->type_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td class="text-end"><?php echo number_format($item->amount, 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td>Monthly Tuition Fee</td>
                                                <td class="text-end">8,500.00</td>
                                            </tr>
                                            <tr>
                                                <td>Lab &amp; Library Charges</td>
                                                <td class="text-end">500.00</td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if(!empty($challan->sibling_discount_amount) && $challan->sibling_discount_amount > 0): ?>
                                            <tr class="text-success">
                                                <td>Sibling Concession (<?php echo $challan->sibling_discount_percent; ?>%)</td>
                                                <td class="text-end">-<?php echo number_format($challan->sibling_discount_amount, 2); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>

                                <!-- Total Box -->
                                <div class="challan-total-box">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold">Payable Within Due Date:</span>
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">
                                            Rs. <?php echo number_format($challan->net_payable_within_due_date, 2); ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.65rem;">
                                        <span>Late Fee Surcharge:</span>
                                        <span>Rs. <?php echo number_format($challan->late_fine, 2); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-1 pt-1 border-top fw-bold text-danger" style="font-size: 0.8rem;">
                                        <span>Payable After Due Date:</span>
                                        <span>Rs. <?php echo number_format($challan->net_payable_after_due_date, 2); ?></span>
                                    </div>
                                </div>

                                <!-- Barcode Representation -->
                                <div class="barcode-strip my-2">
                                    |||| | | ||| |||| | ||||| | |||
                                    <div style="font-size: 0.6rem; letter-spacing: 1px;"><?php echo htmlspecialchars($challan->barcode, ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>

                                <!-- Signatures Footer -->
                                <div class="row pt-3 mt-2 text-center" style="font-size: 0.62rem;">
                                    <div class="col-6">
                                        <div class="border-top pt-1">Authorized Officer</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border-top pt-1">Bank Cashier / Stamp</div>
                                    </div>
                                </div>

                                <div class="text-muted text-center mt-2 pt-1 border-top" style="font-size: 0.58rem; line-height: 1.2;">
                                    Fee non-refundable. Paid via 1Link ATMs, Internet Banking &amp; HBL counters.
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif($admissionNo && !$student): ?>
            <!-- STUDENT NOT FOUND -->
            <div class="candidate-card p-5 text-center">
                <div class="text-warning mb-3" style="font-size: 3rem;">
                    <i class="fa fa-triangle-exclamation"></i>
                </div>
                <h3 class="fw-bold text-dark mb-2">No Student Found</h3>
                <p class="text-muted mb-4" style="max-width: 480px; margin-left: auto; margin-right: auto;">
                    We could not locate an enrolled student matching <strong>"<?php echo htmlspecialchars($admissionNo, ENT_QUOTES, 'UTF-8'); ?>"</strong>.
                </p>
                <div class="p-3 bg-light rounded-3 d-inline-block text-start mb-4" style="border: 1px dashed #cbd5e1; max-width: 450px;">
                    <div class="small fw-bold text-dark mb-1">Check the following:</div>
                    <ul class="mb-0 small text-muted ps-3">
                        <li>Ensure admission number format matches (e.g. <strong>STD-26-001</strong>).</li>
                        <li>You can also search by the student's <strong>NADRA B-Form CNIC</strong>.</li>
                        <li>If you recently enrolled, please confirm fee voucher generation with accounts.</li>
                    </ul>
                </div>
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?php echo URLROOT; ?>/home/challan" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fa fa-rotate-left me-1"></i> Try Another Search
                    </a>
                    <a href="<?php echo URLROOT; ?>/home/fees" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa fa-calculator me-1"></i> Fee Rates &amp; Policy
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- INITIAL LANDING / EXPLAINER -->
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="card p-4 border-0 shadow-sm rounded-3 h-100 text-center">
                        <div class="fs-1 mb-2">🏛️</div>
                        <h5 class="fw-bold text-dark">Habib Bank Branches</h5>
                        <p class="text-muted small mb-0">Present the 3-copy printed challan at any designated HBL branch across the country for direct counter payment.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 border-0 shadow-sm rounded-3 h-100 text-center">
                        <div class="fs-1 mb-2">📱</div>
                        <h5 class="fw-bold text-dark">1Link 1Bill Payments</h5>
                        <p class="text-muted small mb-0">Pay instantly from any mobile banking app, ATM, JazzCash, or EasyPaisa using your unique 1Bill Consumer Number.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 border-0 shadow-sm rounded-3 h-100 text-center">
                        <div class="fs-1 mb-2">🖨️</div>
                        <h5 class="fw-bold text-dark">1-Click A4 Printing</h5>
                        <p class="text-muted small mb-0">Formatted specifically for A4 paper. Prints Bank, School, and Student copies side-by-side with zero page cutting.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- FOOTER -->
    <div class="d-print-none">
        <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
