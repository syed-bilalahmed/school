<?php
// Ensure dynamic settings are available
if (!isset($dynamicSchoolName) && class_exists('SiteSetting')) {
    $dynamicSettings = SiteSetting::getGlobalSettings();
    $dynamicSchoolName = !empty($dynamicSettings['school_name']) ? $dynamicSettings['school_name'] : SITENAME;
    $dynamicCampusName = !empty($dynamicSettings['campus_name']) ? $dynamicSettings['campus_name'] : 'Main Executive Campus';
    $dynamicSchoolLogo = !empty($dynamicSettings['logo']) ? $dynamicSettings['logo'] : '';
} else {
    $dynamicSchoolName = $dynamicSchoolName ?? SITENAME;
    $dynamicCampusName = $dynamicCampusName ?? 'Main Executive Campus';
    $dynamicSchoolLogo = $dynamicSchoolLogo ?? '';
}

$userRole = $_SESSION['user_role'] ?? 'admin';
$roleHomeUrl = URLROOT . '/admin/dashboard';
if ($userRole === 'teacher') $roleHomeUrl = URLROOT . '/teacher/index';
elseif ($userRole === 'student') $roleHomeUrl = URLROOT . '/student/index';
elseif ($userRole === 'parent') $roleHomeUrl = URLROOT . '/parent/index';
elseif ($userRole === 'receptionist') $roleHomeUrl = URLROOT . '/frontoffice/index';
elseif ($userRole === 'accountant') $roleHomeUrl = URLROOT . '/fees/collect';
elseif ($userRole === 'librarian') $roleHomeUrl = URLROOT . '/library/index';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo $roleHomeUrl; ?>" class="sidebar-brand" title="<?php echo htmlspecialchars($dynamicSchoolName, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="brand-icon-box">
                <?php if(!empty($dynamicSchoolLogo)): ?>
                    <img src="<?php echo URLROOT . '/' . htmlspecialchars($dynamicSchoolLogo); ?>" alt="Logo" class="brand-logo-img">
                <?php else: ?>
                    <i class="fa fa-graduation-cap"></i>
                <?php endif; ?>
            </div>
            <div class="brand-text">
                <span class="brand-title" id="sidebarBrandTitle" title="<?php echo htmlspecialchars($dynamicSchoolName, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($dynamicSchoolName, ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <span class="brand-subtitle" id="sidebarBrandSubtitle">
                    <?php echo htmlspecialchars($dynamicCampusName, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>
        </a>
    </div>

    <div class="sidebar-content-scroll">
        <?php require APPROOT . '/Views/layouts/sidebar_content.php'; ?>
    </div>

    <?php if(isset($_SESSION['user_id'])): 
        $userName = $_SESSION['user_name'] ?? 'User';
        $initials = strtoupper(substr($userName, 0, 2));
    ?>
    <div class="sidebar-footer">
        <a href="<?php echo URLROOT; ?>/profile/index" class="user-pill" title="My Profile & Settings">
            <?php if(!empty($_SESSION['user_avatar'])): ?>
                <img src="<?php echo URLROOT . '/' . htmlspecialchars($_SESSION['user_avatar'], ENT_QUOTES, 'UTF-8'); ?>" alt="Avatar" class="avatar" style="object-fit:cover; width:36px; height:36px; border-radius:50%;">
            <?php else: ?>
                <div class="avatar"><?php echo $initials; ?></div>
            <?php endif; ?>
            <div class="user-details">
                <span class="user-name"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="user-role"><?php echo str_replace('_', ' ', $userRole); ?></span>
            </div>
        </a>
        <a href="<?php echo URLROOT; ?>/auth/logout" class="btn-logout-icon" title="Sign Out">
            <i class="fa fa-sign-out-alt"></i>
        </a>
    </div>
    <?php endif; ?>
</aside>
