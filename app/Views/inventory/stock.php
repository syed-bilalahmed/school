<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row fade-in">
        <div class="col-md-12 mb-2">
            <div class="btn-group shadow-sm">
                <a href="<?php echo URLROOT; ?>/inventory/items" class="btn btn-outline-primary">Item List</a>
                <a href="<?php echo URLROOT; ?>/inventory/add_stock" class="btn btn-outline-primary active">Add Stock</a>
                <a href="<?php echo URLROOT; ?>/inventory/issue_item" class="btn btn-outline-primary">Issue Item</a>
                <a href="<?php echo URLROOT; ?>/inventory/setup" class="btn btn-outline-primary">Setup (Supplier/Store)</a>
            </div>
            <hr>
        </div>

        <div class="col-md-4 mb-4">
             <div class="card shadow-sm border-0 h-100">
                 <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                     <h5 class="mb-0 text-success">Add Stock</h5>
                 </div>
                 <div class="card-body">
                      <form action="" method="post" enctype="multipart/form-data">
                          <div class="mb-3">
                              <label class="form-label">Item</label>
                              <select name="item_id" class="form-select" required>
                                  <option value="">Select Item</option>
                                  <?php foreach($data['items'] as $i): ?>
                                      <option value="<?php echo $i->id; ?>"><?php echo $i->name; ?></option>
                                  <?php endforeach; ?>
                              </select>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Supplier</label>
                              <select name="supplier_id" class="form-select">
                                  <option value="">Select Supplier</option>
                                  <?php foreach($data['suppliers'] as $s): ?>
                                      <option value="<?php echo $s->id; ?>"><?php echo $s->item_supplier; ?></option>
                                  <?php endforeach; ?>
                              </select>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Store</label>
                              <select name="store_id" class="form-select">
                                  <option value="">Select Store</option>
                                  <?php foreach($data['stores'] as $st): ?>
                                      <option value="<?php echo $st->id; ?>"><?php echo $st->item_store; ?></option>
                                  <?php endforeach; ?>
                              </select>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Quantity</label>
                              <input type="number" name="quantity" class="form-control" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Date</label>
                              <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Attachment</label>
                              <input type="file" name="attachment" class="form-control">
                          </div>
                          <button type="submit" class="btn btn-success w-100 shadow-sm">Add Stock</button>
                      </form>
                 </div>
             </div>
        </div>

        <div class="col-md-8 mb-4">
             <div class="card shadow-sm border-0 h-100">
                 <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                     <h5 class="mb-0">Stock History</h5>
                 </div>
                 <div class="card-body">
                      <div class="table-responsive">
                          <table class="table table-hover align-middle">
                              <thead class="table-light">
                                  <tr>
                                      <th>Item</th>
                                      <th>Store</th>
                                      <th>Supplier</th>
                                      <th>Qty</th>
                                      <th>Date</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <?php foreach($data['stock_list'] as $st): ?>
                                      <tr>
                                          <td><?php echo $st->item_name; ?> <br><small class="text-muted"><?php echo $st->item_category; ?></small></td>
                                          <td><?php echo $st->item_store; ?></td>
                                          <td><?php echo $st->item_supplier; ?></td>
                                          <td class="fw-bold text-success">+<?php echo $st->quantity; ?></td>
                                          <td><?php echo date('d-m-Y', strtotime($st->date)); ?></td>
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
