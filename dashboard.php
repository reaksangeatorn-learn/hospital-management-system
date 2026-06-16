<?php
require_once 'includes/config.php';
requireLogin();
$page_title = 'Dashboard';

$conn = getDB();
$total_patients     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM patients"))[0];
$total_doctors      = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM doctors WHERE status='active'"))[0];
$today_appointments = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE appointment_date=CURDATE()"))[0];
$pending            = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE status='scheduled'"))[0];

$recent = mysqli_query($conn, "
    SELECT a.*, p.name AS pname, d.name AS dname, d.specialization
    FROM appointments a
    JOIN patients p ON a.patient_id=p.id
    JOIN doctors d ON a.doctor_id=d.id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
    LIMIT 100
");

include 'includes/header.php';
?>

<p class="page-title"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</p>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-primary d-flex justify-content-between align-items-center">
            <div><div class="num"><?= $total_patients ?></div><div class="lbl">Total Patients</div></div>
            <i class="bi bi-people ico"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-success d-flex justify-content-between align-items-center">
            <div><div class="num"><?= $total_doctors ?></div><div class="lbl">Active Doctors</div></div>
            <i class="bi bi-person-badge ico"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-warning d-flex justify-content-between align-items-center">
            <div><div class="num"><?= $today_appointments ?></div><div class="lbl">Today's Appointments</div></div>
            <i class="bi bi-calendar-day ico"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card bg-danger d-flex justify-content-between align-items-center">
            <div><div class="num"><?= $pending ?></div><div class="lbl">Scheduled</div></div>
            <i class="bi bi-clock ico"></i>
        </div>
    </div>
</div>

<!-- Recent Appointments -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2 text-primary"></i>Recent Appointments</span>
        <a href="appointments.php" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th><th>Patient</th><th>Doctor</th><th>Specialization</th>
                        <th>Date</th><th>Time</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($recent) === 0): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No appointments yet.</td></tr>
                <?php else: ?>
                <?php while ($r = mysqli_fetch_assoc($recent)): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= clean($r['pname']) ?></td>
                        <td><?= clean($r['dname']) ?></td>
                        <td class="text-muted"><?= clean($r['specialization']) ?></td>
                        <td><?= date('d M Y', strtotime($r['appointment_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($r['appointment_time'])) ?></td>
                        <td><span class="badge badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                    </tr>
                <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php mysqli_close($conn); include 'includes/footer.php'; ?>
