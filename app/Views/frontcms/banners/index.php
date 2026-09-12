<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Add Banner</div>
            <div class="card-body">
                <form action="<?php echo URLROOT; ?>/frontcms/banners" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Title (Optional)</label>
                        <input type="text" name="title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Image (1920x600 recommended)</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link (Optional)</label>
                        <input type="text" name="link" class="form-control" placeholder="http://...">
                    </div>
                     <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Banner</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Banners List</div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach($data['banners'] as $banner): ?>
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-4">
                                     <img src="<?php echo URLROOT . '/' . $banner->image; ?>" class="img-fluid rounded-start h-100" style="object-fit: cover;">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $banner->title; ?></h5>
                                        <p class="card-text small text-muted"><?php echo $banner->description; ?></p>
                                        <p class="card-text mb-1"><small class="text-muted">Link: <?php echo $banner->link; ?></small></p>
                                        <p class="card-text"><small class="text-muted">Order: <?php echo $banner->sort_order; ?></small></p>
                                        <form action="" method="post" class="d-flex justify-content-end">
                                            <input type="hidden" name="delete_id" value="<?php echo $banner->id; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($data['banners'])): ?>
                        <div class="col-12 text-center p-4">No banners added.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
