<?php require APPROOT . '/Views/layouts/header.php'; 
    $user = $data['user'];
    $activeTab = $data['active_tab'] ?? 'info';
    $roleName = !empty($user->role) ? ucfirst(str_replace('_', ' ', $user->role)) : 'User';
    $initials = strtoupper(substr($user->name ?? 'User', 0, 2));
    $avatarUrl = !empty($user->avatar) ? (URLROOT . '/' . htmlspecialchars($user->avatar)) : '';
?>

<div class="container-fluid px-0">
    <!-- Breadcrumb & Header Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Profile Settings</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-user-gear text-primary me-2"></i>My Profile &amp; Account Settings
            </h2>
            <small class="text-muted">Manage your personal identification, contact details, professional bio, and security credentials.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fa fa-arrow-left me-1"></i> Back to Hub
            </a>
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

    <div class="row g-4">
        <!-- Left Column: User Profile Summary Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4 text-center">
                    <!-- Avatar Display / Preview -->
                    <div class="position-relative d-inline-block mb-3">
                        <div id="profileAvatarPreviewContainer" style="width: 110px; height: 110px; border-radius: 50%; overflow: hidden; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: 4px solid #ffffff;">
                            <?php if(!empty($avatarUrl)): ?>
                                <img src="<?php echo $avatarUrl; ?>" alt="Avatar" id="profileAvatarImg" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div id="profileAvatarInitials" class="d-flex align-items-center justify-content-center bg-primary text-white fs-2 fw-bold" style="width: 100%; height: 100%;">
                                    <?php echo $initials; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <label for="avatarFileInput" class="btn btn-sm btn-light border rounded-circle shadow-sm position-absolute bottom-0 end-0 p-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; cursor: pointer;" title="Upload new photo">
                            <i class="fa fa-camera text-primary"></i>
                        </label>
                    </div>

                    <h5 class="fw-bold text-dark mb-1" id="profileCardName"><?php echo htmlspecialchars($user->name ?? 'Administrator'); ?></h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill mb-2">
                        <i class="fa fa-user-shield me-1"></i><?php echo htmlspecialchars($roleName); ?>
                    </span>
                    <div class="text-muted small mb-3" id="profileCardEmail">
                        <i class="fa fa-envelope me-1 text-secondary"></i><?php echo htmlspecialchars($user->email ?? ''); ?>
                    </div>

                    <?php if(!empty($user->bio)): ?>
                        <p class="text-muted small fst-italic px-2 mb-3" id="profileCardBio">
                            "<?php echo htmlspecialchars($user->bio); ?>"
                        </p>
                    <?php endif; ?>

                    <hr class="my-3">

                    <div class="text-start small">
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted"><i class="fa fa-id-badge text-primary me-2"></i>Account ID</span>
                            <span class="fw-bold text-dark">#<?php echo (int)($user->id ?? 1); ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted"><i class="fa fa-phone text-success me-2"></i>Phone / Mobile</span>
                            <span class="fw-bold text-dark" id="profileCardPhone"><?php echo !empty($user->phone) ? htmlspecialchars($user->phone) : '<span class="text-muted fw-normal">Not provided</span>'; ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted"><i class="fa fa-map-marker-alt text-danger me-2"></i>Location</span>
                            <span class="fw-bold text-dark text-truncate ms-2" style="max-width: 160px;" id="profileCardAddress"><?php echo !empty($user->address) ? htmlspecialchars($user->address) : '<span class="text-muted fw-normal">Not provided</span>'; ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted"><i class="fa fa-calendar-check text-info me-2"></i>Account Status</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Security Checklist -->
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="fa fa-shield-alt text-success me-2"></i>Account Security Advice
                    </h6>
                    <ul class="text-muted small ps-3 mb-0" style="line-height: 1.6;">
                        <li class="mb-1">Keep your login email active and accessible for password reset PINs.</li>
                        <li class="mb-1">Use a unique password with at least 8 characters, combining numbers and symbols.</li>
                        <li>Always sign out when using shared office computers.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings Tabs (Personal Bio & Info + Security) -->
        <div class="col-lg-8">
            <!-- Tabs Navigation -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-2 bg-white rounded-3">
                    <ul class="nav nav-pills nav-justified" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link py-2 px-3 fw-semibold <?php echo ($activeTab === 'info') ? 'active' : ''; ?>" id="tab-info-btn" data-bs-toggle="pill" data-bs-target="#tab-profile-info" type="button" role="tab">
                                <i class="fa fa-user-edit me-2"></i>Personal Profile &amp; Bio
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-2 px-3 fw-semibold <?php echo ($activeTab === 'security') ? 'active' : ''; ?>" id="tab-security-btn" data-bs-toggle="pill" data-bs-target="#tab-profile-security" type="button" role="tab">
                                <i class="fa fa-key me-2"></i>Security &amp; Password
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tabs Content -->
            <div class="tab-content" id="profileTabsContent">
                <!-- ========================================================= -->
                <!-- TAB 1: PERSONAL BIO & IDENTIFICATION                      -->
                <!-- ========================================================= -->
                <div class="tab-pane fade <?php echo ($activeTab === 'info') ? 'show active' : ''; ?>" id="tab-profile-info" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-primary">
                                <i class="fa fa-id-card me-2"></i>Identity &amp; Bio Information
                            </h5>
                            <span class="text-muted small">Update your public profile</span>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo URLROOT; ?>/profile/index" method="post" enctype="multipart/form-data" class="ajax-profile-form no-pjax" data-no-pjax="true" id="profileInfoForm">
                                <input type="hidden" name="action" value="update_profile">
                                <input type="hidden" name="tab" value="info">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                                <!-- Hidden Avatar File Input (Triggered by camera button) -->
                                <input type="file" name="avatar" id="avatarFileInput" class="d-none" accept="image/jpeg,image/png,image/webp,image/gif">

                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-user text-primary me-2"></i>Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="name" id="inputProfileName" class="form-control" required value="<?php echo htmlspecialchars($user->name ?? ''); ?>">
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">Your formal display name across portal notifications and signatures.</div>
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-user-shield text-warning me-2"></i>System Role
                                        </label>
                                        <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($roleName); ?>" readonly>
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">Managed by system administrative policy.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-envelope text-info me-2"></i>Email Address (Login ID) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="fa fa-at text-muted"></i></span>
                                            <input type="email" name="email" id="inputProfileEmail" class="form-control" required value="<?php echo htmlspecialchars($user->email ?? ''); ?>">
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">Used to log into your account and receive system alerts.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-phone text-success me-2"></i>Phone / Mobile Number
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="fa fa-mobile-alt text-muted"></i></span>
                                            <input type="text" name="phone" id="inputProfilePhone" class="form-control" value="<?php echo htmlspecialchars($user->phone ?? ''); ?>" placeholder="e.g. +92 300 1234567">
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">For urgent operational and emergency communications.</div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-comment-dots text-secondary me-2"></i>Professional Bio / Description
                                        </label>
                                        <textarea name="bio" id="inputProfileBio" class="form-control" rows="3" placeholder="Write a brief professional summary, qualifications, or profile overview..."><?php echo htmlspecialchars($user->bio ?? ''); ?></textarea>
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">Visible on your institutional profile summary.</div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-map-marker-alt text-danger me-2"></i>Office / Residential Address
                                        </label>
                                        <input type="text" name="address" id="inputProfileAddress" class="form-control" value="<?php echo htmlspecialchars($user->address ?? ''); ?>" placeholder="e.g. House #12, Street 4, Sector F-8, Islamabad">
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">
                                        <i class="fa fa-info-circle me-1"></i> Changes take effect immediately across all sessions.
                                    </span>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold" id="saveProfileInfoBtn">
                                        <i class="fa fa-save me-2"></i>Save Profile Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ========================================================= -->
                <!-- TAB 2: SECURITY & PASSWORD CHANGE                         -->
                <!-- ========================================================= -->
                <div class="tab-pane fade <?php echo ($activeTab === 'security') ? 'show active' : ''; ?>" id="tab-profile-security" role="tabpanel">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-warning">
                                <i class="fa fa-key me-2"></i>Change Account Password
                            </h5>
                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1 rounded-pill small">
                                <i class="fa fa-lock me-1"></i>Encrypted Storage
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo URLROOT; ?>/profile/index" method="post" class="ajax-profile-form no-pjax" data-no-pjax="true" id="profilePasswordForm">
                                <input type="hidden" name="action" value="change_password">
                                <input type="hidden" name="tab" value="security">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-lock text-secondary me-2"></i>Current Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" name="current_password" class="form-control" required placeholder="Enter current login password">
                                            <button class="btn btn-outline-secondary toggle-pass-btn" type="button">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">Required for identity verification before changing credentials.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-key text-primary me-2"></i>New Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Enter new password (min 6 chars)">
                                            <button class="btn btn-outline-secondary toggle-pass-btn" type="button">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-check-double text-success me-2"></i>Confirm New Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" name="confirm_password" class="form-control" required minlength="6" placeholder="Re-type new password">
                                            <button class="btn btn-outline-secondary toggle-pass-btn" type="button">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">
                                        <i class="fa fa-shield-alt me-1"></i> Passwords are automatically hashed with Bcrypt.
                                    </span>
                                    <button type="submit" class="btn btn-warning text-dark px-4 fw-bold" id="savePasswordBtn">
                                        <i class="fa fa-check-circle me-2"></i>Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function initProfilePage() {
        // 1. Live Avatar File Preview
        const avatarInput = document.getElementById('avatarFileInput');
        if (avatarInput && !avatarInput._hasPreview) {
            avatarInput._hasPreview = true;
            avatarInput.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const container = document.getElementById('profileAvatarPreviewContainer');
                        if (container) {
                            container.innerHTML = `<img src="${e.target.result}" alt="Avatar Preview" id="profileAvatarImg" style="width: 100%; height: 100%; object-fit: cover;">`;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // 2. Password Visibility Toggles
        document.querySelectorAll('.toggle-pass-btn').forEach(btn => {
            if (btn._hasToggle) return;
            btn._hasToggle = true;
            btn.addEventListener('click', function() {
                const input = this.closest('.input-group').querySelector('input');
                const icon = this.querySelector('i');
                if (!input) return;
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // 3. Tab Sync with URL without page reload
        const tabButtons = document.querySelectorAll('#profileTabs button[data-bs-toggle="pill"]');
        tabButtons.forEach(btn => {
            if (btn._hasTabSync) return;
            btn._hasTabSync = true;
            btn.addEventListener('shown.bs.tab', function(e) {
                const target = e.target.getAttribute('data-bs-target');
                if (target) {
                    const tabKey = target.replace('#tab-profile-', '');
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('tab', tabKey);
                    window.history.replaceState({ url: newUrl.toString() }, '', newUrl.toString());
                }
            });
        });

        // 4. Smooth AJAX Form Submissions
        document.querySelectorAll('.ajax-profile-form').forEach(form => {
            if (form._hasAjax) return;
            form._hasAjax = true;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]:not([disabled])');
                if (!submitBtn) return;

                const originalHtml = submitBtn.innerHTML;

                try {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Updating...';

                    const formData = new FormData(form);
                    formData.append('ajax_submit', '1');
                    const formToken = form.querySelector('input[name="csrf_token"]')?.value 
                                   || window.CSRF_TOKEN 
                                   || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                                   || '';
                    if (formToken) {
                        formData.set('csrf_token', formToken);
                    }

                    // Must use form.getAttribute('action') because form.action resolves to input[name="action"]
                    const rawAction = form.getAttribute('action') || window.location.href;
                    const targetUrl = String(rawAction).split('?')[0];

                    const response = await fetch(targetUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const rawText = await response.text();
                    let result;
                    try {
                        result = JSON.parse(rawText);
                    } catch(err) {
                        console.warn('Profile non-JSON response:', rawText);
                        if (response.ok) {
                            result = { success: true, message: 'Profile updated successfully!' };
                        } else if (response.status === 401) {
                            result = { success: false, unauthorized: true, message: 'Session expired. Please log in again.' };
                        } else {
                            throw new Error('Server returned an error (' + response.status + '). Please try again.');
                        }
                    }

                    // Handle session expiration
                    if (result && (result.unauthorized || response.status === 401)) {
                        if (typeof window.showToast === 'function') {
                            window.showToast('Session has expired. Redirecting to login...', 'warning');
                        }
                        setTimeout(() => {
                            window.location.href = result.redirect || ('<?php echo URLROOT; ?>/auth/login');
                        }, 1200);
                        return;
                    }

                    // Keep CSRF token in sync across subsequent requests
                    const syncToken = response.headers.get('X-CSRF-Token') || result?.csrf_token;
                    if (syncToken) {
                        window.CSRF_TOKEN = syncToken;
                        document.querySelectorAll('input[name="csrf_token"]').forEach(inp => inp.value = syncToken);
                    }

                    if (result && result.success) {
                        if (typeof window.showToast === 'function') {
                            window.showToast(result.message || 'Profile saved successfully!', 'success');
                        }

                        // Update Left Profile Card instantly
                        if (result.user_name) {
                            const cardName = document.getElementById('profileCardName');
                            if (cardName) cardName.textContent = result.user_name;

                            // Update topbar username
                            const topUserName = document.querySelector('.top-navbar .user-name');
                            if (topUserName) topUserName.textContent = result.user_name;
                        }

                        if (result.user_email) {
                            const cardEmail = document.getElementById('profileCardEmail');
                            if (cardEmail) cardEmail.innerHTML = `<i class="fa fa-envelope me-1 text-secondary"></i>${result.user_email}`;
                        }

                        const inputPhone = document.getElementById('inputProfilePhone');
                        const cardPhone = document.getElementById('profileCardPhone');
                        if (inputPhone && cardPhone) {
                            cardPhone.textContent = inputPhone.value || 'Not provided';
                        }

                        const inputAddress = document.getElementById('inputProfileAddress');
                        const cardAddress = document.getElementById('profileCardAddress');
                        if (inputAddress && cardAddress) {
                            cardAddress.textContent = inputAddress.value || 'Not provided';
                        }

                        const inputBio = document.getElementById('inputProfileBio');
                        const cardBio = document.getElementById('profileCardBio');
                        if (inputBio && cardBio) {
                            cardBio.textContent = inputBio.value ? `"${inputBio.value}"` : '';
                        }

                        if (result.avatar_url) {
                            const container = document.getElementById('profileAvatarPreviewContainer');
                            if (container) {
                                container.innerHTML = `<img src="${result.avatar_url}?v=${Date.now()}" alt="Avatar" id="profileAvatarImg" style="width: 100%; height: 100%; object-fit: cover;">`;
                            }

                            // Update topbar avatar if present
                            const topAvatarWrap = document.querySelector('.user-avatar-wrap');
                            if (topAvatarWrap) {
                                topAvatarWrap.innerHTML = `<img src="${result.avatar_url}?v=${Date.now()}" alt="Avatar" class="user-avatar" style="object-fit: cover; width: 38px; height: 38px; border-radius: 50%;"><span class="status-dot"></span>`;
                            }
                        }

                        // Clear password inputs on password form success
                        if (form.id === 'profilePasswordForm') {
                            form.reset();
                        }

                        submitBtn.innerHTML = '<i class="fa fa-check me-2"></i>Saved!';
                        submitBtn.classList.remove('btn-primary', 'btn-warning');
                        submitBtn.classList.add('btn-success');
                        setTimeout(() => {
                            submitBtn.innerHTML = originalHtml;
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('btn-success');
                            submitBtn.classList.add(form.id === 'profilePasswordForm' ? 'btn-warning' : 'btn-primary');
                        }, 1800);
                    } else {
                        throw new Error(result.message || 'Failed to update.');
                    }
                } catch(err) {
                    console.error('Profile error:', err);
                    if (typeof window.showToast === 'function') {
                        window.showToast(err.message || 'Error occurred.', 'danger');
                    } else {
                        alert(err.message || 'Error occurred.');
                    }
                    submitBtn.innerHTML = originalHtml;
                    submitBtn.disabled = false;
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initProfilePage);
    document.addEventListener('page:loaded', initProfilePage);
    initProfilePage();
})();
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

