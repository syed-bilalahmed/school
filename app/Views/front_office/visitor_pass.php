<?php
$visitor = $data['visitor'];
$settings = $data['settings'] ?? (object)[];
$siteSettings = class_exists('SiteSetting') ? SiteSetting::getGlobalSettings() : [];

$schoolName = !empty($settings->school_name) ? $settings->school_name : (!empty($siteSettings['school_name']) ? $siteSettings['school_name'] : SITENAME);
$campusTitle = !empty($settings->campus_name) ? $settings->campus_name : (!empty($siteSettings['campus_name']) ? $siteSettings['campus_name'] : 'CAMPUS SECURITY & RECEPTION PASS');
$schoolPhone = !empty($settings->school_phone) ? $settings->school_phone : (!empty($siteSettings['school_phone']) ? $siteSettings['school_phone'] : '');
$schoolAddress = !empty($settings->school_address) ? $settings->school_address : (!empty($siteSettings['school_address']) ? $siteSettings['school_address'] : '');
$schoolEmail = !empty($settings->school_email) ? $settings->school_email : (!empty($siteSettings['school_email']) ? $siteSettings['school_email'] : '');
$schoolLogo = !empty($settings->logo) ? URLROOT . '/' . $settings->logo : (!empty($siteSettings['logo']) ? URLROOT . '/' . $siteSettings['logo'] : '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Gate Pass - <?php echo htmlspecialchars($visitor->pass_no ?: ('VP-' . $visitor->id)); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
        }
        .pass-container {
            max-width: 640px;
            margin: 25px auto;
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }
        .pass-header {
            background: #0f172a;
            color: #ffffff;
            padding: 18px 24px;
            text-align: center;
            border-bottom: 3px solid #eab308;
        }
        .school-logo-img {
            max-height: 48px;
            max-width: 140px;
            object-fit: contain;
        }
        .badge-type {
            background: #eab308;
            color: #000;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 20px;
            display: inline-block;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .pass-body {
            padding: 24px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 18px;
        }
        .info-item .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            display: block;
        }
        .info-item .value {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }
        .notes-card {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }
        .tear-off-stub {
            border-top: 2px dashed #94a3b8;
            padding-top: 16px;
            background: #f8fafc;
            padding: 16px 24px;
        }
        @media print {
            body {
                background: none;
                margin: 0;
            }
            .pass-container {
                margin: 0 auto;
                border: 2px solid #000;
                box-shadow: none;
                break-inside: avoid;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container text-center my-3 no-print">
    <div class="d-inline-flex flex-wrap gap-2 justify-content-center">
        <button onclick="window.print()" class="btn btn-primary px-4 shadow-sm fw-bold">
            <i class="fa fa-print me-2"></i> Print / Save as PDF
        </button>
        <a href="<?php echo URLROOT; ?>/frontoffice/visitor" class="btn btn-outline-secondary px-3">
            <i class="fa fa-arrow-left me-1"></i> Visitor Register
        </a>
        <button onclick="window.close()" class="btn btn-light border px-3">
            <i class="fa fa-times me-1"></i> Close
        </button>
    </div>
</div>

<div class="pass-container">
    <!-- Header with Dynamic School Branding -->
    <div class="pass-header">
        <div class="d-flex align-items-center justify-content-center gap-3 mb-1">
            <?php if(!empty($schoolLogo)): ?>
                <img src="<?php echo $schoolLogo; ?>" alt="School Crest" class="school-logo-img bg-white rounded p-1">
            <?php else: ?>
                <i class="fa fa-school fa-2x text-warning"></i>
            <?php endif; ?>
            <div class="text-start">
                <h5 class="fw-bold mb-0 text-white"><?php echo htmlspecialchars($schoolName); ?></h5>
                <div class="small text-white-50" style="font-size: 0.78rem;">
                    <?php echo htmlspecialchars($campusTitle); ?>
                    <?php if(!empty($schoolPhone)): ?> &bull; Tel: <?php echo htmlspecialchars($schoolPhone); ?><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="mt-2">
            <div class="badge-type">OFFICIAL VISITOR ENTRY BADGE</div>
        </div>
    </div>

    <!-- Pass Body -->
    <div class="pass-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <span class="text-muted small">Pass Serial No:</span>
                <span class="fw-bold font-monospace text-primary fs-6 ms-1"><?php echo htmlspecialchars($visitor->pass_no ?: ('VP-' . $visitor->id)); ?></span>
            </div>
            <div>
                <span class="text-muted small">Date:</span>
                <strong class="ms-1 text-dark"><?php echo date('d-M-Y', strtotime($visitor->date)); ?></strong>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="label">Visitor Name</span>
                <span class="value"><?php echo htmlspecialchars($visitor->name); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Contact / Mobile</span>
                <span class="value"><?php echo htmlspecialchars($visitor->contact ?: 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="label">CNIC / ID Proof</span>
                <span class="value font-monospace text-primary"><?php echo htmlspecialchars($visitor->cnic_passport ?: 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Vehicle Registration</span>
                <span class="value"><?php echo htmlspecialchars($visitor->vehicle_no ?: 'On Foot / Walk-in'); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Person to Meet (Host)</span>
                <span class="value text-danger"><?php echo htmlspecialchars($visitor->person_to_meet ?: 'Principal Office'); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Department &amp; Purpose</span>
                <span class="value"><?php echo htmlspecialchars($visitor->purpose); ?> (<?php echo htmlspecialchars($visitor->department ?: 'Admin'); ?>)</span>
            </div>
            <div class="info-item">
                <span class="label">Time of Entry (In)</span>
                <span class="value text-success"><?php echo htmlspecialchars($visitor->in_time); ?></span>
            </div>
            <div class="info-item">
                <span class="label">No. of Persons</span>
                <span class="value"><?php echo htmlspecialchars($visitor->no_of_person ?: 1); ?></span>
            </div>
        </div>

        <!-- Security Notes / Remarks Display -->
        <?php if(!empty($visitor->note)): ?>
            <div class="notes-card">
                <div class="fw-bold small text-dark mb-1">
                    <i class="fa fa-sticky-note text-warning me-1"></i> Security Notes &amp; Special Remarks:
                </div>
                <div class="text-secondary small">
                    <?php echo nl2br(htmlspecialchars($visitor->note)); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Campus Safety Rules -->
        <div class="alert alert-light border small py-2 px-3 mb-3 text-muted" style="font-size: 0.76rem;">
            <strong>Campus Security Protocol:</strong>
            <ul class="mb-0 ps-3 mt-1">
                <li>This badge must remain visibly worn on campus premises at all times.</li>
                <li>Entry into classrooms, exam halls, or labs during teaching hours is strictly prohibited without principal permit.</li>
                <li>Please obtain host sign below and surrender this pass to gate security at departure.</li>
            </ul>
        </div>

        <div class="row pt-2 text-center">
            <div class="col-6">
                <div style="height: 38px; border-bottom: 1px solid #94a3b8;"></div>
                <small class="text-muted fw-bold" style="font-size: 0.72rem;">Host / Officer Visited Signature</small>
            </div>
            <div class="col-6">
                <div style="height: 38px; border-bottom: 1px solid #94a3b8;"></div>
                <small class="text-muted fw-bold" style="font-size: 0.72rem;">Reception Issuing Officer</small>
            </div>
        </div>
    </div>

    <!-- Guard Exit Stub -->
    <div class="tear-off-stub">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-dark text-white text-uppercase" style="font-size: 0.7rem;">Gate Security Exit Stub (Tear Off)</span>
            <small class="text-muted font-monospace"><?php echo htmlspecialchars($visitor->pass_no ?: ('VP-' . $visitor->id)); ?></small>
        </div>
        <div class="row align-items-center small">
            <div class="col-6">
                <div>Visitor: <strong><?php echo htmlspecialchars($visitor->name); ?></strong></div>
                <div class="text-muted fs-xs">In Time: <?php echo htmlspecialchars($visitor->in_time); ?></div>
                <div class="text-muted fs-xs">Host: <?php echo htmlspecialchars($visitor->person_to_meet ?: 'Office'); ?></div>
            </div>
            <div class="col-6 text-end">
                <div class="d-inline-block text-start">
                    <div style="width: 150px; border-bottom: 1px dashed #000; padding-bottom: 2px; font-size: 0.75rem;">
                        Out Time: <?php echo !empty($visitor->out_time) ? htmlspecialchars($visitor->out_time) : '_________'; ?>
                    </div>
                    <div style="width: 150px; border-bottom: 1px dashed #000; margin-top: 10px; padding-bottom: 2px; font-size: 0.75rem;">
                        Guard Sign: _________
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
