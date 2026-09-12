<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$month = $data['month'];
$year = $data['year'];
$kpis = $data['kpis'];
$payslips = $data['payslips'];
$staffList = $data['staff_list'];
?>

<!-- Page Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small" style="font-size: 0.78rem;">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/staff/index" class="text-decoration-none text-muted">Staff Directory</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Staff Payroll</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0 text-dark fs-5">Staff Payroll &amp; Salary Vouchers</h4>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-primary btn-sm px-2 py-1 shadow-xs" data-bs-toggle="modal" data-bs-target="#singlePayslipModal">
            <i class="fa fa-plus me-1"></i> Single Slip
        </button>
        <form action="<?php echo URLROOT; ?>/payroll/generateAll" method="post" class="d-inline" onsubmit="return confirm('Generate monthly payslips for all active staff members for <?php echo $month . ' ' . $year; ?>?');">
            <input type="hidden" name="month" value="<?php echo $month; ?>">
            <input type="hidden" name="year" value="<?php echo $year; ?>">
            <button type="submit" class="btn btn-primary btn-sm px-3 py-1 shadow-sm">
                <i class="fa fa-bolt me-1"></i> Generate All (<?php echo $month; ?>)
            </button>
        </form>
    </div>
</div>

<!-- Alert Notifications -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3 mb-3 small" role="alert">
        <i class="fa fa-check-circle"></i>
        <div>
            <?php 
                if($_GET['success'] == 'generated') echo "Payslip voucher generated successfully!";
                elseif($_GET['success'] == 'all_generated') echo "Batch generation complete: Successfully created " . htmlspecialchars($_GET['count'] ?? '0') . " payslips for {$month} {$year}!";
                elseif($_GET['success'] == 'paid') echo "Salary disbursement completed! Expense record auto-synced into Accounting Cash Book.";
                else echo "Action completed successfully!";
            ?>
        </div>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Period Filter Toolbar -->
<div class="card shadow-sm border-0 mb-3 bg-white">
    <div class="card-body py-2 px-3">
        <form action="<?php echo URLROOT; ?>/payroll/index" method="get" class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-semibold text-muted"><i class="fa fa-filter me-1"></i> Filter Period:</span>
                <select name="month" class="form-select form-select-sm w-auto py-1" style="font-size: 0.85rem;" onchange="this.form.submit()">
                    <?php 
                    $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                    foreach($months as $m){
                        $sel = ($month == $m) ? 'selected' : '';
                        echo "<option value='$m' $sel>$m</option>";
                    }
                    ?>
                </select>
                <select name="year" class="form-select form-select-sm w-auto py-1" style="font-size: 0.85rem;" onchange="this.form.submit()">
                    <?php for($y = (int)date('Y') + 1; $y >= 2024; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo ($year == $y) ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <a href="<?php echo URLROOT; ?>/incomes/index" class="btn btn-sm btn-light border text-secondary px-2 py-1" style="font-size: 0.82rem;" title="View Cash Book">
                    <i class="fa fa-cash-register me-1"></i> Cash Book
                </a>
            </div>
        </form>
    </div>
</div>

<!-- 4 Executive KPI Cards -->
<div class="row g-2 mb-3">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-wallet"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Monthly Budget</div>
                    <h6 class="fw-bold mb-0 text-dark fs-6">Rs. <?php echo number_format($kpis['total_amount']); ?></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Paid &amp; Disbursed</div>
                    <h6 class="fw-bold mb-0 text-success fs-6"><?php echo $kpis['paid_count']; ?> <small class="text-muted fw-normal" style="font-size: 0.75rem;">Slips</small></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-clock"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Pending Payment</div>
                    <h6 class="fw-bold mb-0 text-warning fs-6"><?php echo $kpis['pending_count']; ?> <small class="text-muted fw-normal" style="font-size: 0.75rem;">Slips</small></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm py-2 px-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem; color: #7c3aed; background: rgba(124, 58, 237, 0.1);">
                    <i class="fa fa-chalkboard-user"></i>
                </div>
                <div>
                    <div class="text-muted text-uppercase" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px;">Visiting Faculty</div>
                    <h6 class="fw-bold mb-0 fs-6" style="color: #7c3aed;"><?php echo $kpis['visiting_count']; ?> <small class="text-muted fw-normal" style="font-size: 0.75rem;">Staff</small></h6>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payslips Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 border-bottom d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-file-invoice-dollar text-primary fs-5"></i>
            <h5 class="fw-bold mb-0 text-dark">Salary Slips &amp; Vouchers &mdash; <?php echo $month . ' ' . $year; ?></h5>
        </div>
        <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2 ms-sm-auto" style="border-radius: 30px;">
            <?php echo count($payslips); ?> Payslips Generated
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="width: 100%; table-layout: fixed;">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3" style="width: 20%;">Staff Member</th>
                        <th style="width: 10%;">Staff Code</th>
                        <th style="width: 14%;">Type &amp; Dept</th>
                        <th style="width: 12%;">Basic / Rate</th>
                        <th style="width: 14%;">Allow / Deduc</th>
                        <th style="width: 12%;">Net Salary</th>
                        <th class="text-center" style="width: 8%;">Status</th>
                        <th class="text-end pe-3" style="width: 10%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($payslips)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa fa-file-excel fa-2x mb-2 d-block opacity-50"></i>
                                No payslips generated for <?php echo $month . ' ' . $year; ?> yet.<br>
                                Click <strong>Generate All</strong> or <strong>Generate Single Slip</strong> to prepare vouchers.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($payslips as $p): 
                            $isPaid = ($p->status == 'Paid');
                            $isVisiting = strpos($p->employment_type ?? '', 'Visiting') !== false;
                        ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($p->name); ?>&background=random&size=36&bold=true" class="rounded-circle shadow-xs flex-shrink-0" width="32" height="32" alt="">
                                        <div class="text-truncate" style="max-width: calc(100% - 40px);">
                                            <div class="fw-bold text-dark text-truncate">
                                                <a href="<?php echo URLROOT; ?>/staff/profile/<?php echo $p->staff_id; ?>" class="text-dark text-decoration-none hover-primary">
                                                    <?php echo htmlspecialchars($p->name); ?>
                                                </a>
                                            </div>
                                            <small class="text-muted text-capitalize d-block text-truncate"><?php echo htmlspecialchars($p->role); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary font-monospace text-truncate d-inline-block" style="max-width: 100%;"><?php echo htmlspecialchars($p->staff_code ?: 'STF'); ?></span>
                                </td>
                                <td>
                                    <?php if($isVisiting): ?>
                                        <span class="badge bg-purple-subtle text-purple border px-2 py-0 text-truncate d-inline-block" style="color: #7c3aed; background: rgba(124, 58, 237, 0.1); font-size: 0.72rem; max-width: 100%;">
                                            Visiting (<?php echo $p->lectures_delivered; ?> Lecs)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border px-2 py-0 text-truncate d-inline-block" style="font-size: 0.72rem; max-width: 100%;"><?php echo htmlspecialchars($p->employment_type ?? 'Permanent'); ?></span>
                                    <?php endif; ?>
                                    <small class="text-muted d-block text-truncate" style="max-width: 100%; font-size: 0.75rem;"><?php echo htmlspecialchars($p->department ?: 'Academics'); ?></small>
                                </td>
                                <td>
                                    <?php if($isVisiting): ?>
                                        <span class="fw-bold small">Rs. <?php echo number_format((float)$p->lecture_rate); ?></span>
                                        <small class="text-muted d-block smaller">/ lecture</small>
                                    <?php else: ?>
                                        <span class="fw-bold small">Rs. <?php echo number_format((float)$p->basic_salary); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small"><span class="text-success fw-bold">+Rs. <?php echo number_format((float)$p->total_allowance); ?></span></div>
                                    <div class="small"><span class="text-danger fw-bold">-Rs. <?php echo number_format((float)$p->total_deduction); ?></span></div>
                                    <?php if((float)($p->leave_deduction ?? 0) > 0): ?>
                                        <div class="badge bg-danger-subtle text-danger border border-danger-subtle px-1 py-0 mt-1 text-truncate d-inline-block" style="font-size: 0.68rem;" title="Leave Cutting: <?php echo (float)($p->absent_days ?? 0); ?> Absents, <?php echo (float)($p->half_days ?? 0); ?> Half Days">
                                            <i class="fa fa-user-clock me-1"></i>Cut: Rs. <?php echo number_format((float)$p->leave_deduction); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">Rs. <?php echo number_format((float)$p->net_salary); ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if($isPaid): ?>
                                        <span class="badge bg-success px-2 py-1">
                                            <i class="fa fa-check me-1"></i>Paid
                                        </span>
                                        <small class="text-muted d-block smaller text-truncate" style="font-size: 0.7rem;"><?php echo date('d M', strtotime($p->payment_date)); ?> (<?php echo htmlspecialchars($p->payment_mode ?? 'Cash'); ?>)</small>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark px-2 py-1">
                                            <i class="fa fa-clock me-1"></i>Due
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <?php if(!$isPaid): ?>
                                            <button type="button" class="btn btn-sm btn-success px-2 py-1" onclick="openPaymentModal(<?php echo $p->id; ?>, '<?php echo htmlspecialchars(addslashes($p->name)); ?>', <?php echo (float)$p->net_salary; ?>)">
                                                <i class="fa fa-cash-register me-1"></i> Pay
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline-dark px-2 py-1" onclick="openSlipModal(<?php echo $p->id; ?>)" title="View Salary Slip Voucher">
                                            <i class="fa fa-print me-1"></i> Slip
                                        </button>
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

<!-- MODAL: Generate Single Payslip -->
<div class="modal fade" id="singlePayslipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fa fa-receipt me-2"></i>Generate Single Payslip</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/payroll/create" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Select Faculty / Staff Member</label>
                        <select name="staff_id" id="modalStaffSelect" class="form-select" required onchange="handleStaffSelectChange(this)">
                            <option value="">-- Choose Staff Member --</option>
                            <?php foreach($staffList as $stf): ?>
                                <option value="<?php echo $stf->id; ?>" 
                                        data-etype="<?php echo htmlspecialchars($stf->employment_type ?? 'Permanent'); ?>"
                                        data-rate="<?php echo (float)($stf->lecture_rate ?? 0); ?>"
                                        data-basic="<?php echo (float)($stf->basic_salary ?? 0); ?>">
                                    <?php echo htmlspecialchars($stf->name . ' (' . $stf->staff_code . ' - ' . ($stf->designation ?: $stf->role) . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Month</label>
                            <select name="month" class="form-select" required>
                                <?php foreach($months as $m): ?>
                                    <option value="<?php echo $m; ?>" <?php echo ($month == $m) ? 'selected' : ''; ?>><?php echo $m; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Year</label>
                            <input type="number" name="year" class="form-control" value="<?php echo $year; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3" id="modalVisitingWrap" style="display: none;">
                        <label class="form-label small fw-bold text-dark">Monthly Lectures Delivered (Visiting Faculty)</label>
                        <input type="number" name="lectures_delivered" id="modalLecturesInput" class="form-control" value="16" min="1">
                        <small class="text-muted smaller">Multiplied by visiting lecture rate for this voucher</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Generate Voucher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Disburse / Pay Salary -->
<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fa fa-cash-register me-2"></i>Disburse Salary Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="payForm" action="" method="post">
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 text-center">
                        <div class="text-muted small">Paying Salary To:</div>
                        <h5 class="fw-bold text-dark mb-1" id="payStaffName">Staff Name</h5>
                        <div class="h4 fw-bold text-success mb-0" id="payAmount">Rs. 0</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Payment Mode</label>
                        <select name="mode" class="form-select" required>
                            <option value="Cash">Cash (Immediate Petty Cash/Cash Box)</option>
                            <option value="Bank Transfer">Bank Transfer (Direct Deposit)</option>
                            <option value="Cheque">Crossed Cheque</option>
                            <option value="Online / EasyPaisa">Online / Mobile Wallet</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Disbursement Date</label>
                        <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Transaction Reference / Remarks</label>
                        <input type="text" name="note" class="form-control" placeholder="e.g. Paid via Cheque #10294 or Cash counter">
                    </div>
                    <div class="alert alert-info small mb-0">
                        <i class="fa fa-sync-alt me-1"></i>
                        <strong>Accounting Auto-Sync:</strong> Confirming disbursement automatically registers an outflow expense into the Daily Cash Book.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: View Salary Slip Voucher -->
<div class="modal fade" id="viewSlipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 860px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2 px-3">
                <h6 class="modal-title fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="fa fa-receipt text-primary"></i> Official Salary Voucher Slip
                </h6>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <button type="button" class="btn btn-sm btn-primary px-3 py-1 fw-semibold" onclick="printSlipIframe()">
                        <i class="fa fa-print me-1"></i> Print Voucher
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0 bg-light">
                <iframe id="slipFrame" src="" style="width: 100%; height: 600px; border: none; display: block;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    function handleStaffSelectChange(select){
        var opt = select.options[select.selectedIndex];
        var etype = opt.getAttribute('data-etype') || '';
        var wrap = document.getElementById('modalVisitingWrap');
        if(etype.indexOf('Visiting') !== -1){
            wrap.style.display = 'block';
        } else {
            wrap.style.display = 'none';
        }
    }

    function openPaymentModal(id, name, amount){
        var modalEl = document.getElementById('payModal');
        if(!modalEl) return;

        // Move modal to body so backdrop stacking context doesn't blur or block modal content
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }

        document.getElementById('payStaffName').innerText = name;
        document.getElementById('payAmount').innerText = 'Rs. ' + parseFloat(amount).toLocaleString('en-US');
        document.getElementById('payForm').action = '<?php echo URLROOT; ?>/payroll/pay/' + id;

        var myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        myModal.show();
    }

    function openSlipModal(id){
        var modalEl = document.getElementById('viewSlipModal');
        if(!modalEl) return;

        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }

        var iframe = document.getElementById('slipFrame');
        iframe.src = '<?php echo URLROOT; ?>/payroll/slip/' + id;

        var myModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        myModal.show();
    }

    function printSlipIframe(){
        var iframe = document.getElementById('slipFrame');
        if(iframe && iframe.contentWindow){
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }
    }
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
