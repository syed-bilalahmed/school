<?php require APPROOT . '/Views/layouts/header.php'; ?>

<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/reports/index">Reports Center</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Student Census &amp; Directory</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-user-graduate text-primary me-2"></i>Student Information &amp; Census Report
            </h2>
            <small class="text-muted">Filter and print class rosters, student demographics, and family contact details.</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print();" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-print me-1"></i> Print Report
            </button>
            <a href="<?php echo URLROOT; ?>/reports/index" class="btn btn-primary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Reports Center
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="<?php echo URLROOT; ?>/reports/student" method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Class</label>
                    <select name="class_id" id="classSelect" class="form-select" onchange="filterSections()">
                        <option value="">-- All Classes --</option>
                        <?php foreach($data['classes'] as $class): ?>
                            <option value="<?php echo $class->id; ?>" <?php echo (isset($_GET['class_id']) && $_GET['class_id'] == $class->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($class->class_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Section</label>
                    <select name="section_id" id="sectionSelect" class="form-select">
                        <option value="">-- All Sections --</option>
                        <?php foreach($data['sections'] as $section): ?>
                            <option value="<?php echo $section->id; ?>" data-class="<?php echo $section->class_id; ?>" class="section-option">
                                <?php echo htmlspecialchars($section->section_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Status</label>
                    <select name="status" class="form-select">
                        <option value="Active" <?php echo ($data['status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                        <option value="Clearance" <?php echo ($data['status'] === 'Clearance') ? 'selected' : ''; ?>>Clearance</option>
                        <option value="Withdrawn" <?php echo ($data['status'] === 'Withdrawn') ? 'selected' : ''; ?>>Withdrawn</option>
                        <option value="" <?php echo ($data['status'] === '') ? 'selected' : ''; ?>>All Statuses</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="">-- Any --</option>
                        <option value="Male" <?php echo ($data['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($data['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="search" value="1" class="btn btn-primary w-100">
                        <i class="fa fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0">Student Census Ledger</h5>
            <span class="badge bg-light text-dark border px-3 py-1"><?php echo count($data['students']); ?> Students Found</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Admission #</th>
                            <th>Student Name</th>
                            <th>Class &amp; Section</th>
                            <th>Father Name</th>
                            <th>B-Form / CNIC</th>
                            <th>Gender</th>
                            <th>Contact</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['students'])): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa fa-user-graduate fa-3x mb-3 text-secondary opacity-50"></i>
                                    <div>No student records found for the selected criteria.</div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['students'] as $s): ?>
                                <tr>
                                    <td class="ps-4 font-monospace fw-bold text-primary"><?php echo htmlspecialchars($s->admission_no ?? 'ADM-' . $s->id); ?></td>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($s->name); ?></td>
                                    <td><?php echo htmlspecialchars(($s->class_name ?? '') . ' (' . ($s->section_name ?? 'A') . ')'); ?></td>
                                    <td><?php echo htmlspecialchars($s->father_name ?? '-'); ?></td>
                                    <td class="font-monospace small text-muted"><?php echo htmlspecialchars($s->bform_cnic ?? '-'); ?></td>
                                    <td><?php echo ucfirst($s->gender ?? 'Male'); ?></td>
                                    <td><?php echo htmlspecialchars($s->phone ?? $s->emergency_contact ?? $s->parent_phone ?? $s->mobileno ?? '-'); ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1"><?php echo htmlspecialchars($s->status ?? 'Active'); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function filterSections(){
        var classId = document.getElementById('classSelect').value;
        var options = document.getElementsByClassName('section-option');
        for(var i=0; i<options.length; i++){
            if(!classId || options[i].getAttribute('data-class') == classId){
                options[i].style.display = 'block';
            } else {
                options[i].style.display = 'none';
            }
        }
    }
    if(document.getElementById('classSelect').value) {
        filterSections();
    }
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

