<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Add Student Category</div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/students/categories" method="post">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="category_name" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Student Category List</div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['categories'] as $cat): ?>
                                <tr>
                                    <td><?php echo $cat->category_name; ?></td>
                                    <td class="text-end">
                                        <form action="<?php echo URLROOT; ?>/students/categories" method="post" class="d-inline">
                                            <input type="hidden" name="delete_id" value="<?php echo $cat->id; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?');"><i class="fa fa-times"></i></button>
                                        </form>
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
