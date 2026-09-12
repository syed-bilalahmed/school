<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Compact Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h5 fw-bold mb-0 text-dark">
            <i class="fa fa-layer-group text-primary me-2"></i>Class Sections &amp; Class Teacher Setup
        </h1>
        <small class="text-muted" style="font-size: 0.8rem;">Configure academic sections (A, B, C) and assign dedicated Class Teachers.</small>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo URLROOT; ?>/classes/index" class="btn btn-outline-secondary btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-chalkboard me-1"></i> Classes List
        </a>
        <a href="<?php echo URLROOT; ?>/subjects/assign" class="btn btn-outline-primary btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-book me-1"></i> Assign Subjects
        </a>
        <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-outline-info btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-user-graduate me-1"></i> Student Directory
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
                    if($_GET['success'] == 'created') echo "New section created successfully!";
                    elseif($_GET['success'] == 'updated') echo "Section updated successfully!";
                    elseif($_GET['success'] == 'deleted') echo "Section removed successfully!";
                    else echo "Action completed successfully!";
                ?>
            </div>
        </div>
        <button type="button" class="btn-close small py-2 px-3" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
    $totalSec = count($data['sections'] ?? []);
    $uniqueClasses = count(array_unique(array_column($data['sections'] ?? [], 'class_id')));
    $assignedTeachers = 0;
    foreach ($data['sections'] ?? [] as $s) {
        if (!empty($s->class_teacher_id)) $assignedTeachers++;
    }
    $unassignedSec = $totalSec - $assignedTeachers;
?>

<!-- Compact Summary Metrics Cards -->
<div class="row g-2 mb-3">
    <!-- Total Sections -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Sections</div>
                    <div class="h5 fw-bold mb-0 text-dark"><?php echo $totalSec; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-layer-group fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Covered -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Classes Covered</div>
                    <div class="h5 fw-bold mb-0 text-info"><?php echo $uniqueClasses; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-chalkboard fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Teachers -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Assigned Teachers</div>
                    <div class="h5 fw-bold mb-0 text-success"><?php echo $assignedTeachers; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-user-check fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Unassigned -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Unassigned</div>
                    <div class="h5 fw-bold mb-0 text-warning"><?php echo $unassignedSec; ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-user-clock fs-6"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main 2-Column Work Area -->
<div class="row g-3">
    <!-- Add / Edit Section Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark" id="sectionFormTitle">
                    <i class="fa fa-plus-circle text-primary me-2" id="sectionFormIcon"></i>Add New Section
                </h6>
            </div>
            <div class="card-body p-3">
                <form id="sectionForm" action="<?php echo URLROOT; ?>/sections/add" method="post">
                    <input type="hidden" name="id" id="section_id" value="">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="section_class_id" class="form-select" required style="border-radius: 8px;">
                            <option value="">Select Class</option>
                            <?php foreach($data['classes'] as $cls): ?>
                                <option value="<?php echo $cls->id; ?>"><?php echo htmlspecialchars($cls->class_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Section Name <span class="text-danger">*</span></label>
                        <input type="text" name="section_name" id="section_name" class="form-control" placeholder="e.g. Section A, Blue, Lotus" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">Class Teacher (In-Charge)</label>
                        <select name="class_teacher_id" id="section_teacher_id" class="form-select" style="border-radius: 8px;">
                            <option value="">No Class Teacher Assigned</option>
                            <?php foreach($data['teachers'] as $tch): ?>
                                <option value="<?php echo $tch->id; ?>"><?php echo htmlspecialchars($tch->name); ?> (<?php echo htmlspecialchars($tch->email); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" id="sectionSubmitBtn" class="btn btn-primary flex-fill fw-bold shadow-sm py-1.5" style="border-radius: 8px;">
                            <i class="fa fa-save me-1"></i> Save Section
                        </button>
                        <button type="button" id="sectionCancelBtn" class="btn btn-outline-secondary py-1.5 d-none" style="border-radius: 8px;">
                            <i class="fa fa-times me-1"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sections List Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-table-list text-primary me-2"></i>Class Sections List
                </h6>
                <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1 rounded-pill">
                    <?php echo count($data['sections']); ?> Sections
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-uppercase text-muted small fw-bold">Class</th>
                                <th class="text-uppercase text-muted small fw-bold">Section</th>
                                <th class="text-uppercase text-muted small fw-bold">Class Teacher</th>
                                <th class="text-end pe-3 text-uppercase text-muted small fw-bold" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['sections'])): ?>
                                <?php foreach($data['sections'] as $sec): ?>
                                    <tr>
                                        <td class="ps-3">
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($sec->class_name); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><?php echo htmlspecialchars($sec->section_name); ?></span>
                                        </td>
                                        <td>
                                            <?php if(!empty($sec->class_teacher_name)): ?>
                                                <div class="fw-bold text-dark small">
                                                    <i class="fa fa-chalkboard-teacher text-success me-1"></i><?php echo htmlspecialchars($sec->class_teacher_name); ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary small">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="btn-group shadow-sm" style="border-radius: 6px; overflow: hidden;">
                                                <button type="button" class="btn btn-sm btn-outline-primary border-0 btn-edit-section"
                                                    data-id="<?php echo $sec->id; ?>"
                                                    data-class="<?php echo $sec->class_id; ?>"
                                                    data-name="<?php echo htmlspecialchars($sec->section_name); ?>"
                                                    data-teacher="<?php echo $sec->class_teacher_id ?? ''; ?>"
                                                    title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <a href="<?php echo URLROOT; ?>/sections/delete/<?php echo $sec->id; ?>?confirm=1" 
                                                   class="btn btn-sm btn-outline-danger border-0"
                                                   onclick="return confirm('Are you sure you want to delete this section?');"
                                                   title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        No class sections registered yet.
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
    const form = document.getElementById('sectionForm');
    const idInput = document.getElementById('section_id');
    const classSelect = document.getElementById('section_class_id');
    const nameInput = document.getElementById('section_name');
    const teacherSelect = document.getElementById('section_teacher_id');
    const title = document.getElementById('sectionFormTitle');
    const submitBtn = document.getElementById('sectionSubmitBtn');
    const cancelBtn = document.getElementById('sectionCancelBtn');

    document.querySelectorAll('.btn-edit-section').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            idInput.value = id;
            classSelect.value = this.dataset.class;
            nameInput.value = this.dataset.name;
            teacherSelect.value = this.dataset.teacher || "";

            form.action = '<?php echo URLROOT; ?>/sections/edit/' + id;
            title.innerHTML = '<i class="fa fa-edit text-warning me-2"></i>Edit Section';
            submitBtn.innerHTML = '<i class="fa fa-save me-1"></i> Update Section';
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
        form.action = '<?php echo URLROOT; ?>/sections/add';
        title.innerHTML = '<i class="fa fa-plus-circle text-primary me-2"></i>Add New Section';
        submitBtn.innerHTML = '<i class="fa fa-save me-1"></i> Save Section';
        submitBtn.className = 'btn btn-primary flex-fill fw-bold shadow-sm py-1.5';
        cancelBtn.classList.add('d-none');
    }
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
