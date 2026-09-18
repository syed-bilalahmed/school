<?php require APPROOT . '/Views/layouts/header.php'; ?>

<?php
$notices = $data['notices'] ?? [];
$stats = $data['stats'] ?? ['total' => 0, 'student_count' => 0, 'staff_count' => 0, 'parent_count' => 0, 'urgent_count' => 0];
$filters = $data['filters'] ?? [];
$isAdmin = $data['isAdmin'] ?? false;
$userRole = $data['userRole'] ?? 'staff';

// Category color mappings
$categoryColors = [
    'General Notice'            => 'badge-primary-soft',
    'Academic / Exam Circular'  => 'badge-info-soft',
    'Fee & Accounts Reminder'   => 'badge-warning-soft',
    'Holiday Announcement'      => 'badge-success-soft',
    'Event & Sports'            => 'badge-purple-soft',
    'Emergency / Urgent Alert'  => 'badge-danger-soft',
    'Faculty & Staff Meeting'   => 'badge-dark-soft',
    'Library & Reading Notice'  => 'badge-info-soft'
];

function getRelativeTime($dateStr) {
    if (empty($dateStr)) return '';
    $timestamp = strtotime($dateStr);
    $diff = time() - $timestamp;
    if ($diff < 86400 && date('Y-m-d') === date('Y-m-d', $timestamp)) {
        return 'Today';
    } elseif ($diff < 172800 && date('Y-m-d', strtotime('-1 day')) === date('Y-m-d', $timestamp)) {
        return 'Yesterday';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' days ago';
    }
    return date('d M Y', $timestamp);
}
?>

<style>
/* Notice Board Modern Styling */
.notice-header-card {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 16px;
    padding: 24px 28px;
    color: #ffffff;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
    position: relative;
    overflow: hidden;
}
.notice-header-card::after {
    content: "\f0a1";
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    position: absolute;
    right: 25px;
    bottom: -15px;
    font-size: 110px;
    color: rgba(255, 255, 255, 0.04);
    pointer-events: none;
}
.kpi-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 20px;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}
.kpi-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.06);
    border-color: #cbd5e1;
}
.kpi-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.kpi-val {
    font-size: 24px;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
}
.kpi-lbl {
    font-size: 12.5px;
    font-weight: 600;
    color: #64748b;
    margin-top: 2px;
}
.filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 22px;
    margin-bottom: 24px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}
/* Notice Item Card */
.notice-item-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px 24px;
    margin-bottom: 18px;
    transition: all 0.2s ease;
    position: relative;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
}
.notice-item-card:hover {
    border-color: #94a3b8;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}
.notice-item-card.is-urgent {
    border-left: 5px solid #ef4444;
}
.notice-item-card.is-important {
    border-left: 5px solid #f59e0b;
}
.notice-item-card.is-normal {
    border-left: 5px solid #0284c7;
}
.notice-ref-badge {
    font-family: monospace;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 3px 8px;
    border-radius: 6px;
}
.notice-title {
    font-size: 18px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
    margin: 8px 0 10px 0;
}
.notice-body-excerpt {
    color: #334155;
    font-size: 13.5px;
    line-height: 1.6;
    margin-bottom: 16px;
    word-break: break-word;
}
.notice-footer-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f1f5f9;
    padding-top: 14px;
    flex-wrap: wrap;
    gap: 12px;
}
.author-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #64748b;
}
.author-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #0f172a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
}
/* Soft Badges */
.badge-soft {
    padding: 5px 12px;
    border-radius: 30px;
    font-weight: 700;
    font-size: 11.5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.badge-primary-soft { background: #e0f2fe; color: #0284c7; }
.badge-info-soft    { background: #e0e7ff; color: #4338ca; }
.badge-warning-soft { background: #fef3c7; color: #b45309; }
.badge-success-soft { background: #dcfce7; color: #15803d; }
.badge-danger-soft  { background: #fee2e2; color: #b91c1c; }
.badge-purple-soft  { background: #f3e8ff; color: #7e22ce; }
.badge-dark-soft    { background: #f1f5f9; color: #334155; }

.audience-pill {
    font-size: 11px;
    font-weight: 600;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 3px 8px;
    border-radius: 20px;
    color: #475569;
}
/* Summernote inside Bootstrap Modal Fixes */
.note-editor.note-frame {
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    box-shadow: none !important;
}
.note-toolbar {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 6px 10px !important;
}
.note-btn {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 6px !important;
    color: #334155 !important;
    padding: 4px 8px !important;
    font-size: 12px !important;
}
.note-btn:hover {
    background: #f1f5f9 !important;
    color: #0f172a !important;
}
.note-btn.active {
    background: #e0f2fe !important;
    color: #0284c7 !important;
    border-color: #bae6fd !important;
}
.note-editable {
    min-height: 180px !important;
    font-size: 14px !important;
    line-height: 1.65 !important;
    color: #1e293b !important;
    background: #ffffff !important;
}
.note-modal {
    z-index: 1065 !important;
}
</style>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<div class="container-fluid px-3 px-md-4 py-3">

    <!-- Flash Alerts -->
    <?php if(!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fa fa-check-circle me-2"></i> <?php echo htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if(!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> <?php echo htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Executive Header Card -->
    <div class="notice-header-card">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 text-white-50" style="font-size: 12px;">
                        <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-white-50 text-decoration-none"><i class="fa fa-home me-1"></i> Hub</a></li>
                        <li class="breadcrumb-item text-white-50">Communication</li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Notice Board</li>
                    </ol>
                </nav>
                <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="fa fa-bullhorn text-warning"></i> Campus Notice Board &amp; Circulars
                    <span class="badge bg-warning text-dark fs-6 rounded-pill"><?php echo count($notices); ?> Active</span>
                </h2>
                <p class="text-white-50 mb-0" style="font-size: 13.5px;">
                    Official institutional circulars, academic notices, holiday bulletins, and executive memorandums.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button onclick="window.print()" class="btn btn-outline-light btn-sm px-3 rounded-pill" title="Print Current Notice Board Summary">
                    <i class="fa fa-print me-1"></i> Print Board
                </button>
                <?php if($isAdmin): ?>
                <button type="button" class="btn btn-warning btn-sm px-4 fw-bold rounded-pill text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
                    <i class="fa fa-plus-circle me-1"></i> Post New Circular
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 4 KPI Insight Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="kpi-stat-card">
                <div class="kpi-icon-box bg-primary-subtle text-primary">
                    <i class="fa fa-bullhorn"></i>
                </div>
                <div>
                    <div class="kpi-val"><?php echo $stats['total']; ?></div>
                    <div class="kpi-lbl">Total Circulars</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-stat-card">
                <div class="kpi-icon-box bg-success-subtle text-success">
                    <i class="fa fa-user-graduate"></i>
                </div>
                <div>
                    <div class="kpi-val"><?php echo $stats['student_count']; ?></div>
                    <div class="kpi-lbl">Student Notices</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-stat-card">
                <div class="kpi-icon-box bg-info-subtle text-info">
                    <i class="fa fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <div class="kpi-val"><?php echo $stats['staff_count']; ?></div>
                    <div class="kpi-lbl">Staff Circulars</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="kpi-stat-card">
                <div class="kpi-icon-box bg-danger-subtle text-danger">
                    <i class="fa fa-triangle-exclamation"></i>
                </div>
                <div>
                    <div class="kpi-val"><?php echo $stats['urgent_count']; ?></div>
                    <div class="kpi-lbl">Urgent / Priority</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="filter-card">
        <form action="<?php echo URLROOT; ?>/notice/index" method="get" id="noticeFilterForm" class="row g-2 align-items-center">
            <!-- Search Keyword Input -->
            <div class="col-12 col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fa fa-search"></i></span>
                    <input type="text" name="search" id="liveSearchInput" class="form-control border-start-0" placeholder="Search notices by title or content..." value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>
            </div>

            <!-- Category Filter -->
            <div class="col-6 col-md-2">
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" <?php echo ($filters['category'] === 'all') ? 'selected' : ''; ?>>All Categories</option>
                    <option value="General Notice" <?php echo ($filters['category'] === 'General Notice') ? 'selected' : ''; ?>>General Notice</option>
                    <option value="Academic / Exam Circular" <?php echo ($filters['category'] === 'Academic / Exam Circular') ? 'selected' : ''; ?>>Academic &amp; Exams</option>
                    <option value="Fee & Accounts Reminder" <?php echo ($filters['category'] === 'Fee & Accounts Reminder') ? 'selected' : ''; ?>>Fee Reminders</option>
                    <option value="Holiday Announcement" <?php echo ($filters['category'] === 'Holiday Announcement') ? 'selected' : ''; ?>>Holidays &amp; Vacations</option>
                    <option value="Event & Sports" <?php echo ($filters['category'] === 'Event & Sports') ? 'selected' : ''; ?>>Events &amp; Sports</option>
                    <option value="Emergency / Urgent Alert" <?php echo ($filters['category'] === 'Emergency / Urgent Alert') ? 'selected' : ''; ?>>Emergency Alerts</option>
                    <option value="Faculty & Staff Meeting" <?php echo ($filters['category'] === 'Faculty & Staff Meeting') ? 'selected' : ''; ?>>Staff Meetings</option>
                    <option value="Library & Reading Notice" <?php echo ($filters['category'] === 'Library & Reading Notice') ? 'selected' : ''; ?>>📚 Library &amp; Books</option>
                </select>
            </div>

            <!-- Priority Filter -->
            <div class="col-6 col-md-2">
                <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" <?php echo ($filters['priority'] === 'all') ? 'selected' : ''; ?>>All Priorities</option>
                    <option value="Urgent" <?php echo ($filters['priority'] === 'Urgent') ? 'selected' : ''; ?>>🔴 Urgent</option>
                    <option value="Important" <?php echo ($filters['priority'] === 'Important') ? 'selected' : ''; ?>>🟡 Important</option>
                    <option value="Normal" <?php echo ($filters['priority'] === 'Normal') ? 'selected' : ''; ?>>🔵 Normal</option>
                </select>
            </div>

            <!-- Audience Filter -->
            <div class="col-6 col-md-2">
                <select name="audience" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" <?php echo ($filters['audience'] === 'all') ? 'selected' : ''; ?>>All Audiences</option>
                    <option value="student" <?php echo ($filters['audience'] === 'student') ? 'selected' : ''; ?>>🎓 Students</option>
                    <option value="staff" <?php echo ($filters['audience'] === 'staff') ? 'selected' : ''; ?>>💼 Staff / Faculty</option>
                    <option value="parent" <?php echo ($filters['audience'] === 'parent') ? 'selected' : ''; ?>>👨‍👩‍👦 Parents</option>
                </select>
            </div>

            <!-- Buttons: Submit & Reset -->
            <div class="col-6 col-md-2 d-flex gap-1 justify-content-end">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="fa fa-filter me-1"></i> Filter
                </button>
                <?php if(!empty($filters['search']) || $filters['category'] !== 'all' || $filters['priority'] !== 'all' || $filters['audience'] !== 'all'): ?>
                    <a href="<?php echo URLROOT; ?>/notice/index" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                        <i class="fa fa-undo"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Notices Stream -->
    <div id="noticesContainer">
        <?php if(empty($notices)): ?>
            <div class="card border-0 shadow-sm p-5 text-center my-4" style="border-radius: 16px;">
                <div class="py-4">
                    <div class="mb-3">
                        <i class="fa fa-clipboard-list text-muted fa-4x opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No Notices or Circulars Found</h5>
                    <p class="text-muted small mb-4">There are currently no circulars matching your active filter criteria.</p>
                    <?php if($isAdmin): ?>
                        <button type="button" class="btn btn-primary btn-sm px-4 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
                            <i class="fa fa-plus-circle me-1"></i> Post the First Circular
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($notices as $notice): 
                $priorityClass = ($notice->priority === 'Urgent') ? 'is-urgent' : (($notice->priority === 'Important') ? 'is-important' : 'is-normal');
                $catColor = $categoryColors[$notice->notice_type ?? 'General Notice'] ?? 'badge-primary-soft';
                $refNo = 'CIR-' . date('Y', strtotime($notice->publish_date)) . '/' . str_pad($notice->id, 4, '0', STR_PAD_LEFT);
                $initials = strtoupper(substr($notice->created_by_name ?? 'Admin', 0, 2));
            ?>
                <div class="notice-item-card <?php echo $priorityClass; ?> notice-card-item" 
                     data-title="<?php echo strtolower(htmlspecialchars($notice->title)); ?>" 
                     data-content="<?php echo strtolower(htmlspecialchars($notice->message)); ?>"
                     data-category="<?php echo htmlspecialchars($notice->notice_type ?? ''); ?>"
                     data-priority="<?php echo htmlspecialchars($notice->priority ?? 'Normal'); ?>">
                    
                    <!-- Header Meta Strip -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge-soft <?php echo $catColor; ?>">
                                <i class="fa fa-tag me-1"></i> <?php echo htmlspecialchars($notice->notice_type ?? 'General Notice'); ?>
                            </span>

                            <?php if(($notice->priority ?? '') === 'Urgent'): ?>
                                <span class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 11px;">
                                    <i class="fa fa-fire me-1"></i> URGENT
                                </span>
                            <?php elseif(($notice->priority ?? '') === 'Important'): ?>
                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1" style="font-size: 11px;">
                                    <i class="fa fa-star me-1"></i> IMPORTANT
                                </span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border rounded-pill px-2 py-1" style="font-size: 11px;">
                                    NORMAL
                                </span>
                            <?php endif; ?>

                            <span class="notice-ref-badge" title="Official Circular Reference Code">
                                <?php echo $refNo; ?>
                            </span>
                        </div>

                        <!-- Target Audience Badges -->
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted small me-1" style="font-size: 11px;">Audience:</span>
                            <?php if(($notice->is_visible_to_student ?? 'no') === 'yes'): ?>
                                <span class="audience-pill" title="Visible to Students"><i class="fa fa-user-graduate text-primary me-1"></i> Students</span>
                            <?php endif; ?>
                            <?php if(($notice->is_visible_to_staff ?? 'no') === 'yes'): ?>
                                <span class="audience-pill" title="Visible to Staff & Teachers"><i class="fa fa-chalkboard-teacher text-info me-1"></i> Staff</span>
                            <?php endif; ?>
                            <?php if(($notice->is_visible_to_parent ?? 'no') === 'yes'): ?>
                                <span class="audience-pill" title="Visible to Parents"><i class="fa fa-users text-success me-1"></i> Parents</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Notice Title -->
                    <h3 class="notice-title">
                        <?php echo htmlspecialchars($notice->title); ?>
                    </h3>

                    <!-- Excerpt Body -->
                    <div class="notice-body-excerpt">
                        <?php 
                            // Render rich HTML if contains tags, otherwise nl2br safe plain text
                            if (strip_tags($notice->message) !== $notice->message) {
                                echo strip_tags($notice->message, '<p><br><b><strong><i><em><u><ul><ol><li><a><span><h1><h2><h3><h4><h5><h6><blockquote><code><table><thead><tbody>tr><th><td><hr>');
                            } else {
                                echo nl2br(htmlspecialchars($notice->message));
                            }
                        ?>
                    </div>

                    <!-- Expiry Date Notice if set -->
                    <?php if(!empty($notice->expiry_date)): ?>
                        <div class="text-warning small mb-3 d-flex align-items-center gap-1 fw-bold" style="font-size: 12px;">
                            <i class="fa fa-clock"></i> Valid / Active Until: <?php echo date('d M Y', strtotime($notice->expiry_date)); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Footer Action Toolbar -->
                    <div class="notice-footer-bar">
                        <div class="author-pill">
                            <div class="author-avatar"><?php echo $initials; ?></div>
                            <span>
                                Posted by <strong><?php echo htmlspecialchars($notice->created_by_name ?? 'Principal Office'); ?></strong>
                                <span class="text-muted mx-1">&bull;</span>
                                <i class="fa fa-calendar-alt me-1"></i> <?php echo date('d M, Y', strtotime($notice->publish_date)); ?>
                                <span class="badge bg-light text-secondary border rounded-pill ms-1" style="font-size: 10.5px;">
                                    <?php echo getRelativeTime($notice->publish_date); ?>
                                </span>
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <!-- View Modal Trigger -->
                            <button type="button" class="btn btn-outline-primary btn-sm px-3 btn-view-notice" 
                                    data-id="<?php echo $notice->id; ?>"
                                    data-title="<?php echo htmlspecialchars($notice->title, ENT_QUOTES); ?>"
                                    data-ref="<?php echo $refNo; ?>"
                                    data-type="<?php echo htmlspecialchars($notice->notice_type ?? 'General Notice', ENT_QUOTES); ?>"
                                    data-priority="<?php echo htmlspecialchars($notice->priority ?? 'Normal', ENT_QUOTES); ?>"
                                    data-date="<?php echo date('d F, Y', strtotime($notice->publish_date)); ?>"
                                    data-author="<?php echo htmlspecialchars($notice->created_by_name ?? 'Principal Office', ENT_QUOTES); ?>"
                                    data-content="<?php echo htmlspecialchars($notice->message, ENT_QUOTES); ?>"
                                    data-student="<?php echo $notice->is_visible_to_student ?? 'no'; ?>"
                                    data-staff="<?php echo $notice->is_visible_to_staff ?? 'no'; ?>"
                                    data-parent="<?php echo $notice->is_visible_to_parent ?? 'no'; ?>"
                                    data-attachment="<?php echo !empty($notice->attachment) ? (URLROOT . '/' . ltrim($notice->attachment, '/')) : ''; ?>"
                                    data-attachmentname="<?php echo !empty($notice->attachment) ? htmlspecialchars(basename($notice->attachment), ENT_QUOTES) : ''; ?>"
                                    data-printurl="<?php echo URLROOT; ?>/notice/print/<?php echo $notice->id; ?>">
                                <i class="fa fa-eye me-1"></i> View Circular
                            </button>

                            <?php if(!empty($notice->attachment)): ?>
                                <a href="<?php echo URLROOT . '/' . ltrim($notice->attachment, '/'); ?>" target="_blank" class="btn btn-outline-danger btn-sm px-3" title="Download Attached PDF">
                                    <i class="fa fa-file-pdf me-1"></i> PDF
                                </a>
                            <?php endif; ?>

                            <!-- Print Circular -->
                            <a href="<?php echo URLROOT; ?>/notice/print/<?php echo $notice->id; ?>" target="_blank" class="btn btn-outline-secondary btn-sm px-3" title="Print Official Circular (Letterhead)">
                                <i class="fa fa-print me-1"></i> Print
                            </a>

                            <?php if($isAdmin): ?>
                                <!-- Send via Gmail Broadcast Button -->
                                <button type="button" class="btn btn-outline-success btn-sm px-3 btn-email-broadcast" 
                                        data-id="<?php echo $notice->id; ?>" 
                                        data-title="<?php echo htmlspecialchars($notice->title, ENT_QUOTES); ?>" 
                                        data-type="<?php echo htmlspecialchars($notice->notice_type ?? 'General Notice', ENT_QUOTES); ?>" 
                                        title="Broadcast Notice via Gmail / Outgoing Email">
                                    <i class="fa fa-paper-plane me-1"></i> Send to Gmail
                                </button>

                                <!-- Edit Notice Button -->
                                <button type="button" class="btn btn-outline-warning btn-sm px-3 btn-edit-notice" data-id="<?php echo $notice->id; ?>" title="Edit Circular Details">
                                    <i class="fa fa-edit me-1"></i> Edit
                                </button>

                                <!-- Delete Form -->
                                <form action="<?php echo URLROOT; ?>/notice/delete/<?php echo $notice->id; ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this circular? This action cannot be undone.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                    <input type="hidden" name="id" value="<?php echo $notice->id; ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm px-2" title="Delete Circular">
                                        <i class="fa fa-trash-alt"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<!-- =========================================================================
     MODAL 1: ADD NEW NOTICE / CIRCULAR
     ========================================================================= -->
<?php if($isAdmin): ?>
<div class="modal fade" id="addNoticeModal" tabindex="-1" aria-labelledby="addNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold" id="addNoticeModalLabel">
                    <i class="fa fa-bullhorn text-warning me-2"></i> Post New Official Circular / Notice
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/notice/index" method="post" enctype="multipart/form-data" id="addNoticeForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">Circular Subject / Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg fs-6" placeholder="e.g. Mid-Term Examination Schedule & Syllabus Notification" required>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Circular Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="General Notice">General Notice</option>
                                <option value="Academic / Exam Circular">Academic / Exam Circular</option>
                                <option value="Fee & Accounts Reminder">Fee &amp; Accounts Reminder</option>
                                <option value="Holiday Announcement">Holiday Announcement</option>
                                <option value="Event & Sports">Event &amp; Sports Announcement</option>
                                <option value="Emergency / Urgent Alert">Emergency / Urgent Alert</option>
                                <option value="Faculty & Staff Meeting">Faculty &amp; Staff Meeting</option>
                                <option value="Library & Reading Notice">📚 Library &amp; Reading Notice</option>
                            </select>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Priority Level <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="Normal">🔵 Normal Priority</option>
                                <option value="Important">🟡 Important (High Visibility)</option>
                                <option value="Urgent">🔴 Urgent (Emergency Attention)</option>
                            </select>
                        </div>

                        <!-- Publish Date & Expiry Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Date of Issue / Publish <span class="text-danger">*</span></label>
                            <input type="date" name="publish_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Valid Until / Expiry Date <span class="text-muted small fw-normal">(Optional)</span></label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>

                        <!-- Audience Target Checkboxes -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark d-block mb-2">Target Circulation Audience</label>
                            <div class="d-flex gap-4 p-3 bg-light rounded border">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_student" value="yes" id="addVisStudent" checked>
                                    <label class="form-check-label fw-semibold" for="addVisStudent">
                                        <i class="fa fa-user-graduate text-primary me-1"></i> Students Portal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_staff" value="yes" id="addVisStaff" checked>
                                    <label class="form-check-label fw-semibold" for="addVisStaff">
                                        <i class="fa fa-chalkboard-teacher text-info me-1"></i> Faculty &amp; Staff Portal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_parent" value="yes" id="addVisParent" checked>
                                    <label class="form-check-label fw-semibold" for="addVisParent">
                                        <i class="fa fa-users text-success me-1"></i> Parents Portal
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Attachment PDF / Document Field -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark"><i class="fa fa-file-pdf text-danger me-1"></i> Attach Official PDF Document <span class="text-muted small fw-normal">(Optional)</span></label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Attach official signed notification or circular document (PDF/Word, Max 15MB).</small>
                        </div>

                        <!-- Gmail SMTP Outgoing Email Dispatch -->
                        <div class="col-12">
                            <div class="p-3 bg-light rounded border border-success-subtle">
                                <div class="form-check form-switch mb-1">
                                    <input class="form-check-input" type="checkbox" name="dispatch_email" value="1" id="addDispatchEmail">
                                    <label class="form-check-label fw-bold text-dark" for="addDispatchEmail">
                                        <i class="fa fa-envelope text-success me-1"></i> Send this Circular via Gmail / Outgoing Email
                                    </label>
                                </div>
                                <div id="emailTargetOptions" class="ps-3 pt-2 d-none">
                                    <div class="small text-muted mb-2">Select who should receive this circular in their Gmail/inbox:</div>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="email_targets[]" value="all_users" id="emTargetAll" checked>
                                            <label class="form-check-label fw-semibold small text-dark" for="emTargetAll">
                                                <i class="fa fa-users text-primary me-1"></i> Send to All Users
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="email_targets[]" value="parents" id="emTargetParents">
                                            <label class="form-check-label fw-semibold small text-dark" for="emTargetParents">
                                                <i class="fa fa-user-friends text-success me-1"></i> Send to Parents
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="email_targets[]" value="staff" id="emTargetStaff">
                                            <label class="form-check-label fw-semibold small text-dark" for="emTargetStaff">
                                                <i class="fa fa-chalkboard-teacher text-info me-1"></i> Send to All Staff &amp; Faculty
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content Message Body (Rich Text WYSIWYG Editor) -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">Circular Memorandum Text / Body <span class="text-danger">*</span></label>
                            <textarea name="message" id="addNoticeMessage" class="form-control summernote-notice" rows="6" placeholder="Type the complete official notification text here..." required></textarea>
                            <small class="text-muted">You can write multi-line formatted instructions, bold text, lists, schedules, deadlines, or directives.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">
                        <i class="fa fa-paper-plane me-1"></i> Publish Circular
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL 2: EDIT EXISTING NOTICE / CIRCULAR
     ========================================================================= -->
<div class="modal fade" id="editNoticeModal" tabindex="-1" aria-labelledby="editNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-dark text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold" id="editNoticeModalLabel">
                    <i class="fa fa-edit text-warning me-2"></i> Edit Circular Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/notice/update" method="post" enctype="multipart/form-data" id="editNoticeForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <input type="hidden" name="id" id="editNoticeId">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">Circular Subject / Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="editNoticeTitle" class="form-control form-control-lg fs-6" required>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Circular Category <span class="text-danger">*</span></label>
                            <select name="category" id="editNoticeCategory" class="form-select" required>
                                <option value="General Notice">General Notice</option>
                                <option value="Academic / Exam Circular">Academic / Exam Circular</option>
                                <option value="Fee & Accounts Reminder">Fee &amp; Accounts Reminder</option>
                                <option value="Holiday Announcement">Holiday Announcement</option>
                                <option value="Event & Sports">Event &amp; Sports Announcement</option>
                                <option value="Emergency / Urgent Alert">Emergency / Urgent Alert</option>
                                <option value="Faculty & Staff Meeting">Faculty &amp; Staff Meeting</option>
                                <option value="Library & Reading Notice">📚 Library &amp; Reading Notice</option>
                            </select>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Priority Level <span class="text-danger">*</span></label>
                            <select name="priority" id="editNoticePriority" class="form-select" required>
                                <option value="Normal">🔵 Normal Priority</option>
                                <option value="Important">🟡 Important (High Visibility)</option>
                                <option value="Urgent">🔴 Urgent (Emergency Attention)</option>
                            </select>
                        </div>

                        <!-- Dates -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Date of Issue <span class="text-danger">*</span></label>
                            <input type="date" name="publish_date" id="editNoticePublishDate" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark">Valid Until Date</label>
                            <input type="date" name="expiry_date" id="editNoticeExpiryDate" class="form-control">
                        </div>

                        <!-- Audience Target Checkboxes -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark d-block mb-2">Target Circulation Audience</label>
                            <div class="d-flex gap-4 p-3 bg-light rounded border">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_student" value="yes" id="editVisStudent">
                                    <label class="form-check-label fw-semibold" for="editVisStudent">
                                        <i class="fa fa-user-graduate text-primary me-1"></i> Students
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_staff" value="yes" id="editVisStaff">
                                    <label class="form-check-label fw-semibold" for="editVisStaff">
                                        <i class="fa fa-chalkboard-teacher text-info me-1"></i> Staff / Faculty
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="visible_parent" value="yes" id="editVisParent">
                                    <label class="form-check-label fw-semibold" for="editVisParent">
                                        <i class="fa fa-users text-success me-1"></i> Parents
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Attachment PDF / Document Field -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark"><i class="fa fa-file-pdf text-danger me-1"></i> Replace / Attach PDF Document <span class="text-muted small fw-normal">(Optional)</span></label>
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small class="text-muted">Upload a new PDF to replace the existing document, or leave blank to keep current.</small>
                        </div>

                        <!-- Message Body (Rich Text WYSIWYG Editor) -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark">Circular Body Text <span class="text-danger">*</span></label>
                            <textarea name="message" id="editNoticeMessage" class="form-control summernote-notice" rows="6" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning px-4 fw-bold rounded-pill text-dark shadow-sm">
                        <i class="fa fa-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- =========================================================================
     MODAL 3: VIEW OFFICIAL CIRCULAR (FORMAL MEMORANDUM FORMAT)
     ========================================================================= -->
<div class="modal fade" id="viewNoticeModal" tabindex="-1" aria-labelledby="viewNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold" id="viewNoticeModalLabel">
                    <i class="fa fa-file-lines me-2"></i> Official Circular Memorandum
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 bg-light rounded border mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="text-muted small">Reference:</span> <strong id="modalViewRef" class="text-dark"></strong>
                            <span class="mx-2 text-muted">|</span>
                            <span class="text-muted small">Category:</span> <span id="modalViewCategory" class="badge bg-secondary"></span>
                            <span class="mx-2 text-muted">|</span>
                            <span id="modalViewPriority"></span>
                        </div>
                        <div>
                            <span class="text-muted small"><i class="fa fa-calendar-alt me-1"></i> Issue Date:</span> <strong id="modalViewDate" class="text-dark"></strong>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="text-uppercase text-muted small fw-bold" style="letter-spacing: 1px;">Subject:</div>
                    <h4 id="modalViewTitle" class="fw-bold text-dark mt-1"></h4>
                </div>

                <div class="mb-3 p-2 bg-white rounded border">
                    <span class="text-muted small fw-bold">Circulation Distribution:</span>
                    <span id="modalViewAudience" class="small ms-1"></span>
                </div>

                <div class="mb-4">
                    <div class="text-uppercase text-muted small fw-bold mb-2" style="letter-spacing: 1px;">Circular Directives:</div>
                    <div id="modalViewContent" class="p-3 bg-light rounded border text-dark" style="line-height: 1.7; font-size: 14px; min-height: 120px; overflow-wrap: break-word;"></div>
                </div>

                <div id="modalViewAttachmentBox" class="mb-3 d-none">
                    <div class="p-3 bg-white rounded border d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-file-pdf fa-2x text-danger"></i>
                            <div>
                                <strong id="modalViewAttachmentName" class="text-dark d-block text-break"></strong>
                                <small class="text-muted">Official Document Attachment</small>
                            </div>
                        </div>
                        <a href="#" id="modalViewAttachmentLink" target="_blank" class="btn btn-sm btn-danger text-white fw-bold px-3">
                            <i class="fa fa-download me-1"></i> Download PDF
                        </a>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center text-muted small border-top pt-3">
                    <div>
                        <i class="fa fa-user-tie me-1"></i> Issued by: <strong id="modalViewAuthor" class="text-dark"></strong>
                    </div>
                    <div>
                        Status: <span class="badge bg-success">Published &amp; Active</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
                <a href="#" id="modalPrintLink" target="_blank" class="btn btn-primary px-4 fw-bold rounded-pill">
                    <i class="fa fa-print me-1"></i> Print Official Letterhead
                </a>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL 4: GMAIL / EMAIL BROADCAST MODAL
     ========================================================================= -->
<?php if($isAdmin): ?>
<div class="modal fade" id="emailBroadcastModal" tabindex="-1" aria-labelledby="emailBroadcastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-success text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold" id="emailBroadcastModalLabel">
                    <i class="fa fa-paper-plane me-2"></i> Send Circular via Gmail / Outgoing Email
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/notice/broadcastEmail" method="post" id="broadcastEmailForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                <input type="hidden" name="id" id="broadcastNoticeId" value="">

                <div class="modal-body p-4">
                    <!-- Notice summary banner -->
                    <div class="alert alert-light border p-3 rounded mb-3 bg-light">
                        <div class="small text-muted text-uppercase fw-bold mb-1" id="broadcastNoticeCategory">General Notice</div>
                        <h6 class="fw-bold text-dark mb-0" id="broadcastNoticeTitle">Notice Title</h6>
                    </div>

                    <label class="form-label fw-bold text-dark mb-2">
                        <i class="fa fa-users text-primary me-1"></i> Select Recipient Email Audience:
                    </label>

                    <div class="list-group mb-3">
                        <label class="list-group-item d-flex gap-3 align-items-center py-3 border rounded mb-2">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="target_audience[]" value="all_users" checked>
                            <span>
                                <strong class="d-block text-dark"><i class="fa fa-globe text-primary me-1"></i> Send to All Users</strong>
                                <small class="text-muted">Broadcast to all registered portal accounts (Students, Parents, Teachers &amp; Staff).</small>
                            </span>
                        </label>
                        <label class="list-group-item d-flex gap-3 align-items-center py-3 border rounded mb-2">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="target_audience[]" value="parents">
                            <span>
                                <strong class="d-block text-dark"><i class="fa fa-user-friends text-success me-1"></i> Send to Parents</strong>
                                <small class="text-muted">Deliver directly to email inboxes of all enrolled students' parents &amp; guardians.</small>
                            </span>
                        </label>
                        <label class="list-group-item d-flex gap-3 align-items-center py-3 border rounded">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="target_audience[]" value="staff">
                            <span>
                                <strong class="d-block text-dark"><i class="fa fa-chalkboard-teacher text-info me-1"></i> Send to All Staff &amp; Faculty</strong>
                                <small class="text-muted">Deliver to teachers, subject specialists, coordinators, and administrative officers.</small>
                            </span>
                        </label>
                    </div>

                    <div class="small text-muted bg-white p-2 rounded border border-warning-subtle">
                        <i class="fa fa-info-circle text-primary me-1"></i> Emails are dispatched via your configured <strong>Gmail SMTP server</strong> with official school branding and responsive letterhead styling.
                    </div>
                </div>

                <div class="modal-footer bg-light p-3" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-secondary px-3 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold rounded-pill shadow-sm" id="broadcastSubmitBtn">
                        <i class="fa fa-paper-plane me-1"></i> Dispatch Gmail Broadcast
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
(function() {
    function initNoticeFilter() {
        const liveSearchInput = document.getElementById('liveSearchInput');
        if (liveSearchInput && !liveSearchInput._hasBound) {
            liveSearchInput._hasBound = true;
            liveSearchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const noticeCards = document.querySelectorAll('.notice-card-item');
                noticeCards.forEach(card => {
                    const title = card.getAttribute('data-title') || '';
                    const content = card.getAttribute('data-content') || '';
                    if (title.includes(query) || content.includes(query)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    }

    // Event Delegation: View Notice Modal Click Handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-view-notice');
        if (!btn) return;

        const refEl = document.getElementById('modalViewRef');
        if (refEl) refEl.textContent = btn.dataset.ref || '';
        const catEl = document.getElementById('modalViewCategory');
        if (catEl) catEl.textContent = btn.dataset.type || '';
        const dateEl = document.getElementById('modalViewDate');
        if (dateEl) dateEl.textContent = btn.dataset.date || '';
        const titleEl = document.getElementById('modalViewTitle');
        if (titleEl) titleEl.textContent = btn.dataset.title || '';
        const contentEl = document.getElementById('modalViewContent');
        if (contentEl) {
            const rawContent = btn.dataset.content || '';
            // If content has HTML tags, render as HTML; otherwise format line breaks
            if (/<[a-z][\s\S]*>/i.test(rawContent)) {
                contentEl.innerHTML = rawContent;
            } else {
                contentEl.innerHTML = rawContent.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
            }
        }
        const authorEl = document.getElementById('modalViewAuthor');
        if (authorEl) authorEl.textContent = btn.dataset.author || '';
        const printLinkEl = document.getElementById('modalPrintLink');
        if (printLinkEl) printLinkEl.href = btn.dataset.printurl || '#';

        // Priority badge
        const priorityBox = document.getElementById('modalViewPriority');
        if (priorityBox) {
            if (btn.dataset.priority === 'Urgent') {
                priorityBox.innerHTML = '<span class="badge bg-danger">Urgent Priority</span>';
            } else if (btn.dataset.priority === 'Important') {
                priorityBox.innerHTML = '<span class="badge bg-warning text-dark">Important</span>';
            } else {
                priorityBox.innerHTML = '<span class="badge bg-info text-dark">Normal Priority</span>';
            }
        }

        // Audience text
        const audiences = [];
        if (btn.dataset.student === 'yes') audiences.push('Students');
        if (btn.dataset.staff === 'yes') audiences.push('Staff / Faculty');
        if (btn.dataset.parent === 'yes') audiences.push('Parents');
        const audEl = document.getElementById('modalViewAudience');
        if (audEl) audEl.textContent = audiences.length ? audiences.join(' | ') : 'General Campus';

        // Attachment Box
        const attBox = document.getElementById('modalViewAttachmentBox');
        const attLink = document.getElementById('modalViewAttachmentLink');
        const attName = document.getElementById('modalViewAttachmentName');
        if (btn.dataset.attachment) {
            if (attBox) attBox.classList.remove('d-none');
            if (attLink) attLink.href = btn.dataset.attachment;
            if (attName) attName.textContent = btn.dataset.attachmentname || 'Official Circular.pdf';
        } else {
            if (attBox) attBox.classList.add('d-none');
        }

        const modalEl = document.getElementById('viewNoticeModal');
        if (modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    });

    // Edit Notice Pre-fill Handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-notice');
        if (!btn) return;

        const id = btn.dataset.id;
        if (!id) return;

        fetch('<?php echo URLROOT; ?>/notice/getNoticeJson/' + id)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.notice) {
                    const n = data.notice;
                    const idEl = document.getElementById('editNoticeId');
                    if (idEl) idEl.value = n.id;
                    const titleEl = document.getElementById('editNoticeTitle');
                    if (titleEl) titleEl.value = n.title;
                    const catEl = document.getElementById('editNoticeCategory');
                    if (catEl) catEl.value = n.notice_type || 'General Notice';
                    const priorityEl = document.getElementById('editNoticePriority');
                    if (priorityEl) priorityEl.value = n.priority || 'Normal';
                    const pubDateEl = document.getElementById('editNoticePublishDate');
                    if (pubDateEl) pubDateEl.value = n.publish_date;
                    const expDateEl = document.getElementById('editNoticeExpiryDate');
                    if (expDateEl) expDateEl.value = n.expiry_date || '';
                    
                    // Set textarea & Summernote code
                    const msgEl = document.getElementById('editNoticeMessage');
                    if (msgEl) {
                        msgEl.value = n.message || '';
                        if (window.jQuery && typeof jQuery.fn.summernote === 'function') {
                            jQuery('#editNoticeMessage').summernote('code', n.message || '');
                        }
                    }

                    const visStudent = document.getElementById('editVisStudent');
                    if (visStudent) visStudent.checked = (n.is_visible_to_student === 'yes');
                    const visStaff = document.getElementById('editVisStaff');
                    if (visStaff) visStaff.checked = (n.is_visible_to_staff === 'yes');
                    const visParent = document.getElementById('editVisParent');
                    if (visParent) visParent.checked = (n.is_visible_to_parent === 'yes');

                    const modalEl = document.getElementById('editNoticeModal');
                    if (modalEl) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    }
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast('Could not fetch circular details.', 'danger');
                    } else {
                        alert('Could not fetch circular details.');
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if (typeof window.showToast === 'function') {
                    window.showToast('Network error while retrieving circular data.', 'danger');
                } else {
                    alert('Network error while retrieving circular data.');
                }
            });
    });

    // Toggle Email Target Options in Add Notice Modal
    const addDispatchEmail = document.getElementById('addDispatchEmail');
    const emailTargetOptions = document.getElementById('emailTargetOptions');
    if (addDispatchEmail && emailTargetOptions) {
        addDispatchEmail.addEventListener('change', function() {
            if (this.checked) {
                emailTargetOptions.classList.remove('d-none');
            } else {
                emailTargetOptions.classList.add('d-none');
            }
        });
    }

    // Event Delegation: Open Gmail Broadcast Modal
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-email-broadcast');
        if (!btn) return;

        const id = btn.dataset.id;
        const title = btn.dataset.title || '';
        const type = btn.dataset.type || 'General Notice';

        const idInput = document.getElementById('broadcastNoticeId');
        if (idInput) idInput.value = id;

        const titleEl = document.getElementById('broadcastNoticeTitle');
        if (titleEl) titleEl.textContent = title;

        const catEl = document.getElementById('broadcastNoticeCategory');
        if (catEl) catEl.textContent = type;

        const modalEl = document.getElementById('emailBroadcastModal');
        if (modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    });

    // Form Submit: Gmail Broadcast Form Loading State
    const broadcastForm = document.getElementById('broadcastEmailForm');
    if (broadcastForm) {
        broadcastForm.addEventListener('submit', function() {
            const submitBtn = document.getElementById('broadcastSubmitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Dispatching via Gmail SMTP...';
            }
        });
    }

    initNoticeFilter();
    document.addEventListener('page:loaded', initNoticeFilter);
})();
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>

<!-- jQuery & Summernote Lite (WYSIWYG Rich Text Editor) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
(function() {
    function initNoticeSummernote() {
        if (!window.jQuery || typeof jQuery.fn.summernote !== 'function') return;

        var summernoteConfig = {
            placeholder: 'Compose official circular content here with formatting, lists, tables, links...',
            tabsize: 2,
            height: 200,
            dialogsInBody: true,
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'hr']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        };

        // Initialize Add Notice Editor
        var $addMsg = jQuery('#addNoticeMessage');
        if ($addMsg.length && !$addMsg.data('summernote')) {
            $addMsg.summernote(summernoteConfig);
        }

        // Initialize Edit Notice Editor
        var $editMsg = jQuery('#editNoticeMessage');
        if ($editMsg.length && !$editMsg.data('summernote')) {
            $editMsg.summernote(summernoteConfig);
        }

        // Reset Add Modal Summernote on Open
        var addModalEl = document.getElementById('addNoticeModal');
        if (addModalEl && !addModalEl._snBound) {
            addModalEl._snBound = true;
            addModalEl.addEventListener('show.bs.modal', function() {
                if (window.jQuery && typeof jQuery.fn.summernote === 'function') {
                    jQuery('#addNoticeMessage').summernote('code', '');
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNoticeSummernote);
    } else {
        initNoticeSummernote();
    }
    document.addEventListener('page:loaded', initNoticeSummernote);
})();
</script>
