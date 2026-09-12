<?php require APPROOT . '/Views/layouts/auth_header.php'; ?>
<style>
    .auth-page { min-height: 100vh; display: grid; place-items: center; padding: 40px 16px; background: linear-gradient(135deg, #f8fbff 0%, #eef4ff 100%); }
    .auth-shell { width: min(100%, 450px); }
    .auth-card { border: 0; border-radius: 22px; overflow: hidden; box-shadow: 0 22px 60px rgba(15, 23, 42, .12); }
    .auth-brand { background: linear-gradient(135deg, #0f3d91, #1769e0); color: #fff; padding: 30px; }
    .auth-brand-icon { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 14px; background: rgba(255,255,255,.16); font-size: 1.3rem; }
    .auth-body { padding: 30px; }
    .auth-body .form-control { min-height: 48px; border-radius: 11px; border-color: #dbe3ef; }
    .auth-body .form-control:focus { border-color: #1769e0; box-shadow: 0 0 0 4px rgba(23, 105, 224, .12); }
</style>
<div class="auth-page">
    <div class="auth-shell">
        <div class="auth-card bg-white">
            <div class="auth-brand">
                <div class="auth-brand-icon mb-3"><i class="fa fa-shield-halved"></i></div>
                <h2 class="h4 fw-bold mb-2">Choose a new password</h2>
                <p class="mb-0 text-white-50">Use at least six characters for your new account password.</p>
            </div>
            <div class="auth-body">
                <?php if(!empty($data['error'])): ?><div class="alert alert-danger small"><?php echo htmlspecialchars($data['error']); ?></div><?php endif; ?>
                <?php if(!empty($data['success'])): ?>
                    <div class="alert alert-success small"><?php echo htmlspecialchars($data['success']); ?></div>
                    <a href="<?php echo URLROOT; ?>/auth/login" class="btn btn-primary w-100 py-2 fw-bold">Continue to Sign In</a>
                <?php elseif(!empty($data['valid']) && empty($data['pin_verified'])): ?>
                    <p class="text-muted small">For your security, enter the six-digit PIN sent to your email before choosing a new password.</p>
                    <form action="<?php echo URLROOT; ?>/auth/resetPassword/<?php echo urlencode($data['token']); ?>" method="post" class="no-pjax" data-no-pjax="true">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        <label class="form-label fw-semibold" for="resetPin">Email PIN</label>
                        <input id="resetPin" type="text" name="pin" class="form-control mb-3 text-center fw-bold" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="000000" required autofocus>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold"><i class="fa fa-shield-halved me-2"></i>Verify PIN</button>
                    </form>
                <?php elseif(!empty($data['valid']) && !empty($data['pin_verified'])): ?>
                    <form action="<?php echo URLROOT; ?>/auth/resetPassword/<?php echo urlencode($data['token']); ?>" method="post" class="no-pjax" data-no-pjax="true">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        <label class="form-label fw-semibold" for="newPassword">New password</label>
                        <input id="newPassword" type="password" name="password" class="form-control mb-3" minlength="6" required autofocus>
                        <label class="form-label fw-semibold" for="confirmPassword">Confirm password</label>
                        <input id="confirmPassword" type="password" name="password_confirmation" class="form-control mb-3" minlength="6" required>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold"><i class="fa fa-lock me-2"></i>Update Password</button>
                    </form>
                <?php else: ?>
                    <div class="text-center text-muted py-2"><i class="fa fa-link-slash fs-2 mb-3 d-block"></i>This reset link is invalid or has expired.</div>
                    <a href="<?php echo URLROOT; ?>/auth/forgotPassword" class="btn btn-outline-primary w-100">Request a New Link</a>
                <?php endif; ?>
                <a href="<?php echo URLROOT; ?>/auth/login" class="d-block text-center mt-4 text-decoration-none"><i class="fa fa-arrow-left me-1"></i> Back to sign in</a>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/auth_footer.php'; ?>
