<?php require APPROOT . '/Views/layouts/header.php'; ?>

<!-- Summernote Lite (WYSIWYG & HTML Code Editor) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    /* Prevent any horizontal overflow */
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
    .badge-menu-active {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .badge-menu-inactive {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
    .cms-tab-btn {
        font-weight: 600;
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 0.9rem;
    }
    .cms-stat-card {
        border-radius: 14px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .cms-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
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
    .filter-pill-btn.active {
        background-color: #1e40af !important;
        color: white !important;
        border-color: #1e40af !important;
    }
</style>

<?php
    $totalPages = count($data['pages'] ?? []);
    $topMenuCount = 0;
    $dropdownCount = 0;
    $hasPdfCount = 0;
    if(!empty($data['pages'])){
        foreach($data['pages'] as $pItem){
            if(!empty($pItem->menu_id)){
                if(($pItem->dropdown_group ?? 'none') !== 'none'){
                    $dropdownCount++;
                } else {
                    $topMenuCount++;
                }
            }
            if(!empty($pItem->file_path)){
                $hasPdfCount++;
            }
        }
    }
?>

<div class="cms-container pb-4">
    <!-- FLASH ALERTS -->
    <?php if(!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="fa fa-check-circle me-2"></i> <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if(!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> <?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- TOP HEADER -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 bg-white p-3 rounded-3 shadow-sm border">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fa fa-compass text-primary me-2"></i> Front CMS: Pages &amp; Navigation Menus
            </h4>
            <div class="text-muted small">
                Manage website pages, top navbar links, rich editorial text, and downloadable documents in one simple place.
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="switchToCreateTab()">
                <i class="fa fa-plus-circle me-1"></i> Create New Page &amp; Menu
            </button>
            <a href="<?php echo URLROOT; ?>" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                <i class="fa fa-external-link-alt me-1"></i> Preview Live Website
            </a>
            <a href="<?php echo URLROOT; ?>/setting/index?tab=website" class="btn btn-light border btn-sm rounded-pill px-3 fw-semibold">
                <i class="fa fa-sliders text-primary me-1"></i> Site Website Settings
            </a>
        </div>
    </div>

    <!-- 4 LUXURY KPI METRIC CARDS -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: Total Pages -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 cms-stat-card bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-xs text-uppercase fw-bold">Total Web Pages</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 38px; height: 38px;">
                        <i class="fa fa-file-lines"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-dark mb-0"><?php echo $totalPages; ?></div>
                <div class="small text-muted mt-1"><i class="fa fa-circle-check text-success me-1"></i> Published &amp; Active</div>
            </div>
        </div>

        <!-- KPI 2: Top Navbar Links -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 cms-stat-card bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-xs text-uppercase fw-bold">Top Navbar Direct</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 38px; height: 38px;">
                        <i class="fa fa-bars"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-dark mb-0"><?php echo $topMenuCount; ?></div>
                <div class="small text-muted mt-1"><i class="fa fa-arrow-up-right-from-square text-primary me-1"></i> Direct Header Links</div>
            </div>
        </div>

        <!-- KPI 3: In Dropdown Groups -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 cms-stat-card bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-xs text-uppercase fw-bold">In Dropdown Groups</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-indigo bg-opacity-10 text-indigo" style="width: 38px; height: 38px; background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                        <i class="fa fa-layer-group"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-dark mb-0"><?php echo $dropdownCount; ?></div>
                <div class="small text-muted mt-1"><i class="fa fa-caret-down text-indigo me-1"></i> Explore &amp; Sub-menus</div>
            </div>
        </div>

        <!-- KPI 4: Attached PDF / Media -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 cms-stat-card bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted fs-xs text-uppercase fw-bold">PDF Resources</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger" style="width: 38px; height: 38px;">
                        <i class="fa fa-file-pdf"></i>
                    </div>
                </div>
                <div class="fs-4 fw-bold text-dark mb-0"><?php echo $hasPdfCount; ?></div>
                <div class="small text-muted mt-1"><i class="fa fa-eye text-danger me-1"></i> Interactive Reader Ready</div>
            </div>
        </div>
    </div>

    <!-- CLEAN TABS -->
    <ul class="nav nav-pills gap-2 mb-3" id="cmsMainTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link cms-tab-btn active" id="tab-list-btn" data-bs-toggle="pill" data-bs-target="#tab-list-pane" type="button" role="tab">
                <i class="fa fa-list-check me-1"></i> All Pages &amp; Menus (<?php echo $totalPages; ?>)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link cms-tab-btn" id="tab-create-btn" data-bs-toggle="pill" data-bs-target="#tab-create-pane" type="button" role="tab">
                <i class="fa fa-file-circle-plus text-success me-1"></i> Create New Page &amp; Menu
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link cms-tab-btn" id="tab-customlink-btn" data-bs-toggle="pill" data-bs-target="#tab-customlink-pane" type="button" role="tab">
                <i class="fa fa-link text-warning me-1"></i> Custom External Links (<?php echo count($data['customLinks'] ?? []); ?>)
            </button>
        </li>
    </ul>

    <!-- TAB CONTENTS -->
    <div class="tab-content" id="cmsMainTabsContent">

        <!-- ============================================================
             TAB 1: ALL PAGES & MENUS (FULL-WIDTH CLEAN TABLE)
             ============================================================ -->
        <div class="tab-pane fade show active" id="tab-list-pane" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-3">
                <!-- Search & Quick Filter Toolbar -->
                <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom">
                    <div class="row g-3 align-items-center justify-content-between">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-search"></i></span>
                                <input type="text" id="pageSearchInput" class="form-control border-start-0 ps-0 bg-light" placeholder="Instant search by page title or slug..." oninput="filterPagesTable()">
                            </div>
                        </div>
                        <div class="col-md-7 text-md-end d-flex flex-wrap align-items-center justify-content-md-end gap-1">
                            <span class="text-muted small me-1 d-none d-lg-inline">Filter:</span>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary active fw-bold filter-pill-btn" onclick="setPageTableFilter('all', this)">All (<?php echo $totalPages; ?>)</button>
                                <button type="button" class="btn btn-outline-secondary filter-pill-btn" onclick="setPageTableFilter('top', this)">Top Menu (<?php echo $topMenuCount; ?>)</button>
                                <button type="button" class="btn btn-outline-secondary filter-pill-btn" onclick="setPageTableFilter('dropdown', this)">In Dropdown (<?php echo $dropdownCount; ?>)</button>
                                <button type="button" class="btn btn-outline-secondary filter-pill-btn" onclick="setPageTableFilter('pdf', this)">With PDF (<?php echo $hasPdfCount; ?>)</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="pagesTable">
                        <thead class="table-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-3 ps-md-4">Page Title &amp; Public Link</th>
                                <th>Top Navbar Menu Status</th>
                                <th>Attached File</th>
                                <th>Status</th>
                                <th class="text-end pe-3 pe-md-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['pages'])): ?>
                                <?php foreach($data['pages'] as $p): 
                                    $hasMenu = !empty($p->menu_id);
                                    $pGroup = $p->dropdown_group ?? 'none';
                                    $pagePublicUrl = in_array($p->slug, ['academics', 'facilities', 'events', 'gallery', 'news', 'fees']) 
                                        ? URLROOT . '/home/' . $p->slug 
                                        : URLROOT . '/home/page/' . htmlspecialchars($p->slug, ENT_QUOTES, 'UTF-8'); 
                                    $displayPath = str_replace(URLROOT, '', $pagePublicUrl);
                                    $ext = !empty($p->file_path) ? strtolower(pathinfo($p->file_path, PATHINFO_EXTENSION)) : '';
                                ?>
                                    <tr class="page-table-row" 
                                        data-title="<?php echo strtolower(htmlspecialchars($p->title, ENT_QUOTES, 'UTF-8')); ?>" 
                                        data-slug="<?php echo strtolower(htmlspecialchars($p->slug, ENT_QUOTES, 'UTF-8')); ?>" 
                                        data-menu="<?php echo $hasMenu ? ($pGroup !== 'none' ? 'dropdown' : 'top') : 'none'; ?>" 
                                        data-pdf="<?php echo ($ext === 'pdf') ? '1' : '0'; ?>" 
                                        data-active="<?php echo ($p->is_active ?? 'yes') === 'yes' ? '1' : '0'; ?>">
                                        <td class="ps-3 ps-md-4">
                                            <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($p->title, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                <a href="<?php echo $pagePublicUrl; ?>" target="_blank" class="small font-monospace text-primary text-decoration-none">
                                                    <?php echo $displayPath; ?> <i class="fa fa-external-link-alt fa-xs"></i>
                                                </a>
                                                <button type="button" class="btn btn-xs btn-light border py-0 px-1 rounded copy-url-btn text-muted" onclick="copyPublicUrl('<?php echo $pagePublicUrl; ?>', this)" title="Copy Link URL">
                                                    <i class="fa fa-copy" style="font-size: 0.7rem;"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($hasMenu): ?>
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <?php if($pGroup !== 'none'): ?>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 fw-bold py-1 px-2" title="Order #<?php echo $p->menu_order; ?>">
                                                            <i class="fa fa-layer-group me-1"></i> <?php echo ucfirst($pGroup); ?> Dropdown (#<?php echo $p->menu_order; ?>)
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge badge-menu-active fw-bold py-1 px-2" title="Order #<?php echo $p->menu_order; ?>">
                                                            <i class="fa fa-check-circle me-1"></i> Top Menu (#<?php echo $p->menu_order; ?>)
                                                        </span>
                                                    <?php endif; ?>
                                                    <form action="<?php echo URLROOT; ?>/frontcms/pages" method="post" class="d-inline">
                                                        <input type="hidden" name="quick_toggle_menu_page_id" value="<?php echo $p->id; ?>">
                                                        <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-1" title="Remove from Top Navbar Menu" style="font-size: 0.72rem;">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <form action="<?php echo URLROOT; ?>/frontcms/pages" method="post" class="d-inline">
                                                    <input type="hidden" name="quick_toggle_menu_page_id" value="<?php echo $p->id; ?>">
                                                    <button type="submit" class="btn btn-xs btn-outline-primary py-1 px-2 rounded-pill fw-bold" style="font-size: 0.75rem;" title="Show this page in top website navbar">
                                                        <i class="fa fa-plus me-1"></i> Add to Menu
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($p->file_path)): ?>
                                                <?php if($ext === 'pdf'): ?>
                                                    <div class="d-inline-flex align-items-center gap-1">
                                                        <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2 rounded-pill fw-bold" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#pdfModal_<?php echo $p->id; ?>" title="Instant PDF Preview">
                                                            <i class="fa fa-file-pdf me-1"></i> Preview PDF
                                                        </button>
                                                        <a href="<?php echo URLROOT . '/' . ltrim($p->file_path, '/'); ?>" download class="btn btn-xs btn-light border py-1 px-2 rounded-pill text-muted" title="Download Document">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <a href="<?php echo URLROOT . '/' . ltrim($p->file_path, '/'); ?>" target="_blank" class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 text-decoration-none py-1 px-2">
                                                        <i class="fa fa-paperclip me-1"></i> <?php echo strtoupper($ext); ?> File
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">&mdash;</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(($p->is_active ?? 'yes') === 'yes'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success fw-bold">Live</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3 pe-md-4">
                                            <div class="d-inline-flex gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-info py-1 px-2" onclick="openDeviceInspector('<?php echo $pagePublicUrl; ?>', '<?php echo htmlspecialchars(addslashes($p->title), ENT_QUOTES, 'UTF-8'); ?>', '<?php echo $displayPath; ?>')" title="Interactive Device Inspector">
                                                    <i class="fa fa-mobile-screen-button"></i>
                                                </button>
                                                <a href="<?php echo $pagePublicUrl; ?>" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Preview in New Tab">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/frontcms/pages/edit/<?php echo $p->id; ?>" class="btn btn-sm btn-primary py-1 px-2 fw-bold" title="Edit Page &amp; Menu">
                                                    <i class="fa fa-edit me-1"></i> Edit
                                                </a>
                                                <form action="<?php echo URLROOT; ?>/frontcms/pages" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this page and its navigation link?');">
                                                    <input type="hidden" name="delete_page_id" value="<?php echo $p->id; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- PDF Preview Modal for Page #<?php echo $p->id; ?> -->
                                    <?php if(!empty($p->file_path) && strtolower(pathinfo($p->file_path, PATHINFO_EXTENSION)) === 'pdf'): ?>
                                    <div class="modal fade" id="pdfModal_<?php echo $p->id; ?>" tabindex="-1" aria-labelledby="pdfModalLabel_<?php echo $p->id; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                                                <div class="modal-header bg-dark text-white py-3 px-4">
                                                    <h5 class="modal-title fs-6 fw-bold mb-0 text-truncate me-2" id="pdfModalLabel_<?php echo $p->id; ?>">
                                                        <i class="fa fa-file-pdf text-danger me-2"></i> <?php echo htmlspecialchars($p->file_name ?? $p->title, ENT_QUOTES, 'UTF-8'); ?>
                                                    </h5>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="<?php echo URLROOT . '/' . ltrim($p->file_path, '/'); ?>" download class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3">
                                                            <i class="fa fa-download me-1"></i> Download
                                                        </a>
                                                        <a href="<?php echo URLROOT . '/' . ltrim($p->file_path, '/'); ?>" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3">
                                                            <i class="fa fa-external-link-alt me-1"></i> Full Window
                                                        </a>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                </div>
                                                <div class="modal-body p-0" style="height: 75vh; background: #525659;">
                                                    <object data="<?php echo URLROOT . '/' . ltrim($p->file_path, '/'); ?>#toolbar=1&navpanes=0" type="application/pdf" width="100%" height="100%">
                                                        <iframe src="<?php echo URLROOT . '/' . ltrim($p->file_path, '/'); ?>" width="100%" height="100%" style="border: none;"></iframe>
                                                    </object>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa fa-file-circle-question fa-3x text-muted opacity-50 mb-3 d-block"></i>
                                        <p class="fs-6 mb-2">No custom web pages found.</p>
                                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" onclick="switchToCreateTab()">
                                            <i class="fa fa-plus me-1"></i> Create Your First Page
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================
             TAB 2: CREATE NEW PAGE & MENU (FULL-WIDTH COMFORTABLE FORM)
             ============================================================ -->
        <div class="tab-pane fade" id="tab-create-pane" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom d-flex align-items-center justify-content-between">
                    <span class="fw-bold text-dark fs-6">
                        <i class="fa fa-file-circle-plus text-success me-2"></i> New Page &amp; Navigation Menu Link
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="switchToListTab()">
                        <i class="fa fa-arrow-left me-1"></i> Back to Directory
                    </button>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form action="<?php echo URLROOT; ?>/frontcms/pages" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="has_menu_sync" value="1">

                        <!-- Row 1: Page Title & URL Slug -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-dark small">Page Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="pageTitleInput" class="form-control form-control-lg" placeholder="e.g. Admission Guidelines, Fee Policy, STEM Labs" required oninput="generateSlug(this.value)">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold text-dark small">URL Slug</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light text-muted small font-monospace">/home/page/</span>
                                    <input type="text" name="slug" id="pageSlugInput" class="form-control font-monospace" placeholder="auto-slug">
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Navbar Menu Sync Integration (User requested feature!) -->
                        <div class="p-3 mb-3 rounded-3 border" style="background: #f8fafc;">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="add_to_menu" id="addToMenuSwitch" value="yes" checked onchange="toggleMenuSettings(this)">
                                    <label class="form-check-label fw-bold text-dark" for="addToMenuSwitch">
                                        <i class="fa fa-bars text-primary me-1"></i> Add directly to Website Top Navigation Bar
                                    </label>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary font-monospace small">1-Click Auto Sync</span>
                            </div>
                            <div class="text-muted small mb-3">
                                When enabled, this page is automatically linked in the website's top header menu.
                            </div>

                            <div id="menuSettingsRow" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary">Menu Display Text</label>
                                    <input type="text" name="menu_title" id="menuTitleInput" class="form-control form-control-sm" placeholder="Leave empty to use page title">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold text-secondary">Menu Placement / Dropdown Group</label>
                                    <select name="menu_dropdown_group" id="menuDropdownGroupSelect" class="form-select form-select-sm" onchange="toggleCustomDropdownInput(this.value)">
                                        <option value="none">Top Navigation Bar (Direct Link)</option>
                                        <option value="explore">Inside "Explore / Campus Life" Dropdown</option>
                                        <option value="academics">Inside "Academics" Dropdown</option>
                                        <option value="about">Inside "About Us" Dropdown</option>
                                        <option value="custom">+ Create / Enter Custom Dropdown Group</option>
                                    </select>
                                    <div id="customDropdownGroupWrap" class="mt-1" style="display: none;">
                                        <input type="text" name="menu_custom_dropdown_group" id="menuCustomDropdownInput" class="form-control form-control-sm" placeholder="e.g. Student Corner, Admissions">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary">Menu Order</label>
                                    <input type="number" name="menu_sort_order" class="form-control form-control-sm" value="10" placeholder="e.g. 1, 2, 5">
                                </div>
                            </div>
                        </div>

                        <!-- Row 3: 1-Click Starter Layout Templates (Interactive Cards) -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-bold small text-dark mb-0">
                                    <i class="fa fa-magic text-warning me-1"></i> 1-Click Designer Layout Templates
                                </label>
                                <span class="text-muted smaller" style="font-size: 0.75rem;">Click any template to inject styled rich layout into editor</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="injectStarterTemplate('about')" title="Inject About Us Layout">
                                        <div class="fs-4 mb-1">🏫</div>
                                        <div class="fw-bold small text-dark">About Us</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">Mission &amp; Pillars</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="injectStarterTemplate('admission')" title="Inject Admissions Layout">
                                        <div class="fs-4 mb-1">📜</div>
                                        <div class="fw-bold small text-dark">Admissions</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">3-Step Enrollment</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="injectStarterTemplate('facilities')" title="Inject Campus Facilities Layout">
                                        <div class="fs-4 mb-1">🔬</div>
                                        <div class="fw-bold small text-dark">Facilities</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">Labs &amp; Commons</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="injectStarterTemplate('notice')" title="Inject Circular / Alert Layout">
                                        <div class="fs-4 mb-1">📢</div>
                                        <div class="fw-bold small text-dark">Circular/Notice</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">Official Policy</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="injectStarterTemplate('contact')" title="Inject Secretariat Contact Layout">
                                        <div class="fs-4 mb-1">📞</div>
                                        <div class="fw-bold small text-dark">Contact Desk</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">Timings &amp; Grounds</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4 col-lg">
                                    <div class="card p-2 text-center border rounded-3 cursor-pointer cms-tpl-card bg-white" onclick="injectStarterTemplate('fees')" title="Inject Tuition & Fee Schedule Layout">
                                        <div class="fs-4 mb-1">💳</div>
                                        <div class="fw-bold small text-dark">Fee Schedule</div>
                                        <div class="text-muted smaller" style="font-size: 0.7rem;">Pricing &amp; Rules</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row 4: Summernote WYSIWYG & HTML Editor -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-dark small mb-0">Page Body Content</label>
                                <span class="text-muted smaller" style="font-size: 0.75rem;">
                                    Use toolbar for bold, headings, tables, or click <strong>&lt;/&gt; Code View</strong> for raw HTML
                                </span>
                            </div>
                            <textarea name="content" id="createPageContentEditor" class="form-control" rows="12"></textarea>
                        </div>

                        <!-- Row 5: Document / File Attachment & Meta Description -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label fw-bold small text-dark mb-0">
                                            <i class="fa fa-paperclip text-info me-1"></i> Attach File / Document (Optional)
                                        </label>
                                        <span class="badge bg-danger bg-opacity-10 text-danger font-monospace" style="font-size: 0.68rem;">PDF Live Viewer Ready</span>
                                    </div>
                                    <input type="file" name="file_attachment" id="createFileInput" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.webp" onchange="handleFilePreview(this, 'create')">
                                    <div class="form-text small text-muted mt-1">
                                        Supports: <strong>PDF, Word, Excel, JPG, PNG</strong> (Max 10MB). 
                                        PDF documents will automatically display with an <strong>interactive live reader &amp; previewer</strong>!
                                    </div>

                                    <!-- Live Client-side Preview Box -->
                                    <div id="createFilePreviewCard" style="display:none;" class="mt-3 p-2 bg-white rounded-3 border shadow-sm">
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                            <div class="d-flex align-items-center gap-2 text-truncate">
                                                <i id="createFilePreviewIcon" class="fa fa-file-pdf text-danger fs-5"></i>
                                                <div class="text-truncate">
                                                    <span id="createFilePreviewName" class="fw-bold small text-dark d-block text-truncate"></span>
                                                    <span id="createFilePreviewSize" class="badge bg-secondary font-monospace" style="font-size: 0.65rem;"></span>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-xs btn-outline-danger py-1 px-2 rounded-pill" onclick="clearSelectedFile('create')">
                                                <i class="fa fa-times me-1"></i> Clear
                                            </button>
                                        </div>
                                        <div id="createPdfPreviewWrapper" style="height: 300px; display: none;" class="rounded overflow-hidden border">
                                            <iframe id="createPdfPreviewFrame" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                                        </div>
                                        <div id="createImgPreviewWrapper" style="display: none;" class="text-center p-2">
                                            <img id="createImgPreview" src="" class="img-fluid rounded border" style="max-height: 240px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <label class="form-label fw-bold small text-dark mb-1">
                                        <i class="fa fa-info-circle text-primary me-1"></i> Short Summary / Subtitle
                                    </label>
                                    <input type="text" name="meta_description" class="form-control form-control-sm" placeholder="1-2 sentences describing this page">
                                    <div class="form-check form-switch mt-3">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="createActiveCheck" value="yes" checked>
                                        <label class="form-check-label fw-bold small text-dark" for="createActiveCheck">Publish live immediately</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-4 fw-bold shadow-sm" style="border-radius: 10px;">
                                <i class="fa fa-save me-2"></i> Save Page &amp; Publish
                            </button>
                            <button type="button" class="btn btn-light border btn-lg px-4" style="border-radius: 10px;" onclick="switchToListTab()">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ============================================================
             TAB 3: CUSTOM EXTERNAL / ANCHOR LINKS
             ============================================================ -->
        <div class="tab-pane fade" id="tab-customlink-pane" role="tabpanel">
            <!-- Add Custom Link Form -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom">
                    <span class="fw-bold text-dark">
                        <i class="fa fa-link text-warning me-2"></i> Add Custom Link to Top Navbar
                    </span>
                </div>
                <div class="card-body p-3 p-md-4">
                    <form action="<?php echo URLROOT; ?>/frontcms/pages" method="post">
                        <input type="hidden" name="add_custom_link" value="1">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-dark">Menu Label</label>
                                <input type="text" name="custom_link_title" class="form-control" placeholder="e.g. Portal Login, Alumni, #academics" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-dark">URL / Anchor</label>
                                <input type="text" name="custom_link_url" class="form-control" placeholder="https://... or #section-id" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-warning w-100 fw-bold">
                                    <i class="fa fa-plus me-1"></i> Add to Menu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Custom Links Table -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 px-3 px-md-4 border-bottom">
                    <span class="fw-bold text-dark">
                        <i class="fa fa-bars text-secondary me-2"></i> Existing Custom Navigation Links
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-secondary small text-uppercase">
                            <tr>
                                <th class="ps-3 ps-md-4">Label</th>
                                <th>Target URL / Destination</th>
                                <th>Order</th>
                                <th class="text-end pe-3 pe-md-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data['customLinks'])): ?>
                                <?php foreach($data['customLinks'] as $cl): ?>
                                    <tr>
                                        <td class="ps-3 ps-md-4 fw-bold text-dark">
                                            <i class="fa fa-link text-warning me-2"></i> <?php echo htmlspecialchars($cl->title, ENT_QUOTES, 'UTF-8'); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace py-1 px-2">
                                                <?php echo htmlspecialchars($cl->link, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-secondary border"><?php echo $cl->sort_order; ?></span>
                                        </td>
                                        <td class="text-end pe-3 pe-md-4">
                                            <form action="<?php echo URLROOT; ?>/frontcms/pages" method="post" class="d-inline" onsubmit="return confirm('Remove this menu link?');">
                                                <input type="hidden" name="delete_menu_id" value="<?php echo $cl->id; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Remove Link">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No custom external links added yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
    // Initialize Summernote Lite on the Create form
    $('#createPageContentEditor').summernote({
        placeholder: 'Write page content here... Format with bold/italic, insert tables, or click </> for raw HTML view...',
        tabsize: 2,
        height: 320,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview', 'fullscreen']]
        ]
    });

    // Handle initial tab activation if requested via URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'create') {
        switchToCreateTab();
    } else if (urlParams.get('tab') === 'menus') {
        const customTabTrigger = document.querySelector('#tab-customlink-btn');
        if (customTabTrigger) {
            new bootstrap.Tab(customTabTrigger).show();
        }
    }
});

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
    btn.innerHTML = '<i class="fa fa-check text-success" style="font-size: 0.7rem;"></i>';
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

// Instant Table Search & Filtering
let currentTableFilter = 'all';

function setPageTableFilter(type, btn) {
    currentTableFilter = type;
    document.querySelectorAll('.filter-pill-btn').forEach(b => {
        b.classList.remove('active', 'fw-bold');
    });
    if (btn) btn.classList.add('active', 'fw-bold');
    filterPagesTable();
}

function filterPagesTable() {
    const searchVal = (document.getElementById('pageSearchInput')?.value || '').trim().toLowerCase();
    const rows = document.querySelectorAll('.page-table-row');

    rows.forEach(row => {
        const title = row.getAttribute('data-title') || '';
        const slug = row.getAttribute('data-slug') || '';
        const menu = row.getAttribute('data-menu') || '';
        const hasPdf = row.getAttribute('data-pdf') === '1';

        const matchesSearch = !searchVal || title.includes(searchVal) || slug.includes(searchVal);
        let matchesFilter = true;

        if (currentTableFilter === 'top') {
            matchesFilter = (menu === 'top');
        } else if (currentTableFilter === 'dropdown') {
            matchesFilter = (menu === 'dropdown');
        } else if (currentTableFilter === 'pdf') {
            matchesFilter = hasPdf;
        }

        row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
    });
}

function switchToCreateTab() {
    const trigger = document.querySelector('#tab-create-btn');
    if (trigger) {
        new bootstrap.Tab(trigger).show();
        setTimeout(function() {
            document.getElementById('pageTitleInput').focus();
        }, 150);
    }
}

function switchToListTab() {
    const trigger = document.querySelector('#tab-list-btn');
    if (trigger) {
        new bootstrap.Tab(trigger).show();
    }
}

function generateSlug(text) {
    const slug = text.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .trim()
        .replace(/[\s_-]+/g, '-');
    document.getElementById('pageSlugInput').value = slug;

    const menuTitleInput = document.getElementById('menuTitleInput');
    if (menuTitleInput && (!menuTitleInput.value || menuTitleInput.dataset.touched !== 'true')) {
        menuTitleInput.value = text;
    }
}

function toggleMenuSettings(checkbox) {
    const row = document.getElementById('menuSettingsRow');
    if (row) {
        row.style.display = checkbox.checked ? 'flex' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const menuTitleInput = document.getElementById('menuTitleInput');
    if (menuTitleInput) {
        menuTitleInput.addEventListener('input', function() {
            this.dataset.touched = 'true';
        });
    }
});

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

function injectStarterTemplate(key) {
    if (!starterTemplates[key]) return;
    const editor = $('#createPageContentEditor');
    const currentCode = editor.summernote('code');
    if (currentCode && currentCode.trim() !== '' && currentCode !== '<p><br></p>') {
        if (!confirm('Replace current editor content with the selected starter template?')) {
            return;
        }
    }
    editor.summernote('code', starterTemplates[key]);
}

function toggleCustomDropdownInput(val) {
    const wrap = document.getElementById('customDropdownGroupWrap');
    if (wrap) {
        wrap.style.display = (val === 'custom') ? 'block' : 'none';
    }
}
</script>
