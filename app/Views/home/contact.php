<?php
$schoolName = $data['school']->school_name ?? $data['settings']->school_name ?? SITENAME ?? 'Our School';
$activePage = 'contact';
$pageTitle = 'Contact Us & Campus Secretariat';
$pageDesc = 'Get in touch with the admissions office, administration desk, and campus visit coordinators at ' . $schoolName . '.';
$schoolPhone = !empty($data['school']->school_phone) ? $data['school']->school_phone : (!empty($data['settings']->school_phone) ? $data['settings']->school_phone : '+92-51-111-222-333');
$schoolEmail = !empty($data['school']->school_email) ? $data['school']->school_email : (!empty($data['settings']->school_email) ? $data['settings']->school_email : 'info@school.edu.pk');
$schoolAddress = !empty($data['school']->school_address) ? $data['school']->school_address : (!empty($data['settings']->school_address) ? $data['settings']->school_address : 'Main Campus, Educational Enclave');
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
        .contact-card-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }
        .contact-info-tile {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }
        .contact-info-tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.1);
            border-color: rgba(99, 102, 241, 0.3);
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
                        <i class="fa fa-headset me-1"></i> Official Helpdesk &amp; Inquiries
                    </span>
                    <h1 class="display-5 fw-bold text-white mb-2">Connect With Campus Secretariat</h1>
                    <p class="lead text-white-50 mb-0" style="max-width: 680px;">
                        We warmly welcome parents, scholars, visitors, and partners. Contact our administrative departments or visit our executive campus during official hours.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <a href="#enquiryFormSection" class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm">
                        <i class="fa fa-envelope-open-text me-1 text-primary"></i> Send Direct Inquiry
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTACT TILES ROW -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Location -->
                <div class="col-lg-3 col-md-6">
                    <div class="contact-info-tile">
                        <div class="contact-card-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fa fa-map-location-dot"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Campus Location</h5>
                        <p class="text-secondary small mb-3" style="line-height: 1.6;">
                            <?php echo htmlspecialchars($schoolAddress); ?>
                        </p>
                        <a href="https://maps.google.com/?q=<?php echo urlencode($schoolAddress); ?>" target="_blank" class="small fw-bold text-primary text-decoration-none">
                            <i class="fa fa-directions me-1"></i> Open in Maps &rarr;
                        </a>
                    </div>
                </div>

                <!-- Phone & Helpline -->
                <div class="col-lg-3 col-md-6">
                    <div class="contact-info-tile">
                        <div class="contact-card-icon bg-success bg-opacity-10 text-success">
                            <i class="fa fa-phone-volume"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Phone Lines</h5>
                        <div class="text-secondary small mb-3">
                            <div><strong>Admissions:</strong> <a href="tel:<?php echo htmlspecialchars($schoolPhone); ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($schoolPhone); ?></a></div>
                            <div class="mt-1"><strong>Front Desk:</strong> <a href="tel:<?php echo htmlspecialchars($schoolPhone); ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($schoolPhone); ?></a></div>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                            <i class="fa fa-circle text-success me-1" style="font-size: 0.5rem;"></i> Lines Active 8am-4pm
                        </span>
                    </div>
                </div>

                <!-- Official Email -->
                <div class="col-lg-3 col-md-6">
                    <div class="contact-info-tile">
                        <div class="contact-card-icon bg-info bg-opacity-10 text-info">
                            <i class="fa fa-envelope-open-text"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Official Email</h5>
                        <div class="text-secondary small mb-3">
                            <div><strong>General:</strong> <a href="mailto:<?php echo htmlspecialchars($schoolEmail); ?>" class="text-decoration-none text-dark"><?php echo htmlspecialchars($schoolEmail); ?></a></div>
                            <div class="mt-1"><strong>Admissions:</strong> <span class="text-muted">admissions@<?php echo parse_url(URLROOT, PHP_URL_HOST) ?: 'school.edu.pk'; ?></span></div>
                        </div>
                        <a href="mailto:<?php echo htmlspecialchars($schoolEmail); ?>" class="small fw-bold text-info text-decoration-none">
                            <i class="fa fa-paper-plane me-1"></i> Write an Email &rarr;
                        </a>
                    </div>
                </div>

                <!-- Office Timings -->
                <div class="col-lg-3 col-md-6">
                    <div class="contact-info-tile">
                        <div class="contact-card-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fa fa-clock"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Office Timings</h5>
                        <div class="text-secondary small mb-2" style="font-size: 0.82rem;">
                            <div><strong>Classes:</strong> Mon&ndash;Fri 8:00am &ndash; 2:00pm</div>
                            <div><strong>Admin Desk:</strong> Mon&ndash;Fri 8:00am &ndash; 4:30pm</div>
                            <div><strong>Saturday:</strong> 8:30am &ndash; 1:30pm</div>
                        </div>
                        <span class="text-muted" style="font-size: 0.75rem;"><em>Friday break: 12:45pm &ndash; 1:45pm</em></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FORM & MAP SECTION -->
    <section class="py-4" id="enquiryFormSection">
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

            <div class="row g-4">
                <!-- Left Column: Interactive Contact Form -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-white border-bottom p-4">
                            <h4 class="fw-bold text-dark mb-1">
                                <i class="fa fa-pen-nib text-primary me-2"></i>Send an Inquiry or Schedule a Visit
                            </h4>
                            <p class="text-muted small mb-0">Fill out this quick form and our admissions desk will get in touch with you.</p>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo URLROOT; ?>/home/contact" method="post">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark small mb-1">Your Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" placeholder="e.g. Muhammad Usman" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark small mb-1">Contact Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" name="phone" class="form-control" placeholder="e.g. 0300-1234567" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark small mb-1">Email Address</label>
                                        <input type="email" name="email" class="form-control" placeholder="name@example.com">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark small mb-1">Inquiry Topic / Department <span class="text-danger">*</span></label>
                                        <select name="subject" class="form-select" required>
                                            <option value="New Admission Inquiry">🎓 New Admission Inquiry</option>
                                            <option value="Campus Tour &amp; Visit Appointment">🏫 Campus Tour &amp; Visit Appointment</option>
                                            <option value="Fee Structure &amp; Scholarship">💳 Fee Structure &amp; Scholarship</option>
                                            <option value="Student Transfer / Leaving Certificate">📄 Student Transfer / Leaving Certificate</option>
                                            <option value="Job / Career Application">💼 Job / Career Application</option>
                                            <option value="General Campus Information">ℹ️ General Campus Information</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark small mb-1">Your Message or Questions <span class="text-danger">*</span></label>
                                        <textarea name="message" class="form-control" rows="5" placeholder="Please write your questions, student age/grade level, or preferred visit date..." required></textarea>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm">
                                            <i class="fa fa-paper-plane me-2"></i>Submit Official Inquiry
                                        </button>
                                        <span class="text-muted small ms-3">Directly recorded in Front Desk CRM desk.</span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Interactive Location Map & Emergency Card -->
                <div class="col-lg-5">
                    <!-- WhatsApp Quick Desk -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4 p-4 text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; font-size: 1.6rem;">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-white">Instant WhatsApp Helpdesk</h5>
                                <p class="small text-white-50 mb-0">Chat live with our admissions counselor during working hours.</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="https://api.whatsapp.com/send?phone=<?php echo preg_replace('/[^0-9]/', '', $schoolPhone); ?>&text=Hello!%20I%20am%20inquiring%20via%20the%20official%20Contact%20page." target="_blank" class="btn btn-light text-success fw-bold w-100 rounded-pill py-2 shadow-sm">
                                <i class="fab fa-whatsapp me-2"></i> Start WhatsApp Chat Now
                            </a>
                        </div>
                    </div>

                    <!-- Campus Map Frame -->
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-location-dot text-danger me-2"></i>Campus Location Map
                            </h6>
                        </div>
                        <div class="position-relative" style="height: 280px; background: #e2e8f0;">
                            <iframe 
                                src="https://maps.google.com/maps?q=<?php echo urlencode($schoolAddress); ?>&t=&z=14&ie=UTF8&iwloc=&output=embed" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Campus Location Map">
                            </iframe>
                        </div>
                        <div class="card-footer bg-white p-3 d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="fa fa-car me-1 text-primary"></i>Visitor parking available inside main gate.</small>
                            <a href="https://maps.google.com/?q=<?php echo urlencode($schoolAddress); ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold">
                                Full Screen Map
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FREQUENTLY ASKED QUESTIONS SECTION -->
    <section class="py-5 bg-white border-top">
        <div class="container" style="max-width: 900px;">
            <div class="text-center mb-5">
                <span class="badge bg-primary-subtle text-primary border px-3 py-1 text-uppercase fw-bold mb-2">Help Center</span>
                <h3 class="fw-bold text-dark">Frequently Asked Questions</h3>
                <p class="text-muted">Quick answers regarding campus visits, admissions, and office protocol.</p>
            </div>

            <div class="accordion accordion-flush shadow-sm rounded-3 border overflow-hidden" id="contactFaqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqOneHeader">
                        <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne">
                            How do I schedule an in-person campus tour?
                        </button>
                    </h2>
                    <div id="faqOne" class="accordion-collapse collapse show" data-bs-parent="#contactFaqAccordion">
                        <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                            You can easily schedule a guided campus tour by submitting the form above or contacting our admissions helpline at <strong><?php echo htmlspecialchars($schoolPhone); ?></strong>. Tours are hosted Monday through Saturday between 9:00 AM and 1:00 PM.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqTwoHeader">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo">
                            What documents should I bring when visiting for student admission?
                        </button>
                    </h2>
                    <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                        <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                            Please bring the student's B-Form/Birth Certificate copy, father/guardian's CNIC copy, 4 passport-size photographs, and the previous class academic report card or School Leaving Certificate (SLC).
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqThreeHeader">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree">
                            What are the visiting hours for the Principal &amp; Academic Heads?
                        </button>
                    </h2>
                    <div id="faqThree" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                        <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                            Parents and visitors can meet the Principal by prior appointment between 10:00 AM and 12:30 PM on weekdays. Appointments can be scheduled through the Front Desk receptionist or via email.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="faqFourHeader">
                        <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faqFour">
                            Are administrative offices open during summer or winter holidays?
                        </button>
                    </h2>
                    <div id="faqFour" class="accordion-collapse collapse" data-bs-parent="#contactFaqAccordion">
                        <div class="accordion-body text-secondary small" style="line-height: 1.7;">
                            Yes, our Admissions Office and Accounts Department remain open throughout term breaks from 8:30 AM to 1:30 PM, Monday through Friday, except on gazetted public holidays.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require_once APPROOT . '/Views/home/partials/footer.php'; ?>

</body>
</html>
