<?php
$pageTitle = 'Beranda';
require_once '../Native/Config/koneksi.php';
require_once '../includes/header.php';

// Ambil produk aktif
$res_prod = mysqli_query($conn, "SELECT * FROM products WHERE is_active = 1 ORDER BY created_at DESC");
$products = [];
while ($row = mysqli_fetch_assoc($res_prod)) {
  $products[] = $row;
}

// Ambil semua data dari tabel hero_sliders yang baru kita buat
$get_hero = mysqli_query($conn, "SELECT * FROM hero_sliders ORDER BY id DESC");

// Ambil semua link marketplace aktif
$res_links = mysqli_query($conn, "SELECT * FROM marketplace_links WHERE is_active = 1");
$links_by_product = [];
while ($row = mysqli_fetch_assoc($res_links)) {
  $links_by_product[$row['product_id']][] = $row;
}


$mp_icons  = ['shopee' => 'bi-bag-fill', 'tokopedia' => 'bi-cart-fill', 'whatsapp' => 'bi-whatsapp', 'lainnya' => 'bi-link-45deg'];
$mp_labels = ['shopee' => 'Shopee', 'tokopedia' => 'Tokopedia', 'whatsapp' => 'WA', 'lainnya' => 'Lainnya'];
?>

<!-- ============================================================
     HERO KATALOG
     ============================================================ -->
<section class="hero-katalog">
  <div id="heroCarousel" class="carousel slide carousel-fade"
    data-bs-ride="carousel"
    data-bs-interval="5000">

    <div class="carousel-inner">

      <?php
      $active = true;
      if (mysqli_num_rows($get_hero) > 0):
        while ($h = mysqli_fetch_assoc($get_hero)):
      ?>
          <div class="carousel-item <?= $active ? 'active' : '' ?>">
            <div class="hero-background" style="background-image: url('../assets/img/hero/<?= $h['image_name'] ?>');"></div>
            <div class="hero-overlay"></div>

            <div class="container hero-content-wrapper">
              <div class="hero-text-wrap">
                <span class="hero-badge">Produk Unggulan Desa</span>
                <h1 class="hero-title"><?= htmlspecialchars($h['title']) ?></h1>
                <p class="hero-sub">
                  Produk berkualitas buatan lokal.<br>
                  Belanja mudah lewat marketplace favorit Anda.
                </p>
                <a href="#produk" class="btn-hero">
                  <i class="bi bi-grid"></i> Lihat Produk
                </a>
              </div>
            </div>
          </div>
        <?php
          $active = false;
        endwhile;
      else:
        ?>
        <div class="carousel-item active">
          <div class="hero-background" style="background-color: var(--blue);"></div>
          <div class="hero-overlay"></div>
          <div class="container hero-content-wrapper text-center">
            <div class="w-100">
              <h1 class="hero-title">Produk UMKM Desa Sempu</h1>
              <p class="hero-sub">Belum ada banner yang diunggah.</p>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>

    <!-- Panah Kiri — transparan -->
    <button class="carousel-control-prev hero-arrow" type="button"
      data-bs-target="#heroCarousel" data-bs-slide="prev">
      <i class="bi bi-chevron-left hero-arrow-icon"></i>
    </button>

    <!-- Panah Kanan — transparan -->
    <button class="carousel-control-next hero-arrow" type="button"
      data-bs-target="#heroCarousel" data-bs-slide="next">
      <i class="bi bi-chevron-right hero-arrow-icon"></i>
    </button>

    <!-- Dots indikator bawah -->
    <div class="carousel-indicators hero-indicators">
      <?php
      $total = mysqli_num_rows($get_hero);
      $total = $total > 0 ? $total : 1;
      for ($i = 0; $i < $total; $i++):
      ?>
        <button type="button" data-bs-target="#heroCarousel"
          data-bs-slide-to="<?= $i ?>"
          <?= $i === 0 ? 'class="active"' : '' ?>></button>
      <?php endfor; ?>
    </div>

  </div>
</section>

<!-- ============================================================
     SECTION PRODUK — slider panah kiri kanan
     ============================================================ -->
<section class="section-produk" id="produk">
  <div class="container">

    <div class="mb-4">
      <span class="section-label">Katalog Produk</span>
      <h2 class="section-title">Produk <span>Unggulan</span></h2>
      <p style="font-size:13px;color:var(--text-muted);margin-top:4px;">
        Belanja Sekarang &rsaquo;
      </p>
    </div>

    <?php if (empty($products)): ?>
      <div class="text-center py-5" style="color:var(--text-muted);">
        <i class="bi bi-box-seam" style="font-size:40px;display:block;margin-bottom:12px;"></i>
        Belum ada produk tersedia.
      </div>
    <?php else: ?>

      <div class="produk-slider-outer">
        <button class="slider-btn prev" id="btnPrev">
          <i class="bi bi-chevron-left"></i>
        </button>
        <button class="slider-btn next" id="btnNext">
          <i class="bi bi-chevron-right"></i>
        </button>

        <div class="produk-slider-wrap">
          <div class="produk-slider-track" id="produkTrack">

            <?php foreach ($products as $p): ?>
              <div class="product-card"
                data-track-product="<?= $p['id'] ?>"
                onclick="window.location='detail.php?id=<?= $p['id'] ?>'">

                <!-- Foto produk dari database -->
                <div class="card-img-wrap">
                  <?php if (!empty($p['image_url'])): ?>
                    <img src="../<?= htmlspecialchars($p['image_url']) ?>"
                      alt="<?= htmlspecialchars($p['name']) ?>">
                  <?php else: ?>
                    <div class="card-img-noimg">
                      <i class="bi bi-image"></i>
                    </div>
                  <?php endif; ?>
                  <span class="card-cat-badge"><?= htmlspecialchars($p['category']) ?></span>
                </div>

                <!-- Info produk -->
                <div class="card-body-umkm">
                  <div class="card-title-umkm"><?= htmlspecialchars($p['name']) ?></div>
                  <div class="card-desc-umkm"><?= htmlspecialchars($p['description']) ?></div>

                  <!-- Link marketplace di dalam kartu -->
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

              </div><!-- end product-card -->
            <?php endforeach; ?>

          </div><!-- end produk-slider-track -->
        </div><!-- end produk-slider-wrap -->
      </div><!-- end produk-slider-outer -->

      <!-- Bar link marketplace bawah slider -->
      <div class="mp-link-bar mt-2">
        <span>Kunjungi Marketplace Kami</span>
        <i class="bi bi-chevron-right" style="color:var(--text-muted);font-size:12px;"></i>
        <a href="#" class="mp-link-icon shopee" title="Shopee"><i class="bi bi-bag-fill"></i></a>
        <a href="#" class="mp-link-icon tokopedia" title="Tokopedia"><i class="bi bi-cart-fill"></i></a>
        <a href="#" class="mp-link-icon whatsapp" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
      </div>

    <?php endif; ?>
  </div>
</section>

<!-- ============================================================
     KENALI LEBIH DEKAT
     ============================================================ -->
<section class="about-section">
  <div class="container">
    <div class="about-banner">
      <div class="row g-0 align-items-stretch">

        <div class="col-lg-7 about-left">
          <span class="section-label">Kenali Lebih Dekat</span>
          <h2 class="section-title mt-1 mb-3">
            Desa Sempu Sukses,<br><span>Warisan</span> dan Kreativitas Lokal
          </h2>
          <p style="font-size:14px;color:var(--text-muted);line-height:1.8;margin-bottom:24px;">
            Rumah bagi para pelaku UMKM yang berdedikasi.
            Setiap produk dibuat dengan keahlian turun-temurun dan
            kecintaan terhadap tradisi lokal.
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="lokasi.php" class="btn-blue">
              <i class="bi bi-geo-alt"></i>Lihat Lokasi
            </a>
            <a href="dokumentasi.php" class="btn-outline-blue">
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