<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row fade-in">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="mb-0 text-primary"><i class="fa fa-book-reader me-2"></i> Issue Book</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="" method="post">
                        <input type="hidden" name="issue_book" value="1">
                        <div class="mb-3">
                            <label class="form-label">Member (Student/Staff)</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Select Member</option>
                                <?php foreach($data['members'] as $m): ?>
                                    <option value="<?php echo $m->id; ?>"><?php echo $m->name . ' (' . ucfirst($m->role) . ')'; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Type to search functionality can be enhanced via JS.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Book</label>
                            <select name="book_id" class="form-select" required>
                                <option value="">Select Book</option>
                                <?php foreach($data['books'] as $b): ?>
                                    <?php if($b->qty > 0): ?>
                                    <option value="<?php echo $b->id; ?>"><?php echo $b->book_title; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Issue Date</label>
                            <input type="date" name="issue_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                         <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">Issue Book</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
             <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark"><i class="fa fa-list me-2"></i> Issued Books List</h5>
                </div>
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Book</th>
                                    <th>Member</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['issued_books'] as $issue): ?>
                                    <tr>
                                        <td class="ps-4"><?php echo $issue->book_title; ?> <br><small class="text-muted"><?php echo $issue->book_no; ?></small></td>
                                        <td><?php echo $issue->user_name; ?> <br><small class="text-muted"><?php echo ucfirst($issue->role); ?></small></td>
                                        <td><?php echo date('d-m-Y', strtotime($issue->issue_date)); ?></td>
                                        <td>
                                            <?php 
                                            $due = strtotime($issue->due_date);
                                            $today = time();
                                            $class = ($today > $due) ? 'text-danger fw-bold' : '';
                                            echo "<span class='$class'>" . date('d-m-Y', $due) . "</span>";
                                            ?>
                                        </td>
                                        <td><span class="badge bg-warning text-dark">Issued</span></td>
                                        <td class="pe-4 text-end">
                                            <form action="" method="post">
                                                <input type="hidden" name="return_book" value="1">
                                                <input type="hidden" name="issue_id" value="<?php echo $issue->id; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Return Book"><i class="fa fa-undo"></i> Return</button>
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
    </div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
