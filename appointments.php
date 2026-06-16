<?php
require_once 'includes/config.php';
requireLogin();
$page_title = 'Appointments';
$conn = getDB();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM appointments WHERE id=$id");
    setFlash('success', 'Appointment deleted.');
    header('Location: appointments.php'); exit();
}

if (isset($_GET['status'], $_GET['id'])) {
    $id     = (int)$_GET['id'];
    $status = in_array($_GET['status'], ['scheduled','completed','cancelled']) ? $_GET['status'] : 'scheduled';
    mysqli_query($conn, "UPDATE appointments SET status='$status' WHERE id=$id");
    setFlash('success', 'Status updated.');
    header('Location: appointments.php'); exit();
}

$edit = null;
if (isset($_GET['edit'])) {
    $id   = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM appointments WHERE id=$id"));
    
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = (int)($_POST['id'] ?? 0);
    $patient_id = (int)($_POST['patient_id'] ?? 0);
    $doctor_id  = (int)($_POST['doctor_id'] ?? 0);
    $date       = clean($_POST['appointment_date'] ?? '');
    $time       = clean($_POST['appointment_time'] ?? '');
    $reason     = clean($_POST['reason'] ?? '');
    $status     = clean($_POST['status'] ?? 'scheduled');

    if (!$patient_id) $errors[] = 'Patient is required.';
    if (!$doctor_id)  $errors[] = 'Doctor is required.';
    if (!$date)       $errors[] = 'Date is required.';
    if (!$time)       $errors[] = 'Time is required.';

    if (empty($errors)) {
        if ($id) {
            mysqli_query($conn, "UPDATE appointments SET patient_id=$patient_id,doctor_id=$doctor_id,appointment_date='$date',appointment_time='$time',reason='$reason',status='$status' WHERE id=$id");
            setFlash('success', 'Appointment updated.');
        } else {
            mysqli_query($conn, "INSERT INTO appointments (patient_id,doctor_id,appointment_date,appointment_time,reason) VALUES ($patient_id,$doctor_id,'$date','$time','$reason')");
            setFlash('success', 'Appointment booked.');
        }
        header('Location: appointments.php'); exit();
    }
}

$filter = clean($_GET['filter'] ?? '');
$where  = $filter ? "WHERE a.status='$filter'" : '';

$appointments = mysqli_query($conn, "
    SELECT a.*, p.name AS pname, d.name AS dname, d.specialization
    FROM appointments a
    JOIN patients p ON a.patient_id=p.id
    JOIN doctors d ON a.doctor_id=d.id
    $where
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");

$patients = mysqli_query($conn, "SELECT id,name FROM patients ORDER BY name ASC");
$doctors  = mysqli_query($conn, "SELECT id,name,specialization FROM doctors WHERE status='active' ORDER BY name ASC");

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="page-title mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Appointments</p>
   <button class="btn btn-primary btn-sm" onclick="openAddModal()">
    <i class="bi bi-plus-lg me-1"></i>New Appointment
</button>
</div>

<div class="mb-3">
    <a href="appointments.php" class="btn btn-sm <?= !$filter?'btn-primary':'btn-outline-primary' ?>">All</a>
    <a href="?filter=scheduled" class="btn btn-sm <?= $filter==='scheduled'?'btn-primary':'btn-outline-primary' ?>">Scheduled</a>
    <a href="?filter=completed" class="btn btn-sm <?= $filter==='completed'?'btn-success':'btn-outline-success' ?>">Completed</a>
    <a href="?filter=cancelled" class="btn btn-sm <?= $filter==='cancelled'?'btn-danger':'btn-outline-danger' ?>">Cancelled</a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Appointment List</span>
        <span class="badge bg-primary"><?= mysqli_num_rows($appointments) ?> records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>#</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Reason</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($appointments) === 0): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No appointments found.</td></tr>
                <?php else: ?>
                <?php while ($a = mysqli_fetch_assoc($appointments)): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= clean($a['pname']) ?></td>
                        <td><?= clean($a['dname']) ?><div class="text-muted small"><?= clean($a['specialization']) ?></div></td>
                        <td><?= date('d M Y', strtotime($a['appointment_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($a['appointment_time'])) ?></td>
                        <td class="small"><?= clean($a['reason']) ?: '—' ?></td>
                        <td><span class="badge badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
                        <td>
                           
<a href="appointments.php?edit=<?= $a['id'] ?>" class="btn btn-sm btn-warning">
    <i class="bi bi-pencil"></i>
</a>
                            <?php if ($a['status']==='scheduled'): ?>
                            <a href="?id=<?= $a['id'] ?>&status=completed" class="btn btn-sm btn-success"
                               onclick="return confirm('Mark as completed?')"><i class="bi bi-check-lg"></i></a>
                            <a href="?id=<?= $a['id'] ?>&status=cancelled" class="btn btn-sm btn-outline-secondary"
                               onclick="return confirm('Cancel?')"><i class="bi bi-x-lg"></i></a>
                            <?php endif; ?>
                            <a href="appointments.php?delete=<?= $a['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete?')"><i class="bi bi-trash"></i></a>
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
<div class="modal fade" id="apptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i><?= $edit ? 'Edit Appointment' : 'New Appointment' ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="appointments.php">
                <div class="modal-body">
                    <?php if ($errors): ?><div class="alert alert-danger"><?= implode('<br>', $errors) ?></div><?php endif; ?>
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Patient *</label>
                            <select name="patient_id" class="form-select" required>
                                <option value="">Select patient...</option>
                                <?php mysqli_data_seek($patients, 0); while ($p = mysqli_fetch_assoc($patients)): ?>
                                <option value="<?= $p['id'] ?>" <?= ($edit['patient_id']??0)==$p['id']?'selected':'' ?>><?= clean($p['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Doctor *</label>
                            <select name="doctor_id" class="form-select" required>
                                <option value="">Select doctor...</option>
                                <?php mysqli_data_seek($doctors, 0); while ($d = mysqli_fetch_assoc($doctors)): ?>
                                <option value="<?= $d['id'] ?>" <?= ($edit['doctor_id']??0)==$d['id']?'selected':'' ?>><?= clean($d['name']) ?> — <?= clean($d['specialization']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date *</label>
                            <input type="date" name="appointment_date" class="form-control"
                                value="<?= clean($edit['appointment_date'] ?? date('Y-m-d')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Time *</label>
                            <input type="time" name="appointment_time" class="form-control"
                                value="<?= isset($edit['appointment_time']) ? substr($edit['appointment_time'],0,5) : '09:00' ?>" required>
                        </div>
                        <?php if ($edit): ?>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="scheduled" <?= $edit['status']==='scheduled'?'selected':'' ?>>Scheduled</option>
                                <option value="completed" <?= $edit['status']==='completed'?'selected':'' ?>>Completed</option>
                                <option value="cancelled" <?= $edit['status']==='cancelled'?'selected':'' ?>>Cancelled</option>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Reason</label>
                            <textarea name="reason" class="form-control" rows="2"><?= clean($edit['reason'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i><?= $edit ? 'Update' : 'Book' ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($edit || !empty($errors)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Modal(document.getElementById('apptModal')).show();
});
</script>
<?php endif; ?>

<script>
function openAddModal() {
    document.querySelector('#apptModal input[name="id"]').value = '0';
    document.querySelector('#apptModal select[name="patient_id"]').value = '';
    document.querySelector('#apptModal select[name="doctor_id"]').value = '';
    document.querySelector('#apptModal input[name="appointment_date"]').value = '<?= date('Y-m-d') ?>';
    document.querySelector('#apptModal input[name="appointment_time"]').value = '09:00';
    document.querySelector('#apptModal textarea[name="reason"]').value = '';
    document.querySelector('#apptModal .modal-title').innerHTML = '<i class="bi bi-calendar-plus me-2"></i>New Appointment';
    document.querySelector('#apptModal button[type="submit"]').innerHTML = '<i class="bi bi-save me-1"></i>Book';
    new bootstrap.Modal(document.getElementById('apptModal')).show();
}
</script>



<?php mysqli_close($conn); include 'includes/footer.php'; ?>
