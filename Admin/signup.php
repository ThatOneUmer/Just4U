<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($username === '' || strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        if (db_user_exists($email, $username)) {
            $errors[] = 'An account with that email or username already exists.';
        } else {
            $res = db_create_user($username, $email, $password, 'admin');
            if ($res['ok']) {
                // Auto-login as admin
                $user = db_find_user_by_email_or_username($email);
                if ($user) {
                    $_SESSION['user_id'] = (int)$user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                }
                header('Location: /JUST4U-1/admin/index.php');
                exit;
            } else {
                $errors[] = 'Failed to create admin account. Please try again later.';
            }
        }
    }
}
?>
<?php
// Admin signup disabled: redirect to main login
header('Location: ../login.php');
exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Signup | Just4U Gaming</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="admin.css">
  <style>
    .auth-wrap{max-width:520px;margin:80px auto 24px;background:var(--secondary-bg);border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:24px}
    .auth-title{font-weight:800;margin-bottom:6px}
    .auth-sub{color:rgba(255,255,255,0.6);margin-bottom:18px}
    .auth-form .form-group{margin-bottom:12px}
    .note{color:rgba(255,255,255,0.6);font-size:13px;margin-top:8px}
  </style>
</head>
<body class="admin-body">
  <div class="admin-layout" style="grid-template-columns:1fr;">
    <main class="admin-main" style="max-width:820px;margin:0 auto;width:100%;">
      <header class="admin-topbar" style="justify-content:center;gap:12px;">
        <a href="../index.php" class="admin-logo" style="text-decoration:none;display:flex;gap:6px;align-items:center;">
          <span class="logo-text">Just4U</span><span class="logo-accent">Gaming</span>
        </a>
      </header>

      <div class="auth-wrap">
        <?php if ($errors): ?>
          <div class="notification notification-error" style="margin-bottom:16px; background:#FF6B6B; color:#fff; padding:12px 16px; border-radius:12px;">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars(implode(' ', $errors)); ?>
          </div>
        <?php endif; ?>
            <input type="text" id="username" name="username" required minlength="3">
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6">
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
            <i class="fas fa-user-shield"></i> Create Admin
          </button>
          <div class="note">This form creates a user with role <strong>admin</strong>.</div>
        </form>
      </div>
    </main>
  </div>

  <script src="admin.js"></script>
</body>
</html>
