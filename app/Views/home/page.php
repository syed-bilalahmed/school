<?php
$schoolName = $data['school']->school_name ?? $data['settings']->school_name ?? SITENAME ?? 'Imperial Academy';
$pageTitle = $data['page']->title ?? 'Academy Overview';
$pageSlug = $data['page']->slug ?? '';
$activePage = $pageSlug;
$theme = $data['settings']->theme_color ?? 'default';

// Color adaptation
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
    
    <!-- Dynamic Favicon -->
    <?php 
    $pageLogo = !empty($siteSettings['logo']) ? $siteSettings['logo'] : (!empty($data['settings']->logo) ? $data['settings']->logo : '');
    if(!empty($pageLogo)): ?>
        <link rel="icon" type="image/png" href="<?php echo URLROOT . '/' . htmlspecialchars($pageLogo); ?>">
        <link rel="shortcut icon" href="<?php echo URLROOT . '/' . htmlspecialchars($pageLogo); ?>">
        <link rel="apple-touch-icon" href="<?php echo URLROOT . '/' . htmlspecialchars($pageLogo); ?>">
    <?php else: ?>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
    <?php endif; ?>

    <?php if(!empty($data['page']->meta_description)): ?>
        <meta name="description" content="<?php echo htmlspecialchars($data['page']->meta_description, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    <?php if(!empty($data['page']->meta_keywords)): ?>
        <meta name="keywords" content="<?php echo htmlspecialchars($data['page']->meta_keywords, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Luxury Frontend Design System -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/frontend.css?v=3.3">
    
    <?php if ($theme !== 'default'): ?>
    <style>
        :root {
            --f-primary: <?php echo $themeColor; ?>;
            --f-primary-gradient: linear-gradient(135deg, <?php echo $themeColor; ?> 0%, #1e1b4b 100%);
        }
    </style>
    <?php endif; ?>
</head>
<body>

    <!-- TOP NAVIGATION BAR -->
    <?php require_once APPROOT . '/Views/home/partials/navbar.php'; ?>

    <!-- INNER PAGE HERO -->
    <header class="inner-hero">
        <div class="container">
            <div class="inner-hero-content">
                <div class="inner-breadcrumbs">
                    <a href="<?php echo URLROOT; ?>"><i class="fa fa-home"></i> Home</a>
                    <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    <span>Pages</span>
                    <i class="fa fa-chevron-right fa-xs text-muted"></i>
                    <span class="text-white"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <h1 class="inner-page-title"><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="inner-page-subtitle">
                    <?php echo !empty($data['page']->meta_description) ? htmlspecialchars($data['page']->meta_description, ENT_QUOTES, 'UTF-8') : 'Fostering academic distinction, ethical integrity, and visionary leadership.'; ?>
                </p>
            </div>
        </div>
    </header>

    <!-- EDITORIAL CONTENT SECTION -->
    <main class="py-5" style="background: var(--f-bg);">
        <div class="container">
            <div class="row g-5">
                <!-- Main Content Column -->
                <div class="col-lg-8">
                    <div class="admission-card-luxury">
                        <article class="editorial-content">
                            <?php if(!empty($data['page']->file_path)): 
                                $filePath = $data['page']->file_path;
                                $fileUrl = (strpos($filePath, 'http') === 0) ? $filePath : URLROOT . '/' . ltrim($filePath, '/');
                                $fileName = !empty($data['page']->file_name) ? $data['page']->file_name : basename($filePath);
                                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                $isPdf = ($ext === 'pdf');
                            ?>
                                <?php if($isImg): ?>
                                    <div class="mb-4 rounded-3 overflow-hidden shadow-sm">
                                        <img src="<?php echo $fileUrl; ?>" alt="<?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?>" class="w-100" style="max-height: 480px; object-fit: cover; border-radius: 12px;">
                                    </div>
                                <?php endif; ?>

                                <?php if($isPdf): ?>
                                    <!-- INTERACTIVE PDF VIEWER EMBED -->
                                    <div class="pdf-viewer-container my-4 rounded-3 overflow-hidden shadow-sm border">
                                        <div class="bg-dark text-white p-3 d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom border-secondary border-opacity-50">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 bg-danger text-white rounded-3 fs-5 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fa fa-file-pdf"></i>
                                                </div>
                                                <div>
                                                    <strong class="d-block text-white fs-6"><?php echo htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'); ?></strong>
                                                    <span class="badge bg-danger text-uppercase font-monospace" style="font-size: 0.65rem;">Official PDF &bull; Interactive Document Reader</span>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a href="<?php echo $fileUrl; ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold" title="Open PDF in Fullscreen Window">
                                                    <i class="fa fa-expand me-1"></i> Fullscreen
                                                </a>
                                                <a href="<?php echo $fileUrl; ?>" download class="btn btn-warning text-dark btn-sm rounded-pill px-3 fw-bold" title="Save PDF to device">
                                                    <i class="fa fa-download me-1"></i> Download PDF
                                                </a>
                                            </div>
                                        </div>
                                        <div class="pdf-frame-wrapper position-relative" style="height: 700px; background: #525659;">
                                            <object data="<?php echo $fileUrl; ?>#toolbar=1&navpanes=0&scrollbar=1" type="application/pdf" width="100%" height="100%">
                                                <iframe src="<?php echo $fileUrl; ?>" width="100%" height="100%" style="border: none;">
                                                    <div class="p-5 text-center text-white">
                                                        <i class="fa fa-file-pdf fa-3x text-danger mb-3"></i>
                                                        <p class="fs-5 fw-bold mb-2">PDF Document Ready for Reading</p>
                                                        <p class="text-white-50 mb-3 small">If your browser does not display the inline preview, click below to open or download.</p>
                                                        <a href="<?php echo $fileUrl; ?>" target="_blank" class="btn btn-primary-lux rounded-pill px-4">
                                                            <i class="fa fa-external-link-alt me-2"></i> Open PDF in Reader
                                                        </a>
                                                    </div>
                                                </iframe>
                                            </object>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- STANDARD DOCUMENT DOWNLOAD CARD -->
                                    <div class="card border-0 shadow-sm p-4 mb-4 rounded-3 bg-light d-flex flex-sm-row align-items-sm-center justify-content-between gap-3 border-start border-4 border-primary">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="p-3 bg-primary text-white rounded-circle fs-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                                <i class="fa <?php echo ($isImg ? 'fa-file-image' : 'fa-file-lines'); ?>"></i>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'); ?></h5>
                                                <span class="badge bg-secondary text-uppercase"><?php echo htmlspecialchars($ext); ?> Document Resource</span>
                                            </div>
                                        </div>
                                        <a href="<?php echo $fileUrl; ?>" class="btn btn-primary-lux btn-sm px-3 py-2 fw-bold text-nowrap" target="_blank" download>
                                            <i class="fa fa-download me-1"></i> Download File
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if(!empty($data['page']->content)): ?>
                                <?php echo $data['page']->content; ?>
                            <?php else: ?>
                                <p class="lead text-muted">
                                    Welcome to this page of <strong><?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></strong>. Our mission is to prepare scholars for lifelong intellectual engagement, moral courage, and global responsibility.
                                </p>
                                
                                <h2>Our Academic Commitment</h2>
                                <p>
                                    At <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?>, we believe education is not merely the transmission of factual knowledge, but the cultivation of an inquiring mind and an ethical heart. Every student is guided by exceptional faculty through rigorous, inquiry-based curricula that cultivate critical thought, creativity, and interdisciplinary problem-solving.
                                </p>

                                <blockquote>
                                    &ldquo;Education is the most powerful weapon which you can use to change the world. We empower scholars to question, create, and lead with empathy.&rdquo;
                                </blockquote>

                                <h3>Core Educational Standards</h3>
                                <ul>
                                    <li><strong>Individualized Academic Mentorship:</strong> Maintaining a 14:1 student-to-faculty ratio to foster personalized intellectual growth.</li>
                                    <li><strong>Interdisciplinary STEAM Labs:</strong> Blending scientific rigor with cutting-edge mechatronics, digital design, and artistic expression.</li>
                                    <li><strong>Global Stewardship:</strong> Engaging in local community partnerships and international exchange symposiums.</li>
                                </ul>

                                <h2>Campus Life &amp; Student Well-being</h2>
                                <p>
                                    Our campus offers an environment where intellectual vigor is complemented by athletics, visual arts, and holistic health initiatives. We encourage students to discover new passions, challenge their limits, and build lifelong friendships.
                                </p>
                            <?php endif; ?>
                        </article>
                    </div>
                </div>

                <!-- Sidebar Column -->
                <div class="col-lg-4">
                    <!-- Quick Navigation Card -->
                    <?php if(!empty($data['menus'])): ?>
                        <div class="admission-sidebar-card">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="brand-icon-emblem" style="width: 34px; height: 34px; font-size: 0.9rem;">
                                    <i class="fa fa-compass"></i>
                                </div>
                                <h3 class="h5 fw-bold mb-0">Explore Further</h3>
                            </div>
                            <ul class="footer-links-list">
                                <?php foreach($data['menus'] as $menu): ?>
                                    <li class="footer-link-item">
                                        <a href="<?php echo ($menu->page_id > 0) ? URLROOT . '/home/page/' . $menu->page_slug : $menu->link; ?>" class="<?php echo ($menu->page_slug == $pageSlug) ? 'text-primary fw-bold' : ''; ?>">
                                            <i class="fa fa-chevron-right fa-xs"></i> <?php echo htmlspecialchars($menu->title, ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Document Quick Actions Card (if document attached) -->
                    <?php if(!empty($data['page']->file_path)): ?>
                        <div class="admission-sidebar-card border-start border-4 border-primary">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa <?php echo ($isPdf ? 'fa-file-pdf text-danger' : 'fa-file-lines text-primary'); ?> fs-4"></i>
                                <h3 class="h6 fw-bold mb-0 text-dark">Document Resource</h3>
                            </div>
                            <p class="small text-muted mb-3 text-truncate" title="<?php echo htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'); ?>">
                                <strong class="text-dark"><?php echo htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8'); ?></strong>
                            </p>
                            <div class="d-grid gap-2">
                                <a href="<?php echo $fileUrl; ?>" download class="btn btn-sm btn-primary-lux">
                                    <i class="fa fa-download me-1"></i> Download File (<?php echo strtoupper($ext); ?>)
                                </a>
                                <a href="<?php echo $fileUrl; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-expand me-1"></i> Open Full Window
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>



                    <!-- Campus Secretariat Card -->
                    <div class="admission-sidebar-card">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="brand-icon-emblem" style="width: 34px; height: 34px; font-size: 0.9rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <i class="fa fa-envelope-open-text"></i>
                            </div>
                            <h3 class="h5 fw-bold mb-0">Campus Secretariat</h3>
                        </div>
                        <div class="d-flex flex-column gap-3 text-muted small">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fa fa-map-marker-alt text-primary mt-1"></i>
                                <span><?php echo htmlspecialchars($data['school']->address ?? '450 Heritage Blvd, Academic District', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-phone text-primary"></i>
                                <span><?php echo htmlspecialchars($data['school']->phone ?? '+1 (800) 456-7890', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-envelope text-primary"></i>
                                <span><?php echo htmlspecialchars($data['school']->email ?? 'info@academy.edu', ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER PARTIAL -->
    <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>

</body>
</html>
