<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase px-2 py-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Catalog Master</span>
                <span class="text-muted small">&bull;</span>
                <span class="text-muted small">Library Management</span>
            </div>
            <h3 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fa fa-books text-primary"></i> Library Book Catalog
            </h3>
            <p class="text-muted small mb-0">Total <?php echo (int)($data['total_books'] ?? 0); ?> book titles registered in the school library database.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo URLROOT; ?>/library/issue_return" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                <i class="fa fa-book-reader me-1"></i> Issue &amp; Return Desk
            </a>
            <?php if(in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'librarian'])): ?>
            <a href="<?php echo URLROOT; ?>/notice/index?category=Library+%26+Reading+Notice" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-3 fw-semibold">
                <i class="fa fa-bullhorn text-warning me-1"></i> Library Notices
            </a>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addBook">
                <i class="fa fa-plus me-1"></i> Add New Book
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Books Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white mb-4">
        <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">Book Inventory &amp; Stock</h6>
            <span class="badge bg-light text-dark rounded-pill px-3 py-1 fw-semibold">Page <?php echo $data['current_page']; ?> of <?php echo $data['total_pages']; ?></span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.04em;">
                            <th class="ps-3 py-3">Book Title &amp; No.</th>
                            <th class="py-3">Author</th>
                            <th class="py-3">Publisher &amp; ISBN</th>
                            <th class="py-3">Rack No</th>
                            <th class="py-3 text-center">Available Stock</th>
                            <th class="py-3">Price</th>
                            <?php if(in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'librarian'])): ?>
                            <th class="pe-3 py-3 text-end">Action</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['books'])): ?>
                            <?php foreach($data['books'] as $book): ?>
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="fw-bold text-dark" style="font-size: 0.88rem;"><?php echo htmlspecialchars($book->book_title); ?></div>
                                        <small class="text-muted">#<?php echo htmlspecialchars($book->book_no); ?></small>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-dark small fw-semibold"><?php echo htmlspecialchars($book->author ?: 'N/A'); ?></span>
                                    </td>
                                    <td class="py-3">
                                        <div class="small text-dark"><?php echo htmlspecialchars($book->publisher ?: 'N/A'); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($book->isbn ?: ''); ?></small>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark border px-2 py-1"><?php echo htmlspecialchars($book->rack_no ?: '-'); ?></span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <?php if($book->qty > 0): ?>
                                            <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold"><?php echo $book->qty; ?> in stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 fw-bold">Out of stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3">
                                        <span class="small fw-semibold"><?php echo !empty($book->price) ? 'PKR ' . number_format($book->price, 2) : '-'; ?></span>
                                    </td>
                                    <?php if(in_array($_SESSION['user_role'] ?? '', ['admin', 'super_admin', 'librarian'])): ?>
                                    <td class="pe-3 py-3 text-end">
                                        <a href="<?php echo URLROOT; ?>/library/delete/<?php echo $book->id; ?>" 
                                           class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" 
                                           title="Delete Book"
                                           onclick="return confirm('Are you sure you want to delete this book?');">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa fa-book-open fa-2x mb-2 d-block opacity-50"></i>
                                    No books currently in library catalog. Click "Add New Book" to start.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
                
                <!-- Pagination -->
                <?php if($data['total_pages'] > 1): ?>
                    <nav class="mt-3">
                        <ul class="pagination pagination-sm justify-content-end">
                            <li class="page-item <?php echo $data['current_page'] <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo URLROOT; ?>/library/index?page=<?php echo $data['current_page'] - 1; ?>">Previous</a>
                            </li>
                            <?php for($i = 1; $i <= $data['total_pages']; $i++): ?>
                                <li class="page-item <?php echo $i == $data['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo URLROOT; ?>/library/index?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo $data['current_page'] >= $data['total_pages'] ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo URLROOT; ?>/library/index?page=<?php echo $data['current_page'] + 1; ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Book Modal -->
<div class="modal fade" id="addBook">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?php echo URLROOT; ?>/library/add" method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Book Title</label>
                            <input type="text" name="book_title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Book No</label>
                            <input type="text" name="book_no" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>ISBN</label>
                            <input type="text" name="isbn" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Publisher</label>
                            <input type="text" name="publisher" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Author</label>
                            <input type="text" name="author" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Rack No</label>
                            <input type="text" name="rack_no" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Quantity</label>
                            <input type="number" name="qty" class="form-control" value="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Price</label>
                            <input type="number" name="price" step="0.01" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Book</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
