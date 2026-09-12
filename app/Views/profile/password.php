<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">Change Password</div>
                <div class="card-body">
                    <?php if(isset($data['success'])): ?>
                        <div class="alert alert-success"><?php echo $data['success']; ?></div>
                    <?php endif; ?>
                    <?php if(isset($data['error'])): ?>
                        <div class="alert alert-danger"><?php echo $data['error']; ?></div>
                    <?php endif; ?>

                    <form action="" method="post">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                             <button type="submit" class="btn btn-primary">Change Password</button>
                             <a href="<?php echo URLROOT; ?>/profile/index" class="btn btn-secondary">Back to Profile</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
