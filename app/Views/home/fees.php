<?php
$schoolName = $data['school']->school_name ?? $data['settings']->school_name ?? SITENAME ?? 'Imperial Academy';
$activePage = 'fees';
$pageTitle = !empty($data['page']->title) ? $data['page']->title : 'Tuition & Fee Structure (2026-2027)';
$pageDesc = !empty($data['page']->meta_description) ? $data['page']->meta_description : ('Transparent, structured tuition fees, sibling concessions, scholarship programs, and official payment guidelines at ' . $schoolName . '.');
$theme = $data['settings']->theme_color ?? 'default';

$classes = $data['classes'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> &mdash; <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/frontend.css?v=3.3">

    <style>
        .fee-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 5px 14px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 12px;
            backdrop-filter: blur(8px);
        }
        .pillar-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
            transition: all 0.25s ease;
            height: 100%;
        }
        .pillar-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }
        .pillar-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 16px;
        }
        .fee-table-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }
        .fee-table thead th {
            background: #0f172a;
            color: #ffffff;
            font-size: 0.84rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 16px;
            border: none;
        }
        .fee-table tbody td {
            padding: 16px;
            vertical-align: middle;
            font-size: 0.92rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .fee-table tbody tr:last-child td {
            border-bottom: none;
        }
        .fee-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .calc-wrapper {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 24px;
            color: #ffffff;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.18);
            position: relative;
            overflow: hidden;
        }
        .calc-wrapper::after {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .calc-summary-card {
            background: #ffffff;
            border-radius: 20px;
            color: #0f172a;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
        }
        .calc-input-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 6px;
            display: block;
        }
        .calc-select, .calc-radio-pill {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 0.92rem;
            font-weight: 500;
            width: 100%;
            transition: all 0.2s ease;
        }
        .calc-select:focus {
            background: rgba(255, 255, 255, 0.14);
            border-color: #60a5fa;
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.25);
            outline: none;
        }
        .calc-select option {
            background: #1e293b;
            color: #ffffff;
        }
        .calc-radio-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 8px;
        }
        .calc-radio-label {
            cursor: pointer;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.05);
            padding: 8px 10px;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 600;
            color: #e2e8f0;
            transition: all 0.2s ease;
        }
        .calc-radio-label:hover {
            background: rgba(255, 255, 255, 0.12);
        }
        .calc-radio-input {
            display: none;
        }
        .calc-radio-input:checked + .calc-radio-label {
            background: #3b82f6;
            border-color: #60a5fa;
            color: #ffffff;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.4);
        }
        .bank-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        }
        .btn-calculator-jump {
            background: #ffffff;
            color: #0f172a;
            font-weight: 700;
            border-radius: 9999px;
            padding: 8px 20px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .btn-calculator-jump:hover {
            transform: translateY(-2px);
            background: #f8fafc;
            color: #1e40af;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #printEstimateReceipt, #printEstimateReceipt * {
                visibility: visible;
            }
            #printEstimateReceipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                display: block !important;
            }
        }
    </style>
</head>
<body>

    <?php require_once APPROOT . '/Views/home/partials/navbar.php'; ?>

    <!-- HERO HEADER -->
    <header class="inner-hero">
        <div class="container">
            <div class="inner-hero-content">
                <div class="inner-breadcrumbs">
                    <a href="<?php echo URLROOT; ?>"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    <span>Admissions</span>
                    <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    <span class="text-white">Fee Schedule</span>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                    <div>
                        <span class="fee-hero-badge">
                            <i class="fa fa-calendar-check text-warning"></i> Academic Session 2026 &ndash; 2027
                        </span>
                        <h1 class="inner-page-title mb-0"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                    </div>
                    <?php if(!empty($data['page']->id) && isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin'): ?>
                        <a href="<?php echo URLROOT; ?>/frontcms/pages/edit/<?php echo $data['page']->id; ?>" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold shadow-sm">
                            <i class="fa fa-pencil-alt me-1"></i> Edit This Page in CMS
                        </a>
                    <?php endif; ?>
                </div>

                <p class="inner-page-subtitle">
                    <?php echo htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <!-- Quick Action Jumps -->
                <div class="d-flex flex-wrap gap-2 mt-3 pt-2">
                    <a href="#feeCalculator" class="btn-calculator-jump">
                        <i class="fa fa-calculator text-primary me-1"></i> Live Fee Calculator
                    </a>
                    <a href="#feeTiers" class="btn btn-outline-light rounded-pill px-3 fw-bold btn-sm">
                        <i class="fa fa-table me-1"></i> Grade-Wise Pricing
                    </a>
                    <a href="#paymentGuide" class="btn btn-outline-light rounded-pill px-3 fw-bold btn-sm">
                        <i class="fa fa-credit-card me-1"></i> Bank Payment Details
                    </a>
                    <?php if(($data['cms']->enable_online_admission ?? 'yes') === 'yes'): ?>
                        <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-success rounded-pill px-3 fw-bold btn-sm ms-auto shadow-sm">
                            <i class="fa fa-file-signature me-1"></i> Apply For Admission
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="py-5" style="background: var(--f-bg, #f8fafc);">
        <div class="container">

            <!-- 4 VALUE PILLARS (SIMPLE, CLEAN, TRUST-BUILDING) -->
            <div class="row g-4 mb-5">
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card">
                        <div class="pillar-icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="fa fa-shield-halved"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Zero Hidden Charges</h5>
                        <p class="text-muted small mb-0">Transparent billing covering digital library, laboratory access, and physical education. No surprise development funds.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card">
                        <div class="pillar-icon-box bg-success bg-opacity-10 text-success">
                            <i class="fa fa-users"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Sibling Concession</h5>
                        <p class="text-muted small mb-0">10% tuition concession guaranteed for the 2nd sibling, and 20% concession for the 3rd child and onwards.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card">
                        <div class="pillar-icon-box bg-warning bg-opacity-10 text-dark">
                            <i class="fa fa-building-columns"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Multi-Bank Channels</h5>
                        <p class="text-muted small mb-0">Pay fees easily via 1Link, mobile banking apps, Easypaisa, JazzCash, KUICKPAY, or over the counter at partner banks.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="pillar-card">
                        <div class="pillar-icon-box bg-danger bg-opacity-10 text-danger">
                            <i class="fa fa-award"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Merit Scholarships</h5>
                        <p class="text-muted small mb-0">Up to 100% tuition waiver awarded to high achievers securing top positions in BISE Board exams.</p>
                    </div>
                </div>
            </div>

            <!-- INTERACTIVE SMART FEE CALCULATOR WIDGET (HERO CONVERSION ELEMENT) -->
            <section class="mb-5" id="feeCalculator">
                <div class="calc-wrapper p-4 p-lg-5">
                    <div class="row g-4 align-items-center">
                        <!-- Controls Column -->
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary text-white text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Interactive Estimator</span>
                                <span class="badge bg-white bg-opacity-10 text-light" style="font-size: 0.72rem;">Updated Session 2026-27</span>
                            </div>
                            <h2 class="fw-extrabold text-white mb-2">Instant Fee Calculator</h2>
                            <p class="text-white text-opacity-75 small mb-4">
                                Select your child's grade, sibling status, and transport facility below to see a real-time, exact estimation of monthly and admission dues.
                            </p>

                            <div class="row g-3">
                                <!-- Grade Selection -->
                                <div class="col-md-6">
                                    <label class="calc-input-label" for="calcGradeSelect">1. Student Grade / Class</label>
                                    <select id="calcGradeSelect" class="form-select calc-select" onchange="calculateFeeEstimate()">
                                        <optgroup label="Pre-School Wing (Montessori)">
                                            <option value="playgroup" data-monthly="6500" data-admission="15000" data-security="5000" data-lab="0">Playgroup (Age 3+)</option>
                                            <option value="nursery" data-monthly="6500" data-admission="15000" data-security="5000" data-lab="0">Nursery (Age 4+)</option>
                                            <option value="prep" data-monthly="7000" data-admission="15000" data-security="5000" data-lab="0">Prep / KG (Age 5+)</option>
                                        </optgroup>
                                        <optgroup label="Primary Wing (Grades 1 - 5)">
                                            <option value="grade1" data-monthly="8000" data-admission="18000" data-security="5000" data-lab="500">Grade 1</option>
                                            <option value="grade2" data-monthly="8000" data-admission="18000" data-security="5000" data-lab="500">Grade 2</option>
                                            <option value="grade3" data-monthly="8500" data-admission="18000" data-security="5000" data-lab="500" selected>Grade 3</option>
                                            <option value="grade4" data-monthly="8500" data-admission="18000" data-security="5000" data-lab="500">Grade 4</option>
                                            <option value="grade5" data-monthly="9000" data-admission="18000" data-security="5000" data-lab="500">Grade 5</option>
                                        </optgroup>
                                        <optgroup label="Middle Wing (Grades 6 - 8)">
                                            <option value="grade6" data-monthly="10000" data-admission="20000" data-security="5000" data-lab="1000">Grade 6</option>
                                            <option value="grade7" data-monthly="10500" data-admission="20000" data-security="5000" data-lab="1000">Grade 7</option>
                                            <option value="grade8" data-monthly="11000" data-admission="20000" data-security="5000" data-lab="1000">Grade 8</option>
                                        </optgroup>
                                        <optgroup label="Senior High (Grades 9 - 10)">
                                            <option value="grade9" data-monthly="13000" data-admission="25000" data-security="7000" data-lab="2000">Grade 9 (Matric / O-Levels)</option>
                                            <option value="grade10" data-monthly="14000" data-admission="25000" data-security="7000" data-lab="2000">Grade 10 (Matric / O-Levels)</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <!-- Admission Status -->
                                <div class="col-md-6">
                                    <label class="calc-input-label">2. Enrollment Type</label>
                                    <div class="calc-radio-group">
                                        <label>
                                            <input type="radio" name="calcEnrollType" value="new" class="calc-radio-input" checked onchange="calculateFeeEstimate()">
                                            <div class="calc-radio-label"><i class="fa fa-user-plus me-1"></i> New Admission</div>
                                        </label>
                                        <label>
                                            <input type="radio" name="calcEnrollType" value="existing" class="calc-radio-input" onchange="calculateFeeEstimate()">
                                            <div class="calc-radio-label"><i class="fa fa-user-check me-1"></i> Existing Student</div>
                                        </label>
                                    </div>
                                </div>

                                <!-- Sibling Concession -->
                                <div class="col-md-6">
                                    <label class="calc-input-label">3. Sibling Concession</label>
                                    <select id="calcSiblingSelect" class="form-select calc-select" onchange="calculateFeeEstimate()">
                                        <option value="0" selected>1st Child &bull; Standard Tuition (0%)</option>
                                        <option value="10">2nd Child &bull; 10% Sibling Concession</option>
                                        <option value="20">3rd Child &bull; 20% Sibling Concession</option>
                                        <option value="25">Staff / Kinship &bull; 25% Concession</option>
                                    </select>
                                </div>

                                <!-- School Transport Route -->
                                <div class="col-md-6">
                                    <label class="calc-input-label">4. School Transport Bus</label>
                                    <select id="calcTransportSelect" class="form-select calc-select" onchange="calculateFeeEstimate()">
                                        <option value="0" selected>No Transport / Self Pick &amp; Drop (Rs. 0)</option>
                                        <option value="3500">Route Zone 1: Within 3 KM (+ Rs. 3,500/mo)</option>
                                        <option value="4500">Route Zone 2: 3 to 7 KM (+ Rs. 4,500/mo)</option>
                                        <option value="5500">Route Zone 3: 7 KM+ (+ Rs. 5,500/mo)</option>
                                    </select>
                                </div>

                                <!-- Optional Add-on Programs -->
                                <div class="col-12">
                                    <label class="calc-input-label">5. Optional Enrichment Clubs</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="addRobotics" value="800" onchange="calculateFeeEstimate()">
                                            <label class="form-check-label text-white text-opacity-90 small" for="addRobotics">
                                                STEAM &amp; Robotics Club (+ Rs. 800/mo)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="addSports" value="1000" onchange="calculateFeeEstimate()">
                                            <label class="form-check-label text-white text-opacity-90 small" for="addSports">
                                                Weekend Sports Academy (+ Rs. 1,000/mo)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estimate Output Column -->
                        <div class="col-lg-5">
                            <div class="calc-summary-card p-4">
                                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                                    <h5 class="fw-bold text-dark mb-0">Fee Estimate Summary</h5>
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold" id="estStatusBadge">New Enrollment</span>
                                </div>

                                <!-- Breakdown Lines -->
                                <div class="d-flex justify-content-between text-muted small mb-2">
                                    <span>Base Monthly Tuition:</span>
                                    <strong class="text-dark" id="dispBaseTuition">Rs. 8,500</strong>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mb-2">
                                    <span>Lab &amp; Activity Charges:</span>
                                    <strong class="text-dark" id="dispLabCharges">Rs. 500</strong>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mb-2">
                                    <span>Sibling Concession:</span>
                                    <strong class="text-success" id="dispDiscount">- Rs. 0</strong>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mb-2">
                                    <span>School Transport:</span>
                                    <strong class="text-dark" id="dispTransport">Rs. 0</strong>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mb-2">
                                    <span>Optional Clubs:</span>
                                    <strong class="text-dark" id="dispAddons">Rs. 0</strong>
                                </div>

                                <div class="p-3 my-3 rounded-3 bg-light border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 700;">Net Monthly Payable</div>
                                            <div class="fs-2 fw-extrabold text-primary" id="dispNetMonthly">Rs. 9,000</div>
                                        </div>
                                        <span class="badge bg-primary rounded-pill px-3 py-2">Monthly</span>
                                    </div>
                                </div>

                                <!-- One Time Charges (Shown for new admissions) -->
                                <div id="oneTimeSection" class="mb-3 pt-2 border-top">
                                    <div class="d-flex justify-content-between text-muted small mb-1">
                                        <span>Admission Fee (One-Time):</span>
                                        <span class="fw-bold text-dark" id="dispAdmissionFee">Rs. 18,000</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small mb-2">
                                        <span>Security Deposit (Refundable):</span>
                                        <span class="fw-bold text-dark" id="dispSecurityFee">Rs. 5,000</span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold text-dark pt-1 border-top" style="font-size: 0.95rem;">
                                        <span>Total Initial Payable (1st Mo + Adm):</span>
                                        <span class="text-danger" id="dispTotalInitial">Rs. 32,000</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 pt-2">
                                    <?php if(($data['cms']->enable_online_admission ?? 'yes') === 'yes'): ?>
                                        <a href="<?php echo URLROOT; ?>/home/admission" id="btnApplyAdmission" class="btn btn-primary-lux fw-bold rounded-pill shadow-sm">
                                            <i class="fa fa-file-signature me-1"></i> Apply for Admission Now
                                        </a>
                                    <?php endif; ?>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-dark btn-sm rounded-pill w-100 fw-bold" onclick="printFeeEstimate()">
                                            <i class="fa fa-print me-1"></i> Print Slip
                                        </button>
                                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $data['school']->school_phone ?? '9251111222333'); ?>?text=<?php echo urlencode('Hello! I calculated fee estimate for my child and would like to confirm admission details.'); ?>" target="_blank" class="btn btn-success btn-sm rounded-pill w-100 fw-bold">
                                            <i class="fab fa-whatsapp me-1"></i> WhatsApp Desk
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- GRADE-WISE PRICING TABLES (CLEAR-CUT & CRYSTAL CLEAR) -->
            <section class="mb-5" id="feeTiers">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div>
                        <h2 class="fw-extrabold text-dark mb-1">Standard Academic Fee Schedule</h2>
                        <p class="text-muted small mb-0">Official approved schedule for Academic Session 2026-2027.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-light text-dark border p-2"><i class="fa fa-check-circle text-success me-1"></i> No Capital Fee</span>
                        <span class="badge bg-light text-dark border p-2"><i class="fa fa-check-circle text-success me-1"></i> 100% Tax Compliant</span>
                    </div>
                </div>

                <div class="fee-table-card">
                    <div class="table-responsive">
                        <table class="table fee-table mb-0">
                            <thead>
                                <tr>
                                    <th>Academic Wing &amp; Grade</th>
                                    <th>Admission Fee (One-Time)</th>
                                    <th>Security (Refundable)</th>
                                    <th>Monthly Tuition</th>
                                    <th>Lab &amp; Activity</th>
                                    <th>Net Monthly Dues</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Pre-School -->
                                <tr class="bg-primary bg-opacity-10 fw-bold">
                                    <td colspan="7" class="text-primary py-2 px-3 small text-uppercase">
                                        <i class="fa fa-shapes me-1"></i> Pre-School / Montessori Wing
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Playgroup</strong>
                                        <div class="text-muted small">Ages 3 &ndash; 4 &bull; Sensory Learning</div>
                                    </td>
                                    <td>Rs. 15,000</td>
                                    <td>Rs. 5,000</td>
                                    <td class="fw-bold text-dark">Rs. 6,500</td>
                                    <td>Included</td>
                                    <td class="fw-extrabold text-primary fs-6">Rs. 6,500 <small class="text-muted fw-normal">/mo</small></td>
                                    <td class="text-end">
                                        <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-sm btn-outline-primary rounded-pill px-3">Apply</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Nursery &amp; Prep (KG)</strong>
                                        <div class="text-muted small">Ages 4 &ndash; 6 &bull; Phonics &amp; Numbers</div>
                                    </td>
                                    <td>Rs. 15,000</td>
                                    <td>Rs. 5,000</td>
                                    <td class="fw-bold text-dark">Rs. 7,000</td>
                                    <td>Included</td>
                                    <td class="fw-extrabold text-primary fs-6">Rs. 7,000 <small class="text-muted fw-normal">/mo</small></td>
                                    <td class="text-end">
                                        <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-sm btn-outline-primary rounded-pill px-3">Apply</a>
                                    </td>
                                </tr>

                                <!-- Primary Wing -->
                                <tr class="bg-success bg-opacity-10 fw-bold">
                                    <td colspan="7" class="text-success py-2 px-3 small text-uppercase">
                                        <i class="fa fa-book me-1"></i> Primary School (Grades 1 &ndash; 5)
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Grades 1 &ndash; 2</strong>
                                        <div class="text-muted small">Foundational Literacy &amp; Arithmetic</div>
                                    </td>
                                    <td>Rs. 18,000</td>
                                    <td>Rs. 5,000</td>
                                    <td class="fw-bold text-dark">Rs. 8,000</td>
                                    <td>Rs. 500</td>
                                    <td class="fw-extrabold text-success fs-6">Rs. 8,500 <small class="text-muted fw-normal">/mo</small></td>
                                    <td class="text-end">
                                        <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-sm btn-outline-success rounded-pill px-3">Apply</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Grades 3 &ndash; 5</strong>
                                        <div class="text-muted small">Science, Mathematics &amp; Languages</div>
                                    </td>
                                    <td>Rs. 18,000</td>
                                    <td>Rs. 5,000</td>
                                    <td class="fw-bold text-dark">Rs. 8,500</td>
                                    <td>Rs. 500</td>
                                    <td class="fw-extrabold text-success fs-6">Rs. 9,000 <small class="text-muted fw-normal">/mo</small></td>
                                    <td class="text-end">
                                        <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-sm btn-outline-success rounded-pill px-3">Apply</a>
                                    </td>
                                </tr>

                                <!-- Middle Wing -->
                                <tr class="bg-warning bg-opacity-10 fw-bold">
                                    <td colspan="7" class="text-dark py-2 px-3 small text-uppercase">
                                        <i class="fa fa-flask me-1"></i> Middle School (Grades 6 &ndash; 8)
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Grades 6 &ndash; 8</strong>
                                        <div class="text-muted small">Computer Coding &amp; Science Labs</div>
                                    </td>
                                    <td>Rs. 20,000</td>
                                    <td>Rs. 5,000</td>
                                    <td class="fw-bold text-dark">Rs. 10,000</td>
                                    <td>Rs. 1,000</td>
                                    <td class="fw-extrabold text-dark fs-6">Rs. 11,000 <small class="text-muted fw-normal">/mo</small></td>
                                    <td class="text-end">
                                        <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-sm btn-outline-dark rounded-pill px-3">Apply</a>
                                    </td>
                                </tr>

                                <!-- Senior Wing -->
                                <tr class="bg-danger bg-opacity-10 fw-bold">
                                    <td colspan="7" class="text-danger py-2 px-3 small text-uppercase">
                                        <i class="fa fa-graduation-cap me-1"></i> Senior Secondary / Matric &amp; Cambridge (Grades 9 &ndash; 10)
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Grade 9 &ndash; 10 (Matric / O-Levels)</strong>
                                        <div class="text-muted small">Physics, Chemistry, Biology / CS Labs &amp; Board Prep</div>
                                    </td>
                                    <td>Rs. 25,000</td>
                                    <td>Rs. 7,000</td>
                                    <td class="fw-bold text-dark">Rs. 13,000</td>
                                    <td>Rs. 2,000</td>
                                    <td class="fw-extrabold text-danger fs-6">Rs. 15,000 <small class="text-muted fw-normal">/mo</small></td>
                                    <td class="text-end">
                                        <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-sm btn-outline-danger rounded-pill px-3">Apply</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- OFFICIAL BANK PAYMENT & ONLINE CHANNELS -->
            <section class="mb-5" id="paymentGuide">
                <div class="bank-card">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-7">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary rounded-pill px-3">Payment Guide</span>
                                <span class="badge bg-light text-dark border">Due Date: 10th of Every Month</span>
                            </div>
                            <h3 class="fw-extrabold text-dark mb-2">Official Banking &amp; Online Payment Channels</h3>
                            <p class="text-muted small mb-4">
                                Fee challans are generated automatically on the 1st of every calendar month. Parents can submit dues conveniently using any of the following verified channels:
                            </p>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-3 bg-light border">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="fa fa-university text-primary fs-5"></i>
                                            <strong class="text-dark">Habib Bank Limited (HBL)</strong>
                                        </div>
                                        <div class="text-muted small">Title: <strong><?php echo htmlspecialchars($schoolName); ?></strong></div>
                                        <div class="text-muted small font-monospace">Acc: 0042-7901-2345-03</div>
                                        <div class="text-muted small font-monospace">IBAN: PK36HABB0000427901234503</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-3 rounded-3 bg-light border">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="fa fa-qrcode text-success fs-5"></i>
                                            <strong class="text-dark">1Link 1Bill &amp; Mobile Wallets</strong>
                                        </div>
                                        <div class="text-muted small">Kuickpay / 1Bill Consumer ID provided on each student challan.</div>
                                        <div class="badge bg-success bg-opacity-10 text-success mt-2">Instant Fee Reconciliation</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="p-4 rounded-3 border" style="background: #f8fafc;">
                                <h6 class="fw-bold text-dark mb-3"><i class="fa fa-circle-info text-primary me-2"></i>Important Payment Rules</h6>
                                <ul class="list-unstyled text-muted small mb-0">
                                    <li class="mb-2 d-flex gap-2">
                                        <i class="fa fa-check text-success mt-1"></i>
                                        <span>Monthly fees are payable in advance by the <strong>10th of each calendar month</strong>.</span>
                                    </li>
                                    <li class="mb-2 d-flex gap-2">
                                        <i class="fa fa-check text-success mt-1"></i>
                                        <span>Late fee fine of <strong>Rs. 50/day</strong> applies after the due date.</span>
                                    </li>
                                    <li class="mb-2 d-flex gap-2">
                                        <i class="fa fa-check text-success mt-1"></i>
                                        <span>Security deposit is 100% refundable upon student clearance and completion of notice period.</span>
                                    </li>
                                    <li class="d-flex gap-2">
                                        <i class="fa fa-check text-success mt-1"></i>
                                        <span>For billing disputes or fee reconciliation, contact <a href="mailto:<?php echo htmlspecialchars($data['school']->school_email ?? 'accounts@school.edu.pk'); ?>" class="text-primary fw-bold">Accounts Desk</a>.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ATTACHED SIGNED PDF FEE SCHEDULE (IF ATTACHED IN CMS) -->
            <?php if(!empty($data['page']->file_path)): 
                $filePath = $data['page']->file_path;
                $fileUrl = (strpos($filePath, 'http') === 0) ? $filePath : URLROOT . '/' . ltrim($filePath, '/');
                $fileName = !empty($data['page']->file_name) ? $data['page']->file_name : basename($filePath);
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $isPdf = ($ext === 'pdf');
            ?>
                <section class="mb-5">
                    <div class="pdf-viewer-container rounded-3 overflow-hidden shadow-sm border">
                        <div class="bg-dark text-white p-3 d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom border-secondary border-opacity-50">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2 bg-danger text-white rounded-3 fs-5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fa fa-file-pdf"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-white fs-6"><?php echo htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <span class="badge bg-danger text-uppercase font-monospace" style="font-size: 0.65rem;">Official Signed Fee Schedule &bull; PDF Reader</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?php echo $fileUrl; ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold">
                                    <i class="fa fa-expand me-1"></i> Fullscreen
                                </a>
                                <a href="<?php echo $fileUrl; ?>" download class="btn btn-warning text-dark btn-sm rounded-pill px-3 fw-bold">
                                    <i class="fa fa-download me-1"></i> Download PDF
                                </a>
                            </div>
                        </div>
                        <div class="pdf-frame-wrapper position-relative" style="height: 600px; background: #525659;">
                            <object data="<?php echo $fileUrl; ?>#toolbar=1&navpanes=0&scrollbar=1" type="application/pdf" width="100%" height="100%">
                                <iframe src="<?php echo $fileUrl; ?>" width="100%" height="100%" style="border: none;">
                                    <div class="p-5 text-center text-white">
                                        <i class="fa fa-file-pdf fa-3x text-danger mb-3"></i>
                                        <p class="fs-5 fw-bold mb-2">Signed Fee Schedule Ready for Reading</p>
                                        <a href="<?php echo $fileUrl; ?>" target="_blank" class="btn btn-primary-lux rounded-pill px-4">
                                            <i class="fa fa-external-link-alt me-2"></i> Open PDF Reader
                                        </a>
                                    </div>
                                </iframe>
                            </object>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- DYNAMIC EDITABLE CMS CONTENT (CUSTOM POLICIES & NOTES) -->
            <?php if(!empty($data['page']->content)): ?>
                <section class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 mb-5 bg-white">
                    <div class="page-rendered-content">
                        <?php echo $data['page']->content; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- BOTTOM ENROLLMENT CTA BANNER -->
            <div class="p-4 p-lg-5 rounded-4 text-center text-white" style="background: linear-gradient(135deg, #1e40af 0%, #0f172a 100%);">
                <h3 class="fw-extrabold mb-2">Ready to Enroll Your Child?</h3>
                <p class="text-white text-opacity-80 max-w-700 mx-auto mb-4">
                    Admissions are open for Academic Session 2026-2027. Seats are filled on a first-come, merit-evaluated basis.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <?php if(($data['cms']->enable_online_admission ?? 'yes') === 'yes'): ?>
                        <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-warning text-dark fw-bold rounded-pill px-4 py-2 shadow-sm">
                            <i class="fa fa-file-signature me-1"></i> Apply for Online Admission
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo URLROOT; ?>/home/contact" class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold">
                        <i class="fa fa-phone me-1"></i> Contact Admissions Desk
                    </a>
                </div>
            </div>

        </div>
    </main>

    <!-- HIDDEN PRINT ESTIMATE RECEIPT SLIP -->
    <div id="printEstimateReceipt" class="d-none p-4" style="font-family: sans-serif; color: #000;">
        <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 24px; text-transform: uppercase;"><?php echo htmlspecialchars($schoolName); ?></h2>
            <div style="font-size: 14px; color: #555;">Estimated Fee Calculation Slip &bull; Academic Session 2026-2027</div>
            <div style="font-size: 12px; color: #777;">Date Generated: <?php echo date('d M Y, h:i A'); ?></div>
        </div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Selected Grade:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;" id="prtGrade">-</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Enrollment Status:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;" id="prtType">-</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Base Monthly Tuition:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;" id="prtBase">-</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Sibling Concession:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;" id="prtDiscount">-</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Transport Charges:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;" id="prtTransport">-</td>
            </tr>
            <tr style="background: #f5f5f5; font-weight: bold;">
                <td style="padding: 10px; border-top: 2px solid #000; border-bottom: 2px solid #000;">Estimated Net Monthly Fee:</td>
                <td style="padding: 10px; border-top: 2px solid #000; border-bottom: 2px solid #000; font-size: 16px;" id="prtNetMonthly">-</td>
            </tr>
            <tr id="prtOneTimeRow" style="font-weight: bold;">
                <td style="padding: 10px; border-bottom: 2px solid #000;">Total Initial Payable (Admission + 1st Month):</td>
                <td style="padding: 10px; border-bottom: 2px solid #000; font-size: 16px; color: #b91c1c;" id="prtInitialPayable">-</td>
            </tr>
        </table>
        <div style="font-size: 11px; color: #666; border-top: 1px dashed #999; padding-top: 10px;">
            * Note: This is an estimated schedule for parent convenience. Final fee challan will be issued by the accounts desk after document verification and admission approval.
        </div>
    </div>

    <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>

    <script>
    function calculateFeeEstimate() {
        const gradeSelect = document.getElementById('calcGradeSelect');
        const selectedOpt = gradeSelect.options[gradeSelect.selectedIndex];
        
        const baseMonthly = parseInt(selectedOpt.getAttribute('data-monthly') || 0);
        const labCharges = parseInt(selectedOpt.getAttribute('data-lab') || 0);
        const admissionFee = parseInt(selectedOpt.getAttribute('data-admission') || 0);
        const securityFee = parseInt(selectedOpt.getAttribute('data-security') || 0);

        const enrollType = document.querySelector('input[name="calcEnrollType"]:checked').value;
        const discountPct = parseInt(document.getElementById('calcSiblingSelect').value || 0);
        const transportFee = parseInt(document.getElementById('calcTransportSelect').value || 0);
        
        let addonFee = 0;
        if (document.getElementById('addRobotics').checked) addonFee += parseInt(document.getElementById('addRobotics').value);
        if (document.getElementById('addSports').checked) addonFee += parseInt(document.getElementById('addSports').value);

        // Discount applies on Base Monthly Tuition
        const discountAmount = Math.round((baseMonthly * discountPct) / 100);
        const netMonthly = (baseMonthly - discountAmount) + labCharges + transportFee + addonFee;

        // Render to UI
        document.getElementById('dispBaseTuition').innerText = 'Rs. ' + baseMonthly.toLocaleString();
        document.getElementById('dispLabCharges').innerText = labCharges > 0 ? ('Rs. ' + labCharges.toLocaleString()) : 'Included';
        document.getElementById('dispDiscount').innerText = discountAmount > 0 ? ('- Rs. ' + discountAmount.toLocaleString() + ' (' + discountPct + '%)') : 'Rs. 0';
        document.getElementById('dispTransport').innerText = transportFee > 0 ? ('Rs. ' + transportFee.toLocaleString()) : 'Rs. 0';
        document.getElementById('dispAddons').innerText = addonFee > 0 ? ('Rs. ' + addonFee.toLocaleString()) : 'Rs. 0';
        document.getElementById('dispNetMonthly').innerText = 'Rs. ' + netMonthly.toLocaleString();

        const oneTimeSection = document.getElementById('oneTimeSection');
        const estStatusBadge = document.getElementById('estStatusBadge');

        if (enrollType === 'new') {
            oneTimeSection.style.display = 'block';
            estStatusBadge.className = 'badge bg-success bg-opacity-10 text-success fw-bold';
            estStatusBadge.innerText = 'New Enrollment';

            document.getElementById('dispAdmissionFee').innerText = 'Rs. ' + admissionFee.toLocaleString();
            document.getElementById('dispSecurityFee').innerText = 'Rs. ' + securityFee.toLocaleString();
            
            const totalInitial = admissionFee + securityFee + netMonthly;
            document.getElementById('dispTotalInitial').innerText = 'Rs. ' + totalInitial.toLocaleString();
        } else {
            oneTimeSection.style.display = 'none';
            estStatusBadge.className = 'badge bg-primary bg-opacity-10 text-primary fw-bold';
            estStatusBadge.innerText = 'Continuing Student';
        }

        // Update print receipt values
        document.getElementById('prtGrade').innerText = selectedOpt.text;
        document.getElementById('prtType').innerText = (enrollType === 'new') ? 'New Student Admission' : 'Existing Student';
        document.getElementById('prtBase').innerText = 'Rs. ' + baseMonthly.toLocaleString();
        document.getElementById('prtDiscount').innerText = discountAmount > 0 ? ('- Rs. ' + discountAmount.toLocaleString()) : 'None (0%)';
        document.getElementById('prtTransport').innerText = transportFee > 0 ? ('Rs. ' + transportFee.toLocaleString()) : 'Self';
        document.getElementById('prtNetMonthly').innerText = 'Rs. ' + netMonthly.toLocaleString() + ' / month';

        if (enrollType === 'new') {
            document.getElementById('prtOneTimeRow').style.display = '';
            document.getElementById('prtInitialPayable').innerText = 'Rs. ' + (admissionFee + securityFee + netMonthly).toLocaleString();
        } else {
            document.getElementById('prtOneTimeRow').style.display = 'none';
        }

        // Pass selected class into admission button URL
        const btnApply = document.getElementById('btnApplyAdmission');
        if (btnApply) {
            btnApply.href = '<?php echo URLROOT; ?>/home/admission?class=' + encodeURIComponent(selectedOpt.text);
        }
    }

    function printFeeEstimate() {
        window.print();
    }

    // Initialize calculator on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculateFeeEstimate();
    });
    </script>
</body>
</html>
