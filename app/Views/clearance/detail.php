<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$c = $data['clearance'];
$auto = $data['auto_check'] ?? ['dues_balance' => 0, 'is_fee_clear' => true, 'borrowed_books' => 0, 'is_library_clear' => true];
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/clearance/index" class="text-decoration-none text-muted">Clearance Hub</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page"><?php echo htmlspecialchars($c->clearance_no); ?></li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h3 class="mb-0 fw-bold"><?php echo htmlspecialchars($c->student_name); ?></h3>
            <span class="badge font-monospace bg-light text-dark border"><?php echo htmlspecialchars($c->clearance_no); ?></span>
            <?php if($c->overall_status == 'Fully Cleared'): ?>
                <span class="badge bg-success text-white px-3 py-1 fw-bold"><i class="fa fa-check-double me-1"></i>Fully Cleared</span>
            <?php else: ?>
                <span class="badge bg-warning text-dark px-3 py-1 fw-bold"><i class="fa fa-hourglass-half me-1"></i>In Progress</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/clearance/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Clearance Hub
        </a>
        <a href="<?php echo URLROOT; ?>/clearance/certificate/<?php echo $c->id; ?>" target="_blank" class="btn btn-outline-primary btn-sm px-3">
            <i class="fa fa-print me-1"></i> Print Clearance NOC
        </a>
        <?php if($c->overall_status == 'Fully Cleared'): ?>
            <a href="<?php echo URLROOT; ?>/clearance/package/<?php echo $c->id; ?>" class="btn btn-success btn-sm px-3 shadow-sm fw-bold">
                <i class="fa fa-box-open me-1"></i> Open Leaving Package
            </a>
            <?php if($c->student_current_status != 'Left' && $c->student_current_status != 'Alumni'): ?>
                <a href="<?php echo URLROOT; ?>/clearance/release/<?php echo $c->id; ?>" class="btn btn-danger btn-sm px-3 shadow-sm" onclick="return confirm('Officially release student and mark status as Left/Alumni?');">
                    <i class="fa fa-user-minus me-1"></i> Principal Release &amp; Mark Left
                </a>
            <?php else: ?>
                <span class="badge bg-secondary px-3 py-2">Student Released (Status: Left)</span>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div><strong>Success!</strong> Clearance action has been recorded and overall eligibility evaluated.</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- STUDENT DOSSIER & AUTOMATED AUDIT CARD -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-circle-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 65px; height: 65px;">
                        <?php echo strtoupper(substr($c->student_name, 0, 1)); ?>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($c->student_name); ?></h4>
                        <div class="text-muted small">
                            <span>Father: <strong><?php echo htmlspecialchars($c->father_name ?: 'N/A'); ?></strong></span> &bull;
                            <span>Admission No: <strong class="font-monospace"><?php echo htmlspecialchars($c->admission_no); ?></strong></span> &bull;
                            <span>Roll No: <strong><?php echo htmlspecialchars($c->roll_no ?: 'None'); ?></strong></span>
                        </div>
                        <div class="text-muted small mt-1">
                            <span>Class: <strong><?php echo htmlspecialchars($c->class_name ?? ''); ?> (<?php echo htmlspecialchars($c->section_name ?? 'A'); ?>)</strong></span> &bull;
                            <span>B-Form: <strong class="font-monospace"><?php echo htmlspecialchars($c->bform_cnic ?: 'N/A'); ?></strong></span> &bull;
                            <span>Reason: <span class="badge bg-secondary-subtle text-secondary"><?php echo htmlspecialchars($c->reason_for_leaving); ?></span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Automated Real-Time Audits -->
            <div class="col-md-5">
                <div class="p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold text-secondary mb-2 small text-uppercase">
                        <i class="fa fa-robot text-primary me-1"></i> Automated Records Audit
                    </h6>
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom small">
                        <span>Accounts &amp; Fee Ledger:</span>
                        <?php if($auto['is_fee_clear']): ?>
                            <span class="badge bg-success-subtle text-success fw-bold"><i class="fa fa-check me-1"></i>Zero Dues (Clear)</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger fw-bold">
                                <i class="fa fa-exclamation-triangle me-1"></i>Dues Pending: PKR <?php echo number_format($auto['dues_balance']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 small">
                        <span>Library Book Issues:</span>
                        <?php if($auto['is_library_clear']): ?>
                            <span class="badge bg-success-subtle text-success fw-bold"><i class="fa fa-check me-1"></i>No Books Pending</span>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-dark fw-bold">
                                <i class="fa fa-book me-1"></i><?php echo $auto['borrowed_books']; ?> Books Issued
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 5-DEPARTMENT CLEARANCE SIGN-OFF TILES -->
<h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
    <i class="fa fa-tasks text-primary"></i> 5-Department Institutional Clearance Matrix
</h5>

<div class="row g-4 mb-4">

    <!-- 1. ACCOUNTS & FINANCE -->
    <div class="col-lg-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary-subtle text-primary rounded-2">
                        <i class="fa fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">1. Accounts &amp; Fees</h6>
                        <small class="text-muted">Fee dues &amp; security deposit</small>
                    </div>
                </div>
                <span class="badge <?php echo ($c->accounts_status == 'Cleared' || $c->accounts_status == 'Waived') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                    <?php echo htmlspecialchars($c->accounts_status); ?>
                </span>
            </div>
            <div class="card-body p-3">
                <form action="<?php echo URLROOT; ?>/clearance/signoff/<?php echo $c->id; ?>" method="post">
                    <input type="hidden" name="department" value="accounts">
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Clearance Decision</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="Cleared" <?php echo ($c->accounts_status == 'Cleared') ? 'selected' : ''; ?>>Cleared (All Dues Paid)</option>
                            <option value="Waived" <?php echo ($c->accounts_status == 'Waived') ? 'selected' : ''; ?>>Waived (Fee Concession Approved)</option>
                            <option value="Pending" <?php echo ($c->accounts_status == 'Pending') ? 'selected' : ''; ?>>Pending (Arrears Remaining)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Officer Remarks</label>
                        <input type="text" name="remarks" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c->accounts_remarks ?: ($auto['is_fee_clear'] ? 'All dues cleared. Zero balance.' : 'Pending fee balance.')); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Cleared By (Officer)</label>
                        <input type="text" name="cleared_by" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c->accounts_cleared_by ?: ($_SESSION['user_name'] ?? 'Accounts Officer')); ?>">
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100 fw-bold">
                        <i class="fa fa-save me-1"></i> Update Accounts Sign-Off
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. LIBRARY -->
    <div class="col-lg-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-info-subtle text-info rounded-2">
                        <i class="fa fa-book"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">2. Library</h6>
                        <small class="text-muted">Books returned &amp; fine settled</small>
                    </div>
                </div>
                <span class="badge <?php echo ($c->library_status == 'Cleared' || $c->library_status == 'Waived') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                    <?php echo htmlspecialchars($c->library_status); ?>
                </span>
            </div>
            <div class="card-body p-3">
                <form action="<?php echo URLROOT; ?>/clearance/signoff/<?php echo $c->id; ?>" method="post">
                    <input type="hidden" name="department" value="library">
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Clearance Decision</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="Cleared" <?php echo ($c->library_status == 'Cleared') ? 'selected' : ''; ?>>Cleared (All Books Returned)</option>
                            <option value="Waived" <?php echo ($c->library_status == 'Waived') ? 'selected' : ''; ?>>Waived</option>
                            <option value="Pending" <?php echo ($c->library_status == 'Pending') ? 'selected' : ''; ?>>Pending (Books Outstanding)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Librarian Remarks</label>
                        <input type="text" name="remarks" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c->library_remarks ?: 'No books outstanding. Library card surrendered.'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Cleared By (Librarian)</label>
                        <input type="text" name="cleared_by" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c->library_cleared_by ?: ($_SESSION['user_name'] ?? 'Head Librarian')); ?>">
                    </div>
                    <button type="submit" class="btn btn-outline-info btn-sm w-100 fw-bold">
                        <i class="fa fa-save me-1"></i> Update Library Sign-Off
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. SCIENCE & COMPUTER LABS -->
    <div class="col-lg-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-success-subtle text-success rounded-2">
                        <i class="fa fa-flask"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">3. Science &amp; IT Labs</h6>
                        <small class="text-muted">Apparatus &amp; computer lab</small>
                    </div>
                </div>
                <span class="badge <?php echo ($c->lab_status == 'Cleared' || $c->lab_status == 'Waived') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                    <?php echo htmlspecialchars($c->lab_status); ?>
                </span>
            </div>
            <div class="card-body p-3">
                <form action="<?php echo URLROOT; ?>/clearance/signoff/<?php echo $c->id; ?>" method="post">
                    <input type="hidden" name="department" value="lab">
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Clearance Decision</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="Cleared" <?php echo ($c->lab_status == 'Cleared') ? 'selected' : ''; ?>>Cleared (No Breakage / Dues)</option>
                            <option value="Waived" <?php echo ($c->lab_status == 'Waived') ? 'selected' : ''; ?>>Waived</option>
                            <option value="Pending" <?php echo ($c->lab_status == 'Pending') ? 'selected' : ''; ?>>Pending (Breakage Fine Unpaid)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Lab In-Charge Remarks</label>
                        <input type="text" name="remarks" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c->lab_remarks ?: 'No apparatus damage or loss. IT lab user cleared.'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Cleared By (Lab In-Charge)</label>
                        <input type="text" name="cleared_by" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c->lab_cleared_by ?: ($_SESSION['user_name'] ?? 'Science Lab In-Charge')); ?>">
                    </div>
                    <button type="submit" class="btn btn-outline-success btn-sm w-100 fw-bold">
                        <i class="fa fa-save me-1"></i> Update Lab Sign-Off
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 4. SPORTS & CO-CURRICULAR -->
    <div class="col-lg-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-danger-subtle text-danger rounded-2">
                        <i class="fa fa-futbol"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">4. Sports Department</h6>
                        <small class="text-muted">Kits, jerseys &amp; gym gear</small>
                    </div>
                </div>
                <span class="badge <?php echo ($c->sports_status == 'Cleared' || $c->sports_status == 'Waived') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                    <?php echo htmlspecialchars($c->sports_status); ?>
                </span>
            </div>
            <div class="card-body p-3">
                <form action="<?php echo URLROOT; ?>/clearance/signoff/<?php echo $c->id; ?>" method="post">
                    <input type="hidden" name="department" value="sports">
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Clearance Decision</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="Cleared" <?php echo ($c->sports_status == 'Cleared') ? 'selected' : ''; ?>>Cleared (All Kits Returned)</option>
                            <option value="Waived" <?php echo ($c->sports_status == 'Waived') ? 'selected' : ''; ?>>Waived</option>
                            <option value="Pending" <?php echo ($c->sports_status == 'Pending') ? 'selected' : ''; ?>>Pending (Sports Gear Missing)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Sports Officer Remarks</label>
                        <input type="text" name="remarks" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c->sports_remarks ?: 'All school sports kits and equipment accounted for.'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Cleared By (Sports Director)</label>
                        <input type="text" name="cleared_by" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c->sports_cleared_by ?: ($_SESSION['user_name'] ?? 'Sports Instructor')); ?>">
                    </div>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 fw-bold">
                        <i class="fa fa-save me-1"></i> Update Sports Sign-Off
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 5. CLASS TEACHER & ID BADGE -->
    <div class="col-lg-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-warning-subtle text-dark rounded-2">
                        <i class="fa fa-user-check"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">5. Class Teacher</h6>
                        <small class="text-muted">Student ID Card &amp; conduct</small>
                    </div>
                </div>
                <span class="badge <?php echo ($c->class_teacher_status == 'Cleared' || $c->class_teacher_status == 'Waived') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                    <?php echo htmlspecialchars($c->class_teacher_status); ?>
                </span>
            </div>
            <div class="card-body p-3">
                <form action="<?php echo URLROOT; ?>/clearance/signoff/<?php echo $c->id; ?>" method="post">
                    <input type="hidden" name="department" value="class_teacher">
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Clearance Decision</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="Cleared" <?php echo ($c->class_teacher_status == 'Cleared') ? 'selected' : ''; ?>>Cleared (ID Card Returned)</option>
                            <option value="Waived" <?php echo ($c->class_teacher_status == 'Waived') ? 'selected' : ''; ?>>Waived</option>
                            <option value="Pending" <?php echo ($c->class_teacher_status == 'Pending') ? 'selected' : ''; ?>>Pending (ID Card / Dues Due)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">Class Teacher Remarks</label>
                        <input type="text" name="remarks" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c->class_teacher_remarks ?: 'Student ID Card returned. Satisfactory classroom conduct.'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Cleared By (Class Teacher)</label>
                        <input type="text" name="cleared_by" class="form-control form-control-sm" value="<?php echo htmlspecialchars($c->class_teacher_cleared_by ?: ($_SESSION['user_name'] ?? 'Class In-Charge')); ?>">
                    </div>
                    <button type="submit" class="btn btn-outline-warning text-dark btn-sm w-100 fw-bold">
                        <i class="fa fa-save me-1"></i> Update Teacher Sign-Off
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- SUMMARY / STATUS TILE -->
    <div class="col-lg-6 col-xl-4">
        <div class="card h-100 border shadow-sm rounded-3 bg-light">
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-2">Overall Clearance Status</h6>
                    <p class="small text-muted mb-3">When all 5 departments mark Cleared or Waived, the clearance is marked Fully Cleared and the student Leaving Package is unlocked.</p>

                    <?php if($c->overall_status == 'Fully Cleared'): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2 py-2 mb-3">
                            <i class="fa fa-check-circle fa-2x"></i>
                            <div>
                                <strong class="d-block">5/5 Departments Cleared!</strong>
                                <span class="fs-xs">Completion Date: <?php echo date('d M, Y', strtotime($c->completion_date ?: date('Y-m-d'))); ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3 text-dark">
                            <i class="fa fa-hourglass-half fa-2x text-warning"></i>
                            <div>
                                <strong class="d-block">Awaiting Department Clearance</strong>
                                <span class="fs-xs">Please ensure all 5 sections are signed off.</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2">
                    <a href="<?php echo URLROOT; ?>/clearance/certificate/<?php echo $c->id; ?>" target="_blank" class="btn btn-outline-dark btn-sm">
                        <i class="fa fa-print me-1"></i> Print Clearance Certificate (NOC)
                    </a>
                    <?php if($c->overall_status == 'Fully Cleared'): ?>
                        <a href="<?php echo URLROOT; ?>/clearance/package/<?php echo $c->id; ?>" class="btn btn-success btn-sm fw-bold">
                            <i class="fa fa-box-open me-1"></i> Open Complete Leaving Package
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
