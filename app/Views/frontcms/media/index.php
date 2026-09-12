<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span>Media Manager</span>
                <form action="<?php echo URLROOT; ?>/frontcms/media" method="post" enctype="multipart/form-data" class="d-flex">
                    <input type="file" name="file" class="form-control form-control-sm me-2" required>
                    <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                </form>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach($data['media'] as $file): ?>
                    <div class="col-6 col-md-2 text-center">
                        <div class="card h-100">
                             <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center" style="height: 120px; background: #f8f9fa;">
                                <?php if(strpos($file->file_type, 'image') !== false): ?>
                                    <img src="<?php echo URLROOT . '/' . $file->file_path; ?>" style="max-height: 80px; max-width: 100%;">
                                <?php else: ?>
                                    <i class="fa fa-file fa-3x text-secondary"></i>
                                <?php endif; ?>
                             </div>
                             <div class="card-footer bg-white p-2 text-truncate small" title="<?php echo $file->file_name; ?>">
                                 <?php echo $file->file_name; ?>
                             </div>
                             <div class="p-2 border-top">
                                 <button class="btn btn-xs btn-outline-info copy-link" data-url="<?php echo URLROOT . '/' . $file->file_path; ?>"><i class="fa fa-link"></i></button>
                                 <form action="" method="post" class="d-inline">
                                     <input type="hidden" name="delete_id" value="<?php echo $file->id; ?>">
                                     <button type="submit" class="btn btn-xs btn-outline-danger" onclick="return confirm('Delete?')"><i class="fa fa-trash"></i></button>
                                 </form>
                             </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($data['media'])): ?>
                        <div class="col-12 text-center text-muted py-5">No files uploaded.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
<script>
    document.querySelectorAll('.copy-link').forEach(btn => {
        btn.addEventListener('click', function(){
            const url = this.getAttribute('data-url');
            navigator.clipboard.writeText(url).then(() => {
                alert('URL Copied: ' + url);
            });
        });
    });
</script>
