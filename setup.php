<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Setup | MediCare Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container">
<div class="row justify-content-center mt-5">
<div class="col-md-6">
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white fw-bold">
        🏥 MediCare Clinic — Database Setup
    </div>
    <div class="card-body">
        <p class="text-muted small">Setting up database with port 3307...</p>
<?php
// Connect using same settings as teacher's project
$conn = mysqli_connect('127.0.0.1', 'root', '', '', 3307);

if (!$conn) {
    echo '<div class="alert alert-danger">❌ Cannot connect: ' . mysqli_connect_error() . '</div>';
    echo '<p class="text-muted small">Check that MySQL is running in XAMPP on port 3307.</p>';
    echo '</div></div></div></div></div></body></html>';
    exit();
}

echo '<div class="alert alert-success">✅ Connected to MySQL successfully!</div>';

// Create database
$queries = [
    "CREATE DATABASE IF NOT EXISTS hospital_db",
    "USE hospital_db",
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin','doctor','receptionist') DEFAULT 'receptionist',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS doctors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        specialization VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        email VARCHAR(100),
        schedule VARCHAR(255) DEFAULT 'Mon-Fri 8am-5pm',
        status ENUM('active','inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS patients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        gender ENUM('male','female','other') NOT NULL,
        dob DATE,
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT,
        blood_type VARCHAR(5),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT NOT NULL,
        doctor_id INT NOT NULL,
        appointment_date DATE NOT NULL,
        appointment_time TIME NOT NULL,
        reason TEXT,
        status ENUM('scheduled','completed','cancelled') DEFAULT 'scheduled',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
        FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
    )"
];

foreach ($queries as $q) {
    if (!mysqli_query($conn, $q)) {
        echo '<div class="alert alert-warning">⚠️ ' . mysqli_error($conn) . '</div>';
    }
}
echo '<div class="alert alert-success">✅ All tables created!</div>';

// Insert admin
$hash = password_hash('admin123', PASSWORD_BCRYPT);
$check = mysqli_query($conn, "SELECT id FROM users WHERE email='admin@hospital.com'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO users (name,email,password,role) VALUES ('Administrator','admin@hospital.com','$hash','admin')");
    echo '<div class="alert alert-success">✅ Admin account created!</div>';
} else {
    mysqli_query($conn, "UPDATE users SET password='$hash' WHERE email='admin@hospital.com'");
    echo '<div class="alert alert-info">ℹ️ Admin password reset to admin123</div>';
}

// Sample doctors
$dc = mysqli_query($conn, "SELECT id FROM doctors LIMIT 1");
if (mysqli_num_rows($dc) == 0) {
    mysqli_query($conn, "INSERT INTO doctors (name,specialization,phone,schedule) VALUES
        ('Dr. Sokha Meas','General Medicine','012-345-678','Mon-Fri 8am-5pm'),
        ('Dr. Bopha Keo','Pediatrics','012-456-789','Mon-Sat 8am-4pm'),
        ('Dr. Dara Chan','Cardiology','012-567-890','Tue-Sat 9am-6pm')");
    echo '<div class="alert alert-success">✅ Sample doctors added!</div>';
}

// Sample patients
$pc = mysqli_query($conn, "SELECT id FROM patients LIMIT 1");
if (mysqli_num_rows($pc) == 0) {
    mysqli_query($conn, "INSERT INTO patients (name,gender,dob,phone,address,blood_type) VALUES
        ('Srey Neang Pov','female','1990-05-15','011-111-222','Phnom Penh','A+'),
        ('Kosal Heng','male','1985-08-22','011-222-333','Siem Reap','O+'),
        ('Lina Sok','female','2000-01-10','011-333-444','Battambang','B+')");
    echo '<div class="alert alert-success">✅ Sample patients added!</div>';
}

// Sample appointments
$ac = mysqli_query($conn, "SELECT id FROM appointments LIMIT 1");
if (mysqli_num_rows($ac) == 0) {
    mysqli_query($conn, "INSERT INTO appointments (patient_id,doctor_id,appointment_date,appointment_time,reason,status) VALUES
        (1,1,CURDATE(),'09:00:00','Regular checkup','scheduled'),
        (2,2,CURDATE(),'10:30:00','Fever and cold','scheduled'),
        (3,3,DATE_ADD(CURDATE(),INTERVAL 1 DAY),'14:00:00','Heart consultation','scheduled')");
    echo '<div class="alert alert-success">✅ Sample appointments added!</div>';
}

mysqli_close($conn);
?>
        <hr>
        <div class="alert alert-info mb-3">
            <strong>Login:</strong><br>
            Email: <code>admin@hospital.com</code><br>
            Password: <code>admin123</code>
        </div>
        <div class="alert alert-warning small">
            ⚠️ Delete <code>setup.php</code> after this!
        </div>
        <a href="login.php" class="btn btn-primary w-100">Go to Login →</a>
    </div>
</div>
</div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
