<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Forbidden']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid user id']);
    exit;
}

$conn = db_connect();
// Delete from sellers table first (if present), then user (restricted to seller role)
$ok = true; $err = null;

// Remove seller profile
$stmt = $conn->prepare('DELETE FROM sellers WHERE user_id = ?');
$stmt->bind_param('i', $id);
$ok = $stmt->execute();
if (!$ok) { $err = $stmt->error; }
$stmt->close();

if ($ok) {
    $stmt2 = $conn->prepare('DELETE FROM users WHERE id = ? AND role = "seller" LIMIT 1');
    $stmt2->bind_param('i', $id);
    $ok = $stmt2->execute();
    $affected = $stmt2->affected_rows;
    if (!$ok) { $err = $stmt2->error; }
    $stmt2->close();
}

if ($ok && !isset($affected) ? false : $affected > 0) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $err ?: 'Seller not found or not deletable']);
}
