<?php
$pageTitle = 'Dokumentasi Kegiatan UMKM';
require_once '../Native/Config/koneksi.php';
require_once '../includes/header.php';

// Simulasi Data (Nanti bisa diambil dari Database oleh Admin)
$gallery_photos = [
    ['file' => 'kegiatan_1.jpg', 'caption' => 'Pelatihan Pembuatan Olahan Nanas'],
    ['file' => 'kegiatan_2.jpg', 'caption' => 'Pelayihan Pembuatan olahan selai'],
    ['file' => 'kegiatan_3.jpg', 'caption' => 'Kunjungan Presiden Indonesia'],
    ['file' => 'kegiatan_4.jpg', 'caption' => 'Bazar Produk Unggulan Desa Sempu']
];

$gallery_videos = [
    ['embed_id' => 'zPo5n3-e18s', 'title' => 'Profil UMKM Desa Sempu'],
    ['embed_id' => 'ScMx_nL-5eU', 'title' => 'Proses Pembuatan Kain Tenun Lokal']
];
?>

<main class="py-5" style="background: #fdfdfd;">
  <div class="container">
    
    <div class="row mb-5 text-center justify-content-center">
      <div class="col-lg-8">
        <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-3 rounded-pill fw-bold">GALERI KEGIATAN</span>
        <h1 class="fw-bold mb-3">Dokumentasi UMKM Desa Sempu</h1>
        <p class="text-muted" style="line-height: 1.8;">
          Kumpulan momen berharga dari berbagai kegiatan, pelatihan, dan pameran yang diikuti oleh para pelaku UMKM Desa Sempu, Ngancar, Kediri.
        </p>
      </div>
    </div>

  <ul class="nav nav-pills nav-justified mb-4 mb-md-5 shadow-sm rounded-4 p-2 bg-white" id="docTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active rounded-pill py-2" id="photo-tab" data-bs-toggle="tab" data-bs-target="#photo-pane" type="button" role="tab">
        <i class="bi bi-images me-2"></i><span class="small-text">Foto</span>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link rounded-pill py-2" id="video-tab" data-bs-toggle="tab" data-bs-target="#video-pane" type="button" role="tab">
        <i class="bi bi-youtube me-2"></i><span class="small-text">Video</span>
      </button>
    </li>
  </ul>

    <div class="tab-content" id="docTabContent">
      
      <div class="tab-pane fade show active" id="photo-pane" role="tabpanel" aria-labelledby="photo-tab" tabindex="0">
        <h5 class="fw-bold mb-4 d-md-none text-center">Galeri Foto Kunjungan</h5>
        <div class="row g-3">
          <?php foreach($gallery_photos as $photo): ?>
          <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden photo-card h-100">
              <div class="bg-light d-flex align-items-center justify-content-center ratio ratio-1x1 photo-frame">
                 <i class="bi bi-camera-fill text-muted opacity-25 fs-1"></i>
                 </div>
              <div class="card-body p-3">
                <p class="text-muted mb-0 small" style="line-height: 1.4;"><?= htmlspecialchars($photo['caption']) ?></p>
              </div>
              <div class="photo-overlay d-flex align-items-center justify-content-center">
                 <i class="bi bi-eye-fill text-white fs-3"></i>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="tab-pane fade" id="video-pane" role="tabpanel" aria-labelledby="video-tab" tabindex="0">
        <h5 class="fw-bold mb-4 d-md-none text-center">Galeri Video Profil</h5>
        <div class="row g-4 justify-content-center">
          <?php foreach($gallery_videos as $video): ?>
          <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
              <div class="ratio ratio-16x9 map-wrapper border">
                 <iframe 
                   src="https://www.youtube.com/embed/<?= $video['embed_id'] ?>" 
                   title="<?= htmlspecialchars($video['title']) ?>" 
                   allowfullscreen="" 
                   loading="lazy">
                 </iframe>
              </div>
              <div class="card-body p-4 bg-white">
                <div class="d-flex align-items-center gap-3">
                  <div class="icon-box-small text-danger fs-4 bg-danger-subtle rounded-3">
                    <i class="bi bi-youtube"></i>
                  </div>
                  <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($video['title']) ?></h6>
                </div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

  </div>
</main>

<?php require_once '../includes/footer.php'; ?>