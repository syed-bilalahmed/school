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
    <!-- Header Title & Quick Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/frontcms/index" class="text-decoration-none text-muted">Front CMS</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">News &amp; Announcements</li>
                </ol>
            </nav>
            <h3 class="h4 fw-bold mb-1 text-dark">
                <i class="fa fa-newspaper text-primary me-2"></i>News, Circulars &amp; Announcements CMS
            </h3>
            <small class="text-muted">Publish official news articles, press releases, and campus announcements with rich formatting and imagery.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/home/news" target="_blank" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fa fa-external-link-alt me-1"></i> View Public News Page
            </a>
        </div>
    </div>

    <!-- Flash Alerts -->
    <?php if(!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Add News Form Column -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-pen-fancy text-primary me-2"></i>Publish New Announcement
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo URLROOT; ?>/frontcms/news" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small mb-1">Article / Announcement Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Annual Sports Gala 2026 Announcement" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small mb-1">Publication Date <span class="text-danger">*</span></label>
                            <input type="date" name="news_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <!-- Rich Text Editor for Announcement Description -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small mb-1">
                                <i class="fa fa-align-left text-primary me-1"></i>Announcement Content (Rich Text Editor) <span class="text-danger">*</span>
                            </label>
                            <textarea name="description" id="newsDescriptionEditor" class="form-control" rows="5"></textarea>
                            <div class="form-text text-muted small">Use headings, bold, bullet points, and links for clean presentation.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small mb-1"><i class="fa fa-image text-danger me-1"></i> Feature Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text text-muted small">JPG, PNG or WebP image (recommended 1200x630).</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                            <i class="fa fa-bullhorn me-1"></i> Publish Announcement
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Latest News List Column -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-list text-primary me-2"></i>Published News &amp; Circulars
                    </h6>
                    <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                        <?php echo count($data['news'] ?? []); ?> Published
                    </span>
                </div>
                <div class="card-body p-3">
                    <?php if(empty($data['news'])): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-newspaper fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                            <h6 class="fw-bold text-dark">No news articles published yet</h6>
                            <p class="small text-muted mb-0">Use the form on the left to write and publish your first announcement.</p>
                        </div>
                    <?php else: ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach($data['news'] as $item): ?>
                                <div class="card border rounded-3 p-3 shadow-xs">
                                    <div class="d-flex gap-3">
                                        <?php if($item->image): ?>
                                            <img src="<?php echo URLROOT . '/' . htmlspecialchars($item->image); ?>" alt="news" width="90" height="90" class="flex-shrink-0 rounded-2" style="object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light border d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 90px; height: 90px;">
                                                <i class="fa fa-newspaper text-muted fa-2x"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex flex-column justify-content-between w-100">
                                            <div>
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($item->title); ?></h6>
                                                    <form action="<?php echo URLROOT; ?>/frontcms/news" method="post" onsubmit="return confirm('Are you sure you want to delete this announcement?');" class="m-0">
                                                        <input type="hidden" name="delete_id" value="<?php echo $item->id; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Delete">
                                                            <i class="fa fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="text-secondary small mb-2" style="max-height: 48px; overflow: hidden; font-size: 0.85rem;">
                                                    <?php echo strip_tags($item->description); ?>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                                <small class="text-muted" style="font-size: 0.78rem;">
                                                    <i class="far fa-calendar-alt text-primary me-1"></i><?php echo date('d M Y', strtotime($item->news_date)); ?>
                                                </small>
                                                <span class="badge bg-success-subtle text-success border px-2 py-0" style="font-size: 0.72rem;">Live on Site</span>
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
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

<!-- Summernote Lite Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    $('#newsDescriptionEditor').summernote({
        placeholder: 'Write the complete announcement or circular text here...',
        tabsize: 2,
        height: 180,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['codeview']]
        ]
    });
});
</script>
