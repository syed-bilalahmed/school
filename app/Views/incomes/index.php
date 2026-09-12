<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h4 fw-bold mb-1 text-dark">
            <i class="fa fa-hand-holding-dollar text-primary me-2"></i>Accounts: Incomes &amp; Cash Book
        </h2>
        <div class="text-muted small">Real-time accounting ledger recording student fee collections, miscellaneous income, and daily closing balances.</div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/expense/index" class="btn btn-outline-danger btn-sm" style="border-radius: 8px;">
            <i class="fa fa-arrow-trend-down me-1"></i> Expenses Ledger
        </a>
        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-outline-primary btn-sm" style="border-radius: 8px;">
            <i class="fa fa-cash-register me-1"></i> Fee Collection Hub
        </a>
    </div>
</div>

<!-- Alert Notifications -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px;" role="alert">
        <i class="fa fa-check-circle me-2"></i>
        <?php 
            if($_GET['success'] == 'created') echo "Income record added successfully!";
            elseif($_GET['success'] == 'deleted') echo "Income record removed.";
            else echo "Action completed successfully!";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Daily Closing KPI Cards -->
<?php 
$closing = $data['closing'];
$netClosing = $closing['net_closing'];
$isPositive = $netClosing >= 0;
?>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3" style="border-radius: 14px;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.25rem;">
                    <i class="fa fa-arrow-down-long"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Today's Inflow (Income)</div>
                    <h4 class="fw-bold mb-0 text-success">Rs. <?php echo number_format($closing['total_income'], 2); ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3" style="border-radius: 14px;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.25rem;">
                    <i class="fa fa-arrow-up-long"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Today's Outflow (Expense)</div>
                    <h4 class="fw-bold mb-0 text-danger">Rs. <?php echo number_format($closing['total_expense'], 2); ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3" style="border-radius: 14px;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 <?php echo $isPositive ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning'; ?> d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.25rem;">
                    <i class="fa fa-scale-balanced"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Daily Net Closing Balance</div>
                    <h4 class="fw-bold mb-0 <?php echo $isPositive ? 'text-primary' : 'text-danger'; ?>">
                        Rs. <?php echo number_format($netClosing, 2); ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Add Manual Income Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-plus-circle text-primary me-2"></i>Record Other Income
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo URLROOT; ?>/incomes/add" method="post">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Title / Head <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Admission Prospectus, Canteen Rent" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Category</label>
                        <select name="category" class="form-select" style="border-radius: 8px;">
                            <option value="Admission Fee">Admission Fee</option>
                            <option value="Transport Fee">Transport Fee</option>
                            <option value="Prospectus Sale">Prospectus / Form Sale</option>
                            <option value="Donation">Donation / Grant</option>
                            <option value="Canteen / Stall Rent">Canteen / Stall Rent</option>
                            <option value="Other" selected>Other Income</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Amount (<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?>) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required style="border-radius: 8px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Date</label>
                            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Payment Mode</label>
                            <select name="payment_mode" class="form-select" style="border-radius: 8px;">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Online">Online / Card</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Ref / Receipt #</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="e.g. REC-102" style="border-radius: 8px;">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Notes</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Optional notes or details..." style="border-radius: 8px;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" style="border-radius: 8px;">
                        <i class="fa fa-save me-1"></i> Record Income
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Incomes Ledger Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-receipt text-primary me-2"></i>Income Transactions Ledger
                </h6>
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2" style="border-radius: 30px;">
                    <?php echo count($data['incomes']); ?> Entries
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Title / Source</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Date &amp; Mode</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['incomes'])): ?>
                                <?php foreach($data['incomes'] as $inc): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($inc->title); ?></div>
                                            <?php if(!empty($inc->fee_payment_id)): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">
                                                    <i class="fa fa-sync-alt me-1"></i> Auto-Synced from Fee Payment
                                                </span>
                                            <?php elseif(!empty($inc->reference_no)): ?>
                                                <small class="text-muted">Ref: <?php echo htmlspecialchars($inc->reference_no); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($inc->category); ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success fs-6">+<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> <?php echo number_format($inc->amount, 2); ?></span>
                                        </td>
                                        <td>
                                            <div><?php echo date('d M Y', strtotime($inc->date)); ?></div>
                                            <span class="badge bg-light text-secondary border"><?php echo htmlspecialchars($inc->payment_mode); ?></span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <?php if(empty($inc->fee_payment_id)): ?>
                                                <a href="<?php echo URLROOT; ?>/incomes/delete/<?php echo $inc->id; ?>" 
                                                   class="btn btn-sm btn-outline-danger border-0"
                                                   onclick="return confirm('Remove this income entry?');"
                                                   title="Delete Entry" style="border-radius: 6px;">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small" title="Synced from fees &mdash; manage in fees register"><i class="fa fa-lock"></i></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa fa-hand-holding-dollar fa-3x mb-3 text-secondary opacity-25"></i>
                                        <p class="mb-0">No income records logged yet.</p>
                                        <small>Fee collections will automatically sync here, or you can record other revenue on the left.</small>
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
