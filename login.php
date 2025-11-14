<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$error = '';
$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? ''); // email or username
    $password = $_POST['password'] ?? '';

    if ($identifier === '' || $password === '') {
        $error = 'Please enter your email/username and password.';
    } else {
        $user = db_find_user_by_email_or_username($identifier);
        if ($user && password_verify($password, $user['password_hash'])) {
            if (($user['role'] ?? '') === 'seller' && (int)($user['is_verified'] ?? 0) !== 1) {
                $notice = 'Your seller account is pending approval. Please wait for an administrator to approve your account.';
            } else {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            if (!empty($user['avatar_url'])) {
                $_SESSION['avatar_url'] = $user['avatar_url'];
            } else {
                unset($_SESSION['avatar_url']);
            }
            $role = $user['role'] ?? 'customer';
            // Prefer returning to original page for normal customers
            $return = $_GET['return'] ?? '';
            if ($role === 'customer' && $return) {
                header('Location: ' . $return);
            } elseif ($role === 'admin') {
                header('Location: admin/index.php');
            } elseif ($role === 'seller') {
                header('Location: seller/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
            }
        } else {
            $error = 'Invalid credentials. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login | Just4U Gaming</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <span class="logo-text">Just4U</span>
                        <span class="logo-accent">Gaming</span>
                    </a>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="index.php" class="nav-link">Home</a></li>
                        <li><a href="shop.php" class="nav-link">Shop</a></li>
                        <li><a href="about.php" class="nav-link">About</a></li>
                        <li><a href="support.php" class="nav-link">Support</a></li>
                    </ul>
                </nav>
                <div class="header-actions">
                    <div class="search-container" style="visibility:hidden; width:0; height:0;"></div>
                    <div class="cart-container">
                        <button class="cart-btn" id="cartBtn"><i class="fas fa-shopping-cart"></i><span class="cart-count" id="cartCount">0</span></button>
                    </div>
                    <div class="user-actions">
                        <button class="user-btn" id="userBtn"><i class="fas fa-user"></i></button>
                        <div class="user-dropdown" id="userDropdown">
                            <div class="dropdown-content">
                                <a href="login.php" class="dropdown-item"><i class="fas fa-sign-in-alt"></i>Login</a>
                                <a href="signup.php" class="dropdown-item"><i class="fas fa-user-plus"></i>Signup</a>
                                <div class="dropdown-divider"></div>
                                <a href="support.php" class="dropdown-item"><i class="fas fa-headset"></i>Support</a>
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
                <h1 class="page-title">Login</h1>
                <p class="page-subtitle">Access your Just4U account</p>
            </div>
        </div>
    </section>

    <section class="support-content">
        <div class="container">
            <div class="support-layout" style="display:flex; justify-content:center;">
                <main class="support-main" style="max-width:480px; width:100%;">
                    <?php if (!empty($_GET['notice']) || !empty($notice)): ?>
                        <div class="notification" style="margin-bottom:16px; background: rgba(0, 191, 255, 0.2); color:#8be9fd; padding:12px 16px; border-radius:12px; border:1px solid rgba(0,191,255,0.35);">
                            <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($_GET['notice'] ?? $notice); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="notification notification-error" style="margin-bottom:16px; background:#FF6B6B; color:#fff; padding:12px 16px; border-radius:12px;">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" class="support-form" style="background: var(--secondary-bg); padding:24px; border:1px solid rgba(0,255,136,0.2); border-radius:16px;">
                        <div class="form-group">
                            <label for="identifier">Email or Username</label>
                            <input type="text" id="identifier" name="identifier" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                        <p style="margin-top:12px; text-align:center; color:var(--text-secondary);">Don't have an account? <a href="signup.php">Create one</a></p>
                    </form>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
