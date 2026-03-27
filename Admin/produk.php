<?php
$pageTitle = 'Kelola Produk';
require_once '../includes/auth_check.php';
require_once '../Native/Config/koneksi.php';

$csrf  = generate_csrf_token();
$alert = '';

// TAMBAH
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'tambah') {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('CSRF error');

  $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
  $desc     = mysqli_real_escape_string($conn, trim($_POST['description']));
  $cat      = mysqli_real_escape_string($conn, trim($_POST['category']));
  $admin_id = (int)$_SESSION['admin_id'];
  $image_url = '';

  if (!empty($_FILES['image']['name'])) {
    $ext_ok = ['jpg','jpeg','png','gif','webp'];
    $ext    = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $ext_ok)) {
      $filename   = time() . '_' . uniqid() . '.' . $ext;
      $upload_dir = '../assets/uploads/';
      if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
      if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
        $image_url = 'assets/uploads/' . $filename;
      }
    }
  }

  $image_safe = mysqli_real_escape_string($conn, $image_url);
  mysqli_query($conn, "INSERT INTO products (admin_id, name, description, category, image_url)
                        VALUES ($admin_id, '$name', '$desc', '$cat', '$image_safe')");
  $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Produk berhasil ditambahkan.</div>';
}

// EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('CSRF error');

  $id   = (int)$_POST['id'];
  $name = mysqli_real_escape_string($conn, trim($_POST['name']));
  $desc = mysqli_real_escape_string($conn, trim($_POST['description']));
  $cat  = mysqli_real_escape_string($conn, trim($_POST['category']));

  if (!empty($_FILES['image']['name'])) {
    $ext_ok = ['jpg','jpeg','png','gif','webp'];
    $ext    = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $ext_ok)) {
      $filename   = time() . '_' . uniqid() . '.' . $ext;
      $upload_dir = '../assets/uploads/';
      if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
      if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
        $image_safe = mysqli_real_escape_string($conn, 'assets/uploads/' . $filename);
        mysqli_query($conn, "UPDATE products SET name='$name', description='$desc', category='$cat', image_url='$image_safe' WHERE id=$id");
      }
    }
  } else {
    mysqli_query($conn, "UPDATE products SET name='$name', description='$desc', category='$cat' WHERE id=$id");
  }

  $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Produk berhasil diperbarui.</div>';
}

// HAPUS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'hapus') {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('CSRF error');
  $id = (int)$_POST['id'];
  $r  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image_url FROM products WHERE id=$id"));
  if ($r && !empty($r['image_url']) && file_exists('../' . $r['image_url'])) {
    unlink('../' . $r['image_url']);
  }
  mysqli_query($conn, "DELETE FROM products WHERE id=$id");
  $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Produk berhasil dihapus.</div>';
}

// TOGGLE AKTIF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
  if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('CSRF error');
  $id = (int)$_POST['id'];
  mysqli_query($conn, "UPDATE products SET is_active = IF(is_active=1,0,1) WHERE id=$id");
  $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Status produk diperbarui.</div>';
}

// Ambil semua produk
$result   = mysqli_query($conn, "SELECT p.*, a.username FROM products p JOIN admins a ON p.admin_id=a.id ORDER BY p.created_at DESC");
$products = [];
while ($row = mysqli_fetch_assoc($result)) $products[] = $row;

require_once '../includes/header_admin.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title"><i class="bi bi-box-seam"></i>Data Produk</div>
    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-lg"></i>Tambah Produk
    </button>
  </div>
  <div class="admin-card-body p-0">
    <?= $alert ?>
    <div style="overflow-x:auto;">
      <table class="table-umkm">
        <thead>
          <tr>
            <th>#</th>
            <th>Foto</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Klik</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
            <tr>
              <td colspan="7" style="text-align:center;padding:24px;color:#6b7280;">Belum ada produk.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($products as $i => $p): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td>
                <?php if (!empty($p['image_url'])): ?>
                  <img src="../<?= htmlspecialchars($p['image_url']) ?>"
                       style="width:50px;height:50px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                <?php else: ?>
                  <div style="width:50px;height:50px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;border:1px solid #e5e7eb;">
                    <i class="bi bi-image" style="color:#9ca3af;"></i>
                  </div>
                <?php endif; ?>
              </td>
              <td style="font-weight:600;"><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['category']) ?></td>
              <td><?= $p['click_count'] ?></td>
              <td>
                <?php if ($p['is_active']): ?>
                  <span class="badge-aktif">Aktif</span>
                <?php else: ?>
                  <span class="badge-nonaktif">Nonaktif</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="d-flex gap-2 flex-wrap">
                  <button class="btn-edit"
                    onclick="openEdit(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', '<?= addslashes($p['description']) ?>', '<?= addslashes($p['category']) ?>')">
                    <i class="bi bi-pencil"></i>Edit
                  </button>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn-toggle">
                      <i class="bi bi-toggle-<?= $p['is_active'] ? 'on' : 'off' ?>"></i>
                      <?= $p['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                    </button>
                  </form>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn-delete"
                      data-confirm="Yakin hapus produk '<?= htmlspecialchars($p['name']) ?>'?">
                      <i class="bi bi-trash"></i>Hapus
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
    <form method="POST" class="modal-content" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="action" value="tambah">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Produk</label>
          <input type="text" name="name" class="form-control" placeholder="Nama produk" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Kategori</label>
          <input type="text" name="category" class="form-control" placeholder="Makanan, Kerajinan, dst">
        </div>
        <div class="mb-3">
          <label class="form-label">Foto Produk</label>
          <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi produk"></textarea>
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
    <form method="POST" class="modal-content" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="editId">
      <div class="modal-header">
        <h5 class="modal-title">Edit Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Produk</label>
          <input type="text" name="name" id="editName" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Kategori</label>
          <input type="text" name="category" id="editCategory" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label">Ganti Foto <small style="color:#9ca3af;">(opsional)</small></label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <small style="color:#6b7280;">Kosongkan jika tidak ingin ganti foto.</small>
        </div>
        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" id="editDesc" class="form-control" rows="3"></textarea>
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
function openEdit(id, name, desc, cat) {
  document.getElementById('editId').value       = id;
  document.getElementById('editName').value     = name;
  document.getElementById('editDesc').value     = desc;
  document.getElementById('editCategory').value = cat;
  new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>

<?php require_once '../includes/footer_admin.php'; ?>