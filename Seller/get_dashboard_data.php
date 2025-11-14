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

$u = db_get_user_by_id($user_id);
if (!$u || (int)($u['is_verified'] ?? 0) !== 1) {
  http_response_code(403);
  echo json_encode(['ok' => false, 'error' => 'Seller pending approval']);
  exit;
}

$seller_id = 0;
$stmt = $conn->prepare('SELECT id FROM sellers WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($seller_id);
$stmt->fetch();
$stmt->close();

if ($seller_id <= 0) {
  echo json_encode([
    'ok' => true,
    'kpis' => ['total_sales' => 0, 'active_products' => 0, 'orders_pending' => 0, 'avg_rating' => 0],
    'recent_orders' => [],
    'top_products' => [],
    'alerts' => [],
    'orders_overview' => []
  ]);
  exit;
}

$total_sales = 0.0;
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE seller_id = ? AND payment_status = 'paid' AND status IN ('confirmed','processing','completed')");
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$stmt->bind_result($total_sales);
$stmt->fetch();
$stmt->close();

$active_products = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ? AND status = 'active'");
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$stmt->bind_result($active_products);
$stmt->fetch();
$stmt->close();

$orders_pending = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE seller_id = ? AND status = 'pending'");
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$stmt->bind_result($orders_pending);
$stmt->fetch();
$stmt->close();

$avg_rating = 0.0;
$stmt = $conn->prepare("SELECT COALESCE(AVG(r.rating), 0) FROM reviews r JOIN products p ON p.id = r.product_id WHERE p.seller_id = ? AND r.status = 'approved'");
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$stmt->bind_result($avg_rating);
$stmt->fetch();
$stmt->close();

$recent_orders = [];
$stmt = $conn->prepare("SELECT order_number, total_amount, payment_status, status, created_at FROM orders WHERE seller_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) { $recent_orders[] = $row; }
$stmt->close();

$top_products = [];
$stmt = $conn->prepare("SELECT p.id, p.title, p.status, COUNT(oi.id) AS purchases
        FROM order_items oi
        JOIN orders o ON o.id = oi.order_id
        JOIN products p ON p.id = oi.product_id
        WHERE o.seller_id = ? AND o.payment_status = 'paid'
        GROUP BY p.id, p.title, p.status
        ORDER BY purchases DESC, p.title ASC
        LIMIT 5");
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) { $top_products[] = $row; }
$stmt->close();

$new_reviews = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM reviews r JOIN products p ON p.id = r.product_id WHERE p.seller_id = ? AND r.status = 'approved' AND r.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$stmt->bind_result($new_reviews);
$stmt->fetch();
$stmt->close();

$alerts = [];
if ($orders_pending > 0) { $alerts[] = ['text' => 'Pending orders: ' . (int)$orders_pending, 'type' => 'warning']; }
if ($new_reviews > 0) { $alerts[] = ['text' => 'New reviews this week: ' . (int)$new_reviews, 'type' => 'success']; }
if ($active_products === 0) { $alerts[] = ['text' => 'No active products', 'type' => 'warning']; }
if (count($alerts) === 0) { $alerts[] = ['text' => 'All systems normal', 'type' => 'success']; }

$orders_overview = [];
$stmt = $conn->prepare("SELECT o.order_number, o.total_amount, o.payment_status, o.status, o.created_at,
        (SELECT p.title FROM order_items oi2 JOIN products p ON p.id = oi2.product_id WHERE oi2.order_id = o.id ORDER BY oi2.id ASC LIMIT 1) AS product_title,
        (SELECT u.username FROM users u WHERE u.id = o.customer_id LIMIT 1) AS buyer_username
        FROM orders o
        WHERE o.seller_id = ?
        ORDER BY o.created_at DESC
        LIMIT 10");
$stmt->bind_param('i', $seller_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) { $orders_overview[] = $row; }
$stmt->close();

echo json_encode([
  'ok' => true,
  'kpis' => [
    'total_sales' => (float)$total_sales,
    'active_products' => (int)$active_products,
    'orders_pending' => (int)$orders_pending,
    'avg_rating' => round((float)$avg_rating, 1)
  ],
  'recent_orders' => $recent_orders,
  'top_products' => $top_products,
  'alerts' => $alerts,
  'orders_overview' => $orders_overview
]);
?>