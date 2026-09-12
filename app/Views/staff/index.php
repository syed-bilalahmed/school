<?php require APPROOT . '/Views/layouts/header.php'; ?>

<?php
$staffList = $data['staff'] ?? [];
$totalStaff = count($staffList);
$teacherCount = 0;
$adminCount = 0;
$visitingCount = 0;

foreach($staffList as $stf){
    if($stf->role == 'teacher') $teacherCount++;
    if(in_array($stf->role, ['admin', 'super_admin', 'accountant'])) $adminCount++;
    if(strpos($stf->employment_type ?? '', 'Visiting') !== false) $visitingCount++;
}
?>

<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small" style="font-size: 0.78rem;">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Staff Directory</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-dark fs-5">Staff &amp; Faculty Directory</h4>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/payroll/index" class="btn btn-outline-success btn-sm px-2 py-1 shadow-xs" style="font-size: 0.82rem;">
            <i class="fa fa-money-check-dollar me-1"></i> Staff Payroll
        </a>
        <a href="<?php echo URLROOT; ?>/subjects/assign" class="btn btn-outline-primary btn-sm px-2 py-1 shadow-xs" style="font-size: 0.82rem;">
            <i class="fa fa-user-check me-1"></i> Workload
        </a>
        <a href="<?php echo URLROOT; ?>/staff/create" class="btn btn-primary btn-sm px-3 py-1 shadow-sm" style="font-size: 0.82rem;">
            <i class="fa fa-user-plus me-1"></i> Add New Staff
        </a>
    </div>
</div>

<!-- Alert Messages -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-3 small" role="alert">
        <i class="fa fa-check-circle"></i>
        <div>
            <?php 
                if($_GET['success'] == 'created') echo "New staff member registered and user credentials generated successfully!";
                elseif($_GET['success'] == 'updated') echo "Staff profile details updated successfully!";
                elseif($_GET['success'] == 'deleted') echo "Staff member marked as resigned/inactive.";
                else echo "Action completed successfully!";
            ?>
        </div>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- 4 Executive KPI Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-users-gear"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Enrolled Staff</div>
                    <h6 class="fw-bold mb-0 text-dark fs-6"><?php echo $totalStaff; ?> <small class="text-muted fw-normal" style="font-size: 0.75rem;">Members</small></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 bg-info-subtle text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Teaching Faculty</div>
                    <h6 class="fw-bold mb-0 text-info fs-6"><?php echo $teacherCount; ?> <small class="text-muted fw-normal" style="font-size: 0.75rem;">Teachers</small></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-user-shield"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Admin &amp; Accounts</div>
                    <h6 class="fw-bold mb-0 text-success fs-6"><?php echo $adminCount; ?> <small class="text-muted fw-normal" style="font-size: 0.75rem;">Staff</small></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem; color: #7c3aed; background: rgba(124, 58, 237, 0.1);">
                    <i class="fa fa-person-chalkboard"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Visiting Faculty</div>
                    <h6 class="fw-bold mb-0 fs-6" style="color: #7c3aed;"><?php echo $visitingCount; ?> <small class="text-muted fw-normal" style="font-size: 0.75rem;">Lecturers</small></h6>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Single-Line Filter Toolbar -->
<div class="card shadow-sm border-0 mb-3 bg-white">
    <div class="card-body py-2 px-3">
        <form action="<?php echo URLROOT; ?>/staff/index" method="get" class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-semibold text-muted"><i class="fa fa-filter me-1"></i> Filter Directory:</span>
                <select name="dept" class="form-select form-select-sm w-auto py-1" style="font-size: 0.85rem;" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    <?php 
                    $depts = ['Academics', 'Science', 'Humanities', 'Mathematics', 'English', 'Administration', 'Finance & Accounts', 'Sports & Physical'];
                    foreach($depts as $d): ?>
                        <option value="<?php echo $d; ?>" <?php echo ($data['current_dept'] == $d) ? 'selected' : ''; ?>><?php echo $d; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="role" class="form-select form-select-sm w-auto py-1" style="font-size: 0.85rem;" onchange="this.form.submit()">
                    <option value="">All System Roles</option>
                    <option value="teacher" <?php echo ($data['current_role'] == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
                    <option value="admin" <?php echo ($data['current_role'] == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                    <option value="accountant" <?php echo ($data['current_role'] == 'accountant') ? 'selected' : ''; ?>>Accountant</option>
                    <option value="librarian" <?php echo ($data['current_role'] == 'librarian') ? 'selected' : ''; ?>>Librarian</option>
                    <option value="receptionist" <?php echo ($data['current_role'] == 'receptionist') ? 'selected' : ''; ?>>Receptionist</option>
                </select>
                <select name="etype" class="form-select form-select-sm w-auto py-1" style="font-size: 0.85rem;" onchange="this.form.submit()">
                    <option value="">All Employment Types</option>
                    <option value="Permanent" <?php echo ($data['current_etype'] == 'Permanent') ? 'selected' : ''; ?>>Permanent</option>
                    <option value="Visiting / Per Lecture" <?php echo ($data['current_etype'] == 'Visiting / Per Lecture') ? 'selected' : ''; ?>>Visiting Faculty</option>
                    <option value="Contract" <?php echo ($data['current_etype'] == 'Contract') ? 'selected' : ''; ?>>Contract</option>
                </select>
            </div>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="<?php echo URLROOT; ?>/staff/index" class="btn btn-sm btn-light border text-secondary px-2 py-1" style="font-size: 0.82rem;" title="Reset Filters">
                    <i class="fa fa-undo me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Staff Cards / Table Directory -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 border-bottom d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-users-gear text-primary fs-5"></i>
            <h5 class="fw-bold mb-0 text-dark">Faculty &amp; Staff Members</h5>
        </div>
        <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 ms-sm-auto" style="border-radius: 30px;">
            <?php echo count($staffList); ?> Enrolled Staff
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="width: 100%; table-layout: fixed;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3" style="width: 22%;">Staff Member</th>
                        <th style="width: 10%;">Staff Code</th>
                        <th style="width: 18%;">Dept &amp; Title</th>
                        <th style="width: 14%;">Type &amp; Role</th>
                        <th style="width: 12%;">Phone &amp; CNIC</th>
                        <th style="width: 12%;">Salary / Rate</th>
                        <th class="text-end pe-3" style="width: 12%;">Quick Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($staffList)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa fa-user-slash fa-2x mb-2 d-block opacity-50"></i>
                                No faculty or staff members matching the filter criteria.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($staffList as $stf): 
                            $isVisiting = strpos($stf->employment_type ?? '', 'Visiting') !== false;
                        ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="<?php echo URLROOT; ?>/staff/profile/<?php echo $stf->id; ?>" class="text-decoration-none">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($stf->name); ?>&background=random&size=36&bold=true" class="rounded-circle shadow-xs flex-shrink-0" width="32" height="32" alt="">
                                        </a>
                                        <div class="text-truncate" style="max-width: calc(100% - 40px);">
                                            <div class="fw-bold text-dark text-truncate">
                                                <a href="<?php echo URLROOT; ?>/staff/profile/<?php echo $stf->id; ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?php echo htmlspecialchars($stf->name); ?>
                                                </a>
                                            </div>
                                            <small class="text-muted text-truncate d-block"><?php echo htmlspecialchars($stf->email); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary font-monospace text-truncate d-inline-block" style="max-width: 100%; font-size: 0.78rem;">
                                        <?php echo htmlspecialchars($stf->staff_code); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 100%; font-size: 0.85rem;"><?php echo htmlspecialchars($stf->designation ?: 'Staff'); ?></div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 100%; font-size: 0.75rem;"><?php echo htmlspecialchars($stf->department ?: 'Academics'); ?></small>
                                </td>
                                <td>
                                    <?php if($isVisiting): ?>
                                        <span class="badge bg-purple-subtle text-purple border px-2 py-0 text-truncate d-inline-block" style="color: #7c3aed; background: rgba(124, 58, 237, 0.1); font-size: 0.72rem; max-width: 100%;">
                                            Visiting
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border px-2 py-0 text-truncate d-inline-block" style="font-size: 0.72rem; max-width: 100%;">
                                            <?php echo htmlspecialchars($stf->employment_type ?? 'Permanent'); ?>
                                        </span>
                                    <?php endif; ?>
                                    <small class="text-muted text-capitalize d-block text-truncate" style="font-size: 0.72rem; max-width: 100%;"><?php echo htmlspecialchars($stf->role); ?></small>
                                </td>
                                <td>
                                    <?php if(!empty($stf->phone)): ?>
                                        <a href="tel:<?php echo htmlspecialchars($stf->phone); ?>" class="text-decoration-none text-dark small fw-bold d-block text-truncate">
                                            <i class="fa fa-phone me-1 text-success"></i><?php echo htmlspecialchars($stf->phone); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">N/A</span>
                                    <?php endif; ?>
                                    <?php if(!empty($stf->cnic)): ?>
                                        <div class="font-monospace text-muted smaller text-truncate" style="font-size: 0.72rem;"><?php echo htmlspecialchars($stf->cnic); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($isVisiting): ?>
                                        <span class="fw-bold small" style="color: #7c3aed;">Rs. <?php echo number_format((float)$stf->lecture_rate); ?></span>
                                        <small class="text-muted d-block smaller">/ Lecture</small>
                                    <?php else: ?>
                                        <span class="fw-bold text-dark small">Rs. <?php echo number_format((float)($stf->net_salary ?: $stf->basic_salary)); ?></span>
                                        <small class="text-muted d-block smaller">/ Month</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <a href="<?php echo URLROOT; ?>/staff/profile/<?php echo $stf->id; ?>" class="btn btn-sm btn-outline-primary px-2 py-1" style="font-size: 0.78rem;" title="View 360 Profile">
                                            <i class="fa fa-id-card"></i>
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/payroll/structure/<?php echo $stf->id; ?>" class="btn btn-sm btn-outline-success px-2 py-1" style="font-size: 0.78rem;" title="Configure Salary Structure">
                                            <i class="fa fa-coins"></i>
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/staff/edit/<?php echo $stf->id; ?>" class="btn btn-sm btn-outline-secondary px-2 py-1" style="font-size: 0.78rem;" title="Edit Details">
                                            <i class="fa fa-edit"></i>
                                        </a>
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

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
