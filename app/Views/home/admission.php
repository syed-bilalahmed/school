<?php
$schoolName = $data['school']->school_name ?? $data['settings']->school_name ?? SITENAME ?? 'City Model High School & College';
$activePage = 'admission';
$theme = $data['settings']->theme_color ?? 'default';

// Color adaptation
$themeColor = '#4f46e5';
if ($theme === 'red') $themeColor = '#e11d48';
elseif ($theme === 'green') $themeColor = '#059669';
elseif ($theme === 'dark') $themeColor = '#0f172a';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Admission &mdash; <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></title>
    
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

        /* Tabbed Wizard Styles */
        .admission-tabs .nav-link {
            border: 1px solid #e2e8f0;
            border-bottom: 3px solid transparent;
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 14px 20px;
            border-radius: 12px;
            transition: all 0.25s ease;
        }
        .admission-tabs .nav-link.active {
            background-color: #ffffff;
            color: var(--f-primary, #4f46e5);
            border-color: var(--f-primary, #4f46e5);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.1);
        }
        .admission-tabs .step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #e2e8f0;
            color: #475569;
            font-size: 0.85rem;
            margin-right: 8px;
            font-weight: 700;
        }
        .admission-tabs .nav-link.active .step-num {
            background-color: var(--f-primary, #4f46e5);
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- TOP NAVIGATION BAR -->
    <?php require_once APPROOT . '/Views/home/partials/navbar.php'; ?>

    <!-- INNER PAGE HERO -->
    <header class="inner-hero">
        <div class="container">
            <div class="inner-hero-content">
                <div class="inner-breadcrumbs">
                    <a href="<?php echo URLROOT; ?>"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    <span>Admissions</span>
                    <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    <span class="text-white">Online Application</span>
                </div>
                <h1 class="inner-page-title">Candidate Admission Portal</h1>
                <p class="inner-page-subtitle">
                    Embark on a voyage of educational distinction. Complete your student's profile across 3 simple steps.
                </p>
            </div>
        </div>
    </header>

    <!-- MAIN ADMISSION CONTAINER -->
    <main class="py-5" style="background: var(--f-bg);">
        <div class="container">
            <div class="row g-5">
                <!-- Admission Form Column -->
                <div class="col-lg-8">
                    <!-- Notifications -->
                    <?php if(isset($data['success_message'])): ?>
                        <div class="admission-card-luxury text-center py-5 mb-4 border-success">
                            <div class="brand-icon-emblem mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.75rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <i class="fa fa-check"></i>
                            </div>
                            <h2 class="h3 fw-bold text-success mb-2">Application Successfully Submitted</h2>
                            <p class="text-muted mb-4" style="max-width: 520px; margin-left: auto; margin-right: auto;">
                                <?php echo htmlspecialchars($data['success_message'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <div class="p-3 bg-light rounded-3 d-inline-block text-start mb-4" style="border: 1px dashed #cbd5e1;">
                                <div class="small text-muted mb-1"><i class="fa fa-info-circle text-primary me-1"></i> Our Admissions Secretariat will contact you via phone/email within <strong>48 hours</strong>.</div>
                                <div class="small text-danger fw-bold"><i class="fa fa-exclamation-circle me-1"></i> Mandatory: Please submit all required documents listed below to finalize enrollment.</div>
                            </div>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#homeRequiredDocsModal">
                                    <i class="fa fa-clipboard-check me-2"></i>View Required Documents Checklist
                                </button>
                                <?php if(!empty($data['submitted_application']['id'])): ?>
                                    <a href="<?php echo URLROOT; ?>/frontoffice/printAdmissionForm/<?php echo (int)$data['submitted_application']['id']; ?>" target="_blank" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                                        <i class="fa fa-print me-1"></i> Print / Download Form
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo URLROOT; ?>" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="fa fa-arrow-left me-1"></i> Return to Homepage
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if(isset($data['error_message'])): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 rounded-3 shadow-sm border-0" role="alert">
                            <i class="fa fa-exclamation-circle fs-5"></i>
                            <div><?php echo htmlspecialchars($data['error_message'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if(!isset($data['success_message'])): ?>
                        <div class="admission-card-luxury p-4 p-md-5">
                            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                                <div>
                                    <h2 class="h4 fw-bold mb-1">Online Student Admission Dossier</h2>
                                    <p class="text-muted small mb-0">Fill in the candidate particulars below. Fields marked with (<span class="text-danger">*</span>) are mandatory.</p>
                                </div>
                                <span class="badge bg-primary rounded-pill px-3 py-2 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                                    Session <?php echo date('Y'); ?> &ndash; <?php echo date('Y')+1; ?>
                                </span>
                            </div>

                            <!-- STEP NAVIGATION TABS -->
                            <ul class="nav nav-pills nav-justified admission-tabs mb-4 gap-2" id="admissionWizardTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active w-100 text-start" id="tab-personal-btn" data-bs-toggle="pill" data-bs-target="#tab-personal" type="button" role="tab">
                                        <span class="step-num">1</span> <i class="fa fa-user me-1"></i> Personal &amp; Family
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link w-100 text-start" id="tab-academic-btn" data-bs-toggle="pill" data-bs-target="#tab-academic" type="button" role="tab">
                                        <span class="step-num">2</span> <i class="fa fa-school me-1"></i> Previous Academic
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link w-100 text-start" id="tab-admission-btn" data-bs-toggle="pill" data-bs-target="#tab-admission" type="button" role="tab">
                                        <span class="step-num">3</span> <i class="fa fa-graduation-cap me-1"></i> Class Selection
                                    </button>
                                </li>
                            </ul>

                            <form action="<?php echo URLROOT; ?>/home/admission" method="POST" id="admissionForm">
                                <div class="tab-content" id="admissionWizardContent">
                                    
                                    <!-- ========================================================= -->
                                    <!-- TAB 1: PERSONAL & FAMILY INFORMATION                      -->
                                    <!-- ========================================================= -->
                                    <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge bg-primary rounded-circle p-2"><i class="fa fa-user text-white"></i></span>
                                            <h5 class="fw-bold mb-0 text-dark">Step 1: Student &amp; Guardian Particulars</h5>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label-lux">Full Name of Candidate <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control-lux" placeholder="e.g. Muhammad Ali Khan" required>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label class="form-label-lux">Gender <span class="text-danger">*</span></label>
                                                <select name="gender" class="form-select-lux" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Date of Birth <span class="text-danger">*</span></label>
                                                <input type="date" name="dob" class="form-control-lux" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Student B-Form / CNIC No.</label>
                                                <input type="text" name="bform_cnic" class="form-control-lux font-monospace" placeholder="e.g. 61101-1234567-1">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Father's Full Name</label>
                                                <input type="text" name="father_name" class="form-control-lux" placeholder="e.g. Tariq Mehmood Khan">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Father's CNIC No.</label>
                                                <input type="text" name="father_cnic" class="form-control-lux font-monospace" placeholder="e.g. 61101-9876543-1">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Mother's Full Name</label>
                                                <input type="text" name="mother_name" class="form-control-lux" placeholder="e.g. Ayesha Khan">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Primary Guardian Name <span class="text-danger">*</span></label>
                                                <input type="text" name="guardian_name" class="form-control-lux" placeholder="Guardian responsible for candidate" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Guardian Relationship</label>
                                                <input type="text" name="guardian_relation" class="form-control-lux" placeholder="e.g. Father, Mother, Uncle">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Primary Contact Phone <span class="text-danger">*</span></label>
                                                <input type="tel" name="phone" class="form-control-lux" placeholder="0300-1234567" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Guardian Email Address</label>
                                                <input type="email" name="email" class="form-control-lux" placeholder="guardian@example.com">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Residential Address <span class="text-danger">*</span></label>
                                                <input type="text" name="address" class="form-control-lux" placeholder="House/Street, Sector, City" required>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="button" class="btn btn-primary-lux px-4 btn-next-tab" data-target="#tab-academic-btn">
                                                Next Step: Previous Academic <i class="fa fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- ========================================================= -->
                                    <!-- TAB 2: ACADEMIC HISTORY & PREVIOUS SCHOOL RECORD          -->
                                    <!-- ========================================================= -->
                                    <div class="tab-pane fade" id="tab-academic" role="tabpanel">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge bg-warning rounded-circle p-2"><i class="fa fa-school text-dark"></i></span>
                                            <h5 class="fw-bold mb-0 text-dark">Step 2: Previous School Record &amp; Transcripts</h5>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label-lux">Previous School / Academy Name</label>
                                                <input type="text" name="previous_school" class="form-control-lux" placeholder="e.g. Army Public School / Beaconhouse">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Last Class / Grade Passed</label>
                                                <input type="text" name="last_class" class="form-control-lux" placeholder="e.g. Class 5th / Grade 5">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Total Marks / Percentage / Grade Obtained</label>
                                                <input type="text" name="last_grade" class="form-control-lux" placeholder="e.g. 88% / Grade A+">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Reason for School Transfer</label>
                                                <input type="text" name="transfer_reason" class="form-control-lux" placeholder="e.g. Relocation to City, Academic Improvement">
                                            </div>
                                        </div>

                                        <div class="p-3 bg-light rounded-3 mb-4" style="border: 1px dashed #cbd5e1;">
                                            <div class="small text-muted"><i class="fa fa-info-circle text-primary me-1"></i> Original School Leaving Certificate (SLC) and previous report cards must be produced at the interview.</div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-4">
                                            <button type="button" class="btn btn-outline-secondary px-4 btn-prev-tab" data-target="#tab-personal-btn">
                                                <i class="fa fa-arrow-left me-2"></i> Previous Step
                                            </button>
                                            <button type="button" class="btn btn-primary-lux px-4 btn-next-tab" data-target="#tab-admission-btn">
                                                Next Step: Class Selection <i class="fa fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- ========================================================= -->
                                    <!-- TAB 3: TARGET CLASS & ADMISSION PREFERENCES              -->
                                    <!-- ========================================================= -->
                                    <div class="tab-pane fade" id="tab-admission" role="tabpanel">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="badge bg-success rounded-circle p-2"><i class="fa fa-graduation-cap text-white"></i></span>
                                            <h5 class="fw-bold mb-0 text-dark">Step 3: New Admission Preferences &amp; Finalize</h5>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label-lux">Applying For Class / Grade Level <span class="text-danger">*</span></label>
                                                <select name="class_id" class="form-select-lux" required>
                                                    <option value="">Choose Grade Level</option>
                                                    <?php if(!empty($data['classes'])): ?>
                                                        <?php foreach($data['classes'] as $class): ?>
                                                            <option value="<?php echo $class->id; ?>">
                                                                <?php echo htmlspecialchars($class->class_name, ENT_QUOTES, 'UTF-8'); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="1">Grade 1 &ndash; Primary</option>
                                                        <option value="2">Grade 2 &ndash; Primary</option>
                                                        <option value="3">Grade 3 &ndash; Primary</option>
                                                        <option value="4">Grade 4 &ndash; Intermediate</option>
                                                        <option value="5">Grade 5 &ndash; Intermediate</option>
                                                        <option value="6">Grade 6 &ndash; Middle School</option>
                                                        <option value="7">Grade 7 &ndash; Middle School</option>
                                                        <option value="8">Grade 8 &ndash; Middle School</option>
                                                        <option value="9">Grade 9 &ndash; High School</option>
                                                        <option value="10">Grade 10 &ndash; High School</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label-lux">Emergency Contact Person &amp; Phone</label>
                                                <input type="text" name="emergency_contact" class="form-control-lux" placeholder="Name & Phone number">
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label-lux">Special Interests, Health Accommodations, or Remarks</label>
                                                <textarea name="description" class="form-control-lux" rows="3" placeholder="Detail any sports achievements, language proficiencies, or medical considerations..."></textarea>
                                            </div>
                                        </div>

                                        <div class="p-3 bg-light rounded-3 mb-4 d-flex align-items-center gap-3" style="border: 1px solid #e2e8f0;">
                                            <i class="fa fa-shield-alt text-primary fs-4"></i>
                                            <div class="small text-muted">
                                                By clicking submit, you verify that all details provided are authentic and accurate.
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <button type="button" class="btn btn-outline-secondary px-4 btn-prev-tab" data-target="#tab-academic-btn">
                                                <i class="fa fa-arrow-left me-2"></i> Previous Step
                                            </button>
                                            <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm fw-bold">
                                                <i class="fa fa-paper-plane me-2"></i> Submit Admission Dossier
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar Guidelines Column -->
                <div class="col-lg-4">
                    <!-- Admission Procedure Card -->
                    <div class="admission-sidebar-card">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="brand-icon-emblem" style="width: 34px; height: 34px; font-size: 0.9rem;">
                                <i class="fa fa-route"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-0">Admission Journey</h3>
                        </div>
                        <p class="text-muted small mb-4">Our thorough 4-step admissions protocol ensures students are aligned with our culture of rigor.</p>

                        <div class="d-flex flex-column">
                            <div class="timeline-step">
                                <div class="timeline-num">1</div>
                                <h4 class="timeline-title">Submit Application</h4>
                                <p class="timeline-desc">Complete candidate particulars via this 3-step online portal.</p>
                            </div>

                            <div class="timeline-step">
                                <div class="timeline-num">2</div>
                                <h4 class="timeline-title">Academic Assessment</h4>
                                <p class="timeline-desc">Cognitive reasoning, linguistic, and quantitative evaluation.</p>
                            </div>

                            <div class="timeline-step">
                                <div class="timeline-num">3</div>
                                <h4 class="timeline-title">Family Interview</h4>
                                <p class="timeline-desc">Dialogue with the academic dean and faculty counselors.</p>
                            </div>

                            <div class="timeline-step">
                                <div class="timeline-num">4</div>
                                <h4 class="timeline-title">Final Enrollment</h4>
                                <p class="timeline-desc">Letter of acceptance issued and onboarding orientation.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Required Documentation Card -->
                    <div class="admission-sidebar-card">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="brand-icon-emblem" style="width: 34px; height: 34px; font-size: 0.9rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <i class="fa fa-file-check"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-0">Required Documentation</h3>
                        </div>
                        <p class="text-muted small mb-3">To be presented during the campus interview stage:</p>

                        <ul class="doc-checklist">
                            <li><i class="fa fa-check-circle"></i> Certified Birth Certificate / B-Form</li>
                            <li><i class="fa fa-check-circle"></i> Previous Official School Transcripts</li>
                            <li><i class="fa fa-check-circle"></i> Complete Immunization &amp; Physical Health Record</li>
                            <li><i class="fa fa-check-circle"></i> 4 Recent Passport-size Scholar Photographs</li>
                            <li><i class="fa fa-check-circle"></i> Official School Leaving Certificate (SLC)</li>
                        </ul>
                    </div>

                    <!-- Need Assistance Box -->
                    <div class="admission-sidebar-card" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%); color: #ffffff; border: none;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fa fa-headset text-warning fs-4"></i>
                            <h3 class="h5 fw-bold text-white mb-0">Admissions Desk</h3>
                        </div>
                        <p class="text-white-50 small mb-3">Have questions regarding tuition, merit scholarships, or campus facilities?</p>
                        <div class="small text-white-50 d-flex flex-column gap-2 mb-3">
                            <div><i class="fa fa-phone text-warning me-2"></i> <?php echo htmlspecialchars($data['school']->phone ?? '+92-51-111-222-333', ENT_QUOTES, 'UTF-8'); ?></div>
                            <div><i class="fa fa-envelope text-warning me-2"></i> <?php echo htmlspecialchars($data['school']->email ?? 'info@citymodelschool.edu.pk', ENT_QUOTES, 'UTF-8'); ?></div>
                            <div><i class="fa fa-clock text-warning me-2"></i> Mon &ndash; Fri: 8:00 AM &ndash; 4:30 PM</div>
                        </div>
                        <a href="mailto:<?php echo htmlspecialchars($data['school']->email ?? 'info@citymodelschool.edu.pk', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-light rounded-pill w-100 fw-bold">
                            Email Admissions Team
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER PARTIAL -->
    <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tabbed Wizard Next / Prev Buttons
        document.querySelectorAll('.btn-next-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetBtnId = this.getAttribute('data-target');
                const targetTabBtn = document.querySelector(targetBtnId);
                if (targetTabBtn) {
                    const tab = new bootstrap.Tab(targetTabBtn);
                    tab.show();
                    window.scrollTo({ top: 300, behavior: 'smooth' });
                }
            });
        });

        document.querySelectorAll('.btn-prev-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetBtnId = this.getAttribute('data-target');
                const targetTabBtn = document.querySelector(targetBtnId);
                if (targetTabBtn) {
                    const tab = new bootstrap.Tab(targetTabBtn);
                    tab.show();
                    window.scrollTo({ top: 300, behavior: 'smooth' });
                }
            });
        });

        // Auto-show Document Requirements Popup when application is submitted
        <?php if(isset($data['success_message'])): ?>
        const modalEl = document.getElementById('homeRequiredDocsModal');
        if (modalEl) {
            const reqModal = new bootstrap.Modal(modalEl);
            reqModal.show();
        }
        <?php endif; ?>

        // Print Checklist Slip
        const slipBtn = document.getElementById('printHomeDocsSlipBtn');
        if (slipBtn) {
            slipBtn.addEventListener('click', function() {
                const printArea = document.getElementById('homeDocsPrintArea');
                if (!printArea) return;
                const printWin = window.open('', '_blank', 'width=850,height=900');
                printWin.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Mandatory Admission Document Submission Checklist</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                        <style>
                            body { font-family: Arial, sans-serif; padding: 25px; color: #1e293b; background: #fff; }
                            .header-box { border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class="text-center header-box">
                            <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></h3>
                            <div class="text-muted small">Admissions Secretariat &bull; Candidate Required Documents Slip</div>
                            <div class="badge bg-dark mt-2">Submission Deadline: Within 3 Working Days</div>
                        </div>
                        ${printArea.innerHTML}
                        <div class="text-center mt-4 pt-3 border-top text-muted small">
                            Please bring all attested photocopies along with original certificates to the Admissions Desk.
                            <br>Office Hours: Monday &ndash; Saturday (8:00 AM &ndash; 2:00 PM)
                        </div>
                        <script>
                            window.onload = function() { window.print(); }
                        <\/script>
                    </body>
                    </html>
                `);
                printWin.document.close();
            });
        }
    });
    </script>

    <!-- ========================================================================= -->
    <!-- MODAL: MANDATORY REQUIRED DOCUMENTS CHECKLIST (PUBLIC APPLICANT)          -->
    <!-- ========================================================================= -->
    <div class="modal fade" id="homeRequiredDocsModal" tabindex="-1" aria-labelledby="homeRequiredDocsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white py-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                            <i class="fa fa-clipboard-check fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="homeRequiredDocsModalLabel">Application Received &bull; Mandatory Required Documents</h5>
                            <small class="text-white-50">داخلہ فارم کے ساتھ درج ذیل ضروری دستاویزات اسکول آفس میں جمع کروائیں</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 bg-light" id="homeDocsPrintArea">
                    <?php if(!empty($data['submitted_application'])): ?>
                    <div class="card border-0 shadow-sm mb-3 bg-white">
                        <div class="card-body p-3">
                            <div class="row g-2 align-items-center">
                                <div class="col-sm-6 col-md-4 border-end">
                                    <span class="text-muted small d-block">Application Ref:</span>
                                    <span class="fw-bold text-success fs-6">ADM-#<?php echo (int)$data['submitted_application']['id']; ?></span>
                                </div>
                                <div class="col-sm-6 col-md-4 border-end">
                                    <span class="text-muted small d-block">Candidate Full Name:</span>
                                    <span class="fw-bold text-dark text-capitalize"><?php echo htmlspecialchars($data['submitted_application']['name']); ?></span>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <span class="text-muted small d-block">Father / Guardian:</span>
                                    <span class="fw-bold text-dark text-capitalize"><?php echo htmlspecialchars($data['submitted_application']['father_name'] ?? 'N/A'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start gap-3 mb-4 p-3 rounded-3" role="alert">
                        <i class="fa fa-bell fs-3 text-warning mt-1"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">
                                Notice for Parents &amp; Candidates / ضروری ہدایات
                            </h6>
                            <p class="small text-muted mb-1" style="line-height: 1.5;">
                                آپ کا آن لائن داخلہ فارم کامیابی سے موصول ہو چکا ہے۔ داخلہ کی حتمی تصدیق اور طالب علم کی کلاس الاٹمنٹ کے لیے <strong>درج ذیل تمام دستاویزات کی تصدیق شدہ فوٹو کاپیاں بمعہ پرنٹ شدہ داخلہ فارم</strong> 3 یوم کے اندر اسکول داخلہ ڈیسک پر جمع کروانا لازمی ہیں۔
                            </p>
                            <p class="small text-muted mb-0" style="line-height: 1.5;">
                                Thank you for applying. To complete candidate physical verification and enrolment, please bring the <strong>printed admission form along with attested photocopies of the following documents</strong> to the school office within <strong>3 working days</strong>.
                            </p>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fa fa-folder-open text-primary me-2"></i>Required Documents Checklist / ضروری دستاویزات
                    </h6>

                    <div class="list-group shadow-sm border-0 mb-4">
                        <!-- 1. Printed Form -->
                        <div class="list-group-item d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                            <i class="fa fa-check-circle text-success fs-5 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">1. Printed &amp; Signed Online Admission Application Form</h6>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">Mandatory (1 Copy)</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">
                                    Printed copy of the online admission form signed by Father/Guardian &amp; Student.
                                    <br><span class="text-secondary small">آن لائن داخلہ فارم کا پرنٹ بمعہ دستخط والد و امیدوار</span>
                                </p>
                            </div>
                        </div>

                        <!-- 2. Photographs -->
                        <div class="list-group-item d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                            <i class="fa fa-check-circle text-success fs-5 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">2. Passport Sized Colored Photographs</h6>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">4 Photographs</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">
                                    4x Recent passport-sized colored photographs of the candidate with sky-blue background.
                                    <br><span class="text-secondary small">امیدوار کی 4 عدد تازہ پاسپورٹ سائز تصاویر (نیلے بیک گراؤنڈ کے ساتھ)</span>
                                </p>
                            </div>
                        </div>

                        <!-- 3. Father CNIC -->
                        <div class="list-group-item d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                            <i class="fa fa-check-circle text-success fs-5 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">3. Father / Guardian CNIC Copies</h6>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">2 Attested Copies</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">
                                    2x Attested photocopies of Father's or Guardian's Computerized National Identity Card (CNIC).
                                    <br><span class="text-secondary small">والد یا سرپرست کے کمپیوٹرائزڈ شناختی کارڈ کی 2 عدد تصدیق شدہ کاپیاں</span>
                                </p>
                            </div>
                        </div>

                        <!-- 4. B-Form -->
                        <div class="list-group-item d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                            <i class="fa fa-check-circle text-success fs-5 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">4. Student NADRA B-Form / Smart Card</h6>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">2 Attested Copies</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">
                                    2x Attested photocopies of NADRA Bay-Form (ب-فارم) or Official Birth Certificate.
                                    <br><span class="text-secondary small">طالب علم کے نادرا ب-فارم یا پیدائش سرٹیفکیٹ کی 2 عدد تصدیق شدہ نقول</span>
                                </p>
                            </div>
                        </div>

                        <!-- 5. Original SLC -->
                        <div class="list-group-item d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                            <i class="fa fa-check-circle text-success fs-5 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">5. Original School Leaving Certificate (SLC / TC)</h6>
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle small">Original + 1 Copy</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">
                                    Original SLC from the previous school (signed &amp; stamped by Head of Institution).
                                    <br><span class="text-secondary small">پچھلے اسکول کا اصل اسکول لیونگ سرٹیفکیٹ (SLC) بمعہ 1 عدد کاپی</span>
                                </p>
                            </div>
                        </div>

                        <!-- 6. Marks Sheet -->
                        <div class="list-group-item d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                            <i class="fa fa-check-circle text-success fs-5 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">6. Previous Examination Report Card / DMC</h6>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">2 Attested Copies</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">
                                    2x Attested photocopies of previous class annual exam result card / marksheet.
                                    <br><span class="text-secondary small">سابقہ جماعت کے سالانہ امتحانی رزلٹ کارڈ / DMC کی 2 عدد نقول</span>
                                </p>
                            </div>
                        </div>

                        <!-- 7. Character Certificate -->
                        <div class="list-group-item d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                            <i class="fa fa-check-circle text-success fs-5 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">7. Character Certificate</h6>
                                    <span class="badge bg-secondary-subtle text-secondary border small">Class 6 &amp; Above</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">
                                    Character certificate issued by Head of previous school (mandatory for Class 6 &amp; above).
                                    <br><span class="text-secondary small">سابقہ اسکول کا کیریکٹر سرٹیفکیٹ (چھٹی اور اس سے اوپر کی کلاسز کے لیے)</span>
                                </p>
                            </div>
                        </div>

                        <!-- 8. Vaccination Card -->
                        <div class="list-group-item d-flex align-items-start gap-3 p-3 bg-white">
                            <i class="fa fa-check-circle text-success fs-5 mt-1"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-dark">8. Student Vaccination / Health Record Card</h6>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle small">1 Photocopy</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">
                                    Photocopy of government immunization / vaccination card or doctor's fitness certificate.
                                    <br><span class="text-secondary small">حفاظتی ٹیکوں کا کارڈ یا میڈیکل فٹنس سرٹیفکیٹ کی کاپی</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Timings & Location -->
                    <div class="p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="small text-muted">
                            <i class="fa fa-clock text-primary me-2"></i><strong>Office Timings:</strong> Monday to Saturday (8:00 AM &ndash; 2:00 PM)
                        </div>
                        <div class="small text-muted">
                            <i class="fa fa-map-marker-alt text-danger me-2"></i><strong>Location:</strong> School Admissions Desk / Front Office
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Close</button>
                    <div class="d-flex gap-2">
                        <?php if(!empty($data['submitted_application']['id'])): ?>
                            <a href="<?php echo URLROOT; ?>/frontoffice/printAdmissionForm/<?php echo (int)$data['submitted_application']['id']; ?>" target="_blank" class="btn btn-outline-primary fw-bold px-3">
                                <i class="fa fa-print me-1"></i> Print Admission Form
                            </a>
                        <?php endif; ?>
                        <button type="button" class="btn btn-success fw-bold px-3 shadow-sm" id="printHomeDocsSlipBtn">
                            <i class="fa fa-file-invoice me-1"></i> Print Checklist Slip
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
