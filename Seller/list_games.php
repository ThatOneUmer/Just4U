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
$res = $conn->query('SELECT id, name FROM games WHERE is_active = 1 ORDER BY name ASC');
$rows = [];
if ($res) { while ($r = $res->fetch_assoc()) { $rows[] = $r; } }

echo json_encode(['ok' => true, 'games' => $rows]);
