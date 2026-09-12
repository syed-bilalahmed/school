<?php require APPROOT . '/Views/layouts/header.php'; ?>
<!-- Header Bar -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="<?php echo URLROOT; ?>/fees/groups" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
            <i class="fa fa-arrow-left me-1"></i> Back to Fee Groups
        </a>
        <div>
            <h2 class="h4 fw-bold mb-0 text-dark">
                <i class="fa fa-cogs text-primary me-2"></i>Fee Master: <?php echo htmlspecialchars($data['group']->group_name ?? ('Group #' . $data['group_id'])); ?>
            </h2>
            <div class="text-muted small">Configure and link fee types, billing amounts, and due dates for this group.</div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/fees/collect" class="btn btn-outline-primary btn-sm" style="border-radius: 8px;">
            <i class="fa fa-list-check me-1"></i> Go to Fee Register
        </a>
    </div>
</div>

<!-- Alert Notifications -->
<?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 12px;" role="alert">
        <i class="fa fa-check-circle me-2"></i>
        <?php 
            if($_GET['success'] == 'updated') echo "Fee item updated successfully!";
            elseif($_GET['success'] == 'deleted') echo "Fee item removed from group successfully!";
            else echo "Fee item linked to group successfully!";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Add / Edit Fee Master Form -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="fw-bold mb-0 text-dark" id="masterCardTitle">
                    <i class="fa fa-plus-circle text-primary me-2" id="masterCardIcon"></i>Add Fee Type to Group
                </h6>
            </div>
            <div class="card-body p-4">
                <form id="masterForm" action="<?php echo URLROOT; ?>/fees/master/<?php echo $data['group_id']; ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo !empty($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : ''; ?>">
                    <input type="hidden" name="group_id" value="<?php echo $data['group_id']; ?>">
                    <input type="hidden" name="id" id="master_id" value="">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Fee Type <span class="text-danger">*</span></label>
                        <select name="type_id" id="master_type_id" class="form-select" required style="border-radius: 8px;">
                            <option value="">Select Fee Type</option>
                            <?php foreach($data['types'] as $type): ?>
                                <option value="<?php echo $type->id; ?>">
                                    <?php echo htmlspecialchars($type->type_name); ?> (<?php echo htmlspecialchars($type->type_code); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Amount (<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?>) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" id="master_amount" class="form-control" placeholder="0.00" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Due Date</label>
                        <input type="date" name="due_date" id="master_due_date" class="form-control" style="border-radius: 8px;">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Fine Amount (<?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?>)</label>
                        <input type="number" step="0.01" name="fine" id="master_fine" class="form-control" value="0.00" style="border-radius: 8px;">
                        <small class="text-muted">Penalty fee applied after the due date.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" id="masterSubmitBtn" class="btn btn-primary flex-fill fw-bold shadow-sm" style="border-radius: 8px;">
                            <i class="fa fa-save me-1"></i> Add to Group
                        </button>
                        <button type="button" id="masterCancelBtn" class="btn btn-outline-secondary d-none" style="border-radius: 8px;">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Fees in Group Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa fa-layer-group text-primary me-2"></i>Fees Linked in this Group
                </h6>
                <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2" style="border-radius: 30px;">
                    <?php echo count($data['master_fees']); ?> Fee Items
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Fee Type</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = 0;
                            if(!empty($data['master_fees'])):
                                foreach($data['master_fees'] as $master): 
                                    $total += $master->amount;
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($master->type_name); ?></div>
                                        <span class="badge bg-light text-secondary border small"><?php echo htmlspecialchars($master->type_code); ?></span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> <?php echo number_format($master->amount, 2); ?></span>
                                        <?php if($master->fine_amount > 0): ?>
                                            <div class="small text-danger"><i class="fa fa-triangle-exclamation me-1"></i>Fine: <?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> <?php echo number_format($master->fine_amount, 2); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(!empty($master->due_date)): ?>
                                            <span class="badge bg-light text-dark border px-2 py-1"><i class="fa fa-calendar me-1 text-muted"></i><?php echo htmlspecialchars($master->due_date); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border px-2 py-1">No Limit</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary border-0 btn-edit-item" 
                                                data-id="<?php echo $master->id; ?>" 
                                                data-type="<?php echo $master->fee_type_id; ?>" 
                                                data-amount="<?php echo $master->amount; ?>" 
                                                data-due="<?php echo htmlspecialchars($master->due_date ?? ''); ?>" 
                                                data-fine="<?php echo $master->fine_amount; ?>"
                                                title="Edit Fee Item" style="border-radius: 6px;">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <a href="<?php echo URLROOT; ?>/fees/deleteMaster/<?php echo $master->id; ?>?group_id=<?php echo $data['group_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger border-0" 
                                               onclick="return confirm('Are you sure you want to delete this fee item from the group?');"
                                               title="Delete Fee Item" style="border-radius: 6px;">
                                               <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                endforeach; 
                            else:
                            ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fa fa-receipt fa-3x mb-3 text-secondary opacity-25"></i>
                                        <p class="mb-0">No fee items assigned to this group yet.</p>
                                        <small>Use the form on the left to add your first fee component.</small>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if(!empty($data['master_fees'])): ?>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="ps-4 fw-bold text-uppercase small text-muted">Total Group Fee</td>
                                    <td colspan="3" class="pe-4 fw-bold text-dark fs-6"><?php echo htmlspecialchars($data['currency'] ?? 'PKR'); ?> <?php echo number_format($total, 2); ?></td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('masterForm');
    const idInput = document.getElementById('master_id');
    const typeSelect = document.getElementById('master_type_id');
    const amountInput = document.getElementById('master_amount');
    const dueInput = document.getElementById('master_due_date');
    const fineInput = document.getElementById('master_fine');
    const title = document.getElementById('masterCardTitle');
    const icon = document.getElementById('masterCardIcon');
    const submitBtn = document.getElementById('masterSubmitBtn');
    const cancelBtn = document.getElementById('masterCancelBtn');

    document.querySelectorAll('.btn-edit-item').forEach(btn => {
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            const typeId = this.dataset.type;
            const amount = this.dataset.amount;
            const due = this.dataset.due;
            const fine = this.dataset.fine;

            idInput.value = id;
            typeSelect.value = typeId;
            amountInput.value = amount;
            dueInput.value = due;
            fineInput.value = fine;

            title.innerHTML = '<i class="fa fa-edit text-warning me-2"></i>Edit Fee Item';
            submitBtn.innerHTML = '<i class="fa fa-save me-1"></i> Update Fee Item';
            submitBtn.className = 'btn btn-warning flex-fill fw-bold shadow-sm';
            cancelBtn.classList.remove('d-none');

            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    cancelBtn.addEventListener('click', function(){
        resetForm();
    });

    function resetForm(){
        idInput.value = '';
        form.reset();
        title.innerHTML = '<i class="fa fa-plus-circle text-primary me-2"></i>Add Fee Type to Group';
        submitBtn.innerHTML = '<i class="fa fa-save me-1"></i> Add to Group';
        submitBtn.className = 'btn btn-primary flex-fill fw-bold shadow-sm';
        cancelBtn.classList.add('d-none');
    }
});
</script>
<?php require APPROOT . '/Views/layouts/footer.php'; ?>


