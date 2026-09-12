<?php 
require APPROOT . '/Views/layouts/header.php'; 

$student = $data['student'] ?? null;
$studentName = $student->name ?? 'Student #' . $data['student_id'];
$admNo = $student->admission_no ?? '-';
$className = $student->class_name ?? 'N/A';
$sectionName = $student->section_name ?? 'General';
$parentPhone = $student->parent_phone ?? 'N/A';
$initials = strtoupper(substr($studentName, 0, 2));

// Calculate totals
$totalAssigned = 0;
$totalPaid = 0;
$totalBalance = 0;
foreach($data['fees'] as $fee){
    $b = ($fee->amount + $fee->fine_amount) - ($fee->total_paid + $fee->total_discount);
    $totalAssigned += (float)$fee->amount;
    $totalPaid += (float)$fee->total_paid;
    $totalBalance += max(0, $b);
}
?>

<!-- HEADER & STUDENT PROFILE CARD -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
            <i class="fa fa-arrow-left me-1"></i> Back to Register
        </a>
        <div>
            <h2 class="h4 fw-bold mb-0 text-dark">
                Collect Fees &mdash; <?php echo htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8'); ?>
            </h2>
            <div class="text-muted small">
                Adm No: <span class="fw-bold font-monospace">#<?php echo htmlspecialchars($admNo, ENT_QUOTES, 'UTF-8'); ?></span> &bull; 
                Class: <span class="text-primary fw-bold"><?php echo htmlspecialchars($className . ' - ' . $sectionName, ENT_QUOTES, 'UTF-8'); ?></span> &bull; 
                Phone: <span><?php echo htmlspecialchars($parentPhone, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- FINANCIAL SNAPSHOT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; font-size: 1.2rem;">
                    <i class="fa fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Total Assigned</div>
                    <div class="h4 fw-bold mb-0 text-dark"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> <?php echo number_format($totalAssigned, 2); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 1.2rem;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Total Paid</div>
                    <div class="h4 fw-bold mb-0 text-success"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> <?php echo number_format($totalPaid, 2); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 1.2rem;">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Net Balance Due</div>
                    <div class="h4 fw-bold mb-0 text-danger"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> <?php echo number_format($totalBalance, 2); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FEE BREAKDOWN TABLE -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="mb-0 fw-bold text-dark">Fee Breakdown &amp; Invoices</h5>
        <span class="badge bg-light text-dark border px-3 py-1 font-monospace">
            <?php echo count($data['fees']); ?> Invoices
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th class="ps-4 py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Fee Group</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Fee Code</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Due Date</th>
                        <th class="py-3 text-end" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Amount</th>
                        <th class="py-3 text-end" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Paid</th>
                        <th class="py-3 text-end" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Balance</th>
                        <th class="pe-4 py-3 text-end" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['fees'])): ?>
                        <?php foreach($data['fees'] as $fee): 
                            $balance = ($fee->amount + $fee->fine_amount) - ($fee->total_paid + $fee->total_discount);
                        ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark" style="font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($fee->group_name, ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">
                                        <?php echo htmlspecialchars($fee->type_code, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <i class="fa fa-calendar-alt me-1 text-primary"></i>
                                    <?php echo !empty($fee->due_date) ? date('d M Y', strtotime($fee->due_date)) : 'N/A'; ?>
                                </td>
                                <td class="text-end font-monospace fw-bold" style="font-size: 0.9rem;">
                                    $<?php echo number_format($fee->amount, 2); ?>
                                </td>
                                <td class="text-end font-monospace fw-bold text-success" style="font-size: 0.9rem;">
                                    $<?php echo number_format($fee->total_paid, 2); ?>
                                </td>
                                <td class="text-end font-monospace fw-bold <?php echo ($balance > 0) ? 'text-danger' : 'text-muted'; ?>" style="font-size: 0.9rem;">
                                    $<?php echo number_format($balance, 2); ?>
                                </td>
                                <td class="pe-4 text-end">
                                    <?php if($balance > 0): ?>
                                        <button type="button" class="btn btn-sm btn-primary shadow-sm" style="border-radius: 8px; padding: 5px 14px;" data-bs-toggle="modal" data-bs-target="#payModal<?php echo $fee->student_fee_id; ?>">
                                            <i class="fa fa-hand-holding-usd me-1"></i> Pay
                                        </button>
                                        
                                        <!-- Payment Modal -->
                                        <div class="modal fade text-start" id="payModal<?php echo $fee->student_fee_id; ?>" tabindex="-1" aria-labelledby="payModalLabel<?php echo $fee->student_fee_id; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                                                    <div class="modal-header bg-light py-3 px-4">
                                                        <h5 class="modal-title fw-bold" id="payModalLabel<?php echo $fee->student_fee_id; ?>">
                                                            <i class="fa fa-cash-register text-primary me-2"></i>Collect Payment
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="<?php echo URLROOT; ?>/fees/pay" method="post">
                                                        <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
                                                        <input type="hidden" name="student_fee_id" value="<?php echo $fee->student_fee_id; ?>">
                                                        <input type="hidden" name="student_id" value="<?php echo $data['student_id']; ?>">
                                                        
                                                        <div class="modal-body p-4">
                                                            <div class="p-3 bg-light rounded-3 mb-3" style="border: 1px dashed #cbd5e1;">
                                                                <div class="d-flex justify-content-between small text-muted mb-1">
                                                                    <span>Invoice:</span>
                                                                    <strong><?php echo htmlspecialchars($fee->group_name . ' (' . $fee->type_code . ')', ENT_QUOTES, 'UTF-8'); ?></strong>
                                                                </div>
                                                                <div class="d-flex justify-content-between small text-muted">
                                                                    <span>Current Balance:</span>
                                                                    <strong class="text-danger"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> <?php echo number_format($balance, 2); ?></strong>
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold small text-muted text-uppercase">Payment Amount (<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?>) <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo $balance; ?>" max="<?php echo $balance; ?>" required style="border-radius: 8px;">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold small text-muted text-uppercase">Payment Mode <span class="text-danger">*</span></label>
                                                                <select name="mode" class="form-select" style="border-radius: 8px;">
                                                                    <option value="Cash">Cash</option>
                                                                    <option value="Cheque">Cheque</option>
                                                                    <option value="Bank Transfer">Bank Transfer</option>
                                                                    <option value="Online">Online</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-0">
                                                                <label class="form-label fw-bold small text-muted text-uppercase">Transaction Note</label>
                                                                <textarea name="note" class="form-control" rows="2" placeholder="Reference number or memo..." style="border-radius: 8px;"></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer bg-light py-3 px-4">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                                                            <button type="submit" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;">
                                                                <i class="fa fa-check me-1"></i> Confirm &amp; Save
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-success px-3 py-2" style="border-radius: 8px;">
                                            <i class="fa fa-check me-1"></i> Paid
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-5 text-center text-muted">
                                <i class="fa fa-info-circle fa-2x mb-2 text-muted"></i>
                                <div>No fee records currently assigned to this student.</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
