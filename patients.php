<?php
require_once 'includes/config.php';
requireLogin();
$page_title = 'Patients';
$conn = getDB();

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM patients WHERE id=$id");
    setFlash('success', 'Patient deleted.');
    header('Location: patients.php'); exit();
}

// LOAD FOR EDIT
$edit = null;
if (isset($_GET['edit'])) {
    $id   = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM patients WHERE id=$id"));
}

// SAVE
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = (int)($_POST['id'] ?? 0);
    $name       = clean($_POST['name'] ?? '');
    $gender     = clean($_POST['gender'] ?? '');
    $dob        = clean($_POST['dob'] ?? '');
    $phone      = clean($_POST['phone'] ?? '');
    $email      = clean($_POST['email'] ?? '');
    $address    = clean($_POST['address'] ?? '');
    $blood_type = clean($_POST['blood_type'] ?? '');

    if (!$name)   $errors[] = 'Name is required.';
    if (!$gender) $errors[] = 'Gender is required.';

    if (empty($errors)) {
        $dob   = $dob ?: 'NULL';
        $dob_q = $dob === 'NULL' ? 'NULL' : "'$dob'";
        if ($id) {
            mysqli_query($conn, "UPDATE patients SET name='$name',gender='$gender',dob=$dob_q,phone='$phone',email='$email',address='$address',blood_type='$blood_type' WHERE id=$id");
            setFlash('success', 'Patient updated.');
        } else {
            mysqli_query($conn, "INSERT INTO patients (name,gender,dob,phone,email,address,blood_type) VALUES ('$name','$gender',$dob_q,'$phone','$email','$address','$blood_type')");
            setFlash('success', "Patient '$name' added.");
        }
        header('Location: patients.php'); exit();
    }
}

$search   = clean($_GET['q'] ?? '');
$where    = $search ? "WHERE name LIKE '%$search%' OR phone LIKE '%$search%' OR id LIKE '%$search%' " : '';
$patients = mysqli_query($conn, "SELECT * FROM patients $where ORDER BY created_at DESC");

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="page-title mb-0"><i class="bi bi-people me-2 text-primary"></i>Patients</p>

    
<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#patientModal">
    <i class="bi bi-plus-lg me-1"></i>Add Patient
</button>
</div>

<form method="GET" class="mb-3 d-flex gap-2" style="max-width:400px">
    <input type="text" name="q" class="form-control form-control-sm" placeholder="Search name or phone..." value="<?= $search ?>">
    <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
    <?php if ($search): ?><a href="patients.php" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
</form>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Patient Records</span>
        <span class="badge bg-primary"><?= mysqli_num_rows($patients) ?> records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Gender</th><th>DOB</th><th>Phone</th><th>Blood</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($patients) === 0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No patients found.</td></tr>
                <?php else: ?>
                <?php while ($p = mysqli_fetch_assoc($patients)): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><strong><?= clean($p['name']) ?></strong></td>
                        <td><?= ucfirst($p['gender']) ?></td>
                        <td><?= $p['dob'] ? date('d M Y', strtotime($p['dob'])) : '—' ?></td>
                        <td><?= clean($p['phone']) ?: '—' ?></td>
                        <td><?= clean($p['blood_type']) ?: '—' ?></td>
                        <td>
                           
<a href="patients.php?edit=<?= $p['id'] ?>"
   class="btn btn-sm btn-warning">
    <i class="bi bi-pencil"></i>
</a>
                            <a href="patients.php?delete=<?= $p['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this patient?')">
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
<div class="modal fade" id="patientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i><?= $edit ? 'Edit Patient' : 'Add Patient' ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="patients.php">
                <div class="modal-body">
                    <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', $errors) ?></div><?php endif; ?>
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="<?= clean($edit['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gender *</label>
                            <select name="gender" class="form-select" required>
                                <option value="">Select...</option>
                                <option value="male"   <?= ($edit['gender']??'')==='male'?'selected':'' ?>>Male</option>
                                <option value="female" <?= ($edit['gender']??'')==='female'?'selected':'' ?>>Female</option>
                                <option value="other"  <?= ($edit['gender']??'')==='other'?'selected':'' ?>>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?= clean($edit['dob'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= clean($edit['phone'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Blood Type</label>
                            <select name="blood_type" class="form-select">
                                <option value="">Unknown</option>
                                <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt): ?>
                                <option value="<?= $bt ?>" <?= ($edit['blood_type']??'')===$bt?'selected':'' ?>><?= $bt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= clean($edit['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Address</label>
                            <input type="text" name="address" class="form-control" value="<?= clean($edit['address'] ?? '') ?>">
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
    new bootstrap.Modal(document.getElementById('patientModal')).show();
});
</script>
<?php endif; ?>

<?php mysqli_close($conn); include 'includes/footer.php'; ?>
