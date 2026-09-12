<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$student = $data['student'];
?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/students/index" class="text-decoration-none text-muted">Students</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/students/profile/<?php echo $student->id; ?>" class="text-decoration-none text-muted"><?php echo htmlspecialchars($student->name); ?></a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Edit Profile</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Modify Student Profile</h2>
        <p class="text-muted mb-0 small">Update civil records, academic placement, guardian information, and fee concession settings.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $student->id; ?>" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Back to 360° Profile
        </a>
    </div>
</div>

<form action="<?php echo URLROOT; ?>/students/update/<?php echo $student->id; ?>" method="post" id="studentEditForm">
    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            
            <!-- SECTION 1: ACADEMIC PLACEMENT -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary-subtle text-primary rounded-3">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Academic Placement & Enrolment</h5>
                        <small class="text-muted">Academic session, assigned grade, section and roll details.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Academic Session</label>
                            <select name="academic_session_id" class="form-select">
                                <option value="">Select Session</option>
                                <?php foreach($data['sessions'] as $session): ?>
                                    <option value="<?php echo $session->id; ?>" <?php echo ($student->academic_session_id == $session->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($session->session_name); ?> <?php echo $session->is_current ? '(Active)' : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Class / Grade <span class="text-danger">*</span></label>
                            <select name="class_id" id="classSelect" class="form-select" required onchange="filterSections()">
                                <option value="">Select Class</option>
                                <?php foreach($data['classes'] as $class): ?>
                                    <option value="<?php echo $class->id; ?>" <?php echo ($student->class_id == $class->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($class->class_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Section <span class="text-danger">*</span></label>
                            <select name="section_id" id="sectionSelect" class="form-select" required>
                                <option value="">Select Section</option>
                                <?php foreach($data['sections'] as $section): ?>
                                    <option value="<?php echo $section->id; ?>" data-class="<?php echo $section->class_id; ?>" class="section-option" <?php echo ($student->section_id == $section->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($section->section_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Admission No</label>
                            <input type="text" class="form-control font-monospace bg-light" value="<?php echo htmlspecialchars($student->admission_no); ?>" readonly title="Admission number is permanent">
                            <small class="text-muted smaller">Permanent admission identifier</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Class Roll Number</label>
                            <input type="text" name="roll_no" class="form-control font-monospace" value="<?php echo htmlspecialchars($student->roll_no ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Registration / Board #</label>
                            <input type="text" name="reg_no" class="form-control" value="<?php echo htmlspecialchars($student->reg_no ?? ''); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Admission Date</label>
                            <input type="date" name="admission_date" class="form-control" value="<?php echo htmlspecialchars($student->admission_date ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Student Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="Active" <?php echo ($student->status == 'Active') ? 'selected' : ''; ?>>Active / Enrolled</option>
                                <option value="Probationary" <?php echo ($student->status == 'Probationary') ? 'selected' : ''; ?>>Probationary</option>
                                <option value="Left" <?php echo ($student->status == 'Left') ? 'selected' : ''; ?>>Left / SLC Issued</option>
                                <option value="Graduated" <?php echo ($student->status == 'Graduated') ? 'selected' : ''; ?>>Graduated / Alumni</option>
                                <option value="Suspended" <?php echo ($student->status == 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: CIVIL & PERSONAL DETAILS -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-success-subtle text-success rounded-3">
                        <i class="fa fa-user"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Civil Identity & Personal Information</h5>
                        <small class="text-muted">NADRA B-Form, date of birth, blood group and prior academic record.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Full Student Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student->name); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">NADRA B-Form / CNIC</label>
                            <input type="text" name="bform_cnic" class="form-control font-monospace" value="<?php echo htmlspecialchars($student->bform_cnic ?? ''); ?>" placeholder="35201-XXXXXXX-X">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?php echo htmlspecialchars($student->dob ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <option value="Male" <?php echo ($student->gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($student->gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($student->gender == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Blood Group</label>
                            <select name="blood_group" class="form-select">
                                <option value="">Unknown / Select</option>
                                <?php 
                                $bGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
                                foreach($bGroups as $bg): ?>
                                    <option value="<?php echo $bg; ?>" <?php echo ($student->blood_group == $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">Portal Email / Username <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student->email ?? ''); ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">Previous School / Institute Attended</label>
                            <input type="text" name="previous_school" class="form-control" value="<?php echo htmlspecialchars($student->previous_school ?? ''); ?>" placeholder="e.g. Beaconhouse / Army Public School">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: PARENTS & GUARDIAN PROFILE -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-purple-subtle text-purple rounded-3" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.1);">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Parents & Guardian Profile</h5>
                        <small class="text-muted">Father/Mother particulars and emergency contact channels.</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Father's Name</label>
                            <input type="text" name="father_name" class="form-control" value="<?php echo htmlspecialchars($student->father_name ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Father's CNIC</label>
                            <input type="text" name="father_cnic" class="form-control font-monospace" value="<?php echo htmlspecialchars($student->father_cnic ?? ''); ?>" placeholder="35201-XXXXXXX-X">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Mother's Name</label>
                            <input type="text" name="mother_name" class="form-control" value="<?php echo htmlspecialchars($student->mother_name ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Emergency Contact / Mobile Phone</label>
                            <input type="text" name="parent_phone" class="form-control" value="<?php echo htmlspecialchars($student->parent_phone ?? ''); ?>" placeholder="0300-1234567">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control" value="<?php echo htmlspecialchars($student->guardian_name ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Guardian Relationship</label>
                            <select name="guardian_relation" class="form-select">
                                <?php 
                                $relations = ['Father', 'Mother', 'Brother', 'Uncle', 'Grandparent', 'Other'];
                                foreach($relations as $rel): ?>
                                    <option value="<?php echo $rel; ?>" <?php echo ($student->guardian_relation == $rel) ? 'selected' : ''; ?>><?php echo $rel; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">Residential Address</label>
                            <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($student->address ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Side Configuration Column -->
        <div class="col-lg-4">
            <!-- Family Unit Linking Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-link text-primary me-2"></i>Family Unit Clustering
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Linked Family Code grouping enrolled brothers and sisters.
                    </p>
                    <label class="form-label small fw-bold text-dark">Assigned Family Group</label>
                    <select name="family_id" class="form-select mb-3">
                        <option value="">-- No Explicit Family / Auto Detect --</option>
                        <?php foreach($data['families'] as $fam): ?>
                            <option value="<?php echo htmlspecialchars($fam->family_code); ?>" <?php echo ($student->family_id == $fam->family_code) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($fam->family_code . ' - ' . $fam->father_name . ' (' . ($fam->student_count ?? 0) . ' children)'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="alert alert-light border small text-muted mb-0">
                        <i class="fa fa-info-circle text-info me-1"></i>
                        Changing Father CNIC or Family Unit automatically recalculates multi-tier sibling concessions for all related children.
                    </div>
                </div>
            </div>

            <!-- Fee Concession & Policy Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-percent text-success me-2"></i>Fee Policy & Concessions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Concession Category</label>
                        <select name="concession_type" class="form-select">
                            <?php 
                            $cTypes = ['None', 'Sibling Concession', 'Staff Child', 'Merit Scholarship', 'Need-based Relief', 'Hafiz-e-Quran', 'Orphan Support'];
                            foreach($cTypes as $ct): ?>
                                <option value="<?php echo $ct; ?>" <?php echo ($student->concession_type == $ct) ? 'selected' : ''; ?>><?php echo $ct; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Sibling Discount Rate (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.5" min="0" max="100" name="sibling_discount_percent" class="form-control" value="<?php echo htmlspecialchars($student->sibling_discount_percent ?? '0.00'); ?>">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted smaller">Policy tier: 1st: 0%, 2nd: 10%, 3rd+: 20%</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Special Fee Relief (Fixed Rs.)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rs.</span>
                            <input type="number" step="100" min="0" name="custom_discount_amount" class="form-control" value="<?php echo htmlspecialchars($student->custom_discount_amount ?? '0.00'); ?>">
                        </div>
                        <small class="text-muted smaller">Flat monthly fee reduction</small>
                    </div>
                </div>
            </div>

            <!-- Form Submit Actions -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-2">
                        <i class="fa fa-save me-1"></i> Save Changes
                    </button>
                    <a href="<?php echo URLROOT; ?>/students/profile/<?php echo $student->id; ?>" class="btn btn-outline-secondary w-100 py-2">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function filterSections(){
        var classId = document.getElementById('classSelect').value;
        var options = document.getElementsByClassName('section-option');
        
        for(var i=0; i<options.length; i++){
            if(!classId || options[i].getAttribute('data-class') == classId){
                options[i].style.display = '';
            } else {
                options[i].style.display = 'none';
            }
        }
    }
    // Filter sections on page load
    document.addEventListener('DOMContentLoaded', function(){
        filterSections();
    });
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
