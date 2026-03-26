<?php
include '../Config/koneksi.php';

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM produk WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $nama  = $_POST['nama_produk'];
    $harga = $_POST['harga'];

    mysqli_query($conn, "UPDATE produk 
                         SET nama_produk='$nama', harga='$harga' 
                         WHERE id='$id'");

    header("Location: Read(index).php");
}
?>

<h2>Edit Produk</h2>
<form method="POST">
    Nama Produk: <input type="text" name="nama_produk" value="<?= $row['nama_produk'] ?>"><br>
    Harga: <input type="number" name="harga" value="<?= $row['harga'] ?>"><br>
    <button type="submit" name="update">Update</button>
</form>