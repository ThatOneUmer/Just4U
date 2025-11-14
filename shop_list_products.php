<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';

try {
  $conn = db_connect();

  // Parse filters
  $games = [];
  if (!empty($_GET['game'])) {
    // support CSV or repeated params
    $raw = is_array($_GET['game']) ? $_GET['game'] : explode(',', $_GET['game']);
    $gameMap = [
      'fortnite' => 'fortnite',
      'valorant' => 'valorant',
      'pubg' => 'pubg mobile',
      'pubg-mobile' => 'pubg mobile',
      'gta' => 'gta 5',
      'cs2' => 'counter-strike 2',
      'lol' => 'league of legends'
    ];
    foreach ($raw as $g) {
      $g = strtolower(trim($g));
      if ($g === '') continue;
      $mapped = $gameMap[$g] ?? $g;
      $games[] = strtolower($mapped);
    }
  }
  $platforms = [];
  if (!empty($_GET['platform'])) {
    $raw = is_array($_GET['platform']) ? $_GET['platform'] : explode(',', $_GET['platform']);
    foreach ($raw as $p) { $p = trim($p); if ($p !== '') $platforms[] = strtolower($p); }
  }
  $deliveries = [];
  if (!empty($_GET['delivery'])) {
    $raw = is_array($_GET['delivery']) ? $_GET['delivery'] : explode(',', $_GET['delivery']);
    foreach ($raw as $d) { $d = trim($d); if ($d !== '') $deliveries[] = strtolower($d); }
  }
  $priceMin = isset($_GET['priceMin']) ? floatval($_GET['priceMin']) : null;
  $priceMax = isset($_GET['priceMax']) ? floatval($_GET['priceMax']) : null;
  $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

  $where = ["p.status = 'active'"];
  $params = [];
  $types = '';

  if ($priceMin !== null) { $where[] = 'p.price >= ?'; $params[] = $priceMin; $types .= 'd'; }
  if ($priceMax !== null && $priceMax > 0) { $where[] = 'p.price <= ?'; $params[] = $priceMax; $types .= 'd'; }

  $inClause = function($count){ return implode(',', array_fill(0, $count, '?')); };

  if (count($games) > 0) {
    // compare on normalized names OR on slug
    $placeholders = $inClause(count($games));
    $where[] = '(LOWER(REPLACE(REPLACE(g.name, "-", ""), " ", "")) IN (' . $placeholders . ') OR LOWER(g.slug) IN (' . $placeholders . '))';
    foreach ($games as $g) {
      $params[] = str_replace(['-', ' '], '', strtolower($g));
      $types .= 's';
    }
    foreach ($games as $g) {
      $params[] = strtolower($g);
      $types .= 's';
    }
  }
  if (count($platforms) > 0) {
    $where[] = 'LOWER(p.platform) IN (' . $inClause(count($platforms)) . ')';
    foreach ($platforms as $p) { $params[] = $p; $types .= 's'; }
  }
  if (count($deliveries) > 0) {
    $where[] = 'LOWER(p.delivery_type) IN (' . $inClause(count($deliveries)) . ')';
    foreach ($deliveries as $d) { $params[] = $d; $types .= 's'; }
  }

  // Sorting
  $orderBy = 'p.created_at DESC';
  if ($sort === 'price-low') $orderBy = 'p.price ASC';
  else if ($sort === 'price-high') $orderBy = 'p.price DESC';
  else if ($sort === 'newest') $orderBy = 'p.created_at DESC';

  $sql = "
    SELECT p.id, p.title, p.price, p.original_price, p.platform, p.region, p.delivery_type,
           p.created_at, p.status,
           g.name AS game_name,
           u.username AS seller_username,
           u.avatar_url AS seller_avatar_url,
           (
             SELECT ROUND(AVG(r.rating), 1)
             FROM reviews r
             JOIN products p2 ON p2.id = r.product_id
             WHERE p2.seller_id = s.id AND r.status = 'approved'
           ) AS seller_rating,
           (SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.id AND pi.is_primary = 1 ORDER BY pi.id ASC LIMIT 1) AS image_url
    FROM products p
    JOIN games g ON g.id = p.game_id
    JOIN sellers s ON s.id = p.seller_id
    JOIN users u ON u.id = s.user_id
    WHERE " . implode(' AND ', $where) . "
    ORDER BY $orderBy
    LIMIT 100
  ";

  $stmt = $conn->prepare($sql);
  if ($stmt === false) { throw new Exception('Failed to prepare statement'); }
  if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $res = $stmt->get_result();

  $rows = [];
  if ($res) {
    while ($r = $res->fetch_assoc()) {
      $rows[] = [
        'id' => (int)$r['id'],
        'name' => $r['title'],
        'game' => $r['game_name'],
        'platform' => $r['platform'] ?: 'PC',
        'price' => (float)$r['price'],
        'originalPrice' => isset($r['original_price']) ? (float)$r['original_price'] : null,
        'image' => $r['image_url'] ?: 'https://placehold.co/400x300/0F1620/FFFFFF?text=IMG',
        'delivery' => $r['delivery_type'] ?: 'Instant',
        'region' => $r['region'] ?: 'Global',
        'seller' => $r['seller_username'] ?: 'Seller',
        'sellerAvatar' => $r['seller_avatar_url'] ?: null,
        'sellerRating' => isset($r['seller_rating']) ? floatval($r['seller_rating']) : null,
        'level' => '',
        'rank' => '',
        'features' => []
      ];
    }
  }

  echo json_encode(['ok' => true, 'products' => $rows]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

