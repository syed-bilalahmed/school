<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="row fade-in">
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary"><i class="fa fa-file-csv me-2"></i> Import Students</h5>
                <a href="<?php echo URLROOT; ?>/students/index" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left me-2"></i> Back to List</a>
            </div>
            <div class="card-body p-4">
                
                <div class="alert alert-info border-0 bg-light-info">
                    <h6 class="alert-heading fw-bold"><i class="fa fa-info-circle me-2"></i> Instructions</h6>
                    <ul class="mb-0 small text-muted">
                        <li>Download the sample CSV file to view the required format.</li>
                        <li>Ensure the <strong>Class ID</strong> and <strong>Section ID</strong> match the system IDs (see below).</li>
                        <li><strong>Email</strong> must be unique for every student.</li>
                        <li><strong>Roll Number</strong> should be unique within the class.</li>
                    </ul>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold mb-3">Step 1: Download Sample</h6>
                        <p class="text-muted small">Get the template with the correct headers.</p>
                        <a href="<?php echo URLROOT; ?>/students/downloadSample" class="btn btn-outline-primary"><i class="fa fa-download me-2"></i> Download Sample CSV</a>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <h6 class="fw-bold mb-3">Step 2: Upload File</h6>
                        <form action="<?php echo URLROOT; ?>/students/import" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Select CSV File</label>
                                <input type="file" name="file" class="form-control" accept=".csv" required>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fa fa-upload me-2"></i> Import Students</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Helper Section for IDs -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
             <div class="card-header bg-white">
                 <h6 class="mb-0">Class & Section Reference IDs</h6>
             </div>
             <div class="card-body">
                 <div class="table-responsive">
                     <table class="table table-sm table-bordered text-center">
                         <thead class="table-light">
                             <tr>
                                 <th>Class</th>
                                 <th>Class ID</th>
                                 <th>Sections (ID)</th>
                             </tr>
                         </thead>
                         <tbody>
                             <?php foreach($data['classes'] as $class): ?>
                                 <tr>
                                     <td class="fw-bold"><?php echo $class->class_name; ?></td>
                                     <td><span class="badge bg-secondary"><?php echo $class->id; ?></span></td>
                                     <td>
                                         <?php 
                                            // Ideally we pass sections mapped by class, but for now we iterate (inefficient but works for small sets)
                                            if(!empty($data['sections'])){
                                                foreach($data['sections'] as $sec){
                                                    if($sec->class_id == $class->id){
                                                        echo "<span class='badge bg-light text-dark border me-1'>{$sec->section_name} ({$sec->id})</span>";
                                                    }
                                                }
                                            }
                                         ?>
                                     </td>
                                 </tr>
                             <?php endforeach; ?>
                         </tbody>
                     </table>
                 </div>
             </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
