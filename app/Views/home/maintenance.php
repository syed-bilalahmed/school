<?php
$theme = $data['settings']->theme_color ?? 'default';
$primary = '#0d6efd';

if($theme === 'red'){
    $primary = '#d63031';
} elseif($theme === 'green'){
    $primary = '#00b894';
} elseif($theme === 'dark'){
    $primary = '#2d3436';
}

$schoolName = $data['school']->school_name ?? 'Smart School';
$email = $data['school']->email ?? '';
$phone = $data['school']->phone ?? '';
$address = $data['school']->address ?? '';
$maintenanceTitle = $data['cms']->maintenance_title ?? ('We are updating ' . $schoolName . '.');
$maintenanceMessage = $data['cms']->maintenance_message ?? 'The public website is temporarily unavailable while maintenance is in progress. Please check back shortly. Internal staff can still sign in from the admin login.';
$maintenanceEta = $data['cms']->maintenance_eta ?? '';
$maintenanceBackground = $data['cms']->maintenance_background ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - <?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --brand: <?php echo $primary; ?>;
            --ink: #1f2937;
            --muted: #6b7280;
            --surface: rgba(255, 255, 255, 0.94);
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(13, 110, 253, 0.16), transparent 35%),
                radial-gradient(circle at bottom right, rgba(17, 24, 39, 0.14), transparent 30%),
                linear-gradient(135deg, #f4f7fb 0%, #eef2f7 100%);
        }

            body.has-maintenance-bg {
                background:
                linear-gradient(rgba(15, 23, 42, 0.54), rgba(15, 23, 42, 0.54)),
                url('<?php echo URLROOT . '/' . $maintenanceBackground; ?>') center/cover no-repeat fixed;
            }

        .maintenance-shell {
            width: min(920px, 100%);
            background: var(--surface);
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 28px;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
            overflow: hidden;
        }

        .accent-bar {
            height: 8px;
            background: linear-gradient(90deg, var(--brand), #8fb8ff);
        }

        .maintenance-body {
            padding: 48px;
        }

        .brand-mark {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(13, 110, 253, 0.1);
            color: var(--brand);
            font-size: 30px;
        }

        .headline {
            font-size: clamp(2rem, 4vw, 3.4rem);
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 20px 0 16px;
        }

        .subtext {
            max-width: 640px;
            font-size: 1.05rem;
            color: var(--muted);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(13, 110, 253, 0.1);
            color: var(--brand);
            font-size: 0.92rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 32px;
        }

        .info-card {
            padding: 18px 20px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.06);
        }

        .info-card small {
            display: block;
            margin-bottom: 8px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
        }

        .actions {
            margin-top: 32px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn-brand {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .btn-brand:hover {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            opacity: 0.92;
        }

        .footer-note {
            margin-top: 28px;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .eta-chip {
            margin-top: 18px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 12px;
            background: rgba(13, 110, 253, 0.08);
            color: var(--brand);
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .maintenance-body {
                padding: 28px 22px;
            }
        }
    </style>
</head>
<body class="<?php echo !empty($maintenanceBackground) ? 'has-maintenance-bg' : ''; ?>">
    <div class="maintenance-shell">
        <div class="accent-bar"></div>
        <div class="maintenance-body">
            <div class="status-pill">
                <i class="fa fa-tools"></i>
                Maintenance Mode
            </div>

            <div class="mt-4">
                <?php if(!empty($data['cms']->logo ?? '')): ?>
                    <img src="<?php echo URLROOT . '/' . $data['cms']->logo; ?>" alt="<?php echo htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8'); ?>" style="max-height: 54px;">
                <?php else: ?>
                    <div class="brand-mark">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                <?php endif; ?>
            </div>

            <h1 class="headline"><?php echo htmlspecialchars($maintenanceTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="subtext"><?php echo htmlspecialchars($maintenanceMessage, ENT_QUOTES, 'UTF-8'); ?></p>

            <?php if($maintenanceEta !== ''): ?>
                <div class="eta-chip">
                    <i class="fa fa-clock"></i>
                    Estimated return: <?php echo htmlspecialchars($maintenanceEta, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="info-grid">
                <div class="info-card">
                    <small>Status</small>
                    System improvements in progress
                </div>
                <?php if($email !== ''): ?>
                    <div class="info-card">
                        <small>Email</small>
                        <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
                <?php if($phone !== ''): ?>
                    <div class="info-card">
                        <small>Phone</small>
                        <?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
                <?php if($address !== ''): ?>
                    <div class="info-card">
                        <small>Address</small>
                        <?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="actions">
                <a href="<?php echo URLROOT; ?>/auth/login" class="btn btn-brand btn-lg px-4">Admin Login</a>
                <a href="<?php echo URLROOT; ?>" class="btn btn-outline-secondary btn-lg px-4">Retry</a>
            </div>

            <div class="footer-note">
                <?php echo htmlspecialchars($data['cms']->footer_text ?? ('Copyright ' . date('Y') . ' ' . $schoolName), ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
    </div>
</body>
</html>