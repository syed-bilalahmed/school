<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row fade-in">
        <div class="col-md-12 mb-2">
            <div class="btn-group shadow-sm">
                <a href="<?php echo URLROOT; ?>/certificate/index" class="btn btn-outline-primary">Certificate Template</a>
                <a href="<?php echo URLROOT; ?>/certificate/generate" class="btn btn-outline-primary active">Generate Certificate</a>
            </div>
            <hr>
        </div>

        <div class="col-md-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="mb-0">Select Criteria</h5>
                </div>
                <div class="card-body">
                    <form action="" method="get" class="row align-items-end">
                         <div class="col-md-4 mb-3">
                            <label class="form-label">Class</label>
                            <select name="class_id" id="class_id" class="form-select" required onchange="this.form.submit()">
                                <option value="">Select Class</option>
                                <?php foreach($data['classes'] as $class): ?>
                                    <option value="<?php echo $class->id; ?>" <?php echo (isset($_GET['class_id']) && $_GET['class_id'] == $class->id) ? 'selected' : ''; ?>>
                                        <?php echo $class->class_name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                             <label class="form-label">Section</label>
                             <select name="section_id" id="section_id" class="form-select" required>
                                 <option value="">Select Section</option>
                                 <!-- Populated by PHP if class selected -->
                                 <?php if(isset($data['sections'])): ?>
                                     <?php foreach($data['sections'] as $sec): ?>
                                          <?php if(isset($_GET['class_id']) && $sec->class_id == $_GET['class_id']): ?>
                                              <option value="<?php echo $sec->id; ?>" <?php echo (isset($_GET['section_id']) && $_GET['section_id'] == $sec->id) ? 'selected' : ''; ?>>
                                                  <?php echo $sec->section_name; ?>
                                              </option>
                                          <?php endif; ?>
                                     <?php endforeach; ?>
                                 <?php endif; ?>
                             </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if(!empty($data['students'])): ?>
        <div class="col-md-12">
             <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between">
                    <h5 class="mb-0">Student List</h5>
                </div>
                <div class="card-body">
                    <form action="" method="post" target="_blank">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Select Certificate Template</label>
                                <select name="certificate_id" class="form-select" required>
                                    <option value="">Select Certificate</option>
                                    <?php foreach($data['certificates'] as $cert): ?>
                                        <option value="<?php echo $cert->id; ?>"><?php echo $cert->certificate_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" name="print" value="1" class="btn btn-success"><i class="fa fa-print me-2"></i> Generate & Print</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50"><input type="checkbox" id="selectAll"></th>
                                        <th>Roll No</th>
                                        <th>Name</th>
                                        <th>Admission No</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data['students'] as $student): ?>
                                        <tr>
                                            <td><input type="checkbox" name="students[]" value="<?php echo $student->id; ?>" class="student-checkbox"></td>
                                            <td><?php echo $student->roll_no; ?></td>
                                            <td class="fw-bold"><?php echo $student->name; ?></td>
                                            <td><?php echo $student->admission_no; ?></td>
                                            <td><?php echo $student->email; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
             </div>
        </div>
        <script>
            document.getElementById('selectAll').addEventListener('change', function() {
                var checkboxes = document.querySelectorAll('.student-checkbox');
                for (var checkbox of checkboxes) {
                    checkbox.checked = this.checked;
                }
            });
        </script>
        <?php endif; ?>
    </div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
