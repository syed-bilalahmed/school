<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa fa-user-check text-primary me-2"></i>Subject Allocation &amp; Faculty Workload
        </h5>
        <div class="text-muted small">Assign teachers to class subjects &amp; monitor weekly teaching periods</div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/timetable/index" class="btn btn-outline-primary btn-sm px-2 py-1" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-clock me-1"></i> Timetable
        </a>
        <a href="<?php echo URLROOT; ?>/subjects/index" class="btn btn-outline-secondary btn-sm px-2 py-1" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-book-open me-1"></i> Subjects Catalog
        </a>
    </div>
</div>

<!-- Alert Notifications -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2 px-3 mb-3 small" style="border-radius: 10px;" role="alert">
        <i class="fa fa-check-circle me-1"></i>
        <?php 
            if($_GET['success'] == 'assigned') echo "Subject allocated to teacher successfully!";
            elseif($_GET['success'] == 'deleted') echo "Course allocation removed successfully!";
            else echo "Action completed successfully!";
        ?>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Executive Workload & Allocation KPIs -->
<?php 
$totalAllocations = count($data['allocations']);
$totalFaculty = count($data['workloads']);
$totalPeriods = 0;
$overloadedTeachers = 0;
foreach($data['workloads'] as $w){
    $periods = (int)$w->total_periods_per_week;
    $totalPeriods += $periods;
    if($periods > 25) $overloadedTeachers++;
}
$avgPeriods = $totalFaculty > 0 ? round($totalPeriods / $totalFaculty, 1) : 0;
?>
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-book-open"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">TOTAL ALLOCATIONS</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $totalAllocations; ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-chalkboard-teacher"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">TEACHING FACULTY</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $totalFaculty; ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-business-time"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">AVG WORKLOAD</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $avgPeriods; ?> <span class="small fw-normal text-muted" style="font-size: 0.7rem;">/wk</span></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-clock"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">TOTAL PERIODS</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $totalPeriods; ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Allocation Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
            <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-dark">
                    <i class="fa fa-plus-circle text-primary me-1"></i> Assign Subject to Teacher
                </span>
            </div>
            <div class="card-body p-3">
                <form action="<?php echo URLROOT; ?>/subjects/store_assign" method="post">
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1">Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="classSelect" class="form-select form-select-sm" required onchange="filterSections()" style="border-radius: 6px;">
                            <option value="">Select Class</option>
                            <?php foreach($data['classes'] as $class): ?>
                                <option value="<?php echo $class->id; ?>" <?php echo (isset($data['selected_class']) && $data['selected_class'] == $class->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class->class_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1">Section <span class="text-danger">*</span></label>
                        <select name="section_id" id="sectionSelect" class="form-select form-select-sm" required style="border-radius: 6px;">
                            <option value="">Select Section</option>
                            <?php foreach($data['sections'] as $sec): ?>
                                <option value="<?php echo $sec->id; ?>" data-class="<?php echo $sec->class_id; ?>" class="section-option">
                                    <?php echo htmlspecialchars($sec->section_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1">Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select form-select-sm" required style="border-radius: 6px;">
                            <option value="">Select Subject</option>
                            <?php foreach($data['subjects'] as $sub): ?>
                                <option value="<?php echo $sub->id; ?>">
                                    <?php echo htmlspecialchars($sub->subject_name); ?> (<?php echo htmlspecialchars($sub->subject_code ?: 'N/A'); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted mb-1">Teacher <span class="text-danger">*</span></label>
                        <select name="teacher_id" class="form-select form-select-sm" required style="border-radius: 6px;">
                            <option value="">Select Teacher</option>
                            <?php foreach($data['teachers'] as $tch): ?>
                                <option value="<?php echo $tch->id; ?>">
                                    <?php echo htmlspecialchars($tch->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">Periods / Week</label>
                        <input type="number" step="1" min="1" max="30" name="periods_per_week" class="form-control form-control-sm" value="5" required style="border-radius: 6px;">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm" style="border-radius: 6px;">
                        <i class="fa fa-save me-1"></i> Save Allocation
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Allocations Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-dark">
                    <i class="fa fa-list-check text-primary me-1"></i> Active Subject Allocations
                </span>
                <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1" style="border-radius: 20px; font-size: 0.72rem;">
                    <?php echo count($data['allocations']); ?> Assigned
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3 py-2">Class / Sec</th>
                                <th class="py-2">Subject</th>
                                <th class="py-2">Teacher</th>
                                <th class="py-2 text-center">Periods</th>
                                <th class="py-2 text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['allocations'])): ?>
                                <?php foreach($data['allocations'] as $alloc): ?>
                                    <tr>
                                        <td class="ps-3 py-2">
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($alloc->class_name); ?></span>
                                            <span class="badge bg-light text-dark border ms-1"><?php echo htmlspecialchars($alloc->section_name); ?></span>
                                        </td>
                                        <td class="py-2">
                                            <span class="fw-bold text-dark"><?php echo htmlspecialchars($alloc->subject_name); ?></span>
                                            <small class="text-muted">(<?php echo htmlspecialchars($alloc->subject_code ?: '-'); ?>)</small>
                                        </td>
                                        <td class="py-2">
                                            <?php if(!empty($alloc->teacher_name)): ?>
                                                <span class="text-dark"><i class="fa fa-user-circle text-primary me-1"></i><?php echo htmlspecialchars($alloc->teacher_name); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning">Unassigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-2 text-center">
                                            <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                                <?php echo (int)($alloc->periods_per_week ?? 5); ?>/wk
                                            </span>
                                        </td>
                                        <td class="py-2 text-end pe-3">
                                            <a href="<?php echo URLROOT; ?>/subjects/delete_assign/<?php echo $alloc->id; ?>" 
                                               class="btn btn-sm btn-outline-danger border-0 p-1"
                                               onclick="return confirm('Remove this course allocation?');"
                                               title="Remove Allocation" style="border-radius: 4px;">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No subject allocations found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Teacher Workload Analytics Table -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-dark">
                    <i class="fa fa-chart-pie text-primary me-1"></i> Faculty Workload Distribution
                </span>
                <span class="badge bg-info-subtle text-info fw-bold px-2 py-1" style="border-radius: 20px; font-size: 0.72rem;">
                    Weekly Summary
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 240px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3 py-2">Teacher</th>
                                <th class="py-2 text-center">Classes</th>
                                <th class="py-2 text-center">Subjects</th>
                                <th class="py-2 text-center">Periods / Wk</th>
                                <th class="py-2 text-end pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['workloads'])): ?>
                                <?php foreach($data['workloads'] as $wk): 
                                    $periods = (int)$wk->total_periods_per_week;
                                    if($periods == 0){
                                        $statusClass = 'bg-secondary-subtle text-secondary';
                                        $statusText = 'None';
                                    } elseif($periods < 15){
                                        $statusClass = 'bg-info-subtle text-info';
                                        $statusText = 'Light';
                                    } elseif($periods <= 25){
                                        $statusClass = 'bg-success-subtle text-success';
                                        $statusText = 'Optimal';
                                    } else {
                                        $statusClass = 'bg-danger-subtle text-danger';
                                        $statusText = 'Heavy';
                                    }
                                ?>
                                    <tr>
                                        <td class="ps-3 py-2">
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($wk->teacher_name); ?></div>
                                        </td>
                                        <td class="py-2 text-center"><span class="badge bg-light text-dark border"><?php echo (int)$wk->total_classes; ?></span></td>
                                        <td class="py-2 text-center"><span class="badge bg-light text-dark border"><?php echo (int)$wk->total_subjects; ?></span></td>
                                        <td class="py-2 text-center">
                                            <span class="fw-bold text-dark"><?php echo $periods; ?></span>
                                        </td>
                                        <td class="py-2 text-end pe-3">
                                            <span class="badge <?php echo $statusClass; ?> px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                                <?php echo $statusText; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">No teacher workload data available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterSections(){
    var classId = document.getElementById('classSelect').value;
    var options = document.getElementsByClassName('section-option');
    var sectionSelect = document.getElementById('sectionSelect');
    
    var hasValid = false;
    for(var i=0; i<options.length; i++){
        if(!classId || options[i].getAttribute('data-class') == classId){
            options[i].style.display = 'block';
            hasValid = true;
        } else {
            options[i].style.display = 'none';
        }
    }
    sectionSelect.value = "";
}

document.addEventListener('DOMContentLoaded', function(){
    filterSections();
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
