<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Compact Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h5 fw-bold mb-0 text-dark">
            <i class="fa fa-calendar-alt text-primary me-2"></i>Academic Session Management
        </h1>
        <small class="text-muted" style="font-size: 0.8rem;">Configure school academic years (e.g. 2026-27), active terms, and system session context.</small>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo URLROOT; ?>/classes/index" class="btn btn-outline-secondary btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-chalkboard me-1"></i> Classes List
        </a>
        <a href="<?php echo URLROOT; ?>/sections/index" class="btn btn-outline-primary btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-layer-group me-1"></i> Sections
        </a>
        <a href="<?php echo URLROOT; ?>/subjects/index" class="btn btn-outline-info btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-book-open me-1"></i> Subjects
        </a>
    </div>
</div>

<!-- Alert Notifications -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2 px-3 mb-3" style="border-radius: 8px;" role="alert">
        <div class="d-flex align-items-center small">
            <i class="fa fa-check-circle fs-6 text-success me-2"></i>
            <div>
                <?php 
                    if($_GET['success'] == 'created') echo "New academic session created successfully!";
                    elseif($_GET['success'] == 'updated') echo "Academic session updated successfully!";
                    elseif($_GET['success'] == 'current_set') echo "Active academic session switched successfully!";
                    elseif($_GET['success'] == 'deleted') echo "Academic session removed successfully!";
                    else echo "Action completed successfully!";
                ?>
            </div>
        </div>
        <button type="button" class="btn-close small py-2 px-3" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm py-2 px-3 mb-3" style="border-radius: 8px;" role="alert">
        <div class="d-flex align-items-center small">
            <i class="fa fa-exclamation-triangle fs-6 text-danger me-2"></i>
            <div>
                <?php 
                    if($_GET['error'] == 'cannot_delete_current') echo "Cannot delete the currently active academic session. Please make another session active first.";
                    else echo "An error occurred while processing request.";
                ?>
            </div>
        </div>
        <button type="button" class="btn-close small py-2 px-3" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
    $totalSessions = count($data['sessions'] ?? []);
    $currentName = !empty($data['current_session']) ? $data['current_session']->session_name : 'None';
    $otherSessions = max(0, $totalSessions - 1);
?>

<!-- Compact Summary Metrics Cards -->
<div class="row g-2 mb-3">
    <!-- Total Sessions -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Sessions</div>
                    <div class="h5 fw-bold mb-0 text-dark"><?php echo $totalSessions; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-calendar-alt fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Current Session -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Current Active</div>
                    <div class="h5 fw-bold mb-0 text-success text-truncate" style="max-width: 140px;"><?php echo htmlspecialchars($currentName); ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-check-circle fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Previous / Other Sessions -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Other Sessions</div>
                    <div class="h5 fw-bold mb-0 text-info"><?php echo $otherSessions; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-history fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Session Status</div>
                    <div class="h5 fw-bold mb-0 text-warning"><?php echo !empty($data['current_session']) ? 'Active' : 'Not Set'; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-toggle-on fs-6"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main 2-Column Work Area -->
<div class="row g-3">
    <!-- Add / Edit Session Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark" id="sessionFormTitle">
                    <i class="fa fa-plus-circle text-primary me-2" id="sessionFormIcon"></i>Add Academic Session
                </h6>
            </div>
            <div class="card-body p-3">
                <form id="sessionForm" action="<?php echo URLROOT; ?>/sessions/add" method="post">
                    <input type="hidden" name="id" id="session_id" value="">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Session Name <span class="text-danger">*</span></label>
                        <input type="text" name="session_name" id="session_name" class="form-control" placeholder="e.g. 2026-27" required style="border-radius: 8px;">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" style="border-radius: 8px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="mb-3 form-check form-switch ps-5">
                        <input class="form-check-input" type="checkbox" name="is_current" id="is_current" value="1">
                        <label class="form-check-label fw-bold text-dark small" for="is_current">Set as Active Default</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" id="sessionSubmitBtn" class="btn btn-primary flex-fill fw-bold shadow-sm py-1.5" style="border-radius: 8px;">
                            <i class="fa fa-save me-1"></i> Save Session
                        </button>
                        <button type="button" id="sessionCancelBtn" class="btn btn-outline-secondary py-1.5 d-none" style="border-radius: 8px;">
                            <i class="fa fa-times me-1"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sessions List Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-table-list text-primary me-2"></i>Academic Sessions List
                </h6>
                <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1 rounded-pill">
                    <?php echo count($data['sessions']); ?> Sessions
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-uppercase text-muted small fw-bold">Session Name</th>
                                <th class="text-uppercase text-muted small fw-bold">Duration</th>
                                <th class="text-uppercase text-muted small fw-bold">Status</th>
                                <th class="text-end pe-3 text-uppercase text-muted small fw-bold" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['sessions'])): ?>
                                <?php foreach($data['sessions'] as $session): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($session->session_name); ?></span>
                                        </td>
                                        <td>
                                            <?php if(!empty($session->start_date) && !empty($session->end_date)): ?>
                                                <span class="badge bg-light text-dark border px-2 py-1 small">
                                                    <?php echo date('d M Y', strtotime($session->start_date)); ?> &mdash; <?php echo date('d M Y', strtotime($session->end_date)); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted small">Not specified</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($session->is_current): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold">
                                                    <i class="fa fa-check-circle me-1"></i> Active
                                                </span>
                                            <?php else: ?>
                                                <a href="<?php echo URLROOT; ?>/sessions/setCurrent/<?php echo $session->id; ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" style="border-radius: 6px; font-size: 0.75rem;">
                                                    Set Active
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group shadow-sm" style="border-radius: 6px; overflow: hidden;">
                                                <button type="button" class="btn btn-sm btn-outline-primary border-0 btn-edit-session"
                                                    data-id="<?php echo $session->id; ?>"
                                                    data-name="<?php echo htmlspecialchars($session->session_name); ?>"
                                                    data-start="<?php echo htmlspecialchars($session->start_date ?? ''); ?>"
                                                    data-end="<?php echo htmlspecialchars($session->end_date ?? ''); ?>"
                                                    data-current="<?php echo $session->is_current; ?>"
                                                    title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <?php if(!$session->is_current): ?>
                                                    <a href="<?php echo URLROOT; ?>/sessions/delete/<?php echo $session->id; ?>" 
                                                       class="btn btn-sm btn-outline-danger border-0"
                                                       onclick="return confirm('Are you sure you want to delete this session?');"
                                                       title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        No academic sessions found.
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

<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('sessionForm');
    const idInput = document.getElementById('session_id');
    const nameInput = document.getElementById('session_name');
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const currentInput = document.getElementById('is_current');
    const title = document.getElementById('sessionFormTitle');
    const submitBtn = document.getElementById('sessionSubmitBtn');
    const cancelBtn = document.getElementById('sessionCancelBtn');

    document.querySelectorAll('.btn-edit-session').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            idInput.value = id;
            nameInput.value = this.dataset.name;
            startInput.value = this.dataset.start;
            endInput.value = this.dataset.end;
            currentInput.checked = (this.dataset.current === '1');

            form.action = '<?php echo URLROOT; ?>/sessions/edit/' + id;
            title.innerHTML = '<i class="fa fa-edit text-warning me-2"></i>Edit Session';
            submitBtn.innerHTML = '<i class="fa fa-save me-1"></i> Update Session';
            submitBtn.className = 'btn btn-warning flex-fill fw-bold shadow-sm py-1.5';
            cancelBtn.classList.remove('d-none');

            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    cancelBtn.addEventListener('click', function(){
        resetForm();
    });

    function resetForm(){
        idInput.value = '';
        form.reset();
        form.action = '<?php echo URLROOT; ?>/sessions/add';
        title.innerHTML = '<i class="fa fa-plus-circle text-primary me-2"></i>Add Academic Session';
        submitBtn.innerHTML = '<i class="fa fa-save me-1"></i> Save Session';
        submitBtn.className = 'btn btn-primary flex-fill fw-bold shadow-sm py-1.5';
        cancelBtn.classList.add('d-none');
    }
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
