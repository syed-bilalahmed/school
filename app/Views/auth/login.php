<?php require APPROOT . '/Views/layouts/auth_header.php'; ?>
<style>
    .auth-page { 
        min-height: 100vh; 
        display: grid; 
        place-items: center; 
        padding: 40px 16px; 
        background: linear-gradient(135deg, #f0f4fc 0%, #e2ebf8 100%); 
    }
    .auth-shell { 
        width: min(100%, 1040px); 
    }
    .auth-card { 
        display: grid; 
        grid-template-columns: 0.85fr 1.15fr; 
        border: 0; 
        border-radius: 24px; 
        overflow: hidden; 
        box-shadow: 0 25px 70px rgba(15, 23, 42, .12), 0 4px 12px rgba(15, 23, 42, .04); 
        background: #fff;
    }
    .auth-brand { 
        display: flex; 
        flex-direction: column; 
        justify-content: space-between; 
        min-height: 580px; 
        padding: 46px 40px; 
        color: #fff; 
        background: linear-gradient(155deg, #0b2e73 0%, #1769e0 60%, #1e40af 100%); 
        position: relative;
    }
    .auth-brand::after {
        content: '';
        position: absolute;
        bottom: -50px;
        right: -50px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        pointer-events: none;
    }
    .auth-brand-icon { 
        width: 68px; 
        height: 68px; 
        display: grid; 
        place-items: center; 
        border-radius: 18px; 
        background: rgba(255,255,255,.16); 
        font-size: 1.5rem; 
        overflow: hidden; 
    }
    .auth-brand-icon img { 
        width: 100%; 
        height: 100%; 
        padding: 8px; 
        object-fit: contain; 
        background: #fff; 
    }
    .auth-brand-mark { 
        letter-spacing: .08em; 
        font-size: .78rem; 
        font-weight: 700; 
        text-transform: uppercase; 
        color: rgba(255,255,255,.75); 
    }
    .auth-brand h1 { 
        max-width: 320px; 
        font-size: 2.1rem; 
        font-weight: 800;
        line-height: 1.15; 
        letter-spacing: -0.02em;
    }
    .auth-brand-list { 
        padding: 0; 
        margin: 0; 
        list-style: none; 
        color: rgba(255,255,255,.8); 
    }
    .auth-brand-list li { 
        margin-top: 12px; 
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.88rem;
    }
    .auth-body { 
        padding: 44px 48px; 
        background: #fff; 
    }
    .auth-body .form-control { 
        min-height: 48px; 
        border-radius: 10px; 
        border-color: #dbe3ef; 
        font-size: 0.93rem;
    }
    .auth-body .form-control:focus { 
        border-color: #1769e0; 
        box-shadow: 0 0 0 4px rgba(23, 105, 224, .12); 
    }
    .password-wrap { 
        position: relative; 
    }
    .password-wrap .form-control { 
        padding-right: 46px; 
    }
    .password-toggle { 
        position: absolute; 
        top: 50%; 
        right: 12px; 
        border: 0; 
        padding: 4px; 
        color: #64748b; 
        background: transparent; 
        transform: translateY(-50%); 
        cursor: pointer;
    }

    /* Role Quick Selector */
    .role-quick-panel {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 18px;
        margin-bottom: 22px;
    }
    .role-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .role-panel-title {
        font-size: 0.82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .role-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 8px;
    }
    .role-chip {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        text-align: left;
        user-select: none;
    }
    .role-chip:hover {
        border-color: #93c5fd;
        background: #f0f7ff;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.08);
    }
    .role-chip.active {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }
    .role-chip-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        font-size: 0.88rem;
        flex-shrink: 0;
        color: #fff;
    }
    .role-chip-text {
        overflow: hidden;
    }
    .role-chip-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        line-height: 1.2;
    }
    .role-chip-desc {
        font-size: 0.68rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        line-height: 1.2;
    }
    .role-active-toast {
        display: none;
        font-size: 0.78rem;
        padding: 6px 10px;
        border-radius: 8px;
        margin-top: 10px;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
        animation: fadeIn 0.3s ease;
    }

    .divider-text {
        display: flex;
        align-items: center;
        text-align: center;
        color: #94a3b8;
        font-size: 0.76rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin: 18px 0;
    }
    .divider-text::before,
    .divider-text::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }
    .divider-text:not(:empty)::before {
        margin-right: 12px;
    }
    .divider-text:not(:empty)::after {
        margin-left: 12px;
    }

    @media (max-width: 860px) {
        .auth-card { display: block; }
        .auth-brand { min-height: auto; padding: 30px; }
        .auth-brand h1 { font-size: 1.7rem; }
        .auth-brand-list { display: none; }
        .auth-body { padding: 28px 20px; }
        .role-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<div class="auth-page">
    <div class="auth-shell">
        <div class="auth-card">
            <section class="auth-brand">
                <div>
                    <div class="auth-brand-icon mb-3">
                        <?php if(!empty($dynamicSchoolLogo)): ?>
                            <img src="<?php echo URLROOT . '/' . htmlspecialchars(ltrim($dynamicSchoolLogo, '/'), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($dynamicSchoolName, ENT_QUOTES, 'UTF-8'); ?> logo">
                        <?php else: ?>
                            <i class="fa fa-graduation-cap"></i>
                        <?php endif; ?>
                    </div>
                    <div class="auth-brand-mark mb-2"><?php echo htmlspecialchars($dynamicSchoolName, ENT_QUOTES, 'UTF-8'); ?></div>
                    <h1 class="mb-3">Dedicated Campus Portal Access.</h1>
                    <p class="mb-0 text-white-50 small">Secure, role-based unified environment for administration, faculty, learners, and families.</p>
                </div>
                <ul class="auth-brand-list small">
                    <li><i class="fa fa-circle-check text-warning"></i> Role-segregated workspaces</li>
                    <li><i class="fa fa-circle-check text-warning"></i> Encrypted authentication &amp; session security</li>
                    <li><i class="fa fa-circle-check text-warning"></i> Single-click institutional access</li>
                </ul>
                <div class="pt-3 border-top border-white border-opacity-10">
                    <a href="<?php echo URLROOT; ?>/" class="text-white text-decoration-none small opacity-75 hover-opacity-100">
                        <i class="fa fa-arrow-left me-1"></i> Back to School Website
                    </a>
                </div>
            </section>

            <section class="auth-body">
                <?php if(isset($_SESSION['user_id'])): 
                    $currRole = $_SESSION['user_role'] ?? 'admin';
                    $currDash = URLROOT . '/admin/dashboard';
                    if ($currRole === 'teacher') $currDash = URLROOT . '/teacher/index';
                    elseif ($currRole === 'student') $currDash = URLROOT . '/student/index';
                    elseif ($currRole === 'parent') $currDash = URLROOT . '/parent/index';
                    elseif ($currRole === 'receptionist') $currDash = URLROOT . '/frontoffice/index';
                    elseif ($currRole === 'accountant') $currDash = URLROOT . '/fees/collect';
                    elseif ($currRole === 'librarian') $currDash = URLROOT . '/library/index';
                ?>
                    <div class="alert alert-primary d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 py-2 px-3 mb-3 border-0 shadow-sm" style="border-radius: 12px; background: #e0f2fe; color: #0369a1; font-size: 0.84rem;">
                        <div>
                            <i class="fa fa-circle-check me-1 text-success"></i>
                            Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong> (<?php echo htmlspecialchars(ucfirst($currRole)); ?>)
                        </div>
                        <div class="d-flex gap-2">
                            <a href="<?php echo $currDash; ?>" class="btn btn-sm btn-primary py-1 px-3 fw-semibold" style="font-size: 0.78rem; border-radius: 6px;">
                                <i class="fa fa-gauge me-1"></i> Dashboard
                            </a>
                            <a href="<?php echo URLROOT; ?>/auth/logout" class="btn btn-sm btn-outline-danger py-1 px-2 fw-semibold" style="font-size: 0.78rem; border-radius: 6px;">
                                <i class="fa fa-arrow-right-from-bracket me-1"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php 
                    $loginActiveSchoolName = $_SESSION['school_name'] ?? (TenantContext::getSchoolCode() !== 'default' ? TenantContext::getSchoolCode() : ($dynamicSchoolName ?? 'Main Campus'));
                    $availableBranches = [];
                    try {
                        $dbBr = new Database();
                        $dbBr->query("SELECT id, name, code FROM schools WHERE status = 'active' ORDER BY id ASC");
                        $availableBranches = $dbBr->resultSet() ?: [];
                    } catch (Throwable $e) {}
                ?>
                <div class="d-flex align-items-center justify-content-between p-2.5 px-3 mb-3 rounded-3 border bg-light shadow-sm">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa fa-school text-primary fs-6"></i>
                        <div>
                            <span class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.1;">CAMPUS / BRANCH PORTAL</span>
                            <strong class="text-dark fs-6"><?php echo htmlspecialchars($loginActiveSchoolName); ?></strong>
                        </div>
                    </div>
                    <?php if(count($availableBranches) > 1): ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary py-1 px-2.5 dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown" style="font-size: 0.75rem; border-radius: 6px;">
                                <i class="fa fa-code-branch me-1"></i>Switch Campus
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 220px;">
                                <li class="dropdown-header small text-muted text-uppercase fw-bold" style="font-size:0.7rem;">Select Campus Portal</li>
                                <?php foreach($availableBranches as $br): 
                                    $isSel = ((isset($_SESSION['school_id']) && (int)$_SESSION['school_id'] === (int)$br->id) || TenantContext::getSchoolCode() === $br->code);
                                ?>
                                    <li>
                                        <a class="dropdown-item py-2 small d-flex align-items-center justify-content-between <?php echo $isSel ? 'active fw-bold' : ''; ?>" href="<?php echo URLROOT; ?>/auth/login?branch=<?php echo htmlspecialchars($br->code); ?>">
                                            <span><?php echo htmlspecialchars($br->name); ?></span>
                                            <?php if($isSel): ?>
                                                <i class="fa fa-check ms-2"></i>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-2 py-1 mb-2" style="font-size: 0.72rem; letter-spacing: 0.05em;">Authorized Access</span>
                    <h2 class="h4 fw-bold text-dark mb-1">Sign in to your account</h2>
                    <p class="text-muted small mb-0">Sign in with your branch credentials. Administrators &amp; staff are automatically authenticated to their campus.</p>
                </div>

                <!-- 1-Click Role Credentials Selector (Strictly Excluding Super Admin) -->
                <?php
                $roleList = [
                    'admin' => [
                        'label' => 'School Admin',
                        'desc' => 'Principal Lead',
                        'email' => 'admin@school.com',
                        'password' => '123456',
                        'icon' => 'fa-shield-halved',
                        'color' => '#2563eb'
                    ],
                    'teacher' => [
                        'label' => 'Teacher',
                        'desc' => 'Faculty & Academics',
                        'email' => 'teacher@school.com',
                        'password' => '123456',
                        'icon' => 'fa-chalkboard-user',
                        'color' => '#059669'
                    ],
                    'student' => [
                        'label' => 'Student',
                        'desc' => 'Learner Hub',
                        'email' => 'student@school.com',
                        'password' => '123456',
                        'icon' => 'fa-user-graduate',
                        'color' => '#0284c7'
                    ],
                    'parent' => [
                        'label' => 'Parent',
                        'desc' => 'Guardian Portal',
                        'email' => 'parent@school.com',
                        'password' => '123456',
                        'icon' => 'fa-people-roof',
                        'color' => '#d97706'
                    ],
                    'accountant' => [
                        'label' => 'Accountant',
                        'desc' => 'Fees & Billing',
                        'email' => 'accountant@school.com',
                        'password' => '123456',
                        'icon' => 'fa-file-invoice-dollar',
                        'color' => '#0f766e'
                    ],
                    'receptionist' => [
                        'label' => 'Receptionist',
                        'desc' => 'Front Office Desk',
                        'email' => 'receptionist@school.com',
                        'password' => '123456',
                        'icon' => 'fa-headset',
                        'color' => '#e11d48'
                    ],
                    'librarian' => [
                        'label' => 'Librarian',
                        'desc' => 'Books & Media',
                        'email' => 'librarian@school.com',
                        'password' => '123456',
                        'icon' => 'fa-book-bookmark',
                        'color' => '#7c3aed'
                    ]
                ];
                ?>

                <div class="role-quick-panel">
                    <div class="role-panel-header">
                        <span class="role-panel-title">
                            <i class="fa fa-layer-group text-primary"></i> Select Your Portal
                        </span>
                        <span class="text-muted" style="font-size: 0.72rem;">Choose your role to sign in</span>
                    </div>

                    <div class="role-grid">
                        <?php foreach($roleList as $rKey => $r): ?>
                            <button type="button" 
                                    class="role-chip" 
                                    data-role="<?php echo $rKey; ?>"
                                    data-email="<?php echo htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-pass="<?php echo htmlspecialchars($r['password'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-label="<?php echo htmlspecialchars($r['label'], ENT_QUOTES, 'UTF-8'); ?>"
                                    title="Select <?php echo htmlspecialchars($r['label'], ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="role-chip-icon" style="background: <?php echo $r['color']; ?>;">
                                    <i class="fa <?php echo $r['icon']; ?>"></i>
                                </div>
                                <div class="role-chip-text">
                                    <span class="role-chip-label"><?php echo htmlspecialchars($r['label']); ?></span>
                                    <span class="role-chip-desc"><?php echo htmlspecialchars($r['desc']); ?></span>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div id="roleActiveToast" class="role-active-toast" style="display: none;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <span><i class="fa fa-circle-check me-1 text-success"></i> <span id="roleActiveText">Role selected. Please enter your credentials.</span></span>
                            <button type="button" id="btnFillDemoCreds" class="btn btn-sm btn-link p-0 text-decoration-none fw-semibold" style="font-size: 0.75rem; color: #166534;">
                                <i class="fa fa-wand-magic-sparkles me-1"></i>Fill demo credentials
                            </button>
                        </div>
                    </div>
                </div>

                <div class="divider-text">Enter Login Credentials</div>

                <?php if(!empty($data['email_err']) || !empty($data['password_err'])): ?>
                    <div class="alert alert-danger d-flex gap-2 align-items-start small py-2 px-3 mb-3">
                        <i class="fa fa-circle-exclamation mt-1"></i>
                        <span><?php echo htmlspecialchars(!empty($data['email_err']) ? $data['email_err'] : $data['password_err']); ?></span>
                    </div>
                <?php endif; ?>

                <form id="authLoginForm" action="<?php echo URLROOT; ?>/auth/login" method="post" class="no-pjax" data-no-pjax="true">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary" for="loginEmail">Email address / Username</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-envelope"></i></span>
                            <input id="loginEmail" type="text" name="email" class="form-control border-start-0 ps-0" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" placeholder="Enter your email or username" autocomplete="username" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold small text-secondary mb-0" for="loginPassword">Password</label>
                            <a href="<?php echo URLROOT; ?>/auth/forgotPassword" class="small text-decoration-none" style="font-size: 0.78rem;">Forgot password?</a>
                        </div>
                        <div class="input-group password-wrap">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-lock"></i></span>
                            <input id="loginPassword" type="password" name="password" class="form-control border-start-0 ps-0" placeholder="Enter your password" autocomplete="current-password" required>
                            <button type="button" class="password-toggle" id="toggleLoginPassword" aria-label="Show password"><i class="fa fa-eye"></i></button>
                        </div>
                    </div>

                    <button type="submit" id="btnSubmitLogin" class="btn btn-primary w-100 py-2 mt-2 fw-bold" style="border-radius: 10px; font-size: 0.95rem;">
                        <i class="fa fa-arrow-right-to-bracket me-2"></i>Sign In
                    </button>
                </form>
            </section>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('loginEmail');
    const passInput = document.getElementById('loginPassword');
    const toast = document.getElementById('roleActiveToast');
    const toastText = document.getElementById('roleActiveText');
    const btnFillDemo = document.getElementById('btnFillDemoCreds');
    const roleChips = document.querySelectorAll('.role-chip');
    const toggle = document.getElementById('toggleLoginPassword');
    let activeRoleChip = null;

    // Role Selection Handler (Does NOT auto-fill email/password, keeps fields clean for user)
    roleChips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            activeRoleChip = this;
            const label = this.getAttribute('data-label');
            const role = this.getAttribute('data-role');

            // Update active visual state
            roleChips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            // Set contextual placeholder without filling actual value
            if (emailInput) {
                if (role === 'student') {
                    emailInput.placeholder = 'student@school.com or Admission No';
                } else if (role === 'teacher') {
                    emailInput.placeholder = 'teacher@school.com or Staff Email';
                } else if (role === 'parent') {
                    emailInput.placeholder = 'parent@school.com or Phone Number';
                } else {
                    emailInput.placeholder = 'Enter your ' + label + ' email';
                }
                emailInput.focus();
            }

            // Show selected portal info
            if (toast && toastText) {
                toastText.innerHTML = 'Selected: <strong>' + label + ' Portal</strong>. Please enter your credentials.';
                toast.style.display = 'block';
            }
        });
    });

    // Optional Demo Auto-fill: Only if user explicitly clicks the link
    if (btnFillDemo) {
        btnFillDemo.addEventListener('click', function(e) {
            e.preventDefault();
            if (activeRoleChip && emailInput && passInput) {
                const demoEmail = activeRoleChip.getAttribute('data-email');
                const demoPass = activeRoleChip.getAttribute('data-pass');
                const label = activeRoleChip.getAttribute('data-label');
                emailInput.value = demoEmail;
                passInput.value = demoPass;

                if (toastText) {
                    toastText.innerHTML = 'Demo credentials loaded for <strong>' + label + '</strong> (' + demoEmail + '). Click <strong>Sign In</strong>.';
                }
            }
        });
    }

    // Password Toggle
    if (toggle && passInput) {
        toggle.addEventListener('click', function() {
            const isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
            toggle.innerHTML = isPassword ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>';
            toggle.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        });
    }

    // URL Query Role Selection (Selects the role tab without auto-filling values into fields)
    const urlParams = new URLSearchParams(window.location.search);
    const requestedRole = urlParams.get('role');
    if (requestedRole) {
        const targetChip = document.querySelector(`.role-chip[data-role="${requestedRole}"]`);
        if (targetChip) {
            targetChip.click();
        }
    }
});
</script>
<?php require APPROOT . '/Views/layouts/auth_footer.php'; ?>
