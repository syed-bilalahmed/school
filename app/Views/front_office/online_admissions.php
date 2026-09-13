<?php require APPROOT . '/Views/layouts/header.php'; 
$submittedInfo = $_SESSION['admission_submitted_info'] ?? null;
unset($_SESSION['admission_submitted_info']);

$lastEnrolled = $_SESSION['last_enrolled_student'] ?? null;
unset($_SESSION['last_enrolled_student']);

$counts = $data['counts'] ?? (object)[
    'total_all' => count($data['admissions'] ?? []),
    'total_pending' => 0,
    'total_new' => 0,
    'total_follow' => 0,
    'total_approved' => 0,
    'total_rejected' => 0
];

// Helper functions for modern display
if (!function_exists('oa_get_pak_phone')) {
    function oa_get_pak_phone($phone) {
        $clean = preg_replace('/[^0-9]/', '', (string)$phone);
        if (strpos($clean, '0') === 0) {
            return '92' . substr($clean, 1);
        }
        if (strpos($clean, '92') === 0) {
            return $clean;
        }
        return '92' . $clean;
    }
}

if (!function_exists('oa_get_initials')) {
    function oa_get_initials($name) {
        $parts = preg_split('/\s+/', trim((string)$name));
        $first = !empty($parts[0]) ? mb_substr($parts[0], 0, 1) : 'A';
        $second = !empty($parts[1]) ? mb_substr($parts[1], 0, 1) : '';
        return strtoupper($first . $second);
    }
}

if (!function_exists('oa_calc_age')) {
    function oa_calc_age($dob) {
        if (empty($dob) || $dob === '0000-00-00') return null;
        try {
            $bday = new DateTime($dob);
            $today = new DateTime();
            $diff = $today->diff($bday);
            return $diff->y > 0 ? $diff->y . ' Yrs' : ($diff->m > 0 ? $diff->m . ' Mo' : null);
        } catch(Exception $e) {
            return null;
        }
    }
}

if (!function_exists('oa_relative_time')) {
    function oa_relative_time($dateStr) {
        if (empty($dateStr)) return '';
        $ts = strtotime($dateStr);
        if (!$ts) return $dateStr;
        $diff = time() - $ts;
        if (date('Y-m-d') === date('Y-m-d', $ts)) {
            return '<span class="text-success fw-bold"><i class="fa fa-circle me-1" style="font-size: 0.5rem;"></i>Today</span>';
        } elseif (date('Y-m-d', strtotime('-1 day')) === date('Y-m-d', $ts)) {
            return '<span class="text-primary fw-semibold">Yesterday</span>';
        } else {
            $days = floor($diff / 86400);
            if ($days > 0 && $days < 30) {
                return '<span class="text-muted">' . $days . 'd ago</span>';
            }
            return '<span class="text-muted">' . date('d M Y', $ts) . '</span>';
        }
    }
}
?>

<style>
/* ==========================================================================
   ONLINE ADMISSIONS DESK - ULTRA MODERN LUXURY UI STYLES
   ========================================================================== */
.oa-shell {
    color: #0f172a;
}

/* Page Header & Breadcrumb */
.oa-breadcrumb {
    font-size: 0.82rem;
}
.oa-breadcrumb a {
    color: #64748b;
    text-decoration: none;
    transition: color 0.15s ease;
}
.oa-breadcrumb a:hover {
    color: #4f46e5;
}

/* Metric KPI Stat Cards */
.oa-stat-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    padding: 1.25rem 1.35rem;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
}
.oa-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.1), 0 4px 6px -2px rgba(15, 23, 42, 0.04);
    border-color: #cbd5e1;
}
.oa-stat-card.active-filter {
    border-color: #4f46e5;
    background: linear-gradient(180deg, #ffffff 0%, #f8faff 100%);
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2), 0 8px 16px -4px rgba(79, 70, 229, 0.15);
}
.oa-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}
.oa-stat-card:hover .oa-stat-icon {
    transform: scale(1.08);
}
.oa-stat-value {
    font-size: 1.85rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.1;
}
.oa-stat-label {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
}

/* Linear-style Segmented Navigation Pills */
.oa-nav-container {
    background: #ffffff;
    padding: 0.4rem;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}
.oa-nav-pill {
    font-size: 0.84rem;
    font-weight: 600;
    padding: 0.55rem 1.1rem;
    border-radius: 10px;
    color: #475569;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
}
.oa-nav-pill:hover {
    color: #0f172a;
    background-color: #f1f5f9;
}
.oa-nav-pill.active {
    background: #4f46e5;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
}
.oa-nav-pill.active .badge {
    background-color: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

/* Filter Command Bar */
.oa-filter-bar {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    padding: 1rem 1.25rem;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
}
.oa-search-box {
    position: relative;
}
.oa-search-box .fa-search {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}
.oa-search-input {
    padding-left: 2.5rem !important;
    padding-right: 2.2rem !important;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.88rem;
    transition: all 0.2s ease;
}
.oa-search-input:focus {
    background: #ffffff;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}
.oa-search-clear {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 0.85rem;
    display: none;
}
.oa-search-clear:hover {
    color: #ef4444;
}

/* Main Table Container */
.oa-table-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    overflow: hidden;
}
.oa-table-header {
    padding: 1.1rem 1.35rem;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
}
.oa-table {
    margin-bottom: 0;
    font-size: 0.88rem;
}
.oa-table thead th {
    background: #f8fafc;
    color: #475569;
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    border-top: none;
}
.oa-table tbody td {
    padding: 1rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    transition: background-color 0.15s ease;
}
.oa-table tbody tr.adm-row:hover td {
    background-color: #f8faff;
}
.oa-table tbody tr:last-child td {
    border-bottom: none;
}

/* Applicant Initials Avatar */
.oa-avatar {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.88rem;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
}

/* Action Dropdown & Quick Buttons */
.oa-quick-btn {
    padding: 0.38rem 0.75rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}
.oa-whatsapp-btn {
    background-color: #25D366;
    color: #ffffff !important;
    border: none;
    padding: 0.28rem 0.55rem;
    border-radius: 7px;
    font-size: 0.74rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    text-decoration: none;
    transition: all 0.15s ease;
}
.oa-whatsapp-btn:hover {
    background-color: #1eb956;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);
}

/* Floating Sticky Bulk Action Bar */
.oa-bulk-bar {
    background: #0f172a;
    color: #ffffff;
    border-radius: 14px;
    border-left: 5px solid #10b981 !important;
    box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.35);
    animation: slideUpFade 0.25s ease-out;
}
@keyframes slideUpFade {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Status Badges */
.badge-status-new {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
    font-weight: 600;
    padding: 0.35rem 0.65rem;
    border-radius: 20px;
    font-size: 0.76rem;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}
.badge-status-follow {
    background-color: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    font-weight: 600;
    padding: 0.35rem 0.65rem;
    border-radius: 20px;
    font-size: 0.76rem;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}
.badge-status-enrolled {
    background-color: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
    font-weight: 700;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.76rem;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}
.badge-status-rejected {
    background-color: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
    font-weight: 600;
    padding: 0.35rem 0.65rem;
    border-radius: 20px;
    font-size: 0.76rem;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}

/* Pulsing Status Dot */
.oa-pulse-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
    box-shadow: 0 0 0 rgba(245, 158, 11, 0.4);
    animation: pulseGlow 2s infinite;
}
@keyframes pulseGlow {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(245, 158, 11, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}

/* Custom Checkbox */
.oa-checkbox {
    width: 18px;
    height: 18px;
    border-radius: 5px;
    cursor: pointer;
    border: 1.5px solid #cbd5e1;
    transition: all 0.15s ease;
}
.oa-checkbox:checked {
    background-color: #4f46e5;
    border-color: #4f46e5;
}

/* Modal Enhancements */
.oa-modal-header-gradient {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.oa-modal-header-emerald {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}
.oa-modal-header-indigo {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
}
</style>

<div class="container-fluid px-0 oa-shell">
    
    <!-- ========================================================================= -->
    <!-- 1. TOP HEADER & EXECUTIVE ACTION TOOLBAR                                  -->
    <!-- ========================================================================= -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb" class="oa-breadcrumb mb-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard"><i class="fa fa-home me-1"></i>Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/frontoffice/index">Front Office Desk</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Online Admissions</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h2 class="h3 fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">
                    Online Admissions Secretariat
                </h2>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold rounded-pill" style="font-size: 0.75rem;">
                    <i class="fa fa-globe me-1"></i>Live Portal Inbox
                </span>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold rounded-pill" style="font-size: 0.75rem;">
                    <i class="fa fa-check-circle me-1"></i>SaaS Active
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1" style="max-width: 720px;">
                Central command desk for screening prospective candidate applications, validating mandatory registration dossiers, and 1-click converting qualified applicants into active class rosters.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Manual Log Application Modal Button -->
            <button type="button" class="btn btn-success fw-bold px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newOnlineAdmissionModal" style="border-radius: 10px; font-size: 0.85rem;">
                <i class="fa fa-plus-circle fa-lg"></i>
                <span>Register Application</span>
            </button>

            <!-- Document Checklist Modal Button -->
            <button type="button" class="btn btn-outline-primary fw-semibold px-3 py-2 shadow-xs d-inline-flex align-items-center gap-2" id="topViewRequirementsBtn" style="border-radius: 10px; font-size: 0.85rem; background: #fff;">
                <i class="fa fa-clipboard-check text-primary"></i>
                <span>Document Checklist</span>
            </button>

            <!-- Public Tracker Quick Link -->
            <a href="<?php echo URLROOT; ?>/home/track_admission" target="_blank" class="btn btn-outline-secondary fw-semibold px-3 py-2 d-inline-flex align-items-center gap-2 shadow-xs" title="Open Public Admission Status Tracker" style="border-radius: 10px; font-size: 0.85rem; background: #fff;">
                <i class="fa fa-search-location text-info"></i>
                <span>Public Tracker</span>
            </a>

            <!-- Direct Student Admission -->
            <a href="<?php echo URLROOT; ?>/students/admission" class="btn btn-dark fw-semibold px-3 py-2 d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-size: 0.85rem;">
                <i class="fa fa-user-plus text-warning"></i>
                <span>Direct Admission</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. ENROLMENT SUCCESS NOTIFICATION BANNER (When converted)                 -->
    <!-- ========================================================================= -->
    <?php if(!empty($lastEnrolled)): ?>
        <div class="card border-0 shadow-md mb-4" style="background: linear-gradient(135deg, #065f46 0%, #047857 100%); color: #fff; border-radius: 16px;">
            <div class="card-body p-3.5 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="fa fa-user-check fa-lg"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="fw-bold mb-0 text-white fs-6">Candidate Approved &amp; Enrolled into Class!</h6>
                            <span class="badge bg-warning text-dark fw-bold px-2 py-0.5">Class Roster Updated</span>
                        </div>
                        <p class="small mb-0 text-white-50 mt-0.5">
                            <strong><?php echo htmlspecialchars($lastEnrolled['name']); ?></strong> is officially assigned to <strong><?php echo htmlspecialchars($lastEnrolled['class_name']); ?></strong> (Admission No: <span class="text-white font-monospace fw-bold"><?php echo htmlspecialchars($lastEnrolled['admission_no']); ?></span> &bull; Roll No: <span class="text-white font-monospace fw-bold"><?php echo htmlspecialchars($lastEnrolled['roll_no']); ?></span>).
                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <?php if(!empty($lastEnrolled['id'])): ?>
                        <!-- Printable Fee Challan / Slip -->
                        <a href="<?php echo URLROOT; ?>/home/challan?admission_no=<?php echo urlencode($lastEnrolled['admission_no']); ?>" target="_blank" class="btn btn-warning btn-sm fw-bold px-3 text-dark shadow-xs" style="border-radius: 8px;">
                            <i class="fa fa-receipt me-1"></i>3-Copy Fee Challan
                        </a>
                        <!-- Collect Fee in Office -->
                        <a href="<?php echo URLROOT; ?>/fees/collect?student_id=<?php echo $lastEnrolled['id']; ?>" class="btn btn-light btn-sm fw-bold px-3 text-dark shadow-xs" style="border-radius: 8px;">
                            <i class="fa fa-money-bill-wave text-success me-1"></i>Collect Fee
                        </a>
                        <!-- Official Admission Form -->
                        <a href="<?php echo URLROOT; ?>/students/printAdmission?student_id=<?php echo $lastEnrolled['id']; ?>" target="_blank" class="btn btn-outline-light btn-sm fw-bold px-3" style="border-radius: 8px;">
                            <i class="fa fa-print me-1"></i>Print Dossier
                        </a>
                    <?php endif; ?>
                    <?php if(!empty($lastEnrolled['class_id'])): ?>
                        <a href="<?php echo URLROOT; ?>/students/index?class_id=<?php echo $lastEnrolled['class_id']; ?>" class="btn btn-outline-light btn-sm fw-semibold px-2.5" style="border-radius: 8px;">
                            <i class="fa fa-users me-1"></i>Class Roster
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['flash_success']) && empty($lastEnrolled)): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center gap-2" role="alert">
            <i class="fa fa-check-circle fs-5 text-success"></i>
            <div><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif(isset($_SESSION['flash_success'])): unset($_SESSION['flash_success']); endif; ?>

    <?php if(isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center gap-2" role="alert">
            <i class="fa fa-exclamation-triangle fs-5 text-danger"></i>
            <div><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- ========================================================================= -->
    <!-- 3. TOP KPI METRIC CARDS (Interactive Quick Filters)                       -->
    <!-- ========================================================================= -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Applications -->
        <div class="col-6 col-md-3">
            <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=all&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" class="text-decoration-none">
                <div class="oa-stat-card <?php echo ($data['filter_status'] === 'all') ? 'active-filter' : ''; ?>">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="oa-stat-label">Total Applications</span>
                        <div class="oa-stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fa fa-layer-group"></i>
                        </div>
                    </div>
                    <div class="oa-stat-value text-dark"><?php echo (int)$counts->total_all; ?></div>
                    <div class="text-muted small mt-1 d-flex align-items-center justify-content-between">
                        <span>All logged records</span>
                        <i class="fa fa-arrow-right text-muted opacity-50 small"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 2: Pending Inbox -->
        <div class="col-6 col-md-3">
            <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=pending&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" class="text-decoration-none">
                <div class="oa-stat-card <?php echo ($data['filter_status'] === 'pending' || $data['filter_status'] === 'New' || empty($data['filter_status'])) ? 'active-filter' : ''; ?>">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="oa-stat-label text-warning-emphasis">Pending Action</span>
                        <div class="oa-stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fa fa-envelope-open-text"></i>
                        </div>
                    </div>
                    <div class="oa-stat-value text-warning-emphasis d-flex align-items-center gap-2">
                        <span><?php echo (int)$counts->total_pending; ?></span>
                        <?php if(!empty($counts->total_pending)): ?>
                            <span class="oa-pulse-dot text-warning"></span>
                        <?php endif; ?>
                    </div>
                    <div class="text-muted small mt-1 d-flex align-items-center justify-content-between">
                        <span>Awaiting screening</span>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5" style="font-size: 0.68rem;">Action Required</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 3: In Contact / Follow Up -->
        <div class="col-6 col-md-3">
            <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Follow+Up&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" class="text-decoration-none">
                <div class="oa-stat-card <?php echo ($data['filter_status'] === 'Follow Up') ? 'active-filter' : ''; ?>">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="oa-stat-label text-info-emphasis">In Follow-Up</span>
                        <div class="oa-stat-icon bg-info bg-opacity-10 text-info">
                            <i class="fa fa-comments"></i>
                        </div>
                    </div>
                    <div class="oa-stat-value text-info-emphasis"><?php echo (int)$counts->total_follow; ?></div>
                    <div class="text-muted small mt-1 d-flex align-items-center justify-content-between">
                        <span>Parent contacted</span>
                        <i class="fa fa-phone-alt text-info opacity-50 small"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 4: Enrolled in Class -->
        <div class="col-6 col-md-3">
            <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Approved&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" class="text-decoration-none">
                <div class="oa-stat-card <?php echo (strtolower($data['filter_status']) === 'approved' || $data['filter_status'] === 'enrolled') ? 'active-filter' : ''; ?>">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="oa-stat-label text-success">Admitted &amp; Enrolled</span>
                        <div class="oa-stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fa fa-user-check"></i>
                        </div>
                    </div>
                    <div class="oa-stat-value text-success"><?php echo (int)$counts->total_approved; ?></div>
                    <div class="text-muted small mt-1 d-flex align-items-center justify-content-between">
                        <span>Moved to class roster</span>
                        <i class="fa fa-check-double text-success opacity-50 small"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. SEGMENTED NAVIGATION TABS & CONTEXT PILLS                              -->
    <!-- ========================================================================= -->
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
        <div class="oa-nav-container d-inline-flex flex-wrap gap-1">
            <!-- Tab: Pending Inbox -->
            <a class="oa-nav-pill <?php echo ($data['filter_status'] === 'pending' || $data['filter_status'] === 'New' || empty($data['filter_status'])) ? 'active' : ''; ?>" 
               href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=pending&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>">
                <i class="fa fa-inbox"></i>
                <span>Pending Inbox</span>
                <span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill"><?php echo (int)$counts->total_pending; ?></span>
            </a>

            <!-- Tab: Enrolled in Class -->
            <a class="oa-nav-pill <?php echo (strtolower($data['filter_status']) === 'approved' || $data['filter_status'] === 'enrolled') ? 'active' : ''; ?>" 
               href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Approved&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>">
                <i class="fa fa-user-check"></i>
                <span>Enrolled Archive</span>
                <span class="badge bg-success-subtle text-success border px-2 py-0.5 rounded-pill"><?php echo (int)$counts->total_approved; ?></span>
            </a>

            <!-- Tab: Follow Up -->
            <a class="oa-nav-pill <?php echo ($data['filter_status'] === 'Follow Up') ? 'active' : ''; ?>" 
               href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Follow+Up&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>">
                <i class="fa fa-comments"></i>
                <span>Follow-Up</span>
                <span class="badge bg-info-subtle text-info border px-2 py-0.5 rounded-pill"><?php echo (int)$counts->total_follow; ?></span>
            </a>

            <!-- Tab: All Applications -->
            <a class="oa-nav-pill <?php echo ($data['filter_status'] === 'all') ? 'active' : ''; ?>" 
               href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=all&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>">
                <i class="fa fa-list-ul"></i>
                <span>All Records</span>
                <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill"><?php echo (int)$counts->total_all; ?></span>
            </a>

            <!-- Tab: Rejected (if any) -->
            <?php if(!empty($counts->total_rejected)): ?>
                <a class="oa-nav-pill <?php echo ($data['filter_status'] === 'Rejected') ? 'active' : ''; ?>" 
                   href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Rejected&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>">
                    <i class="fa fa-ban"></i>
                    <span>Rejected</span>
                    <span class="badge bg-danger text-white px-2 py-0.5 rounded-pill"><?php echo (int)$counts->total_rejected; ?></span>
                </a>
            <?php endif; ?>
        </div>

        <div class="d-flex align-items-center gap-2">
            <?php if($data['filter_status'] === 'pending' || empty($data['filter_status']) || $data['filter_status'] === 'New'): ?>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5" style="border-radius: 8px;">
                    <i class="fa fa-info-circle text-warning"></i>
                    <span>Applicants approved here are removed from inbox and transferred directly to class lists.</span>
                </span>
            <?php elseif(strtolower($data['filter_status']) === 'approved' || $data['filter_status'] === 'enrolled'): ?>
                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1.5" style="border-radius: 8px;">
                    <i class="fa fa-check-double text-success"></i>
                    <span>Showing students who completed verification and have active Admission &amp; Roll numbers.</span>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. FILTER, SEARCH & SORT COMMAND TOOLBAR                                  -->
    <!-- ========================================================================= -->
    <div class="oa-filter-bar mb-4">
        <form id="filterForm" method="GET" action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions" class="row g-2 align-items-center">
            <!-- Search input with real-time DOM filter -->
            <div class="col-lg-4 col-md-12">
                <div class="oa-search-box">
                    <i class="fa fa-search"></i>
                    <input type="text" name="search" id="appSearchInput" class="form-control oa-search-input" placeholder="Search candidate, father, mobile, ref..." value="<?php echo htmlspecialchars($data['search'] ?? ''); ?>" autocomplete="off">
                    <button type="button" id="clearSearchBtn" class="oa-search-clear" title="Clear Search"><i class="fa fa-times-circle"></i></button>
                </div>
            </div>

            <!-- Status Dropdown Filter -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted small" style="border-radius: 10px 0 0 10px;">
                        <i class="fa fa-filter"></i>
                    </span>
                    <select name="status" class="form-select auto-submit-select bg-light border-start-0" style="border-radius: 0 10px 10px 0; font-size: 0.86rem;">
                        <option value="pending" <?php echo ($data['filter_status'] === 'pending') ? 'selected' : ''; ?>>Pending / Unprocessed</option>
                        <option value="Approved" <?php echo (strtolower($data['filter_status']) === 'approved' || $data['filter_status'] === 'enrolled') ? 'selected' : ''; ?>>Enrolled / Approved</option>
                        <option value="all" <?php echo ($data['filter_status'] === 'all') ? 'selected' : ''; ?>>All Statuses</option>
                        <option value="New" <?php echo ($data['filter_status'] === 'New') ? 'selected' : ''; ?>>New Submissions Only</option>
                        <option value="Follow Up" <?php echo ($data['filter_status'] === 'Follow Up') ? 'selected' : ''; ?>>In Follow-Up / Contacted</option>
                        <option value="Rejected" <?php echo ($data['filter_status'] === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
            </div>

            <!-- Target Class Dropdown Filter -->
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted small" style="border-radius: 10px 0 0 10px;">
                        <i class="fa fa-graduation-cap"></i>
                    </span>
                    <select name="class_id" class="form-select auto-submit-select bg-light border-start-0" style="border-radius: 0 10px 10px 0; font-size: 0.86rem;">
                        <option value="all" <?php echo ($data['filter_class'] === 'all') ? 'selected' : ''; ?>>All Target Classes</option>
                        <?php foreach($data['classes'] as $cls): ?>
                            <option value="<?php echo $cls->id; ?>" <?php echo ($data['filter_class'] == $cls->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cls->class_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Sort By Dropdown -->
            <div class="col-lg-2 col-md-4 col-sm-12">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted small" style="border-radius: 10px 0 0 10px;">
                        <i class="fa fa-sort"></i>
                    </span>
                    <select name="sort" class="form-select auto-submit-select bg-light border-start-0" style="border-radius: 0 10px 10px 0; font-size: 0.86rem;">
                        <option value="AZ" <?php echo ($data['sort'] === 'AZ') ? 'selected' : ''; ?>>Name (A &rarr; Z)</option>
                        <option value="ZA" <?php echo ($data['sort'] === 'ZA') ? 'selected' : ''; ?>>Name (Z &rarr; A)</option>
                        <option value="DATE_DESC" <?php echo ($data['sort'] === 'DATE_DESC') ? 'selected' : ''; ?>>Newest First</option>
                        <option value="DATE_ASC" <?php echo ($data['sort'] === 'DATE_ASC') ? 'selected' : ''; ?>>Oldest First</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. SLIDE-DOWN / STICKY BULK ACTION BAR (When checkboxes selected)         -->
    <!-- ========================================================================= -->
    <div id="bulkActionBar" class="oa-bulk-bar mb-4 p-3 d-none">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-success px-3 py-2 fs-6 fw-bold rounded-pill">
                    <i class="fa fa-check-square me-1.5"></i><span id="selectedCount">0</span> Applications Selected
                </span>
                <span class="small text-white-50">Select an executive batch action to apply across all checked candidates:</span>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form id="bulkAdmissionsForm" action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="POST" class="d-inline-flex gap-2 align-items-center m-0">
                    <button type="submit" name="bulk_action" value="approve" class="btn btn-success btn-sm fw-bold px-3 d-inline-flex align-items-center gap-1.5" style="border-radius: 8px;">
                        <i class="fa fa-check-double"></i>
                        <span>Bulk Approve &amp; Enrol</span>
                    </button>
                    
                    <button type="button" id="bulkPrintBtn" class="btn btn-primary btn-sm fw-bold px-3 d-inline-flex align-items-center gap-1.5" style="border-radius: 8px;">
                        <i class="fa fa-print"></i>
                        <span>Print Application Forms</span>
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle fw-semibold px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px;">
                            More Batch Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2" style="border-radius: 12px; min-width: 200px;">
                            <li>
                                <button type="submit" name="bulk_action" value="follow_up" class="dropdown-item py-2 fw-semibold text-info d-flex align-items-center gap-2">
                                    <i class="fa fa-comments"></i>
                                    <span>Mark as Follow-Up</span>
                                </button>
                            </li>
                            <li>
                                <button type="submit" name="bulk_action" value="reject" class="dropdown-item py-2 fw-semibold text-warning d-flex align-items-center gap-2">
                                    <i class="fa fa-ban"></i>
                                    <span>Mark as Rejected</span>
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button type="submit" name="bulk_action" value="delete" class="dropdown-item py-2 fw-semibold text-danger d-flex align-items-center gap-2" onclick="return confirm('WARNING: Are you sure you want to permanently delete all selected admission application records?');">
                                    <i class="fa fa-trash-alt"></i>
                                    <span>Delete Applications</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </form>

                <button type="button" id="deselectAllBtn" class="btn btn-outline-secondary btn-sm text-white-50 px-2.5" title="Clear selection" style="border-radius: 8px;">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. MAIN ADMISSIONS DATA TABLE CARD                                        -->
    <!-- ========================================================================= -->
    <div class="oa-table-card">
        <div class="oa-table-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fa fa-users"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <?php if($data['filter_status'] === 'pending' || $data['filter_status'] === 'New' || empty($data['filter_status'])): ?>
                            Pending Admission Enquiries &amp; Applications
                        <?php elseif(strtolower($data['filter_status']) === 'approved' || $data['filter_status'] === 'enrolled'): ?>
                            Enrolled &amp; Placed Students Archive
                        <?php elseif($data['filter_status'] === 'Follow Up'): ?>
                            Active Screening &amp; Follow-Up Pipeline
                        <?php else: ?>
                            All Online Admission Applications
                        <?php endif; ?>
                    </h6>
                    <small class="text-muted">Showing verified records matching your active filter criteria</small>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="headerPrintAllBtn" class="btn btn-outline-secondary btn-sm px-3 fw-semibold d-inline-flex align-items-center gap-1.5" style="border-radius: 8px; font-size: 0.8rem;" title="Print All Currently Filtered Applications">
                    <i class="fa fa-print"></i>
                    <span>Print Roster</span>
                </button>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 fw-bold rounded-pill" style="font-size: 0.78rem;">
                    <?php echo count($data['admissions']); ?> Records
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle oa-table" id="admissionsTable">
                <thead>
                    <tr>
                        <th class="ps-3 text-center" style="width: 4%;">
                            <input type="checkbox" id="selectAllCheckbox" class="form-check-input oa-checkbox" title="Select / Deselect All">
                        </th>
                        <th style="width: 4%;">Ref #</th>
                        <th style="width: 23%;">Candidate Particulars</th>
                        <th style="width: 17%;">Father / Guardian</th>
                        <th style="width: 16%;">Contact &amp; WhatsApp</th>
                        <th style="width: 13%;">Target Class</th>
                        <th style="width: 10%;">Received</th>
                        <th class="text-center" style="width: 9%;">Status</th>
                        <th class="text-end pe-3" style="width: 14%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($data['admissions'])): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <?php if($data['filter_status'] === 'pending' || $data['filter_status'] === 'New' || empty($data['filter_status'])): ?>
                                    <div class="py-4">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                            <i class="fa fa-clipboard-check fa-2x"></i>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1">All Caught Up! Inbox is Clear.</h5>
                                        <p class="text-muted small mb-3" style="max-width: 480px; margin: 0 auto;">
                                            There are no pending admission applications waiting for screening right now.<br>
                                            All approved students have been placed into their respective classes.
                                        </p>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=Approved" class="btn btn-sm btn-outline-success fw-bold px-3" style="border-radius: 8px;">
                                                <i class="fa fa-user-check me-1"></i>View Enrolled Students
                                            </a>
                                            <button type="button" class="btn btn-sm btn-primary fw-bold px-3" data-bs-toggle="modal" data-bs-target="#newOnlineAdmissionModal" style="border-radius: 8px;">
                                                <i class="fa fa-plus-circle me-1"></i>Register New Application
                                            </button>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="py-4">
                                        <div class="rounded-circle bg-light text-secondary d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                            <i class="fa fa-search fa-2x text-muted opacity-50"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">No Matching Admission Applications Found</h6>
                                        <p class="small text-muted mb-3">No records match the current filter or search criteria.</p>
                                        <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=all" class="btn btn-sm btn-outline-primary fw-bold px-3" style="border-radius: 8px;">
                                            <i class="fa fa-redo me-1"></i>Reset All Filters
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $avatarPalette = [
                            ['bg' => '#e0e7ff', 'text' => '#4338ca'], // Indigo
                            ['bg' => '#dbeafe', 'text' => '#1d4ed8'], // Blue
                            ['bg' => '#dcfce7', 'text' => '#15803d'], // Emerald
                            ['bg' => '#fef3c7', 'text' => '#b45309'], // Amber
                            ['bg' => '#f3e8ff', 'text' => '#7e22ce'], // Purple
                            ['bg' => '#fae8ff', 'text' => '#a21caf'], // Fuchsia
                            ['bg' => '#ffedd5', 'text' => '#c2410c'], // Orange
                            ['bg' => '#ccfbf1', 'text' => '#0f766e'], // Teal
                        ];
                        ?>
                        <?php foreach($data['admissions'] as $idx => $adm): ?>
                            <?php 
                                $isEnrolled = !empty($adm->enrolled_student_id);
                                $cleanPhone = oa_get_pak_phone($adm->phone ?? '');
                                $initials = oa_get_initials($adm->name);
                                $colorIdx = abs(crc32($adm->name)) % count($avatarPalette);
                                $avatarBg = $avatarPalette[$colorIdx]['bg'];
                                $avatarColor = $avatarPalette[$colorIdx]['text'];
                                $ageStr = oa_calc_age($adm->dob ?? null);

                                // Parse description parts if available
                                $bformStr = '';
                                if (!empty($adm->description) && preg_match('/Student B-Form:\s*([^\|]+)/i', $adm->description, $m)) {
                                    $bformStr = trim($m[1]);
                                }

                                // Status styling
                                $rawStatus = strtolower($adm->status ?? 'new');
                            ?>
                            <tr class="adm-row" 
                                data-id="<?php echo $adm->id; ?>" 
                                data-name="<?php echo htmlspecialchars(strtolower($adm->name)); ?>" 
                                data-father="<?php echo htmlspecialchars(strtolower($adm->father_name ?? '')); ?>" 
                                data-phone="<?php echo htmlspecialchars($adm->phone ?? ''); ?>"
                                data-class="<?php echo htmlspecialchars(strtolower($adm->class_name ?? '')); ?>">
                                
                                <!-- Checkbox -->
                                <td class="ps-3 text-center">
                                    <input type="checkbox" name="admission_ids[]" value="<?php echo $adm->id; ?>" class="form-check-input oa-checkbox adm-checkbox" form="bulkAdmissionsForm">
                                </td>

                                <!-- Application Ref # -->
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace px-2 py-1" style="font-size: 0.72rem;">
                                        #<?php echo (int)$adm->id; ?>
                                    </span>
                                </td>

                                <!-- Candidate Particulars -->
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <!-- Avatar -->
                                        <div class="oa-avatar" style="background-color: <?php echo $avatarBg; ?>; color: <?php echo $avatarColor; ?>;">
                                            <?php echo $initials; ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark text-capitalize mb-0" style="font-size: 0.92rem; line-height: 1.25;">
                                                <?php echo htmlspecialchars($adm->name); ?>
                                            </div>
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap mt-1">
                                                <!-- Gender Pill -->
                                                <?php if(strtolower($adm->gender ?? '') === 'female'): ?>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                        <i class="fa fa-female me-0.5"></i>Female
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                        <i class="fa fa-male me-0.5"></i>Male
                                                    </span>
                                                <?php endif; ?>

                                                <!-- Age or DOB -->
                                                <?php if(!empty($ageStr)): ?>
                                                    <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                        <i class="fa fa-birthday-cake text-warning me-1"></i><?php echo $ageStr; ?>
                                                    </span>
                                                <?php elseif(!empty($adm->dob)): ?>
                                                    <span class="badge bg-light text-muted border px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                        <?php echo date('d M Y', strtotime($adm->dob)); ?>
                                                    </span>
                                                <?php endif; ?>

                                                <!-- B-Form chip -->
                                                <?php if(!empty($bformStr)): ?>
                                                    <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 0.66rem;" title="Student NADRA B-Form">
                                                        <i class="fa fa-id-card text-muted me-0.5"></i><?php echo htmlspecialchars($bformStr); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Father / Guardian Particulars -->
                                <td>
                                    <div class="fw-semibold text-dark text-capitalize mb-0" style="font-size: 0.88rem;">
                                        <?php echo htmlspecialchars($adm->father_name ?: ($adm->guardian_name ?: 'Not Provided')); ?>
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                        <span class="text-muted small">
                                            <i class="fa fa-user-shield text-secondary me-1"></i><?php echo htmlspecialchars($adm->guardian_relation ?: 'Father'); ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Contact & Communication -->
                                <td>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <a href="tel:<?php echo htmlspecialchars($adm->phone); ?>" class="fw-bold text-dark text-decoration-none" title="Click to call parent" style="font-size: 0.86rem;">
                                            <i class="fa fa-phone-alt text-success me-1" style="font-size: 0.78rem;"></i><?php echo htmlspecialchars($adm->phone); ?>
                                        </a>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <!-- WhatsApp 1-Click Link -->
                                        <?php 
                                            $schoolNameWa = htmlspecialchars($dynamicSchoolName ?? SITENAME, ENT_QUOTES, 'UTF-8');
                                            $waMsg = urlencode("Assalam-o-Alaikum, regarding the admission application for {$adm->name} at {$schoolNameWa}. Please let us know if you need any assistance with document submission.");
                                        ?>
                                        <a href="https://wa.me/<?php echo $cleanPhone; ?>?text=<?php echo $waMsg; ?>" target="_blank" class="oa-whatsapp-btn shadow-xs" title="Chat on WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                            <span>WhatsApp</span>
                                        </a>

                                        <?php if(!empty($adm->email)): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($adm->email); ?>" class="text-muted small text-truncate text-decoration-none" style="max-width: 90px;" title="<?php echo htmlspecialchars($adm->email); ?>">
                                                <i class="fa fa-envelope text-primary"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Target Class & Enrolment -->
                                <td>
                                    <?php if($isEnrolled): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold" style="font-size: 0.78rem;">
                                            <i class="fa fa-check-circle me-1"></i><?php echo htmlspecialchars($adm->class_name ?? 'Class'); ?>
                                            <?php if(!empty($adm->enrolled_section_name)): ?>
                                                &bull; Sec <?php echo htmlspecialchars($adm->enrolled_section_name); ?>
                                            <?php endif; ?>
                                        </span>
                                        <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
                                            <?php if(!empty($adm->enrolled_roll_no)): ?>
                                                <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.68rem;">Roll: <?php echo htmlspecialchars($adm->enrolled_roll_no); ?></span>
                                            <?php endif; ?>
                                            <?php if(!empty($adm->enrolled_admission_no)): ?>
                                                <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.68rem;">Adm: <?php echo htmlspecialchars($adm->enrolled_admission_no); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold" style="font-size: 0.78rem;">
                                            <i class="fa fa-graduation-cap me-1"></i><?php echo htmlspecialchars($adm->class_name ?? 'Not Assigned'); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Submission Date -->
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        <?php echo oa_relative_time($adm->date ?? $adm->created_at ?? ''); ?>
                                    </div>
                                    <span class="text-muted" style="font-size: 0.72rem;">
                                        <i class="fa fa-laptop me-0.5 text-info"></i>Web Form
                                    </span>
                                </td>

                                <!-- Status Badge -->
                                <td class="text-center">
                                    <?php if($isEnrolled): ?>
                                        <span class="badge-status-enrolled">
                                            <i class="fa fa-check-circle"></i>
                                            <span>Enrolled</span>
                                        </span>
                                    <?php else: ?>
                                        <?php if($rawStatus === 'new'): ?>
                                            <span class="badge-status-new">
                                                <span class="oa-pulse-dot"></span>
                                                <span>New Enquiry</span>
                                            </span>
                                        <?php elseif($rawStatus === 'follow up' || $rawStatus === 'contacted'): ?>
                                            <span class="badge-status-follow">
                                                <i class="fa fa-comments"></i>
                                                <span>Follow Up</span>
                                            </span>
                                        <?php elseif($rawStatus === 'approved'): ?>
                                            <span class="badge-status-enrolled">
                                                <i class="fa fa-check"></i>
                                                <span>Approved</span>
                                            </span>
                                        <?php elseif($rawStatus === 'rejected'): ?>
                                            <span class="badge-status-rejected">
                                                <i class="fa fa-ban"></i>
                                                <span>Rejected</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary px-2 py-1 fw-semibold" style="border-radius: 20px; font-size: 0.74rem;">
                                                <?php echo htmlspecialchars(ucfirst($adm->status ?? 'New')); ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions -->
                                <td class="text-end pe-3 text-nowrap">
                                    <?php if($isEnrolled): ?>
                                        <!-- Enrolled Student Quick Actions -->
                                        <a href="<?php echo URLROOT; ?>/home/challan?admission_no=<?php echo urlencode($adm->enrolled_admission_no ?? ''); ?>" target="_blank" class="btn btn-sm btn-warning text-dark fw-bold me-1 shadow-xs oa-quick-btn" title="Generate / Print 3-Copy Bank Fee Challan Slip">
                                            <i class="fa fa-receipt"></i>
                                            <span>Challan</span>
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/fees/collect?student_id=<?php echo $adm->enrolled_student_id; ?>" class="btn btn-sm btn-success text-white fw-bold me-1 shadow-xs oa-quick-btn" title="Collect Admission Fee">
                                            <i class="fa fa-money-bill-wave"></i>
                                            <span>Fee</span>
                                        </a>
                                    <?php else: ?>
                                        <!-- Not Enrolled: 1-Click / Modal Approve & Place into Class -->
                                        <button type="button" class="btn btn-sm btn-success text-white fw-bold me-1 shadow-xs oa-quick-btn btn-open-enroll-modal"
                                            data-id="<?php echo $adm->id; ?>" 
                                            data-name="<?php echo htmlspecialchars($adm->name); ?>" 
                                            data-father="<?php echo htmlspecialchars($adm->father_name ?: ($adm->guardian_name ?: 'N/A')); ?>" 
                                            data-class-id="<?php echo $adm->class_id; ?>" 
                                            data-class-name="<?php echo htmlspecialchars($adm->class_name ?? 'Class'); ?>" 
                                            data-phone="<?php echo htmlspecialchars($adm->phone); ?>" 
                                            title="Approve Candidate &amp; Enrol into Class Roster">
                                            <i class="fa fa-user-check"></i>
                                            <span>Approve &amp; Enrol</span>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Quick Print Button -->
                                    <a href="<?php echo URLROOT; ?>/frontoffice/printAdmissionForm/<?php echo $adm->id; ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-1 oa-quick-btn" title="Print Admission Dossier (A4 Form)">
                                        <i class="fa fa-print"></i>
                                    </a>

                                    <!-- Quick Required Docs Checklist Button -->
                                    <button type="button" class="btn btn-sm btn-outline-info me-1 oa-quick-btn btn-open-req-docs" 
                                        data-id="<?php echo $adm->id; ?>" 
                                        data-name="<?php echo htmlspecialchars($adm->name); ?>" 
                                        data-father="<?php echo htmlspecialchars($adm->father_name ?: ($adm->guardian_name ?: 'N/A')); ?>" 
                                        data-class="<?php echo htmlspecialchars($adm->class_name ?? 'Not Assigned'); ?>" 
                                        data-phone="<?php echo htmlspecialchars($adm->phone); ?>" 
                                        title="View Required Documents Checklist Slip">
                                        <i class="fa fa-clipboard-check"></i>
                                    </button>

                                    <!-- Manage Dropdown -->
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-light border dropdown-toggle fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px; font-size: 0.8rem;">
                                            <i class="fa fa-ellipsis-v text-muted"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2" style="border-radius: 12px; min-width: 220px;">
                                            <?php if($isEnrolled): ?>
                                                <li><h6 class="dropdown-header text-uppercase text-muted" style="font-size: 0.68rem;">Enrolled Student</h6></li>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-warning" href="<?php echo URLROOT; ?>/home/challan?admission_no=<?php echo urlencode($adm->enrolled_admission_no ?? ''); ?>" target="_blank">
                                                        <i class="fa fa-receipt me-2 text-warning"></i>Generate Fee Challan
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-success" href="<?php echo URLROOT; ?>/fees/collect?student_id=<?php echo $adm->enrolled_student_id; ?>">
                                                        <i class="fa fa-money-bill-wave me-2 text-success"></i>Collect Admission Fee
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-dark" href="<?php echo URLROOT; ?>/students/printAdmission?student_id=<?php echo $adm->enrolled_student_id; ?>" target="_blank">
                                                        <i class="fa fa-print me-2 text-dark"></i>Official Admission Form (A4)
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-primary" href="<?php echo URLROOT; ?>/students/index?class_id=<?php echo $adm->enrolled_class_id ?: $adm->class_id; ?>">
                                                        <i class="fa fa-users me-2 text-primary"></i>Open Class Student Roster
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-secondary" href="<?php echo URLROOT; ?>/students/profile/<?php echo $adm->enrolled_student_id; ?>">
                                                        <i class="fa fa-id-badge me-2 text-secondary"></i>View Student Profile
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                            <?php else: ?>
                                                <li><h6 class="dropdown-header text-uppercase text-muted" style="font-size: 0.68rem;">Candidate Placement</h6></li>
                                                <li>
                                                    <a class="dropdown-item py-2 fw-semibold text-success btn-open-enroll-modal" href="javascript:void(0)"
                                                        data-id="<?php echo $adm->id; ?>" 
                                                        data-name="<?php echo htmlspecialchars($adm->name); ?>" 
                                                        data-father="<?php echo htmlspecialchars($adm->father_name ?: ($adm->guardian_name ?: 'N/A')); ?>" 
                                                        data-class-id="<?php echo $adm->class_id; ?>" 
                                                        data-class-name="<?php echo htmlspecialchars($adm->class_name ?? 'Class'); ?>" 
                                                        data-phone="<?php echo htmlspecialchars($adm->phone); ?>">
                                                        <i class="fa fa-user-check me-2 text-success"></i>Approve &amp; Enrol into Class
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="post">
                                                        <input type="hidden" name="id" value="<?php echo $adm->id; ?>">
                                                        <input type="hidden" name="update_status" value="1">
                                                        <input type="hidden" name="status" value="Approved">
                                                        <button type="submit" class="dropdown-item py-1.5 text-success fw-semibold">
                                                            <i class="fa fa-bolt me-2 text-warning"></i>Quick 1-Click Auto Enrol
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                            <?php endif; ?>

                                            <li><h6 class="dropdown-header text-uppercase text-muted" style="font-size: 0.68rem;">Dossier &amp; Checklist</h6></li>
                                            <li>
                                                <a class="dropdown-item py-1.5 fw-semibold text-secondary" href="<?php echo URLROOT; ?>/frontoffice/printAdmissionForm/<?php echo $adm->id; ?>" target="_blank">
                                                    <i class="fa fa-print me-2 text-primary"></i>Print Application Form
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-1.5 fw-semibold text-info btn-open-req-docs" href="javascript:void(0)"
                                                    data-id="<?php echo $adm->id; ?>" 
                                                    data-name="<?php echo htmlspecialchars($adm->name); ?>" 
                                                    data-father="<?php echo htmlspecialchars($adm->father_name ?: ($adm->guardian_name ?: 'N/A')); ?>" 
                                                    data-class="<?php echo htmlspecialchars($adm->class_name ?? 'Not Assigned'); ?>" 
                                                    data-phone="<?php echo htmlspecialchars($adm->phone); ?>">
                                                    <i class="fa fa-clipboard-check me-2 text-info"></i>Required Documents Checklist
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-1.5 fw-semibold text-dark copy-track-link" href="javascript:void(0)"
                                                    data-url="<?php echo URLROOT; ?>/home/track_admission?app_no=<?php echo $adm->id; ?>&phone=<?php echo urlencode($adm->phone ?? ''); ?>">
                                                    <i class="fa fa-link me-2 text-secondary"></i>Copy Public Tracking Link
                                                </a>
                                            </li>

                                            <li><hr class="dropdown-divider"></li>
                                            <li><h6 class="dropdown-header text-uppercase text-muted" style="font-size: 0.68rem;">Change Status</h6></li>
                                            <li>
                                                <form action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="post">
                                                    <input type="hidden" name="id" value="<?php echo $adm->id; ?>">
                                                    <input type="hidden" name="update_status" value="1">
                                                    <input type="hidden" name="status" value="Follow Up">
                                                    <button type="submit" class="dropdown-item py-1.5 text-info fw-semibold"><i class="fa fa-comments me-2"></i>Mark as Follow Up</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="post">
                                                    <input type="hidden" name="id" value="<?php echo $adm->id; ?>">
                                                    <input type="hidden" name="update_status" value="1">
                                                    <input type="hidden" name="status" value="Rejected">
                                                    <button type="submit" class="dropdown-item py-1.5 text-danger fw-semibold"><i class="fa fa-times me-2"></i>Mark as Rejected</button>
                                                </form>
                                            </li>
                                        </ul>
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

<!-- ========================================================================= -->
<!-- MODAL 1: REQUIRED DOCUMENTS SUBMISSION CHECKLIST & INSTRUCTIONS           -->
<!-- ========================================================================= -->
<div class="modal fade" id="requiredDocsModal" tabindex="-1" aria-labelledby="requiredDocsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-xl" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header oa-modal-header-indigo text-white py-3 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                        <i class="fa fa-clipboard-check fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="requiredDocsModalLabel">Mandatory Documents Submission Checklist</h5>
                        <small class="text-white-50">داخلہ فارم اور ضروری دستاویزات کی دفتر میں جمع آوری کی فہرست</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 bg-light" id="requiredDocsPrintArea">
                <!-- Candidate Brief Summary Banner -->
                <div class="card border-0 shadow-sm mb-3 bg-white" style="border-radius: 12px;">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-sm-6 col-md-3 border-end">
                                <span class="text-muted small d-block">Application Ref:</span>
                                <span class="fw-bold text-primary fs-6 font-monospace" id="reqCandidateRef">ADM-<?php echo !empty($submittedInfo['id']) ? '#' . (int)$submittedInfo['id'] : '---'; ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3 border-end">
                                <span class="text-muted small d-block">Candidate Name:</span>
                                <span class="fw-bold text-dark text-capitalize" id="reqCandidateName"><?php echo htmlspecialchars($submittedInfo['name'] ?? 'Candidate'); ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3 border-end">
                                <span class="text-muted small d-block">Father / Guardian:</span>
                                <span class="fw-bold text-dark text-capitalize" id="reqCandidateFather"><?php echo htmlspecialchars($submittedInfo['father_name'] ?? 'Father / Guardian'); ?></span>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <span class="text-muted small d-block">Target Class:</span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold" id="reqCandidateClass">Class</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mandatory Notice Callout Banner -->
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-start gap-3 mb-4 p-3 rounded-3" role="alert">
                    <i class="fa fa-exclamation-triangle fs-3 text-warning mt-1"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">
                            Important Admission Instructions / ضروری ہدایات برائے داخلہ
                        </h6>
                        <p class="small text-muted mb-1" style="line-height: 1.55;">
                            آن لائن داخلہ فارم جمع ہونے کے بعد داخلہ کی حتمی تصدیق کے لیے <strong>درج ذیل تمام مطلوبہ دستاویزات کی تصدیق شدہ فوٹو کاپیاں بمعہ پرنٹ شدہ داخلہ فارم</strong> 3 یوم کے اندر اسکول کے داخلہ دفتر میں جمع کروانا لازمی ہیں۔
                        </p>
                        <p class="small text-muted mb-0" style="line-height: 1.55;">
                            All candidates/parents must submit the attested photocopies of mandatory documents listed below along with the <strong>printed and signed admission form</strong> to the School Admissions Desk within <strong>3 working days</strong> for physical verification and enrolment completion.
                        </p>
                    </div>
                </div>

                <!-- Stylized Checklist of Required Documents -->
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="fa fa-folder-open text-primary me-2"></i>Mandatory Required Documents Checklist</span>
                    <span class="badge bg-light text-muted border">8 Items</span>
                </h6>

                <div class="list-group shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                    <!-- Item 1 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input oa-checkbox" type="checkbox" checked id="doc1">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">1. Printed &amp; Signed Online Admission Application Form</h6>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">Mandatory (1 Copy)</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                Complete printed copy of the online admission form with signatures of Father/Guardian and Student.
                                <br><span class="text-secondary small">آن لائن داخلہ فارم کا پرنٹ بمعہ والد اور امیدوار کے دستخط</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input oa-checkbox" type="checkbox" checked id="doc2">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">2. Passport Sized Colored Photographs</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">4 Photographs</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                4x Recent passport-sized colored photographs of candidate with sky-blue background (candidate name on back).
                                <br><span class="text-secondary small">امیدوار کی 4 عدد پاسپورٹ سائز تازہ تصاویر (نیلے بیک گراؤنڈ کے ساتھ)</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input oa-checkbox" type="checkbox" checked id="doc3">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">3. Father / Guardian CNIC Copies</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">2 Attested Copies</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                2x Attested photocopies of Father's or Legal Guardian's valid National Identity Card (CNIC / NICOP).
                                <br><span class="text-secondary small">والد یا قانونی سرپرست کے شناختی کارڈ کی 2 عدد تصدیق شدہ فوٹو کاپیاں</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input oa-checkbox" type="checkbox" checked id="doc4">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">4. Student NADRA B-Form / Smart Card</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">2 Attested Copies</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                2x Attested photocopies of NADRA Bay-Form (ب-فارم) or Municipal Birth Registration Certificate.
                                <br><span class="text-secondary small">امیدوار کے نادرا ب-فارم یا پیدائش سرٹیفکیٹ کی 2 عدد تصدیق شدہ نقول</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input oa-checkbox" type="checkbox" id="doc5">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">5. Original School Leaving Certificate (SLC / Transfer Certificate)</h6>
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle small">Original + Copy</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                Original SLC/Transfer Certificate from previous school (signed &amp; stamped by Head of Institution).
                                <br><span class="text-secondary small">پچھلے اسکول کا اصل اسکول لیونگ سرٹیفکیٹ (SLC) بمعہ 1 عدد کاپی</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 6 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input oa-checkbox" type="checkbox" id="doc6">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">6. Previous Examination Report Card / DMC</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">2 Attested Copies</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                2x Attested photocopies of the last annual/term progress report card / detailed marks certificate (DMC).
                                <br><span class="text-secondary small">سابقہ جماعت کے سالانہ امتحانی رزلٹ کارڈ / DMC کی 2 عدد تصدیق شدہ نقول</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 7 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white border-bottom">
                        <div class="form-check pt-1">
                            <input class="form-check-input oa-checkbox" type="checkbox" id="doc7">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">7. Character Certificate</h6>
                                <span class="badge bg-secondary-subtle text-secondary border small">For Class 6 &amp; Above</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                Character &amp; Conduct Certificate issued by Head of previous school (mandatory for Class 6 and above).
                                <br><span class="text-secondary small">سابقہ اسکول کا کیریکٹر سرٹیفکیٹ (چھٹی اور اس سے اوپر کی کلاسز کے لیے)</span>
                            </p>
                        </div>
                    </div>

                    <!-- Item 8 -->
                    <div class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 bg-white">
                        <div class="form-check pt-1">
                            <input class="form-check-input oa-checkbox" type="checkbox" id="doc8">
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark">8. Student Vaccination / Health Record Card</h6>
                                <span class="badge bg-info-subtle text-info border border-info-subtle small">1 Photocopy</span>
                            </div>
                            <p class="text-muted small mb-0 mt-1">
                                Photocopy of government immunization record or basic fitness certificate from a qualified doctor.
                                <br><span class="text-secondary small">حفاظتی ٹیکوں کا کارڈ یا میڈیکل فٹنس سرٹیفکیٹ کی نقل</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Office Schedule Footer Note -->
                <div class="p-3 bg-white rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="small text-muted">
                        <i class="fa fa-clock text-primary me-2"></i><strong>Admissions Office Timings:</strong> Monday to Saturday, 8:00 AM &ndash; 2:00 PM
                    </div>
                    <div class="small text-muted">
                        <i class="fa fa-map-marker-alt text-danger me-2"></i><strong>Venue:</strong> School Front Office &amp; Reception Desk
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal" style="border-radius: 8px;">
                    <i class="fa fa-times me-1"></i> Close
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary fw-bold px-3" id="reqPrintFormBtn" style="border-radius: 8px;">
                        <i class="fa fa-print me-1"></i> Print Admission Form
                    </button>
                    <button type="button" class="btn btn-success fw-bold px-3 shadow-sm" id="reqPrintChecklistSlipBtn" style="border-radius: 8px;">
                        <i class="fa fa-file-invoice me-1"></i> Print Requirements Slip
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 2: LOG NEW ONLINE ADMISSION APPLICATION (DESK ENTRY)                -->
<!-- ========================================================================= -->
<div class="modal fade" id="newOnlineAdmissionModal" tabindex="-1" aria-labelledby="newOnlineAdmissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-xl" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header oa-modal-header-emerald text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fa fa-plus-circle fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="newOnlineAdmissionModalLabel">Register Online Admission Application</h5>
                        <small class="text-white-50">Log new applicant particulars received online or via phone enquiry</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="POST" id="newOnlineAdmForm">
                <input type="hidden" name="create_online_admission" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
                
                <div class="modal-body p-4 bg-light">
                    <!-- Section 1: Student Particulars -->
                    <div class="card border-0 shadow-sm p-3.5 mb-3 bg-white" style="border-radius: 12px;">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="fa fa-user-graduate text-primary"></i>
                            <h6 class="fw-bold text-dark mb-0">1. Student Particulars</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Full Name of Student <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Muhammad Ali Khan">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Date of Birth</label>
                                <input type="date" name="dob" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Target Class <span class="text-danger">*</span></label>
                                <select name="class_id" class="form-select" required>
                                    <option value="">-- Select Target Class --</option>
                                    <?php foreach($data['classes'] as $cls): ?>
                                        <option value="<?php echo $cls->id; ?>"><?php echo htmlspecialchars($cls->class_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NADRA B-Form / Smart Card No.</label>
                                <input type="text" name="bform_cnic" class="form-control font-monospace" placeholder="e.g. 61101-1234567-1">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Guardian & Contact Details -->
                    <div class="card border-0 shadow-sm p-3.5 mb-3 bg-white" style="border-radius: 12px;">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="fa fa-users text-primary"></i>
                            <h6 class="fw-bold text-dark mb-0">2. Guardian &amp; Contact Details</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Father's Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="father_name" class="form-control" required placeholder="e.g. Tariq Mehmood Khan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Father's CNIC No.</label>
                                <input type="text" name="father_cnic" class="form-control font-monospace" placeholder="e.g. 61101-9876543-1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Primary Mobile / WhatsApp Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required placeholder="e.g. 0300-1234567">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="parent@gmail.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Residential Address</label>
                                <input type="text" name="address" class="form-control" placeholder="House #, Street, Mohallah / Sector, City">
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Previous School & Academic Records -->
                    <div class="card border-0 shadow-sm p-3.5 bg-white" style="border-radius: 12px;">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="fa fa-school text-primary"></i>
                            <h6 class="fw-bold text-dark mb-0">3. Previous School &amp; History</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Previous School Name</label>
                                <input type="text" name="previous_school" class="form-control" placeholder="e.g. Army Public School / Govt Model High School">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Last Class Passed</label>
                                <input type="text" name="last_class" class="form-control" placeholder="e.g. Class 7">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Marks / Grade</label>
                                <input type="text" name="last_grade" class="form-control" placeholder="e.g. 85% / Grade A">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm" style="border-radius: 8px;">
                        <i class="fa fa-save me-1.5"></i> Register Application &amp; View Checklist
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL 3: APPROVE & ENROL DIRECTLY INTO ACTIVE CLASS                       -->
<!-- ========================================================================= -->
<div class="modal fade" id="approveEnrollModal" tabindex="-1" aria-labelledby="approveEnrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-xl" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header oa-modal-header-emerald text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fa fa-user-check fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-white" id="approveEnrollModalLabel">
                            Approve &amp; Enrol into Class
                        </h5>
                        <small class="text-white-50">Assign student to active class roster &amp; section</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions?status=<?php echo urlencode($data['filter_status']); ?>&class_id=<?php echo urlencode($data['filter_class']); ?>&sort=<?php echo urlencode($data['sort']); ?>" method="POST" id="approveEnrollForm">
                <input type="hidden" name="enroll_student" value="1">
                <input type="hidden" name="id" id="enrollAdmId" value="">
                <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">

                <div class="modal-body p-4 bg-light">
                    <!-- Applicant Summary Box -->
                    <div class="p-3 bg-white rounded-3 border mb-3 shadow-xs">
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                            <div>
                                <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Candidate Name</span>
                                <h5 class="fw-bold text-dark mb-0" id="enrollStudentName">Muhammad Ali</h5>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border px-2.5 py-1 fw-bold" id="enrollTargetClassBadge">Class 8</span>
                        </div>
                        <div class="row g-2 small text-muted">
                            <div class="col-6">
                                <i class="fa fa-user-shield me-1 text-secondary"></i><span id="enrollFatherName">Father Name</span>
                            </div>
                            <div class="col-6">
                                <i class="fa fa-phone-alt me-1 text-success"></i><span id="enrollPhone">0300-1234567</span>
                            </div>
                        </div>
                    </div>

                    <!-- Enrolment Settings Form -->
                    <div class="bg-white p-3.5 rounded-3 border shadow-xs">
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase d-flex align-items-center gap-1.5">
                            <i class="fa fa-sliders-h text-primary"></i>
                            <span>Class Placement &amp; Roster Details</span>
                        </h6>

                        <div class="row g-3">
                            <!-- Class Selection -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-dark">Target Class <span class="text-danger">*</span></label>
                                <select name="class_id" id="enrollClassSelect" class="form-select" required>
                                    <option value="">-- Select Class --</option>
                                    <?php foreach($data['classes'] as $cls): ?>
                                        <option value="<?php echo $cls->id; ?>"><?php echo htmlspecialchars($cls->class_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Section Selection -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Section</label>
                                <select name="section_id" id="enrollSectionSelect" class="form-select">
                                    <option value="">-- Auto Assign Section --</option>
                                    <?php if(!empty($data['sections'])): ?>
                                        <?php foreach($data['sections'] as $sec): ?>
                                            <option value="<?php echo $sec->id; ?>" data-class-id="<?php echo $sec->class_id; ?>">
                                                <?php echo htmlspecialchars($sec->section_name); ?> (<?php echo htmlspecialchars($sec->class_name ?? ''); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Academic Session -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-dark">Academic Session</label>
                                <select name="academic_session_id" class="form-select">
                                    <?php if(!empty($data['sessions'])): ?>
                                        <?php foreach($data['sessions'] as $sess): ?>
                                            <option value="<?php echo $sess->id; ?>" <?php echo (!empty($sess->is_current) || (!empty($data['current_session']) && $data['current_session']->id == $sess->id)) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sess->session_name); ?> <?php echo !empty($sess->is_current) ? '(Current)' : ''; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">Current Session</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Roll No & Admission No (Auto if left blank) -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Roll Number</label>
                                <input type="text" name="roll_no" class="form-control font-monospace" placeholder="Auto next in class">
                                <span class="text-muted" style="font-size: 0.68rem;">Leave blank to auto-calculate</span>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Admission Number</label>
                                <input type="text" name="admission_no" class="form-control font-monospace" placeholder="Auto STD-<?php echo date('y'); ?>-xxx">
                                <span class="text-muted" style="font-size: 0.68rem;">Leave blank to auto-generate</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm" id="btnConfirmEnroll" style="border-radius: 8px;">
                        <i class="fa fa-check-circle me-1.5"></i> Confirm &amp; Enrol into Class
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT INTERACTIONS & FAST EVENT LISTENERS                            -->
<!-- ========================================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Auto submit filter form on dropdown select change
    const autoSelects = document.querySelectorAll('.auto-submit-select');
    autoSelects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // 2. Real-time DOM filtering for typing into search bar
    const searchInput = document.getElementById('appSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');

    function executeFilter(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('#admissionsTable tbody tr.adm-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const father = row.getAttribute('data-father') || '';
            const phone = row.getAttribute('data-phone') || '';
            const cls = row.getAttribute('data-class') || '';
            const id = row.getAttribute('data-id') || '';

            if (!query || name.includes(query) || father.includes(query) || phone.includes(query) || cls.includes(query) || id.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (clearSearchBtn) {
            clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';
        }
        updateBulkState();
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            executeFilter(this.value);
        });

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                executeFilter('');
                searchInput.focus();
            });
        }
    }

    // 3. Checkbox and Bulk Operations Handling
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.adm-checkbox');
    const bulkActionBar = document.getElementById('bulkActionBar');
    const selectedCountSpan = document.getElementById('selectedCount');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const bulkPrintBtn = document.getElementById('bulkPrintBtn');
    const headerPrintAllBtn = document.getElementById('headerPrintAllBtn');

    function updateBulkState() {
        const checked = Array.from(rowCheckboxes).filter(cb => cb.checked && cb.closest('tr').style.display !== 'none');
        const count = checked.length;
        if (selectedCountSpan) {
            selectedCountSpan.textContent = count;
        }

        if (count > 0) {
            bulkActionBar.classList.remove('d-none');
        } else {
            bulkActionBar.classList.add('d-none');
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        }

        if (selectAllCheckbox) {
            const visibleRows = Array.from(rowCheckboxes).filter(cb => cb.closest('tr').style.display !== 'none');
            selectAllCheckbox.checked = visibleRows.length > 0 && visibleRows.every(cb => cb.checked);
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            rowCheckboxes.forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = isChecked;
                }
            });
            updateBulkState();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkState);
    });

    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            rowCheckboxes.forEach(cb => { cb.checked = false; });
            updateBulkState();
        });
    }

    // Bulk Print Selected
    if (bulkPrintBtn) {
        bulkPrintBtn.addEventListener('click', function() {
            const checked = Array.from(rowCheckboxes).filter(cb => cb.checked && cb.closest('tr').style.display !== 'none');
            if (checked.length === 0) {
                alert('Please select at least one application to print.');
                return;
            }
            const ids = checked.map(cb => cb.value);
            const printUrl = '<?php echo URLROOT; ?>/frontoffice/printAdmissionForm?ids=' + ids.join(',');
            window.open(printUrl, '_blank');
        });
    }

    // Print all currently visible applications
    if (headerPrintAllBtn) {
        headerPrintAllBtn.addEventListener('click', function() {
            const visibleRows = Array.from(rowCheckboxes).filter(cb => cb.closest('tr').style.display !== 'none');
            if (visibleRows.length === 0) {
                alert('No application records are currently displayed to print.');
                return;
            }
            const ids = visibleRows.map(cb => cb.value);
            const printUrl = '<?php echo URLROOT; ?>/frontoffice/printAdmissionForm?ids=' + ids.join(',');
            window.open(printUrl, '_blank');
        });
    }

    // 4. Copy Public Tracking Link Helper
    document.querySelectorAll('.copy-track-link').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            if (url && navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    alert('Candidate tracking link copied to clipboard!\nShare this with parents via WhatsApp or SMS.');
                });
            }
        });
    });

    // 5. Required Documents Modal Interactive Data Binding
    const reqDocsModalEl = document.getElementById('requiredDocsModal');
    let reqDocsModal = null;
    if (reqDocsModalEl && typeof bootstrap !== 'undefined') {
        reqDocsModal = new bootstrap.Modal(reqDocsModalEl);
    }

    let currentReqAppId = null;

    function openRequirementsForCandidate(id, name, father, className, phone) {
        currentReqAppId = id || null;
        const refEl = document.getElementById('reqCandidateRef');
        const nameEl = document.getElementById('reqCandidateName');
        const fatherEl = document.getElementById('reqCandidateFather');
        const classEl = document.getElementById('reqCandidateClass');

        if (refEl) refEl.textContent = id ? 'ADM-#' + id : 'ADM-GENERAL';
        if (nameEl) nameEl.textContent = name || 'Student Applicant';
        if (fatherEl) fatherEl.textContent = father || 'Father / Guardian';
        if (classEl) classEl.textContent = className || 'All Classes';

        if (reqDocsModal) {
            reqDocsModal.show();
        }
    }

    document.querySelectorAll('.btn-open-req-docs').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const father = this.getAttribute('data-father');
            const className = this.getAttribute('data-class');
            const phone = this.getAttribute('data-phone');
            openRequirementsForCandidate(id, name, father, className, phone);
        });
    });

    const topViewReqBtn = document.getElementById('topViewRequirementsBtn');
    if (topViewReqBtn) {
        topViewReqBtn.addEventListener('click', function() {
            openRequirementsForCandidate('', 'Prospective Student', 'Father / Guardian', 'Standard Admission', '');
        });
    }

    // Print Admission Form from inside Required Docs Modal
    const reqPrintFormBtn = document.getElementById('reqPrintFormBtn');
    if (reqPrintFormBtn) {
        reqPrintFormBtn.addEventListener('click', function() {
            if (currentReqAppId) {
                window.open('<?php echo URLROOT; ?>/frontoffice/printAdmissionForm/' + currentReqAppId, '_blank');
            } else {
                window.open('<?php echo URLROOT; ?>/frontoffice/printAdmissionForm', '_blank');
            }
        });
    }

    // Print Checklist Slip with Printable Format
    const reqPrintChecklistSlipBtn = document.getElementById('reqPrintChecklistSlipBtn');
    if (reqPrintChecklistSlipBtn) {
        reqPrintChecklistSlipBtn.addEventListener('click', function() {
            const printContent = document.getElementById('requiredDocsPrintArea');
            if (!printContent) return;

            const printWin = window.open('', '_blank', 'width=850,height=900');
            printWin.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Admission Document Submission Checklist</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                    <style>
                        body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; background: #fff; padding: 25px; color: #0f172a; }
                        .checklist-header { border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 20px; }
                        .checklist-item { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; margin-bottom: 8px; }
                        @media print {
                            body { padding: 0; }
                            .no-print { display: none !important; }
                        }
                    </style>
                </head>
                <body>
                    <div class="text-center checklist-header">
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($dynamicSchoolName ?? SITENAME, ENT_QUOTES, 'UTF-8'); ?></h4>
                        <div class="text-muted small">Admissions Secretariat &bull; Candidate Required Documents Slip</div>
                        <div class="badge bg-dark mt-2">Submission Deadline: Within 3 Working Days</div>
                    </div>
                    ${printContent.innerHTML}
                    <div class="text-center mt-4 pt-3 border-top text-muted small">
                        Please submit all verified original documents and attested photocopies to the Admissions Desk.
                        <br>Office Timings: Monday &ndash; Saturday (8:00 AM &ndash; 2:00 PM)
                    </div>
                    <script>
                        window.onload = function() { window.print(); }
                    <\/script>
                </body>
                </html>
            `);
            printWin.document.close();
        });
    }

    // Auto popup on submission
    <?php if(!empty($submittedInfo)): ?>
        openRequirementsForCandidate(
            '<?php echo (int)$submittedInfo['id']; ?>',
            '<?php echo addslashes($submittedInfo['name']); ?>',
            '<?php echo addslashes($submittedInfo['father_name'] ?? ''); ?>',
            'Registered Class',
            '<?php echo addslashes($submittedInfo['phone'] ?? ''); ?>'
        );
    <?php endif; ?>

    // 6. Enroll Modal setup & populate
    const enrollModalEl = document.getElementById('approveEnrollModal');
    let enrollModal = null;
    if (enrollModalEl && typeof bootstrap !== 'undefined') {
        enrollModal = new bootstrap.Modal(enrollModalEl);
    }

    document.querySelectorAll('.btn-open-enroll-modal').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const name = this.dataset.name || 'Applicant';
            const father = this.dataset.father || 'N/A';
            const phone = this.dataset.phone || 'N/A';
            const classId = this.dataset.classId || '';
            const className = this.dataset.className || 'Target Class';

            document.getElementById('enrollAdmId').value = id;
            document.getElementById('enrollStudentName').textContent = name;
            document.getElementById('enrollFatherName').textContent = father;
            document.getElementById('enrollPhone').textContent = phone;
            document.getElementById('enrollTargetClassBadge').textContent = className;

            const classSelect = document.getElementById('enrollClassSelect');
            if (classSelect && classId) {
                classSelect.value = classId;
                filterSectionsByClass(classId);
            }

            if (enrollModal) {
                enrollModal.show();
            }
        });
    });

    const classSelect = document.getElementById('enrollClassSelect');
    if (classSelect) {
        classSelect.addEventListener('change', function() {
            filterSectionsByClass(this.value);
        });
    }

    function filterSectionsByClass(cId) {
        const secSelect = document.getElementById('enrollSectionSelect');
        if (!secSelect) return;
        const options = secSelect.querySelectorAll('option');
        options.forEach(opt => {
            if (!opt.value) {
                opt.style.display = '';
                return;
            }
            const optClass = opt.dataset.classId;
            if (!cId || optClass == cId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
        secSelect.value = '';
    }
});
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
