<?php
$pageTitle = 'Kelola Lokasi';
require_once '../includes/auth_check.php';
require_once '../Native/Config/koneksi.php';

$csrf  = generate_csrf_token();
$alert = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('CSRF error');
    $action = $_POST['action'] ?? '';

    if ($action === 'tambah' || $action === 'edit') {
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
        $addr = mysqli_real_escape_string($conn, trim($_POST['address']));
        $lat  = (float)$_POST['latitude'];
        $lng  = (float)$_POST['longitude'];
        $desc = mysqli_real_escape_string($conn, trim($_POST['description']));

        if ($action === 'tambah') {
            mysqli_query($conn, "INSERT INTO locations (name,address,latitude,longitude,description) VALUES ('$name','$addr',$lat,$lng,'$desc')");
            $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Lokasi berhasil ditambahkan.</div>';
        } else {
            $id = (int)$_POST['id'];
            mysqli_query($conn, "UPDATE locations SET name='$name',address='$addr',latitude=$lat,longitude=$lng,description='$desc' WHERE id=$id");
            $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Lokasi berhasil diperbarui.</div>';
        }
    }
    if ($action === 'hapus') {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "DELETE FROM locations WHERE id=$id");
        $alert = '<div class="alert-umkm alert-success"><i class="bi bi-check-circle"></i>Lokasi berhasil dihapus.</div>';
    }
}

$lokasi = [];
$res = mysqli_query($conn, "SELECT * FROM locations ORDER BY created_at DESC");
while ($r = mysqli_fetch_assoc($res)) $lokasi[] = $r;

require_once '../includes/header_admin.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div class="admin-card-title"><i class="bi bi-geo-alt"></i>Data Lokasi</div>
    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalTambah">
      <i class="bi bi-plus-lg"></i>Tambah Lokasi
    </button>
  </div>
  <div class="admin-card-body p-0">
    <?= $alert ?>
    <div style="overflow-x:auto;">
    <table class="table-umkm">
      <thead><tr><th>#</th><th>Nama</th><th>Alamat</th><th>Koordinat</th><th>Aksi</th></tr></thead>
      <tbody>
        <?php if (empty($lokasi)): ?>
        <tr><td colspan="5" style="text-align:center;padding:24px;color:#6b7280;">Belum ada data lokasi.</td></tr>
        <?php else: ?>
        <?php foreach ($lokasi as $i => $l): ?>
        <tr>
          <td><?= $i+1 ?></td>
          <td style="font-weight:600;"><?= htmlspecialchars($l['name']) ?></td>
          <td style="font-size:12px;"><?= htmlspecialchars($l['address']) ?></td>
          <td style="font-size:12px;"><?= $l['latitude'] ?>, <?= $l['longitude'] ?></td>
          <td>
            <div class="d-flex gap-2">
              <button class="btn-edit"
                onclick="openEdit(<?= $l['id'] ?>,'<?= addslashes($l['name']) ?>','<?= addslashes($l['address']) ?>','<?= $l['latitude'] ?>','<?= $l['longitude'] ?>','<?= addslashes($l['description']) ?>')">
                <i class="bi bi-pencil"></i>Edit
              </button>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="hapus">
                <input type="hidden" name="id" value="<?= $l['id'] ?>">
                <button type="submit" class="btn-delete" data-confirm="Yakin hapus lokasi ini?">
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
    <form method="POST" class="modal-content">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="action" value="tambah">
      <div class="modal-header"><h5 class="modal-title">Tambah Lokasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Nama Tempat</label>
          <input type="text" name="name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Alamat</label>
          <textarea name="address" class="form-control" rows="2"></textarea></div>
        <div class="row g-3 mb-3">
          <div class="col-6"><label class="form-label">Latitude</label>
            <input type="number" name="latitude" step="any" class="form-control" placeholder="-7.123456"></div>
          <div class="col-6"><label class="form-label">Longitude</label>
            <input type="number" name="longitude" step="any" class="form-control" placeholder="112.123456"></div>
        </div>
        <div class="mb-3"><label class="form-label">Deskripsi</label>
          <textarea name="description" class="form-control" rows="2"></textarea></div>
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
      <input type="hidden" name="id" id="eId">
      <div class="modal-header"><h5 class="modal-title">Edit Lokasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Nama Tempat</label>
          <input type="text" name="name" id="eName" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Alamat</label>
          <textarea name="address" id="eAddr" class="form-control" rows="2"></textarea></div>
        <div class="row g-3 mb-3">
          <div class="col-6"><label class="form-label">Latitude</label>
            <input type="number" name="latitude" id="eLat" step="any" class="form-control"></div>
          <div class="col-6"><label class="form-label">Longitude</label>
            <input type="number" name="longitude" id="eLng" step="any" class="form-control"></div>
        </div>
        <div class="mb-3"><label class="form-label">Deskripsi</label>
          <textarea name="description" id="eDesc" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn-add">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEdit(id,name,addr,lat,lng,desc){
  document.getElementById('eId').value   = id;
  document.getElementById('eName').value = name;
  document.getElementById('eAddr').value = addr;
  document.getElementById('eLat').value  = lat;
  document.getElementById('eLng').value  = lng;
  document.getElementById('eDesc').value = desc;
  new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>

<?php require_once '../includes/footer_admin.php'; ?>