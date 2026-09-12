<?php require APPROOT . '/Views/layouts/header.php'; ?>
<style>
    .exam-directory-table {
        table-layout: fixed;
        width: 100%;
    }

    .exam-directory-table th:nth-child(1),
    .exam-directory-table th:nth-child(1),
    .exam-directory-table td:nth-child(1) { width: 18%; }
    .exam-directory-table th:nth-child(2),
    .exam-directory-table th:nth-child(2),
    .exam-directory-table td:nth-child(2) { width: 18%; }
    .exam-directory-table th:nth-child(3),
    .exam-directory-table th:nth-child(3),
    .exam-directory-table td:nth-child(3) { width: 16%; }
    .exam-directory-table th:nth-child(4),
    .exam-directory-table th:nth-child(4),
    .exam-directory-table td:nth-child(4) { width: 14%; }
    .exam-directory-table th:nth-child(5),
    .exam-directory-table th:nth-child(5),
    .exam-directory-table td:nth-child(5) { width: 34%; }

    .exam-directory-table td,
    .exam-directory-table th {
        overflow-wrap: anywhere;
        text-align: left !important;
    }

    .exam-action-list {
        display: grid;
        grid-template-columns: repeat(3, max-content);
        justify-content: start;
        gap: 4px;
    }

    .exam-action-list .btn {
        min-width: 0;
        padding-left: 6px !important;
        padding-right: 6px !important;
        font-size: 0.72rem;
        white-space: nowrap;
    }

    .exam-action-list .action-label {
        display: inline;
    }

    .exam-create-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        align-items: end;
    }

    .exam-create-grid .form-label {
        white-space: nowrap;
    }

    .exam-create-grid > div:nth-child(6) {
        grid-column: span 2;
    }

    @media (max-width: 767.98px) {
        .exam-create-grid {
            grid-template-columns: 1fr;
        }

        .exam-create-grid > div:nth-child(6) {
            grid-column: auto;
        }

    }

    @media (max-width: 767.98px) {
        .exam-directory-table,
        .exam-directory-table tbody,
        .exam-directory-table tr,
        .exam-directory-table td {
            display: block;
            width: 100% !important;
        }

        .exam-directory-table thead {
            display: none;
        }

        .exam-directory-table tr {
            padding: 14px 16px;
            border-bottom: 1px solid #e9ecef;
        }

        .exam-directory-table tr:last-child {
            border-bottom: 0;
        }

        .exam-directory-table td {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 7px 0 !important;
            border: 0;
            text-align: right !important;
        }

        .exam-directory-table td::before {
            flex: 0 0 34%;
            color: #6c757d;
            content: attr(data-label);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-align: left;
            text-transform: uppercase;
        }

        .exam-directory-table td:first-child {
            display: block;
            padding-top: 0 !important;
            text-align: left !important;
        }

        .exam-directory-table td:first-child::before {
            display: block;
            margin-bottom: 4px;
        }

        .exam-directory-table td:last-child {
            display: block;
            text-align: left !important;
        }

        .exam-directory-table td:last-child::before {
            display: block;
            margin-bottom: 7px;
        }

        .exam-action-list {
            justify-content: flex-start;
        }
    }
</style>
<?php
$currentSession = $data['current_session'];
$sessions = $data['sessions'];
$exams = $data['exams'];
?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Examinations Management</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">Examinations &amp; Academic Assessments</h2>
        <p class="text-muted mb-0 small">Schedule term exams, configure theory/practical weightage, generate hall signature sheets, and monitor the 4-tier approval chain.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/exam/signatureSlip" class="btn btn-outline-warning text-dark btn-sm px-3 shadow-xs">
            <i class="fa fa-file-signature me-1"></i> Signature Slips
        </a>
        <a href="<?php echo URLROOT; ?>/certificate/hub" class="btn btn-outline-danger btn-sm px-3 shadow-xs">
            <i class="fa fa-id-card me-1"></i> Roll No Slips Hub
        </a>
        <a href="<?php echo URLROOT; ?>/exam/marks" class="btn btn-outline-success btn-sm px-3 shadow-xs">
            <i class="fa fa-marker me-1"></i> Marks Entry
        </a>
        <a href="<?php echo URLROOT; ?>/exam/approval" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
            <i class="fa fa-stamp me-1"></i> 4-Tier Approval Chain
        </a>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div>Examination created and wired to academic session successfully!</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="mb-4">
    <!-- Create New Examination -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center gap-2">
            <div class="p-2 bg-primary-subtle text-primary rounded-3"><i class="fa fa-calendar-plus"></i></div>
            <h5 class="fw-bold mb-0 text-dark">Create New Examination</h5>
        </div>
        <div class="card-body p-3">
            <form action="<?php echo URLROOT; ?>/exam/index" method="post">
                <div class="exam-create-grid">
                    <div>
                        <label class="form-label small fw-bold text-dark">Academic Session <span class="text-danger">*</span></label>
                        <select name="academic_session_id" class="form-select" required>
                            <?php foreach($sessions as $sess): ?>
                                <option value="<?php echo $sess->id; ?>" <?php echo (!empty($currentSession) && $currentSession->id == $sess->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($sess->session_name); ?> <?php echo $sess->is_current ? '(Active)' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-dark">Examination Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Mid Term Examination 2026" required>
                    </div>
                    <div>
                        <label class="form-label small fw-bold text-dark">Assessment Type <span class="text-danger">*</span></label>
                        <select name="exam_type" class="form-select" required>
                            <option value="First Term Exam">First Term Exam</option>
                            <option value="Mid Term Exam" selected>Mid Term Exam</option>
                            <option value="Final Term Exam">Final Term Exam</option>
                            <option value="Annual Examination">Annual Examination</option>
                            <option value="Pre-Board Exam">Pre-Board Exam (Matric / FSc)</option>
                            <option value="Monthly Test">Monthly Class Test</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small text-muted">Start Date</label>
                        <input type="date" name="start_date" class="form-control" aria-label="Start Date">
                    </div>
                    <div>
                        <label class="form-label small text-muted">End Date</label>
                        <input type="date" name="end_date" class="form-control" aria-label="End Date">
                    </div>
                    <div>
                        <label class="form-label small text-muted">Instructions / Description</label>
                        <input type="text" name="description" class="form-control" placeholder="Exam rules, instructions or notes...">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="fa fa-plus-circle me-1"></i> Create Examination
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Examinations Directory -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-0 border-bottom d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fa fa-clipboard-list text-primary fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark">Institutional Examinations Catalog</h5>
            </div>
            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2" style="border-radius: 30px;">
                <?php echo count($exams); ?> Exams Configured
            </span>
        </div>
        <div class="card-body p-0">
            <div class="overflow-hidden">
                    <table class="table table-hover align-middle mb-0 exam-directory-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Examination</th>
                                <th>Session &amp; Type</th>
                                <th>Duration Dates</th>
                                <th>Papers Scheduled</th>
                                <th class="pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($exams)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fa fa-file-excel fa-2x mb-2 d-block opacity-50"></i>
                                        No examinations set up yet. Use the left form to schedule a new term exam.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($exams as $ex): ?>
                                    <tr>
                                        <td class="ps-4" data-label="Examination">
                                            <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($ex->name); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($ex->description ?: 'Standard examination'); ?></small>
                                        </td>
                                        <td data-label="Session &amp; Type">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1 d-inline-block">
                                                <i class="fa fa-calendar-alt me-1"></i><?php echo htmlspecialchars($ex->session_name ?? '2026-27'); ?>
                                            </span>
                                            <div>
                                                <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($ex->exam_type ?? 'Term Exam'); ?></span>
                                            </div>
                                        </td>
                                        <td data-label="Duration Dates">
                                            <?php if(!empty($ex->start_date)): ?>
                                                <div class="small fw-bold text-dark"><?php echo date('d M Y', strtotime($ex->start_date)); ?></div>
                                                <small class="text-muted">to <?php echo !empty($ex->end_date) ? date('d M Y', strtotime($ex->end_date)) : 'TBD'; ?></small>
                                            <?php else: ?>
                                                <span class="text-muted small">Dates not announced</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Papers Scheduled">
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 font-monospace">
                                                <?php echo (int)($ex->total_schedules ?? 0); ?> Papers
                                            </span>
                                        </td>
                                        <td class="pe-4" data-label="Actions">
                                            <div class="exam-action-list">
                                                <a href="<?php echo URLROOT; ?>/exam/signatureSlip?exam_id=<?php echo $ex->id; ?>" class="btn btn-sm btn-outline-warning text-dark px-2 py-1" title="Print Class Attendance & Candidate Signature Slips">
                                                    <i class="fa fa-file-signature me-1"></i><span class="action-label">Signature Sheet</span>
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/exam/schedule/<?php echo $ex->id; ?>" class="btn btn-sm btn-outline-primary px-2 py-1" title="Schedule Class Papers">
                                                    <i class="fa fa-calendar-days me-1"></i><span class="action-label">Papers</span>
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/exam/marks/<?php echo $ex->id; ?>" class="btn btn-sm btn-outline-success px-2 py-1" title="Enter Student Marks">
                                                    <i class="fa fa-marker me-1"></i><span class="action-label">Marks</span>
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/exam/approval?exam_id=<?php echo $ex->id; ?>" class="btn btn-sm btn-outline-dark px-2 py-1" title="4-Tier Result Approval Chain">
                                                    <i class="fa fa-stamp me-1"></i><span class="action-label">Approvals</span>
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/certificate/batchAdmitCards?exam_id=<?php echo $ex->id; ?>&class_id=1" class="btn btn-sm btn-outline-danger px-2 py-1" title="Print Examination Roll Number Slips / Admit Cards">
                                                    <i class="fa fa-id-card me-1"></i><span class="action-label">Slips</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
