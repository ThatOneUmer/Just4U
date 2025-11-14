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
if (!is_numeric($price) || (float)$price < 0) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => 'Invalid price']);
  exit;
}

// Update product ensured to belong to seller
$sql = 'UPDATE products SET game_id = ?, title = ?, description = ?, specification = ?, price = ?, platform = ?, region = ? WHERE id = ? AND seller_id = ?';
$stmt = $conn->prepare($sql);
if (!$stmt){ http_response_code(500); echo json_encode(['ok'=>false,'error'=>$conn->error]); exit; }
$priceF = (float)$price;
$stmt->bind_param('isssdssii', $game_id, $title, $description, $specification, $priceF, $platform, $region, $id, $seller_id);
$ok = $stmt->execute();
$err = $ok ? null : $stmt->error;
$stmt->close();
if (!$ok){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>$err?:'Update failed']); exit; }

$image_url = null;
if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
  $allowed = [ 'image/jpeg'=>'jpg', 'image/png'=>'png', 'image/gif'=>'gif', 'image/webp'=>'webp' ];
  $type = @mime_content_type($_FILES['image']['tmp_name']);
  if (isset($allowed[$type])){
    $ext = $allowed[$type];
    $uploadDir = realpath(__DIR__ . '/..') . '/uploads/products';
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
    $safeName = 'p' . $id . '_' . time() . '.' . $ext;
    $dest = $uploadDir . '/' . $safeName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)){
      $image_url = 'uploads/products/' . $safeName;
      // Upsert primary image
      $stmt2 = $conn->prepare('SELECT id FROM product_images WHERE product_id = ? AND is_primary = 1 LIMIT 1');
      $stmt2->bind_param('i', $id);
      $stmt2->execute();
      $stmt2->bind_result($imgId);
      if ($stmt2->fetch()){
        $stmt2->close();
        $stmt3 = $conn->prepare('UPDATE product_images SET image_url = ? WHERE id = ?');
        if ($stmt3){
          $stmt3->bind_param('si', $image_url, $imgId);
          $stmt3->execute();
          $stmt3->close();
        }
      } else {
        $stmt2->close();
        $stmt4 = $conn->prepare('INSERT INTO product_images (product_id, image_url, alt_text, is_primary, sort_order, created_at) VALUES (?, ?, NULL, 1, 0, NOW())');
        $stmt4->bind_param('is', $id, $image_url);
        $stmt4->execute();
        $stmt4->close();
      }
    }
  }
}

// Fetch status for UI patch
$st = $conn->prepare('SELECT status FROM products WHERE id = ? AND seller_id = ?');
$st->bind_param('ii', $id, $seller_id);
$st->execute();
$st->bind_result($status);
$st->fetch();
$st->close();

echo json_encode(['ok'=>true, 'status'=>$status ?? 'active', 'image_url'=>$image_url]);
