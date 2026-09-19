<?php
$siteSettings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];
$schoolName = !empty($siteSettings['school_name']) ? $siteSettings['school_name'] : ($data['school']->school_name ?? $data['settings']->school_name ?? SITENAME);
$activePage = 'home';
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
    <title><?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?> &mdash; Excellence in Education</title>
    
    <!-- Dynamic Favicon -->
    <?php if(!empty($siteSettings['logo'])): ?>
        <link rel="icon" type="image/png" href="<?php echo URLROOT . '/' . htmlspecialchars($siteSettings['logo']); ?>">
        <link rel="shortcut icon" href="<?php echo URLROOT . '/' . htmlspecialchars($siteSettings['logo']); ?>">
        <link rel="apple-touch-icon" href="<?php echo URLROOT . '/' . htmlspecialchars($siteSettings['logo']); ?>">
    <?php else: ?>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
    <?php endif; ?>

    <!-- High-Speed Resource Hints & DNS Prefetching -->
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//images.unsplash.com">
    <link rel="preconnect" href="https://images.unsplash.com" crossorigin>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Animate.css v4 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- AOS (Animate On Scroll) -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    
    <!-- Clean Frontend Design System -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/frontend.css?v=3.3">
    
    <?php if ($theme !== 'default'): ?>
    <style>
        :root {
            --f-primary: <?php echo $themeColor; ?>;
            --f-primary-gradient: linear-gradient(135deg, <?php echo $themeColor; ?> 0%, #1e1b4b 100%);
        }
    </style>
    <?php endif; ?>

    <style>
        /* Simple, Decent Styling Overrides */
        .hero-clean-wrapper {
            background: linear-gradient(145deg, #0b1d40 0%, #17366e 60%, #1e4b8f 100%);
            color: #ffffff;
            padding: 130px 0 80px;
            position: relative;
            overflow: hidden;
        }
        .hero-clean-wrapper::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-badge-clean {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 50px;
            margin-bottom: 20px;
        }
        .hero-title-clean {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.2;
            color: #ffffff;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
        }
        .hero-desc-clean {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.7;
            max-width: 580px;
            margin-bottom: 30px;
        }
        .hero-img-box {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            border: 4px solid rgba(255, 255, 255, 0.15);
        }
        .hero-img-box img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
        }
        .features-strip {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 24px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }
        .strip-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px;
        }
        .strip-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .strip-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .strip-desc {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }
        .pillar-card-decent {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 30px 26px;
            transition: all 0.3s ease;
            height: 100%;
        }
        .pillar-card-decent:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }
        .program-card-clean {
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .program-card-clean:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.09);
        }
        .program-card-clean img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .program-card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .facility-card-clean {
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            height: 250px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }
        .facility-card-clean img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .facility-card-clean:hover img {
            transform: scale(1.04);
        }
        .facility-card-clean .facility-overlay-clean {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(180deg, transparent 0%, rgba(15, 23, 42, 0.9) 100%);
            color: #ffffff;
        }
        .notice-card-clean {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            transition: all 0.2s ease;
        }
        .notice-card-clean:hover {
            border-color: #93c5fd;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.06);
        }
        .event-chip-date {
            width: 58px;
            height: 58px;
            border-radius: 12px;
            background: #eff6ff;
            color: #1e40af;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            flex-shrink: 0;
            border: 1px solid #dbeafe;
        }
        .event-chip-date .day {
            font-size: 1.25rem;
            line-height: 1;
        }
        .event-chip-date .month {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-top: 2px;
        }
        .cta-box-decent {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border-radius: 24px;
            padding: 48px;
            color: #ffffff;
            box-shadow: 0 20px 50px rgba(30, 58, 138, 0.2);
        }
        @media (max-width: 767.98px) {
            .hero-title-clean { font-size: 2rem; }
            .cta-box-decent { padding: 30px 20px; }
            .hero-img-box img { height: 260px; }
        }
    </style>
</head>
<body>

    <!-- TOP NAVIGATION BAR -->
    <?php require_once APPROOT . '/Views/home/partials/navbar.php'; ?>

    <!-- 1. CLEAN & DECENT HERO SECTION -->
    <?php
    $heroBadge = !empty($siteSettings['hero_badge']) ? $siteSettings['hero_badge'] : 'Accredited Premier Educational Institution';
    $heroTitle = !empty($siteSettings['hero_title']) ? $siteSettings['hero_title'] : 'Empowering Minds, Shaping Tomorrow\'s Leaders';
    $heroDesc = !empty($siteSettings['hero_subtitle']) ? $siteSettings['hero_subtitle'] : ('Welcome to ' . htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8') . '. We provide an inspiring, disciplined environment dedicated to scholastic excellence, character development, and holistic growth.');
    
    $defaultHeroPath = dirname(APPROOT) . '/public/uploads/front/school_hero_campus.jpg';
    $heroImg = file_exists($defaultHeroPath) 
        ? URLROOT . '/uploads/front/school_hero_campus.jpg' 
        : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1000&auto=format&fit=crop';

    if (!empty($siteSettings['hero_image'])) {
        $heroImg = (strpos($siteSettings['hero_image'], 'http') === 0) ? $siteSettings['hero_image'] : URLROOT . '/' . ltrim($siteSettings['hero_image'], '/');
    }
    ?>

    <header class="hero-clean-wrapper">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7" data-aos="fade-right">
                    <div class="hero-badge-clean">
                        <i class="fa fa-award text-warning"></i>
                        <span><?php echo htmlspecialchars($heroBadge, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>

                    <h1 class="hero-title-clean">
                        <?php echo nl2br(htmlspecialchars(strip_tags($heroTitle), ENT_QUOTES, 'UTF-8')); ?>
                    </h1>

                    <p class="hero-desc-clean">
                        <?php echo nl2br(htmlspecialchars(strip_tags($heroDesc), ENT_QUOTES, 'UTF-8')); ?>
                    </p>

                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <?php if(($data['cms']->enable_online_admission ?? 'yes') === 'yes'): ?>
                            <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-light px-4 py-2 fw-bold shadow-sm" style="border-radius: 50px; font-size: 0.95rem; color: #1e3a8a;">
                                <i class="fa fa-file-signature me-2 text-primary"></i> Apply for Admission
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo URLROOT; ?>/home/academics" class="btn btn-outline-light px-4 py-2 fw-semibold" style="border-radius: 50px; font-size: 0.95rem;">
                            <i class="fa fa-graduation-cap me-2"></i> Academic Programs
                        </a>

                        <a href="<?php echo URLROOT; ?>/auth/login" class="btn btn-outline-light px-3 py-2 fw-semibold" style="border-radius: 50px; font-size: 0.92rem; border-color: rgba(255,255,255,0.4);">
                            <i class="fa fa-lock me-1 text-warning"></i> Portal Login
                        </a>
                    </div>
                </div>

                <div class="col-lg-5" data-aos="fade-left">
                    <div class="hero-img-box">
                        <img src="<?php echo $heroImg; ?>" alt="<?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?> Campus" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. KEY HIGHLIGHTS STRIP -->
    <div class="features-strip">
        <div class="container">
            <div class="row g-3">
                <div class="col-lg-3 col-sm-6">
                    <div class="strip-item">
                        <div class="strip-icon">
                            <i class="fa fa-book-open"></i>
                        </div>
                        <div>
                            <div class="strip-title">Proven Curriculum</div>
                            <p class="strip-desc">Comprehensive &amp; structured syllabus</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="strip-item">
                        <div class="strip-icon" style="background: #ecfdf5; color: #059669;">
                            <i class="fa fa-chalkboard-user"></i>
                        </div>
                        <div>
                            <div class="strip-title">Qualified Faculty</div>
                            <p class="strip-desc">Experienced &amp; dedicated educators</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="strip-item">
                        <div class="strip-icon" style="background: #fef3c7; color: #d97706;">
                            <i class="fa fa-microchip"></i>
                        </div>
                        <div>
                            <div class="strip-title">Modern Science &amp; IT</div>
                            <p class="strip-desc">Equipped labs &amp; digital learning</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="strip-item">
                        <div class="strip-icon" style="background: #fdf2f8; color: #db2777;">
                            <i class="fa fa-shield-heart"></i>
                        </div>
                        <div>
                            <div class="strip-title">Safe Environment</div>
                            <p class="strip-desc">Secure, caring &amp; disciplined campus</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. ABOUT OUR INSTITUTION & MISSION -->
    <section class="section-py bg-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="section-tag mb-2">
                        <i class="fa fa-school"></i> About Our Institution
                    </span>
                    <h2 class="section-title text-start mb-3">
                        Building Strong Foundations for Lifelong Success
                    </h2>
                    <p class="text-secondary lh-lg mb-4" style="font-size: 1.02rem;">
                        At <strong><?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></strong>, we are committed to nurturing intellectual curiosity, ethical values, and self-confidence. Our teaching approach combines foundational knowledge with interactive learning, preparing every scholar to thrive in a rapidly advancing world.
                    </p>

                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold small">
                                <i class="fa fa-circle-check text-success"></i> Individual student mentorship
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold small">
                                <i class="fa fa-circle-check text-success"></i> Continuous academic assessment
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold small">
                                <i class="fa fa-circle-check text-success"></i> Character &amp; leadership education
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold small">
                                <i class="fa fa-circle-check text-success"></i> Active parent-school communication
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="<?php echo URLROOT; ?>/home/academics" class="btn btn-primary px-4 py-2 fw-semibold" style="border-radius: 8px;">
                            <i class="fa fa-compass me-2"></i> Explore Academics
                        </a>
                        <a href="<?php echo URLROOT; ?>/home/facilities" class="btn btn-outline-secondary px-4 py-2 fw-semibold" style="border-radius: 8px;">
                            <i class="fa fa-building me-2"></i> Campus Facilities
                        </a>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="pillar-card-decent">
                                <div class="text-primary fs-3 mb-3">
                                    <i class="fa fa-brain"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Academic Distinction</h5>
                                <p class="text-muted small mb-0">Rigorous subject mastery with continuous diagnostic feedback and personal guidance.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="pillar-card-decent">
                                <div class="text-success fs-3 mb-3">
                                    <i class="fa fa-hand-holding-heart"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Moral Integrity</h5>
                                <p class="text-muted small mb-0">Cultivating respect, social empathy, honesty, and strong civic responsibility.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="pillar-card-decent">
                                <div class="text-warning fs-3 mb-3">
                                    <i class="fa fa-laptop-code"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Modern Technology</h5>
                                <p class="text-muted small mb-0">Interactive computing, science exploration, and digital literacy tools.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="pillar-card-decent">
                                <div class="text-danger fs-3 mb-3">
                                    <i class="fa fa-trophy"></i>
                                </div>
                                <h5 class="fw-bold mb-2">Sports &amp; Arts</h5>
                                <p class="text-muted small mb-0">Co-curricular activities, physical fitness, creative arts, and teamwork.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. ACADEMIC WINGS -->
    <section class="section-py" id="academics" style="background: #f8fafc;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-tag">
                    <i class="fa fa-graduation-cap"></i> Academic Pathways
                </span>
                <h2 class="section-title">Educational Programs Designed for Growth</h2>
                <p class="section-subtitle">
                    Structured stages carefully designed to guide students from early discovery through advanced secondary achievement.
                </p>
            </div>

            <div class="row g-4">
                <!-- Primary Wing -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="program-card-clean">
                        <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?q=80&w=600&auto=format&fit=crop" alt="Primary Wing" loading="lazy">
                        <div class="program-card-body">
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-2 py-1 mb-2 align-self-start" style="font-size: 0.72rem;">Pre-School &ndash; Grade 5</span>
                            <h4 class="fw-bold text-dark mb-2">Primary Wing</h4>
                            <p class="text-muted small mb-4">Focuses on foundational literacy, mathematical reasoning, sensory engagement, and cooperative social learning.</p>
                            <a href="<?php echo URLROOT; ?>/home/academics" class="text-primary fw-bold small text-decoration-none mt-auto">
                                View Curriculum <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Middle Wing -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="program-card-clean">
                        <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=600&auto=format&fit=crop" alt="Middle Wing" loading="lazy">
                        <div class="program-card-body">
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase px-2 py-1 mb-2 align-self-start" style="font-size: 0.72rem;">Grades 6 &ndash; 8</span>
                            <h4 class="fw-bold text-dark mb-2">Middle Wing</h4>
                            <p class="text-muted small mb-4">Encourages analytical inquiry, introductory laboratory sciences, language mastery, and independent project work.</p>
                            <a href="<?php echo URLROOT; ?>/home/academics" class="text-success fw-bold small text-decoration-none mt-auto">
                                View Curriculum <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Senior Wing -->
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="program-card-clean">
                        <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=600&auto=format&fit=crop" alt="Senior Wing" loading="lazy">
                        <div class="program-card-body">
                            <span class="badge bg-indigo bg-opacity-10 text-indigo fw-bold text-uppercase px-2 py-1 mb-2 align-self-start" style="font-size: 0.72rem; color: #4f46e5; background: #eef2ff;">Grades 9 &ndash; 12</span>
                            <h4 class="fw-bold text-dark mb-2">Senior Secondary Wing</h4>
                            <p class="text-muted small mb-4">Preparation for board examinations, specialized science &amp; arts streams, career counseling, and collegiate readiness.</p>
                            <a href="<?php echo URLROOT; ?>/home/academics" class="text-primary fw-bold small text-decoration-none mt-auto">
                                View Curriculum <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. CAMPUS FACILITIES -->
    <section class="section-py bg-white" id="facilities">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-tag">
                    <i class="fa fa-building"></i> Campus Environment
                </span>
                <h2 class="section-title">Well-Equipped Facilities</h2>
                <p class="section-subtitle">
                    Clean, safe, and modern infrastructure designed to support academics, research, and healthy physical activities.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-6 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                    <div class="facility-card-clean">
                        <img src="https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=800&auto=format&fit=crop" alt="Library" loading="lazy">
                        <div class="facility-overlay-clean">
                            <h5 class="fw-bold mb-1">Library &amp; Reading Room</h5>
                            <p class="small text-white-50 mb-0">Rich collection of reference books, textbooks, educational journals, and quiet reading areas.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                    <div class="facility-card-clean">
                        <img src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?q=80&w=800&auto=format&fit=crop" alt="Science Lab" loading="lazy">
                        <div class="facility-overlay-clean">
                            <h5 class="fw-bold mb-1">Science &amp; Computer Laboratories</h5>
                            <p class="small text-white-50 mb-0">Hands-on apparatus for Physics, Chemistry, Biology, and modern computing workstations.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6" data-aos="zoom-in" data-aos-delay="300">
                    <div class="facility-card-clean">
                        <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?q=80&w=800&auto=format&fit=crop" alt="Classrooms" loading="lazy">
                        <div class="facility-overlay-clean">
                            <h5 class="fw-bold mb-1">Bright &amp; Ventilated Classrooms</h5>
                            <p class="small text-white-50 mb-0">Ergonomic seating, smart presentation boards, and spacious natural lighting.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6" data-aos="zoom-in" data-aos-delay="400">
                    <div class="facility-card-clean">
                        <img src="https://images.unsplash.com/photo-1461896836934-ffe607ba8211?q=80&w=800&auto=format&fit=crop" alt="Sports Ground" loading="lazy">
                        <div class="facility-overlay-clean">
                            <h5 class="fw-bold mb-1">Sports &amp; Physical Fitness Ground</h5>
                            <p class="small text-white-50 mb-0">Open play area and courts for athletics, football, cricket, and physical training.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="<?php echo URLROOT; ?>/home/facilities" class="btn btn-outline-primary px-4 py-2 fw-semibold" style="border-radius: 8px;">
                    <i class="fa fa-arrow-right me-1"></i> View All Campus Facilities
                </a>
            </div>
        </div>
    </section>

    <!-- 6. OFFICIAL NOTICES & CIRCULARS -->
    <?php if(!empty($data['notices'])): ?>
    <section class="section-py" id="notices" style="background: #f8fafc;">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4" data-aos="fade-up">
                <div>
                    <span class="section-tag mb-1"><i class="fa fa-bullhorn"></i> Circulars</span>
                    <h2 class="section-title text-start mb-0">Official Notice Board</h2>
                </div>
            </div>

            <div class="row g-3">
                <?php foreach(array_slice($data['notices'], 0, 4) as $idx => $notice): 
                    $nDate = strtotime($notice->publish_date ?? 'now');
                ?>
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="<?php echo ($idx + 1) * 100; ?>">
                        <div class="notice-card-clean">
                            <div class="d-flex align-items-center gap-3">
                                <div class="event-chip-date">
                                    <span class="day"><?php echo date('d', $nDate); ?></span>
                                    <span class="month"><?php echo date('M', $nDate); ?></span>
                                </div>
                                <div>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary small fw-bold mb-1" style="font-size: 0.68rem;">
                                        <?php echo htmlspecialchars($notice->notice_type ?? 'Notice'); ?>
                                    </span>
                                    <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($notice->title); ?></h6>
                                    <p class="text-muted small mb-0 text-truncate" style="max-width: 320px;">
                                        <?php echo htmlspecialchars(substr(strip_tags($notice->message), 0, 90)); ?>...
                                    </p>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm px-3 py-1 text-nowrap" data-bs-toggle="modal" data-bs-target="#noticeModal<?php echo $notice->id; ?>" style="border-radius: 6px; font-size: 0.8rem;">
                                View Notice
                            </button>
                        </div>
                    </div>

                    <!-- Individual Notice Modal -->
                    <div class="modal fade" id="noticeModal<?php echo $notice->id; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-light">
                                    <div>
                                        <h5 class="modal-title fw-bold text-dark mb-0"><?php echo htmlspecialchars($notice->title); ?></h5>
                                        <small class="text-muted"><i class="fa fa-calendar-alt me-1"></i> Published: <?php echo date('F d, Y', $nDate); ?></small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4 text-secondary lh-lg">
                                    <?php echo nl2br(htmlspecialchars($notice->message)); ?>
                                </div>
                                <div class="modal-footer bg-light border-0">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 7. UPCOMING EVENTS & CAMPUS HIGHLIGHTS -->
    <section class="section-py bg-white" id="events-news">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-5" data-aos="fade-up">
                <div>
                    <span class="section-tag mb-1"><i class="fa fa-calendar-alt"></i> School Calendar</span>
                    <h2 class="section-title text-start mb-0">Events &amp; Campus Life</h2>
                </div>
                <a href="<?php echo URLROOT; ?>/home/events" class="btn btn-outline-primary btn-sm px-3 py-2 fw-semibold" style="border-radius: 6px;">
                    View All Events <i class="fa fa-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-4">
                <?php if(!empty($data['events'])): ?>
                    <?php foreach(array_slice($data['events'], 0, 3) as $event): 
                        $evDate = strtotime($event->start_date ?? 'now');
                    ?>
                        <div class="col-lg-4 col-md-6" data-aos="fade-up">
                            <div class="p-4 rounded-3 border bg-white shadow-sm h-100 d-flex flex-column">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="event-chip-date">
                                        <span class="day"><?php echo date('d', $evDate); ?></span>
                                        <span class="month"><?php echo date('M', $evDate); ?></span>
                                    </div>
                                    <div>
                                        <div class="text-primary small fw-semibold"><i class="fa fa-clock me-1"></i> <?php echo date('h:i A', $evDate); ?></div>
                                        <small class="text-muted"><i class="fa fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($event->venue ?? 'School Campus'); ?></small>
                                    </div>
                                </div>
                                <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($event->title); ?></h5>
                                <p class="text-muted small mb-0 mt-auto"><?php echo htmlspecialchars(substr($event->description ?? '', 0, 100)); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="p-4 rounded-3 border bg-white shadow-sm h-100">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="event-chip-date">
                                    <span class="day">15</span>
                                    <span class="month">OCT</span>
                                </div>
                                <div>
                                    <div class="text-primary small fw-semibold">09:00 AM</div>
                                    <small class="text-muted">School Auditorium</small>
                                </div>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Annual Science &amp; Art Exhibition</h5>
                            <p class="text-muted small mb-0">Showcasing student scientific models, robotics experiments, and creative art compositions.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="p-4 rounded-3 border bg-white shadow-sm h-100">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="event-chip-date">
                                    <span class="day">02</span>
                                    <span class="month">NOV</span>
                                </div>
                                <div>
                                    <div class="text-primary small fw-semibold">10:00 AM</div>
                                    <small class="text-muted">Sports Grounds</small>
                                </div>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Inter-House Sports Gala</h5>
                            <p class="text-muted small mb-0">Track and field athletics, cricket matches, football tournaments, and prize distribution.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="p-4 rounded-3 border bg-white shadow-sm h-100">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="event-chip-date">
                                    <span class="day">20</span>
                                    <span class="month">NOV</span>
                                </div>
                                <div>
                                    <div class="text-primary small fw-semibold">11:00 AM</div>
                                    <small class="text-muted">Main Campus Hall</small>
                                </div>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Parent-Teacher Conference</h5>
                            <p class="text-muted small mb-0">One-on-one progress review between parents and class educators regarding term academic evaluations.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- 8. CAMPUS PHOTO GALLERY PREVIEW -->
    <section class="section-py" id="gallery" style="background: #f8fafc;">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4" data-aos="fade-up">
                <div>
                    <span class="section-tag mb-1"><i class="fa fa-camera"></i> Media</span>
                    <h2 class="section-title text-start mb-0">Life at Our Campus</h2>
                </div>
                <a href="<?php echo URLROOT; ?>/home/gallery" class="btn btn-outline-primary btn-sm px-3 py-2 fw-semibold" style="border-radius: 6px;">
                    View Photo Gallery <i class="fa fa-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="row g-3">
                <?php if(!empty($data['gallery'])): ?>
                    <?php foreach(array_slice($data['gallery'], 0, 4) as $idx => $img): 
                        $gSrc = !empty($img->featured_image) ? URLROOT . '/' . $img->featured_image : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=600';
                    ?>
                        <div class="col-lg-3 col-sm-6" data-aos="zoom-in" data-aos-delay="<?php echo ($idx + 1) * 100; ?>">
                            <div class="rounded-3 overflow-hidden shadow-sm border" style="height: 190px;">
                                <img src="<?php echo $gSrc; ?>" alt="Campus Life" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-lg-3 col-sm-6">
                        <div class="rounded-3 overflow-hidden shadow-sm border" style="height: 190px;">
                            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=600&auto=format&fit=crop" alt="Classroom" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="rounded-3 overflow-hidden shadow-sm border" style="height: 190px;">
                            <img src="https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=600&auto=format&fit=crop" alt="Students" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="rounded-3 overflow-hidden shadow-sm border" style="height: 190px;">
                            <img src="https://images.unsplash.com/photo-1588072432836-e10032774350?q=80&w=600&auto=format&fit=crop" alt="Laboratory" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="rounded-3 overflow-hidden shadow-sm border" style="height: 190px;">
                            <img src="https://images.unsplash.com/photo-1461896836934-ffe607ba8211?q=80&w=600&auto=format&fit=crop" alt="Sports" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- 9. ADMISSIONS CALLOUT -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="cta-box-decent" data-aos="fade-up">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="fw-bold text-white mb-2">Begin Your Child's Journey of Excellence</h2>
                        <p class="text-white-50 mb-0" style="font-size: 1.05rem;">
                            Admissions for the upcoming academic session are open. Contact our front office or submit an online application today.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                            <?php if(($data['cms']->enable_online_admission ?? 'yes') === 'yes'): ?>
                                <a href="<?php echo URLROOT; ?>/home/admission" class="btn btn-light px-4 py-2 fw-bold" style="border-radius: 8px; color: #1e3a8a;">
                                    <i class="fa fa-file-signature me-1"></i> Apply Online
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo URLROOT; ?>/auth/login" class="btn btn-outline-light px-4 py-2 fw-semibold" style="border-radius: 8px;">
                                <i class="fa fa-lock me-1"></i> Portal Access
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER PARTIAL -->
    <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>
</body>
</html>
