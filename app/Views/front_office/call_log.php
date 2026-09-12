<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$callLogs = $data['call_logs'] ?? [];

$totalCalls = count($callLogs);
$incomingCount = 0;
$outgoingCount = 0;
foreach($callLogs as $c) {
    if($c->call_type == 'Incoming') $incomingCount++;
    elseif($c->call_type == 'Outgoing') $outgoingCount++;
}
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/frontoffice/index" class="text-decoration-none text-muted">Front Office</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Phone Call Log</li>
            </ol>
        </nav>
        <h3 class="mb-0 fw-bold">Reception Phone Call &amp; Inquiries Register</h3>
        <p class="text-muted small mb-0">Record incoming and outgoing telephone calls, parent inquiries, follow-up callbacks, and administrative queries.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/frontoffice/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Reception Desk
        </a>
        <button type="button" class="btn btn-dark btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newCallModal">
            <i class="fa fa-phone-alt me-1"></i> Log New Phone Call
        </button>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div><strong>Success!</strong> Telephonic call log record has been updated successfully.</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- QUICK STATS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Calls</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?php echo $totalCalls; ?></h3>
                </div>
                <div class="p-3 bg-dark-subtle text-dark rounded-3">
                    <i class="fa fa-phone-volume fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Incoming Calls</span>
                    <h3 class="fw-bold mb-0 text-success mt-1"><?php echo $incomingCount; ?></h3>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="fa fa-phone-slash fa-2x" style="transform: rotate(135deg);"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Outgoing Callbacks</span>
                    <h3 class="fw-bold mb-0 text-primary mt-1"><?php echo $outgoingCount; ?></h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="fa fa-headset fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CALL LOG TABLE CARD -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="p-2 bg-dark-subtle text-dark rounded-2">
                <i class="fa fa-phone-alt"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark">Telephonic Calls Register</h6>
                <small class="text-muted">Parent queries and staff callbacks</small>
            </div>
        </div>
        <div class="text-muted small">Total calls: <strong><?php echo $totalCalls; ?></strong></div>
    </div>

    <div class="card-body p-0">
        <?php if(empty($callLogs)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-phone-slash fa-3x mb-3 text-secondary opacity-50"></i>
                <h5>No Phone Calls Logged</h5>
                <p class="small text-muted mb-3">No telephonic calls have been recorded yet.</p>
                <button type="button" class="btn btn-dark btn-sm px-4" data-bs-toggle="modal" data-bs-target="#newCallModal">
                    <i class="fa fa-phone me-1"></i> Log First Phone Call
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Call Type</th>
                            <th>Caller Name &amp; Contact</th>
                            <th>Date &amp; Time</th>
                            <th>Duration</th>
                            <th>Purpose / Subject</th>
                            <th>Follow-up Callback</th>
                            <th>Notes</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($callLogs as $call): ?>
                            <tr>
                                <td>
                                    <?php if($call->call_type == 'Incoming'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                            <i class="fa fa-arrow-down me-1"></i>Incoming
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold">
                                            <i class="fa fa-arrow-up me-1"></i>Outgoing
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($call->caller_name); ?></div>
                                    <div class="text-muted fs-xs font-monospace">
                                        <i class="fa fa-phone me-1"></i><?php echo htmlspecialchars($call->phone); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo date('d M, Y', strtotime($call->call_date)); ?></div>
                                    <div class="text-muted fs-xs"><?php echo htmlspecialchars($call->call_time ?: 'N/A'); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($call->duration ?: '1 min'); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary fw-bold px-2 py-1"><?php echo htmlspecialchars($call->purpose ?: 'General'); ?></span>
                                </td>
                                <td>
                                    <?php if(!empty($call->follow_up_date)): ?>
                                        <span class="fw-bold text-warning"><?php echo date('d M, Y', strtotime($call->follow_up_date)); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-muted fs-xs text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($call->note); ?>">
                                        <?php echo htmlspecialchars($call->note ?: 'No notes'); ?>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="<?php echo URLROOT; ?>/frontoffice/deleteCallLog/<?php echo $call->id; ?>" class="btn btn-sm btn-outline-danger" title="Delete Log" onclick="return confirm('Delete this call log?');">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL: LOG NEW PHONE CALL -->
<div class="modal fade" id="newCallModal" tabindex="-1" aria-labelledby="newCallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="newCallModalLabel">
                    <i class="fa fa-phone-alt me-2"></i> Log Phone Call &amp; Parent Inquiry
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontoffice/callLog" method="post">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Call Direction <span class="text-danger">*</span></label>
                            <select name="call_type" class="form-select" required>
                                <option value="Incoming">Incoming (Parent / External Caller)</option>
                                <option value="Outgoing">Outgoing (Callback by Reception / Administration)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Caller / Parent Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="caller_name" class="form-control" placeholder="e.g. Mrs. Farzana / Mr. Khalid" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="0300-1234567" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Call Purpose / Category <span class="text-danger">*</span></label>
                            <select name="purpose" class="form-select" required>
                                <option value="Admission Query">Admission Query</option>
                                <option value="Fee &amp; Payment Inquiry">Fee &amp; Payment Inquiry</option>
                                <option value="Student Attendance / Leave">Student Attendance / Leave</option>
                                <option value="Exam / Result Inquiry">Exam / Result Inquiry</option>
                                <option value="Complaint / Grievance">Complaint / Grievance</option>
                                <option value="General Inquiry">General Inquiry</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Call Date</label>
                            <input type="date" name="call_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Call Time</label>
                            <input type="text" name="call_time" class="form-control" value="<?php echo date('h:i A'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Call Duration</label>
                            <input type="text" name="duration" class="form-control" placeholder="e.g. 3 mins" value="2 mins">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Callback / Follow-up Date</label>
                            <input type="date" name="follow_up_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted pt-4">Leave empty if no callback is required.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Call Conversation Notes &amp; Action Taken</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Brief conversation summary and action taken..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark btn-sm px-4 fw-bold">
                        <i class="fa fa-save me-1"></i> Save Call Log
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
