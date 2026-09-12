<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="row">
    <div class="col-md-12 mb-3">
        <?php if($_SESSION['user_role'] == 'admin' || $_SESSION['user_role'] == 'super_admin'): ?>
        <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#addBook">Add New Book</button>
        <?php endif; ?>
        <h3>Library Management</h3>
        <hr>
    </div>

    <!-- Books List -->
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Book No</th>
                                <th>ISBN</th>
                                <th>Publisher</th>
                                <th>Author</th>
                                <th>Rack No</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <?php if($_SESSION['user_role'] == 'admin'): ?>
                                <th>Action</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['books'] as $book): ?>
                                <tr>
                                    <td><?php echo $book->book_title; ?></td>
                                    <td><?php echo $book->book_no; ?></td>
                                    <td><?php echo $book->isbn; ?></td>
                                    <td><?php echo $book->publisher; ?></td>
                                    <td><?php echo $book->author; ?></td>
                                    <td><?php echo $book->rack_no; ?></td>
                                    <td><?php echo $book->qty; ?></td>
                                    <td><?php echo $book->price; ?></td>
                                    <?php if($_SESSION['user_role'] == 'admin'): ?>
                                    <td>
                                        <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
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
