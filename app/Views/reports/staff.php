<?php require APPROOT . '/Views/layouts/header.php'; ?>

<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/reports/index">Reports Center</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Staff HR &amp; Faculty Workload</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-users text-primary me-2"></i>Staff &amp; Faculty Workload Report
            </h2>
            <small class="text-muted">Review institutional personnel, department allocations, remuneration models, and employment classifications.</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print();" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-print me-1"></i> Print Report
            </button>
            <a href="<?php echo URLROOT; ?>/reports/index" class="btn btn-primary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Reports Center
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="<?php echo URLROOT; ?>/reports/staff" method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Department</label>
                    <input type="text" name="department" class="form-control" placeholder="e.g. Science, Mathematics" value="<?php echo htmlspecialchars($data['department']); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Employment Type</label>
                    <select name="employment_type" class="form-select">
                        <option value="">-- All Types --</option>
                        <option value="Permanent" <?php echo ($data['employment_type'] === 'Permanent') ? 'selected' : ''; ?>>Permanent</option>
                        <option value="Visiting" <?php echo ($data['employment_type'] === 'Visiting') ? 'selected' : ''; ?>>Visiting Faculty</option>
                        <option value="Contract" <?php echo ($data['employment_type'] === 'Contract') ? 'selected' : ''; ?>>Contractual</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="Active" <?php echo ($data['status'] === 'Active') ? 'selected' : ''; ?>>Active Only</option>
                        <option value="All" <?php echo ($data['status'] === 'All') ? 'selected' : ''; ?>>All Personnel</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary KPI Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Total Personnel</div>
                <div class="h3 fw-bold text-dark mb-0 mt-1"><?php echo $data['total_staff']; ?></div>
                <small class="text-muted">Registered faculty &amp; staff</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Permanent Staff</div>
                <div class="h3 fw-bold text-primary mb-0 mt-1"><?php echo $data['total_permanent']; ?></div>
                <small class="text-muted">Fixed salary faculty</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Visiting Faculty</div>
                <div class="h3 fw-bold text-info mb-0 mt-1"><?php echo $data['total_visiting']; ?></div>
                <small class="text-muted">Per-lecture workload</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Monthly Base Payroll</div>
                <div class="h3 fw-bold text-success mb-0 mt-1"><?php echo htmlspecialchars($data['currency']); ?> <?php echo number_format($data['monthly_payroll_estimate']); ?></div>
                <small class="text-muted">Permanent staff basic total</small>
            </div>
        </div>
    </div>

    <!-- Staff Directory Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0">Personnel Directory &amp; Remuneration Schedule</h5>
            <span class="badge bg-light text-dark border px-3 py-1"><?php echo count($data['staff_list']); ?> Staff</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">S#</th>
                            <th>Code</th>
                            <th>Full Name &amp; Contact</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Employment Type</th>
                            <th class="text-end">Basic Salary / Rate</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['staff_list'])): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa fa-user-slash fa-3x mb-3 text-secondary opacity-50"></i>
                                    <div>No staff profiles match the chosen criteria.</div>
                                </td>
                            </tr>
                        <?php else: 
                            $idx = 1;
                            foreach($data['staff_list'] as $st): 
                                $isVisiting = (strcasecmp($st->employment_type ?? '', 'Visiting') === 0);
                        ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?php echo $idx++; ?></td>
                                <td><span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($st->staff_code ?? 'EMP-' . $st->id); ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($st->name); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($st->email ?? $st->phone ?? ''); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($st->department ?? 'General'); ?></td>
                                <td><?php echo htmlspecialchars($st->designation ?? 'Staff Member'); ?></td>
                                <td>
                                    <?php if($isVisiting): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1">Visiting Faculty</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1">Permanent</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold">
                                    <?php if($isVisiting): ?>
                                        <span class="text-info"><?php echo htmlspecialchars($data['currency']); ?> <?php echo number_format($st->lecture_rate ?? 0); ?> / lect</span>
                                    <?php else: ?>
                                        <span class="text-success"><?php echo htmlspecialchars($data['currency']); ?> <?php echo number_format($st->basic_salary ?? 0); ?> / mo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1"><?php echo htmlspecialchars($st->status ?? 'Active'); ?></span>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
