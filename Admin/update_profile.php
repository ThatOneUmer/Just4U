<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Forbidden']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$id = (int)$_SESSION['user_id'];
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$first_name = trim($_POST['first_name'] ?? '') ?: null;
$last_name = trim($_POST['last_name'] ?? '') ?: null;
$phone = trim($_POST['phone'] ?? '') ?: null;
$avatar_url = null;

if ($username === '' || $email === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Username and email are required']);
    exit;
}

// Handle avatar upload if present
if (!empty($_FILES['avatar']['name']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    $type = @mime_content_type($_FILES['avatar']['tmp_name']);
    if (!isset($allowed[$type])) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid image type']);
        exit;
    }
    $ext = $allowed[$type];
    $uploadDir = realpath(__DIR__ . '/..') . '/uploads/avatars';
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
    $safeName = 'u' . $id . '_' . time() . '.' . $ext;
    $dest = $uploadDir . '/' . $safeName;
    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Failed to save image']);
        exit;
    }
    // Path relative to site root
    $avatar_url = 'uploads/avatars/' . $safeName;
}

$res = db_update_user_profile($id, $username, $email, $first_name, $last_name, $phone, $avatar_url);
if ($res['ok']) {
    // refresh session display values
    $_SESSION['username'] = $username;
    if ($avatar_url) { $_SESSION['avatar_url'] = $avatar_url; }
    echo json_encode(['ok' => true]);
} else {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $res['error'] ?? 'Update failed']);
}
