<?php
$pageTitle = 'Kelola Marketplace';
require_once '../includes/auth_check.php';
require_once '../Native/Config/koneksi.php';

$csrf  = generate_csrf_token();
$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('CSRF error');
    $action = $_POST['action'] ?? '';

    if ($action === 'tambah') {
        $pid      = (int)$_POST['product_id'];
        $platform = mysqli_real_escape_string($conn, $_POST['platform']);
        $url      = mysqli_real_escape_string($conn, trim($_POST['url']));
        mysqli_query($conn, "INSERT INTO marketplace_links (product_id,platform,url) VALUES ($pid,'$platform','$url')");
        $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Link berhasil ditambahkan.</div>';
    }
    if ($action === 'edit') {
        $id       = (int)$_POST['id'];
        $platform = mysqli_real_escape_string($conn, $_POST['platform']);
        $url      = mysqli_real_escape_string($conn, trim($_POST['url']));
        mysqli_query($conn, "UPDATE marketplace_links SET platform='$platform',url='$url' WHERE id=$id");
        $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Link berhasil diperbarui.</div>';
    }
    if ($action === 'hapus') {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "DELETE FROM marketplace_links WHERE id=$id");
        $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Link berhasil dihapus.</div>';
    }
    if ($action === 'toggle') {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "UPDATE marketplace_links SET is_active=IF(is_active=1,0,1) WHERE id=$id");
        $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Status diperbarui.</div>';
    }
}

$links = [];
$res = mysqli_query($conn, "SELECT ml.*, p.name as product_name FROM marketplace_links ml JOIN products p ON ml.product_id=p.id ORDER BY ml.created_at DESC");
while ($r = mysqli_fetch_assoc($res)) $links[] = $r;

$products = [];
$res_p = mysqli_query($conn, "SELECT id, name FROM products WHERE is_active=1 ORDER BY name ASC");
while ($r = mysqli_fetch_assoc($res_p)) $products[] = $r;

require_once '../includes/header_admin.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title"><i class="bi bi-shop"></i>Link Marketplace</div>
    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-lg"></i>Tambah Link
    </button>
  </div>
  <div class="admin-card-body p-0">
    <?= $alert ?>
    <div style="overflow-x:auto;">
    <table class="table-umkm">
      <thead>
        <tr><th>#</th><th>Produk</th><th>Platform</th><th>URL</th><th>Klik</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php if (empty($links)): ?>
        <tr><td colspan="7" style="text-align:center;padding:24px;color:#6b7280;">Belum ada link marketplace.</td></tr>
        <?php else: ?>
        <?php foreach ($links as $i => $l): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= htmlspecialchars($l['product_name']) ?></td>
          <td><span class="badge-aktif"><?= ucfirst($l['platform']) ?></span></td>
          <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            <a href="<?= htmlspecialchars($l['url']) ?>" target="_blank" style="color:#1A56DB;font-size:12px;">
              <?= htmlspecialchars($l['url']) ?>
            </a>
          </td>
          <td><?= $l['click_count'] ?></td>
          <td><?= $l['is_active'] ? '<span class="badge-aktif">Aktif</span>' : '<span class="badge-nonaktif">Nonaktif</span>' ?></td>
          <td>
            <div class="d-flex gap-2 flex-wrap">
              <button class="btn-edit"
                onclick="openEdit(<?= $l['id'] ?>,'<?= $l['platform'] ?>','<?= addslashes($l['url']) ?>')">
                <i class="bi bi-pencil"></i>Edit
              </button>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= $l['id'] ?>">
                <button type="submit" class="btn-toggle">
                  <i class="bi bi-toggle-<?= $l['is_active'] ? 'on' : 'off' ?>"></i>
                </button>
              </form>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="hapus">
                <input type="hidden" name="id" value="<?= $l['id'] ?>">
                <button type="submit" class="btn-delete" data-confirm="Yakin hapus link ini?">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
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
    <form method="POST" class="modal-content">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="action" value="tambah">
      <div class="modal-header"><h5 class="modal-title">Tambah Link Marketplace</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Produk</label>
          <select name="product_id" class="form-select" required>
            <option value="">-- Pilih Produk --</option>
            <?php foreach ($products as $p): ?>
              <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Platform</label>
          <select name="platform" class="form-select" required>
            <option value="shopee">Shopee</option>
            <option value="tokopedia">Tokopedia</option>
            <option value="whatsapp">WhatsApp</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">URL / Link</label>
          <input type="url" name="url" class="form-control" placeholder="https://..." required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn-add">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="editId">
      <div class="modal-header"><h5 class="modal-title">Edit Link</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Platform</label>
          <select name="platform" id="editPlatform" class="form-select">
            <option value="shopee">Shopee</option>
            <option value="tokopedia">Tokopedia</option>
            <option value="whatsapp">WhatsApp</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">URL / Link</label>
          <input type="url" name="url" id="editUrl" class="form-control" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn-add">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEdit(id, platform, url) {
  document.getElementById('editId').value       = id;
  document.getElementById('editPlatform').value = platform;
  document.getElementById('editUrl').value      = url;
  new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>

<?php require_once '../includes/footer_admin.php'; ?>