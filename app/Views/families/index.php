<?php require APPROOT . '/Views/layouts/header.php'; ?>

<?php
$families = $data['families'] ?? [];
$totalFamilies = count($families);
?>

<style>
/* Simple, Decent, Clean Family Management Layout - Zero Horizontal Scroll */
.family-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    overflow: hidden;
}
.table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}
.family-decent-table {
    width: 100% !important;
    min-width: 680px; /* Fits 100% on desktop, smoothly scrolls horizontally on mobile */
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}
.family-decent-table thead th {
    background: #f8fafc;
    color: #334155;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 16px;
    border-bottom: 2px solid #e2e8f0;
    border-top: none;
    white-space: nowrap;
}
.family-decent-table tbody td {
    padding: 12px 16px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13.5px;
}
.family-decent-table tbody tr:hover {
    background-color: #f8fafc;
}
.family-decent-table tbody tr:last-child td {
    border-bottom: none;
}
.family-code-badge {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 12px;
    font-weight: 700;
    color: #1e293b;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    padding: 3px 8px;
    border-radius: 6px;
    letter-spacing: 0.5px;
    display: inline-block;
}
.parent-name-title {
    font-weight: 700;
    color: #0f172a;
    font-size: 13.5px;
    line-height: 1.3;
}
.parent-meta-sub {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 2px;
}
.actions-cell {
    white-space: nowrap;
    text-align: right;
}
</style>

<div class="families-page-wrap">

    <!-- Header Navigation & Quick Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted"><i class="fa fa-home me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Families &amp; Sibling Groups</li>
                </ol>
            </nav>
            <h2 class="h3 fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="fa fa-people-roof text-primary"></i> Family &amp; Sibling Groups
                <span class="badge bg-primary fs-6 rounded-pill" id="familyCountBadge"><?php echo $totalFamilies; ?> Families</span>
            </h2>
            <p class="text-secondary opacity-75 mb-0 small">
                Manage family records, link enrolled siblings, and automate institutional sibling discount policies.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addFamilyModal">
                <i class="fa fa-plus-circle me-1"></i> Register Family
            </button>
            <a href="<?php echo URLROOT; ?>/students/admission" class="btn btn-outline-primary btn-sm px-3 rounded-pill">
                <i class="fa fa-user-plus me-1"></i> Student Admission
            </a>
            <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                <i class="fa fa-receipt me-1"></i> Fee Register
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: 12px;">
            <i class="fa fa-check-circle fs-5"></i>
            <div>
                <?php 
                    if($_GET['success'] == 'created') echo "Family record registered successfully!";
                    elseif($_GET['success'] == 'updated') echo "Family details updated successfully!";
                    elseif($_GET['success'] == 'recalculated') echo "Sibling discounts recalculated and applied to all children!";
                    elseif($_GET['success'] == 'deleted') echo "Family record removed successfully.";
                    else echo "Action completed successfully!";
                ?>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Compact Live Filter & Search Toolbar (ZERO RELOAD) -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <!-- Live Search Box -->
                <div class="col-12 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-search"></i></span>
                        <input type="text" id="liveFamilySearch" class="form-control border-start-0" placeholder="Type Family Code, Father Name, CNIC, or Phone for instant search...">
                        <button type="button" class="btn btn-outline-secondary border-start-0" onclick="document.getElementById('liveFamilySearch').value=''; applyLiveFamilyFilters();" title="Clear Search">
                            <i class="fa fa-times text-muted"></i>
                        </button>
                    </div>
                </div>

                <!-- Filter by Sibling Status -->
                <div class="col-8 col-md-4">
                    <select id="filterSiblingStatus" class="form-select form-select-sm" onchange="applyLiveFamilyFilters()">
                        <option value="">-- All Families (Instant Filter) --</option>
                        <option value="siblings">Sibling Groups (2+ Enrolled)</option>
                        <option value="single">Single Child Enrolled (1)</option>
                        <option value="none">No Enrolled Children (0)</option>
                    </select>
                </div>

                <!-- Reset Filters Button -->
                <div class="col-4 col-md-2 d-flex">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-pill" onclick="resetFamilyFilters()">
                        <i class="fa fa-rotate-left me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple, Decent, Clean Family Table (Zero Horizontal Scroll) -->
    <div class="family-table-card">
        <div class="table-responsive">
            <table class="table family-decent-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Family Code</th>
                    <th style="width: 33%;">Father / Guardian Details</th>
                    <th style="width: 18%;">Enrolled Siblings</th>
                    <th style="width: 15%;">Sibling Concession</th>
                    <th style="width: 10%;">Remarks</th>
                    <th class="text-end pe-3" style="width: 12%;">Actions</th>
                </tr>
            </thead>
            <tbody id="familyTableBody">
                <?php if(empty($families)): ?>
                    <tr id="emptyFamilyRow">
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa fa-people-roof fa-3x mb-3 text-secondary opacity-25"></i>
                            <p class="mb-0 fw-bold">No family groups registered yet.</p>
                            <small>Click "+ Register Family" above to add a new family or siblings group.</small>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($families as $fam): 
                        $childrenCount = (int)($fam->total_children ?? 0);
                        $statusType = ($childrenCount > 1) ? 'siblings' : (($childrenCount == 1) ? 'single' : 'none');
                        $famJson = htmlspecialchars(json_encode([
                            'id' => (int)$fam->id,
                            'family_code' => $fam->family_code,
                            'father_name' => $fam->father_name,
                            'father_cnic' => $fam->father_cnic ?? '',
                            'guardian_phone' => $fam->guardian_phone ?? '',
                            'default_discount_percent' => (float)$fam->default_discount_percent,
                            'notes' => $fam->notes ?? ''
                        ]), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr class="family-row-item" 
                            data-code="<?php echo strtolower(htmlspecialchars($fam->family_code)); ?>" 
                            data-name="<?php echo strtolower(htmlspecialchars($fam->father_name)); ?>" 
                            data-cnic="<?php echo strtolower(htmlspecialchars($fam->father_cnic ?? '')); ?>" 
                            data-phone="<?php echo strtolower(htmlspecialchars($fam->guardian_phone ?? '')); ?>"
                            data-status="<?php echo $statusType; ?>">
                            
                            <!-- Column 1: Family Code -->
                            <td>
                                <span class="family-code-badge"><?php echo htmlspecialchars($fam->family_code); ?></span>
                            </td>

                            <!-- Column 2: Father / Guardian Details -->
                            <td>
                                <div class="parent-name-title"><?php echo htmlspecialchars($fam->father_name); ?></div>
                                <div class="parent-meta-sub">
                                    <?php if(!empty($fam->father_cnic)): ?>
                                        <span><i class="fa fa-id-card me-1 text-muted"></i><?php echo htmlspecialchars($fam->father_cnic); ?></span>
                                    <?php endif; ?>
                                    <?php if(!empty($fam->father_cnic) && !empty($fam->guardian_phone)): ?>
                                        <span class="mx-1 text-muted">•</span>
                                    <?php endif; ?>
                                    <?php if(!empty($fam->guardian_phone)): ?>
                                        <span><i class="fa fa-phone me-1 text-muted"></i><?php echo htmlspecialchars($fam->guardian_phone); ?></span>
                                    <?php endif; ?>
                                    <?php if(empty($fam->father_cnic) && empty($fam->guardian_phone)): ?>
                                        <span class="text-muted fst-italic">No contact/CNIC recorded</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Column 3: Enrolled Siblings -->
                            <td>
                                <?php if($childrenCount > 1): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary border-primary-subtle fw-bold btn-view-siblings px-2 py-1 rounded-pill" 
                                        data-code="<?php echo htmlspecialchars($fam->family_code); ?>" style="font-size: 0.78rem;">
                                        <i class="fa fa-children me-1"></i> <?php echo $childrenCount; ?> Siblings
                                    </button>
                                <?php elseif($childrenCount == 1): ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-2 py-1 rounded-pill btn-view-siblings" 
                                        data-code="<?php echo htmlspecialchars($fam->family_code); ?>" style="font-size: 0.78rem;">
                                        <i class="fa fa-user me-1"></i> 1 Child
                                    </button>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size: 0.75rem;">
                                        0 Enrolled
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Column 4: Sibling Concession -->
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 0.78rem;">
                                    <i class="fa fa-percent me-1" style="font-size: 10px;"></i><?php echo number_format($fam->default_discount_percent, 1); ?>% Concession
                                </span>
                            </td>

                            <!-- Column 5: Remarks / Notes -->
                            <td>
                                <?php if(!empty($fam->notes)): ?>
                                    <span class="small text-muted" title="<?php echo htmlspecialchars($fam->notes); ?>">
                                        <?php echo htmlspecialchars(mb_strimwidth($fam->notes, 0, 24, '...')); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Column 6: Actions -->
                            <td class="actions-cell pe-3">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- Recalculate Button -->
                                    <a href="<?php echo URLROOT; ?>/families/recalculate/<?php echo urlencode($fam->family_code); ?>" 
                                       class="btn btn-sm btn-outline-info px-2 py-1 rounded-pill" 
                                       title="Recalculate &amp; Apply Sibling Discounts" style="font-size: 0.76rem;">
                                        <i class="fa fa-calculator"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <button type="button" class="btn btn-sm btn-primary px-2 py-1 rounded-pill" 
                                        onclick='openEditFamilyModal(<?php echo $famJson; ?>)' 
                                        title="Edit Family" style="font-size: 0.76rem;">
                                        <i class="fa fa-pen me-1"></i> Edit
                                    </button>

                                    <!-- Delete Button -->
                                    <a href="<?php echo URLROOT; ?>/families/delete/<?php echo $fam->id; ?>" 
                                       class="btn btn-sm btn-outline-danger px-2 py-1 rounded-pill"
                                       onclick="return confirm('Are you sure you want to delete family \'<?php echo htmlspecialchars(addslashes($fam->family_code)); ?>\'?');"
                                       title="Delete Family" style="font-size: 0.76rem;">
                                        <i class="fa fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Row shown when live filter finds no matches -->
                <tr id="noFamilyFilterMatchesRow" style="display: none;">
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="fa fa-search fa-2x mb-2 text-secondary opacity-25"></i>
                        <p class="mb-0 fw-bold">No families match your search/filter criteria.</p>
                        <small>Click "Reset" above to show all registered families.</small>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: REGISTER NEW FAMILY GROUP                                         -->
<!-- ========================================================================= -->
<div class="modal fade" id="addFamilyModal" tabindex="-1" aria-labelledby="addFamilyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa fa-people-roof fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="addFamilyModalLabel">Register Family Group</h5>
                        <small class="text-white-50">Link siblings and define automated discount policy</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?php echo URLROOT; ?>/families/add" method="post" id="addFamilyForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Family Code (Optional) -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Family Code
                            </label>
                            <input type="text" name="family_code" class="form-control font-monospace" placeholder="e.g. FAM-001 (Auto if blank)">
                            <small class="text-muted smaller">Leave blank to auto-generate</small>
                        </div>

                        <!-- Father / Guardian Name -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Father / Guardian <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="father_name" class="form-control" required placeholder="e.g. Muhammad Tariq">
                        </div>

                        <!-- Father CNIC -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Father / Guardian CNIC
                            </label>
                            <input type="text" name="father_cnic" class="form-control" placeholder="e.g. 42101-1234567-1">
                            <small class="text-muted smaller">Auto-links students with matching CNIC</small>
                        </div>

                        <!-- Guardian Phone -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Contact Phone
                            </label>
                            <input type="text" name="guardian_phone" class="form-control" placeholder="e.g. 0300-1234567">
                        </div>

                        <!-- Sibling Discount (%) -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Default Sibling Discount (%)
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.5" min="0" max="100" name="default_discount_percent" class="form-control" value="10.00" required>
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted smaller">Auto-applied: 2nd child (10%), 3rd child (20%)</small>
                        </div>

                        <!-- Family Notes -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Remarks / Concession Notes
                            </label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes or special concessions..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-3 px-4">
                    <button type="button" class="btn btn-secondary px-3 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">
                        <i class="fa fa-save me-1"></i> Save Family Group
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: EDIT FAMILY GROUP                                                -->
<!-- ========================================================================= -->
<div class="modal fade" id="editFamilyModal" tabindex="-1" aria-labelledby="editFamilyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa fa-pen-to-square fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="editFamilyModalLabel">Edit Family Group</h5>
                        <small class="text-white-50" id="editFamilySubtitle">Update details &amp; concession policy</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?php echo URLROOT; ?>/families/edit" method="post" id="editFamilyForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <input type="hidden" name="id" id="editFamilyId" value="">

                <div class="modal-body p-4">
                    <!-- Family Header Card -->
                    <div class="p-3 rounded-3 bg-light mb-3 border d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" id="editModalFamilyName">Father Name</h6>
                            <small class="text-muted">Family Identification</small>
                        </div>
                        <span class="family-code-badge" id="editModalFamilyCode">FAM-000</span>
                    </div>

                    <div class="row g-3">
                        <!-- Father / Guardian Name -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Father / Guardian <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="father_name" id="editFatherName" class="form-control" required>
                        </div>

                        <!-- Father CNIC -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Father / Guardian CNIC
                            </label>
                            <input type="text" name="father_cnic" id="editFatherCnic" class="form-control" placeholder="e.g. 42101-1234567-1">
                        </div>

                        <!-- Guardian Phone -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Contact Phone
                            </label>
                            <input type="text" name="guardian_phone" id="editGuardianPhone" class="form-control" placeholder="e.g. 0300-1234567">
                        </div>

                        <!-- Sibling Discount (%) -->
                        <div class="col-12 col-sm-6">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Sibling Discount (%)
                            </label>
                            <div class="input-group">
                                <input type="number" step="0.5" min="0" max="100" name="default_discount_percent" id="editDiscountPercent" class="form-control" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>

                        <!-- Family Notes -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark mb-1">
                                Remarks / Concession Notes
                            </label>
                            <textarea name="notes" id="editNotes" class="form-control" rows="2" placeholder="Optional notes or special concessions..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-3 px-4">
                    <button type="button" class="btn btn-secondary px-3 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">
                        <i class="fa fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: VIEW ENROLLED SIBLINGS                                           -->
<!-- ========================================================================= -->
<div class="modal fade" id="siblingsModal" tabindex="-1" aria-labelledby="siblingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa fa-children fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="siblingsModalLabel">
                            Enrolled Siblings in Family: <span id="modalFamilyCodeBadge" class="family-code-badge bg-white text-dark ms-1"></span>
                        </h5>
                        <small class="text-white-50">Students enrolled under this family record</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="font-size: 12px; text-transform: uppercase;">Student Name</th>
                                <th style="font-size: 12px; text-transform: uppercase;">Admission No</th>
                                <th style="font-size: 12px; text-transform: uppercase;">Class &amp; Section</th>
                                <th style="font-size: 12px; text-transform: uppercase;">Discount Applied</th>
                                <th style="font-size: 12px; text-transform: uppercase;">Status</th>
                                <th class="text-end pe-4" style="font-size: 12px; text-transform: uppercase;">Profile</th>
                            </tr>
                        </thead>
                        <tbody id="siblingsTableBody">
                            <!-- Populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Live Client-Side Filtering (ZERO PAGE RELOAD)
    function applyLiveFamilyFilters() {
        var query = (document.getElementById('liveFamilySearch') ? document.getElementById('liveFamilySearch').value : '').toLowerCase().trim();
        var statusFilter = document.getElementById('filterSiblingStatus') ? document.getElementById('filterSiblingStatus').value : '';
        var rows = document.querySelectorAll('.family-row-item');
        var visibleCount = 0;

        rows.forEach(function(row) {
            var code = (row.getAttribute('data-code') || '').toLowerCase();
            var name = (row.getAttribute('data-name') || '').toLowerCase();
            var cnic = (row.getAttribute('data-cnic') || '').toLowerCase();
            var phone = (row.getAttribute('data-phone') || '').toLowerCase();
            var status = row.getAttribute('data-status') || '';

            var matchesQuery = !query || code.includes(query) || name.includes(query) || cnic.includes(query) || phone.includes(query);
            var matchesStatus = !statusFilter || status === statusFilter;

            if (matchesQuery && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Toggle empty message row
        var noMatchesRow = document.getElementById('noFamilyFilterMatchesRow');
        if (noMatchesRow) {
            noMatchesRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }

        // Update count badge
        var badge = document.getElementById('familyCountBadge');
        if (badge) {
            badge.textContent = visibleCount + ' Families';
        }
    }

    function resetFamilyFilters() {
        if (document.getElementById('liveFamilySearch')) {
            document.getElementById('liveFamilySearch').value = '';
        }
        if (document.getElementById('filterSiblingStatus')) {
            document.getElementById('filterSiblingStatus').value = '';
        }
        applyLiveFamilyFilters();
    }

    // Attach real-time input listener on search box
    document.addEventListener('DOMContentLoaded', function() {
        var liveSearch = document.getElementById('liveFamilySearch');
        if (liveSearch) {
            liveSearch.addEventListener('input', applyLiveFamilyFilters);
        }
    });

    // 2. Open Edit Family Modal
    function openEditFamilyModal(fam) {
        document.getElementById('editFamilyId').value = fam.id;
        document.getElementById('editFatherName').value = fam.father_name || '';
        document.getElementById('editFatherCnic').value = fam.father_cnic || '';
        document.getElementById('editGuardianPhone').value = fam.guardian_phone || '';
        document.getElementById('editDiscountPercent').value = fam.default_discount_percent || '10.00';
        document.getElementById('editNotes').value = fam.notes || '';

        document.getElementById('editModalFamilyName').innerText = fam.father_name || 'Family Record';
        document.getElementById('editModalFamilyCode').innerText = fam.family_code || 'FAM-000';
        document.getElementById('editFamilyForm').action = '<?php echo URLROOT; ?>/families/edit/' + fam.id;

        var modal = new bootstrap.Modal(document.getElementById('editFamilyModal'));
        modal.show();
    }

    // 3. View Enrolled Siblings Modal (AJAX)
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('siblingsModal');
        const modal = new bootstrap.Modal(modalEl);
        const modalFamilyCodeBadge = document.getElementById('modalFamilyCodeBadge');
        const tableBody = document.getElementById('siblingsTableBody');

        document.querySelectorAll('.btn-view-siblings').forEach(btn => {
            btn.addEventListener('click', function() {
                const code = this.dataset.code;
                modalFamilyCodeBadge.textContent = code;
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><i class="fa fa-spinner fa-spin me-2"></i>Loading siblings...</td></tr>';
                modal.show();

                fetch('<?php echo URLROOT; ?>/families/view_children/' + encodeURIComponent(code))
                    .then(res => res.json())
                    .then(data => {
                        if(data.success && data.children && data.children.length > 0) {
                            let html = '';
                            data.children.forEach(child => {
                                const disc = parseFloat(child.sibling_discount_percent || 0);
                                const discBadge = disc > 0 ? 
                                    `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">${disc}% Sibling Discount</span>` : 
                                    `<span class="badge bg-light text-muted border px-2 py-1 rounded-pill">Primary (0%)</span>`;
                                
                                html += `<tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">${child.name}</div>
                                        <small class="text-muted">${child.bform_cnic ? 'B-Form: ' + child.bform_cnic : ''}</small>
                                    </td>
                                    <td><span class="badge bg-light text-dark border font-monospace">${child.admission_no}</span></td>
                                    <td>${child.class_name || ''} &bull; ${child.section_name || ''}</td>
                                    <td>${discBadge}</td>
                                    <td><span class="badge bg-success-subtle text-success">${child.status || 'Active'}</span></td>
                                    <td class="text-end pe-4">
                                        <a href="<?php echo URLROOT; ?>/students/profile/${child.id}" class="btn btn-sm btn-outline-primary px-2 py-1 rounded-pill" style="font-size: 0.76rem;">
                                            Profile
                                        </a>
                                    </td>
                                </tr>`;
                            });
                            tableBody.innerHTML = html;
                        } else {
                            tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No children currently linked to this family code.</td></tr>';
                        }
                    })
                    .catch(err => {
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load siblings data.</td></tr>';
                    });
            });
        });
    });
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
