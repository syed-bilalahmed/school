<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="container mt-5">
    <a href="<?php echo URLROOT; ?>/parent/index" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Back</a>
    <div class="row">
        <div class="col-md-12">
            <h2>Exam Results: <?php echo $data['student']->name; ?></h2>
            <p>Class: <?php echo $data['student']->class_name; ?></p>
            
            <?php if(empty($data['results'])): ?>
                <p class="alert alert-warning">No results found.</p>
            <?php else: ?>
                <table class="table table-striped mt-3">
                    <thead>
                        <tr>
                            <th>Exam Name</th>
                            <th>Subject</th>
                            <th>Marks Obtained</th>
                            <th>Full Marks</th>
                            <th>Pass Marks</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['results'] as $res): ?>
                            <tr class="<?php echo ($res->is_absent == 'yes') ? 'table-danger' : ''; ?>">
                                <td><?php echo $res->exam_name; ?></td>
                                <td><?php echo $res->subject_name; ?></td>
                                <td>
                                    <?php 
                                    if($res->is_absent == 'yes'){
                                        echo 'Absent';
                                    } else {
                                        echo $res->get_marks;
                                    }
                                    ?>
                                </td>
                                <td><?php echo $res->full_marks; ?></td>
                                <td><?php echo $res->passing_marks; ?></td>
                                <td>
                                    <?php 
                                    if($res->is_absent == 'yes'){
                                        echo '<span class="badge badge-danger">Absent</span>';
                                    } elseif($res->get_marks >= $res->passing_marks){
                                        echo '<span class="badge badge-success">Pass</span>';
                                    } else {
                                        echo '<span class="badge badge-danger">Fail</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
