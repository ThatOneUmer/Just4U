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
$name = trim($_POST['name'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$description = trim($_POST['description'] ?? '');
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

if ($id <= 0 || $name === '' || $slug === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Invalid data']);
  exit;
}

$conn = db_connect();
$image_url = null;
if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp'
  ];
  $type = @mime_content_type($_FILES['image']['tmp_name']);
  if (isset($allowed[$type])) {
    $ext = $allowed[$type];
    $uploadDir = realpath(__DIR__ . '/..') . '/uploads/games';
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
    $safeName = 'game_' . $id . '_' . time() . '.' . $ext;
    $dest = $uploadDir . '/' . $safeName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
      $image_url = 'uploads/games/' . $safeName;
    }
  }
}

if ($image_url) {
  $stmt = $conn->prepare('UPDATE games SET name = ?, slug = ?, description = ?, image_url = ?, is_active = ?, created_at = created_at WHERE id = ?');
  $stmt->bind_param('ssssii', $name, $slug, $description, $image_url, $is_active, $id);
} else {
  $stmt = $conn->prepare('UPDATE games SET name = ?, slug = ?, description = ?, is_active = ?, created_at = created_at WHERE id = ?');
  $stmt->bind_param('sssii', $name, $slug, $description, $is_active, $id);
}

if (!$stmt) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $conn->error]);
  exit;
}

$ok = $stmt->execute();
$err = $ok ? null : $stmt->error;
$stmt->close();

if ($ok) {
  echo json_encode(['ok' => true]);
} else {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => $err ?: 'Update failed']);
}
