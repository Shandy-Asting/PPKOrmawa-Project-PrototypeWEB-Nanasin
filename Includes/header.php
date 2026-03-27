<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? $pageTitle . ' — ' : '' ?>UMKM Desa</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>

  <?php $current = basename($_SERVER['PHP_SELF']); ?>

  <nav class="navbar navbar-expand-lg umkm-navbar sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="beranda(index).php">
        <div class="navbar-logo-wrap">
          <i class="bi bi-grid-3x3-gap-fill"></i>
        </div>
        <div>
          <span class="navbar-desa">UMKM Desa</span>
          <span class="navbar-sub">Sukses</span>
        </div>
      </a>

      <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <i class="bi bi-list" style="font-size:22px;color:#333;"></i>
      </button>

      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto gap-1 mt-2 mt-lg-0">
          <li class="nav-item">
            <a class="nav-link <?= ($current == 'beranda(index).php') ? 'active' : '' ?>" href="beranda(index).php">
              <i class="bi bi-house me-1"></i>Beranda
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($current == 'detail.php') ? 'active' : '' ?>" href="beranda(index).php#produk">
              <i class="bi bi-box-seam me-1"></i>Detail Produk
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($current == 'lokasi.php') ? 'active' : '' ?>" href="lokasi.php">
              <i class="bi bi-geo-alt me-1"></i>Lokasi
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($current == 'dokumentasi.php') ? 'active' : '' ?>" href="dokumentasi.php">
              <i class="bi bi-images me-1"></i>Dokumentasi
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>