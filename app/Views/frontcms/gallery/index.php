<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Summernote Lite (WYSIWYG Rich Editor) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        overflow: hidden !important;
    }
    .note-toolbar {
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
</style>

<div class="container-fluid px-0">
    <!-- Header Title & Quick Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/setting/index?tab=website" class="text-decoration-none text-muted">Website Settings</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Photo Gallery Albums</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-camera-retro text-danger me-2"></i>Photo Gallery Albums &amp; Media CMS
            </h2>
            <small class="text-muted">Manage campus photo albums, event photos, and public media showcases displayed on the public website.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-danger btn-sm px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadGalleryModal">
                <i class="fa fa-plus-circle me-1"></i> Upload New Image
            </button>
            <a href="<?php echo URLROOT; ?>/home/gallery" target="_blank" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fa fa-external-link-alt me-1"></i> Preview Public Gallery
            </a>
        </div>
    </div>

    <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Main Gallery Grid -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-images text-danger me-2"></i>Campus Media Showcase Albums
                    </h6>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fw-bold">
                        <?php echo count($data['gallery'] ?? []); ?> Photos Published
                    </span>
                </div>
                <div class="card-body p-3">
                    <?php if(empty($data['gallery'])): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-images fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                            <h6 class="fw-bold text-dark">No gallery images uploaded yet</h6>
                            <p class="small text-muted mb-3">Upload campus event photos to feature them on your public website.</p>
                            <button type="button" class="btn btn-danger btn-sm px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#uploadGalleryModal">
                                <i class="fa fa-upload me-1"></i> Upload First Photo
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach($data['gallery'] as $img): ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="card h-100 border shadow-xs overflow-hidden rounded-3">
                                        <div class="position-relative bg-dark" style="height: 180px;">
                                            <img src="<?php echo URLROOT . '/' . htmlspecialchars($img->featured_image); ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($img->title); ?>">
                                            <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75 text-white font-monospace small">
                                                ID #<?php echo $img->id; ?>
                                            </span>
                                        </div>
                                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1 text-truncate" title="<?php echo htmlspecialchars($img->title); ?>">
                                                    <?php echo htmlspecialchars($img->title); ?>
                                                </h6>
                                                <p class="small text-muted mb-2 text-truncate" style="font-size: 0.82rem;" title="<?php echo htmlspecialchars($img->description ?? ''); ?>">
                                                    <?php echo htmlspecialchars($img->description ?: 'No detailed caption provided.'); ?>
                                                </p>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top mt-2">
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="fa fa-calendar-alt me-1"></i><?php echo date('d M Y', strtotime($img->created_at ?? date('Y-m-d'))); ?>
                                                </small>
                                                <form action="<?php echo URLROOT; ?>/frontcms/gallery" method="post" onsubmit="return confirm('Are you sure you want to delete this gallery photo?');" class="m-0">
                                                    <input type="hidden" name="delete_id" value="<?php echo $img->id; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-0" title="Delete Photo" style="font-size: 0.78rem;">
                                                        <i class="fa fa-trash-alt me-1"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Add Photo Sidebar Panel -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-cloud-upload-alt text-primary me-2"></i>Quick Photo Upload &amp; Editor
                    </h6>
                </div>
                <div class="card-body p-3">
                    <form action="<?php echo URLROOT; ?>/frontcms/gallery" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-heading text-primary me-1"></i> Image Title / Album Name <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Annual Sports Day 2026" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-align-left text-info me-1"></i> Detailed Caption / Description</label>
                            <textarea name="description" id="gallerySidebarDesc" class="form-control" rows="4" placeholder="Enter detailed image caption, event context, or photographer notes..."></textarea>
                            <div class="form-text text-muted" style="font-size: 0.78rem;">Description will be presented in the public photo lightbox viewer.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-image text-danger me-1"></i> Select Photo File (JPG / PNG / WebP) <span class="text-danger">*</span></label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                            <div class="form-text text-muted" style="font-size: 0.78rem;">High resolution landscape images (Max 5MB) recommended.</div>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 fw-bold shadow-sm py-2">
                            <i class="fa fa-upload me-1"></i> Publish to Public Gallery
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Upload Gallery Photo Popup -->
<div class="modal fade" id="uploadGalleryModal" tabindex="-1" aria-labelledby="uploadGalleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold" id="uploadGalleryModalLabel">
                    <i class="fa fa-camera-retro me-2"></i>Upload Photo to Public Gallery
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontcms/gallery" method="post" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark"><i class="fa fa-heading me-1 text-primary"></i> Photo Title / Album Name <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Science Exhibition 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark"><i class="fa fa-align-left me-1 text-info"></i> Detailed Caption / Text Description</label>
                        <textarea name="description" id="galleryModalDesc" class="form-control" rows="4" placeholder="Write a rich caption describing the event or activity..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark"><i class="fa fa-image me-1 text-danger"></i> Choose Image File <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold">
                        <i class="fa fa-cloud-upload-alt me-1"></i> Upload Photo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

<!-- Summernote Lite Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    $('#gallerySidebarDesc, #galleryModalDesc').summernote({
        placeholder: 'Write photo description, event notes, or rich caption...',
        tabsize: 2,
        height: 140,
        toolbar: [
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']]
        ]
    });
});
</script>

