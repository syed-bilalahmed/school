<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Top Header Navigation -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
            <i class="fa fa-arrow-left me-1"></i> Back to Fee Register
        </a>
        <div>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-user-plus text-primary me-2"></i>Assign Fee Structure to Class
            </h2>
            <div class="text-muted small">Apply fee master bundles and billing heads to students of a selected class.</div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/fees/groups" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
            <i class="fa fa-folder me-1"></i> Manage Fee Groups
        </a>
        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" style="border-radius: 8px;">
            <i class="fa fa-cash-register me-1"></i> Fee Collection Hub
        </a>
    </div>
</div>

<!-- Alert Feedback -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px;" role="alert">
        <i class="fa fa-check-circle me-2"></i>
        Fee package successfully assigned to class!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
        <div class="card border-0 shadow-sm" style="border-radius: 18px; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-layer-group text-primary me-2"></i>Class Fee Assignment Form
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo URLROOT; ?>/fees/assign" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
                    <input type="hidden" name="redirect_to" value="/fees/collect?success=assigned">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Fee Package / Group <span class="text-danger">*</span></label>
                        <select name="fee_group_id" id="assign_fee_group_id" class="form-select" required style="border-radius: 10px; padding: 10px 14px; font-weight: 600;">
                            <option value="">-- Choose Fee Group --</option>
                            <?php foreach($data['groups'] as $group): ?>
                                <option value="<?php echo $group->id; ?>">
                                    <?php echo htmlspecialchars($group->group_name, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Target Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="assign_class_id" class="form-select" required onchange="filterAssignSections(this.value)" style="border-radius: 10px; padding: 10px 14px; font-weight: 600;">
                            <option value="">-- Choose Class --</option>
                            <?php foreach($data['classes'] as $class): ?>
                                <option value="<?php echo $class->id; ?>">
                                    <?php echo htmlspecialchars($class->class_name, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Section (Optional)</label>
                        <select name="section_id" id="assign_section_id" class="form-select" style="border-radius: 10px; padding: 10px 14px;">
                            <option value="">-- All Sections in Selected Class --</option>
                            <?php if(!empty($data['sections'])): ?>
                                <?php foreach($data['sections'] as $sec): ?>
                                    <option value="<?php echo $sec->id; ?>" data-class-id="<?php echo $sec->class_id; ?>">
                                        <?php echo htmlspecialchars($sec->class_name . ' - ' . $sec->section_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-4 text-muted small">
                        <i class="fa fa-info-circle text-primary me-1"></i>
                        Is action se is Fee Group ke tamam heads (Tuition, Exam, Lab, etc.) is class ke sabhi active students ke record me add ho jayenge aur register me foran reflect honge.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-bold shadow-sm py-2" style="border-radius: 10px;">
                            <i class="fa fa-check-circle me-1"></i> Assign Fees Now
                        </button>
                        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-outline-secondary px-4" style="border-radius: 10px;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function filterAssignSections(classId) {
    const secSelect = document.getElementById('assign_section_id');
    if (!secSelect) return;
    const options = secSelect.querySelectorAll('option');
    options.forEach((opt, idx) => {
        if (idx === 0) return;
        const optClassId = opt.getAttribute('data-class-id');
        if (!classId || optClassId === classId) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });
    secSelect.value = '';
}
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
