<?php
// Hubungkan ke database kamu
require_once '../Config/koneksi.php'; 

// Data admin baru yang ingin kita buat
$userbaru = 'admin';
$passbaru = 'admin123'; // Ini password yang akan kamu ketik nanti

// Cek apakah tabel users ada dan masukkan datanya
$sql = "INSERT INTO users (username, password) VALUES ('$userbaru', '$passbaru')";

if (mysqli_query($conn, $sql)) {
    echo "<h3>Sukses! Akun Admin berhasil dibuat.</h3>";
    echo "<p>Username: <b>admin</b></p>";
    echo "<p>Password: <b>admin123</b></p>";
    echo "<br><a href='login.php'>Klik di sini untuk ke halaman Login</a>";
} else {
    echo "Gagal membuat akun: " . mysqli_error($conn);
}
?>