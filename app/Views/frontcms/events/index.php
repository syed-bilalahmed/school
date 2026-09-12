<?php require APPROOT . '/Views/layouts/header.php'; ?>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Add Event</div>
            <div class="card-body">
                <form action="<?php echo URLROOT; ?>/frontcms/events" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Event Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Venue</label>
                        <input type="text" name="venue" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="datetime-local" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Event</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white">Event List</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Venue</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['events'] as $event): ?>
                            <tr>
                                <td><?php echo $event->title; ?></td>
                                <td><?php echo date('d M Y h:i A', strtotime($event->start_date)); ?></td>
                                <td><?php echo $event->venue; ?></td>
                                <td>
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="delete_id" value="<?php echo $event->id; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($data['events'])): ?>
                                <tr><td colspan="4" class="text-center">No events found</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
