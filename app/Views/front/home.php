<?php require APPROOT . '/Views/layouts/header.php'; ?>

<div style="text-align: center; padding: 50px 20px; background: #e9ecef; border-radius: 5px;">
    <h1 style="font-size: 3em;"><?php echo $data['settings']['hero_title'] ?? 'Welcome'; ?></h1>
    <p style="font-size: 1.5em; color: #555;"><?php echo $data['settings']['hero_description'] ?? 'School Management System'; ?></p>
</div>

<div style="margin-top: 40px; display: flex; justify-content: space-around; flex-wrap: wrap;">
    <div style="flex: 1; padding: 20px; min-width: 300px;">
        <h3>About Our School</h3>
        <p><strong>Name:</strong> <?php echo $data['settings']['school_name'] ?? 'School Name'; ?></p>
        <p><strong>Address:</strong> <?php echo $data['settings']['school_address'] ?? 'Address'; ?></p>
        <p><strong>Contact:</strong> <a href="mailto:<?php echo $data['settings']['contact_email'] ?? ''; ?>"><?php echo $data['settings']['contact_email'] ?? 'Email'; ?></a></p>
    </div>
    
    <div style="flex: 1; padding: 20px; min-width: 300px;">
        <h3>Student Access</h3>
        <p>Students can login to check results, attendance, and more.</p>
        <a href="<?php echo URLROOT; ?>/auth/login" class="btn">Login to Portal</a>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
