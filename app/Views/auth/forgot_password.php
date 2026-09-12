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
                <div class="auth-brand-icon mb-3"><i class="fa fa-key"></i></div>
                <h2 class="h4 fw-bold mb-2">Reset your password</h2>
                <p class="mb-0 text-white-50">Enter your account email and we will prepare a secure reset link.</p>
            </div>
            <div class="auth-body">
                <?php if(!empty($data['error'])): ?><div class="alert alert-danger small"><?php echo htmlspecialchars($data['error']); ?></div><?php endif; ?>
                <?php if(!empty($data['success'])): ?><div class="alert alert-success small"><?php echo htmlspecialchars($data['success']); ?></div><?php endif; ?>
                <form action="<?php echo URLROOT; ?>/auth/forgotPassword" method="post" class="no-pjax" data-no-pjax="true">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <label class="form-label fw-semibold" for="resetEmail">Email address</label>
                    <input id="resetEmail" type="email" name="email" class="form-control mb-3" value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>" placeholder="name@school.com" required autofocus>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold"><i class="fa fa-paper-plane me-2"></i>Send Reset Link</button>
                </form>
                <a href="<?php echo URLROOT; ?>/auth/login" class="d-block text-center mt-4 text-decoration-none"><i class="fa fa-arrow-left me-1"></i> Back to sign in</a>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/auth_footer.php'; ?>
