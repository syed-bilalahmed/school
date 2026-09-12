<?php
$schoolName = $data['school']->school_name ?? $data['settings']->school_name ?? SITENAME ?? 'Imperial Academy';
$activePage = 'news';
$theme = $data['settings']->theme_color ?? 'default';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News &amp; Institutional Journal &mdash; <?php echo htmlspecialchars($schoolName); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/frontend.css?v=3.3">
</head>
<body>

    <?php require_once APPROOT . '/Views/home/partials/navbar.php'; ?>

    <section class="bg-primary text-white py-5" style="background: var(--f-primary-gradient, linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%)) !important;">
        <div class="container py-4 text-center">
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold text-uppercase mb-2"><i class="fa fa-newspaper me-1"></i> Campus Journal</span>
            <h1 class="display-5 fw-bold mb-2">Latest News &amp; Achievements</h1>
            <p class="lead opacity-85 mb-0" style="max-width: 650px; margin: 0 auto;">Read stories of student excellence, faculty honors, and academic breakthroughs at <?php echo htmlspecialchars($schoolName); ?>.</p>
        </div>
    </section>

    <section class="section-py bg-white">
        <div class="container">
            <div class="row g-4">
                <?php if(!empty($data['news'])): ?>
                    <?php foreach($data['news'] as $newsItem): 
                        $nDate = strtotime($newsItem->news_date ?? 'now');
                        $nImg = !empty($newsItem->image) ? URLROOT . '/' . $newsItem->image : 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=600&auto=format&fit=crop';
                    ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="news-card-luxury border rounded-3 overflow-hidden shadow-sm h-100 bg-white">
                                <img src="<?php echo $nImg; ?>" class="news-img" alt="<?php echo htmlspecialchars($newsItem->title); ?>" loading="lazy" style="height: 200px; object-fit: cover; width: 100%;">
                                <div class="news-body p-4">
                                    <div class="news-date text-primary fw-bold small mb-2">
                                        <i class="fa fa-calendar-alt me-1"></i> <?php echo date('F d, Y', $nDate); ?>
                                    </div>
                                    <h4 class="news-title fw-bold text-dark h5 mb-2"><?php echo htmlspecialchars($newsItem->title); ?></h4>
                                    <p class="news-desc text-muted small mb-0"><?php echo htmlspecialchars($newsItem->description ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="mb-3 text-muted"><i class="fa fa-newspaper fa-4x opacity-50"></i></div>
                        <h4 class="fw-bold text-dark">No news articles published yet.</h4>
                        <p class="text-muted">Check back soon for latest campus updates.</p>
                        <a href="<?php echo URLROOT; ?>" class="btn btn-primary btn-sm px-4">Back to Home</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>

</body>
</html>
