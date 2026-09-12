<?php require APPROOT . '/Views/layouts/header.php'; ?>
<!-- Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
            <i class="fa fa-arrow-left me-1"></i> Back to Fee Register
        </a>
        <div>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-tags text-primary me-2"></i>Fee Types Management
            </h2>
            <div class="text-muted small">Configure tuition, admission, laboratory, sports, and recurring fee heads.</div>
        </div>
    </div>
</div>

<!-- Alert Notifications -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px;" role="alert">
        <i class="fa fa-check-circle me-2"></i>
        <?php 
            if($_GET['success'] == 'updated') echo "Fee type updated successfully!";
            elseif($_GET['success'] == 'deleted') echo "Fee type removed successfully!";
            else echo "Fee type created successfully!";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Add / Edit Type Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa <?php echo !empty($data['type']) ? 'fa-edit text-warning' : 'fa-plus-circle text-primary'; ?> me-2"></i>
                    <?php echo !empty($data['type']) ? 'Edit Fee Type' : 'Add Fee Type'; ?>
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo URLROOT; ?>/fees/<?php echo !empty($data['type']) ? 'editType/' . $data['type']->id : 'types'; ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
                    <?php if(!empty($data['type'])): ?>
                        <input type="hidden" name="id" value="<?php echo $data['type']->id; ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Type Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Monthly Tuition Fee" value="<?php echo htmlspecialchars($data['type']->type_name ?? '', ENT_QUOTES, 'UTF-8'); ?>" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Fee Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. TUIT-01" value="<?php echo htmlspecialchars($data['type']->type_code ?? '', ENT_QUOTES, 'UTF-8'); ?>" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Details about this fee type..." style="border-radius: 8px;"><?php echo htmlspecialchars($data['type']->description ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill fw-bold shadow-sm" style="border-radius: 8px;">
                            <i class="fa fa-save me-1"></i> <?php echo !empty($data['type']) ? 'Update Type' : 'Save Fee Type'; ?>
                        </button>
                        <?php if(!empty($data['type'])): ?>
                            <a href="<?php echo URLROOT; ?>/fees/types" class="btn btn-outline-secondary" style="border-radius: 8px;">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Types List Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="fw-bold mb-0 text-dark">Existing Fee Types</h6>
                <span class="badge bg-light text-dark border font-monospace"><?php echo count($data['types']); ?> Types</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Type Name</th>
                                <th class="py-3">Code</th>
                                <th class="py-3">Description</th>
                                <th class="pe-4 py-3 text-end" style="min-width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['types'])): ?>
                                <?php foreach($data['types'] as $type): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($type->type_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($type->type_code, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                        <td class="text-muted small"><?php echo htmlspecialchars($type->description ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="pe-4 text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?php echo URLROOT; ?>/fees/editType/<?php echo $type->id; ?>" class="btn btn-outline-secondary py-1 px-2" title="Edit Type">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/fees/deleteType/<?php echo $type->id; ?>" class="btn btn-outline-danger py-1 px-2" title="Delete Type" onclick="return confirm('Are you sure you want to delete fee type <?php echo htmlspecialchars($type->type_name, ENT_QUOTES, 'UTF-8'); ?>?');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="fa fa-tags fs-2 mb-2 d-block text-black-50"></i>
                                        No fee types found. Create your first fee type using the form on the left.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
