<?php
$pageTitle = 'Detail Produk';
require_once '../Native/Config/koneksi.php';
require_once '../includes/header.php';

// Ambil ID dari URL, pastikan angka
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Redirect ke beranda kalau ID tidak valid
if ($id <= 0) {
    header('Location: beranda(index).php');
    exit;
}

// Ambil data produk dari database
$result = mysqli_query($conn, "SELECT * FROM products WHERE id = $id AND is_active = 1 LIMIT 1");

// Kalau produk tidak ditemukan
if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: beranda(index).php');
    exit;
}

$p = mysqli_fetch_assoc($result);

// Tambah click_count
mysqli_query($conn, "UPDATE products SET click_count = click_count + 1 WHERE id = $id");

// Ambil link marketplace produk ini
$res_links = mysqli_query($conn, "SELECT * FROM marketplace_links WHERE product_id = $id AND is_active = 1");
$links = [];
while ($row = mysqli_fetch_assoc($res_links)) {
    $links[] = $row;
}

// Ambil produk lain untuk bagian "Lihat Produk Lainnya"
$res_lain = mysqli_query($conn, "SELECT * FROM products WHERE is_active = 1 AND id != $id ORDER BY created_at DESC LIMIT 4");
$produk_lain = [];
while ($row = mysqli_fetch_assoc($res_lain)) {
    $produk_lain[] = $row;
}

// Icon dan label marketplace
$mp_icons  = ['shopee'=>'bi-bag-check-fill','tokopedia'=>'bi-cart-check-fill','whatsapp'=>'bi-whatsapp','lainnya'=>'bi-link-45deg'];
$mp_labels = ['shopee'=>'Shopee','tokopedia'=>'Tokopedia','whatsapp'=>'Chat WhatsApp','lainnya'=>'Beli Sekarang'];
$mp_class  = ['shopee'=>'shopee','tokopedia'=>'tokopedia','whatsapp'=>'whatsapp','lainnya'=>'lainnya'];
?>

<main class="py-4 py-lg-5" style="background:#fdfdfd;">
  <div class="container">

    <div class="row g-4 g-lg-5">

      <!-- Foto Produk -->
      <div class="col-12 col-lg-6">
        <div class="detail-img-container shadow-sm">
          <?php if (!empty($p['image_url'])): ?>
            <img src="../<?= htmlspecialchars($p['image_url']) ?>"
                 alt="<?= htmlspecialchars($p['name']) ?>"
                 style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
          <?php else: ?>
            <i class="bi bi-image" style="font-size:80px;color:#ddd;"></i>
          <?php endif; ?>
        </div>
      </div>

      <!-- Info Produk -->
      <div class="col-12 col-lg-6">
        <div class="ps-lg-3">

          <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-2 rounded-pill small fw-bold">
            <?= htmlspecialchars($p['category']) ?>
          </span>

          <h1 class="fw-bold mb-2 h2"><?= htmlspecialchars($p['name']) ?></h1>

          <!-- Deskripsi dengan Read More -->
          <div class="description-section mb-5">
            <h6 class="fw-bold mb-2">Deskripsi Lengkap:</h6>
            <div id="descWrapper" class="desc-content collapsed"
                 style="max-height:100px;overflow:hidden;transition:max-height .3s;">
              <p class="text-muted mb-0" style="font-size:14px;line-height:1.6;">
                <?= nl2br(htmlspecialchars($p['description'])) ?>
              </p>
            </div>
            <button onclick="toggleDesc()" id="btnReadMore"
                    class="btn btn-link p-0 mt-1 text-decoration-none fw-bold small">
              Baca Selengkapnya...
            </button>
          </div>

          <!-- Tombol Marketplace dari database -->
          <?php if (!empty($links)): ?>
          <div class="action-buttons">
            <h6 class="fw-bold mb-3 small text-uppercase">Beli Sekarang Melalui:</h6>
            <div class="d-grid gap-3">
              <?php foreach ($links as $lnk): ?>
                <a href="<?= htmlspecialchars($lnk['url']) ?>"
                   class="btn-order <?= $mp_class[$lnk['platform']] ?? 'lainnya' ?> shadow-sm"
                   target="_blank" rel="noopener"
                   data-track-link="<?= $lnk['id'] ?>">
                  <i class="bi <?= $mp_icons[$lnk['platform']] ?? 'bi-link' ?>"></i>
                  <?= $mp_labels[$lnk['platform']] ?? 'Beli' ?>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php else: ?>
          <div class="p-3 bg-light rounded-3 text-muted text-center" style="font-size:13px;">
            <i class="bi bi-info-circle me-1"></i>Link pembelian belum tersedia.
          </div>
          <?php endif; ?>

        </div>
      </div>
    </div>

    <!-- Produk Lainnya -->
    <?php if (!empty($produk_lain)): ?>
    <div class="mt-5 pt-5 border-top">
      <h5 class="fw-bold mb-4">Lihat Produk Lainnya</h5>
      <div class="row g-3">
        <?php foreach ($produk_lain as $lain): ?>
        <div class="col-6 col-lg-3">
          <a href="detail.php?id=<?= $lain['id'] ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden product-card-simple">
              <div class="bg-light d-flex align-items-center justify-content-center"
                   style="height:140px;overflow:hidden;">
                <?php if (!empty($lain['image_url'])): ?>
                  <img src="../<?= htmlspecialchars($lain['image_url']) ?>"
                       alt="<?= htmlspecialchars($lain['name']) ?>"
                       style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                  <i class="bi bi-image text-muted opacity-25 fs-1"></i>
                <?php endif; ?>
              </div>
              <div class="card-body p-3">
                <h6 class="text-dark fw-bold mb-1 small"><?= htmlspecialchars($lain['name']) ?></h6>
                <small class="text-primary fw-bold"><?= htmlspecialchars($lain['category']) ?></small>
              </div>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</main>

<script>
function toggleDesc() {
  var wrapper = document.getElementById('descWrapper');
  var btn     = document.getElementById('btnReadMore');
  if (wrapper.classList.contains('collapsed')) {
    wrapper.classList.remove('collapsed');
    wrapper.style.maxHeight = 'none';
    btn.innerText = 'Sembunyikan';
  } else {
    wrapper.classList.add('collapsed');
    wrapper.style.maxHeight = '100px';
    btn.innerText = 'Baca Selengkapnya...';
  }
}

// Tracking klik link marketplace
document.querySelectorAll('[data-track-link]').forEach(function (el) {
  el.addEventListener('click', function () {
    fetch('../api/track.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type: 'link', id: this.dataset.trackLink })
    }).catch(function () {});
  });
});
</script>

<?php require_once '../includes/footer.php'; ?>