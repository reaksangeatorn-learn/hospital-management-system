<?php
// includes/header.php
requireLogin();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' | ' : '' ?><?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; box-shadow: 0 0 10px rgba(0,0,0,.07); border-radius: 10px; }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; font-weight: 600; padding: 14px 18px; }
        .table thead th { background-color: #f8f9fa; font-size: .82rem; text-transform: uppercase; letter-spacing: .04em; }
        .table tbody tr:hover { background-color: #f8f9fa; }
        .stat-card { border-radius: 10px; color: #fff; padding: 20px 24px; }
        .stat-card .num { font-size: 2rem; font-weight: 700; line-height: 1; }
        .stat-card .lbl { font-size: .85rem; opacity: .85; margin-top: 4px; }
        .stat-card .ico { font-size: 2.4rem; opacity: .25; }
        .badge-scheduled { background-color: #cfe2ff !important; color: #084298 !important; }
        .badge-completed { background-color: #d1e7dd !important; color: #0a3622 !important; }
        .badge-cancelled { background-color: #f8d7da !important; color: #842029 !important; }
        .page-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 20px; color: #333; }
    </style>
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<div class="container-fluid px-4 py-4">
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type']==='error'?'danger':$flash['type'] ?> alert-dismissible fade show">
    <?= clean($flash['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
