<?php require APPROOT . '/Views/layouts/header.php'; ?>
<?php
$enquiries = $data['enquiries'] ?? [];
$classes = $data['classes'] ?? [];
$staffs = $data['staffs'] ?? [];
$filterStatus = $data['filter_status'] ?? null;
$schoolName = !empty($dynamicSchoolName) ? $dynamicSchoolName : 'Pakistan Higher Secondary School';

$totalLeads = count($enquiries);
$convertedCount = 0;
$followUpCount = 0;
foreach($enquiries as $e) {
    if($e->status == 'Converted') $convertedCount++;
    elseif($e->status == 'Follow Up' || $e->status == 'New' || empty($e->status)) $followUpCount++;
}
$conversionRate = $totalLeads > 0 ? round(($convertedCount / $totalLeads) * 100, 1) : 0;
?>

<!-- Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/frontoffice/index" class="text-decoration-none text-muted">Front Office</a></li>
                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Admission Enquiries</li>
            </ol>
        </nav>
        <h3 class="mb-0 fw-bold">Admission Leads &amp; Prospectus Inquiries</h3>
        <p class="text-muted small mb-0">Track walk-in inquiries, scheduled parent follow-ups, and convert leads into enrolled students.</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="<?php echo URLROOT; ?>/frontoffice/index" class="btn btn-outline-secondary btn-sm px-3">
            <i class="fa fa-arrow-left me-1"></i> Reception Desk
        </a>
        <a href="<?php echo URLROOT; ?>/frontoffice/printEnquiry<?php echo !empty($filterStatus) ? '?status=' . urlencode($filterStatus) : ''; ?>" target="_blank" class="btn btn-outline-dark btn-sm px-3 shadow-sm" title="Print Admission Inquiry Dossier">
            <i class="fa fa-print me-1"></i> Print Enquiry Forms
        </a>
        <button type="button" class="btn btn-success btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#newEnquiryModal">
            <i class="fa fa-user-plus me-1"></i> Log New Admission Lead
        </button>
    </div>
</div>

<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="fa fa-check-circle fs-5"></i>
        <div><strong>Success!</strong> Admission enquiry record has been updated successfully.</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- PIPELINE KPI TILES -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Total Leads</span>
                    <h3 class="fw-bold mb-0 text-dark mt-1"><?php echo $totalLeads; ?></h3>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3">
                    <i class="fa fa-filter fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Follow-ups Active</span>
                    <h3 class="fw-bold mb-0 text-warning mt-1"><?php echo $followUpCount; ?></h3>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-3">
                    <i class="fa fa-calendar-alt fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Converted to Enrolled</span>
                    <h3 class="fw-bold mb-0 text-success mt-1"><?php echo $convertedCount; ?></h3>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-3">
                    <i class="fa fa-user-check fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold text-uppercase">Conversion Rate</span>
                    <h3 class="fw-bold mb-0 text-info mt-1"><?php echo $conversionRate; ?>%</h3>
                </div>
                <div class="p-3 bg-info-subtle text-info rounded-3">
                    <i class="fa fa-chart-line fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- LEADS DIRECTORY & STATUS FILTER -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-0 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <!-- Filter Tabs -->
        <ul class="nav nav-pills small gap-1">
            <li class="nav-item">
                <a class="nav-link <?php echo empty($filterStatus) ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/frontoffice/enquiry">
                    All Leads
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($filterStatus == 'New') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/frontoffice/enquiry?status=New">
                    New
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($filterStatus == 'Follow Up') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/frontoffice/enquiry?status=Follow+Up">
                    Follow Up
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($filterStatus == 'Form Issued') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/frontoffice/enquiry?status=Form+Issued">
                    Form Issued
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($filterStatus == 'Converted') ? 'active' : ''; ?>" href="<?php echo URLROOT; ?>/frontoffice/enquiry?status=Converted">
                    Converted
                </a>
            </li>
        </ul>

        <div class="text-muted small">
            Showing <strong><?php echo count($enquiries); ?></strong> leads
        </div>
    </div>

    <div class="card-body p-0">
        <?php if(empty($enquiries)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fa fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                <h5>No Enquiries Found</h5>
                <p class="small text-muted mb-3">No admission enquiries recorded matching this criteria.</p>
                <button type="button" class="btn btn-success btn-sm px-4" data-bs-toggle="modal" data-bs-target="#newEnquiryModal">
                    <i class="fa fa-user-plus me-1"></i> Add First Admission Enquiry
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-hover align-middle mb-0 small w-100" style="table-layout: auto;">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 170px;">Applicant &amp; Guardian</th>
                            <th style="min-width: 130px;">Class &amp; Offer</th>
                            <th style="min-width: 130px;">Contact &amp; Source</th>
                            <th style="min-width: 130px;">Follow-up &amp; Staff</th>
                            <th style="min-width: 100px;">Status</th>
                            <th class="text-end pe-3" style="min-width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($enquiries as $e): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($e->name); ?></div>
                                    <div class="text-muted fs-xs">
                                        Father: <strong><?php echo htmlspecialchars($e->father_name ?: 'N/A'); ?></strong>
                                        <?php if(!empty($e->gender)): ?>
                                            &bull; <span class="badge bg-light text-dark border px-1"><?php echo htmlspecialchars($e->gender); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if(!empty($e->previous_school)): ?>
                                        <div class="text-secondary fs-xs text-truncate" style="max-width: 220px;" title="<?php echo htmlspecialchars($e->previous_school); ?>">Prev: <?php echo htmlspecialchars($e->previous_school); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-2 py-1">
                                        <?php echo htmlspecialchars($e->class_name ?: 'General'); ?>
                                    </span>
                                    <?php if(!empty($e->discount_offered) && $e->discount_offered > 0): ?>
                                        <div class="text-success fs-xs fw-bold mt-1">
                                            <i class="fa fa-tag me-1"></i>PKR <?php echo number_format($e->discount_offered); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-dark fw-bold"><i class="fa fa-phone me-1 text-muted"></i><?php echo htmlspecialchars($e->phone); ?></div>
                                    <div class="text-muted fs-xs">
                                        <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($e->source ?: 'Walk-in'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if(!empty($e->next_follow_up_date)): ?>
                                        <?php
                                        $isOverdue = (strtotime($e->next_follow_up_date) < strtotime(date('Y-m-d')) && $e->status != 'Converted' && $e->status != 'Closed');
                                        ?>
                                        <div class="fw-bold <?php echo $isOverdue ? 'text-danger' : 'text-dark'; ?>">
                                            <?php echo date('d M, Y', strtotime($e->next_follow_up_date)); ?>
                                        </div>
                                        <?php if($isOverdue): ?>
                                            <span class="badge bg-danger-subtle text-danger px-1 fs-xs">Overdue</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not Set</span>
                                    <?php endif; ?>
                                    <div class="text-secondary fs-xs mt-1">
                                        <i class="fa fa-user-tie me-1 opacity-50"></i><?php echo htmlspecialchars($e->staff_name ?: 'Front Desk'); ?>
                                    </div>
                                </td>
                                <td>
                                    <!-- Status Update Dropdown -->
                                    <div class="dropdown">
                                        <?php
                                        $stClass = 'bg-primary-subtle text-primary';
                                        if ($e->status == 'Converted') $stClass = 'bg-success-subtle text-success';
                                        elseif ($e->status == 'Follow Up') $stClass = 'bg-warning-subtle text-dark';
                                        elseif ($e->status == 'Form Issued') $stClass = 'bg-info-subtle text-info';
                                        elseif ($e->status == 'Closed') $stClass = 'bg-secondary-subtle text-secondary';
                                        ?>
                                        <button class="btn btn-sm <?php echo $stClass; ?> fw-bold dropdown-toggle border-0 px-2 py-1 fs-xs" type="button" data-bs-toggle="dropdown">
                                            <?php echo htmlspecialchars($e->status ?: 'New'); ?>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 fs-xs">
                                            <li><h6 class="dropdown-header text-uppercase fs-xxs">Change Lead Stage</h6></li>
                                            <li>
                                                <form action="<?php echo URLROOT; ?>/frontoffice/updateEnquiryStatus/<?php echo $e->id; ?>" method="post">
                                                    <input type="hidden" name="status" value="New">
                                                    <button type="submit" class="dropdown-item">New Lead</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?php echo URLROOT; ?>/frontoffice/updateEnquiryStatus/<?php echo $e->id; ?>" method="post">
                                                    <input type="hidden" name="status" value="Follow Up">
                                                    <button type="submit" class="dropdown-item">Follow Up Scheduled</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?php echo URLROOT; ?>/frontoffice/updateEnquiryStatus/<?php echo $e->id; ?>" method="post">
                                                    <input type="hidden" name="status" value="Form Issued">
                                                    <button type="submit" class="dropdown-item">Prospectus / Form Issued</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?php echo URLROOT; ?>/frontoffice/updateEnquiryStatus/<?php echo $e->id; ?>" method="post">
                                                    <input type="hidden" name="status" value="Converted">
                                                    <button type="submit" class="dropdown-item text-success fw-bold">Converted to Admission</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="<?php echo URLROOT; ?>/frontoffice/updateEnquiryStatus/<?php echo $e->id; ?>" method="post">
                                                    <input type="hidden" name="status" value="Closed">
                                                    <button type="submit" class="dropdown-item text-muted">Closed / Not Interested</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <!-- Prominent Direct Enroll Button -->
                                        <a href="<?php echo URLROOT; ?>/students/admission?name=<?php echo urlencode($e->name); ?>&father_name=<?php echo urlencode($e->father_name); ?>&phone=<?php echo urlencode($e->phone); ?>&class_id=<?php echo $e->class_id; ?>" class="btn btn-success btn-sm px-2 py-1 fw-bold text-white shadow-sm d-inline-flex align-items-center gap-1" title="Accept &amp; Enroll Candidate into Student Profile">
                                            <i class="fa fa-user-plus"></i>
                                            <span>Enroll</span>
                                        </a>

                                        <!-- Print Slip Button -->
                                        <a href="<?php echo URLROOT; ?>/frontoffice/printEnquiry/<?php echo $e->id; ?>" target="_blank" class="btn btn-outline-dark btn-sm px-2 py-1" title="Print Admission Inquiry Dossier Slip">
                                            <i class="fa fa-print"></i>
                                        </a>

                                        <!-- WhatsApp Follow Up -->
                                        <?php
                                        $cleanPhone = preg_replace('/[^0-9]/', '', $e->phone);
                                        if (substr($cleanPhone, 0, 1) === '0') $cleanPhone = '92' . substr($cleanPhone, 1);
                                        $msg = urlencode("Respected Parent, Thank you for inquiring about admission at " . $schoolName . " for student " . $e->name . ". Kindly let us know if you have any questions or would like to visit the campus.");
                                        ?>
                                        <a href="https://wa.me/<?php echo $cleanPhone; ?>?text=<?php echo $msg; ?>" target="_blank" class="btn btn-outline-success btn-sm px-2 py-1" title="WhatsApp Follow-up">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>

                                        <!-- Delete -->
                                        <a href="<?php echo URLROOT; ?>/frontoffice/deleteEnquiry/<?php echo $e->id; ?>" class="btn btn-outline-danger btn-sm px-2 py-1" title="Delete Lead" onclick="return confirm('Delete this admission lead record?');">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL: LOG NEW ADMISSION LEAD -->
<div class="modal fade" id="newEnquiryModal" tabindex="-1" aria-labelledby="newEnquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="newEnquiryModalLabel">
                    <i class="fa fa-user-plus me-2"></i> Log New Admission Lead &amp; Inquiry
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontoffice/enquiry" method="post">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Candidate / Child Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Student name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Father / Guardian Name</label>
                            <input type="text" name="father_name" class="form-control" placeholder="Father's full name">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Contact / Mobile Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="0300-1234567" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="parent@example.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Class Desired <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select" required>
                                <option value="">-- Choose Class --</option>
                                <?php foreach($classes as $c): ?>
                                    <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->class_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Lead Source</label>
                            <select name="source" class="form-select">
                                <option value="Direct Walk-in">Direct Walk-in</option>
                                <option value="Phone Inquiry">Phone Inquiry</option>
                                <option value="Social Media / Facebook">Social Media / Facebook</option>
                                <option value="Friend / Parent Referral">Friend / Parent Referral</option>
                                <option value="Newspaper / Banner">Newspaper / Banner</option>
                                <option value="School Website">School Website</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Inquiry Date</label>
                            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Next Follow-Up Date</label>
                            <input type="date" name="next_follow_up_date" class="form-control" value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Counselor Assigned</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Front Desk</option>
                                <?php foreach($staffs as $s): ?>
                                    <option value="<?php echo $s->id; ?>"><?php echo htmlspecialchars($s->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Previous School</label>
                            <input type="text" name="previous_school" class="form-control" placeholder="Last school attended">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Fee Concession Offered (PKR)</label>
                            <input type="number" name="discount_offered" class="form-control" placeholder="0.00" step="100">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Residential Address</label>
                            <input type="text" name="address" class="form-control" placeholder="House / Street / Colony / City">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Inquiry Notes / Special Requirements</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Parent expectations, sibling context, transportation inquiry..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold">
                        <i class="fa fa-save me-1"></i> Save Admission Lead
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
