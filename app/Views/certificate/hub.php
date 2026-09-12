<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- TOP EXECUTIVE HEADER -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1">
            <i class="fa fa-stamp text-primary me-2"></i>Certificates &amp; Credentials Hub (Modules 20, 21)
        </h2>
        <p class="text-muted small mb-0">Issue School Leaving Certificates (SLC), Character Certificates, Bonafide Proofs &amp; Examination Roll Number Slips.</p>
    </div>
    
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo URLROOT; ?>/exam/index" class="btn btn-outline-secondary btn-sm px-3 fw-bold" style="border-radius: 8px;">
            <i class="fa fa-file-signature text-primary me-1"></i> Exams Hub
        </a>
        <a href="<?php echo URLROOT; ?>/certificate/index" class="btn btn-outline-primary btn-sm px-3 fw-bold" style="border-radius: 8px;">
            <i class="fa fa-palette me-1"></i> Custom Templates
        </a>
    </div>
</div>

<!-- QUICK ACTION HERO CARDS -->
<div class="row g-3 mb-4">
    <!-- Card 1: School Leaving Certificate (SLC) -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="brand-icon-box mb-3" style="width: 46px; height: 46px; border-radius: 12px; background: rgba(30, 58, 138, 0.1); color: #1e3a8a; font-size: 1.3rem;">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">School Leaving Certificate</h6>
                    <p class="text-muted small mb-3">Official transfer certificate with BISE/Board registration, conduct, and dues verification.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 font-monospace" style="font-size: 0.72rem;">Module 20 &bull; SLC/TC</span>
            </div>
        </div>
    </div>

    <!-- Card 2: Character Certificate -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="brand-icon-box mb-3" style="width: 46px; height: 46px; border-radius: 12px; background: rgba(180, 83, 9, 0.1); color: #b45309; font-size: 1.3rem;">
                        <i class="fa fa-certificate"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Character Certificate</h6>
                    <p class="text-muted small mb-3">Attestation of good moral conduct, discipline, and co-curricular achievements for college admissions.</p>
                </div>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 font-monospace" style="font-size: 0.72rem;">Module 20 &bull; Conduct</span>
            </div>
        </div>
    </div>

    <!-- Card 3: Bonafide Certificate -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="brand-icon-box mb-3" style="width: 46px; height: 46px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 1.3rem;">
                        <i class="fa fa-user-check"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Bonafide Certificate</h6>
                    <p class="text-muted small mb-3">Institutional enrollment proof for NADRA Smart Card, Passport Office, Visa, or Scholarships.</p>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 font-monospace" style="font-size: 0.72rem;">Module 20 &bull; Status Proof</span>
            </div>
        </div>
    </div>

    <!-- Card 4: Roll No Slips / Admit Cards -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <div class="brand-icon-box mb-3" style="width: 46px; height: 46px; border-radius: 12px; background: rgba(220, 38, 38, 0.1); color: #dc2626; font-size: 1.3rem;">
                        <i class="fa fa-id-card"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Exam Roll No Slips</h6>
                    <p class="text-muted small mb-3">Printable examination admit cards with timetable papers schedule, photo, and rules.</p>
                </div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 font-monospace" style="font-size: 0.72rem;">Module 21 &bull; Admit Cards</span>
            </div>
        </div>
    </div>
</div>

<!-- BATCH CLASS ADMIT CARDS GENERATOR BAR -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color: #ffffff;">
    <div class="card-body p-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-5">
                <h5 class="fw-bold mb-1"><i class="fa fa-layer-group text-warning me-2"></i>Class Batch Roll Number Slips Generator</h5>
                <p class="small opacity-85 mb-0">Generate &amp; mass-print admit cards for all students in a class with scheduled exam papers.</p>
            </div>
            <div class="col-lg-7">
                <form action="<?php echo URLROOT; ?>/certificate/batchAdmitCards" method="GET" target="_blank" class="row g-2 justify-content-end">
                    <div class="col-sm-5">
                        <select name="class_id" class="form-select form-select-sm" required style="border-radius: 8px;">
                            <option value="">-- Choose Class --</option>
                            <?php foreach($data['classes'] as $cls): ?>
                                <option value="<?php echo $cls->id; ?>"><?php echo htmlspecialchars($cls->class_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select name="exam_id" class="form-select form-select-sm" style="border-radius: 8px;">
                            <option value="">-- Latest Active Exam --</option>
                            <?php foreach($data['exams'] as $ex): ?>
                                <option value="<?php echo $ex->id; ?>"><?php echo htmlspecialchars($ex->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold" style="border-radius: 8px;">
                            <i class="fa fa-print me-1"></i> Print Batch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- STUDENT DIRECTORY & CREDENTIALS ISSUER TABLE -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header bg-white py-3 px-4 d-flex flex-wrap justify-content-between align-items-center border-bottom gap-2">
        <div class="d-flex align-items-center gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fa fa-user-graduate text-primary me-2"></i>Student Credentials Directory
            </h5>
            <span class="badge bg-primary rounded-pill px-3 py-1 font-monospace" id="studentCountBadge">
                <?php echo count($data['students']); ?> Students
            </span>
        </div>
        
        <!-- Filters -->
        <div class="d-flex flex-wrap gap-2">
            <form action="<?php echo URLROOT; ?>/certificate/hub" method="GET" class="d-flex gap-2">
                <select name="class_id" class="form-select form-select-sm" style="border-radius: 8px; font-weight: 600;" onchange="this.form.submit()">
                    <option value="">-- All Classes --</option>
                    <?php foreach($data['classes'] as $cls): ?>
                        <option value="<?php echo $cls->id; ?>" <?php echo ($data['selected_class'] == $cls->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cls->class_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <input type="text" id="certSearchInput" class="form-control form-control-sm" placeholder="Live search by student name, roll #..." style="border-radius: 8px; width: 220px;">
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="certStudentsTable">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th class="ps-4 py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 90px;">Adm No</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Student Name</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Class &amp; Section</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Fee Status</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Father Name</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">B-Form / CNIC</th>
                        <th class="pe-4 py-3 text-end" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 340px;">Issue Documents</th>
                    </tr>
                </thead>
                <tbody id="certStudentsBody">
                    <?php if(!empty($data['students'])): ?>
                        <?php foreach($data['students'] as $s): 
                            $initials = strtoupper(substr($s->name, 0, 2));
                            $searchStr = strtolower($s->name . ' ' . ($s->admission_no ?? '') . ' ' . ($s->roll_no ?? '') . ' ' . ($s->class_name ?? '') . ' ' . ($s->father_name ?? ''));
                        ?>
                        <tr class="student-cert-row" data-search="<?php echo htmlspecialchars($searchStr); ?>">
                            <td class="ps-4 font-monospace fw-bold text-muted small">
                                #<?php echo htmlspecialchars($s->admission_no ?: '-'); ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar" style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-gradient); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                                        <?php echo $initials; ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">
                                            <?php echo htmlspecialchars($s->name); ?>
                                        </div>
                                        <?php if(!empty($s->roll_no)): ?>
                                            <div class="text-muted" style="font-size: 0.72rem;">Roll No: <?php echo htmlspecialchars($s->roll_no); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.78rem;">
                                    <?php echo htmlspecialchars($s->class_name ?? 'General'); ?>
                                    <?php if(!empty($s->section_name)): ?>
                                        <span class="text-primary fw-bold">&bull; <?php echo htmlspecialchars($s->section_name); ?></span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <?php if(!empty($s->is_fee_cleared)): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.75rem;">
                                        <i class="fa fa-check-circle me-1"></i>Cleared
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 0.75rem;" title="Dues Pending: Rs. <?php echo number_format($s->fee_balance); ?>">
                                        <i class="fa fa-lock me-1"></i>Dues: Rs. <?php echo number_format($s->fee_balance); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark"><?php echo htmlspecialchars($s->father_name ?: 'N/A'); ?></div>
                            </td>
                            <td class="font-monospace small text-muted">
                                <?php echo htmlspecialchars($s->bform_cnic ?: 'Verified'); ?>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- SLC -->
                                    <a href="<?php echo URLROOT; ?>/certificate/slc/<?php echo $s->id; ?>" target="_blank" class="btn btn-sm <?php echo $s->is_fee_cleared ? 'btn-outline-primary' : 'btn-outline-secondary'; ?> px-2 py-1" title="<?php echo $s->is_fee_cleared ? 'School Leaving Certificate' : 'Dues Pending - Click for Fee Lock Details'; ?>" style="border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fa <?php echo $s->is_fee_cleared ? 'fa-graduation-cap' : 'fa-lock text-danger'; ?> me-1"></i>SLC
                                    </a>

                                    <!-- Character -->
                                    <a href="<?php echo URLROOT; ?>/certificate/character/<?php echo $s->id; ?>" target="_blank" class="btn btn-sm <?php echo $s->is_fee_cleared ? 'btn-outline-warning text-dark' : 'btn-outline-secondary'; ?> px-2 py-1" title="<?php echo $s->is_fee_cleared ? 'Character Certificate' : 'Dues Pending - Click for Fee Lock Details'; ?>" style="border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fa <?php echo $s->is_fee_cleared ? 'fa-certificate' : 'fa-lock text-danger'; ?> me-1"></i>Character
                                    </a>

                                    <!-- Bonafide -->
                                    <a href="<?php echo URLROOT; ?>/certificate/bonafide/<?php echo $s->id; ?>" target="_blank" class="btn btn-sm <?php echo $s->is_fee_cleared ? 'btn-outline-success' : 'btn-outline-secondary'; ?> px-2 py-1" title="<?php echo $s->is_fee_cleared ? 'Bonafide Certificate' : 'Dues Pending - Click for Fee Lock Details'; ?>" style="border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fa <?php echo $s->is_fee_cleared ? 'fa-user-check' : 'fa-lock text-danger'; ?> me-1"></i>Bonafide
                                    </a>

                                    <!-- Admit Card -->
                                    <a href="<?php echo URLROOT; ?>/certificate/admitCard/<?php echo $s->id; ?>" target="_blank" class="btn btn-sm <?php echo $s->is_fee_cleared ? 'btn-outline-danger' : 'btn-outline-secondary'; ?> px-2 py-1" title="<?php echo $s->is_fee_cleared ? 'Admit Card / Roll No Slip' : 'Dues Pending - Click for Fee Lock Details'; ?>" style="border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fa <?php echo $s->is_fee_cleared ? 'fa-id-card' : 'fa-lock text-danger'; ?> me-1"></i>Admit Slip
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-4 text-center text-muted">No students found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- AUDIT LOG OF ISSUED CERTIFICATES -->
<?php if(!empty($data['issued_log'])): ?>
<div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="fa fa-history text-muted me-2"></i>Recently Issued Credentials &amp; Certifications Ledger
        </h6>
        <span class="badge bg-light text-dark border font-monospace"><?php echo count($data['issued_log']); ?> Records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Cert #</th>
                        <th>Type</th>
                        <th>Student Name</th>
                        <th>Class &amp; Section</th>
                        <th>Issue Date</th>
                        <th>Status / Remarks</th>
                        <th class="pe-4 text-end">Reprint</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($data['issued_log'] as $log): 
                        $route = 'slc';
                        if($log->certificate_type === 'Character') $route = 'character';
                        elseif($log->certificate_type === 'Bonafide') $route = 'bonafide';
                    ?>
                    <tr>
                        <td class="ps-4 font-monospace fw-bold text-danger"><?php echo htmlspecialchars($log->certificate_no); ?></td>
                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($log->certificate_type); ?></span></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($log->student_name); ?></td>
                        <td><?php echo htmlspecialchars($log->class_name . ' - ' . ($log->section_name ?: 'General')); ?></td>
                        <td class="font-monospace text-muted"><?php echo date('d M, Y', strtotime($log->issue_date)); ?></td>
                        <td class="text-muted"><?php echo htmlspecialchars($log->remarks ?: ($log->conduct ?? 'Verified')); ?></td>
                        <td class="pe-4 text-end">
                            <a href="<?php echo URLROOT; ?>/certificate/<?php echo $route; ?>/<?php echo $log->student_id; ?>" target="_blank" class="btn btn-sm btn-outline-secondary px-2 py-1">
                                <i class="fa fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('certSearchInput');
    const rows = document.querySelectorAll('.student-cert-row');
    const badge = document.getElementById('studentCountBadge');

    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        let visible = 0;

        rows.forEach(r => {
            const str = r.getAttribute('data-search');
            if(!q || str.includes(q)) {
                r.style.display = '';
                visible++;
            } else {
                r.style.display = 'none';
            }
        });

        badge.textContent = visible + ' Students';
    });
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
