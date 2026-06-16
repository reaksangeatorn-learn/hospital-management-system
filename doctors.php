<?php
require_once 'includes/config.php';
requireLogin();
$page_title = 'Doctors';
$conn = getDB();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM doctors WHERE id=$id");
    setFlash('success', 'Doctor removed.');
    header('Location: doctors.php'); exit();
}

$edit = null;
if (isset($_GET['edit'])) {
    $id   = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM doctors WHERE id=$id"));
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id             = (int)($_POST['id'] ?? 0);
    $name           = clean($_POST['name'] ?? '');
    $specialization = clean($_POST['specialization'] ?? '');
    $phone          = clean($_POST['phone'] ?? '');
    $email          = clean($_POST['email'] ?? '');
    $schedule       = clean($_POST['schedule'] ?? '');
    $status         = clean($_POST['status'] ?? 'active');
    $photo = $edit['photo'] ?? '';
if (!empty($_FILES['photo']['name'])) {
    $ext      = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $filename = 'doctor_' . time() . '.' . $ext;
    move_uploaded_file($_FILES['photo']['tmp_name'], 'uploads/doctors/' . $filename);
    $photo = $filename;
}

    if (!$name)           $errors[] = 'Name is required.';
    if (!$specialization) $errors[] = 'Specialization is required.';

 if (empty($errors)) {
    if ($id) {
        mysqli_query($conn, "UPDATE doctors SET name='$name',specialization='$specialization',phone='$phone',email='$email',schedule='$schedule',status='$status',photo='$photo' WHERE id=$id");
        setFlash('success', 'Doctor updated.');
    } else {
        mysqli_query($conn, "INSERT INTO doctors (name,specialization,phone,email,schedule,status,photo) VALUES ('$name','$specialization','$phone','$email','$schedule','$status','$photo')");
        setFlash('success', "Dr. $name added.");
    }
    header('Location: doctors.php'); exit();
}
}

$doctors = mysqli_query($conn, "SELECT * FROM doctors ORDER BY name ASC");
include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="page-title mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Doctors</p>
  <button class="btn btn-primary btn-sm" onclick="openAddModal()">
    <i class="bi bi-plus-lg me-1"></i>Add Doctor
</button>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Medical Staff</span>
        <span class="badge bg-primary"><?= mysqli_num_rows($doctors) ?> records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Specialization</th><th>Phone</th><th>Schedule</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($doctors) === 0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No doctors found.</td></tr>
                <?php else: ?>
                <?php while ($d = mysqli_fetch_assoc($doctors)): ?>
                    <tr>
                        <td><?= $d['id'] ?></td>
                        <td><strong><?= clean($d['name']) ?></strong></td>
                        <td><?= clean($d['specialization']) ?></td>
                        <td><?= clean($d['phone']) ?: '—' ?></td>
                        <td class="text-muted small"><?= clean($d['schedule']) ?></td>
                        <td><span class="badge <?= $d['status']==='active'?'bg-success':'bg-secondary' ?>"><?= ucfirst($d['status']) ?></span></td>
                        <td>
                           <a href="doctors.php?edit=<?= $d['id'] ?>" class="btn btn-sm btn-warning">
    <i class="bi bi-pencil"></i>
</a>
                            <a href="doctors.php?delete=<?= $d['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Remove this doctor?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i><?= $edit ? 'Edit Doctor' : 'Add Doctor' ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="doctors.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Profile Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <?php if ($edit && $edit['photo']): ?>
                        <img src="uploads/doctors/<?= $edit['photo'] ?>" class="mt-2 rounded-circle" width="60" height="60" style="object-fit:cover">
                        <?php endif; ?>
                    </div>
                    <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', $errors) ?></div><?php endif; ?>
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="<?= clean($edit['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Specialization *</label>
                            <input type="text" name="specialization" class="form-control" value="<?= clean($edit['specialization'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="active"   <?= ($edit['status']??'active')==='active'?'selected':'' ?>>Active</option>
                                <option value="inactive" <?= ($edit['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= clean($edit['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= clean($edit['email'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Schedule</label>
                            <input type="text" name="schedule" class="form-control" placeholder="e.g. Mon-Fri 8am-5pm" value="<?= clean($edit['schedule'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i><?= $edit ? 'Update' : 'Save' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit || !empty($errors)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Modal(document.getElementById('doctorModal')).show();
});
</script>
<?php endif; ?>





<script>
function openAddModal() {
    // Clear all form fields
    document.querySelector('#doctorModal input[name="id"]').value = '0';
    document.querySelector('#doctorModal input[name="name"]').value = '';
    document.querySelector('#doctorModal input[name="specialization"]').value = '';
    document.querySelector('#doctorModal input[name="phone"]').value = '';
    document.querySelector('#doctorModal input[name="email"]').value = '';
    document.querySelector('#doctorModal input[name="schedule"]').value = '';
    document.querySelector('#doctorModal select[name="status"]').value = 'active';
    document.querySelector('#doctorModal .modal-title').innerHTML = '<i class="bi bi-person-badge me-2"></i>Add Doctor';
    document.querySelector('#doctorModal button[type="submit"]').innerHTML = '<i class="bi bi-save me-1"></i>Save';
    new bootstrap.Modal(document.getElementById('doctorModal')).show();
}
</script>

<?php mysqli_close($conn); include 'includes/footer.php'; ?>
