<?php require APPROOT . '/Views/layouts/header.php'; ?>

<?php
$users = $data['users'] ?? [];
$stats = $data['stats'] ?? ['total' => count($users), 'admins' => 0, 'teachers' => 0, 'students' => 0, 'parents' => 0, 'staff' => 0];
$search = $data['search'] ?? '';
$roleFilter = $data['role'] ?? '';
$createdUser = $_SESSION['flash_created_user'] ?? null;
unset($_SESSION['flash_created_user']);

$roleIcons = [
    'super_admin' => 'fa-crown text-danger',
    'admin'       => 'fa-shield-halved text-primary',
    'teacher'     => 'fa-chalkboard-user text-info',
    'student'     => 'fa-user-graduate text-success',
    'parent'      => 'fa-people-roof text-dark',
    'accountant'  => 'fa-calculator text-warning',
    'librarian'   => 'fa-book text-purple',
    'receptionist'=> 'fa-headset text-secondary'
];

$roleBadges = [
    'super_admin' => 'bg-danger text-white',
    'admin'       => 'bg-primary text-white',
    'teacher'     => 'bg-info text-white',
    'student'     => 'bg-success text-white',
    'parent'      => 'bg-dark text-white',
    'accountant'  => 'bg-warning text-dark',
    'librarian'   => 'bg-purple text-white',
    'receptionist'=> 'bg-secondary text-white'
];
?>

<style>
/* Clean, Decent, Professional Table Layout - No Horizontal Scroll */
.users-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    overflow: hidden;
}
.table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}
.users-decent-table {
    width: 100% !important;
    min-width: 680px; /* Fits 100% on desktop, smoothly scrolls horizontally on mobile devices */
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}
.users-decent-table thead th {
    background: #f8fafc;
    color: #334155;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 13px 18px;
    border-bottom: 2px solid #e2e8f0;
    border-top: none;
    white-space: nowrap;
}
.users-decent-table tbody td {
    padding: 13px 18px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13.5px;
}
.users-decent-table tbody tr:hover {
    background-color: #f8fafc;
}
.users-decent-table tbody tr:last-child td {
    border-bottom: none;
}
.user-name-text {
    font-weight: 700;
    color: #0f172a;
    font-size: 14px;
    line-height: 1.3;
}
.user-campus-sub {
    font-size: 11.5px;
    color: #64748b;
    margin-top: 2px;
}
.user-email-code {
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 12.5px;
    font-weight: 600;
    color: #1e293b;
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
}
.user-role-chip {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}
.actions-cell {
    white-space: nowrap;
    text-align: right;
}
.kpi-mini-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 18px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    display: flex;
    align-items: center;
    gap: 14px;
}
.kpi-mini-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
</style>

<div class="users-page-wrap">

    <!-- Top Navigation & Action Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted"><i class="fa fa-home me-1"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Users Management</li>
                </ol>
            </nav>
            <h2 class="h3 fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="fa fa-users-cog text-primary"></i> All System Users Directory
                <span class="badge bg-primary fs-6 rounded-pill" id="userCountBadge"><?php echo count($users); ?> Total</span>
            </h2>
            <p class="text-secondary opacity-75 mb-0 small">
                Manage user login credentials, assign access roles, generate passwords, and update profiles across the institution.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#createUserModal" onclick="prepareCreateUserModal()">
                <i class="fa fa-user-plus me-1"></i> Create New User
            </button>
            <a href="<?php echo URLROOT; ?>/students/admission" class="btn btn-outline-primary btn-sm px-3 rounded-pill">
                <i class="fa fa-graduation-cap me-1"></i> Student Admission
            </a>
            <a href="<?php echo URLROOT; ?>/staff/create" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                <i class="fa fa-user-tie me-1"></i> Add Staff Member
            </a>
        </div>
    </div>

    <!-- Alert: Newly Created User Credentials Slip -->
    <?php if(!empty($createdUser)): ?>
        <div class="card border-0 mb-4 shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-radius: 14px;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 46px; height: 46px; flex-shrink: 0;">
                            <i class="fa fa-key"></i>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark text-uppercase mb-1" style="font-size: 11px;">
                                <i class="fa fa-check me-1"></i> Account Created Successfully
                            </span>
                            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($createdUser['name']); ?> (<?php echo ucfirst(htmlspecialchars($createdUser['role'])); ?>)</h5>
                            <div class="d-flex align-items-center gap-3 flex-wrap text-white-50 small">
                                <span>Login Email: <strong class="text-white font-monospace"><?php echo htmlspecialchars($createdUser['email']); ?></strong></span>
                                <span>Generated Password: <strong class="text-warning font-monospace bg-dark px-2 py-1 rounded border border-secondary"><?php echo htmlspecialchars($createdUser['password']); ?></strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning btn-sm px-3 fw-bold rounded-pill text-dark" onclick="copyLoginCredentials('<?php echo htmlspecialchars(addslashes($createdUser['email'])); ?>', '<?php echo htmlspecialchars(addslashes($createdUser['password'])); ?>')">
                            <i class="fa fa-copy me-1"></i> Copy Login Details
                        </button>
                    </div>
                </div>
                <div class="text-white-50 small mt-2 pt-2 border-top border-secondary">
                    <i class="fa fa-info-circle text-warning me-1"></i> <strong>Note:</strong> Share these credentials with the user. The user can change their password at any time from their profile settings after login.
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Standard Flash Alerts -->
    <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa fa-check-circle fs-5"></i>
            <div><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa fa-exclamation-circle fs-5"></i>
            <div><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- 4 KPI Mini Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-mini-card">
                <div class="kpi-mini-icon bg-primary-subtle text-primary">
                    <i class="fa fa-users"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0 text-dark"><?php echo $stats['total']; ?></div>
                    <small class="text-muted fw-semibold">Total Accounts</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-mini-card">
                <div class="kpi-mini-icon bg-danger-subtle text-danger">
                    <i class="fa fa-shield-halved"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0 text-dark"><?php echo $stats['admins']; ?></div>
                    <small class="text-muted fw-semibold">Administrators</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-mini-card">
                <div class="kpi-mini-icon bg-info-subtle text-info">
                    <i class="fa fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0 text-dark"><?php echo $stats['teachers']; ?></div>
                    <small class="text-muted fw-semibold">Teachers &amp; Faculty</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-mini-card">
                <div class="kpi-mini-icon bg-success-subtle text-success">
                    <i class="fa fa-user-graduate"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0 text-dark"><?php echo $stats['students']; ?></div>
                    <small class="text-muted fw-semibold">Students Active</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Filter & Search Toolbar (NO RELOAD - 100% INSTANT LIVE FILTER) -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <!-- Live Search Box -->
                <div class="col-12 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa fa-search"></i></span>
                        <input type="text" id="liveUserSearch" class="form-control border-start-0" placeholder="Type user name or email for instant search..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="button" class="btn btn-outline-secondary border-start-0" onclick="document.getElementById('liveUserSearch').value=''; applyLiveFilters();" title="Clear Search">
                            <i class="fa fa-times text-muted"></i>
                        </button>
                    </div>
                </div>

                <!-- Role Filter Dropdown (Instant Live Filter - No Page Reload) -->
                <div class="col-8 col-md-4">
                    <select id="filterRoleSelect" class="form-select form-select-sm" onchange="applyLiveFilters()">
                        <option value="">-- All System Roles (Instant Filter) --</option>
                        <option value="super_admin" <?php echo ($roleFilter === 'super_admin') ? 'selected' : ''; ?>>Super Administrator</option>
                        <option value="admin" <?php echo ($roleFilter === 'admin') ? 'selected' : ''; ?>>Administrator / Principal</option>
                        <option value="teacher" <?php echo ($roleFilter === 'teacher') ? 'selected' : ''; ?>>Teacher / Faculty</option>
                        <option value="student" <?php echo ($roleFilter === 'student') ? 'selected' : ''; ?>>Student</option>
                        <option value="parent" <?php echo ($roleFilter === 'parent') ? 'selected' : ''; ?>>Parent / Guardian</option>
                        <option value="accountant" <?php echo ($roleFilter === 'accountant') ? 'selected' : ''; ?>>Accountant / Finance</option>
                        <option value="librarian" <?php echo ($roleFilter === 'librarian') ? 'selected' : ''; ?>>Librarian</option>
                        <option value="receptionist" <?php echo ($roleFilter === 'receptionist') ? 'selected' : ''; ?>>Receptionist / Front Desk</option>
                    </select>
                </div>

                <!-- Reset Filters Button -->
                <div class="col-4 col-md-2 d-flex">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="resetLiveFilters()">
                        <i class="fa fa-rotate-left me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Decent, Simple, Professional Users Table (Zero Horizontal Scroll, No Avatar Initials) -->
    <div class="users-table-card">
        <div class="table-responsive">
            <table class="table users-decent-table">
            <thead>
                <tr>
                    <th style="width: 8%;"># ID</th>
                    <th style="width: 32%;">User Full Name &amp; Campus</th>
                    <th style="width: 28%;">Login Email / Username</th>
                    <th style="width: 17%;">Access Role</th>
                    <th class="text-end" style="width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <?php if(empty($users)): ?>
                    <tr id="emptyUsersRow">
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fa fa-user-slash fa-3x mb-3 text-secondary opacity-25"></i>
                            <p class="mb-0 fw-bold">No user accounts found in the database.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    foreach($users as $u): 
                        $rBadge = $roleBadges[$u->role] ?? 'bg-secondary text-white';
                        $rIcon = $roleIcons[$u->role] ?? 'fa-user text-muted';
                        $userJson = htmlspecialchars(json_encode([
                            'id' => (int)$u->id,
                            'name' => $u->name,
                            'email' => $u->email,
                            'role' => $u->role,
                            'school_name' => $u->school_name ?? 'Main Campus'
                        ]), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr class="user-row-item" 
                            data-name="<?php echo strtolower(htmlspecialchars($u->name)); ?>" 
                            data-email="<?php echo strtolower(htmlspecialchars($u->email)); ?>" 
                            data-role="<?php echo htmlspecialchars($u->role); ?>">
                            
                            <!-- Column 1: ID -->
                            <td class="text-muted fw-bold font-monospace" style="font-size: 12px;">
                                #<?php echo (int)$u->id; ?>
                            </td>

                            <!-- Column 2: User Name & Campus (Clean text, NO initials avatar like 'SB') -->
                            <td>
                                <div class="user-name-text"><?php echo htmlspecialchars($u->name); ?></div>
                                <div class="user-campus-sub">
                                    <i class="fa fa-school me-1 text-muted" style="font-size: 10px;"></i><?php echo htmlspecialchars($u->school_name ?? 'Main Executive Campus'); ?>
                                </div>
                            </td>

                            <!-- Column 3: Login Email / Username -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="user-email-code"><?php echo htmlspecialchars($u->email); ?></span>
                                    <button type="button" class="btn btn-sm btn-link text-muted p-0" title="Copy Login Email" onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars(addslashes($u->email)); ?>'); if(window.showToast) showToast('Email copied to clipboard');">
                                        <i class="fa fa-copy" style="font-size: 12px;"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Column 4: System Access Role -->
                            <td>
                                <span class="badge <?php echo $rBadge; ?> user-role-chip">
                                    <i class="fa <?php echo $rIcon; ?> text-white" style="font-size: 10px;"></i>
                                    <?php echo htmlspecialchars(str_replace('_', ' ', $u->role)); ?>
                                </span>
                            </td>

                            <!-- Column 5: Actions -->
                            <td class="actions-cell">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <!-- Profile & Password Setting Button -->
                                    <button type="button" class="btn btn-sm btn-primary px-2 py-1 rounded-pill" onclick='openEditUserModal(<?php echo $userJson; ?>)' title="Edit Name, Email, Role &amp; Password" style="font-size: 0.76rem;">
                                        <i class="fa fa-user-edit me-1"></i> Edit
                                    </button>

                                    <?php if($u->role === 'student'): ?>
                                        <a href="<?php echo URLROOT; ?>/students/index?search=<?php echo urlencode($u->name); ?>" class="btn btn-sm btn-outline-secondary px-2 py-1 rounded-pill" title="View in Students Directory" style="font-size: 0.76rem;">
                                            <i class="fa fa-graduation-cap"></i>
                                        </a>
                                    <?php elseif(in_array($u->role, ['teacher', 'accountant', 'librarian', 'receptionist'])): ?>
                                        <a href="<?php echo URLROOT; ?>/staff/index" class="btn btn-sm btn-outline-secondary px-2 py-1 rounded-pill" title="View in Staff Directory" style="font-size: 0.76rem;">
                                            <i class="fa fa-id-badge"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if((int)$u->id > 1 && (int)$u->id !== (int)($_SESSION['user_id'] ?? 0)): ?>
                                        <form action="<?php echo URLROOT; ?>/admin/deleteUser/<?php echo (int)$u->id; ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete user account \'<?php echo htmlspecialchars(addslashes($u->email)); ?>\'? This action cannot be undone.');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1 rounded-pill" title="Delete User Account" style="font-size: 0.76rem;">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" title="Primary Administrator Account (Protected)" style="font-size: 0.72rem;">
                                            <i class="fa fa-shield-alt text-primary"></i> Main
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Row shown when live filter finds no matches -->
                <tr id="noFilterMatchesRow" style="display: none;">
                    <td colspan="5" class="text-center text-muted py-5">
                        <i class="fa fa-search fa-2x mb-2 text-secondary opacity-25"></i>
                        <p class="mb-0 fw-bold">No user accounts match your search/filter criteria.</p>
                        <small>Click "Reset" above to show all system users.</small>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL 1: CREATE NEW USER ACCOUNT (WITH SYSTEM GENERATED PASSWORD)         -->
<!-- ========================================================================= -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa fa-user-plus fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="createUserModalLabel">Create System User</h5>
                        <small class="text-white-50">Register new portal user with auto-generated credentials</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?php echo URLROOT; ?>/admin/createUser" method="post" id="createUserForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                <div class="modal-body p-4">
                    <!-- Full Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="fa fa-user text-muted me-1"></i> Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="createInputName" class="form-control" required placeholder="e.g. Muhammad Aslam">
                    </div>

                    <!-- Login Email -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="fa fa-envelope text-muted me-1"></i> System Login Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email" id="createInputEmail" class="form-control font-monospace" required placeholder="user@school.edu.pk">
                        <small class="text-muted smaller">This email will be used as the login username</small>
                    </div>

                    <!-- System Access Role -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="fa fa-shield-alt text-muted me-1"></i> System Access Role <span class="text-danger">*</span>
                        </label>
                        <select name="role" id="createSelectRole" class="form-select" required>
                            <option value="teacher">Teacher / Faculty</option>
                            <option value="admin">Administrator / Principal</option>
                            <option value="accountant">Accountant / Finance</option>
                            <option value="librarian">Librarian</option>
                            <option value="receptionist">Receptionist / Front Desk</option>
                            <option value="student">Student</option>
                            <option value="parent">Parent / Guardian</option>
                            <option value="super_admin">Super Administrator</option>
                        </select>
                    </div>

                    <!-- Auto-Generated Password -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label small fw-bold text-dark mb-0">
                                <i class="fa fa-key text-muted me-1"></i> System Generated Password <span class="text-danger">*</span>
                            </label>
                            <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none fw-bold" onclick="generateNewPasswordForCreate()" style="font-size: 0.78rem;">
                                <i class="fa fa-magic me-1"></i> Generate Another
                            </button>
                        </div>
                        <div class="input-group">
                            <input type="text" name="password" id="createInputPassword" class="form-control font-monospace fw-bold text-primary" required style="letter-spacing: 1px;">
                            <button class="btn btn-outline-secondary" type="button" title="Copy Password" onclick="copyInputText('createInputPassword')">
                                <i class="fa fa-copy"></i>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" title="Show/Hide Password" onclick="togglePasswordVisibility('createInputPassword', this)">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        <div class="p-2 bg-light border rounded mt-2 small text-muted">
                            <i class="fa fa-lightbulb text-warning me-1"></i> <strong>Temporary Initial Password:</strong> The system has generated a secure password. You can copy and share it with the user. The user can change their password at any time from their profile settings.
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-3 px-4">
                    <button type="button" class="btn btn-secondary px-3 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">
                        <i class="fa fa-check-circle me-1"></i> Create User Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: USER PROFILE & PASSWORD SETTINGS MODAL                           -->
<!-- ========================================================================= -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="fa fa-user-cog fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="editUserModalLabel">User Profile &amp; Settings</h5>
                        <small class="text-white-50" id="modalUserSubtitle">Update credentials, name &amp; password</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?php echo URLROOT; ?>/admin/updateUser" method="post" id="editUserForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <input type="hidden" name="user_id" id="modalUserId" value="">

                <div class="modal-body p-4">
                    <!-- User Details Header -->
                    <div class="p-3 rounded-3 bg-light mb-3 border">
                        <h6 class="fw-bold mb-1 text-dark" id="modalUserCardName">User Name</h6>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary text-uppercase" id="modalUserRoleBadge" style="font-size: 0.7rem;">Role</span>
                            <small class="text-muted" id="modalUserIdBadge">ID: #0</small>
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="fa fa-user text-muted me-1"></i> Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="modalInputName" class="form-control" required placeholder="e.g. Ahmad Khan">
                    </div>

                    <!-- Email / Login ID -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="fa fa-envelope text-muted me-1"></i> System Login Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="email" id="modalInputEmail" class="form-control font-monospace" required placeholder="user@school.edu.pk">
                        <small class="text-muted smaller">Used by the user to sign in to the portal</small>
                    </div>

                    <!-- System Role -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">
                            <i class="fa fa-shield-alt text-muted me-1"></i> System Access Role <span class="text-danger">*</span>
                        </label>
                        <select name="role" id="modalSelectRole" class="form-select" required>
                            <option value="super_admin">Super Administrator</option>
                            <option value="admin">Administrator / Principal</option>
                            <option value="teacher">Teacher / Faculty</option>
                            <option value="student">Student</option>
                            <option value="parent">Parent / Guardian</option>
                            <option value="accountant">Accountant / Finance</option>
                            <option value="librarian">Librarian</option>
                            <option value="receptionist">Receptionist / Front Desk</option>
                        </select>
                        <small class="text-muted smaller" id="roleChangeNote">Assign system privileges &amp; portal dashboard view</small>
                    </div>

                    <!-- New Password / Reset -->
                    <div class="mb-2">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label small fw-bold text-dark mb-0">
                                <i class="fa fa-key text-muted me-1"></i> New Password / Reset
                            </label>
                            <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none fw-bold" onclick="generateRandomPasswordForEdit()" style="font-size: 0.78rem;">
                                <i class="fa fa-magic me-1"></i> Generate New Password
                            </button>
                        </div>
                        <div class="input-group">
                            <input type="password" name="password" id="modalInputPassword" class="form-control font-monospace" placeholder="Leave blank to keep unchanged" minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="copyInputText('modalInputPassword')">
                                <i class="fa fa-copy"></i>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('modalInputPassword', this)">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted smaller d-block mt-1">
                            <i class="fa fa-info-circle me-1"></i> Leave empty to keep the existing password. The user can change it anytime.
                        </small>
                    </div>
                </div>

                <div class="modal-footer bg-light py-3 px-4">
                    <button type="button" class="btn btn-secondary px-3 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">
                        <i class="fa fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. Instant Live Search & Role Filter (ZERO RELOAD)
    function applyLiveFilters() {
        var query = (document.getElementById('liveUserSearch') ? document.getElementById('liveUserSearch').value : '').toLowerCase().trim();
        var selectedRole = document.getElementById('filterRoleSelect') ? document.getElementById('filterRoleSelect').value : '';
        var rows = document.querySelectorAll('.user-row-item');
        var visibleCount = 0;

        rows.forEach(function(row) {
            var name = (row.getAttribute('data-name') || '').toLowerCase();
            var email = (row.getAttribute('data-email') || '').toLowerCase();
            var role = row.getAttribute('data-role') || '';

            var matchesQuery = !query || name.includes(query) || email.includes(query);
            var matchesRole = !selectedRole || role === selectedRole;

            if (matchesQuery && matchesRole) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Toggle empty message row
        var noMatchesRow = document.getElementById('noFilterMatchesRow');
        if (noMatchesRow) {
            noMatchesRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
        }

        // Update count badge
        var badge = document.getElementById('userCountBadge');
        if (badge) {
            badge.textContent = visibleCount + ' Shown';
        }
    }

    function resetLiveFilters() {
        if (document.getElementById('liveUserSearch')) {
            document.getElementById('liveUserSearch').value = '';
        }
        if (document.getElementById('filterRoleSelect')) {
            document.getElementById('filterRoleSelect').value = '';
        }
        applyLiveFilters();
    }

    // Attach real-time input listener on search box
    document.addEventListener('DOMContentLoaded', function() {
        var liveSearch = document.getElementById('liveUserSearch');
        if (liveSearch) {
            liveSearch.addEventListener('input', applyLiveFilters);
        }
    });

    // 2. Password Generator Utility
    function generateSecurePassword(length = 9) {
        var letters = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz";
        var numbers = "23456789";
        var symbols = "!@#$%&*";
        
        var pass = "";
        pass += letters.charAt(Math.floor(Math.random() * letters.length));
        pass += letters.charAt(Math.floor(Math.random() * letters.length));
        pass += numbers.charAt(Math.floor(Math.random() * numbers.length));
        pass += symbols.charAt(Math.floor(Math.random() * symbols.length));
        
        var all = letters + numbers + symbols;
        for (var i = pass.length; i < length; i++) {
            pass += all.charAt(Math.floor(Math.random() * all.length));
        }
        return pass.split('').sort(function() { return 0.5 - Math.random(); }).join('');
    }

    function prepareCreateUserModal() {
        var input = document.getElementById('createInputPassword');
        if (input) {
            input.value = generateSecurePassword(9);
        }
    }

    function generateNewPasswordForCreate() {
        var input = document.getElementById('createInputPassword');
        if (input) {
            input.value = generateSecurePassword(9);
            input.focus();
        }
    }

    function generateRandomPasswordForEdit() {
        var passInput = document.getElementById('modalInputPassword');
        if (passInput) {
            passInput.type = 'text';
            passInput.value = generateSecurePassword(9);
            passInput.focus();
        }
    }

    function copyInputText(inputId) {
        var input = document.getElementById(inputId);
        if (!input || !input.value) return;
        navigator.clipboard.writeText(input.value).then(function() {
            if (window.showToast) {
                window.showToast('Password copied to clipboard: ' + input.value);
            } else {
                alert('Copied to clipboard: ' + input.value);
            }
        });
    }

    function copyLoginCredentials(email, password) {
        var text = "School ERP Login Credentials:\nEmail: " + email + "\nPassword: " + password + "\nLogin URL: " + window.location.origin + "<?php echo URLROOT; ?>/auth/login";
        navigator.clipboard.writeText(text).then(function() {
            if (window.showToast) {
                window.showToast('Credentials copied! You can paste and send them to the user.');
            } else {
                alert('Credentials copied to clipboard!');
            }
        });
    }

    function togglePasswordVisibility(inputId, btn) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }

    function openEditUserModal(user) {
        document.getElementById('modalUserId').value = user.id;
        document.getElementById('modalInputName').value = user.name || '';
        document.getElementById('modalInputEmail').value = user.email || '';
        document.getElementById('modalSelectRole').value = user.role || 'student';
        document.getElementById('modalInputPassword').value = '';

        document.getElementById('modalUserCardName').innerText = user.name || 'User Account';
        document.getElementById('modalUserIdBadge').innerText = 'ID: #' + user.id;
        document.getElementById('modalUserRoleBadge').innerText = (user.role || '').replace('_', ' ');

        // Role select protection for Primary Admin (id 1)
        var roleSelect = document.getElementById('modalSelectRole');
        var roleNote = document.getElementById('roleChangeNote');
        if (user.id == 1) {
            roleSelect.setAttribute('disabled', 'disabled');
            roleNote.innerHTML = '<span class="text-danger fw-bold"><i class="fa fa-lock me-1"></i>Primary Super Admin role is protected and cannot be changed.</span>';
        } else {
            roleSelect.removeAttribute('disabled');
            roleNote.innerText = 'Assign system privileges & portal dashboard view';
        }

        var modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
    }
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
