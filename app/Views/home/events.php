<?php
$schoolName = $data['school']->school_name ?? $data['settings']->school_name ?? SITENAME ?? 'Imperial Academy';
$activePage = 'events';
$pageTitle = !empty($data['page']->title) ? $data['page']->title : 'Upcoming Events & Calendar';
$pageDesc = !empty($data['page']->meta_description) ? $data['page']->meta_description : ('Stay informed about academic schedules, sports competitions, symposia, and cultural galas at ' . $schoolName . '.');
$theme = $data['settings']->theme_color ?? 'default';

$themeColor = '#4f46e5';
if ($theme === 'red') $themeColor = '#e11d48';
elseif ($theme === 'green') $themeColor = '#059669';
elseif ($theme === 'dark') $themeColor = '#0f172a';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> &mdash; <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/frontend.css?v=3.3">
</head>
<body>

    <?php require_once APPROOT . '/Views/home/partials/navbar.php'; ?>

    <!-- HERO HEADER -->
    <header class="inner-hero">
        <div class="container">
            <div class="inner-hero-content">
                <div class="inner-breadcrumbs">
                    <a href="<?php echo URLROOT; ?>"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    <span>Events &amp; News</span>
                    <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    <span class="text-white"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <h1 class="inner-page-title mb-0"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <?php if(!empty($data['page']->id) && isset($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin'): ?>
                        <a href="<?php echo URLROOT; ?>/frontcms/pages/edit/<?php echo $data['page']->id; ?>" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold shadow-sm">
                            <i class="fa fa-pencil-alt me-1"></i> Edit This Page in CMS
                        </a>
                    <?php endif; ?>
                </div>
                <p class="inner-page-subtitle">
                    <?php echo htmlspecialchars($pageDesc, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT SECTION -->
    <main class="py-5" style="background: var(--f-bg, #f8fafc);">
        <div class="container">

            <!-- ATTACHED DOCUMENT / CALENDAR PDF VIEWER -->
            <?php if(!empty($data['page']->file_path)): 
                $filePath = $data['page']->file_path;
                $fileUrl = (strpos($filePath, 'http') === 0) ? $filePath : URLROOT . '/' . ltrim($filePath, '/');
                $fileName = !empty($data['page']->file_name) ? $data['page']->file_name : basename($filePath);
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $isPdf = ($ext === 'pdf');
            ?>
                <?php if($isPdf): ?>
                    <div class="pdf-viewer-container mb-5 rounded-3 overflow-hidden shadow-sm border">
                        <div class="bg-dark text-white p-3 d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom border-secondary border-opacity-50">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2 bg-danger text-white rounded-3 fs-5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fa fa-file-pdf"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-white fs-6"><?php echo htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <span class="badge bg-danger text-uppercase font-monospace" style="font-size: 0.65rem;">Official Calendar &bull; Interactive PDF Reader</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?php echo $fileUrl; ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold">
                                    <i class="fa fa-expand me-1"></i> Fullscreen
                                </a>
                                <a href="<?php echo $fileUrl; ?>" download class="btn btn-warning text-dark btn-sm rounded-pill px-3 fw-bold">
                                    <i class="fa fa-download me-1"></i> Download PDF
                                </a>
                            </div>
                        </div>
                        <div class="pdf-frame-wrapper position-relative" style="height: 650px; background: #525659;">
                            <object data="<?php echo $fileUrl; ?>#toolbar=1&navpanes=0&scrollbar=1" type="application/pdf" width="100%" height="100%">
                                <iframe src="<?php echo $fileUrl; ?>" width="100%" height="100%" style="border: none;">
                                    <div class="p-5 text-center text-white">
                                        <i class="fa fa-file-pdf fa-3x text-danger mb-3"></i>
                                        <p class="fs-5 fw-bold mb-2">Calendar Document Ready for Reading</p>
                                        <a href="<?php echo $fileUrl; ?>" target="_blank" class="btn btn-primary-lux rounded-pill px-4">
                                            <i class="fa fa-external-link-alt me-2"></i> Open PDF Reader
                                        </a>
                                    </div>
                                </iframe>
                            </object>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card border-0 shadow-sm p-4 mb-4 rounded-3 bg-white border-start border-4 border-primary d-flex flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-primary text-white rounded-circle fs-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                <i class="fa fa-file-lines"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'); ?></h5>
                                <span class="badge bg-secondary text-uppercase"><?php echo htmlspecialchars($ext); ?> Document Resource</span>
                            </div>
                        </div>
                        <a href="<?php echo $fileUrl; ?>" class="btn btn-primary-lux btn-sm px-3 py-2 fw-bold" target="_blank" download>
                            <i class="fa fa-download me-1"></i> Download File
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- DYNAMIC EDITORIAL ANNOUNCEMENT / CONTENT -->
            <?php if(!empty($data['page']->content)): ?>
                <div class="admission-card-luxury mb-4">
                    <article class="editorial-content">
                        <?php echo $data['page']->content; ?>
                    </article>
                </div>
            <?php endif; ?>

            <!-- DYNAMIC EVENTS GRID -->
            <div class="d-flex align-items-center justify-content-between mb-3 pt-2">
                <h3 class="h4 fw-bold text-dark mb-0">
                    <i class="fa fa-calendar-alt text-primary me-2"></i> Scheduled Assemblies &amp; Activities
                </h3>
                <span class="badge bg-light text-dark border px-3 py-2">
                    <?php echo count($data['events'] ?? []); ?> Active Events
                </span>
            </div>

            <div class="row g-4">
                <?php if(!empty($data['events'])): ?>
                    <?php foreach($data['events'] as $event): 
                        $evDate = strtotime($event->start_date ?? 'now');
                    ?>
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm h-100 p-3 bg-white" style="border-radius: 14px; border: 1px solid #e2e8f0 !important;">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="event-date-box text-center p-3 rounded-3" style="background: #1e3a8a; color: #fff; min-width: 75px;">
                                        <span class="d-block fw-extrabold h3 mb-0" style="line-height: 1;"><?php echo date('d', $evDate); ?></span>
                                        <span class="d-block text-uppercase small fw-bold text-warning"><?php echo date('M', $evDate); ?></span>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold text-dark mb-2 h5"><?php echo htmlspecialchars($event->title); ?></h4>
                                        <p class="text-muted small mb-2"><i class="fa fa-map-marker-alt text-primary me-1"></i> <?php echo htmlspecialchars($event->venue ?? 'Main Campus'); ?> &bull; <i class="fa fa-clock text-primary me-1"></i> <?php echo date('h:i A', $evDate); ?></p>
                                        <p class="text-dark small mb-0"><?php echo nl2br(htmlspecialchars($event->description ?? '')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5 bg-white rounded-3 border">
                        <div class="mb-3 text-muted"><i class="fa fa-calendar-times fa-4x opacity-50"></i></div>
                        <h4 class="fw-bold text-dark">No upcoming events scheduled right now.</h4>
                        <p class="text-muted">Check back soon for upcoming institutional dates.</p>
                        <a href="<?php echo URLROOT; ?>" class="btn btn-primary btn-sm px-4">Back to Home</a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>

</body>
</html>
