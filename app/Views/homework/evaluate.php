<?php require APPROOT . '/Views/layouts/header.php'; ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Evaluate Homework: <?php echo $data['homework']->subject_name; ?></h3>
        <a href="<?php echo URLROOT; ?>/homework/index" class="btn btn-secondary">Back</a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white">
            <h5 class="mb-0">Student List</h5>
        </div>
        <div class="card-body p-0">
            <form action="<?php echo URLROOT; ?>/homework/evaluate/<?php echo $data['homework']->id; ?>" method="post">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Admission No</th>
                                <th>Status</th>
                                <th>Marks (Opt)</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['students'] as $student): ?>
                                <tr>
                                    <td><?php echo $student->name; ?></td>
                                    <td><?php echo $student->admission_no; ?></td>
                                    <td>
                                        <select name="students[<?php echo $student->student_id; ?>][status]" class="form-select form-select-sm">
                                            <option value="Pending" <?php echo ($student->status == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="submitted" <?php echo ($student->status == 'submitted') ? 'selected' : ''; ?>>Submitted</option>
                                            <option value="complete" <?php echo ($student->status == 'complete') ? 'selected' : ''; ?>>Complete</option>
                                            <option value="incomplete" <?php echo ($student->status == 'incomplete') ? 'selected' : ''; ?>>Incomplete</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="students[<?php echo $student->student_id; ?>][marks]" class="form-control form-control-sm" style="width: 80px;" value="<?php echo $student->marks; ?>">
                                    </td>
                                    <td>
                                        <input type="text" name="students[<?php echo $student->student_id; ?>][note]" class="form-control form-control-sm" value="<?php echo $student->note; ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success px-4">Save Evaluation</button>
                </div>
            </form>
        </div>
    </div>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>
