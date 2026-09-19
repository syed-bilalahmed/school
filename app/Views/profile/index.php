<?php require APPROOT . '/Views/layouts/header.php'; 
    $user = $data['user'];
    $activeTab = $data['active_tab'] ?? 'info';
    $roleName = !empty($user->role) ? ucfirst(str_replace('_', ' ', $user->role)) : 'User';
    $initials = strtoupper(substr($user->name ?? 'User', 0, 2));
    $avatarUrl = !empty($user->avatar) ? (URLROOT . '/' . htmlspecialchars($user->avatar)) : '';
    $schoolName = defined('SITENAME') ? SITENAME : 'Greenwood International School';
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $browserInfo = $_SERVER['HTTP_USER_AGENT'] ?? 'Standard Web Browser';
    $isChrome = (strpos($browserInfo, 'Chrome') !== false);
    $isEdge = (strpos($browserInfo, 'Edg') !== false);
    $isFirefox = (strpos($browserInfo, 'Firefox') !== false);
    $browserName = $isEdge ? 'Microsoft Edge' : ($isChrome ? 'Google Chrome' : ($isFirefox ? 'Mozilla Firefox' : 'Web Browser'));
?>

<style>
/* Modern Executive Profile Styling */
.profile-hero-card {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #4f46e5 100%);
    border-radius: 20px;
    padding: 30px 35px 25px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.25);
    margin-bottom: 25px;
}
.profile-hero-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.profile-avatar-outer {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto;
}
.profile-avatar-box {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #ffffff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    background: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-avatar-btn {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 38px;
    height: 38px;
    background: #ffffff;
    color: #2563eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid #ffffff;
}
.profile-avatar-btn:hover {
    background: #2563eb;
    color: #ffffff;
    transform: scale(1.08);
}
.profile-info-pill {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 30px;
    padding: 6px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.profile-card {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.06);
    border-radius: 16px;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.2s ease;
}
.profile-card-header {
    background: #ffffff;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    padding: 18px 24px;
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.profile-nav-tabs .nav-link {
    border: none;
    color: #64748b;
    font-weight: 600;
    padding: 12px 20px;
    border-radius: 12px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.profile-nav-tabs .nav-link.active {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}
.profile-nav-tabs .nav-link:hover:not(.active) {
    background: rgba(37, 99, 235, 0.08);
    color: #2563eb;
}
.stat-mini-badge {
    padding: 10px 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.copy-btn {
    cursor: pointer;
    border: none;
    background: transparent;
    color: #94a3b8;
    padding: 2px 6px;
    border-radius: 4px;
    transition: color 0.15s;
}
.copy-btn:hover {
    color: #2563eb;
}
.password-meter-bar {
    height: 6px;
    border-radius: 4px;
    background: #e2e8f0;
    overflow: hidden;
    margin-top: 8px;
}
.password-meter-fill {
    height: 100%;
    width: 0%;
    transition: width 0.3s ease, background-color 0.3s ease;
}
.strength-rule {
    font-size: 0.78rem;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
}
.strength-rule.passed {
    color: #10b981;
    font-weight: 600;
}
</style>

<div class="container-fluid px-0">
    <!-- Breadcrumb & Top Controls -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted"><i class="fa fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Profile Settings</li>
                </ol>
            </nav>
            <h3 class="h4 fw-bold mb-0 text-dark">
                <i class="fa-solid fa-id-badge text-primary me-2"></i>Executive Profile &amp; Account Settings
            </h3>
            <small class="text-muted">Manage your institutional identity, verified credentials, and security controls.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                <i class="fa fa-arrow-left me-1"></i> Back to Dashboard
            </a>
            <button type="button" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm" onclick="document.getElementById('inputProfileName').focus();">
                <i class="fa fa-edit me-1"></i> Edit Details
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4 rounded-3" role="alert">
            <i class="fa fa-check-circle fs-5"></i>
            <div class="fw-semibold"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4 rounded-3" role="alert">
            <i class="fa fa-exclamation-circle fs-5"></i>
            <div class="fw-semibold"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Profile Hero Header Banner -->
    <div class="profile-hero-card">
        <div class="row align-items-center g-3">
            <div class="col-md-auto text-center text-md-start">
                <div class="profile-avatar-outer">
                    <div class="profile-avatar-box" id="profileAvatarPreviewContainer">
                        <?php if(!empty($avatarUrl)): ?>
                            <img src="<?php echo $avatarUrl; ?>" alt="Avatar" id="profileAvatarImg" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div id="profileAvatarInitials" class="text-white fs-2 fw-bold">
                                <?php echo $initials; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <label for="avatarFileInput" class="profile-avatar-btn" title="Click to upload new photo">
                        <i class="fa fa-camera"></i>
                    </label>
                </div>
            </div>
            <div class="col-md text-center text-md-start">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-2">
                    <h2 class="h3 fw-bold mb-0 text-white" id="heroDisplayName"><?php echo htmlspecialchars($user->name ?? 'Administrator'); ?></h2>
                    <span class="profile-info-pill">
                        <i class="fa fa-shield-alt text-warning"></i> <?php echo htmlspecialchars($roleName); ?>
                    </span>
                    <span class="profile-info-pill" style="background: rgba(16, 185, 129, 0.25); border-color: rgba(16, 185, 129, 0.4);">
                        <i class="fa fa-circle-check text-success"></i> Active Verified
                    </span>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-3 text-white-50 small">
                    <div><i class="fa fa-school me-1 text-white"></i> <?php echo htmlspecialchars($schoolName); ?></div>
                    <div><i class="fa fa-envelope me-1 text-white"></i> <span id="heroDisplayEmail"><?php echo htmlspecialchars($user->email ?? ''); ?></span></div>
                    <div><i class="fa fa-id-card me-1 text-white"></i> ID: #<?php echo str_pad((string)$user->id, 5, '0', STR_PAD_LEFT); ?></div>
                </div>
            </div>
            <div class="col-md-auto text-center text-md-end">
                <div class="d-flex flex-column align-items-center align-items-md-end gap-1">
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold shadow-sm">
                        <i class="fa fa-lock me-1"></i> Security: Enterprise Grade
                    </span>
                    <small class="text-white-50" style="font-size: 0.78rem;">Last Synchronized: <?php echo date('M d, Y H:i'); ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="row g-4">
        <!-- ============================================================= -->
        <!-- LEFT COLUMN: Identity Dossier, Contact & Security Widgets     -->
        <!-- ============================================================= -->
        <div class="col-lg-4">
            <!-- Account Summary Card -->
            <div class="profile-card mb-4">
                <div class="profile-card-header">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa fa-address-card text-primary me-2"></i>Account Dossier
                    </h6>
                    <span class="badge bg-primary-subtle text-primary rounded-pill small px-2">ID #<?php echo (int)$user->id; ?></span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-2">
                        <!-- Full Name -->
                        <div class="stat-mini-badge">
                            <div>
                                <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Full Name</span>
                                <span class="fw-bold text-dark" id="profileCardName"><?php echo htmlspecialchars($user->name ?? 'User'); ?></span>
                            </div>
                            <i class="fa fa-user text-primary opacity-50 fs-5"></i>
                        </div>

                        <!-- Email -->
                        <div class="stat-mini-badge">
                            <div>
                                <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Login Email</span>
                                <span class="fw-bold text-dark" id="profileCardEmail"><?php echo htmlspecialchars($user->email ?? ''); ?></span>
                            </div>
                            <button type="button" class="copy-btn" onclick="copyToClipboard('<?php echo htmlspecialchars($user->email ?? ''); ?>', 'Email copied!')" title="Copy Email">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>

                        <!-- Phone -->
                        <div class="stat-mini-badge">
                            <div>
                                <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Phone / Contact</span>
                                <span class="fw-bold text-dark" id="profileCardPhone"><?php echo !empty($user->phone) ? htmlspecialchars($user->phone) : '<span class="text-muted fw-normal">Not configured</span>'; ?></span>
                            </div>
                            <i class="fa fa-phone text-success opacity-50 fs-5"></i>
                        </div>

                        <!-- Address -->
                        <div class="stat-mini-badge">
                            <div>
                                <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">Assigned Location</span>
                                <span class="fw-bold text-dark text-truncate d-inline-block" style="max-width: 210px;" id="profileCardAddress"><?php echo !empty($user->address) ? htmlspecialchars($user->address) : '<span class="text-muted fw-normal">Not configured</span>'; ?></span>
                            </div>
                            <i class="fa fa-map-marker-alt text-danger opacity-50 fs-5"></i>
                        </div>
                    </div>

                    <?php if(!empty($user->bio)): ?>
                        <div class="mt-3 p-3 bg-light rounded-3 border">
                            <div class="text-muted small fw-bold mb-1"><i class="fa fa-quote-left text-primary me-1"></i> Professional Bio</div>
                            <p class="text-secondary small mb-0 fst-italic" id="profileCardBio">
                                "<?php echo htmlspecialchars($user->bio); ?>"
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Security Health Score Card -->
            <div class="profile-card mb-4">
                <div class="profile-card-header">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa fa-shield-halved text-success me-2"></i>Security Integrity
                    </h6>
                    <span class="badge bg-success text-white rounded-pill px-2" style="font-size: 0.72rem;">100% Secure</span>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-dark">Password Hashing</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Bcrypt Active</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-dark">CSRF Protection</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Token Enforced</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-dark">Session Isolation</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Multi-Tenant Scoped</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small fw-semibold text-dark">Rate Limiter</span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle">Active (DoS Shield)</span>
                    </div>
                </div>
            </div>

            <!-- Active Session Telemetry Widget -->
            <div class="profile-card">
                <div class="profile-card-header">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa fa-laptop-code text-info me-2"></i>Session &amp; Telemetry
                    </h6>
                    <span class="badge bg-light text-muted border px-2 py-1 small">Current Device</span>
                </div>
                <div class="card-body p-3 small text-muted">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span><i class="fa fa-globe me-2 text-primary"></i>IP Address</span>
                        <span class="fw-bold text-dark font-monospace"><?php echo htmlspecialchars($clientIp); ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span><i class="fa fa-compass me-2 text-info"></i>Browser</span>
                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($browserName); ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span><i class="fa fa-clock me-2 text-warning"></i>Login State</span>
                        <span class="badge bg-success-subtle text-success">Authenticated</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- RIGHT COLUMN: Interactive Tabbed Settings Hub                 -->
        <!-- ============================================================= -->
        <div class="col-lg-8">
            <!-- Tabs Navigation Bar -->
            <div class="profile-card mb-4 p-2">
                <ul class="nav profile-nav-tabs nav-pills nav-fill" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo ($activeTab === 'info') ? 'active' : ''; ?>" id="tab-info-btn" data-bs-toggle="pill" data-bs-target="#tab-profile-info" type="button" role="tab">
                            <i class="fa fa-user-gear"></i>
                            <span>Personal Details &amp; Bio</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo ($activeTab === 'security') ? 'active' : ''; ?>" id="tab-security-btn" data-bs-toggle="pill" data-bs-target="#tab-profile-security" type="button" role="tab">
                            <i class="fa fa-key"></i>
                            <span>Security &amp; Password</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo ($activeTab === 'preferences') ? 'active' : ''; ?>" id="tab-preferences-btn" data-bs-toggle="pill" data-bs-target="#tab-profile-preferences" type="button" role="tab">
                            <i class="fa fa-sliders"></i>
                            <span>Preferences</span>
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Panels -->
            <div class="tab-content" id="profileTabsContent">
                <!-- --------------------------------------------------------- -->
                <!-- TAB 1: PERSONAL DETAILS & BIO                             -->
                <!-- --------------------------------------------------------- -->
                <div class="tab-pane fade <?php echo ($activeTab === 'info') ? 'show active' : ''; ?>" id="tab-profile-info" role="tabpanel">
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="fa fa-user-edit text-primary me-2"></i>Personal Identification &amp; Contact
                                </h5>
                                <small class="text-muted">Update your public display name, email, phone number, and institutional bio.</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                                Real-time Sync
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo URLROOT; ?>/profile/index" method="post" enctype="multipart/form-data" class="ajax-profile-form no-pjax" data-no-pjax="true" id="profileInfoForm">
                                <input type="hidden" name="action" value="update_profile">
                                <input type="hidden" name="tab" value="info">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                                <!-- Hidden File Input for Avatar (Triggered by hero avatar button) -->
                                <input type="file" name="avatar" id="avatarFileInput" class="d-none" accept="image/jpeg,image/png,image/webp,image/gif">

                                <div class="row g-3">
                                    <!-- Full Name -->
                                    <div class="col-md-7">
                                        <label class="form-label text-dark fw-bold small mb-1">
                                            <i class="fa fa-user text-primary me-1"></i> Full Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-user text-muted"></i></span>
                                            <input type="text" name="name" id="inputProfileName" class="form-control border-start-0 ps-0" required value="<?php echo htmlspecialchars($user->name ?? ''); ?>" placeholder="Enter your full legal name">
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.76rem;">This name will appear on student reports, staff memos, and signatures.</div>
                                    </div>

                                    <!-- System Role (Read Only) -->
                                    <div class="col-md-5">
                                        <label class="form-label text-dark fw-bold small mb-1">
                                            <i class="fa fa-shield-alt text-warning me-1"></i> System Role
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-lock text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0 ps-0 bg-light text-muted fw-bold" value="<?php echo htmlspecialchars($roleName); ?>" readonly>
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.76rem;">Managed by Super Administrator security policy.</div>
                                    </div>

                                    <!-- Email Address -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-bold small mb-1">
                                            <i class="fa fa-envelope text-info me-1"></i> Login Email Address <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-at text-muted"></i></span>
                                            <input type="email" name="email" id="inputProfileEmail" class="form-control border-start-0 ps-0" required value="<?php echo htmlspecialchars($user->email ?? ''); ?>" placeholder="name@school.com">
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.76rem;">Primary identifier used for account authentication and reset PINs.</div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-bold small mb-1">
                                            <i class="fa fa-phone text-success me-1"></i> Contact / Mobile Number
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-phone-volume text-muted"></i></span>
                                            <input type="text" name="phone" id="inputProfilePhone" class="form-control border-start-0 ps-0" value="<?php echo htmlspecialchars($user->phone ?? ''); ?>" placeholder="+92 300 1234567">
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.76rem;">Used for urgent institutional notifications and WhatsApp alerts.</div>
                                    </div>

                                    <!-- Professional Bio -->
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-bold small mb-1">
                                            <i class="fa fa-align-left text-secondary me-1"></i> Professional Bio / Description
                                        </label>
                                        <textarea name="bio" id="inputProfileBio" class="form-control" rows="3" placeholder="Write a short summary about your academic background, experience, or department..."><?php echo htmlspecialchars($user->bio ?? ''); ?></textarea>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <span class="form-text text-muted" style="font-size: 0.76rem;">Brief institutional summary shown on the faculty dossier.</span>
                                            <span class="text-muted" style="font-size: 0.75rem;" id="bioCharCount">0 / 300 chars</span>
                                        </div>
                                    </div>

                                    <!-- Residential / Office Address -->
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-bold small mb-1">
                                            <i class="fa fa-map-pin text-danger me-1"></i> Residential / Campus Address
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-building text-muted"></i></span>
                                            <input type="text" name="address" id="inputProfileAddress" class="form-control border-start-0 ps-0" value="<?php echo htmlspecialchars($user->address ?? ''); ?>" placeholder="e.g. Office Suite 204, Executive Admin Block, Main Campus">
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                                    <span class="text-muted small">
                                        <i class="fa fa-circle-info text-primary me-1"></i> Profile changes reflect instantly across all active sessions.
                                    </span>
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm rounded-pill" id="saveProfileInfoBtn">
                                        <i class="fa fa-floppy-disk me-2"></i>Save Profile Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- --------------------------------------------------------- -->
                <!-- TAB 2: SECURITY & PASSWORD CHANGE                         -->
                <!-- --------------------------------------------------------- -->
                <div class="tab-pane fade <?php echo ($activeTab === 'security') ? 'show active' : ''; ?>" id="tab-profile-security" role="tabpanel">
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="fa fa-key text-warning me-2"></i>Update Account Credentials
                                </h5>
                                <small class="text-muted">Ensure your password is strong and unique to protect institutional data.</small>
                            </div>
                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1 rounded-pill">
                                <i class="fa fa-shield-alt me-1"></i>Bcrypt Enforced
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo URLROOT; ?>/profile/index" method="post" class="ajax-profile-form no-pjax" data-no-pjax="true" id="profilePasswordForm">
                                <input type="hidden" name="action" value="change_password">
                                <input type="hidden" name="tab" value="security">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                                <div class="row g-3">
                                    <!-- Current Password -->
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-bold small mb-1">
                                            <i class="fa fa-lock text-secondary me-1"></i> Current Login Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-key text-muted"></i></span>
                                            <input type="password" name="current_password" id="current_password" class="form-control border-start-0 ps-0" required placeholder="Enter your current password">
                                            <button class="btn btn-light border border-start-0 toggle-pass-btn text-muted" type="button" tabindex="-1">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.76rem;">Identity verification required prior to rotating credentials.</div>
                                    </div>

                                    <!-- New Password -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-bold small mb-1">
                                            <i class="fa fa-shield-halved text-primary me-1"></i> New Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-lock text-muted"></i></span>
                                            <input type="password" name="new_password" id="new_password" class="form-control border-start-0 ps-0" required minlength="6" placeholder="Create a strong password">
                                            <button class="btn btn-light border border-start-0 toggle-pass-btn text-muted" type="button" tabindex="-1">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                        <!-- Dynamic Password Strength Bar -->
                                        <div class="password-meter-bar">
                                            <div class="password-meter-fill" id="passMeterFill"></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                            <span class="text-muted" style="font-size: 0.75rem;">Password Strength:</span>
                                            <span class="fw-bold" style="font-size: 0.75rem;" id="passStrengthText">Too Short</span>
                                        </div>
                                    </div>

                                    <!-- Confirm New Password -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-bold small mb-1">
                                            <i class="fa fa-check-double text-success me-1"></i> Confirm New Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-lock text-muted"></i></span>
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control border-start-0 ps-0" required minlength="6" placeholder="Confirm your new password">
                                            <button class="btn btn-light border border-start-0 toggle-pass-btn text-muted" type="button" tabindex="-1">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="mt-1" id="passMatchIndicator" style="font-size: 0.75rem; display: none;"></div>
                                    </div>

                                    <!-- Interactive Password Strength Checklist -->
                                    <div class="col-12 mt-3">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <h6 class="fw-bold text-dark small mb-2"><i class="fa fa-list-check text-primary me-1"></i> Recommended Password Standards</h6>
                                            <div class="row g-2">
                                                <div class="col-sm-6">
                                                    <div class="strength-rule" id="rule-length"><i class="fa fa-circle-xmark text-muted"></i> At least 8 characters</div>
                                                    <div class="strength-rule" id="rule-number"><i class="fa fa-circle-xmark text-muted"></i> Contains numbers (0-9)</div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="strength-rule" id="rule-case"><i class="fa fa-circle-xmark text-muted"></i> Upper &amp; lower case letters</div>
                                                    <div class="strength-rule" id="rule-symbol"><i class="fa fa-circle-xmark text-muted"></i> Special symbols (@, $, #, !, etc.)</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                                    <span class="text-muted small">
                                        <i class="fa fa-shield-alt text-success me-1"></i> Old sessions are automatically invalidated upon password reset.
                                    </span>
                                    <button type="submit" class="btn btn-warning text-dark px-4 py-2 fw-bold shadow-sm rounded-pill" id="savePasswordBtn">
                                        <i class="fa fa-check-circle me-2"></i>Update Password Now
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- --------------------------------------------------------- -->
                <!-- TAB 3: SYSTEM PREFERENCES & NOTIFICATIONS                 -->
                <!-- --------------------------------------------------------- -->
                <div class="tab-pane fade <?php echo ($activeTab === 'preferences') ? 'show active' : ''; ?>" id="tab-profile-preferences" role="tabpanel">
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">
                                    <i class="fa fa-sliders text-info me-2"></i>System &amp; Notification Preferences
                                </h5>
                                <small class="text-muted">Customize how you receive operational alerts, notices, and dashboard views.</small>
                            </div>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill">
                                User Config
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex flex-column gap-3">
                                <!-- Notification Channel 1 -->
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="btn btn-primary-subtle text-primary rounded-circle p-2" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-envelope fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Email Dispatch Alerts</h6>
                                            <small class="text-muted">Receive email digests for fee deposits, academic circulars, and system warnings.</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-5 mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="toggleEmailAlerts" checked>
                                    </div>
                                </div>

                                <!-- Notification Channel 2 -->
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="btn btn-success-subtle text-success rounded-circle p-2" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-brands fa-whatsapp fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">WhatsApp &amp; SMS Broadcasts</h6>
                                            <small class="text-muted">Receive critical attendance anomalies and emergency closure alerts on mobile.</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-5 mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="toggleSmsAlerts" checked>
                                    </div>
                                </div>

                                <!-- UI Density -->
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="btn btn-warning-subtle text-warning rounded-circle p-2" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-table-cells fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">High-Density Table View</h6>
                                            <small class="text-muted">Compact student and fee ledger records to view more rows per page.</small>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-5 mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="toggleCompactTables">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <i class="fa fa-check-circle text-success me-1"></i> Settings are saved automatically to your user profile.
                                </span>
                                <button type="button" class="btn btn-outline-primary px-4 rounded-pill fw-bold" onclick="if(typeof window.showToast==='function') window.showToast('Preferences updated!', 'success');">
                                    <i class="fa fa-check me-2"></i>Save Preferences
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    // 1. Copy to Clipboard Helper
    window.copyToClipboard = function(text, successMsg) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            if (typeof window.showToast === 'function') {
                window.showToast(successMsg || 'Copied to clipboard!', 'success');
            } else {
                alert(successMsg || 'Copied to clipboard!');
            }
        }).catch(err => {
            console.error('Clipboard copy failed:', err);
        });
    };

    function initProfilePage() {
        // 2. Real-time Avatar File Preview & Auto-Trigger
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

                    // Auto-submit profile form when new avatar photo is selected
                    const form = document.getElementById('profileInfoForm');
                    if (form) {
                        setTimeout(() => {
                            if (typeof window.showToast === 'function') {
                                window.showToast('Uploading new avatar photo...', 'info');
                            }
                            const submitEvent = new Event('submit', { cancelable: true, bubbles: true });
                            form.dispatchEvent(submitEvent);
                        }, 250);
                    }
                }
            });
        }

        // 3. Password Visibility Toggles
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

        // 4. Real-time Password Strength Meter & Interactive Rules
        const newPassInput = document.getElementById('new_password');
        const confirmPassInput = document.getElementById('confirm_password');
        const passMeterFill = document.getElementById('passMeterFill');
        const passStrengthText = document.getElementById('passStrengthText');
        const matchIndicator = document.getElementById('passMatchIndicator');

        const ruleLength = document.getElementById('rule-length');
        const ruleNumber = document.getElementById('rule-number');
        const ruleCase = document.getElementById('rule-case');
        const ruleSymbol = document.getElementById('rule-symbol');

        function updateRule(el, passed) {
            if (!el) return;
            if (passed) {
                el.classList.add('passed');
                el.querySelector('i').className = 'fa fa-circle-check text-success';
            } else {
                el.classList.remove('passed');
                el.querySelector('i').className = 'fa fa-circle-xmark text-muted';
            }
        }

        if (newPassInput) {
            newPassInput.addEventListener('input', function() {
                const val = this.value;
                let score = 0;

                const hasLen = val.length >= 8;
                const hasNum = /\d/.test(val);
                const hasCase = /[a-z]/.test(val) && /[A-Z]/.test(val);
                const hasSym = /[^a-zA-Z0-9]/.test(val);

                updateRule(ruleLength, hasLen);
                updateRule(ruleNumber, hasNum);
                updateRule(ruleCase, hasCase);
                updateRule(ruleSymbol, hasSym);

                if (hasLen) score += 25;
                if (hasNum) score += 25;
                if (hasCase) score += 25;
                if (hasSym) score += 25;

                if (!val) {
                    passMeterFill.style.width = '0%';
                    passStrengthText.textContent = 'Too Short';
                    passStrengthText.className = 'fw-bold text-muted';
                } else if (score <= 25) {
                    passMeterFill.style.width = '25%';
                    passMeterFill.style.backgroundColor = '#ef4444';
                    passStrengthText.textContent = 'Weak';
                    passStrengthText.className = 'fw-bold text-danger';
                } else if (score <= 50) {
                    passMeterFill.style.width = '50%';
                    passMeterFill.style.backgroundColor = '#f59e0b';
                    passStrengthText.textContent = 'Fair';
                    passStrengthText.className = 'fw-bold text-warning';
                } else if (score <= 75) {
                    passMeterFill.style.width = '75%';
                    passMeterFill.style.backgroundColor = '#3b82f6';
                    passStrengthText.textContent = 'Good';
                    passStrengthText.className = 'fw-bold text-primary';
                } else {
                    passMeterFill.style.width = '100%';
                    passMeterFill.style.backgroundColor = '#10b981';
                    passStrengthText.textContent = 'Strong (Excellent)';
                    passStrengthText.className = 'fw-bold text-success';
                }

                checkPasswordMatch();
            });
        }

        function checkPasswordMatch() {
            if (!confirmPassInput || !newPassInput || !matchIndicator) return;
            const newP = newPassInput.value;
            const confP = confirmPassInput.value;

            if (!confP) {
                matchIndicator.style.display = 'none';
                return;
            }

            matchIndicator.style.display = 'block';
            if (newP === confP) {
                matchIndicator.innerHTML = '<span class="text-success fw-bold"><i class="fa fa-circle-check me-1"></i> Passwords match perfectly.</span>';
            } else {
                matchIndicator.innerHTML = '<span class="text-danger fw-bold"><i class="fa fa-circle-xmark me-1"></i> Passwords do not match.</span>';
            }
        }

        if (confirmPassInput) {
            confirmPassInput.addEventListener('input', checkPasswordMatch);
        }

        // 5. Bio Character Counter
        const bioInput = document.getElementById('inputProfileBio');
        const bioCharCount = document.getElementById('bioCharCount');
        if (bioInput && bioCharCount) {
            const updateBioCount = () => {
                bioCharCount.textContent = `${bioInput.value.length} / 300 chars`;
            };
            bioInput.addEventListener('input', updateBioCount);
            updateBioCount();
        }

        // 6. Tab URL Synchronization without Page Reload
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

        // 7. Smooth High-Performance AJAX Form Submissions
        document.querySelectorAll('.ajax-profile-form').forEach(form => {
            if (form._hasAjax) return;
            form._hasAjax = true;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Check password match client-side if submitting password form
                if (form.id === 'profilePasswordForm') {
                    const p1 = document.getElementById('new_password')?.value;
                    const p2 = document.getElementById('confirm_password')?.value;
                    if (p1 && p2 && p1 !== p2) {
                        alert('New password and confirmation do not match.');
                        return;
                    }
                }

                const submitBtn = form.querySelector('button[type="submit"]:not([disabled])');
                if (!submitBtn) return;

                const originalHtml = submitBtn.innerHTML;

                try {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Processing...';

                    const formData = new FormData(form);
                    formData.append('ajax_submit', '1');
                    const formToken = form.querySelector('input[name="csrf_token"]')?.value 
                                   || window.CSRF_TOKEN 
                                   || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                                   || '';
                    if (formToken) {
                        formData.set('csrf_token', formToken);
                    }

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

                    // Keep CSRF token in sync
                    const syncToken = response.headers.get('X-CSRF-Token') || result?.csrf_token;
                    if (syncToken) {
                        window.CSRF_TOKEN = syncToken;
                        document.querySelectorAll('input[name="csrf_token"]').forEach(inp => inp.value = syncToken);
                    }

                    if (result && result.success) {
                        if (typeof window.showToast === 'function') {
                            window.showToast(result.message || 'Profile saved successfully!', 'success');
                        }

                        // Update Hero Banner & Left Profile Card in real time
                        if (result.user_name) {
                            const heroName = document.getElementById('heroDisplayName');
                            const cardName = document.getElementById('profileCardName');
                            if (heroName) heroName.textContent = result.user_name;
                            if (cardName) cardName.textContent = result.user_name;

                            // Update topbar username
                            const topUserName = document.querySelector('.top-navbar .user-name');
                            if (topUserName) topUserName.textContent = result.user_name;
                        }

                        if (result.user_email) {
                            const heroEmail = document.getElementById('heroDisplayEmail');
                            const cardEmail = document.getElementById('profileCardEmail');
                            if (heroEmail) heroEmail.textContent = result.user_email;
                            if (cardEmail) cardEmail.textContent = result.user_email;
                        }

                        const inputPhone = document.getElementById('inputProfilePhone');
                        const cardPhone = document.getElementById('profileCardPhone');
                        if (inputPhone && cardPhone) {
                            cardPhone.textContent = inputPhone.value || 'Not configured';
                        }

                        const inputAddress = document.getElementById('inputProfileAddress');
                        const cardAddress = document.getElementById('profileCardAddress');
                        if (inputAddress && cardAddress) {
                            cardAddress.textContent = inputAddress.value || 'Not configured';
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
                            if (passMeterFill) passMeterFill.style.width = '0%';
                            if (passStrengthText) {
                                passStrengthText.textContent = 'Too Short';
                                passStrengthText.className = 'fw-bold text-muted';
                            }
                            if (matchIndicator) matchIndicator.style.display = 'none';
                            updateRule(ruleLength, false);
                            updateRule(ruleNumber, false);
                            updateRule(ruleCase, false);
                            updateRule(ruleSymbol, false);
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
