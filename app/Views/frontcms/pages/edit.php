<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Summernote Lite -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .cms-container {
        max-width: 100%;
        overflow-x: hidden;
    }
    .note-editor.note-frame {
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px !important;
        overflow: hidden !important;
        max-width: 100% !important;
    }
    .note-toolbar {
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 4px;
        padding: 6px 10px !important;
    }
    .cms-tpl-card {
        transition: all 0.2s ease;
    }
    .cms-tpl-card:hover {
        transform: translateY(-2px);
        background: #f8fafc !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.12);
    }
    .copy-url-btn {
        transition: all 0.15s ease;
    }
    .copy-url-btn:hover {
        background-color: #e2e8f0 !important;
        color: #1e293b !important;
    }
</style>

<div class="cms-container pb-4">
    <div class="row justify-content-center mx-0">
        <div class="col-12 col-xl-10 px-0">
            <div class="card shadow-sm border-0" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">
                            <i class="fa fa-edit text-primary me-2"></i> Edit Page &amp; Navigation Menu Settings
                        </h4>
                        <?php 
                            $pagePublicUrl = in_array($data['page']->slug, ['academics', 'facilities', 'events', 'gallery', 'news', 'fees']) 
                                ? URLROOT . '/home/' . $data['page']->slug 
                                : URLROOT . '/home/page/' . htmlspecialchars($data['page']->slug, ENT_QUOTES, 'UTF-8'); 
                            $displayPath = str_replace(URLROOT, '', $pagePublicUrl);
                        ?>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-light text-dark border">Page ID: #<?php echo $data['page']->id; ?></span>
                            <span class="text-muted small">Public URL:</span>
                            <a href="<?php echo $pagePublicUrl; ?>" target="_blank" class="font-monospace text-primary fw-semibold small text-decoration-none">
                                <?php echo $displayPath; ?> <i class="fa fa-external-link-alt fa-xs"></i>
                            </a>
                            <button type="button" class="btn btn-xs btn-light border py-0 px-2 rounded copy-url-btn text-muted" onclick="copyPublicUrl('<?php echo $pagePublicUrl; ?>', this)" title="Copy Public URL">
                                <i class="fa fa-copy" style="font-size: 0.7rem;"></i> Copy
                            </button>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3 fw-semibold" onclick="openDeviceInspector('<?php echo $pagePublicUrl; ?>', '<?php echo htmlspecialchars(addslashes($data['page']->title), ENT_QUOTES, 'UTF-8'); ?>', '<?php echo $displayPath; ?>')">
                            <i class="fa fa-mobile-screen-button me-1"></i> Multi-Device Preview
                        </button>
                        <a href="<?php echo $pagePublicUrl; ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                            <i class="fa fa-eye me-1"></i> Live Tab
                        </a>
                        <a href="<?php echo URLROOT; ?>/frontcms/pages" class="btn btn-light border btn-sm rounded-pill px-3">
                            <i class="fa fa-arrow-left me-1"></i> Back to Directory
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="<?php echo URLROOT; ?>/frontcms/pages" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $data['page']->id; ?>">
                        <input type="hidden" name="has_menu_sync" value="1">

                        <div class="row g-3">
                            <!-- Page Title -->
                            <div class="col-md-7">
                                <label class="form-label fw-bold small text-dark">Page Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="editPageTitle" class="form-control form-control-lg" value="<?php echo htmlspecialchars($data['page']->title, ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- URL Slug -->
                            <div class="col-md-5">
                                <label class="form-label fw-bold small text-dark">URL Slug</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted font-monospace small">/home/page/</span>
                                    <input type="text" name="slug" class="form-control font-monospace" value="<?php echo htmlspecialchars($data['page']->slug, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Top Navbar Menu Sync Box -->
                        <?php 
                            $isInMenu = !empty($data['page']->menu_id);
                            $menuTitle = !empty($data['page']->menu_title) ? $data['page']->menu_title : $data['page']->title;
                            $menuOrder = !empty($data['page']->menu_order) ? $data['page']->menu_order : 10;
                        ?>
                        <div class="p-3 my-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="add_to_menu" id="editAddToMenuSwitch" value="yes" <?php echo $isInMenu ? 'checked' : ''; ?> onchange="toggleEditMenu(this)">
                                <label class="form-check-label fw-bold text-dark" for="editAddToMenuSwitch">
                                    <i class="fa fa-bars text-primary me-1"></i> Display in Website Top Navigation Bar
                                </label>
                            </div>
                            <div class="small text-muted mb-2">
                                <?php if($isInMenu): ?>
                                    <span class="text-success fw-bold"><i class="fa fa-check-circle"></i> Currently linked in top navbar</span> (Menu Item ID: #<?php echo $data['page']->menu_id; ?>)
                                <?php else: ?>
                                    <span>Not currently displayed in the top navbar menu. Check to add it.</span>
                                <?php endif; ?>
                            </div>

                            <div id="editMenuDiv" class="row g-2 pt-2" style="display: <?php echo $isInMenu ? 'flex' : 'none'; ?>;">
                                <div class="col-sm-4">
                                    <label class="form-label small fw-semibold text-secondary">Menu Display Label</label>
                                    <input type="text" name="menu_title" class="form-control form-control-sm" value="<?php echo htmlspecialchars($menuTitle, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-sm-5">
                                    <label class="form-label small fw-semibold text-secondary">Menu Placement / Dropdown Group</label>
                                    <?php $curGroup = $data['page']->dropdown_group ?? 'none'; ?>
                                    <select name="menu_dropdown_group" id="editMenuDropdownGroupSelect" class="form-select form-select-sm" onchange="toggleEditCustomDropdown(this.value)">
                                        <option value="none" <?php echo ($curGroup === 'none') ? 'selected' : ''; ?>>Top Navigation Bar (Direct Link)</option>
                                        <option value="explore" <?php echo ($curGroup === 'explore') ? 'selected' : ''; ?>>Inside "Explore / Campus Life" Dropdown</option>
                                        <option value="academics" <?php echo ($curGroup === 'academics') ? 'selected' : ''; ?>>Inside "Academics" Dropdown</option>
                                        <option value="about" <?php echo ($curGroup === 'about') ? 'selected' : ''; ?>>Inside "About Us" Dropdown</option>
                                        <option value="custom" <?php echo (!in_array($curGroup, ['none', 'explore', 'academics', 'about'])) ? 'selected' : ''; ?>>+ Custom Dropdown Group</option>
                                    </select>
                                    <div id="editCustomDropdownGroupWrap" class="mt-1" style="display: <?php echo (!in_array($curGroup, ['none', 'explore', 'academics', 'about'])) ? 'block' : 'none'; ?>;">
                                        <input type="text" name="menu_custom_dropdown_group" id="editMenuCustomDropdownInput" class="form-control form-control-sm" value="<?php echo (!in_array($curGroup, ['none', 'explore', 'academics', 'about'])) ? htmlspecialchars($curGroup, ENT_QUOTES, 'UTF-8') : ''; ?>" placeholder="e.g. Student Corner">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label small fw-semibold text-secondary">Menu Sort Order</label>
                                    <input type="number" name="menu_sort_order" class="form-control form-control-sm" value="<?php echo htmlspecialchars((string)$menuOrder, ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>
                        </div>

                        <!-- 1-Click Starter Layout Templates (Interactive Cards) -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-bold small text-dark mb-0">
                                    <i class="fa fa-magic text-warning me-1"></i> Quick Designer Layout Templates
                                </label>
                                <span class="text-muted smaller" style="font-size: 0.75rem;">Click any template to inject styled rich layout into editor</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="insertStarterTemplate('about')" title="Inject About Us Layout">
                                        <div class="fs-4 mb-1">🏫</div>
                                        <div class="fw-bold small text-dark">About Us</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">Mission &amp; Pillars</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="insertStarterTemplate('admission')" title="Inject Admissions Layout">
                                        <div class="fs-4 mb-1">📜</div>
                                        <div class="fw-bold small text-dark">Admissions</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">3-Step Enrollment</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="insertStarterTemplate('facilities')" title="Inject Campus Facilities Layout">
                                        <div class="fs-4 mb-1">🔬</div>
                                        <div class="fw-bold small text-dark">Facilities</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">Labs &amp; Commons</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="insertStarterTemplate('notice')" title="Inject Circular / Alert Layout">
                                        <div class="fs-4 mb-1">📢</div>
                                        <div class="fw-bold small text-dark">Circular/Notice</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">Official Policy</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="insertStarterTemplate('contact')" title="Inject Secretariat Contact Layout">
                                        <div class="fs-4 mb-1">📞</div>
                                        <div class="fw-bold small text-dark">Contact Desk</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">Timings &amp; Grounds</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="insertStarterTemplate('fees')" title="Inject Tuition & Fee Schedule Layout">
                                        <div class="fs-4 mb-1">💳</div>
                                        <div class="fw-bold small text-dark">Fee Schedule</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">Pricing &amp; Rules</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summernote WYSIWYG & HTML Editor -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold small text-dark mb-0">Page Body Content</label>
                                <span class="text-muted smaller" style="font-size: 0.75rem;">
                                    Click <strong>&lt;/&gt; Code View</strong> button in the toolbar to edit raw HTML
                                </span>
                            </div>
                            <textarea name="content" id="editPageContent" class="form-control" rows="15"><?php echo htmlspecialchars($data['page']->content ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <!-- File / Document Attachment -->
                        <div class="mb-3 p-3 rounded-3 bg-light border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-bold small text-dark mb-0">
                                    <i class="fa fa-paperclip text-info me-1"></i> Attached Document / File
                                </label>
                                <span class="badge bg-danger bg-opacity-10 text-danger font-monospace" style="font-size: 0.68rem;">PDF Live Viewer Ready</span>
                            </div>

                            <?php if(!empty($data['page']->file_path)): 
                                $editExt = strtolower(pathinfo($data['page']->file_path, PATHINFO_EXTENSION));
                                $editFileUrl = URLROOT . '/' . ltrim($data['page']->file_path, '/');
                                $editFileName = $data['page']->file_name ?? basename($data['page']->file_path);
                            ?>
                                <div class="p-3 mb-3 bg-white rounded-3 border shadow-sm">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa <?php echo ($editExt === 'pdf' ? 'fa-file-pdf text-danger' : 'fa-file text-primary'); ?> fs-5"></i>
                                            <div>
                                                <strong class="text-dark small">Current File:</strong> 
                                                <span class="fw-semibold text-primary small"><?php echo htmlspecialchars($editFileName, ENT_QUOTES, 'UTF-8'); ?></span>
                                                <span class="badge bg-secondary text-uppercase ms-1 font-monospace" style="font-size: 0.65rem;"><?php echo $editExt; ?></span>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="<?php echo $editFileUrl; ?>" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-3 fw-bold rounded-pill">
                                                <i class="fa fa-external-link-alt me-1"></i> Open In New Tab
                                            </a>
                                            <a href="<?php echo $editFileUrl; ?>" download class="btn btn-sm btn-warning text-dark py-1 px-3 fw-bold rounded-pill">
                                                <i class="fa fa-download me-1"></i> Download
                                            </a>
                                        </div>
                                    </div>

                                    <?php if($editExt === 'pdf'): ?>
                                        <div class="mt-2 border rounded overflow-hidden" style="height: 380px; background: #525659;">
                                            <object data="<?php echo $editFileUrl; ?>#toolbar=1&navpanes=0" type="application/pdf" width="100%" height="100%">
                                                <iframe src="<?php echo $editFileUrl; ?>" width="100%" height="100%" style="border: none;"></iframe>
                                            </object>
                                        </div>
                                    <?php elseif(in_array($editExt, ['jpg', 'jpeg', 'png', 'webp', 'gif'])): ?>
                                        <div class="mt-2 text-center p-2 bg-light rounded border">
                                            <img src="<?php echo $editFileUrl; ?>" alt="Current Attachment" class="img-fluid rounded border" style="max-height: 250px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <label class="form-label small fw-semibold text-secondary mb-1">
                                <?php echo !empty($data['page']->file_path) ? 'Replace Current File (Optional)' : 'Attach Document / File (Optional)'; ?>
                            </label>
                            <input type="file" name="file_attachment" id="editFileInput" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.webp" onchange="handleFilePreview(this, 'edit')">
                            <div class="form-text small text-muted">
                                Supports: <strong>PDF, Word, Excel, JPG, PNG</strong> (Max 10MB). Uploading a file here will attach or replace the page document.
                            </div>

                            <!-- Live Client-side Replacement Preview Box -->
                            <div id="editFilePreviewCard" style="display:none;" class="mt-3 p-2 bg-white rounded-3 border shadow-sm">
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2 text-truncate">
                                        <i id="editFilePreviewIcon" class="fa fa-file-pdf text-danger fs-5"></i>
                                        <div class="text-truncate">
                                            <span class="badge bg-success me-1 font-monospace" style="font-size: 0.65rem;">New File Selected</span>
                                            <span id="editFilePreviewName" class="fw-bold small text-dark d-inline text-truncate"></span>
                                            <span id="editFilePreviewSize" class="badge bg-secondary font-monospace ms-1" style="font-size: 0.65rem;"></span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2 rounded-pill" onclick="clearSelectedFile('edit')">
                                        <i class="fa fa-times me-1"></i> Clear Selection
                                    </button>
                                </div>
                                <div id="editPdfPreviewWrapper" style="height: 320px; display: none;" class="rounded overflow-hidden border">
                                    <iframe id="editPdfPreviewFrame" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                                </div>
                                <div id="editImgPreviewWrapper" style="display: none;" class="text-center p-2">
                                    <img id="editImgPreview" src="" class="img-fluid rounded border" style="max-height: 250px;">
                                </div>
                            </div>
                        </div>

                        <!-- SEO Subtitle / Meta Description -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Short Summary / Subtitle</label>
                            <input type="text" name="meta_description" class="form-control form-control-sm" value="<?php echo htmlspecialchars($data['page']->meta_description ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="1-2 sentences describing this page">
                        </div>

                        <!-- Active Status Switch -->
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="editActiveCheck" value="yes" <?php echo (($data['page']->is_active ?? 'yes') === 'yes') ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold small text-dark" for="editActiveCheck">Page is Live &amp; Active</label>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-4 fw-bold shadow-sm" style="border-radius: 10px;">
                                <i class="fa fa-save me-2"></i> Save &amp; Update Page
                            </button>
                            <a href="<?php echo URLROOT; ?>/frontcms/pages" class="btn btn-light border btn-lg px-4" style="border-radius: 10px;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     INTERACTIVE MULTI-DEVICE LIVE INSPECTOR MODAL
     ============================================================ -->
<div class="modal fade" id="deviceInspectorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header py-2 px-3 bg-black text-white border-bottom border-secondary d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary px-2 py-1"><i class="fa fa-mobile-screen me-1"></i> Live Inspector</span>
                    <h6 class="modal-title fw-bold mb-0 text-white text-truncate" id="inspectorPageTitle">Live Responsive Inspector</h6>
                    <span class="badge bg-secondary font-monospace d-none d-md-inline" id="inspectorPageSlug"></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <!-- Responsive Viewport Switcher -->
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-light active fw-semibold" id="btnDeviceDesktop" onclick="setInspectorDevice('desktop')">
                            <i class="fa fa-desktop me-1"></i> Desktop (100%)
                        </button>
                        <button type="button" class="btn btn-outline-light fw-semibold" id="btnDeviceTablet" onclick="setInspectorDevice('tablet')">
                            <i class="fa fa-tablet-screen-button me-1"></i> Tablet (768px)
                        </button>
                        <button type="button" class="btn btn-outline-light fw-semibold" id="btnDeviceMobile" onclick="setInspectorDevice('mobile')">
                            <i class="fa fa-mobile-screen-button me-1"></i> Mobile (390px)
                        </button>
                    </div>
                    <a href="" target="_blank" id="inspectorExternalLink" class="btn btn-sm btn-outline-info rounded-pill px-3">
                        <i class="fa fa-external-link-alt me-1"></i> Open Tab
                    </a>
                    <button type="button" class="btn-close btn-close-white ms-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 d-flex justify-content-center align-items-center" style="background: #0f172a; overflow: hidden; height: calc(100vh - 55px);">
                <div id="inspectorFrameContainer" style="width: 100%; height: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 20px 50px rgba(0,0,0,0.6); background: white; overflow: hidden;">
                    <iframe id="inspectorIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

<!-- jQuery & Summernote Lite -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
$(document).ready(function() {
    $('#editPageContent').summernote({
        placeholder: 'Edit page content...',
        tabsize: 2,
        height: 380,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
});

function toggleEditMenu(checkbox) {
    const div = document.getElementById('editMenuDiv');
    div.style.display = checkbox.checked ? 'flex' : 'none';
}

const starterTemplates = {
    about: `<section class="py-4">
    <div class="row align-items-center g-4">
        <div class="col-md-6">
            <h2 class="fw-bold mb-3 text-dark">About Our Institution</h2>
            <p class="lead text-secondary">Founded on the principles of academic rigor, integrity, and lifelong curiosity, our academy nurtures the leaders of tomorrow.</p>
            <p>Our comprehensive curriculum bridges theoretical disciplines with experiential inquiry, preparing scholars to thrive in collegiate studies and global society.</p>
        </div>
        <div class="col-md-6">
            <div class="p-4 bg-light rounded-3 border-start border-4 border-primary">
                <h4 class="fw-bold mb-3">Core Educational Pillars</h4>
                <ul class="mb-0">
                    <li><strong>Scholastic Mastery:</strong> Comprehensive sciences, mathematics, and humanities.</li>
                    <li><strong>Character Formation:</strong> Developing empathetic, principled, and responsible citizens.</li>
                    <li><strong>Experiential Discovery:</strong> Hands-on STEM experimentation and arts exploration.</li>
                </ul>
            </div>
        </div>
    </div>
</section>`,

    admission: `<section class="py-4">
    <h2 class="fw-bold mb-3 text-dark">Admissions &amp; Enrollment Guidelines</h2>
    <p class="lead text-muted">We welcome applications from motivated learners seeking a rigorous, holistic educational environment.</p>
    
    <div class="row g-4 my-3">
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3 border h-100">
                <h5 class="fw-bold text-primary">1. Application Submission</h5>
                <p class="small text-muted mb-0">Submit the official admissions application along with the student's previous academic transcripts.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3 border h-100">
                <h5 class="fw-bold text-primary">2. Assessment &amp; Dialogue</h5>
                <p class="small text-muted mb-0">Scholars complete an aptitude assessment followed by a welcoming parent dialogue session.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 bg-light rounded-3 border h-100">
                <h5 class="fw-bold text-primary">3. Formal Enrollment</h5>
                <p class="small text-muted mb-0">Upon acceptance notification, secure placement by fulfilling registration requirements.</p>
            </div>
        </div>
    </div>
</section>`,

    facilities: `<section class="py-4">
    <h2 class="fw-bold mb-3 text-dark">Campus Infrastructure &amp; Learning Spaces</h2>
    <p class="text-muted">Designed to stimulate curiosity, intellectual focus, and creative collaboration.</p>
    
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="p-4 border rounded-3 bg-light">
                <h4 class="fw-bold"><i class="fa fa-microchip text-primary me-2"></i> Modern STEM &amp; Mechatronics Lab</h4>
                <p class="text-secondary mb-0">Equipped with 3D printers, microcontroller kits, and precision scientific instruments for practical research.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 border rounded-3 bg-light">
                <h4 class="fw-bold"><i class="fa fa-book-open text-success me-2"></i> Digital Library &amp; Commons</h4>
                <p class="text-secondary mb-0">Extensive physical collection and international research subscriptions in a peaceful reading haven.</p>
            </div>
        </div>
    </div>
</section>`,

    notice: `<section class="py-4">
    <div class="p-4 rounded-3 bg-light border-start border-5 border-warning shadow-sm">
        <span class="badge bg-warning text-dark mb-2 font-monospace">Official Circular</span>
        <h3 class="fw-bold text-dark mb-2">Important School Notification</h3>
        <p class="text-secondary mb-3">Please review the following directives, guidelines, and upcoming calendar schedules for all enrolled scholars and parents.</p>
        <hr>
        <p class="mb-0"><strong>Note:</strong> For further inquiries or assistance, please contact the campus secretariat office during standard administrative hours.</p>
    </div>
</section>`,

    contact: `<section class="py-4">
    <h2 class="fw-bold mb-3 text-dark">Connect With Campus Secretariat</h2>
    <p class="text-muted">Our admissions and administrative team is available to assist your family.</p>
    
    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="p-4 bg-light rounded-3 border">
                <h5 class="fw-bold mb-3">Office Inquiries</h5>
                <p class="mb-2"><i class="fa fa-clock text-primary me-2"></i> Monday &ndash; Friday: 08:00 AM &ndash; 04:00 PM</p>
                <p class="mb-2"><i class="fa fa-clock text-primary me-2"></i> Saturday: 08:30 AM &ndash; 01:00 PM</p>
                <p class="mb-0"><i class="fa fa-calendar-xmark text-danger me-2"></i> Sunday: Closed</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-4 bg-light rounded-3 border">
                <h5 class="fw-bold mb-3">Campus Address</h5>
                <p class="mb-2"><i class="fa fa-map-location-dot text-primary me-2"></i> Main Executive Campus Grounds</p>
                <p class="mb-0"><i class="fa fa-envelope text-primary me-2"></i> Contact via official school administration portal</p>
            </div>
        </div>
    </div>
</section>`,

    fees: `<section class="py-4">
    <h2 class="fw-bold mb-3 text-dark">Tuition &amp; Fee Policy Guidelines</h2>
    <p class="lead text-muted">Complete financial transparency and predictable investment in your child's education.</p>
    
    <div class="row g-4 my-3">
        <div class="col-md-6 col-lg-3">
            <div class="p-3 bg-light rounded-3 border h-100 text-center">
                <span class="badge bg-primary mb-2">Montessori</span>
                <h5 class="fw-bold">Playgroup &ndash; KG</h5>
                <div class="fs-4 fw-bold text-primary my-2">Rs. 6,500 <small class="text-muted fs-6">/mo</small></div>
                <p class="small text-muted mb-0">Activity-based learning kits and sensory equipment included.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="p-3 bg-light rounded-3 border h-100 text-center">
                <span class="badge bg-success mb-2">Primary Wing</span>
                <h5 class="fw-bold">Grades 1 &ndash; 5</h5>
                <div class="fs-4 fw-bold text-success my-2">Rs. 8,500 <small class="text-muted fs-6">/mo</small></div>
                <p class="small text-muted mb-0">Foundational math, language labs, and sports access.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="p-3 bg-light rounded-3 border h-100 text-center">
                <span class="badge bg-warning text-dark mb-2">Middle Wing</span>
                <h5 class="fw-bold">Grades 6 &ndash; 8</h5>
                <div class="fs-4 fw-bold text-dark my-2">Rs. 10,500 <small class="text-muted fs-6">/mo</small></div>
                <p class="small text-muted mb-0">Computer coding, science laboratories, and library membership.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="p-3 bg-light rounded-3 border h-100 text-center">
                <span class="badge bg-danger mb-2">Senior Wing</span>
                <h5 class="fw-bold">Grades 9 &ndash; 10</h5>
                <div class="fs-4 fw-bold text-danger my-2">Rs. 13,500 <small class="text-muted fs-6">/mo</small></div>
                <p class="small text-muted mb-0">Intensive board preparation, physics & chemistry practicals.</p>
            </div>
        </div>
    </div>
    
    <div class="p-4 rounded-3 border bg-white mt-4">
        <h5 class="fw-bold text-dark mb-2"><i class="fa fa-info-circle text-primary me-2"></i>Sibling Concession Policy</h5>
        <p class="text-muted mb-0">A <strong>10% concession</strong> is granted to the second child, and a <strong>20% concession</strong> to the third child and subsequent siblings enrolled simultaneously.</p>
    </div>
</section>`
};

function insertStarterTemplate(key) {
    if (!starterTemplates[key]) return;
    if (confirm('Replace current editor content with the selected starter template?')) {
        $('#editPageContent').summernote('code', starterTemplates[key]);
    }
}

function handleFilePreview(input, context) {
    const file = input.files && input.files[0];
    const previewCard = document.getElementById(context + 'FilePreviewCard');
    const nameEl = document.getElementById(context + 'FilePreviewName');
    const sizeEl = document.getElementById(context + 'FilePreviewSize');
    const iconEl = document.getElementById(context + 'FilePreviewIcon');
    const pdfWrapper = document.getElementById(context + 'PdfPreviewWrapper');
    const pdfFrame = document.getElementById(context + 'PdfPreviewFrame');
    const imgWrapper = document.getElementById(context + 'ImgPreviewWrapper');
    const imgEl = document.getElementById(context + 'ImgPreview');

    if (!file) {
        if (previewCard) previewCard.style.display = 'none';
        return;
    }

    const fileName = file.name;
    const fileExt = fileName.split('.').pop().toLowerCase();
    const fileSizeKB = (file.size / 1024).toFixed(1);
    const fileSizeStr = fileSizeKB > 1024 ? (fileSizeKB / 1024).toFixed(2) + ' MB' : fileSizeKB + ' KB';

    if (nameEl) nameEl.textContent = fileName;
    if (sizeEl) sizeEl.textContent = fileSizeStr;

    if (fileExt === 'pdf') {
        if (iconEl) iconEl.className = 'fa fa-file-pdf text-danger fs-5';
        const fileUrl = URL.createObjectURL(file);
        if (pdfFrame) pdfFrame.src = fileUrl + '#toolbar=1&navpanes=0';
        if (pdfWrapper) pdfWrapper.style.display = 'block';
        if (imgWrapper) imgWrapper.style.display = 'none';
    } else if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(fileExt)) {
        if (iconEl) iconEl.className = 'fa fa-file-image text-primary fs-5';
        const fileUrl = URL.createObjectURL(file);
        if (imgEl) imgEl.src = fileUrl;
        if (imgWrapper) imgWrapper.style.display = 'block';
        if (pdfWrapper) pdfWrapper.style.display = 'none';
    } else {
        if (iconEl) iconEl.className = 'fa fa-file-lines text-secondary fs-5';
        if (pdfWrapper) pdfWrapper.style.display = 'none';
        if (imgWrapper) imgWrapper.style.display = 'none';
    }

    if (previewCard) previewCard.style.display = 'block';
}

function clearSelectedFile(context) {
    const input = document.getElementById(context + 'FileInput');
    if (input) input.value = '';
    const previewCard = document.getElementById(context + 'FilePreviewCard');
    if (previewCard) previewCard.style.display = 'none';
    const pdfFrame = document.getElementById(context + 'PdfPreviewFrame');
    if (pdfFrame) pdfFrame.src = '';
    const imgEl = document.getElementById(context + 'ImgPreview');
    if (imgEl) imgEl.src = '';
}

function toggleEditCustomDropdown(val) {
    const wrap = document.getElementById('editCustomDropdownGroupWrap');
    if (wrap) {
        wrap.style.display = (val === 'custom') ? 'block' : 'none';
    }
}

// 1-Click Copy Public URL to clipboard with visual feedback
function copyPublicUrl(url, btn) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(function() {
            showCopyFeedback(btn);
        }).catch(function() {
            fallbackCopy(url, btn);
        });
    } else {
        fallbackCopy(url, btn);
    }
}

function fallbackCopy(url, btn) {
    const temp = document.createElement('input');
    temp.value = url;
    document.body.appendChild(temp);
    temp.select();
    document.execCommand('copy');
    document.body.removeChild(temp);
    showCopyFeedback(btn);
}

function showCopyFeedback(btn) {
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-check text-success"></i> Copied!';
    btn.classList.add('bg-success-subtle');
    setTimeout(function() {
        btn.innerHTML = origHtml;
        btn.classList.remove('bg-success-subtle');
    }, 1800);
}

// Multi-Device Live Inspector Modal
function openDeviceInspector(url, title, slug) {
    const titleEl = document.getElementById('inspectorPageTitle');
    const slugEl = document.getElementById('inspectorPageSlug');
    const iframe = document.getElementById('inspectorIframe');
    const extLink = document.getElementById('inspectorExternalLink');

    if (titleEl) titleEl.textContent = title || 'Live Preview';
    if (slugEl) slugEl.textContent = slug || url;
    if (extLink) extLink.href = url;
    if (iframe) iframe.src = url;

    setInspectorDevice('desktop');

    const modalEl = document.getElementById('deviceInspectorModal');
    if (modalEl) {
        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
    }
}

function setInspectorDevice(mode) {
    const container = document.getElementById('inspectorFrameContainer');
    const btnDesktop = document.getElementById('btnDeviceDesktop');
    const btnTablet = document.getElementById('btnDeviceTablet');
    const btnMobile = document.getElementById('btnDeviceMobile');

    [btnDesktop, btnTablet, btnMobile].forEach(b => {
        if (b) {
            b.classList.remove('active', 'btn-light');
            b.classList.add('btn-outline-light');
        }
    });

    if (mode === 'mobile') {
        if (container) {
            container.style.width = '390px';
            container.style.height = '844px';
            container.style.borderRadius = '24px';
            container.style.border = '10px solid #334155';
        }
        if (btnMobile) {
            btnMobile.classList.add('active', 'btn-light');
            btnMobile.classList.remove('btn-outline-light');
        }
    } else if (mode === 'tablet') {
        if (container) {
            container.style.width = '768px';
            container.style.height = '90%';
            container.style.borderRadius = '16px';
            container.style.border = '8px solid #334155';
        }
        if (btnTablet) {
            btnTablet.classList.add('active', 'btn-light');
            btnTablet.classList.remove('btn-outline-light');
        }
    } else {
        if (container) {
            container.style.width = '100%';
            container.style.height = '100%';
            container.style.borderRadius = '0px';
            container.style.border = 'none';
        }
        if (btnDesktop) {
            btnDesktop.classList.add('active', 'btn-light');
            btnDesktop.classList.remove('btn-outline-light');
        }
    }
}
</script>
