<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="container-fluid px-0">
    <!-- Page Header & Quick Stats -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-2 py-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Circulation Desk</span>
                <span class="text-muted small">&bull;</span>
                <span class="text-muted small">Library Management</span>
            </div>
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fa fa-book-reader text-primary"></i> Issue &amp; Return Center
            </h3>
            <p class="text-muted small mb-0">Issue library books to students or faculty members separately, and process returns in one click.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/library/index" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="fa fa-books me-1"></i> Books Catalog
            </a>
            <?php if(in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'librarian'])): ?>
            <a href="<?php echo URLROOT; ?>/notice/index?category=Library+%26+Reading+Notice" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-3 fw-semibold">
                <i class="fa fa-bullhorn text-warning me-1"></i> Library Notices
            </a>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#quickAddBookModal">
                <i class="fa fa-plus me-1"></i> Add New Book
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Stats Summary Row -->
    <?php
        $totalIssued = count($data['issued_books'] ?? []);
        $studentIssued = 0;
        $facultyIssued = 0;
        $overdueCount = 0;
        $todayTs = strtotime(date('Y-m-d'));

        foreach ($data['issued_books'] as $ib) {
            if (($ib->user_type ?? '') === 'student' || ($ib->role ?? '') === 'student') {
                $studentIssued++;
            } else {
                $facultyIssued++;
            }
            if (strtotime($ib->due_date) < $todayTs) {
                $overdueCount++;
            }
        }
    ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa fa-book-bookmark fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Active Issues</div>
                        <div class="h5 fw-bold text-dark mb-0"><?php echo $totalIssued; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa fa-user-graduate fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Student Borrows</div>
                        <div class="h5 fw-bold text-dark mb-0"><?php echo $studentIssued; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa fa-chalkboard-user fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Faculty Borrows</div>
                        <div class="h5 fw-bold text-dark mb-0"><?php echo $facultyIssued; ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 <?php echo $overdueCount > 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-secondary bg-opacity-10 text-muted'; ?> d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="fa fa-clock-rotate-left fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Overdue Books</div>
                        <div class="h5 fw-bold <?php echo $overdueCount > 0 ? 'text-danger' : 'text-dark'; ?> mb-0"><?php echo $overdueCount; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Circulation Interface: Left Issue Form / Right Issued List -->
    <div class="row g-4">
        
        <!-- Left: Smart Issue Form (Separate Student vs Faculty) -->
        <div class="col-lg-4 col-xl-4">
            <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden bg-white">
                <div class="card-header bg-white border-bottom p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa fa-arrow-up-right-from-square text-primary"></i> Issue Book
                        </h6>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1" style="font-size: 0.7rem;">Desk Operation</span>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <!-- Member Type Selector Tabs (Student vs Faculty Alag Alag) -->
                    <label class="form-label fw-bold text-dark small mb-2 d-flex align-items-center justify-content-between">
                        <span>Select Member Category:</span>
                        <span class="text-muted fw-normal" style="font-size: 0.72rem;">Choose tab below</span>
                    </label>
                    <ul class="nav nav-pills nav-fill mb-3 p-1 bg-light rounded-pill" id="memberTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill py-2 fw-semibold text-capitalize small" 
                                    id="tab-student" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#panel-student" 
                                    type="button" 
                                    role="tab"
                                    onclick="setMemberType('student')">
                                <i class="fa fa-user-graduate me-1 text-info"></i> Student
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill py-2 fw-semibold text-capitalize small" 
                                    id="tab-faculty" 
                                    data-bs-toggle="pill" 
                                    data-bs-target="#panel-faculty" 
                                    type="button" 
                                    role="tab"
                                    onclick="setMemberType('faculty')">
                                <i class="fa fa-chalkboard-user me-1 text-success"></i> Faculty / Staff
                            </button>
                        </li>
                    </ul>

                    <form action="<?php echo URLROOT; ?>/library/issue_return" method="post" id="issueBookForm">
                        <input type="hidden" name="issue_book" value="1">
                        <input type="hidden" name="member_type" id="activeMemberType" value="student">

                        <!-- Tab Content: Student Select vs Faculty Select -->
                        <div class="tab-content mb-3" id="memberTypeTabContent">
                            
                            <!-- Student Selection Panel -->
                            <div class="tab-pane fade show active" id="panel-student" role="tabpanel">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="fa fa-user me-1 text-primary"></i> Select Student
                                </label>
                                <div class="mb-2">
                                    <input type="text" 
                                           id="studentSearchInput" 
                                           class="form-control form-control-sm rounded-3" 
                                           placeholder="🔍 Type roll no, name or class to filter..."
                                           onkeyup="filterDropdownOptions('student_user_id', this.value)">
                                </div>
                                <select name="student_user_id" id="student_user_id" class="form-select rounded-3">
                                    <option value="">-- Choose Student (Total <?php echo count($data['students'] ?? []); ?>) --</option>
                                    <?php if(!empty($data['students'])): ?>
                                        <?php foreach($data['students'] as $st): 
                                            $admTxt = !empty($st->admission_no) ? 'Adm #' . $st->admission_no : '';
                                            $rollTxt = !empty($st->roll_no) ? 'Roll #' . $st->roll_no : '';
                                            $classTxt = !empty($st->class_name) ? $st->class_name . (!empty($st->section_name) ? ' (' . $st->section_name . ')' : '') : '';
                                            $metaParts = array_filter([$admTxt, $rollTxt, $classTxt]);
                                            $metaStr = !empty($metaParts) ? ' [' . implode(' • ', $metaParts) . ']' : '';
                                        ?>
                                            <option value="<?php echo $st->user_id; ?>">
                                                <?php echo htmlspecialchars($st->name) . $metaStr; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No students found in current school session</option>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text text-muted" style="font-size: 0.72rem;">Student profile, Class and Admission numbers are auto-linked.</div>
                            </div>

                            <!-- Faculty Selection Panel -->
                            <div class="tab-pane fade" id="panel-faculty" role="tabpanel">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="fa fa-chalkboard-user me-1 text-success"></i> Select Teacher / Staff
                                </label>
                                <div class="mb-2">
                                    <input type="text" 
                                           id="facultySearchInput" 
                                           class="form-control form-control-sm rounded-3" 
                                           placeholder="🔍 Type staff code, name or department to filter..."
                                           onkeyup="filterDropdownOptions('faculty_user_id', this.value)">
                                </div>
                                <select name="faculty_user_id" id="faculty_user_id" class="form-select rounded-3">
                                    <option value="">-- Choose Faculty / Staff (Total <?php echo count($data['faculty'] ?? []); ?>) --</option>
                                    <?php if(!empty($data['faculty'])): ?>
                                        <?php foreach($data['faculty'] as $fc): 
                                            $deptTxt = !empty($fc->department) ? $fc->department : ucfirst($fc->role);
                                            $desigTxt = !empty($fc->designation) ? $fc->designation : '';
                                            $codeTxt = !empty($fc->staff_code) ? 'ID: ' . $fc->staff_code : '';
                                            $metaParts = array_filter([$desigTxt, $deptTxt, $codeTxt]);
                                            $metaStr = !empty($metaParts) ? ' [' . implode(' • ', $metaParts) . ']' : ' [' . ucfirst($fc->role) . ']';
                                        ?>
                                            <option value="<?php echo $fc->user_id; ?>">
                                                <?php echo htmlspecialchars($fc->name) . $metaStr; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>No faculty members found</option>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text text-muted" style="font-size: 0.72rem;">Staff code, department and designation are tracked.</div>
                            </div>

                        </div>

                        <!-- Book Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fa fa-book me-1 text-primary"></i> Select Book from Catalog
                            </label>
                            <select name="book_id" id="book_id" class="form-select rounded-3" required>
                                <option value="">-- Choose Book from Stock --</option>
                                <?php foreach($data['books'] as $b): ?>
                                    <?php if($b->qty > 0): ?>
                                        <option value="<?php echo $b->id; ?>">
                                            <?php echo htmlspecialchars($b->book_title); ?> 
                                            [#<?php echo htmlspecialchars($b->book_no); ?> • In Stock: <?php echo $b->qty; ?><?php echo !empty($b->rack_no) ? ' • Rack: ' . $b->rack_no : ''; ?>]
                                        </option>
                                    <?php else: ?>
                                        <option value="<?php echo $b->id; ?>" disabled class="text-muted">
                                            <?php echo htmlspecialchars($b->book_title); ?> [#<?php echo htmlspecialchars($b->book_no); ?> - OUT OF STOCK]
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-muted" style="font-size: 0.72rem;">Stock automatically decrements when issued.</div>
                        </div>

                        <!-- Dates: Issue Date & Due Date -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small mb-1">Issue Date</label>
                                <input type="date" name="issue_date" class="form-control rounded-3" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small mb-1">Due Date</label>
                                <input type="date" name="due_date" class="form-control rounded-3" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required>
                            </div>
                        </div>

                        <!-- Issue Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold shadow-sm">
                            <i class="fa fa-check-circle me-1"></i> Issue Book Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Issued Books Ledger with Filter Tabs -->
        <div class="col-lg-8 col-xl-8">
            <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden bg-white">
                
                <!-- Card Header with Category Filters -->
                <div class="card-header bg-white border-bottom p-3">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <div>
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="fa fa-list-check text-primary"></i> Currently Issued Books
                            </h6>
                            <small class="text-muted">Active circulation records waiting for return</small>
                        </div>
                        
                        <!-- Filter Pill Buttons -->
                        <div class="d-flex align-items-center gap-1">
                            <a href="<?php echo URLROOT; ?>/library/issue_return" 
                               class="btn btn-sm <?php echo empty($data['current_filter']) ? 'btn-primary' : 'btn-light'; ?> rounded-pill px-3 fw-semibold">
                                All (<?php echo $totalIssued; ?>)
                            </a>
                            <a href="<?php echo URLROOT; ?>/library/issue_return?filter=student" 
                               class="btn btn-sm <?php echo ($data['current_filter'] ?? '') === 'student' ? 'btn-info text-white' : 'btn-light'; ?> rounded-pill px-3 fw-semibold">
                                <i class="fa fa-user-graduate me-1"></i> Students (<?php echo $studentIssued; ?>)
                            </a>
                            <a href="<?php echo URLROOT; ?>/library/issue_return?filter=faculty" 
                               class="btn btn-sm <?php echo ($data['current_filter'] ?? '') === 'faculty' ? 'btn-success text-white' : 'btn-light'; ?> rounded-pill px-3 fw-semibold">
                                <i class="fa fa-chalkboard-user me-1"></i> Faculty (<?php echo $facultyIssued; ?>)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Issued Books Table -->
                <div class="card-body p-0">
                    <?php if(!empty($data['issued_books'])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.04em;">
                                        <th class="ps-3 py-3">Book Details</th>
                                        <th class="py-3">Member &amp; Category</th>
                                        <th class="py-3">Issue / Due Date</th>
                                        <th class="py-3">Status</th>
                                        <th class="pe-3 py-3 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data['issued_books'] as $issue): 
                                        $dueTs = strtotime($issue->due_date);
                                        $isOverdue = ($todayTs > $dueTs);
                                        $isStudent = (($issue->user_type ?? '') === 'student' || ($issue->role ?? '') === 'student');
                                    ?>
                                        <tr>
                                            <!-- Book Details -->
                                            <td class="ps-3 py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded-3 p-2 bg-light text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                                                        <i class="fa fa-book"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark text-break" style="font-size: 0.88rem;">
                                                            <?php echo htmlspecialchars($issue->book_title); ?>
                                                        </div>
                                                        <div class="text-muted small" style="font-size: 0.75rem;">
                                                            No: <strong><?php echo htmlspecialchars($issue->book_no); ?></strong>
                                                            <?php if(!empty($issue->author)): ?>
                                                                &bull; By <?php echo htmlspecialchars($issue->author); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Member Details -->
                                            <td class="py-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if($isStudent): ?>
                                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.68rem;">
                                                            <i class="fa fa-user-graduate me-1"></i>Student
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.68rem;">
                                                            <i class="fa fa-chalkboard-user me-1"></i>Faculty
                                                        </span>
                                                    <?php endif; ?>
                                                    <div class="fw-semibold text-dark" style="font-size: 0.85rem;">
                                                        <?php echo htmlspecialchars($issue->user_name); ?>
                                                    </div>
                                                </div>
                                                <div class="text-muted small ps-1" style="font-size: 0.72rem;">
                                                    <?php if($isStudent && !empty($issue->class_name)): ?>
                                                        Class: <?php echo htmlspecialchars($issue->class_name); ?><?php echo !empty($issue->section_name) ? ' (' . $issue->section_name . ')' : ''; ?>
                                                        <?php if(!empty($issue->admission_no)): ?> &bull; Adm #<?php echo $issue->admission_no; ?><?php endif; ?>
                                                    <?php elseif(!$isStudent && !empty($issue->department)): ?>
                                                        Dept: <?php echo htmlspecialchars($issue->department); ?>
                                                        <?php if(!empty($issue->designation)): ?> &bull; <?php echo $issue->designation; ?><?php endif; ?>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($issue->email ?? ''); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                            <!-- Dates -->
                                            <td class="py-3">
                                                <div class="small fw-semibold text-dark">
                                                    <i class="fa fa-calendar-check text-muted me-1"></i><?php echo date('d M Y', strtotime($issue->issue_date)); ?>
                                                </div>
                                                <div class="small <?php echo $isOverdue ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                                    <i class="fa fa-hourglass-half me-1"></i>Due: <?php echo date('d M Y', $dueTs); ?>
                                                </div>
                                            </td>

                                            <!-- Status & Fine Calculation -->
                                            <td class="py-3">
                                                <?php 
                                                    $currency = $_SESSION['currency_symbol'] ?? 'Rs.';
                                                    $dailyFineRate = 10; // Rs. 10 per day default
                                                    $overdueDays = 0;
                                                    $calculatedFine = 0;
                                                    if ($isOverdue) {
                                                        $diffSecs = $todayTs - $dueTs;
                                                        $overdueDays = max(1, (int)floor($diffSecs / 86400));
                                                        $calculatedFine = $overdueDays * $dailyFineRate;
                                                    }
                                                ?>
                                                <?php if($isOverdue): ?>
                                                    <div class="d-flex flex-column gap-1">
                                                        <span class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                                            <i class="fa fa-triangle-exclamation me-1"></i>Overdue (<?php echo $overdueDays; ?>d)
                                                        </span>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 fw-bold" style="font-size: 0.72rem;" title="Fine: <?php echo $currency; ?> <?php echo $dailyFineRate; ?>/day">
                                                            Fine: <?php echo $currency; ?> <?php echo number_format($calculatedFine, 2); ?>
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-warning bg-opacity-15 text-dark border border-warning border-opacity-50 rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                                        <i class="fa fa-clock me-1"></i>Issued
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <!-- Actions: 1-Click Reminder (if overdue) & Return Button with Fine -->
                                            <td class="pe-3 py-3 text-end">
                                                <div class="d-flex align-items-center justify-content-end gap-1 flex-wrap">
                                                    <?php if($isOverdue): ?>
                                                        <!-- 1-Click Reminder Alert Button -->
                                                        <form action="<?php echo URLROOT; ?>/library/send_reminder" method="post" class="d-inline" onsubmit="return confirm('Send overdue notice & reminder alert to <?php echo htmlspecialchars($issue->user_name); ?>?');">
                                                            <input type="hidden" name="issue_id" value="<?php echo $issue->id; ?>">
                                                            <input type="hidden" name="user_name" value="<?php echo htmlspecialchars($issue->user_name); ?>">
                                                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($issue->email ?? ''); ?>">
                                                            <input type="hidden" name="book_title" value="<?php echo htmlspecialchars($issue->book_title); ?>">
                                                            <input type="hidden" name="due_date" value="<?php echo htmlspecialchars($issue->due_date); ?>">
                                                            <input type="hidden" name="overdue_days" value="<?php echo $overdueDays; ?>">
                                                            <input type="hidden" name="fine_amount" value="<?php echo $calculatedFine; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 fw-semibold" title="Send 1-Click Notice Board & Email Reminder" style="font-size: 0.75rem;">
                                                                <i class="fa fa-bell me-1"></i> Remind
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <!-- Return Form -->
                                                    <form action="<?php echo URLROOT; ?>/library/issue_return" method="post" class="d-inline return-book-form" data-overdue="<?php echo $isOverdue ? '1' : '0'; ?>" data-fine="<?php echo $calculatedFine; ?>" data-book="<?php echo htmlspecialchars($issue->book_title); ?>">
                                                        <input type="hidden" name="return_book" value="1">
                                                        <input type="hidden" name="issue_id" value="<?php echo $issue->id; ?>">
                                                        <input type="hidden" name="fine" class="input-fine" value="<?php echo $calculatedFine; ?>">
                                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold btn-process-return" onclick="processReturn(this)" style="font-size: 0.78rem;">
                                                            <i class="fa fa-rotate-left me-1"></i> Return
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-4 mb-3" style="width: 80px; height: 80px;">
                                <i class="fa fa-book-open fa-2x text-muted"></i>
                            </div>
                            <h6 class="fw-bold text-dark">No Books Currently Issued</h6>
                            <p class="text-muted small mb-0">Use the circulation form on the left to issue books to students or teachers.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Quick Add Book Modal -->
<div class="modal fade" id="quickAddBookModal" tabindex="-1" aria-labelledby="quickAddBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-0 py-3 px-4 rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="quickAddBookModalLabel">
                    <i class="fa fa-plus-circle text-primary me-2"></i> Add Book to Library Catalog
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/library/add" method="post">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-dark small">Book Title <span class="text-danger">*</span></label>
                            <input type="text" name="book_title" class="form-control rounded-3" placeholder="e.g. Modern Physics 10th Edition" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small">Book / Accession No.</label>
                            <input type="text" name="book_no" class="form-control rounded-3" placeholder="e.g. BK-1049 (Auto if blank)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Author Name</label>
                            <input type="text" name="author" class="form-control rounded-3" placeholder="e.g. Dr. H. C. Verma">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Publisher</label>
                            <input type="text" name="publisher" class="form-control rounded-3" placeholder="e.g. Oxford University Press">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small">ISBN Number</label>
                            <input type="text" name="isbn" class="form-control rounded-3" placeholder="e.g. 978-0-123456-47-2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark small">Rack / Shelf Location</label>
                            <input type="text" name="rack_no" class="form-control rounded-3" placeholder="e.g. Shelf B-4">
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label fw-bold text-dark small">Quantity</label>
                            <input type="number" name="qty" class="form-control rounded-3" value="1" min="1" required>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label fw-bold text-dark small">Price (PKR)</label>
                            <input type="number" name="price" step="0.01" class="form-control rounded-3" placeholder="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4 rounded-bottom-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="fa fa-save me-1"></i> Save Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setMemberType(type) {
    var activeInput = document.getElementById('activeMemberType');
    if (activeInput) activeInput.value = type;
    
    var stSelect = document.getElementById('student_user_id');
    var fcSelect = document.getElementById('faculty_user_id');

    if (type === 'student') {
        if (stSelect) stSelect.required = true;
        if (fcSelect) {
            fcSelect.required = false;
            fcSelect.value = '';
        }
    } else {
        if (fcSelect) fcSelect.required = true;
        if (stSelect) {
            stSelect.required = false;
            stSelect.value = '';
        }
    }
}

// Client-side quick filter for member dropdowns
function filterDropdownOptions(selectId, filterText) {
    var select = document.getElementById(selectId);
    if (!select) return;
    var filter = filterText.toLowerCase().trim();
    var options = select.options;
    
    for (var i = 1; i < options.length; i++) {
        var text = options[i].text.toLowerCase();
        if (text.includes(filter)) {
            options[i].style.display = '';
        } else {
            options[i].style.display = 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setMemberType('student');
});

function processReturn(btn) {
    var form = btn.closest('.return-book-form');
    if (!form) return;
    var isOverdue = form.getAttribute('data-overdue') === '1';
    var defaultFine = parseFloat(form.getAttribute('data-fine') || 0);
    var bookTitle = form.getAttribute('data-book') || 'Book';
    var fineInput = form.querySelector('.input-fine');

    if (isOverdue && defaultFine > 0) {
        var currency = '<?php echo $_SESSION['currency_symbol'] ?? 'Rs.'; ?>';
        var finePrompt = prompt('This book "' + bookTitle + '" is OVERDUE.\nCalculated Fine: ' + currency + ' ' + defaultFine.toFixed(2) + '\n\nEnter collected/agreed fine amount (or 0 for fee waiver):', defaultFine);
        if (finePrompt === null) {
            return; // Cancelled
        }
        var fineVal = parseFloat(finePrompt);
        if (isNaN(fineVal) || fineVal < 0) fineVal = 0;
        if (fineInput) fineInput.value = fineVal;
    } else {
        if (!confirm('Confirm book return for "' + bookTitle + '"? Stock will be replenished.')) {
            return;
        }
        if (fineInput) fineInput.value = 0;
    }

    form.submit();
}
</script>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
