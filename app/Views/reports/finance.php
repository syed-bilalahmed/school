<?php require APPROOT . '/Views/layouts/header.php'; ?>

<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/reports/index">Reports Center</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Finance &amp; Collections</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-money-bill-wave text-success me-2"></i>Institutional Finance &amp; Collections Report
            </h2>
            <small class="text-muted">Audit tuition fee remittances, payment modes, and operational disbursement trends.</small>
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

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="<?php echo URLROOT; ?>/reports/finance" method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo $data['from_date']; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo $data['to_date']; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Payment Mode</label>
                    <select name="payment_mode" class="form-select">
                        <option value="">-- All Modes --</option>
                        <option value="Cash" <?php echo ($data['mode'] === 'Cash') ? 'selected' : ''; ?>>Cash</option>
                        <option value="Bank Transfer" <?php echo ($data['mode'] === 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer / Challan</option>
                        <option value="Cheque" <?php echo ($data['mode'] === 'Cheque') ? 'selected' : ''; ?>>Cheque</option>
                        <option value="Online" <?php echo ($data['mode'] === 'Online') ? 'selected' : ''; ?>>Online / Gateway</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-filter me-1"></i> Filter Collections
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Total Collections</div>
                <div class="h3 fw-bold text-success mb-0 mt-1"><?php echo htmlspecialchars($data['currency']); ?> <?php echo number_format($data['total_collected'], 2); ?></div>
                <small class="text-muted"><?php echo count($data['payments']); ?> receipts logged</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Cash Receipts</div>
                <div class="h3 fw-bold text-primary mb-0 mt-1"><?php echo htmlspecialchars($data['currency']); ?> <?php echo number_format($data['total_cash'], 2); ?></div>
                <small class="text-muted">On-counter counter cash</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Bank / Challan</div>
                <div class="h3 fw-bold text-info mb-0 mt-1"><?php echo htmlspecialchars($data['currency']); ?> <?php echo number_format($data['total_bank'], 2); ?></div>
                <small class="text-muted">Direct bank &amp; online</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="small text-muted fw-bold text-uppercase">Period Expenses</div>
                <div class="h3 fw-bold text-danger mb-0 mt-1"><?php echo htmlspecialchars($data['currency']); ?> <?php echo number_format($data['total_expenses'], 2); ?></div>
                <small class="text-muted">Operational disbursements</small>
            </div>
        </div>
    </div>

    <!-- Payments Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0">Fee Collection Journal</h5>
            <span class="badge bg-light text-dark border px-3 py-1"><?php echo count($data['payments']); ?> Receipts</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Receipt #</th>
                            <th>Date</th>
                            <th>Admission #</th>
                            <th>Student Name</th>
                            <th>Fee Group / Type</th>
                            <th>Mode</th>
                            <th class="text-end pe-4">Amount (<?php echo htmlspecialchars($data['currency']); ?>)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['payments'])): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa fa-receipt fa-3x mb-3 text-secondary opacity-50"></i>
                                    <div>No fee receipts found for the selected period.</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['payments'] as $p): ?>
                                <tr>
                                    <td class="ps-4 font-monospace fw-bold text-primary">#<?php echo $p->id; ?></td>
                                    <td><?php echo date('d M, Y', strtotime($p->date)); ?></td>
                                    <td><span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($p->admission_no ?? '-'); ?></span></td>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($p->student_name); ?></td>
                                    <td><?php echo htmlspecialchars(($p->fee_group ?? 'Tuition') . ' - ' . ($p->fee_type ?? 'Monthly')); ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($p->mode ?? 'Cash'); ?></span>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-success"><?php echo number_format($p->amount, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="table-light fw-bold">
                                <td colspan="6" class="text-end py-3">Total Period Collections:</td>
                                <td class="text-end pe-4 py-3 text-success h6 mb-0"><?php echo htmlspecialchars($data['currency']); ?> <?php echo number_format($data['total_collected'], 2); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

