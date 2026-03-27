<?php
// ============================================================
// API : track.php — catat klik produk & link marketplace
// Method  : POST
// Header  : Content-Type: application/json
// ============================================================
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

require_once '../Config/Connection.php';

// Baca body JSON
$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

$type = $body['type'] ?? '';
$id   = (int)($body['id'] ?? 0);

if (!in_array($type, ['product', 'link']) || $id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameter']);
    exit;
}

if ($type === 'product') {
    mysqli_query($conn, "UPDATE products SET click_count = click_count + 1 WHERE id = $id");
} elseif ($type === 'link') {
    mysqli_query($conn, "UPDATE marketplace_links SET click_count = click_count + 1 WHERE id = $id");
}

// Catat ke visitor_logs
$ip    = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR'] ?? '');
$page  = mysqli_real_escape_string($conn, $type === 'product' ? "product:$id" : "link:$id");
$agent = mysqli_real_escape_string($conn, substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255));
mysqli_query($conn, "INSERT INTO visitor_logs (ip_address, page_visited, user_agent) VALUES ('$ip','$page','$agent')");

echo json_encode(['status' => 'ok']);