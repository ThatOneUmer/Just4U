<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'seller') {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Forbidden']);
  exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Invalid id']);
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

if ($seller_id <= 0) { echo json_encode(['ok' => false, 'error' => 'Seller not found']); exit; }

$check = $conn->prepare('SELECT id FROM products WHERE id = ? AND seller_id = ? LIMIT 1');
$check->bind_param('ii', $id, $seller_id);
$check->execute();
$check->store_result();
if ($check->num_rows === 0) { $check->close(); echo json_encode(['ok' => false, 'error' => 'Not allowed']); exit; }
$check->close();

$stmt1 = $conn->prepare('DELETE FROM product_images WHERE product_id = ?');
$stmt1->bind_param('i', $id);
$stmt1->execute();
$stmt1->close();

$stmt2 = $conn->prepare('DELETE FROM products WHERE id = ? LIMIT 1');
$stmt2->bind_param('i', $id);
$ok = $stmt2->execute();
$err = $ok ? null : $stmt2->error;
$stmt2->close();

if ($ok) {
  echo json_encode(['ok' => true, 'deleted' => $id]);
} else {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => $err ?: 'Delete failed']);
}
?>