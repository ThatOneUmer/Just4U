<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';

try {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $category = trim($_POST['subject'] ?? ''); // form's subject is actually a topic/category
  $message = trim($_POST['message'] ?? '');
  $user_id = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; // must be logged in

  if ($name === '' || $email === '' || $category === '' || $message === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'All fields are required']);
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid email address']);
    exit;
  }

  if ($user_id === 0) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Login required']);
    exit;
  }

  $conn = db_connect();

  // Insert into support_tickets table (schema: ticket_number, user_id NOT NULL, subject, message, category, status, ...)
  $status = 'open';
  $ticket_number = 'TCK-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
  // Append name/email to message for reference since table lacks those columns
  $combinedMessage = "From: $name <$email>\n\n" . $message;

  $stmt = $conn->prepare('INSERT INTO support_tickets (ticket_number, user_id, subject, message, category, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
  if (!$stmt) { throw new Exception($conn->error); }
  // Use a generic subject line; category is stored separately
  $subjectLine = 'Support request';
  $stmt->bind_param('sissss', $ticket_number, $user_id, $subjectLine, $combinedMessage, $category, $status);

  $ok = $stmt->execute();
  if (!$ok) { throw new Exception($stmt->error ?: 'Insert failed'); }
  $ticket_id = $stmt->insert_id;
  $stmt->close();

  echo json_encode(['ok' => true, 'ticket_id' => $ticket_id, 'ticket_number' => $ticket_number]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
