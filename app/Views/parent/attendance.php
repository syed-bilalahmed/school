<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="container mt-5">
    <a href="<?php echo URLROOT; ?>/parent/index" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Back</a>
    <div class="row">
        <div class="col-md-12">
            <h2>Attendance Record: <?php echo $data['student']->name; ?></h2>
            
            <?php if(empty($data['attendance'])): ?>
                <div class="alert alert-info">No attendance records found.</div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['attendance'] as $att): ?>
                                    <tr>
                                        <td><?php echo date('d M Y', strtotime($att->date)); ?></td>
                                        <td>
                                            <?php 
                                            if($att->attendance_type == 'present'){
                                                echo '<span class="badge bg-success">Present</span>';
                                            } elseif($att->attendance_type == 'absent'){
                                                echo '<span class="badge bg-danger">Absent</span>';
                                            } elseif($att->attendance_type == 'late'){
                                                echo '<span class="badge bg-warning text-dark">Late</span>';
                                            } elseif($att->attendance_type == 'half_day'){
                                                echo '<span class="badge bg-info text-dark">Half Day</span>';
                                            } else {
                                                echo $att->attendance_type;
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $att->remark; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
