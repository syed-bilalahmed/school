<?php
$schoolName = $data['school']->school_name ?? $data['settings']->school_name ?? SITENAME ?? 'Our School';
$activePage = 'requirements';
$pageTitle = 'School Requirements, Careers & Tenders';
$pageDesc = 'Explore official job openings, institutional tenders, procurement requirements, and emergency school notices at ' . $schoolName . '.';
$requirements = $data['requirements'] ?? [];
$categories = $data['categories'] ?? [];
$selectedCat = $data['selectedCat'] ?? null;
$schoolPhone = !empty($data['school']->school_phone) ? $data['school']->school_phone : '+92-51-111-222-333';
$schoolEmail = !empty($data['school']->school_email) ? $data['school']->school_email : 'careers@school.edu.pk';
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
        .req-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .req-card:hover {
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.1);
            border-color: rgba(99, 102, 241, 0.3);
        }
        .req-cat-badge-emergency {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            font-weight: 700;
            letter-spacing: 0.04em;
        }
        .req-cat-badge-career {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
            font-weight: 700;
            border: 1px solid rgba(79, 70, 229, 0.2);
        }
        .req-cat-badge-tender {
            background: rgba(245, 158, 11, 0.12);
            color: #b45309;
            font-weight: 700;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }
        .cat-pill-btn {
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
        .cat-pill-btn:hover,
        .cat-pill-btn.active {
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
                        <i class="fa fa-briefcase me-1"></i> Opportunities &bull; Tenders &bull; Notices
                    </span>
                    <h1 class="display-5 fw-bold text-white mb-2">School Requirements &amp; Careers</h1>
                    <p class="lead text-white-50 mb-0" style="max-width: 720px;">
                        Explore current faculty recruitment openings, administrative vacancies, institutional tenders, and official campus procurement requirements at <?php echo htmlspecialchars($schoolName); ?>.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <a href="mailto:<?php echo htmlspecialchars($schoolEmail); ?>" class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm">
                        <i class="fa fa-envelope me-1 text-primary"></i> Contact HR / Procurement
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN SECTION -->
    <section class="py-5">
        <div class="container">
            <!-- Category Filter Bar -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="fw-bold text-dark small me-1"><i class="fa fa-layer-group text-primary me-1"></i> Categories:</span>
                    <a href="<?php echo URLROOT; ?>/home/requirements" class="cat-pill-btn <?php echo empty($selectedCat) ? 'active' : ''; ?>">
                        All Requirements
                    </a>
                    <?php foreach($categories as $cat): ?>
                        <a href="<?php echo URLROOT; ?>/home/requirements?category=<?php echo urlencode($cat); ?>" class="cat-pill-btn <?php echo ($selectedCat === $cat) ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="text-muted small">
                    Showing <strong><?php echo count($requirements); ?></strong> published openings &amp; requirements
                </div>
            </div>

            <div class="row g-4">
                <!-- Requirements List (Left 8 cols) -->
                <div class="col-lg-8">
                    <?php if(empty($requirements)): ?>
                        <div class="text-center py-5 bg-white rounded-3 border">
                            <i class="fa fa-briefcase fa-3x text-muted opacity-50 mb-3 d-block"></i>
                            <h5 class="fw-bold text-dark">No Current Requirements in this Category</h5>
                            <p class="text-muted small mb-0">Please check back periodically or send your resume to our HR office.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($requirements as $item): 
                            $isEmergency = (stripos($item->category, 'emergency') !== false || stripos($item->category, 'urgent') !== false);
                            $isTender = (stripos($item->category, 'tender') !== false || stripos($item->category, 'procurement') !== false);
                            $badgeClass = $isEmergency ? 'req-cat-badge-emergency' : ($isTender ? 'req-cat-badge-tender' : 'req-cat-badge-career');
                            $hasPdf = !empty($item->attachment);
                            $pdfUrl = $hasPdf ? (URLROOT . '/' . ltrim($item->attachment, '/')) : '';
                        ?>
                            <div class="req-card p-4" id="req-<?php echo $item->id; ?>">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-1 rounded-pill" style="font-size: 0.76rem;">
                                            <?php if($isEmergency): ?><i class="fa fa-triangle-exclamation me-1"></i><?php endif; ?>
                                            <?php echo htmlspecialchars($item->category); ?>
                                        </span>
                                        <?php if($item->vacancies > 1): ?>
                                            <span class="badge bg-light text-muted border px-2 py-1 small">
                                                <i class="fa fa-users me-1 text-secondary"></i><?php echo $item->vacancies; ?> Vacancies
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if(!empty($item->deadline)): ?>
                                        <div class="small fw-semibold text-danger">
                                            <i class="far fa-calendar-check me-1"></i> Deadline: <?php echo date('d M Y', strtotime($item->deadline)); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border px-2 py-1 small">
                                            <i class="fa fa-circle text-success me-1" style="font-size: 0.45rem;"></i> Actively Open
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h4 class="fw-bold text-dark mb-2">
                                    <?php echo htmlspecialchars($item->title); ?>
                                </h4>

                                <?php if(!empty($item->department)): ?>
                                    <div class="text-primary small fw-semibold mb-3">
                                        <i class="fa fa-building-columns me-1"></i> Department: <?php echo htmlspecialchars($item->department); ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Rich Description -->
                                <div class="text-secondary mb-3" style="font-size: 0.95rem; line-height: 1.65;">
                                    <?php echo $item->description; ?>
                                </div>

                                <!-- Eligibility Box if available -->
                                <?php if(!empty($item->eligibility)): ?>
                                    <div class="p-3 bg-light rounded-3 border-start border-4 border-primary mb-3">
                                        <div class="fw-bold text-dark small mb-1">
                                            <i class="fa fa-graduation-cap text-primary me-1"></i> Eligibility &amp; Criteria:
                                        </div>
                                        <div class="text-secondary small" style="line-height: 1.6;">
                                            <?php echo nl2br(htmlspecialchars($item->eligibility)); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Actions Row -->
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top mt-3">
                                    <div>
                                        <?php if($hasPdf): ?>
                                            <a href="<?php echo $pdfUrl; ?>" target="_blank" class="btn btn-sm btn-outline-danger fw-semibold px-3 rounded-pill me-2">
                                                <i class="fa fa-file-pdf me-1"></i> View Official Notice (PDF)
                                            </a>
                                            <a href="<?php echo $pdfUrl; ?>" download class="btn btn-sm btn-light border fw-semibold px-3 rounded-pill">
                                                <i class="fa fa-download me-1"></i> Download
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary px-4 py-2 rounded-pill fw-bold" onclick="openApplyModal('<?php echo htmlspecialchars(addslashes($item->title)); ?>')">
                                        <i class="fa fa-paper-plane me-1"></i> Apply / Submit Bid
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Right Sidebar (4 cols) -->
                <div class="col-lg-4">
                    <!-- Guidelines Card -->
                    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3">
                            <i class="fa fa-circle-info text-primary me-2"></i>Application Protocol
                        </h5>
                        <ul class="text-secondary small ps-3 mb-0" style="line-height: 1.8;">
                            <li>Please clearly mention the <strong>Position Title</strong> or <strong>Tender Ref</strong> in your subject.</li>
                            <li>Attach your updated CV / Company Profile along with relevant certifications.</li>
                            <li>Tenders must comply with the official sealed quotation specifications before the cutoff deadline.</li>
                            <li>Only shortlisted candidates and qualified bidders will be contacted for formal interviews.</li>
                        </ul>
                    </div>

                    <!-- Direct HR Contact Card -->
                    <div class="card border-0 shadow-sm rounded-3 p-4 text-white mb-4" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                        <h5 class="fw-bold text-white mb-2">
                            <i class="fa fa-headset me-2"></i>HR &amp; Procurement Desk
                        </h5>
                        <p class="small text-white-50 mb-3">For queries related to current requirements or tender submissions, reach out directly:</p>
                        <div class="small mb-2">
                            <i class="fa fa-phone text-warning me-2"></i> <a href="tel:<?php echo htmlspecialchars($schoolPhone); ?>" class="text-white text-decoration-none fw-bold"><?php echo htmlspecialchars($schoolPhone); ?></a>
                        </div>
                        <div class="small mb-4">
                            <i class="fa fa-envelope text-warning me-2"></i> <a href="mailto:<?php echo htmlspecialchars($schoolEmail); ?>" class="text-white text-decoration-none fw-bold"><?php echo htmlspecialchars($schoolEmail); ?></a>
                        </div>
                        <a href="<?php echo URLROOT; ?>/home/contact" class="btn btn-light text-primary btn-sm rounded-pill fw-bold w-100 py-2">
                            <i class="fa fa-map-marker-alt me-1"></i> Campus Secretariat &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL: QUICK APPLY / INQUIRE -->
    <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                    <h5 class="modal-title fw-bold" id="applyModalLabel">
                        <i class="fa fa-paper-plane me-2"></i>Apply / Inquire for Position
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo URLROOT; ?>/home/contact" method="post">
                    <input type="hidden" name="subject" id="modalApplySubject" value="Application for Requirement">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small mb-1">Applying / Inquiring For:</label>
                            <input type="text" id="modalApplyTitleDisplay" class="form-control fw-bold bg-light" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small mb-1">Your Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Sana Tariq" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small mb-1">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" placeholder="0300-1234567" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small mb-1">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="applicant@domain.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small mb-1">Brief Cover Note / Experience Summary <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Briefly highlight your relevant qualifications, current role, or tender proposal highlights..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                            <i class="fa fa-paper-plane me-1"></i> Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>

    <script>
    function openApplyModal(title) {
        document.getElementById('modalApplyTitleDisplay').value = title;
        document.getElementById('modalApplySubject').value = 'Requirement Application: ' + title;
        new bootstrap.Modal(document.getElementById('applyModal')).show();
    }
    </script>

</body>
</html>
