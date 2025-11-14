<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$messages = ['success' => '', 'error' => ''];
$user = db_get_user_by_id($userId);
if (!$user) {
    $messages['error'] = 'User not found.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'profile';

    if ($action === 'profile') {
        $username   = trim($_POST['username'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $first_name = trim($_POST['first_name'] ?? '') ?: null;
        $last_name  = trim($_POST['last_name'] ?? '') ?: null;
        $phone      = trim($_POST['phone'] ?? '') ?: null;
        $avatar_url = trim($_POST['avatar_url'] ?? '') ?: null; // fallback if needed

        // Handle avatar file upload if provided
        if (!empty($_FILES['avatar']['name']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
            $type = mime_content_type($_FILES['avatar']['tmp_name']);
            if (!isset($allowed[$type])) {
                $messages['error'] = 'Invalid image type. Please upload JPG, PNG, GIF, or WEBP.';
            } else {
                $ext = $allowed[$type];
                $uploadDir = __DIR__ . '/uploads/avatars';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }
                $safeName = 'u' . $userId . '_' . time() . '.' . $ext;
                $dest = $uploadDir . '/' . $safeName;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                    $avatar_url = 'uploads/avatars/' . $safeName;
                } else {
                    $messages['error'] = 'Failed to upload avatar image.';
                }
            }
        }

        if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $messages['error'] = 'Please provide a valid username and email.';
        } else {
            $res = db_update_user_profile($userId, $username, $email, $first_name, $last_name, $phone, $avatar_url);
            if ($res['ok']) {
                $_SESSION['username'] = $username;
                if ($avatar_url) { $_SESSION['avatar_url'] = $avatar_url; }
                $messages['success'] = 'Profile updated successfully.';
                $user = db_get_user_by_id($userId);
            } else {
                $messages['error'] = $res['error'] ?: 'Failed to update profile.';
            }
        }
    } elseif ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($new) < 6 || $new !== $confirm) {
            $messages['error'] = 'Password must be at least 6 characters and match confirmation.';
        } else {
            // Verify current password
            $conn = db_connect();
            $stmt = $conn->prepare('SELECT password_hash FROM users WHERE id = ? LIMIT 1');
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();

            if (!$row || !password_verify($current, $row['password_hash'])) {
                $messages['error'] = 'Current password is incorrect.';
            } else {
                $res = db_update_user_password($userId, $new);
                if ($res['ok']) {
                    $messages['success'] = 'Password updated successfully.';
                } else {
                    $messages['error'] = $res['error'] ?: 'Failed to update password.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Account | Just4U Gaming</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Header -->
  <header class="header">
    <div class="container">
      <div class="header-content">
        <div class="logo">
          <a href="index.php"><span class="logo-text">Just4U</span><span class="logo-accent">Gaming</span></a>
        </div>
        <nav class="nav">
          <ul class="nav-list">
            <li><a class="nav-link" href="index.php">Home</a></li>
            <li><a class="nav-link" href="shop.php">Shop</a></li>
            <li><a class="nav-link" href="about.php">About</a></li>
            <li><a class="nav-link" href="support.php">Support</a></li>
          </ul>
        </nav>
        <div class="header-actions">
          <div class="search-container" style="visibility:hidden;width:0;height:0;"></div>
          <div class="cart-container">
            <button class="cart-btn" id="cartBtn"><i class="fas fa-shopping-cart"></i><span class="cart-count" id="cartCount">0</span></button>
          </div>
          <div class="user-actions">
            <button class="user-btn" id="userBtn">
              <?php if (!empty($_SESSION['avatar_url'])): ?>
                <img src="<?php echo htmlspecialchars($_SESSION['avatar_url']); ?>" alt="Avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
              <?php else: ?>
                <i class="fas fa-user"></i>
              <?php endif; ?>
            </button>
            <div class="user-dropdown" id="userDropdown">
              <div class="dropdown-content">
                <a href="account.php" class="dropdown-item"><i class="fas fa-user"></i>My Profile</a>
                <a href="orders.php" class="dropdown-item"><i class="fas fa-shopping-bag"></i>My Orders</a>
                <a href="support.php" class="dropdown-item"><i class="fas fa-headset"></i>Support</a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item"><i class="fas fa-sign-out-alt"></i>Logout</a>
              </div>
            </div>
          </div>
          <button class="mobile-menu-toggle" id="mobileMenuToggle"><span></span><span></span><span></span></button>
        </div>
      </div>
    </div>
  </header>

  <section class="support-hero" style="margin-top:100px;">
    <div class="container">
      <div class="support-hero-content">
        <h1 class="page-title">My Profile</h1>
        <p class="page-subtitle">Update your account details</p>
      </div>
    </div>
  </section>

  <section class="support-content">
    <div class="container">
      <div class="support-layout">
        <aside class="support-nav">
          <?php 
            $avatarSrc = !empty($_SESSION['avatar_url']) ? $_SESSION['avatar_url'] : (!empty($user['avatar_url']) ? $user['avatar_url'] : '');
          ?>
          <div style="display:flex;flex-direction:column;align-items:center;gap:10px;margin-bottom:14px;">
            <div style="width:110px;height:110px;border-radius:999px;overflow:hidden;border:2px solid rgba(31,182,255,0.35);background:#0F1620;display:flex;align-items:center;justify-content:center;">
              <?php if ($avatarSrc): ?>
                <img src="<?php echo htmlspecialchars($avatarSrc); ?>" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
              <?php else: ?>
                <img src="https://placehold.co/220x220/0F1620/9EB3C7?text=User" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">
              <?php endif; ?>
            </div>
            <div style="font-weight:700;"><?php echo htmlspecialchars($_SESSION['username'] ?? ($user['username'] ?? '')); ?></div>
          </div>
          <h3>Account</h3>
          <ul class="support-links">
            <li><a href="#profile" class="support-link active">Profile</a></li>
            <li><a href="#password" class="support-link">Password</a></li>
          </ul>
        </aside>
        <main class="support-main">
          <?php if ($messages['success']): ?>
            <div class="notification" style="margin-bottom:16px;background:rgba(0,255,136,0.15);color:#00FF88;border:1px solid rgba(0,255,136,0.3);padding:12px 16px;border-radius:12px;">
              <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($messages['success']); ?>
            </div>
          <?php endif; ?>
          <?php if ($messages['error']): ?>
            <div class="notification" style="margin-bottom:16px;background:#FF6B6B;color:#fff;padding:12px 16px;border-radius:12px;">
              <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($messages['error']); ?>
            </div>
          <?php endif; ?>

          <div class="support-section active" id="profile">
            <h2>Profile Details</h2>
            <form method="post" class="support-form" enctype="multipart/form-data">
              <input type="hidden" name="action" value="profile">
              <div class="form-grid">
                <div class="form-group">
                  <label for="username">Username</label>
                  <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                  <label for="first_name">First Name</label>
                  <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                  <label for="last_name">Last Name</label>
                  <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                  <label for="avatar">Avatar Image</label>
                  <input type="file" id="avatar" name="avatar" accept="image/*">
                  <small style="color:var(--text-secondary)">Max ~2-3MB recommended. JPG/PNG/GIF/WEBP</small>
                </div>
              </div>
              <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </form>
          </div>

          <div class="support-section" id="password" style="margin-top:24px;">
            <h2>Change Password</h2>
            <form method="post" class="support-form">
              <input type="hidden" name="action" value="password">
              <div class="form-grid">
                <div class="form-group">
                  <label for="current_password">Current Password</label>
                  <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                  <label for="new_password">New Password</label>
                  <input type="password" id="new_password" name="new_password" minlength="6" required>
                </div>
                <div class="form-group">
                  <label for="confirm_password">Confirm New Password</label>
                  <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
                </div>
              </div>
              <button type="submit" class="btn btn-secondary"><i class="fas fa-key"></i> Update Password</button>
            </form>
          </div>
        </main>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <div class="footer-logo"><span class="logo-text">Just4U</span><span class="logo-accent">Gaming</span></div>
          <p class="footer-description">Your trusted source for premium gaming accounts.</p>
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
          <h3>Support</h3>
          <ul class="footer-links">
            <li><a href="support.php">Help Center</a></li>
            <li><a href="support.php#contact">Contact Us</a></li>
            <li><a href="support.php#faq">FAQ</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <div class="footer-bottom-content">
          <p>&copy; 2025 Just4U Gaming. All rights reserved.</p>
          <div class="footer-bottom-links">
            <a href="privacy.php">Privacy</a>
            <a href="terms.php">Terms</a>
            <a href="cookies.php">Cookies</a>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <script>
    (function(){
      function initLocalUserDropdown(){
        var btn = document.getElementById('userBtn');
        var dd = document.getElementById('userDropdown');
        if(!btn || !dd) return;
        // If global initializer already attached, skip
        if (btn.__localDropdownBound) return; 
        btn.__localDropdownBound = true;
        dd.style.display = 'none';
        dd.style.zIndex = 3000;
        function position(){
          var r = btn.getBoundingClientRect();
          dd.style.position = 'fixed';
          dd.style.top = Math.round(r.bottom + 8) + 'px';
          dd.style.right = Math.max(8, window.innerWidth - r.right - 8) + 'px';
          dd.style.minWidth = '240px';
        }
        function open(){ dd.style.display = 'block'; position(); }
        function close(){ dd.style.display = 'none'; }
        function toggle(){ if (dd.style.display === 'block') { close(); } else { open(); } }
        btn.addEventListener('click', function(e){ e.preventDefault(); toggle(); });
        document.addEventListener('click', function(e){ if (!btn.contains(e.target) && !dd.contains(e.target)) close(); });
        window.addEventListener('scroll', function(){ if (dd.style.display === 'block') position(); }, {passive:true});
        window.addEventListener('resize', function(){ if (dd.style.display === 'block') position(); });
      }
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLocalUserDropdown);
      } else { initLocalUserDropdown(); }
    })();
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <script src="script.js"></script>
</body>
</html>
