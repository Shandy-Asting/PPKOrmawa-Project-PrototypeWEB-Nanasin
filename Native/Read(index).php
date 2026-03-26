<?php
include '../Config/koneksi.php';

$query = mysqli_query($conn, "SELECT * FROM produk");
?>

<h2>Data Produk</h2>
<a href="Create.php">Tambah Data</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Nama Produk</th>
        <th>Harga</th>
        <th>Aksi</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($query)) { ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['nama_produk'] ?></td>
        <td><?= $row['harga'] ?></td>
        <td>
            <a href="Update.php?id=<?= $row['id'] ?>">Edit</a>
            <a href="Delete.php?id=<?= $row['id'] ?>">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>