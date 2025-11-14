<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'seller') {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Forbidden']);
  exit;
}

$conn = db_connect();
$user_id = (int)$_SESSION['user_id'];

$u = db_get_user_by_id($user_id);
if (!$u || (int)($u['is_verified'] ?? 0) !== 1) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Seller pending approval']);
  exit;
}

$seller_id = 0;
$stmt = $conn->prepare('SELECT id FROM sellers WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($seller_id);
$stmt->fetch();
$stmt->close();

if ($seller_id <= 0) {
  echo json_encode(['ok' => true, 'reviews' => []]);
  exit;
}

$sql = "SELECT r.id, r.product_id, r.customer_id, r.rating, r.title, r.comment, r.created_at, r.is_verified_purchase,
               u.username AS customer_username,
               p.title AS product_title
        FROM reviews r
        JOIN products p ON p.id = r.product_id
        JOIN users u ON u.id = r.customer_id
        WHERE p.seller_id = ? AND r.status = 'approved'
        ORDER BY r.created_at DESC
        LIMIT 200";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($row = $res->fetch_assoc()) { $rows[] = $row; }
$stmt->close();

echo json_encode(['ok' => true, 'reviews' => $rows]);
?>