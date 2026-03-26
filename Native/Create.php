<?php
include '../Config/koneksi.php';

if (isset($_POST['submit'])) {
    $nama  = $_POST['nama_produk'];
    $harga = $_POST['harga'];

    mysqli_query($conn, "INSERT INTO produk (nama_produk, harga) 
                         VALUES ('$nama', '$harga')");

    header("Location: Read(index).php");
}
?>

<h2>Tambah Produk</h2>
<form method="POST">
    Nama Produk: <input type="text" name="nama_produk"><br>
    Harga: <input type="number" name="harga"><br>
    <button type="submit" name="submit">Simpan</button>
</form>