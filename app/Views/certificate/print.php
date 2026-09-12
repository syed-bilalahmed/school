<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Certificate</title>
    <style>
        @page { size: landscape; margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Arial', sans-serif; }
        .certificate-container {
            position: relative;
            width: 100%;
            height: 100vh;
            page-break-after: always;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-size: cover;
            background-position: center;
        }
        .content-layer {
            position: relative;
            z-index: 2;
            width: 80%;
            text-align: center;
        }
        .header-row { display: flex; justify-content: space-between; margin-bottom: 20px; font-weight: bold; }
        .footer-row { display: flex; justify-content: space-between; margin-top: 50px; font-weight: bold; }
        .cert-title { font-size: 3rem; margin-bottom: 30px; font-family: 'Times New Roman', serif; color: #333; }
        .cert-body { font-size: 1.5rem; line-height: 2; color: #555; }
        
        <?php if($data['certificate']->background_image): ?>
        .certificate-container {
            background-image: url('<?php echo URLROOT . '/' . $data['certificate']->background_image; ?>');
        }
        <?php else: ?>
        .certificate-container {
            background-color: #f9f9f9;
            border: 20px solid #ddd;
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <?php foreach($data['students'] as $student): ?>
        <?php
            // Replace Placeholders
            $text = $data['certificate']->certificate_text;
            $text = str_replace('[name]', '<b>' . $student->name . '</b>', $text);
            $text = str_replace('[admission_no]', $student->admission_no, $text);
            $text = str_replace('[roll_no]', $student->roll_no, $text);
            $text = str_replace('[class]', $student->class_name . ' (' . $student->section_name . ')', $text);
            $text = str_replace('[dob]', (isset($student->dob) ? $student->dob : ''), $text); // Assuming dob exists
        ?>
        <div class="certificate-container">
            <div class="content-layer">
                <div class="header-row">
                    <div><?php echo $data['certificate']->left_header; ?></div>
                    <div style="font-size: 1.2rem;"><?php echo $data['certificate']->center_header; ?></div>
                    <div><?php echo $data['certificate']->right_header; ?></div>
                </div>

                <div class="cert-title"><?php echo $data['certificate']->certificate_name; ?></div>

                <div class="cert-body">
                    <?php echo nl2br($text); ?>
                </div>

                <div class="footer-row">
                    <div><?php echo $data['certificate']->left_footer; ?></div>
                    <div><?php echo $data['certificate']->center_footer; ?></div>
                    <div><?php echo $data['certificate']->right_footer; ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <script>window.print();</script>
</body>
</html>
