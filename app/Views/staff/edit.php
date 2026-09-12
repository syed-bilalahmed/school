<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$staff = $data['staff'];
$isVisiting = strpos($staff->employment_type ?? '', 'Visiting') !== false;
?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/staff/index" class="text-decoration-none text-muted">Staff Directory</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/staff/profile/<?php echo $staff->id; ?>" class="text-decoration-none text-muted"><?php echo htmlspecialchars($staff->name); ?></a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Profile</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Modify Staff Profile: <?php echo htmlspecialchars($staff->name); ?></h2>
        <p class="text-muted mb-0 small">Update employment tenure, designation, civil identity, and payroll particulars.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/staff/profile/<?php echo $staff->id; ?>" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Back to 360° Profile
        </a>
    </div>
</div>

<form action="<?php echo URLROOT; ?>/staff/update/<?php echo $staff->id; ?>" method="post" id="staffEditForm">
    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            
            <!-- SECTION 1: USER ACCOUNT & ERP CREDENTIALS -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary-subtle text-primary rounded-3">
                        <i class="fa fa-user-shield"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Portal Credentials &amp; Access Role</h5>
                        <small class="text-muted">Staff user account and administrative role settings.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Full Staff Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($staff->name); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Portal Email (Username) <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($staff->email); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Staff Code</label>
                            <input type="text" class="form-control font-monospace bg-light" value="<?php echo htmlspecialchars($staff->staff_code); ?>" readonly>
                            <small class="text-muted smaller">Permanent staff identifier</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">System Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="teacher" <?php echo ($staff->role == 'teacher') ? 'selected' : ''; ?>>Teacher / Faculty</option>
                                <option value="admin" <?php echo ($staff->role == 'admin') ? 'selected' : ''; ?>>Administrator / Principal</option>
                                <option value="accountant" <?php echo ($staff->role == 'accountant') ? 'selected' : ''; ?>>Accountant / Cashier</option>
                                <option value="librarian" <?php echo ($staff->role == 'librarian') ? 'selected' : ''; ?>>Librarian</option>
                                <option value="receptionist" <?php echo ($staff->role == 'receptionist') ? 'selected' : ''; ?>>Receptionist / Front Desk</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: EMPLOYMENT & CONTRACT DETAILS -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-success-subtle text-success rounded-3">
                        <i class="fa fa-briefcase"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Employment Contract &amp; Department</h5>
                        <small class="text-muted">Job designation, permanent vs visiting tenure, and salary structure.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Department <span class="text-danger">*</span></label>
                            <select name="department" class="form-select" required>
                                <?php 
                                $depts = ['Academics', 'Science', 'Mathematics', 'English', 'Humanities', 'Administration', 'Finance & Accounts', 'Sports & Physical'];
                                foreach($depts as $d): ?>
                                    <option value="<?php echo $d; ?>" <?php echo ($staff->department == $d) ? 'selected' : ''; ?>><?php echo $d; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Designation <span class="text-danger">*</span></label>
                            <input type="text" name="designation" class="form-control" value="<?php echo htmlspecialchars($staff->designation ?? ''); ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Employment Tenure <span class="text-danger">*</span></label>
                            <select name="employment_type" id="etypeSelect" class="form-select" required onchange="togglePayInputs()">
                                <option value="Permanent" <?php echo ($staff->employment_type == 'Permanent') ? 'selected' : ''; ?>>Permanent / Full-Time</option>
                                <option value="Visiting / Per Lecture" <?php echo ($isVisiting) ? 'selected' : ''; ?>>Visiting / Per Lecture</option>
                                <option value="Contract" <?php echo ($staff->employment_type == 'Contract') ? 'selected' : ''; ?>>Contractual Basis</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="basicSalaryWrap" style="<?php echo $isVisiting ? 'display: none;' : ''; ?>">
                            <label class="form-label small fw-bold text-dark">Basic Monthly Salary (Rs.)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="1000" min="0" name="basic_salary" class="form-control" value="<?php echo htmlspecialchars($staff->basic_salary ?? 0); ?>">
                            </div>
                        </div>
                        <div class="col-md-4" id="lectureRateWrap" style="<?php echo $isVisiting ? '' : 'display: none;'; ?>">
                            <label class="form-label small fw-bold text-dark">Visiting Lecture Rate (Rs.)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="100" min="0" name="lecture_rate" class="form-control" value="<?php echo htmlspecialchars($staff->lecture_rate ?? 0); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Date of Joining</label>
                            <input type="date" name="date_of_joining" class="form-control" value="<?php echo htmlspecialchars($staff->date_of_joining ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: CIVIL RECORDS & CONTACT -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-info-subtle text-info rounded-3">
                        <i class="fa fa-id-card"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Pakistani Civil Identity &amp; Contact</h5>
                        <small class="text-muted">NADRA CNIC, date of birth, mobile phone and home address.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">NADRA CNIC Number</label>
                            <input type="text" name="cnic" class="form-control font-monospace" value="<?php echo htmlspecialchars($staff->cnic ?? ''); ?>" placeholder="35201-XXXXXXX-X">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($staff->dob ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="Male" <?php echo ($staff->gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($staff->gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($staff->gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Primary Mobile Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($staff->phone ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Emergency Contact Person &amp; Phone</label>
                            <input type="text" name="emergency_contact" class="form-control" value="<?php echo htmlspecialchars($staff->emergency_contact ?? ''); ?>">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">Residential Address</label>
                            <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($staff->address ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Side Configuration Column -->
        <div class="col-lg-4">
            <!-- Academic Background & Qualifications -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-graduation-cap text-primary me-2"></i>Academic Qualification
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Highest Degree</label>
                        <input type="text" name="qualification" class="form-control" value="<?php echo htmlspecialchars($staff->qualification ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Teaching Experience</label>
                        <input type="text" name="experience_years" class="form-control" value="<?php echo htmlspecialchars($staff->experience_years ?? ''); ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-dark">Employment Status</label>
                        <select name="status" class="form-select">
                            <option value="Active" <?php echo ($staff->status == 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="On Leave" <?php echo ($staff->status == 'On Leave') ? 'selected' : ''; ?>>On Leave</option>
                            <option value="Resigned" <?php echo ($staff->status == 'Resigned') ? 'selected' : ''; ?>>Resigned</option>
                            <option value="Terminated" <?php echo ($staff->status == 'Terminated') ? 'selected' : ''; ?>>Terminated</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bank Account Particulars -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-building-columns text-success me-2"></i>Bank &amp; Payroll Account
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="<?php echo htmlspecialchars($staff->bank_name ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Account Number / IBAN</label>
                        <input type="text" name="bank_account_no" class="form-control font-monospace" value="<?php echo htmlspecialchars($staff->bank_account_no ?? ''); ?>">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-2">
                        <i class="fa fa-save me-1"></i> Update Staff Profile
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
    function togglePayInputs(){
        var etype = document.getElementById('etypeSelect').value;
        var basicWrap = document.getElementById('basicSalaryWrap');
        var lectureWrap = document.getElementById('lectureRateWrap');

        if(etype === 'Visiting / Per Lecture'){
            lectureWrap.style.display = 'block';
            basicWrap.style.display = 'none';
        } else {
            lectureWrap.style.display = 'none';
            basicWrap.style.display = 'block';
        }
    }
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
