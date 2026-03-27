<?php
$pageTitle = 'Lokasi Strategis UMKM';
require_once '../Native/Config/koneksi.php';
require_once '../includes/header.php';

// Data Lokasi (Bisa kamu tambah/ubah nantinya)
$locations = [
    [
        'nama' => 'Pusat Galeri UMKM Sempu',
        'desc' => 'Tempat berkumpulnya produk unggulan dari seluruh produk Desa Sempu.',
        'icon' => 'bi-shop'
    ],
    [
        'nama' => 'Olahan Kripik Nanas',
        'desc' => 'Lokasi pembuatan produk Kripik nanas.',
        'icon' => 'bi-tools'
    ],
    [
        'nama' => 'Olahan Selai Nanas',
        'desc' => 'Lokasi pembuatan Olahan selai nanas',
        'icon' => 'bi-info-square'
    ]
];
?>

<main class="py-5" style="background: #fdfdfd;">
  <div class="container">
    
    <div class="row mb-5">
      <div class="col-lg-6">
        <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-3 rounded-pill fw-bold">DESTINASI UMKM</span>
        <h1 class="fw-bold mb-3">Titik Lokasi Strategis</h1>
        <p class="text-muted">Jelajahi berbagai titik penting di Desa Sempu, Ngancar. Dari galeri produk hingga tempat produksi langsung.</p>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-5">
        <div class="location-list">
          <?php foreach($locations as $loc): ?>
          <div class="location-item p-4 mb-3 shadow-sm rounded-4 border-start border-4 border-primary bg-white">
            <div class="d-flex gap-3">
              <div class="icon-box text-primary fs-3">
                <i class="bi <?= $loc['icon'] ?>"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1"><?= $loc['nama'] ?></h6>
                <p class="small text-muted mb-0"><?= $loc['desc'] ?></p>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="alert alert-primary rounded-4 border-0 p-4 mt-4">
          <div class="d-flex gap-3 align-items-center">
            <i class="bi bi-geo-alt-fill fs-2"></i>
            <div>
              <h6 class="fw-bold mb-1">Koordinat Presisi</h6>
              <p class="small mb-0 opacity-75">Sempu, Ngancar, Kabupaten Kediri, Jawa Timur 64291</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="map-wrapper shadow-sm rounded-4 overflow-hidden border">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3950.443217436034!2d112.1989445!3d-7.9405833!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7894f6f7b1899d%3A0x63310065793077d1!2sSempu%2C%20Ngancar%2C%20Kediri%20Regency%2C%20East%20Java!5e0!3m2!1sen!2sid!4v1711462000000!5m2!1sen!2sid" 
            width="100%" 
            height="550" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy">
          </iframe>
        </div>
      </div>
    </div>

  </div>
</main>

<?php require_once '../includes/footer.php'; ?>