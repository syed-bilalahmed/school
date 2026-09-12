<?php require APPROOT . '/Views/layouts/header.php'; ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Schools</h1>
        <p class="text-secondary opacity-75 mb-0">Manage tenant onboarding and school status.</p>
    </div>
</div>

<?php if(isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-exclamation-circle fs-5"></i>
        <div><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header p-4">
                <h5 class="mb-0 fw-bold">Add New School</h5>
            </div>
            <div class="card-body p-4 pt-3">
                <form action="<?php echo URLROOT; ?>/admin/addSchool" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                    <div class="mb-3">
                        <label class="form-label">School Name</label>
                        <input type="text" name="name" class="form-control <?php echo !empty($data['errors']['name']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['form']['name'] ?? ''); ?>" required>
                        <?php if(!empty($data['errors']['name'])): ?>
                            <div class="invalid-feedback"><?php echo $data['errors']['name']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">School Code</label>
                        <input type="text" name="code" class="form-control <?php echo !empty($data['errors']['code']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['form']['code'] ?? ''); ?>" placeholder="example: greenfield" required>
                        <div class="form-text">Used in tenant URL path: /s/{school_code}/...</div>
                        <?php if(!empty($data['errors']['code'])): ?>
                            <div class="invalid-feedback"><?php echo $data['errors']['code']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Domain (Optional)</label>
                        <input type="text" name="domain" class="form-control <?php echo !empty($data['errors']['domain']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['form']['domain'] ?? ''); ?>" placeholder="school.example.com">
                        <?php if(!empty($data['errors']['domain'])): ?>
                            <div class="invalid-feedback"><?php echo $data['errors']['domain']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <?php $status = $data['form']['status'] ?? 'active'; ?>
                            <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="suspended" <?php echo $status === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Create School</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header p-4 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold">All Schools</h5>
                <span class="badge bg-primary"><?php echo count($data['schools'] ?? []); ?> total</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">School</th>
                                <th>Code</th>
                                <th>Domain</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($data['schools'])): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No schools found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($data['schools'] as $school): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold"><?php echo htmlspecialchars($school->name); ?></div>
                                            <small class="text-muted">Created: <?php echo htmlspecialchars($school->created_at); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($school->code); ?></td>
                                        <td><?php echo htmlspecialchars($school->domain ?: '-'); ?></td>
                                        <td>
                                            <?php
                                                $badge = 'secondary';
                                                if ($school->status === 'active') $badge = 'success';
                                                if ($school->status === 'pending') $badge = 'warning';
                                                if ($school->status === 'suspended') $badge = 'danger';
                                            ?>
                                            <span class="badge bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($school->status); ?></span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <div class="btn-group btn-group-sm" role="group" aria-label="Status actions">
                                                    <a class="btn btn-outline-success <?php echo $school->status === 'active' ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/admin/schoolStatus/<?php echo (int)$school->id; ?>/active" title="Set Active">Active</a>
                                                    <a class="btn btn-outline-warning <?php echo $school->status === 'pending' ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/admin/schoolStatus/<?php echo (int)$school->id; ?>/pending" title="Set Pending">Pending</a>
                                                    <a class="btn btn-outline-danger <?php echo $school->status === 'suspended' ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/admin/schoolStatus/<?php echo (int)$school->id; ?>/suspended" title="Set Suspended">Suspend</a>
                                                </div>
                                                <?php if((int)$school->id > 1): ?>
                                                    <form action="<?php echo URLROOT; ?>/admin/deleteSchool/<?php echo (int)$school->id; ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete \'<?php echo htmlspecialchars(addslashes($school->name)); ?>\'? All tenant data will be removed. This action cannot be undone.');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete School">
                                                            <i class="fa fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border px-2 py-1" title="Primary System School (Protected)">
                                                        <i class="fa fa-shield-alt text-primary"></i> Main
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
