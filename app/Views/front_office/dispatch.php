<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$dispatches = $data['dispatches'] ?? [];
$filterType = $data['filter_type'] ?? null;

$totalCount = count($dispatches);
$inwardCount = 0;
$outwardCount = 0;
foreach($dispatches as $d) {
    if($d->dispatch_type == 'Inward') $inwardCount++;
    elseif($d->dispatch_type == 'Outward') $outwardCount++;
}
?>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/frontoffice/index" class="text-decoration-none text-muted">Front Office</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Postal Dispatch</li>
            </ol>
        </nav>
        <h3 class="mb-0 fw-bold">Postal &amp; Courier Dispatch Registry</h3>
        <p class="text-muted small mb-0">Record official inward mail from BISE Boards, Ministry of Education, and outward institutional correspondence.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/frontoffice/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Reception
        </a>
        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newDispatchModal">
            <i class="fa fa-envelope-open-text me-1"></i> Log New Postal Letter
        </button>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div><strong>Success!</strong> Postal dispatch log entry has been saved successfully.</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- QUICK STATS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Postal Letters</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?php echo $totalCount; ?></h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="fa fa-mail-bulk fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Inward Received (Boards / Govt)</span>
                    <h3 class="fw-bold mb-0 text-success mt-1"><?php echo $inwardCount; ?></h3>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="fa fa-arrow-down fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Outward Dispatched</span>
                    <h3 class="fw-bold mb-0 text-info mt-1"><?php echo $outwardCount; ?></h3>
                </div>
                <div class="p-3 bg-info-subtle text-info rounded-3">
                    <i class="fa fa-arrow-up fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- POSTAL DIRECTORY CARD -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <ul class="nav nav-pills small gap-1">
            <li class="nav-item">
                <a class="nav-link <?php echo empty($filterType) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/frontoffice/dispatch">
                    All Dispatches
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($filterType == 'Inward') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/frontoffice/dispatch?type=Inward">
                    <i class="fa fa-arrow-down me-1 text-success"></i> Inward (Received)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($filterType == 'Outward') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/frontoffice/dispatch?type=Outward">
                    <i class="fa fa-arrow-up me-1 text-primary"></i> Outward (Sent)
                </a>
            </li>
        </ul>

        <div class="text-muted small">
            Total records: <strong><?php echo count($dispatches); ?></strong>
        </div>
    </div>

    <div class="card-body p-0">
        <?php if(empty($dispatches)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-envelope-open-text fa-3x mb-3 text-secondary opacity-50"></i>
                <h5>No Postal Records Logged</h5>
                <p class="small text-muted mb-3">No mail records recorded matching this criteria.</p>
                <button type="button" class="btn btn-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#newDispatchModal">
                    <i class="fa fa-plus me-1"></i> Log First Dispatch
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Reference / Letter #</th>
                            <th>Sender (From)</th>
                            <th>Addressee (To)</th>
                            <th>Category</th>
                            <th>Courier &amp; Tracking ID</th>
                            <th>Date</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($dispatches as $d): ?>
                            <tr>
                                <td>
                                    <?php if($d->dispatch_type == 'Inward'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                            <i class="fa fa-arrow-down me-1"></i>Inward
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold">
                                            <i class="fa fa-arrow-up me-1"></i>Outward
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold font-monospace text-dark"><?php echo htmlspecialchars($d->reference_no ?: 'No Ref #'); ?></div>
                                    <?php if(!empty($d->note)): ?>
                                        <div class="text-muted fs-xs text-truncate" style="max-width: 170px;" title="<?php echo htmlspecialchars($d->note); ?>">
                                            <?php echo htmlspecialchars($d->note); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($d->sender_title); ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold text-secondary"><?php echo htmlspecialchars($d->receiver_title); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1"><?php echo htmlspecialchars($d->category ?: 'General'); ?></span>
                                </td>
                                <td>
                                    <div class="text-dark fw-bold"><?php echo htmlspecialchars($d->courier_name ?: 'TCS'); ?></div>
                                    <?php if(!empty($d->tracking_id)): ?>
                                        <div class="text-muted font-monospace fs-xs">
                                            Track: <span class="text-primary"><?php echo htmlspecialchars($d->tracking_id); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-dark fw-bold"><?php echo date('d M, Y', strtotime($d->record_date)); ?></div>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="<?php echo URLROOT; ?>/frontoffice/deleteDispatch/<?php echo $d->id; ?>" class="btn btn-sm btn-outline-danger" title="Delete Log" onclick="return confirm('Delete this dispatch record?');">
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

<!-- MODAL: LOG NEW POSTAL DISPATCH -->
<div class="modal fade" id="newDispatchModal" tabindex="-1" aria-labelledby="newDispatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="newDispatchModalLabel">
                    <i class="fa fa-envelope-open-text me-2"></i> Log Postal Correspondence / Dispatch
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontoffice/dispatch" method="post">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Correspondence Type <span class="text-danger">*</span></label>
                            <select name="dispatch_type" class="form-select" required>
                                <option value="Inward">Inward (Letter / Courier Received)</option>
                                <option value="Outward">Outward (Letter / Notice Sent Out)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Reference / Dispatch Number</label>
                            <input type="text" name="reference_no" class="form-control font-monospace" placeholder="e.g. BISE-LHR/2026/892">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Sender (From) <span class="text-danger">*</span></label>
                            <input type="text" name="sender_title" class="form-control" placeholder="e.g. Controller of Exams, BISE Lahore" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Addressee / Receiver (To) <span class="text-danger">*</span></label>
                            <input type="text" name="receiver_title" class="form-control" placeholder="e.g. Principal Office / Accounts Section" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Category</label>
                            <select name="category" class="form-select">
                                <option value="Official Board Circular">Official Board Circular</option>
                                <option value="Directorate / Govt Directive">Directorate / Govt Directive</option>
                                <option value="Exam Stationery &amp; Papers">Exam Stationery &amp; Papers</option>
                                <option value="Fee Draft / Bank Cheque">Fee Draft / Bank Cheque</option>
                                <option value="Legal / Judicial Notice">Legal / Judicial Notice</option>
                                <option value="Vendor Invoice / Material">Vendor Invoice / Material</option>
                                <option value="General Correspondence">General Correspondence</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Courier / Postal Service</label>
                            <input type="text" name="courier_name" class="form-control" placeholder="e.g. TCS / Pakistan Post / Leopards">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Tracking / Consignment #</label>
                            <input type="text" name="tracking_id" class="form-control font-monospace" placeholder="e.g. 774892019">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Date <span class="text-danger">*</span></label>
                            <input type="date" name="record_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Summary / Notes</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Subject or summary of contents..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fa fa-save me-1"></i> Save Dispatch Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
