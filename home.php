
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="data:,">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediCare Clinic — Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .hero { background: linear-gradient(135deg, #0d6efd, #0a58ca); color: #fff; padding: 60px 0 50px; text-align: center; }
        .hero h1 { font-size: 2.4rem; font-weight: 700; }
        .hero p  { font-size: 1.05rem; opacity: .85; }
        .card { border: none; box-shadow: 0 2px 12px rgba(0,0,0,.07); border-radius: 10px; }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; font-weight: 600; padding: 14px 18px; }
        .table thead th { background: #f8f9fa; font-size: .8rem; text-transform: uppercase; }
        .doctor-card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.07); text-align: center; padding: 24px 16px; height: 100%; }
        .doctor-avatar { width: 70px; height: 70px; border-radius: 50%; background: #cfe2ff; color: #084298; font-size: 1.8rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
        .section-title { font-size: 1.3rem; font-weight: 700; margin-bottom: 20px; color: #212529; }
    </style>
</head>
<body>



<div class="hero">
    <div class="container">
        <i class="bi bi-hospital fs-1 mb-3 d-block"></i>
        <h1>Welcome to MediCare Clinic</h1>
        <p class="mb-4">Health is our priority. Meeting patients.</p>
        <a href="login.php" class="btn btn-light btn-lg fw-semibold px-4">
            <i class="bi bi-box-arrow-in-right me-2"></i>Staff Login
        </a>
    </div>
</div>

<div class="container py-5">
<?php
$conn = mysqli_connect('127.0.0.1', 'root', '', 'hospital_db', 3307);
if (!$conn) {
    echo '<div class="alert alert-danger">Database not connected. Please run <a href="setup.php">setup.php</a> first.</div>';
} else {
    $total_patients = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM patients"))[0];
    $total_doctors  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM doctors WHERE status='active'"))[0];
    $total_appts    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments"))[0];
?>

<div class="row g-3 mb-5">
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fs-1 fw-bold text-primary"><?= $total_patients ?></div>
            <div class="text-muted">Total Patients</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fs-1 fw-bold text-success"><?= $total_doctors ?></div>
            <div class="text-muted">Active Doctors</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center py-3">
            <div class="fs-1 fw-bold text-warning"><?= $total_appts ?></div>
            <div class="text-muted">Total Appointments</div>
        </div>
    </div>
</div>

<div class="section-title"><i class="bi bi-person-badge me-2 text-primary"></i>Doctors</div>
<div class="row g-3 mb-5">
<?php
$doctors = mysqli_query($conn, "SELECT * FROM doctors WHERE status='active' ORDER BY name ASC");
while ($d = mysqli_fetch_assoc($doctors)):
?>
<div class="col-xl-3 col-md-4 col-sm-6">
    <div class="doctor-card">
        <div class="doctor-avatar" style="overflow:hidden;padding:0">
    <?php if ($d['photo']): ?>
    <img src="uploads/doctors/<?= $d['photo'] ?>" width="70" height="70" style="object-fit:cover;border-radius:50%">
    <?php else: ?>
    <i class="bi bi-person-fill"></i>
    <?php endif; ?>
</div>
        <h6 class="fw-bold mb-1"><?= htmlspecialchars($d['name']) ?></h6>
        <span class="badge bg-primary mb-2"><?= htmlspecialchars($d['specialization']) ?></span>
        <div class="text-muted small"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($d['phone']) ?: '—' ?></div>
        <div class="text-muted small mt-1"><i class="bi bi-clock me-1"></i><?= htmlspecialchars($d['schedule']) ?: '—' ?></div>
    </div>
</div>
<?php endwhile; ?>
</div>

<div class="section-title"><i class="bi bi-people me-2 text-primary"></i>Total Patient</div>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Registered Patients</span>
        <span class="badge bg-primary"><?= $total_patients ?> total</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>#</th><th>Name</th><th>Gender</th><th>Phone</th><th>Blood Type</th><th>Address</th></tr>
                </thead>
                <tbody>
                <?php
                $patients = mysqli_query($conn, "SELECT * FROM patients ORDER BY created_at DESC");
                while ($p = mysqli_fetch_assoc($patients)):
                ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                    <td><?= ucfirst($p['gender']) ?></td>
                    <td><?= htmlspecialchars($p['phone']) ?: '—' ?></td>
                    <td><?= $p['blood_type'] ? '<span class="badge bg-danger">'.$p['blood_type'].'</span>' : '—' ?></td>
                    <td class="text-muted"><?= htmlspecialchars($p['address']) ?: '—' ?></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php mysqli_close($conn); } ?>
</div>

<footer class="text-center text-muted py-3 mt-4" style="font-size:.8rem;border-top:1px solid #dee2e6;">
    &copy; <?= date('Y') ?> MediCare Clinic &mdash; Hospital Management System by REAKSA NGEATORN
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

