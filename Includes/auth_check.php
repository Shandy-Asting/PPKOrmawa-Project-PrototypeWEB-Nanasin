<?php
// ============================================================
// auth_check.php — wajib di-include di setiap halaman admin
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah sudah login
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Simpan halaman yang dituju, redirect ke login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../admin/login.php');
    exit;
}

// Cek session timeout — 2 jam tidak aktif otomatis logout
$timeout = 7200;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header('Location: ../admin/login.php?msg=timeout');
    exit;
}
$_SESSION['last_activity'] = time();

// Regenerasi session ID tiap 30 menit untuk keamanan
if (!isset($_SESSION['created_at'])) {
    $_SESSION['created_at'] = time();
} elseif (time() - $_SESSION['created_at'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created_at'] = time();
}

// Fungsi generate CSRF token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fungsi validasi CSRF token
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}