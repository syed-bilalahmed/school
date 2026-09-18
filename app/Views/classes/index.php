<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Compact Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h5 fw-bold mb-0 text-dark">
            <i class="fa fa-chalkboard-user text-primary me-2"></i>Class &amp; Grade Management
        </h1>
        <small class="text-muted" style="font-size: 0.8rem;">Configure academic classes, sections, and monitor student enrollment.</small>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo URLROOT; ?>/sections/index" class="btn btn-outline-primary btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-layer-group me-1"></i> Manage Sections
        </a>
        <a href="<?php echo URLROOT; ?>/subjects/assign" class="btn btn-outline-secondary btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-book me-1"></i> Assign Subjects
        </a>
        <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-outline-info btn-sm px-2 py-1 shadow-sm" style="border-radius: 6px; font-size: 0.8rem;">
            <i class="fa fa-user-graduate me-1"></i> Student Directory
        </a>
    </div>
</div>

<!-- Session Alerts (PHP Fallback) -->
<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2 px-3 mb-3" style="border-radius: 8px;" role="alert">
        <div class="d-flex align-items-center small">
            <i class="fa fa-check-circle fs-6 text-success me-2"></i>
            <div><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
        </div>
        <button type="button" class="btn-close btn-close-white small py-2 px-3" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm py-2 px-3 mb-3" style="border-radius: 8px;" role="alert">
        <div class="d-flex align-items-center small">
            <i class="fa fa-exclamation-circle fs-6 text-danger me-2"></i>
            <div><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
        </div>
        <button type="button" class="btn-close btn-close-white small py-2 px-3" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Compact Summary Metrics Cards -->
<div class="row g-2 mb-3">
    <!-- Total Classes -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Classes</div>
                    <div class="h5 fw-bold mb-0 text-dark" id="statTotalClasses"><?php echo (int)($data['total_classes'] ?? 0); ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-chalkboard fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Sections -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Active Sections</div>
                    <div class="h5 fw-bold mb-0 text-info" id="statTotalSections"><?php echo (int)($data['total_sections'] ?? 0); ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-layer-group fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrolled Students -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Enrolled Students</div>
                    <div class="h5 fw-bold mb-0 text-success" id="statTotalStudents"><?php echo (int)($data['total_students'] ?? 0); ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-user-graduate fs-6"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Students per Class -->
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 10px;">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Avg per Class</div>
                    <div class="h5 fw-bold mb-0 text-warning" id="statAvgStudents"><?php echo htmlspecialchars($data['avg_students'] ?? 0); ?></div>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning flex-shrink-0" style="width: 36px; height: 36px;">
                    <i class="fa fa-chart-pie fs-6"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main 2-Column Work Area -->
<div class="row g-3">
    <!-- Left Column: Add / Edit Form -->
    <div class="col-lg-4">
        <!-- Form Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-2.5 px-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark" id="classFormHeading">
                        <i class="fa fa-plus-circle text-primary me-2" id="classFormIcon"></i><span id="classFormTitle">Add New Class</span>
                    </h6>
                    <span class="badge bg-light text-secondary border d-none" id="editModeIndicator">Editing</span>
                </div>
            </div>
            <div class="card-body p-3">
                <form id="classForm" action="<?php echo URLROOT; ?>/classes/add" method="post">
                    <input type="hidden" name="id" id="class_id" value="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                    <div class="mb-3">
                        <label for="class_name" class="form-label small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.72rem;">
                            Class Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="class_name" 
                               id="class_name" 
                               class="form-control" 
                               placeholder="e.g. Class 1, Grade 10" 
                               required 
                               maxlength="100"
                               autocomplete="off"
                               style="border-radius: 8px;">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" id="classSubmitBtn" class="btn btn-primary flex-fill fw-bold shadow-sm py-1.5" style="border-radius: 8px;">
                            <i class="fa fa-save me-1" id="classSubmitIcon"></i> <span id="classSubmitText">Save Class</span>
                        </button>
                        <button type="button" id="classCancelBtn" class="btn btn-outline-secondary py-1.5 d-none" style="border-radius: 8px;">
                            <i class="fa fa-times me-1"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Classes Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <!-- Simple Card Header -->
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-chalkboard text-primary me-2"></i>Classes List
                </h6>
                <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1 rounded-pill" id="classCountBadge">
                    <?php echo count($data['classes'] ?? []); ?> Classes
                </span>
            </div>

            <!-- Table Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="classesTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-uppercase text-muted small fw-bold" style="width: 70px;">ID</th>
                                <th class="text-uppercase text-muted small fw-bold">Class Name</th>
                                <th class="text-uppercase text-muted small fw-bold">Sections</th>
                                <th class="text-uppercase text-muted small fw-bold text-center">Enrollment</th>
                                <th class="text-uppercase text-muted small fw-bold text-end pe-3" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="classesTableBody">
                            <?php if(!empty($data['classes'])): ?>
                                <?php foreach($data['classes'] as $cls): ?>
                                    <tr id="class-row-<?php echo $cls->id; ?>" data-id="<?php echo $cls->id; ?>">
                                        <!-- ID -->
                                        <td class="ps-3">
                                            <span class="badge bg-light text-secondary border font-monospace px-2 py-1" style="border-radius: 6px;">
                                                #<?php echo $cls->id; ?>
                                            </span>
                                        </td>

                                        <!-- Class Name -->
                                        <td>
                                            <span class="fw-bold text-dark class-name-label"><?php echo htmlspecialchars($cls->class_name); ?></span>
                                        </td>

                                        <!-- Sections -->
                                        <td>
                                            <?php if (!empty($cls->section_names)): ?>
                                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                                    <?php 
                                                        $secArr = explode(',', $cls->section_names);
                                                        foreach ($secArr as $sName): 
                                                            $sName = trim($sName);
                                                            if (empty($sName)) continue;
                                                    ?>
                                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="border-radius: 6px;">
                                                            <?php echo htmlspecialchars($sName); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">None</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Enrollment -->
                                        <td class="text-center">
                                            <?php $stCount = (int)($cls->student_count ?? 0); ?>
                                            <?php if ($stCount > 0): ?>
                                                <a href="<?php echo URLROOT; ?>/students/index?class_id=<?php echo $cls->id; ?>" 
                                                   class="text-decoration-none badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-bold"
                                                   title="View enrolled students">
                                                    <?php echo $stCount; ?> Students
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill small">
                                                    0 Enrolled
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-end pe-3">
                                            <div class="btn-group shadow-sm" style="border-radius: 6px; overflow: hidden;">
                                                <!-- Edit -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary border-0 btn-edit-class"
                                                        data-id="<?php echo $cls->id; ?>"
                                                        data-name="<?php echo htmlspecialchars($cls->class_name); ?>"
                                                        title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </button>

                                                <!-- Delete -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger border-0 btn-delete-class"
                                                        data-id="<?php echo $cls->id; ?>"
                                                        data-name="<?php echo htmlspecialchars($cls->class_name); ?>"
                                                        data-students="<?php echo (int)($cls->student_count ?? 0); ?>"
                                                        title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="noClassesRow">
                                    <td colspan="5" class="text-center py-4 text-muted small">
                                        No classes created yet. Use the form on the left to add one.
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

<!-- Modal: Delete Class Confirmation -->
<div class="modal fade" id="deleteClassModal" tabindex="-1" aria-labelledby="deleteClassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-danger text-white py-3 px-4 border-0">
                <h6 class="modal-title fw-bold" id="deleteClassModalLabel">
                    <i class="fa fa-triangle-exclamation me-2"></i>Delete Academic Class
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                    <i class="fa fa-trash-alt fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="deleteModalClassName">Are you sure?</h5>
                <p class="text-muted small mb-3">
                    You are about to delete this class record permanently. This action cannot be undone.
                </p>

                <!-- Warning if students exist -->
                <div class="alert alert-warning text-start small border-0 d-none" id="deleteStudentWarning" style="border-radius: 10px;">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-exclamation-triangle fs-5 text-warning me-2 flex-shrink-0"></i>
                        <div>
                            <strong>Active Enrollment Warning:</strong>
                            <span id="deleteWarningText">This class has students enrolled. You must reassign students before deleting.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-3 px-4 border-0 justify-content-center gap-2">
                <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger px-4 fw-bold shadow-sm" id="confirmDeleteBtn" style="border-radius: 8px;">
                    <i class="fa fa-trash me-1" id="confirmDeleteIcon"></i> <span id="confirmDeleteText">Delete Class</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Class Management Interactive Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const URL_ROOT = '<?php echo URLROOT; ?>';

    // DOM Elements - Form
    const classForm = document.getElementById('classForm');
    const classIdInput = document.getElementById('class_id');
    const classNameInput = document.getElementById('class_name');
    const classFormTitle = document.getElementById('classFormTitle');
    const classFormIcon = document.getElementById('classFormIcon');
    const classSubmitBtn = document.getElementById('classSubmitBtn');
    const classSubmitText = document.getElementById('classSubmitText');
    const classSubmitIcon = document.getElementById('classSubmitIcon');
    const classCancelBtn = document.getElementById('classCancelBtn');
    const editModeIndicator = document.getElementById('editModeIndicator');

    // DOM Elements - Table
    const classesTableBody = document.getElementById('classesTableBody');
    const classCountBadge = document.getElementById('classCountBadge');
    const statTotalClasses = document.getElementById('statTotalClasses');

    // DOM Elements - Delete Modal
    const deleteModalEl = document.getElementById('deleteClassModal');
    let deleteModalInstance = null;
    if (deleteModalEl && typeof bootstrap !== 'undefined') {
        deleteModalInstance = new bootstrap.Modal(deleteModalEl);
    }
    const deleteModalClassName = document.getElementById('deleteModalClassName');
    const deleteStudentWarning = document.getElementById('deleteStudentWarning');
    const deleteWarningText = document.getElementById('deleteWarningText');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const confirmDeleteText = document.getElementById('confirmDeleteText');
    const confirmDeleteIcon = document.getElementById('confirmDeleteIcon');
    let pendingDeleteId = null;

    // ----------------------------------------------------
    // 1. EDIT CLASS HANDLER
    // ----------------------------------------------------
    function initEditButtons() {
        document.querySelectorAll('.btn-edit-class').forEach(btn => {
            btn.removeEventListener('click', handleEditClick);
            btn.addEventListener('click', handleEditClick);
        });
    }

    function handleEditClick() {
        const id = this.dataset.id;
        const name = this.dataset.name;

        classIdInput.value = id;
        classNameInput.value = name;
        classNameInput.focus();

        // Update form action and UI state to Edit Mode
        classForm.setAttribute('action', URL_ROOT + '/classes/update/' + id);
        classFormTitle.textContent = 'Edit Class';
        classFormIcon.className = 'fa fa-edit text-warning me-2';
        classSubmitText.textContent = 'Update Class';
        classSubmitIcon.className = 'fa fa-check me-1';
        classSubmitBtn.className = 'btn btn-warning flex-fill fw-bold shadow-sm';
        classCancelBtn.classList.remove('d-none');
        editModeIndicator.classList.remove('d-none');

        // Smooth scroll to form if on mobile/small screen
        if (window.innerWidth < 992) {
            classForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Cancel Edit button
    classCancelBtn.addEventListener('click', resetClassForm);

    function resetClassForm() {
        classIdInput.value = '';
        classForm.reset();
        classForm.setAttribute('action', URL_ROOT + '/classes/add');
        classFormTitle.textContent = 'Add New Class';
        classFormIcon.className = 'fa fa-plus-circle text-primary me-2';
        classSubmitText.textContent = 'Save Class';
        classSubmitIcon.className = 'fa fa-save me-1';
        classSubmitBtn.className = 'btn btn-primary flex-fill fw-bold shadow-sm';
        classCancelBtn.classList.add('d-none');
        editModeIndicator.classList.add('d-none');
        classNameInput.focus();
    }

    // Add/Update uses global PJAX form interceptor in footer.php (no full page reload)

    // ----------------------------------------------------
    // 2. DELETE CLASS HANDLER
    // ----------------------------------------------------
    function initDeleteButtons() {
        document.querySelectorAll('.btn-delete-class').forEach(btn => {
            btn.removeEventListener('click', handleDeleteClick);
            btn.addEventListener('click', handleDeleteClick);
        });
    }

    function handleDeleteClick() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        const students = parseInt(this.dataset.students || 0, 10);

        pendingDeleteId = id;
        deleteModalClassName.textContent = `Delete "${name}"?`;

        if (students > 0) {
            deleteStudentWarning.classList.remove('d-none');
            deleteWarningText.textContent = `This class currently has ${students} enrolled student(s). You must transfer or graduate these students before this class can be safely deleted.`;
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.classList.add('opacity-50');
            confirmDeleteBtn.title = "Cannot delete class with active students";
        } else {
            deleteStudentWarning.classList.add('d-none');
            confirmDeleteBtn.disabled = false;
            confirmDeleteBtn.classList.remove('opacity-50');
            confirmDeleteBtn.title = "Delete Class";
        }

        if (deleteModalInstance) {
            deleteModalInstance.show();
        } else {
            // Fallback native confirm
            if (confirm(`Are you sure you want to delete class "${name}"?`)) {
                executeDelete(id);
            }
        }
    }

    confirmDeleteBtn.addEventListener('click', function() {
        if (!pendingDeleteId) return;
        executeDelete(pendingDeleteId);
    });

    function executeDelete(id) {
        confirmDeleteBtn.disabled = true;
        confirmDeleteIcon.className = 'fa fa-spinner fa-spin me-1';
        confirmDeleteText.textContent = 'Deleting...';

        const formData = new FormData();
        formData.append('id', id);
        formData.append('ajax_submit', '1');
        if (window.CSRF_TOKEN) formData.append('csrf_token', window.CSRF_TOKEN);

        fetch(URL_ROOT + '/classes/delete/' + id, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                return response.json();
            }
            return { success: response.ok, reload: true };
        })
        .then(data => {
            if (data.reload) {
                if (typeof window.spaNavigate === 'function') {
                    window.spaNavigate(window.location.href, false);
                }
                return;
            }

            if (deleteModalInstance) {
                deleteModalInstance.hide();
            }

            if (data.success) {
                if (window.showToast) {
                    window.showToast(data.message || 'Class deleted successfully.', 'success');
                }

                // Remove row with smooth collapse animation
                const targetRow = document.getElementById('class-row-' + id);
                if (targetRow) {
                    targetRow.style.transition = 'all 0.3s ease';
                    targetRow.style.opacity = '0';
                    targetRow.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        targetRow.remove();
                        updateRowCount();
                    }, 300);
                }

                // If user was currently editing this class, reset the form
                if (classIdInput.value == id) {
                    resetClassForm();
                }
            } else {
                if (window.showToast) {
                    window.showToast(data.message || 'Failed to delete class.', 'danger');
                } else {
                    alert(data.message || 'Failed to delete class.');
                }
            }
        })
        .catch(err => {
            console.error('Delete Error:', err);
            if (window.showToast) {
                window.showToast('Server communication error.', 'danger');
            }
        })
        .finally(() => {
            confirmDeleteBtn.disabled = false;
            confirmDeleteIcon.className = 'fa fa-trash me-1';
            confirmDeleteText.textContent = 'Delete Class';
            pendingDeleteId = null;
        });
    }

    // ----------------------------------------------------
    // 4. HELPER: UPDATE TABLE ROW COUNTERS
    // ----------------------------------------------------
    function updateRowCount() {
        const remainingRows = classesTableBody.querySelectorAll('tr[id^="class-row-"]');
        const count = remainingRows.length;
        if (classCountBadge) classCountBadge.textContent = count + ' Classes';
        if (statTotalClasses) statTotalClasses.textContent = count;

        if (count === 0) {
            const emptyRow = document.getElementById('noClassesRow');
            if (emptyRow) emptyRow.classList.remove('d-none');
        }
    }

    // Initialize listeners
    initEditButtons();
    initDeleteButtons();
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

