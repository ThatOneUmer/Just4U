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

// Ensure seller is approved
$u = db_get_user_by_id($user_id);
if (!$u || (int)($u['is_verified'] ?? 0) !== 1) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Seller pending approval']);
  exit;
}

// Map user->seller id
$seller_id = 0;
$stmt = $conn->prepare('SELECT id FROM sellers WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($seller_id);
$stmt->fetch();
$stmt->close();
if ($seller_id <= 0) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Seller profile not found']);
  exit;
}

// Inputs
$title = trim($_POST['title'] ?? '');
$price = trim($_POST['price'] ?? '');
$game_id = (int)($_POST['game_id'] ?? 0);
$platform = trim($_POST['platform'] ?? '');
$region = trim($_POST['region'] ?? '');
$description = trim($_POST['description'] ?? '');
$specification = trim($_POST['specification'] ?? '');

if ($title === '' || $price === '' || $game_id <= 0 || $platform === '' || $region === ''){
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Missing required fields']);
  exit;
}

// Price numeric
if (!is_numeric($price) || (float)$price < 0) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Invalid price']);
  exit;
}

// Generate slug (simple)
function slugify($text){
  $text = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $text));
  $text = trim($text, '-');
  if ($text === '') $text = 'product';
  return $text . '-' . time();
}
$slug = slugify($title);

// Insert product
$sql = 'INSERT INTO products (seller_id, game_id, title, slug, description, specification, price, original_price, platform, region, account_level, account_rank, features, delivery_type, delivery_time, stock_quantity, is_verified, is_featured, status, views_count, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, NULL, NULL, NULL, "Instant", "5 minutes", 1, 0, 0, "active", 0, NOW())';
$stmt = $conn->prepare($sql);
if (!$stmt){
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $conn->error]);
  exit;
}
$priceF = (float)$price;
$stmt->bind_param('iissssdss', $seller_id, $game_id, $title, $slug, $description, $specification, $priceF, $platform, $region);
$ok = $stmt->execute();
$err = $ok ? null : $stmt->error;
$product_id = $ok ? $stmt->insert_id : 0;
$stmt->close();

if (!$ok){
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => $err ?: 'Insert failed']);
  exit;
}

// Handle primary image
if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp'
  ];
  $type = @mime_content_type($_FILES['image']['tmp_name']);
  if (!isset($allowed[$type])) {
    // Not fatal: keep product but no image
  } else {
    $ext = $allowed[$type];
    $uploadDir = realpath(__DIR__ . '/..') . '/uploads/products';
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
    $safeName = 'p' . $product_id . '_' . time() . '.' . $ext;
    $dest = $uploadDir . '/' . $safeName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
      $image_url = 'uploads/products/' . $safeName;
      $stmt2 = $conn->prepare('INSERT INTO product_images (product_id, image_url, alt_text, is_primary, sort_order, created_at) VALUES (?, ?, NULL, 1, 0, NOW())');
      $stmt2->bind_param('is', $product_id, $image_url);
      $stmt2->execute();
      $stmt2->close();
    }
  }
}

echo json_encode(['ok' => true, 'id' => $product_id]);
