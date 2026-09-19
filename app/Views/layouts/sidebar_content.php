<?php if(isset($_SESSION['user_role'])): 
    $role = $_SESSION['user_role'];
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    
    // Helper to determine if link is active
    $isRouteActive = function($path) use ($currentUri) {
        return (strpos($currentUri, $path) !== false) ? 'active' : '';
    };
    $isFrontOfficeActive = (strpos($currentUri, 'frontoffice') !== false || strpos($currentUri, 'notice') !== false);
?>

<?php /* =========================================================================
         ROLE: TEACHER
         ========================================================================= */ ?>
<?php if($role === 'teacher'): ?>
<div class="sidebar-category-header text-uppercase fw-bold text-primary opacity-75" style="letter-spacing: 0.05em; font-size: 0.7rem;">Faculty Command Hub</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/teacher/index" class="sidebar-nav-link <?php echo $isRouteActive('teacher/index') ?: ($currentUri === '/school/' || $currentUri === '/school' ? 'active' : ''); ?>">
            <i class="fa fa-tachometer-alt text-primary fs-5"></i>
            <span class="fw-bold">Teacher Dashboard</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/profile/index" class="sidebar-nav-link <?php echo $isRouteActive('profile'); ?>">
            <i class="fa fa-user-circle text-info"></i>
            <span>My Profile &amp; Bio</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Classroom &amp; Academics</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/attendance/student" class="sidebar-nav-link <?php echo $isRouteActive('attendance/student'); ?>">
            <i class="fa fa-calendar-check text-success"></i>
            <span>Daily Gate Attendance</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/attendance/monthly" class="sidebar-nav-link <?php echo $isRouteActive('attendance/monthly'); ?>">
            <i class="fa fa-calendar-days text-info"></i>
            <span>Monthly Register Matrix</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/timetable/index" class="sidebar-nav-link <?php echo $isRouteActive('timetable'); ?>">
            <i class="fa fa-clock text-warning"></i>
            <span>Class Timetable</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/homework/index" class="sidebar-nav-link <?php echo $isRouteActive('homework'); ?>">
            <i class="fa fa-book-reader text-purple"></i>
            <span>Homework Management</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Examinations &amp; Results</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/exam/marks" class="sidebar-nav-link <?php echo $isRouteActive('exam/marks'); ?>">
            <i class="fa fa-marker text-danger"></i>
            <span>Online Marks Entry</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/exam/index" class="sidebar-nav-link <?php echo $isRouteActive('exam/index'); ?>">
            <i class="fa fa-file-signature text-warning"></i>
            <span>Exams &amp; Papers</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/exam/gazette" class="sidebar-nav-link <?php echo $isRouteActive('exam/gazette'); ?>">
            <i class="fa fa-award text-success"></i>
            <span>Result Gazette &amp; DMC</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Campus &amp; Community</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/clearance/index" class="sidebar-nav-link <?php echo $isRouteActive('clearance'); ?>">
            <i class="fa fa-clipboard-check text-primary"></i>
            <span>Exit Clearance Hub</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/library/index" class="sidebar-nav-link <?php echo $isRouteActive('library'); ?>">
            <i class="fa fa-book text-info"></i>
            <span>Campus Library</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/notice/index" class="sidebar-nav-link <?php echo $isRouteActive('notice'); ?>">
            <i class="fa fa-bullhorn text-warning"></i>
            <span>Notice Board</span>
        </a>
    </li>
</ul>

<?php /* =========================================================================
         ROLE: STUDENT
         ========================================================================= */ ?>
<?php elseif($role === 'student'): ?>
<div class="sidebar-category-header text-uppercase fw-bold text-primary opacity-75" style="letter-spacing: 0.05em; font-size: 0.7rem;">Student 360 Hub</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/student/index" class="sidebar-nav-link <?php echo $isRouteActive('student/index') ?: ($currentUri === '/school/' || $currentUri === '/school' ? 'active' : ''); ?>">
            <i class="fa fa-user-graduate text-primary fs-5"></i>
            <span class="fw-bold">My Student Dashboard</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/profile/index" class="sidebar-nav-link <?php echo $isRouteActive('profile'); ?>">
            <i class="fa fa-user-circle text-info"></i>
            <span>My Profile &amp; Bio</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Academics &amp; Schedule</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/student/index#timetableTab" class="sidebar-nav-link">
            <i class="fa fa-calendar-alt text-warning"></i>
            <span>Class Timetable</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/student/index#homeworkTab" class="sidebar-nav-link">
            <i class="fa fa-book-reader text-purple"></i>
            <span>Homework Diary</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Examinations &amp; Results</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/student/results" class="sidebar-nav-link <?php echo $isRouteActive('student/results'); ?>">
            <i class="fa fa-award text-success"></i>
            <span>Online Results &amp; DMC</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Accounts &amp; Campus</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/fees/challan" class="sidebar-nav-link <?php echo $isRouteActive('fees/challan'); ?>">
            <i class="fa fa-receipt text-success"></i>
            <span>My Fee Challans</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/student/index#attendanceTab" class="sidebar-nav-link">
            <i class="fa fa-calendar-check text-info"></i>
            <span>My Attendance Log</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/library/index" class="sidebar-nav-link <?php echo $isRouteActive('library'); ?>">
            <i class="fa fa-book text-primary"></i>
            <span>Library Catalog</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/notice/index" class="sidebar-nav-link <?php echo $isRouteActive('notice'); ?>">
            <i class="fa fa-bullhorn text-warning"></i>
            <span>Campus Notice Board</span>
        </a>
    </li>
</ul>

<?php /* =========================================================================
         ROLE: PARENT
         ========================================================================= */ ?>
<?php elseif($role === 'parent'): ?>
<div class="sidebar-category-header text-uppercase fw-bold text-primary opacity-75" style="letter-spacing: 0.05em; font-size: 0.7rem;">Family Portal</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/parent/index" class="sidebar-nav-link <?php echo $isRouteActive('parent/index') ?: ($currentUri === '/school/' || $currentUri === '/school' ? 'active' : ''); ?>">
            <i class="fa fa-people-roof text-primary fs-5"></i>
            <span class="fw-bold">Family &amp; Children Hub</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/profile/index" class="sidebar-nav-link <?php echo $isRouteActive('profile'); ?>">
            <i class="fa fa-user-circle text-info"></i>
            <span>My Profile &amp; Bio</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/student/index" class="sidebar-nav-link <?php echo $isRouteActive('student/index'); ?>">
            <i class="fa fa-user-graduate text-info"></i>
            <span>Child 360 Dossier</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Academics &amp; Learning</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/parent/index#homework" class="sidebar-nav-link">
            <i class="fa fa-book-reader text-purple"></i>
            <span>Homework Diary</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/parent/index#attendance" class="sidebar-nav-link">
            <i class="fa fa-calendar-check text-warning"></i>
            <span>Attendance Records</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Examinations &amp; Progress</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/student/results" class="sidebar-nav-link <?php echo $isRouteActive('student/results'); ?>">
            <i class="fa fa-award text-success"></i>
            <span>Report Cards &amp; DMC</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Invoices &amp; Notices</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/fees/challan" class="sidebar-nav-link <?php echo $isRouteActive('fees/challan'); ?>">
            <i class="fa fa-money-check text-success"></i>
            <span>Bank Fee Challans</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/notice/index" class="sidebar-nav-link <?php echo $isRouteActive('notice'); ?>">
            <i class="fa fa-bullhorn text-warning"></i>
            <span>School Announcements</span>
        </a>
    </li>
</ul>

<?php /* =========================================================================
         ROLE: ACCOUNTANT
         ========================================================================= */ ?>
<?php elseif($role === 'accountant'): ?>
<div class="sidebar-category-header text-uppercase fw-bold text-primary opacity-75" style="letter-spacing: 0.05em; font-size: 0.7rem;">Accounts &amp; Finance Hub</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/fees/collect" class="sidebar-nav-link <?php echo $isRouteActive('fees/collect') ?: ($currentUri === '/school/' || $currentUri === '/school' ? 'active' : ''); ?>">
            <i class="fa fa-cash-register text-success fs-5"></i>
            <span class="fw-bold">Fees Collection Desk</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/profile/index" class="sidebar-nav-link <?php echo $isRouteActive('profile'); ?>">
            <i class="fa fa-user-circle text-info"></i>
            <span>My Profile &amp; Bio</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Fees &amp; Invoicing</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/fees/challan" class="sidebar-nav-link <?php echo $isRouteActive('fees/challan'); ?>">
            <i class="fa fa-money-check text-primary"></i>
            <span>Bank Fee Challans</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/fees/defaulters" class="sidebar-nav-link <?php echo $isRouteActive('fees/defaulters'); ?>">
            <i class="fa fa-user-clock text-danger"></i>
            <span>Fee Defaulters Ledger</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Accounts &amp; Ledger</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/incomes/index" class="sidebar-nav-link <?php echo $isRouteActive('incomes'); ?>">
            <i class="fa fa-hand-holding-dollar text-success"></i>
            <span>Incomes &amp; Cash Book</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/expense/index" class="sidebar-nav-link <?php echo $isRouteActive('expense'); ?>">
            <i class="fa fa-wallet text-warning"></i>
            <span>Expense Manager</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/payroll/index" class="sidebar-nav-link <?php echo $isRouteActive('payroll'); ?>">
            <i class="fa fa-money-check-dollar text-purple"></i>
            <span>Staff Payroll &amp; Slips</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Clearance &amp; Reports</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/students/index" class="sidebar-nav-link <?php echo $isRouteActive('students'); ?>">
            <i class="fa fa-user-graduate text-info"></i>
            <span>Students Directory</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/clearance/index" class="sidebar-nav-link <?php echo $isRouteActive('clearance'); ?>">
            <i class="fa fa-clipboard-check text-primary"></i>
            <span>Exit Clearance Hub</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/reports/index" class="sidebar-nav-link <?php echo $isRouteActive('reports'); ?>">
            <i class="fa fa-chart-line text-success"></i>
            <span>Financial Reports</span>
        </a>
    </li>
</ul>

<?php /* =========================================================================
         ROLE: RECEPTIONIST
         ========================================================================= */ ?>
<?php elseif($role === 'receptionist'): ?>
<div class="sidebar-category-header text-uppercase fw-bold text-primary opacity-75" style="letter-spacing: 0.05em; font-size: 0.7rem;">Front Office Command</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/frontoffice/index" class="sidebar-nav-link <?php echo $isRouteActive('frontoffice/index') ?: ($currentUri === '/school/' || $currentUri === '/school' ? 'active' : ''); ?>">
            <i class="fa fa-desktop text-info fs-5"></i>
            <span class="fw-bold">Reception Command Desk</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/profile/index" class="sidebar-nav-link <?php echo $isRouteActive('profile'); ?>">
            <i class="fa fa-user-circle text-info"></i>
            <span>My Profile &amp; Bio</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Front Office Desk</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions" class="sidebar-nav-link <?php echo $isRouteActive('frontoffice/onlineAdmissions'); ?>">
            <i class="fa fa-inbox text-warning"></i>
            <span>Online Admission Inbox</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/notice/index" class="sidebar-nav-link <?php echo $isRouteActive('notice'); ?>">
            <i class="fa fa-bullhorn text-warning"></i>
            <span>Notice Board &amp; Circulars</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/frontoffice/visitor" class="sidebar-nav-link <?php echo $isRouteActive('frontoffice/visitor'); ?>">
            <i class="fa fa-address-book text-success"></i>
            <span>Visitor Gate Book</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/frontoffice/gatePass" class="sidebar-nav-link <?php echo $isRouteActive('frontoffice/gatePass'); ?>">
            <i class="fa fa-door-open text-purple"></i>
            <span>Student Gate Pass</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/frontoffice/enquiry" class="sidebar-nav-link <?php echo $isRouteActive('frontoffice/enquiry'); ?>">
            <i class="fa fa-user-plus text-info"></i>
            <span>Admission Leads</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/frontoffice/dispatch" class="sidebar-nav-link <?php echo $isRouteActive('frontoffice/dispatch'); ?>">
            <i class="fa fa-envelope-open-text text-secondary"></i>
            <span>Postal &amp; Courier</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/frontoffice/callLog" class="sidebar-nav-link <?php echo $isRouteActive('frontoffice/callLog'); ?>">
            <i class="fa fa-phone-alt text-danger"></i>
            <span>Phone Call Log</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Campus Gate Operations</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/attendance/student" class="sidebar-nav-link <?php echo $isRouteActive('attendance/student'); ?>">
            <i class="fa fa-calendar-check text-success"></i>
            <span>Daily Gate Attendance</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/students/index" class="sidebar-nav-link <?php echo $isRouteActive('students'); ?>">
            <i class="fa fa-user-graduate text-primary"></i>
            <span>Students Directory</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/notice/index" class="sidebar-nav-link <?php echo $isRouteActive('notice'); ?>">
            <i class="fa fa-bullhorn text-warning"></i>
            <span>Notice Board</span>
        </a>
    </li>
</ul>

<?php /* =========================================================================
         ROLE: LIBRARIAN
         ========================================================================= */ ?>
<?php elseif($role === 'librarian'): ?>
<div class="sidebar-category-header text-uppercase fw-bold text-primary opacity-75" style="letter-spacing: 0.05em; font-size: 0.7rem;">Library Hub</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/library/index" class="sidebar-nav-link <?php echo $isRouteActive('library/index') ?: ($currentUri === '/school/' || $currentUri === '/school' ? 'active' : ''); ?>">
            <i class="fa fa-book text-primary fs-5"></i>
            <span class="fw-bold">Library Catalog</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/profile/index" class="sidebar-nav-link <?php echo $isRouteActive('profile'); ?>">
            <i class="fa fa-user-circle text-info"></i>
            <span>My Profile &amp; Bio</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/library/issue_return" class="sidebar-nav-link <?php echo $isRouteActive('library/issue_return'); ?>">
            <i class="fa fa-book-reader text-success"></i>
            <span>Issue &amp; Return Desk</span>
        </a>
    </li>
</ul>

<div class="sidebar-category-header">Operations &amp; Clearance</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/clearance/index" class="sidebar-nav-link <?php echo $isRouteActive('clearance'); ?>">
            <i class="fa fa-clipboard-check text-warning"></i>
            <span>Exit Clearance Hub</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/students/index" class="sidebar-nav-link <?php echo $isRouteActive('students'); ?>">
            <i class="fa fa-user-graduate text-info"></i>
            <span>Students Directory</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/notice/index" class="sidebar-nav-link <?php echo $isRouteActive('notice'); ?>">
            <i class="fa fa-bullhorn text-warning"></i>
            <span>Campus Notice Board</span>
        </a>
    </li>
</ul>

<?php /* =========================================================================
         ROLE: ADMIN / SUPER ADMIN (FULL ERP SUITE)
         ========================================================================= */ ?>
<?php else: ?>
<!-- 1. DASHBOARD AT TOP -->
<div class="sidebar-category-header text-uppercase fw-bold text-primary opacity-75" style="letter-spacing: 0.05em; font-size: 0.7rem;">Main Control Hub</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/admin/dashboard" class="sidebar-nav-link <?php echo $isRouteActive('admin/dashboard') ?: ($currentUri === '/school/' || $currentUri === '/school' ? 'active' : ''); ?>">
            <i class="fa fa-tachometer-alt text-primary fs-5"></i>
            <span class="fw-bold">Principal Dashboard</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/profile/index" class="sidebar-nav-link <?php echo $isRouteActive('profile'); ?>">
            <i class="fa fa-user-circle text-info"></i>
            <span>My Profile &amp; Bio</span>
        </a>
    </li>
</ul>

<!-- 2. FRONT OFFICE COLLAPSIBLE MODULE -->
<div class="sidebar-category-header">Front Office</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a class="sidebar-nav-link d-flex align-items-center justify-content-between <?php echo $isFrontOfficeActive ? 'active' : ''; ?>" data-bs-toggle="collapse" href="#frontOfficeSubmenu" role="button" aria-expanded="<?php echo $isFrontOfficeActive ? 'true' : 'false'; ?>">
            <div>
                <i class="fa fa-headset text-info"></i>
                <span class="fw-bold">Front Office Hub</span>
            </div>
            <i class="fa fa-chevron-down fa-xs text-muted ms-auto"></i>
        </a>
        <div class="collapse <?php echo $isFrontOfficeActive ? 'show' : ''; ?> pt-1 ps-2" id="frontOfficeSubmenu">
            <ul class="sidebar-nav border-start ps-2 my-1" style="border-color: rgba(255,255,255,0.15) !important;">
                <li class="sidebar-nav-item">
                    <a href="<?php echo URLROOT; ?>/frontoffice/index" class="sidebar-nav-link py-1 text-white-50 small <?php echo $isRouteActive('frontoffice/index'); ?>">
                        <i class="fa fa-desktop text-primary me-2"></i> Reception Desk
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?php echo URLROOT; ?>/frontoffice/onlineAdmissions" class="sidebar-nav-link py-1 text-white-50 small <?php echo $isRouteActive('frontoffice/onlineAdmissions'); ?>">
                        <i class="fa fa-inbox text-warning me-2"></i> Online Admission Inbox
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?php echo URLROOT; ?>/notice/index" class="sidebar-nav-link py-1 text-white-50 small <?php echo $isRouteActive('notice'); ?>">
                        <i class="fa fa-bullhorn text-warning me-2"></i> Notice Board &amp; Circulars
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?php echo URLROOT; ?>/frontoffice/visitor" class="sidebar-nav-link py-1 text-white-50 small <?php echo $isRouteActive('frontoffice/visitor'); ?>">
                        <i class="fa fa-address-book text-success me-2"></i> Visitor Gate Book
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?php echo URLROOT; ?>/frontoffice/gatePass" class="sidebar-nav-link py-1 text-white-50 small <?php echo $isRouteActive('frontoffice/gatePass'); ?>">
                        <i class="fa fa-door-open text-purple me-2"></i> Student Gate Pass
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?php echo URLROOT; ?>/frontoffice/enquiry" class="sidebar-nav-link py-1 text-white-50 small <?php echo $isRouteActive('frontoffice/enquiry'); ?>">
                        <i class="fa fa-user-plus text-info me-2"></i> Admission Leads
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?php echo URLROOT; ?>/frontoffice/dispatch" class="sidebar-nav-link py-1 text-white-50 small <?php echo $isRouteActive('frontoffice/dispatch'); ?>">
                        <i class="fa fa-envelope-open-text text-secondary me-2"></i> Postal &amp; Courier
                    </a>
                </li>
                <li class="sidebar-nav-item">
                    <a href="<?php echo URLROOT; ?>/frontoffice/callLog" class="sidebar-nav-link py-1 text-white-50 small <?php echo $isRouteActive('frontoffice/callLog'); ?>">
                        <i class="fa fa-phone-alt text-danger me-2"></i> Phone Call Log
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>

<!-- 3. STUDENT MANAGEMENT -->
<div class="sidebar-category-header">Student Management</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/students/index" class="sidebar-nav-link <?php echo $isRouteActive('students'); ?>">
            <i class="fa fa-user-graduate text-primary"></i>
            <span>Students Directory</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/families/index" class="sidebar-nav-link <?php echo $isRouteActive('families'); ?>">
            <i class="fa fa-people-roof text-info"></i>
            <span>Families &amp; Siblings</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/attendance/student" class="sidebar-nav-link <?php echo $isRouteActive('attendance/student'); ?>">
            <i class="fa fa-calendar-check text-success"></i>
            <span>Daily Gate Attendance</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/attendance/monthly" class="sidebar-nav-link <?php echo $isRouteActive('attendance/monthly'); ?>">
            <i class="fa fa-calendar-days text-warning"></i>
            <span>Monthly Register Matrix</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/parent/index" class="sidebar-nav-link <?php echo $isRouteActive('parent'); ?>">
            <i class="fa fa-child-reaching text-purple"></i>
            <span>Parent &amp; Family Portal</span>
        </a>
    </li>
</ul>

<!-- 4. ACADEMICS -->
<div class="sidebar-category-header">Academics</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/classes/index" class="sidebar-nav-link <?php echo $isRouteActive('classes'); ?>">
            <i class="fa fa-chalkboard text-primary"></i>
            <span>Classes</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/sections/index" class="sidebar-nav-link <?php echo $isRouteActive('sections'); ?>">
            <i class="fa fa-layer-group text-info"></i>
            <span>Sections</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/subjects/index" class="sidebar-nav-link <?php echo ($isRouteActive('subjects/index') || ($isRouteActive('subjects') && strpos($currentUri, 'subjects/assign') === false)) ? 'active' : ''; ?>">
            <i class="fa fa-book-open text-warning"></i>
            <span>Subjects</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/sessions/index" class="sidebar-nav-link <?php echo $isRouteActive('sessions'); ?>">
            <i class="fa fa-calendar-alt text-secondary"></i>
            <span>Academic Sessions</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/subjects/assign" class="sidebar-nav-link <?php echo $isRouteActive('subjects/assign'); ?>">
            <i class="fa fa-user-check text-success"></i>
            <span>Teacher Allocation</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/timetable/index" class="sidebar-nav-link <?php echo $isRouteActive('timetable'); ?>">
            <i class="fa fa-clock text-danger"></i>
            <span>Class Timetable</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/homework/index" class="sidebar-nav-link <?php echo $isRouteActive('homework'); ?>">
            <i class="fa fa-book-reader text-purple"></i>
            <span>Homework</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/promote/index" class="sidebar-nav-link <?php echo $isRouteActive('promote'); ?>">
            <i class="fa fa-level-up-alt text-primary"></i>
            <span>Promotion Committee</span>
        </a>
    </li>
</ul>

<!-- 5. EXAMINATIONS -->
<div class="sidebar-category-header">Examinations</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/exam/index" class="sidebar-nav-link <?php echo ($isRouteActive('exam/index') || ($isRouteActive('exam') && strpos($currentUri, 'exam/schedule') === false && strpos($currentUri, 'exam/marks') === false && strpos($currentUri, 'exam/approval') === false && strpos($currentUri, 'exam/gazette') === false && strpos($currentUri, 'exam/signature') === false)) ? 'active' : ''; ?>">
            <i class="fa fa-file-signature text-warning"></i>
            <span>Exams &amp; Papers</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/exam/schedule" class="sidebar-nav-link <?php echo $isRouteActive('exam/schedule'); ?>">
            <i class="fa fa-calendar-check text-primary"></i>
            <span>Exam Schedule</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/exam/marks" class="sidebar-nav-link <?php echo $isRouteActive('exam/marks'); ?>">
            <i class="fa fa-marker text-success"></i>
            <span>Online Marks Entry</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/exam/approval" class="sidebar-nav-link <?php echo $isRouteActive('exam/approval'); ?>">
            <i class="fa fa-stamp text-info"></i>
            <span>Result Approval Chain</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/exam/signatureSlip" class="sidebar-nav-link <?php echo $isRouteActive('exam/signatureSlip') ?: $isRouteActive('exam/hallSheet'); ?>">
            <i class="fa fa-id-card text-purple"></i>
            <span>Candidate Signature Slip</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/exam/gazette" class="sidebar-nav-link <?php echo $isRouteActive('exam/gazette') ?: $isRouteActive('exam/results'); ?>">
            <i class="fa fa-award text-warning"></i>
            <span>Result Gazette &amp; DMC</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/certificate/hub" class="sidebar-nav-link <?php echo $isRouteActive('certificate'); ?>">
            <i class="fa fa-certificate text-secondary"></i>
            <span>Certificates &amp; SLC</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/clearance/index" class="sidebar-nav-link <?php echo $isRouteActive('clearance'); ?>">
            <i class="fa fa-clipboard-check text-danger"></i>
            <span>Exit Clearance Hub</span>
        </a>
    </li>
</ul>

<!-- 6. FINANCE & ACCOUNTS -->
<div class="sidebar-category-header">Finance &amp; Accounts</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/fees/collect" class="sidebar-nav-link <?php echo $isRouteActive('fees/collect'); ?>">
            <i class="fa fa-receipt text-success"></i>
            <span>Fees Collection</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/fees/challan" class="sidebar-nav-link <?php echo $isRouteActive('fees/challan'); ?>">
            <i class="fa fa-money-check text-primary"></i>
            <span>Bank Fee Challans</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/fees/defaulters" class="sidebar-nav-link <?php echo $isRouteActive('fees/defaulters'); ?>">
            <i class="fa fa-user-clock text-danger"></i>
            <span>Fee Defaulters Ledger</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/incomes/index" class="sidebar-nav-link <?php echo $isRouteActive('incomes'); ?>">
            <i class="fa fa-hand-holding-dollar text-warning"></i>
            <span>Incomes &amp; Cash Book</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/expense/index" class="sidebar-nav-link <?php echo $isRouteActive('expense'); ?>">
            <i class="fa fa-wallet text-secondary"></i>
            <span>Expenses</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/payroll/index" class="sidebar-nav-link <?php echo $isRouteActive('payroll'); ?>">
            <i class="fa fa-money-check-dollar text-purple"></i>
            <span>Staff Payroll</span>
        </a>
    </li>
</ul>

<!-- 7. ADMINISTRATION & STAFF -->
<div class="sidebar-category-header">Administration &amp; Staff</div>
<ul class="sidebar-nav mb-3">
    <?php if($role === 'super_admin'): ?>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/admin/schools" class="sidebar-nav-link <?php echo $isRouteActive('admin/schools'); ?>">
            <i class="fa fa-school text-warning"></i>
            <span>All Schools</span>
        </a>
    </li>
    <?php endif; ?>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/staff/index" class="sidebar-nav-link <?php echo $isRouteActive('staff'); ?>">
            <i class="fa fa-id-badge text-info"></i>
            <span>Staff Directory</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/admin/users" class="sidebar-nav-link <?php echo $isRouteActive('admin/users'); ?>">
            <i class="fa fa-users-cog text-primary"></i>
            <span>All Users</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/notice/index" class="sidebar-nav-link <?php echo $isRouteActive('notice'); ?>">
            <i class="fa fa-bullhorn text-warning"></i>
            <span>Notice Board</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/frontcms/pages" class="sidebar-nav-link <?php echo (strpos($currentUri, 'frontcms') !== false) ? 'active' : ''; ?>">
            <i class="fa fa-globe text-success"></i>
            <span>Front CMS &amp; Menus</span>
        </a>
    </li>
</ul>

<!-- 8. RESOURCES & LOGISTICS -->
<div class="sidebar-category-header">Resources &amp; Logistics</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/library/index" class="sidebar-nav-link <?php echo $isRouteActive('library'); ?>">
            <i class="fa fa-book text-success"></i>
            <span>Library</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/inventory/items" class="sidebar-nav-link <?php echo $isRouteActive('inventory'); ?>">
            <i class="fa fa-boxes text-purple"></i>
            <span>Inventory</span>
        </a>
    </li>
</ul>

<!-- 9. SYSTEM & INTELLIGENCE -->
<div class="sidebar-category-header">System &amp; Intelligence</div>
<ul class="sidebar-nav mb-3">
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/reports/index" class="sidebar-nav-link <?php echo $isRouteActive('reports'); ?>">
            <i class="fa fa-chart-line text-info"></i>
            <span>Reports Center</span>
        </a>
    </li>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/setting/index" class="sidebar-nav-link <?php echo $isRouteActive('setting'); ?>">
            <i class="fa fa-sliders text-warning"></i>
            <span>Settings &amp; Roles</span>
        </a>
    </li>
    <?php if(in_array($role, ['admin', 'super_admin'])): ?>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/admin/schools" class="sidebar-nav-link <?php echo $isRouteActive('admin/schools'); ?>">
            <i class="fa fa-code-branch text-success"></i>
            <span>Campuses &amp; Branches</span>
        </a>
    </li>
    <?php endif; ?>
    <li class="sidebar-nav-item">
        <a href="<?php echo URLROOT; ?>/profile/index" class="sidebar-nav-link <?php echo $isRouteActive('profile'); ?>">
            <i class="fa fa-user-gear text-primary"></i>
            <span>Profile &amp; Bio Settings</span>
        </a>
    </li>
</ul>
<?php endif; ?>

<?php endif; ?>
