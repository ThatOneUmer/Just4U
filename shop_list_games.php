<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';

try {
  $conn = db_connect();

  $sql = "
    SELECT g.id,
           g.name,
           COALESCE(g.slug, '') AS slug,
           COUNT(p.id) AS product_count
    FROM games g
    LEFT JOIN products p ON p.game_id = g.id AND p.status = 'active'
    WHERE g.is_active = 1
    GROUP BY g.id, g.name, g.slug
    ORDER BY g.name ASC
  ";

  $res = $conn->query($sql);
  $rows = [];
  while ($r = $res->fetch_assoc()) {
    $name = $r['name'];
    $slug = $r['slug'];
    if ($slug === '' || $slug === null) {
      $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
      $slug = trim($slug, '-');
    }
    $rows[] = [
      'id' => (int)$r['id'],
      'name' => $name,
      'slug' => $slug,
      'count' => (int)$r['product_count']
    ];
  }

  echo json_encode(['ok' => true, 'games' => $rows]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
