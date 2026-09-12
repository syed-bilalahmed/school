<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Page Header & Action Bar -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/students/index" class="text-decoration-none text-muted">Students</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Admission Desk</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0 text-dark">
            <i class="fa fa-user-plus text-primary me-2"></i>Student Enrolment &amp; Admission Desk
        </h2>
        <p class="text-muted mb-0 small">Official Pakistani School Board Registration Dossier • NADRA Form-B • Father CNIC • Transfer &amp; SLC • Sibling Clustering</p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="<?php echo URLROOT; ?>/students/printAdmission?blank=1" target="_blank" class="btn btn-outline-dark btn-sm px-3 shadow-sm">
            <i class="fa fa-print me-1"></i> Print Blank A4 Admission Form
        </a>
        <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-users me-1"></i> Student Directory
        </a>
    </div>
</div>

<?php 
$old = $data['old_post'] ?? [];
$successStudentId = $_GET['student_id'] ?? null;
$successAdmNo = $_GET['admission_no'] ?? '';
?>

<!-- SUCCESS BANNER: ENROLLED CONFIRMATION WITH DIRECT A4 PRINT ACTION -->
<?php if(isset($_GET['success']) && $_GET['success'] === 'admitted'): ?>
    <div class="card shadow-sm border-0 mb-4" style="border-left: 6px solid #10b981 !important; background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border-radius: 12px;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-success text-white rounded-circle shadow-sm">
                        <i class="fa fa-check-circle fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-success mb-1">Student Enrolled Successfully!</h4>
                        <p class="text-muted mb-0 small">
                            The student admission record has been verified and registered. Admission No: <strong class="badge bg-dark font-monospace fs-6 text-white px-2 py-1"><?php echo htmlspecialchars($successAdmNo ?: 'Generated'); ?></strong>
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <?php if(!empty($successStudentId)): ?>
                        <a href="<?php echo URLROOT; ?>/students/printAdmission/<?php echo (int)$successStudentId; ?>" target="_blank" class="btn btn-dark fw-bold px-3 py-2 shadow-sm">
                            <i class="fa fa-print me-1 text-warning"></i> Print Official A4 Admission Form
                        </a>
                        <a href="<?php echo URLROOT; ?>/students/profile/<?php echo (int)$successStudentId; ?>" class="btn btn-outline-success fw-bold px-3 py-2">
                            <i class="fa fa-user-graduate me-1"></i> 360° Profile
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo URLROOT; ?>/students/admission" class="btn btn-success fw-bold px-3 py-2">
                        <i class="fa fa-plus me-1"></i> Enrol Next Student
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- DUPLICATE EMAIL DETECTED ALERT -->
<?php if(isset($_GET['error']) && $_GET['error'] == 'email_exists'): 
    $conflictEmail = $data['conflict_email'] ?? ($_GET['email'] ?? ($old['email'] ?? ''));
    $conflictUser = $data['conflict_user'] ?? null;
?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex flex-column gap-2 mb-4 p-3" role="alert" style="border-left: 5px solid #ef4444 !important; background-color: #fef2f2; border-radius: 12px;">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-exclamation-triangle fs-4 text-danger"></i>
            <h5 class="mb-0 fw-bold text-danger">Duplicate Email Detected!</h5>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="text-dark" style="font-size: 0.92rem;">
            The email address <strong class="badge bg-dark text-white px-2 py-1 font-monospace"><?php echo htmlspecialchars($conflictEmail ?: 'entered'); ?></strong> is already registered in the system<?php if($conflictUser): ?> and belongs to <strong><?php echo htmlspecialchars($conflictUser['name']); ?></strong> (System Role: <span class="badge bg-primary text-uppercase"><?php echo htmlspecialchars($conflictUser['role']); ?></span>)<?php endif; ?>. Please specify a unique login email for this student.
        </div>
        <div class="d-flex flex-wrap gap-2 pt-2 border-top border-danger-subtle align-items-center mt-1">
            <span class="small text-muted fw-bold"><i class="fa fa-compass me-1"></i>Quick Lookup:</span>
            <a href="<?php echo URLROOT; ?>/admin/users?search=<?php echo urlencode($conflictEmail); ?>" class="btn btn-sm btn-danger px-3 py-1 shadow-xs" target="_blank">
                <i class="fa fa-users-cog me-1"></i> View in All Users
            </a>
            <?php if($conflictUser && $conflictUser['role'] === 'student'): ?>
                <a href="<?php echo URLROOT; ?>/students/index?search=<?php echo urlencode($conflictUser['name']); ?>" class="btn btn-sm btn-outline-danger px-3 py-1" target="_blank">
                    <i class="fa fa-user-graduate me-1"></i> View Student Record
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Visual Process Pipeline -->
<div class="card shadow-sm border-0 mb-4 bg-white">
    <div class="card-body p-3">
        <div class="row text-center g-2 g-md-0 align-items-center">
            <div class="col-6 col-md-3">
                <div class="p-2 border-end border-light-subtle text-start">
                    <span class="badge bg-primary-subtle text-primary mb-1">Step 1</span>
                    <div class="fw-bold small text-dark"><i class="fa fa-graduation-cap text-primary me-1"></i>Academic Placement</div>
                    <small class="text-muted smaller">Session, Class &amp; Section</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-2 border-end border-light-subtle text-start">
                    <span class="badge bg-success-subtle text-success mb-1">Step 2</span>
                    <div class="fw-bold small text-dark"><i class="fa fa-id-card text-success me-1"></i>Civil Demographics</div>
                    <small class="text-muted smaller">NADRA Form-B &amp; Bio</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-2 border-end border-light-subtle text-start">
                    <span class="badge bg-purple-subtle text-purple mb-1" style="color: #8b5cf6;">Step 3</span>
                    <div class="fw-bold small text-dark"><i class="fa fa-users text-purple me-1" style="color: #8b5cf6;"></i>Parents &amp; Address</div>
                    <small class="text-muted smaller">CNIC, Mobile &amp; Residence</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-2 text-start">
                    <span class="badge bg-warning-subtle text-warning mb-1">Step 4</span>
                    <div class="fw-bold small text-dark"><i class="fa fa-exchange-alt text-warning me-1"></i>Admission Mode</div>
                    <small class="text-muted smaller">Fresh vs Transfer SLC</small>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="<?php echo URLROOT; ?>/students/store" method="post" id="studentAdmissionForm">
    <div class="row g-4">
        <!-- Main Form Left Column -->
        <div class="col-lg-8">

            <!-- SECTION 0: FRESH ADMISSION VS TRANSFER SWITCHER -->
            <div class="card shadow-sm border-0 mb-4 border-start border-primary border-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                <div class="card-body p-3 p-md-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h5 class="fw-bold mb-1 text-dark">
                                <i class="fa fa-school text-primary me-2"></i>Admission Category (داخلہ کی قسم)
                            </h5>
                            <p class="text-muted small mb-0">Is this a <strong>Fresh Admission</strong> (starting schooling / first entry) or a <strong>Transfer</strong> from another institution with a School Leaving Certificate (SLC)?</p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <div class="btn-group w-100 shadow-sm" role="group" aria-label="Fresh Admission Toggle">
                                <input type="radio" class="btn-check" name="is_fresh_admission" id="freshYes" value="1" <?php echo (($old['is_fresh_admission'] ?? '1') == '1') ? 'checked' : ''; ?> onchange="toggleTransferFields()">
                                <label class="btn btn-outline-success fw-bold py-2" for="freshYes">
                                    <i class="fa fa-check-circle me-1"></i> Fresh Admission<br><small class="fw-normal">طالب علم کا نیا داخلہ</small>
                                </label>

                                <input type="radio" class="btn-check" name="is_fresh_admission" id="freshNo" value="0" <?php echo (($old['is_fresh_admission'] ?? '') === '0') ? 'checked' : ''; ?> onchange="toggleTransferFields()">
                                <label class="btn btn-outline-primary fw-bold py-2" for="freshNo">
                                    <i class="fa fa-exchange-alt me-1"></i> Transfer (SLC)<br><small class="fw-normal">دیگر سکول سے منتقلی</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 1: PREVIOUS SCHOOL & TRANSFER CREDENTIALS (DYNAMIC UNFOLD) -->
            <div class="card shadow-sm border-0 mb-4 border-start border-warning border-4" id="transferCredentialsCard" style="<?php echo (($old['is_fresh_admission'] ?? '1') == '1') ? 'display: none;' : ''; ?>">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-warning-subtle text-warning rounded-3">
                        <i class="fa fa-history fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Previous School &amp; Transfer Credentials (سابقہ تعلیمی ادارہ)</h5>
                        <small class="text-muted">Enter School Leaving Certificate (SLC), past institute, marks obtained, and migration credentials.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-dark">Previous School / College Name</label>
                            <input type="text" name="prev_school_name" class="form-control" placeholder="e.g. Army Public School, Lahore / Beaconhouse / Govt High School" value="<?php echo htmlspecialchars($old['prev_school_name'] ?? ($old['previous_school'] ?? '')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">School City / District</label>
                            <input type="text" name="prev_school_city" class="form-control" placeholder="e.g. Rawalpindi / Lahore" value="<?php echo htmlspecialchars($old['prev_school_city'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Last Class Passed / Studied</label>
                            <input type="text" name="prev_class" class="form-control" placeholder="e.g. Class 7th / Grade 8" value="<?php echo htmlspecialchars($old['prev_class'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Medium of Instruction</label>
                            <select name="prev_medium" class="form-select">
                                <option value="English" <?php echo (($old['prev_medium'] ?? 'English') === 'English') ? 'selected' : ''; ?>>English Medium</option>
                                <option value="Urdu" <?php echo (($old['prev_medium'] ?? '') === 'Urdu') ? 'selected' : ''; ?>>Urdu Medium</option>
                                <option value="Bilingual" <?php echo (($old['prev_medium'] ?? '') === 'Bilingual') ? 'selected' : ''; ?>>Bilingual / Both</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Board / School Roll No.</label>
                            <input type="text" name="prev_board_roll_no" class="form-control font-monospace" placeholder="e.g. 592819" value="<?php echo htmlspecialchars($old['prev_board_roll_no'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">SLC / TC Number</label>
                            <input type="text" name="slc_number" class="form-control font-monospace" placeholder="e.g. SLC-2025-0819" value="<?php echo htmlspecialchars($old['slc_number'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">SLC Issue Date</label>
                            <input type="date" name="slc_date" class="form-control" value="<?php echo htmlspecialchars($old['slc_date'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Grade / Division Awarded</label>
                            <input type="text" name="prev_grade" class="form-control" placeholder="e.g. A+ / A / 1st Div" value="<?php echo htmlspecialchars($old['prev_grade'] ?? ''); ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Marks Obtained</label>
                            <input type="number" step="0.5" name="prev_marks_obtained" class="form-control" placeholder="e.g. 485" value="<?php echo htmlspecialchars($old['prev_marks_obtained'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Total Marks</label>
                            <input type="number" step="0.5" name="prev_total_marks" class="form-control" placeholder="e.g. 550" value="<?php echo htmlspecialchars($old['prev_total_marks'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Reason for Leaving Previous School</label>
                            <input type="text" name="reason_for_leaving" class="form-control" placeholder="e.g. Parent Job Transfer / Relocation / Better Education" value="<?php echo htmlspecialchars($old['reason_for_leaving'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: ACADEMIC PLACEMENT -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary-subtle text-primary rounded-3">
                        <i class="fa fa-graduation-cap fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Academic Placement &amp; Enrolment</h5>
                        <small class="text-muted">Select target academic session, class/grade, section and roll details.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Academic Session <span class="text-danger">*</span></label>
                            <select name="academic_session_id" id="sessionSelect" class="form-select" required onchange="updateSummaryPreview()">
                                <?php foreach($data['sessions'] as $session): ?>
                                    <option value="<?php echo $session->id; ?>" <?php echo (!empty($data['current_session']) && $data['current_session']->id == $session->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($session->session_name); ?> <?php echo $session->is_current ? '(Active)' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Class / Grade <span class="text-danger">*</span></label>
                            <select name="class_id" id="classSelect" class="form-select" required onchange="filterSections(); updateSummaryPreview();">
                                <option value="">Select Class</option>
                                <?php foreach($data['classes'] as $class): ?>
                                    <option value="<?php echo $class->id; ?>" <?php echo (isset($_GET['class_id']) && $_GET['class_id'] == $class->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class->class_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Section <span class="text-danger">*</span></label>
                            <select name="section_id" id="sectionSelect" class="form-select" required onchange="updateSummaryPreview()">
                                <option value="">Select Section</option>
                                <?php foreach($data['sections'] as $section): ?>
                                    <option value="<?php echo $section->id; ?>" data-class="<?php echo $section->class_id; ?>" class="section-option">
                                        <?php echo htmlspecialchars($section->section_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Admission Number</label>
                            <input type="text" name="admission_no" id="inputAdmNo" class="form-control font-monospace" placeholder="Auto-generated if blank" oninput="updateSummaryPreview()">
                            <small class="text-muted smaller">System generates automatic sequence</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Class Roll Number</label>
                            <input type="text" name="roll_no" class="form-control font-monospace" placeholder="Auto-assigned if blank">
                            <small class="text-muted smaller">Next available class sequence</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Board / Registration #</label>
                            <input type="text" name="reg_no" class="form-control" placeholder="e.g. REG-2026-004">
                            <small class="text-muted smaller">Board/FBISE registration if assigned</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Admission Date <span class="text-danger">*</span></label>
                            <input type="date" name="admission_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Student Enrolment Status</label>
                            <select name="status" class="form-select">
                                <option value="Active" selected>Active / Regular Enrolment</option>
                                <option value="Probationary">Probationary Enrolment</option>
                                <option value="Pending Clearance">Pending Clearance</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: CIVIL & PERSONAL IDENTITY (PAKISTANI DEMOGRAPHICS) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-success-subtle text-success rounded-3">
                        <i class="fa fa-id-card fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Civil Identity &amp; Personal Particulars</h5>
                        <small class="text-muted">NADRA Form-B / CRC, date of birth, religion, nationality, and medical notes.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Full Student Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="inputStudentName" class="form-control" placeholder="e.g. Muhammad Ali Khan" value="<?php echo htmlspecialchars($old['name'] ?? ($_GET['name'] ?? '')); ?>" required oninput="updateSummaryPreview()">
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="form-label small fw-bold text-dark mb-1">NADRA B-Form / CNIC <span class="text-danger">*</span></label>
                                <span class="badge bg-light text-secondary border small">13 Digits</span>
                            </div>
                            <input type="text" name="bform_cnic" id="inputFormB" class="form-control font-monospace cnic-mask" placeholder="35201-XXXXXXX-X" value="<?php echo htmlspecialchars($old['bform_cnic'] ?? ''); ?>" required maxlength="15">
                            <small class="text-muted smaller">Pakistani Child Registration Certificate (CRC / ب فارم)</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($old['dob'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="Male" <?php echo (($old['gender'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male (لڑکا)</option>
                                <option value="Female" <?php echo (($old['gender'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female (لڑکی)</option>
                                <option value="Other" <?php echo (($old['gender'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">Unknown / Select</option>
                                <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                                    <option value="<?php echo $bg; ?>" <?php echo (($old['blood_group'] ?? '') === $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Religion</label>
                            <select name="religion" class="form-select">
                                <option value="Islam" <?php echo (($old['religion'] ?? 'Islam') === 'Islam') ? 'selected' : ''; ?>>Islam (مسلمان)</option>
                                <option value="Christianity" <?php echo (($old['religion'] ?? '') === 'Christianity') ? 'selected' : ''; ?>>Christianity (عیسائی)</option>
                                <option value="Hinduism" <?php echo (($old['religion'] ?? '') === 'Hinduism') ? 'selected' : ''; ?>>Hinduism (ہندو)</option>
                                <option value="Sikhism" <?php echo (($old['religion'] ?? '') === 'Sikhism') ? 'selected' : ''; ?>>Sikhism (سکھ)</option>
                                <option value="Other" <?php echo (($old['religion'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Nationality</label>
                            <select name="nationality" class="form-select">
                                <option value="Pakistani" <?php echo (($old['nationality'] ?? 'Pakistani') === 'Pakistani') ? 'selected' : ''; ?>>Pakistani (پاکستانی)</option>
                                <option value="Dual / Overseas" <?php echo (($old['nationality'] ?? '') === 'Dual / Overseas') ? 'selected' : ''; ?>>Dual / Overseas Pakistani</option>
                                <option value="Foreign National" <?php echo (($old['nationality'] ?? '') === 'Foreign National') ? 'selected' : ''; ?>>Foreign National</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Mother Tongue (مادری زبان)</label>
                            <select name="mother_tongue" class="form-select">
                                <option value="Urdu" <?php echo (($old['mother_tongue'] ?? 'Urdu') === 'Urdu') ? 'selected' : ''; ?>>Urdu (اردو)</option>
                                <option value="Punjabi" <?php echo (($old['mother_tongue'] ?? '') === 'Punjabi') ? 'selected' : ''; ?>>Punjabi (پنجابی)</option>
                                <option value="Pashto" <?php echo (($old['mother_tongue'] ?? '') === 'Pashto') ? 'selected' : ''; ?>>Pashto (پشتو)</option>
                                <option value="Sindhi" <?php echo (($old['mother_tongue'] ?? '') === 'Sindhi') ? 'selected' : ''; ?>>Sindhi (سندھی)</option>
                                <option value="Balochi" <?php echo (($old['mother_tongue'] ?? '') === 'Balochi') ? 'selected' : ''; ?>>Balochi (بلوچی)</option>
                                <option value="Saraiki" <?php echo (($old['mother_tongue'] ?? '') === 'Saraiki') ? 'selected' : ''; ?>>Saraiki (سرائیکی)</option>
                                <option value="English" <?php echo (($old['mother_tongue'] ?? '') === 'English') ? 'selected' : ''; ?>>English</option>
                                <option value="Other" <?php echo (($old['mother_tongue'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Student Email (System Login) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="e.g. ali@school.edu.pk" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>" required>
                            </div>
                            <small class="text-muted smaller">Unique username for portal &amp; homework submission</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Initial Portal Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa fa-key"></i></span>
                                <input type="text" name="password" class="form-control font-monospace" value="<?php echo htmlspecialchars($old['password'] ?? '123456'); ?>" placeholder="Default: 123456">
                            </div>
                            <small class="text-muted smaller">Default initial passcode (Changeable upon first login)</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Special Medical Needs / Physical Disabilities (if any)</label>
                            <input type="text" name="special_needs" class="form-control" placeholder="e.g. Asthma, Glasses, Food Allergies, or None" value="<?php echo htmlspecialchars($old['special_needs'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: PARENTS & GUARDIAN PROFILE (PAKISTANI STANDARD) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 rounded-3" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.1);">
                        <i class="fa fa-users fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Parents &amp; Guardian Profile (والدین / سرپرست کے کوائف)</h5>
                        <small class="text-muted">Father/Mother CNIC, profession, monthly income, emergency contact and residential address.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Father Info -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Father's Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" id="inputFatherName" class="form-control" placeholder="e.g. Tariq Mehmood" value="<?php echo htmlspecialchars($old['father_name'] ?? ($_GET['father_name'] ?? '')); ?>" required oninput="updateSummaryPreview()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Father's NADRA CNIC <span class="text-danger">*</span></label>
                            <input type="text" name="father_cnic" id="inputFatherCnic" class="form-control font-monospace cnic-mask" placeholder="35201-XXXXXXX-X" value="<?php echo htmlspecialchars($old['father_cnic'] ?? ''); ?>" required maxlength="15">
                            <small class="text-muted smaller">13 digits NADRA CNIC (Used for sibling auto-link)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Father's Mobile / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="parent_phone" id="inputFatherPhone" class="form-control font-monospace phone-mask" placeholder="0300-1234567" value="<?php echo htmlspecialchars($old['parent_phone'] ?? ($_GET['phone'] ?? '')); ?>" required oninput="updateSummaryPreview()">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Father's Profession / Department</label>
                            <input type="text" name="father_occupation" class="form-control" placeholder="e.g. Businessman / Govt Officer / Engineer" value="<?php echo htmlspecialchars($old['father_occupation'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Father's Monthly Income (PKR)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rs.</span>
                                <input type="text" name="father_income" class="form-control" placeholder="e.g. 100,000" value="<?php echo htmlspecialchars($old['father_income'] ?? ''); ?>">
                            </div>
                        </div>

                        <!-- Mother Info -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Mother's Full Name</label>
                            <input type="text" name="mother_name" class="form-control" placeholder="e.g. Nasreen Tariq" value="<?php echo htmlspecialchars($old['mother_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Mother's NADRA CNIC</label>
                            <input type="text" name="mother_cnic" class="form-control font-monospace cnic-mask" placeholder="35201-XXXXXXX-X" value="<?php echo htmlspecialchars($old['mother_cnic'] ?? ''); ?>" maxlength="15">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Mother's Occupation</label>
                            <input type="text" name="mother_occupation" class="form-control" placeholder="e.g. Housewife / Teacher / Doctor" value="<?php echo htmlspecialchars($old['mother_occupation'] ?? 'Housewife'); ?>">
                        </div>

                        <!-- Guardian (if other than Father) -->
                        <div class="col-12 pt-2 border-top">
                            <span class="badge bg-light text-secondary border mb-2">Guardian Particulars (Fill only if father is deceased, abroad, or guardian is appointed)</span>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control" placeholder="Leave blank if father is guardian" value="<?php echo htmlspecialchars($old['guardian_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Guardian Relationship</label>
                            <select name="guardian_relation" class="form-select">
                                <?php foreach(['Father', 'Mother', 'Brother', 'Uncle', 'Grandparent', 'Other'] as $rel): ?>
                                    <option value="<?php echo $rel; ?>" <?php echo (($old['guardian_relation'] ?? 'Father') === $rel) ? 'selected' : ''; ?>><?php echo $rel; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Guardian CNIC</label>
                            <input type="text" name="guardian_cnic" class="form-control font-monospace cnic-mask" placeholder="35201-XXXXXXX-X" value="<?php echo htmlspecialchars($old['guardian_cnic'] ?? ''); ?>" maxlength="15">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Guardian Phone / WhatsApp</label>
                            <input type="text" name="guardian_phone" class="form-control font-monospace phone-mask" placeholder="0300-1234567" value="<?php echo htmlspecialchars($old['guardian_phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Guardian Occupation</label>
                            <input type="text" name="guardian_occupation" class="form-control" placeholder="e.g. Advocate / Contractor" value="<?php echo htmlspecialchars($old['guardian_occupation'] ?? ''); ?>">
                        </div>

                        <!-- Addresses -->
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">City</label>
                            <input type="text" name="city" id="inputCity" class="form-control" placeholder="e.g. Islamabad / Lahore" value="<?php echo htmlspecialchars($old['city'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">District (ضلع)</label>
                            <input type="text" name="district" id="inputDistrict" class="form-control" placeholder="e.g. Rawalpindi" value="<?php echo htmlspecialchars($old['district'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Tehsil (تحصیل)</label>
                            <input type="text" name="tehsil" id="inputTehsil" class="form-control" placeholder="e.g. Gujar Khan / Cantt" value="<?php echo htmlspecialchars($old['tehsil'] ?? ''); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Current Residential Address (موجودہ رہائشی پتہ) <span class="text-danger">*</span></label>
                            <textarea name="address" id="currentAddress" class="form-control" rows="2" placeholder="House #, Street #, Sector / Colony, City..." required><?php echo htmlspecialchars($old['address'] ?? ''); ?></textarea>
                        </div>

                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label small fw-bold text-dark mb-0">Permanent Home Address (مستقل پتہ)</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="sameAddressCheck" onchange="copyAddress()">
                                    <label class="form-check-label small text-muted" for="sameAddressCheck">Same as Current Residential Address</label>
                                </div>
                            </div>
                            <textarea name="permanent_address" id="permAddress" class="form-control" rows="2" placeholder="Village / Mauza / Town, Tehsil &amp; District..."><?php echo htmlspecialchars($old['permanent_address'] ?? ($old['address'] ?? '')); ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Parent Portal Email (Optional)</label>
                            <input type="email" name="parent_email" class="form-control" placeholder="parent@example.com (To link to parent mobile portal)" value="<?php echo htmlspecialchars($old['parent_email'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Side Configuration & Summary Column (Sticky) -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 85px; z-index: 10;">

                <!-- LIVE REGISTRATION DOSSIER PREVIEW -->
                <div class="card shadow-sm border-0 mb-3 border-top border-primary border-3" style="background: #ffffff;">
                    <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="fa fa-id-badge text-primary me-2"></i>Live Registration Summary
                        </h6>
                        <span class="badge bg-primary-subtle text-primary small" id="liveBadgeCategory">Fresh</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded-3 bg-light">
                            <div style="width: 45px; height: 45px; border-radius: 50%; background: #1e3a8a; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold;">
                                <i class="fa fa-user"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark" id="previewStudentName">Candidate Name</h6>
                                <small class="text-muted" id="previewPlacement">Class &bull; Section</small>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush small mb-0">
                            <li class="list-group-item px-0 py-1 d-flex justify-content-between text-muted">
                                <span>Academic Session:</span>
                                <strong class="text-dark" id="previewSession">-</strong>
                            </li>
                            <li class="list-group-item px-0 py-1 d-flex justify-content-between text-muted">
                                <span>Admission No:</span>
                                <strong class="text-dark font-monospace" id="previewAdmNo">Auto-Assigned</strong>
                            </li>
                            <li class="list-group-item px-0 py-1 d-flex justify-content-between text-muted">
                                <span>Father / Guardian:</span>
                                <strong class="text-dark" id="previewFatherName">-</strong>
                            </li>
                            <li class="list-group-item px-0 py-1 d-flex justify-content-between text-muted">
                                <span>Emergency Phone:</span>
                                <strong class="text-dark font-monospace" id="previewFatherPhone">-</strong>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Family Unit Clustering Card -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white py-2 border-0 border-bottom">
                        <h6 class="fw-bold mb-0 text-dark small">
                            <i class="fa fa-link text-primary me-2"></i>Family Unit Clustering
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <p class="text-muted smaller mb-2">
                            System will auto-link siblings sharing the same <strong>Father CNIC</strong> to their family unit and apply sibling discounts.
                        </p>
                        <label class="form-label smaller fw-bold text-dark">Existing Family Group</label>
                        <select name="family_id" class="form-select form-select-sm mb-2">
                            <option value="">-- Auto-Create / Link via Father CNIC --</option>
                            <?php foreach($data['families'] as $fam): ?>
                                <option value="<?php echo htmlspecialchars($fam->family_code); ?>">
                                    <?php echo htmlspecialchars($fam->family_code . ' - ' . $fam->father_name . ' (' . ($fam->student_count ?? 0) . ' children)'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Fee Concession & Policy Card -->
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-white py-2 border-0 border-bottom">
                        <h6 class="fw-bold mb-0 text-dark small">
                            <i class="fa fa-percent text-success me-2"></i>Fee Policy &amp; Concessions
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-2">
                            <label class="form-label smaller fw-bold text-dark">Concession Category</label>
                            <select name="concession_type" class="form-select form-select-sm">
                                <option value="None" selected>None (Standard Fee)</option>
                                <option value="Sibling Concession">Sibling Concession</option>
                                <option value="Staff Child">Staff Child (Teacher Ward)</option>
                                <option value="Merit Scholarship">Merit Scholarship</option>
                                <option value="Need-based Relief">Need-based Financial Relief</option>
                                <option value="Hafiz-e-Quran">Hafiz-e-Quran</option>
                                <option value="Orphan Support">Orphan Support</option>
                            </select>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label smaller fw-bold text-dark">Sibling %</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" min="0" max="100" name="sibling_discount_percent" class="form-control" value="0.00">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label smaller fw-bold text-dark">Fixed Relief</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" step="100" min="0" name="custom_discount_amount" class="form-control" value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submission Actions -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-3">
                        <button type="submit" name="action_print" value="1" class="btn btn-dark w-100 py-2 fw-bold mb-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fa fa-print text-warning"></i> Enrol &amp; Print Official A4 Form
                        </button>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fa fa-check-circle"></i> Confirm &amp; Enrol Student
                        </button>
                        <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-outline-secondary w-100 py-2 btn-sm">
                            Cancel
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

<script>
    // Filter sections based on selected class
    function filterSections(){
        var classId = document.getElementById('classSelect').value;
        var options = document.getElementsByClassName('section-option');
        
        for(var i=0; i<options.length; i++){
            if(!classId || options[i].getAttribute('data-class') == classId){
                options[i].style.display = '';
            } else {
                options[i].style.display = 'none';
            }
        }
        document.getElementById('sectionSelect').value = "";
    }

    // Toggle Previous School / Transfer section
    function toggleTransferFields() {
        var isFresh = document.getElementById('freshYes').checked;
        var transferCard = document.getElementById('transferCredentialsCard');
        var liveBadge = document.getElementById('liveBadgeCategory');
        if (transferCard) {
            if (isFresh) {
                transferCard.style.display = 'none';
                if(liveBadge) {
                    liveBadge.className = 'badge bg-success-subtle text-success small';
                    liveBadge.innerText = 'Fresh Admission';
                }
            } else {
                transferCard.style.display = 'block';
                if(liveBadge) {
                    liveBadge.className = 'badge bg-primary-subtle text-primary small';
                    liveBadge.innerText = 'Transfer (SLC)';
                }
            }
        }
    }

    // Copy Current Address to Permanent Address
    function copyAddress() {
        var check = document.getElementById('sameAddressCheck');
        var curr = document.getElementById('currentAddress');
        var perm = document.getElementById('permAddress');
        if (check && check.checked && curr && perm) {
            perm.value = curr.value;
        }
    }

    // Reactive Preview Drawer Updater
    function updateSummaryPreview() {
        var nameInput = document.getElementById('inputStudentName');
        var previewName = document.getElementById('previewStudentName');
        if (previewName) {
            previewName.innerText = (nameInput && nameInput.value.trim()) ? nameInput.value.trim() : 'Candidate Name';
        }

        var classSel = document.getElementById('classSelect');
        var secSel = document.getElementById('sectionSelect');
        var previewPlacement = document.getElementById('previewPlacement');
        if (previewPlacement) {
            var cText = classSel && classSel.selectedIndex > 0 ? classSel.options[classSel.selectedIndex].text : 'Class';
            var sText = secSel && secSel.selectedIndex > 0 ? secSel.options[secSel.selectedIndex].text : 'Section';
            previewPlacement.innerText = cText + ' • ' + sText;
        }

        var sessionSel = document.getElementById('sessionSelect');
        var previewSession = document.getElementById('previewSession');
        if (previewSession && sessionSel && sessionSel.selectedIndex >= 0) {
            previewSession.innerText = sessionSel.options[sessionSel.selectedIndex].text;
        }

        var admInput = document.getElementById('inputAdmNo');
        var previewAdm = document.getElementById('previewAdmNo');
        if (previewAdm) {
            previewAdm.innerText = (admInput && admInput.value.trim()) ? admInput.value.trim() : 'Auto-Assigned';
        }

        var fName = document.getElementById('inputFatherName');
        var previewFather = document.getElementById('previewFatherName');
        if (previewFather) {
            previewFather.innerText = (fName && fName.value.trim()) ? fName.value.trim() : '-';
        }

        var fPhone = document.getElementById('inputFatherPhone');
        var previewPhone = document.getElementById('previewFatherPhone');
        if (previewPhone) {
            previewPhone.innerText = (fPhone && fPhone.value.trim()) ? fPhone.value.trim() : '-';
        }
    }

    // Pakistani CNIC & Mobile Masking Engine
    document.addEventListener('DOMContentLoaded', function() {
        // CNIC Input Auto-mask (35201-1234567-1)
        document.querySelectorAll('.cnic-mask').forEach(function(input) {
            input.addEventListener('input', function(e) {
                var value = this.value.replace(/[^0-9]/g, '');
                if (value.length > 13) value = value.substring(0, 13);
                
                var formatted = '';
                if (value.length > 5) {
                    formatted += value.substring(0, 5) + '-';
                    if (value.length > 12) {
                        formatted += value.substring(5, 12) + '-' + value.substring(12, 13);
                    } else {
                        formatted += value.substring(5);
                    }
                } else {
                    formatted = value;
                }
                this.value = formatted;
            });
        });

        // Pakistani Phone Auto-mask (0300-1234567)
        document.querySelectorAll('.phone-mask').forEach(function(input) {
            input.addEventListener('input', function(e) {
                var value = this.value.replace(/[^0-9]/g, '');
                if (value.length > 11) value = value.substring(0, 11);
                
                var formatted = '';
                if (value.length > 4) {
                    formatted = value.substring(0, 4) + '-' + value.substring(4);
                } else {
                    formatted = value;
                }
                this.value = formatted;
                updateSummaryPreview();
            });
        });

        updateSummaryPreview();
        toggleTransferFields();
    });
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
