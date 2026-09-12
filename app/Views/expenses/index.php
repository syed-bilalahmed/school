<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="row fade-in">
        <!-- Add Expense Column -->
        <div class="col-md-4 mb-4">
             <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                    <h5 class="mb-0 text-danger"><i class="fa fa-plus-circle me-2"></i> Add Expense</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="add_expense" value="1">
                        <div class="mb-3">
                            <label class="form-label">Expense Head</label>
                            <div class="d-flex">
                                <select name="exp_head_id" class="form-select me-2" required>
                                    <option value="">Select Head</option>
                                    <?php foreach($data['heads'] as $h): ?>
                                        <option value="<?php echo $h->id; ?>"><?php echo $h->exp_category; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addHeadModal" title="Add New Head"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Invoice Number</label>
                            <input type="text" name="invoice_no" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount (<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?>)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Attach Document</label>
                            <input type="file" name="documents" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 shadow-sm">Save Expense</button>
                    </form>
                </div>
             </div>
        </div>

        <!-- Expense List Column -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark"><i class="fa fa-list me-2"></i> Expense List</h5>
                    <form action="" method="get" class="d-flex" style="width: 250px;">
                        <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search..." value="<?php echo $data['search']; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa fa-search"></i></button>
                    </form>
                </div>
                <div class="px-4 pt-2 text-muted small">Total Records: <?php echo (int)($data['total_expenses'] ?? 0); ?></div>
                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Name/Invoice</th>
                                    <th>Date</th>
                                    <th>Expense Head</th>
                                    <th>Amount</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data['expenses'] as $exp): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <?php echo $exp->name; ?>
                                            <?php if($exp->invoice_no): ?>
                                                <br><small class="text-muted">Inv: <?php echo $exp->invoice_no; ?></small>
                                            <?php endif; ?>
                                            <?php if($exp->documents): ?>
                                                <a href="<?php echo URLROOT . '/' . $exp->documents; ?>" target="_blank" class="text-info ms-1"><i class="fa fa-paperclip"></i></a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d-m-Y', strtotime($exp->date)); ?></td>
                                        <td><?php echo $exp->exp_category; ?></td>
                                        <td class="fw-bold text-danger"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> <?php echo number_format($exp->amount, 2); ?></td>
                                        <td class="text-end pe-4">
                                            <form action="" method="post" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="delete_expense" value="1">
                                                <input type="hidden" name="expense_id" value="<?php echo $exp->id; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(empty($data['expenses'])): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No expenses found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if(($data['total_pages'] ?? 1) > 1): ?>
                        <nav aria-label="Expenses pagination" class="px-4 mt-3">
                            <ul class="pagination justify-content-end mb-0">
                                <?php $currentPage = (int)($data['current_page'] ?? 1); ?>
                                <?php $totalPages = (int)($data['total_pages'] ?? 1); ?>
                                <?php $searchQuery = !empty($data['search']) ? '&search=' . urlencode($data['search']) : ''; ?>

                                <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo URLROOT; ?>/expense/index?page=<?php echo max(1, $currentPage - 1) . $searchQuery; ?>">Previous</a>
                                </li>

                                <?php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                for($i = $startPage; $i <= $endPage; $i++):
                                ?>
                                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo URLROOT; ?>/expense/index?page=<?php echo $i . $searchQuery; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo URLROOT; ?>/expense/index?page=<?php echo min($totalPages, $currentPage + 1) . $searchQuery; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Head Modal -->
    <div class="modal fade" id="addHeadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Add Expense Head</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="post">
                    <input type="hidden" name="add_head" value="1">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category Name</label>
                            <input type="text" name="exp_category" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Save Head</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>

