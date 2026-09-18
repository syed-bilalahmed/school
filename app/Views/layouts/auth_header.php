<?php
// app/Views/layouts/auth_header.php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$globalSiteSettings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];
$dynamicSchoolName = !empty($globalSiteSettings['school_name']) ? $globalSiteSettings['school_name'] : SITENAME;
$dynamicCampusName = !empty($globalSiteSettings['campus_name']) ? $globalSiteSettings['campus_name'] : 'Main Campus';
$dynamicSchoolLogo = !empty($globalSiteSettings['logo']) ? $globalSiteSettings['logo'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Dynamic Favicon -->
    <?php if(!empty($dynamicSchoolLogo)): ?>
        <link rel="icon" type="image/png" href="<?php echo URLROOT . '/' . htmlspecialchars($dynamicSchoolLogo); ?>">
        <link rel="shortcut icon" href="<?php echo URLROOT . '/' . htmlspecialchars($dynamicSchoolLogo); ?>">
        <link rel="apple-touch-icon" href="<?php echo URLROOT . '/' . htmlspecialchars($dynamicSchoolLogo); ?>">
    <?php else: ?>
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
    <?php endif; ?>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Application CSS -->
    <link rel="stylesheet" href="<?php echo URLROOT; ?>/css/style.css?v=2.2.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
</head>
<body class="auth-standalone-body">
