<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? $pageTitle . ' — ' : '' ?>Admin UMKM Desa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="../assets/css/admin.css" rel="stylesheet">
</head>

<body>

  <?php $current = basename($_SERVER['PHP_SELF']); ?>

  <!-- SIDEBAR -->
  <div class="admin-wrapper">
    <aside class="admin-sidebar" id="adminSidebar">
      <div class="sidebar-brand">
        <div class="sidebar-logo">
          <i class="bi bi-grid-3x3-gap-fill"></i>
        </div>
        <div>
          <span class="sidebar-title">UMKM Desa</span>
          <span class="sidebar-sub">Panel Admin</span>
        </div>
      </div>

      <nav class="sidebar-nav">
        <span class="nav-group-label">Menu Utama</span>
        <a href="dashboard.php" class="sidebar-link <?= $current == 'dashboard.php' ? 'active' : '' ?>">
          <i class="bi bi-speedometer2"></i>Dashboard
        </a>
        <a href="produk.php" class="sidebar-link <?= $current == 'produk.php' ? 'active' : '' ?>">
          <i class="bi bi-box-seam"></i>Produk
        </a>
        <a href="marketplace.php" class="sidebar-link <?= $current == 'marketplace.php' ? 'active' : '' ?>">
          <i class="bi bi-shop"></i>Marketplace
        </a>
        <a href="lokasi.php" class="sidebar-link <?= $current == 'lokasi.php' ? 'active' : '' ?>">
          <i class="bi bi-geo-alt"></i>Lokasi
        </a>
        <a href="dokumentasi.php" class="sidebar-link <?= $current == 'dokumentasi.php' ? 'active' : '' ?>">
          <i class="bi bi-images"></i>Konten
        </a>
        <a href="manage_hero.php" class="sidebar-link <?= $current == 'manage_hero.php' ? 'active' : '' ?>">
          <i class="bi bi-image-fill"></i>Slider Hero
        </a>

        <span class="nav-group-label mt-3">Akun</span>
        <a href="../public/beranda(index).php" class="sidebar-link" target="_blank">
          <i class="bi bi-eye"></i>Lihat Web
        </a>
        <a href="logout.php" class="sidebar-link text-danger-soft">
          <i class="bi bi-box-arrow-right"></i>Logout
        </a>
      </nav>

      <div class="sidebar-user">
        <i class="bi bi-person-circle"></i>
        <div>
          <span class="sidebar-username"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></span>
          <span class="sidebar-role">Administrator</span>
        </div>
      </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <div class="admin-main">

      <!-- Topbar -->
      <div class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle">
          <i class="bi bi-list"></i>
        </button>
        <div class="topbar-title"><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></div>
        <div class="topbar-right">
          <span style="font-size:12px;color:var(--gray-500);">
            <i class="bi bi-clock me-1"></i><?= date('d M Y, H:i') ?>
          </span>
        </div>
      </div>

      <!-- Area konten halaman -->
      <div class="admin-content">