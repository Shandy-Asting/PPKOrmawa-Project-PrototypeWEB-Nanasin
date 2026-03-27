<?php
session_start();

// Kalau sudah login langsung ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

require_once '../Native/Config/koneksi.php';

$error   = '';
$msg     = '';
$max_try = 10;
$cooldown = 300; // 5 menit dalam detik

// Pesan dari redirect
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'timeout') $msg = 'Sesi Anda telah berakhir. Silakan login kembali.';
    if ($_GET['msg'] === 'logout')  $msg = 'Anda telah berhasil logout.';
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $ip       = $_SERVER['REMOTE_ADDR'];

    // Cek brute force — simpan di session
    $key_try  = 'login_try_' . $ip;
    $key_time = 'login_time_' . $ip;

    if (!isset($_SESSION[$key_try]))  $_SESSION[$key_try]  = 0;
    if (!isset($_SESSION[$key_time])) $_SESSION[$key_time] = 0;

    // Cek apakah sedang cooldown
    if ($_SESSION[$key_try] >= $max_try) {
        $sisa = $cooldown - (time() - $_SESSION[$key_time]);
        if ($sisa > 0) {
            $error = 'Terlalu banyak percobaan. Coba lagi dalam ' . ceil($sisa / 60) . ' menit.';
        } else {
            // Reset setelah cooldown habis
            $_SESSION[$key_try]  = 0;
            $_SESSION[$key_time] = 0;
        }
    }

    if (empty($error) && !empty($username) && !empty($password)) {
        $username_safe = mysqli_real_escape_string($conn, $username);
        $result = mysqli_query($conn, "SELECT * FROM admins WHERE username = '$username_safe' LIMIT 1");

        if ($result && mysqli_num_rows($result) === 1) {
            $admin = mysqli_fetch_assoc($result);

            if (password_verify($password, $admin['password_hash'])) {
                // Login berhasil — set session
                session_regenerate_id(true);
                $_SESSION['admin_id']       = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_logged_in']= true;
                $_SESSION['last_activity']  = time();
                $_SESSION['created_at']     = time();

                // Reset counter brute force
                $_SESSION[$key_try]  = 0;
                $_SESSION[$key_time] = 0;

                // Update last_login
                $now = date('Y-m-d H:i:s');
                mysqli_query($conn, "UPDATE admins SET last_login = '$now' WHERE id = {$admin['id']}");

                // Redirect ke dashboard atau halaman yang dituju
                $redirect = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;

            } else {
                $_SESSION[$key_try]++;
                $_SESSION[$key_time] = time();
                $sisa_coba = $max_try - $_SESSION[$key_try];
                $error = 'Username atau password salah.' . ($sisa_coba > 0 ? " Sisa percobaan: $sisa_coba." : '');
            }
        } else {
            $_SESSION[$key_try]++;
            $_SESSION[$key_time] = time();
            $error = 'Username atau password salah.';
        }
    } elseif (empty($error)) {
        $error = 'Username dan password wajib diisi.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin — UMKM Desa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; background: #1e3a8a; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
    .login-card { background: #fff; border-radius: 14px; padding: 36px; width: 100%; max-width: 400px; box-shadow: 0 8px 32px rgba(0,0,0,0.2); }
    .login-logo { width: 48px; height: 48px; background: #1A56DB; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: #fff; margin: 0 auto 16px; }
    .login-title { font-size: 20px; font-weight: 700; text-align: center; color: #111827; margin-bottom: 4px; }
    .login-sub { font-size: 13px; color: #6b7280; text-align: center; margin-bottom: 28px; }
    .form-label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 5px; }
    .form-control { font-size: 13px; border-color: #d1d5db; border-radius: 8px; padding: 10px 12px; }
    .form-control:focus { border-color: #1A56DB; box-shadow: 0 0 0 3px rgba(26,86,219,0.12); }
    .btn-login { background: #1A56DB; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; padding: 11px; width: 100%; margin-top: 8px; cursor: pointer; transition: background .2s; }
    .btn-login:hover { background: #1e3a8a; }
    .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; border-radius: 8px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .alert-info  { background: #eff6ff; color: #1A56DB; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .input-wrap { position: relative; }
    .toggle-pw  { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; font-size: 16px; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-logo"><i class="bi bi-grid-3x3-gap-fill"></i></div>
    <div class="login-title">Panel Admin</div>
    <div class="login-sub">UMKM Desa Sumber Makmur</div>

    <?php if ($error): ?>
      <div class="alert-error"><i class="bi bi-exclamation-circle"></i><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($msg): ?>
      <div class="alert-info"><i class="bi bi-info-circle"></i><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               placeholder="Masukkan username" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-wrap">
          <input type="password" name="password" id="pwInput" class="form-control"
                 placeholder="Masukkan password" required>
          <button type="button" class="toggle-pw" onclick="togglePw()">
            <i class="bi bi-eye" id="pwIcon"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
      </button>
    </form>
  </div>

  <script>
    function togglePw() {
      var inp  = document.getElementById('pwInput');
      var icon = document.getElementById('pwIcon');
      if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'bi bi-eye-slash';
      } else {
        inp.type = 'password';
        icon.className = 'bi bi-eye';
      }
    }
  </script>
</body>
</html>