<?php
$pageTitle = 'Kelola Slider Hero';
require_once '../Native/Config/koneksi.php';
require_once '../includes/auth_check.php';

$alert = '';

// LOGIKA POIN 2 (BACKEND UPLOAD)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_hero'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $file  = $_FILES['hero_image'];
    
    // Validasi Folder (Buat jika belum ada)
    $target_dir = "../assets/img/hero/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_name = "hero_" . time() . "." . $ext;
    $target_file = $target_dir . $new_name;

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        mysqli_query($conn, "INSERT INTO hero_sliders (image_name, title) VALUES ('$new_name', '$title')");
        $alert = '<div class="alert alert-success">Gambar Hero berhasil ditambahkan!</div>';
    } else {
        $alert = '<div class="alert alert-danger">Gagal mengunggah gambar.</div>';
    }
}

// LOGIKA HAPUS
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $cek = mysqli_query($conn, "SELECT image_name FROM hero_sliders WHERE id = $id");
    $data = mysqli_fetch_assoc($cek);
    
    if ($data) {
        unlink("../assets/img/hero/" . $data['image_name']); // Hapus file fisik
        mysqli_query($conn, "DELETE FROM hero_sliders WHERE id = $id");
        $alert = '<div class="alert alert-warning">Gambar Hero telah dihapus.</div>';
    }
}

$sliders = mysqli_query($conn, "SELECT * FROM hero_sliders ORDER BY id DESC");
require_once '../includes/header_admin.php';
?>

<div class="container mt-4">
    <div class="admin-card">
        <div class="admin-card-header">
            <h5 class="mb-0">Upload Banner Hero (Slider)</h5>
        </div>
        <div class="admin-card-body">
            <?= $alert ?>
            <form action="" method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Pilih Gambar (Rekomendasi Landscape 1920x800)</label>
                    <input type="file" name="hero_image" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Judul Banner (Opsional)</label>
                    <input type="text" name="title" class="form-control" placeholder="Contoh: Produk Unggulan Desa">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="upload_hero" class="btn btn-primary w-100">Upload</button>
                </div>
            </form>

            <hr class="my-4">

            <div class="row">
                <?php while($row = mysqli_fetch_assoc($sliders)): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <img src="../assets/img/hero/<?= $row['image_name'] ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                        <div class="card-body p-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted"><?= htmlspecialchars($row['title']) ?></small>
                            <a href="?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus gambar ini?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer_admin.php'; ?>