<?php
$c = $data['character'];
$s = $c->student;
$sch = $c->school;
$schoolName = !empty($sch->name) ? $sch->name : 'PAK ACADEMY MODEL HIGH SCHOOL';
$schoolAddress = !empty($sch->address) ? $sch->address : 'Main Educational Complex, Lahore, Pakistan';
$schoolPhone = !empty($sch->phone) ? $sch->phone : '+92 42 35889000';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Character Certificate - <?php echo htmlspecialchars($s->student_name); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
        }
        body {
            background: #f1f5f9;
            color: #0f172a;
            padding: 30px 15px;
        }
        .no-print {
            max-width: 860px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 24px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            font-family: 'Segoe UI', sans-serif;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-family: 'Segoe UI', sans-serif;
        }
        .btn-primary { background: #1e3a8a; color: #fff; }
        .btn-secondary { background: #64748b; color: #fff; }

        /* CERTIFICATE CONTAINER (A4 LANDSCAPE) */
        .char-certificate {
            max-width: 900px;
            margin: 0 auto;
            background: #fffdfa;
            padding: 50px 60px;
            border: 8px double #1e3a8a;
            outline: 2px solid #b45309;
            outline-offset: -16px;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(30, 58, 138, 0.03);
            text-transform: uppercase;
            letter-spacing: 6px;
            pointer-events: none;
            white-space: nowrap;
        }

        .cert-inner {
            position: relative;
            z-index: 1;
        }

        .school-name {
            font-size: 28px;
            font-weight: 900;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }
        .school-sub {
            font-size: 12px;
            color: #475569;
            margin-bottom: 24px;
        }

        .title-box {
            margin: 20px 0 30px 0;
        }
        .title-text {
            font-size: 24px;
            font-weight: 800;
            color: #b45309;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 2px solid #b45309;
            display: inline-block;
            padding-bottom: 4px;
        }

        .cert-serial {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 30px;
        }

        .cert-paragraph {
            font-size: 16px;
            line-height: 2.2;
            color: #1e293b;
            text-align: justify;
            margin-bottom: 40px;
        }
        .cert-paragraph strong {
            color: #0f172a;
            border-bottom: 1px dotted #0f172a;
            padding: 0 4px;
        }

        .sig-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 50px;
            padding: 0 20px;
        }
        .seal-box {
            width: 110px;
            height: 110px;
            border: 2px dashed #b45309;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: #b45309;
            text-transform: uppercase;
            margin: 0 auto;
        }
        .sig-box {
            width: 220px;
            border-top: 1.5px solid #0f172a;
            padding-top: 6px;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            text-align: center;
        }

        @media print {
            body { background: #ffffff; padding: 0; }
            .no-print { display: none !important; }
            .char-certificate {
                box-shadow: none;
                margin: 0;
                width: 100%;
                max-width: 100%;
                padding: 40px 50px;
            }
            @page { size: A4 landscape; margin: 10mm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div>
            <a href="<?php echo URLROOT; ?>/certificate/hub" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Credentials Hub
            </a>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fa fa-print"></i> Print Character Certificate
            </button>
        </div>
    </div>

    <div class="char-certificate">
        <div class="watermark">CHARACTER CERTIFICATE</div>

        <div class="cert-inner">
            <div class="school-name"><?php echo htmlspecialchars($schoolName); ?></div>
            <div class="school-sub"><?php echo htmlspecialchars($schoolAddress); ?> &bull; Phone: <?php echo htmlspecialchars($schoolPhone); ?></div>

            <div class="title-box">
                <span class="title-text">Certificate of Good Moral Character</span>
            </div>

            <div class="cert-serial">
                <span>Certificate No: <strong style="color: #dc2626; font-family: monospace;"><?php echo htmlspecialchars($c->certificate_no); ?></strong></span>
                <span>Date: <strong><?php echo date('d-F-Y', strtotime($c->issue_date)); ?></strong></span>
            </div>

            <div class="cert-paragraph">
                This is to solemnly certify that <strong><?php echo strtoupper(htmlspecialchars($s->student_name)); ?></strong>, son/daughter of <strong><?php echo strtoupper(htmlspecialchars($s->father_name ?: 'N/A')); ?></strong>, residing at <?php echo htmlspecialchars($s->address ?: 'on institution record'); ?>, holding Admission No. <strong>#<?php echo htmlspecialchars($s->admission_no ?: '-'); ?></strong> and Roll No. <strong><?php echo htmlspecialchars($s->roll_no ?: '-'); ?></strong>, has been a regular bonafide student of this institution in Class <strong><?php echo htmlspecialchars($s->class_name . ' (' . ($s->section_name ?: 'General') . ')'); ?></strong>.
                <br><br>
                During his/her tenure at this institution, his/her moral conduct, character, and disposition have been found <strong><?php echo htmlspecialchars($c->conduct); ?></strong>. He/She has exhibited commendable discipline, obedience towards teachers, and healthy sportsmanship. <?php echo htmlspecialchars($c->co_curricular); ?>
                <br><br>
                To the best of our official institutional knowledge, he/she has never taken part in any subversive or indisciplinary activity. We wish him/her every success and divine guidance in all future academic pursuits.
            </div>

            <div class="sig-container">
                <div class="sig-box">
                    Class In-Charge / Counselor<br>
                    <span style="font-size: 11px; font-weight: normal; color: #64748b;">Department of Academics</span>
                </div>
                <div>
                    <div class="seal-box">
                        Institutional<br>Seal
                    </div>
                </div>
                <div class="sig-box">
                    Principal / Headmaster<br>
                    <span style="font-size: 11px; font-weight: normal; color: #64748b;"><?php echo htmlspecialchars($schoolName); ?></span>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
