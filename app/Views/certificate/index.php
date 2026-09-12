<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row fade-in">
        <div class="col-md-12 mb-2">
            <div class="btn-group shadow-sm">
                <a href="<?php echo URLROOT; ?>/certificate/index" class="btn btn-outline-primary active">Certificate Template</a>
                <a href="<?php echo URLROOT; ?>/certificate/generate" class="btn btn-outline-primary">Generate Certificate</a>
            </div>
            <hr>
        </div>

        <div class="col-md-4 mb-4">
             <div class="card shadow-sm border-0 h-100">
                 <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                     <h5 class="mb-0 text-primary">Add Certificate</h5>
                 </div>
                 <div class="card-body">
                      <form action="" method="post" enctype="multipart/form-data">
                          <div class="mb-3">
                              <label class="form-label">Certificate Name</label>
                              <input type="text" name="certificate_name" class="form-control" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Header Text</label>
                              <div class="row g-2">
                                  <div class="col-4"><input type="text" name="left_header" class="form-control form-control-sm" placeholder="Left"></div>
                                  <div class="col-4"><input type="text" name="center_header" class="form-control form-control-sm" placeholder="Center" value="Certificate of Merit"></div>
                                  <div class="col-4"><input type="text" name="right_header" class="form-control form-control-sm" placeholder="Right"></div>
                              </div>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Body Text</label>
                              <textarea name="certificate_text" class="form-control" rows="5" placeholder="This is to certify that [name] of Class [class] has..."></textarea>
                              <div class="form-text small">Tags: [name], [dob], [class], [admission_no], [roll_no]</div>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Footer Text</label>
                              <div class="row g-2">
                                  <div class="col-4"><input type="text" name="left_footer" class="form-control form-control-sm" placeholder="Left"></div>
                                  <div class="col-4"><input type="text" name="center_footer" class="form-control form-control-sm" placeholder="Center"></div>
                                  <div class="col-4"><input type="text" name="right_footer" class="form-control form-control-sm" placeholder="Right"></div>
                              </div>
                          </div>
                           <div class="mb-3">
                              <label class="form-label">Background Image</label>
                              <input type="file" name="background_image" class="form-control">
                          </div>
                          <button type="submit" class="btn btn-primary w-100 shadow-sm">Save Template</button>
                      </form>
                 </div>
             </div>
        </div>

        <div class="col-md-8 mb-4">
             <div class="card shadow-sm border-0 h-100">
                 <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                     <h5 class="mb-0">Certificate List</h5>
                 </div>
                 <div class="card-body">
                     <div class="table-responsive">
                         <table class="table table-hover align-middle">
                             <thead class="table-light">
                                 <tr>
                                     <th>Name</th>
                                     <th>Background</th>
                                     <th>Action</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php foreach($data['certificates'] as $cert): ?>
                                     <tr>
                                         <td class="fw-bold"><?php echo $cert->certificate_name; ?></td>
                                         <td>
                                             <?php if($cert->background_image): ?>
                                                 <img src="<?php echo URLROOT . '/' . $cert->background_image; ?>" height="40">
                                             <?php else: ?>
                                                 <span class="text-muted">None</span>
                                             <?php endif; ?>
                                         </td>
                                         <td>
                                             <a href="<?php echo URLROOT; ?>/certificate/delete/<?php echo $cert->id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="fa fa-trash"></i></a>
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
