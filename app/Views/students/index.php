<?php require APPROOT . '/Views/layouts/header.php'; ?>

<?php
$studentsList = $data['students'] ?? [];
$visibleCount = count($studentsList);
$totalEnrolled = $data['total_enrolled'] ?? ($data['total_students'] ?? $visibleCount);
$totalClasses = count($data['classes'] ?? []);
$isFilterApplied = !empty($data['class_id']) || !empty($data['keyword']) || (!empty($data['search_type']) && $data['search_type'] === 'all');
?>

<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small" style="font-size: 0.78rem;">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Student Directory</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-dark fs-5">Student Directory &amp; Enrolment Hub</h4>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/families/index" class="btn btn-outline-secondary btn-sm px-2 py-1 shadow-xs" style="font-size: 0.82rem;">
            <i class="fa fa-people-roof me-1"></i> Families
        </a>
        <a href="<?php echo URLROOT; ?>/students/import" class="btn btn-outline-success btn-sm px-2 py-1 shadow-xs" style="font-size: 0.82rem;">
            <i class="fa fa-file-import me-1"></i> Import CSV
        </a>
        <a href="<?php echo URLROOT; ?>/students/printAdmission?blank=1" target="_blank" class="btn btn-outline-dark btn-sm px-2 py-1 shadow-xs" style="font-size: 0.82rem;" title="Print Official A4 Blank Registration Form">
            <i class="fa fa-print me-1"></i> Blank Form
        </a>
        <button type="button" class="btn btn-warning btn-sm px-2 py-1 shadow-xs text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#admissionDesignerModal" style="font-size: 0.82rem;" title="Design Admission Form & Add Custom Fields">
            <i class="fa fa-pen-ruler me-1"></i> Form Designer &amp; Fields
        </button>
        <a href="<?php echo URLROOT; ?>/students/admission" class="btn btn-primary btn-sm px-3 py-1 shadow-sm" style="font-size: 0.82rem;">
            <i class="fa fa-user-plus me-1"></i> New Admission
        </a>
    </div>
</div>

<!-- Alert Messages -->
<?php if(isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-3 small shadow-xs" role="alert">
        <i class="fa fa-check-circle fs-6"></i>
        <div class="fw-semibold text-dark"><?php echo $_SESSION['flash_success']; ?></div>
        <button type="button" class="btn-close py-2 ms-auto" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php elseif(isset($_GET['success']) && $_GET['success'] == 'admitted'): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-3 small shadow-xs" role="alert">
        <i class="fa fa-check-circle fs-6"></i>
        <div class="fw-semibold text-dark"><strong>Student Admitted Successfully!</strong> Enrolment record created in system database.</div>
        <button type="button" class="btn-close py-2 ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(isset($_GET['msg'])): ?>
    <?php if($_GET['msg'] == 'imported'): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-3 small shadow-xs" role="alert">
            <i class="fa fa-check-circle"></i>
            <div>Successfully imported <strong><?php echo htmlspecialchars($_GET['count'] ?? '0', ENT_QUOTES, 'UTF-8'); ?></strong> students.</div>
            <button type="button" class="btn-close py-2 ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif($_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-3 small shadow-xs" role="alert">
            <i class="fa fa-check-circle"></i>
            <div>Student record deleted successfully.</div>
            <button type="button" class="btn-close py-2 ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif($_GET['msg'] == 'bulk_deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-3 small shadow-xs" role="alert">
            <i class="fa fa-check-circle"></i>
            <div>Successfully deleted <strong><?php echo htmlspecialchars($_GET['count'] ?? '0', ENT_QUOTES, 'UTF-8'); ?></strong> student(s).</div>
            <button type="button" class="btn-close py-2 ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- 4 Executive KPI Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-user-graduate"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Enrolled Students</div>
                    <h6 class="fw-bold mb-0 text-dark fs-6"><?php echo number_format($totalEnrolled); ?> <small class="text-muted fw-normal" style="font-size: 0.75rem;">Total</small></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 bg-info-subtle text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-users"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Visible On Page</div>
                    <h6 class="fw-bold mb-0 text-info fs-6" id="kpiVisibleCount"><?php echo $visibleCount; ?> <small class="text-muted fw-normal" style="font-size: 0.75rem;">Students</small></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-chalkboard"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Active Classes</div>
                    <h6 class="fw-bold mb-0 text-success fs-6"><?php echo $totalClasses; ?> <small class="text-muted fw-normal" style="font-size: 0.75rem;">Grades</small></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem; color: #7c3aed; background: rgba(124, 58, 237, 0.1);">
                    <i class="fa fa-calendar-check"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Attendance Portal</div>
                    <a href="<?php echo URLROOT; ?>/attendance/student" class="fw-bold mb-0 d-block text-decoration-none" style="color: #7c3aed; font-size: 0.85rem;">
                        Mark Daily <i class="fa fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Single-Line Filter Toolbar -->
<div class="card shadow-sm border-0 mb-3 bg-white">
    <div class="card-body py-2 px-3">
        <form action="<?php echo URLROOT; ?>/students/index" method="get" id="filterForm" class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">
                <span class="small fw-semibold text-muted"><i class="fa fa-filter me-1"></i> Filter:</span>
                
                <!-- Class Selection -->
                <select name="class_id" id="class_id" class="form-select form-select-sm w-auto py-1" style="font-size: 0.85rem;" onchange="handleClassFilterChange(this.value)">
                    <option value="">-- Select Class --</option>
                    <option value="all" <?php echo (($data['class_id'] ?? '') === 'all') ? 'selected' : ''; ?>>-- All Classes --</option>
                    <?php foreach($data['classes'] as $class): ?>
                        <option value="<?php echo $class->id; ?>" <?php echo ((string)($data['class_id'] ?? '') === (string)$class->id) ? 'selected' : ''; ?>>
                            <?php echo $class->class_name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Section Selection -->
                <select name="section_id" id="section_id" class="form-select form-select-sm w-auto py-1" style="font-size: 0.85rem;" onchange="applyInstantClientFilter()">
                    <option value="">All Sections</option>
                </select>

                <!-- Search Input -->
                <div class="input-group input-group-sm w-auto flex-grow-1" style="max-width: 320px;">
                    <span class="input-group-text bg-white border-end-0 py-1">
                        <i class="fa fa-search text-muted small"></i>
                    </span>
                    <input type="text" name="keyword" id="liveStudentSearch" class="form-control form-control-sm border-start-0 ps-0 py-1" style="font-size: 0.85rem;" placeholder="Type name, roll, admission..." value="<?php echo htmlspecialchars($data['keyword'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <button class="btn btn-sm btn-light border border-start-0 py-1" id="clearSearchBtn" type="button" style="<?php echo !empty($data['keyword']) ? 'display: inline-flex;' : 'display: none;'; ?>" title="Clear Search">
                        <i class="fa fa-times text-muted"></i>
                    </button>
                </div>
                
                <button type="submit" class="btn btn-sm btn-dark px-2 py-1" style="font-size: 0.82rem;">
                    <i class="fa fa-database me-1"></i> Search
                </button>
            </div>

            <div class="d-flex align-items-center gap-2 ms-auto">
                <?php if(!empty($data['class_id']) || !empty($data['section_id']) || !empty($data['keyword'])): ?>
                    <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-sm btn-light border text-secondary px-2 py-1" style="font-size: 0.82rem;" title="Reset Filters">
                        <i class="fa fa-undo me-1"></i> Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Student Table Card with Bulk Action -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-0 border-bottom d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-user-graduate text-primary fs-5"></i>
            <h5 class="fw-bold mb-0 text-dark">Enrolled Students</h5>
        </div>
        <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 ms-sm-auto" style="border-radius: 30px;" id="visibleCountBadge">
            <?php echo $visibleCount; ?> Students
        </span>
    </div>

    <!-- Bulk Action Floating Bar -->
    <div id="bulkActionBar" class="d-flex align-items-center justify-content-between py-2 px-3 border-bottom flex-wrap gap-2" style="display: none !important; background: rgba(99, 102, 241, 0.08); border-color: rgba(99, 102, 241, 0.2) !important;">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-check-double text-primary"></i>
            <span class="fw-bold text-dark small"><span id="selectedCountText">0</span> student(s) selected</span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-primary py-1 shadow-xs fw-bold" id="bulkPrintChallanBtn" onclick="printBulkChallans()" style="font-size: 0.78rem;">
                <i class="fa fa-print me-1"></i> Print Fee Challans
            </button>
            <button type="button" class="btn btn-sm btn-outline-dark py-1 shadow-xs" id="bulkPrintFormBtn" onclick="printBulkForms()" style="font-size: 0.78rem;">
                <i class="fa fa-file-alt me-1"></i> Print Forms
            </button>
            <button type="button" class="btn btn-sm btn-light border py-1" id="clearSelectionBtn" style="font-size: 0.78rem;">
                Clear Selection
            </button>
            <button type="button" class="btn btn-sm btn-danger py-1 shadow-xs" id="bulkDeleteBtn" style="font-size: 0.78rem;">
                <i class="fa fa-trash-alt me-1"></i> Delete Selected
            </button>
        </div>
    </div>

    <!-- Bulk Delete Form -->
    <form id="bulkDeleteForm" action="<?php echo URLROOT; ?>/students/bulkDelete" method="post">
        <div class="table-responsive" style="min-height: 320px;">
            <table class="table table-hover align-middle mb-0" id="studentDirectoryTable" style="width: 100%; table-layout: fixed;">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 4%; text-align: center;">
                            <input type="checkbox" id="selectAllCheckbox" class="form-check-input" title="Select All" style="cursor: pointer;">
                        </th>
                        <th style="width: 12%;">Admission No</th>
                        <th style="width: 30%;">Student Details</th>
                        <th style="width: 16%;">Class &amp; Section</th>
                        <th style="width: 10%;">Roll No</th>
                        <th style="width: 14%;">Parent Phone</th>
                        <th style="width: 14%;" class="text-end pe-3">Quick Actions</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    <?php if(empty($studentsList)): ?>
                        <?php if(!$isFilterApplied): ?>
                            <tr id="initialPromptRow">
                                <td colspan="7" class="text-center py-5">
                                    <div class="mb-3">
                                        <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 65px; height: 65px;">
                                            <i class="fa fa-filter fa-2x"></i>
                                        </span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">Select Class to View Enrolled Students</h5>
                                    <p class="text-muted small mb-3">Please select a class from the filter dropdown above or click one of the classes below.</p>
                                    <div class="d-flex justify-content-center gap-2 flex-wrap px-3">
                                        <?php foreach(array_slice($data['classes'] ?? [], 0, 8) as $c): ?>
                                            <a href="<?php echo URLROOT; ?>/students/index?class_id=<?php echo $c->id; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-xs">
                                                <i class="fa fa-graduation-cap me-1"></i><?php echo htmlspecialchars($c->class_name); ?>
                                            </a>
                                        <?php endforeach; ?>
                                        <a href="<?php echo URLROOT; ?>/students/index?class_id=all" class="btn btn-light border text-secondary btn-sm rounded-pill px-3 shadow-xs">
                                            <i class="fa fa-users me-1"></i>View All Classes
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr id="initialEmptyRow">
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa fa-user-graduate fa-2x mb-2 d-block opacity-50"></i>
                                    No matching student records found for selected filter.
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php foreach($studentsList as $student) : ?>
                            <tr class="student-row" 
                                data-class-id="<?php echo $student->class_id ?? ''; ?>" 
                                data-section-id="<?php echo $student->section_id ?? ''; ?>"
                                data-search="<?php echo htmlspecialchars(strtolower($student->admission_no . ' ' . $student->name . ' ' . ($student->email ?? '') . ' ' . ($student->class_name ?? '') . ' ' . ($student->section_name ?? '') . ' ' . $student->roll_no . ' ' . ($student->parent_phone ?? '') . ' ' . ($student->bform_cnic ?? '') . ' ' . ($student->family_id ?? '') . ' ' . ($student->father_name ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
                                <td style="text-align: center;">
                                    <input type="checkbox" name="student_ids[]" value="<?php echo $student->id; ?>" class="form-check-input student-row-checkbox" style="cursor: pointer;">
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace text-truncate d-inline-block" style="max-width: 100%; font-size: 0.8rem;">
                                        <?php echo htmlspecialchars($student->admission_no, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-1 flex-wrap">
                                            <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $student->id; ?>" class="text-dark text-decoration-none hover-primary fw-bold" style="font-size: 0.88rem;">
                                                <?php echo htmlspecialchars($student->name, ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                            <?php if(!empty($student->family_id)): ?>
                                                <span class="badge bg-purple-subtle text-purple flex-shrink-0" style="font-size: 0.65rem; color: #7c3aed; background: rgba(124, 58, 237, 0.1);" title="Family Unit">
                                                    <i class="fa fa-users me-1"></i><?php echo htmlspecialchars($student->family_id); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 0.76rem;"><?php echo htmlspecialchars($student->father_name ? 'S/O: ' . $student->father_name : ($student->email ?? ''), ENT_QUOTES, 'UTF-8'); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle text-truncate d-inline-block" style="max-width: 100%; font-size: 0.74rem;">
                                        <?php echo htmlspecialchars($student->class_name ?? 'Unassigned', ENT_QUOTES, 'UTF-8'); ?>
                                        (Sec <?php echo htmlspecialchars($student->section_name ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>)
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark small"><?php echo htmlspecialchars($student->roll_no, ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <?php if(!empty($student->parent_phone)): ?>
                                        <a href="tel:<?php echo htmlspecialchars($student->parent_phone, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none text-dark small fw-semibold text-truncate d-block" style="font-size: 0.78rem;">
                                            <i class="fa fa-phone-alt me-1 text-success"></i><?php echo htmlspecialchars($student->parent_phone, ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="<?php echo URLROOT; ?>/students/printAdmission/<?php echo $student->id; ?>" target="_blank" class="btn btn-sm btn-outline-dark px-2 py-1" style="font-size: 0.78rem;" title="Print Official A4 Admission Form">
                                            <i class="fa fa-print"></i>
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $student->id; ?>" class="btn btn-sm btn-outline-primary px-2 py-1" style="font-size: 0.78rem;" title="View 360° Profile">
                                            <i class="fa fa-id-card"></i>
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/students/edit/<?php echo $student->id; ?>" class="btn btn-sm btn-outline-secondary px-2 py-1" style="font-size: 0.78rem;" title="Edit Student Profile">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" style="font-size: 0.78rem;" onclick="deleteSingleStudent(<?php echo $student->id; ?>, '<?php echo htmlspecialchars(addslashes($student->name), ENT_QUOTES, 'UTF-8'); ?>')" title="Delete Record">
                                            <i class="fa fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <!-- Dynamic Live Search Empty Row -->
                    <tr id="noLiveSearchResultsRow" style="display: none;">
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa fa-search fa-2x mb-2 d-block opacity-50"></i>
                            <span>No students match your instant search filter.</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>

<!-- Hidden Individual Delete Form -->
<form id="singleDeleteForm" method="post" style="display: none;"></form>

<!-- Pagination -->
<?php if(($data['search_type'] ?? '') === '' && ($data['total_pages'] ?? 1) > 1): ?>
    <nav class="mt-3 mb-4">
        <ul class="pagination pagination-sm justify-content-center">
            <li class="page-item <?php echo ($data['current_page'] <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo URLROOT; ?>/students/index?page=<?php echo max(1, $data['current_page'] - 1); ?>">Prev</a>
            </li>
            <?php for($p = 1; $p <= $data['total_pages']; $p++): ?>
                <li class="page-item <?php echo ($p == $data['current_page']) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo URLROOT; ?>/students/index?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($data['current_page'] >= $data['total_pages']) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo URLROOT; ?>/students/index?page=<?php echo min($data['total_pages'], $data['current_page'] + 1); ?>">Next</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>

<script>
    // AJAX to get sections when class is selected
    function getSections(classId, selectedSectionId = null, callback = null){
        const secSelect = document.getElementById('section_id');
        if(!classId){
             secSelect.innerHTML = '<option value="">All Sections</option>';
             if(callback) callback();
             return;
        }
        fetch('<?php echo URLROOT; ?>/students/ajaxGetSections/' + classId)
        .then(response => response.json())
        .then(data => {
             let html = '<option value="">All Sections</option>';
             data.forEach(sec => {
                  let selected = (selectedSectionId == sec.id) ? 'selected' : '';
                  html += `<option value="${sec.id}" ${selected}>${sec.section_name}</option>`;
             });
             secSelect.innerHTML = html;
             if(callback) callback();
        });
    }

    function handleClassFilterChange(classId) {
        const form = document.getElementById('filterForm');
        if (form) form.submit();
    }

    window.printBulkChallans = function() {
        const checkedBoxes = document.querySelectorAll('.student-row-checkbox:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value).filter(Boolean);
        if (ids.length === 0) {
            alert("Please select at least one student.");
            return;
        }
        const url = '<?php echo URLROOT; ?>/fees/batchChallans?student_ids=' + encodeURIComponent(ids.join(','));
        window.open(url, '_blank');
    };

    window.printBulkForms = function() {
        const checkedBoxes = document.querySelectorAll('.student-row-checkbox:checked');
        const ids = Array.from(checkedBoxes).map(cb => cb.value).filter(Boolean);
        if (ids.length === 0) {
            alert("Please select at least one student.");
            return;
        }
        ids.slice(0, 10).forEach(id => {
            window.open('<?php echo URLROOT; ?>/students/printAdmission/' + id, '_blank');
        });
    };

    function applyInstantClientFilter() {
        const classId = document.getElementById('class_id').value;
        const sectionId = document.getElementById('section_id').value;
        const query = document.getElementById('liveStudentSearch') ? document.getElementById('liveStudentSearch').value.toLowerCase().trim() : '';

        const studentRows = document.querySelectorAll('.student-row');
        const noLiveResultsRow = document.getElementById('noLiveSearchResultsRow');
        const visibleCountBadge = document.getElementById('visibleCountBadge');
        const kpiVisibleCount = document.getElementById('kpiVisibleCount');
        let visibleCount = 0;
        const totalRows = studentRows.length;

        studentRows.forEach(row => {
            const rClass = row.getAttribute('data-class-id') || '';
            const rSec = row.getAttribute('data-section-id') || '';
            const rSearch = row.getAttribute('data-search') || '';

            const classMatch = (!classId || rClass === classId);
            const secMatch = (!sectionId || rSec === sectionId);
            const searchMatch = (!query || rSearch.includes(query));

            if (classMatch && secMatch && searchMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noLiveResultsRow) {
            noLiveResultsRow.style.display = (visibleCount === 0 && totalRows > 0) ? '' : 'none';
        }

        if (visibleCountBadge) {
            visibleCountBadge.textContent = visibleCount + ' Students';
        }
        if (kpiVisibleCount) {
            kpiVisibleCount.innerHTML = visibleCount + ' <small class="text-muted fw-normal" style="font-size: 0.75rem;">Students</small>';
        }

        updateBulkBar();
    }

    // On Page Load: if class_id is set, populate sections dropdown
    document.addEventListener('DOMContentLoaded', function() {
        const currentClassId = '<?php echo $data['class_id'] ?? ''; ?>';
        const currentSectionId = '<?php echo $data['section_id'] ?? ''; ?>';
        if (currentClassId) {
            getSections(currentClassId, currentSectionId, function() {
                applyInstantClientFilter();
            });
        }

        const liveSearchInput = document.getElementById('liveStudentSearch');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        if (liveSearchInput) {
            liveSearchInput.addEventListener('input', function() {
                if (this.value.trim().length > 0) {
                    if (clearSearchBtn) clearSearchBtn.style.display = 'inline-flex';
                } else {
                    if (clearSearchBtn) clearSearchBtn.style.display = 'none';
                }
                applyInstantClientFilter();
            });

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                    liveSearchInput.value = '';
                    if (clearSearchBtn) clearSearchBtn.style.display = 'none';
                    applyInstantClientFilter();
                    liveSearchInput.focus();
                });
            }
        }
    });

    // Individual Student Delete
    function deleteSingleStudent(id, studentName){
        if(confirm("Are you sure you want to delete student: " + studentName + "?\n\nThis will remove their profile and all attendance/exam records permanently.")){
            const form = document.getElementById('singleDeleteForm');
            form.action = '<?php echo URLROOT; ?>/students/delete/' + id;
            form.submit();
        }
    }

    // Checkbox & Bulk Actions Logic
    let updateBulkBar = function() {};

    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const rowCheckboxes = document.querySelectorAll('.student-row-checkbox');
        const bulkActionBar = document.getElementById('bulkActionBar');
        const selectedCountText = document.getElementById('selectedCountText');
        const clearSelectionBtn = document.getElementById('clearSelectionBtn');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');

        updateBulkBar = function() {
            const checkedBoxes = document.querySelectorAll('.student-row-checkbox:checked');
            const count = checkedBoxes.length;

            if (bulkActionBar && selectedCountText) {
                if (count > 0) {
                    bulkActionBar.style.setProperty('display', 'flex', 'important');
                    selectedCountText.textContent = count;
                } else {
                    bulkActionBar.style.setProperty('display', 'none', 'important');
                    selectedCountText.textContent = '0';
                }
            }

            // Update master checkbox state
            if (selectAllCheckbox) {
                const visibleCheckboxes = Array.from(document.querySelectorAll('.student-row'))
                    .filter(row => row.style.display !== 'none')
                    .map(row => row.querySelector('.student-row-checkbox'))
                    .filter(Boolean);

                if (visibleCheckboxes.length > 0) {
                    const allVisibleChecked = visibleCheckboxes.every(cb => cb.checked);
                    const someVisibleChecked = visibleCheckboxes.some(cb => cb.checked);
                    selectAllCheckbox.checked = allVisibleChecked;
                    selectAllCheckbox.indeterminate = !allVisibleChecked && someVisibleChecked;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                }
            }
        };

        // Master Select All toggle
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const visibleRows = document.querySelectorAll('.student-row');
                visibleRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const cb = row.querySelector('.student-row-checkbox');
                        if (cb) cb.checked = selectAllCheckbox.checked;
                    }
                });
                updateBulkBar();
            });
        }

        // Row Checkbox change
        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        // Clear selection button
        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener('click', function() {
                rowCheckboxes.forEach(cb => cb.checked = false);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                }
                updateBulkBar();
            });
        }

        // Bulk delete submission with confirmation
        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.student-row-checkbox:checked');
                const count = checkedBoxes.length;
                if (count === 0) {
                    alert("Please select at least one student to delete.");
                    return;
                }

                if (confirm("Are you sure you want to delete " + count + " selected student(s)?\n\nThis action cannot be undone and will delete all their academic and attendance records.")) {
                    bulkDeleteForm.submit();
                }
            });
        }
    });
</script>

<!-- MODAL: ADMISSION FORM DESIGNER & CUSTOM FIELDS -->
<?php
$schoolSettings = $data['settings'] ?? [];
$admSchoolName = !empty($schoolSettings['school_name']) ? $schoolSettings['school_name'] : 'PAKISTAN HIGHER SECONDARY SCHOOL';
$admCampusName = !empty($schoolSettings['campus_name']) ? $schoolSettings['campus_name'] : 'MAIN EXECUTIVE CAMPUS';
$admSchoolAddress = !empty($schoolSettings['school_address']) ? $schoolSettings['school_address'] : 'Education City, Islamabad, Pakistan';
$admSchoolPhone = !empty($schoolSettings['school_phone']) ? $schoolSettings['school_phone'] : '+92-51-111-222-333';
$admSchoolEmail = !empty($schoolSettings['school_email']) ? $schoolSettings['school_email'] : 'admissions@school.edu.pk';
$admAffiliation = !empty($schoolSettings['affiliation_board']) ? $schoolSettings['affiliation_board'] : 'Affiliated with Federal Board (FBISE) / BISE';
$admAcademicSession = !empty($schoolSettings['academic_session']) ? $schoolSettings['academic_session'] : 'Academic Session: 2026 - 2027';
$admFormTitle = !empty($schoolSettings['admission_form_title']) ? $schoolSettings['admission_form_title'] : 'STUDENT ADMISSION & ENROLMENT FORM';
$admCustomFieldsJson = !empty($schoolSettings['admission_custom_fields']) ? $schoolSettings['admission_custom_fields'] : '[]';
?>
<div class="modal fade" id="admissionDesignerModal" tabindex="-1" aria-labelledby="admissionDesignerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="admissionDesignerModalLabel">
                        <i class="fa fa-pen-ruler text-warning me-2"></i>Admission Form Designer &amp; Custom Fields
                    </h5>
                    <div class="text-white-50 small mt-1" style="font-size: 0.76rem;">
                        داخلہ فارم کے ہیڈر، فیلڈز اور نئی معلومات (Custom Fields) یہاں سے ڈیزائن اور محفوظ کریں
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Tab Navigation -->
            <div class="bg-light border-bottom px-4 pt-2">
                <ul class="nav nav-tabs border-bottom-0" id="designerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold small text-primary" id="des-tab-custom-fields" data-bs-toggle="tab" data-bs-target="#des-pane-custom-fields" type="button" role="tab">
                            <i class="fa fa-plus-circle text-danger me-1"></i>➕ Custom Fields (نئی فیلڈز شامل کریں)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold small text-dark" id="des-tab-branding" data-bs-toggle="tab" data-bs-target="#des-pane-branding" type="button" role="tab">
                            <i class="fa fa-school text-info me-1"></i>School Branding (سکول ہیڈر)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold small text-dark" id="des-tab-layout" data-bs-toggle="tab" data-bs-target="#des-pane-layout" type="button" role="tab">
                            <i class="fa fa-layer-group text-success me-1"></i>Form Layout (سیکشنز اور اختیارات)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body p-4 bg-light">
                <div class="tab-content" id="designerTabContent">

                    <!-- TAB 1: CUSTOM FIELDS BUILDER -->
                    <div class="tab-pane fade show active" id="des-pane-custom-fields" role="tabpanel">
                        <div class="card border-primary shadow-sm mb-3">
                            <div class="card-header bg-primary text-white py-2 px-3 d-flex justify-content-between align-items-center">
                                <strong class="small"><i class="fa fa-magic me-1"></i> Add New Particular / Field to Print Form</strong>
                                <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.7rem;">Dynamic Field Builder</span>
                            </div>
                            <div class="card-body p-3 bg-white">
                                <div class="alert alert-info py-2 px-3 small border-0 mb-3" style="font-size: 0.82rem;">
                                    <i class="fa fa-info-circle me-1"></i> <strong>Aapki Marzi Ki Nayi Fields:</strong> Agar aapko admission form par koi aisi information print karni ho jo standard form mein nahi hoti (e.g. <em>Transport Route, Fee Concession, Hostel Room, Emergency Contact, Guardian Relation, Medical Condition</em>), to yahan se add karein. Yeh print sheet par ek khubsoorat table section mein shamil ho jayengi!
                                </div>

                                <!-- Presets -->
                                <div class="mb-3">
                                    <label class="form-label text-uppercase fw-bold text-muted small mb-1" style="font-size: 0.72rem;">Quick Presets (ایک کلک سے شامل کریں):</label>
                                    <div class="d-flex flex-wrap gap-1">
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.75rem; border-radius: 20px;" onclick="setIndexPreset('Transport Route', 'Bus / Van # 5 - Saddar Route')">
                                            <i class="fa fa-bus text-primary me-1"></i> Transport Route
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.75rem; border-radius: 20px;" onclick="setIndexPreset('Fee Concession', 'Kinship / Merit 25% Discount')">
                                            <i class="fa fa-percent text-success me-1"></i> Fee Concession
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.75rem; border-radius: 20px;" onclick="setIndexPreset('Emergency Contact', 'Relative Name & Phone: 0300-1234567')">
                                            <i class="fa fa-phone-volume text-danger me-1"></i> Emergency Contact
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.75rem; border-radius: 20px;" onclick="setIndexPreset('Hostel / Boarding', 'Room # 14 - Jinnah Hostel')">
                                            <i class="fa fa-bed text-secondary me-1"></i> Hostel Room
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.75rem; border-radius: 20px;" onclick="setIndexPreset('Guardian Relation', 'Uncle / Maternal Guardian')">
                                            <i class="fa fa-user-shield text-info me-1"></i> Guardian Relation
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size: 0.75rem; border-radius: 20px;" onclick="setIndexPreset('Medical / Allergy', 'Asthma / Dust Allergy')">
                                            <i class="fa fa-heartbeat text-warning me-1"></i> Medical Alert
                                        </button>
                                    </div>
                                </div>

                                <!-- Add input row -->
                                <div class="row g-2 mb-2">
                                    <div class="col-12 col-md-5">
                                        <label class="form-label small fw-bold text-dark mb-1">Field Name / Label (عنوان)</label>
                                        <input type="text" id="idxFieldLabel" class="form-control form-control-sm fw-semibold" placeholder="e.g. Transport Route">
                                    </div>
                                    <div class="col-12 col-md-7">
                                        <label class="form-label small fw-bold text-dark mb-1">Default Value / Placeholder (تفصیل)</label>
                                        <input type="text" id="idxFieldValue" class="form-control form-control-sm" placeholder="e.g. Route # 4 - Saddar / ____________________">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm py-2 mt-1" onclick="addCustomFieldFromIndex()">
                                    <i class="fa fa-plus-circle me-1"></i> Add This Field to Form / فیلڈ شامل کریں
                                </button>
                            </div>
                        </div>

                        <!-- Active Custom Fields Table -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-2 px-3 d-flex justify-content-between align-items-center">
                                <strong class="small text-dark"><i class="fa fa-list-check text-primary me-1"></i> Active Custom Fields on Print Form (<span id="idxCustomCount">0</span>)</strong>
                                <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0 small" onclick="clearAllIndexCustomFields()" id="idxBtnClearAll" style="display: none;">
                                    <i class="fa fa-trash me-1"></i> Clear All
                                </button>
                            </div>
                            <div class="card-body p-3 bg-white">
                                <div id="idxCustomFieldsList">
                                    <div class="text-center text-muted small py-3" id="idxNoFieldsMsg">
                                        <i class="fa fa-info-circle text-muted fs-5 mb-1 d-block"></i>
                                        Abhi koi custom field shamil nahi ki gayi.<br>Upar se field name aur value daal kar "+ Add" dabayein.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: SCHOOL BRANDING & HEADER INFO -->
                    <div class="tab-pane fade" id="des-pane-branding" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3 bg-white">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">School Name / ادارہ کا نام</label>
                                    <input type="text" id="idxSchoolName" class="form-control fw-bold" value="<?php echo htmlspecialchars($admSchoolName); ?>" placeholder="e.g. PAKISTAN MODEL HIGH SCHOOL">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Campus Name / Branch / کیمپس</label>
                                    <input type="text" id="idxCampusName" class="form-control" value="<?php echo htmlspecialchars($admCampusName); ?>" placeholder="e.g. MAIN EXECUTIVE CAMPUS">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">Affiliation / Board / الحاق</label>
                                    <input type="text" id="idxAffiliation" class="form-control" value="<?php echo htmlspecialchars($admAffiliation); ?>" placeholder="e.g. Affiliated with Federal Board (FBISE) / BISE">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase mb-1">School Address / پتہ</label>
                                    <input type="text" id="idxAddress" class="form-control" value="<?php echo htmlspecialchars($admSchoolAddress); ?>" placeholder="e.g. Education City, Islamabad, Pakistan">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Phone / Helpline</label>
                                        <input type="text" id="idxPhone" class="form-control" value="<?php echo htmlspecialchars($admSchoolPhone); ?>" placeholder="+92-51-111-222-333">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Email / ای میل</label>
                                        <input type="text" id="idxEmail" class="form-control" value="<?php echo htmlspecialchars($admSchoolEmail); ?>" placeholder="admissions@school.edu.pk">
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Academic Session / تعلیمی سال</label>
                                        <input type="text" id="idxSession" class="form-control" value="<?php echo htmlspecialchars($admAcademicSession); ?>" placeholder="e.g. Session: 2026 - 2027">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Form Main Title / فارم کا عنوان</label>
                                        <input type="text" id="idxTitle" class="form-control" value="<?php echo htmlspecialchars($admFormTitle); ?>" placeholder="STUDENT ADMISSION &amp; ENROLMENT FORM">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: SECTIONS & LAYOUT OPTIONS -->
                    <div class="tab-pane fade" id="des-pane-layout" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3 bg-white">
                                <h6 class="fw-bold small text-dark mb-3"><i class="fa fa-sliders text-primary me-2"></i>Choose Sections to Include on A4 Print:</h6>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="chkOfficeUse" checked>
                                    <label class="form-check-label small fw-semibold" for="chkOfficeUse">
                                        Office Use Only Box (برائے دفتری استعمال: Admission No, Date, Class, Section, Roll No)
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="chkChecklist" checked>
                                    <label class="form-check-label small fw-semibold" for="chkChecklist">
                                        Section E: Mandatory Document Checklist (ضروری دستاویزات کی فہرست)
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="chkUndertaking" checked>
                                    <label class="form-check-label small fw-semibold" for="chkUndertaking">
                                        Section F: Parent Solemn Undertaking &amp; Declaration (اقرار نامہ والد / سرپرست)
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="chkSignatures" checked>
                                    <label class="form-check-label small fw-semibold" for="chkSignatures">
                                        Section G: 4-Tier Official Signatures (Parent, Incharge, Accounts, Principal Seal)
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="chkCnicBoxes" checked>
                                    <label class="form-check-label small fw-semibold" for="chkCnicBoxes">
                                        13-Digit Pakistani NADRA CNIC / Form-B Separate Boxes (13 خانے)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white border-top py-2 px-4 d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="resetIndexDesignerDefaults()">
                    <i class="fa fa-undo me-1"></i> Reset Defaults
                </button>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary btn-sm px-3 fw-bold" onclick="saveIndexDesignerSettings(false)">
                        <i class="fa fa-save me-1"></i> Save Settings
                    </button>
                    <button type="button" class="btn btn-success btn-sm px-3 fw-bold shadow-sm" onclick="saveIndexDesignerSettings(true)">
                        <i class="fa fa-print me-1"></i> Save &amp; Print Blank Form
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let indexCustomFields = [];

document.addEventListener('DOMContentLoaded', function() {
    initIndexDesigner();
});

function initIndexDesigner() {
    // 1. Load custom fields from localStorage or server
    try {
        const stored = localStorage.getItem('admission_custom_fields');
        if (stored) {
            indexCustomFields = JSON.parse(stored);
        } else {
            <?php if(!empty($admCustomFieldsJson) && $admCustomFieldsJson !== '[]'): ?>
                indexCustomFields = <?php echo $admCustomFieldsJson; ?>;
            <?php else: ?>
                indexCustomFields = [];
            <?php endif; ?>
        }
    } catch(e) {
        indexCustomFields = [];
    }
    renderIndexCustomFields();

    // 2. Load branding from localStorage
    const bRaw = localStorage.getItem('admission_form_branding');
    if (bRaw) {
        try {
            const b = JSON.parse(bRaw);
            if (b.schoolName && document.getElementById('idxSchoolName')) document.getElementById('idxSchoolName').value = b.schoolName;
            if (b.campusName && document.getElementById('idxCampusName')) document.getElementById('idxCampusName').value = b.campusName;
            if (b.affiliation && document.getElementById('idxAffiliation')) document.getElementById('idxAffiliation').value = b.affiliation;
            if (b.address && document.getElementById('idxAddress')) document.getElementById('idxAddress').value = b.address;
            if (b.phone && document.getElementById('idxPhone')) document.getElementById('idxPhone').value = b.phone;
            if (b.email && document.getElementById('idxEmail')) document.getElementById('idxEmail').value = b.email;
            if (b.session && document.getElementById('idxSession')) document.getElementById('idxSession').value = b.session;
            if (b.title && document.getElementById('idxTitle')) document.getElementById('idxTitle').value = b.title;
        } catch(e) {}
    }

    // 3. Load layout options
    const lRaw = localStorage.getItem('admission_layout_options');
    if (lRaw) {
        try {
            const l = JSON.parse(lRaw);
            if (l.chkOfficeUse !== undefined && document.getElementById('chkOfficeUse')) document.getElementById('chkOfficeUse').checked = l.chkOfficeUse;
            if (l.chkChecklist !== undefined && document.getElementById('chkChecklist')) document.getElementById('chkChecklist').checked = l.chkChecklist;
            if (l.chkUndertaking !== undefined && document.getElementById('chkUndertaking')) document.getElementById('chkUndertaking').checked = l.chkUndertaking;
            if (l.chkSignatures !== undefined && document.getElementById('chkSignatures')) document.getElementById('chkSignatures').checked = l.chkSignatures;
            if (l.chkCnicBoxes !== undefined && document.getElementById('chkCnicBoxes')) document.getElementById('chkCnicBoxes').checked = l.chkCnicBoxes;
        } catch(e) {}
    }
}

function setIndexPreset(label, val) {
    const lInp = document.getElementById('idxFieldLabel');
    const vInp = document.getElementById('idxFieldValue');
    if (lInp) lInp.value = label;
    if (vInp) {
        vInp.value = '';
        vInp.placeholder = 'e.g. ' + val;
        vInp.focus();
    }
}

function addCustomFieldFromIndex() {
    const lInp = document.getElementById('idxFieldLabel');
    const vInp = document.getElementById('idxFieldValue');
    const label = lInp ? lInp.value.trim() : '';
    const val = vInp ? vInp.value.trim() : '';

    if (!label) {
        alert('Baraye meherbani field ka label / name darj karein.');
        if (lInp) lInp.focus();
        return;
    }

    const field = {
        id: 'cf_' + Date.now() + '_' + Math.floor(Math.random()*1000),
        label: label,
        value: val || '____________________'
    };

    indexCustomFields.push(field);
    renderIndexCustomFields();

    if (lInp) lInp.value = '';
    if (vInp) {
        vInp.value = '';
        vInp.placeholder = 'e.g. Route # 4 - Saddar Stop';
    }
}

function removeIndexCustomField(id) {
    indexCustomFields = indexCustomFields.filter(f => f.id !== id);
    renderIndexCustomFields();
}

function clearAllIndexCustomFields() {
    if (confirm('Kya aap waqai sabhi custom fields khatam karna chahte hain?')) {
        indexCustomFields = [];
        renderIndexCustomFields();
    }
}

function renderIndexCustomFields() {
    const container = document.getElementById('idxCustomFieldsList');
    const countBadge = document.getElementById('idxCustomCount');
    const clearBtn = document.getElementById('idxBtnClearAll');

    if (countBadge) countBadge.textContent = indexCustomFields.length;
    if (clearBtn) clearBtn.style.display = indexCustomFields.length > 0 ? 'inline-block' : 'none';

    if (!container) return;

    if (indexCustomFields.length === 0) {
        container.innerHTML = '<div class="text-center text-muted small py-3" id="idxNoFieldsMsg">' +
            '<i class="fa fa-info-circle text-muted fs-5 mb-1 d-block"></i>' +
            'Abhi koi custom field shamil nahi ki gayi.<br>Upar se field name aur value daal kar "+ Add" dabayein.</div>';
    } else {
        let html = '';
        indexCustomFields.forEach(f => {
            html += '<div class="d-flex align-items-center justify-content-between p-2 mb-2 bg-light border rounded">' +
                '<div class="d-flex align-items-center gap-2 text-truncate" style="flex: 1;">' +
                    '<span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1"><i class="fa fa-tag me-1"></i>' + escapeHtmlText(f.label) + '</span>' +
                    '<span class="text-dark small fw-semibold text-truncate">' + escapeHtmlText(f.value) + '</span>' +
                '</div>' +
                '<button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" title="Remove Field" onclick="removeIndexCustomField(\'' + f.id + '\')">' +
                    '<i class="fa fa-times"></i>' +
                '</button>' +
            '</div>';
        });
        container.innerHTML = html;
    }
}

function escapeHtmlText(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function saveIndexDesignerSettings(andOpenPrint = false) {
    const branding = {
        schoolName: document.getElementById('idxSchoolName')?.value.trim() || '',
        campusName: document.getElementById('idxCampusName')?.value.trim() || '',
        affiliation: document.getElementById('idxAffiliation')?.value.trim() || '',
        address: document.getElementById('idxAddress')?.value.trim() || '',
        phone: document.getElementById('idxPhone')?.value.trim() || '',
        email: document.getElementById('idxEmail')?.value.trim() || '',
        session: document.getElementById('idxSession')?.value.trim() || '',
        title: document.getElementById('idxTitle')?.value.trim() || ''
    };

    const layoutOptions = {
        chkOfficeUse: document.getElementById('chkOfficeUse')?.checked ?? true,
        chkChecklist: document.getElementById('chkChecklist')?.checked ?? true,
        chkUndertaking: document.getElementById('chkUndertaking')?.checked ?? true,
        chkSignatures: document.getElementById('chkSignatures')?.checked ?? true,
        chkCnicBoxes: document.getElementById('chkCnicBoxes')?.checked ?? true
    };

    // 1. Save to localStorage
    localStorage.setItem('admission_custom_fields', JSON.stringify(indexCustomFields));
    localStorage.setItem('admission_form_branding', JSON.stringify(branding));
    localStorage.setItem('admission_layout_options', JSON.stringify(layoutOptions));

    // 2. Also save to server via AJAX
    const postPayload = {
        custom_fields: indexCustomFields,
        branding: {
            school_name: branding.schoolName,
            campus_name: branding.campusName,
            affiliation_board: branding.affiliation,
            school_address: branding.address,
            school_phone: branding.phone,
            school_email: branding.email,
            academic_session: branding.session,
            admission_form_title: branding.title
        },
        layout_options: layoutOptions
    };

    fetch('<?php echo URLROOT; ?>/students/saveAdmissionFormSettings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(postPayload)
    }).catch(err => console.log('Saved locally'));

    // Close modal
    const modalEl = document.getElementById('admissionDesignerModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    if (andOpenPrint) {
        window.open('<?php echo URLROOT; ?>/students/printAdmission?blank=1', '_blank');
    } else {
        alert('Admission Form settings aur custom fields kamiyabi se save ho chuki hain!\nAb aap "Blank Form" print karein ge to yeh tamam settings shamil hongi.');
    }
}

function resetIndexDesignerDefaults() {
    if (confirm('Kya aap waqai sabhi settings aur custom fields default par reset karna chahte hain?')) {
        localStorage.removeItem('admission_custom_fields');
        localStorage.removeItem('admission_form_branding');
        localStorage.removeItem('admission_layout_options');
        location.reload();
    }
}
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
