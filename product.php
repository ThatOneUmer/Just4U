<?php if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/includes/db.php';
// Load product by id
$pid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$conn = db_connect();
$reviewMsg = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review'])) {
  $uid = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
  $rid = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
  if ($uid <= 0) {
    $reviewMsg = 'Please login to delete your review.';
  } elseif ($pid <= 0 || $rid <= 0) {
    $reviewMsg = 'Invalid review.';
  } else {
    $std = $conn->prepare('DELETE FROM reviews WHERE id = ? AND customer_id = ? AND product_id = ? LIMIT 1');
    $std->bind_param('iii', $rid, $uid, $pid);
    $ok = $std->execute();
    $err = $ok ? null : $std->error;
    $std->close();
    $ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($ok) {
      if ($ajax) {
        $sumTotal = 0; $sumAvg = 0.0; $bd = [1=>0,2=>0,3=>0,4=>0,5=>0];
        $stS = $conn->prepare('SELECT COUNT(*) AS total, COALESCE(AVG(rating),0) AS avg FROM reviews WHERE product_id = ? AND status = "approved"');
        $stS->bind_param('i', $pid);
        $stS->execute();
        $rsS = $stS->get_result();
        if ($rsS) { $rowS = $rsS->fetch_assoc(); if ($rowS) { $sumTotal = (int)$rowS['total']; $sumAvg = (float)$rowS['avg']; } }
        $stS->close();
        $stB = $conn->prepare('SELECT rating, COUNT(*) AS c FROM reviews WHERE product_id = ? AND status = "approved" GROUP BY rating');
        $stB->bind_param('i', $pid);
        $stB->execute();
        $rsB = $stB->get_result();
        if ($rsB) { while ($r = $rsB->fetch_assoc()) { $bd[(int)$r['rating']] = (int)$r['c']; } }
        $stB->close();
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'deleted' => (int)$rid, 'summary' => ['total'=>$sumTotal, 'avg'=>$sumAvg, 'breakdown'=>$bd]]);
        exit;
      } else {
        header('Location: product.php?id=' . $pid . '#reviews');
        exit;
      }
    } else {
      if ($ajax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => $err]);
        exit;
      } else {
        $reviewMsg = 'Error: ' . $err;
      }
    }
  }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
  $uid = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
  $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
  $title = isset($_POST['title']) ? trim($_POST['title']) : null;
  $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
  if ($uid <= 0) {
    $reviewMsg = 'Please login to write a review.';
  } elseif ($pid <= 0) {
    $reviewMsg = 'Invalid product.';
  } elseif ($rating < 1 || $rating > 5 || $comment === '') {
    $reviewMsg = 'Rating and comment are required.';
  } else {
    $isVerified = 0;
    $stc = $conn->prepare('SELECT 1 FROM order_items oi JOIN orders o ON o.id = oi.order_id WHERE oi.product_id = ? AND o.customer_id = ? LIMIT 1');
    $stc->bind_param('ii', $pid, $uid);
    $stc->execute();
    $stc->store_result();
    if ($stc->num_rows > 0) { $isVerified = 1; }
    $stc->close();
    $sqlIns = 'INSERT INTO reviews (product_id, customer_id, order_id, rating, title, comment, is_verified_purchase, status, created_at) VALUES (?, ?, NULL, ?, ?, ?, ?, "approved", NOW()) ON DUPLICATE KEY UPDATE rating=VALUES(rating), title=VALUES(title), comment=VALUES(comment), is_verified_purchase=VALUES(is_verified_purchase), status="approved", updated_at=NOW()';
    $sti = $conn->prepare($sqlIns);
    $sti->bind_param('iiissi', $pid, $uid, $rating, $title, $comment, $isVerified);
    $ok = $sti->execute();
    $err = $ok ? null : $sti->error;
    $sti->close();
    $ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($ok) {
      if ($ajax) {
        $stOne = $conn->prepare('SELECT r.id, r.customer_id, r.rating, r.title, r.comment, r.created_at, r.is_verified_purchase, u.username, u.avatar_url FROM reviews r JOIN users u ON u.id = r.customer_id WHERE r.product_id = ? AND r.customer_id = ? LIMIT 1');
        $stOne->bind_param('ii', $pid, $uid);
        $stOne->execute();
        $ro = $stOne->get_result();
        $rv = $ro ? $ro->fetch_assoc() : null;
        $stOne->close();

        $sumTotal = 0; $sumAvg = 0.0; $bd = [1=>0,2=>0,3=>0,4=>0,5=>0];
        $stS = $conn->prepare('SELECT COUNT(*) AS total, COALESCE(AVG(rating),0) AS avg FROM reviews WHERE product_id = ? AND status = "approved"');
        $stS->bind_param('i', $pid);
        $stS->execute();
        $rsS = $stS->get_result();
        if ($rsS) { $rowS = $rsS->fetch_assoc(); if ($rowS) { $sumTotal = (int)$rowS['total']; $sumAvg = (float)$rowS['avg']; } }
        $stS->close();
        $stB = $conn->prepare('SELECT rating, COUNT(*) AS c FROM reviews WHERE product_id = ? AND status = "approved" GROUP BY rating');
        $stB->bind_param('i', $pid);
        $stB->execute();
        $rsB = $stB->get_result();
        if ($rsB) { while ($r = $rsB->fetch_assoc()) { $bd[(int)$r['rating']] = (int)$r['c']; } }
        $stB->close();

        header('Content-Type: application/json');
        echo json_encode([
          'ok' => true,
          'review' => $rv,
          'summary' => [ 'total' => $sumTotal, 'avg' => $sumAvg, 'breakdown' => $bd ],
          'owner' => true
        ]);
        exit;
      } else {
        header('Location: product.php?id=' . $pid . '#reviews');
        exit;
      }
    } else {
      if ($ajax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => $err]);
        exit;
      } else {
        $reviewMsg = 'Error: ' . $err;
      }
    }
  }
}
$product = null; $images = [];
if ($pid > 0) {
  $sql = "SELECT p.*, g.name AS game_name, u.username AS seller_username,
                 (SELECT pi.image_url FROM product_images pi WHERE pi.product_id=p.id AND pi.is_primary=1 ORDER BY pi.id ASC LIMIT 1) AS primary_image
          FROM products p
          JOIN games g ON g.id = p.game_id
          JOIN sellers s ON s.id = p.seller_id
          JOIN users u ON u.id = s.user_id
          WHERE p.id = ? LIMIT 1";
  $st = $conn->prepare($sql);
  $st->bind_param('i', $pid);
  $st->execute();
  $res = $st->get_result();
  $product = $res ? $res->fetch_assoc() : null;
  $st->close();
  // images list
  $st2 = $conn->prepare('SELECT image_url FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, id ASC');
  $st2->bind_param('i', $pid);
  $st2->execute();
  $r2 = $st2->get_result();
  while ($row = $r2->fetch_assoc()) { $images[] = $row['image_url']; }
  $st2->close();
}
$title = $product['title'] ?? 'Product';
$price = isset($product['price']) ? (float)$product['price'] : 0.0;
$orig = isset($product['original_price']) && $product['original_price'] !== null ? (float)$product['original_price'] : null;
$savings = ($orig !== null && $orig > $price) ? ($orig - $price) : 0;
$gameName = $product['game_name'] ?? '';
$sellerName = $product['seller_username'] ?? '';
$platform = $product['platform'] ?? 'PC';
$region = $product['region'] ?? 'Global';
$delivery = $product['delivery_type'] ?? 'Instant';
$mainImage = !empty($images) ? $images[0] : 'https://placehold.co/600x400/0F1620/FFFFFF?text=IMG';
$thumbs = !empty($images) ? $images : [$mainImage];
// sanitize product description allowing limited HTML
function j4u_sanitize_desc($html){
  if (!is_string($html) || $html === '') return '';
  $allowed = '<h1><h2><h3><h4><h5><h6><p><div><ul><ol><li><strong><b><em><i><u><br><a><blockquote><code><pre><span>';
  $clean = strip_tags($html, $allowed);
  // remove inline event handlers and style attributes
  $clean = preg_replace('/\son[a-z]+\s*=\s*"[^"]*"/i', '', $clean);
  $clean = preg_replace("/\son[a-z]+\s*=\s*'[^']*'/i", '', $clean);
  $clean = preg_replace('/\sstyle\s*=\s*"[^"]*"/i', '', $clean);
  $clean = preg_replace("/\sstyle\s*=\s*'[^']*'/i", '', $clean);
  // neutralize javascript: hrefs
  $clean = preg_replace('/href\s*=\s*"\s*javascript:[^"]*"/i', 'href="#"', $clean);
  $clean = preg_replace("/href\s*=\s*'\s*javascript:[^']*'/i", 'href="#"', $clean);
  return $clean;
}
$_DESC_RAW = $product['description'] ?? '';
$_DESC_SAFE = j4u_sanitize_desc($_DESC_RAW);
$_SPEC_RAW = $product['specification'] ?? '';
$_SPEC_SAFE = j4u_sanitize_desc($_SPEC_RAW);
$reviewsTotal = 0; $reviewsAvg = 0.0; $breakdown = [1=>0,2=>0,3=>0,4=>0,5=>0]; $reviews = [];
if ($pid > 0) {
  $stSum = $conn->prepare('SELECT COUNT(*) AS total, COALESCE(AVG(rating),0) AS avg FROM reviews WHERE product_id = ? AND status = "approved"');
  $stSum->bind_param('i', $pid);
  $stSum->execute();
  $rs = $stSum->get_result();
  if ($rs) { $row = $rs->fetch_assoc(); if ($row) { $reviewsTotal = (int)$row['total']; $reviewsAvg = (float)$row['avg']; } }
  $stSum->close();
  $stBr = $conn->prepare('SELECT rating, COUNT(*) AS c FROM reviews WHERE product_id = ? AND status = "approved" GROUP BY rating');
  $stBr->bind_param('i', $pid);
  $stBr->execute();
  $rb = $stBr->get_result();
  if ($rb) { while ($r = $rb->fetch_assoc()) { $k = (int)$r['rating']; $breakdown[$k] = (int)$r['c']; } }
  $stBr->close();
  $stList = $conn->prepare('SELECT r.id, r.customer_id, r.rating, r.title, r.comment, r.created_at, r.is_verified_purchase, u.username, u.avatar_url FROM reviews r JOIN users u ON u.id = r.customer_id WHERE r.product_id = ? AND r.status = "approved" ORDER BY r.created_at DESC LIMIT 50');
  $stList->bind_param('i', $pid);
  $stList->execute();
  $rl = $stList->get_result();
  if ($rl) { while ($row = $rl->fetch_assoc()) { $reviews[] = $row; } }
  $stList->close();
}
$myReview = null;
if (!empty($_SESSION['user_id']) && $pid > 0) {
  $cid = (int)$_SESSION['user_id'];
  $stm = $conn->prepare('SELECT id, rating, title, comment FROM reviews WHERE product_id = ? AND customer_id = ? LIMIT 1');
  $stm->bind_param('ii', $pid, $cid);
  $stm->execute();
  $rm = $stm->get_result();
  $myReview = $rm ? $rm->fetch_assoc() : null;
  $stm->close();
}
$pfRating = $myReview['rating'] ?? '';
$pfTitle = $myReview['title'] ?? '';
$pfComment = $myReview['comment'] ?? '';
$pfId = $myReview['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> | Just4U Gaming</title>
    <meta name="description" content="Buy <?php echo htmlspecialchars($title); ?> on Just4U Gaming.">
    <meta name="keywords" content="fortnite account, fortnite pro account, fortnite skins, fortnite v-bucks, instant delivery">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="style.css">
    <style>
      /* Description rich text spacing */
      .rich-description{line-height:1.7;color:#CFE3FF;white-space:pre-line}
      .rich-description p{display:block;margin:0 0 12px;white-space:inherit}
      .rich-description h1,.rich-description h2,.rich-description h3,.rich-description h4,.rich-description h5,.rich-description h6{display:block;margin:16px 0 8px;color:#E6F1FF}
      .rich-description ul,.rich-description ol{display:block;margin:0 0 12px 20px;padding-left:20px}
      .rich-description ul{list-style:disc outside}
      .rich-description ol{list-style:decimal outside}
      .rich-description li{display:list-item;margin:4px 0}
      .rich-description blockquote{margin:12px 0;padding:8px 12px;border-left:3px solid #1FB6FF;background:rgba(31,182,255,0.08)}
      .rich-description div{display:block;margin:0 0 12px}
      .rich-description pre{background:#0D141B;border:1px solid var(--border);padding:10px;border-radius:8px;overflow:auto;color:#E6F1FF}
      .rich-description code{background:#0D141B;border:1px solid var(--border);padding:2px 4px;border-radius:4px}
      .rich-description a{color:#1FB6FF;text-decoration:underline}
      .rich-description br{display:block;content:"";margin-bottom:8px}
      .rich-description span{display:block;margin-bottom:8px}
      /* Quill alignment and indentation support */
      .rich-description .ql-align-center{ text-align:center; }
      .rich-description .ql-align-right{ text-align:right; }
      .rich-description .ql-align-justify{ text-align:justify; }
      .rich-description .ql-indent-1{ margin-left: 1.5em; }
      .rich-description .ql-indent-2{ margin-left: 3em; }
      .rich-description .ql-indent-3{ margin-left: 4.5em; }
      .rich-description .ql-indent-4{ margin-left: 6em; }
      .rich-description .ql-indent-5{ margin-left: 7.5em; }
      .rich-description .ql-indent-6{ margin-left: 9em; }
      .rich-description .ql-indent-7{ margin-left: 10.5em; }
      .rich-description .ql-indent-8{ margin-left: 12em; }
      .modal-overlay{position:fixed;left:0;top:0;right:0;bottom:0;background:rgba(0,0,0,0.6);display:none;align-items:center;justify-content:center;z-index:1000}
      .modal{background:#0F1620;border:1px solid var(--border);border-radius:12px;width:520px;max-width:calc(100% - 32px);padding:16px;box-shadow:0 10px 30px rgba(0,0,0,0.4)}
      .modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
      .modal-header h3{margin:0;color:#E6F1FF}
      .modal-close{background:transparent;border:1px solid var(--border);color:#CFE3FF;border-radius:8px;width:36px;height:36px;cursor:pointer}
      .modal-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:8px}
      .review-form{display:grid;gap:12px;max-width:560px}
      .form-row{display:flex;align-items:center;gap:12px}
      .form-label{min-width:80px;color:#E6F1FF}
      .input,.textarea{background:#0F1620;border:1px solid var(--border);color:#E6F1FF;border-radius:12px;padding:12px}
      .input:focus,.textarea:focus{outline:none;border-color:#1FB6FF;box-shadow:0 0 0 3px rgba(31,182,255,0.2)}
      .rating-stars{display:flex;gap:8px}
      .rating-star{font-size:22px;color:#5B6B7C;cursor:pointer;transition:color .15s ease}
      .rating-star.active{color:#FFD700}
      .btn.glow{box-shadow:0 10px 24px rgba(46,213,115,0.35)}
      .review-item{position:relative}
      .review-actions{position:absolute;right:12px;bottom:12px;display:flex;gap:8px}
      .action-icon{width:36px;height:36px;display:flex;align-items:center;justify-content:center;background:#0F1620;border:1px solid var(--border);border-radius:8px;color:#CFE3FF;cursor:pointer}
      .action-icon:hover{border-color:#1FB6FF;color:#1FB6FF}
    </style>
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": <?php echo json_encode($title); ?>,
        "description": <?php echo json_encode('Buy '.$title.' on Just4U Gaming'); ?>,
        "image": <?php echo json_encode($mainImage); ?>,
        "offers": {
            "@type": "Offer",
            "price": <?php echo json_encode(number_format($price,2,'.','')); ?>,
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock",
            "seller": {
                "@type": "Organization",
                "name": <?php echo json_encode($sellerName ?: 'Seller'); ?>
            }
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": <?php echo json_encode($reviewsTotal ? number_format($reviewsAvg,1) : '0.0'); ?>,
            "reviewCount": <?php echo json_encode((string)(int)$reviewsTotal); ?>
        }
    }
    </script>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <div class="logo">
                    <a href="index.php">
                        <span class="logo-text">Just4U</span>
                        <span class="logo-accent">Gaming</span>
                    </a>
                </div>
                
                <!-- Navigation -->
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="index.php" class="nav-link">Home</a></li>
                        <li><a href="shop.php" class="nav-link active">Shop</a></li>
                        <li><a href="about.php" class="nav-link">About</a></li>
                        <li><a href="support.php" class="nav-link">Support</a></li>
                    </ul>
                </nav>
                
                <!-- Search & Cart -->
                <div class="header-actions">
                    <div class="search-container">
                        <input type="text" class="search-input" placeholder="Search games, accounts...">
                        <button class="search-btn"><i class="fas fa-search"></i></button>
                        <div class="search-suggestions" id="searchSuggestions"></div>
                    </div>
                    
                    <?php if (!empty($_SESSION['user_id'])): ?>
                    <div class="cart-container">
                        <button class="cart-btn" id="cartBtn">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="cart-count" id="cartCount">0</span>
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="user-actions">
                        <button class="user-btn" id="userBtn">
                            <i class="fas fa-user"></i>
                        </button>
                        <!-- User Dropdown -->
                        <div class="user-dropdown" id="userDropdown">
                            <div class="dropdown-content">
                                <a href="account.php" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    My Profile
                                </a>
                                <a href="orders.php" class="dropdown-item">
                                    <i class="fas fa-shopping-bag"></"></i>
                                    My Orders
                                </a>
                                <a href="support.php" class="dropdown-item">
                                    <i class="fas fa-headset"></i>
                                    Support
                                </a>
                                <div class="dropdown-divider"></div>
                                <?php if (!empty($_SESSION['user_id'])): ?>
                                <a href="logout.php" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    Logout
                                </a>
                                <?php else: ?>
                                <a href="login.php" class="dropdown-item">
                                    <i class="fas fa-sign-in-alt"></i>
                                    Login
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Menu Toggle -->
                    <button class="mobile-menu-toggle" id="mobileMenuToggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Product Detail -->
    <section class="product-detail">
        <div class="container">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="shop.php">Shop</a>
                <i class="fas fa-chevron-right"></i>
                <a href="shop.php"><?php echo htmlspecialchars($gameName); ?></a>
                <i class="fas fa-chevron-right"></i>
                <span><?php echo htmlspecialchars($title); ?></span>
            </div>

            <div class="product-layout">
                <!-- Product Images -->
                <div class="product-images">
                    <div class="main-image">
                        <img src="<?php echo htmlspecialchars($mainImage); ?>" alt="<?php echo htmlspecialchars($title); ?>" id="mainImage">
                        <div class="image-badges">
                            <span class="badge instant">Instant Delivery</span>
                            <span class="badge verified">Verified Account</span>
                        </div>
                    </div>
                    <div class="image-thumbnails">
                        <?php foreach ($thumbs as $i => $img): $thumb = $img; ?>
                        <div class="thumbnail<?php echo $i===0?' active':''; ?>" data-image="<?php echo htmlspecialchars($img); ?>">
                            <img src="<?php echo htmlspecialchars($thumb); ?>" alt="Thumb <?php echo $i+1; ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <div class="product-header">
                        <h1 class="product-title"><?php echo htmlspecialchars($title); ?></h1>
                        <div class="product-rating">
                            <div class="stars">
                                <?php $rounded = (int)round($reviewsAvg); for ($s=1;$s<=5;$s++): ?>
                                <i class="fas fa-star" style="color:<?php echo $s <= $rounded ? '#FFD700' : '#5B6B7C'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-text"><?php echo ($reviewsTotal ? number_format($reviewsAvg,1) : '0.0'); ?> (<?php echo (int)$reviewsTotal; ?> reviews)</span>
                        </div>
                    </div>

                    <div class="product-price">
                        <div class="price-main">
                            <span class="current-price">$<?php echo number_format($price, 2); ?></span>
                            <?php if ($orig !== null && $orig > $price): ?>
                            <span class="original-price">$<?php echo number_format($orig, 2); ?></span>
                            <span class="discount"><?php echo (int)round((($orig - $price)/$orig)*100); ?>% OFF</span>
                            <?php endif; ?>
                            
                        </div>
                        <?php if ($savings > 0): ?>
                        <div class="price-note">Limited time offer - Save $<?php echo number_format($savings, 2); ?>!</div>
                        <?php endif; ?>
                    </div>

                    

                    <!-- Account Attributes -->
                    <div class="account-attributes">
                        <h3>Account Details</h3>
                        <div class="attributes-grid">
                            <div class="attribute">
                                <i class="fas fa-trophy"></i>
                                <div class="attribute-info">
                                    <span class="attribute-label">Level</span>
                                    <span class="attribute-value">200+</span>
                                </div>
                            </div>
                            <div class="attribute">
                                <i class="fas fa-gamepad"></i>
                                <div class="attribute-info">
                                    <span class="attribute-label">Platform</span>
                                    <span class="attribute-value"><?php echo htmlspecialchars($platform); ?></span>
                                </div>
                            </div>
                            <div class="attribute">
                                <i class="fas fa-globe"></i>
                                <div class="attribute-info">
                                    <span class="attribute-label">Region</span>
                                    <span class="attribute-value"><?php echo htmlspecialchars($region); ?></span>
                                </div>
                            </div>
                            <div class="attribute">
                                <i class="fas fa-bolt"></i>
                                <div class="attribute-info">
                                    <span class="attribute-label">Delivery</span>
                                    <span class="attribute-value"><?php echo htmlspecialchars($delivery); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features List -->
                    <div class="product-features">
                        <h3>What's Included</h3>
                        <ul class="features-list">
                            <li><i class="fas fa-check"></i> All Battle Passes (Chapter 1-4)</li>
                            <li><i class="fas fa-check"></i> 50+ Rare & Legendary Skins</li>
                            <li><i class="fas fa-check"></i> 15,000+ V-Bucks</li>
                            <li><i class="fas fa-check"></i> All Emotes & Pickaxes</li>
                            <li><i class="fas fa-check"></i> Victory Royale Wins: 500+</li>
                            <li><i class="fas fa-check"></i> K/D Ratio: 2.5+</li>
                            <li><i class="fas fa-check"></i> No VAC Bans or Restrictions</li>
                            <li><i class="fas fa-check"></i> Full Email Access</li>
                        </ul>
                    </div>

                    <!-- Purchase Options -->
                    <div class="purchase-options">
                        <div class="quantity-selector">
                            <label for="quantity">Quantity:</label>
                            <div class="quantity-controls">
                                <button class="quantity-btn" id="decreaseQty">-</button>
                                <input type="number" id="quantity" value="1" min="1" max="10">
                                <button class="quantity-btn" id="increaseQty">+</button>
                            </div>
                        </div>

                        <div class="purchase-buttons">
                            <button class="btn btn-primary btn-large" id="addToCart">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Add to Cart - $<?php echo number_format($price,2); ?></span>
                            </button>
                            <button class="btn btn-secondary btn-large" id="buyNow">
                                <i class="fas fa-bolt"></i>
                                <span>Buy Now</span>
                            </button>
                        </div>
                    </div>

                    <!-- Trust Signals -->
                    <div class="trust-signals">
                        <div class="trust-item">
                            <i class="fas fa-shield-alt"></i>
                            <span>100% Secure Payment</span>
                        </div>
                        <div class="trust-item">
                            <i class="fas fa-undo"></i>
                            <span>30-Day Money Back</span>
                        </div>
                        <div class="trust-item">
                            <i class="fas fa-headset"></i>
                            <span>24/7 Support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Tabs -->
    <section class="product-tabs">
        <div class="container">
            <div class="tabs-navigation">
                <button class="tab-btn active" data-tab="description">Description</button>
                <button class="tab-btn" data-tab="specifications">Specifications</button>
                <button class="tab-btn" data-tab="reviews">Reviews (<?php echo (int)$reviewsTotal; ?>)</button>
                <button class="tab-btn" data-tab="delivery">Delivery Info</button>
            </div>

            <div class="tabs-content">
                <!-- Description Tab -->
                <div class="tab-panel active" id="description">
                    <div class="tab-content">
                        <?php $desc = trim($_DESC_SAFE); $raw = trim($_DESC_RAW); ?>
                        <?php if ($desc !== ''): ?>
                        <?php $hasTags = preg_match('/<(p|div|span|ul|ol|li|h[1-6]|blockquote|code|pre|br)\b/i', $desc); ?>
                        <div class="rich-description">
                          <?php if ($hasTags): ?>
                            <?php echo $desc; ?>
                          <?php else: ?>
                            <?php echo nl2br(htmlspecialchars($raw)); ?>
                          <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <p>No description available.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-panel" id="specifications">
                    <div class="tab-content">
                        <?php $spec = trim($_SPEC_SAFE); $rawSpec = trim($_SPEC_RAW); ?>
                        <?php if ($spec !== ''): ?>
                        <?php $hasTags = preg_match('/<(p|div|span|ul|ol|li|h[1-6]|blockquote|code|pre|br)\b/i', $spec); ?>
                        <div class="rich-description">
                          <?php if ($hasTags): ?>
                            <?php echo $spec; ?>
                          <?php else: ?>
                            <?php echo nl2br(htmlspecialchars($rawSpec)); ?>
                          <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <p>No specifications available.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-panel" id="reviews">
                    <div class="tab-content">
                        <div class="reviews-summary">
                            <div class="rating-overview">
                                <div class="rating-score"><?php echo $reviewsTotal ? number_format($reviewsAvg,1) : '0.0'; ?></div>
                                <div class="rating-stars">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="rating-count">Based on <?php echo (int)$reviewsTotal; ?> reviews</div>
                            </div>
                            <div class="rating-breakdown">
                                <?php $tot = max($reviewsTotal,1); $p5 = round(($breakdown[5]/$tot)*100); $p4 = round(($breakdown[4]/$tot)*100); $p3 = round(($breakdown[3]/$tot)*100); $p2 = round(($breakdown[2]/$tot)*100); $p1 = round(($breakdown[1]/$tot)*100); ?>
                                <div class="rating-bar"><span>5★</span><div class="bar"><div class="fill" style="width: <?php echo $p5; ?>%"></div></div><span><?php echo $p5; ?>%</span></div>
                                <div class="rating-bar"><span>4★</span><div class="bar"><div class="fill" style="width: <?php echo $p4; ?>%"></div></div><span><?php echo $p4; ?>%</span></div>
                                <div class="rating-bar"><span>3★</span><div class="bar"><div class="fill" style="width: <?php echo $p3; ?>%"></div></div><span><?php echo $p3; ?>%</span></div>
                                <div class="rating-bar"><span>2★</span><div class="bar"><div class="fill" style="width: <?php echo $p2; ?>%"></div></div><span><?php echo $p2; ?>%</span></div>
                                <div class="rating-bar"><span>1★</span><div class="bar"><div class="fill" style="width: <?php echo $p1; ?>%"></div></div><span><?php echo $p1; ?>%</span></div>
                            </div>
                        </div>

                        <div class="reviews-list">
                            <?php if (empty($reviews)): ?>
                            <p>No reviews yet.</p>
                            <?php else: ?>
                            <?php foreach ($reviews as $rv): ?>
                            <div class="review-item" data-review-id="<?php echo (int)$rv['id']; ?>">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <?php $av = $rv['avatar_url'] ?: 'https://placehold.co/80x80/1FB6FF/ffffff?text=U'; ?>
                                            <img src="<?php echo htmlspecialchars($av); ?>" alt="<?php echo htmlspecialchars($rv['username']); ?>">
                                        </div>
                                        <div class="reviewer-details">
                                            <h4><?php echo htmlspecialchars($rv['username']); ?><?php if (!empty($rv['is_verified_purchase'])): ?> <span style="font-size:12px;color:#1FB6FF">Verified Purchase</span><?php endif; ?></h4>
                                            <div class="review-rating">
                                                <?php for ($s=1;$s<=5;$s++): ?>
                                                <i class="fas fa-star" style="color:<?php echo $s <= (int)$rv['rating'] ? '#FFD700' : '#5B6B7C'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="review-date"><?php echo date('M j, Y', strtotime($rv['created_at'])); ?></div>
                                </div>
                                <div class="review-content">
                                    <?php if (!empty($rv['title'])): ?><h4 style="margin:0 0 6px 0; color:#CFE3FF;"><?php echo htmlspecialchars($rv['title']); ?></h4><?php endif; ?>
                                    <p><?php echo nl2br(htmlspecialchars($rv['comment'])); ?></p>
                                    <?php if (!empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$rv['customer_id']): ?>
                                    <div class="review-actions">
                                        <button type="button" class="action-icon review-edit" title="Edit" data-review-id="<?php echo (int)$rv['id']; ?>" data-rating="<?php echo (int)$rv['rating']; ?>" data-title="<?php echo htmlspecialchars($rv['title'] ?? '', ENT_QUOTES); ?>" data-comment="<?php echo htmlspecialchars($rv['comment'] ?? '', ENT_QUOTES); ?>"><i class="fas fa-pen"></i></button>
                                        <form method="post" style="display:inline-flex;">
                                            <input type="hidden" name="delete_review" value="1">
                                            <input type="hidden" name="review_id" value="<?php echo (int)$rv['id']; ?>">
                                            <button type="submit" class="action-icon" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div style="margin-top:16px">
                            <?php if (!empty($_SESSION['user_id'])): ?>
                            <form id="addReviewForm" method="post" class="review-form">
                                <input type="hidden" name="add_review" value="1">
                                <div class="form-row">
                                    <label class="form-label">Rating</label>
                                    <div class="rating-stars" id="addRatingStars">
                                        <i class="fas fa-star rating-star" data-value="1"></i>
                                        <i class="fas fa-star rating-star" data-value="2"></i>
                                        <i class="fas fa-star rating-star" data-value="3"></i>
                                        <i class="fas fa-star rating-star" data-value="4"></i>
                                        <i class="fas fa-star rating-star" data-value="5"></i>
                                    </div>
                                    <input type="hidden" name="rating" id="addRating" value="">
                                </div>
                                <input class="input" type="text" name="title" placeholder="Title (optional)">
                                <textarea class="textarea" name="comment" required rows="4" placeholder="Write your review"></textarea>
                                <button class="btn btn-primary glow" type="submit" style="width:max-content;">Submit Review</button>
                            </form>
                            <?php else: ?>
                            <p>Please <a href="login.php">login</a> to write a review.</p>
                            <?php endif; ?>
                        </div>
                        <div id="editReviewModal" class="modal-overlay">
                          <div class="modal">
                            <div class="modal-header">
                              <h3>Edit Review</h3>
                              <button id="editModalClose" class="modal-close">×</button>
                            </div>
                            <form method="post" id="editReviewForm" class="review-form">
                              <input type="hidden" name="add_review" value="1">
                              <div class="form-row">
                                <label class="form-label">Rating</label>
                                <div class="rating-stars" id="editRatingStars">
                                  <i class="fas fa-star rating-star" data-value="1"></i>
                                  <i class="fas fa-star rating-star" data-value="2"></i>
                                  <i class="fas fa-star rating-star" data-value="3"></i>
                                  <i class="fas fa-star rating-star" data-value="4"></i>
                                  <i class="fas fa-star rating-star" data-value="5"></i>
                                </div>
                                <input type="hidden" name="rating" id="editRating" value="">
                              </div>
                              <input class="input" type="text" name="title" placeholder="Title (optional)">
                              <textarea class="textarea" name="comment" required rows="4" placeholder="Write your review"></textarea>
                              <div class="modal-actions">
                                <button type="button" class="btn btn-secondary" id="editModalCancel">Cancel</button>
                                <button type="submit" class="btn btn-primary glow">Save Changes</button>
                              </div>
                            </form>
                          </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Tab -->
                <div class="tab-panel" id="delivery">
                    <div class="tab-content">
                        <h3>Delivery Information</h3>
                        
                        <div class="delivery-info">
                            <div class="delivery-method">
                                <h4><i class="fas fa-bolt"></i> Instant Digital Delivery</h4>
                                <p>This account will be delivered instantly via email and your account dashboard after successful payment.</p>
                            </div>

                            <div class="delivery-steps">
                                <h4>How it works:</h4>
                                <ol>
                                    <li>Complete your purchase securely</li>
                                    <li>Receive account details via email within 5 minutes</li>
                                    <li>Access your account through our secure dashboard</li>
                                    <li>Change password and security settings</li>
                                    <li>Start playing immediately!</li>
                                </ol>
                            </div>

                            <div class="delivery-guarantee">
                                <h4><i class="fas fa-shield-alt"></i> Delivery Guarantee</h4>
                                <ul>
                                    <li>100% instant delivery within 5 minutes</li>
                                    <li>Email confirmation with all details</li>
                                    <li>Dashboard access for account management</li>
                                    <li>24/7 support if you need assistance</li>
                                    <li>Full refund if delivery fails</li>
                                </ul>
                            </div>

                            <div class="delivery-security">
                                <h4><i class="fas fa-lock"></i> Security & Privacy</h4>
                                <p>Your account details are encrypted and delivered securely. We never store your payment information and all transactions are processed through secure payment gateways.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <section class="related-products">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">You Might Also Like</h2>
                <p class="section-subtitle">Similar accounts you might be interested in</p>
            </div>
            
            <div class="products-grid">
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1607853202273-797f1c43a3e1?w=300&h=200&fit=crop&crop=center" alt="Valorant Immortal">
                        <div class="product-badge verified">Verified</div>
                    </div>
                    <div class="product-info">
                        <h3>Valorant Immortal</h3>
                        <p>High Rank • Premium Skins</p>
                        <div class="product-price">
                            <span class="current-price">$104.99</span>
                            <span class="original-price">$149.99</span>
                        </div>
                        <button class="btn btn-primary">View Details</button>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1556438064-2d7646166914?w=300&h=200&fit=crop&crop=center" alt="PUBG Conqueror">
                        <div class="product-badge new">New</div>
                    </div>
                    <div class="product-info">
                        <h3>PUBG Conqueror</h3>
                        <p>Top Rank • Premium Outfits</p>
                        <div class="product-price">
                            <span class="current-price">$59.99</span>
                            <span class="original-price">$79.99</span>
                        </div>
                        <button class="btn btn-primary">View Details</button>
                    </div>
                </div>
                
                <div class="product-card">
                    <div class="product-image">
                        <img src="https://images.unsplash.com/photo-1552820728-8b83bb6b773f?w=300&h=200&fit=crop&crop=center" alt="GTA V Money">
                        <div class="product-badge verified">Verified</div>
                    </div>
                    <div class="product-info">
                        <h3>GTA V Money Account</h3>
                        <p>$50M+ • Premium Cars</p>
                        <div class="product-price">
                            <span class="current-price">$45.99</span>
                            <span class="original-price">$65.99</span>
                        </div>
                        <button class="btn btn-primary">View Details</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <span class="logo-text">Just4U</span>
                        <span class="logo-accent">Gaming</span>
                    </div>
                    <p class="footer-description">
                        Your trusted source for premium gaming accounts. 
                        Verified, secure, and delivered instantly.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-discord"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="shop.php">Shop</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="support.php">Support</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Categories</h3>
                    <ul class="footer-links">
                        <li><a href="shop.php?game=fortnite">Fortnite</a></li>
                        <li><a href="shop.php?game=valorant">Valorant</a></li>
                        <li><a href="shop.php?game=pubg">PUBG Mobile</a></li>
                        <li><a href="shop.php?game=gta">GTA V</a></li>
                        <li><a href="shop.php?game=cs2">Counter-Strike 2</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="support.php">Help Center</a></li>
                        <li><a href="support.php#contact">Contact Us</a></li>
                        <li><a href="support.php#faq">FAQ</a></li>
                        <li><a href="support.php#disputes">Dispute Resolution</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; 2024 Just4U Gaming. All rights reserved.</p>
                    <div class="footer-bottom-links">
                        <a href="privacy.php">Privacy</a>
                        <a href="terms.php">Terms</a>
                        <a href="cookies.php">Cookies</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>Shopping Cart</h3>
            <button class="cart-close" id="cartClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Cart items will be populated here -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total: <span id="cartTotal">$0.00</span></span>
            </div>
            <button class="btn btn-primary cart-checkout">Checkout</button>
        </div>
    </div>

    

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="script.js"></script>
    <script>
      (function(){
        var addBtn = document.getElementById('addToCart');
        var buyBtn = document.getElementById('buyNow');
        var loggedIn = <?php echo !empty($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        function redirectToLogin(){
          var msg = encodeURIComponent('Please login to continue with your purchase');
          var ret = encodeURIComponent(window.location.pathname + window.location.search);
          window.location.href = 'login.php?notice=' + msg + '&return=' + ret;
        }
        if (addBtn) addBtn.addEventListener('click', function(e){
          if (!loggedIn) { e.preventDefault(); redirectToLogin(); }
        });
        if (buyBtn) buyBtn.addEventListener('click', function(e){
          if (!loggedIn) { e.preventDefault(); redirectToLogin(); }
        });
      })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="product.js"></script>
</body>
</html>
