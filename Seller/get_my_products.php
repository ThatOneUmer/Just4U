<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

// Require seller login and approved
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'seller') {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Forbidden']);
  exit;
}

$conn = db_connect();
$user_id = (int)$_SESSION['user_id'];

// Ensure seller is approved
$u = db_get_user_by_id($user_id);
if (!$u || (int)($u['is_verified'] ?? 0) !== 1) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Seller pending approval']);
  exit;
}

// Lookup seller_id by user_id
$seller_id = 0;
$stmt = $conn->prepare('SELECT id FROM sellers WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($seller_id);
$stmt->fetch();
$stmt->close();

if ($seller_id <= 0) {
  echo json_encode(['ok' => true, 'products' => []]);
  exit;
}

// Fetch products for this seller with a primary image if exists
$sql = "SELECT p.id, p.title, p.price, p.status, p.created_at,
               (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 ORDER BY pi.id ASC LIMIT 1) AS image_url
        FROM products p
        WHERE p.seller_id = ?
        ORDER BY p.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($row = $res->fetch_assoc()) { $rows[] = $row; }
$stmt->close();

echo json_encode(['ok' => true, 'products' => $rows]);
