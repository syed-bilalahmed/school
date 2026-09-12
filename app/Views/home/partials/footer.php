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
                        <img src="<?php echo URLROOT . '/' . htmlspecialchars($cmsLogo); ?>" height="42" alt="<?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 140px; object-fit: contain;">
                    <?php else: ?>
                        <div class="brand-icon-emblem">
                            <i class="fa fa-graduation-cap"></i>
                        </div>
                    <?php endif; ?>
                    <span class="footer-brand-title mb-0"><?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></span>
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
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/events"><i class="fa fa-chevron-right fa-xs"></i> Events &amp; News</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/gallery"><i class="fa fa-chevron-right fa-xs"></i> Gallery</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/alumni"><i class="fa fa-chevron-right fa-xs"></i> Alumni</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/requirements"><i class="fa fa-chevron-right fa-xs"></i> Requirements</a></li>
                    <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/contact"><i class="fa fa-chevron-right fa-xs"></i> Contact Us</a></li>
                    <?php if(($data['cms']->enable_online_admission ?? 'yes') === 'yes'): ?>
                        <li class="footer-link-item"><a href="<?php echo URLROOT; ?>/home/admission"><i class="fa fa-chevron-right fa-xs"></i> Online Admission</a></li>
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

    <?php if ($isLivechatEnabled && $livechatProvider === 'builtin'): ?>
        <!-- 3. Floating Live Chat Support Assistant Button -->
        <button type="button" class="comm-fab-btn livechat-fab-btn" id="livechatFabBtn" aria-label="Open Live Chat Support" title="Live Support">
            <div class="comm-pulse-ring"></div>
            <i class="fa fa-comment-dots" id="livechatFabIcon"></i>
            <span class="comm-online-dot"></span>
            <span class="comm-fab-tooltip">Live Support</span>
        </button>
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

<?php if ($isLivechatEnabled && $livechatProvider === 'builtin'): ?>
    <!-- LIVE CHAT INTERACTIVE SCHOOL SUPPORT WINDOW -->
    <div class="livechat-support-window" id="livechatSupportWindow" aria-hidden="true">
        <div class="livechat-header">
            <div class="d-flex align-items-center gap-2">
                <div class="livechat-avatar-wrap">
                    <i class="fa fa-graduation-cap"></i>
                    <span class="online-indicator"></span>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold fs-6 text-white"><?php echo htmlspecialchars($livechatTitle, ENT_QUOTES, 'UTF-8'); ?></h6>
                    <small class="text-white-50" style="font-size: 0.72rem;">
                        <i class="fa fa-bolt text-warning me-1"></i>Automated Campus Assistant &bull; 24/7
                    </small>
                </div>
            </div>
            <div class="livechat-header-actions d-flex align-items-center gap-1">
                <button type="button" id="livechatCallbackToggleBtn" title="Request Counselor Callback" class="me-1" aria-label="Request Callback">
                    <i class="fa fa-phone-alt"></i>
                </button>
                <button type="button" id="livechatCloseBtn" aria-label="Close Live Chat">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Inline Callback / Lead Drawer (Toggleable) -->
        <div class="livechat-enquiry-drawer" id="livechatEnquiryDrawer">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-dark small"><i class="fa fa-user-edit text-primary me-1"></i>Request Admissions Officer Callback</span>
                <button type="button" class="btn-close btn-close-sm" id="livechatCloseDrawerBtn" style="font-size: 0.65rem;" aria-label="Close"></button>
            </div>
            <form id="livechatEnquiryForm">
                <div class="row g-2">
                    <div class="col-6">
                        <input type="text" id="chatLeadName" class="form-control form-control-sm" placeholder="Your Full Name *" required>
                    </div>
                    <div class="col-6">
                        <input type="tel" id="chatLeadPhone" class="form-control form-control-sm" placeholder="Mobile / WhatsApp *" required>
                    </div>
                    <div class="col-12">
                        <input type="email" id="chatLeadEmail" class="form-control form-control-sm" placeholder="Email Address (Optional)">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold" id="chatLeadSubmitBtn" style="font-size: 0.75rem;">
                            <i class="fa fa-paper-plane me-1"></i>Submit Callback Request
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Messages Flow Stream -->
        <div class="livechat-messages-container" id="livechatMessagesStream">
            <!-- Initial Welcome Bubble -->
            <div class="chat-bubble bot-bubble">
                <div class="fw-bold text-primary mb-1" style="font-size: 0.78rem;">
                    <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?> Assistant
                </div>
                <div><?php echo nl2br(htmlspecialchars($livechatMsg, ENT_QUOTES, 'UTF-8')); ?></div>
                <div class="chat-quick-replies-tray mt-2">
                    <button type="button" class="chat-reply-chip" data-query="How to apply for online admission?">🎓 How to Apply</button>
                    <button type="button" class="chat-reply-chip" data-query="Tuition fee details and payment methods">💳 Fee Details</button>
                    <button type="button" class="chat-reply-chip" data-query="School timings and office hours">⏰ School Timings</button>
                    <button type="button" class="chat-reply-chip" data-query="Campus location and directions">📍 Campus Location</button>
                    <button type="button" class="chat-reply-chip chat-urgent-chip text-danger fw-bold" data-query="Urgent: Talk to team member / رابطہ فوری"><i class="fa fa-bolt me-1 text-danger"></i>🚨 Urgent Help / رابطہ کونسلر</button>
                </div>
                <span class="chat-time-tag text-muted"><?php echo date('h:i A'); ?></span>
            </div>

            <!-- Typing Indicator Element -->
            <div class="chat-typing-dots" id="chatTypingDots">
                <span></span><span></span><span></span>
            </div>
        </div>

        <!-- Bottom Input Bar -->
        <div class="livechat-input-bar">
            <input type="text" id="livechatUserInput" class="livechat-input-field" placeholder="Ask about admissions, fee, timings..." autocomplete="off">
            <button type="button" id="livechatSendBtn" class="livechat-send-btn" title="Send Question" aria-label="Send">
                <i class="fa fa-paper-plane" style="font-size: 0.9rem;"></i>
            </button>
        </div>
    </div>
<?php endif; ?>

<?php if ($isLivechatEnabled && $livechatProvider === 'tawk' && !empty($livechatTawkProp) && !empty($livechatTawkWidget)): ?>
    <!-- TAWK.TO RUNTIME INJECTION -->
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/<?php echo htmlspecialchars($livechatTawkProp, ENT_QUOTES, 'UTF-8'); ?>/<?php echo htmlspecialchars($livechatTawkWidget, ENT_QUOTES, 'UTF-8'); ?>';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
<?php endif; ?>

<?php if ($isLivechatEnabled && $livechatProvider === 'crisp' && !empty($livechatCrispId)): ?>
    <!-- CRISP RUNTIME INJECTION -->
    <script type="text/javascript">
    window.$crisp=[];window.CRISP_WEBSITE_ID="<?php echo htmlspecialchars($livechatCrispId, ENT_QUOTES, 'UTF-8'); ?>";
    (function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();
    </script>
<?php endif; ?>

<?php if ($isLivechatEnabled && $livechatProvider === 'custom' && !empty($livechatCustomScript)): ?>
    <!-- CUSTOM CHAT RUNTIME SCRIPT -->
    <?php echo $livechatCustomScript; ?>
<?php endif; ?>

<!-- Bootstrap Bundle JS & Animation Handlers -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
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
    // 4. FLOATING WHATSAPP & LIVE CHAT INTERACTIVE LOGIC
    // =========================================================================
    const wpFabBtn = document.getElementById('whatsappFabBtn');
    const wpPopup = document.getElementById('whatsappChatPopup');
    const wpCloseBtn = document.getElementById('whatsappCloseBtn');
    const wpSendBtn = document.getElementById('whatsappSendBtn');
    const wpCustomInput = document.getElementById('whatsappCustomInput');

    const livechatFabBtn = document.getElementById('livechatFabBtn');
    const livechatWindow = document.getElementById('livechatSupportWindow');
    const livechatCloseBtn = document.getElementById('livechatCloseBtn');
    const livechatFabIcon = document.getElementById('livechatFabIcon');
    const livechatSendBtn = document.getElementById('livechatSendBtn');
    const livechatInput = document.getElementById('livechatUserInput');
    const livechatStream = document.getElementById('livechatMessagesStream');
    const livechatTyping = document.getElementById('chatTypingDots');
    const callbackToggleBtn = document.getElementById('livechatCallbackToggleBtn');
    const callbackDrawer = document.getElementById('livechatEnquiryDrawer');
    const closeDrawerBtn = document.getElementById('livechatCloseDrawerBtn');
    const callbackForm = document.getElementById('livechatEnquiryForm');

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
                    if (livechatWindow) {
                        livechatWindow.classList.remove('active');
                        if (livechatFabBtn) livechatFabBtn.classList.remove('active-chat');
                        if (livechatFabIcon) {
                            livechatFabIcon.className = 'fa fa-comment-dots';
                        }
                    }
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
        const cleanNumber = wpFabBtn ? wpFabBtn.getAttribute('data-number') : '923001234567';
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

    // Live Chat Window Toggle
    if (livechatFabBtn && livechatWindow) {
        livechatFabBtn.addEventListener('click', function() {
            const isOpen = livechatWindow.classList.contains('active');
            if (isOpen) {
                livechatWindow.classList.remove('active');
                livechatFabBtn.classList.remove('active-chat');
                if (livechatFabIcon) livechatFabIcon.className = 'fa fa-comment-dots';
            } else {
                livechatWindow.classList.add('active');
                livechatFabBtn.classList.add('active-chat');
                if (livechatFabIcon) livechatFabIcon.className = 'fa fa-times';
                if (wpPopup) wpPopup.classList.remove('active');
                if (livechatInput) livechatInput.focus();
                scrollChatToBottom();
            }
        });
    }

    if (livechatCloseBtn && livechatWindow) {
        livechatCloseBtn.addEventListener('click', function() {
            livechatWindow.classList.remove('active');
            if (livechatFabBtn) livechatFabBtn.classList.remove('active-chat');
            if (livechatFabIcon) livechatFabIcon.className = 'fa fa-comment-dots';
        });
    }

    function scrollChatToBottom() {
        if (livechatStream) {
            livechatStream.scrollTop = livechatStream.scrollHeight;
        }
    }

    function formatTimeNow() {
        const d = new Date();
        let h = d.getHours();
        const m = String(d.getMinutes()).padStart(2, '0');
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12;
        return h + ':' + m + ' ' + ampm;
    }

    // Send Message to Live Chat API
    function sendLiveChatMessage(messageText) {
        const text = (messageText || (livechatInput ? livechatInput.value : '')).trim();
        if (!text || !livechatStream) return;

        // Append User Bubble
        const userBubble = document.createElement('div');
        userBubble.className = 'chat-bubble user-bubble';
        userBubble.innerHTML = '<div>' + escapeHtml(text) + '</div><span class="chat-time-tag">' + formatTimeNow() + '</span>';
        
        if (livechatTyping) {
            livechatStream.insertBefore(userBubble, livechatTyping);
        } else {
            livechatStream.appendChild(userBubble);
        }

        if (livechatInput) livechatInput.value = '';
        if (livechatTyping) livechatTyping.classList.add('active');
        scrollChatToBottom();

        // Call Runtime API
        const apiUrl = '<?php echo URLROOT; ?>/home/livechatApi';
        fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=query&message=' + encodeURIComponent(text)
        })
        .then(res => res.json())
        .then(data => {
            if (livechatTyping) livechatTyping.classList.remove('active');
            
            const botBubble = document.createElement('div');
            botBubble.className = 'chat-bubble bot-bubble';

            let botHtml = '<div>' + (data.reply || 'Thank you for reaching out! How else can I assist you?') + '</div>';

            if (data.quick_replies && data.quick_replies.length > 0) {
                botHtml += '<div class="chat-quick-replies-tray mt-2">';
                data.quick_replies.forEach(chipText => {
                    botHtml += '<button type="button" class="chat-reply-chip" data-query="' + escapeHtml(chipText) + '">' + escapeHtml(chipText) + '</button>';
                });
                botHtml += '</div>';
            }

            botHtml += '<span class="chat-time-tag text-muted">' + formatTimeNow() + '</span>';
            botBubble.innerHTML = botHtml;

            if (livechatTyping) {
                livechatStream.insertBefore(botBubble, livechatTyping);
            } else {
                livechatStream.appendChild(botBubble);
            }

            scrollChatToBottom();
        })
        .catch(err => {
            console.error('Chat API error:', err);
            if (livechatTyping) livechatTyping.classList.remove('active');
            
            const errBubble = document.createElement('div');
            errBubble.className = 'chat-bubble bot-bubble text-danger';
            errBubble.innerHTML = '<div>Our admissions counselor is standing by on WhatsApp. Feel free to click the WhatsApp button to chat directly!</div><span class="chat-time-tag text-muted">' + formatTimeNow() + '</span>';
            if (livechatTyping) {
                livechatStream.insertBefore(errBubble, livechatTyping);
            } else {
                livechatStream.appendChild(errBubble);
            }
            scrollChatToBottom();
        });
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    if (livechatSendBtn) {
        livechatSendBtn.addEventListener('click', function() {
            sendLiveChatMessage();
        });
    }
    if (livechatInput) {
        livechatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendLiveChatMessage();
            }
        });
    }

    // Delegated click for dynamic Quick Reply Chips in Chat Stream
    if (livechatStream) {
        livechatStream.addEventListener('click', function(e) {
            const chip = e.target.closest('.chat-reply-chip');
            if (chip) {
                const query = chip.getAttribute('data-query');
                if (query) {
                    if (query.includes('WhatsApp') || query.includes('Open WhatsApp')) {
                        dispatchWhatsAppMsg('Hello! I would like to connect directly with an Admissions Officer.');
                    } else if (query.includes('Callback') || query.includes('Request callback')) {
                        if (callbackDrawer) callbackDrawer.classList.add('active');
                    } else {
                        sendLiveChatMessage(query);
                    }
                }
            }
        });
    }

    // Callback Drawer Handlers
    if (callbackToggleBtn && callbackDrawer) {
        callbackToggleBtn.addEventListener('click', function() {
            callbackDrawer.classList.toggle('active');
        });
    }
    if (closeDrawerBtn && callbackDrawer) {
        closeDrawerBtn.addEventListener('click', function() {
            callbackDrawer.classList.remove('active');
        });
    }

    if (callbackForm) {
        callbackForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const nameInput = document.getElementById('chatLeadName');
            const phoneInput = document.getElementById('chatLeadPhone');
            const emailInput = document.getElementById('chatLeadEmail');
            const submitBtn = document.getElementById('chatLeadSubmitBtn');

            if (!nameInput || !phoneInput) return;
            const name = nameInput.value.trim();
            const phone = phoneInput.value.trim();
            const email = emailInput ? emailInput.value.trim() : '';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Submitting...';
            }

            const apiUrl = '<?php echo URLROOT; ?>/home/livechatApi';
            fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=submit_enquiry&name=' + encodeURIComponent(name) + '&phone=' + encodeURIComponent(phone) + '&email=' + encodeURIComponent(email) + '&message=' + encodeURIComponent('Live Chat Callback Request')
            })
            .then(res => res.json())
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-paper-plane me-1"></i>Submit Callback Request';
                }
                if (callbackDrawer) callbackDrawer.classList.remove('active');
                callbackForm.reset();

                // Append Confirmation Bubble
                const confBubble = document.createElement('div');
                confBubble.className = 'chat-bubble bot-bubble bg-success text-white border-0';
                confBubble.innerHTML = '<div><i class="fa fa-check-circle me-1"></i> ' + (data.message || 'Thank you! Your callback request has been logged. Our admissions counselor will call you shortly.') + '</div><span class="chat-time-tag text-white-50">' + formatTimeNow() + '</span>';
                if (livechatTyping) {
                    livechatStream.insertBefore(confBubble, livechatTyping);
                } else {
                    livechatStream.appendChild(confBubble);
                }
                scrollChatToBottom();
            })
            .catch(err => {
                console.error('Callback error:', err);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa fa-paper-plane me-1"></i>Submit Callback Request';
                }
                alert('Could not submit callback request. Please contact us directly on WhatsApp or phone.');
            });
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
});

// Global Mobile Live Chat Trigger
window.triggerMobileLiveChat = function() {
    const livechatFab = document.getElementById('livechatFabBtn');
    if (livechatFab) {
        livechatFab.click();
    } else {
        const win = document.getElementById('livechatWindow');
        if (win) win.classList.add('active');
    }
};

// Global Inline Callback Submitter (Inside Urgent Chat Bubble)
window.submitInlineCallback = function(btn) {
    const box = btn.closest('.chat-callback-box');
    if (!box) return;
    const nameInput = box.querySelector('#inlineCallbackName');
    const phoneInput = box.querySelector('#inlineCallbackPhone');
    const msgDiv = box.querySelector('#inlineCallbackMsg');

    const name = nameInput ? nameInput.value.trim() : '';
    const phone = phoneInput ? phoneInput.value.trim() : '';

    if (!name || !phone) {
        alert('براہِ کرم اپنا نام اور فون نمبر درج کریں۔\nPlease enter your name and phone number.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

    const apiUrl = '<?php echo URLROOT; ?>/home/livechatApi';
    fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=submit_enquiry&priority=Urgent&name=' + encodeURIComponent(name) + '&phone=' + encodeURIComponent(phone) + '&message=' + encodeURIComponent('Urgent bot escalation callback request')
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-check"></i> Sent';
        btn.className = 'btn btn-sm btn-success fw-bold';
        if (nameInput) nameInput.disabled = true;
        if (phoneInput) phoneInput.disabled = true;
        if (msgDiv) {
            msgDiv.style.display = 'block';
            msgDiv.innerHTML = '<i class="fa fa-check-circle me-1"></i> شکریہ ' + name + '! آپ کی ارجنٹ درخواست موصول ہو گئی ہے۔ کونسلر فوراً رابطہ کرے گا۔';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = 'Submit';
        alert('Network error. Please call helpline directly: <?php echo htmlspecialchars($schoolPhone); ?>');
    });
};
</script>

