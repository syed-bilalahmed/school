<?php require APPROOT . '/Views/layouts/header.php'; ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h2 mb-1">Schools</h1>
        <p class="text-secondary opacity-75 mb-0">Manage tenant onboarding and school status.</p>
    </div>
</div>

<?php if(isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-exclamation-circle fs-5"></i>
        <div><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
$activeSchoolId = TenantContext::getSchoolId() ?: 1;
?>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-header bg-white border-bottom p-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3">
                        <i class="fa fa-code-branch fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Add New Branch / Campus</h5>
                        <small class="text-muted">Auto-generates dedicated branch URL &amp; routing slug</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 pt-3">
                <form action="<?php echo URLROOT; ?>/admin/addSchool" method="post" id="branchCreateForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Branch / Campus Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="branchNameInput" class="form-control <?php echo !empty($data['errors']['name']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['form']['name'] ?? ''); ?>" placeholder="e.g. Gulshan Campus, North Campus" required style="border-radius: 8px;">
                        <?php if(!empty($data['errors']['name'])): ?>
                            <div class="invalid-feedback"><?php echo $data['errors']['name']; ?></div>
                        <?php endif; ?>
                        <div class="form-text small">Type any branch name — its routing code and URL will generate automatically.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small d-flex justify-content-between">
                            <span>Branch Slug / Code</span>
                            <span class="badge bg-light text-primary border" id="autoGenBadge">Auto-Generated</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-tag"></i></span>
                            <input type="text" name="code" id="branchCodeInput" class="form-control font-monospace <?php echo !empty($data['errors']['code']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['form']['code'] ?? ''); ?>" placeholder="e.g. gulshan-campus" required style="border-radius: 0 8px 8px 0;">
                        </div>
                        <?php if(!empty($data['errors']['code'])): ?>
                            <div class="invalid-feedback d-block"><?php echo $data['errors']['code']; ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Live Auto-Generated Branch URL Preview Card -->
                    <div class="p-3 mb-3 bg-light rounded-3 border border-primary border-opacity-25" id="urlPreviewBox" style="background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <small class="fw-bold text-primary"><i class="fa fa-link me-1"></i> Auto-Generated Branch URL:</small>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem;">Instant Routing</span>
                        </div>
                        <div class="font-monospace small text-break text-dark fw-bold" id="liveBranchUrlPreview">
                            <?php echo URLROOT; ?>/?branch=<span id="slugText"><?php echo htmlspecialchars($data['form']['code'] ?? 'branch-name'); ?></span>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.75rem;">
                            <i class="fa fa-info-circle me-1"></i>Also accessible via tenant path: <code>/s/<span id="slugPathText"><?php echo htmlspecialchars($data['form']['code'] ?? 'branch-name'); ?></span></code>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Custom Subdomain / Domain (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted" style="border-radius: 8px 0 0 8px;"><i class="fa fa-globe"></i></span>
                            <input type="text" name="domain" id="branchDomainInput" class="form-control <?php echo !empty($data['errors']['domain']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($data['form']['domain'] ?? ''); ?>" placeholder="e.g. gulshan.myschool.edu" style="border-radius: 0 8px 8px 0;">
                        </div>
                        <?php if(!empty($data['errors']['domain'])): ?>
                            <div class="invalid-feedback d-block"><?php echo $data['errors']['domain']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">Branch Status</label>
                        <select name="status" class="form-select" style="border-radius: 8px;">
                            <?php $status = $data['form']['status'] ?? 'active'; ?>
                            <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active (Immediate Access)</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending (Setup Phase)</option>
                            <option value="suspended" <?php echo $status === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold shadow-sm" style="border-radius: 8px;">
                        <i class="fa fa-plus-circle me-1"></i> Create Branch &amp; Generate URL
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-header bg-white border-bottom p-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold text-dark"><i class="fa fa-building text-primary me-2"></i>Campus Network &amp; Branches</h5>
                    <small class="text-muted">Multi-tenant isolation &amp; one-click branch switching</small>
                </div>
                <span class="badge bg-primary px-3 py-1.5 rounded-pill"><?php echo count($data['schools'] ?? []); ?> Branches Total</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Branch Details</th>
                                <th>Direct URL</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Switch / Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($data['schools'])): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="fa fa-school fs-1 text-muted opacity-50 mb-2"></i>
                                        <div>No branches found in database.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($data['schools'] as $school): 
                                    $isCurrentActive = ((int)$school->id === (int)$activeSchoolId);
                                    $branchDirectUrl = URLROOT . '/?branch=' . urlencode($school->code);
                                ?>
                                    <tr class="<?php echo $isCurrentActive ? 'table-primary-subtle' : ''; ?>" style="<?php echo $isCurrentActive ? 'background: #f0f7ff;' : ''; ?>">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($school->name); ?></div>
                                                <?php if($isCurrentActive): ?>
                                                    <span class="badge bg-success shadow-sm px-2 py-1" style="font-size: 0.72rem;">
                                                        <i class="fa fa-check-circle me-1"></i> Active Now
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.75rem;">
                                                    Code: <?php echo htmlspecialchars($school->code); ?>
                                                </span>
                                                <?php if(!empty($school->domain)): ?>
                                                    <small class="text-muted"><i class="fa fa-globe me-1"></i><?php echo htmlspecialchars($school->domain); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                <a href="<?php echo $branchDirectUrl; ?>" target="_blank" class="text-primary text-decoration-none small fw-semibold font-monospace" title="Open this branch in new tab">
                                                    /?branch=<?php echo htmlspecialchars($school->code); ?>
                                                    <i class="fa fa-external-link-alt fa-xs ms-1"></i>
                                                </a>
                                                <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-muted" onclick="navigator.clipboard.writeText('<?php echo $branchDirectUrl; ?>'); alert('Branch URL copied to clipboard: <?php echo $branchDirectUrl; ?>');" title="Copy Full URL">
                                                    <i class="fa fa-copy"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                                $badge = 'secondary';
                                                if ($school->status === 'active') $badge = 'success';
                                                if ($school->status === 'pending') $badge = 'warning';
                                                if ($school->status === 'suspended') $badge = 'danger';
                                            ?>
                                            <span class="badge bg-<?php echo $badge; ?> px-2 py-1"><?php echo ucfirst(htmlspecialchars($school->status)); ?></span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">
                                                <?php if(!$isCurrentActive): ?>
                                                    <a href="<?php echo URLROOT; ?>/admin/switchSchool/<?php echo (int)$school->id; ?>" class="btn btn-sm btn-primary shadow-sm fw-semibold px-2.5 py-1" style="border-radius: 6px; font-size: 0.82rem;" title="Switch active management to this branch">
                                                        <i class="fa fa-random me-1"></i> Switch Branch
                                                    </a>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-sm btn-outline-success disabled px-2.5 py-1" style="border-radius: 6px; font-size: 0.82rem;">
                                                        <i class="fa fa-check me-1"></i> Current Active
                                                    </button>
                                                <?php endif; ?>

                                                <div class="btn-group btn-group-sm" role="group" aria-label="Status actions">
                                                    <a class="btn btn-outline-success <?php echo $school->status === 'active' ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/admin/schoolStatus/<?php echo (int)$school->id; ?>/active" title="Activate Branch">Active</a>
                                                    <a class="btn btn-outline-warning <?php echo $school->status === 'pending' ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/admin/schoolStatus/<?php echo (int)$school->id; ?>/pending" title="Set Pending">Pending</a>
                                                    <a class="btn btn-outline-danger <?php echo $school->status === 'suspended' ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/admin/schoolStatus/<?php echo (int)$school->id; ?>/suspended" title="Suspend Branch">Suspend</a>
                                                </div>

                                                <?php if((int)$school->id > 1): ?>
                                                    <form action="<?php echo URLROOT; ?>/admin/deleteSchool/<?php echo (int)$school->id; ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete \'<?php echo htmlspecialchars(addslashes($school->name)); ?>\'? All tenant data will be removed. This action cannot be undone.');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Branch">
                                                            <i class="fa fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border px-2 py-1" title="Primary Central Campus (Protected)">
                                                        <i class="fa fa-shield-alt text-primary"></i> Main
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Real-time Automatic Branch Code & URL Generator
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('branchNameInput');
    const codeInput = document.getElementById('branchCodeInput');
    const domainInput = document.getElementById('branchDomainInput');
    const slugText = document.getElementById('slugText');
    const slugPathText = document.getElementById('slugPathText');
    const baseUrl = <?php echo json_encode(URLROOT); ?>;
    let manualCodeEdit = false;

    codeInput.addEventListener('input', function() {
        manualCodeEdit = (this.value.trim() !== '');
        updateLivePreview(this.value);
    });

    nameInput.addEventListener('input', function() {
        if (!manualCodeEdit) {
            const rawVal = this.value;
            // Slugify: lowercase, replace spaces/special chars with hyphens
            let slug = rawVal.toLowerCase()
                .trim()
                .replace(/[^a-z0-9_-]/g, '-')
                .replace(/-+/g, '-');
            
            codeInput.value = slug;
            updateLivePreview(slug);

            if (domainInput && !domainInput.value) {
                // optional domain suggestion
                try {
                    const parsedHost = window.location.hostname;
                    if (parsedHost !== 'localhost' && parsedHost !== '127.0.0.1') {
                        domainInput.placeholder = slug + '.' + parsedHost;
                    }
                } catch(e) {}
            }
        }
    });

    function updateLivePreview(slug) {
        const displaySlug = slug ? slug : 'branch-name';
        if (slugText) slugText.textContent = displaySlug;
        if (slugPathText) slugPathText.textContent = displaySlug;
    }
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
