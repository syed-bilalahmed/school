<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row fade-in">
        <div class="col-md-12 mb-2">
            <div class="btn-group shadow-sm">
                <a href="<?php echo URLROOT; ?>/inventory/items" class="btn btn-outline-primary">Item List</a>
                <a href="<?php echo URLROOT; ?>/inventory/add_stock" class="btn btn-outline-primary">Add Stock</a>
                <a href="<?php echo URLROOT; ?>/inventory/issue_item" class="btn btn-outline-primary active">Issue Item</a>
                <a href="<?php echo URLROOT; ?>/inventory/setup" class="btn btn-outline-primary">Setup (Supplier/Store)</a>
            </div>
            <hr>
        </div>

        <div class="col-md-4 mb-4">
             <div class="card shadow-sm border-0 h-100">
                 <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                     <h5 class="mb-0 text-warning">Issue Item</h5>
                 </div>
                 <div class="card-body">
                      <form action="" method="post">
                          <div class="mb-3">
                              <label class="form-label">Issue To (User)</label>
                              <select name="issue_to" class="form-select" required>
                                  <option value="">Select User</option>
                                  <?php foreach($data['users'] as $u): ?>
                                      <option value="<?php echo $u->id; ?>"><?php echo $u->name . ' (' . ucfirst($u->role) . ')'; ?></option>
                                  <?php endforeach; ?>
                              </select>
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
                              <label class="form-label">Item</label>
                              <select name="item_id" class="form-select" required>
                                  <option value="">Select Item</option>
                                  <?php foreach($data['items'] as $i): ?>
                                      <option value="<?php echo $i->id; ?>"><?php echo $i->name; ?></option>
                                  <?php endforeach; ?>
                              </select>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Quantity</label>
                              <input type="number" name="quantity" class="form-control" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Issue Date</label>
                              <input type="date" name="issue_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Return Date</label>
                              <input type="date" name="return_date" class="form-control">
                          </div>
                          <div class="mb-3">
                              <label class="form-label">Note</label>
                              <textarea name="note" class="form-control"></textarea>
                          </div>
                          <button type="submit" class="btn btn-warning w-100 shadow-sm text-white">Issue</button>
                      </form>
                 </div>
             </div>
        </div>

        <div class="col-md-8 mb-4">
             <div class="card shadow-sm border-0 h-100">
                 <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                     <h5 class="mb-0">Issued Items</h5>
                 </div>
                 <div class="card-body">
                      <div class="table-responsive">
                          <table class="table table-hover align-middle">
                              <thead class="table-light">
                                  <tr>
                                      <th>Item</th>
                                      <th>Issued To</th>
                                      <th>Qty</th>
                                      <th>Date</th>
                                      <th>Status</th>
                                      <th>Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <?php foreach($data['issued_items'] as $iss): ?>
                                      <tr>
                                          <td><?php echo $iss->item_name; ?></td>
                                          <td><?php echo $iss->user_name; ?> <br><small class="text-muted"><?php echo ucfirst($iss->user_role); ?></small></td>
                                          <td class="fw-bold">-<?php echo $iss->quantity; ?></td>
                                          <td><?php echo date('d-m-Y', strtotime($iss->issue_date)); ?></td>
                                          <td>
                                              <?php if($iss->is_returned): ?>
                                                  <span class="badge bg-success">Returned</span>
                                              <?php else: ?>
                                                  <span class="badge bg-danger">Issued</span>
                                              <?php endif; ?>
                                          </td>
                                          <td>
                                              <?php if(!$iss->is_returned): ?>
                                                  <form action="" method="post">
                                                      <input type="hidden" name="return_id" value="<?php echo $iss->id; ?>">
                                                      <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Confirm Return?')"><i class="fa fa-undo"></i></button>
                                                  </form>
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
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
