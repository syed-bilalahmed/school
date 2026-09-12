<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- SUCCESS ALERTS -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 border-0 shadow-sm" style="border-radius: 12px;" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div>
            <?php 
                if($_GET['success'] == 'assigned') echo "Fees successfully assigned to class!";
                else echo "Action completed successfully!";
            ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- UNIFIED TABS & ACTIONS BAR (NO FULL PAGE RELOADS) -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1">
            <i class="fa fa-cash-register text-primary me-2"></i>Fees Hub &amp; Collection
        </h2>
        <p class="text-muted small mb-0">Runtime class &amp; section based fees management with instant in-place payment popups.</p>
    </div>
    
    <!-- Top Action Tabs / Modal Triggers (Pure AJAX & Safe Modals, No Freezing) -->
    <div class="d-flex flex-wrap align-items-center gap-2">
        <button type="button" class="btn btn-primary btn-sm px-3 py-2 fw-bold shadow-sm" style="border-radius: 10px;" onclick="scrollToFeeRegister()">
            <i class="fa fa-cash-register me-1"></i> Fee Register
        </button>
        <button type="button" class="btn btn-dark text-white btn-sm px-3 py-2 fw-bold shadow-sm" style="border-radius: 10px;" onclick="openFeeGeneratorModal()" title="Batch Monthly Vouchers & Fresh Admission Token Generator">
            <i class="fa fa-ticket-alt text-warning me-1"></i> Fee Generator &amp; Token
        </button>
        <button type="button" class="btn btn-warning text-dark btn-sm px-3 py-2 fw-bold shadow-sm" style="border-radius: 10px;" onclick="openClassFeeParticularsModal()">
            <i class="fa fa-layer-group me-1"></i> Class Fee Structure
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-bold" style="border-radius: 10px;" onclick="openFeeMasterModal()">
            <i class="fa fa-cogs text-primary me-1"></i> Fee Master
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-bold" style="border-radius: 10px;" onclick="openFeeGroupsModal()">
            <i class="fa fa-folder text-success me-1"></i> Fee Groups
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-bold" style="border-radius: 10px;" onclick="openFeeTypesModal()">
            <i class="fa fa-tags text-info me-1"></i> Fee Types
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-bold" style="border-radius: 10px;" onclick="openAssignFeesModal()">
            <i class="fa fa-user-plus text-secondary me-1"></i> Assign Fees
        </button>
        <a href="<?php echo URLROOT; ?>/fees/challan" class="btn btn-outline-primary btn-sm px-3 py-2 fw-bold shadow-sm" style="border-radius: 10px;">
            <i class="fa fa-money-check text-primary me-1"></i> Bank Challans
        </a>
        <a href="<?php echo URLROOT; ?>/fees/defaulters" class="btn btn-outline-danger btn-sm px-3 py-2 fw-bold shadow-sm" style="border-radius: 10px;">
            <i class="fa fa-user-clock text-danger me-1"></i> Defaulters Ledger
        </a>
        <a href="<?php echo URLROOT; ?>/fees_workflow_guide.html" target="_blank" class="btn btn-outline-info text-dark btn-sm px-3 py-2 fw-bold shadow-sm" style="border-radius: 10px;" title="Open Visual Workflow &amp; Fees Architecture Guide">
            <i class="fa fa-book-open text-info me-1"></i> Workflow Guide
        </a>
    </div>
</div>

<!-- CLASS & SECTION RUNTIME FILTER PANEL -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 16px;">
    <div class="card-body p-4">
        <div class="row g-3 align-items-end">
            <!-- Select Class -->
            <div class="col-lg-3 col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-bold small text-muted text-uppercase mb-0">
                        <i class="fa fa-graduation-cap text-primary me-1"></i> Class
                    </label>
                    <button type="button" id="btnQuickEditClassFee" class="btn btn-link p-0 text-primary small fw-bold text-decoration-none" style="display: none; font-size: 0.74rem;" onclick="openClassFeeParticularsModal(document.getElementById('filterClass').value)">
                        <i class="fa fa-edit me-1"></i>Edit Fee Heads
                    </button>
                </div>
                <select id="filterClass" class="form-select border-1" style="border-radius: 10px; font-weight: 600; padding: 10px 14px;">
                    <option value="">-- All Classes --</option>
                    <?php if(!empty($data['classes'])): ?>
                        <?php foreach($data['classes'] as $cls): ?>
                            <option value="<?php echo $cls->id; ?>">
                                <?php echo htmlspecialchars($cls->class_name, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Select Section (Dynamically updated based on class) -->
            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                    <i class="fa fa-users text-primary me-1"></i> Section
                </label>
                <select id="filterSection" class="form-select border-1" style="border-radius: 10px; font-weight: 600; padding: 10px 14px;">
                    <option value="">-- All Sections --</option>
                    <?php if(!empty($data['sections'])): ?>
                        <?php foreach($data['sections'] as $sec): ?>
                            <option value="<?php echo $sec->id; ?>" data-class-id="<?php echo $sec->class_id; ?>">
                                <?php echo htmlspecialchars($sec->class_name . ' - ' . $sec->section_name, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Instant Live Filter Search Input -->
            <div class="col-lg-4 col-md-8">
                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                    <i class="fa fa-search text-primary me-1"></i> Live Filter
                </label>
                <div class="position-relative">
                    <input type="text" id="liveSearchInput" class="form-control" placeholder="Search by name, roll no, adm no, phone..." style="border-radius: 10px; padding: 10px 38px 10px 14px;">
                    <button type="button" id="clearSearchBtn" class="btn btn-sm text-muted position-absolute end-0 top-50 translate-middle-y border-0 bg-transparent pe-3" style="display: none;">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Quick Reset Action -->
            <div class="col-lg-2 col-md-4">
                <button type="button" id="resetFiltersBtn" class="btn btn-outline-secondary w-100 fw-bold" style="border-radius: 10px; padding: 10px 16px;">
                    <i class="fa fa-redo-alt me-1"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- REAL-TIME METRIC SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; font-size: 1.25rem;">
                    <i class="fa fa-user-graduate"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Students Visible</div>
                    <div class="h4 fw-bold mb-0 text-dark" id="statStudentCount">0</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9; font-size: 1.25rem;">
                    <i class="fa fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Total Assigned</div>
                    <div class="h4 fw-bold mb-0 text-dark" id="statTotalFees"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> 0.00</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 1.25rem;">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Total Collected</div>
                    <div class="h4 fw-bold mb-0 text-success" id="statTotalPaid"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> 0.00</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="brand-icon-box" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; font-size: 1.25rem;">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.72rem;">Outstanding Balance</div>
                    <div class="h4 fw-bold mb-0 text-danger" id="statTotalBalance"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> 0.00</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- STUDENT FEES REGISTER TABLE -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 16px; overflow: hidden;">
    <div class="card-header bg-white py-3 px-4 d-flex flex-wrap justify-content-between align-items-center border-bottom gap-2">
        <div class="d-flex align-items-center gap-3">
            <h5 class="mb-0 fw-bold text-dark">Student Fee Register</h5>
            <span class="badge bg-primary rounded-pill px-3 py-1 font-monospace" id="visibleCounterBadge">
                0 Students
            </span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Bulk Print Selected Challans Button -->
            <button type="button" id="btnPrintSelectedChallans" class="btn btn-success btn-sm px-3 py-2 fw-bold shadow-sm animate__animated animate__fadeIn" style="border-radius: 8px; display: none;" onclick="printSelectedChallans()">
                <i class="fa fa-print me-1"></i> Print Selected Challans (<span id="selectedCountNum">0</span>)
            </button>
            <div class="text-muted small" id="activeFilterLabel">
                <i class="fa fa-filter text-primary me-1"></i> <span id="labelFilterText">No Filter Applied &bull; Select Class or Filter to View</span>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <style>
            /* Clean responsive fit for Student Fee Register Table - No Horizontal Scroll */
            #feeStudentsTable {
                width: 100% !important;
                table-layout: auto;
            }
            #feeStudentsTable th, #feeStudentsTable td {
                padding: 10px 8px !important;
                vertical-align: middle;
            }
            #feeStudentsTable th:first-child, #feeStudentsTable td:first-child {
                padding-left: 18px !important;
            }
            #feeStudentsTable th:last-child, #feeStudentsTable td:last-child {
                padding-right: 18px !important;
            }
            @media (min-width: 992px) {
                .table-responsive-fit {
                    overflow-x: visible !important;
                }
            }
        </style>
        <div class="table-responsive table-responsive-fit">
            <table class="table table-hover align-middle mb-0" id="feeStudentsTable">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th style="font-size: 0.74rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 85px;">Adm No</th>
                        <th>
                            <div class="d-flex align-items-center gap-2">
                                <input type="checkbox" id="selectAllStudentsCheckbox" class="form-check-input m-0" style="cursor: pointer; width: 17px; height: 17px; border-radius: 4px; border: 1.5px solid #94a3b8;" title="Select All Visible Students" onchange="toggleSelectAllVisible(this)">
                                <span style="font-size: 0.74rem; text-transform: uppercase; color: #64748b; font-weight: 700;">Student Name</span>
                            </div>
                        </th>
                        <th style="font-size: 0.74rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 140px;">Class &amp; Section</th>
                        <th style="font-size: 0.74rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 120px;">Contact</th>
                        <th class="text-end" style="font-size: 0.74rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 95px;">Total</th>
                        <th class="text-end" style="font-size: 0.74rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 90px;">Paid</th>
                        <th class="text-end" style="font-size: 0.74rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 95px;">Balance</th>
                        <th class="text-center" style="font-size: 0.74rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 85px;">Status</th>
                        <th class="text-end" style="font-size: 0.74rem; text-transform: uppercase; color: #64748b; font-weight: 700; width: 135px;">Action</th>
                    </tr>
                </thead>
                <tbody id="feeStudentsTableBody">
                    <?php if(!empty($data['students'])): ?>
                        <?php foreach($data['students'] as $student): 
                            $studentId = $student->student_id ?? $student->id;
                            $totalFee = (float)($student->total_amount ?? 0);
                            $totalPaid = (float)($student->total_paid ?? 0);
                            $balance = max(0, $totalFee - $totalPaid);
                            
                            // Determine status
                            if($totalFee <= 0){
                                $statusBadge = '<span class="badge bg-light text-muted border px-2 py-1">No Fees</span>';
                            } elseif($balance <= 0){
                                $statusBadge = '<span class="badge bg-success px-2 py-1"><i class="fa fa-check me-1"></i>Paid</span>';
                            } elseif($totalPaid > 0){
                                $statusBadge = '<span class="badge bg-warning text-dark px-2 py-1"><i class="fa fa-clock me-1"></i>Partial</span>';
                            } else {
                                $statusBadge = '<span class="badge bg-danger px-2 py-1"><i class="fa fa-times me-1"></i>Unpaid</span>';
                            }

                            $studentName = $student->student_name ?? $student->name ?? 'Unknown Student';
                            $initials = strtoupper(substr($studentName, 0, 2));
                            $admNo = $student->admission_no ?? '-';
                            $rollNo = $student->roll_no ?? '';
                            $className = $student->class_name ?? 'Unassigned';
                            $sectionName = $student->section_name ?? 'General';
                            $parentPhone = $student->parent_phone ?? 'N/A';

                            $searchContent = strtolower($studentName . ' ' . $admNo . ' ' . $rollNo . ' ' . $className . ' ' . $sectionName . ' ' . $parentPhone);
                        ?>
                            <tr class="student-row" 
                                id="studentRow_<?php echo $studentId; ?>"
                                style="display: none;"
                                data-student-id="<?php echo $studentId; ?>"
                                data-class-id="<?php echo (int)$student->class_id; ?>" 
                                data-section-id="<?php echo (int)$student->section_id; ?>"
                                data-fee="<?php echo $totalFee; ?>"
                                data-paid="<?php echo $totalPaid; ?>"
                                data-balance="<?php echo $balance; ?>"
                                data-search="<?php echo htmlspecialchars($searchContent, ENT_QUOTES, 'UTF-8'); ?>">
                                
                                <!-- Admission Number -->
                                <td class="ps-4 font-monospace fw-bold text-muted small">
                                    #<?php echo htmlspecialchars($admNo, ENT_QUOTES, 'UTF-8'); ?>
                                </td>

                                <!-- Student Name & Roll (Avatar removed, Checkbox added) -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="checkbox" class="form-check-input student-select-checkbox m-0" value="<?php echo $studentId; ?>" style="cursor: pointer; width: 17px; height: 17px; border-radius: 4px; border: 1.5px solid #94a3b8;" title="Select student for batch challan print" onchange="updateSelectedChallanCount()">
                                        <div>
                                            <div class="fw-bold text-dark mb-0 student-row-name" style="font-size: 0.88rem;">
                                                <?php echo htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8'); ?>
                                            </div>
                                            <?php if(!empty($rollNo)): ?>
                                                <div class="text-muted" style="font-size: 0.72rem;">Roll No: <?php echo htmlspecialchars($rollNo, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Class & Section -->
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.78rem;">
                                        <?php echo htmlspecialchars($className, ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if(!empty($sectionName)): ?>
                                            <span class="text-primary fw-bold">&bull; <?php echo htmlspecialchars($sectionName, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php endif; ?>
                                    </span>
                                </td>

                                <!-- Parent Phone -->
                                <td class="small text-muted">
                                    <?php if($parentPhone !== 'N/A'): ?>
                                        <a href="tel:<?php echo htmlspecialchars($parentPhone, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none text-muted hover-primary">
                                            <i class="fa fa-phone-alt text-primary me-1 fa-xs"></i><?php echo htmlspecialchars($parentPhone, ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Total Assigned -->
                                <td class="text-end font-monospace fw-bold cell-total-fee" style="font-size: 0.88rem; color: #475569;">
                                    $<?php echo number_format($totalFee, 2); ?>
                                </td>

                                <!-- Paid Amount -->
                                <td class="text-end font-monospace fw-bold text-success cell-total-paid" style="font-size: 0.88rem;">
                                    $<?php echo number_format($totalPaid, 2); ?>
                                </td>

                                <!-- Balance Due -->
                                <td class="text-end font-monospace fw-bold cell-balance <?php echo ($balance > 0) ? 'text-danger' : 'text-muted'; ?>" style="font-size: 0.88rem;">
                                    $<?php echo number_format($balance, 2); ?>
                                </td>

                                <!-- Status Badge -->
                                <td class="text-center cell-status">
                                    <?php echo $statusBadge; ?>
                                </td>

                                <!-- Action Button (Opens in-page popup modal without reload) -->
                                <td class="pe-4 text-end cell-action">
                                    <div class="d-inline-flex gap-1">
                                        <a href="<?php echo URLROOT; ?>/fees/challan/<?php echo $studentId; ?>" target="_blank" 
                                           class="btn btn-sm btn-outline-primary" 
                                           style="border-radius: 8px; font-size: 0.78rem; padding: 5px 10px;" 
                                           title="Print 3-Copy Bank Challan">
                                            <i class="fa fa-print me-1"></i> Challan
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm <?php echo ($balance > 0) ? 'btn-primary shadow-sm' : 'btn-outline-secondary'; ?>" 
                                                style="border-radius: 8px; font-size: 0.78rem; padding: 5px 12px;"
                                                onclick="openCollectFeeModal(<?php echo $studentId; ?>)">
                                            <i class="fa <?php echo ($balance > 0) ? 'fa-hand-holding-usd' : 'fa-receipt'; ?> me-1"></i>
                                            <?php echo ($balance > 0) ? 'Collect' : 'View'; ?>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- 1. Default Prompt State: Shown by default when no filter is applied -->
            <div id="selectFilterPromptState" class="py-5 text-center">
                <div class="brand-icon-box mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 18px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; font-size: 1.5rem; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-filter"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Select Class or Filter to View Students</h5>
                <p class="text-muted small mb-3" style="max-width: 480px; margin-left: auto; margin-right: auto;">
                    By default, no student records are shown. Please select a <strong>Class</strong>, <strong>Section</strong>, or type in the <strong>Live Filter</strong> search box above to load fee records.
                </p>
                <?php if(!empty($data['classes'])): ?>
                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-2">
                        <span class="text-muted small align-self-center me-1">Quick Class:</span>
                        <?php foreach(array_slice($data['classes'], 0, 6) as $qCls): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" onclick="selectClassQuick('<?php echo $qCls->id; ?>')">
                                <i class="fa fa-graduation-cap me-1"></i><?php echo htmlspecialchars($qCls->class_name, ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 2. No Results State: Shown when a filter IS applied but 0 matching students found -->
            <div id="noResultsState" class="py-5 text-center" style="display: none;">
                <div class="brand-icon-box mx-auto mb-3" style="width: 56px; height: 56px; border-radius: 16px; background: rgba(148, 163, 184, 0.1); color: #94a3b8; font-size: 1.5rem; display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-search"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">No Students Found</h5>
                <p class="text-muted small mb-3" style="max-width: 400px; margin-left: auto; margin-right: auto;">
                    No student records match the selected class, section, or search criteria.
                </p>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="document.getElementById('resetFiltersBtn').click();">
                    <i class="fa fa-sync-alt me-1"></i> Reset All Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 1. IN-PAGE DYNAMIC FEE COLLECTION & PAYMENT HISTORY MODAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="collectFeeModal" tabindex="-1" aria-labelledby="collectFeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            
            <!-- Modal Header -->
            <div class="modal-header py-3 px-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color: #ffffff;">
                <div class="d-flex align-items-center gap-3">
                    <div id="modalStudentAvatar" class="avatar" style="width: 42px; height: 42px; border-radius: 50%; background: var(--primary-gradient); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700;">
                        ST
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="modalStudentName">Collect Student Fee</h5>
                        <div class="text-white-50 small" id="modalStudentMeta">Adm No: #-- &bull; Class: --</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <!-- Loading State Spinner -->
                <div id="modalLoadingState" class="py-5 text-center">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <div class="text-muted fw-bold">Loading Student Fee Ledger...</div>
                </div>

                <!-- Main Modal Content -->
                <div id="modalMainContent" style="display: none;">
                    <!-- Notification Banner inside Modal -->
                    <div id="modalAlertBox" style="display: none;"></div>

                    <!-- Financial Summary Tiles -->
                    <div class="row g-3 mb-3">
                        <div class="col-sm-4">
                            <div class="p-3 bg-white rounded-3 border text-center shadow-xs">
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Assigned</div>
                                <div class="h5 fw-bold mb-0 text-dark" id="modalTotalAssigned"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> 0.00</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-white rounded-3 border text-center shadow-xs">
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Total Paid</div>
                                <div class="h5 fw-bold mb-0 text-success" id="modalTotalPaid"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> 0.00</div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-white rounded-3 border text-center shadow-xs">
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Net Balance Due</div>
                                <div class="h5 fw-bold mb-0 text-danger" id="modalTotalBalance"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> 0.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub Tabs: Collect Payment vs Payment History -->
                    <ul class="nav nav-pills mb-3 p-1 bg-white rounded-3 border shadow-xs" id="collectModalTabs" role="tablist">
                        <li class="nav-item flex-fill text-center" role="presentation">
                            <button class="nav-link active fw-bold w-100 py-2" id="tab-payment-btn" data-bs-toggle="pill" data-bs-target="#tab-payment-content" type="button" role="tab">
                                <i class="fa fa-hand-holding-usd me-1"></i> Collect Payment
                            </button>
                        </li>
                        <li class="nav-item flex-fill text-center" role="presentation">
                            <button class="nav-link fw-bold w-100 py-2" id="tab-history-btn" data-bs-toggle="pill" data-bs-target="#tab-history-content" type="button" role="tab">
                                <i class="fa fa-history me-1"></i> Payment History &amp; Receipts 
                                <span class="badge bg-secondary ms-1" id="modalHistoryCount">0</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="collectModalTabsContent">
                        <!-- TAB 1: COLLECT PAYMENT -->
                        <div class="tab-pane fade show active" id="tab-payment-content" role="tabpanel">
                            <!-- Invoices List Table -->
                            <div class="card border-0 shadow-xs mb-3" style="border-radius: 12px; overflow: hidden;">
                                <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="fw-bold small text-muted text-uppercase">Assigned Fee Invoices</span>
                                    <span class="badge bg-light text-dark border" id="modalInvoiceCount">0 Invoices</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 180px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-3 py-2">Fee Group</th>
                                                    <th class="py-2">Code</th>
                                                    <th class="py-2 text-end">Amount</th>
                                                    <th class="py-2 text-end">Paid</th>
                                                    <th class="py-2 text-end">Balance</th>
                                                    <th class="pe-3 py-2 text-end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="modalInvoiceTableBody">
                                                <!-- Populated dynamically via JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Instant In-Page Payment Box -->
                            <div class="card border-0 shadow-sm" style="border-radius: 14px; background: #ffffff;">
                                <div class="card-header bg-white py-2 px-3 border-bottom">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.92rem;">
                                        <i class="fa fa-credit-card text-primary me-2"></i>Record Transaction
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <form id="modalPaymentForm" onsubmit="submitPaymentAjax(event)">
                                        <input type="hidden" id="payStudentId" name="student_id" value="">
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small text-muted text-uppercase mb-1">Target Fee Invoice <span class="text-danger">*</span></label>
                                                <select id="payStudentFeeId" name="student_fee_id" class="form-select form-select-sm" required style="border-radius: 8px;">
                                                    <!-- Populated dynamically -->
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small text-muted text-uppercase mb-1">Amount (<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?>) <span class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="0.01" id="payAmount" name="amount" class="form-control form-control-sm" required style="border-radius: 8px;">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small text-muted text-uppercase mb-1">Payment Mode <span class="text-danger">*</span></label>
                                                <select id="payMode" name="mode" class="form-select form-select-sm" style="border-radius: 8px;">
                                                    <option value="Cash">Cash</option>
                                                    <option value="Cheque">Cheque</option>
                                                    <option value="Bank Transfer">Bank Transfer</option>
                                                    <option value="Online">Online / Card</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small text-muted text-uppercase mb-1">Transaction Note / Memo</label>
                                                <input type="text" id="payNote" name="note" class="form-control form-control-sm" placeholder="e.g. Receipt #5021 or reference..." style="border-radius: 8px;">
                                            </div>
                                        </div>

                                        <div class="mt-3 d-flex justify-content-between align-items-center">
                                            <span class="text-muted small" style="font-size: 0.78rem;">
                                                <i class="fa fa-bolt text-warning me-1"></i> Instantly updates register in real-time.
                                            </span>
                                            <button type="submit" id="btnSubmitPayment" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm" style="border-radius: 8px;">
                                                <i class="fa fa-check-circle me-1"></i> Record Payment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: PAYMENT HISTORY & VOID -->
                        <div class="tab-pane fade" id="tab-history-content" role="tabpanel">
                            <div class="card border-0 shadow-xs" style="border-radius: 12px; overflow: hidden;">
                                <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="fw-bold small text-muted text-uppercase">Transaction History</span>
                                    <span class="text-muted small">Manage past receipts or void errors</span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-3 py-2">Receipt #</th>
                                                    <th class="py-2">Date</th>
                                                    <th class="py-2">Invoice / Group</th>
                                                    <th class="py-2">Mode</th>
                                                    <th class="py-2 text-end">Amount</th>
                                                    <th class="py-2">Note</th>
                                                    <th class="pe-3 py-2 text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="modalHistoryTableBody">
                                                <!-- Populated dynamically via JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white py-3 px-4 border-top">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal" style="border-radius: 8px;">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 2. ASSIGN FEES MODAL (AJAX POWERED, NO PAGE RELOAD) -->
<!-- ========================================================================= -->
<div class="modal fade" id="assignFeesModal" tabindex="-1" aria-labelledby="assignFeesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-light py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="assignFeesModalLabel">
                    <i class="fa fa-user-plus text-primary me-2"></i>Assign Fee Group to Class
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="modalAssignFeesForm" onsubmit="submitAssignFeesAjax(event)">
                <div class="modal-body p-4">
                    <div id="modalAssignAlertBox" style="display: none;"></div>

                    <p class="text-muted small mb-3">
                        Select a fee group and class to assign the fee structure to all students in that class.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Fee Group <span class="text-danger">*</span></label>
                        <select id="assign_fee_group_id" name="fee_group_id" class="form-select" required style="border-radius: 8px;">
                            <option value="">-- Choose Fee Group --</option>
                            <?php if(!empty($data['groups'])): ?>
                                <?php foreach($data['groups'] as $grp): ?>
                                    <option value="<?php echo $grp->id; ?>"><?php echo htmlspecialchars($grp->group_name, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Class <span class="text-danger">*</span></label>
                        <select id="assign_class_id" name="class_id" class="form-select" required style="border-radius: 8px;">
                            <option value="">-- Choose Class --</option>
                            <?php if(!empty($data['classes'])): ?>
                                <?php foreach($data['classes'] as $cls): ?>
                                    <option value="<?php echo $cls->id; ?>"><?php echo htmlspecialchars($cls->class_name, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Section (Optional)</label>
                        <select id="assign_section_id" name="section_id" class="form-select" style="border-radius: 8px;">
                            <option value="">-- All Sections --</option>
                            <?php if(!empty($data['sections'])): ?>
                                <?php foreach($data['sections'] as $sec): ?>
                                    <option value="<?php echo $sec->id; ?>" data-class-id="<?php echo $sec->class_id; ?>">
                                        <?php echo htmlspecialchars($sec->class_name . ' - ' . $sec->section_name, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="p-3 bg-light rounded-3 text-muted small border">
                        <i class="fa fa-info-circle text-primary me-1"></i> This applies each fee master item in this group to all enrolled students.
                    </div>
                </div>

                <div class="modal-footer bg-light py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" id="btnSubmitAssign" class="btn btn-primary px-4 fw-bold" style="border-radius: 8px;">
                        <i class="fa fa-check me-1"></i> Assign Fees Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 3. FEE GROUPS CRUD MODAL (INLINE ADD, EDIT, DELETE & MASTER LINK) -->
<!-- ========================================================================= -->
<div class="modal fade" id="feeGroupsModal" tabindex="-1" aria-labelledby="feeGroupsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-light py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="feeGroupsModalLabel">
                    <i class="fa fa-folder text-primary me-2"></i>Fee Groups Management
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="groupModalAlert" style="display: none;"></div>
                <div class="row g-4">
                    <!-- Quick Create / Edit Group Form -->
                    <div class="col-lg-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold mb-3 text-dark" id="groupFormTitle">Add New Fee Group</h6>
                            <form id="feeGroupForm" onsubmit="submitGroupAjax(event)">
                                <input type="hidden" id="group_id" name="id" value="">
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Group Name <span class="text-danger">*</span></label>
                                    <input type="text" id="group_name" name="name" class="form-control form-control-sm" placeholder="e.g. Tuition Fee 2026" required style="border-radius: 6px;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Description</label>
                                    <textarea id="group_description" name="description" class="form-control form-control-sm" rows="2" placeholder="Notes or terms..." style="border-radius: 6px;"></textarea>
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" id="btnSaveGroup" class="btn btn-primary btn-sm flex-fill fw-bold" style="border-radius: 6px;">
                                        <i class="fa fa-save me-1"></i> Save Group
                                    </button>
                                    <button type="button" id="btnCancelGroupEdit" class="btn btn-outline-secondary btn-sm" style="display: none; border-radius: 6px;" onclick="resetGroupForm()">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Groups List -->
                    <div class="col-lg-8">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Existing Fee Groups</h6>
                            <span class="badge bg-light text-dark border" id="groupsCountBadge"><?php echo count($data['groups']); ?> Groups</span>
                        </div>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-hover table-sm align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th class="text-end" style="min-width: 170px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="groupsTableBody">
                                    <?php if(!empty($data['groups'])): ?>
                                        <?php foreach($data['groups'] as $grp): ?>
                                            <tr id="groupRow_<?php echo $grp->id; ?>">
                                                <td class="fw-bold text-dark group-cell-name"><?php echo htmlspecialchars($grp->group_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="text-muted small group-cell-desc"><?php echo htmlspecialchars($grp->description ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary py-1 px-2" title="Manage Master" onclick="openFeeMasterModal(<?php echo $grp->id; ?>)">
                                                            <i class="fa fa-cogs me-1"></i> Master
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary py-1 px-2" title="Edit Group" onclick="editGroupInline(<?php echo $grp->id; ?>, '<?php echo addslashes($grp->group_name); ?>', '<?php echo addslashes($grp->description ?? ''); ?>')">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger py-1 px-2" title="Delete Group" onclick="deleteGroupInline(<?php echo $grp->id; ?>, '<?php echo addslashes($grp->group_name); ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr id="emptyGroupsRow"><td colspan="3" class="text-center text-muted py-3">No fee groups found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <span class="text-muted small me-auto"><i class="fa fa-info-circle me-1"></i> Changes take effect immediately without reloading.</span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 4. FEE TYPES CRUD MODAL (INLINE ADD, EDIT, DELETE) -->
<!-- ========================================================================= -->
<div class="modal fade" id="feeTypesModal" tabindex="-1" aria-labelledby="feeTypesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-light py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="feeTypesModalLabel">
                    <i class="fa fa-tags text-primary me-2"></i>Fee Types Management
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="typeModalAlert" style="display: none;"></div>
                <div class="row g-4">
                    <!-- Quick Create / Edit Type Form -->
                    <div class="col-lg-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold mb-3 text-dark" id="typeFormTitle">Add New Fee Type</h6>
                            <form id="feeTypeForm" onsubmit="submitTypeAjax(event)">
                                <input type="hidden" id="type_id" name="id" value="">

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Type Name <span class="text-danger">*</span></label>
                                    <input type="text" id="type_name" name="name" class="form-control form-control-sm" placeholder="e.g. Monthly Tuition" required style="border-radius: 6px;">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Type Code <span class="text-danger">*</span></label>
                                    <input type="text" id="type_code" name="code" class="form-control form-control-sm" placeholder="e.g. TUIT-01" required style="border-radius: 6px;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Description</label>
                                    <textarea id="type_description" name="description" class="form-control form-control-sm" rows="2" placeholder="Details..." style="border-radius: 6px;"></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" id="btnSaveType" class="btn btn-primary btn-sm flex-fill fw-bold" style="border-radius: 6px;">
                                        <i class="fa fa-save me-1"></i> Save Fee Type
                                    </button>
                                    <button type="button" id="btnCancelTypeEdit" class="btn btn-outline-secondary btn-sm" style="display: none; border-radius: 6px;" onclick="resetTypeForm()">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Types List -->
                    <div class="col-lg-8">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Existing Fee Types</h6>
                            <span class="badge bg-light text-dark border" id="typesCountBadge"><?php echo count($data['types']); ?> Types</span>
                        </div>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-hover table-sm align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th class="text-end" style="min-width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="typesTableBody">
                                    <?php if(!empty($data['types'])): ?>
                                        <?php foreach($data['types'] as $tp): ?>
                                            <tr id="typeRow_<?php echo $tp->id; ?>">
                                                <td class="fw-bold text-dark type-cell-name"><?php echo htmlspecialchars($tp->type_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><span class="badge bg-light text-dark border font-monospace type-cell-code"><?php echo htmlspecialchars($tp->type_code, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                                <td class="text-muted small type-cell-desc"><?php echo htmlspecialchars($tp->description ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-secondary py-1 px-2" title="Edit Type" onclick="editTypeInline(<?php echo $tp->id; ?>, '<?php echo addslashes($tp->type_name); ?>', '<?php echo addslashes($tp->type_code); ?>', '<?php echo addslashes($tp->description ?? ''); ?>')">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger py-1 px-2" title="Delete Type" onclick="deleteTypeInline(<?php echo $tp->id; ?>, '<?php echo addslashes($tp->type_name); ?>')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr id="emptyTypesRow"><td colspan="4" class="text-center text-muted py-3">No fee types found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 px-4">
                <span class="text-muted small me-auto"><i class="fa fa-info-circle me-1"></i> Instantly synced without full reload.</span>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 5. FEE MASTER MODAL (GROUP-TYPE STRUCTURE CRUD IN-PLACE) -->
<!-- ========================================================================= -->
<div class="modal fade" id="feeMasterModal" tabindex="-1" aria-labelledby="feeMasterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-light py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="brand-icon-box" style="width: 38px; height: 38px; border-radius: 10px; background: rgba(79, 70, 229, 0.1); color: #4f46e5; font-size: 1.1rem;">
                        <i class="fa fa-cogs"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="feeMasterModalLabel">Fee Master Structure</h5>
                        <small class="text-muted">Attach fee types, amounts, due dates, and fines to fee groups.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Group Selection Bar -->
                <div class="card border-0 shadow-xs mb-4" style="border-radius: 12px; background: #f8fafc;">
                    <div class="card-body p-3">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                                    <i class="fa fa-folder text-primary me-1"></i> Active Fee Group
                                </label>
                            </div>
                            <div class="col-md-9">
                                <select id="masterGroupSelect" class="form-select fw-bold border-1" onchange="loadGroupMaster(this.value)" style="border-radius: 8px;">
                                    <?php if(!empty($data['groups'])): ?>
                                        <?php foreach($data['groups'] as $grp): ?>
                                            <option value="<?php echo $grp->id; ?>"><?php echo htmlspecialchars($grp->group_name, ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No Groups Created Yet</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="masterModalAlert" style="display: none;"></div>

                <div class="row g-4">
                    <!-- Form: Add/Edit Master item -->
                    <div class="col-lg-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold mb-3 text-dark" id="masterFormTitle">Add Fee Type to Group</h6>
                            <form id="masterItemForm" onsubmit="submitMasterAjax(event)">
                                <input type="hidden" id="master_id" name="id" value="">
                                <input type="hidden" id="master_group_id" name="group_id" value="">

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Fee Type <span class="text-danger">*</span></label>
                                    <select id="master_type_id" name="type_id" class="form-select form-select-sm" required style="border-radius: 6px;">
                                        <option value="">-- Choose Type --</option>
                                        <?php if(!empty($data['types'])): ?>
                                            <?php foreach($data['types'] as $tp): ?>
                                                <option value="<?php echo $tp->id; ?>"><?php echo htmlspecialchars($tp->type_name . ' (' . $tp->type_code . ')', ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Amount (<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?>) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0.01" id="master_amount" name="amount" class="form-control form-control-sm" placeholder="0.00" required style="border-radius: 6px;">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Due Date</label>
                                    <input type="date" id="master_due_date" name="due_date" class="form-control form-control-sm" style="border-radius: 6px;">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Fine Amount (<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?>)</label>
                                    <input type="number" step="0.01" min="0" id="master_fine" name="fine" class="form-control form-control-sm" value="0.00" style="border-radius: 6px;">
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" id="btnSaveMaster" class="btn btn-primary btn-sm flex-fill fw-bold" style="border-radius: 6px;">
                                        <i class="fa fa-save me-1"></i> Add to Group
                                    </button>
                                    <button type="button" id="btnCancelMasterEdit" class="btn btn-outline-secondary btn-sm" style="display: none; border-radius: 6px;" onclick="resetMasterForm()">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Table of Master items in this group -->
                    <div class="col-lg-8">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Fee Types in Selected Group</h6>
                            <span class="badge bg-light text-dark border" id="masterItemsCountBadge">0 Items</span>
                        </div>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-hover table-sm align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Fee Type</th>
                                        <th>Code</th>
                                        <th class="text-end">Amount</th>
                                        <th>Due Date</th>
                                        <th class="text-end">Fine</th>
                                        <th class="text-end" style="min-width: 90px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="masterTableBody">
                                    <!-- Populated dynamically via JS -->
                                </tbody>
                                <tfoot id="masterTableFooter" class="bg-light" style="display: none;">
                                    <tr class="fw-bold">
                                        <td colspan="2" class="ps-2">Total Group Amount:</td>
                                        <td class="text-end font-monospace text-primary" id="masterSumAmount"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> 0.00</td>
                                        <td></td>
                                        <td class="text-end font-monospace text-danger" id="masterSumFine"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> 0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 7. CLASS FEE STRUCTURE & PARTICULARS SETUP MODAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="classFeeParticularsModal" tabindex="-1" aria-labelledby="classFeeParticularsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <div class="modal-header py-3 px-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
                <div class="d-flex align-items-center gap-3">
                    <div class="brand-icon-box" style="width: 42px; height: 42px; border-radius: 12px; background: rgba(245, 158, 11, 0.2); color: #f59e0b; font-size: 1.25rem;">
                        <i class="fa fa-layer-group"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="classFeeParticularsModalLabel">Class Fee Structure &amp; Particulars</h5>
                        <small class="text-white-50">Manage class-wise yearly/monthly fee heads — automatically synced with Challans &amp; Register</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <div id="classFeeAlertBox" style="display: none;"></div>

                <!-- Class Selection & Package Name Header -->
                <div class="card border-0 shadow-xs mb-3" style="border-radius: 12px; background: #ffffff;">
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                                    <i class="fa fa-graduation-cap text-primary me-1"></i> Target Class <span class="text-danger">*</span>
                                </label>
                                <select id="cfpClassSelect" class="form-select border-1 fw-bold" onchange="loadClassFeeParticulars(this.value)" style="border-radius: 8px;">
                                    <option value="">-- Choose Class to Setup Fees --</option>
                                    <?php if(!empty($data['classes'])): ?>
                                        <?php foreach($data['classes'] as $cls): ?>
                                            <option value="<?php echo $cls->id; ?>">
                                                <?php echo htmlspecialchars($cls->class_name, ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                                    <i class="fa fa-folder text-primary me-1"></i> Fee Package / Group Name
                                </label>
                                <input type="text" id="cfpGroupName" class="form-control form-control-sm border-1 fw-bold" placeholder="e.g. Class 1 Fee Package" style="border-radius: 8px; padding: 7px 12px;">
                                <input type="hidden" id="cfpGroupId" value="">
                            </div>
                            <div class="col-md-3 text-md-end">
                                <div class="badge bg-light text-dark border px-3 py-2" id="cfpStudentCountBadge" style="font-size: 0.85rem;">
                                    <i class="fa fa-users text-primary me-1"></i> <span id="cfpStudentCountNum">0</span> Students Enrolled
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Particulars Items Table -->
                <div class="card border-0 shadow-xs mb-3" style="border-radius: 12px; background: #ffffff;">
                    <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold small text-muted text-uppercase">
                            <i class="fa fa-list-check text-primary me-1"></i> Fee Heads &amp; Monthly Amounts
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Total Fee / Month:</span>
                            <span class="fw-bold font-monospace text-primary h6 mb-0" id="cfpTotalAmountDisplay"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> 0.00</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0" id="cfpParticularsTable" style="font-size: 0.88rem;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3 py-2" style="width: 40px;">#</th>
                                        <th class="py-2" style="min-width: 220px;">Fee Head / Particular</th>
                                        <th class="py-2" style="width: 120px;">Code</th>
                                        <th class="py-2 text-end" style="width: 160px;">Amount (<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?>)</th>
                                        <th class="py-2 text-end" style="width: 130px;">Late Fine</th>
                                        <th class="pe-3 py-2 text-center" style="width: 70px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cfpParticularsTableBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white p-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm fw-bold px-3 shadow-xs" style="border-radius: 8px;" onclick="addCfpParticularRow()">
                            <i class="fa fa-plus-circle me-1"></i> Add Fee Head / Particular
                        </button>
                        <div class="d-flex align-items-center gap-3">
                            <label class="form-check-label small fw-bold text-muted mb-0 d-flex align-items-center gap-2">
                                <input type="checkbox" id="cfpSyncAllStudents" class="form-check-input mt-0" checked>
                                Automatically apply &amp; sync to all enrolled students in this class
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Explanatory note -->
                <div class="p-3 bg-white border rounded-3 text-muted small d-flex align-items-start gap-2">
                    <i class="fa fa-info-circle text-primary fs-5 mt-1 flex-shrink-0"></i>
                    <div>
                        <strong>Yahan se har class ki fees set hoti hai:</strong> 
                        Jaise hi aap kisi class ke particulars save karenge, yeh us class ke Fee Master me update ho jayega aur class ke sabhi students ko assign ho jayega. Phir chahe aap <strong>Fees Collection Register</strong> me "View / Collect" karein ya <strong>Batch Bank Challan (3-Copy)</strong> print karein, yahi exact particulars aur rates har jagah dynamically reflect honge!
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white py-3 px-4 border-top d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
                <button type="button" id="btnSaveClassFeeStructure" class="btn btn-success px-4 fw-bold shadow-sm" style="border-radius: 8px;" onclick="saveClassFeeParticularsAjax()">
                    <i class="fa fa-check-circle me-1"></i> Save &amp; Sync Class Fee Structure
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 6. PRINTABLE FEE RECEIPT MODAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header py-3 px-4 bg-light">
                <h5 class="modal-title fw-bold text-dark mb-0">
                    <i class="fa fa-receipt text-primary me-2"></i>Official Fee Receipt
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4" id="receiptPrintArea">
                <!-- Receipt Card Container -->
                <div class="border rounded-3 p-4 bg-white shadow-xs">
                    <!-- Receipt Header -->
                    <div class="text-center pb-3 border-bottom mb-3">
                        <h4 class="fw-bold mb-1 text-primary"><?php echo htmlspecialchars(SITENAME, ENT_QUOTES, 'UTF-8'); ?></h4>
                        <div class="text-muted small">Fee Collection &amp; Acknowledgment Voucher</div>
                        <span class="badge bg-success mt-2 px-3 py-1 text-uppercase" style="letter-spacing: 1px; font-size: 0.72rem;">Payment Verified</span>
                    </div>

                    <!-- Meta details -->
                    <div class="row g-2 small mb-3">
                        <div class="col-6">
                            <span class="text-muted">Receipt No:</span>
                            <span class="fw-bold font-monospace text-dark ms-1" id="recId">#0000</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted">Date:</span>
                            <span class="fw-bold text-dark ms-1" id="recDate">--</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted">Student:</span>
                            <span class="fw-bold text-dark ms-1" id="recStudentName">--</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted">Adm No:</span>
                            <span class="fw-bold font-monospace text-dark ms-1" id="recAdmNo">#--</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted">Class &amp; Section:</span>
                            <span class="fw-bold text-dark ms-1" id="recClassSec">--</span>
                        </div>
                    </div>

                    <!-- Breakdown Table -->
                    <table class="table table-bordered table-sm mb-3" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr>
                                <th>Description / Item</th>
                                <th>Mode</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="fw-bold" id="recFeeItem">Tuition Fee</div>
                                    <small class="text-muted" id="recFeeGroup">Class Fee Group</small>
                                </td>
                                <td id="recMode">Cash</td>
                                <td class="text-end font-monospace fw-bold text-success" id="recAmount"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> 0.00</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="p-2 bg-light rounded text-muted small mb-3">
                        <strong>Note:</strong> <span id="recNote">None</span>
                    </div>

                    <!-- Footer Stamp & Signature -->
                    <div class="d-flex justify-content-between align-items-end pt-3 border-top mt-4">
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            Generated electronically.<br>Valid without signature.
                        </div>
                        <div class="text-center">
                            <div style="border-bottom: 1px solid #94a3b8; width: 140px; margin-bottom: 4px;"></div>
                            <span class="text-muted small" style="font-size: 0.72rem;">Authorized Cashier</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light py-3 px-4">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary px-4 fw-bold" onclick="printReceiptDirect()">
                    <i class="fa fa-print me-1"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 8. FEE GENERATOR & ADMISSION TOKEN HUB MODAL                              -->
<!-- ========================================================================= -->
<div class="modal fade" id="feeGeneratorModal" tabindex="-1" aria-labelledby="feeGeneratorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="feeGeneratorModalLabel">
                        <i class="fa fa-bolt text-warning me-2"></i>Fees Hub: Batch Generator &amp; Admission Token
                    </h5>
                    <div class="text-white-50 small mt-0.5" style="font-size: 0.76rem;">
                        Generate monthly fee vouchers for regular students or issue instant tokens for fresh admissions
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Tab Navigation -->
            <div class="bg-light border-bottom px-4 pt-2">
                <ul class="nav nav-tabs border-bottom-0" id="generatorTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold small text-dark" id="tab-monthly-batch" data-bs-toggle="tab" data-bs-target="#pane-monthly-batch" type="button" role="tab">
                            <i class="fa fa-calendar-alt text-primary me-1"></i> 1. Regular Monthly Vouchers (Normal)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold small text-primary" id="tab-admission-token" data-bs-toggle="tab" data-bs-target="#pane-admission-token" type="button" role="tab">
                            <i class="fa fa-ticket-alt text-warning me-1"></i> 2. Fresh / New Admission Fee Token
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body p-4">
                <div class="tab-content" id="generatorTabContent">

                    <!-- TAB 1: REGULAR MONTHLY BATCH INVOICES (BAQI NORMAL CHALTA RAHAY) -->
                    <div class="tab-pane fade" id="pane-monthly-batch" role="tabpanel">
                        <div class="alert alert-light border d-flex align-items-center gap-2 py-2 px-3 mb-3" style="border-radius: 10px;">
                            <i class="fa fa-info-circle text-primary fs-5"></i>
                            <div class="small text-muted">
                                <strong>Regular Enrolled Students Cycle:</strong> Tamam mojooda classes ya kisi khas class ke liye mahana fee vouchers generate karein. Sibling concessions auto-apply hongi.
                            </div>
                        </div>

                        <form action="<?php echo URLROOT; ?>/fees/generateMonthly" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Target Class</label>
                                    <select name="class_id" class="form-select" required style="border-radius: 8px;">
                                        <option value="0">-- All Classes (Entire School) --</option>
                                        <?php if(!empty($data['classes'])): ?>
                                            <?php foreach($data['classes'] as $cls): ?>
                                                <option value="<?php echo $cls->id; ?>"><?php echo htmlspecialchars($cls->class_name); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Billing Month</label>
                                    <select name="month" class="form-select" required style="border-radius: 8px;">
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
                                    <input type="number" name="year" class="form-control" value="<?php echo date('Y'); ?>" required style="border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Payment Due Date</label>
                                    <input type="date" name="due_date" class="form-control" value="<?php echo date('Y-m-10'); ?>" required style="border-radius: 8px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Fee Package / Group</label>
                                    <select name="fee_group_id" class="form-select" style="border-radius: 8px;">
                                        <?php if(!empty($data['groups'])): ?>
                                            <?php foreach($data['groups'] as $g): ?>
                                                <option value="<?php echo $g->id; ?>"><?php echo htmlspecialchars($g->group_name); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm" style="border-radius: 8px;">
                                    <i class="fa fa-bolt me-1"></i> Generate Monthly Invoices Now
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: FRESH / NEW ADMISSION FEE & TOKEN GENERATOR -->
                    <div class="tab-pane fade show active" id="pane-admission-token" role="tabpanel">
                        <div class="alert alert-warning border-0 d-flex align-items-center gap-2 py-2 px-3 mb-3" style="border-radius: 10px; background-color: #fffbeb;">
                            <i class="fa fa-ticket-alt text-warning fs-5"></i>
                            <div class="small text-dark">
                                <strong>Fresh / New Admission Fee Token:</strong> Naye candidate ya newly admitted student ke liye Admission Fee, Prospectus Token ya Initial Package Voucher foran generate karein.
                            </div>
                        </div>

                        <form id="admissionTokenGenForm" onsubmit="submitAdmissionTokenGenerator(event)">
                            <!-- Candidate Selection Mode Toggle -->
                            <div class="bg-light p-2.5 rounded-3 border mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-1 d-block">Candidate Type / داخلہ امیدوار کی قسم</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="candidate_mode" id="candModeWalkin" value="walkin" checked onchange="toggleCandidateSource('walkin')">
                                        <label class="form-check-label fw-bold small" for="candModeWalkin">
                                            <i class="fa fa-user-edit text-primary me-1"></i> New Admission / Walk-in Candidate
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="candidate_mode" id="candModeEnrolled" value="enrolled" onchange="toggleCandidateSource('enrolled')">
                                        <label class="form-check-label fw-bold small" for="candModeEnrolled">
                                            <i class="fa fa-user-graduate text-success me-1"></i> Active Enrolled Student
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Enrolled Student Selector (Hidden by default) -->
                            <div id="enrolledStudentSelectorBox" class="mb-3" style="display: none;">
                                <label class="form-label fw-bold small text-muted text-uppercase">Select Enrolled Student <span class="text-danger">*</span></label>
                                <select id="tokenSelectStudentId" class="form-select" onchange="onSelectEnrolledStudentForToken(this.value)" style="border-radius: 8px;">
                                    <option value="">-- Choose Student from List --</option>
                                    <?php if(!empty($data['students'])): ?>
                                        <?php foreach($data['students'] as $st): ?>
                                            <option value="<?php echo $st->student_id; ?>" 
                                                    data-name="<?php echo htmlspecialchars($st->student_name, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-father="<?php echo htmlspecialchars($st->father_name ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-class="<?php echo htmlspecialchars($st->class_name, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-adm="<?php echo htmlspecialchars($st->admission_no, ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-phone="<?php echo htmlspecialchars($st->parent_phone ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars($st->student_name); ?> (Adm: <?php echo htmlspecialchars($st->admission_no); ?> - Class: <?php echo htmlspecialchars($st->class_name); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Candidate Details Grid -->
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Candidate / Student Name <span class="text-danger">*</span></label>
                                    <input type="text" id="tokenStudentName" name="student_name" class="form-control form-control-sm" placeholder="e.g. Muhammad Ali" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Father / Guardian Name</label>
                                    <input type="text" id="tokenFatherName" name="father_name" class="form-control form-control-sm" placeholder="e.g. Muhammad Tariq">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Applying Class <span class="text-danger">*</span></label>
                                    <input type="text" id="tokenClassName" name="class_name" class="form-control form-control-sm" placeholder="e.g. Class 9 - Science" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Admission / Form #</label>
                                    <input type="text" id="tokenAdmNo" name="admission_no" class="form-control form-control-sm" placeholder="e.g. ADM-2026-001">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">WhatsApp / Contact #</label>
                                    <input type="text" id="tokenPhone" name="phone" class="form-control form-control-sm" placeholder="0300-1234567">
                                </div>
                            </div>

                            <!-- Fee Heads & Items Selector -->
                            <div class="card border mb-3" style="border-radius: 10px;">
                                <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                    <strong class="small text-uppercase text-dark"><i class="fa fa-list-check text-primary me-1"></i> Admission Fee Particulars / فیس تفصیل</strong>
                                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 fw-bold" style="font-size: 0.74rem;" onclick="addCustomFeeItemRow()">
                                        <i class="fa fa-plus me-1"></i> Add Other Head
                                    </button>
                                </div>
                                <div class="card-body p-2.5">
                                    <div id="feeItemsContainer">
                                        <!-- Item 1: Admission Registration Fee -->
                                        <div class="row g-2 align-items-center fee-item-row mb-2">
                                            <div class="col-1 text-center">
                                                <input type="checkbox" class="form-check-input item-check" checked onchange="recalcAdmissionTokenTotals()">
                                            </div>
                                            <div class="col-7">
                                                <input type="text" class="form-control form-control-sm item-title" value="Admission Registration Fee" oninput="recalcAdmissionTokenTotals()">
                                            </div>
                                            <div class="col-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                                    <input type="number" class="form-control form-control-sm fw-bold item-amount" value="5000" step="50" oninput="recalcAdmissionTokenTotals()">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Item 2: Prospectus & Admission Token Fee -->
                                        <div class="row g-2 align-items-center fee-item-row mb-2">
                                            <div class="col-1 text-center">
                                                <input type="checkbox" class="form-check-input item-check" checked onchange="recalcAdmissionTokenTotals()">
                                            </div>
                                            <div class="col-7">
                                                <input type="text" class="form-control form-control-sm item-title" value="Prospectus &amp; Admission Token" oninput="recalcAdmissionTokenTotals()">
                                            </div>
                                            <div class="col-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                                    <input type="number" class="form-control form-control-sm fw-bold item-amount" value="1000" step="50" oninput="recalcAdmissionTokenTotals()">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Item 3: Security Deposit -->
                                        <div class="row g-2 align-items-center fee-item-row mb-2">
                                            <div class="col-1 text-center">
                                                <input type="checkbox" class="form-check-input item-check" checked onchange="recalcAdmissionTokenTotals()">
                                            </div>
                                            <div class="col-7">
                                                <input type="text" class="form-control form-control-sm item-title" value="Security Deposit (Refundable)" oninput="recalcAdmissionTokenTotals()">
                                            </div>
                                            <div class="col-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                                    <input type="number" class="form-control form-control-sm fw-bold item-amount" value="2000" step="50" oninput="recalcAdmissionTokenTotals()">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Item 4: 1st Month Tuition Fee -->
                                        <div class="row g-2 align-items-center fee-item-row mb-2">
                                            <div class="col-1 text-center">
                                                <input type="checkbox" class="form-check-input item-check" checked onchange="recalcAdmissionTokenTotals()">
                                            </div>
                                            <div class="col-7">
                                                <input type="text" class="form-control form-control-sm item-title" value="First Month Tuition Fee" oninput="recalcAdmissionTokenTotals()">
                                            </div>
                                            <div class="col-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                                                    <input type="number" class="form-control form-control-sm fw-bold item-amount" value="3500" step="50" oninput="recalcAdmissionTokenTotals()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Totals & Concession Row -->
                                    <div class="bg-light p-2.5 rounded-2 border mt-2">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-4 text-muted small">
                                                Subtotal: <strong class="text-dark font-monospace" id="tokenSubtotalText">PKR 11,500.00</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-white small">Discount</span>
                                                    <input type="number" id="tokenDiscountInput" class="form-control form-control-sm fw-bold" value="0" step="50" oninput="recalcAdmissionTokenTotals()">
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <span class="text-muted small">Net Payable:</span>
                                                <span class="fs-6 fw-bold text-success font-monospace ms-1" id="tokenNetTotalText">PKR 11,500.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Status & Mode Row -->
                            <div class="row g-2 mb-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Payment Status <span class="text-danger">*</span></label>
                                    <select id="tokenPaymentStatus" name="payment_status" class="form-select form-select-sm fw-bold" onchange="onTokenPaymentStatusChange(this.value)">
                                        <option value="paid" class="text-success">✓ Paid at Counter (فوری موصول)</option>
                                        <option value="unpaid" class="text-danger">⏳ Unpaid / Bank Challan (بینک چالان)</option>
                                    </select>
                                </div>
                                <div class="col-md-4" id="tokenPaymentModeBox">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Payment Mode</label>
                                    <select id="tokenPaymentMode" name="payment_mode" class="form-select form-select-sm">
                                        <option value="Cash">Cash Counter</option>
                                        <option value="Bank Transfer">Bank Transfer / 1Link</option>
                                        <option value="JazzCash">JazzCash / EasyPaisa</option>
                                        <option value="Cheque">Bank Cheque</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Valid Till / Due Date</label>
                                    <input type="date" id="tokenDueDate" name="due_date" class="form-control form-control-sm" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                                </div>
                            </div>

                            <div class="modal-footer border-top px-0 pb-0 pt-3 d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="btnSubmitTokenGen" class="btn btn-success btn-sm px-4 fw-bold shadow-sm" style="border-radius: 8px;">
                                    <i class="fa fa-ticket-alt me-1"></i> Generate &amp; Print Admission Token
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- 9. OFFICIAL DUAL-COPY ADMISSION TOKEN SLIP MODAL                          -->
<!-- ========================================================================= -->
<div class="modal fade" id="admissionTokenSlipModal" tabindex="-1" aria-labelledby="admissionTokenSlipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-2.5 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa fa-ticket-alt text-warning fs-5"></i>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0">Official Admission Fee Token &amp; Enrolment Slip</h6>
                        <span class="text-white-50" style="font-size: 0.72rem;">Dual Copy: Office Record &amp; Student / Parent Copy</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-warning btn-sm fw-bold px-3 py-1 shadow-sm" onclick="printAdmissionTokenDirect()">
                        <i class="fa fa-print me-1"></i> Print Token Slip
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-3 bg-light" id="tokenSlipModalBody" style="max-height: 82vh; overflow-y: auto;">
                <div id="tokenSlipRenderContainer">
                    <!-- Dynamic Dual-Copy Voucher Rendered here by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #tokenSlipRenderContainer, #tokenSlipRenderContainer * {
        visibility: visible !important;
    }
    #tokenSlipRenderContainer {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 5mm !important;
        background: #fff !important;
    }
}
</style>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #receiptPrintArea, #receiptPrintArea * {
        visibility: visible !important;
    }
    #receiptPrintArea {
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 20px !important;
        background: #fff !important;
    }
}
</style>

<!-- ========================================================================= -->
<!-- JAVASCRIPT: RUNTIME FILTERING & IN-PAGE POPUP FEE COLLECTION (NO RELOAD)  -->
<!-- ========================================================================= -->
<script>
window.CSRF_TOKEN = '<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>';
const origFetch = window.fetch;
window.fetch = function(url, options = {}) {
    if (options.method && options.method.toUpperCase() === 'POST') {
        if (options.body instanceof FormData && window.CSRF_TOKEN) {
            if (!options.body.has('csrf_token')) {
                options.body.append('csrf_token', window.CSRF_TOKEN);
            }
        }
        if (!options.headers) options.headers = {};
        if (options.headers instanceof Headers) {
            if (!options.headers.has('X-CSRF-Token') && window.CSRF_TOKEN) {
                options.headers.set('X-CSRF-Token', window.CSRF_TOKEN);
            }
        } else if (typeof options.headers === 'object') {
            if (!options.headers['X-CSRF-Token'] && window.CSRF_TOKEN) {
                options.headers['X-CSRF-Token'] = window.CSRF_TOKEN;
            }
        }
    }
    return origFetch.call(this, url, options);
};

let activeStudentFees = [];
let currentOpenStudentId = null;
let currentOpenStudentClassId = null;
let cfpAvailableTypes = <?php echo json_encode($data['types'] ?? []); ?>;

// Currency Formatter
function formatCurrency(amount) {
    const cur = window.APP_CURRENCY || '<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?>';
    return cur + ' ' + Number(amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

// =============================================================
// SAFE MODAL CONTROLLER & BACKDROP CLEANUP (PREVENTS PAGE FREEZE / HANG)
// =============================================================
function cleanupStuckBackdrops() {
    // If no modal has 'show' class, remove all leftover backdrops and unfreeze body
    if (!document.querySelector('.modal.show')) {
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
        document.body.style.removeProperty('overflow');
    }
}

document.addEventListener('hidden.bs.modal', function() {
    setTimeout(cleanupStuckBackdrops, 60);
});

function openSafeModal(modalId, onReadyCallback) {
    const targetModalEl = document.getElementById(modalId);
    if (!targetModalEl) return;

    // Check if any modal is currently visible
    const openModals = Array.from(document.querySelectorAll('.modal.show'));
    if (openModals.length > 0) {
        if (openModals.length === 1 && openModals[0].id === modalId) {
            if (typeof onReadyCallback === 'function') onReadyCallback(targetModalEl);
            return;
        }

        // Hide open modals cleanly
        openModals.forEach(m => {
            const inst = bootstrap.Modal.getInstance(m);
            if (inst) inst.hide();
        });

        setTimeout(() => {
            cleanupStuckBackdrops();
            _launchSafeModal(targetModalEl, onReadyCallback);
        }, 180);
    } else {
        cleanupStuckBackdrops();
        _launchSafeModal(targetModalEl, onReadyCallback);
    }
}

function _launchSafeModal(modalEl, onReadyCallback) {
    const modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);
    modalInst.show();
    if (typeof onReadyCallback === 'function') {
        onReadyCallback(modalEl, modalInst);
    }
}

window.openFeeTypesModal = function() {
    openSafeModal('feeTypesModal');
};

window.openFeeGroupsModal = function() {
    openSafeModal('feeGroupsModal');
};

window.openFeeGeneratorModal = function(defaultTab = 'tab-admission-token') {
    openSafeModal('feeGeneratorModal', () => {
        if (defaultTab === 'tab-monthly-batch') {
            const btn = document.getElementById('tab-monthly-batch');
            if (btn) btn.click();
        } else {
            const btn = document.getElementById('tab-admission-token');
            if (btn) btn.click();
        }
        recalcAdmissionTokenTotals();
    });
};

window.toggleCandidateSource = function(mode) {
    const selectorBox = document.getElementById('enrolledStudentSelectorBox');
    if (selectorBox) {
        selectorBox.style.display = (mode === 'enrolled') ? 'block' : 'none';
    }
    if (mode === 'walkin') {
        const sel = document.getElementById('tokenSelectStudentId');
        if (sel) sel.value = '';
    }
};

window.onSelectEnrolledStudentForToken = function(studentId) {
    if (!studentId) return;
    const sel = document.getElementById('tokenSelectStudentId');
    const opt = sel.options[sel.selectedIndex];
    if (!opt) return;

    document.getElementById('tokenStudentName').value = opt.getAttribute('data-name') || '';
    document.getElementById('tokenFatherName').value = opt.getAttribute('data-father') || '';
    document.getElementById('tokenClassName').value = opt.getAttribute('data-class') || '';
    document.getElementById('tokenAdmNo').value = opt.getAttribute('data-adm') || '';
    document.getElementById('tokenPhone').value = opt.getAttribute('data-phone') || '';
};

window.recalcAdmissionTokenTotals = function() {
    let subtotal = 0;
    const rows = document.querySelectorAll('#feeItemsContainer .fee-item-row');
    rows.forEach(r => {
        const chk = r.querySelector('.item-check');
        const amtInput = r.querySelector('.item-amount');
        if (chk && chk.checked && amtInput) {
            subtotal += parseFloat(amtInput.value) || 0;
        }
    });

    const discInput = document.getElementById('tokenDiscountInput');
    const discount = discInput ? (parseFloat(discInput.value) || 0) : 0;
    const net = Math.max(0, subtotal - discount);

    const subEl = document.getElementById('tokenSubtotalText');
    const netEl = document.getElementById('tokenNetTotalText');
    if (subEl) subEl.textContent = 'PKR ' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2 });
    if (netEl) netEl.textContent = 'PKR ' + net.toLocaleString('en-US', { minimumFractionDigits: 2 });
};

window.addCustomFeeItemRow = function() {
    const container = document.getElementById('feeItemsContainer');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'row g-2 align-items-center fee-item-row mb-2';
    div.innerHTML = `
        <div class="col-1 text-center">
            <input type="checkbox" class="form-check-input item-check" checked onchange="recalcAdmissionTokenTotals()">
        </div>
        <div class="col-7">
            <div class="input-group input-group-sm">
                <input type="text" class="form-control form-control-sm item-title" placeholder="e.g. Annual Resource, Sports Fund" oninput="recalcAdmissionTokenTotals()">
                <button type="button" class="btn btn-outline-danger btn-sm py-0" onclick="this.closest('.fee-item-row').remove(); recalcAdmissionTokenTotals();" title="Remove">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <div class="col-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light text-muted fw-bold">PKR</span>
                <input type="number" class="form-control form-control-sm fw-bold item-amount" value="1000" step="50" oninput="recalcAdmissionTokenTotals()">
            </div>
        </div>
    `;
    container.appendChild(div);
    recalcAdmissionTokenTotals();
};

window.onTokenPaymentStatusChange = function(status) {
    const modeBox = document.getElementById('tokenPaymentModeBox');
    if (modeBox) {
        modeBox.style.display = (status === 'paid') ? 'block' : 'none';
    }
};

window.submitAdmissionTokenGenerator = function(event) {
    event.preventDefault();
    const btn = document.getElementById('btnSubmitTokenGen');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Generating Token...';

    const candMode = document.querySelector('input[name="candidate_mode"]:checked')?.value || 'walkin';
    const studentId = (candMode === 'enrolled') ? (document.getElementById('tokenSelectStudentId')?.value || 0) : 0;
    const studentName = document.getElementById('tokenStudentName')?.value || '';
    const fatherName = document.getElementById('tokenFatherName')?.value || '';
    const className = document.getElementById('tokenClassName')?.value || '';
    const admissionNo = document.getElementById('tokenAdmNo')?.value || '';
    const phone = document.getElementById('tokenPhone')?.value || '';
    const paymentStatus = document.getElementById('tokenPaymentStatus')?.value || 'paid';
    const paymentMode = document.getElementById('tokenPaymentMode')?.value || 'Cash';
    const dueDate = document.getElementById('tokenDueDate')?.value || '';
    const discount = parseFloat(document.getElementById('tokenDiscountInput')?.value) || 0;

    const items = [];
    document.querySelectorAll('#feeItemsContainer .fee-item-row').forEach(r => {
        const chk = r.querySelector('.item-check');
        const titleInput = r.querySelector('.item-title');
        const amtInput = r.querySelector('.item-amount');
        if (chk && chk.checked && titleInput && amtInput) {
            const title = titleInput.value.trim();
            const amt = parseFloat(amtInput.value) || 0;
            if (title && amt > 0) {
                items.push({ name: title, amount: amt });
            }
        }
    });

    const fd = new FormData();
    fd.append('student_id', studentId);
    fd.append('student_name', studentName);
    fd.append('father_name', fatherName);
    fd.append('class_name', className);
    fd.append('admission_no', admissionNo);
    fd.append('phone', phone);
    fd.append('payment_status', paymentStatus);
    fd.append('payment_mode', paymentMode);
    fd.append('due_date', dueDate);
    fd.append('discount', discount);
    fd.append('items', JSON.stringify(items));
    if (window.CSRF_TOKEN) fd.append('csrf_token', window.CSRF_TOKEN);

    fetch('<?php echo URLROOT; ?>/fees/ajaxGenerateAdmissionToken', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = origHtml;

        if (res.success) {
            // Close generator modal
            const genModalEl = document.getElementById('feeGeneratorModal');
            if (genModalEl) {
                const modalInst = bootstrap.Modal.getInstance(genModalEl);
                if (modalInst) modalInst.hide();
            }

            // Render dual copy slip and open slip modal
            renderDualCopyAdmissionToken(res);
            openSafeModal('admissionTokenSlipModal');

            // If enrolled student was used, reload table data dynamically
            if (studentId > 0) {
                const curCls = document.getElementById('filterClass')?.value;
                if (curCls && typeof reloadStudentsForClassAjax === 'function') {
                    reloadStudentsForClassAjax(curCls);
                }
            }
        } else {
            alert('Error: ' + (res.message || 'Failed to generate admission token.'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = origHtml;
        console.error(err);
        alert('Network error while generating admission token.');
    });
};

window.renderDualCopyAdmissionToken = function(d) {
    const container = document.getElementById('tokenSlipRenderContainer');
    if (!container) return;

    const schoolName = d.school?.name || 'PAK ACADEMY MODEL SCHOOL SYSTEM';
    const campusName = d.school?.campus || 'MAIN EXECUTIVE CAMPUS';
    const schoolAddress = d.school?.address || 'City Educational Zone, Pakistan';
    const schoolPhone = d.school?.phone || '+92 42 35889000';
    const logoUrl = d.school?.logo ? ('<?php echo URLROOT; ?>/' + d.school.logo.replace(/^\//, '')) : '';

    const isPaid = (d.payment_status === 'paid');

    const renderSingleCopy = (copyTitle, copyUrdu, isOffice) => {
        let itemsHtml = '';
        if (d.items && d.items.length) {
            d.items.forEach((it, idx) => {
                itemsHtml += `
                    <tr>
                        <td style="padding: 3px 6px; border: 1px solid #cbd5e1; font-size: 10px; width: 10%; text-align: center;">${idx + 1}</td>
                        <td style="padding: 3px 6px; border: 1px solid #cbd5e1; font-size: 10px; font-weight: 600;">${it.title || it.name}</td>
                        <td style="padding: 3px 6px; border: 1px solid #cbd5e1; font-size: 10px; text-align: right; font-family: monospace; font-weight: 700;">Rs. ${Number(it.amount).toLocaleString()}</td>
                    </tr>
                `;
            });
        }

        const stampHtml = isPaid 
            ? `<div style="border: 2px solid #16a34a; background: #f0fdf4; color: #15803d; padding: 4px 8px; border-radius: 6px; font-size: 9.5px; font-weight: 700; text-align: center;">
                 <i class="fa fa-check-circle me-1"></i> PAID IN FULL (ادائیگی موصول شد) | Mode: ${d.payment_mode || 'Cash'}
               </div>`
            : `<div style="border: 2px solid #dc2626; background: #fef2f2; color: #b91c1c; padding: 4px 8px; border-radius: 6px; font-size: 9.5px; font-weight: 700; text-align: center;">
                 <i class="fa fa-exclamation-triangle me-1"></i> UNPAID (بینک چالان) - Payable Till: ${d.due_date}
               </div>`;

        return `
            <div class="token-single-copy bg-white p-3 border shadow-xs" style="border-radius: 8px; font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; position: relative;">
                <!-- Copy Badge -->
                <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                    <span class="badge ${isOffice ? 'bg-dark text-white' : 'bg-primary text-white'}" style="font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.5px;">
                        ${copyTitle}
                    </span>
                    <span style="font-size: 9px; font-weight: 600; color: #64748b;">${copyUrdu}</span>
                </div>

                <!-- School Header -->
                <div class="text-center mb-1.5 pb-1 border-bottom">
                    <h6 style="font-weight: 800; color: #0f172a; margin-bottom: 1px; font-size: 12.5px; text-transform: uppercase;">${schoolName}</h6>
                    <div style="font-size: 9.5px; font-weight: 700; color: #2563eb; margin-bottom: 1px;">${campusName}</div>
                    <div style="font-size: 8.5px; color: #64748b;">${schoolAddress} &bull; Ph: ${schoolPhone}</div>
                    <div style="margin-top: 4px; display: inline-block; background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; font-size: 9px; font-weight: 800; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">
                        Fresh Admission Fee Token &amp; Enrolment Slip
                    </div>
                </div>

                <!-- Token & Date Banner -->
                <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; margin-bottom: 6px;" class="d-flex justify-content-between align-items-center">
                    <div>
                        <span style="font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Token Number:</span><br>
                        <span style="font-family: monospace; font-size: 13px; font-weight: 800; color: #1e3a8a;">${d.token_no}</span>
                    </div>
                    <div class="text-end">
                        <span style="font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Issue Date:</span><br>
                        <span style="font-size: 10px; font-weight: 700; color: #334155;">${d.issue_date}</span>
                    </div>
                </div>

                <!-- Candidate Particulars Grid -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px; font-size: 9.5px;">
                    <tr>
                        <td style="padding: 2px 4px; color: #64748b; width: 22%;">Candidate:</td>
                        <td style="padding: 2px 4px; font-weight: 700; color: #0f172a; width: 38%;">${d.student_name}</td>
                        <td style="padding: 2px 4px; color: #64748b; width: 18%;">Father:</td>
                        <td style="padding: 2px 4px; font-weight: 700; color: #0f172a; width: 22%;">${d.father_name}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; color: #64748b;">Class &amp; Sec:</td>
                        <td style="padding: 2px 4px; font-weight: 700; color: #0f172a;">${d.class_name}</td>
                        <td style="padding: 2px 4px; color: #64748b;">Form / Adm #:</td>
                        <td style="padding: 2px 4px; font-weight: 700; font-family: monospace; color: #0f172a;">${d.admission_no}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; color: #64748b;">Contact #:</td>
                        <td style="padding: 2px 4px; font-weight: 700; font-family: monospace; color: #0f172a;">${d.phone || 'N/A'}</td>
                        <td style="padding: 2px 4px; color: #64748b;">Due Date:</td>
                        <td style="padding: 2px 4px; font-weight: 700; color: #dc2626;">${d.due_date}</td>
                    </tr>
                </table>

                <!-- Fee Breakdown Table -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
                    <thead>
                        <tr style="background: #f1f5f9; color: #334155; font-size: 9px; font-weight: 700;">
                            <th style="padding: 3px 6px; border: 1px solid #cbd5e1; text-align: center;">#</th>
                            <th style="padding: 3px 6px; border: 1px solid #cbd5e1;">Fee Particular / فیس تفصیل</th>
                            <th style="padding: 3px 6px; border: 1px solid #cbd5e1; text-align: right;">Amount (PKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                        <tr>
                            <td colspan="2" style="padding: 3px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 9.5px; font-weight: 600;">Subtotal</td>
                            <td style="padding: 3px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 10px; font-family: monospace; font-weight: 700;">Rs. ${Number(d.subtotal).toLocaleString()}</td>
                        </tr>
                        ${d.discount > 0 ? `
                        <tr>
                            <td colspan="2" style="padding: 2px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 9px; color: #16a34a; font-weight: 600;">Special Admission Discount</td>
                            <td style="padding: 2px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 9.5px; font-family: monospace; color: #16a34a; font-weight: 700;">- Rs. ${Number(d.discount).toLocaleString()}</td>
                        </tr>` : ''}
                        <tr style="background: #eef2ff;">
                            <td colspan="2" style="padding: 4px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 10.5px; font-weight: 800; color: #1e3a8a;">Total ${isPaid ? 'Paid' : 'Payable'}</td>
                            <td style="padding: 4px 6px; border: 1px solid #cbd5e1; text-align: right; font-size: 12px; font-family: monospace; font-weight: 800; color: #1e3a8a;">Rs. ${Number(d.net_total).toLocaleString()}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Status Stamp -->
                <div class="mb-2">
                    ${stampHtml}
                </div>

                <!-- Signatures Grid -->
                <div class="d-flex justify-content-between text-center pt-3 mt-1" style="font-size: 8px; color: #475569;">
                    <div style="width: 30%;">
                        <div style="border-top: 1px solid #94a3b8; padding-top: 2px; font-weight: 600;">Parent / Guardian</div>
                    </div>
                    <div style="width: 30%;">
                        <div style="border-top: 1px solid #94a3b8; padding-top: 2px; font-weight: 600;">Cashier / Accounts</div>
                    </div>
                    <div style="width: 30%;">
                        <div style="border-top: 1px solid #94a3b8; padding-top: 2px; font-weight: 600;">Admission Officer</div>
                    </div>
                </div>
            </div>
        `;
    };

    container.innerHTML = `
        <div class="row g-2">
            <div class="col-md-6 col-12 pe-md-2" style="border-right: 1.5px dashed #94a3b8;">
                ${renderSingleCopy('Office / School Copy', 'دفتری ریکارڈ', true)}
            </div>
            <div class="col-md-6 col-12 ps-md-2">
                ${renderSingleCopy('Student / Parent Copy', 'امیدوار / والدین کاپی', false)}
            </div>
        </div>
    `;
};

window.printAdmissionTokenDirect = function() {
    window.print();
};

window.openAssignFeesModal = function() {
    openSafeModal('assignFeesModal', () => {
        const curClass = document.getElementById('filterClass');
        if (curClass && curClass.value) {
            const ac = document.getElementById('assign_class_id');
            if (ac) {
                ac.value = curClass.value;
                ac.dispatchEvent(new Event('change'));
            }
        }
    });
};

window.scrollToFeeRegister = function() {
    const tableCard = document.getElementById('feeStudentsTable');
    if (tableCard) {
        tableCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
};

// Reload student list & stats for a class dynamically via AJAX (No page reload)
function reloadStudentsForClassAjax(classId) {
    if (!classId) return;

    fetch('<?php echo URLROOT; ?>/fees/collect?ajax=1&class_id=' + classId)
        .then(r => r.json())
        .then(students => {
            if (!Array.isArray(students)) return;

            students.forEach(st => {
                const sId = st.student_id || st.id;
                const total = parseFloat(st.total_amount || 0);
                const paid = parseFloat(st.total_paid || 0);
                const balance = Math.max(0, total - paid);

                const row = document.getElementById('studentRow_' + sId);
                if (row) {
                    row.setAttribute('data-fee', total);
                    row.setAttribute('data-paid', paid);
                    row.setAttribute('data-balance', balance);

                    const cellTotal = row.querySelector('.cell-total-fee');
                    const cellPaid = row.querySelector('.cell-total-paid');
                    const cellBal = row.querySelector('.cell-balance');
                    const cellStat = row.querySelector('.cell-status');

                    if (cellTotal) cellTotal.textContent = '$' + total.toFixed(2);
                    if (cellPaid) cellPaid.textContent = '$' + paid.toFixed(2);
                    if (cellBal) {
                        cellBal.textContent = '$' + balance.toFixed(2);
                        cellBal.className = 'text-end font-monospace fw-bold cell-balance ' + (balance > 0 ? 'text-danger' : 'text-muted');
                    }
                    if (cellStat) {
                        if (total <= 0) cellStat.innerHTML = '<span class="badge bg-light text-muted border px-2 py-1">No Fees</span>';
                        else if (balance <= 0) cellStat.innerHTML = '<span class="badge bg-success px-2 py-1"><i class="fa fa-check me-1"></i>Paid</span>';
                        else if (paid > 0) cellStat.innerHTML = '<span class="badge bg-warning text-dark px-2 py-1"><i class="fa fa-clock me-1"></i>Partial</span>';
                        else cellStat.innerHTML = '<span class="badge bg-danger px-2 py-1"><i class="fa fa-times me-1"></i>Unpaid</span>';
                    }
                }
            });

            // Set active class filter and refresh table visibility & KPI counts
            const filterClass = document.getElementById('filterClass');
            if (filterClass) {
                filterClass.value = classId;
                updateSectionDropdown(classId);
            }
            if (typeof window.applyLiveFilter === 'function') {
                window.applyLiveFilter();
            }
        })
        .catch(err => console.error('Error reloading students via AJAX:', err));
}

// -------------------------------------------------------------
// 1. OPEN COLLECT FEE MODAL (VIA AJAX, NO PAGE RELOAD)
// -------------------------------------------------------------
function openCollectFeeModal(studentId) {
    currentOpenStudentId = studentId;

    openSafeModal('collectFeeModal', (modalEl) => {
        // UI Reset
        document.getElementById('modalLoadingState').style.display = 'block';
        document.getElementById('modalMainContent').style.display = 'none';
        document.getElementById('modalAlertBox').style.display = 'none';
        document.getElementById('modalPaymentForm').reset();

        // Default to Payment Tab
        const tabPayBtn = document.getElementById('tab-payment-btn');
        if (tabPayBtn) {
            bootstrap.Tab.getOrCreateInstance(tabPayBtn).show();
        }

        // Fetch student fee details and history via AJAX
        fetch('<?php echo URLROOT; ?>/fees/ajaxGetStudentFees/' + studentId)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalLoadingState').style.display = 'none';
                document.getElementById('modalMainContent').style.display = 'block';

                if (data.success && data.student) {
                    // Populate student banner
                    const s = data.student;
                    currentOpenStudentClassId = s.class_id || null;
                    const studentName = s.name || ('Student #' + studentId);
                    const initials = studentName.substring(0, 2).toUpperCase();
                    
                    document.getElementById('modalStudentName').textContent = studentName;
                    document.getElementById('modalStudentAvatar').textContent = initials;
                    document.getElementById('modalStudentMeta').textContent = 
                        'Adm No: #' + (s.admission_no || '-') + ' • Class: ' + (s.class_name || 'N/A') + ' - ' + (s.section_name || 'General') + ' • Phone: ' + (s.parent_phone || 'N/A');

                    renderModalLedger(data);
                    renderPaymentHistory(data.history || []);
                } else {
                    document.getElementById('modalAlertBox').innerHTML = '<div class="alert alert-danger">Unable to load student fee records.</div>';
                    document.getElementById('modalAlertBox').style.display = 'block';
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('modalLoadingState').style.display = 'none';
                document.getElementById('modalMainContent').style.display = 'block';
                document.getElementById('modalAlertBox').innerHTML = '<div class="alert alert-danger">Connection error while retrieving fee records.</div>';
                document.getElementById('modalAlertBox').style.display = 'block';
            });
    });
}

// -------------------------------------------------------------
// 2. RENDER MODAL LEDGER & SELECTABLE INVOICES
// -------------------------------------------------------------
function renderModalLedger(data) {
    activeStudentFees = data.fees || [];
    
    // Update KPI Tiles
    document.getElementById('modalTotalAssigned').textContent = formatCurrency(data.total_assigned);
    document.getElementById('modalTotalPaid').textContent = formatCurrency(data.total_paid);
    document.getElementById('modalTotalBalance').textContent = formatCurrency(data.balance);
    document.getElementById('modalInvoiceCount').textContent = activeStudentFees.length + ' Invoices';

    // Invoices Table
    const tbody = document.getElementById('modalInvoiceTableBody');
    tbody.innerHTML = '';

    // Target Invoice Select for Payment Form
    const paySelect = document.getElementById('payStudentFeeId');
    paySelect.innerHTML = '';

    let firstDueFound = false;

    if (activeStudentFees.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4 bg-white">
                    <div class="p-3">
                        <i class="fa fa-info-circle text-warning fs-3 mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">No Fees Assigned to this Student Yet</h6>
                        <p class="text-muted small mb-3">Is student ko fee package assign nahi hua. Aap 1-Click me standard fee assign kar ke foran payment collect kar sakte hain:</p>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <button type="button" class="btn btn-sm btn-success fw-bold px-3 shadow-sm" id="btnQuickAssignNow" onclick="quickAssignFeeToCurrentStudent()">
                                <i class="fa fa-bolt me-1"></i> Quick Assign Class Fee Now (1-Click)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3" onclick="openClassFeeParticularsModal(currentOpenStudentClassId)">
                                <i class="fa fa-layer-group me-1"></i> Class Fee Particulars
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary fw-bold px-3" onclick="bootstrap.Modal.getInstance(document.getElementById('collectFeeModal')).hide(); bootstrap.Modal.getOrCreateInstance(document.getElementById('assignFeesModal')).show();">
                                <i class="fa fa-user-plus me-1"></i> Assign Modal
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        `;
        paySelect.innerHTML = '<option value="">-- No Invoices Available --</option>';
        document.getElementById('btnSubmitPayment').disabled = true;
        return;
    }

    document.getElementById('btnSubmitPayment').disabled = false;
    document.getElementById('payStudentId').value = currentOpenStudentId;

    activeStudentFees.forEach(fee => {
        const amt = parseFloat(fee.amount || 0);
        const paid = parseFloat(fee.total_paid || 0);
        const bal = Math.max(0, (amt + parseFloat(fee.fine_amount || 0)) - (paid + parseFloat(fee.total_discount || 0)));

        // Tr in modal
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="ps-3 fw-bold text-dark">${escapeHtml(fee.group_name || 'Tuition')}</td>
            <td><span class="badge bg-light text-dark border font-monospace">${escapeHtml(fee.type_code || '')}</span></td>
            <td class="text-end font-monospace">${formatCurrency(amt)}</td>
            <td class="text-end font-monospace text-success">${formatCurrency(paid)}</td>
            <td class="text-end font-monospace ${bal > 0 ? 'text-danger fw-bold' : 'text-muted'}">${formatCurrency(bal)}</td>
            <td class="pe-3 text-end">
                ${bal > 0 ? `<button type="button" class="btn btn-xs btn-primary shadow-xs" style="font-size:0.75rem; padding: 2px 10px;" onclick="selectInvoiceForPayment(${fee.student_fee_id}, ${bal})">Select</button>` : `<span class="badge bg-success" style="font-size:0.7rem;">Paid</span>`}
            </td>
        `;
        tbody.appendChild(tr);

        // Add to payment select options
        if (bal > 0) {
            const opt = document.createElement('option');
            opt.value = fee.student_fee_id;
            opt.textContent = (fee.group_name || 'Fee') + ' (' + (fee.type_code || '') + ') - Due: ' + formatCurrency(bal);
            opt.setAttribute('data-balance', bal);
            paySelect.appendChild(opt);

            if (!firstDueFound) {
                firstDueFound = true;
                paySelect.value = fee.student_fee_id;
                document.getElementById('payAmount').value = bal.toFixed(2);
                document.getElementById('payAmount').max = bal.toFixed(2);
            }
        }
    });

    if (!firstDueFound) {
        paySelect.innerHTML = '<option value="">All Invoices are Paid in Full</option>';
        document.getElementById('payAmount').value = '';
        document.getElementById('btnSubmitPayment').disabled = true;
    }
}

// Select an invoice from table row into payment form
function selectInvoiceForPayment(studentFeeId, balance) {
    const paySelect = document.getElementById('payStudentFeeId');
    paySelect.value = studentFeeId;
    document.getElementById('payAmount').value = balance.toFixed(2);
    document.getElementById('payAmount').max = balance.toFixed(2);
    document.getElementById('payAmount').focus();
}

// When invoice dropdown changes in payment form
document.getElementById('payStudentFeeId').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    if (opt) {
        const bal = parseFloat(opt.getAttribute('data-balance') || '0');
        if (bal > 0) {
            document.getElementById('payAmount').value = bal.toFixed(2);
            document.getElementById('payAmount').max = bal.toFixed(2);
        }
    }
});

// -------------------------------------------------------------
// 3. PAYMENT HISTORY & VOID PAYMENT (CRUD)
// -------------------------------------------------------------
function renderPaymentHistory(history) {
    const tbody = document.getElementById('modalHistoryTableBody');
    const badge = document.getElementById('modalHistoryCount');
    if (!tbody) return;

    badge.textContent = history.length;
    tbody.innerHTML = '';

    if (history.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4"><i class="fa fa-info-circle me-1"></i> No transactions recorded yet.</td></tr>';
        return;
    }

    history.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="ps-3 font-monospace fw-bold text-dark">#${item.id}</td>
            <td class="text-muted small">${escapeHtml(item.payment_date || '')}</td>
            <td>
                <div class="fw-bold text-dark" style="font-size:0.82rem;">${escapeHtml(item.type_name || '')}</div>
                <small class="text-muted">${escapeHtml(item.group_name || '')} (${escapeHtml(item.type_code || '')})</small>
            </td>
            <td><span class="badge bg-light text-dark border">${escapeHtml(item.mode || 'Cash')}</span></td>
            <td class="text-end font-monospace fw-bold text-success">${formatCurrency(item.amount)}</td>
            <td class="text-muted small text-truncate" style="max-width: 120px;">${escapeHtml(item.note || '-')}</td>
            <td class="pe-3 text-end">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary py-1 px-2" title="Print Receipt" onclick="printReceiptFromHistory(${item.id})">
                        <i class="fa fa-print"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger py-1 px-2" title="Void / Delete Transaction" onclick="voidPaymentAjax(${item.id})">
                        <i class="fa fa-trash-alt"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Void (Delete) payment transaction via AJAX
function voidPaymentAjax(paymentId) {
    if (!confirm('Are you sure you want to void and delete payment #' + paymentId + '? The student ledger will be adjusted immediately.')) {
        return;
    }

    const formData = new FormData();
    formData.append('student_id', currentOpenStudentId);

    fetch('<?php echo URLROOT; ?>/fees/ajaxDeletePayment/' + paymentId, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const alertBox = document.getElementById('modalAlertBox');
            alertBox.innerHTML = '<div class="alert alert-warning d-flex align-items-center gap-2 mb-3 border-0 py-2"><i class="fa fa-info-circle fs-5"></i><div>' + escapeHtml(res.message) + '</div></div>';
            alertBox.style.display = 'block';

            // Re-render ledger & history
            renderModalLedger(res);
            renderPaymentHistory(res.history || []);

            // Update background row in student register table
            updateStudentRowInMemory(res.student_id, res.total_assigned, res.total_paid, res.balance);
        } else {
            alert('Error voiding payment: ' + (res.message || 'Operation failed.'));
        }
    })
    .catch(err => {
        console.error(err);
        alert('An unexpected network error occurred while voiding payment.');
    });
}

// Print Receipt from History
function printReceiptFromHistory(paymentId) {
    fetch('<?php echo URLROOT; ?>/fees/ajaxGetReceipt/' + paymentId)
        .then(r => r.json())
        .then(res => {
            if (res.success && res.payment) {
                const p = res.payment;
                document.getElementById('recId').textContent = '#REC-' + p.id;
                document.getElementById('recDate').textContent = p.payment_date || '--';
                document.getElementById('recStudentName').textContent = p.student_name || '--';
                document.getElementById('recAdmNo').textContent = '#' + (p.admission_no || '--');
                document.getElementById('recClassSec').textContent = (p.class_name || '') + ' - ' + (p.section_name || 'General');
                document.getElementById('recFeeItem').textContent = (p.type_name || 'Fee') + ' (' + (p.type_code || '') + ')';
                document.getElementById('recFeeGroup').textContent = p.group_name || 'General Fee Group';
                document.getElementById('recMode').textContent = p.mode || 'Cash';
                document.getElementById('recAmount').textContent = formatCurrency(p.amount);
                document.getElementById('recNote').textContent = p.note || 'None';

                const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('receiptModal'));
                modal.show();
            } else {
                alert('Could not retrieve receipt details.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error loading receipt.');
        });
}

function printReceiptDirect() {
    window.print();
}

// -------------------------------------------------------------
// 4. SUBMIT PAYMENT VIA AJAX (NO PAGE RELOAD)
// -------------------------------------------------------------
function submitPaymentAjax(event) {
    event.preventDefault();
    const btn = document.getElementById('btnSubmitPayment');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Recording...';

    const form = document.getElementById('modalPaymentForm');
    const formData = new FormData(form);

    fetch('<?php echo URLROOT; ?>/fees/ajaxPayFee', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = origHtml;

        if (res.success) {
            // Show success alert in modal
            const alertBox = document.getElementById('modalAlertBox');
            alertBox.innerHTML = '<div class="alert alert-success d-flex align-items-center gap-2 mb-3 border-0 py-2"><i class="fa fa-check-circle fs-5"></i><div>' + escapeHtml(res.message) + '</div></div>';
            alertBox.style.display = 'block';

            // Re-render modal ledger with updated fees & history
            renderModalLedger(res);
            renderPaymentHistory(res.history || []);

            // Dynamically update the student's row in the background table
            updateStudentRowInMemory(res.student_id, res.total_assigned, res.total_paid, res.balance);
        } else {
            alert('Error recording payment: ' + (res.message || 'Please verify amount.'));
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = origHtml;
        alert('An unexpected network error occurred while submitting payment.');
    });
}

// Helper to update student row in table without reloading
function updateStudentRowInMemory(studentId, totalAssigned, totalPaid, balance) {
    const row = document.getElementById('studentRow_' + studentId);
    if (!row) return;

    row.setAttribute('data-fee', totalAssigned);
    row.setAttribute('data-paid', totalPaid);
    row.setAttribute('data-balance', balance);

    const cellPaid = row.querySelector('.cell-total-paid');
    const cellBalance = row.querySelector('.cell-balance');
    const cellStatus = row.querySelector('.cell-status');
    const cellAction = row.querySelector('.cell-action');

    if (cellPaid) cellPaid.textContent = formatCurrency(totalPaid);
    if (cellBalance) {
        cellBalance.textContent = formatCurrency(balance);
        if (balance <= 0) {
            cellBalance.className = 'text-end font-monospace fw-bold cell-balance text-muted';
        } else {
            cellBalance.className = 'text-end font-monospace fw-bold cell-balance text-danger';
        }
    }

    if (cellStatus) {
        if (totalAssigned <= 0) {
            cellStatus.innerHTML = '<span class="badge bg-light text-muted border px-2 py-1">No Fees</span>';
        } else if (balance <= 0) {
            cellStatus.innerHTML = '<span class="badge bg-success px-2 py-1"><i class="fa fa-check me-1"></i>Paid</span>';
        } else if (totalPaid > 0) {
            cellStatus.innerHTML = '<span class="badge bg-warning text-dark px-2 py-1"><i class="fa fa-clock me-1"></i>Partial</span>';
        } else {
            cellStatus.innerHTML = '<span class="badge bg-danger px-2 py-1"><i class="fa fa-times me-1"></i>Unpaid</span>';
        }
    }

    if (cellAction) {
        if (balance > 0) {
            cellAction.innerHTML = `<button type="button" class="btn btn-sm btn-primary shadow-sm" style="border-radius: 8px; font-size: 0.8rem; padding: 5px 12px;" onclick="openCollectFeeModal(${studentId})"><i class="fa fa-hand-holding-usd me-1"></i> Collect</button>`;
        } else {
            cellAction.innerHTML = `<button type="button" class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-size: 0.8rem; padding: 5px 12px;" onclick="openCollectFeeModal(${studentId})"><i class="fa fa-receipt me-1"></i> View</button>`;
        }
    }

    // Recalculate summary cards
    if (typeof applyLiveFilter === 'function') {
        applyLiveFilter();
    }
}

// -------------------------------------------------------------
// 5. ASSIGN FEES MODAL AJAX (NO RELOAD)
// -------------------------------------------------------------
function submitAssignFeesAjax(event) {
    event.preventDefault();
    const btn = document.getElementById('btnSubmitAssign');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Assigning...';

    const form = document.getElementById('modalAssignFeesForm');
    const formData = new FormData(form);

    fetch('<?php echo URLROOT; ?>/fees/ajaxAssignToClass', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = origHtml;

        const alertBox = document.getElementById('modalAssignAlertBox');
        if (res.success) {
            alertBox.innerHTML = '<div class="alert alert-success py-2 mb-3 border-0"><i class="fa fa-check-circle me-1"></i> ' + escapeHtml(res.message) + '</div>';
            alertBox.style.display = 'block';

            const assignedClassId = document.getElementById('assign_class_id').value;
            if (assignedClassId) {
                reloadStudentsForClassAjax(assignedClassId);
            }

            setTimeout(() => {
                const m = bootstrap.Modal.getInstance(document.getElementById('assignFeesModal'));
                if (m) m.hide();
                alertBox.style.display = 'none';
            }, 1200);
        } else {
            alertBox.innerHTML = '<div class="alert alert-danger py-2 mb-3 border-0"><i class="fa fa-exclamation-circle me-1"></i> ' + escapeHtml(res.message || 'Assignment failed.') + '</div>';
            alertBox.style.display = 'block';
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = origHtml;
        alert('Network error assigning fees.');
    });
}

// Dynamic section dropdown for assign modal
const assignClassSelect = document.getElementById('assign_class_id');
const assignSecSelect = document.getElementById('assign_section_id');
if (assignClassSelect && assignSecSelect) {
    const allSecOpts = Array.from(assignSecSelect.querySelectorAll('option')).map(o => ({
        val: o.value,
        txt: o.textContent,
        cid: o.getAttribute('data-class-id')
    }));

    assignClassSelect.addEventListener('change', function() {
        const selectedCid = this.value;
        assignSecSelect.innerHTML = '<option value="">-- All Sections --</option>';
        allSecOpts.forEach(item => {
            if (!item.val) return;
            if (!selectedCid || item.cid === selectedCid) {
                const opt = document.createElement('option');
                opt.value = item.val;
                opt.textContent = item.txt;
                opt.setAttribute('data-class-id', item.cid);
                assignSecSelect.appendChild(opt);
            }
        });
    });
}

// -------------------------------------------------------------
// 6. FEE GROUPS CRUD (AJAX INLINE)
// -------------------------------------------------------------
function submitGroupAjax(event) {
    event.preventDefault();
    const btn = document.getElementById('btnSaveGroup');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    const form = document.getElementById('feeGroupForm');
    const formData = new FormData(form);

    fetch('<?php echo URLROOT; ?>/fees/ajaxSaveGroup', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = origHtml;

        if (res.success) {
            renderGroupsTable(res.groups);
            updateGroupDropdowns(res.groups);
            resetGroupForm();
            const alertBox = document.getElementById('groupModalAlert');
            alertBox.innerHTML = '<div class="alert alert-success py-2 mb-3 border-0"><i class="fa fa-check-circle me-1"></i> ' + escapeHtml(res.message) + '</div>';
            alertBox.style.display = 'block';
            setTimeout(() => { alertBox.style.display = 'none'; }, 3000);
        } else {
            alert('Error: ' + (res.message || 'Failed to save group'));
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = origHtml;
        alert('Network error saving fee group.');
    });
}

function renderGroupsTable(groups) {
    const tbody = document.getElementById('groupsTableBody');
    const badge = document.getElementById('groupsCountBadge');
    if (!tbody) return;

    badge.textContent = groups.length + ' Groups';
    tbody.innerHTML = '';

    if (groups.length === 0) {
        tbody.innerHTML = '<tr id="emptyGroupsRow"><td colspan="3" class="text-center text-muted py-3">No fee groups found.</td></tr>';
        return;
    }

    groups.forEach(grp => {
        const tr = document.createElement('tr');
        tr.id = 'groupRow_' + grp.id;
        tr.innerHTML = `
            <td class="fw-bold text-dark group-cell-name">${escapeHtml(grp.group_name)}</td>
            <td class="text-muted small group-cell-desc">${escapeHtml(grp.description || '-')}</td>
            <td class="text-end">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary py-1 px-2" title="Manage Master" onclick="openFeeMasterModal(${grp.id})">
                        <i class="fa fa-cogs me-1"></i> Master
                    </button>
                    <button type="button" class="btn btn-outline-secondary py-1 px-2" title="Edit Group" onclick="editGroupInline(${grp.id}, '${escapeJs(grp.group_name)}', '${escapeJs(grp.description || '')}')">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger py-1 px-2" title="Delete Group" onclick="deleteGroupInline(${grp.id}, '${escapeJs(grp.group_name)}')">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function editGroupInline(id, name, desc) {
    document.getElementById('group_id').value = id;
    document.getElementById('group_name').value = name;
    document.getElementById('group_description').value = desc;
    document.getElementById('groupFormTitle').textContent = 'Edit Fee Group';
    document.getElementById('btnSaveGroup').innerHTML = '<i class="fa fa-check me-1"></i> Update Group';
    document.getElementById('btnCancelGroupEdit').style.display = 'inline-block';
    document.getElementById('group_name').focus();
}

function resetGroupForm() {
    document.getElementById('feeGroupForm').reset();
    document.getElementById('group_id').value = '';
    document.getElementById('groupFormTitle').textContent = 'Add New Fee Group';
    document.getElementById('btnSaveGroup').innerHTML = '<i class="fa fa-save me-1"></i> Save Group';
    document.getElementById('btnCancelGroupEdit').style.display = 'none';
}

function deleteGroupInline(id, name) {
    if (!confirm('Are you sure you want to delete fee group "' + name + '"? All attached master fees will also be removed.')) {
        return;
    }

    fetch('<?php echo URLROOT; ?>/fees/ajaxDeleteGroup/' + id, { method: 'POST' })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                renderGroupsTable(res.groups);
                updateGroupDropdowns(res.groups);
            } else {
                alert('Could not delete group: ' + (res.message || ''));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error deleting fee group.');
        });
}

function updateGroupDropdowns(groups) {
    // Update masterGroupSelect
    const mSelect = document.getElementById('masterGroupSelect');
    if (mSelect) {
        const curVal = mSelect.value;
        mSelect.innerHTML = '';
        groups.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.group_name;
            mSelect.appendChild(opt);
        });
        if (curVal) mSelect.value = curVal;
    }

    // Update assign_fee_group_id
    const aSelect = document.getElementById('assign_fee_group_id');
    if (aSelect) {
        aSelect.innerHTML = '<option value="">-- Choose Fee Group --</option>';
        groups.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.id;
            opt.textContent = g.group_name;
            aSelect.appendChild(opt);
        });
    }
}

// -------------------------------------------------------------
// 7. FEE TYPES CRUD (AJAX INLINE)
// -------------------------------------------------------------
function submitTypeAjax(event) {
    event.preventDefault();
    const btn = document.getElementById('btnSaveType');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    const form = document.getElementById('feeTypeForm');
    const formData = new FormData(form);

    fetch('<?php echo URLROOT; ?>/fees/ajaxSaveType', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = origHtml;

        if (res.success) {
            renderTypesTable(res.types);
            updateTypeDropdowns(res.types);
            resetTypeForm();
            const alertBox = document.getElementById('typeModalAlert');
            alertBox.innerHTML = '<div class="alert alert-success py-2 mb-3 border-0"><i class="fa fa-check-circle me-1"></i> ' + escapeHtml(res.message) + '</div>';
            alertBox.style.display = 'block';
            setTimeout(() => { alertBox.style.display = 'none'; }, 3000);
        } else {
            alert('Error: ' + (res.message || 'Failed to save fee type'));
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = origHtml;
        alert('Network error saving fee type.');
    });
}

function renderTypesTable(types) {
    const tbody = document.getElementById('typesTableBody');
    const badge = document.getElementById('typesCountBadge');
    if (!tbody) return;

    badge.textContent = types.length + ' Types';
    tbody.innerHTML = '';

    if (types.length === 0) {
        tbody.innerHTML = '<tr id="emptyTypesRow"><td colspan="4" class="text-center text-muted py-3">No fee types found.</td></tr>';
        return;
    }

    types.forEach(tp => {
        const tr = document.createElement('tr');
        tr.id = 'typeRow_' + tp.id;
        tr.innerHTML = `
            <td class="fw-bold text-dark type-cell-name">${escapeHtml(tp.type_name)}</td>
            <td><span class="badge bg-light text-dark border font-monospace type-cell-code">${escapeHtml(tp.type_code)}</span></td>
            <td class="text-muted small type-cell-desc">${escapeHtml(tp.description || '-')}</td>
            <td class="text-end">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary py-1 px-2" title="Edit Type" onclick="editTypeInline(${tp.id}, '${escapeJs(tp.type_name)}', '${escapeJs(tp.type_code)}', '${escapeJs(tp.description || '')}')">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger py-1 px-2" title="Delete Type" onclick="deleteTypeInline(${tp.id}, '${escapeJs(tp.type_name)}')">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function editTypeInline(id, name, code, desc) {
    document.getElementById('type_id').value = id;
    document.getElementById('type_name').value = name;
    document.getElementById('type_code').value = code;
    document.getElementById('type_description').value = desc;
    document.getElementById('typeFormTitle').textContent = 'Edit Fee Type';
    document.getElementById('btnSaveType').innerHTML = '<i class="fa fa-check me-1"></i> Update Type';
    document.getElementById('btnCancelTypeEdit').style.display = 'inline-block';
    document.getElementById('type_name').focus();
}

function resetTypeForm() {
    document.getElementById('feeTypeForm').reset();
    document.getElementById('type_id').value = '';
    document.getElementById('typeFormTitle').textContent = 'Add New Fee Type';
    document.getElementById('btnSaveType').innerHTML = '<i class="fa fa-save me-1"></i> Save Fee Type';
    document.getElementById('btnCancelTypeEdit').style.display = 'none';
}

function deleteTypeInline(id, name) {
    if (!confirm('Are you sure you want to delete fee type "' + name + '"?')) {
        return;
    }

    fetch('<?php echo URLROOT; ?>/fees/ajaxDeleteType/' + id, { method: 'POST' })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                renderTypesTable(res.types);
                updateTypeDropdowns(res.types);
            } else {
                alert('Could not delete type: ' + (res.message || ''));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network error deleting fee type.');
        });
}

function updateTypeDropdowns(types) {
    const mTypeSelect = document.getElementById('master_type_id');
    if (mTypeSelect) {
        mTypeSelect.innerHTML = '<option value="">-- Choose Type --</option>';
        types.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.type_name + ' (' + t.type_code + ')';
            mTypeSelect.appendChild(opt);
        });
    }
}

// -------------------------------------------------------------
// 8. FEE MASTER CRUD (AJAX INLINE)
// -------------------------------------------------------------
function openFeeMasterModal(groupId = null) {
    openSafeModal('feeMasterModal', () => {
        const mSelect = document.getElementById('masterGroupSelect');
        if (groupId && mSelect) {
            mSelect.value = groupId;
        }
        if (mSelect && mSelect.value) {
            loadGroupMaster(mSelect.value);
        } else if (mSelect && mSelect.options.length > 0) {
            mSelect.selectedIndex = 0;
            loadGroupMaster(mSelect.value);
        }
    });
}

function loadGroupMaster(groupId) {
    if (!groupId) return;
    document.getElementById('master_group_id').value = groupId;
    resetMasterForm();

    fetch('<?php echo URLROOT; ?>/fees/ajaxGetGroupMasters/' + groupId)
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                renderMasterTable(res.masters || []);
            }
        })
        .catch(err => {
            console.error(err);
        });
}

function renderMasterTable(masters) {
    const tbody = document.getElementById('masterTableBody');
    const badge = document.getElementById('masterItemsCountBadge');
    const footer = document.getElementById('masterTableFooter');
    const sumAmtEl = document.getElementById('masterSumAmount');
    const sumFineEl = document.getElementById('masterSumFine');
    if (!tbody) return;

    badge.textContent = masters.length + ' Items';
    tbody.innerHTML = '';

    if (masters.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="fa fa-info-circle me-1"></i> No fee types attached to this group yet. Add one from the left form.</td></tr>';
        if (footer) footer.style.display = 'none';
        return;
    }

    let totalAmt = 0;
    let totalFine = 0;

    masters.forEach(m => {
        const amt = parseFloat(m.amount || 0);
        const fine = parseFloat(m.fine_amount || 0);
        totalAmt += amt;
        totalFine += fine;

        const tr = document.createElement('tr');
        tr.id = 'masterRow_' + m.id;
        tr.innerHTML = `
            <td class="fw-bold text-dark">${escapeHtml(m.type_name)}</td>
            <td><span class="badge bg-light text-dark border font-monospace">${escapeHtml(m.type_code)}</span></td>
            <td class="text-end font-monospace fw-bold">${formatCurrency(amt)}</td>
            <td class="text-muted small">${escapeHtml(m.due_date || 'No Limit')}</td>
            <td class="text-end font-monospace text-danger">${formatCurrency(fine)}</td>
            <td class="text-end">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary py-1 px-2" title="Edit Item" onclick="editMasterInline(${m.id}, ${m.fee_type_id}, ${amt}, '${escapeJs(m.due_date || '')}', ${fine})">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger py-1 px-2" title="Remove Item" onclick="deleteMasterInline(${m.id})">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });

    if (footer) {
        footer.style.display = '';
        sumAmtEl.textContent = formatCurrency(totalAmt);
        sumFineEl.textContent = formatCurrency(totalFine);
    }
}

function submitMasterAjax(event) {
    event.preventDefault();
    const btn = document.getElementById('btnSaveMaster');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    const form = document.getElementById('masterItemForm');
    const formData = new FormData(form);

    fetch('<?php echo URLROOT; ?>/fees/ajaxSaveMaster', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = origHtml;

        if (res.success) {
            renderMasterTable(res.masters || []);
            resetMasterForm();
            const alertBox = document.getElementById('masterModalAlert');
            alertBox.innerHTML = '<div class="alert alert-success py-2 mb-3 border-0"><i class="fa fa-check-circle me-1"></i> ' + escapeHtml(res.message) + '</div>';
            alertBox.style.display = 'block';
            setTimeout(() => { alertBox.style.display = 'none'; }, 3000);
        } else {
            alert('Error: ' + (res.message || 'Failed to save master fee item'));
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = origHtml;
        alert('Network error saving fee master item.');
    });
}

function editMasterInline(id, typeId, amount, dueDate, fine) {
    document.getElementById('master_id').value = id;
    document.getElementById('master_type_id').value = typeId;
    document.getElementById('master_amount').value = amount;
    document.getElementById('master_due_date').value = dueDate || '';
    document.getElementById('master_fine').value = fine || '0.00';

    document.getElementById('masterFormTitle').textContent = 'Edit Fee Item';
    document.getElementById('btnSaveMaster').innerHTML = '<i class="fa fa-check me-1"></i> Update Item';
    document.getElementById('btnCancelMasterEdit').style.display = 'inline-block';
    document.getElementById('master_amount').focus();
}

function resetMasterForm() {
    document.getElementById('master_id').value = '';
    document.getElementById('master_type_id').value = '';
    document.getElementById('master_amount').value = '';
    document.getElementById('master_due_date').value = '';
    document.getElementById('master_fine').value = '0.00';

    document.getElementById('masterFormTitle').textContent = 'Add Fee Type to Group';
    document.getElementById('btnSaveMaster').innerHTML = '<i class="fa fa-save me-1"></i> Add to Group';
    document.getElementById('btnCancelMasterEdit').style.display = 'none';
}

function deleteMasterInline(id) {
    if (!confirm('Are you sure you want to remove this fee type from the group?')) {
        return;
    }

    const groupId = document.getElementById('masterGroupSelect').value;
    const formData = new FormData();
    formData.append('group_id', groupId);

    fetch('<?php echo URLROOT; ?>/fees/ajaxDeleteMaster/' + id, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            renderMasterTable(res.masters || []);
        } else {
            alert('Could not delete: ' + (res.message || ''));
        }
    })
    .catch(err => {
        console.error(err);
        alert('Network error deleting fee master item.');
    });
}

function escapeJs(str) {
    if (!str) return '';
    return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

// -------------------------------------------------------------
// 8. QUICK ASSIGN FEE TO STUDENT & CLASS FEE STRUCTURE FUNCTIONS
// -------------------------------------------------------------
function quickAssignFeeToCurrentStudent() {
    if (!currentOpenStudentId) return;
    const btn = document.getElementById('btnQuickAssignNow');
    let origHtml = '';
    if (btn) {
        origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Assigning...';
    }

    const formData = new FormData();
    formData.append('student_id', currentOpenStudentId);

    fetch('<?php echo URLROOT; ?>/fees/ajaxQuickAssignStudentFee', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = origHtml;
        }
        if (res.success) {
            const alertBox = document.getElementById('modalAlertBox');
            alertBox.innerHTML = '<div class="alert alert-success d-flex align-items-center gap-2 mb-3 border-0 py-2"><i class="fa fa-check-circle fs-5"></i><div>' + escapeHtml(res.message) + '</div></div>';
            alertBox.style.display = 'block';

            renderModalLedger(res);
            renderPaymentHistory(res.history || []);
            updateStudentRowInMemory(res.student_id, res.total_assigned, res.total_paid, res.balance);
        } else {
            alert('Could not assign fee: ' + (res.message || 'Operation failed.'));
        }
    })
    .catch(err => {
        console.error(err);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = origHtml;
        }
        alert('Network error while assigning class fee.');
    });
}

function openClassFeeParticularsModal(classId = null) {
    if (!classId) {
        const fClass = document.getElementById('filterClass');
        if (fClass && fClass.value) classId = fClass.value;
    }
    if (!classId) {
        const clsSel = document.getElementById('cfpClassSelect');
        if (clsSel && clsSel.options.length > 1) {
            classId = clsSel.options[1].value;
        }
    }

    openSafeModal('classFeeParticularsModal', () => {
        const alertBox = document.getElementById('classFeeAlertBox');
        if (alertBox) alertBox.style.display = 'none';
        if (classId) {
            const cSel = document.getElementById('cfpClassSelect');
            if (cSel) cSel.value = classId;
            loadClassFeeParticulars(classId);
        }
    });
}

function loadClassFeeParticulars(classId) {
    if (!classId) {
        document.getElementById('cfpParticularsTableBody').innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Please select a class.</td></tr>';
        document.getElementById('cfpTotalAmountDisplay').textContent = formatCurrency(0);
        document.getElementById('cfpStudentCountNum').textContent = '0';
        return;
    }

    const tbody = document.getElementById('cfpParticularsTableBody');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary me-2"></div> Loading class fee particulars...</td></tr>';

    fetch('<?php echo URLROOT; ?>/fees/ajaxGetClassFeeParticulars/' + classId)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                if (data.types && Array.isArray(data.types)) {
                    cfpAvailableTypes = data.types;
                }
                document.getElementById('cfpStudentCountNum').textContent = data.student_count || 0;
                document.getElementById('cfpGroupId').value = data.group ? data.group.id : '';
                document.getElementById('cfpGroupName').value = data.group ? data.group.group_name : data.default_group_name;

                renderCfpParticulars(data.particulars || []);
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">' + escapeHtml(data.message || 'Error loading class particulars.') + '</td></tr>';
            }
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Network error loading class particulars.</td></tr>';
        });
}

function renderCfpParticulars(items) {
    const tbody = document.getElementById('cfpParticularsTableBody');
    tbody.innerHTML = '';

    if (!items || items.length === 0) {
        const defaultHeads = [
            { type_name: 'Monthly Tuition Fee', type_code: 'TUIT', amount: 3500.00, fine: 200.00 },
            { type_name: 'Computer & IT Lab Fee', type_code: 'LAB', amount: 500.00, fine: 0.00 },
            { type_name: 'Assessment & Exam Fund', type_code: 'EXM', amount: 300.00, fine: 0.00 },
            { type_name: 'Generator & Utility Fund', type_code: 'UTL', amount: 200.00, fine: 0.00 }
        ];
        defaultHeads.forEach(dh => {
            addCfpParticularRow(dh);
        });
    } else {
        items.forEach(it => {
            addCfpParticularRow({
                type_id: it.fee_type_id,
                type_name: it.type_name,
                type_code: it.type_code,
                amount: it.amount,
                fine: it.fine_amount
            });
        });
    }
    recalculateCfpTotal();
}

function addCfpParticularRow(prefill = {}) {
    const tbody = document.getElementById('cfpParticularsTableBody');
    if (tbody.children.length === 1 && tbody.children[0].querySelector('td[colspan]')) {
        tbody.innerHTML = '';
    }

    const rowIdx = tbody.children.length + 1;
    const tr = document.createElement('tr');
    tr.className = 'cfp-item-row';

    let typeOptions = '<option value="">-- Choose Existing Fee Head --</option>';
    let selectedTypeId = prefill.type_id || '';
    let matchedInTypes = false;

    if (cfpAvailableTypes && Array.isArray(cfpAvailableTypes)) {
        cfpAvailableTypes.forEach(t => {
            const isSel = (selectedTypeId && selectedTypeId == t.id) || (!selectedTypeId && prefill.type_name && prefill.type_name.toLowerCase() === t.type_name.toLowerCase());
            if (isSel) {
                matchedInTypes = true;
                selectedTypeId = t.id;
            }
            typeOptions += `<option value="${t.id}" data-code="${escapeHtml(t.type_code)}" ${isSel ? 'selected' : ''}>${escapeHtml(t.type_name)} (${escapeHtml(t.type_code)})</option>`;
        });
    }

    const typeCodeVal = prefill.type_code || (matchedInTypes ? '' : 'FEE');
    const typeNameVal = prefill.type_name || '';
    const amountVal = parseFloat(prefill.amount || 0).toFixed(2);
    const fineVal = parseFloat(prefill.fine || 0).toFixed(2);

    tr.innerHTML = `
        <td class="ps-3 font-monospace fw-bold text-muted small row-num">${rowIdx}</td>
        <td>
            <select class="form-select form-select-sm cfp-type-select mb-1" onchange="onCfpTypeSelectChange(this)" style="border-radius: 6px;">
                ${typeOptions}
                <option value="custom" ${(!matchedInTypes && typeNameVal) ? 'selected' : ''}>+ Custom Particular Name...</option>
            </select>
            <input type="text" class="form-control form-control-sm cfp-custom-name" placeholder="Type particular name..." value="${escapeHtml(typeNameVal)}" style="border-radius: 6px; ${(!matchedInTypes && typeNameVal) ? '' : 'display: none;'}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm font-monospace cfp-type-code" value="${escapeHtml(typeCodeVal)}" placeholder="CODE" style="border-radius: 6px;">
        </td>
        <td class="text-end">
            <input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace text-end cfp-amount-input fw-bold" value="${amountVal}" oninput="recalculateCfpTotal()" style="border-radius: 6px;">
        </td>
        <td class="text-end">
            <input type="number" step="0.01" min="0" class="form-control form-control-sm font-monospace text-end cfp-fine-input" value="${fineVal}" style="border-radius: 6px;">
        </td>
        <td class="pe-3 text-center">
            <button type="button" class="btn btn-outline-danger btn-xs py-1 px-2" style="font-size: 0.75rem; border-radius: 6px;" onclick="removeCfpParticularRow(this)" title="Remove Head">
                <i class="fa fa-trash-alt"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    recalculateCfpTotal();
}

function onCfpTypeSelectChange(selectEl) {
    const tr = selectEl.closest('tr');
    const customInput = tr.querySelector('.cfp-custom-name');
    const codeInput = tr.querySelector('.cfp-type-code');

    if (selectEl.value === 'custom') {
        customInput.style.display = 'block';
        customInput.focus();
    } else {
        customInput.style.display = 'none';
        const opt = selectEl.options[selectEl.selectedIndex];
        if (opt && opt.getAttribute('data-code')) {
            codeInput.value = opt.getAttribute('data-code');
        }
    }
}

function removeCfpParticularRow(btn) {
    const tr = btn.closest('tr');
    tr.remove();
    const rows = document.querySelectorAll('#cfpParticularsTableBody .row-num');
    rows.forEach((td, idx) => {
        td.textContent = idx + 1;
    });
    recalculateCfpTotal();
}

function recalculateCfpTotal() {
    const amountInputs = document.querySelectorAll('.cfp-amount-input');
    let total = 0;
    amountInputs.forEach(inp => {
        total += parseFloat(inp.value || 0);
    });
    document.getElementById('cfpTotalAmountDisplay').textContent = formatCurrency(total);
}

function saveClassFeeParticularsAjax() {
    const classId = document.getElementById('cfpClassSelect').value;
    if (!classId) {
        alert('Please select a class first.');
        return;
    }

    const btn = document.getElementById('btnSaveClassFeeStructure');
    const origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving & Syncing...';

    const rows = document.querySelectorAll('#cfpParticularsTableBody .cfp-item-row');
    const items = [];

    rows.forEach(tr => {
        const typeSelect = tr.querySelector('.cfp-type-select');
        const customName = tr.querySelector('.cfp-custom-name');
        const codeInput = tr.querySelector('.cfp-type-code');
        const amountInput = tr.querySelector('.cfp-amount-input');
        const fineInput = tr.querySelector('.cfp-fine-input');

        let typeId = typeSelect.value !== 'custom' ? typeSelect.value : '';
        let typeName = '';
        if (typeSelect.value === 'custom') {
            typeName = customName.value.trim();
        } else if (typeSelect.selectedIndex > 0) {
            typeName = typeSelect.options[typeSelect.selectedIndex].text.split('(')[0].trim();
        }

        const typeCode = codeInput.value.trim();
        const amount = parseFloat(amountInput.value || 0);
        const fine = parseFloat(fineInput.value || 0);

        if (typeId || typeName) {
            items.push({
                type_id: typeId,
                type_name: typeName,
                type_code: typeCode,
                amount: amount,
                fine: fine
            });
        }
    });

    const formData = new FormData();
    formData.append('class_id', classId);
    formData.append('group_id', document.getElementById('cfpGroupId').value);
    formData.append('group_name', document.getElementById('cfpGroupName').value);
    formData.append('sync_all_students', document.getElementById('cfpSyncAllStudents').checked ? '1' : '0');
    formData.append('items_json', JSON.stringify(items));

    fetch('<?php echo URLROOT; ?>/fees/ajaxSaveClassFeeParticulars', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = origHtml;

        if (res.success) {
            const alertBox = document.getElementById('classFeeAlertBox');
            alertBox.innerHTML = '<div class="alert alert-success d-flex align-items-center gap-2 mb-3 border-0 py-2"><i class="fa fa-check-circle fs-5"></i><div>' + escapeHtml(res.message) + '</div></div>';
            alertBox.style.display = 'block';

            if (res.group_id) {
                document.getElementById('cfpGroupId').value = res.group_id;
            }

            // Sync updated student fees to table automatically via AJAX
            if (document.getElementById('cfpSyncAllStudents').checked) {
                reloadStudentsForClassAjax(classId);
            }

            // Cleanly close modal after short notification
            setTimeout(() => {
                const modalInst = bootstrap.Modal.getInstance(document.getElementById('classFeeParticularsModal'));
                if (modalInst) modalInst.hide();
            }, 1200);
        } else {
            alert('Error saving fee structure: ' + (res.message || 'Operation failed.'));
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = origHtml;
        alert('Network error occurred while saving fee structure.');
    });
}

// -------------------------------------------------------------
// 9. RUNTIME CLASS & SECTION LIVE FILTER (NO PAGE RELOAD)
// -------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    const filterClass = document.getElementById('filterClass');
    const filterSection = document.getElementById('filterSection');
    const liveSearchInput = document.getElementById('liveSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    const tableBody = document.getElementById('feeStudentsTableBody');
    const rows = tableBody ? Array.from(tableBody.querySelectorAll('.student-row')) : [];
    const noResultsState = document.getElementById('noResultsState');
    const visibleCounterBadge = document.getElementById('visibleCounterBadge');
    const labelFilterText = document.getElementById('labelFilterText');
    
    // KPI Elements
    const statStudentCount = document.getElementById('statStudentCount');
    const statTotalFees = document.getElementById('statTotalFees');
    const statTotalPaid = document.getElementById('statTotalPaid');
    const statTotalBalance = document.getElementById('statTotalBalance');

    // Auto-fill assign class modal when opened
    const assignFeesModalEl = document.getElementById('assignFeesModal');
    if (assignFeesModalEl) {
        assignFeesModalEl.addEventListener('show.bs.modal', function() {
            if (filterClass && filterClass.value) {
                const ac = document.getElementById('assign_class_id');
                if (ac) ac.value = filterClass.value;
            }
        });
    }

    // Cache initial section options
    const sectionOptions = Array.from(filterSection.querySelectorAll('option')).map(opt => ({
        value: opt.value,
        text: opt.text,
        classId: opt.getAttribute('data-class-id')
    }));

    function updateSectionDropdown(selectedClassId) {
        filterSection.innerHTML = '<option value="">-- All Sections --</option>';
        sectionOptions.forEach(opt => {
            if (!opt.value) return;
            if (!selectedClassId || opt.classId === selectedClassId) {
                const optionEl = document.createElement('option');
                optionEl.value = opt.value;
                optionEl.textContent = opt.text;
                optionEl.setAttribute('data-class-id', opt.classId);
                filterSection.appendChild(optionEl);
            }
        });
    }

    window.selectClassQuick = function(classId) {
        if (!filterClass) return;
        filterClass.value = classId;
        updateSectionDropdown(classId);
        filterSection.value = '';
        applyLiveFilter();
        const qEdit = document.getElementById('btnQuickEditClassFee');
        if (qEdit) qEdit.style.display = 'inline-block';
    };

    window.toggleSelectAllVisible = function(masterCb) {
        const isChecked = masterCb.checked;
        const visibleRows = rows.filter(r => r.style.display !== 'none');
        visibleRows.forEach(r => {
            const cb = r.querySelector('.student-select-checkbox');
            if (cb) cb.checked = isChecked;
        });
        updateSelectedChallanCount();
    };

    window.updateSelectedChallanCount = function() {
        const visibleRows = rows.filter(r => r.style.display !== 'none');
        const checkedBoxes = visibleRows.map(r => r.querySelector('.student-select-checkbox')).filter(cb => cb && cb.checked);
        const count = checkedBoxes.length;

        const btn = document.getElementById('btnPrintSelectedChallans');
        const countNum = document.getElementById('selectedCountNum');
        if (countNum) countNum.textContent = count;
        if (btn) btn.style.display = count > 0 ? 'inline-block' : 'none';

        const masterCb = document.getElementById('selectAllStudentsCheckbox');
        if (masterCb) {
            if (visibleRows.length === 0) {
                masterCb.checked = false;
                masterCb.indeterminate = false;
            } else {
                masterCb.checked = (count === visibleRows.length && count > 0);
                masterCb.indeterminate = (count > 0 && count < visibleRows.length);
            }
        }
    };

    window.printSelectedChallans = function() {
        const visibleRows = rows.filter(r => r.style.display !== 'none');
        const checkedBoxes = visibleRows.map(r => r.querySelector('.student-select-checkbox')).filter(cb => cb && cb.checked);
        if (checkedBoxes.length === 0) {
            alert('Please select at least one student using the checkboxes to print challans.');
            return;
        }

        const ids = checkedBoxes.map(cb => cb.value).filter(Boolean);
        const selectedClassId = filterClass.value.trim();

        let url = '<?php echo URLROOT; ?>/fees/batchChallans?student_ids=' + encodeURIComponent(ids.join(','));
        if (selectedClassId) {
            url += '&class_id=' + encodeURIComponent(selectedClassId);
        }

        window.open(url, '_blank');
    };

    window.applyLiveFilter = function() {
        const selectedClassId = filterClass.value.trim();
        const selectedSectionId = filterSection.value.trim();
        const keyword = liveSearchInput.value.toLowerCase().trim();

        const hasFilter = Boolean(selectedClassId || selectedSectionId || keyword);
        const promptState = document.getElementById('selectFilterPromptState');

        let visibleCount = 0;
        let sumFees = 0;
        let sumPaid = 0;
        let sumBalance = 0;

        if (!hasFilter) {
            // Default state: 0 entries shown until user selects/filters
            rows.forEach(row => {
                row.style.display = 'none';
                const cb = row.querySelector('.student-select-checkbox');
                if (cb) cb.checked = false;
            });

            if (promptState) promptState.style.display = 'block';
            if (noResultsState) noResultsState.style.display = 'none';
            if (visibleCounterBadge) visibleCounterBadge.textContent = '0 Students';
            if (statStudentCount) statStudentCount.textContent = '0';
            if (statTotalFees) statTotalFees.textContent = formatCurrency(0);
            if (statTotalPaid) statTotalPaid.textContent = formatCurrency(0);
            if (statTotalBalance) statTotalBalance.textContent = formatCurrency(0);
            if (labelFilterText) labelFilterText.textContent = 'No Filter Applied • Select Class or Filter to View';
            if (clearSearchBtn) clearSearchBtn.style.display = 'none';
            updateSelectedChallanCount();
            return;
        }

        // When a filter is selected or keyword entered:
        if (promptState) promptState.style.display = 'none';

        rows.forEach(row => {
            const rowClassId = (row.getAttribute('data-class-id') || '').trim();
            const rowSectionId = (row.getAttribute('data-section-id') || '').trim();
            const rowSearch = (row.getAttribute('data-search') || '').toLowerCase();
            const fee = parseFloat(row.getAttribute('data-fee') || '0');
            const paid = parseFloat(row.getAttribute('data-paid') || '0');
            const balance = parseFloat(row.getAttribute('data-balance') || '0');

            const matchClass = !selectedClassId || (rowClassId === selectedClassId);
            const matchSection = !selectedSectionId || (rowSectionId === selectedSectionId);
            const matchKeyword = !keyword || rowSearch.includes(keyword);

            if (matchClass && matchSection && matchKeyword) {
                row.style.display = '';
                visibleCount++;
                sumFees += fee;
                sumPaid += paid;
                sumBalance += balance;
            } else {
                row.style.display = 'none';
                const cb = row.querySelector('.student-select-checkbox');
                if (cb) cb.checked = false;
            }
        });

        // Update Counter Badges & KPIs
        if (visibleCounterBadge) visibleCounterBadge.textContent = visibleCount + ' Students';
        if (statStudentCount) statStudentCount.textContent = visibleCount;
        if (statTotalFees) statTotalFees.textContent = formatCurrency(sumFees);
        if (statTotalPaid) statTotalPaid.textContent = formatCurrency(sumPaid);
        if (statTotalBalance) statTotalBalance.textContent = formatCurrency(sumBalance);

        if (noResultsState) {
            noResultsState.style.display = (visibleCount === 0) ? 'block' : 'none';
        }

        if (labelFilterText) {
            let className = 'All Classes';
            if (selectedClassId) {
                const selOption = filterClass.options[filterClass.selectedIndex];
                if (selOption) className = selOption.text.trim();
            }
            let sectionName = 'All Sections';
            if (selectedSectionId) {
                const selSecOption = filterSection.options[filterSection.selectedIndex];
                if (selSecOption) sectionName = selSecOption.text.trim();
            }
            labelFilterText.textContent = className + ' • ' + sectionName + (keyword ? ' (filtered: "' + keyword + '")' : '');
        }

        if (clearSearchBtn) {
            clearSearchBtn.style.display = keyword ? 'block' : 'none';
        }

        updateSelectedChallanCount();
    };

    filterClass.addEventListener('change', function() {
        updateSectionDropdown(this.value);
        filterSection.value = '';
        applyLiveFilter();
        const qEdit = document.getElementById('btnQuickEditClassFee');
        if (qEdit) {
            qEdit.style.display = this.value ? 'inline-block' : 'none';
        }
    });

    filterSection.addEventListener('change', function() {
        applyLiveFilter();
    });

    liveSearchInput.addEventListener('input', function() {
        applyLiveFilter();
    });

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            liveSearchInput.value = '';
            applyLiveFilter();
            liveSearchInput.focus();
        });
    }

    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            filterClass.value = '';
            updateSectionDropdown('');
            filterSection.value = '';
            liveSearchInput.value = '';
            const qEdit = document.getElementById('btnQuickEditClassFee');
            if (qEdit) qEdit.style.display = 'none';
            applyLiveFilter();
        });
    }

    // Auto-open modal if student_id is in query string or URL hash
    const urlParams = new URLSearchParams(window.location.search);
    const targetStudentId = urlParams.get('student_id');
    const targetClassId = urlParams.get('class_id');
    const targetSectionId = urlParams.get('section_id');
    const targetSearch = urlParams.get('search');

    if (targetClassId) {
        filterClass.value = targetClassId;
        updateSectionDropdown(targetClassId);
        if (targetSectionId) {
            filterSection.value = targetSectionId;
        }
        const qEdit = document.getElementById('btnQuickEditClassFee');
        if (qEdit) qEdit.style.display = 'inline-block';
    } else if (targetStudentId) {
        const targetRow = document.getElementById('studentRow_' + targetStudentId);
        if (targetRow) {
            const clsId = targetRow.getAttribute('data-class-id');
            if (clsId) {
                filterClass.value = clsId;
                updateSectionDropdown(clsId);
            }
        }
    }

    if (targetSearch) {
        liveSearchInput.value = targetSearch;
    }

    // Initial Filter (by default 0 entries unless URL params specified)
    applyLiveFilter();

    if (targetStudentId) {
        setTimeout(() => openCollectFeeModal(targetStudentId), 200);
    }
    if (window.location.hash === '#assign') {
        openAssignFeesModal();
    } else if (window.location.hash === '#groups') {
        openFeeGroupsModal();
    } else if (window.location.hash === '#types') {
        openFeeTypesModal();
    } else if (window.location.hash === '#master') {
        openFeeMasterModal();
    } else if (window.location.hash === '#class-fee') {
        openClassFeeParticularsModal();
    }
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
