<?php
$pageTitle = 'Kelola Dokumentasi';
require_once '../includes/auth_check.php';
require_once '../Native/Config/koneksi.php';

$csrf  = generate_csrf_token();
$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('CSRF error');
    $action = $_POST['action'] ?? '';

    if ($action === 'tambah') {
        $pid     = (int)$_POST['product_id'];
        $type    = in_array($_POST['type'], ['photo','video']) ? $_POST['type'] : 'photo';
        $caption = mysqli_real_escape_string($conn, trim($_POST['caption']));
        $order   = (int)($_POST['sort_order'] ?? 0);
        $file_url = '';

        if (!empty($_FILES['file']['name'])) {
            $ext_allowed = ['jpg','jpeg','png','gif','mp4','webm'];
            $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $ext_allowed)) {
                $filename = time() . '_' . uniqid() . '.' . $ext;
                $upload_dir = '../assets/uploads/doc/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $filename)) {
                    $file_url = 'assets/uploads/doc/' . $filename;
                }
            }
        }

        if ($file_url) {
            $file_url_safe = mysqli_real_escape_string($conn, $file_url);
            mysqli_query($conn, "INSERT INTO documentations (product_id,type,file_url,caption,sort_order) VALUES ($pid,'$type','$file_url_safe','$caption',$order)");
            $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Dokumentasi berhasil ditambahkan.</div>';
        } else {
            $alert = '<div class="alert-umkm alert-error"><i class="bi bi-x-circle"></i>File gagal diupload. Pastikan format benar (jpg, png, mp4).</div>';
        }
    }

    if ($action === 'hapus') {
        $id = (int)$_POST['id'];
        // Ambil file_url dulu untuk hapus file
        $r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT file_url FROM documentations WHERE id=$id"));
        if ($r && file_exists('../' . $r['file_url'])) unlink('../' . $r['file_url']);
        mysqli_query($conn, "DELETE FROM documentations WHERE id=$id");
        $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Dokumentasi berhasil dihapus.</div>';
    }
}

$docs = [];
$res = mysqli_query($conn, "SELECT d.*, p.name as product_name FROM documentations d JOIN products p ON d.product_id=p.id ORDER BY d.created_at DESC");
while ($r = mysqli_fetch_assoc($res)) $docs[] = $r;

$products = [];
$res_p = mysqli_query($conn, "SELECT id,name FROM products WHERE is_active=1 ORDER BY name ASC");
while ($r = mysqli_fetch_assoc($res_p)) $products[] = $r;

require_once '../includes/header_admin.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title"><i class="bi bi-images"></i>Dokumentasi Produk</div>
    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-lg"></i>Upload Dokumen
    </button>
  </div>
  <div class="admin-card-body p-0">
    <?= $alert ?>
    <div style="overflow-x:auto;">
    <table class="table-umkm">
      <thead><tr><th>#</th><th>Produk</th><th>Tipe</th><th>Keterangan</th><th>File</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php if (empty($docs)): ?>
        <tr><td colspan="6" style="text-align:center;padding:24px;color:#6b7280;">Belum ada dokumentasi.</td></tr>
        <?php else: ?>
        <?php foreach ($docs as $i => $d): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td><?= htmlspecialchars($d['product_name']) ?></td>
          <td><span class="badge-aktif"><?= $d['type'] === 'photo' ? 'Foto' : 'Video' ?></span></td>
          <td><?= htmlspecialchars($d['caption'] ?? '-') ?></td>
          <td style="font-size:11px;color:#1A56DB;">
            <a href="../<?= htmlspecialchars($d['file_url']) ?>" target="_blank">Lihat File</a>
          </td>
          <td>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
              <input type="hidden" name="action" value="hapus">
              <input type="hidden" name="id" value="<?= $d['id'] ?>">
              <button type="submit" class="btn-delete" data-confirm="Yakin hapus dokumentasi ini?">
                <i class="bi bi-trash"></i>Hapus
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    </div>
  </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" enctype="multipart/form-data" class="modal-content">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="action" value="tambah">
      <div class="modal-header"><h5 class="modal-title">Upload Dokumentasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Produk</label>
          <select name="product_id" class="form-select" required>
            <option value="">-- Pilih Produk --</option>
            <?php foreach ($products as $p): ?>
              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3"><label class="form-label">Tipe</label>
          <select name="type" class="form-select">
            <option value="photo">Foto</option>
            <option value="video">Video</option>
          </select>
        </div>
        <div class="mb-3"><label class="form-label">File (JPG, PNG, MP4)</label>
          <input type="file" name="file" class="form-control" accept=".jpg,.jpeg,.png,.gif,.mp4,.webm" required>
        </div>
        <div class="mb-3"><label class="form-label">Keterangan</label>
          <input type="text" name="caption" class="form-control" placeholder="Opsional"></div>
        <div class="mb-3"><label class="form-label">Urutan Tampil</label>
          <input type="number" name="sort_order" class="form-control" value="0" min="0"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn-add">Upload</button>
      </div>
    </form>
  </div>
</div>

<?php require_once '../includes/footer_admin.php'; ?>