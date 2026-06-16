<?php // includes/navbar.php ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= SITE_URL ?>/dashboard.php">
            <i class="bi bi-hospital me-2"></i><?= SITE_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>"
                       href="<?= SITE_URL ?>/dashboard.php">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='appointments.php'?'active':'' ?>"
                       href="<?= SITE_URL ?>/appointments.php">
                        <i class="bi bi-calendar-check me-1"></i>Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='patients.php'?'active':'' ?>"
                       href="<?= SITE_URL ?>/patients.php">
                        <i class="bi bi-people me-1"></i>Patients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='doctors.php'?'active':'' ?>"
                       href="<?= SITE_URL ?>/doctors.php">
                        <i class="bi bi-person-badge me-1"></i>Doctors
                    </a>
                </li>
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='users.php'?'active':'' ?>"
                       href="<?= SITE_URL ?>/admin/users.php">
                        <i class="bi bi-gear me-1"></i>Users
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= clean($_SESSION['user_name'] ?? 'User') ?>
                        <span class="badge bg-light text-primary ms-1"><?= ucfirst($_SESSION['user_role'] ?? '') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item text-danger" href="<?= SITE_URL ?>/logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
