<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="container mt-5">
    <a href="<?php echo URLROOT; ?>/parent/index" class="btn btn-light mb-3"><i class="fa fa-backward"></i> Back</a>
    <div class="row">
        <div class="col-md-12">
            <h2>Homework: <?php echo $data['student']->name; ?></h2>
            <p class="text-muted">Class: <?php echo $data['student']->class_name . ' (' . $data['student']->section_name . ')'; ?></p>

            <?php if(empty($data['homework'])): ?>
                <div class="alert alert-info">No homework found for this class.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($data['homework'] as $hw): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?php echo $hw->subject_name; ?></h5>
                                    <small class="text-muted"><?php echo date('d M Y', strtotime($hw->homework_date)); ?></small>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">Submission Date: <span class="text-danger"><?php echo date('d M Y', strtotime($hw->submission_date)); ?></span></h6>
                                    <p class="card-text"><?php echo $hw->description; ?></p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <small class="text-muted">Assigned by: Teacher (ID: <?php echo $hw->created_by; ?>)</small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
