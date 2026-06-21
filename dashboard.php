<?php
require_once 'includes/config.php';
requireLogin();
$page_title = 'Dashboard';

$conn = getDB();
$total_patients     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM patients"))[0];
$total_doctors      = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM doctors WHERE status='active'"))[0];
$today_appointments = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE appointment_date=CURDATE()"))[0];
$pending            = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE status='scheduled'"))[0];
$completed          = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE status='completed'"))[0];
$cancelled          = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE status='cancelled'"))[0];

// Monthly appointments for chart (last 6 months)
$monthly = ['labels' => [], 'data' => []];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $label = date('M', strtotime("-$i months"));
    $count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments WHERE DATE_FORMAT(appointment_date,'%Y-%m')='$month'"))[0];
    $monthly['labels'][] = $label;
    $monthly['data'][]   = (int)$count;
}

// Recent appointments
$recent = mysqli_query($conn, "
    SELECT a.*, p.name AS pname, d.name AS dname
    FROM appointments a
    JOIN patients p ON a.patient_id=p.id
    JOIN doctors d ON a.doctor_id=d.id
    ORDER BY a.created_at DESC LIMIT 6
");

mysqli_close($conn);
include 'includes/header.php';
?>

<style>
.dash-title { font-size: 1rem; font-weight: 700; color: #1a1f36; margin-bottom: 20px; }
.dash-title span { color: #6c757d; font-weight: 400; font-size: .85rem; margin-left: 8px; }

.stat-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 24px; }
.scard { background:#fff; border-radius:12px; padding:20px; box-shadow:0 1px 8px rgba(0,0,0,.06); display:flex; align-items:center; gap:16px; border:1px solid #f0f0f0; }
.scard-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0; }
.scard-icon.purple { background:#ede9fe; color:#7c3aed; }
.scard-icon.blue   { background:#dbeafe; color:#2563eb; }
.scard-icon.green  { background:#dcfce7; color:#16a34a; }
.scard-icon.orange { background:#ffedd5; color:#ea580c; }
.scard-val { font-size:1.8rem; font-weight:700; color:#1a1f36; line-height:1; }
.scard-lbl { font-size:.78rem; color:#6c757d; margin-top:3px; }
.scard-trend { font-size:.72rem; margin-top:4px; }
.scard-trend.up   { color:#16a34a; }
.scard-trend.down { color:#dc2626; }

.mini-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:24px; }
.mcard { background:#fff; border-radius:12px; padding:16px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f0f0f0; text-align:center; }
.mcard .val { font-size:1.5rem; font-weight:700; }
.mcard .lbl { font-size:.75rem; color:#6c757d; margin-top:2px; }

.dash-grid { display:grid; grid-template-columns:1.4fr 1fr; gap:20px; margin-bottom:24px; }
.dcard { background:#fff; border-radius:12px; box-shadow:0 1px 8px rgba(0,0,0,.06); border:1px solid #f0f0f0; overflow:hidden; }
.dcard-head { padding:16px 20px; border-bottom:1px solid #f5f5f5; display:flex; align-items:center; justify-content:space-between; }
.dcard-head h6 { font-size:.9rem; font-weight:700; color:#1a1f36; margin:0; }
.dcard-body { padding:20px; }

.dash-table { width:100%; border-collapse:collapse; font-size:.83rem; }
.dash-table thead th { padding:10px 14px; text-align:left; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#6c757d; border-bottom:1px solid #f0f0f0; background:#fafafa; }
.dash-table tbody td { padding:11px 14px; border-bottom:1px solid #f8f8f8; color:#1a1f36; }
.dash-table tbody tr:last-child td { border-bottom:none; }
.dash-table tbody tr:hover { background:#fafafa; }

.bs { font-size:.7rem; padding:3px 9px; border-radius:20px; font-weight:600; }
.bs-scheduled { background:#ede9fe; color:#7c3aed; }
.bs-completed  { background:#dcfce7; color:#16a34a; }
.bs-cancelled  { background:#fee2e2; color:#dc2626; }

@media(max-width:900px){
    .stat-row { grid-template-columns:1fr 1fr; }
    .dash-grid { grid-template-columns:1fr; }
}
</style>

<div class="dash-title">
    Dashboard <span>Welcome back, <?= clean($_SESSION['user_name']) ?>!</span>
</div>

<!-- Stat Cards -->
<div class="stat-row">
    <div class="scard">
        <div class="scard-icon purple"><i class="bi bi-people-fill"></i></div>
        <div>
            <div class="scard-val"><?= $total_patients ?></div>
            <div class="scard-lbl">Total Patients</div>
            <div class="scard-trend up"><i class="bi bi-arrow-up-short"></i>Registered</div>
        </div>
    </div>
    <div class="scard">
        <div class="scard-icon blue"><i class="bi bi-person-badge-fill"></i></div>
        <div>
            <div class="scard-val"><?= $total_doctors ?></div>
            <div class="scard-lbl">Active Doctors</div>
            <div class="scard-trend up"><i class="bi bi-arrow-up-short"></i>On duty</div>
        </div>
    </div>
    <div class="scard">
        <div class="scard-icon green"><i class="bi bi-calendar-check-fill"></i></div>
        <div>
            <div class="scard-val"><?= $today_appointments ?></div>
            <div class="scard-lbl">Today's Appointments</div>
            <div class="scard-trend up"><i class="bi bi-arrow-up-short"></i>Today</div>
        </div>
    </div>
    <div class="scard">
        <div class="scard-icon orange"><i class="bi bi-clock-fill"></i></div>
        <div>
            <div class="scard-val"><?= $pending ?></div>
            <div class="scard-lbl">Pending</div>
            <div class="scard-trend down"><i class="bi bi-arrow-down-short"></i>Scheduled</div>
        </div>
    </div>
</div>

<!-- Mini Stats -->
<div class="mini-stats">
    <div class="mcard">
        <div class="val text-success"><?= $completed ?></div>
        <div class="lbl">Completed</div>
    </div>
    <div class="mcard">
        <div class="val text-danger"><?= $cancelled ?></div>
        <div class="lbl">Cancelled</div>
    </div>
    <div class="mcard">
        <div class="val text-primary"><?= $pending ?></div>
        <div class="lbl">Scheduled</div>
    </div>
</div>

<!-- Chart + Doughnut -->
<div class="dash-grid">
    <div class="dcard">
        <div class="dcard-head">
            <h6><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Monthly Appointments</h6>
            <span style="font-size:.75rem;color:#6c757d">Last 6 months</span>
        </div>
        <div class="dcard-body">
            <canvas id="apptChart" height="200"></canvas>
        </div>
    </div>
    <div class="dcard">
        <div class="dcard-head">
            <h6><i class="bi bi-pie-chart-fill me-2" style="color:#7c3aed"></i>Appointment Status</h6>
        </div>
        <div class="dcard-body d-flex flex-column align-items-center">
            <canvas id="statusChart" height="200"></canvas>
            <div class="d-flex gap-3 mt-3" style="font-size:.78rem">
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#7c3aed;margin-right:4px"></span>Scheduled</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#16a34a;margin-right:4px"></span>Completed</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#dc2626;margin-right:4px"></span>Cancelled</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Appointments -->
<div class="dcard">
    <div class="dcard-head">
        <h6><i class="bi bi-clock-history me-2 text-primary"></i>Recent Appointments</h6>
        <a href="appointments.php" style="font-size:.78rem;color:#7c3aed;text-decoration:none;font-weight:600">View All →</a>
    </div>
    <div style="overflow-x:auto">
        <table class="dash-table">
            <thead>
                <tr><th>#</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($recent) === 0): ?>
                <tr><td colspan="6" style="text-align:center;color:#6c757d;padding:24px">No appointments yet.</td></tr>
            <?php else: ?>
            <?php while ($r = mysqli_fetch_assoc($recent)): ?>
                <tr>
                    <td style="color:#6c757d"><?= $r['id'] ?></td>
                    <td><strong><?= clean($r['pname']) ?></strong></td>
                    <td><?= clean($r['dname']) ?></td>
                    <td><?= date('d M Y', strtotime($r['appointment_date'])) ?></td>
                    <td><?= date('h:i A', strtotime($r['appointment_time'])) ?></td>
                    <td><span class="bs bs-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                </tr>
            <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('apptChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($monthly['labels']) ?>,
        datasets: [{
            label: 'Appointments',
            data: <?= json_encode($monthly['data']) ?>,
            backgroundColor: 'rgba(124,58,237,0.15)',
            borderColor: '#7c3aed',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f0f0f0' } },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Scheduled', 'Completed', 'Cancelled'],
        datasets: [{
            data: [<?= $pending ?>, <?= $completed ?>, <?= $cancelled ?>],
            backgroundColor: ['#7c3aed', '#16a34a', '#dc2626'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        cutout: '70%',
        plugins: { legend: { display: false } }
    }
});
</script>

<?php include 'includes/footer.php'; ?>
