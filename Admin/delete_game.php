<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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
// Remove relations
$stmt = $conn->prepare('DELETE FROM products WHERE game_id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

$stmt2 = $conn->prepare('DELETE FROM games WHERE id = ? LIMIT 1');
$stmt2->bind_param('i', $id);
$ok = $stmt2->execute();
$err = $ok ? null : $stmt2->error;
$stmt2->close();

if ($ok) {
  echo json_encode(['ok' => true]);
} else {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => $err ?: 'Delete failed']);
}
