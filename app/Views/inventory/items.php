<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row">
        <div class="col-md-12 mb-2">
            <div class="btn-group shadow-sm">
                <a href="<?php echo URLROOT; ?>/inventory/items" class="btn btn-outline-primary active">Item List</a>
                <a href="<?php echo URLROOT; ?>/inventory/add_stock" class="btn btn-outline-primary">Add Stock</a>
                <a href="<?php echo URLROOT; ?>/inventory/issue_item" class="btn btn-outline-primary">Issue Item</a>
                <a href="<?php echo URLROOT; ?>/inventory/setup" class="btn btn-outline-primary">Setup (Supplier/Store)</a>
            </div>
            <hr>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">Add Item</div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="item_category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                <?php foreach($data['categories'] as $c): ?>
                                    <option value="<?php echo $c->id; ?>"><?php echo $c->item_category; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Unit</label>
                            <input type="text" name="unit" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Item</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header">Item List</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Unit</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['items'] as $item): ?>
                                <tr>
                                    <td><?php echo $item->name; ?></td>
                                    <td><?php echo $item->item_category; ?></td>
                                    <td><?php echo $item->unit; ?></td>
                                    <td><?php echo $item->description; ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
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
