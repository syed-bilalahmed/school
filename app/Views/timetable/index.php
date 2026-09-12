<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa fa-clock text-primary me-2"></i>Class Timetable &amp; Schedule
        </h5>
        <div class="text-muted small">Manage daily class periods and schedule timetable slots</div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/subjects/assign" class="btn btn-outline-primary btn-sm px-2 py-1" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-user-check me-1"></i> Teacher Allocation
        </a>
        <a href="<?php echo URLROOT; ?>/classes/index" class="btn btn-outline-secondary btn-sm px-2 py-1" style="border-radius: 8px; font-size: 0.82rem;">
            <i class="fa fa-chalkboard me-1"></i> Classes
        </a>
    </div>
</div>

<!-- Alert Notifications -->
<?php if(isset($_GET['clash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm py-2 px-3 mb-3 small" style="border-radius: 10px;" role="alert">
        <i class="fa fa-shield-halved me-1 text-danger"></i>
        <strong>Clash Prevented:</strong> <?php echo htmlspecialchars(urldecode($_GET['clash_error']), ENT_QUOTES, 'UTF-8'); ?>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm py-2 px-3 mb-3 small" style="border-radius: 10px;" role="alert">
        <i class="fa fa-check-circle me-1"></i>
        <?php 
            if($_GET['success'] == 'added') echo "Class period scheduled successfully!";
            elseif($_GET['success'] == 'deleted') echo "Period slot removed from schedule.";
            else echo "Schedule updated successfully!";
        ?>
        <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- 4 Compact KPI Cards -->
<?php
$totalScheduled = 0;
if(!empty($data['matrix'])){
    foreach($data['days'] as $d){
        $totalScheduled += count($data['matrix'][$d] ?? []);
    }
}
$totalClassesCount = count($data['classes'] ?? []);
$totalStaffCount = count($data['staff'] ?? []);
$activeDaysCount = count($data['days'] ?? []);
?>
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-calendar-check"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">SCHEDULED SLOTS</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $totalScheduled; ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-chalkboard"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">TOTAL CLASSES</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $totalClassesCount; ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-user-tie"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">TEACHERS</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $totalStaffCount; ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm py-2 px-3 h-100" style="border-radius: 10px;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.95rem;">
                    <i class="fa fa-calendar-week"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small text-truncate" style="font-size: 0.72rem;">WORKING DAYS</div>
                    <h5 class="fw-bold mb-0 text-dark"><?php echo $activeDaysCount; ?> <span class="small fw-normal text-muted" style="font-size: 0.7rem;">days</span></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Class & Section Selector Bar -->
<div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
    <div class="card-body p-2 px-3">
        <form action="" method="get" class="row g-2 align-items-center">
            <div class="col-md-5">
                <select name="class_id" id="classSelect" class="form-select form-select-sm" onchange="filterSections()" required style="border-radius: 6px;">
                    <option value="">-- Select Class --</option>
                    <?php foreach($data['classes'] as $c): ?>
                        <option value="<?php echo $c->id; ?>" <?php echo ($data['selected_class'] == $c->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c->class_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <select name="section_id" id="sectionSelect" class="form-select form-select-sm" required style="border-radius: 6px;">
                    <option value="">-- Select Section --</option>
                    <?php foreach($data['sections'] as $s): ?>
                        <option value="<?php echo $s->id; ?>" data-class="<?php echo $s->class_id; ?>" class="section-option" <?php echo ($data['selected_section'] == $s->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s->section_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm" style="border-radius: 6px;">
                    <i class="fa fa-eye me-1"></i> View Timetable
                </button>
            </div>
        </form>
    </div>
</div>

<?php if($data['selected_class'] && $data['selected_section']): ?>
    <!-- Top Action Bar for Schedule -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold" style="border-radius: 20px; font-size: 0.78rem;">
            <i class="fa fa-calendar-check me-1"></i> Timetable Matrix
        </span>
        <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'super_admin'): ?>
            <button class="btn btn-primary btn-sm fw-bold shadow-sm px-3 py-1" type="button" data-bs-toggle="modal" data-bs-target="#addPeriodModal" style="border-radius: 6px; font-size: 0.82rem;">
                <i class="fa fa-plus-circle me-1"></i> Add Period Slot
            </button>
        <?php endif; ?>
    </div>

    <!-- Weekly Matrix Table Grid -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 text-center align-middle small" style="border-color: #edf2f7;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <?php foreach($data['days'] as $day): ?>
                                <th style="min-width: 140px; padding: 10px 8px; font-weight: 700; color: #1e293b; border-bottom: 2px solid #e2e8f0;">
                                    <?php echo $day; ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach($data['days'] as $day): ?>
                                <td class="align-top p-2" style="background: #fdfdfe; min-height: 220px;">
                                    <?php if(!empty($data['matrix'][$day])): ?>
                                        <?php foreach($data['matrix'][$day] as $slot): ?>
                                            <div class="card mb-1 border shadow-xs text-start" style="border-radius: 8px;">
                                                 <div class="card-body p-2" style="background: #ffffff; border-radius: 7px;">
                                                     <div class="d-flex justify-content-between align-items-start mb-1">
                                                         <span class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;">
                                                             <?php echo htmlspecialchars($slot->subject_name); ?>
                                                         </span>
                                                         <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'super_admin'): ?>
                                                             <form action="" method="post" class="d-inline" onsubmit="return confirm('Remove this period slot?');">
                                                                 <input type="hidden" name="delete_schedule" value="1">
                                                                 <input type="hidden" name="id" value="<?php echo $slot->id; ?>">
                                                                 <input type="hidden" name="class_id" value="<?php echo $data['selected_class']; ?>">
                                                                 <input type="hidden" name="section_id" value="<?php echo $data['selected_section']; ?>">
                                                                 <button type="submit" class="btn btn-xs btn-link text-danger p-0 border-0 ms-1" title="Delete Period">
                                                                     <i class="fa fa-times"></i>
                                                                 </button>
                                                             </form>
                                                         <?php endif; ?>
                                                     </div>
                                                     
                                                     <div class="badge bg-light text-primary border px-1 py-0 mb-1 d-inline-block" style="font-size: 0.72rem; font-weight: 600;">
                                                         <i class="far fa-clock me-1"></i><?php echo date('h:i A', strtotime($slot->time_from)) . ' - ' . date('h:i A', strtotime($slot->time_to)); ?>
                                                     </div>

                                                     <?php if(!empty($slot->staff_name)): ?>
                                                         <div class="text-muted text-truncate" style="font-size: 0.75rem;" title="<?php echo htmlspecialchars($slot->staff_name); ?>">
                                                             <i class="fa fa-user text-secondary me-1"></i><?php echo htmlspecialchars($slot->staff_name); ?>
                                                         </div>
                                                     <?php endif; ?>

                                                     <?php if(!empty($slot->room_no)): ?>
                                                         <div class="mt-1">
                                                             <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.68rem;">
                                                                 Room: <?php echo htmlspecialchars($slot->room_no); ?>
                                                             </span>
                                                         </div>
                                                     <?php endif; ?>
                                                 </div>
                                             </div>
                                         <?php endforeach; ?>
                                     <?php else: ?>
                                         <div class="py-4 text-muted opacity-50 small">
                                             <i class="fa fa-bed d-block mb-1"></i>
                                             No Periods
                                         </div>
                                     <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Period Modal with Live Clash Checking -->
    <div class="modal fade" id="addPeriodModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header bg-white py-3 px-4 border-bottom">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fa fa-calendar-plus text-primary me-2"></i>Schedule Class Period
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post" id="addScheduleForm">
                    <input type="hidden" name="add_schedule" value="1">
                    <input type="hidden" name="class_id" value="<?php echo $data['selected_class']; ?>">
                    <input type="hidden" name="section_id" value="<?php echo $data['selected_section']; ?>">
                    
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Day of Week <span class="text-danger">*</span></label>
                            <select name="day_name" id="modal_day" class="form-select" required style="border-radius: 8px;">
                                <?php foreach($data['days'] as $day): ?>
                                    <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Subject <span class="text-danger">*</span></label>
                            <select name="subject_id" id="modal_subject" class="form-select" required style="border-radius: 8px;">
                                <option value="">Select Subject</option>
                                <?php foreach($data['subjects'] as $sub): ?>
                                    <option value="<?php echo $sub->id; ?>">
                                        <?php echo htmlspecialchars($sub->subject_name); ?> (<?php echo htmlspecialchars($sub->subject_code ?: 'N/A'); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Teacher (Staff) <span class="text-danger">*</span></label>
                            <select name="staff_id" id="modal_staff" class="form-select" required style="border-radius: 8px;">
                                <option value="">Select Teacher</option>
                                <?php foreach($data['staff'] as $st): ?>
                                    <option value="<?php echo $st->id; ?>">
                                        <?php echo htmlspecialchars($st->name); ?> (<?php echo htmlspecialchars($st->email); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Time From <span class="text-danger">*</span></label>
                                <input type="time" name="time_from" id="modal_from" class="form-control" value="09:00" required style="border-radius: 8px;">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Time To <span class="text-danger">*</span></label>
                                <input type="time" name="time_to" id="modal_to" class="form-control" value="09:45" required style="border-radius: 8px;">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted text-uppercase">Room Number / Lab</label>
                            <input type="text" name="room_no" id="modal_room" class="form-control" placeholder="e.g. Room 12, Science Lab A" style="border-radius: 8px;">
                        </div>

                        <div id="liveClashNotice" class="alert alert-warning border-0 small mt-3 d-none" style="border-radius: 8px;">
                            <i class="fa fa-triangle-exclamation me-1"></i> <span id="liveClashText"></span>
                        </div>
                    </div>
                    <div class="modal-footer bg-light px-4 py-3 border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4" style="border-radius: 8px;">
                            <i class="fa fa-save me-1"></i> Save Period
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
function filterSections(){
    var classId = document.getElementById('classSelect').value;
    var options = document.getElementsByClassName('section-option');
    var sectionSelect = document.getElementById('sectionSelect');
    
    var firstMatch = null;
    for(var i=0; i<options.length; i++){
        if(!classId || options[i].getAttribute('data-class') == classId){
            options[i].style.display = 'block';
            if(!firstMatch) firstMatch = options[i].value;
        } else {
            options[i].style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', function(){
    filterSections();
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
