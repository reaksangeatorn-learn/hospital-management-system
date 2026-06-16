<?php
require_once 'includes/config.php';
if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit(); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $conn = getDB();
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: dashboard.php'); exit();
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | MediCare Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .login-card { border: none; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.1); }
        .login-top { background: #0d6efd; color: #fff; border-radius: 12px 12px 0 0; padding: 30px; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4 col-sm-9">
            <div class="card login-card">
                <div class="login-top">
                    <i class="bi bi-hospital fs-1"></i>
                    <h4 class="mt-2 fw-bold mb-0">MediCare Clinic</h4>
                    <small class="opacity-75">Hospital Management System</small>
                    
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control"
                                    placeholder="admin@hospital.com"
                                    value="<?= clean($_POST['email'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control"
                                    placeholder="••••••••" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login

        
                        </button>

                        <a href="home.php" class="btn btn-primary w-100 py-2 fw-semibold mt-2">
    <i class="bi bi-arrow-left me-2"></i>Back 
</a>
                    </form>
                    <div class="alert alert-info mt-3 mb-0 small">
                        <strong>Demo:</strong> admin@hospital.com / admin123
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
