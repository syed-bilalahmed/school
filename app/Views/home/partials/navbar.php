<?php
$siteSettings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];
$schoolName = !empty($siteSettings['school_name']) ? $siteSettings['school_name'] : ($data['school']->school_name ?? $data['settings']->school_name ?? SITENAME);
$cmsLogo = !empty($siteSettings['logo']) ? $siteSettings['logo'] : ($data['cms']->logo ?? '');
$activePage = $activePage ?? 'home';

// Fetch public notices with attached PDFs for all pages
$publicNotices = $data['notices'] ?? null;
if (!isset($publicNotices) || !is_array($publicNotices)) {
    if (class_exists('Notice')) {
        $publicNotices = Notice::getPublicNoticesWithAttachments();
    } else {
        $publicNotices = [];
    }
}
$noticeCount = is_array($publicNotices) ? count($publicNotices) : 0;
?>

<style>
/* CRITICAL FIX: Ensure Header and Ticker Never Overlap */
.global-site-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    pointer-events: auto;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.top-notice-ticker-bar {
    position: relative !important;
    top: auto !important;
    left: auto !important;
    right: auto !important;
    width: 100% !important;
    height: 38px !important;
    min-height: 38px !important;
    background: #081225 !important;
    color: #e2e8f0 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
    box-shadow: 0 1px 6px rgba(0, 0, 0, 0.25) !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 16px !important;
    font-size: 0.82rem !important;
    line-height: 1 !important;
    z-index: 1060 !important;
}

.notice-live-badge-wrapper {
    display: inline-flex !important;
    align-items: center !important;
    gap: 7px !important;
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
    color: #ffffff !important;
    padding: 4px 10px !important;
    border-radius: 6px !important;
    font-weight: 700 !important;
    font-size: 0.72rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    white-space: nowrap !important;
    cursor: pointer !important;
    flex-shrink: 0 !important;
    box-shadow: 0 0 8px rgba(239, 68, 68, 0.5) !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
}

.notice-blink-dot {
    width: 7px !important;
    height: 7px !important;
    background-color: #22c55e !important;
    border-radius: 50% !important;
    display: inline-block !important;
    box-shadow: 0 0 6px #22c55e !important;
    animation: noticeDotBlink 1.1s infinite alternate;
}

.notice-marquee-container {
    flex: 1 1 auto !important;
    overflow: hidden !important;
    margin: 0 12px !important;
    display: flex !important;
    align-items: center !important;
    min-width: 0 !important;
}

.notice-marquee-track {
    width: 100% !important;
    color: #f1f5f9 !important;
    font-size: 0.82rem !important;
    font-weight: 500 !important;
    cursor: pointer !important;
}

.notice-ticker-chip {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    padding: 2px 8px !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    color: #f8fafc !important;
}

.notice-ticker-chip:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #38bdf8 !important;
}

.ticker-cat-tag {
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    padding: 2px 6px !important;
    border-radius: 4px !important;
    background: rgba(59, 130, 246, 0.3) !important;
    color: #93c5fd !important;
    border: 1px solid rgba(59, 130, 246, 0.4) !important;
    text-transform: uppercase !important;
}

.ticker-title-text {
    font-weight: 600 !important;
    color: #f8fafc !important;
}

.ticker-pdf-pill {
    display: inline-flex !important;
    align-items: center !important;
    gap: 3px !important;
    background: rgba(239, 68, 68, 0.25) !important;
    color: #fca5a5 !important;
    font-size: 0.66rem !important;
    font-weight: 700 !important;
    padding: 1px 5px !important;
    border-radius: 4px !important;
    border: 1px solid rgba(239, 68, 68, 0.45) !important;
}

.ticker-separator {
    color: #64748b !important;
    margin: 0 8px !important;
    font-size: 0.72rem !important;
}

.btn-ticker-view-all {
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    background: rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    padding: 3px 10px !important;
    border-radius: 6px !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
}

.btn-ticker-view-all:hover {
    background: #ffffff !important;
    color: #0f172a !important;
}

/* NAVBAR (Sits cleanly in normal vertical flow under ticker) */
.front-navbar {
    position: relative !important;
    top: auto !important;
    left: auto !important;
    right: auto !important;
    width: 100% !important;
    padding: 10px 0 !important;
    background: transparent;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
    z-index: 1050 !important;
}

/* Scrolled Navbar Style */
.global-site-header.header-scrolled .front-navbar,
.front-navbar.header-scrolled {
    padding: 8px 0 !important;
    background: rgba(255, 255, 255, 0.96) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border-bottom: 1px solid rgba(226, 232, 240, 0.9) !important;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.08) !important;
}

.global-site-header.header-scrolled .navbar-brand,
.front-navbar.header-scrolled .navbar-brand {
    color: #0f172a !important;
}

.global-site-header.header-scrolled .nav-link,
.front-navbar.header-scrolled .nav-link {
    color: #334155 !important;
}

.global-site-header.header-scrolled .nav-link:hover,
.global-site-header.header-scrolled .nav-link.active,
.front-navbar.header-scrolled .nav-link:hover,
.front-navbar.header-scrolled .nav-link.active {
    color: #4f46e5 !important;
}

.global-site-header.header-scrolled .btn-nav-admission,
.front-navbar.header-scrolled .btn-nav-admission {
    background: #eef2ff !important;
    color: #4f46e5 !important;
    border-color: rgba(79, 70, 229, 0.2) !important;
}

.global-site-header.header-scrolled .navbar-toggler,
.front-navbar.header-scrolled .navbar-toggler {
    border-color: #cbd5e1 !important;
    color: #0f172a !important;
}

/* STRICT SINGLE-LINE COMPACT NAVBAR FIX (Zero vertical wrapping) */
.front-navbar .navbar-brand {
    font-size: 1.02rem !important;
    white-space: nowrap !important;
    flex-shrink: 0 !important;
    margin-right: 12px !important;
}

.front-navbar .navbar-brand img {
    height: 38px !important;
}

.front-navbar .nav-link {
    font-size: 0.81rem !important;
    padding: 5px 8px !important;
    white-space: nowrap !important;
    letter-spacing: -0.01em !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;
    border-radius: 9999px !important;
    display: inline-flex !important;
    align-items: center !important;
}

@media (min-width: 1400px) {
    .front-navbar .nav-link {
        font-size: 0.85rem !important;
        padding: 6px 11px !important;
    }
}

.btn-nav-admission {
    font-size: 0.79rem !important;
    padding: 5px 12px !important;
    white-space: nowrap !important;
    letter-spacing: 0 !important;
    line-height: 1.2 !important;
    border-radius: 9999px !important;
}

.btn-nav-login {
    font-size: 0.79rem !important;
    padding: 5px 12px !important;
    white-space: nowrap !important;
    letter-spacing: 0 !important;
    line-height: 1.2 !important;
    border-radius: 9999px !important;
}

@keyframes noticeDotBlink {
    0% { opacity: 1; transform: scale(1.2); background-color: #22c55e; box-shadow: 0 0 10px #22c55e; }
    50% { opacity: 0.2; transform: scale(0.65); }
    100% { opacity: 1; transform: scale(1.2); background-color: #eab308; box-shadow: 0 0 10px #eab308; }
.emergency-alert-bar {
    position: relative;
    width: 100%;
    padding: 6px 16px;
    font-size: 0.83rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 1065;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    transition: all 0.3s ease;
}
.emergency-alert-bar.theme-danger {
    background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
    color: #ffffff;
}
.emergency-alert-bar.theme-warning {
    background: linear-gradient(135deg, #b45309 0%, #d97706 100%);
    color: #ffffff;
}
.emergency-alert-bar.theme-info {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
    color: #ffffff;
}
.emergency-alert-bar.theme-success {
    background: linear-gradient(135deg, #047857 0%, #059669 100%);
    color: #ffffff;
}
.emergency-alert-badge {
    background: rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.3);
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}
.emergency-close-btn {
    background: transparent;
    border: none;
    color: #ffffff;
    opacity: 0.8;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 0 6px;
    line-height: 1;
    transition: opacity 0.2s;
}
.emergency-close-btn:hover {
    opacity: 1;
}
</style>

<!-- GLOBAL FIXED HEADER WRAPPER (PREVENTS ANY OVERLAP BETWEEN MARQUEE AND NAVBAR) -->
<header class="global-site-header" id="globalSiteHeader">
    <!-- EMERGENCY / URGENT ANNOUNCEMENT BAR (WHEN ENABLED BY ADMIN) -->
    <?php 
    $cmsSettingsObj = $data['cms'] ?? null;
    $emergencyEnabled = !empty($cmsSettingsObj->enable_emergency_alert) && ($cmsSettingsObj->enable_emergency_alert === 'yes') && !empty($cmsSettingsObj->emergency_alert_text);
    $emergencyBg = !empty($cmsSettingsObj->emergency_alert_bg) ? $cmsSettingsObj->emergency_alert_bg : 'danger';
    ?>
    <?php if($emergencyEnabled): ?>
        <div class="emergency-alert-bar theme-<?php echo htmlspecialchars($emergencyBg); ?>" id="siteEmergencyAlertBar">
            <div class="container-fluid px-lg-4 px-2 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                    <span class="emergency-alert-badge">
                        <i class="fa fa-triangle-exclamation"></i>
                        <?php echo ($emergencyBg === 'warning') ? 'HOLIDAY / NOTICE' : (($emergencyBg === 'info') ? 'CAMPUS UPDATE' : 'URGENT ALERT'); ?>
                    </span>
                    <span class="emergency-alert-msg text-truncate"><?php echo htmlspecialchars($cmsSettingsObj->emergency_alert_text); ?></span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <?php if(!empty($cmsSettingsObj->emergency_alert_link)): ?>
                        <a href="<?php echo htmlspecialchars($cmsSettingsObj->emergency_alert_link); ?>" class="btn btn-xs btn-light rounded-pill px-2 py-0 fw-bold" style="font-size: 0.75rem;">
                            View Details <i class="fa fa-arrow-right fa-xs ms-1"></i>
                        </a>
                    <?php endif; ?>
                    <button type="button" class="emergency-close-btn" onclick="dismissEmergencyAlert()" title="Dismiss this alert">&times;</button>
                </div>
            </div>
        </div>
        <script>
        (function(){
            if (sessionStorage.getItem('emergency_alert_dismissed') === 'true') {
                const el = document.getElementById('siteEmergencyAlertBar');
                if (el) el.style.display = 'none';
            }
        })();
        function dismissEmergencyAlert() {
            const el = document.getElementById('siteEmergencyAlertBar');
            if (el) {
                el.style.display = 'none';
                sessionStorage.setItem('emergency_alert_dismissed', 'true');
            }
        }
        </script>
    <?php endif; ?>

    <!-- TOP NOTICE MARQUEE TICKER & BLINKING ALERT BADGE (VISIBLE ON ALL PAGES) -->
    <div class="top-notice-ticker-bar" id="globalTopNoticeTicker">
        <div class="container-fluid px-lg-4 px-2 d-flex align-items-center justify-content-between h-100">
            <!-- Blinking Live Badge -->
            <div class="notice-live-badge-wrapper" onclick="openGlobalNoticeModal()" title="Click to view all notices, circulars &amp; PDFs">
                <span class="notice-blink-dot"></span>
                <i class="fa fa-bullhorn me-1"></i>
            <span>NOTICE BOARD</span>
        </div>

        <!-- Marquee Ticker Track (Pause on Hover, Click to Open Modal) -->
        <div class="notice-marquee-container">
            <?php if (!empty($publicNotices)): ?>
                <marquee class="notice-marquee-track" scrollamount="6" behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                    <?php foreach ($publicNotices as $notice): ?>
                        <span class="notice-ticker-chip" onclick="openGlobalNoticeModal(<?php echo $notice->id; ?>)" title="Click to view details &amp; PDF document">
                            <span class="ticker-cat-tag"><?php echo htmlspecialchars($notice->notice_type ?? 'Notice', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="ticker-title-text"><?php echo htmlspecialchars($notice->title, ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php if (!empty($notice->attachment)): ?>
                                <span class="ticker-pdf-pill"><i class="fa fa-file-pdf text-danger"></i> PDF</span>
                            <?php endif; ?>
                        </span>
                        <span class="ticker-separator">✦</span>
                    <?php endforeach; ?>
                </marquee>
            <?php else: ?>
                <div class="text-light opacity-75 small">
                    <i class="fa fa-info-circle me-1"></i> Welcome to <?php echo htmlspecialchars($schoolName); ?> &mdash; Official Circulars and Notifications portal.
                </div>
            <?php endif; ?>
        </div>

        <!-- View All Notices & PDFs Button -->
        <button type="button" class="btn btn-ticker-view-all" onclick="openGlobalNoticeModal()" title="View All Circulars &amp; PDF Documents">
            <i class="fa fa-file-pdf text-danger"></i>
            <span class="d-none d-md-inline">All Notices &amp; PDFs</span>
            <span class="d-inline d-md-none">PDFs</span>
            <span class="badge bg-danger rounded-pill"><?php echo $noticeCount; ?></span>
        </button>
    </div>
</div>

<!-- TOP SCROLL-EFFECT NAVIGATION BAR (CONTAINER-FLUID ENSURES SINGLE-LINE FIT) -->
<nav class="navbar navbar-expand-lg front-navbar" id="mainFrontNavbar">
    <div class="container-fluid px-lg-4 px-xl-5">
        <!-- Brand / Logo -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo URLROOT; ?>">
            <?php if(!empty($cmsLogo)): ?>
                <img src="<?php echo URLROOT . '/' . htmlspecialchars($cmsLogo); ?>" height="38" alt="<?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 130px; object-fit: contain;">
            <?php else: ?>
                <div class="brand-icon-emblem" style="width: 36px; height: 36px; font-size: 1.05rem;">
                    <i class="fa fa-graduation-cap"></i>
                </div>
            <?php endif; ?>
            <span class="d-none d-sm-inline fw-bold text-truncate" style="max-width: 250px;"><?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></span>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 shadow-none text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFrontNav" aria-controls="navbarFrontNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-bars fs-4"></i>
        </button>

        <!-- Navbar Links -->
        <?php
        // Organize dynamic menus by dropdown group
        $directMenus = [];
        $exploreSubMenus = [];
        $academicsSubMenus = [];
        $customDropdownGroups = [];

        if (!empty($data['menus'])) {
            foreach ($data['menus'] as $m) {
                $grp = strtolower(trim($m->dropdown_group ?? 'none'));
                if ($grp === 'explore') {
                    $exploreSubMenus[] = $m;
                } elseif ($grp === 'academics') {
                    $academicsSubMenus[] = $m;
                } elseif ($grp === 'none' || empty($grp)) {
                    $directMenus[] = $m;
                } else {
                    $customDropdownGroups[$grp][] = $m;
                }
            }
        }

        $isExploreActive = in_array($activePage, ['facilities', 'fees', 'gallery', 'alumni', 'requirements']);
        ?>
        <div class="collapse navbar-collapse" id="navbarFrontNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($activePage === 'home') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>">Home</a>
                </li>

                <!-- Academics (Direct Link or Dropdown if sub-pages exist) -->
                <?php if(!empty($academicsSubMenus)): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($activePage === 'academics') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/home/academics" id="navDropdownAcademics" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Academics <i class="fa fa-chevron-down ms-1" style="font-size: 0.65rem;"></i>
                        </a>
                        <ul class="dropdown-menu shadow-lg border-0 py-2" aria-labelledby="navDropdownAcademics">
                            <li><a class="dropdown-item <?php echo ($activePage === 'academics') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/home/academics"><i class="fa fa-book-open me-2 text-primary"></i> Academics Overview</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <?php foreach($academicsSubMenus as $am): ?>
                                <li>
                                    <a class="dropdown-item <?php echo ($activePage === ($am->page_slug ?? '')) ? 'active' : ''; ?>" href="<?php echo ($am->page_id > 0) ? URLROOT . '/home/page/' . $am->page_slug : $am->link; ?>">
                                        <i class="fa fa-angle-right me-2 text-muted"></i> <?php echo htmlspecialchars($am->title, ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($activePage === 'academics') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/home/academics">Academics</a>
                    </li>
                <?php endif; ?>

                <!-- Explore / Campus Life Dropdown (Consolidates Campus Life, Fees, Gallery, Alumni, Requirements) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo $isExploreActive ? 'active' : ''; ?>" href="#" id="navDropdownExplore" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Explore <i class="fa fa-chevron-down ms-1" style="font-size: 0.65rem;"></i>
                    </a>
                    <ul class="dropdown-menu shadow-lg border-0 py-2" aria-labelledby="navDropdownExplore">
                        <li>
                            <a class="dropdown-item <?php echo ($activePage === 'facilities') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/home/facilities">
                                <i class="fa fa-building-columns me-2 text-info"></i> Campus Life &amp; Facilities
                            </a>
                        </li>
                        <?php if(($data['cms']->enable_fee_structure ?? 'yes') === 'yes'): ?>
                            <li>
                                <a class="dropdown-item <?php echo ($activePage === 'fees') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/home/fees">
                                    <i class="fa fa-receipt me-2 text-success"></i> Tuition &amp; Fee Structure
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a class="dropdown-item <?php echo ($activePage === 'gallery') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/home/gallery">
                                <i class="fa fa-camera-retro me-2 text-danger"></i> Photo Gallery
                            </a>
                        </li>
                        <?php if(($data['cms']->enable_alumni ?? 'yes') === 'yes'): ?>
                            <li>
                                <a class="dropdown-item <?php echo ($activePage === 'alumni') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/home/alumni">
                                    <i class="fa fa-user-graduate me-2 text-warning"></i> Alumni Network
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(($data['cms']->enable_requirements ?? 'yes') === 'yes'): ?>
                            <li>
                                <a class="dropdown-item <?php echo ($activePage === 'requirements') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/home/requirements">
                                    <i class="fa fa-briefcase me-2 text-primary"></i> School Requirements &amp; Jobs
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(!empty($exploreSubMenus)): ?>
                            <li><hr class="dropdown-divider my-1"></li>
                            <?php foreach($exploreSubMenus as $em): ?>
                                <li>
                                    <a class="dropdown-item <?php echo ($activePage === ($em->page_slug ?? '')) ? 'active' : ''; ?>" href="<?php echo ($em->page_id > 0) ? URLROOT . '/home/page/' . $em->page_slug : $em->link; ?>">
                                        <i class="fa fa-angle-right me-2 text-muted"></i> <?php echo htmlspecialchars($em->title, ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Events & News -->
                <li class="nav-item">
                    <a class="nav-link <?php echo ($activePage === 'events') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/home/events">Events &amp; News</a>
                </li>

                <!-- Full Contact Us Page Link -->
                <li class="nav-item">
                    <a class="nav-link <?php echo ($activePage === 'contact') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/home/contact">Contact Us</a>
                </li>

                <!-- Direct Dynamic CMS Menus -->
                <?php if(!empty($directMenus)): ?>
                    <?php foreach($directMenus as $menu): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($activePage === ($menu->page_slug ?? '')) ? 'active' : ''; ?>" href="<?php echo ($menu->page_id > 0) ? URLROOT . '/home/page/' . $menu->page_slug : $menu->link; ?>">
                                <?php echo htmlspecialchars($menu->title, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Custom Dropdown Groups (e.g. About, Admissions, etc.) -->
                <?php if(!empty($customDropdownGroups)): ?>
                    <?php foreach($customDropdownGroups as $grpName => $grpItems): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="dropdown_<?php echo md5($grpName); ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars(ucwords(str_replace(['-', '_'], ' ', $grpName)), ENT_QUOTES, 'UTF-8'); ?> <i class="fa fa-chevron-down ms-1" style="font-size: 0.65rem;"></i>
                            </a>
                            <ul class="dropdown-menu shadow-lg border-0 py-2" aria-labelledby="dropdown_<?php echo md5($grpName); ?>">
                                <?php foreach($grpItems as $gItem): ?>
                                    <li>
                                        <a class="dropdown-item <?php echo ($activePage === ($gItem->page_slug ?? '')) ? 'active' : ''; ?>" href="<?php echo ($gItem->page_id > 0) ? URLROOT . '/home/page/' . $gItem->page_slug : $gItem->link; ?>">
                                            <i class="fa fa-angle-right me-2 text-muted"></i> <?php echo htmlspecialchars($gItem->title, ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Online Admission Button -->
                <?php if(($data['cms']->enable_online_admission ?? 'yes') === 'yes'): ?>
                    <li class="nav-item ms-lg-1 my-1 my-lg-0">
                        <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-nav-admission <?php echo ($activePage === 'admission') ? 'active' : ''; ?>">
                            <i class="fa fa-file-signature me-1"></i> Apply Online
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Portal Login / Dashboard Dropdown -->
                <li class="nav-item dropdown ms-lg-1 my-1 my-lg-0">
                    <?php if(isset($_SESSION['user_id'])): 
                        $navUserRole = $_SESSION['user_role'] ?? 'admin';
                        $navHomeUrl = URLROOT . '/admin/dashboard';
                        if ($navUserRole === 'teacher') $navHomeUrl = URLROOT . '/teacher/index';
                        elseif ($navUserRole === 'student') $navHomeUrl = URLROOT . '/student/index';
                        elseif ($navUserRole === 'parent') $navHomeUrl = URLROOT . '/parent/index';
                        elseif ($navUserRole === 'receptionist') $navHomeUrl = URLROOT . '/frontoffice/index';
                        elseif ($navUserRole === 'accountant') $navHomeUrl = URLROOT . '/fees/collect';
                        elseif ($navUserRole === 'librarian') $navHomeUrl = URLROOT . '/library/index';
                    ?>
                        <div class="btn-group">
                            <a href="<?php echo $navHomeUrl; ?>" class="btn btn-nav-login">
                                <i class="fa fa-tachometer-alt me-1"></i> Dashboard
                            </a>
                            <button type="button" class="btn btn-nav-login dropdown-toggle dropdown-toggle-split ps-2 pe-2" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Toggle user menu">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2" style="border-radius: 12px; min-width: 220px; z-index: 1060;">
                                <li class="px-3 py-1">
                                    <div class="fw-bold text-dark text-truncate" style="font-size: 0.88rem;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Active User'); ?></div>
                                    <div class="badge bg-primary bg-opacity-10 text-primary text-uppercase" style="font-size: 0.68rem;"><?php echo htmlspecialchars($navUserRole); ?></div>
                                </li>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li>
                                    <a class="dropdown-item py-2 small" href="<?php echo $navHomeUrl; ?>">
                                        <i class="fa fa-gauge me-2 text-primary"></i> <?php echo ucfirst($navUserRole); ?> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 small" href="<?php echo URLROOT; ?>/auth/login?switch=1">
                                        <i class="fa fa-arrows-rotate me-2 text-warning"></i> Switch Role / Portal
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li>
                                    <a class="dropdown-item py-2 small text-danger" href="<?php echo URLROOT; ?>/auth/logout">
                                        <i class="fa fa-arrow-right-from-bracket me-2"></i> Log Out
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="btn-group">
                            <a href="<?php echo URLROOT; ?>/auth/login" class="btn btn-nav-login">
                                <i class="fa fa-lock me-1"></i> Portal Login
                            </a>
                            <button type="button" class="btn btn-nav-login dropdown-toggle dropdown-toggle-split ps-2 pe-2" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Toggle role portal menu">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2" style="border-radius: 14px; min-width: 230px; z-index: 1060;">
                                <li>
                                    <span class="dropdown-header text-uppercase text-muted" style="font-size: 0.7rem; letter-spacing: 0.05em;">Select Portal Hub</span>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 small fw-bold text-primary" href="<?php echo URLROOT; ?>/auth/login">
                                        <i class="fa fa-arrow-right-to-bracket me-2"></i> All Portals Login
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-1 px-3 small d-flex align-items-center gap-2" href="<?php echo URLROOT; ?>/auth/login?role=admin">
                                        <span class="badge rounded-circle p-1" style="background: #2563eb;"><i class="fa fa-shield-halved text-white" style="font-size: 0.7rem;"></i></span>
                                        <span>School Admin</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1 px-3 small d-flex align-items-center gap-2" href="<?php echo URLROOT; ?>/auth/login?role=teacher">
                                        <span class="badge rounded-circle p-1" style="background: #059669;"><i class="fa fa-chalkboard-user text-white" style="font-size: 0.7rem;"></i></span>
                                        <span>Teacher Portal</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1 px-3 small d-flex align-items-center gap-2" href="<?php echo URLROOT; ?>/auth/login?role=student">
                                        <span class="badge rounded-circle p-1" style="background: #0284c7;"><i class="fa fa-user-graduate text-white" style="font-size: 0.7rem;"></i></span>
                                        <span>Student Portal</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1 px-3 small d-flex align-items-center gap-2" href="<?php echo URLROOT; ?>/auth/login?role=parent">
                                        <span class="badge rounded-circle p-1" style="background: #d97706;"><i class="fa fa-people-roof text-white" style="font-size: 0.7rem;"></i></span>
                                        <span>Parent Portal</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1 px-3 small d-flex align-items-center gap-2" href="<?php echo URLROOT; ?>/auth/login?role=accountant">
                                        <span class="badge rounded-circle p-1" style="background: #0f766e;"><i class="fa fa-file-invoice-dollar text-white" style="font-size: 0.7rem;"></i></span>
                                        <span>Accounts / Fees</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1 px-3 small d-flex align-items-center gap-2" href="<?php echo URLROOT; ?>/auth/login?role=receptionist">
                                        <span class="badge rounded-circle p-1" style="background: #e11d48;"><i class="fa fa-headset text-white" style="font-size: 0.7rem;"></i></span>
                                        <span>Front Office</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-1 px-3 small d-flex align-items-center gap-2" href="<?php echo URLROOT; ?>/auth/login?role=librarian">
                                        <span class="badge rounded-circle p-1" style="background: #7c3aed;"><i class="fa fa-book-bookmark text-white" style="font-size: 0.7rem;"></i></span>
                                        <span>Library Portal</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>
</header>

<!-- GLOBAL NOTICE BOARD & PDF DOCUMENTS MODAL POPUP (AVAILABLE ON ALL PAGES) -->
<div class="modal fade" id="globalNoticePdfModal" tabindex="-1" aria-labelledby="globalNoticePdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <!-- Modal Header -->
            <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #090e1a 0%, #1e293b 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="notice-modal-icon-emblem">
                        <i class="fa fa-bullhorn"></i>
                    </div>
                    <div>
                        <h4 class="modal-title fw-bold text-white mb-0" id="globalNoticePdfModalLabel">
                            Official School Notices, Circulars &amp; PDF Documents
                        </h4>
                        <div class="text-white-50 small mt-1">
                            <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?> &bull; Public Notifications Desk
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" onclick="closeGlobalNoticeModal()" aria-label="Close"></button>
            </div>

            <!-- Modal Search & Filter Toolbar -->
            <div class="bg-light p-3 border-bottom">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-5 col-md-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0 shadow-none" id="globalNoticeSearchInput" placeholder="Search notices, circulars, or keywords..." onkeyup="filterGlobalNotices()">
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-6">
                        <div class="d-flex flex-wrap gap-1 justify-content-md-end" id="globalNoticeCatFilters">
                            <button type="button" class="notice-cat-filter-btn active" data-cat="all" onclick="filterNoticeCategory('all', this)">All</button>
                            <button type="button" class="notice-cat-filter-btn" data-cat="examination" onclick="filterNoticeCategory('examination', this)">Examination</button>
                            <button type="button" class="notice-cat-filter-btn" data-cat="admissions" onclick="filterNoticeCategory('admissions', this)">Admissions</button>
                            <button type="button" class="notice-cat-filter-btn" data-cat="general notice" onclick="filterNoticeCategory('general notice', this)">General</button>
                            <button type="button" class="notice-cat-filter-btn" data-cat="events" onclick="filterNoticeCategory('events', this)">Events</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Body (List of Notices & PDF Documents) -->
            <div class="modal-body p-4" style="max-height: 65vh; overflow-y: auto;">
                <div id="globalNoticeListContainer">
                    <?php if (!empty($publicNotices)): ?>
                        <?php foreach ($publicNotices as $notice): 
                            $cat = strtolower(trim($notice->notice_type ?? 'General Notice'));
                            $priority = ucfirst(strtolower($notice->priority ?? 'Normal'));
                            $pDate = !empty($notice->publish_date) ? date('d M Y', strtotime($notice->publish_date)) : date('d M Y');
                            $hasPdf = !empty($notice->attachment);
                            $pdfUrl = $hasPdf ? (URLROOT . '/' . ltrim($notice->attachment, '/')) : '';
                            $fileName = $hasPdf ? basename($notice->attachment) : '';
                            
                            $badgeColor = 'bg-secondary text-white';
                            if ($cat === 'examination') $badgeColor = 'bg-danger text-white';
                            elseif ($cat === 'admissions') $badgeColor = 'bg-primary text-white';
                            elseif ($cat === 'events') $badgeColor = 'bg-success text-white';
                            elseif ($cat === 'general notice') $badgeColor = 'bg-info text-dark';
                            
                            $prioBadge = 'bg-light text-muted border';
                            if ($priority === 'Urgent') $prioBadge = 'bg-danger text-white';
                            elseif ($priority === 'Important') $prioBadge = 'bg-warning text-dark';
                        ?>
                            <div class="global-notice-card" id="notice-card-<?php echo $notice->id; ?>" data-category="<?php echo htmlspecialchars($cat); ?>" data-search-text="<?php echo htmlspecialchars(strtolower(($notice->title ?? '') . ' ' . ($notice->message ?? '') . ' ' . $cat)); ?>">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge <?php echo $badgeColor; ?> px-2 py-1"><?php echo htmlspecialchars($notice->notice_type ?? 'Notice'); ?></span>
                                        <?php if ($priority !== 'Normal'): ?>
                                            <span class="badge <?php echo $prioBadge; ?> px-2 py-1"><?php echo htmlspecialchars($priority); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-muted small">
                                        <i class="far fa-calendar-alt me-1"></i> Published: <?php echo $pDate; ?>
                                    </span>
                                </div>
                                
                                <h5 class="fw-bold text-dark mb-2">
                                    <?php echo htmlspecialchars($notice->title); ?>
                                </h5>

                                <p class="text-secondary mb-3" style="font-size: 0.95rem; line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($notice->message)); ?>
                                </p>

                                <?php if ($hasPdf): ?>
                                    <div class="notice-attachment-box">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fa fa-file-pdf fa-2x text-danger"></i>
                                            <div>
                                                <div class="fw-bold text-dark text-break" style="font-size: 0.92rem;">
                                                    <?php echo htmlspecialchars($fileName); ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <i class="fa fa-shield-alt text-success me-1"></i> Official Verified Institutional Document &bull; PDF
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <a href="<?php echo $pdfUrl; ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold px-3">
                                                <i class="fa fa-external-link-alt me-1"></i> View PDF
                                            </a>
                                            <a href="<?php echo $pdfUrl; ?>" download="<?php echo htmlspecialchars($fileName); ?>" class="btn btn-sm btn-danger text-white fw-semibold px-3 shadow-sm">
                                                <i class="fa fa-download me-1"></i> Download PDF
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fa fa-bullhorn fa-3x text-muted mb-3"></i>
                            <h5 class="fw-bold text-muted">No notices currently published</h5>
                            <p class="text-muted small">Please check back later for updates and official circulars.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Empty State for Search Filter -->
                <div id="noticeEmptySearchState" class="text-center py-5" style="display: none;">
                    <i class="fa fa-search fa-3x text-muted opacity-50 mb-3"></i>
                    <h5 class="fw-bold text-muted">No circulars matching your search</h5>
                    <p class="text-muted small">Try a different keyword or reset the category filter.</p>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetNoticeFilters()">Reset Search</button>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light px-4 py-2 justify-content-between">
                <span class="text-muted small">
                    <i class="fa fa-info-circle me-1"></i> For document verification or queries, contact the School Administration Desk.
                </span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="window.print()">
                        <i class="fa fa-print me-1"></i> Print
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" onclick="closeGlobalNoticeModal()">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal & Marquee Interactive Handlers -->
<script>
let currentNoticeCategory = 'all';

function openGlobalNoticeModal(targetNoticeId) {
    const modalEl = document.getElementById('globalNoticePdfModal');
    if (!modalEl) return;

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();
    } else {
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        document.body.classList.add('modal-open');
    }

    if (targetNoticeId) {
        setTimeout(() => {
            const card = document.getElementById('notice-card-' + targetNoticeId);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.classList.add('notice-card-highlighted');
                setTimeout(() => card.classList.remove('notice-card-highlighted'), 2500);
            }
        }, 300);
    }
}

function closeGlobalNoticeModal() {
    const modalEl = document.getElementById('globalNoticePdfModal');
    if (!modalEl) return;
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const bsModal = bootstrap.Modal.getInstance(modalEl);
        if (bsModal) bsModal.hide();
        else modalEl.style.display = 'none';
    } else {
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
}

function filterGlobalNotices() {
    const query = (document.getElementById('globalNoticeSearchInput')?.value || '').toLowerCase().trim();
    const cards = document.querySelectorAll('.global-notice-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const text = card.getAttribute('data-search-text') || '';
        const cat = card.getAttribute('data-category') || '';
        const matchesQuery = !query || text.includes(query);
        const matchesCat = currentNoticeCategory === 'all' || cat === currentNoticeCategory;

        if (matchesQuery && matchesCat) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    const emptyState = document.getElementById('noticeEmptySearchState');
    if (emptyState) {
        emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
    }
}

function filterNoticeCategory(cat, btn) {
    currentNoticeCategory = cat.toLowerCase();
    document.querySelectorAll('#globalNoticeCatFilters .notice-cat-filter-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    filterGlobalNotices();
}

function resetNoticeFilters() {
    const input = document.getElementById('globalNoticeSearchInput');
    if (input) input.value = '';
    currentNoticeCategory = 'all';
    document.querySelectorAll('#globalNoticeCatFilters .notice-cat-filter-btn').forEach(b => {
        if (b.getAttribute('data-cat') === 'all') b.classList.add('active');
        else b.classList.remove('active');
    });
    filterGlobalNotices();
}

document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('globalSiteHeader');
    const navbar = document.getElementById('mainFrontNavbar');
    function checkHeaderScroll() {
        const isScrolled = window.scrollY > 25;
        if (header) {
            if (isScrolled) header.classList.add('header-scrolled');
            else header.classList.remove('header-scrolled');
        }
        if (navbar) {
            if (isScrolled) navbar.classList.add('header-scrolled');
            else navbar.classList.remove('header-scrolled');
        }
    }
    window.addEventListener('scroll', checkHeaderScroll, { passive: true });
    checkHeaderScroll();
});
</script>
