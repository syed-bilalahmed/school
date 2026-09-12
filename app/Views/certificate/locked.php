<?php require APPROOT . '/Views/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-lg border-0 rounded-4 text-center overflow-hidden">
                <div class="bg-danger text-white py-4 px-3">
                    <div class="avatar-lg rounded-circle bg-white text-danger shadow-sm p-3 d-inline-block mb-2">
                        <i class="fa fa-lock fa-3x"></i>
                    </div>
                    <h3 class="fw-bold mb-1">Access Locked: Outstanding Dues</h3>
                    <p class="mb-0 text-white-50 small">Institutional Policy Enforcement &bull; Dues Clearance Required</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="alert alert-danger-subtle border border-danger-subtle rounded-3 py-3 mb-4">
                        <div class="text-danger small fw-bold text-uppercase mb-1">Total Outstanding Dues Balance</div>
                        <div class="display-6 fw-bold text-danger">Rs. <?php echo number_format($data['balance'] ?? 0); ?></div>
                    </div>

                    <?php if(!empty($data['student'])): ?>
                        <div class="p-3 border rounded-3 bg-light mb-4 text-start small">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Student Name:</span>
                                <span class="fw-bold text-dark"><?php echo htmlspecialchars($data['student']->name); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Admission No:</span>
                                <span class="fw-bold text-dark font-monospace"><?php echo htmlspecialchars($data['student']->admission_no); ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Class &amp; Section:</span>
                                <span class="fw-bold text-primary"><?php echo htmlspecialchars($data['student']->class_name ?? 'N/A'); ?> (Sec <?php echo htmlspecialchars($data['student']->section_name ?? 'N/A'); ?>)</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted small mb-4">
                        Under institutional policy, School Leaving Certificates (SLC), Character Credentials, Bonafide Proofs, and Roll Number Slips cannot be issued until all outstanding tuition and fee dues are cleared.
                    </p>

                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-success btn-lg fw-bold shadow-sm">
                            <i class="fa fa-cash-register me-2"></i> Collect Fees &amp; Clear Dues
                        </a>
                        <a href="<?php echo $data['target_url'] . (strpos($data['target_url'], '?') !== false ? '&' : '?') . 'override=1'; ?>" class="btn btn-outline-danger fw-bold">
                            <i class="fa fa-key me-2"></i> Authorize Admin Dues Override
                        </a>
                        <a href="<?php echo URLROOT; ?>/certificate/hub" class="btn btn-light border text-secondary fw-semibold">
                            <i class="fa fa-arrow-left me-2"></i> Return to Certificate Hub
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
