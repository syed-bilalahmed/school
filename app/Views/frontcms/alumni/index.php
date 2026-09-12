<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Summernote Lite (WYSIWYG Rich Editor) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
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
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Alumni Network</li>
                </ol>
            </nav>
            <h3 class="h4 fw-bold mb-1 text-dark">
                <i class="fa fa-user-graduate text-primary me-2"></i>Alumni Network &amp; Hall of Fame CMS
            </h3>
            <small class="text-muted">Manage distinguished alumni profiles, success stories, graduating batches, and public directory.</small>
        </div>
        
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Feature Enable / Disable Toggle -->
            <form action="<?php echo URLROOT; ?>/frontcms/alumni" method="post" class="d-inline m-0">
                <input type="hidden" name="toggle_feature_enable" value="1">
                <?php $isAlumniEnabled = ($data['cms']->enable_alumni ?? 'yes') === 'yes'; ?>
                <button type="submit" class="btn btn-sm <?php echo $isAlumniEnabled ? 'btn-success text-white' : 'btn-outline-secondary'; ?> fw-semibold px-3 shadow-sm" title="Click to toggle public page visibility">
                    <i class="fa <?php echo $isAlumniEnabled ? 'fa-toggle-on' : 'fa-toggle-off'; ?> me-1"></i>
                    Page: <?php echo $isAlumniEnabled ? 'Enabled' : 'Disabled'; ?>
                </button>
            </form>

            <button type="button" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" onclick="openAddAlumniModal()">
                <i class="fa fa-plus-circle me-1"></i> Add Alumni Profile
            </button>
            
            <a href="<?php echo URLROOT; ?>/home/alumni" target="_blank" class="btn btn-outline-dark btn-sm px-3 fw-semibold">
                <i class="fa fa-external-link-alt me-1"></i> Preview Public Page
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

    <?php if(!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Main Content Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-users text-primary me-2"></i>Registered Alumni Directory
                </h6>
                <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                    <?php echo count($data['alumni'] ?? []); ?> Records
                </span>
            </div>
            <div class="small text-muted">
                Public URL: <a href="<?php echo URLROOT; ?>/home/alumni" target="_blank" class="text-primary text-decoration-none fw-semibold"><?php echo URLROOT; ?>/home/alumni</a>
            </div>
        </div>

        <div class="card-body p-0">
            <?php if(empty($data['alumni'])): ?>
                <div class="text-center py-5">
                    <i class="fa fa-user-graduate fa-3x text-muted opacity-50 mb-3 d-block"></i>
                    <h6 class="fw-bold text-dark">No Alumni Profiles Added Yet</h6>
                    <p class="text-muted small mb-3">Feature your top graduates, professionals, and achievers on the public website.</p>
                    <button type="button" class="btn btn-primary btn-sm px-4 fw-bold" onclick="openAddAlumniModal()">
                        <i class="fa fa-plus-circle me-1"></i> Add First Alumni
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3" style="width: 70px;">Photo</th>
                                <th class="py-3">Alumni Details</th>
                                <th class="py-3">Batch &amp; Degree</th>
                                <th class="py-3">Current Profession</th>
                                <th class="py-3 text-center">Featured</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="pe-3 py-3 text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['alumni'] as $item): ?>
                                <tr>
                                    <td class="ps-3">
                                        <?php if(!empty($item->image)): ?>
                                            <img src="<?php echo URLROOT . '/' . htmlspecialchars($item->image); ?>" alt="<?php echo htmlspecialchars($item->name); ?>" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold border" style="width: 48px; height: 48px; font-size: 1.1rem;">
                                                <?php echo strtoupper(substr($item->name, 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($item->name); ?></div>
                                        <div class="small text-muted">
                                            <?php if(!empty($item->location)): ?>
                                                <i class="fa fa-map-marker-alt text-danger me-1"></i><?php echo htmlspecialchars($item->location); ?>
                                            <?php endif; ?>
                                            <?php if(!empty($item->email)): ?>
                                                &bull; <i class="fa fa-envelope text-primary me-1"></i><?php echo htmlspecialchars($item->email); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-dark px-2 py-1"><?php echo htmlspecialchars($item->batch_year); ?></span>
                                        <div class="small text-muted mt-1"><?php echo htmlspecialchars($item->graduation_class ?: 'Graduate'); ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($item->current_position ?: 'Professional'); ?></div>
                                        <div class="small text-muted"><?php echo htmlspecialchars($item->company_organization ?: ''); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <form action="<?php echo URLROOT; ?>/frontcms/alumni" method="post" class="d-inline m-0">
                                            <input type="hidden" name="toggle_featured_id" value="<?php echo $item->id; ?>">
                                            <button type="submit" class="btn btn-sm border-0 p-0" title="Click to toggle featured spotlight">
                                                <?php if($item->is_featured == 1): ?>
                                                    <span class="badge bg-warning text-dark px-2 py-1"><i class="fa fa-star me-1"></i>Featured</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border px-2 py-1"><i class="far fa-star me-1"></i>Standard</span>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <form action="<?php echo URLROOT; ?>/frontcms/alumni" method="post" class="d-inline m-0">
                                            <input type="hidden" name="toggle_status_id" value="<?php echo $item->id; ?>">
                                            <button type="submit" class="btn btn-sm border-0 p-0" title="Click to toggle active status">
                                                <?php if($item->is_active === 'yes'): ?>
                                                    <span class="badge bg-success px-2 py-1"><i class="fa fa-check me-1"></i>Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary px-2 py-1"><i class="fa fa-eye-slash me-1"></i>Hidden</span>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" onclick='openEditAlumniModal(<?php echo json_encode($item, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>)' title="Edit Profile">
                                            <i class="fa fa-pencil-alt"></i>
                                        </button>
                                        <form action="<?php echo URLROOT; ?>/frontcms/alumni" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this alumni profile?');">
                                            <input type="hidden" name="delete_id" value="<?php echo $item->id; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Delete Profile">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Add / Edit Alumni Profile -->
<div class="modal fade" id="alumniModal" tabindex="-1" aria-labelledby="alumniModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="alumniModalLabel">
                    <i class="fa fa-user-graduate me-2"></i>Add Alumni Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontcms/alumni" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="modalAlumniId" value="">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small mb-1">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="modalAlumniName" class="form-control" placeholder="e.g. Dr. Ayesha Khan" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-dark small mb-1">Batch / Class Of <span class="text-danger">*</span></label>
                            <input type="text" name="batch_year" id="modalAlumniBatch" class="form-control" placeholder="e.g. 2021" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-dark small mb-1">Graduation Degree/Class</label>
                            <input type="text" name="graduation_class" id="modalAlumniClass" class="form-control" placeholder="e.g. Matric / FSc Pre-Med">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small mb-1">Current Role / Designation</label>
                            <input type="text" name="current_position" id="modalAlumniPosition" class="form-control" placeholder="e.g. Senior Software Engineer">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small mb-1">Company / University / Organization</label>
                            <input type="text" name="company_organization" id="modalAlumniCompany" class="form-control" placeholder="e.g. Microsoft / Aga Khan University">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark small mb-1">Current City / Country</label>
                            <input type="text" name="location" id="modalAlumniLocation" class="form-control" placeholder="e.g. Islamabad, Pakistan">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark small mb-1">LinkedIn Profile URL</label>
                            <input type="url" name="linkedin_url" id="modalAlumniLinkedin" class="form-control" placeholder="https://linkedin.com/in/username">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark small mb-1">Email / Contact (Optional)</label>
                            <input type="email" name="email" id="modalAlumniEmail" class="form-control" placeholder="alumni@domain.com">
                        </div>

                        <!-- Rich Text Editor for Testimonial / Story -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small mb-1">
                                <i class="fa fa-quote-left text-warning me-1"></i>Alumni Story, Testimonial or School Memory
                            </label>
                            <textarea name="testimonial" id="modalAlumniTestimonial" class="form-control" rows="4"></textarea>
                            <div class="form-text text-muted small">You can format text with bold, quotes, and bullet points using the editor toolbar.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small mb-1">Profile Photograph</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text text-muted small">Square passport photo or high-res headshot recommended.</div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center gap-4 pt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="modalAlumniFeatured">
                                <label class="form-check-label fw-semibold small text-dark" for="modalAlumniFeatured">
                                    <i class="fa fa-star text-warning me-1"></i>Feature in Spotlight
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="yes" id="modalAlumniActive" checked>
                                <label class="form-check-label fw-semibold small text-dark" for="modalAlumniActive">
                                    <i class="fa fa-check text-success me-1"></i>Publish Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fa fa-save me-1"></i> Save Alumni Profile
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
    $('#modalAlumniTestimonial').summernote({
        placeholder: 'Write the alumni testimonial, career achievements, and school memories here...',
        tabsize: 2,
        height: 160,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']]
        ]
    });
});

function openAddAlumniModal() {
    document.getElementById('modalAlumniId').value = '';
    document.getElementById('modalAlumniName').value = '';
    document.getElementById('modalAlumniBatch').value = '<?php echo date('Y'); ?>';
    document.getElementById('modalAlumniClass').value = '';
    document.getElementById('modalAlumniPosition').value = '';
    document.getElementById('modalAlumniCompany').value = '';
    document.getElementById('modalAlumniLocation').value = '';
    document.getElementById('modalAlumniLinkedin').value = '';
    document.getElementById('modalAlumniEmail').value = '';
    document.getElementById('modalAlumniFeatured').checked = false;
    document.getElementById('modalAlumniActive').checked = true;
    $('#modalAlumniTestimonial').summernote('code', '');
    document.getElementById('alumniModalLabel').innerHTML = '<i class="fa fa-user-graduate me-2"></i>Add Alumni Profile';

    const modalEl = document.getElementById('alumniModal');
    new bootstrap.Modal(modalEl).show();
}

function openEditAlumniModal(data) {
    document.getElementById('modalAlumniId').value = data.id || '';
    document.getElementById('modalAlumniName').value = data.name || '';
    document.getElementById('modalAlumniBatch').value = data.batch_year || '';
    document.getElementById('modalAlumniClass').value = data.graduation_class || '';
    document.getElementById('modalAlumniPosition').value = data.current_position || '';
    document.getElementById('modalAlumniCompany').value = data.company_organization || '';
    document.getElementById('modalAlumniLocation').value = data.location || '';
    document.getElementById('modalAlumniLinkedin').value = data.linkedin_url || '';
    document.getElementById('modalAlumniEmail').value = data.email || '';
    document.getElementById('modalAlumniFeatured').checked = (data.is_featured == 1);
    document.getElementById('modalAlumniActive').checked = (data.is_active === 'yes');
    $('#modalAlumniTestimonial').summernote('code', data.testimonial || '');
    document.getElementById('alumniModalLabel').innerHTML = '<i class="fa fa-pencil-alt me-2"></i>Edit Alumni Profile: ' + (data.name || '');

    const modalEl = document.getElementById('alumniModal');
    new bootstrap.Modal(modalEl).show();
}
</script>
