<?php require APPROOT . '/Views/layouts/header.php'; 
    $s = $data['settings_raw'] ?? [];
    $activeTab = $data['active_tab'] ?? 'general';
?>

<div class="container-fluid px-0">
    <!-- Page Header & Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/admin/dashboard" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Institutional Settings</li>
                </ol>
            </nav>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-sliders text-primary me-2"></i>Institutional Settings &amp; Access Control
            </h2>
            <small class="text-muted">Configure campus profile, code prefixes, role permissions matrix, and system security switches.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/reports/index" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fa fa-chart-line me-1"></i> Reports Center
            </a>
            <a href="<?php echo URLROOT; ?>/admin/dashboard" class="btn btn-primary btn-sm px-3">
                <i class="fa fa-tachometer-alt me-1"></i> Principal Dashboard
            </a>
        </div>
    </div>

    <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa fa-check-circle fs-5"></i>
            <div><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fa fa-exclamation-circle fs-5"></i>
            <div><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Clean Modern Navigation Tabs Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-2 bg-white rounded-3">
            <ul class="nav nav-pills nav-justified flex-column flex-md-row gap-1" id="settingsTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link py-2 px-3 fw-semibold <?php echo ($activeTab === 'general') ? 'active' : ''; ?>" id="general-tab" data-bs-toggle="pill" data-bs-target="#tab-general" type="button" role="tab">
                        <i class="fa fa-school me-2"></i>Campus &amp; Profile
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 px-3 fw-semibold <?php echo ($activeTab === 'prefixes') ? 'active' : ''; ?>" id="prefixes-tab" data-bs-toggle="pill" data-bs-target="#tab-prefixes" type="button" role="tab">
                        <i class="fa fa-hashtag me-2"></i>Numbering Prefixes
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 px-3 fw-semibold <?php echo ($activeTab === 'roles') ? 'active' : ''; ?>" id="roles-tab" data-bs-toggle="pill" data-bs-target="#tab-roles" type="button" role="tab">
                        <i class="fa fa-user-shield me-2"></i>Roles &amp; Permissions
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 px-3 fw-semibold <?php echo ($activeTab === 'features') ? 'active' : ''; ?>" id="features-tab" data-bs-toggle="pill" data-bs-target="#tab-features" type="button" role="tab">
                        <i class="fa fa-toggle-on me-2"></i>Feature Switches
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 px-3 fw-semibold <?php echo ($activeTab === 'website') ? 'active' : ''; ?>" id="website-tab" data-bs-toggle="pill" data-bs-target="#tab-website" type="button" role="tab">
                        <i class="fa fa-globe me-2"></i>Public Website CMS
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 px-3 fw-semibold <?php echo ($activeTab === 'email') ? 'active' : ''; ?>" id="email-tab" data-bs-toggle="pill" data-bs-target="#tab-email" type="button" role="tab">
                        <i class="fa fa-envelope me-2"></i>Email &amp; SMTP
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-2 px-3 fw-semibold <?php echo ($activeTab === 'livechat') ? 'active' : ''; ?>" id="livechat-tab" data-bs-toggle="pill" data-bs-target="#tab-livechat" type="button" role="tab">
                        <i class="fab fa-whatsapp text-success me-2"></i>WhatsApp &amp; Live Chat
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="settingsTabsContent">
        
        <!-- ============================================================= -->
        <!-- TAB 1: INSTITUTIONAL & CAMPUS PROFILE                         -->
        <!-- ============================================================= -->
        <div class="tab-pane fade <?php echo ($activeTab === 'general') ? 'show active' : ''; ?>" id="tab-general" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="card-title fw-bold mb-0 text-primary">
                                <i class="fa fa-building me-2"></i>Institutional Identification &amp; Regulatory Profile
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo URLROOT; ?>/setting/index?tab=general" method="post" enctype="multipart/form-data" class="ajax-settings-form">
                                <input type="hidden" name="tab" value="general">
                                <input type="hidden" name="general_setting" value="1">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-school text-primary me-2"></i>School / College Legal Name</label>
                                        <input type="text" name="school_name" class="form-control" required value="<?php echo htmlspecialchars($s['school_name'] ?? 'City Model High School & College'); ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-map-marker-alt text-info me-2"></i>Campus / Branch Name</label>
                                        <input type="text" name="campus_name" class="form-control" value="<?php echo htmlspecialchars($s['campus_name'] ?? 'Main Executive Campus'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-certificate text-warning me-2"></i>Affiliation Board / Body</label>
                                        <input type="text" name="affiliation_board" class="form-control" placeholder="e.g. FBISE Islamabad, Cambridge" value="<?php echo htmlspecialchars($s['affiliation_board'] ?? 'Federal Board of Intermediate & Secondary Education'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-hashtag text-secondary me-2"></i>Board Reg #</label>
                                        <input type="text" name="affiliation_no" class="form-control" placeholder="e.g. FBISE-REG-88219" value="<?php echo htmlspecialchars($s['affiliation_no'] ?? 'FBISE-REG-88219'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-receipt text-danger me-2"></i>NTN / Tax ID</label>
                                        <input type="text" name="ntn_number" class="form-control" placeholder="e.g. NTN-7349102-1" value="<?php echo htmlspecialchars($s['ntn_number'] ?? 'NTN-7349102-1'); ?>">
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-envelope text-primary me-2"></i>Official Contact Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i class="fa fa-envelope text-primary"></i></span>
                                            <input type="email" name="school_email" class="form-control border-start-0" value="<?php echo htmlspecialchars($s['school_email'] ?? 'info@citymodelschool.edu.pk'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-phone text-success me-2"></i>Official Landline / UAN</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i class="fa fa-phone text-success"></i></span>
                                            <input type="text" name="school_phone" class="form-control border-start-0" value="<?php echo htmlspecialchars($s['school_phone'] ?? '+92-51-111-222-333'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-money-bill-wave text-success me-2"></i>Currency Symbol</label>
                                        <input type="text" name="currency_symbol" list="currencyOptions" class="form-control" value="<?php echo htmlspecialchars($s['currency_symbol'] ?? 'PKR'); ?>" placeholder="e.g. PKR, Rs., $">
                                        <datalist id="currencyOptions">
                                            <option value="PKR">PKR (Pakistani Rupee)</option>
                                            <option value="Rs.">Rs. (Rupees)</option>
                                            <option value="$">USD ($ Dollar)</option>
                                            <option value="€">EUR (€ Euro)</option>
                                            <option value="£">GBP (£ Pound)</option>
                                            <option value="AED">AED (Dirham)</option>
                                            <option value="SAR">SAR (Riyal)</option>
                                        </datalist>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-location-dot text-danger me-2"></i>Campus Physical Address</label>
                                        <textarea name="school_address" class="form-control" rows="2"><?php echo htmlspecialchars($s['school_address'] ?? 'Plot 45-B, Sector H-8/4, Education City, Islamabad'); ?></textarea>
                                    </div>

                                    <div class="col-md-7">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-image text-info me-2"></i>Update Institutional Crest / Logo</label>
                                        <input type="file" name="logo" class="form-control" accept="image/*">
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">PNG, JPG or WebP (Max 2MB). Applied across all Fee Challans &amp; Certificates.</div>
                                    </div>

                                    <div class="col-md-5">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-calendar-check text-primary me-2"></i>Default Academic Session</label>
                                        <select name="session_id" class="form-select">
                                            <?php foreach($data['sessions'] as $sess): ?>
                                                <option value="<?php echo $sess->id; ?>" <?php echo (isset($s['session_id']) && $s['session_id'] == $sess->id) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($sess->session_name ?? $sess->session ?? 'Session'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- Official Institutional Bank Account & Challan Particulars -->
                                    <div class="col-12 mt-4">
                                        <div class="border rounded-3 p-3 bg-light-subtle shadow-xs">
                                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3 border-bottom pb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="fa fa-university text-success fs-5"></i>
                                                    <h6 class="fw-bold mb-0 text-dark">Official Institutional Banking &amp; Fee Collection Particulars</h6>
                                                </div>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small fw-bold">
                                                    Printed on Student Fee Challans
                                                </span>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-building-columns text-primary me-2"></i>Bank Name</label>
                                                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. Meezan Bank / HBL" value="<?php echo htmlspecialchars($s['bank_name'] ?? 'Meezan Bank Limited'); ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-user-tie text-info me-2"></i>Account Title / Payee</label>
                                                    <input type="text" name="bank_account_title" class="form-control" placeholder="e.g. City Model School Fee Collection A/C" value="<?php echo htmlspecialchars($s['bank_account_title'] ?? 'City Model High School & College Collection A/C'); ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-credit-card text-success me-2"></i>Account Number</label>
                                                    <input type="text" name="bank_account_no" class="form-control font-monospace fw-bold" placeholder="e.g. 0102-0103498102" value="<?php echo htmlspecialchars($s['bank_account_no'] ?? '0102-0103498102'); ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-barcode text-danger me-2"></i>IBAN (International Bank Account Number)</label>
                                                    <input type="text" name="bank_iban" class="form-control font-monospace fw-bold text-uppercase" placeholder="e.g. PK12MEZN0001020103498102" value="<?php echo htmlspecialchars($s['bank_iban'] ?? 'PK12MEZN0001020103498102'); ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-map-pin text-secondary me-2"></i>Branch Name &amp; Code</label>
                                                    <input type="text" name="bank_branch" class="form-control" placeholder="e.g. Main Branch (Code: 0102)" value="<?php echo htmlspecialchars($s['bank_branch'] ?? 'Executive Branch, Sector H-8 Markaz (Branch Code: 0102)'); ?>">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-info-circle text-primary me-2"></i>Fee Challan Instructions &amp; Policies</label>
                                                    <textarea name="bank_instructions" class="form-control" rows="2" placeholder="Instructions displayed on printed fee challans..."><?php echo htmlspecialchars($s['bank_instructions'] ?? 'Fee must be deposited across any online branch of Meezan Bank by the 10th of every month. Late payment surcharge of Rs. 200 applies after the due date.'); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" name="general_setting" class="btn btn-primary px-4 shadow-sm fw-bold">
                                        <i class="fa fa-save me-1"></i> Save Institutional Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Logo Preview & Sessions -->
                <div class="col-lg-4">
                    <!-- Crest Preview Card -->
                    <div class="card border-0 shadow-sm mb-4 text-center p-4">
                        <div class="mb-3" id="logoPreviewContainer">
                            <?php if(!empty($s['logo'])): ?>
                                <img id="schoolLogoPreviewImg" src="<?php echo URLROOT . '/' . htmlspecialchars($s['logo']); ?>" alt="School Crest" class="img-fluid rounded border shadow-sm p-2" style="max-height: 120px; object-fit: contain;">
                            <?php else: ?>
                                <div id="schoolLogoFallback" class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center text-primary" style="width: 90px; height: 90px;">
                                    <i class="fa fa-graduation-cap fa-3x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($s['school_name'] ?? 'City Model High School & College'); ?></h6>
                        <span class="badge bg-primary-subtle text-primary mb-2 px-3 py-1"><?php echo htmlspecialchars($s['campus_name'] ?? 'Main Executive Campus'); ?></span>
                        <div class="small text-muted"><i class="fa fa-certificate text-warning me-1"></i><?php echo htmlspecialchars($s['affiliation_board'] ?? 'FBISE Affiliated'); ?></div>
                    </div>

                    <!-- Academic Sessions Management Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0"><i class="fa fa-calendar-alt text-primary me-2"></i>Academic Sessions</h6>
                        </div>
                        <div class="card-body p-3">
                            <form action="<?php echo URLROOT; ?>/setting/index?tab=general" method="post" class="d-flex gap-2 mb-3">
                                <input type="text" name="session" class="form-control form-control-sm" placeholder="e.g. 2026-27" required>
                                <button type="submit" name="add_session" class="btn btn-sm btn-outline-primary text-nowrap fw-bold">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </form>

                            <ul class="list-group list-group-flush small">
                                <?php foreach($data['sessions'] as $sess): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa fa-check-circle <?php echo (!empty($sess->is_current)) ? 'text-success' : 'text-muted'; ?>"></i>
                                            <span class="fw-semibold"><?php echo htmlspecialchars($sess->session_name ?? $sess->session ?? 'Session'); ?></span>
                                            <?php if(!empty($sess->is_current)): ?>
                                                <span class="badge bg-success-subtle text-success py-0 px-2" style="font-size: 0.7rem;">Active</span>
                                            <?php endif; ?>
                                        </div>
                                        <form action="<?php echo URLROOT; ?>/setting/index?tab=general" method="post" onsubmit="return confirm('Delete this session?');">
                                            <input type="hidden" name="delete_id" value="<?php echo $sess->id; ?>">
                                            <button type="submit" name="delete_session" class="btn btn-sm btn-link text-danger p-0">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 2: NUMBERING & CODE PREFIXES                              -->
        <!-- ============================================================= -->
        <div class="tab-pane fade <?php echo ($activeTab === 'prefixes') ? 'show active' : ''; ?>" id="tab-prefixes" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title fw-bold mb-0 text-primary">
                                <i class="fa fa-hashtag me-2"></i>Institutional Code &amp; Serial Number Prefixes
                            </h5>
                            <small class="text-muted">Define standardized identifiers prepended to computer-generated records across modules.</small>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2">
                            <i class="fa fa-sync-alt me-1"></i> Auto-Generated On Save
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo URLROOT; ?>/setting/index?tab=prefixes" method="post" class="ajax-settings-form">
                        <input type="hidden" name="tab" value="prefixes">
                        <input type="hidden" name="prefixes_setting" value="1">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        <div class="row g-4">
                            <!-- Student Admission Prefix -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded-3 p-3 bg-white shadow-xs h-100 position-relative">
                                    <label class="form-label text-dark fw-semibold small mb-1">
                                        <i class="fa fa-user-graduate text-primary me-2"></i>Student Admission
                                    </label>
                                    <input type="text" name="prefix_student" class="form-control font-monospace fw-bold text-uppercase prefix-input" value="<?php echo htmlspecialchars($s['prefix_student'] ?? 'ADM-'); ?>" required>
                                    <div class="mt-2 text-dark small">Sample: <span class="badge bg-dark text-white font-monospace sample-prefix-badge" data-suffix="2026-0042"><?php echo htmlspecialchars($s['prefix_student'] ?? 'ADM-'); ?>2026-0042</span></div>
                                </div>
                            </div>

                            <!-- Staff Employee Prefix -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded-3 p-3 bg-white shadow-xs h-100 position-relative">
                                    <label class="form-label text-dark fw-semibold small mb-1">
                                        <i class="fa fa-id-badge text-success me-2"></i>Staff Employee
                                    </label>
                                    <input type="text" name="prefix_staff" class="form-control font-monospace fw-bold text-uppercase prefix-input" value="<?php echo htmlspecialchars($s['prefix_staff'] ?? 'EMP-'); ?>" required>
                                    <div class="mt-2 text-dark small">Sample: <span class="badge bg-dark text-white font-monospace sample-prefix-badge" data-suffix="1049"><?php echo htmlspecialchars($s['prefix_staff'] ?? 'EMP-'); ?>1049</span></div>
                                </div>
                            </div>

                            <!-- Teacher Faculty Prefix -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded-3 p-3 bg-white shadow-xs h-100 position-relative">
                                    <label class="form-label text-dark fw-semibold small mb-1">
                                        <i class="fa fa-chalkboard-teacher text-info me-2"></i>Teacher Faculty
                                    </label>
                                    <input type="text" name="prefix_teacher" class="form-control font-monospace fw-bold text-uppercase prefix-input" value="<?php echo htmlspecialchars($s['prefix_teacher'] ?? 'TCH-'); ?>" required>
                                    <div class="mt-2 text-dark small">Sample: <span class="badge bg-dark text-white font-monospace sample-prefix-badge" data-suffix="8012"><?php echo htmlspecialchars($s['prefix_teacher'] ?? 'TCH-'); ?>8012</span></div>
                                </div>
                            </div>

                            <!-- Family / Sibling Account Prefix -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded-3 p-3 bg-white shadow-xs h-100 position-relative">
                                    <label class="form-label text-dark fw-semibold small mb-1">
                                        <i class="fa fa-users text-warning me-2"></i>Family Account
                                    </label>
                                    <input type="text" name="prefix_family" class="form-control font-monospace fw-bold text-uppercase prefix-input" value="<?php echo htmlspecialchars($s['prefix_family'] ?? 'FAM-'); ?>" required>
                                    <div class="mt-2 text-dark small">Sample: <span class="badge bg-dark text-white font-monospace sample-prefix-badge" data-suffix="0492"><?php echo htmlspecialchars($s['prefix_family'] ?? 'FAM-'); ?>0492</span></div>
                                </div>
                            </div>

                            <!-- Fee Bank Challan Prefix -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded-3 p-3 bg-white shadow-xs h-100 position-relative">
                                    <label class="form-label text-dark fw-semibold small mb-1">
                                        <i class="fa fa-receipt text-danger me-2"></i>Bank Fee Challan
                                    </label>
                                    <input type="text" name="prefix_challan" class="form-control font-monospace fw-bold text-uppercase prefix-input" value="<?php echo htmlspecialchars($s['prefix_challan'] ?? 'CHL-'); ?>" required>
                                    <div class="mt-2 text-dark small">Sample: <span class="badge bg-dark text-white font-monospace sample-prefix-badge" data-suffix="99214"><?php echo htmlspecialchars($s['prefix_challan'] ?? 'CHL-'); ?>99214</span></div>
                                </div>
                            </div>

                            <!-- Student Clearance Prefix -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded-3 p-3 bg-white shadow-xs h-100 position-relative">
                                    <label class="form-label text-dark fw-semibold small mb-1">
                                        <i class="fa fa-clipboard-check text-purple me-2"></i>Exit Clearance
                                    </label>
                                    <input type="text" name="prefix_clearance" class="form-control font-monospace fw-bold text-uppercase prefix-input" value="<?php echo htmlspecialchars($s['prefix_clearance'] ?? 'CLR-'); ?>" required>
                                    <div class="mt-2 text-dark small">Sample: <span class="badge bg-dark text-white font-monospace sample-prefix-badge" data-suffix="0042"><?php echo htmlspecialchars($s['prefix_clearance'] ?? 'CLR-'); ?>0042</span></div>
                                </div>
                            </div>

                            <!-- Visitor Gate Pass Prefix -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded-3 p-3 bg-white shadow-xs h-100 position-relative">
                                    <label class="form-label text-dark fw-semibold small mb-1">
                                        <i class="fa fa-address-book text-secondary me-2"></i>Visitor Pass
                                    </label>
                                    <input type="text" name="prefix_visitor" class="form-control font-monospace fw-bold text-uppercase prefix-input" value="<?php echo htmlspecialchars($s['prefix_visitor'] ?? 'VIS-'); ?>" required>
                                    <div class="mt-2 text-dark small">Sample: <span class="badge bg-dark text-white font-monospace sample-prefix-badge" data-suffix="2026-012"><?php echo htmlspecialchars($s['prefix_visitor'] ?? 'VIS-'); ?>2026-012</span></div>
                                </div>
                            </div>

                            <!-- Early Departure Gate Pass Prefix -->
                            <div class="col-md-6 col-lg-3">
                                <div class="border rounded-3 p-3 bg-white shadow-xs h-100 position-relative">
                                    <label class="form-label text-dark fw-semibold small mb-1">
                                        <i class="fa fa-door-open text-dark me-2"></i>Student Gate Pass
                                    </label>
                                    <input type="text" name="prefix_gatepass" class="form-control font-monospace fw-bold text-uppercase prefix-input" value="<?php echo htmlspecialchars($s['prefix_gatepass'] ?? 'GP-'); ?>" required>
                                    <div class="mt-2 text-dark small">Sample: <span class="badge bg-dark text-white font-monospace sample-prefix-badge" data-suffix="8831"><?php echo htmlspecialchars($s['prefix_gatepass'] ?? 'GP-'); ?>8831</span></div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-end">
                            <button type="submit" name="prefixes_setting" class="btn btn-primary px-4 shadow-sm fw-bold">
                                <i class="fa fa-save me-1"></i> Save Code Prefixes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 3: ROLES & GRANULAR PERMISSIONS MATRIX (RBAC)             -->
        <!-- ============================================================= -->
        <div class="tab-pane fade <?php echo ($activeTab === 'roles') ? 'show active' : ''; ?>" id="tab-roles" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="card-title fw-bold mb-0 text-primary">
                            <i class="fa fa-user-shield me-2"></i>Institutional Roles &amp; Granular Permissions Matrix
                        </h5>
                        <small class="text-muted">Manage system roles, configure permissions, or onboard staff &amp; users directly.</small>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                            <i class="fa fa-plus-circle me-1"></i> Add Role
                        </button>
                        <button type="button" class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#quickAddUserModal">
                            <i class="fa fa-user-plus me-1"></i> Add User / Teacher
                        </button>
                        <a href="<?php echo URLROOT; ?>/admin/users" class="btn btn-sm btn-outline-dark fw-bold">
                            <i class="fa fa-users-cog me-1"></i> All Users
                        </a>
                    </div>
                </div>

                <form action="<?php echo URLROOT; ?>/setting/index?tab=roles" method="post" class="ajax-settings-form m-0">
                    <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 border-top">
                            <thead class="table-light sticky-top" style="z-index: 5;">
                                <tr>
                                    <th style="width: 30%;" class="ps-4">
                                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">Functional Module &amp; Permission</div>
                                    </th>
                                    <?php 
                                    $roleMeta = [
                                        'super_admin' => ['bg' => 'bg-danger text-white', 'tag' => 'Bypass', 'short' => 'S.Admin'],
                                        'admin' => ['bg' => 'bg-primary text-white', 'tag' => 'Exec', 'short' => 'Admin'],
                                        'teacher' => ['bg' => 'bg-info text-white', 'tag' => 'Faculty', 'short' => 'Teacher'],
                                        'accountant' => ['bg' => 'bg-warning text-dark', 'tag' => 'Finance', 'short' => 'Account'],
                                        'librarian' => ['bg' => 'bg-purple text-white', 'tag' => 'Library', 'short' => 'Librarian'],
                                        'receptionist' => ['bg' => 'bg-secondary text-white', 'tag' => 'Desk', 'short' => 'Reception'],
                                        'student' => ['bg' => 'bg-success text-white', 'tag' => 'Portal', 'short' => 'Student'],
                                        'parent' => ['bg' => 'bg-dark text-white', 'tag' => 'Family', 'short' => 'Parent']
                                    ];
                                    foreach($data['roles'] as $role): 
                                        $rm = $roleMeta[$role->name] ?? ['bg' => 'bg-secondary text-white', 'tag' => 'Role', 'short' => $role->name];
                                    ?>
                                        <th style="width: 8.5%;" class="text-center px-1">
                                            <span class="badge <?php echo $rm['bg']; ?> px-1 py-0 mb-1" style="font-size: 0.62rem;"><?php echo $rm['tag']; ?></span>
                                            <div class="text-dark fw-bold text-truncate" style="font-size: 0.78rem;"><?php echo $rm['short']; ?></div>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['grouped_permissions'] as $catName => $perms): ?>
                                    <tr class="table-secondary">
                                        <td colspan="<?php echo count($data['roles']) + 1; ?>" class="ps-4 fw-bold text-dark py-2">
                                            <i class="fa fa-folder-open text-primary me-2"></i><?php echo htmlspecialchars($catName); ?> Module &mdash; (<?php echo count($perms); ?> Permissions)
                                        </td>
                                    </tr>
                                    <?php foreach($perms as $p): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold text-dark" style="font-size: 0.84rem;">
                                                    <?php echo htmlspecialchars($p->description); ?>
                                                </div>
                                                <code class="text-primary fw-bold" style="font-size: 0.72rem;"><?php echo htmlspecialchars($p->permission_key); ?></code>
                                            </td>
                                            <?php foreach($data['roles'] as $role): 
                                                $isAllowed = isset($data['matrix'][$role->id][$p->id]);
                                                $isSuper = ($role->name === 'super_admin');
                                            ?>
                                                <td class="text-center px-1">
                                                    <input class="form-check-input role-perm-toggle" 
                                                           type="checkbox" 
                                                           name="permissions[<?php echo $role->id; ?>][]" 
                                                           value="<?php echo $p->id; ?>" 
                                                           <?php echo ($isAllowed || $isSuper) ? 'checked' : ''; ?>
                                                           <?php echo ($isSuper) ? 'disabled' : ''; ?>>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-white py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 border-top">
                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <i class="fa fa-shield-alt text-success fs-5"></i>
                            <span>Super Admin always maintains full system bypass. Matrix edits take effect immediately upon saving.</span>
                        </div>
                        <button type="submit" name="update_role_permissions" class="btn btn-success px-4 shadow-sm fw-bold">
                            <i class="fa fa-save me-1"></i> Save Permissions Matrix
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 4: STUDENT PROFILE & FEATURE ENFORCEMENT SWITCHES         -->
        <!-- ============================================================= -->
        <div class="tab-pane fade <?php echo ($activeTab === 'features') ? 'show active' : ''; ?>" id="tab-features" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title fw-bold mb-0 text-primary">
                        <i class="fa fa-toggle-on me-2"></i>Institutional Operational &amp; Profile Enforcement Switches
                    </h5>
                    <small class="text-muted">Turn critical validation rules and cross-module automated synchronizations ON or OFF.</small>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo URLROOT; ?>/setting/index?tab=features" method="post" class="ajax-settings-form">
                        <input type="hidden" name="tab" value="features">
                        <input type="hidden" name="features_setting" value="1">
                        <input type="hidden" name="features_flag" value="1">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                        <div class="row g-4">
                            
                            <!-- Toggle 1: Mandatory B-Form / CNIC -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-white shadow-xs h-100 d-flex align-items-center justify-content-between">
                                    <div class="pe-3">
                                        <div class="fw-bold text-dark mb-1">
                                            <i class="fa fa-id-card text-primary me-2"></i>Mandatory Student B-Form / CNIC
                                        </div>
                                        <div class="small text-muted">
                                            Enforce strict 13-digit National ID / NADRA B-Form verification during student admission registration.
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="toggle_require_bform" value="1" <?php echo (!isset($s['toggle_require_bform']) || $s['toggle_require_bform'] == '1') ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle 2: Mandatory Father CNIC -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-white shadow-xs h-100 d-flex align-items-center justify-content-between">
                                    <div class="pe-3">
                                        <div class="fw-bold text-dark mb-1">
                                            <i class="fa fa-fingerprint text-info me-2"></i>Mandatory Father / Guardian CNIC
                                        </div>
                                        <div class="small text-muted">
                                            Require valid Guardian National Identity Card number before an admission file can be accepted into ledger.
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="toggle_require_father_cnic" value="1" <?php echo (!isset($s['toggle_require_father_cnic']) || $s['toggle_require_father_cnic'] == '1') ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle 3: Sibling Discount Clustering -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-white shadow-xs h-100 d-flex align-items-center justify-content-between">
                                    <div class="pe-3">
                                        <div class="fw-bold text-dark mb-1">
                                            <i class="fa fa-people-arrows text-success me-2"></i>Automatic Sibling Discount Clustering
                                        </div>
                                        <div class="small text-muted">
                                            Auto-link students sharing the same Father CNIC into a family cluster and apply the institutional sibling concession.
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="toggle_sibling_discount" value="1" <?php echo (!isset($s['toggle_sibling_discount']) || $s['toggle_sibling_discount'] == '1') ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle 4: Daily Absence SMS -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-white shadow-xs h-100 d-flex align-items-center justify-content-between">
                                    <div class="pe-3">
                                        <div class="fw-bold text-dark mb-1">
                                            <i class="fa fa-comment-sms text-warning me-2"></i>Automated Daily Absence Alert
                                        </div>
                                        <div class="small text-muted">
                                            Automatically queue SMS and parent portal alerts whenever a student is marked Absent during morning roll-call.
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="toggle_daily_attendance_sms" value="1" <?php echo (!isset($s['toggle_daily_attendance_sms']) || $s['toggle_daily_attendance_sms'] == '1') ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle 5: Fee Sync to Accounting Incomes -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-white shadow-xs h-100 d-flex align-items-center justify-content-between">
                                    <div class="pe-3">
                                        <div class="fw-bold text-dark mb-1">
                                            <i class="fa fa-coins text-danger me-2"></i>Auto-Sync Fee Payments to Incomes Cash Book
                                        </div>
                                        <div class="small text-muted">
                                            Every fee payment collected is automatically posted as a credit entry into the institutional Incomes &amp; Cash Book.
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="toggle_fee_accounting_sync" value="1" <?php echo (!isset($s['toggle_fee_accounting_sync']) || $s['toggle_fee_accounting_sync'] == '1') ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Toggle 6: Strict Clearance Enforcement -->
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3 bg-white shadow-xs h-100 d-flex align-items-center justify-content-between">
                                    <div class="pe-3">
                                        <div class="fw-bold text-dark mb-1">
                                            <i class="fa fa-stamp text-purple me-2"></i>Strict Clearance Enforcement for SLC &amp; Admit Cards
                                        </div>
                                        <div class="small text-muted">
                                            Block generation of School Leaving Certificates and Examination Admit Cards if any library book or accounts dues remain pending.
                                        </div>
                                    </div>
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="toggle_strict_clearance_slc" value="1" <?php echo (!isset($s['toggle_strict_clearance_slc']) || $s['toggle_strict_clearance_slc'] == '1') ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                            </div>

                            <!-- Dedicated Section: Staff Attendance & Payroll Leave Cutting Policy -->
                            <div class="col-12 mt-4">
                                <div class="card border border-primary border-opacity-25 rounded-3 bg-light-subtle shadow-xs">
                                    <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-primary">
                                                <i class="fa fa-money-bill-transfer text-primary me-2"></i>Staff Attendance &amp; Payroll Leave Cutting Policy
                                            </h6>
                                            <small class="text-muted">Configure monthly free leave quota and automated salary deductions per unapproved absence / half-day.</small>
                                        </div>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 font-monospace">
                                            Automated Salary Slip Deduction
                                        </span>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="row g-3 align-items-center">
                                            <!-- Toggle: Enable Leave Cutting -->
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                                    <div class="pe-3">
                                                        <div class="fw-bold text-dark mb-1">
                                                            <i class="fa fa-scissors text-danger me-2"></i>Automated Leave Deduction in Payroll
                                                        </div>
                                                        <div class="small text-muted">
                                                            When enabled, the payroll engine automatically deducts salary for chargeable absences based on the staff attendance register.
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch fs-4 m-0">
                                                        <input class="form-check-input" type="checkbox" name="payroll_leave_cutting_enabled" value="1" <?php echo (!isset($s['payroll_leave_cutting_enabled']) || $s['payroll_leave_cutting_enabled'] == '1') ? 'checked' : ''; ?>>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Free Leaves Quota -->
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded-3 bg-white">
                                                    <label class="form-label text-dark fw-bold small mb-1">
                                                        <i class="fa fa-calendar-check text-success me-2"></i>Allowed Free Leaves Per Month (Quota)
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" name="payroll_free_leaves_per_month" class="form-control fw-bold" min="0" max="31" step="1" value="<?php echo htmlspecialchars($s['payroll_free_leaves_per_month'] ?? '2'); ?>">
                                                        <span class="input-group-text bg-light text-muted small">Days / Month</span>
                                                    </div>
                                                    <div class="form-text text-muted" style="font-size: 0.76rem;">
                                                        First N leaves per month are exempt from deductions. Additional absences will be charged.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Absent Cutting % -->
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded-3 bg-white">
                                                    <label class="form-label text-dark fw-bold small mb-1">
                                                        <i class="fa fa-user-xmark text-danger me-2"></i>Full-Day Absent Deduction Rate
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" name="payroll_absent_cutting_percent" class="form-control fw-bold" min="0" max="100" step="5" value="<?php echo htmlspecialchars($s['payroll_absent_cutting_percent'] ?? '100'); ?>">
                                                        <span class="input-group-text bg-light text-muted small">% of Daily Basic Wage</span>
                                                    </div>
                                                    <div class="form-text text-muted" style="font-size: 0.76rem;">
                                                        Formula: <code>(Basic Salary &divide; 30) &times; Rate%</code> per chargeable absent day.
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Half-Day Cutting % -->
                                            <div class="col-md-6">
                                                <div class="p-3 border rounded-3 bg-white">
                                                    <label class="form-label text-dark fw-bold small mb-1">
                                                        <i class="fa fa-hourglass-half text-warning me-2"></i>Half-Day Deduction Rate
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="number" name="payroll_half_day_cutting_percent" class="form-control fw-bold" min="0" max="100" step="5" value="<?php echo htmlspecialchars($s['payroll_half_day_cutting_percent'] ?? '50'); ?>">
                                                        <span class="input-group-text bg-light text-muted small">% of Daily Basic Wage</span>
                                                    </div>
                                                    <div class="form-text text-muted" style="font-size: 0.76rem;">
                                                        Default is 50% (half of 1 day basic salary cut per late/half-day).
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-end">
                            <button type="submit" name="features_setting" class="btn btn-primary px-4 shadow-sm fw-bold">
                                <i class="fa fa-save me-1"></i> Save Operational Switches
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 5: WEBSITE & PUBLIC PORTAL CMS                            -->
        <!-- ============================================================= -->
        <div class="tab-pane fade <?php echo ($activeTab === 'website') ? 'show active' : ''; ?>" id="tab-website" role="tabpanel">
            <?php $cms = $data['cms_settings'] ?? (object)[]; ?>

            <!-- 1. EXECUTIVE OVERVIEW HERO BANNER -->
            <div class="card border-0 shadow-sm mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); border-radius: 14px;">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-primary bg-opacity-75 px-3 py-1 rounded-pill small">
                                    <i class="fa fa-globe me-1"></i> Public Website CMS &amp; Pages Suite
                                </span>
                                <?php if(($cms->is_active_website ?? 'yes') === 'yes'): ?>
                                    <span class="badge bg-success bg-opacity-75 px-3 py-1 rounded-pill small"><i class="fa fa-check-circle me-1"></i> Public Website Online</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill small"><i class="fa fa-tools me-1"></i> Maintenance Mode</span>
                                <?php endif; ?>
                            </div>
                            <h4 class="fw-bold mb-1 text-white">Front Website, Pages &amp; Branding Command Center</h4>
                            <p class="text-white-50 small mb-0">Manage landing page showcase, visual color theme, custom web pages, and top navigation header in real-time.</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?php echo URLROOT; ?>" target="_blank" class="btn btn-light btn-sm fw-bold px-3 rounded-pill shadow-sm">
                                <i class="fa fa-external-link-alt me-1 text-primary"></i> Preview Live Website
                            </a>
                            <a href="<?php echo URLROOT; ?>/frontcms/pages" class="btn btn-primary btn-sm fw-bold px-3 rounded-pill shadow-sm border-light border-opacity-25">
                                <i class="fa fa-compass me-1"></i> Full Pages Manager
                            </a>
                        </div>
                    </div>

                    <!-- Mini KPI Grid inside header -->
                    <div class="row g-2 mt-3 pt-3 border-top border-white border-opacity-10">
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.08);">
                                <div class="text-white-50 fs-xs text-uppercase fw-semibold">Custom Web Pages</div>
                                <div class="fs-5 fw-bold text-white"><?php echo count($data['front_pages'] ?? []); ?> <span class="fs-xs fw-normal text-white-50">Published</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <?php 
                                $activeMenuCount = 0;
                                if(!empty($data['front_pages'])){
                                    foreach($data['front_pages'] as $p){ if(!empty($p->menu_id)) $activeMenuCount++; }
                                }
                            ?>
                            <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.08);">
                                <div class="text-white-50 fs-xs text-uppercase fw-semibold">Top Navbar Links</div>
                                <div class="fs-5 fw-bold text-white"><?php echo $activeMenuCount; ?> <span class="fs-xs fw-normal text-white-50">Active Links</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.08);">
                                <div class="text-white-50 fs-xs text-uppercase fw-semibold">Active Theme</div>
                                <div class="fs-5 fw-bold text-white text-capitalize"><?php echo ($cms->theme_color ?? 'default') === 'default' ? 'Academic Blue' : htmlspecialchars($cms->theme_color ?? 'Blue'); ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-2 rounded-3" style="background: rgba(255,255,255,0.08);">
                                <div class="text-white-50 fs-xs text-uppercase fw-semibold">Online Admissions</div>
                                <div class="fs-5 fw-bold text-white"><?php echo (($cms->enable_online_admission ?? 'yes') === 'yes') ? '<span class="text-success-light">Active Portal</span>' : '<span class="text-warning">Paused</span>'; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Left Column: Forms for Hero & Website Settings -->
                <div class="col-lg-8">
                    <form action="<?php echo URLROOT; ?>/setting/index?tab=website" method="post" enctype="multipart/form-data" class="ajax-settings-form">
                        <input type="hidden" name="tab" value="website">
                        <input type="hidden" name="website_setting" value="1">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                        <!-- 2. THEME, ACCENT & ONLINE ADMISSIONS CONFIG -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                                <h5 class="card-title fw-bold mb-0 text-primary">
                                    <i class="fa fa-palette me-2"></i>Theme Color, Portal Switches &amp; Inquiry
                                </h5>
                                <span class="badge bg-light text-muted border">Brand Identity</span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-check form-switch fs-6 p-3 border rounded-3 bg-light h-100">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active_website" value="yes" <?php echo (($cms->is_active_website ?? 'yes') === 'yes') ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-bold text-dark mb-0">Public Website</label>
                                            <div class="small text-muted">When disabled, visitors see Maintenance screen.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch fs-6 p-3 border rounded-3 bg-light h-100">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" name="enable_online_admission" value="yes" <?php echo (($cms->enable_online_admission ?? 'yes') === 'yes') ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-bold text-dark mb-0">Online Admissions</label>
                                            <div class="small text-muted">Accept digital parent admission forms.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-switch fs-6 p-3 border rounded-3 bg-light h-100">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" name="enable_fee_structure" value="yes" <?php echo (($cms->enable_fee_structure ?? 'yes') === 'yes') ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-bold text-dark mb-0">Fee Schedule &amp; Calculator</label>
                                            <div class="small text-muted">Public fee tables &amp; interactive estimator.</div>
                                        </div>
                                    </div>

                                    <!-- Interactive Visual Theme Swatches -->
                                    <div class="col-12 mt-2">
                                        <label class="form-label text-dark fw-bold small mb-2"><i class="fa fa-swatchbook text-primary me-2"></i>Website Theme Accent Swatches</label>
                                        <input type="hidden" name="theme_color" id="selectedThemeColorInput" value="<?php echo htmlspecialchars($cms->theme_color ?? 'default'); ?>">
                                        <div class="row g-2">
                                            <!-- Swatch 1: Academic Blue -->
                                            <div class="col-6 col-md-3">
                                                <div class="theme-swatch-card p-3 rounded-3 border text-center cursor-pointer position-relative <?php echo (($cms->theme_color ?? 'default') === 'default') ? 'active-swatch border-primary bg-primary-subtle' : 'bg-white'; ?>" onclick="selectThemeSwatch('default', this)">
                                                    <div class="d-inline-block rounded-circle mb-2 shadow-sm" style="width: 32px; height: 32px; background: #1e40af;"></div>
                                                    <div class="fw-bold small text-dark">Academic Blue</div>
                                                    <div class="fs-xs text-muted">Classic Prestige</div>
                                                    <i class="fa fa-check-circle position-absolute top-0 end-0 m-2 text-primary swatch-check" style="display: <?php echo (($cms->theme_color ?? 'default') === 'default') ? 'block' : 'none'; ?>;"></i>
                                                </div>
                                            </div>
                                            <!-- Swatch 2: Crimson Red -->
                                            <div class="col-6 col-md-3">
                                                <div class="theme-swatch-card p-3 rounded-3 border text-center cursor-pointer position-relative <?php echo (($cms->theme_color ?? '') === 'red') ? 'active-swatch border-danger bg-danger-subtle' : 'bg-white'; ?>" onclick="selectThemeSwatch('red', this)">
                                                    <div class="d-inline-block rounded-circle mb-2 shadow-sm" style="width: 32px; height: 32px; background: #991b1b;"></div>
                                                    <div class="fw-bold small text-dark">Crimson Red</div>
                                                    <div class="fs-xs text-muted">Executive &amp; Bold</div>
                                                    <i class="fa fa-check-circle position-absolute top-0 end-0 m-2 text-danger swatch-check" style="display: <?php echo (($cms->theme_color ?? '') === 'red') ? 'block' : 'none'; ?>;"></i>
                                                </div>
                                            </div>
                                            <!-- Swatch 3: Emerald Green -->
                                            <div class="col-6 col-md-3">
                                                <div class="theme-swatch-card p-3 rounded-3 border text-center cursor-pointer position-relative <?php echo (($cms->theme_color ?? '') === 'green') ? 'active-swatch border-success bg-success-subtle' : 'bg-white'; ?>" onclick="selectThemeSwatch('green', this)">
                                                    <div class="d-inline-block rounded-circle mb-2 shadow-sm" style="width: 32px; height: 32px; background: #065f46;"></div>
                                                    <div class="fw-bold small text-dark">Emerald Green</div>
                                                    <div class="fs-xs text-muted">Cadet &amp; Ecology</div>
                                                    <i class="fa fa-check-circle position-absolute top-0 end-0 m-2 text-success swatch-check" style="display: <?php echo (($cms->theme_color ?? '') === 'green') ? 'block' : 'none'; ?>;"></i>
                                                </div>
                                            </div>
                                            <!-- Swatch 4: Midnight Dark -->
                                            <div class="col-6 col-md-3">
                                                <div class="theme-swatch-card p-3 rounded-3 border text-center cursor-pointer position-relative <?php echo (($cms->theme_color ?? '') === 'dark') ? 'active-swatch border-dark bg-dark-subtle' : 'bg-white'; ?>" onclick="selectThemeSwatch('dark', this)">
                                                    <div class="d-inline-block rounded-circle mb-2 shadow-sm" style="width: 32px; height: 32px; background: #0f172a;"></div>
                                                    <div class="fw-bold small text-dark">Midnight Dark</div>
                                                    <div class="fs-xs text-muted">Modern Tech Navy</div>
                                                    <i class="fa fa-check-circle position-absolute top-0 end-0 m-2 text-dark swatch-check" style="display: <?php echo (($cms->theme_color ?? '') === 'dark') ? 'block' : 'none'; ?>;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-3">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-envelope text-info me-2"></i>Public Inquiry Email</label>
                                        <input type="email" name="school_email" class="form-control" value="<?php echo htmlspecialchars($s['school_email'] ?? 'info@citymodelschool.edu.pk'); ?>">
                                    </div>
                                    <div class="col-md-6 mt-3">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-phone text-success me-2"></i>Public Helpline Phone</label>
                                        <input type="text" name="school_phone" class="form-control" value="<?php echo htmlspecialchars($s['school_phone'] ?? '051-111-222-333'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2.5 EMERGENCY & HIGH-PRIORITY ANNOUNCEMENT BAR CONTROLLER -->
                        <div class="card border-0 shadow-sm mb-4 border-start border-4 border-danger">
                            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                                <h5 class="card-title fw-bold mb-0 text-danger">
                                    <i class="fa fa-triangle-exclamation me-2"></i>Emergency &amp; Holiday Announcement Bar
                                </h5>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 small fw-bold">
                                    Top Header Banner
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch fs-6 p-3 border rounded-3 bg-light h-100">
                                            <input class="form-check-input ms-0 me-2" type="checkbox" name="enable_emergency_alert" value="yes" id="enableEmergencyAlertSwitch" <?php echo (($cms->enable_emergency_alert ?? 'no') === 'yes') ? 'checked' : ''; ?> onchange="updateEmergencyAlertPreview()">
                                            <label class="form-check-label fw-bold text-dark mb-0">Enable Emergency Alert Bar</label>
                                            <div class="small text-muted">Displays an urgent announcement bar at the top of all public pages.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-bold small mb-2"><i class="fa fa-fill-drip text-danger me-1"></i>Alert Color Style</label>
                                        <select name="emergency_alert_bg" id="emergencyAlertBgSelect" class="form-select" onchange="updateEmergencyAlertPreview()">
                                            <option value="danger" <?php echo (($cms->emergency_alert_bg ?? 'danger') === 'danger') ? 'selected' : ''; ?>>🔴 Critical / Emergency Alert (Red)</option>
                                            <option value="warning" <?php echo (($cms->emergency_alert_bg ?? '') === 'warning') ? 'selected' : ''; ?>>🟡 Holiday / Weather Notice (Amber)</option>
                                            <option value="info" <?php echo (($cms->emergency_alert_bg ?? '') === 'info') ? 'selected' : ''; ?>>🔵 General / Campus Update (Blue)</option>
                                            <option value="success" <?php echo (($cms->emergency_alert_bg ?? '') === 'success') ? 'selected' : ''; ?>>🟢 Event / Admissions Open (Green)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-bullhorn text-danger me-1"></i>Urgent Announcement Message</label>
                                        <input type="text" name="emergency_alert_text" id="emergencyAlertTextInput" class="form-control" placeholder="e.g. Heavy Rain Warning: Campus will remain closed on Monday, 15th Sep. Online classes active." value="<?php echo htmlspecialchars($cms->emergency_alert_text ?? ''); ?>" oninput="updateEmergencyAlertPreview()">
                                        <div class="text-muted fs-xs mt-1">Keep it concise and clear for mobile and desktop visitors.</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-link text-primary me-1"></i>Action Link URL (Optional)</label>
                                        <input type="text" name="emergency_alert_link" id="emergencyAlertLinkInput" class="form-control" placeholder="e.g. /home/news or https://..." value="<?php echo htmlspecialchars($cms->emergency_alert_link ?? ''); ?>" oninput="updateEmergencyAlertPreview()">
                                        <div class="text-muted fs-xs mt-1">Leave empty if no button is needed.</div>
                                    </div>

                                    <!-- Live Preview Card -->
                                    <div class="col-12 mt-3">
                                        <label class="form-label text-muted fw-bold fs-xs text-uppercase mb-1"><i class="fa fa-eye me-1"></i>Live Public Preview:</label>
                                        <div id="emergencyPreviewBox" class="p-2 px-3 rounded-3 text-white fw-bold d-flex align-items-center justify-content-between shadow-sm" style="background: #dc2626; font-size: 0.82rem;">
                                            <div class="d-flex align-items-center gap-2 text-truncate">
                                                <span class="badge bg-black bg-opacity-25 text-white" id="emergencyPreviewBadge"><i class="fa fa-triangle-exclamation me-1"></i>URGENT ALERT</span>
                                                <span id="emergencyPreviewText" class="text-truncate"><?php echo htmlspecialchars($cms->emergency_alert_text ?? 'Campus will remain closed tomorrow due to official holiday.'); ?></span>
                                            </div>
                                            <span class="badge bg-white text-dark rounded-pill ms-2" id="emergencyPreviewBtn">View Details &rarr;</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. HOMEPAGE HERO SECTION & VISUAL SHOWCASE -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                                <h5 class="card-title fw-bold mb-0 text-primary">
                                    <i class="fa fa-paint-brush me-2"></i>Homepage Hero Section &amp; Visual Showcase
                                </h5>
                                <span class="badge bg-primary-subtle text-primary border px-2 py-1 small">
                                    <i class="fa fa-star me-1"></i>Landing Page
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <!-- Hero Badge / Tagline -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-award text-warning me-1"></i>Top Badge / Accreditation Pill
                                        </label>
                                        <input type="text" name="hero_badge" class="form-control" placeholder="Nationally Accredited Premier Institution" value="<?php echo htmlspecialchars($s['hero_badge'] ?? 'Nationally Accredited Premier Institution'); ?>">
                                        <div class="text-muted fs-xs mt-1">Small pill displayed directly above the main headline.</div>
                                    </div>

                                    <!-- Main Headline -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-heading text-primary me-1"></i>Hero Main Headline
                                        </label>
                                        <input type="text" name="hero_title" class="form-control" placeholder="Empowering Minds, Shaping Tomorrow's Leaders." value="<?php echo htmlspecialchars($s['hero_title'] ?? 'Empowering Minds, Shaping Tomorrow\'s Leaders.'); ?>">
                                        <div class="text-muted fs-xs mt-1">Tip: Use <code>&lt;span class="highlight-text"&gt;text&lt;/span&gt;</code> for gradient highlight.</div>
                                    </div>

                                    <!-- Subtitle / Description -->
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-align-left text-info me-1"></i>Hero Subtitle / Introductory Description
                                        </label>
                                        <textarea name="hero_subtitle" class="form-control" rows="3" placeholder="A brief welcome note..."><?php echo htmlspecialchars($s['hero_subtitle'] ?? ('Welcome to ' . ($s['school_name'] ?? SITENAME) . ' &mdash; a distinguished sanctuary of academic distinction, technological innovation, and ethical leadership where potential transforms into world-changing achievement.')); ?></textarea>
                                    </div>

                                    <!-- Hero Image Upload & Live Preview -->
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <label class="form-label text-dark fw-bold small mb-2 d-flex align-items-center justify-content-between">
                                                <span><i class="fa fa-image text-success me-1"></i>Hero Visual Showcase Image</span>
                                                <span class="text-muted fw-normal fs-xs">Recommended: 1200x800px (JPG/PNG/WEBP)</span>
                                            </label>
                                            <div class="row align-items-center g-3">
                                                <div class="col-sm-4 text-center">
                                                    <?php 
                                                    $currHeroImg = !empty($s['hero_image']) ? (strpos($s['hero_image'], 'http') === 0 ? $s['hero_image'] : URLROOT . '/' . ltrim($s['hero_image'], '/')) : 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1000&auto=format&fit=crop';
                                                    ?>
                                                    <div class="position-relative d-inline-block w-100">
                                                        <img id="hero_image_preview" src="<?php echo $currHeroImg; ?>" alt="Hero Visual Preview" class="img-fluid rounded-3 border shadow-sm" style="max-height: 140px; width: 100%; object-fit: cover;">
                                                        <span class="badge bg-dark position-absolute bottom-0 start-50 translate-middle-x mb-1 opacity-75" style="font-size: 0.65rem;">Current Image</span>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <input type="file" name="hero_image" id="heroImageFileInput" class="form-control mb-2" accept="image/*">
                                                    <div class="text-muted fs-xs">Choose a new photo from your computer to replace the hero image. It will appear on the homepage immediately after saving.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Floating Overlay Card on Image -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-tag text-purple me-1"></i>Image Floating Card Title
                                        </label>
                                        <input type="text" name="hero_floating_badge" class="form-control" placeholder="Ranked #1 Regional Academy" value="<?php echo htmlspecialchars($s['hero_floating_badge'] ?? 'Ranked #1 Regional Academy'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-subscript text-muted me-1"></i>Image Floating Card Subtext
                                        </label>
                                        <input type="text" name="hero_floating_sub" class="form-control" placeholder="Excellence &amp; STEM Leadership" value="<?php echo htmlspecialchars($s['hero_floating_sub'] ?? 'Excellence & STEM Leadership'); ?>">
                                    </div>

                                    <!-- CTA Buttons -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-link text-primary me-1"></i>Primary CTA Button (Text &amp; Link)
                                        </label>
                                        <div class="input-group">
                                            <input type="text" name="hero_cta_text" class="form-control" placeholder="Apply for Admission" value="<?php echo htmlspecialchars($s['hero_cta_text'] ?? 'Apply for Admission'); ?>" style="max-width: 45%;">
                                            <input type="text" name="hero_cta_link" class="form-control" placeholder="/home/admission" value="<?php echo htmlspecialchars($s['hero_cta_link'] ?? (URLROOT . '/home/admission')); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-compass text-secondary me-1"></i>Secondary CTA Button (Text &amp; Link)
                                        </label>
                                        <div class="input-group">
                                            <input type="text" name="hero_cta_sec_text" class="form-control" placeholder="Explore Programs" value="<?php echo htmlspecialchars($s['hero_cta_sec_text'] ?? 'Explore Programs'); ?>" style="max-width: 45%;">
                                            <input type="text" name="hero_cta_sec_link" class="form-control" placeholder="#academics" value="<?php echo htmlspecialchars($s['hero_cta_sec_link'] ?? '#academics'); ?>">
                                        </div>
                                    </div>

                                    <!-- 4 Achievement Key Metric Counters -->
                                    <div class="col-12 mt-3">
                                        <label class="form-label text-dark fw-bold small mb-2">
                                            <i class="fa fa-chart-line text-success me-1"></i>Hero Metric Counters (4 Quick Badges)
                                        </label>
                                        <div class="row g-2">
                                            <div class="col-6 col-md-3">
                                                <div class="p-2 border rounded bg-light">
                                                    <input type="text" name="hero_stat_1_val" class="form-control form-control-sm fw-bold mb-1" placeholder="99.8%" value="<?php echo htmlspecialchars($s['hero_stat_1_val'] ?? '99.8%'); ?>">
                                                    <input type="text" name="hero_stat_1_lbl" class="form-control form-control-sm text-muted" placeholder="Graduation Rate" value="<?php echo htmlspecialchars($s['hero_stat_1_lbl'] ?? 'Graduation Rate'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="p-2 border rounded bg-light">
                                                    <input type="text" name="hero_stat_2_val" class="form-control form-control-sm fw-bold mb-1" placeholder="14 : 1" value="<?php echo htmlspecialchars($s['hero_stat_2_val'] ?? '14 : 1'); ?>">
                                                    <input type="text" name="hero_stat_2_lbl" class="form-control form-control-sm text-muted" placeholder="Student / Teacher" value="<?php echo htmlspecialchars($s['hero_stat_2_lbl'] ?? 'Student / Teacher'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="p-2 border rounded bg-light">
                                                    <input type="text" name="hero_stat_3_val" class="form-control form-control-sm fw-bold mb-1" placeholder="45+" value="<?php echo htmlspecialchars($s['hero_stat_3_val'] ?? '45+'); ?>">
                                                    <input type="text" name="hero_stat_3_lbl" class="form-control form-control-sm text-muted" placeholder="Honors & AP Courses" value="<?php echo htmlspecialchars($s['hero_stat_3_lbl'] ?? 'Honors & AP Courses'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="p-2 border rounded bg-light">
                                                    <input type="text" name="hero_stat_4_val" class="form-control form-control-sm fw-bold mb-1" placeholder="100%" value="<?php echo htmlspecialchars($s['hero_stat_4_val'] ?? '100%'); ?>">
                                                    <input type="text" name="hero_stat_4_lbl" class="form-control form-control-sm text-muted" placeholder="College Placement" value="<?php echo htmlspecialchars($s['hero_stat_4_lbl'] ?? 'College Placement'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. SOCIAL MEDIA CONNECTIVITY & FOOTER TAGLINE -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="card-title fw-bold mb-0 text-primary">
                                    <i class="fa fa-share-nodes me-2"></i>Social Channels &amp; Footer Branding
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fab fa-facebook text-primary me-2"></i>Facebook Page URL</label>
                                        <input type="url" name="facebook_url" class="form-control" placeholder="https://facebook.com/your-school" value="<?php echo htmlspecialchars($cms->facebook_url ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fab fa-twitter text-info me-2"></i>Twitter / X Profile</label>
                                        <input type="url" name="twitter_url" class="form-control" placeholder="https://twitter.com/your-school" value="<?php echo htmlspecialchars($cms->twitter_url ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fab fa-instagram text-danger me-2"></i>Instagram URL</label>
                                        <input type="url" name="instagram_url" class="form-control" placeholder="https://instagram.com/your-school" value="<?php echo htmlspecialchars($cms->instagram_url ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fab fa-youtube text-danger me-2"></i>YouTube Channel</label>
                                        <input type="url" name="youtube_url" class="form-control" placeholder="https://youtube.com/your-school" value="<?php echo htmlspecialchars($cms->youtube_url ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fab fa-linkedin text-primary me-2"></i>LinkedIn Company</label>
                                        <input type="url" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/school/your-school" value="<?php echo htmlspecialchars($cms->linkedin_url ?? ''); ?>">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label text-dark fw-semibold small mb-1"><i class="fa fa-quote-left text-warning me-2"></i>Public Footer Tagline / Text</label>
                                        <textarea name="footer_text" class="form-control" rows="2"><?php echo htmlspecialchars($cms->footer_text ?? 'Dedicated to academic distinction, innovative leadership, and holistic character development.'); ?></textarea>
                                    </div>
                                </div>

                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">
                                        <i class="fa fa-info-circle me-1 text-primary"></i> Theme, Hero and social details update instantly on the public website.
                                    </span>
                                    <button type="submit" name="website_setting" class="btn btn-primary px-4 shadow-sm fw-bold">
                                        <i class="fa fa-save me-1"></i> Save Website &amp; Hero Settings
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Dedicated Pages Directory & CMS Launcher Suite -->
                <div class="col-lg-4">
                    <!-- 5. DEDICATED WEBSITE PAGES & NAVBAR DIRECTORY (IN SITE SETTINGS) -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="fa fa-compass text-primary me-2"></i>Website Pages Directory
                                </h6>
                                <span class="text-muted smaller" style="font-size: 0.72rem;">Pages &amp; Navbar status</span>
                            </div>
                            <a href="<?php echo URLROOT; ?>/frontcms/pages?tab=create" class="btn btn-xs btn-primary rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">
                                <i class="fa fa-plus me-1"></i> New Page
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" style="max-height: 380px; overflow-y: auto;">
                                <?php if(!empty($data['front_pages'])): ?>
                                    <?php foreach($data['front_pages'] as $pg): 
                                        $hasM = !empty($pg->menu_id);
                                        $pgGroup = $pg->dropdown_group ?? 'none';
                                        $pgUrl = in_array($pg->slug, ['academics', 'facilities', 'events', 'gallery', 'news', 'fees']) 
                                            ? URLROOT . '/home/' . $pg->slug 
                                            : URLROOT . '/home/page/' . htmlspecialchars($pg->slug, ENT_QUOTES, 'UTF-8');
                                    ?>
                                        <div class="list-group-item p-3 d-flex align-items-center justify-content-between gap-2">
                                            <div class="text-truncate">
                                                <div class="fw-bold text-dark small text-truncate"><?php echo htmlspecialchars($pg->title, ENT_QUOTES, 'UTF-8'); ?></div>
                                                <div class="d-flex align-items-center gap-1 mt-1">
                                                    <?php if($hasM): ?>
                                                        <?php if($pgGroup !== 'none'): ?>
                                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.68rem;">
                                                                <i class="fa fa-layer-group me-1"></i> <?php echo ucfirst($pgGroup); ?> (#<?php echo $pg->menu_order; ?>)
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.68rem;">
                                                                <i class="fa fa-check-circle me-1"></i> Top Nav (#<?php echo $pg->menu_order; ?>)
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">Direct URL only</span>
                                                    <?php endif; ?>
                                                    <?php if(!empty($pg->file_path)): ?>
                                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size: 0.68rem;"><i class="fa fa-file-pdf"></i> PDF</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                                <a href="<?php echo $pgUrl; ?>" target="_blank" class="btn btn-xs btn-light border py-1 px-2" style="font-size: 0.72rem;" title="Preview Public Page">
                                                    <i class="fa fa-external-link-alt text-muted"></i>
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/frontcms/pages/edit/<?php echo $pg->id; ?>" class="btn btn-xs btn-outline-primary py-1 px-2 fw-bold" style="font-size: 0.72rem;" title="Edit Page &amp; Menu">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4 text-muted small">No custom pages created yet.</div>
                                <?php endif; ?>
                            </div>
                            <div class="p-2 border-top bg-light text-center">
                                <a href="<?php echo URLROOT; ?>/frontcms/pages" class="btn btn-sm btn-link text-decoration-none fw-bold small p-0">
                                    Open Full Pages Manager &amp; Menus &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 6. FRONT CMS CONTENT MANAGEMENT SUITE CARDS -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-layer-group text-primary me-2"></i>Runtime CMS Modules Suite
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex flex-column gap-2">
                                <!-- 1. Online Admission Inbox -->
                                <div class="p-2 border rounded-3 bg-primary-subtle d-flex align-items-center justify-content-between">
                                    <div class="pe-2">
                                        <div class="fw-bold text-primary small"><i class="fa fa-inbox me-1"></i>Online Admission Inbox</div>
                                        <div class="text-muted smaller" style="font-size: 0.75rem;">View &amp; convert submitted admission forms</div>
                                    </div>
                                    <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions" class="btn btn-sm btn-primary fw-bold text-nowrap" style="font-size: 0.78rem;">
                                        Open Inbox <i class="fa fa-arrow-right ms-1"></i>
                                    </a>
                                </div>

                                <!-- 1.5 Tuition & Fee Structure -->
                                <div class="p-2 border rounded-3 bg-success-subtle d-flex align-items-center justify-content-between">
                                    <div class="pe-2">
                                        <div class="fw-bold text-success small"><i class="fa fa-receipt me-1"></i>Tuition &amp; Fee Structure</div>
                                        <div class="text-muted smaller" style="font-size: 0.75rem;">Grade pricing, fee calculator &amp; bank challans</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo URLROOT; ?>/home/fees" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2 fw-bold" style="font-size: 0.75rem;" title="View Live Fee Page">
                                            <i class="fa fa-external-link-alt me-1"></i>Live
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/frontcms/pages" class="btn btn-sm btn-success py-1 px-2 fw-bold" style="font-size: 0.75rem;" title="Manage in CMS">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- 2. Events & Programs -->
                                <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                    <div class="pe-2">
                                        <div class="fw-bold text-dark small"><i class="fa fa-calendar-alt text-warning me-1"></i>Events &amp; Programs</div>
                                        <div class="text-muted smaller" style="font-size: 0.75rem;">Schedules, galas &amp; academic calendar</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning text-dark py-1 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#cmsAddEventModal">
                                            <i class="fa fa-plus me-1"></i>Modal
                                        </button>
                                        <a href="<?php echo URLROOT; ?>/frontcms/events" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.75rem;" title="Full Events Roster">
                                            <i class="fa fa-list"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- 3. Custom Runtime Pages -->
                                <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                    <div class="pe-2">
                                        <div class="fw-bold text-dark small"><i class="fa fa-file-code text-success me-1"></i>Custom Runtime Pages</div>
                                        <div class="text-muted smaller" style="font-size: 0.75rem;">Create About Us, Policies &amp; Custom URLs</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#cmsAddPageModal">
                                            <i class="fa fa-plus me-1"></i>Modal
                                        </button>
                                        <a href="<?php echo URLROOT; ?>/frontcms/pages" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.75rem;" title="Full Pages Roster">
                                            <i class="fa fa-list"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- 4. Photo Gallery Albums -->
                                <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                    <div class="pe-2">
                                        <div class="fw-bold text-dark small"><i class="fa fa-camera-retro text-danger me-1"></i>Photo Gallery Albums</div>
                                        <div class="text-muted smaller" style="font-size: 0.75rem;">Upload campus showcase pictures &amp; text</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#cmsUploadGalleryModal">
                                            <i class="fa fa-upload me-1"></i>Modal
                                        </button>
                                        <a href="<?php echo URLROOT; ?>/frontcms/gallery" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.75rem;" title="Full Gallery Roster">
                                            <i class="fa fa-list"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- 5. News & Announcements -->
                                <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                    <div class="pe-2">
                                        <div class="fw-bold text-dark small"><i class="fa fa-newspaper text-info me-1"></i>News &amp; Announcements</div>
                                        <div class="text-muted smaller" style="font-size: 0.75rem;">Campus headlines, stories &amp; circulars</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-info py-1 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#cmsAddNewsModal">
                                            <i class="fa fa-plus me-1"></i>Modal
                                        </button>
                                        <a href="<?php echo URLROOT; ?>/frontcms/news" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.75rem;" title="Full News Roster">
                                            <i class="fa fa-list"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- 6. Homepage Banner Sliders -->
                                <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                    <div class="pe-2">
                                        <div class="fw-bold text-dark small"><i class="fa fa-image text-purple me-1"></i>Homepage Banner Sliders</div>
                                        <div class="text-muted smaller" style="font-size: 0.75rem;">Carousel sliders &amp; banner images</div>
                                    </div>
                                    <a href="<?php echo URLROOT; ?>/frontcms/banners" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.75rem;">
                                        Manage Sliders <i class="fa fa-chevron-right ms-1"></i>
                                    </a>
                                </div>

                                <!-- 7. Navbar Navigation Menus -->
                                <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                    <div class="pe-2">
                                        <div class="fw-bold text-dark small"><i class="fa fa-bars text-secondary me-1"></i>Navbar Navigation Menus</div>
                                        <div class="text-muted smaller" style="font-size: 0.75rem;">Header nav items &amp; page links</div>
                                    </div>
                                    <a href="<?php echo URLROOT; ?>/frontcms/menus" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.75rem;">
                                        Manage Menus <i class="fa fa-chevron-right ms-1"></i>
                                    </a>
                                </div>

                                <!-- 8. Alumni Network & Hall of Fame -->
                                <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                    <div class="pe-2">
                                        <div class="fw-bold text-dark small"><i class="fa fa-user-graduate text-primary me-1"></i>Alumni Network &amp; Directory</div>
                                        <div class="text-muted smaller" style="font-size: 0.75rem;">Manage graduates, batches &amp; stories</div>
                                    </div>
                                    <a href="<?php echo URLROOT; ?>/frontcms/alumni" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.75rem;">
                                        Manage Alumni <i class="fa fa-chevron-right ms-1"></i>
                                    </a>
                                </div>

                                <!-- 9. School Requirements, Careers & Tenders -->
                                <div class="p-2 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                                    <div class="pe-2">
                                        <div class="fw-bold text-dark small"><i class="fa fa-briefcase text-success me-1"></i>Requirements, Careers &amp; Tenders</div>
                                        <div class="text-muted smaller" style="font-size: 0.75rem;">Post jobs, tenders &amp; emergency notices</div>
                                    </div>
                                    <a href="<?php echo URLROOT; ?>/frontcms/requirements" class="btn btn-sm btn-light border py-1 px-2" style="font-size: 0.75rem;">
                                        Manage Postings <i class="fa fa-chevron-right ms-1"></i>
                                    </a>
                                </div>
                            </div>

                            <hr class="my-3">
                            <a href="<?php echo URLROOT; ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100 fw-bold">
                                <i class="fa fa-external-link-alt me-1"></i> Preview Public Website
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 6: EMAIL & SMTP GATEWAY CONFIGURATION                     -->
        <!-- ============================================================= -->
        <div class="tab-pane fade <?php echo ($activeTab === 'email') ? 'show active' : ''; ?>" id="tab-email" role="tabpanel">
            <div class="row g-4">
                <!-- Left Column: Primary SMTP Configuration Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h5 class="card-title fw-bold mb-0 text-primary">
                                <i class="fa fa-envelope me-2"></i>Outgoing SMTP Mail Server Configuration
                            </h5>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small">
                                <i class="fa fa-shield-alt me-1"></i>Secured Mail Transport
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <!-- Quick Presets -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 rounded-3 bg-light border gap-2">
                                <div>
                                    <span class="fw-bold text-dark small"><i class="fa fa-magic text-primary me-2"></i>Quick Configuration Presets:</span>
                                    <span class="text-muted small ms-1 d-none d-md-inline">Auto-fill recommended port and encryption settings</span>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-danger" id="presetGmailBtn">
                                        <i class="fab fa-google me-1"></i> Gmail / Workspace
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="presetOfficeBtn">
                                        <i class="fab fa-microsoft me-1"></i> Outlook / 365
                                    </button>
                                </div>
                            </div>

                            <form action="<?php echo URLROOT; ?>/setting/index?tab=email" method="post" class="ajax-settings-form" id="smtpSettingsForm">
                                <input type="hidden" name="tab" value="email">
                                <input type="hidden" name="email_setting" value="1">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                                <div class="row g-3">
                                    <!-- Enable SMTP Switch -->
                                    <div class="col-12">
                                        <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                            <input class="form-check-input ms-0 me-3" type="checkbox" name="smtp_enabled" value="1" id="smtpEnabledCheck" <?php echo (($s['smtp_enabled'] ?? '1') !== '0') ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-bold text-dark cursor-pointer" for="smtpEnabledCheck">
                                                Enable Outgoing SMTP Mail Delivery
                                                <span class="d-block text-muted small fw-normal">When enabled, system dispatches account passwords, staff credentials, admission notices, and password reset PINs via SMTP.</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- SMTP Host -->
                                    <div class="col-md-7">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-server text-primary me-2"></i>SMTP Server Host <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="smtp_host" id="smtpHostInput" class="form-control font-monospace" required 
                                               value="<?php echo htmlspecialchars($s['smtp_host'] ?? 'smtp.gmail.com'); ?>" 
                                               placeholder="e.g. smtp.gmail.com">
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">For Gmail, use <code>smtp.gmail.com</code>. For Office365, use <code>smtp.office365.com</code>.</div>
                                    </div>

                                    <!-- SMTP Port & Encryption -->
                                    <div class="col-md-5">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-network-wired text-info me-2"></i>Port &amp; Protocol <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="smtp_port" id="smtpPortInput" class="form-control font-monospace" required min="1" max="65535" 
                                                   value="<?php echo htmlspecialchars($s['smtp_port'] ?? '587'); ?>">
                                            <select name="smtp_encryption" id="smtpEncryptionSelect" class="form-select text-uppercase" style="max-width: 110px;">
                                                <option value="tls" <?php echo (strtolower($s['smtp_encryption'] ?? 'tls') === 'tls') ? 'selected' : ''; ?>>TLS</option>
                                                <option value="ssl" <?php echo (strtolower($s['smtp_encryption'] ?? 'tls') === 'ssl') ? 'selected' : ''; ?>>SSL</option>
                                                <option value="none" <?php echo (strtolower($s['smtp_encryption'] ?? 'tls') === 'none') ? 'selected' : ''; ?>>None</option>
                                            </select>
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">Port 587 (TLS recommended) or 465 (SSL).</div>
                                    </div>

                                    <!-- Username -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-user-circle text-success me-2"></i>SMTP Username / Account Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="smtp_username" id="smtpUsernameInput" class="form-control" required 
                                               value="<?php echo htmlspecialchars($s['smtp_username'] ?? ''); ?>" 
                                               placeholder="e.g. your-school@gmail.com">
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">Full email address used to authenticate on your SMTP provider.</div>
                                    </div>

                                    <!-- Password -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-key text-warning me-2"></i>SMTP Password / App Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" name="smtp_password" id="smtpPasswordInput" class="form-control font-monospace" 
                                                   value="<?php echo !empty($s['smtp_password']) ? htmlspecialchars($s['smtp_password']) : ''; ?>" 
                                                   placeholder="<?php echo !empty($s['smtp_password']) ? '•••••••••••••••• (Leave blank to keep current)' : 'Enter 16-character App Password'; ?>">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordVisibilityBtn" title="Show / Hide Password">
                                                <i class="fa fa-eye" id="togglePasswordEyeIcon"></i>
                                            </button>
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">For Gmail, generate and use a 16-char <strong>Google App Password</strong> (spaces auto-removed).</div>
                                    </div>

                                    <!-- From Email -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-at text-secondary me-2"></i>Sender Email Address (From Email)
                                        </label>
                                        <input type="email" name="smtp_from_email" id="smtpFromEmailInput" class="form-control" 
                                               value="<?php echo htmlspecialchars($s['smtp_from_email'] ?? ($s['smtp_username'] ?? '')); ?>" 
                                               placeholder="e.g. no-reply@school.edu.pk">
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">The address recipients see in the 'From' header.</div>
                                    </div>

                                    <!-- From Name -->
                                    <div class="col-md-6">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-signature text-secondary me-2"></i>Sender Display Name (From Name)
                                        </label>
                                        <input type="text" name="smtp_from_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($s['smtp_from_name'] ?? ($s['school_name'] ?? 'City Model High School & College')); ?>" 
                                               placeholder="e.g. City Model High School">
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">Human-readable sender name displayed in inboxes.</div>
                                    </div>

                                    <!-- Timeout -->
                                    <div class="col-md-4">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-stopwatch text-danger me-2"></i>Socket Timeout (Seconds)
                                        </label>
                                        <input type="number" name="smtp_timeout" class="form-control font-monospace" min="15" max="60" 
                                               value="<?php echo htmlspecialchars(max(15, (int)($s['smtp_timeout'] ?? 15))); ?>">
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">Connection wait time before timeout (default: 15s).</div>
                                    </div>

                                    <!-- SSL Mode Notice -->
                                    <div class="col-md-8 d-flex align-items-center">
                                        <div class="alert alert-light border small text-muted mb-0 w-100 py-2">
                                            <i class="fa fa-info-circle text-primary me-1"></i>
                                            Windows/XAMPP SSL stream context overrides are automatically applied, ensuring no local certificate verification hangs or socket aborts.
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">
                                        <i class="fa fa-info-circle me-1"></i> Changes take effect immediately across all system modules.
                                    </span>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                                        <i class="fa fa-save me-2"></i>Save SMTP Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Diagnostics & Quick Test Tool & Help Guide -->
                <div class="col-lg-4">
                    <!-- Test Email Card -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h6 class="card-title fw-bold mb-0 text-success">
                                <i class="fa fa-paper-plane me-2"></i>Send Test Email
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-3">
                                Test your SMTP configuration immediately. A live verification email will be dispatched through your configured mail server.
                            </p>
                            <form action="<?php echo URLROOT; ?>/setting/index?tab=email" method="post" class="ajax-settings-form" id="smtpTestForm">
                                <input type="hidden" name="tab" value="email">
                                <input type="hidden" name="test_email_submit" value="1">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                                <div class="mb-3">
                                    <label class="form-label text-dark fw-semibold small mb-1">
                                        <i class="fa fa-inbox text-primary me-2"></i>Recipient Email Address
                                    </label>
                                    <input type="email" name="test_recipient_email" class="form-control" required 
                                           value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" 
                                           placeholder="e.g. principal@gmail.com">
                                    <div class="form-text text-muted" style="font-size: 0.78rem;">We will send a formatted diagnostic test email here.</div>
                                </div>

                                <button type="submit" class="btn btn-success w-100 fw-bold">
                                    <i class="fa fa-paper-plane me-2"></i>Send Verification Test
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Gmail App Password Instruction Guide Card -->
                    <div class="card border-0 shadow-sm bg-light">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                <i class="fab fa-google text-danger me-2 fs-5"></i> Gmail App Password Setup
                            </h6>
                            <p class="text-muted small mb-2">
                                Standard Google account passwords are not permitted by Gmail's secure SMTP policy. Follow these 4 quick steps:
                            </p>
                            <ol class="ps-3 text-muted small mb-3" style="line-height: 1.6;">
                                <li class="mb-1">Go to your Google Account at <a href="https://myaccount.google.com/security" target="_blank" class="text-decoration-none fw-semibold">Google Security <i class="fa fa-external-link-alt" style="font-size:0.7rem;"></i></a> and ensure <strong>2-Step Verification</strong> is ON.</li>
                                <li class="mb-1">Search or visit <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-decoration-none fw-semibold">App Passwords <i class="fa fa-external-link-alt" style="font-size:0.7rem;"></i></a>.</li>
                                <li class="mb-1">Enter App name: <code>School ERP</code> and click <strong>Create</strong>.</li>
                                <li class="mb-1">Copy the <strong>16-character code</strong> (e.g. <code>abcd efgh ijkl mnop</code>) and paste it into the <strong>SMTP Password</strong> field on the left.</li>
                            </ol>
                            <div class="p-2 rounded bg-white border border-warning-subtle small text-dark">
                                <i class="fa fa-check-circle text-success me-1"></i>
                                Spaces in the 16-character code are automatically stripped by the ERP core.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================= -->
        <!-- TAB 7: WHATSAPP COMMUNICATION & RUNTIME LIVE CHAT API         -->
        <!-- ============================================================= -->
        <div class="tab-pane fade <?php echo ($activeTab === 'livechat') ? 'show active' : ''; ?>" id="tab-livechat" role="tabpanel">
            <form action="<?php echo URLROOT; ?>/setting/index?tab=livechat" method="post" class="ajax-settings-form" id="livechatSettingsForm">
                <input type="hidden" name="tab" value="livechat">
                <input type="hidden" name="livechat_setting" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                <div class="row g-4">
                    <!-- Left Column: WhatsApp Configuration -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                                <h5 class="card-title fw-bold mb-0 text-success d-flex align-items-center">
                                    <i class="fab fa-whatsapp me-2 fs-4"></i>WhatsApp Floating Helpdesk
                                </h5>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill small">
                                    <i class="fa fa-bolt me-1"></i>Direct Contact Channel
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <p class="text-muted small mb-4">
                                    Displays a high-conversion floating WhatsApp button on every public website page, enabling prospective parents and students to start an immediate chat with your admissions office.
                                </p>

                                <div class="row g-3">
                                    <!-- Enable Switch -->
                                    <div class="col-12">
                                        <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                            <input class="form-check-input ms-0 me-3" type="checkbox" name="whatsapp_enabled" value="1" id="whatsappEnabledCheck" <?php echo (($s['whatsapp_enabled'] ?? '1') !== '0') ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-bold text-dark cursor-pointer" for="whatsappEnabledCheck">
                                                Enable Floating WhatsApp Button
                                                <span class="d-block text-muted small fw-normal">Shows the floating WhatsApp launcher at the bottom-right corner of the public website.</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="col-md-12">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fab fa-whatsapp text-success me-1"></i>WhatsApp Contact Number <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i class="fa fa-phone text-success"></i></span>
                                            <input type="text" name="whatsapp_number" id="whatsappNumberInput" class="form-control border-start-0 font-monospace" 
                                                   value="<?php echo htmlspecialchars($s['whatsapp_number'] ?? '+92-300-1234567'); ?>" 
                                                   placeholder="e.g. +923001234567 or 03001234567">
                                        </div>
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">
                                            Include country code (e.g. <code>+923001234567</code> for Pakistan). Spaces and hyphens will be automatically formatted for the WhatsApp API.
                                        </div>
                                    </div>

                                    <!-- Desk / Agent Display Name -->
                                    <div class="col-md-12">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-user-tie text-primary me-1"></i>Helpdesk / Representative Title
                                        </label>
                                        <input type="text" name="whatsapp_agent_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($s['whatsapp_agent_name'] ?? 'Admissions & Helpdesk'); ?>" 
                                               placeholder="e.g. Admissions & Student Helpdesk">
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">
                                            Displayed as the agent title inside the interactive WhatsApp quick chat card.
                                        </div>
                                    </div>

                                    <!-- Popup Mode Switch -->
                                    <div class="col-12">
                                        <div class="form-check form-switch p-3 bg-white rounded-3 border">
                                            <input class="form-check-input ms-0 me-3" type="checkbox" name="whatsapp_popup_enabled" value="1" id="whatsappPopupCheck" <?php echo (($s['whatsapp_popup_enabled'] ?? '1') !== '0') ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-bold text-dark cursor-pointer" for="whatsappPopupCheck">
                                                Enable Interactive Quick-Chat Card
                                                <span class="d-block text-muted small fw-normal">When clicked, opens a sleek WhatsApp prompt card with quick-topic pills (Admissions, Fees, Timings) before redirecting to WhatsApp.</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Default Message -->
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-comment-dots text-secondary me-1"></i>Default Prefilled Message
                                        </label>
                                        <textarea name="whatsapp_default_msg" rows="3" class="form-control" placeholder="Hello! I would like to inquire about admissions..."><?php echo htmlspecialchars($s['whatsapp_default_msg'] ?? 'Hello! I would like to inquire about admissions and school programs.'); ?></textarea>
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">
                                            Pre-populated in the user's WhatsApp message input when they initiate contact.
                                        </div>
                                    </div>

                                    <!-- Live Test Link Button -->
                                    <div class="col-12 pt-2">
                                        <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="fw-bold small text-dark"><i class="fa fa-paper-plane text-success me-1"></i>Test Current Link</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">Opens WhatsApp in a new tab with your configured settings.</div>
                                            </div>
                                            <?php 
                                            $cleanWpNum = preg_replace('/[^0-9]/', '', $s['whatsapp_number'] ?? '923001234567');
                                            $wpDefaultMsg = rawurlencode($s['whatsapp_default_msg'] ?? 'Hello! I would like to inquire about admissions.');
                                            $wpTestUrl = "https://api.whatsapp.com/send?phone={$cleanWpNum}&text={$wpDefaultMsg}";
                                            ?>
                                            <a href="<?php echo $wpTestUrl; ?>" target="_blank" class="btn btn-sm btn-outline-success fw-bold">
                                                <i class="fab fa-whatsapp me-1"></i>Test WhatsApp Link
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Live Chat Configuration & Runtime API -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                                <h5 class="card-title fw-bold mb-0 text-primary d-flex align-items-center">
                                    <i class="fa fa-comments me-2 fs-4"></i>Live Chat System &amp; Runtime API
                                </h5>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill small">
                                    <i class="fa fa-robot me-1"></i>Multi-Provider Support
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <p class="text-muted small mb-4">
                                    Deploy an automated smart helpdesk assistant or connect third-party live chat services (Tawk.to, Crisp, or Custom Script) dynamically at runtime.
                                </p>

                                <div class="row g-3">
                                    <!-- Enable Switch -->
                                    <div class="col-12">
                                        <div class="form-check form-switch p-3 bg-light rounded-3 border">
                                            <input class="form-check-input ms-0 me-3" type="checkbox" name="livechat_enabled" value="1" id="livechatEnabledCheck" <?php echo (($s['livechat_enabled'] ?? '1') !== '0') ? 'checked' : ''; ?>>
                                            <label class="form-check-label fw-bold text-dark cursor-pointer" for="livechatEnabledCheck">
                                                Enable Website Live Chat Widget
                                                <span class="d-block text-muted small fw-normal">Displays an interactive floating Live Chat button and widget on the public website.</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Provider Selection -->
                                    <div class="col-12">
                                        <label class="form-label text-dark fw-semibold small mb-1">
                                            <i class="fa fa-sliders-h text-primary me-1"></i>Live Chat Engine / Provider <span class="text-danger">*</span>
                                        </label>
                                        <?php $currentProvider = $s['livechat_provider'] ?? 'builtin'; ?>
                                        <select name="livechat_provider" id="livechatProviderSelect" class="form-select fw-semibold">
                                            <option value="builtin" <?php echo ($currentProvider === 'builtin') ? 'selected' : ''; ?>>
                                                🤖 Built-in Smart School Assistant (Instant, Zero Setup, CRM Integrated)
                                            </option>
                                            <option value="tawk" <?php echo ($currentProvider === 'tawk') ? 'selected' : ''; ?>>
                                                💬 Tawk.to Live Chat (Free Agent Live Chat)
                                            </option>
                                            <option value="crisp" <?php echo ($currentProvider === 'crisp') ? 'selected' : ''; ?>>
                                                ⚡ Crisp Chat (Modern Omnichannel Chat)
                                            </option>
                                            <option value="custom" <?php echo ($currentProvider === 'custom') ? 'selected' : ''; ?>>
                                                🔧 Custom Script / Third-Party Embed Code
                                            </option>
                                        </select>
                                        <div class="form-text text-muted" style="font-size: 0.78rem;">
                                            Select the chat engine to load on the frontend.
                                        </div>
                                    </div>

                                    <!-- Section A: Built-in Smart Assistant Fields -->
                                    <div class="col-12 provider-fields" id="fieldsBuiltin" style="<?php echo ($currentProvider === 'builtin') ? '' : 'display: none;'; ?>">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <h6 class="fw-bold text-dark mb-2 small"><i class="fa fa-magic text-primary me-1"></i>Smart School Assistant Customization</h6>
                                            <div class="mb-3">
                                                <label class="form-label text-dark small mb-1 fw-semibold">Widget Header Title</label>
                                                <input type="text" name="livechat_welcome_title" class="form-control form-control-sm" 
                                                       value="<?php echo htmlspecialchars($s['livechat_welcome_title'] ?? 'Live School Support'); ?>" 
                                                       placeholder="e.g. Live School Support">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label text-dark small mb-1 fw-semibold">Welcome Greeting Message</label>
                                                <textarea name="livechat_welcome_msg" rows="2" class="form-control form-control-sm" placeholder="Welcome message..."><?php echo htmlspecialchars($s['livechat_welcome_msg'] ?? 'Hello! Welcome to our school helpdesk. How can we assist you today?'); ?></textarea>
                                            </div>
                                            <div class="alert alert-info py-2 px-3 mb-0 small" style="font-size: 0.76rem;">
                                                <i class="fa fa-info-circle me-1"></i>
                                                <strong>Auto-CRM Sync:</strong> When visitors submit their name &amp; phone in the chat, it automatically creates a new lead in <a href="<?php echo URLROOT; ?>/frontoffice/enquiry" class="alert-link" target="_blank">Admission Enquiries</a>!
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section B: Tawk.to Fields -->
                                    <div class="col-12 provider-fields" id="fieldsTawk" style="<?php echo ($currentProvider === 'tawk') ? '' : 'display: none;'; ?>">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <h6 class="fw-bold text-dark mb-2 small"><i class="fa fa-comment-dots text-success me-1"></i>Tawk.to Account Credentials</h6>
                                            <div class="row g-2 mb-2">
                                                <div class="col-md-6">
                                                    <label class="form-label text-dark small mb-1 fw-semibold">Tawk.to Property ID</label>
                                                    <input type="text" name="livechat_tawk_property_id" class="form-control form-control-sm font-monospace" 
                                                           value="<?php echo htmlspecialchars($s['livechat_tawk_property_id'] ?? ''); ?>" 
                                                           placeholder="e.g. 64a8b1c2d3e4f5...">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-dark small mb-1 fw-semibold">Tawk.to Widget ID</label>
                                                    <input type="text" name="livechat_tawk_widget_id" class="form-control form-control-sm font-monospace" 
                                                           value="<?php echo htmlspecialchars($s['livechat_tawk_widget_id'] ?? ''); ?>" 
                                                           placeholder="e.g. 1h4j5k6l7">
                                                </div>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                Found in Tawk.to Dashboard &gt; Administration &gt; Chat Widget code (format: <code>embed.tawk.to/{property_id}/{widget_id}</code>).
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section C: Crisp Chat Fields -->
                                    <div class="col-12 provider-fields" id="fieldsCrisp" style="<?php echo ($currentProvider === 'crisp') ? '' : 'display: none;'; ?>">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <h6 class="fw-bold text-dark mb-2 small"><i class="fa fa-bolt text-primary me-1"></i>Crisp Chat Configuration</h6>
                                            <div class="mb-2">
                                                <label class="form-label text-dark small mb-1 fw-semibold">Crisp Website ID (UUID)</label>
                                                <input type="text" name="livechat_crisp_website_id" class="form-control form-control-sm font-monospace" 
                                                       value="<?php echo htmlspecialchars($s['livechat_crisp_website_id'] ?? ''); ?>" 
                                                       placeholder="e.g. 12345678-abcd-1234-abcd-1234567890ab">
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                Found in Crisp Dashboard &gt; Settings &gt; Website Settings &gt; Setup Instructions.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section D: Custom Embed Script -->
                                    <div class="col-12 provider-fields" id="fieldsCustom" style="<?php echo ($currentProvider === 'custom') ? '' : 'display: none;'; ?>">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <h6 class="fw-bold text-dark mb-2 small"><i class="fa fa-code text-danger me-1"></i>Custom Embed JavaScript Snippet</h6>
                                            <div class="mb-2">
                                                <label class="form-label text-dark small mb-1 fw-semibold">JavaScript Embed Code</label>
                                                <textarea name="livechat_custom_script" rows="4" class="form-control form-control-sm font-monospace" placeholder="<script>...your chat embed snippet...</script>"><?php echo htmlspecialchars($s['livechat_custom_script'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                Paste any widget script (e.g. Zendesk, Intercom, JivoChat, LiveChat). It will be safely injected at runtime.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Runtime API Details Card -->
                                    <div class="col-12">
                                        <div class="p-3 bg-white rounded-3 border">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="fw-bold text-dark small"><i class="fa fa-network-wired text-info me-1"></i>Runtime REST API Endpoint:</span>
                                                <span class="badge bg-success small">ACTIVE</span>
                                            </div>
                                            <code class="d-block p-2 bg-light rounded text-dark small mb-1" style="font-size: 0.75rem;">
                                                <?php echo URLROOT; ?>/home/livechatApi
                                            </code>
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                Accepts <code>action=config</code>, <code>action=query</code>, and <code>action=submit_enquiry</code> via GET/POST JSON.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Bar: Submit Button -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                                <div class="text-muted small">
                                    <i class="fa fa-check-circle text-success me-1"></i>
                                    Settings take effect immediately on public web pages without browser caching delays.
                                </div>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">
                                    <i class="fa fa-save me-1"></i> Save WhatsApp &amp; Live Chat Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- Modal 1: Add Custom Role -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title fw-bold" id="addRoleModalLabel">
                    <i class="fa fa-user-shield me-2"></i>Create New System Role
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/setting/index?tab=roles" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark"><i class="fa fa-tag text-primary me-2"></i>Role Identifier / Title</label>
                        <input type="text" name="role_name" class="form-control" placeholder="e.g. vice_principal, exam_controller, coordinator" required>
                        <div class="form-text text-muted" style="font-size: 0.78rem;">
                            Role name will be normalized (lowercase, underscore). It will immediately appear as a column in the RBAC matrix.
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_custom_role" class="btn btn-primary btn-sm px-4 fw-bold">
                        <i class="fa fa-check me-1"></i> Create Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Quick Onboard User / Teacher -->
<div class="modal fade" id="quickAddUserModal" tabindex="-1" aria-labelledby="quickAddUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold" id="quickAddUserModalLabel">
                    <i class="fa fa-user-plus me-2"></i>Quick Onboard User / Teacher
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/setting/index?tab=roles" method="post">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark"><i class="fa fa-user me-2 text-success"></i>Full Name</label>
                        <input type="text" name="user_name" class="form-control" placeholder="e.g. Muhammad Ali" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark"><i class="fa fa-envelope me-2 text-primary"></i>Email Address (Login ID)</label>
                        <input type="email" name="user_email" class="form-control" placeholder="e.g. ali.teacher@school.edu.pk" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark"><i class="fa fa-user-tag me-2 text-warning"></i>Assigned Role</label>
                            <select name="user_role" class="form-select text-capitalize" required>
                                <?php foreach($data['roles'] as $role): ?>
                                    <option value="<?php echo htmlspecialchars($role->name); ?>" <?php echo ($role->name === 'teacher') ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $role->name))); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold text-dark"><i class="fa fa-key me-2 text-danger"></i>Initial Password</label>
                            <input type="text" name="user_password" class="form-control font-monospace" value="123456" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="quick_add_user" class="btn btn-success btn-sm px-4 fw-bold">
                        <i class="fa fa-user-check me-1"></i> Register Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CMS MODAL 1: Add Event & Program -->
<div class="modal fade" id="cmsAddEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark py-3">
                <h5 class="modal-title fw-bold"><i class="fa fa-calendar-alt me-2"></i>Create Event &amp; Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontcms/events" method="post" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Event Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Annual Sports Gala 2026" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Start Date &amp; Time</label>
                            <input type="datetime-local" name="start_date" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-dark">Venue / Location</label>
                            <input type="text" name="venue" class="form-control" placeholder="e.g. Main Auditorium" value="Main Campus Auditorium">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Event Feature Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Detailed Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Write event highlights, schedule details, and guest guidelines..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold">Publish Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CMS MODAL 2: Create Custom Page -->
<div class="modal fade" id="cmsAddPageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title fw-bold"><i class="fa fa-file-code me-2"></i>Create Custom Runtime Page</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontcms/pages" method="post">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold text-dark">Page Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. About Our Academy" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-dark">URL Slug</label>
                            <input type="text" name="slug" class="form-control font-monospace" placeholder="e.g. about-us">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Rich Page Content (HTML supported)</label>
                        <textarea name="content" class="form-control font-monospace" rows="7" placeholder="Enter page content text or custom HTML sections..."></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="yes" checked id="pageActiveCheck">
                        <label class="form-check-label fw-bold text-dark" for="pageActiveCheck">Publish Page Immediately</label>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold">Save &amp; Publish Page</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CMS MODAL 3: Upload Gallery Photo -->
<div class="modal fade" id="cmsUploadGalleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title fw-bold"><i class="fa fa-camera-retro me-2"></i>Upload Photo to Gallery</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontcms/gallery" method="post" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Photo Title / Album <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Science Exhibition 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Caption / Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Enter image caption and detailed description..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Select Image File (JPG / PNG) <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold">Upload to Gallery</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CMS MODAL 4: Add News Story -->
<div class="modal fade" id="cmsAddNewsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white py-3">
                <h5 class="modal-title fw-bold"><i class="fa fa-newspaper me-2"></i>Publish News &amp; Headline</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/frontcms/news" method="post" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Headline / Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. High Performance Board Results Announced" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">News Date</label>
                        <input type="date" name="news_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Feature Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Full News Text Story</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Write full news article content..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white btn-sm px-4 fw-bold">Publish News</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    function initSettingsPage() {
        // 1. Smooth Tab Navigation (Updates URL ?tab=... without page reload)
        const tabButtons = document.querySelectorAll('#settingsTabs button[data-bs-toggle="pill"]');
        tabButtons.forEach(btn => {
            if (btn._hasTabSync) return;
            btn._hasTabSync = true;
            btn.addEventListener('shown.bs.tab', function(e) {
                const target = e.target.getAttribute('data-bs-target');
                if (target) {
                    const tabKey = target.replace('#tab-', '');
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('tab', tabKey);
                    window.history.replaceState({ url: newUrl.toString() }, '', newUrl.toString());
                }
            });
        });

        // 2. Live Prefix Typing Previews
        document.querySelectorAll('.prefix-input').forEach(input => {
            if (input._hasLivePreview) return;
            input._hasLivePreview = true;
            input.addEventListener('input', function() {
                const container = this.closest('.position-relative') || this.parentElement;
                const badge = container.querySelector('.sample-prefix-badge');
                if (badge) {
                    const suffix = badge.getAttribute('data-suffix') || '001';
                    badge.textContent = this.value.toUpperCase() + suffix;
                }
            });
        });

        // 3. Live Crest / Logo File Preview
        const logoInput = document.querySelector('input[name="logo"]');
        if (logoInput && !logoInput._hasPreviewHandler) {
            logoInput._hasPreviewHandler = true;
            logoInput.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewContainer = document.getElementById('logoPreviewContainer');
                        if (previewContainer) {
                            previewContainer.innerHTML = `<img id="schoolLogoPreviewImg" src="${e.target.result}" alt="School Crest Preview" class="img-fluid rounded border shadow-sm p-2" style="max-height: 120px; object-fit: contain;">`;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Tab switching without full page reload
        const tabNavPills = document.querySelectorAll('#settingsTabs [data-bs-toggle="pill"]');
        tabNavPills.forEach(btn => {
            if (btn._hasTabListener) return;
            btn._hasTabListener = true;
            btn.addEventListener('shown.bs.tab', function(e) {
                const targetId = e.target.getAttribute('data-bs-target');
                const tabKey = targetId.replace('#tab-', '');
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('tab', tabKey);
                window.history.replaceState(null, '', newUrl.toString());
            });
        });

        // 4. Email & SMTP Tab Interactive Helpers
        const presetGmailBtn = document.getElementById('presetGmailBtn');
        if (presetGmailBtn && !presetGmailBtn._hasPreset) {
            presetGmailBtn._hasPreset = true;
            presetGmailBtn.addEventListener('click', function() {
                const host = document.getElementById('smtpHostInput');
                const port = document.getElementById('smtpPortInput');
                const enc = document.getElementById('smtpEncryptionSelect');
                if (host) host.value = 'smtp.gmail.com';
                if (port) port.value = '587';
                if (enc) enc.value = 'tls';
                if (typeof window.showToast === 'function') {
                    window.showToast('Gmail presets applied (smtp.gmail.com:587 TLS). Please provide your 16-char Google App Password.', 'info');
                }
            });
        }

        const presetOfficeBtn = document.getElementById('presetOfficeBtn');
        if (presetOfficeBtn && !presetOfficeBtn._hasPreset) {
            presetOfficeBtn._hasPreset = true;
            presetOfficeBtn.addEventListener('click', function() {
                const host = document.getElementById('smtpHostInput');
                const port = document.getElementById('smtpPortInput');
                const enc = document.getElementById('smtpEncryptionSelect');
                if (host) host.value = 'smtp.office365.com';
                if (port) port.value = '587';
                if (enc) enc.value = 'tls';
                if (typeof window.showToast === 'function') {
                    window.showToast('Office 365 presets applied (smtp.office365.com:587 TLS).', 'info');
                }
            });
        }

        const togglePassBtn = document.getElementById('togglePasswordVisibilityBtn');
        if (togglePassBtn && !togglePassBtn._hasToggle) {
            togglePassBtn._hasToggle = true;
            togglePassBtn.addEventListener('click', function() {
                const input = document.getElementById('smtpPasswordInput');
                const icon = document.getElementById('togglePasswordEyeIcon');
                if (!input || !icon) return;
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }

        const smtpUserInput = document.getElementById('smtpUsernameInput');
        const smtpFromInput = document.getElementById('smtpFromEmailInput');
        if (smtpUserInput && smtpFromInput && !smtpUserInput._hasSync) {
            smtpUserInput._hasSync = true;
            smtpUserInput.addEventListener('blur', function() {
                if (!smtpFromInput.value && smtpUserInput.value.includes('@')) {
                    smtpFromInput.value = smtpUserInput.value;
                }
            });
        }

        // 5. Smooth AJAX Form Submissions (Zero Reload & Instant Feedback)
        document.querySelectorAll('.ajax-settings-form').forEach(form => {
            if (form._hasAjaxHandler) return;
            form._hasAjaxHandler = true;
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]:not([disabled])');
                if (!submitBtn) return;

                const formData = new FormData(form);
                if (submitBtn.name) {
                    formData.append(submitBtn.name, '1');
                }
                formData.append('ajax_submit', '1');
                if (!formData.has('csrf_token') && window.CSRF_TOKEN) {
                    formData.append('csrf_token', window.CSRF_TOKEN);
                }

                const originalBtnHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = (form.id === 'smtpTestForm') 
                    ? '<i class="fa fa-spinner fa-spin me-2"></i>Testing Connection...' 
                    : '<i class="fa fa-spinner fa-spin me-2"></i>Saving...';

                // If testing SMTP, also attach credentials currently entered in the left form
                if (form.id === 'smtpTestForm') {
                    const settingsForm = document.getElementById('smtpSettingsForm');
                    if (settingsForm) {
                        const settingsData = new FormData(settingsForm);
                        for (let [k, v] of settingsData.entries()) {
                            if (!formData.has(k) && k !== 'tab' && k !== 'csrf_token') {
                                formData.append(k, v);
                            }
                        }
                    }
                }

                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const rawText = await response.text();
                    let result;
                    try {
                        result = JSON.parse(rawText);
                    } catch (parseErr) {
                        console.warn('Non-JSON server response received:', rawText);
                        if (response.ok) {
                            result = { success: true, message: 'Settings saved successfully!' };
                        } else {
                            throw new Error('Server returned an error status (' + response.status + '). Please try again.');
                        }
                    }

                    if (result && result.success) {
                        if (typeof window.showToast === 'function') {
                            window.showToast(result.message || 'Settings saved successfully!', 'success');
                        }

                        // If a new logo was uploaded, update the crest preview card and sidebar immediately
                        if (result.logo_url) {
                            const previewImg = document.getElementById('schoolLogoPreviewImg');
                            const previewContainer = document.getElementById('logoPreviewContainer');
                            if (previewImg) {
                                previewImg.src = result.logo_url;
                            } else if (previewContainer) {
                                previewContainer.innerHTML = `<img id="schoolLogoPreviewImg" src="${result.logo_url}" alt="School Crest" class="img-fluid rounded border shadow-sm p-2" style="max-height: 120px; object-fit: contain;">`;
                            }

                            // Update sidebar brand icon box immediately
                            const brandIconBoxes = document.querySelectorAll('.brand-icon-box');
                            brandIconBoxes.forEach(box => {
                                box.innerHTML = `<img src="${result.logo_url}" alt="Logo" class="brand-logo-img">`;
                            });

                            // Update top navbar logo if present
                            const navbarBadges = document.querySelectorAll('.school-badge img');
                            navbarBadges.forEach(img => {
                                img.src = result.logo_url;
                            });
                        }

                        // If a new hero image was uploaded, update the hero image preview
                        if (result.hero_image_path) {
                            const heroPreview = document.getElementById('hero_image_preview');
                            if (heroPreview) {
                                heroPreview.src = result.hero_image_path;
                            }
                        }

                        // Update sidebar brand title & subtitle immediately
                        if (result.school_name) {
                            const brandTitle = document.getElementById('sidebarBrandTitle');
                            if (brandTitle) {
                                brandTitle.textContent = result.school_name;
                                brandTitle.title = result.school_name;
                            }
                            const schoolBadgeSpan = document.querySelector('.school-badge span');
                            if (schoolBadgeSpan) {
                                schoolBadgeSpan.textContent = result.school_name;
                            }
                        }

                        if (result.campus_name) {
                            const brandSubtitle = document.getElementById('sidebarBrandSubtitle');
                            if (brandSubtitle) {
                                brandSubtitle.textContent = result.campus_name;
                            }
                        }

                        const wasSuccess = submitBtn.classList.contains('btn-success');
                        submitBtn.innerHTML = '<i class="fa fa-check me-2"></i>' + (submitBtn.name === 'test_email_submit' || form.id === 'smtpTestForm' ? 'Sent!' : 'Saved!');
                        if (!wasSuccess) {
                            submitBtn.classList.remove('btn-primary');
                            submitBtn.classList.add('btn-success');
                        }
                        setTimeout(() => {
                            submitBtn.innerHTML = originalBtnHtml;
                            submitBtn.disabled = false;
                            if (!wasSuccess) {
                                submitBtn.classList.remove('btn-success');
                                submitBtn.classList.add('btn-primary');
                            }
                        }, 1800);
                    } else {
                        throw new Error(result.message || 'Failed to save settings.');
                    }
                } catch(err) {
                    console.error('Settings save error:', err);
                    if (typeof window.showToast === 'function') {
                        window.showToast(err.message || 'Failed to save settings. Please try again.', 'danger');
                    } else {
                        alert(err.message || 'Failed to save settings.');
                    }
                    submitBtn.innerHTML = originalBtnHtml;
                    submitBtn.disabled = false;
                }
        });

        const heroInput = document.getElementById('heroImageFileInput');
        if (heroInput && !heroInput._hasHeroPreviewListener) {
            heroInput._hasHeroPreviewListener = true;
            heroInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const p = document.getElementById('hero_image_preview');
                        if (p) p.src = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // Live Chat Provider Switcher
        const providerSelect = document.getElementById('livechatProviderSelect');
        if (providerSelect && !providerSelect._hasChangeListener) {
            providerSelect._hasChangeListener = true;
            const updateProviderFields = function() {
                const val = providerSelect.value;
                const fieldMap = {
                    'builtin': 'fieldsBuiltin',
                    'tawk': 'fieldsTawk',
                    'crisp': 'fieldsCrisp',
                    'custom': 'fieldsCustom'
                };
                Object.keys(fieldMap).forEach(key => {
                    const el = document.getElementById(fieldMap[key]);
                    if (el) el.style.display = (key === val) ? 'block' : 'none';
                });
            };
            providerSelect.addEventListener('change', updateProviderFields);
            updateProviderFields();
        }
    }

    window.selectThemeSwatch = function(color, el) {
        const input = document.getElementById('selectedThemeColorInput');
        if (input) input.value = color;
        document.querySelectorAll('.theme-swatch-card').forEach(card => {
            card.classList.remove('active-swatch', 'border-primary', 'bg-primary-subtle', 'border-danger', 'bg-danger-subtle', 'border-success', 'bg-success-subtle', 'border-dark', 'bg-dark-subtle');
            card.classList.add('bg-white');
            const chk = card.querySelector('.swatch-check');
            if (chk) chk.style.display = 'none';
        });
        if (el) {
            el.classList.remove('bg-white');
            el.classList.add('active-swatch');
            if (color === 'default') el.classList.add('border-primary', 'bg-primary-subtle');
            else if (color === 'red') el.classList.add('border-danger', 'bg-danger-subtle');
            else if (color === 'green') el.classList.add('border-success', 'bg-success-subtle');
            else if (color === 'dark') el.classList.add('border-dark', 'bg-dark-subtle');
            const chk = el.querySelector('.swatch-check');
            if (chk) chk.style.display = 'block';
        }
    };

    window.updateEmergencyAlertPreview = function() {
        const sw = document.getElementById('enableEmergencyAlertSwitch');
        const bgSelect = document.getElementById('emergencyAlertBgSelect');
        const textInput = document.getElementById('emergencyAlertTextInput');
        const linkInput = document.getElementById('emergencyAlertLinkInput');

        const previewBox = document.getElementById('emergencyPreviewBox');
        const previewBadge = document.getElementById('emergencyPreviewBadge');
        const previewText = document.getElementById('emergencyPreviewText');
        const previewBtn = document.getElementById('emergencyPreviewBtn');

        if (!previewBox) return;

        const isEnabled = sw ? sw.checked : true;
        const bgVal = bgSelect ? bgSelect.value : 'danger';
        const textVal = (textInput && textInput.value.trim()) ? textInput.value.trim() : 'Campus notice or holiday alert text goes here...';
        const linkVal = linkInput ? linkInput.value.trim() : '';

        const colorMap = {
            'danger': { bg: '#dc2626', badge: '<i class="fa fa-triangle-exclamation me-1"></i>URGENT ALERT' },
            'warning': { bg: '#d97706', badge: '<i class="fa fa-triangle-exclamation me-1"></i>HOLIDAY / NOTICE' },
            'info': { bg: '#2563eb', badge: '<i class="fa fa-circle-info me-1"></i>CAMPUS UPDATE' },
            'success': { bg: '#059669', badge: '<i class="fa fa-check-circle me-1"></i>ANNOUNCEMENT' }
        };

        const currentStyle = colorMap[bgVal] || colorMap['danger'];
        previewBox.style.backgroundColor = currentStyle.bg;
        previewBox.style.opacity = isEnabled ? '1' : '0.4';
        if (previewBadge) previewBadge.innerHTML = currentStyle.badge;
        if (previewText) previewText.textContent = textVal;
        if (previewBtn) previewBtn.style.display = linkVal ? 'inline-block' : 'none';
    };

    document.addEventListener('DOMContentLoaded', function() {
        initSettingsPage();
        if (typeof window.updateEmergencyAlertPreview === 'function') window.updateEmergencyAlertPreview();
    });
    document.addEventListener('page:loaded', function() {
        initSettingsPage();
        if (typeof window.updateEmergencyAlertPreview === 'function') window.updateEmergencyAlertPreview();
    });
    initSettingsPage();
    if (typeof window.updateEmergencyAlertPreview === 'function') window.updateEmergencyAlertPreview();
})();
</script>

<?php require APPROOT . '/Views/layouts/footer.php'; ?>
