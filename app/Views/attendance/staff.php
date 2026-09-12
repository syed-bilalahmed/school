<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <?php if(isset($data['success'])): ?>
                <div class="alert alert-success"><?php echo $data['success']; ?></div>
            <?php endif; ?>

            <!-- Search Area -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Staff Attendance Criteria</div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/attendance/staff" method="post">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select">
                                    <option value="">All Roles</option>
                                    <?php foreach($data['roles'] as $r): ?>
                                        <option value="<?php echo $r; ?>" <?php echo ($data['role'] == $r) ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($r); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" value="<?php echo $data['date']; ?>" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="search" class="btn btn-primary w-100"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Staff List -->
            <?php if(!empty($data['staffs'])): ?>
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Staff List</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="markAll('Present')">Mark All Present</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('Absent')">Mark All Absent</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <form action="<?php echo URLROOT; ?>/attendance/staff" method="post">
                        <input type="hidden" name="role" value="<?php echo $data['role']; ?>">
                        <input type="hidden" name="date" value="<?php echo $data['date']; ?>">
                        <input type="hidden" name="save_attendance" value="1">

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Staff ID</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th width="35%">Attendance</th>
                                        <th>Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data['staffs'] as $staff): ?>
                                        <tr>
                                            <td><?php echo $staff->staff_id; ?></td>
                                            <td><?php echo $staff->name; ?></td>
                                            <td><span class="badge bg-secondary"><?php echo ucfirst($staff->role); ?></span></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php 
                                                        $types = ['Present', 'Late', 'Absent', 'Half Day'];
                                                        foreach($types as $type):
                                                            // Default check Present if not set
                                                            $checked = ($staff->attendance_type == $type) ? 'checked' : (($type == 'Present' && !$staff->attendance_type) ? 'checked' : '');
                                                            $class = ($type == 'Present') ? 'btn-outline-success' : (($type == 'Absent') ? 'btn-outline-danger' : 'btn-outline-warning');
                                                    ?>
                                                        <input type="radio" class="btn-check" name="staff[<?php echo $staff->staff_id; ?>][type]" id="btnradio_<?php echo $staff->staff_id . '_' . $type; ?>" value="<?php echo $type; ?>" <?php echo $checked; ?>>
                                                        <label class="btn btn-sm <?php echo $class; ?>" for="btnradio_<?php echo $staff->staff_id . '_' . $type; ?>"><?php echo $type; ?></label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" name="staff[<?php echo $staff->staff_id; ?>][remark]" class="form-control form-control-sm" value="<?php echo $staff->remark; ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary px-4">Save Attendance</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
<script>
    function markAll(status){
        var radios = document.querySelectorAll('input[type="radio"]');
        radios.forEach(function(radio){
            if(radio.value == status){
                radio.checked = true;
            }
        });
    }
</script>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>

