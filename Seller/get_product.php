<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'seller') {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Forbidden']);
  exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }

$conn = db_connect();
$user_id = (int)$_SESSION['user_id'];

// Ensure seller is approved and resolve seller_id
$u = db_get_user_by_id($user_id);
if (!$u || (int)($u['is_verified'] ?? 0) !== 1) {
  http_response_code(403);
  echo json_encode(['ok'=>false,'error'=>'Seller pending approval']);
  exit;
}

$seller_id = 0;
$stmt = $conn->prepare('SELECT id FROM sellers WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($seller_id);
$stmt->fetch();
$stmt->close();
if ($seller_id <= 0) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Seller profile not found']); exit; }

$sql = "SELECT p.id, p.title, p.price, p.game_id, p.platform, p.region, p.description, p.specification, p.status,
               (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 ORDER BY pi.id ASC LIMIT 1) AS image_url
        FROM products p
        WHERE p.id = ? AND p.seller_id = ?
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $id, $seller_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$row) { http_response_code(404); echo json_encode(['ok'=>false,'error'=>'Not found']); exit; }

echo json_encode(['ok'=>true, 'product'=>$row]);
