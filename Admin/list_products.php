<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Forbidden']);
  exit;
}

$conn = db_connect();
$sql = "SELECT p.id, p.title, p.price, p.status, p.created_at,
               u.username AS seller_username,
               (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 ORDER BY pi.id ASC LIMIT 1) AS image_url
        FROM products p
        JOIN sellers s ON s.id = p.seller_id
        JOIN users u ON u.id = s.user_id
        ORDER BY p.created_at DESC";
$res = $conn->query($sql);
$rows = [];
if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } }

echo json_encode(['ok' => true, 'products' => $rows]);
