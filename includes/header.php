<?php
requireLogin();
$flash = getFlash();
$cur = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' | ' : '' ?>MediCare Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="../images/hospital.png">
    <style>
        :root{--sw:240px;--purple:#7c3aed;--purple-light:#ede9fe;--navy:#1a1f36;}
        *{box-sizing:border-box;}
        body{background:#f4f5f7;margin:0;font-family:'Segoe UI',sans-serif;}
        .sidebar{position:fixed;top:0;left:0;width:var(--sw);height:100vh;background:#fff;border-right:1px solid #ececec;display:flex;flex-direction:column;z-index:200;box-shadow:2px 0 8px rgba(0,0,0,.04);}
        .sb-brand{padding:18px 20px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;gap:10px;}
        .sb-logo{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#a78bfa);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0;}
        .sb-brand span{font-size:.9rem;font-weight:700;color:var(--navy);}
        .sb-user{padding:12px 20px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;gap:10px;}
        .sb-avatar{width:34px;height:34px;border-radius:50%;background:var(--purple-light);color:var(--purple);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;flex-shrink:0;}
        .sb-uname{font-size:.82rem;font-weight:600;color:var(--navy);}
        .sb-urole{font-size:.7rem;color:#6c757d;text-transform:capitalize;}
        .sidenav{flex:1;padding:10px 0;overflow-y:auto;}
        .nav-sec{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#adb5bd;padding:10px 20px 4px;}
        .nav-item-link{display:flex;align-items:center;gap:10px;padding:9px 20px;color:#6c757d;text-decoration:none;font-size:.84rem;font-weight:500;border-left:3px solid transparent;transition:all .15s;margin:1px 0;}
        .nav-item-link:hover{background:#f8f5ff;color:var(--purple);}
        .nav-item-link.active{background:var(--purple-light);color:var(--purple);border-left-color:var(--purple);font-weight:600;}
        .nav-item-link i{width:18px;text-align:center;font-size:.9rem;}
        .sb-footer{padding:12px 20px;border-top:1px solid #f0f0f0;}
        .sb-footer a{display:flex;align-items:center;gap:8px;color:#dc2626;text-decoration:none;font-size:.82rem;font-weight:500;padding:8px 10px;border-radius:8px;transition:background .15s;}
        .sb-footer a:hover{background:#fee2e2;}
        .topbar{position:fixed;top:0;left:var(--sw);right:0;height:56px;background:#fff;border-bottom:1px solid #ececec;display:flex;align-items:center;justify-content:space-between;padding:0 24px;z-index:100;box-shadow:0 1px 4px rgba(0,0,0,.04);}
        .topbar-left{font-size:.95rem;font-weight:700;color:var(--navy);}
        .topbar-right{display:flex;align-items:center;gap:12px;}
        .topbar-right .clock{font-size:.78rem;color:#6c757d;}
        .main-wrap{margin-left:var(--sw);margin-top:56px;padding:24px;min-height:calc(100vh - 56px);}
        .card{border:none;box-shadow:0 1px 8px rgba(0,0,0,.06);border-radius:12px;}
        .card-header{background:#fff;border-bottom:1px solid #f0f0f0;font-weight:600;padding:14px 18px;border-radius:12px 12px 0 0!important;}
        .table thead th{background:#fafafa;font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;color:#6c757d;border-bottom:2px solid #f0f0f0;}
        .table tbody tr:hover{background:#fafafa;}
        .bs{font-size:.7rem;padding:3px 10px;border-radius:20px;font-weight:600;display:inline-block;}
        .bs-scheduled{background:#ede9fe;color:#7c3aed;}
        .bs-completed{background:#dcfce7;color:#16a34a;}
        .bs-cancelled{background:#fee2e2;color:#dc2626;}
        .bs-active{background:#dcfce7;color:#16a34a;}
        .bs-inactive{background:#f1f5f9;color:#64748b;}
        .form-control:focus,.form-select:focus{border-color:var(--purple);box-shadow:0 0 0 3px rgba(124,58,237,.1);}
        .page-title{font-size:1rem;font-weight:700;color:var(--navy);margin-bottom:20px;}
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-logo"><i class="bi bi-hospital"></i></div>
        <span>MediCare Clinic</span>
    </div>
    <div class="sb-user">
        <div class="sb-avatar"><?= strtoupper(substr($_SESSION['user_name'],0,1)) ?></div>
        <div>
            <div class="sb-uname"><?= clean($_SESSION['user_name']) ?></div>
            <div class="sb-urole"><?= $_SESSION['user_role'] ?></div>
        </div>
    </div>
    <nav class="sidenav">
        <div class="nav-sec">Main</div>
        <a href="<?= SITE_URL ?>/dashboard.php" class="nav-item-link <?= $cur==='dashboard.php'?'active':'' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <div class="nav-sec">Management</div>
        <a href="<?= SITE_URL ?>/appointments.php" class="nav-item-link <?= $cur==='appointments.php'?'active':'' ?>">
            <i class="bi bi-calendar-check"></i> Appointments
        </a>
        <a href="<?= SITE_URL ?>/patients.php" class="nav-item-link <?= $cur==='patients.php'?'active':'' ?>">
            <i class="bi bi-people"></i> Patients
        </a>
        <a href="<?= SITE_URL ?>/doctors.php" class="nav-item-link <?= $cur==='doctors.php'?'active':'' ?>">
            <i class="bi bi-person-badge"></i> Doctors
        </a>
        <?php if(isset($_SESSION['user_role'])&&$_SESSION['user_role']==='admin'): ?>
        <div class="nav-sec">Admin</div>
        <a href="<?= SITE_URL ?>/admin/users.php" class="nav-item-link <?= $cur==='users.php'?'active':'' ?>">
            <i class="bi bi-gear"></i> User Accounts
        </a>
        <?php endif; ?>
    </nav>
    <div class="sb-footer">
        <a href="<?= SITE_URL ?>/logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
    </div>
</aside>

<div class="topbar">
    <div class="topbar-left"><?= isset($page_title)?$page_title:'Dashboard' ?></div>
    <div class="topbar-right">
        <span class="clock" id="clock"></span>
        <span class="badge" style="background:var(--purple)"><?= ucfirst($_SESSION['user_role']) ?></span>
    </div>
</div>

<div class="main-wrap">
<?php if($flash): ?>
<div class="alert alert-<?= $flash['type']==='error'?'danger':$flash['type'] ?> alert-dismissible fade show">
    <?= clean($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>