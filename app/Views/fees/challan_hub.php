<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- SUCCESS / NOTIFICATION ALERTS -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" style="border-radius: 12px;" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div>
            <?php 
                if($_GET['success'] == 'invoices_generated') echo "Batch monthly fee vouchers generated successfully for " . htmlspecialchars($_GET['count'] ?? '') . " student entries!";
                elseif($_GET['success'] == 'bank_updated') echo "School Bank account details updated successfully for challan printing!";
                else echo "Action completed successfully!";
            ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- TOP EXECUTIVE HEADER -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1">
            <i class="fa fa-money-check text-primary me-2"></i>Bank Fee Challans (Module 15)
        </h2>
        <p class="text-muted small mb-0">Generate, customize &amp; print authentic 3-Copy Bank Challans (Bank, School, Student) with automated sibling concessions.</p>
    </div>
    
    <!-- Action buttons -->
    <div class="d-flex flex-wrap gap-2">
        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" style="border-radius: 8px;">
            <i class="fa fa-cash-register me-1"></i> Fee Collection Hub
        </a>
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#bankSettingsModal">
            <i class="fa fa-university text-primary me-1"></i> Bank Particulars
        </button>
        <button type="button" class="btn btn-outline-primary btn-sm px-3 fw-bold shadow-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#batchGenerateModal">
            <i class="fa fa-cogs me-1"></i> Batch Generate Invoices
        </button>
        <a href="<?php echo URLROOT; ?>/fees/defaulters" class="btn btn-danger btn-sm px-3 fw-bold shadow-sm" style="border-radius: 8px;">
            <i class="fa fa-user-clock me-1"></i> Defaulters Ledger
        </a>
    </div>
</div>

<!-- BANK ACCOUNT BADGE & BILLING PERIOD SELECTOR -->
<div class="row g-3 mb-4">
    <!-- Active Bank Info Banner -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color: #ffffff;">
            <div class="card-body p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1" style="font-size: 0.72rem;">DEFAULT COLLECTION BANK</span>
                        <span class="badge bg-white text-dark font-monospace px-2 py-1" style="font-size: 0.72rem;">PKR OFFICIAL</span>
                    </div>
                    <h4 class="fw-bold mb-1" style="letter-spacing: 0.5px;">
                        <i class="fa fa-landmark me-2 text-warning"></i><?php echo htmlspecialchars($data['bank']->bank_name); ?>
                    </h4>
                    <div class="small opacity-85">
                        <span>A/C Title: <strong><?php echo htmlspecialchars($data['bank']->account_title); ?></strong></span> &bull; 
                        <span>A/C: <strong class="font-monospace text-warning"><?php echo htmlspecialchars($data['bank']->account_no); ?></strong></span>
                    </div>
                    <div class="small opacity-75 mt-1 font-monospace" style="font-size: 0.78rem;">
                        IBAN: <?php echo htmlspecialchars($data['bank']->iban ?: 'N/A'); ?> &bull; Branch: <?php echo htmlspecialchars($data['bank']->branch_name ?: 'City Campus'); ?>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-light btn-sm fw-bold px-3" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#bankSettingsModal">
                        <i class="fa fa-edit me-1"></i> Modify Bank
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Batch Class Quick Print -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="fa fa-layer-group text-primary me-2"></i>Class Batch Print
                </h6>
                <p class="text-muted small mb-3">Print 3-copy challans for all students in a class at once with automatic page breaks.</p>
                
                <form action="<?php echo URLROOT; ?>/fees/batchChallans" method="GET" target="_blank">
                    <div class="mb-2">
                        <select name="class_id" class="form-select form-select-sm" required style="border-radius: 8px;">
                            <option value="">-- Choose Class --</option>
                            <?php foreach($data['classes'] as $cls): ?>
                                <option value="<?php echo $cls->id; ?>" <?php echo ($data['selected_class'] == $cls->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cls->class_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" style="border-radius: 8px;">
                        <i class="fa fa-print me-1"></i> Open Batch Print View
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- FILTER & SEARCH PANEL -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
    <div class="card-body p-4">
        <div class="row g-3 align-items-end">
            <!-- Filter Class -->
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                    <i class="fa fa-graduation-cap text-primary me-1"></i> Filter By Class
                </label>
                <select id="hubClassFilter" class="form-select" style="border-radius: 10px; font-weight: 600;">
                    <option value="">-- All Classes --</option>
                    <?php foreach($data['classes'] as $cls): ?>
                        <option value="<?php echo $cls->id; ?>" <?php echo ($data['selected_class'] == $cls->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cls->class_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Search student -->
            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                    <i class="fa fa-search text-primary me-1"></i> Search Student
                </label>
                <input type="text" id="hubSearchInput" class="form-control" placeholder="Search by student name, roll number, admission number..." style="border-radius: 10px;">
            </div>

            <!-- Reset -->
            <div class="col-md-2">
                <button type="button" id="hubResetBtn" class="btn btn-outline-secondary w-100 fw-bold" style="border-radius: 10px;">
                    <i class="fa fa-redo-alt me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- STUDENTS DIRECTORY FOR CHALLAN PRINTING -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom flex-wrap gap-2">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="fa fa-users text-primary me-2"></i>Students Challan Register
        </h5>
        <div class="d-flex align-items-center gap-2">
            <button type="button" id="btnHubPrintSelected" class="btn btn-primary btn-sm fw-bold px-3 shadow-sm d-none" onclick="printHubSelectedChallans()" style="border-radius: 8px;">
                <i class="fa fa-print me-1"></i> Print Selected Challans (<span id="hubSelectedCount">0</span>)
            </button>
            <span class="badge bg-primary rounded-pill px-3 py-1 font-monospace" id="hubCountBadge">
                0 Students
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="hubStudentsTable" style="display: none;">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th class="ps-3 py-3" style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="hubSelectAllCheckbox" title="Select All Visible" onchange="toggleHubSelectAll(this.checked)">
                        </th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 110px;">Adm No</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Student Name</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Class &amp; Section</th>
                        <th class="py-3" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Father &amp; Contact</th>
                        <th class="py-3 text-center" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Sibling Concession</th>
                        <th class="pe-4 py-3 text-end" style="font-size: 0.78rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 180px;">Action</th>
                    </tr>
                </thead>
                <tbody id="hubStudentsBody">
                    <?php if(!empty($data['students'])): ?>
                        <?php foreach($data['students'] as $s): 
                            $searchStr = strtolower($s->name . ' ' . ($s->admission_no ?? '') . ' ' . ($s->roll_no ?? '') . ' ' . ($s->class_name ?? '') . ' ' . ($s->father_name ?? ''));
                            $siblingDisc = (float)($s->sibling_discount_percent ?? 0);
                        ?>
                        <tr class="hub-row" data-student-id="<?php echo (int)$s->id; ?>" data-class-id="<?php echo (int)$s->class_id; ?>" data-search="<?php echo htmlspecialchars($searchStr); ?>" style="display: none;">
                            <td class="ps-3">
                                <input type="checkbox" class="form-check-input hub-student-checkbox" value="<?php echo (int)$s->id; ?>" onchange="updateHubSelectedCount()">
                            </td>
                            <td class="font-monospace fw-bold text-muted small">
                                #<?php echo htmlspecialchars($s->admission_no ?: '-'); ?>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">
                                        <?php echo htmlspecialchars($s->name); ?>
                                    </div>
                                    <?php if(!empty($s->roll_no)): ?>
                                        <div class="text-muted" style="font-size: 0.72rem;">Roll No: <?php echo htmlspecialchars($s->roll_no); ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.78rem;">
                                    <?php echo htmlspecialchars($s->class_name ?? 'General'); ?>
                                    <?php if(!empty($s->section_name)): ?>
                                        <span class="text-primary fw-bold">&bull; <?php echo htmlspecialchars($s->section_name); ?></span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark"><?php echo htmlspecialchars($s->father_name ?: 'N/A'); ?></div>
                                <div class="small text-muted font-monospace"><?php echo htmlspecialchars($s->parent_phone ?: $s->phone ?: 'N/A'); ?></div>
                            </td>
                            <td class="text-center">
                                <?php if($siblingDisc > 0): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.75rem;">
                                        <i class="fa fa-users me-1"></i><?php echo $siblingDisc; ?>% Concession
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.72rem;">Standard Fee</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="<?php echo URLROOT; ?>/fees/challan/<?php echo $s->id; ?>" target="_blank" class="btn btn-primary btn-sm px-3 shadow-sm" style="border-radius: 8px; font-size: 0.8rem;">
                                    <i class="fa fa-print me-1"></i> 3-Copy Challan
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Prompt State: When no filter is applied -->
            <div id="hubPromptState" class="text-center py-5">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 70px; height: 70px;">
                        <i class="fa fa-filter fa-2x"></i>
                    </span>
                </div>
                <h5 class="fw-bold text-dark mb-1">Select Class to View Student Challans</h5>
                <p class="text-muted small mb-3">Please select a class from the filter dropdown above or search a student to generate/print challans.</p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <?php foreach(array_slice($data['classes'] ?? [], 0, 6) as $c): ?>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="selectHubClass('<?php echo $c->id; ?>')">
                            <i class="fa fa-graduation-cap me-1"></i><?php echo htmlspecialchars($c->class_name); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Empty Search State: When filtered but 0 results -->
            <div id="hubNoResultsState" class="text-center py-5" style="display: none;">
                <div class="mb-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 60px; height: 60px;">
                        <i class="fa fa-search fa-2x"></i>
                    </span>
                </div>
                <h6 class="fw-bold text-secondary mb-1">No Students Found</h6>
                <p class="text-muted small mb-0">No students match your selected filter or search term.</p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: BANK ACCOUNT SETTINGS -->
<div class="modal fade" id="bankSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fa fa-landmark text-primary me-2"></i>Collection Bank Account Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/fees/saveBank" method="POST">
                <input type="hidden" name="redirect_to" value="/fees/challan?success=bank_updated">
                <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="<?php echo htmlspecialchars($data['bank']->bank_name); ?>" required placeholder="e.g. Habib Bank Limited (HBL) / Meezan Bank">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Branch Name / Code</label>
                        <input type="text" name="branch_name" class="form-control" value="<?php echo htmlspecialchars($data['bank']->branch_name); ?>" placeholder="e.g. Main Commercial Boulevard Branch (0412)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Account Title</label>
                        <input type="text" name="account_title" class="form-control" value="<?php echo htmlspecialchars($data['bank']->account_title); ?>" required placeholder="e.g. PAK ACADEMY MODEL SCHOOL SYSTEM">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Account Number</label>
                            <input type="text" name="account_no" class="form-control font-monospace" value="<?php echo htmlspecialchars($data['bank']->account_no); ?>" required placeholder="e.g. 1029-3847-2910-01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">IBAN Number (24 Digits)</label>
                            <input type="text" name="iban" class="form-control font-monospace" value="<?php echo htmlspecialchars($data['bank']->iban); ?>" placeholder="e.g. PK36HABB0001029384729101">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-danger text-uppercase"><i class="fa fa-clock me-1"></i> Late Fee Surcharge Fine (Rs.)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">PKR</span>
                            <input type="number" step="10" name="late_fine" class="form-control fw-bold" value="<?php echo htmlspecialchars($data['late_fine'] ?? 200); ?>" placeholder="e.g. 200">
                        </div>
                        <small class="text-muted">This fine amount is automatically added to the challan if paid after the due date.</small>
                    </div>
                    <p class="text-muted small mb-0">
                        <i class="fa fa-info-circle text-primary me-1"></i> These bank particulars and late fine will be printed on every 3-copy bank challan voucher.
                    </p>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fa fa-save me-1"></i> Save Bank Info
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: BATCH GENERATE MONTHLY INVOICES -->
<div class="modal fade" id="batchGenerateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fa fa-cogs text-primary me-2"></i>Batch Generate Monthly Vouchers
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/fees/generateMonthly" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Target Class</label>
                        <select name="class_id" class="form-select" required>
                            <option value="0">-- All Classes (Entire School) --</option>
                            <?php foreach($data['classes'] as $cls): ?>
                                <option value="<?php echo $cls->id; ?>"><?php echo htmlspecialchars($cls->class_name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Billing Month</label>
                            <select name="month" class="form-select" required>
                                <?php 
                                $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                $curMonth = date('F');
                                foreach($months as $m):
                                ?>
                                    <option value="<?php echo $m; ?>" <?php echo ($m === $curMonth) ? 'selected' : ''; ?>><?php echo $m; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Billing Year</label>
                            <input type="number" name="year" class="form-control" value="<?php echo date('Y'); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Payment Due Date</label>
                        <input type="date" name="due_date" class="form-control" value="<?php echo date('Y-m-10'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Fee Package / Group</label>
                        <select name="fee_group_id" class="form-select">
                            <?php if(!empty($data['groups'])): ?>
                                <?php foreach($data['groups'] as $g): ?>
                                    <option value="<?php echo $g->id; ?>"><?php echo htmlspecialchars($g->group_name); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="alert alert-info py-2 px-3 small border-0 mb-0" style="border-radius: 8px;">
                        <i class="fa fa-shield-alt me-1"></i> Sibling concessions (10%/20%) and unique challan barcodes will be automatically applied to each student.
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fa fa-bolt me-1"></i> Generate Invoices Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classFilter = document.getElementById('hubClassFilter');
    const searchInput = document.getElementById('hubSearchInput');
    const resetBtn = document.getElementById('hubResetBtn');
    const rows = document.querySelectorAll('.hub-row');
    const countBadge = document.getElementById('hubCountBadge');
    const promptState = document.getElementById('hubPromptState');
    const noResultsState = document.getElementById('hubNoResultsState');
    const tableEl = document.getElementById('hubStudentsTable');

    function filterRows() {
        const cls = classFilter ? classFilter.value : '';
        const q = searchInput ? searchInput.value.toLowerCase().trim() : '';
        let visible = 0;

        // If no filter selected and no search query, show 0 entries and show prompt
        if (!cls && !q) {
            rows.forEach(r => {
                r.style.display = 'none';
                const cb = r.querySelector('.hub-student-checkbox');
                if (cb) cb.checked = false;
            });
            if (countBadge) countBadge.textContent = '0 Students';
            if (promptState) promptState.style.display = 'block';
            if (noResultsState) noResultsState.style.display = 'none';
            if (tableEl) tableEl.style.display = 'none';
            updateHubSelectedCount();
            return;
        }

        if (promptState) promptState.style.display = 'none';
        if (tableEl) tableEl.style.display = '';

        rows.forEach(r => {
            const rowCls = r.getAttribute('data-class-id');
            const searchStr = r.getAttribute('data-search');

            const matchCls = !cls || rowCls === cls;
            const matchQ = !q || searchStr.includes(q);

            if (matchCls && matchQ) {
                r.style.display = '';
                visible++;
            } else {
                r.style.display = 'none';
                const cb = r.querySelector('.hub-student-checkbox');
                if (cb) cb.checked = false;
            }
        });

        if (countBadge) countBadge.textContent = visible + ' Students';
        if (noResultsState) {
            noResultsState.style.display = (visible === 0) ? 'block' : 'none';
        }
        updateHubSelectedCount();
    }

    if (classFilter) classFilter.addEventListener('change', filterRows);
    if (searchInput) searchInput.addEventListener('input', filterRows);
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (classFilter) classFilter.value = '';
            if (searchInput) searchInput.value = '';
            filterRows();
        });
    }

    window.selectHubClass = function(classId) {
        if (classFilter) {
            classFilter.value = classId;
            filterRows();
        }
    };

    window.toggleHubSelectAll = function(checked) {
        document.querySelectorAll('.hub-row').forEach(row => {
            if (row.style.display !== 'none') {
                const cb = row.querySelector('.hub-student-checkbox');
                if (cb) cb.checked = checked;
            }
        });
        updateHubSelectedCount();
    };

    window.updateHubSelectedCount = function() {
        const checked = document.querySelectorAll('.hub-row:not([style*="display: none"]) .hub-student-checkbox:checked');
        const count = checked.length;
        const countEl = document.getElementById('hubSelectedCount');
        const btn = document.getElementById('btnHubPrintSelected');
        const selectAllCb = document.getElementById('hubSelectAllCheckbox');

        if (countEl) countEl.textContent = count;
        if (btn) {
            if (count > 0) {
                btn.classList.remove('d-none');
            } else {
                btn.classList.add('d-none');
            }
        }

        const totalVisible = document.querySelectorAll('.hub-row:not([style*="display: none"]) .hub-student-checkbox').length;
        if (selectAllCb) {
            selectAllCb.checked = (totalVisible > 0 && count === totalVisible);
            selectAllCb.indeterminate = (count > 0 && count < totalVisible);
        }
    };

    window.printHubSelectedChallans = function() {
        const checked = document.querySelectorAll('.hub-row:not([style*="display: none"]) .hub-student-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value).filter(Boolean);

        if (ids.length === 0) {
            alert('Please select at least one student.');
            return;
        }

        const url = '<?php echo URLROOT; ?>/fees/batchChallans?student_ids=' + encodeURIComponent(ids.join(','));
        window.open(url, '_blank');
    };

    // Check if class_id or search is in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('class_id') && classFilter) {
        classFilter.value = urlParams.get('class_id');
    }
    if (urlParams.has('search') && searchInput) {
        searchInput.value = urlParams.get('search');
    }

    // Initialize with default 0 entries / prompt state
    filterRows();
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
