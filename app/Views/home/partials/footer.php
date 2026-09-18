<?php
$siteSettings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];
$schoolName = !empty($siteSettings['school_name']) ? $siteSettings['school_name'] : ($data['school']->school_name ?? $data['settings']->school_name ?? SITENAME);
$schoolPhone = !empty($siteSettings['school_phone']) ? $siteSettings['school_phone'] : ($data['school']->phone ?? $data['settings']->phone ?? '+92-51-111-222-333');
$schoolEmail = !empty($siteSettings['school_email']) ? $siteSettings['school_email'] : ($data['school']->email ?? $data['settings']->email ?? 'info@citymodelschool.edu.pk');
$schoolAddress = !empty($siteSettings['school_address']) ? $siteSettings['school_address'] : ($data['school']->address ?? $data['settings']->address ?? 'Main Campus, Sector H-8, Islamabad');
$cmsLogo = !empty($siteSettings['logo']) ? $siteSettings['logo'] : ($data['cms']->logo ?? '');

// WhatsApp & Live Chat Configurations
$isWhatsAppEnabled = ($siteSettings['whatsapp_enabled'] ?? '1') !== '0';
$rawWpNumber = !empty($siteSettings['whatsapp_number']) ? $siteSettings['whatsapp_number'] : '+92-300-1234567';
$cleanWpNumber = preg_replace('/[^0-9]/', '', $rawWpNumber);
$wpDefaultMsg = !empty($siteSettings['whatsapp_default_msg']) ? $siteSettings['whatsapp_default_msg'] : 'Hello! I would like to inquire about admissions and school programs.';
$wpAgentName = !empty($siteSettings['whatsapp_agent_name']) ? $siteSettings['whatsapp_agent_name'] : 'Admissions & Helpdesk';
$wpPopupEnabled = ($siteSettings['whatsapp_popup_enabled'] ?? '1') !== '0';

$isLivechatEnabled = ($siteSettings['livechat_enabled'] ?? '1') !== '0';
$livechatProvider = !empty($siteSettings['livechat_provider']) ? $siteSettings['livechat_provider'] : 'builtin';
$livechatTitle = !empty($siteSettings['livechat_welcome_title']) ? $siteSettings['livechat_welcome_title'] : 'Live School Support';
$livechatMsg = !empty($siteSettings['livechat_welcome_msg']) ? $siteSettings['livechat_welcome_msg'] : 'Hello! Welcome to our school helpdesk. How can we assist you today?';
$livechatTawkProp = $siteSettings['livechat_tawk_property_id'] ?? '';
$livechatTawkWidget = $siteSettings['livechat_tawk_widget_id'] ?? '';
$livechatCrispId = $siteSettings['livechat_crisp_website_id'] ?? '';
$livechatCustomScript = $siteSettings['livechat_custom_script'] ?? '';
?>
<!-- MULTI-COLUMN LUXURY FOOTER -->
<footer class="front-footer">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- Brand & Mission Column -->
            <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <?php if(!empty($cmsLogo)): ?>
                        <img src="<?php echo URLROOT . '/' . htmlspecialchars($cmsLogo); ?>" height="52" alt="<?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?>" style="height: 52px; max-height: 52px; max-width: 170px; object-fit: contain;">
                    <?php else: ?>
                        <div class="brand-icon-emblem" style="width: 46px; height: 46px; font-size: 1.3rem;">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                    <?php endif; ?>
                    <span class="footer-brand-title mb-0" style="font-size: 1.25rem; font-weight: 800;"><?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <p class="footer-bio">
                    Dedicated to academic distinction, innovative leadership, and holistic character development. Empowering generations of visionary global thinkers and future leaders.
                </p>
                <div class="footer-social-links">
                    <?php if(!empty($data['cms']->facebook_url)): ?>
                        <a href="<?php echo htmlspecialchars($data['cms']->facebook_url); ?>" target="_blank" class="social-icon-btn" title="Facebook" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php else: ?>
                        <a href="#" class="social-icon-btn" title="Facebook" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>

                    <?php if(!empty($data['cms']->twitter_url)): ?>
                        <a href="<?php echo htmlspecialchars($data['cms']->twitter_url); ?>" target="_blank" class="social-icon-btn" title="Twitter" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <?php else: ?>
                        <a href="#" class="social-icon-btn" title="Twitter" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>

                    <?php if(!empty($data['cms']->instagram_url)): ?>
                        <a href="<?php echo htmlspecialchars($data['cms']->instagram_url); ?>" target="_blank" class="social-icon-btn" title="Instagram" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php else: ?>
                        <a href="#" class="social-icon-btn" title="Instagram" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>

                    <?php if(!empty($data['cms']->linkedin_url)): ?>
                        <a href="<?php echo htmlspecialchars($data['cms']->linkedin_url); ?>" target="_blank" class="social-icon-btn" title="LinkedIn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <?php else: ?>
                        <a href="#" class="social-icon-btn" title="LinkedIn" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>

                    <?php if(!empty($data['cms']->youtube_url)): ?>
                        <a href="<?php echo htmlspecialchars($data['cms']->youtube_url); ?>" target="_blank" class="social-icon-btn" title="YouTube" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <?php else: ?>
                        <a href="#" class="social-icon-btn" title="YouTube" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Navigation Links -->
            <div class="col-lg-2 col-md-6 col-6 mb-4 mb-lg-0">
                <h6 class="footer-col-title">Navigation</h6>
                <ul class="footer-links-list">
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/"><i class="fa fa-chevron-right fa-xs"></i> Home</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/academics"><i class="fa fa-chevron-right fa-xs"></i> Academics</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/facilities"><i class="fa fa-chevron-right fa-xs"></i> Campus Life</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/fees"><i class="fa fa-chevron-right fa-xs"></i> Tuition &amp; Fees</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/challan"><i class="fa fa-chevron-right fa-xs"></i> Bank Fee Challan</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/events"><i class="fa fa-chevron-right fa-xs"></i> Events &amp; News</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/gallery"><i class="fa fa-chevron-right fa-xs"></i> Gallery</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/alumni"><i class="fa fa-chevron-right fa-xs"></i> Alumni</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/requirements"><i class="fa fa-chevron-right fa-xs"></i> Requirements</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/contact"><i class="fa fa-chevron-right fa-xs"></i> Contact Us</a></li>
                    <?php if(($data['cms']->enable_online_admission ?? 'yes') === 'yes'): ?>
                        <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/admission"><i class="fa fa-chevron-right fa-xs"></i> Online Admission</a></li>
                        <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/track_admission"><i class="fa fa-chevron-right fa-xs"></i> Track Application</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Academic Programs -->
            <div class="col-lg-3 col-md-6 col-6 mb-4 mb-lg-0">
                <h6 class="footer-col-title">Programs</h6>
                <ul class="footer-links-list">
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/#academics"><i class="fa fa-chevron-right fa-xs"></i> Early Childhood Prep</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/#academics"><i class="fa fa-chevron-right fa-xs"></i> Elementary Discovery</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/#academics"><i class="fa fa-chevron-right fa-xs"></i> Middle School Honors</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/#academics"><i class="fa fa-chevron-right fa-xs"></i> High School &amp; AP/IB</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/#academics"><i class="fa fa-chevron-right fa-xs"></i> STEAM &amp; Robotics Labs</a></li>
                </ul>
            </div>

            <!-- Campus Contact Info -->
            <div class="col-lg-3 col-md-6">
                <h6 class="footer-col-title">Campus Info</h6>
                <div class="footer-contact-info">
                    <div class="contact-info-row">
                        <i class="fa fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($schoolAddress, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="contact-info-row">
                        <i class="fa fa-phone"></i>
                        <span><?php echo htmlspecialchars($schoolPhone, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="contact-info-row">
                        <i class="fa fa-envelope"></i>
                        <span><?php echo htmlspecialchars($schoolEmail, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="contact-info-row">
                        <i class="fa fa-clock"></i>
                        <span>Mon &ndash; Fri: 8:00 AM &ndash; 4:30 PM</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom-bar">
            <div>
                &copy; <?php echo date('Y'); ?> <strong><?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></strong>. All rights reserved.
            </div>
            <div class="d-flex align-items-center gap-3">
                <span>Inspiring Excellence &amp; Character</span>
                <span class="text-white-50">&bull;</span>
                <?php if(isset($_SESSION['user_id'])): 
                    $footUserRole = $_SESSION['user_role'] ?? 'admin';
                    $footHomeUrl = URLROOT . '/admin/dashboard';
                    if ($footUserRole === 'teacher') $footHomeUrl = URLROOT . '/teacher/index';
                    elseif ($footUserRole === 'student') $footHomeUrl = URLROOT . '/student/index';
                    elseif ($footUserRole === 'parent') $footHomeUrl = URLROOT . '/parent/index';
                    elseif ($footUserRole === 'receptionist') $footHomeUrl = URLROOT . '/frontoffice/index';
                    elseif ($footUserRole === 'accountant') $footHomeUrl = URLROOT . '/fees/collect';
                    elseif ($footUserRole === 'librarian') $footHomeUrl = URLROOT . '/library/index';
                ?>
                    <a href="<?php echo $footHomeUrl; ?>" class="text-muted text-decoration-none hover-white">
                        <i class="fa fa-tachometer-alt me-1"></i> My Portal
                    </a>
                <?php else: ?>
                    <a href="<?php echo URLROOT; ?>/auth/login" class="text-muted text-decoration-none hover-white">
                        <i class="fa fa-lock me-1"></i> Staff Portal
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================================= -->
<!-- LUXURY MOBILE BOTTOM NAVIGATION BAR (Visible on screens <= 991px) -->
<!-- ============================================================= -->
<?php 
$wpDirectUrl = "https://api.whatsapp.com/send?phone=" . urlencode($cleanWpNumber) . "&text=" . rawurlencode($wpDefaultMsg);
?>
<nav class="mobile-bottom-nav" id="mobileBottomNav" aria-label="Mobile Navigation">
    <a href="<?php echo URLROOT; ?>/" class="mobile-nav-item <?php echo ($activePage === 'home') ? 'active' : ''; ?>">
        <i class="fa fa-home"></i>
        <span>Home</span>
    </a>
    <a href="<?php echo URLROOT; ?>/home/admission" class="mobile-nav-item <?php echo ($activePage === 'admission') ? 'active' : ''; ?>">
        <i class="fa fa-graduation-cap"></i>
        <span>Admission</span>
    </a>
    <?php if ($isLivechatEnabled && $livechatProvider === 'builtin'): ?>
        <button type="button" class="mobile-nav-item" onclick="triggerMobileLiveChat()" aria-label="Live Chat">
            <span class="mobile-nav-badge-dot"></span>
            <i class="fa fa-comment-dots text-primary"></i>
            <span>Live Chat</span>
        </button>
    <?php endif; ?>
    <?php if ($isWhatsAppEnabled): ?>
        <a href="<?php echo htmlspecialchars($wpDirectUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="mobile-nav-item" aria-label="WhatsApp">
            <i class="fab fa-whatsapp text-success"></i>
            <span>WhatsApp</span>
        </a>
    <?php endif; ?>
    <a href="tel:<?php echo htmlspecialchars($schoolPhone); ?>" class="mobile-nav-item" aria-label="Call Helpline">
        <i class="fa fa-phone text-danger"></i>
        <span>Call Desk</span>
    </a>
    <a href="<?php echo isset($_SESSION['user_id']) ? URLROOT . '/admin/dashboard' : URLROOT . '/auth/login'; ?>" class="mobile-nav-item" aria-label="Portal Login">
        <i class="fa fa-lock"></i>
        <span><?php echo isset($_SESSION['user_id']) ? 'Dashboard' : 'Portal'; ?></span>
    </a>
</nav>

<!-- ============================================================= -->
<!-- UNIFIED FLOATING ACTION HUB (Scroll-To-Top, WhatsApp, Live Chat) -->
<!-- ============================================================= -->
<div class="floating-comm-hub" id="floatingCommHub">
    <!-- 1. Clear High-Contrast Back-To-Top Button -->
    <button type="button" class="scroll-to-top-btn" id="scrollToTopBtn" aria-label="Scroll to top" title="Back to top">
        <i class="fa fa-arrow-up"></i>
    </button>

    <?php if ($isWhatsAppEnabled): ?>
        <!-- 2. Floating WhatsApp Helpdesk Button -->
        <?php 
        $wpDirectUrl = "https://api.whatsapp.com/send?phone=" . urlencode($cleanWpNumber) . "&text=" . rawurlencode($wpDefaultMsg);
        ?>
        <a href="<?php echo htmlspecialchars($wpDirectUrl, ENT_QUOTES, 'UTF-8'); ?>" 
           class="comm-fab-btn whatsapp-fab-btn" 
           id="whatsappFabBtn" 
           target="_blank" 
           rel="noopener noreferrer" 
           aria-label="Chat on WhatsApp" 
           data-popup="<?php echo $wpPopupEnabled ? '1' : '0'; ?>"
           data-number="<?php echo htmlspecialchars($cleanWpNumber, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="comm-pulse-ring"></div>
            <i class="fab fa-whatsapp"></i>
            <span class="comm-online-dot"></span>
            <span class="comm-fab-tooltip">Chat on WhatsApp</span>
        </a>
    <?php endif; ?>
</div>

<?php if ($isWhatsAppEnabled && $wpPopupEnabled): ?>
    <!-- WHATSAPP INTERACTIVE QUICK-CHAT POPUP CARD -->
    <div class="whatsapp-chat-popup" id="whatsappChatPopup" aria-hidden="true">
        <div class="whatsapp-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="whatsapp-avatar-wrap">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold fs-6 text-white"><?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></h6>
                    <small class="text-white-50" style="font-size: 0.72rem;">
                        <i class="fa fa-circle text-success me-1" style="font-size: 0.55rem;"></i><?php echo htmlspecialchars($wpAgentName, ENT_QUOTES, 'UTF-8'); ?> &bull; Online
                    </small>
                </div>
            </div>
            <button type="button" class="whatsapp-close-btn" id="whatsappCloseBtn" aria-label="Close WhatsApp card">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="whatsapp-card-body">
            <div class="whatsapp-bubble">
                <div class="fw-bold mb-1 text-success" style="font-size: 0.78rem;">
                    <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?> Helpdesk
                </div>
                <div>Hi there! 👋 Welcome to our campus helpdesk. How can we assist you today? Click a quick topic or type your message below:</div>
                <div class="whatsapp-chips-container mt-2">
                    <span class="whatsapp-chip" data-msg="Hello! I would like to inquire about new admissions, class eligibility, and enrollment procedure.">🎓 Admissions Desk</span>
                    <span class="whatsapp-chip" data-msg="Hello! Can you please share details regarding the tuition fee structure, sibling discounts, and bank payment options?">💳 Fee &amp; Accounts Desk</span>
                    <span class="whatsapp-chip" data-msg="Hello! I would like to inquire about school timings and schedule a counseling appointment with the campus administration.">📞 Campus Administration</span>
                    <span class="whatsapp-chip" data-msg="Hello! I have a general inquiry regarding academic curriculum, faculty, and school programs.">📚 Academic Programs</span>
                </div>
            </div>
        </div>
        <div class="whatsapp-card-footer">
            <div class="whatsapp-input-group">
                <input type="text" id="whatsappCustomInput" class="whatsapp-msg-input" placeholder="Type your WhatsApp message..." value="<?php echo htmlspecialchars($wpDefaultMsg, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="button" id="whatsappSendBtn" class="whatsapp-send-btn" title="Open WhatsApp Chat">
                    <i class="fa fa-paper-plane" style="font-size: 0.9rem;"></i>
                </button>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                <small class="text-muted" style="font-size: 0.7rem;"><i class="fa fa-lock me-1"></i>Official WhatsApp Desk</small>
                <a href="<?php echo htmlspecialchars($wpDirectUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="text-success fw-bold text-decoration-none" style="font-size: 0.72rem;">
                    Direct Chat <i class="fa fa-chevron-right fa-xs ms-1"></i>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Bootstrap Bundle JS & Animation Handlers -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize AOS (Animate On Scroll)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60
        });
    }

    // 2. Scroll to top handler
    const scrollBtn = document.getElementById('scrollToTopBtn');
    if (scrollBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 350) {
                scrollBtn.classList.add('visible');
            } else {
                scrollBtn.classList.remove('visible');
            }
        });
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // 3. Counter-Up Animation for Stats
    const counterElements = document.querySelectorAll('.hero-stat-num, .counter-value');
    if ('IntersectionObserver' in window && counterElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const text = el.innerText.trim();
                    const numMatch = text.match(/([0-9.]+)/);
                    if (numMatch) {
                        const target = parseFloat(numMatch[1]);
                        const prefix = text.split(numMatch[1])[0] || '';
                        const suffix = text.split(numMatch[1])[1] || '';
                        const isDecimal = numMatch[1].includes('.');
                        let current = 0;
                        const steps = 35;
                        const increment = target / steps;
                        const stepTime = 30;
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= target) {
                                current = target;
                                clearInterval(timer);
                            }
                            el.innerText = prefix + (isDecimal ? current.toFixed(1) : Math.floor(current)) + suffix;
                        }, stepTime);
                    }
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.3 });

        counterElements.forEach(el => observer.observe(el));
    }

    // =========================================================================
    // 4. FLOATING WHATSAPP INTERACTIVE LOGIC
    // =========================================================================
    const wpFabBtn = document.getElementById('whatsappFabBtn');
    const wpPopup = document.getElementById('whatsappChatPopup');
    const wpCloseBtn = document.getElementById('whatsappCloseBtn');
    const wpSendBtn = document.getElementById('whatsappSendBtn');
    const wpCustomInput = document.getElementById('whatsappCustomInput');

    // WhatsApp Popup Toggle
    if (wpFabBtn && wpPopup) {
        wpFabBtn.addEventListener('click', function(e) {
            if (wpFabBtn.getAttribute('data-popup') === '1') {
                e.preventDefault();
                const isOpen = wpPopup.classList.contains('active');
                if (isOpen) {
                    wpPopup.classList.remove('active');
                } else {
                    wpPopup.classList.add('active');
                    if (wpCustomInput) wpCustomInput.focus();
                }
            }
        });
    }

    if (wpCloseBtn && wpPopup) {
        wpCloseBtn.addEventListener('click', function() {
            wpPopup.classList.remove('active');
        });
    }

    // WhatsApp Quick Chips Click
    document.querySelectorAll('.whatsapp-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            const msg = this.getAttribute('data-msg') || this.innerText;
            if (wpCustomInput) wpCustomInput.value = msg;
            dispatchWhatsAppMsg(msg);
        });
    });

    // WhatsApp Send Custom Input
    function dispatchWhatsAppMsg(msg) {
        const cleanNumber = wpFabBtn ? wpFabBtn.getAttribute('data-number') : '923360606905';
        const finalMsg = msg || (wpCustomInput ? wpCustomInput.value : '');
        const targetUrl = 'https://api.whatsapp.com/send?phone=' + encodeURIComponent(cleanNumber) + '&text=' + encodeURIComponent(finalMsg);
        window.open(targetUrl, '_blank');
        if (wpPopup) wpPopup.classList.remove('active');
    }

    if (wpSendBtn) {
        wpSendBtn.addEventListener('click', function() {
            dispatchWhatsAppMsg();
        });
    }
    if (wpCustomInput) {
        wpCustomInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                dispatchWhatsAppMsg();
            }
        });
    }

    // Scroll-To-Top Button Interactive Logic
    const scrollTopBtn = document.getElementById('scrollToTopBtn');
    if (scrollTopBtn) {
        function checkScrollPosition() {
            if (window.scrollY > 250) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        }
        window.addEventListener('scroll', checkScrollPosition, { passive: true });
        checkScrollPosition();

        scrollTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // 5. Instant Navigation Prefetcher (Instant Page Transitions)
    const prefetchedLinks = new Set();
    function prefetchInternalLink(url) {
        if (!url || prefetchedLinks.has(url)) return;
        if (url.includes('#') || url.startsWith('javascript:') || url.startsWith('mailto:') || url.startsWith('tel:') || url.includes('api.whatsapp.com')) return;
        try {
            const dest = new URL(url, window.location.href);
            if (dest.origin !== window.location.origin) return;
            prefetchedLinks.add(url);
            const linkEl = document.createElement('link');
            linkEl.rel = 'prefetch';
            linkEl.href = url;
            linkEl.as = 'document';
            document.head.appendChild(linkEl);
        } catch(e) {}
    }

    document.addEventListener('mouseover', function(e) {
        const a = e.target.closest('a');
        if (a && a.href) prefetchInternalLink(a.href);
    }, { passive: true });

    document.addEventListener('touchstart', function(e) {
        const a = e.target.closest('a');
        if (a && a.href) prefetchInternalLink(a.href);
    }, { passive: true });
});
</script>

