<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/staff/index" class="text-decoration-none text-muted">Staff Directory</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">New Staff Registration</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Register Faculty &amp; Staff Member</h2>
        <p class="text-muted mb-0 small">Enrol a new teacher or staff personnel with system login credentials, employment contract, and civil records.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/staff/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Back to Directory
        </a>
    </div>
</div>

<?php if(isset($_GET['error']) && $_GET['error'] == 'email_exists'): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-exclamation-circle fs-5"></i>
        <div><strong>Registration Error!</strong> A user with this email address already exists. Please use a unique email.</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form action="<?php echo URLROOT; ?>/staff/add" method="post" id="staffCreateForm">
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
                        <small class="text-muted">Staff portal username, system password and assigned security role.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Full Staff Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Prof. Tariq Mehmood" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Portal Email (Login Username) <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. tariq@school.edu.pk" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Initial Password</label>
                            <input type="text" name="password" class="form-control" value="123456" placeholder="Default: 123456">
                            <small class="text-muted smaller">Staff will be prompted to change password upon first login</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">System Access Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="teacher" selected>Teacher / Faculty</option>
                                <option value="admin">Administrator / Principal</option>
                                <option value="accountant">Accountant / Cashier</option>
                                <option value="librarian">Librarian</option>
                                <option value="receptionist">Receptionist / Front Desk</option>
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
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Staff Code</label>
                            <input type="text" name="staff_code" class="form-control font-monospace" placeholder="Auto: TCH-26-001">
                            <small class="text-muted smaller">Leave blank to auto-generate</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Department <span class="text-danger">*</span></label>
                            <select name="department" class="form-select" required>
                                <option value="Academics" selected>Academics</option>
                                <option value="Science">Science (Physics/Chem/Bio)</option>
                                <option value="Mathematics">Mathematics</option>
                                <option value="English">English &amp; Literature</option>
                                <option value="Humanities">Humanities &amp; Social Studies</option>
                                <option value="Administration">General Administration</option>
                                <option value="Finance & Accounts">Finance &amp; Accounts</option>
                                <option value="Sports & Physical">Sports &amp; Physical Training</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Designation <span class="text-danger">*</span></label>
                            <input type="text" name="designation" class="form-control" placeholder="e.g. Senior Subject Specialist" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Employment Tenure <span class="text-danger">*</span></label>
                            <select name="employment_type" id="etypeSelect" class="form-select" required onchange="togglePayInputs()">
                                <option value="Permanent" selected>Permanent / Full-Time</option>
                                <option value="Visiting / Per Lecture">Visiting / Per Lecture</option>
                                <option value="Contract">Contractual Basis</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="basicSalaryWrap">
                            <label class="form-label small fw-bold text-dark">Basic Monthly Salary (Rs.)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="1000" min="0" name="basic_salary" class="form-control" value="40000">
                            </div>
                        </div>
                        <div class="col-md-4" id="lectureRateWrap" style="display: none;">
                            <label class="form-label small fw-bold text-dark">Visiting Lecture Rate (Rs.)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" step="100" min="0" name="lecture_rate" class="form-control" value="1500">
                            </div>
                            <small class="text-muted smaller">Multiplied by timetable weekly periods</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Date of Joining</label>
                            <input type="date" name="date_of_joining" class="form-control" value="<?php echo date('Y-m-d'); ?>">
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
                            <input type="text" name="cnic" class="form-control font-monospace" placeholder="35201-XXXXXXX-X">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Date of Birth</label>
                            <input type="date" name="dob" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="Male" selected>Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Primary Mobile Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="0300-1234567" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Emergency Contact Person &amp; Phone</label>
                            <input type="text" name="emergency_contact" class="form-control" placeholder="Spouse / Father (03XX-XXXXXXX)">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">Residential Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="House #, Street, Area, City..."></textarea>
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
                        <label class="form-label small fw-bold text-dark">Highest Degree / Certification</label>
                        <input type="text" name="qualification" class="form-control" placeholder="e.g. M.Phil Physics (PU)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Total Teaching Experience</label>
                        <input type="text" name="experience_years" class="form-control" placeholder="e.g. 6 Years">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-dark">Initial Status</label>
                        <select name="status" class="form-select">
                            <option value="Active" selected>Active</option>
                            <option value="On Leave">On Leave</option>
                            <option value="Probationary">Probationary</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bank & Disbursement Information -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-building-columns text-success me-2"></i>Bank &amp; Payroll Account
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" placeholder="e.g. Meezan Bank / HBL">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Account Number / IBAN</label>
                        <input type="text" name="bank_account_no" class="form-control font-monospace" placeholder="PK00MEZN0000000000000000">
                    </div>
                    <div class="alert alert-light border small text-muted mb-0">
                        <i class="fa fa-info-circle text-info me-1"></i>
                        Monthly payroll salary slips can be disbursed directly into this registered account.
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-2">
                        <i class="fa fa-check-circle me-1"></i> Register &amp; Create Account
                    </button>
                    <a href="<?php echo URLROOT; ?>/staff/index" class="btn btn-outline-secondary w-100 py-2">
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
