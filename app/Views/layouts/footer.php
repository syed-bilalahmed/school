<?php
// Fast-Path Partial PJAX: If requested via AJAX, close .main-content-container and exit immediately!
if (!empty($_SERVER['HTTP_X_PJAX'])) {
    echo '</div>' . PHP_EOL;
    return;
}
?>
        </div> <!-- End Main Content Container -->
        </div> <!-- End Content Area -->
    </div> <!-- End App Wrapper -->

<!-- Dedicated Root Portal for all Bootstrap Modals (Guarantees Stacking Order above Backdrops) -->
<div id="erp-global-modal-portal"></div>

<!-- Bootstrap 5 Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- ============================================================
     GLOBAL CSRF AUTO-INJECTION
     1. Automatically adds csrf_token hidden field to every <form>
        that uses method="post" and doesn't already have one.
     2. Patches global fetch() to automatically include X-CSRF-Token
        header on all POST requests (covers AJAX modal calls).
     ============================================================ -->
<script>
(function() {
    var csrfToken = window.CSRF_TOKEN || '';
    if (!csrfToken) return;

    // --- 1. Inject into HTML Forms ---
    function injectCsrfIntoForms(root) {
        var activeToken = window.CSRF_TOKEN || csrfToken;
        if (!activeToken) return;
        var forms = (root || document).querySelectorAll('form[method="post"], form[method="POST"]');
        forms.forEach(function(form) {
            var existing = form.querySelector('input[name="csrf_token"]');
            if (!existing) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'csrf_token';
                input.value = activeToken;
                form.appendChild(input);
            } else if (!existing.value) {
                existing.value = activeToken;
            }
        });
    }
    window.injectCsrfIntoForms = injectCsrfIntoForms;

    injectCsrfIntoForms(document);

    document.addEventListener('DOMContentLoaded', function() {
        injectCsrfIntoForms(document);
    });

    document.addEventListener('page:loaded', function() {
        injectCsrfIntoForms(document.querySelector('.main-content-container'));
    });

    // --- 2. Patch global fetch() to add X-CSRF-Token on POST ---
    var originalFetch = window.fetch;
    window.fetch = function(resource, options) {
        options = options || {};
        var method = (options.method || 'GET').toUpperCase();
        // Always read the latest token - keeps closure var in sync with window.CSRF_TOKEN
        var activeToken = window.CSRF_TOKEN || csrfToken;
        csrfToken = activeToken; // keep closure variable up-to-date
        if (method === 'POST' && activeToken) {
            // Also append csrf_token to FormData if missing or empty
            if (options.body instanceof FormData) {
                if (!options.body.get('csrf_token')) {
                    options.body.set('csrf_token', activeToken);
                }
            }

            options.headers = options.headers || {};
            // Support both plain objects and Headers instances
            if (options.headers instanceof Headers) {
                if (!options.headers.has('X-CSRF-Token')) {
                    options.headers.set('X-CSRF-Token', activeToken);
                }
            } else {
                if (!options.headers['X-CSRF-Token'] && !options.headers['x-csrf-token']) {
                    options.headers['X-CSRF-Token'] = activeToken;
                }
            }
        }
        return originalFetch.call(this, resource, options);
    };

    // --- 3. Keep csrfToken closure var in sync whenever window.CSRF_TOKEN changes ---
    // This handles PJAX navigation responses that update window.CSRF_TOKEN via response headers
    document.addEventListener('page:loaded', function() {
        if (window.CSRF_TOKEN) {
            csrfToken = window.CSRF_TOKEN;
        }
    });
})();
</script>

<!-- Toast Container -->
<div class="spa-toast-container" id="spaToastContainer"></div>

<!-- Mobile Navigation Toggle Script & Global UI Enhancements -->
<script>
// Global Floating Toast Helper
window.showToast = function(message, type = 'success') {
    const container = document.getElementById('spaToastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `spa-toast toast-${type}`;
    
    let icon = 'check-circle text-success';
    if (type === 'danger') icon = 'exclamation-circle text-danger';
    if (type === 'warning') icon = 'exclamation-triangle text-warning';
    if (type === 'info') icon = 'info-circle text-info';

    toast.innerHTML = `
        <i class="fa fa-${icon} fs-5"></i>
        <div class="flex-grow-1">${message}</div>
        <button type="button" class="btn-close ms-auto small" aria-label="Close" style="font-size: 0.75rem;"></button>
    `;

    container.appendChild(toast);

    toast.querySelector('.btn-close').addEventListener('click', () => {
        toast.classList.add('toast-hiding');
        setTimeout(() => toast.remove(), 250);
    });

    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.add('toast-hiding');
            setTimeout(() => toast.remove(), 250);
        }
    }, 4000);
};

// Global Page Initializer (Tooltips, Sidebar, Mobile Toggle, Flash-to-Toast)
window.initPageComponents = function() {
    // Re-initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Mobile sidebar toggle
    const toggleBtn = document.getElementById('mobileSidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (toggleBtn && sidebar && overlay && !toggleBtn._hasListener) {
        toggleBtn._hasListener = true;
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // Auto-dismiss session flash alerts after 5 seconds
    document.querySelectorAll('.alert.alert-dismissible').forEach(alert => {
        setTimeout(() => {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch(e) {}
        }, 5000);
    });

    // Flash-to-Toast Converter:
    // After PJAX navigation, convert server-rendered flash alerts into floating toasts
    // so they appear non-intrusively without displacing page content.
    if (typeof window.showToast === 'function') {
        const flashMap = {
            'alert-success': 'success',
            'alert-danger':  'danger',
            'alert-warning': 'warning',
            'alert-info':    'info'
        };
        Object.entries(flashMap).forEach(([cls, toastType]) => {
            // Only convert alerts that were rendered by the global flash block (have animate-fade-in-up)
            document.querySelectorAll('.alert.' + cls + '.animate-fade-in-up').forEach(alertEl => {
                const msgEl = alertEl.querySelector('.flex-grow-1') || alertEl;
                const msg = msgEl.innerHTML.trim();
                if (msg) {
                    window.showToast(msg, toastType);
                    // Hide the inline alert silently
                    alertEl.style.display = 'none';
                }
            });
        });
    }
};


// ============================================================================
// SPA DOMContentLoaded Polyfill & Lifecycle Bridge
// Fires synthetic DOMContentLoaded for inline view scripts added by PJAX.
// Only active during the PJAX script-execution window (window._spaScriptExec).
// This prevents third-party libraries loaded before PJAX from double-firing.
// ============================================================================
(function() {
    const originalDocAdd = document.addEventListener;
    document.addEventListener = function(type, listener, options) {
        // Only intercept DOMContentLoaded when DOM is already ready AND
        // we are inside a PJAX script-execution window (guarded flag)
        if (type === 'DOMContentLoaded' &&
            (document.readyState === 'interactive' || document.readyState === 'complete') &&
            window._spaScriptExec === true) {
            setTimeout(function() {
                try {
                    listener.call(document, new Event('DOMContentLoaded'));
                } catch(e) {
                    console.warn('SPA DOMContentLoaded error:', e);
                }
            }, 1);
            return;
        }
        return originalDocAdd.call(this, type, listener, options);
    };

    const originalWinAdd = window.addEventListener;
    window.addEventListener = function(type, listener, options) {
        if (type === 'DOMContentLoaded' &&
            (document.readyState === 'interactive' || document.readyState === 'complete') &&
            window._spaScriptExec === true) {
            setTimeout(function() {
                try {
                    listener.call(window, new Event('DOMContentLoaded'));
                } catch(e) {
                    console.warn('SPA window DOMContentLoaded error:', e);
                }
            }, 1);
            return;
        }
        return originalWinAdd.call(this, type, listener, options);
    };
})();

// ============================================================================
// GLOBAL MODAL PORTAL & STACKING ARCHITECTURE
// Eliminates all backdrop traps, blurred screen locks, and container clipping
// ============================================================================
window.cleanupModalsAndBackdrops = function() {
    // 1. Dispose any active Bootstrap modal instance
    document.querySelectorAll('.modal').forEach(function(m) {
        try {
            const inst = bootstrap.Modal.getInstance(m);
            if (inst) {
                inst.hide();
                inst.dispose();
            }
        } catch(e) {}
    });

    // 2. Remove all lingering backdrops
    document.querySelectorAll('.modal-backdrop').forEach(function(b) {
        b.remove();
    });

    // 3. Reset body overflow & modal state
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');

    // 4. Clean out portal of previous page's modals
    const portal = document.getElementById('erp-global-modal-portal');
    if (portal) {
        portal.innerHTML = '';
    }
};

window.portalAllModals = function() {
    const portal = document.getElementById('erp-global-modal-portal') || document.body;
    const container = document.querySelector('.main-content-container');
    if (!container) return;

    // Move all modals from main container to root portal outside stacking contexts
    const modals = Array.from(container.querySelectorAll('.modal'));
    modals.forEach(function(modal) {
        portal.appendChild(modal);
    });

    // Re-inject CSRF into any forms that were moved
    if (typeof window.injectCsrfIntoForms === 'function') {
        window.injectCsrfIntoForms(portal);
    }
};

if (!window._modalStackingFixRegistered) {
    window._modalStackingFixRegistered = true;

    // Safety fallback: if any modal is opened before portaling, move it to portal immediately
    document.addEventListener('show.bs.modal', function(event) {
        var modal = event.target;
        var portal = document.getElementById('erp-global-modal-portal') || document.body;
        if (modal && modal.classList && modal.classList.contains('modal')) {
            if (modal.parentElement !== portal && modal.parentElement !== document.body) {
                portal.appendChild(modal);
            }
        }
    });

    // Purge lingering backdrops when all modals finish hiding
    document.addEventListener('hidden.bs.modal', function() {
        setTimeout(function() {
            if (!document.querySelector('.modal.show')) {
                document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }
        }, 100);
    });
}

// ============================================================================
// Lightning-Fast Smooth SPA Navigator (PJAX Engine)
// Eliminates page reloads and white flashes across internal school ERP routes
// ============================================================================
(function() {
    const urlRoot = '<?php echo URLROOT; ?>';
    let isNavigating = false;

    function executeScripts(container) {
        const scripts = container.querySelectorAll('script');
        // Set flag so DOMContentLoaded polyfill only fires during PJAX script execution
        window._spaScriptExec = true;
        scripts.forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
            });
            if (!oldScript.src && oldScript.textContent) {
                // Wrap in block scope to prevent "Identifier already declared" errors when re-visiting views
                newScript.textContent = "try { {\n" + oldScript.textContent + "\n} } catch(e) { console.warn('Script notice:', e); }";
            } else {
                newScript.textContent = oldScript.textContent;
            }
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
        // Clear flag after scripts finish executing (use timeout to catch async DOMContentLoaded)
        setTimeout(function() { window._spaScriptExec = false; }, 100);
    }

    function updateActiveSidebarLink(targetPath) {
        document.querySelectorAll('.sidebar .sidebar-nav-link').forEach(link => {
            const href = link.getAttribute('href');
            if (!href) return;
            const linkUrl = new URL(href, window.location.origin);
            
            // Check if current targetPath contains or matches this link
            const isMatch = (targetPath === linkUrl.pathname || targetPath.startsWith(linkUrl.pathname + '/'));
            if (isMatch) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    const prefetchCache = new Map();

    function prefetchUrl(targetUrl) {
        if (!targetUrl || prefetchCache.has(targetUrl)) return;
        const p = fetch(targetUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-PJAX': 'true'
            }
        }).then(r => r.ok ? r.text() : null).catch(() => null);

        prefetchCache.set(targetUrl, p);
        setTimeout(() => prefetchCache.delete(targetUrl), 20000);
    }

    function isEligibleSpaLink(link, targetUrl) {
        if (!link || !targetUrl) return false;
        const href = link.getAttribute('href') || '';
        if (
            link.target === '_blank' ||
            link.hasAttribute('download') ||
            link.hasAttribute('data-no-pjax') ||
            link.hasAttribute('data-bs-toggle') ||
            link.getAttribute('role') === 'tab' ||
            link.classList.contains('no-pjax') ||
            href.startsWith('#') ||
            href.startsWith('javascript:') ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:') ||
            href.includes('/auth/') ||
            href.includes('/certificate/') ||
            href.includes('/fees/challan') ||
            href.includes('/fees/batchChallans') ||
            href.includes('/payroll/slip') ||
            href.includes('/exam/reportCard') ||
            href.includes('/exam/batchReportCards') ||
            href.includes('/clearance/certificate') ||
            href.includes('/clearance/package') ||
            href.includes('/frontoffice/visitorPass') ||
            href.includes('/frontoffice/printGatePass') ||
            href.includes('/frontoffice/printEnquiry') ||
            href.includes('/students/printAdmission') ||
            href.includes('/notice/print')
        ) {
            return false;
        }

        if (targetUrl.origin !== window.location.origin) return false;
        const rootUrlPath = new URL(urlRoot, window.location.origin).pathname.replace(/\/$/, '');
        if (!targetUrl.pathname.startsWith(rootUrlPath)) return false;
        if (targetUrl.pathname === rootUrlPath || targetUrl.pathname === rootUrlPath + '/' || targetUrl.pathname.startsWith(rootUrlPath + '/home')) {
            return false;
        }

        return true;
    }

    window.spaNavigate = async function(url, pushState = true) {
        if (isNavigating) return;
        isNavigating = true;

        const container = document.querySelector('.main-content-container');
        if (container) {
            container.style.opacity = '0.65';
            container.style.transition = 'opacity 0.1s ease-out';
        }

        // Start PJAX progress bar
        document.body.classList.add('pjax-loading');
        document.body.classList.remove('pjax-done');

        // Clean up any open modals from current page before navigating
        window.cleanupModalsAndBackdrops();

        try {
            let html = null;
            if (prefetchCache.has(url)) {
                html = await prefetchCache.get(url);
                prefetchCache.delete(url);
            }

            if (!html) {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-PJAX': 'true'
                    }
                });

                // 1. Check for 401 Unauthorized or Auth Redirect Header
                const authRedirect = response.headers.get('X-Auth-Redirect');
                if (response.status === 401 || authRedirect) {
                    window.location.href = authRedirect || (urlRoot + '/auth/login');
                    return;
                }

                // 2. Check if redirected to auth or login URL
                if (response.url && (response.url.includes('/auth/') || response.url.includes('/auth/login'))) {
                    window.location.href = response.url;
                    return;
                }

                const newCsrf = response.headers.get('X-CSRF-Token');
                if (newCsrf) {
                    window.CSRF_TOKEN = newCsrf;
                    csrfToken = newCsrf;
                }

                html = await response.text();
            }

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // 3. Absolute Safety: Check if returned HTML is an auth/login page
            if (doc.querySelector('.auth-page') || doc.querySelector('.auth-card') || doc.querySelector('#loginEmail') || doc.querySelector('.auth-standalone-body')) {
                window.location.href = urlRoot + '/auth/login';
                return;
            }

            const newContainer = doc.querySelector('.main-content-container');

            if (!newContainer || !container) {
                // Not an ERP page with main-content-container (e.g., printable sheet)
                window.location.href = url;
                return;
            }

            // Update Page Title
            if (doc.title) {
                document.title = doc.title;
            }

            // Update Browser History State
            if (pushState) {
                window.history.pushState({ url: url }, '', url);
            }

            // Clean previous modals
            window.cleanupModalsAndBackdrops();

            // Swap Content Instantly (Zero delay, partial payload)
            container.innerHTML = newContainer.innerHTML;
            container.style.opacity = '1';

            // Update active menu link in sidebar
            const parsedUrl = new URL(url, window.location.origin);
            updateActiveSidebarLink(parsedUrl.pathname);

            // Move new modals into root portal before scripts execute
            window.portalAllModals();

            // Execute scripts in newly swapped container
            executeScripts(container);

            // Re-initialize tooltips and page components
            window.initPageComponents();

            // Smoothly scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Close mobile sidebar if open
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                overlay && overlay.classList.remove('show');
            }

            // Dispatch global event for views that listen for page loaded
            document.dispatchEvent(new CustomEvent('page:loaded', { detail: { url: url } }));

            // Complete progress bar
            document.body.classList.remove('pjax-loading');
            document.body.classList.add('pjax-done');
            setTimeout(function() { document.body.classList.remove('pjax-done'); }, 600);

        } catch (error) {
            console.error('SPA Navigation error, falling back:', error);
            window.location.href = url;
        } finally {
            if (container) container.style.opacity = '1';
            isNavigating = false;
            // Ensure progress bar always resets even on error
            document.body.classList.remove('pjax-loading');
        }
    };


    // Pre-fetch pages on mouse hover / pointer proximity (Instant 0ms click feel)
    document.addEventListener('mouseover', function(e) {
        const link = e.target.closest('a');
        if (!link) return;
        try {
            const targetUrl = new URL(link.href, window.location.origin);
            if (isEligibleSpaLink(link, targetUrl)) {
                prefetchUrl(targetUrl.href);
            }
        } catch(e) {}
    }, { passive: true });

    // Intercept clicks on links
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

        try {
            const targetUrl = new URL(link.href, window.location.origin);
            if (!isEligibleSpaLink(link, targetUrl)) return;

            // Skip if exact same URL with hash
            if (targetUrl.href === window.location.href) {
                e.preventDefault();
                return;
            }

            e.preventDefault();
            // Immediate tactile highlight on sidebar
            updateActiveSidebarLink(targetUrl.pathname);
            window.spaNavigate(targetUrl.href);
        } catch(e) {}
    });

    // ========================================================================
    // Global Form Submission Interceptor (AJAX Forms Everywhere)
    // Eliminates full page reloads on searches, filters, CRUD forms and modals
    // ========================================================================
    document.addEventListener('submit', async function(e) {
        const form = e.target.closest('form');
        if (!form) return;

        // Skip non-SPA forms
        if (
            form.target === '_blank' ||
            form.hasAttribute('data-no-pjax') ||
            form.hasAttribute('data-ajax-form') ||
            form.classList.contains('no-pjax') ||
            form.classList.contains('ajax-form') ||
            form.classList.contains('ajax-settings-form') ||
            form.classList.contains('ajax-profile-form')
        ) {
            return;
        }

        const action = form.getAttribute('action') || window.location.href;
        const actionUrl = new URL(action, window.location.origin);
        if (actionUrl.origin !== window.location.origin) return;

        // Skip excluded routes - NEVER intercept auth forms
        if (
            actionUrl.pathname.includes('/auth/') ||
            actionUrl.pathname.includes('/students/downloadSample') ||
            actionUrl.pathname.includes('/certificate/') ||
            actionUrl.pathname.includes('/students/printAdmission') ||
            actionUrl.pathname.includes('/frontoffice/printEnquiry') ||
            actionUrl.pathname.includes('/fees/challan') ||
            actionUrl.pathname.includes('/payroll/slip')
        ) {
            return;
        }

        e.preventDefault();

        // Capture submitter
        const submitter = e.submitter;
        const formData = new FormData(form);
        if (submitter && submitter.name && !formData.has(submitter.name)) {
            formData.append(submitter.name, submitter.value || '1');
        }

        const method = (form.getAttribute('method') || 'GET').toUpperCase();

        // Button feedback
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        submitButtons.forEach(btn => {
            btn.disabled = true;
            btn.dataset.prevHtml = btn.innerHTML;
            if (btn.tagName === 'BUTTON') {
                btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Processing...';
            }
        });

        try {
            let fetchUrl = actionUrl.href;
            let fetchOptions = {
                method: method,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-PJAX': 'true'
                }
            };

            if (method === 'GET') {
                const params = new URLSearchParams(formData);
                const queryStr = params.toString();
                fetchUrl = actionUrl.origin + actionUrl.pathname + (queryStr ? '?' + queryStr : '');
            } else {
                fetchOptions.body = formData;
            }

            const response = await fetch(fetchUrl, fetchOptions);

            // 1. Handle 401 Unauthorized or redirect to login
            const formAuthRedirect = response.headers.get('X-Auth-Redirect');
            if (response.status === 401 || formAuthRedirect) {
                window.location.href = formAuthRedirect || (urlRoot + '/auth/login');
                return;
            }

            // 2. Handle redirect to auth or login URL
            if (response.url && (response.url.includes('/auth/') || response.url.includes('/auth/login'))) {
                window.location.href = response.url;
                return;
            }

            const contentType = response.headers.get('content-type') || '';

            // Handle JSON response
            if (contentType.includes('application/json')) {
                const json = await response.json();
                if (json.success) {
                    if (typeof window.showToast === 'function') {
                        window.showToast(json.message || 'Saved successfully!', 'success');
                    }
                    const modalEl = form.closest('.modal');
                    if (modalEl) {
                        const inst = bootstrap.Modal.getInstance(modalEl);
                        if (inst) inst.hide();
                    }
                    if (json.redirect_url) {
                        window.spaNavigate(json.redirect_url);
                    } else {
                        window.spaNavigate(window.location.href, false);
                    }
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast(json.message || 'Error occurred.', 'danger');
                    } else {
                        alert(json.message || 'Error occurred.');
                    }
                }
                return;
            }

            // Handle HTML response
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // 3. Absolute Safety: Check if returned HTML is an auth/login page
            if (doc.querySelector('.auth-page') || doc.querySelector('.auth-card') || doc.querySelector('#loginEmail') || doc.querySelector('.auth-standalone-body')) {
                window.location.href = urlRoot + '/auth/login';
                return;
            }

            const newContainer = doc.querySelector('.main-content-container');

            if (!newContainer) {
                window.location.href = response.url || fetchUrl;
                return;
            }

            // Cleanly close any open modal and remove backdrops
            window.cleanupModalsAndBackdrops();

            // Update title
            if (doc.title) {
                document.title = doc.title;
            }

            // Update browser URL
            const finalUrl = response.url || fetchUrl;
            window.history.pushState({ url: finalUrl }, '', finalUrl);

            // Swap Container Instantly
            const container = document.querySelector('.main-content-container');
            if (container) {
                container.innerHTML = newContainer.innerHTML;

                const parsed = new URL(finalUrl, window.location.origin);
                updateActiveSidebarLink(parsed.pathname);

                window.portalAllModals();
                executeScripts(container);
                window.initPageComponents();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                document.dispatchEvent(new CustomEvent('page:loaded', { detail: { url: finalUrl } }));

                // Surface success feedback from query string or flash alerts
                try {
                    const parsedFinal = new URL(finalUrl, window.location.origin);
                    if (parsedFinal.searchParams.has('success') && typeof window.showToast === 'function') {
                        window.showToast('Changes saved successfully.', 'success');
                    } else {
                        const flashSuccess = container.querySelector('.alert-success');
                        if (flashSuccess && typeof window.showToast === 'function') {
                            const msg = flashSuccess.textContent.trim();
                            if (msg) window.showToast(msg, 'success');
                        }
                    }
                } catch (toastErr) {}
            }

        } catch (err) {
            console.error('AJAX Form submission error, falling back:', err);
            form.submit();
        } finally {
            submitButtons.forEach(btn => {
                btn.disabled = false;
                if (btn.dataset.prevHtml) {
                    btn.innerHTML = btn.dataset.prevHtml;
                }
            });
        }
    });

    // Handle Browser Back & Forward buttons
    window.addEventListener('popstate', function(e) {
        const url = (e.state && e.state.url) ? e.state.url : window.location.href;
        window.spaNavigate(url, false);
    });

    // Initialize on initial page load
    document.addEventListener('DOMContentLoaded', function() {
        window.portalAllModals();
        window.initPageComponents();
    });
})();
</script>
</body>
</html>
