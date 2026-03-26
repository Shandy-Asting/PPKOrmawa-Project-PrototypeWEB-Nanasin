<?php
$pageTitle = 'Beranda';
require_once '../Native/Config/koneksi.php';
require_once '../includes/header.php';

// ===============================
// Ambil produk aktif
// ===============================
$query = mysqli_query($conn, "SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC");

$products = [];
while ($row = mysqli_fetch_assoc($query)) {
    $products[] = $row;
}

$query_kat = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE is_active = 1");

$kategori_list = [];
while ($row = mysqli_fetch_assoc($query_kat)) {
    $kategori_list[] = $row['category'];
}

$query_link = mysqli_query($conn, "SELECT * FROM marketplace_links WHERE is_active = 1");

$all_links = [];
while ($row = mysqli_fetch_assoc($query_link)) {
    $all_links[] = $row;
}

$links_by_product = [];
foreach ($all_links as $lnk) {
    $links_by_product[$lnk['product_id']][] = $lnk;
}
?>

<?php
$today = date('Y-m-d');
$query_visit = mysqli_query($conn, "SELECT COUNT(*) as total FROM visitor_logs WHERE DATE(visited_at) = '$today'");
$data_visit = mysqli_fetch_assoc($query_visit);
$visit_today = $data_visit['total'];

$mp_icons  = ['shopee'=>'bi-bag-fill','tokopedia'=>'bi-cart-fill','whatsapp'=>'bi-whatsapp','lainnya'=>'bi-link-45deg'];
$mp_labels = ['shopee'=>'Shopee','tokopedia'=>'Tokopedia','whatsapp'=>'WhatsApp','lainnya'=>'Lainnya'];
?>

<!-- ============================================================
     HERO
     ============================================================ -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <span class="hero-label">Produk Nanas</span>
        <h1 class="hero-title">
          Temukan Produk <span>UMKM Desa</span><br>dari Desa Sempu
        </h1>
        <p class="hero-desc">
          Produk berkualitas buatan Masyarakat desa sempu
        </p>
        <div class="d-flex flex-wrap gap-3">
          <a href="#katalog" class="btn-hero-primary">
            <i class="bi bi-grid"></i>Lihat Katalog
          </a>
          <a href="lokasi.php" class="btn-hero-outline">
            <i class="bi bi-geo-alt"></i>Lokasi Kami
          </a>
        </div>
      </div>

      <!-- Kanan hero: info singkat -->
      <div class="col-lg-5 d-none d-lg-block">
        <div class="d-flex flex-column gap-3 ps-4">
          <?php
          $info_cards = [
            ['icon'=>'bi-box-seam',    'val'=> count($products).' Produk', 'sub'=>'Tersedia sekarang'],
            ['icon'=>'bi-shop',        'val'=>'3 Platform',                'sub'=>'Shopee · Tokopedia · WA'],
            ['icon'=>'bi-people',      'val'=>$visit_today.' Kunjungan',   'sub'=>'Hari ini'],
          ];
          foreach ($info_cards as $ic): ?>
          <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(255,255,255,0.07);">
            <div style="width:40px;height:40px;background:var(--accent);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="bi <?= $ic['icon'] ?>" style="font-size:18px;color:#fff;"></i>
            </div>
            <div>
              <div style="font-weight:700;color:#fff;font-size:14px;"><?= $ic['val'] ?></div>
              <div style="font-size:12px;color:rgba(255,255,255,0.5);"><?= $ic['sub'] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     TICKER
     ============================================================ -->
<div class="ticker-bar">
  <div class="container">
    <div class="d-flex align-items-center gap-3 overflow-hidden">
      <span class="ticker-label">Info</span>
      <div class="ticker-track">
        <span class="ticker-item"><i class="bi bi-star-fill"></i>Produk baru hadir setiap minggu</span>
        <span class="ticker-item"><i class="bi bi-truck"></i>Pengiriman ke seluruh Indonesia via marketplace resmi</span>
        <span class="ticker-item"><i class="bi bi-award"></i>100% produk buatan lokal desa</span>
        <span class="ticker-item"><i class="bi bi-whatsapp"></i>Pemesanan khusus via WhatsApp tersedia</span>
        <!-- duplikat untuk loop mulus -->
        <span class="ticker-item"><i class="bi bi-star-fill"></i>Produk baru hadir setiap minggu</span>
        <span class="ticker-item"><i class="bi bi-truck"></i>Pengiriman ke seluruh Indonesia via marketplace resmi</span>
        <span class="ticker-item"><i class="bi bi-award"></i>100% produk buatan pengrajin lokal desa</span>
        <span class="ticker-item"><i class="bi bi-whatsapp"></i>Pemesanan khusus via WhatsApp tersedia</span>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================
     STAT BAR
     ============================================================ -->
<div class="stat-bar">
  <div class="container">
    <div class="row g-0 text-center">
      <div class="col-6 col-md-3 stat-item border-end border-bottom border-md-bottom-0">
        <div class="stat-num"><?= count($products) ?>+</div>
        <div class="stat-label">Produk Tersedia</div>
      </div>
      <div class="col-6 col-md-3 stat-item border-bottom border-md-bottom-0">
        <div class="stat-num"><?= count($kategori_list) ?></div>
        <div class="stat-label">Kategori</div>
      </div>
      <div class="col-6 col-md-3 stat-item border-end">
        <div class="stat-num">3</div>
        <div class="stat-label">Platform Marketplace</div>
      </div>
      <div class="col-6 col-md-3 stat-item">
        <div class="stat-num">100%</div>
        <div class="stat-label">Produk Lokal</div>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================
     KATALOG PRODUK
     ============================================================ -->
<section class="py-5" id="katalog">
  <div class="container">

    <!-- Header + Filter -->
    <div class="row align-items-end mb-4 gy-3">
      <div class="col-md-6">
        <p class="section-label mb-1">Katalog Produk</p>
        <h2 class="section-title mb-0">Produk <span>Unggulan</span> Kami</h2>
      </div>
      <div class="col-md-6">
        <div class="filter-tabs justify-content-md-end">
          <button class="btn-filter active" data-filter="semua">Semua</button>
          <?php foreach ($kategori_list as $kat): ?>
            <button class="btn-filter" data-filter="<?= htmlspecialchars($kat) ?>">
              <?= htmlspecialchars($kat) ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Grid Produk -->
    <?php if (empty($products)): ?>
      <div class="text-center py-5 text-muted">
        <i class="bi bi-box-seam" style="font-size:42px;display:block;margin-bottom:12px;"></i>
        Belum ada produk tersedia saat ini.
      </div>
    <?php else: ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4" id="produkGrid">
      <?php foreach ($products as $p): ?>
      <div class="col product-item" data-category="<?= htmlspecialchars($p['category']) ?>">
        <div class="product-card" data-track-product="<?= $p['id'] ?>"
             onclick="window.location='detail.php?id=<?= $p['id'] ?>'">

          <!-- Placeholder pengganti foto -->
          <div class="card-img-placeholder">
            <i class="bi bi-image ph-icon"></i>
            <span class="ph-text"><?= htmlspecialchars($p['name']) ?></span>
            <span class="card-category-badge"><?= htmlspecialchars($p['category']) ?></span>
          </div>

          <div class="card-body-umkm">
            <div class="card-seller">
              <i class="bi bi-person-circle"></i>UMKM Desa Sempu
            </div>
            <div class="card-title-umkm"><?= htmlspecialchars($p['name']) ?></div>
            <div class="card-desc-umkm"><?= htmlspecialchars($p['description']) ?></div>

            <!-- Link Marketplace -->
            <?php if (!empty($links_by_product[$p['id']])): ?>
            <div class="mp-badges">
              <?php foreach ($links_by_product[$p['id']] as $lnk): ?>
                <a href="<?= htmlspecialchars($lnk['url']) ?>"
                   class="mp-badge <?= $lnk['platform'] ?>"
                   target="_blank" rel="noopener"
                   data-track-link="<?= $lnk['id'] ?>"
                   onclick="event.stopPropagation();">
                  <i class="bi <?= $mp_icons[$lnk['platform']] ?? 'bi-link' ?>"></i>
                  <?= $mp_labels[$lnk['platform']] ?? 'Beli' ?>
                </a>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <a href="detail.php?id=<?= $p['id'] ?>" class="btn-detail-link"
               onclick="event.stopPropagation();">
              Selengkapnya <i class="bi bi-arrow-right"></i>
            </a>
          </div>

        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

  </div>
</section>

<!-- ============================================================
     BANNER TENTANG DESA
     ============================================================ -->
<section class="py-5" style="background:var(--gray-100);">
  <div class="container">
    <div class="about-banner">
      <div class="row g-0 align-items-stretch">

        <div class="col-lg-7 p-4 p-lg-5">
          <p class="section-label mb-1">Kenali Lebih Dekat</p>
          <h2 class="section-title">
            Desa Sempu Sukses, <span>Warisan</span> dan Kreativitas
          </h2>
          <p style="font-size:14px;color:var(--text-muted);line-height:1.8;margin-bottom:24px;">
            Rumah bagi para pelaku UMKM yang berdedikasi. Setiap produk dibuat
            dengan keahlian turun-temurun dan kecintaan terhadap tradisi lokal.
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="lokasi.php" class="btn-accent">
              <i class="bi bi-geo-alt"></i>Lihat Lokasi
            </a>
            <a href="dokumentasi.php" class="btn-outline-dark">
              <i class="bi bi-images"></i>Dokumentasi
            </a>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="about-right">
            <div class="about-icon">
              <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<?php require_once '../includes/footer.php'; ?>