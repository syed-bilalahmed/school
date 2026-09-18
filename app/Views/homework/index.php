<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row">
        <?php if(AuthGuard::hasPermission('manage_academics')): ?>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">Add Homework</div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/homework/index" method="post">
                        <?php if(!isset($_GET['class_id'])): ?>
                            <div class="alert alert-info">Please select class and section from filters first.</div>
                        <?php else: ?>
                            <input type="hidden" name="add_homework" value="1">
                            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($_GET['class_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($_GET['section_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <select name="subject_id" class="form-select" required>
                                    <option value="">Select Subject</option>
                                    <?php foreach($data['subjects'] as $sub): ?>
                                        <option value="<?php echo $sub->subject_id; ?>"><?php echo $sub->subject_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Homework Date</label>
                                <input type="date" name="homework_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Submission Date</label>
                                <input type="date" name="submission_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Homework</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="<?php echo ($_SESSION['user_role'] == 'student') ? 'col-md-12' : 'col-md-8'; ?>">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form action="" method="get" class="row g-2">
                        <div class="col-md-4">
                            <select name="class_id" id="classSelect" class="form-select" required onchange="filterSections()">
                                <option value="">Select Class</option>
                                <?php foreach($data['classes'] as $class): ?>
                                    <option value="<?php echo $class->id; ?>" <?php echo (isset($_GET['class_id']) && $_GET['class_id'] == $class->id) ? 'selected' : ''; ?>><?php echo $class->class_name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="section_id" id="sectionSelect" class="form-select" required>
                                <option value="">Select Section</option>
                                <?php foreach($data['sections'] as $section): ?>
                                     <option value="<?php echo $section->id; ?>" data-class="<?php echo $section->class_id; ?>" class="section-option">
                                         <?php echo $section->section_name; ?>
                                     </option>
                                 <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-dark w-100">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header">Homework List</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Subject</th>
                                    <th>Homework Date</th>
                                    <th>Submission Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['homeworks'] as $hw): ?>
                                    <tr>
                                        <td><?php echo $hw->class_name; ?></td>
                                        <td><?php echo $hw->section_name; ?></td>
                                        <td><?php echo $hw->subject_name; ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($hw->homework_date)); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($hw->submission_date)); ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                                            <?php if($_SESSION['user_role'] != 'student'): ?>
                                                <a href="<?php echo URLROOT; ?>/homework/evaluate/<?php echo $hw->id; ?>" class="btn btn-sm btn-primary">Evaluate</a>
                                            <?php endif; ?>
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
    <script>
        function filterSections(){
            var classId = document.getElementById('classSelect').value;
            var options = document.getElementsByClassName('section-option');
            for(var i=0; i<options.length; i++){
                if(options[i].getAttribute('data-class') == classId){
                    options[i].style.display = 'block';
                } else {
                    options[i].style.display = 'none';
                }
            }
        }
        if(document.getElementById('classSelect').value) {
            filterSections();
            document.getElementById('sectionSelect').value = "<?php echo isset($_GET['section_id']) ? $_GET['section_id'] : ''; ?>";
        }
    </script>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
