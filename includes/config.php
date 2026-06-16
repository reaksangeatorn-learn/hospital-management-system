<?php
// includes/config.php

if (session_status() === PHP_SESSION_NONE) session_start();

define('SITE_NAME', 'MediCare Clinic');
define('SITE_URL', 'http://localhost/hospital3');

function getDB() {
    $conn = mysqli_connect('127.0.0.1', 'root', '', 'hospital_db', 3307);
    if (!$conn) {
        die('<div class="container mt-4"><div class="alert alert-danger"><strong>DB Error:</strong> ' . mysqli_connect_error() . '</div></div>');
    }
    mysqli_set_charset($conn, 'utf8');
    return $conn;
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . SITE_URL . '/login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['user_role'] !== 'admin') {
        header('Location: ' . SITE_URL . '/dashboard.php');
        exit();
    }
}

function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
?>
