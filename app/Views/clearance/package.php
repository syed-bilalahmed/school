<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$c = $data['clearance'];
$student = $data['student'];
$exams = $data['exams'] ?? [];
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 no-print">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/clearance/index" class="text-decoration-none text-muted">Clearance Hub</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Leaving Package</li>
            </ol>
        </nav>
        <h3 class="mb-0 fw-bold">Student Graduation &amp; Exit Package Bundle</h3>
        <p class="text-muted small mb-0">Consolidated institutional exit package: School Leaving Certificate, Character Certificate, Clearance NOC, and Academic DMC.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/clearance/detail/<?php echo $c->id; ?>" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Clearance Review
        </a>
        <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $c->student_id; ?>" class="btn btn-outline-primary btn-sm px-3">
            <i class="fa fa-user me-1"></i> Student 360° Profile
        </a>
    </div>
</div>

<!-- STUDENT HERO BANNER -->
<div class="card shadow-sm border-0 mb-4 bg-white">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-circle-lg bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 70px; height: 70px;">
                    <?php echo strtoupper(substr($c->student_name, 0, 1)); ?>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($c->student_name); ?></h4>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 font-monospace fw-bold">
                            <i class="fa fa-check-double me-1"></i>Clearance Verified
                        </span>
                        <?php if($c->student_current_status == 'Left'): ?>
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-1 fw-bold">Status: Left / Alumni</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted small">
                        <span>Admission No: <strong class="font-monospace"><?php echo htmlspecialchars($c->admission_no); ?></strong></span> &bull;
                        <span>Father: <strong><?php echo htmlspecialchars($c->father_name ?: 'N/A'); ?></strong></span> &bull;
                        <span>Class: <strong><?php echo htmlspecialchars($c->class_name ?? ''); ?> (<?php echo htmlspecialchars($c->section_name ?? 'A'); ?>)</strong></span> &bull;
                        <span>B-Form: <strong class="font-monospace text-primary"><?php echo htmlspecialchars($c->bform_cnic ?: 'N/A'); ?></strong></span>
                    </div>
                </div>
            </div>

            <div class="text-md-end">
                <div class="small text-muted">Clearance Certificate Reference:</div>
                <div class="fw-bold font-monospace text-primary fs-6"><?php echo htmlspecialchars($c->clearance_no); ?></div>
                <div class="fs-xs text-muted">Completed: <?php echo date('d M, Y', strtotime($c->completion_date ?: date('Y-m-d'))); ?></div>
            </div>
        </div>
    </div>
</div>

<!-- LEAVING CREDENTIALS BUNDLE CARDS -->
<h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
    <i class="fa fa-box-open text-primary"></i> 5 Essential Exit Credentials (Print Ready)
</h5>

<div class="row g-4 mb-4">

    <!-- 1. SCHOOL LEAVING CERTIFICATE (SLC / TC) -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 bg-danger-subtle text-danger rounded-3">
                    <i class="fa fa-file-contract fa-2x"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">School Leaving Certificate</h6>
                    <span class="badge bg-danger-subtle text-danger fs-xs">Legal Transfer Certificate (TC)</span>
                </div>
            </div>
            <p class="small text-muted mb-3 flex-grow-1">
                Formal legal document compliant with BISE Boards and Education Department. Includes 16 credentials, promotion class, dues clearance, and 3-tier signatures.
            </p>
            <a href="<?php echo URLROOT; ?>/certificate/slc/<?php echo $c->student_id; ?>" target="_blank" class="btn btn-outline-danger btn-sm w-100 fw-bold">
                <i class="fa fa-print me-1"></i> Print Official SLC / TC
            </a>
        </div>
    </div>

    <!-- 2. CHARACTER CERTIFICATE -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="fa fa-award fa-2x"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Character Certificate</h6>
                    <span class="badge bg-primary-subtle text-primary fs-xs">Conduct &amp; Moral Testimonial</span>
                </div>
            </div>
            <p class="small text-muted mb-3 flex-grow-1">
                Institutional testimonial certifying moral conduct, civic participation, discipline, and co-curricular achievements throughout enrollment.
            </p>
            <a href="<?php echo URLROOT; ?>/certificate/character/<?php echo $c->student_id; ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100 fw-bold">
                <i class="fa fa-print me-1"></i> Print Character Cert.
            </a>
        </div>
    </div>

    <!-- 3. CLEARANCE CERTIFICATE & NOC -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="fa fa-clipboard-check fa-2x"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Institutional Clearance NOC</h6>
                    <span class="badge bg-success-subtle text-success fs-xs">No-Objection Certificate</span>
                </div>
            </div>
            <p class="small text-muted mb-3 flex-grow-1">
                Official document certifying zero liabilities across Accounts, Library, Science &amp; IT Labs, Sports, and Class In-Charge.
            </p>
            <a href="<?php echo URLROOT; ?>/clearance/certificate/<?php echo $c->id; ?>" target="_blank" class="btn btn-outline-success btn-sm w-100 fw-bold">
                <i class="fa fa-print me-1"></i> Print Clearance NOC
            </a>
        </div>
    </div>

    <!-- 4. BONAFIDE ENROLLMENT CERTIFICATE -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 bg-info-subtle text-info rounded-3">
                    <i class="fa fa-stamp fa-2x"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Bonafide / Enrollment Cert.</h6>
                    <span class="badge bg-info-subtle text-info fs-xs">NADRA &amp; Embassy Verification</span>
                </div>
            </div>
            <p class="small text-muted mb-3 flex-grow-1">
                Official institutional verification used for NADRA Smart Card, Passport Office, and Provincial scholarships.
            </p>
            <a href="<?php echo URLROOT; ?>/certificate/bonafide/<?php echo $c->student_id; ?>" target="_blank" class="btn btn-outline-info btn-sm w-100 fw-bold">
                <i class="fa fa-print me-1"></i> Print Bonafide Cert.
            </a>
        </div>
    </div>

    <!-- 5. DETAILED MARKS CERTIFICATE (DMC) -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 bg-warning-subtle text-warning rounded-3">
                    <i class="fa fa-id-card fa-2x"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Academic DMC / Report Card</h6>
                    <span class="badge bg-warning-subtle text-dark fs-xs">Detailed Marks Certificate</span>
                </div>
            </div>
            <p class="small text-muted mb-3 flex-grow-1">
                Academic progress report with subject-wise marks breakdown, theory/practical split, grade scale, and position rank.
            </p>
            <?php if(!empty($exams)): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-warning text-dark btn-sm w-100 fw-bold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fa fa-print me-1"></i> Select Exam &amp; Print DMC
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 fs-xs">
                        <?php foreach($exams as $ex): ?>
                            <li>
                                <a class="dropdown-item py-2" href="<?php echo URLROOT; ?>/exam/reportCard/<?php echo $ex->id; ?>/<?php echo $c->student_id; ?>" target="_blank">
                                    <i class="fa fa-file-alt text-primary me-2"></i> <?php echo htmlspecialchars($ex->exam_name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <span class="text-muted small">No term exams available.</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- 6. STUDENT 360 DOSSIER PRINT -->
    <div class="col-md-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3 p-3 bg-light">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="p-3 bg-dark-subtle text-dark rounded-3">
                    <i class="fa fa-folder-open fa-2x"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Complete Student 360° Dossier</h6>
                    <span class="badge bg-dark-subtle text-dark fs-xs">Full Archival Record</span>
                </div>
            </div>
            <p class="small text-muted mb-3 flex-grow-1">
                Complete institutional profile containing attendance history, fee ledger, sibling links, and civil identity documents.
            </p>
            <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $c->student_id; ?>" target="_blank" class="btn btn-outline-dark btn-sm w-100 fw-bold">
                <i class="fa fa-external-link-alt me-1"></i> Open Student Profile
            </a>
        </div>
    </div>

</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
