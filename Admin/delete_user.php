<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

// Basic auth check (optional expand later)
// Only allow if logged-in role is admin
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
// Only delete customers for safety
$sql = 'DELETE FROM users WHERE id = ? AND role = "customer" LIMIT 1';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$ok = $stmt->execute();
$affected = $stmt->affected_rows;
$err = $ok ? null : $stmt->error;
$stmt->close();

if ($ok && $affected > 0) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $err ?: 'User not found or not deletable']);
}
