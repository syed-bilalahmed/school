<?php
$schoolName = $data['school']->school_name ?? $data['settings']->school_name ?? SITENAME ?? 'Our School';
$activePage = 'alumni';
$pageTitle = 'Distinguished Alumni Network & Hall of Fame';
$pageDesc = 'Celebrating the achievements, career journeys, and global impact of graduates from ' . $schoolName . '.';
$alumniList = $data['alumni'] ?? [];
$featuredList = $data['featured'] ?? [];
$batches = $data['batches'] ?? [];
$selectedBatch = $data['selectedBatch'] ?? null;
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
    <style>
        .alumni-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .alumni-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.12);
            border-color: rgba(99, 102, 241, 0.35);
        }
        .alumni-avatar-wrap {
            position: relative;
            height: 190px;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .alumni-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .alumni-card:hover .alumni-avatar-img {
            transform: scale(1.05);
        }
        .alumni-quote-box {
            background: #f8fafc;
            border-left: 3px solid #6366f1;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 0.88rem;
            color: #475569;
            font-style: italic;
        }
        .batch-pill-btn {
            border-radius: 50px;
            padding: 6px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid #cbd5e1;
            color: #334155;
            background: #ffffff;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .batch-pill-btn:hover,
        .batch-pill-btn.active {
            background: #4f46e5;
            color: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
    </style>
</head>
<body class="bg-light">

    <?php require_once APPROOT . '/Views/home/partials/navbar.php'; ?>

    <!-- HERO BANNER -->
    <header class="inner-hero py-5" style="background: linear-gradient(135deg, #090e1a 0%, #1e1b4b 50%, #312e81 100%); margin-top: 100px; color: #ffffff;">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-primary px-3 py-2 rounded-pill text-uppercase fw-bold mb-3" style="letter-spacing: 0.08em; font-size: 0.75rem;">
                        <i class="fa fa-award me-1"></i> Hall of Fame &bull; Alumni Directory
                    </span>
                    <h1 class="display-5 fw-bold text-white mb-2">Our Distinguished Alumni Network</h1>
                    <p class="lead text-white-50 mb-0" style="max-width: 700px;">
                        Honoring the legacy, career accomplishments, and worldwide leadership of scholars who graduated from <?php echo htmlspecialchars($schoolName); ?>.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#joinAlumniModal">
                        <i class="fa fa-user-plus me-1 text-primary"></i> Register as Alumni
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- STATS COUNTER BAR -->
    <section class="py-4 bg-white border-bottom shadow-xs">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3 col-6 border-end">
                    <div class="h3 fw-bold text-primary mb-0">2,500+</div>
                    <div class="small text-muted text-uppercase fw-semibold">Alumni Worldwide</div>
                </div>
                <div class="col-md-3 col-6 border-end">
                    <div class="h3 fw-bold text-success mb-0">150+</div>
                    <div class="small text-muted text-uppercase fw-semibold">Global Universities</div>
                </div>
                <div class="col-md-3 col-6 border-end">
                    <div class="h3 fw-bold text-warning mb-0">98%</div>
                    <div class="small text-muted text-uppercase fw-semibold">Career Success</div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="h3 fw-bold text-dark mb-0">25+</div>
                    <div class="small text-muted text-uppercase fw-semibold">Graduation Batches</div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN DIRECTORY SECTION -->
    <section class="py-5">
        <div class="container">
            <!-- Flash Messages -->
            <?php if(!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa fa-check-circle me-2 fs-5"></i><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa fa-exclamation-circle me-2 fs-5"></i><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Batch Filter Bar -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="fw-bold text-dark small me-1"><i class="fa fa-filter text-primary me-1"></i> Filter by Batch:</span>
                    <a href="<?php echo URLROOT; ?>/home/alumni" class="batch-pill-btn <?php echo empty($selectedBatch) ? 'active' : ''; ?>">
                        All Batches
                    </a>
                    <?php foreach($batches as $batchYear): ?>
                        <a href="<?php echo URLROOT; ?>/home/alumni?batch=<?php echo urlencode($batchYear); ?>" class="batch-pill-btn <?php echo ($selectedBatch === $batchYear) ? 'active' : ''; ?>">
                            Class of <?php echo htmlspecialchars($batchYear); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="text-muted small">
                    Showing <strong><?php echo count($alumniList); ?></strong> alumni profiles
                </div>
            </div>

            <!-- Alumni Directory Grid -->
            <?php if(empty($alumniList)): ?>
                <div class="text-center py-5 bg-white rounded-3 border">
                    <i class="fa fa-user-graduate fa-3x text-muted opacity-50 mb-3 d-block"></i>
                    <h5 class="fw-bold text-dark">No Alumni Listed in this Batch</h5>
                    <p class="text-muted small mb-4">Are you a graduate of <?php echo htmlspecialchars($schoolName); ?>? Join our official directory today.</p>
                    <button type="button" class="btn btn-primary btn-sm px-4 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#joinAlumniModal">
                        <i class="fa fa-user-plus me-1"></i> Register Your Alumni Profile
                    </button>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach($alumniList as $alumnus): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="alumni-card">
                                <!-- Photo or Initials -->
                                <div class="alumni-avatar-wrap">
                                    <?php if(!empty($alumnus->image)): ?>
                                        <img src="<?php echo URLROOT . '/' . htmlspecialchars($alumnus->image); ?>" alt="<?php echo htmlspecialchars($alumnus->name); ?>" class="alumni-avatar-img">
                                    <?php else: ?>
                                        <div class="text-white text-center">
                                            <div class="display-4 fw-bold opacity-75"><?php echo strtoupper(substr($alumnus->name, 0, 1)); ?></div>
                                            <span class="badge bg-white bg-opacity-25 mt-1 small">Official Graduate</span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Batch Floating Pill -->
                                    <span class="position-absolute top-0 start-0 m-3 badge bg-dark bg-opacity-75 text-white px-3 py-2 rounded-pill font-monospace" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                        Class of <?php echo htmlspecialchars($alumnus->batch_year); ?>
                                    </span>

                                    <?php if($alumnus->is_featured == 1): ?>
                                        <span class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark px-2 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                            <i class="fa fa-star me-1"></i>Spotlight
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Body -->
                                <div class="p-4 d-flex flex-column justify-content-between flex-grow-1">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($alumnus->name); ?></h5>
                                        <div class="text-primary fw-semibold small mb-2">
                                            <?php echo htmlspecialchars($alumnus->current_position ?: 'Professional Graduate'); ?>
                                            <?php if(!empty($alumnus->company_organization)): ?>
                                                &bull; <span class="text-secondary"><?php echo htmlspecialchars($alumnus->company_organization); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <?php if(!empty($alumnus->graduation_class) || !empty($alumnus->location)): ?>
                                            <div class="text-muted small mb-3">
                                                <?php if(!empty($alumnus->graduation_class)): ?>
                                                    <span><i class="fa fa-graduation-cap me-1"></i><?php echo htmlspecialchars($alumnus->graduation_class); ?></span>
                                                <?php endif; ?>
                                                <?php if(!empty($alumnus->location)): ?>
                                                    <span class="ms-2"><i class="fa fa-map-marker-alt text-danger me-1"></i><?php echo htmlspecialchars($alumnus->location); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if(!empty($alumnus->testimonial)): ?>
                                            <div class="alumni-quote-box mb-3">
                                                &ldquo;<?php echo strip_tags($alumnus->testimonial); ?>&rdquo;
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Footer Links -->
                                    <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-2">
                                        <span class="text-muted small">
                                            <i class="fa fa-shield-check text-success me-1"></i> Verified Alumnus
                                        </span>
                                        <?php if(!empty($alumnus->linkedin_url)): ?>
                                            <a href="<?php echo htmlspecialchars($alumnus->linkedin_url); ?>" target="_blank" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-semibold">
                                                <i class="fab fa-linkedin me-1"></i> LinkedIn
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- JOIN ALUMNI NETWORK BANNER -->
    <section class="py-5 bg-white border-top">
        <div class="container text-center" style="max-width: 760px;">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                <i class="fa fa-users-viewfinder"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">Are You an Alumni of <?php echo htmlspecialchars($schoolName); ?>?</h3>
            <p class="text-muted mb-4">
                Stay connected with former classmates, mentor younger scholars, attend annual reunions, and expand your professional network. Register your profile in under two minutes.
            </p>
            <button type="button" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#joinAlumniModal">
                <i class="fa fa-user-plus me-2"></i>Join Official Alumni Directory
            </button>
        </div>
    </section>

    <!-- MODAL: REGISTER AS ALUMNI -->
    <div class="modal fade" id="joinAlumniModal" tabindex="-1" aria-labelledby="joinAlumniModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                    <h5 class="modal-title fw-bold" id="joinAlumniModalLabel">
                        <i class="fa fa-user-graduate me-2"></i>Register for the Alumni Directory
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo URLROOT; ?>/home/alumni" method="post" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-1">Your Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Engr. Hamza Farooq" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark small mb-1">Graduation Batch <span class="text-danger">*</span></label>
                                <input type="text" name="batch_year" class="form-control" placeholder="e.g. 2022" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold text-dark small mb-1">Program / Degree</label>
                                <input type="text" name="graduation_class" class="form-control" placeholder="e.g. Matric / FSc">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-1">Current Job / Role</label>
                                <input type="text" name="current_position" class="form-control" placeholder="e.g. Data Scientist / Lawyer / Founder">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-1">Current Company / Organization</label>
                                <input type="text" name="company_organization" class="form-control" placeholder="e.g. Tech Global Inc.">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small mb-1">City / Country</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Lahore, Pakistan">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small mb-1">LinkedIn URL</label>
                                <input type="url" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/username">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark small mb-1">Email Contact</label>
                                <input type="email" name="email" class="form-control" placeholder="you@domain.com">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark small mb-1">
                                    <i class="fa fa-quote-left text-warning me-1"></i>School Memory, Message or Advice for Current Students
                                </label>
                                <textarea name="testimonial" class="form-control" rows="3" placeholder="Share a favorite memory from your school years, guidance for junior students, or how your school helped shape your career..."></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-1">Profile Photo (Optional)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small mb-1">Phone Number (Confidential)</label>
                                <input type="tel" name="phone" class="form-control" placeholder="0300-1234567">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                            <i class="fa fa-paper-plane me-1"></i> Submit Alumni Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>

</body>
</html>
