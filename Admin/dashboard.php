<?php
$pageTitle = 'dashboard';
require_once '../includes/auth_check.php';
require_once '../Native/Config/koneksi.php';
require_once '../includes/header_admin.php';

// Statistik
$total_produk   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE is_active=1"))[0];
$total_pengunjung = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM visitor_logs"))[0];
$kunjungan_hari = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM visitor_logs WHERE DATE(visited_at) = CURDATE()"))[0];
$total_klik_link= mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(click_count) FROM marketplace_links"))[0] ?? 0;

// Produk paling sering diklik
$top_produk = mysqli_query($conn, "SELECT name, click_count FROM products ORDER BY click_count DESC LIMIT 5");

// Link marketplace paling sering diklik
$top_links = mysqli_query($conn, "SELECT ml.platform, ml.url, ml.click_count, p.name as product_name
    FROM marketplace_links ml
    JOIN products p ON ml.product_id = p.id
    ORDER BY ml.click_count DESC LIMIT 5");

// Kunjungan 7 hari terakhir
$kunjungan_7hari = mysqli_query($conn, "
    SELECT DATE(visited_at) as tgl, COUNT(*) as jml
    FROM visitor_logs
    WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(visited_at)
    ORDER BY tgl ASC
");
$chart_labels = [];
$chart_data   = [];
while ($r = mysqli_fetch_assoc($kunjungan_7hari)) {
    $chart_labels[] = date('d M', strtotime($r['tgl']));
    $chart_data[]   = (int)$r['jml'];
}
?>

<!-- STAT CARDS -->
<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon blue"><i class="bi bi-box-seam"></i></div>
    <div>
      <div class="stat-num"><?= $total_produk ?></div>
      <div class="stat-label">Total Produk Aktif</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green"><i class="bi bi-people"></i></div>
    <div>
      <div class="stat-num"><?= number_format($total_pengunjung) ?></div>
      <div class="stat-label">Total Pengunjung</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon amber"><i class="bi bi-calendar-day"></i></div>
    <div>
      <div class="stat-num"><?= $kunjungan_hari ?></div>
      <div class="stat-label">Kunjungan Hari Ini</div>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red"><i class="bi bi-cursor"></i></div>
    <div>
      <div class="stat-num"><?= number_format($total_klik_link) ?></div>
      <div class="stat-label">Total Klik Marketplace</div>
    </div>
  </div>
</div>

<!-- GRAFIK KUNJUNGAN -->
<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title"><i class="bi bi-graph-up"></i>Kunjungan 7 Hari Terakhir</div>
  </div>
  <div class="admin-card-body">
    <canvas id="chartKunjungan" height="80"></canvas>
  </div>
</div>

<!-- PRODUK & LINK TERPOPULER -->
<div class="row g-4">
  <div class="col-lg-6">
    <div class="admin-card">
      <div class="admin-card-header">
        <div class="admin-card-title"><i class="bi bi-fire"></i>Produk Paling Diklik</div>
      </div>
      <div class="admin-card-body p-0">
        <table class="table-umkm">
          <thead><tr><th>Nama Produk</th><th>Klik</th></tr></thead>
          <tbody>
            <?php while ($p = mysqli_fetch_assoc($top_produk)): ?>
            <tr>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><span class="badge-aktif"><?= $p['click_count'] ?></span></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="admin-card">
      <div class="admin-card-header">
        <div class="admin-card-title"><i class="bi bi-shop"></i>Link Marketplace Paling Diklik</div>
      </div>
      <div class="admin-card-body p-0">
        <table class="table-umkm">
          <thead><tr><th>Platform</th><th>Produk</th><th>Klik</th></tr></thead>
          <tbody>
            <?php while ($l = mysqli_fetch_assoc($top_links)): ?>
            <tr>
              <td><span class="badge-aktif"><?= ucfirst($l['platform']) ?></span></td>
              <td><?= htmlspecialchars($l['product_name']) ?></td>
              <td><?= $l['click_count'] ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('chartKunjungan'), {
  type: 'line',
  data: {
    labels: <?= json_encode($chart_labels) ?>,
    datasets: [{
      label: 'Kunjungan',
      data: <?= json_encode($chart_data) ?>,
      borderColor: '#1A56DB',
      backgroundColor: 'rgba(26,86,219,0.08)',
      borderWidth: 2,
      tension: 0.4,
      fill: true,
      pointBackgroundColor: '#1A56DB',
      pointRadius: 4
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } },
      x: { grid: { display: false } }
    }
  }
});
</script>

<?php require_once '../includes/footer_admin.php'; ?>