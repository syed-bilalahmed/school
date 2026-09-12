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
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">School Requirements &amp; Careers</li>
                </ol>
            </nav>
            <h3 class="h4 fw-bold mb-1 text-dark">
                <i class="fa fa-briefcase text-primary me-2"></i>School Requirements, Careers &amp; Emergency Notices CMS
            </h3>
            <small class="text-muted">Manage teacher job vacancies, procurement tenders, school equipment needs, and link emergency requirements to the website top menu.</small>
        </div>
        
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Feature Enable / Disable Toggle -->
            <form action="<?php echo URLROOT; ?>/frontcms/requirements" method="post" class="d-inline m-0">
                <input type="hidden" name="toggle_feature_enable" value="1">
                <?php $isReqEnabled = ($data['cms']->enable_requirements ?? 'yes') === 'yes'; ?>
                <button type="submit" class="btn btn-sm <?php echo $isReqEnabled ? 'btn-success text-white' : 'btn-outline-secondary'; ?> fw-semibold px-3 shadow-sm" title="Click to toggle public portal visibility">
                    <i class="fa <?php echo $isReqEnabled ? 'fa-toggle-on' : 'fa-toggle-off'; ?> me-1"></i>
                    Portal: <?php echo $isReqEnabled ? 'Enabled' : 'Disabled'; ?>
                </button>
            </form>

            <button type="button" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" onclick="openAddReqModal()">
                <i class="fa fa-plus-circle me-1"></i> Add New Requirement
            </button>
            
            <a href="<?php echo URLROOT; ?>/home/requirements" target="_blank" class="btn btn-outline-dark btn-sm px-3 fw-semibold">
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
                    <i class="fa fa-list-check text-primary me-2"></i>Published Requirements &amp; Opportunities
                </h6>
                <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                    <?php echo count($data['requirements'] ?? []); ?> Active / Published
                </span>
            </div>
            <div class="small text-muted">
                Public URL: <a href="<?php echo URLROOT; ?>/home/requirements" target="_blank" class="text-primary text-decoration-none fw-semibold"><?php echo URLROOT; ?>/home/requirements</a>
            </div>
        </div>

        <div class="card-body p-0">
            <?php if(empty($data['requirements'])): ?>
                <div class="text-center py-5">
                    <i class="fa fa-briefcase fa-3x text-muted opacity-50 mb-3 d-block"></i>
                    <h6 class="fw-bold text-dark">No Requirements Published Yet</h6>
                    <p class="text-muted small mb-3">Publish teacher job openings, campus procurement tenders, or emergency school needs.</p>
                    <button type="button" class="btn btn-primary btn-sm px-4 fw-bold" onclick="openAddReqModal()">
                        <i class="fa fa-plus-circle me-1"></i> Add First Requirement
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3">Title &amp; Category</th>
                                <th class="py-3">Department</th>
                                <th class="py-3">Deadline</th>
                                <th class="py-3 text-center">Top Menu</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3 text-center">Attachment</th>
                                <th class="pe-3 py-3 text-end" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['requirements'] as $item): 
                                $catBadge = 'bg-primary-subtle text-primary border-primary-subtle';
                                if(stripos($item->category, 'emergency') !== false || stripos($item->category, 'urgent') !== false){
                                    $catBadge = 'bg-danger text-white';
                                } elseif(stripos($item->category, 'tender') !== false || stripos($item->category, 'procurement') !== false){
                                    $catBadge = 'bg-warning text-dark';
                                }
                            ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($item->title); ?></div>
                                        <span class="badge <?php echo $catBadge; ?> px-2 py-0" style="font-size: 0.72rem;">
                                            <?php echo htmlspecialchars($item->category); ?>
                                        </span>
                                        <?php if($item->vacancies > 1): ?>
                                            <span class="badge bg-light text-muted border px-2 py-0 ms-1" style="font-size: 0.72rem;">
                                                <?php echo $item->vacancies; ?> Positions
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark"><?php echo htmlspecialchars($item->department ?: 'General Administration'); ?></div>
                                    </td>
                                    <td>
                                        <?php if(!empty($item->deadline)): ?>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fa fa-clock text-danger me-1"></i><?php echo date('d M Y', strtotime($item->deadline)); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">Open Until Filled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <!-- Quick Toggle Top Menu Display -->
                                        <form action="<?php echo URLROOT; ?>/frontcms/requirements" method="post" class="d-inline m-0">
                                            <input type="hidden" name="toggle_menu_id" value="<?php echo $item->id; ?>">
                                            <button type="submit" class="btn btn-sm border-0 p-0" title="Click to toggle top navigation menu link">
                                                <?php if($item->show_in_menu == 1): ?>
                                                    <span class="badge bg-success px-2 py-1"><i class="fa fa-bars me-1"></i>In Menu</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border px-2 py-1"><i class="fa fa-minus me-1"></i>Not in Menu</span>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <!-- Quick Toggle Status -->
                                        <form action="<?php echo URLROOT; ?>/frontcms/requirements" method="post" class="d-inline m-0">
                                            <input type="hidden" name="toggle_status_id" value="<?php echo $item->id; ?>">
                                            <button type="submit" class="btn btn-sm border-0 p-0" title="Click to toggle Active / Closed status">
                                                <?php if($item->status === 'active'): ?>
                                                    <span class="badge bg-success px-2 py-1"><i class="fa fa-check me-1"></i>Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary px-2 py-1"><i class="fa fa-lock me-1"></i>Closed</span>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <?php if(!empty($item->attachment)): ?>
                                            <a href="<?php echo URLROOT . '/' . htmlspecialchars($item->attachment); ?>" target="_blank" class="btn btn-sm btn-outline-danger px-2 py-0" title="View Attachment Document">
                                                <i class="fa fa-file-pdf"></i> View
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-3 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" onclick='openEditReqModal(<?php echo json_encode($item, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP); ?>)' title="Edit Requirement">
                                            <i class="fa fa-pencil-alt"></i>
                                        </button>
                                        <form action="<?php echo URLROOT; ?>/frontcms/requirements" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this requirement listing?');">
                                            <input type="hidden" name="delete_id" value="<?php echo $item->id; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" title="Delete Requirement">
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

<!-- Modal: Add / Edit School Requirement -->
<div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="reqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="reqModalLabel">
                    <i class="fa fa-briefcase me-2"></i>Add School Requirement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontcms/requirements" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="modalReqId" value="">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold text-dark small mb-1">Title / Requirement Position <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="modalReqTitle" class="form-control" placeholder="e.g. Senior Physics &amp; Mathematics Teacher (O/A Levels)" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark small mb-1">Category <span class="text-danger">*</span></label>
                            <select name="category" id="modalReqCategory" class="form-select" required>
                                <option value="Faculty Career">Faculty / Teaching Career</option>
                                <option value="Administrative Staff">Administrative / Support Staff</option>
                                <option value="Procurement &amp; Tender">Procurement &amp; Tender</option>
                                <option value="Emergency Urgent Requirement">🚨 Emergency / Urgent Requirement</option>
                                <option value="Student Admission Criteria">Student Admission Requirement</option>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold text-dark small mb-1">Department / Wing</label>
                            <input type="text" name="department" id="modalReqDept" class="form-control" placeholder="e.g. Science &amp; STEM Wing">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark small mb-1">Submission Deadline</label>
                            <input type="date" name="deadline" id="modalReqDeadline" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-dark small mb-1">Vacancies / Quantity</label>
                            <input type="number" name="vacancies" id="modalReqVacancies" class="form-control" value="1" min="1">
                        </div>

                        <!-- Rich Text Editor for Description -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small mb-1">
                                <i class="fa fa-align-left text-primary me-1"></i>Detailed Description, Scope &amp; Responsibilities <span class="text-danger">*</span>
                            </label>
                            <textarea name="description" id="modalReqDescription" class="form-control" rows="4"></textarea>
                            <div class="form-text text-muted small">Format responsibilities, role guidelines, and job criteria using the editor toolbar.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small mb-1">
                                <i class="fa fa-certificate text-success me-1"></i>Minimum Eligibility / Qualifications
                            </label>
                            <textarea name="eligibility" id="modalReqEligibility" class="form-control" rows="2" placeholder="e.g. Master's degree in Physics with 3+ years teaching experience in Cambridge curriculum..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small mb-1">Official Document / PDF Form (Max 20MB)</label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png">
                            <div class="form-text text-muted small">Attach official tender notice, job spec, or application form.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small mb-1">Status</label>
                            <select name="status" id="modalReqStatus" class="form-select">
                                <option value="active">Active (Open for Applications / Inquiries)</option>
                                <option value="closed">Closed (Applications Ended)</option>
                            </select>
                        </div>

                        <!-- 1-Click Top Menu Integration -->
                        <div class="col-12 p-3 bg-light rounded-3 border">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="show_in_menu" value="1" id="modalReqShowInMenu" onchange="toggleMenuTitleInput(this)">
                                <label class="form-check-label fw-bold text-dark small" for="modalReqShowInMenu">
                                    <i class="fa fa-bars text-primary me-1"></i>Display in Top Website Navigation Menu (Ideal for Urgent / Emergency Notices)
                                </label>
                            </div>
                            <div id="modalMenuTitleWrapper" style="display: none;">
                                <label class="form-label fw-semibold text-dark small mb-1">Custom Menu Link Title</label>
                                <input type="text" name="menu_title" id="modalReqMenuTitle" class="form-control form-control-sm" placeholder="e.g. Urgent Vacancy / Tenders">
                                <div class="form-text text-muted small">This label will appear directly inside the main website navigation bar.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fa fa-save me-1"></i> Save Requirement
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
    $('#modalReqDescription').summernote({
        placeholder: 'Write job requirements, tender specifications, or emergency criteria here...',
        tabsize: 2,
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen']]
        ]
    });
});

function toggleMenuTitleInput(checkbox) {
    const wrapper = document.getElementById('modalMenuTitleWrapper');
    if (wrapper) {
        wrapper.style.display = checkbox.checked ? 'block' : 'none';
        if (checkbox.checked) {
            const titleInput = document.getElementById('modalReqTitle');
            const menuInput = document.getElementById('modalReqMenuTitle');
            if (menuInput && (!menuInput.value || menuInput.value.trim() === '')) {
                menuInput.value = titleInput ? titleInput.value : '';
            }
        }
    }
}

function openAddReqModal() {
    document.getElementById('modalReqId').value = '';
    document.getElementById('modalReqTitle').value = '';
    document.getElementById('modalReqCategory').value = 'Faculty Career';
    document.getElementById('modalReqDept').value = '';
    document.getElementById('modalReqDeadline').value = '';
    document.getElementById('modalReqVacancies').value = '1';
    document.getElementById('modalReqEligibility').value = '';
    document.getElementById('modalReqStatus').value = 'active';
    document.getElementById('modalReqShowInMenu').checked = false;
    document.getElementById('modalReqMenuTitle').value = '';
    document.getElementById('modalMenuTitleWrapper').style.display = 'none';
    $('#modalReqDescription').summernote('code', '');
    document.getElementById('reqModalLabel').innerHTML = '<i class="fa fa-briefcase me-2"></i>Add School Requirement';

    const modalEl = document.getElementById('reqModal');
    new bootstrap.Modal(modalEl).show();
}

function openEditReqModal(data) {
    document.getElementById('modalReqId').value = data.id || '';
    document.getElementById('modalReqTitle').value = data.title || '';
    document.getElementById('modalReqCategory').value = data.category || 'Faculty Career';
    document.getElementById('modalReqDept').value = data.department || '';
    document.getElementById('modalReqDeadline').value = data.deadline || '';
    document.getElementById('modalReqVacancies').value = data.vacancies || '1';
    document.getElementById('modalReqEligibility').value = data.eligibility || '';
    document.getElementById('modalReqStatus').value = data.status || 'active';
    document.getElementById('modalReqShowInMenu').checked = (data.show_in_menu == 1);
    document.getElementById('modalReqMenuTitle').value = data.menu_title || data.title || '';
    document.getElementById('modalMenuTitleWrapper').style.display = (data.show_in_menu == 1) ? 'block' : 'none';
    $('#modalReqDescription').summernote('code', data.description || '');
    document.getElementById('reqModalLabel').innerHTML = '<i class="fa fa-pencil-alt me-2"></i>Edit Requirement: ' + (data.title || '');

    const modalEl = document.getElementById('reqModal');
    new bootstrap.Modal(modalEl).show();
}
</script>
