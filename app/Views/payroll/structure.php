<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$staff = $data['staff'];
$payroll = $data['payroll'];
$isVisiting = strpos($payroll->employment_type ?? ($staff->employment_type ?? ''), 'Visiting') !== false;

$basic = (float)($payroll->basic_salary ?? ($staff->basic_salary ?? 40000));
$rate = (float)($payroll->lecture_rate ?? ($staff->lecture_rate ?? 1500));
$med = (float)($payroll->medical_allowance ?? 0);
$rent = (float)($payroll->house_rent_allowance ?? 0);
$conv = (float)($payroll->conveyance_allowance ?? 0);
$otherEarn = (float)($payroll->other_allowance ?? 0);

$tax = (float)($payroll->tax_deduction ?? 0);
$pf = (float)($payroll->provident_fund ?? 0);
$otherDeduc = (float)($payroll->other_deductions ?? 0);

$net = (float)($payroll->net_salary ?? max(0, $basic + ($med + $rent + $conv + $otherEarn) - ($tax + $pf + $otherDeduc)));
?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/payroll/index" class="text-decoration-none text-muted">Staff Payroll</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Compensation Structure</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Salary Structure &amp; Allowances: <?php echo htmlspecialchars($staff->name); ?></h2>
        <p class="text-muted mb-0 small">Configure base salary or visiting lecture compensation, allowances, tax &amp; statutory deductions.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/staff/profile/<?php echo $staff->id; ?>" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Back to 360° Profile
        </a>
    </div>
</div>

<form action="<?php echo URLROOT; ?>/payroll/structure/<?php echo $staff->id; ?>" method="post" id="payrollStructureForm">
    <div class="row g-4 justify-content-center">
        <!-- Main Form -->
        <div class="col-lg-8">
            
            <!-- Employment Tenure Selector -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-briefcase text-primary me-2"></i>Compensation Contract Model
                    </h6>
                    <span class="badge badge-soft-info font-monospace"><?php echo htmlspecialchars($staff->staff_code); ?></span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Employment Tenure Type <span class="text-danger">*</span></label>
                            <select name="employment_type" id="etypeSelect" class="form-select" required onchange="toggleStructureInputs()">
                                <option value="Permanent" <?php echo !$isVisiting ? 'selected' : ''; ?>>Permanent / Full-Time Regular</option>
                                <option value="Visiting / Per Lecture" <?php echo $isVisiting ? 'selected' : ''; ?>>Visiting Faculty (Per Delivered Lecture)</option>
                                <option value="Contract">Fixed Term Contract</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="basicSalaryWrap" style="<?php echo $isVisiting ? 'display: none;' : ''; ?>">
                            <label class="form-label small fw-bold text-dark">Basic Monthly Pay (Rs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="500" min="0" name="basic_salary" id="basicInput" class="form-control" value="<?php echo $basic; ?>" oninput="calculateNetSalary()">
                            </div>
                        </div>
                        <div class="col-md-6" id="lectureRateWrap" style="<?php echo $isVisiting ? '' : 'display: none;'; ?>">
                            <label class="form-label small fw-bold text-dark">Rate Per Lecture (Rs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="100" min="0" name="lecture_rate" id="rateInput" class="form-control" value="<?php echo $rate; ?>" oninput="calculateNetSalary()">
                            </div>
                            <small class="text-muted smaller">Multiplied by monthly delivered lectures upon payslip generation</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings & Allowances Breakdown -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-success-subtle text-success rounded-3">
                            <i class="fa fa-plus-circle"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Monthly Allowances &amp; Benefits (Earnings)</h6>
                    </div>
                    <span class="badge bg-success-subtle text-success fw-bold" id="totalEarningsBadge">+Rs. <?php echo number_format($med + $rent + $conv + $otherEarn); ?></span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Medical Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="100" min="0" name="medical_allowance" id="medInput" class="form-control allowance-input" value="<?php echo $med; ?>" oninput="calculateNetSalary()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">House Rent Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="100" min="0" name="house_rent_allowance" id="rentInput" class="form-control allowance-input" value="<?php echo $rent; ?>" oninput="calculateNetSalary()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Conveyance Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="100" min="0" name="conveyance_allowance" id="convInput" class="form-control allowance-input" value="<?php echo $conv; ?>" oninput="calculateNetSalary()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Special / Other Allowance</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="100" min="0" name="other_allowance" id="otherEarnInput" class="form-control allowance-input" value="<?php echo $otherEarn; ?>" oninput="calculateNetSalary()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deductions Breakdown -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-danger-subtle text-danger rounded-3">
                            <i class="fa fa-minus-circle"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Statutory &amp; Payroll Deductions</h6>
                    </div>
                    <span class="badge bg-danger-subtle text-danger fw-bold" id="totalDeductionsBadge">-Rs. <?php echo number_format($tax + $pf + $otherDeduc); ?></span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Income Tax (Withholding)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="50" min="0" name="tax_deduction" id="taxInput" class="form-control deduction-input" value="<?php echo $tax; ?>" oninput="calculateNetSalary()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Provident Fund (PF)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="50" min="0" name="provident_fund" id="pfInput" class="form-control deduction-input" value="<?php echo $pf; ?>" oninput="calculateNetSalary()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">Other / Advance Deduction</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="50" min="0" name="other_deductions" id="otherDeducInput" class="form-control deduction-input" value="<?php echo $otherDeduc; ?>" oninput="calculateNetSalary()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Summary Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 90px;">
                <div class="card-header bg-dark text-white py-3 border-0">
                    <h6 class="fw-bold mb-0"><i class="fa fa-calculator me-2"></i>Net Compensation Summary</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Base / Basic Salary:</span>
                        <span class="fw-bold" id="summaryBasic">Rs. <?php echo number_format($basic); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Total Allowances:</span>
                        <span class="text-success fw-bold" id="summaryAllowances">+Rs. <?php echo number_format($med + $rent + $conv + $otherEarn); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small pb-2 border-bottom">
                        <span class="text-muted">Total Deductions:</span>
                        <span class="text-danger fw-bold" id="summaryDeductions">-Rs. <?php echo number_format($tax + $pf + $otherDeduc); ?></span>
                    </div>

                    <div class="p-3 bg-primary-subtle text-primary rounded-3 text-center mb-4">
                        <div class="smaller fw-bold text-uppercase">Projected Net Monthly Salary</div>
                        <div class="h3 fw-bold mb-0" id="summaryNetSalary">Rs. <?php echo number_format($net); ?></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-2">
                        <i class="fa fa-save me-1"></i> Save Salary Structure
                    </button>
                    <a href="<?php echo URLROOT; ?>/staff/profile/<?php echo $staff->id; ?>" class="btn btn-outline-secondary w-100 py-2">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleStructureInputs(){
        var etype = document.getElementById('etypeSelect').value;
        var basicWrap = document.getElementById('basicSalaryWrap');
        var lectureWrap = document.getElementById('lectureRateWrap');

        if(etype.indexOf('Visiting') !== -1){
            lectureWrap.style.display = 'block';
            basicWrap.style.display = 'none';
        } else {
            lectureWrap.style.display = 'none';
            basicWrap.style.display = 'block';
        }
        calculateNetSalary();
    }

    function calculateNetSalary(){
        var etype = document.getElementById('etypeSelect').value;
        var basic = 0;
        if(etype.indexOf('Visiting') !== -1){
            var rate = parseFloat(document.getElementById('rateInput').value) || 0;
            basic = rate * 16; // Projected 16 lectures (4 periods/wk * 4 wks)
            document.getElementById('summaryBasic').innerText = 'Rs. ' + rate.toLocaleString() + ' / lecture (Proj. ~16 lecs)';
        } else {
            basic = parseFloat(document.getElementById('basicInput').value) || 0;
            document.getElementById('summaryBasic').innerText = 'Rs. ' + basic.toLocaleString();
        }

        var med = parseFloat(document.getElementById('medInput').value) || 0;
        var rent = parseFloat(document.getElementById('rentInput').value) || 0;
        var conv = parseFloat(document.getElementById('convInput').value) || 0;
        var otherEarn = parseFloat(document.getElementById('otherEarnInput').value) || 0;
        var totalEarn = med + rent + conv + otherEarn;

        var tax = parseFloat(document.getElementById('taxInput').value) || 0;
        var pf = parseFloat(document.getElementById('pfInput').value) || 0;
        var otherDeduc = parseFloat(document.getElementById('otherDeducInput').value) || 0;
        var totalDeduc = tax + pf + otherDeduc;

        var net = Math.max(0, basic + totalEarn - totalDeduc);

        document.getElementById('totalEarningsBadge').innerText = '+Rs. ' + totalEarn.toLocaleString();
        document.getElementById('summaryAllowances').innerText = '+Rs. ' + totalEarn.toLocaleString();

        document.getElementById('totalDeductionsBadge').innerText = '-Rs. ' + totalDeduc.toLocaleString();
        document.getElementById('summaryDeductions').innerText = '-Rs. ' + totalDeduc.toLocaleString();

        document.getElementById('summaryNetSalary').innerText = 'Rs. ' + Math.round(net).toLocaleString();
    }
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
