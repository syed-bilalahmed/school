<?php
$schoolName = $data['school']->school_name ?? $data['settings']->school_name ?? SITENAME ?? 'Executive Model High School & College';
$activePage = 'admission';
$theme = $data['settings']->theme_color ?? 'default';

// Color adaptation
$themeColor = '#4f46e5';
if ($theme === 'red') $themeColor = '#e11d48';
elseif ($theme === 'green') $themeColor = '#059669';
elseif ($theme === 'dark') $themeColor = '#0f172a';

$enquiry = $data['enquiry'] ?? null;
$searched = $data['searched'] ?? false;
$searchQuery = $data['searchQuery'] ?? '';
$timeline = $data['timeline'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Admission Application &mdash; <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></title>
    
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
        <?php if ($theme !== 'default'): ?>
        :root {
            --f-primary: <?php echo $themeColor; ?>;
            --f-primary-gradient: linear-gradient(135deg, <?php echo $themeColor; ?> 0%, #1e1b4b 100%);
        }
        <?php endif; ?>

        .tracker-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            padding: 50px 0 60px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }
        .tracker-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.15) 0%, rgba(0,0,0,0) 70%);
            pointer-events: none;
        }

        .search-box-card {
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 12px 36px rgba(15, 23, 42, 0.15);
            padding: 24px;
            margin-top: -36px;
            position: relative;
            z-index: 10;
        }

        /* 5-Stage Stepper */
        .stepper-container {
            position: relative;
            padding: 20px 0;
        }
        .stepper-progress-bar {
            position: absolute;
            top: 42px;
            left: 5%;
            right: 5%;
            height: 4px;
            background: #e2e8f0;
            z-index: 1;
        }
        .stepper-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #3b82f6 100%);
            transition: width 0.6s ease;
        }
        .stepper-item {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .stepper-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #cbd5e1;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .stepper-item.completed .stepper-circle {
            background: #10b981;
            border-color: #10b981;
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
        }
        .stepper-item.current .stepper-circle {
            background: #3b82f6;
            border-color: #3b82f6;
            color: #ffffff;
            box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.2);
            animation: pulse-ring 2s infinite;
        }
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }

        .stepper-title {
            font-weight: 700;
            font-size: 0.88rem;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .stepper-subtitle {
            font-size: 0.72rem;
            color: #64748b;
        }

        .candidate-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .info-pill-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
        }

        @media (max-width: 768px) {
            .stepper-progress-bar {
                display: none;
            }
            .stepper-item {
                display: flex;
                align-items: flex-start;
                text-align: left;
                margin-bottom: 20px;
                gap: 14px;
            }
            .stepper-circle {
                margin-bottom: 0;
                flex-shrink: 0;
            }
        }
    </style>
</head>
<body style="background: #f8fafc;">

    <!-- TOP NAVIGATION BAR -->
    <?php require_once APPROOT . '/Views/home/partials/navbar.php'; ?>

    <!-- HERO HEADER -->
    <header class="tracker-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1">
                            <i class="fa fa-radar me-1"></i> Live Application Status
                        </span>
                        <span class="text-white-50 small">&bull; Session <?php echo date('Y'); ?>&ndash;<?php echo date('Y')+1; ?></span>
                    </div>
                    <h1 class="h2 fw-extrabold text-white mb-2">Online Admission Status Tracker</h1>
                    <p class="text-white-50 mb-0" style="max-width: 620px;">
                        Track your child's admission progress in real-time. Enter your official <strong>Application Reference ID</strong> or registered <strong>Mobile Number</strong>.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-outline-light rounded-pill px-4 fw-semibold">
                        <i class="fa fa-user-plus me-1"></i> Submit New Application
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container pb-5">
        <!-- SEARCH BOX CARD -->
        <div class="search-box-card">
            <form action="<?php echo URLROOT; ?>/home/track_admission" method="GET" class="row g-2 align-items-center">
                <div class="col-md-9">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="ref" class="form-control bg-light border-start-0 ps-0" placeholder="Enter Application ID (e.g. #15) or Mobile Phone (e.g. 03001234567)" value="<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-3 shadow-sm">
                        <i class="fa fa-search me-1"></i> Track Dossier
                    </button>
                </div>
            </form>
            <div class="d-flex align-items-center justify-content-between mt-2 pt-1">
                <span class="text-muted smaller" style="font-size: 0.78rem;">
                    <i class="fa fa-info-circle text-primary me-1"></i> Your Application ID was shown on screen upon form submission and sent via confirmation SMS/Email.
                </span>
                <?php if($searched): ?>
                    <a href="<?php echo URLROOT; ?>/home/track_admission" class="text-secondary small text-decoration-none">
                        <i class="fa fa-rotate-left me-1"></i> Clear Search
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- SEARCH RESULTS -->
        <?php if($searched && $enquiry): 
            $isEnrolled = !empty($enquiry->converted_student_id);
            $isApproved = $isEnrolled || ($enquiry->status === 'Approved');
            $isFollowUp = ($enquiry->status === 'Follow Up');
            $isRejected = ($enquiry->status === 'Rejected');

            // Count completed steps
            $completedSteps = 0;
            foreach ($timeline as $t) {
                if ($t['status'] === 'completed') $completedSteps++;
            }
            $progressPercent = ($completedSteps / max(1, count($timeline))) * 100;
        ?>

            <div class="mt-4">
                <!-- Status Banner -->
                <div class="p-3 mb-4 rounded-3 border <?php 
                    if($isEnrolled) echo 'bg-success bg-opacity-10 border-success text-success';
                    elseif($isApproved) echo 'bg-primary bg-opacity-10 border-primary text-primary';
                    elseif($isRejected) echo 'bg-danger bg-opacity-10 border-danger text-danger';
                    elseif($isFollowUp) echo 'bg-info bg-opacity-10 border-info text-dark';
                    else echo 'bg-warning bg-opacity-10 border-warning text-dark';
                ?>">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fs-2">
                                <?php if($isEnrolled): ?>
                                    🎓
                                <?php elseif($isApproved): ?>
                                    ✅
                                <?php elseif($isRejected): ?>
                                    ❌
                                <?php elseif($isFollowUp): ?>
                                    📅
                                <?php else: ?>
                                    ⏳
                                <?php endif; ?>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0">
                                    <?php if($isEnrolled): ?>
                                        Congratulations! Candidate Formally Enrolled
                                    <?php elseif($isApproved): ?>
                                        Admission Approved &mdash; Fee Voucher Ready
                                    <?php elseif($isRejected): ?>
                                        Application Closed / Not Accepted
                                    <?php elseif($isFollowUp): ?>
                                        Aptitude Assessment / Interview Scheduled
                                    <?php else: ?>
                                        Application Dossier Received &amp; Under Scrutiny
                                    <?php endif; ?>
                                </h4>
                                <span class="small opacity-75">
                                    Candidate: <strong><?php echo htmlspecialchars($enquiry->name, ENT_QUOTES, 'UTF-8'); ?></strong> &bull; 
                                    Target: <strong><?php echo htmlspecialchars($enquiry->class_name ?: 'Selected Class', ENT_QUOTES, 'UTF-8'); ?></strong> &bull; 
                                    Ref ID: <strong>#<?php echo $enquiry->id; ?></strong>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="<?php echo URLROOT; ?>/frontoffice/printAdmissionForm/<?php echo (int)$enquiry->id; ?>" target="_blank" class="btn btn-sm btn-outline-dark bg-white fw-semibold rounded-pill px-3 shadow-sm">
                                <i class="fa fa-print me-1"></i> Print Form
                            </a>
                            <?php if($isEnrolled && !empty($enquiry->enrolled_student_id)): ?>
                                <a href="<?php echo URLROOT; ?>/home/challan?student_id=<?php echo (int)$enquiry->enrolled_student_id; ?>" class="btn btn-sm btn-success fw-bold rounded-pill px-3 shadow-sm">
                                    <i class="fa fa-receipt me-1"></i> View Fee Challan
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 5-STAGE PROGRESS STEPPER -->
                <div class="candidate-card p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="fa fa-list-check text-primary me-2"></i> Admission Workflow Timeline
                        </h5>
                        <span class="badge bg-light text-dark border">
                            Stage <?php echo min(5, $completedSteps + 1); ?> of 5
                        </span>
                    </div>

                    <div class="stepper-container">
                        <div class="stepper-progress-bar">
                            <div class="stepper-progress-fill" style="width: <?php echo min(100, max(5, $progressPercent)); ?>%;"></div>
                        </div>
                        <div class="row g-2">
                            <?php foreach($timeline as $idx => $step): ?>
                                <div class="col-md stepper-item <?php echo $step['status']; ?>">
                                    <div class="stepper-circle">
                                        <?php if($step['status'] === 'completed'): ?>
                                            <i class="fa fa-check"></i>
                                        <?php else: ?>
                                            <i class="fa <?php echo $step['icon']; ?>"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="stepper-title"><?php echo htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="stepper-subtitle"><?php echo htmlspecialchars($step['date'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <p class="text-muted smaller d-none d-lg-block mt-1 px-1" style="font-size: 0.72rem; line-height: 1.3;">
                                        <?php echo htmlspecialchars($step['desc'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- PARTICULARS & ACTION MATRIX -->
                <div class="row g-4">
                    <!-- Left: Student & Guardian Particulars -->
                    <div class="col-lg-7">
                        <div class="candidate-card p-4 h-100">
                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                <i class="fa fa-user-graduate text-primary me-2"></i> Application Dossier Particulars
                            </h5>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="info-pill-item">
                                        <div class="text-muted smaller text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Student Full Name</div>
                                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($enquiry->name, ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-pill-item">
                                        <div class="text-muted smaller text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Father / Guardian Name</div>
                                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($enquiry->father_name ?: ($enquiry->guardian_name ?: 'N/A'), ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-pill-item">
                                        <div class="text-muted smaller text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Class Applied For</div>
                                        <div class="fw-bold text-primary fs-6"><?php echo htmlspecialchars($enquiry->class_name ?: 'Standard Class', ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-pill-item">
                                        <div class="text-muted smaller text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Application Submission Date</div>
                                        <div class="fw-bold text-dark fs-6"><?php echo !empty($enquiry->date) ? date('d M Y', strtotime($enquiry->date)) : date('d M Y'); ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-pill-item">
                                        <div class="text-muted smaller text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Registered Contact Phone</div>
                                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($enquiry->phone, ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-pill-item">
                                        <div class="text-muted smaller text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Official Application Ref ID</div>
                                        <div class="fw-bold text-primary font-monospace fs-6">#<?php echo $enquiry->id; ?></div>
                                    </div>
                                </div>
                                <?php if($isEnrolled && !empty($enquiry->admission_no)): ?>
                                    <div class="col-sm-6">
                                        <div class="info-pill-item bg-success bg-opacity-10 border-success">
                                            <div class="text-success smaller text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Enrolled Admission No</div>
                                            <div class="fw-bold text-success fs-5 font-monospace"><?php echo htmlspecialchars($enquiry->admission_no, ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="info-pill-item bg-success bg-opacity-10 border-success">
                                            <div class="text-success smaller text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Allocated Roll No</div>
                                            <div class="fw-bold text-success fs-5 font-monospace"><?php echo htmlspecialchars($enquiry->roll_no ?: 'Assigned', ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Additional description excerpt if available -->
                            <?php if(!empty($enquiry->description)): ?>
                                <div class="mt-3 p-3 bg-light rounded-3 border">
                                    <div class="text-muted smaller fw-bold mb-1"><i class="fa fa-notes-medical text-secondary me-1"></i> Recorded Academic Details &amp; Identification:</div>
                                    <p class="small text-secondary mb-0"><?php echo htmlspecialchars($enquiry->description, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Next Steps & Direct Helpline -->
                    <div class="col-lg-5">
                        <div class="candidate-card p-4 h-100">
                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">
                                <i class="fa fa-clipboard-check text-success me-2"></i> Mandatory Verification Checklist
                            </h5>

                            <p class="small text-muted mb-3">
                                Please ensure the following physical documents are brought to the campus admissions office to conclude enrollment:
                            </p>

                            <ul class="list-unstyled mb-4 small">
                                <li class="mb-2 d-flex align-items-center gap-2">
                                    <i class="fa fa-circle-check text-success"></i>
                                    <span><strong>4 Passport-size</strong> photographs (Sky Blue background)</span>
                                </li>
                                <li class="mb-2 d-flex align-items-center gap-2">
                                    <i class="fa fa-circle-check text-success"></i>
                                    <span><strong>NADRA B-Form</strong> / Birth Certificate photocopy</span>
                                </li>
                                <li class="mb-2 d-flex align-items-center gap-2">
                                    <i class="fa fa-circle-check text-success"></i>
                                    <span><strong>Father / Guardian CNIC</strong> photocopy</span>
                                </li>
                                <li class="mb-2 d-flex align-items-center gap-2">
                                    <i class="fa fa-circle-check text-success"></i>
                                    <span>Previous <strong>School Leaving Certificate (SLC)</strong> &amp; Report Card</span>
                                </li>
                                <li class="mb-0 d-flex align-items-center gap-2">
                                    <i class="fa fa-circle-check text-success"></i>
                                    <span>Candidate <strong>Immunization / Polio record</strong> copy</span>
                                </li>
                            </ul>

                            <!-- Quick Action Helpline -->
                            <div class="p-3 rounded-3 bg-light border text-center">
                                <div class="fw-bold small text-dark mb-1">Need Urgent Assistance with Your Application?</div>
                                <p class="smaller text-muted mb-3" style="font-size: 0.78rem;">
                                    Connect with the Admissions Secretariat on WhatsApp with your Application Ref <strong>#<?php echo $enquiry->id; ?></strong>.
                                </p>
                                <?php 
                                    $wpMsg = rawurlencode("Assalam-o-Alaikum, I am inquiring regarding Online Admission Application #" . $enquiry->id . " for " . $enquiry->name . " (Class: " . ($enquiry->class_name ?: 'General') . "). Please assist.");
                                    $wpNum = preg_replace('/[^0-9]/', '', $data['cms']->whatsapp_number ?? '923001234567');
                                ?>
                                <a href="https://wa.me/<?php echo $wpNum; ?>?text=<?php echo $wpMsg; ?>" target="_blank" class="btn btn-success btn-sm w-100 fw-bold rounded-pill shadow-sm">
                                    <i class="fab fa-whatsapp me-1"></i> Inquire via Admissions Desk
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif($searched && !$enquiry): ?>
            <!-- NOT FOUND STATE -->
            <div class="mt-4">
                <div class="candidate-card p-5 text-center">
                    <div class="mx-auto mb-3 text-warning" style="font-size: 3.5rem;">
                        <i class="fa fa-circle-question"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-2">No Application Record Found</h3>
                    <p class="text-muted mb-4" style="max-width: 520px; margin-left: auto; margin-right: auto;">
                        We could not find an active admission dossier matching <strong>"<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>"</strong>.
                    </p>

                    <div class="p-3 bg-light rounded-3 d-inline-block text-start mb-4" style="border: 1px dashed #cbd5e1; max-width: 480px;">
                        <div class="small fw-bold text-dark mb-2"><i class="fa fa-lightbulb text-warning me-1"></i> Helpful Tips:</div>
                        <ul class="mb-0 small text-muted ps-3">
                            <li>Ensure you entered the numeric Application ID (e.g. <strong>15</strong>) without extra characters.</li>
                            <li>If searching by phone, use the exact mobile number provided during registration (e.g. <strong>03001234567</strong>).</li>
                            <li>If you recently applied, please allow 10-15 minutes for the dossier to synchronize.</li>
                        </ul>
                    </div>

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a href="<?php echo URLROOT; ?>/home/track_admission" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fa fa-arrow-left me-1"></i> Try Another Search
                        </a>
                        <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fa fa-file-signature me-1"></i> Submit New Admission
                        </a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- INITIAL LANDING EXPLAINER -->
            <div class="mt-4">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="candidate-card p-4 text-center h-100">
                            <div class="fs-1 mb-2">📝</div>
                            <h5 class="fw-bold text-dark">1. Submit Application</h5>
                            <p class="text-muted small mb-0">Fill out our 3-step online admission form with candidate particulars and previous academic details.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="candidate-card p-4 text-center h-100">
                            <div class="fs-1 mb-2">🔍</div>
                            <h5 class="fw-bold text-dark">2. Track Real-time</h5>
                            <p class="text-muted small mb-0">Use your Application Reference ID to check document verification, interview calls, and committee decisions.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="candidate-card p-4 text-center h-100">
                            <div class="fs-1 mb-2">💳</div>
                            <h5 class="fw-bold text-dark">3. Bank Fee Challan</h5>
                            <p class="text-muted small mb-0">Upon approval, download your official 3-copy bank challan to deposit tuition at any branch or via 1Link 1Bill.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- FOOTER -->
    <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
