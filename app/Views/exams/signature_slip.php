<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Candidate Attendance & Signature Sheet</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8fafc;
            font-family: Arial, sans-serif;
            color: #000000;
            padding: 20px;
        }
        .sheet-container {
            max-width: 960px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #cccccc;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            padding: 30px 40px;
        }
        .doc-table {
            border: 1px solid #000000 !important;
            width: 100%;
            border-collapse: collapse;
        }
        .doc-table th, .doc-table td {
            border: 1px solid #000000 !important;
            padding: 6px 10px;
            vertical-align: middle;
        }
        .doc-table th {
            background-color: #f2f2f2 !important;
            color: #000000 !important;
            font-weight: bold;
            font-size: 0.88rem;
            text-transform: uppercase;
        }
        .doc-table td {
            font-size: 0.9rem;
        }
        
        @media print {
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .sheet-container {
                max-width: 100% !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }
            .doc-table th {
                background-color: #e6e6e6 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<?php
$exam = $data['selected_exam'];
$students = $data['students'] ?? [];
$totalStudents = count($students);
$classId = $data['class_id'];
$sectionId = $data['section_id'];

$className = 'N/A';
if(!empty($data['classes'])){
    foreach($data['classes'] as $c){
        if($c->id == $classId) $className = $c->class_name;
    }
}
$sectionName = 'All Sections';
if(!empty($data['sections'])){
    foreach($data['sections'] as $s){
        if($s->id == $sectionId) $sectionName = $s->section_name;
    }
}
?>

<!-- Selection Controls Bar (Screen Only) -->
<div class="container no-print mb-4" style="max-width: 960px;">
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body p-3 bg-white border rounded">
            <form action="<?php echo URLROOT; ?>/exam/signatureSlip" method="get" class="row g-2 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label class="form-label small text-muted mb-1 fw-bold">Select Examination</label>
                    <select name="exam_id" class="form-select form-select-sm" required>
                        <option value="">-- Select Exam --</option>
                        <?php foreach($data['exams'] as $ex): ?>
                            <option value="<?php echo $ex->id; ?>" <?php echo ($data['exam_id'] == $ex->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($ex->name . ' (' . ($ex->session_name ?? '2026-27') . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small text-muted mb-1 fw-bold">Class</label>
                    <select name="class_id" id="sigClassSelect" class="form-select form-select-sm" required onchange="filterSigSections()">
                        <option value="">-- Choose Class --</option>
                        <?php foreach($data['classes'] as $cls): ?>
                            <option value="<?php echo $cls->id; ?>" <?php echo ($classId == $cls->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cls->class_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small text-muted mb-1 fw-bold">Section</label>
                    <select name="section_id" id="sigSectionSelect" class="form-select form-select-sm">
                        <option value="">All Sections</option>
                        <?php foreach($data['sections'] as $sec): ?>
                            <option value="<?php echo $sec->id; ?>" data-class="<?php echo $sec->class_id; ?>" <?php echo ($sectionId == $sec->id) ? 'selected' : ''; ?> class="sig-sec-opt">
                                <?php echo htmlspecialchars($sec->section_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                        <i class="fa fa-filter me-1"></i> Generate
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if($classId): ?>
        <div class="d-flex align-items-center justify-content-between p-3 bg-white rounded shadow-sm border">
            <a href="<?php echo URLROOT; ?>/exam/index" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fa fa-arrow-left me-1"></i> Back to Exams
            </a>
            <button onclick="window.print()" class="btn btn-dark px-4 fw-bold shadow-sm">
                <i class="fa fa-print me-2"></i> Print Signature Sheet
            </button>
        </div>
    <?php endif; ?>
</div>

<?php if(!$classId): ?>
    <div class="sheet-container text-center py-5">
        <i class="fa fa-file-signature fa-3x text-primary opacity-50 mb-3 d-block"></i>
        <h4 class="fw-bold text-dark">Select Class &amp; Examination to View Signature Sheet</h4>
        <p class="text-muted mx-auto" style="max-width: 500px;">
            Please choose an <strong>Examination</strong> and <strong>Class</strong> from the dropdown filters above.
        </p>
    </div>
<?php else: ?>
    <!-- Printable Sheet Container (Simple Word Doc Table Style) -->
    <div class="sheet-container">
        <!-- 1. Institute & Exam Header -->
        <div class="text-center mb-4">
            <h2 class="fw-bold mb-1 text-uppercase text-dark" style="letter-spacing: 0.5px; font-size: 1.75rem;">
                <?php echo defined('SITENAME') ? SITENAME : 'SCHOOL ERP SYSTEM'; ?>
            </h2>
            <div class="fw-bold fs-5 text-dark">
                <?php echo htmlspecialchars($exam->name ?? 'EXAMINATION'); ?> (<?php echo htmlspecialchars($exam->session_name ?? date('Y')); ?>)
            </div>
            <div class="fw-bold text-decoration-underline text-uppercase text-secondary small mt-1" style="letter-spacing: 1px;">
                EXAMINATION CANDIDATE ATTENDANCE &amp; SIGNATURE SHEET
            </div>
        </div>

        <!-- 2. Class Metadata Subheader Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-dark small fw-bold">
            <div>Class: <span class="text-uppercase"><?php echo htmlspecialchars($className); ?></span> (Section: <?php echo htmlspecialchars($sectionName); ?>)</div>
            <div>Total Students: <?php echo $totalStudents; ?> Enrolled</div>
            <div>Date of Exam: ____________________</div>
        </div>

        <!-- 3. Candidate Signature Roster Table -->
        <table class="table doc-table mb-4 align-middle">
            <thead>
                <tr class="text-center">
                    <th style="width: 45px;">Sr #</th>
                    <th style="width: 90px;">Roll No</th>
                    <th style="width: 120px;">Reg / Adm No</th>
                    <th class="text-start">Student Name</th>
                    <th class="text-start">Father Name</th>
                    <th style="width: 280px;">Student Signature</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($students)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No candidates found for this class section.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($students as $idx => $st): ?>
                        <tr style="height: 42px;">
                            <td class="text-center fw-bold"><?php echo $idx + 1; ?></td>
                            <td class="text-center font-monospace fw-bold"><?php echo htmlspecialchars($st->roll_no ?: ($idx + 1)); ?></td>
                            <td class="text-center font-monospace small"><?php echo htmlspecialchars($st->admission_no ?: 'ADM-' . $st->id); ?></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($st->name); ?></td>
                            <td><?php echo htmlspecialchars($st->father_name ?: '-'); ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- 4. Word-Doc Style Invigilator & Controller Signatures Footer -->
        <div class="row pt-4 mt-4 small">
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #000000; width: 85%; margin: 0 auto; padding-top: 6px;">
                    <strong>Invigilator Signature</strong>
                </div>
            </div>
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #000000; width: 85%; margin: 0 auto; padding-top: 6px;">
                    <strong>Class Teacher Signature</strong>
                </div>
            </div>
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #000000; width: 85%; margin: 0 auto; padding-top: 6px;">
                    <strong>Controller of Examinations</strong>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    function filterSigSections(){
        var classId = document.getElementById('sigClassSelect').value;
        var options = document.getElementsByClassName('sig-sec-opt');
        for(var i=0; i<options.length; i++){
            if(!classId || options[i].getAttribute('data-class') == classId){
                options[i].style.display = '';
            } else {
                options[i].style.display = 'none';
            }
        }
    }
    document.addEventListener('DOMContentLoaded', function(){
        if(document.getElementById('sigClassSelect') && document.getElementById('sigClassSelect').value) {
            filterSigSections();
        }
    });
</script>

</body>
</html>
