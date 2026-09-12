<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row">
        <div class="col-md-12 mb-3">
            <h3>Data Export</h3>
        </div>
        
        <div class="col-md-6 mb-4">
             <div class="card shadow-sm">
                 <div class="card-body text-center">
                     <i class="fa fa-user-graduate fa-3x text-primary mb-3"></i>
                     <h5 class="card-title">Export Students</h5>
                     <p class="text-muted">Download all student data in CSV format.</p>
                     <a href="<?php echo URLROOT; ?>/export/students" class="btn btn-primary"><i class="fa fa-download"></i> Export CSV</a>
                 </div>
             </div>
        </div>

        <div class="col-md-6 mb-4">
             <div class="card shadow-sm">
                 <div class="card-body text-center">
                     <i class="fa fa-chalkboard-teacher fa-3x text-success mb-3"></i>
                     <h5 class="card-title">Export Staff</h5>
                     <p class="text-muted">Download list of all staff members in CSV format.</p>
                     <a href="<?php echo URLROOT; ?>/export/staff" class="btn btn-success"><i class="fa fa-download"></i> Export CSV</a>
                 </div>
             </div>
        </div>
    </div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
