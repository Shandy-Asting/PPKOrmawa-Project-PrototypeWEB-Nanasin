<?php
// ============================================================
// API : stats.php — data statistik dashboard admin
// Method  : GET
// Keamanan: X-API-Key header
// ============================================================
header('Content-Type: application/json');

// Validasi API Key
$valid_key = 'UMKM_SECRET_KEY_2024'; // Ganti dengan key acak yang kuat
$api_key   = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($api_key !== $valid_key) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

require_once '../Config/Connection.php';

$total_produk     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE is_active=1"))[0];
$total_pengunjung = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM visitor_logs"))[0];
$kunjungan_hari   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM visitor_logs WHERE DATE(visited_at)=CURDATE()"))[0];
$total_klik       = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(click_count) FROM marketplace_links"))[0] ?? 0;

// Top produk
$top_produk = [];
$r = mysqli_query($conn, "SELECT name, click_count FROM products ORDER BY click_count DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($r)) $top_produk[] = $row;

// Top link
$top_links = [];
$r = mysqli_query($conn, "SELECT ml.platform, ml.click_count, p.name as product FROM marketplace_links ml JOIN products p ON ml.product_id=p.id ORDER BY ml.click_count DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($r)) $top_links[] = $row;

// Kunjungan 7 hari
$kunjungan_chart = [];
$r = mysqli_query($conn, "SELECT DATE(visited_at) as tgl, COUNT(*) as jml FROM visitor_logs WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(visited_at) ORDER BY tgl ASC");
while ($row = mysqli_fetch_assoc($r)) $kunjungan_chart[] = $row;

echo json_encode([
    'status' => 'ok',
    'data'   => [
        'total_produk'      => (int)$total_produk,
        'total_pengunjung'  => (int)$total_pengunjung,
        'kunjungan_hari_ini'=> (int)$kunjungan_hari,
        'total_klik_link'   => (int)$total_klik,
        'top_produk'        => $top_produk,
        'top_links'         => $top_links,
        'kunjungan_7hari'   => $kunjungan_chart,
    ]
]);