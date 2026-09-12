<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Compact Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h5 fw-bold mb-0 text-dark">
            <i class="fa fa-book-open text-primary me-2"></i>Academic Subjects Setup
        </h1>
        <small class="text-muted" style="font-size: 0.8rem;">Configure core &amp; optional curriculum subjects and passing criteria.</small>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo URLROOT; ?>/subjects/assign" class="btn btn-outline-primary btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-user-check me-1"></i> Teacher Allocation
        </a>
        <a href="<?php echo URLROOT; ?>/classes/index" class="btn btn-outline-secondary btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-chalkboard me-1"></i> Classes List
        </a>
        <a href="<?php echo URLROOT; ?>/sections/index" class="btn btn-outline-info btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-layer-group me-1"></i> Sections
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
                    if($_GET['success'] == 'created') echo "Subject created successfully!";
                    elseif($_GET['success'] == 'updated') echo "Subject updated successfully!";
                    elseif($_GET['success'] == 'deleted') echo "Subject deleted successfully!";
                    else echo "Action completed successfully!";
                ?>
            </div>
        </div>
        <button type="button" class="btn-close small py-2 px-3" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
    $totalSubs = count($data['subjects'] ?? []);
    $coreCount = 0;
    $optionalCount = 0;
    $practicalCount = 0;
    foreach ($data['subjects'] ?? [] as $sub) {
        if (isset($sub->is_core) && $sub->is_core == 1) $coreCount++;
        else $optionalCount++;
        if (!empty($sub->type) && strtolower($sub->type) === 'practical') $practicalCount++;
    }
?>

<!-- Compact Summary Metrics Cards -->
<div class="row g-2 mb-3">
    <!-- Total Subjects -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Subjects</div>
                    <div class="h5 fw-bold mb-0 text-dark"><?php echo $totalSubs; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-book-open fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Core / Compulsory -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Core / Compulsory</div>
                    <div class="h5 fw-bold mb-0 text-info"><?php echo $coreCount; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-star fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional / Electives -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Electives</div>
                    <div class="h5 fw-bold mb-0 text-success"><?php echo $optionalCount; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-list-check fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Practical -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Practical / Lab</div>
                    <div class="h5 fw-bold mb-0 text-warning"><?php echo $practicalCount; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-flask fs-6"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main 2-Column Work Area -->
<div class="row g-3">
    <!-- Add / Edit Subject Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark" id="subjectFormTitle">
                    <i class="fa fa-plus-circle text-primary me-2" id="subjectFormIcon"></i>Add New Subject
                </h6>
            </div>
            <div class="card-body p-3">
                <form id="subjectForm" action="<?php echo URLROOT; ?>/subjects/add" method="post">
                    <input type="hidden" name="id" id="subject_id" value="">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Subject Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="subject_name" class="form-control" placeholder="e.g. Mathematics, English" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Subject Code</label>
                        <input type="text" name="code" id="subject_code" class="form-control" placeholder="e.g. MTH-101, ENG-7" style="border-radius: 8px;">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Category</label>
                            <select name="is_core" id="subject_is_core" class="form-select" style="border-radius: 8px;">
                                <option value="1">Core / Compulsory</option>
                                <option value="0">Optional / Elective</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Type</label>
                            <select name="type" id="subject_type" class="form-select" style="border-radius: 8px;">
                                <option value="Theory">Theory</option>
                                <option value="Practical">Practical</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Full Marks</label>
                            <input type="number" step="1" name="full_marks" id="subject_full_marks" class="form-control" value="100" style="border-radius: 8px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Passing Marks</label>
                            <input type="number" step="1" name="passing_marks" id="subject_passing_marks" class="form-control" value="33" style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" id="subjectSubmitBtn" class="btn btn-primary flex-fill fw-bold shadow-sm py-1.5" style="border-radius: 8px;">
                            <i class="fa fa-save me-1"></i> Save Subject
                        </button>
                        <button type="button" id="subjectCancelBtn" class="btn btn-outline-secondary py-1.5 d-none" style="border-radius: 8px;">
                            <i class="fa fa-times me-1"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Subjects List Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-table-list text-primary me-2"></i>Curriculum Subjects List
                </h6>
                <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1 rounded-pill">
                    <?php echo count($data['subjects']); ?> Subjects
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-uppercase text-muted small fw-bold">Subject</th>
                                <th class="text-uppercase text-muted small fw-bold">Category</th>
                                <th class="text-uppercase text-muted small fw-bold">Type</th>
                                <th class="text-uppercase text-muted small fw-bold">Marks</th>
                                <th class="text-end pe-3 text-uppercase text-muted small fw-bold" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['subjects'])): ?>
                                <?php foreach($data['subjects'] as $sub): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($sub->subject_name); ?></div>
                                            <?php if(!empty($sub->subject_code)): ?>
                                                <small class="badge bg-light text-secondary border"><?php echo htmlspecialchars($sub->subject_code); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isset($sub->is_core) && $sub->is_core == 1): ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Core</span>
                                            <?php else: ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Optional</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1"><?php echo htmlspecialchars($sub->type); ?></span>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <span><strong>Full:</strong> <?php echo (int)($sub->full_marks ?? 100); ?></span> | 
                                                <span class="text-danger"><strong>Pass:</strong> <?php echo (int)($sub->passing_marks ?? 33); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group shadow-sm" style="border-radius: 6px; overflow: hidden;">
                                                <button type="button" class="btn btn-sm btn-outline-primary border-0 btn-edit-subject"
                                                    data-id="<?php echo $sub->id; ?>"
                                                    data-name="<?php echo htmlspecialchars($sub->subject_name); ?>"
                                                    data-code="<?php echo htmlspecialchars($sub->subject_code ?? ''); ?>"
                                                    data-type="<?php echo htmlspecialchars($sub->type); ?>"
                                                    data-core="<?php echo isset($sub->is_core) ? $sub->is_core : 1; ?>"
                                                    data-full="<?php echo (int)($sub->full_marks ?? 100); ?>"
                                                    data-pass="<?php echo (int)($sub->passing_marks ?? 33); ?>"
                                                    title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <a href="<?php echo URLROOT; ?>/subjects/delete/<?php echo $sub->id; ?>" 
                                                   class="btn btn-sm btn-outline-danger border-0"
                                                   onclick="return confirm('Are you sure you want to delete this subject?');"
                                                   title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        No subjects registered yet.
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
    const form = document.getElementById('subjectForm');
    const idInput = document.getElementById('subject_id');
    const nameInput = document.getElementById('subject_name');
    const codeInput = document.getElementById('subject_code');
    const typeInput = document.getElementById('subject_type');
    const coreInput = document.getElementById('subject_is_core');
    const fullInput = document.getElementById('subject_full_marks');
    const passInput = document.getElementById('subject_passing_marks');
    const title = document.getElementById('subjectFormTitle');
    const submitBtn = document.getElementById('subjectSubmitBtn');
    const cancelBtn = document.getElementById('subjectCancelBtn');

    document.querySelectorAll('.btn-edit-subject').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            idInput.value = id;
            nameInput.value = this.dataset.name;
            codeInput.value = this.dataset.code;
            typeInput.value = this.dataset.type;
            coreInput.value = this.dataset.core;
            fullInput.value = this.dataset.full;
            passInput.value = this.dataset.pass;

            form.action = '<?php echo URLROOT; ?>/subjects/edit/' + id;
            title.innerHTML = '<i class="fa fa-edit text-warning me-2"></i>Edit Subject';
            submitBtn.innerHTML = '<i class="fa fa-save me-1"></i> Update Subject';
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
        form.action = '<?php echo URLROOT; ?>/subjects/add';
        title.innerHTML = '<i class="fa fa-plus-circle text-primary me-2"></i>Add New Subject';
        submitBtn.innerHTML = '<i class="fa fa-save me-1"></i> Save Subject';
        submitBtn.className = 'btn btn-primary flex-fill fw-bold shadow-sm py-1.5';
        cancelBtn.classList.add('d-none');
    }
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
