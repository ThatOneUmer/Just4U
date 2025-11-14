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
$rid = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;

if ($rid <= 0) { echo json_encode(['ok' => false, 'error' => 'Invalid review']); exit; }

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

// Ensure the review belongs to a product of this seller
$sqlCheck = 'SELECT r.id FROM reviews r JOIN products p ON p.id = r.product_id WHERE r.id = ? AND p.seller_id = ? LIMIT 1';
$stc = $conn->prepare($sqlCheck);
$stc->bind_param('ii', $rid, $seller_id);
$stc->execute();
$stc->store_result();
if ($stc->num_rows === 0) { $stc->close(); echo json_encode(['ok' => false, 'error' => 'Not allowed']); exit; }
$stc->close();

$std = $conn->prepare('DELETE FROM reviews WHERE id = ? LIMIT 1');
$std->bind_param('i', $rid);
$ok = $std->execute();
$err = $ok ? null : $std->error;
$std->close();

if ($ok) {
  echo json_encode(['ok' => true, 'deleted' => $rid]);
} else {
  echo json_encode(['ok' => false, 'error' => $err ?: 'Delete failed']);
}
?>