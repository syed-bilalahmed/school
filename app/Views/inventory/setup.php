<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row fade-in">
        <div class="col-md-12 mb-2">
            <div class="btn-group shadow-sm">
                <a href="<?php echo URLROOT; ?>/inventory/items" class="btn btn-outline-primary">Item List</a>
                <a href="<?php echo URLROOT; ?>/inventory/add_stock" class="btn btn-outline-primary">Add Stock</a>
                <a href="<?php echo URLROOT; ?>/inventory/issue_item" class="btn btn-outline-primary">Issue Item</a>
                <a href="<?php echo URLROOT; ?>/inventory/setup" class="btn btn-outline-primary active">Setup (Supplier/Store)</a>
            </div>
            <hr>
        </div>

        <!-- Add Category -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h6 class="mb-0 text-primary">Add Item Category</h6>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <input type="hidden" name="add_category" value="1">
                        <div class="mb-3">
                            <input type="text" name="item_category" class="form-control" placeholder="Category Name" required>
                        </div>
                        <div class="mb-3">
                            <textarea name="description" class="form-control" placeholder="Description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">Save Category</button>
                    </form>
                    <hr>
                    <ul class="list-group list-group-flush small">
                        <?php foreach($data['categories'] as $c): ?>
                            <li class="list-group-item"><?php echo $c->item_category; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Add Store -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                     <h6 class="mb-0 text-success">Add Item Store</h6>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <input type="hidden" name="add_store" value="1">
                        <div class="mb-3">
                            <input type="text" name="item_store" class="form-control" placeholder="Store Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="code" class="form-control" placeholder="Store Code">
                        </div>
                         <div class="mb-3">
                            <textarea name="description" class="form-control" placeholder="Description"></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-success w-100">Save Store</button>
                    </form>
                    <hr>
                    <ul class="list-group list-group-flush small">
                        <?php foreach($data['stores'] as $s): ?>
                            <li class="list-group-item"><?php echo $s->item_store; ?> (<?php echo $s->code; ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Add Supplier -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                     <h6 class="mb-0 text-info">Add Supplier</h6>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <input type="hidden" name="add_supplier" value="1">
                        <div class="mb-3">
                            <input type="text" name="item_supplier" class="form-control" placeholder="Supplier Name" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="contact_person_name" class="form-control" placeholder="Contact Person">
                        </div>
                        <div class="mb-3">
                            <input type="text" name="phone" class="form-control" placeholder="Phone">
                        </div>
                         <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email">
                        </div>
                         <div class="mb-3">
                            <textarea name="address" class="form-control" placeholder="Address"></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-info text-white w-100">Save Supplier</button>
                    </form>
                     <hr>
                    <ul class="list-group list-group-flush small">
                        <?php foreach($data['suppliers'] as $sup): ?>
                            <li class="list-group-item"><?php echo $sup->item_supplier; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>

