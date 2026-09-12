<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row">
        <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'teacher' || $_SESSION['user_role'] == 'super_admin'): ?>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Upload Content</div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/downloadcenter/index" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Content Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content Type</label>
                            <select name="content_type" class="form-select" required>
                                <option value="Assignment">Assignment</option>
                                <option value="Study Material">Study Material</option>
                                <option value="Syllabus">Syllabus</option>
                                <option value="Other">Other Issues</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Available For</label>
                            <select name="available_for" class="form-select" required>
                                <option value="all">All</option>
                                <option value="student">Student</option>
                                <option value="staff">Staff Only</option>
                            </select>
                        </div>
                         <div class="mb-3">
                            <label class="form-label">Class (Optional)</label>
                            <select name="class_id" class="form-select">
                                <option value="">Select Class (All)</option>
                                <?php foreach($data['classes'] as $class): ?>
                                    <option value="<?php echo $class->id; ?>"><?php echo $class->class_name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Upload</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="<?php echo ($_SESSION['user_role'] == 'student') ? 'col-md-12' : 'col-md-8'; ?>">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Download Center</div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Content Title</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Available For</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['contents'] as $content): ?>
                                <tr>
                                    <td><?php echo $content->content_title; ?></td>
                                    <td><?php echo $content->content_type; ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($content->upload_date)); ?></td>
                                    <td>
                                        <?php echo ucfirst($content->available_for); ?>
                                        <?php if($content->class_name) echo " (" . $content->class_name . ")"; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo URLROOT; ?>/<?php echo $content->file_path; ?>" class="btn btn-sm btn-info text-white" download title="Download"><i class="fa fa-download"></i></a>
                                        <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'super_admin'): ?>
                                            <a href="<?php echo URLROOT; ?>/downloadcenter/delete/<?php echo $content->id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')" title="Delete"><i class="fa fa-trash"></i></a>
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
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
