<?php
$pass = $data['pass'];
$schoolName = "PAKISTAN HIGHER SECONDARY SCHOOL";
$campusTitle = "CAMPUS RECEPTION & SECURITY GATE PASS";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Gate Pass - <?php echo htmlspecialchars($pass->pass_no); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #0f172a;
        }
        .a4-page {
            max-width: 800px;
            margin: 20px auto;
            background: #ffffff;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 8px;
        }
        .slip-block {
            border: 2px solid #334155;
            border-radius: 8px;
            padding: 16px 20px;
            background: #ffffff;
            position: relative;
        }
        .slip-badge {
            position: absolute;
            top: 12px;
            right: 16px;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cut-line {
            text-align: center;
            margin: 20px 0;
            position: relative;
            font-size: 12px;
            color: #64748b;
            font-weight: bold;
        }
        .cut-line::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 2px dashed #94a3b8;
            z-index: 1;
        }
        .cut-line span {
            background: #ffffff;
            padding: 0 14px;
            position: relative;
            z-index: 2;
        }
        .field-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
        }
        .field-val {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .table-data td {
            padding: 4px 8px;
            border: none;
        }
        @media print {
            body {
                background: none;
                margin: 0;
            }
            .a4-page {
                margin: 0;
                padding: 10px;
                box-shadow: none;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            .slip-block {
                border: 2px solid #000;
            }
        }
    </style>
</head>
<body>

<div class="container text-center my-3 no-print">
    <div class="d-inline-flex gap-2">
        <button onclick="window.print()" class="btn btn-warning text-dark px-4 shadow-sm fw-bold">
            <i class="fa fa-print me-2"></i> Print Dual-Copy Gate Pass
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-3">
            <i class="fa fa-times me-1"></i> Close
        </button>
    </div>
</div>

<div class="a4-page">

    <?php
    // We will render 2 copies: 1. Office / Reception Copy, 2. Security Gate Checkpoint Copy
    $copies = [
        ['title' => 'OFFICE / RECEPTION RECORD COPY', 'bg' => 'bg-primary text-white'],
        ['title' => 'MAIN GATE SECURITY CHECKPOINT COPY', 'bg' => 'bg-danger text-white']
    ];

    foreach($copies as $index => $c):
    ?>

    <div class="slip-block mb-3">
        <span class="slip-badge <?php echo $c['bg']; ?>"><?php echo $c['title']; ?></span>

        <!-- Header -->
        <div class="d-flex align-items-center gap-3 mb-2">
            <div class="p-2 bg-warning-subtle text-dark rounded border">
                <i class="fa fa-school fa-2x"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark"><?php echo $schoolName; ?></h5>
                <p class="small text-muted mb-0">Student Early Departure &amp; Child Protection Authorization Slip</p>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
            <div>
                <span class="field-label">Gate Pass Serial:</span>
                <span class="font-monospace fw-bold text-danger fs-6 ms-1"><?php echo htmlspecialchars($pass->pass_no); ?></span>
            </div>
            <div>
                <span class="field-label">Date:</span>
                <span class="fw-bold text-dark ms-1"><?php echo date('d-M-Y', strtotime($pass->pass_date)); ?></span>
                <span class="ms-3 field-label">Departure Time:</span>
                <span class="fw-bold text-dark ms-1"><?php echo htmlspecialchars($pass->leave_time); ?></span>
            </div>
        </div>

        <!-- Student & Leave Info -->
        <table class="table table-sm table-borderless mb-2 small table-data">
            <tr>
                <td style="width: 20%;" class="field-label">Student Name:</td>
                <td style="width: 30%;" class="field-val text-primary"><?php echo htmlspecialchars($pass->student_name); ?></td>
                <td style="width: 20%;" class="field-label">Father's Name:</td>
                <td style="width: 30%;" class="field-val"><?php echo htmlspecialchars($pass->father_name ?: 'N/A'); ?></td>
            </tr>
            <tr>
                <td class="field-label">Admission No:</td>
                <td class="field-val font-monospace"><?php echo htmlspecialchars($pass->admission_no); ?></td>
                <td class="field-label">Class &amp; Section:</td>
                <td class="field-val"><?php echo htmlspecialchars($pass->class_name ?? ''); ?> (<?php echo htmlspecialchars($pass->section_name ?? 'A'); ?>) &bull; Roll: <?php echo htmlspecialchars($pass->roll_no ?: 'None'); ?></td>
            </tr>
            <tr>
                <td class="field-label">Reason Category:</td>
                <td class="field-val text-danger fw-bold"><?php echo htmlspecialchars($pass->reason_type); ?></td>
                <td class="field-label">Reason Notes:</td>
                <td class="field-val text-muted"><?php echo htmlspecialchars($pass->reason_details ?: 'None specified'); ?></td>
            </tr>
            <tr style="background: #f8fafc; border-top: 1px dashed #cbd5e1; border-bottom: 1px dashed #cbd5e1;">
                <td class="field-label py-2">Collected By (Person):</td>
                <td class="field-val py-2 text-dark"><?php echo htmlspecialchars($pass->collected_by_name); ?> (<?php echo htmlspecialchars($pass->collected_by_relation); ?>)</td>
                <td class="field-label py-2">Collector CNIC / Phone:</td>
                <td class="field-val py-2 font-monospace"><?php echo htmlspecialchars($pass->collected_by_cnic ?: 'CNIC Verified'); ?> &bull; <?php echo htmlspecialchars($pass->collected_by_phone ?: ''); ?></td>
            </tr>
        </table>

        <!-- Signatures Deck -->
        <div class="row pt-3 text-center small mt-2">
            <div class="col-3">
                <div style="height: 28px; border-bottom: 1px solid #475569;"></div>
                <span class="text-muted fs-xs fw-bold">Class Teacher</span>
            </div>
            <div class="col-3">
                <div style="height: 28px; border-bottom: 1px solid #475569;"></div>
                <span class="text-muted fs-xs fw-bold">Authorized Collector Sign</span>
            </div>
            <div class="col-3">
                <div style="height: 28px; border-bottom: 1px solid #475569;"></div>
                <span class="text-muted fs-xs fw-bold">Principal / In-Charge</span>
            </div>
            <div class="col-3">
                <div style="height: 28px; border-bottom: 1px solid #475569;"></div>
                <span class="text-muted fs-xs fw-bold">Gate Guard Clearance</span>
            </div>
        </div>
    </div>

    <?php if($index == 0): ?>
        <div class="cut-line">
            <span><i class="fa fa-cut me-1"></i> CUT HERE &bull; DETACH FOR MAIN SECURITY GATE CHECKPOINT <i class="fa fa-cut ms-1"></i></span>
        </div>
    <?php endif; ?>

    <?php endforeach; ?>

</div>

</body>
</html>
