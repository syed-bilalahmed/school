<?php require APPROOT . '/Views/layouts/header.php'; 
$ledger = $data['ledger'];
$defaulters = $ledger->defaulters;
$selectedAging = $data['selected_aging'] ?? 'all';
?>

<!-- TOP EXECUTIVE HEADER -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1">
            <i class="fa fa-user-clock text-danger me-2"></i>Fee Defaulters &amp; Aging Ledger (Module 16)
        </h2>
        <p class="text-muted small mb-0">Track aging overdue accounts (30, 60, 90+ days), dispatch WhatsApp fee reminders &amp; print formal demand notices.</p>
    </div>
    
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo URLROOT; ?>/fees/challan" class="btn btn-outline-primary btn-sm px-3 fw-bold" style="border-radius: 8px;">
            <i class="fa fa-money-check me-1"></i> Bank Challans
        </a>
        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" style="border-radius: 8px;">
            <i class="fa fa-cash-register me-1"></i> Fee Collection Hub
        </a>
    </div>
</div>

<!-- EXECUTIVE AGING KPI SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <!-- Total Defaulters -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 46px; height: 46px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 1.3rem;">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Defaulter Students</div>
                    <div class="h4 fw-bold mb-0 text-danger"><?php echo number_format($ledger->total_defaulters); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Overdue Balance -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 46px; height: 46px; border-radius: 12px; background: rgba(220, 38, 38, 0.1); color: #dc2626; font-size: 1.3rem;">
                    <i class="fa fa-money-bill-wave"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Total Outstanding Arrears</div>
                    <div class="h4 fw-bold mb-0 text-dark">Rs. <?php echo number_format($ledger->total_outstanding); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 31 - 60 Days Bracket -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 46px; height: 46px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; font-size: 1.3rem;">
                    <i class="fa fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">31 - 60 Days (2 Mos)</div>
                    <div class="h5 fw-bold mb-0 text-warning"><?php echo $ledger->bracket31_60; ?> Students <span class="small text-muted fw-normal">(Rs. <?php echo number_format($ledger->bracket31_60_amt); ?>)</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 90+ Days Critical Bracket -->
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 46px; height: 46px; border-radius: 12px; background: rgba(153, 27, 27, 0.1); color: #991b1b; font-size: 1.3rem;">
                    <i class="fa fa-ban"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">90+ Days (Critical)</div>
                    <div class="h5 fw-bold mb-0 text-danger"><?php echo $ledger->bracket90_plus; ?> Students <span class="small text-muted fw-normal">(Rs. <?php echo number_format($ledger->bracket90_plus_amt); ?>)</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AGING BRACKET TABS & FILTERS -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
    <div class="card-body p-4">
        <!-- Aging Navigation Pills -->
        <div class="d-flex flex-wrap gap-2 mb-3 pb-3 border-bottom">
            <a href="<?php echo URLROOT; ?>/fees/defaulters?aging=all<?php echo !empty($data['selected_class']) ? '&class_id='.$data['selected_class'] : ''; ?>" 
               class="btn btn-sm <?php echo ($selectedAging === 'all') ? 'btn-dark' : 'btn-outline-secondary'; ?> fw-bold" style="border-radius: 8px;">
                All Defaulters (<?php echo $ledger->total_defaulters; ?>)
            </a>
            <a href="<?php echo URLROOT; ?>/fees/defaulters?aging=1-30<?php echo !empty($data['selected_class']) ? '&class_id='.$data['selected_class'] : ''; ?>" 
               class="btn btn-sm <?php echo ($selectedAging === '1-30') ? 'btn-primary' : 'btn-outline-secondary'; ?> fw-bold" style="border-radius: 8px;">
                1 - 30 Days (<?php echo $ledger->bracket1_30; ?>)
            </a>
            <a href="<?php echo URLROOT; ?>/fees/defaulters?aging=31-60<?php echo !empty($data['selected_class']) ? '&class_id='.$data['selected_class'] : ''; ?>" 
               class="btn btn-sm <?php echo ($selectedAging === '31-60') ? 'btn-warning text-dark' : 'btn-outline-secondary'; ?> fw-bold" style="border-radius: 8px;">
                31 - 60 Days (<?php echo $ledger->bracket31_60; ?>)
            </a>
            <a href="<?php echo URLROOT; ?>/fees/defaulters?aging=61-90<?php echo !empty($data['selected_class']) ? '&class_id='.$data['selected_class'] : ''; ?>" 
               class="btn btn-sm <?php echo ($selectedAging === '61-90') ? 'btn-danger' : 'btn-outline-secondary'; ?> fw-bold" style="border-radius: 8px;">
                61 - 90 Days (<?php echo $ledger->bracket61_90; ?>)
            </a>
            <a href="<?php echo URLROOT; ?>/fees/defaulters?aging=90+<?php echo !empty($data['selected_class']) ? '&class_id='.$data['selected_class'] : ''; ?>" 
               class="btn btn-sm <?php echo ($selectedAging === '90+') ? 'btn-danger' : 'btn-outline-secondary'; ?> fw-bold" style="border-radius: 8px;">
                <i class="fa fa-skull-crossbones me-1"></i> 90+ Days Critical (<?php echo $ledger->bracket90_plus; ?>)
            </a>
        </div>

        <!-- Class filter & Search Input -->
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                    <i class="fa fa-graduation-cap text-primary me-1"></i> Filter By Class
                </label>
                <form action="<?php echo URLROOT; ?>/fees/defaulters" method="GET" id="defaulterClassForm">
                    <input type="hidden" name="aging" value="<?php echo htmlspecialchars($selectedAging); ?>">
                    <select name="class_id" class="form-select" style="border-radius: 10px; font-weight: 600;" onchange="this.form.submit()">
                        <option value="">-- All Classes --</option>
                        <?php foreach($data['classes'] as $cls): ?>
                            <option value="<?php echo $cls->id; ?>" <?php echo ($data['selected_class'] == $cls->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cls->class_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                    <i class="fa fa-search text-primary me-1"></i> Instant Live Search
                </label>
                <input type="text" id="defaulterSearchInput" class="form-control" placeholder="Search by student name, roll number, admission number, parent phone..." style="border-radius: 10px;">
            </div>

            <div class="col-md-2">
                <a href="<?php echo URLROOT; ?>/fees/defaulters" class="btn btn-outline-secondary w-100 fw-bold" style="border-radius: 10px;">
                    <i class="fa fa-redo-alt me-1"></i> Reset
                </a>
            </div>
        </div>
    </div>
</div>

<!-- DEFAULTERS LEDGER TABLE -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <div class="d-flex align-items-center gap-2">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fa fa-clipboard-list text-danger me-2"></i>Outstanding Arrears Ledger
            </h5>
            <span class="badge bg-danger rounded-pill px-3 py-1 font-monospace" id="defaulterCountBadge">
                <?php echo count($defaulters); ?> Defaulters
            </span>
        </div>
        <div class="text-muted small">
            Bracket: <strong><?php echo ucfirst($selectedAging); ?> Days</strong>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="defaultersTable">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th class="ps-4 py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 100px;">Adm No</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Student Name</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Class &amp; Section</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Parent Phone</th>
                        <th class="py-3 text-end" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Assigned</th>
                        <th class="py-3 text-end" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Paid</th>
                        <th class="py-3 text-end" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Arrears Due</th>
                        <th class="py-3 text-center" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Aging Bracket</th>
                        <th class="pe-4 py-3 text-end" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 220px;">Recovery Actions</th>
                    </tr>
                </thead>
                <tbody id="defaultersBody">
                    <?php if(!empty($defaulters)): ?>
                        <?php foreach($defaulters as $d): 
                            $initials = strtoupper(substr($d->student_name, 0, 2));
                            $searchStr = strtolower($d->student_name . ' ' . ($d->admission_no ?? '') . ' ' . ($d->roll_no ?? '') . ' ' . ($d->class_name ?? '') . ' ' . ($d->parent_phone ?? ''));
                            
                            // WhatsApp message pre-fill
                            $cleanPhone = preg_replace('/[^0-9]/', '', $d->parent_phone ?? '');
                            if(substr($cleanPhone, 0, 1) === '0') {
                                $cleanPhone = '92' . substr($cleanPhone, 1);
                            }
                            $waMsg = "Assalam-o-Alaikum! Respected Parent of " . $d->student_name . " (Adm #" . $d->admission_no . ", Class " . $d->class_name . "), this is a fee reminder from School Accounts. An outstanding fee balance of Rs. " . number_format($d->balance) . " is overdue. Kindly deposit the fee voucher at the bank or school accounts office at your earliest. Thank you.";
                            $waUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($waMsg);
                            
                            // Aging badge color
                            if($d->bracket === '1-30') {
                                $badgeClass = 'bg-warning-subtle text-warning border border-warning-subtle';
                            } elseif($d->bracket === '31-60') {
                                $badgeClass = 'bg-warning text-dark';
                            } elseif($d->bracket === '61-90') {
                                $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                            } else {
                                $badgeClass = 'bg-danger text-white';
                            }
                        ?>
                        <tr class="defaulter-row" data-search="<?php echo htmlspecialchars($searchStr); ?>">
                            <td class="ps-4 font-monospace fw-bold text-muted small">
                                #<?php echo htmlspecialchars($d->admission_no ?: '-'); ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar" style="width: 32px; height: 32px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                                        <?php echo $initials; ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">
                                            <?php echo htmlspecialchars($d->student_name); ?>
                                        </div>
                                        <?php if(!empty($d->roll_no)): ?>
                                            <div class="text-muted" style="font-size: 0.72rem;">Roll: <?php echo htmlspecialchars($d->roll_no); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.78rem;">
                                    <?php echo htmlspecialchars($d->class_name ?? 'General'); ?>
                                    <?php if(!empty($d->section_name)): ?>
                                        <span class="text-primary fw-bold">&bull; <?php echo htmlspecialchars($d->section_name); ?></span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <?php if(!empty($d->parent_phone) && $d->parent_phone !== 'N/A'): ?>
                                    <a href="tel:<?php echo htmlspecialchars($d->parent_phone); ?>" class="font-monospace text-decoration-none text-dark small fw-bold">
                                        <i class="fa fa-phone-alt text-muted me-1"></i><?php echo htmlspecialchars($d->parent_phone); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end font-monospace small text-muted">
                                Rs. <?php echo number_format($d->total_fee); ?>
                            </td>
                            <td class="text-end font-monospace small text-success fw-bold">
                                Rs. <?php echo number_format($d->total_paid); ?>
                            </td>
                            <td class="text-end font-monospace fw-bold text-danger" style="font-size: 0.92rem;">
                                Rs. <?php echo number_format($d->balance); ?>
                            </td>
                            <td class="text-center">
                                <span class="badge <?php echo $badgeClass; ?> px-2 py-1" style="font-size: 0.75rem; font-weight: 700;">
                                    <?php echo htmlspecialchars($d->bracket_label); ?>
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- WhatsApp Alert Action -->
                                    <?php if(!empty($cleanPhone)): ?>
                                    <a href="<?php echo $waUrl; ?>" target="_blank" class="btn btn-sm btn-success px-2 py-1" title="Send WhatsApp Reminder" style="border-radius: 6px;">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <?php endif; ?>

                                    <!-- Demand Notice Letter -->
                                    <a href="<?php echo URLROOT; ?>/fees/printNotice/<?php echo $d->student_id; ?>" target="_blank" class="btn btn-sm btn-outline-danger px-2 py-1" title="Print Formal Demand Notice" style="border-radius: 6px;">
                                        <i class="fa fa-file-invoice"></i> Notice
                                    </a>

                                    <!-- Collect Fee Link -->
                                    <a href="<?php echo URLROOT; ?>/fees/collect?student_id=<?php echo $d->student_id; ?>" class="btn btn-sm btn-primary px-2 py-1" title="Collect Fee" style="border-radius: 6px;">
                                        <i class="fa fa-hand-holding-usd"></i> Collect
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="py-5 text-center text-muted">
                                <i class="fa fa-check-circle text-success fs-1 mb-2"></i>
                                <div class="fw-bold">No Fee Defaulters In This Category!</div>
                                <div class="small">All students have cleared their scheduled dues on time.</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('defaulterSearchInput');
    const rows = document.querySelectorAll('.defaulter-row');
    const badge = document.getElementById('defaulterCountBadge');

    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        let visible = 0;

        rows.forEach(r => {
            const searchStr = r.getAttribute('data-search');
            if(!q || searchStr.includes(q)) {
                r.style.display = '';
                visible++;
            } else {
                r.style.display = 'none';
            }
        });

        badge.textContent = visible + ' Defaulters';
    });
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
