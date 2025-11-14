<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Forbidden']);
  exit;
}

$name = trim($_POST['name'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$description = trim($_POST['description'] ?? '');
$is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

if ($name === '' || $slug === '') {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Name and Slug are required']);
  exit;
}

$conn = db_connect();
// Optional image upload
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
    $uploadDir = realpath(__DIR__ . '/..') . '/uploads/categories';
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
    $safeName = 'cat_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
    $dest = $uploadDir . '/' . $safeName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
      $image_url = 'uploads/categories/' . $safeName;
    }
  }
}

$stmt = $conn->prepare('INSERT INTO categories (name, slug, description, image_url, parent_id, is_active, sort_order, created_at) VALUES (?, ?, ?, ?, NULL, ?, 0, NOW())');
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $conn->error]);
  exit;
}
$stmt->bind_param('sss si', $name, $slug, $description, $image_url, $is_active);
// fix bind types without space
$stmt->close();
$stmt = $conn->prepare('INSERT INTO categories (name, slug, description, image_url, parent_id, is_active, sort_order, created_at) VALUES (?, ?, ?, ?, NULL, ?, 0, NOW())');
$stmt->bind_param('ssssi', $name, $slug, $description, $image_url, $is_active);
$ok = $stmt->execute();
if (!$ok) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => $stmt->error]);
  $stmt->close();
  exit;
}
$id = $stmt->insert_id;
$stmt->close();

// fetch created row data
$res = $conn->query('SELECT id, name, slug, is_active, created_at FROM categories WHERE id = ' . (int)$id . ' LIMIT 1');
$row = $res ? $res->fetch_assoc() : null;

echo json_encode(['ok' => true, 'category' => $row]);
