<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Add Student House</div>
                <div class="card-body">
                    <form action="<?php echo URLROOT; ?>/students/houses" method="post">
                        <div class="mb-3">
                            <label class="form-label">House Name</label>
                            <input type="text" name="house_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">Student House List</div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>House Name</th>
                                <th>Description</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['houses'] as $house): ?>
                                <tr>
                                    <td><?php echo $house->house_name; ?></td>
                                    <td><?php echo $house->description; ?></td>
                                    <td class="text-end">
                                        <form action="<?php echo URLROOT; ?>/students/houses" method="post" class="d-inline">
                                            <input type="hidden" name="delete_id" value="<?php echo $house->id; ?>">
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
