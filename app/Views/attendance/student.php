<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$classId = $data['class_id'];
$sectionId = $data['section_id'];
$date = $data['date'];
$students = $data['students'] ?? [];
$absentees = $data['absentees'] ?? [];

// Calculate initial counts
$countTotal = count($students);
$countPresent = 0;
$countAbsent = 0;
$countLate = 0;
$countHalfDay = 0;

foreach($students as $st){
    $t = $st->attendance_type ?: 'Present';
    if($t === 'Present') $countPresent++;
    elseif($t === 'Absent') $countAbsent++;
    elseif($t === 'Late') $countLate++;
    elseif($t === 'Half Day') $countHalfDay++;
}
$passRate = $countTotal > 0 ? round((($countPresent + $countLate) / $countTotal) * 100, 1) : 0;
?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/attendance/student" class="text-decoration-none text-muted">Attendance</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Daily Gate Register</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Daily Student Attendance Terminal</h2>
        <p class="text-muted mb-0 small">Class roll-call recording, gate entry timestamps, and automated absentee parent notification preview.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <?php if($classId && $sectionId): ?>
            <a href="<?php echo URLROOT; ?>/attendance/monthly?class_id=<?php echo $classId; ?>&section_id=<?php echo $sectionId; ?>" class="btn btn-outline-primary btn-sm px-3">
                <i class="fa fa-calendar-days me-1"></i> Monthly Register Matrix
            </a>
        <?php endif; ?>
        <a href="<?php echo URLROOT; ?>/attendance/staff" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-user-clock me-1"></i> Staff Attendance
        </a>
    </div>
</div>

<!-- Success Alert -->
<?php if(isset($data['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div><strong>Attendance Recorded!</strong> Student attendance and entry logs have been securely saved.</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Criteria Selection Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form action="<?php echo URLROOT; ?>/attendance/student" method="get" class="row g-2 align-items-end">
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">Class / Grade</label>
                <select name="class_id" id="classSelect" class="form-select form-select-sm" required onchange="filterSections()">
                    <option value="">Select Class</option>
                    <?php foreach($data['classes'] as $class): ?>
                        <option value="<?php echo $class->id; ?>" <?php echo ($classId == $class->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class->class_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">Section</label>
                <select name="section_id" id="sectionSelect" class="form-select form-select-sm" required>
                     <option value="">Select Section</option>
                     <?php foreach($data['sections'] as $section): ?>
                         <option value="<?php echo $section->id; ?>" data-class="<?php echo $section->class_id; ?>" class="section-option" <?php echo ($sectionId == $section->id) ? 'selected' : ''; ?>>
                             <?php echo htmlspecialchars($section->section_name); ?>
                         </option>
                     <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label small text-muted mb-1">Attendance Date</label>
                <input type="date" name="date" class="form-control form-control-sm" value="<?php echo $date; ?>" required>
            </div>
            <div class="col-md-3 col-sm-6">
                <button type="submit" name="search" value="1" class="btn btn-primary btn-sm w-100 fw-bold">
                    <i class="fa fa-filter me-1"></i> Load Roll Call
                </button>
            </div>
        </form>
    </div>
</div>

<?php if(!empty($students)): ?>
    <!-- Real-time Attendance Metrics -->
    <div class="row g-3 mb-4">
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Total Enrolled</div>
                <h4 class="fw-bold mb-0 text-dark" id="statTotal"><?php echo $countTotal; ?></h4>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Present</div>
                <h4 class="fw-bold mb-0 text-success" id="statPresent"><?php echo $countPresent; ?></h4>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Absent</div>
                <h4 class="fw-bold mb-0 text-danger" id="statAbsent"><?php echo $countAbsent; ?></h4>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Late Entry</div>
                <h4 class="fw-bold mb-0 text-warning" id="statLate"><?php echo $countLate; ?></h4>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Half Day</div>
                <h4 class="fw-bold mb-0 text-info" id="statHalf"><?php echo $countHalfDay; ?></h4>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-6">
            <div class="card shadow-sm border-0 text-center py-2">
                <div class="small text-muted">Attendance Rate</div>
                <h4 class="fw-bold mb-0 text-primary" id="statRate"><?php echo $passRate; ?>%</h4>
            </div>
        </div>
    </div>

    <!-- Attendance Form Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-0 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-clipboard-user text-primary me-2"></i>Class Roll Call: <?php echo date('d M, Y (l)', strtotime($date)); ?>
                </h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-success btn-sm fw-bold px-3" onclick="batchSetAttendance('Present')">
                    <i class="fa fa-check-double me-1"></i> Mark All Present
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="batchSetAttendance('Absent')">
                    <i class="fa fa-times me-1"></i> Mark All Absent
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <form action="<?php echo URLROOT; ?>/attendance/student" method="post" id="attendanceForm">
                <input type="hidden" name="class_id" value="<?php echo $classId; ?>">
                <input type="hidden" name="section_id" value="<?php echo $sectionId; ?>">
                <input type="hidden" name="date" value="<?php echo $date; ?>">
                <input type="hidden" name="save_attendance" value="1">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 75px;">Roll #</th>
                                <th style="width: 100px;">Adm No</th>
                                <th>Student & Father Details</th>
                                <th style="width: 320px;">Attendance Status</th>
                                <th style="width: 130px;">Entry Time</th>
                                <th class="pe-4" style="width: 200px;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $st): 
                                $curType = $st->attendance_type ?: 'Present';
                                $curTime = !empty($st->entry_time) ? date('H:i', strtotime($st->entry_time)) : '08:00';
                            ?>
                                <tr class="student-row" id="row-<?php echo $st->student_id; ?>">
                                    <td class="ps-4">
                                        <span class="badge bg-primary-subtle text-primary font-monospace px-2 py-1">
                                            <?php echo htmlspecialchars($st->roll_no ?: '-'); ?>
                                        </span>
                                    </td>
                                    <td class="font-monospace text-muted small">
                                        <?php echo htmlspecialchars($st->admission_no ?: 'ADM-' . $st->student_id); ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($st->name); ?></div>
                                        <div class="text-muted small">
                                            S/O: <?php echo htmlspecialchars($st->father_name ?: 'N/A'); ?> &bull;
                                            <i class="fa fa-phone me-1 text-secondary"></i><?php echo htmlspecialchars($st->parent_phone ?: 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <!-- Radio Pill Group -->
                                        <div class="btn-group w-100" role="group">
                                            <!-- Present -->
                                            <input type="radio" class="btn-check att-radio" 
                                                   name="student[<?php echo $st->student_id; ?>][type]" 
                                                   id="btn_p_<?php echo $st->student_id; ?>" 
                                                   value="Present" 
                                                   <?php echo ($curType === 'Present') ? 'checked' : ''; ?>
                                                   onchange="onStatusChanged(<?php echo $st->student_id; ?>, 'Present')">
                                            <label class="btn btn-sm btn-outline-success" for="btn_p_<?php echo $st->student_id; ?>">
                                                <i class="fa fa-check me-1"></i>Present
                                            </label>

                                            <!-- Late -->
                                            <input type="radio" class="btn-check att-radio" 
                                                   name="student[<?php echo $st->student_id; ?>][type]" 
                                                   id="btn_l_<?php echo $st->student_id; ?>" 
                                                   value="Late" 
                                                   <?php echo ($curType === 'Late') ? 'checked' : ''; ?>
                                                   onchange="onStatusChanged(<?php echo $st->student_id; ?>, 'Late')">
                                            <label class="btn btn-sm btn-outline-warning text-dark" for="btn_l_<?php echo $st->student_id; ?>">
                                                <i class="fa fa-clock me-1"></i>Late
                                            </label>

                                            <!-- Absent -->
                                            <input type="radio" class="btn-check att-radio" 
                                                   name="student[<?php echo $st->student_id; ?>][type]" 
                                                   id="btn_a_<?php echo $st->student_id; ?>" 
                                                   value="Absent" 
                                                   <?php echo ($curType === 'Absent') ? 'checked' : ''; ?>
                                                   onchange="onStatusChanged(<?php echo $st->student_id; ?>, 'Absent')">
                                            <label class="btn btn-sm btn-outline-danger" for="btn_a_<?php echo $st->student_id; ?>">
                                                <i class="fa fa-times me-1"></i>Absent
                                            </label>

                                            <!-- Half Day -->
                                            <input type="radio" class="btn-check att-radio" 
                                                   name="student[<?php echo $st->student_id; ?>][type]" 
                                                   id="btn_h_<?php echo $st->student_id; ?>" 
                                                   value="Half Day" 
                                                   <?php echo ($curType === 'Half Day') ? 'checked' : ''; ?>
                                                   onchange="onStatusChanged(<?php echo $st->student_id; ?>, 'Half Day')">
                                            <label class="btn btn-sm btn-outline-info text-dark" for="btn_h_<?php echo $st->student_id; ?>">
                                                <i class="fa fa-adjust me-1"></i>Half
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="time" name="student[<?php echo $st->student_id; ?>][entry_time]" 
                                               class="form-control form-control-sm font-monospace" 
                                               value="<?php echo $curTime; ?>">
                                    </td>
                                    <td class="pe-4">
                                        <input type="text" name="student[<?php echo $st->student_id; ?>][remark]" 
                                               class="form-control form-control-sm" 
                                               placeholder="e.g. Leave note / Sick"
                                               value="<?php echo htmlspecialchars($st->remark ?? ''); ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="p-3 bg-light border-top d-flex align-items-center justify-content-between">
                    <div class="small text-muted">
                        <i class="fa fa-info-circle me-1 text-primary"></i>
                        Attendance status will be updated immediately. Absent students can be notified via SMS/WhatsApp below.
                    </div>
                    <button type="submit" class="btn btn-success px-5 py-2 fw-bold shadow-sm">
                        <i class="fa fa-save me-1"></i> Save Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Module 7 & 8: Absentee Parent Alert Broadcast Box -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-danger">
                <i class="fa fa-comment-sms text-danger me-2"></i>Absentee Parent SMS / WhatsApp Notification Dispatch
            </h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#absenteeCollapse">
                <i class="fa fa-chevron-down"></i> Toggle Dispatch Center
            </button>
        </div>
        <div class="collapse show" id="absenteeCollapse">
            <div class="card-body p-4">
                <?php if(empty($absentees)): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2 mb-0">
                        <i class="fa fa-circle-check fs-5"></i>
                        <div><strong>No Absent Students Recorded!</strong> 100% attendance recorded or roll call has not flagged any absentees yet.</div>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <h6 class="fw-bold text-dark small text-uppercase mb-2">Automated SMS Broadcast Preview</h6>
                            <div class="p-3 bg-light rounded-3 border font-monospace small text-dark mb-3">
                                <strong>Assalam-o-Alaikum!</strong><br>
                                Respected Parent, your child <span class="text-primary fw-bold">[Student Name]</span> (Roll # <span class="text-primary fw-bold">[Roll]</span>), Class <strong><?php echo htmlspecialchars($absentees[0]->class_name . ' - ' . $absentees[0]->section_name); ?></strong> has been marked <span class="text-danger fw-bold">ABSENT</span> today (<?php echo date('d-M-Y', strtotime($date)); ?>) at <?php echo defined('SITENAME') ? SITENAME : 'Excellence Academy'; ?>.<br><br>
                                If this absence is unintended or for medical leave approval, please immediately notify the school administrative office.
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="copyAbsenteePhones()">
                                    <i class="fa fa-copy me-1"></i> Copy Mobile Numbers (<?php echo count($absentees); ?>)
                                </button>
                                <span class="text-muted small">Ready for SMS Gateway / SIM Broadcast</span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <h6 class="fw-bold text-dark small text-uppercase mb-2">Absent Students Parent Roster</h6>
                            <div class="table-responsive border rounded-3" style="max-height: 200px; overflow-y: auto;">
                                <table class="table table-sm table-hover align-middle mb-0 small">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th>Roll</th>
                                            <th>Student Name</th>
                                            <th>Father Name</th>
                                            <th>Mobile</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($absentees as $ab): ?>
                                            <tr>
                                                <td class="font-monospace fw-bold"><?php echo htmlspecialchars($ab->roll_no); ?></td>
                                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($ab->name); ?></td>
                                                <td><?php echo htmlspecialchars($ab->father_name ?: 'N/A'); ?></td>
                                                <td class="font-monospace text-primary parent-mobile"><?php echo htmlspecialchars($ab->parent_phone ?: '0300-0000000'); ?></td>
                                                <td>
                                                    <?php if(!empty($ab->parent_phone)): 
                                                        $waMsg = urlencode("Assalam-o-Alaikum! Respected Parent, your child " . $ab->name . " (Roll #" . $ab->roll_no . ") is marked ABSENT today (" . date('d-M-Y', strtotime($date)) . ") at " . (defined('SITENAME') ? SITENAME : 'the school') . ".");
                                                    ?>
                                                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $ab->parent_phone); ?>?text=<?php echo $waMsg; ?>" target="_blank" class="btn btn-sm btn-outline-success px-2 py-0" title="Send WhatsApp Message">
                                                            <i class="fa-brands fa-whatsapp"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

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

    function batchSetAttendance(status){
        var rows = document.querySelectorAll('.student-row');
        rows.forEach(function(row){
            var radio = row.querySelector('input.att-radio[value="' + status + '"]');
            if(radio){
                radio.checked = true;
            }
        });
        recalculateCounters();
    }

    function onStatusChanged(studentId, status){
        recalculateCounters();
    }

    function recalculateCounters(){
        var total = 0;
        var present = 0;
        var absent = 0;
        var late = 0;
        var half = 0;

        var rows = document.querySelectorAll('.student-row');
        total = rows.length;

        rows.forEach(function(row){
            var checked = row.querySelector('input.att-radio:checked');
            if(checked){
                var val = checked.value;
                if(val === 'Present') present++;
                else if(val === 'Absent') absent++;
                else if(val === 'Late') late++;
                else if(val === 'Half Day') half++;
            }
        });

        if(document.getElementById('statTotal')) document.getElementById('statTotal').innerText = total;
        if(document.getElementById('statPresent')) document.getElementById('statPresent').innerText = present;
        if(document.getElementById('statAbsent')) document.getElementById('statAbsent').innerText = absent;
        if(document.getElementById('statLate')) document.getElementById('statLate').innerText = late;
        if(document.getElementById('statHalf')) document.getElementById('statHalf').innerText = half;

        var rate = total > 0 ? Math.round(((present + late) / total) * 100) : 0;
        if(document.getElementById('statRate')) document.getElementById('statRate').innerText = rate + '%';
    }

    function copyAbsenteePhones(){
        var mobiles = [];
        document.querySelectorAll('.parent-mobile').forEach(function(el){
            var num = el.innerText.trim();
            if(num && num !== '0300-0000000') mobiles.push(num);
        });
        if(mobiles.length > 0){
            navigator.clipboard.writeText(mobiles.join(', '));
            alert('Copied ' + mobiles.length + ' parent mobile numbers to clipboard!');
        } else {
            alert('No mobile numbers found to copy.');
        }
    }

    document.addEventListener('DOMContentLoaded', function(){
        if(document.getElementById('classSelect') && document.getElementById('classSelect').value) {
            filterSections();
        }
        recalculateCounters();
    });
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
