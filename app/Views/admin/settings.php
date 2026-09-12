<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-sm border-0 animate-fade-in-up">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon-squircle primary" style="width: 42px; height: 42px; font-size: 1.1rem;">
                            <i class="fa fa-sliders"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">Site Settings</h4>
                            <small class="text-muted">Manage the school details shown on the front page and portal</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo URLROOT; ?>/admin/updateSettings" method="post">
                        <?php foreach($data['settings'] as $key => $val) : ?>
                            <div class="mb-3">
                                <label class="form-label"><?php echo ucwords(str_replace('_', ' ', $key)); ?></label>
                                <?php if($key == 'hero_description'): ?>
                                    <textarea name="<?php echo $key; ?>" rows="4" class="form-control" placeholder="Enter <?php echo strtolower(str_replace('_', ' ', $key)); ?>"><?php echo htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                <?php else: ?>
                                    <input type="text" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Enter <?php echo strtolower(str_replace('_', ' ', $key)); ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
